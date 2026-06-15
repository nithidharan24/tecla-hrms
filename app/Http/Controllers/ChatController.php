<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\AllEmployee;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    private function hrCanAccess(Conversation $conv): bool
    {
        return $conv->hr_id == session('user_id')
            || $conv->branch_id == session('branch_id');
    }

    public function hrIndex()
    {
        if (session('role') !== 'hr') {
            abort(403);
        }

        $conversations = Conversation::where(function ($q) {
                $q->where('hr_id', session('user_id'))
                  ->orWhere('branch_id', session('branch_id'));
            })
            ->with([
                'employee',
                'latestMessage',
                'messages' => fn($q) => $q->where('sender_role', 'employee')
                                          ->where('is_read', false),
            ])
            ->get()
            ->map(function ($conversation) {
                $conversation->unreadCount = $conversation->messages->count();
                return $conversation;
            })
            ->sortByDesc(fn($c) => optional($c->latestMessage)->created_at)
            ->values();

        return view('chat.hr-inbox', compact('conversations'));
    }

    public function hrShow($conversationId)
    {
        if (session('role') !== 'hr') {
            abort(403);
        }

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($q) {
                $q->where('hr_id', session('user_id'))
                  ->orWhere('branch_id', session('branch_id'));
            })
            ->firstOrFail();

        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get()
            ->sortBy('created_at')
            ->values();

        Message::where('conversation_id', $conversationId)
            ->where('sender_role', 'employee')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.hr-show', compact('conversation', 'messages'));
    }

    public function employeeChat()
    {
        if (session('role') === 'hr') {
            abort(403);
        }

        $conversation = Conversation::where('employee_id', session('user_id'))
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            $hrRecord = DB::table('allemployees')
                ->join('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
                ->whereIn(DB::raw('LOWER(TRIM(hierarchies.hierarchy_level))'), ['hr', 'hr manager'])
                ->where('allemployees.branch_id', session('branch_id'))
                ->select('allemployees.id')
                ->first();

            if (!$hrRecord) {
                return back()->with('error', 'No HR available in your branch.');
            }

            $conversation = Conversation::create([
                'employee_id' => session('user_id'),
                'hr_id'       => $hrRecord->id,
                'branch_id'   => session('branch_id'),
                'status'      => 'open',
            ]);
        }

        $conversation->load('hr');

        $hrHierarchyLevel = DB::table('hierarchies')
            ->where('id', $conversation->hr->hierarchy_id ?? null)
            ->value('hierarchy_level') ?? 'HR';

        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get()
            ->sortBy('created_at')
            ->values();

        Message::where('conversation_id', $conversation->id)
            ->where('sender_role', 'hr')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.employee-chat', compact('conversation', 'messages', 'hrHierarchyLevel'));
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $userId = session('user_id');
        $canSend = $conversation->employee_id == $userId
                || $conversation->hr_id == $userId
                || (session('role') === 'hr' && $conversation->branch_id == session('branch_id'));
        if (!$canSend) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id'       => $userId,
            'sender_role'     => session('role'),
            'body'            => $request->body,
        ]);

        \Log::info('CHAT: firing broadcast for conversation ' . $conversationId . ' channel chat.' . $conversationId);
        try {
            event(new MessageSent($message, session('first_name') . ' ' . session('last_name')));
            \Log::info('CHAT: broadcast dispatched OK');
        } catch (\Throwable $e) {
            \Log::error('CHAT: broadcast FAILED: ' . $e->getMessage() . ' | ' . get_class($e));
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $message->id,
                'body'        => $message->body,
                'sender_id'   => $message->sender_id,
                'sender_role' => $message->sender_role,
                'sender_name' => session('first_name') . ' ' . session('last_name'),
                'created_at'  => $message->created_at->format('h:i A'),
            ],
        ]);
    }

    public function markRead($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $userId = session('user_id');
        $canAccess = $conversation->employee_id == $userId
                  || $conversation->hr_id == $userId
                  || (session('role') === 'hr' && $conversation->branch_id == session('branch_id'));
        if (!$canAccess) {
            abort(403);
        }

        $oppositeRole = session('role') === 'hr' ? 'employee' : 'hr';

        Message::where('conversation_id', $conversationId)
            ->where('sender_role', $oppositeRole)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getConversations()
    {
        if (session('role') !== 'hr') abort(403);

        $conversations = Conversation::where(function ($q) {
                $q->where('hr_id', session('user_id'))
                  ->orWhere('branch_id', session('branch_id'));
            })
            ->with(['employee', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn($q) =>
                $q->where('sender_role', 'employee')->where('is_read', false)
            ])
            ->get()
            ->sortByDesc(fn($c) => optional($c->latestMessage)->created_at)
            ->values()
            ->map(fn($c) => [
                'id'           => $c->id,
                'emp_name'     => $c->employee->firstname . ' ' . $c->employee->lastname,
                'emp_initial'  => strtoupper(mb_substr($c->employee->firstname, 0, 1)),
                'preview'      => $c->latestMessage ? \Str::limit($c->latestMessage->body, 45) : 'No messages yet',
                'time'         => $c->latestMessage ? $c->latestMessage->created_at->diffForHumans() : '',
                'unread_count' => $c->unread_count,
            ]);

        return response()->json($conversations);
    }

    public function employeeUnread()
    {
        if (session('role') === 'hr') abort(403);

        $conversation = Conversation::where('employee_id', session('user_id'))
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            return response()->json(['unread' => 0]);
        }

        $count = Message::where('conversation_id', $conversation->id)
            ->where('sender_role', 'hr')
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $count]);
    }

    public function getMessages($conversationId, \Illuminate\Http\Request $request)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $userId = session('user_id');
        $canAccess = $conversation->employee_id == $userId
                  || $conversation->hr_id == $userId
                  || (session('role') === 'hr' && $conversation->branch_id == session('branch_id'));
        if (!$canAccess) {
            abort(403);
        }

        $query = $conversation->messages()->orderBy('created_at', 'asc');

        if ($request->filled('after')) {
            $query->where('id', '>', (int) $request->after);
        } else {
            // Return last 60 messages on initial load
            $query = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(60);
        }

        $conversation->load('hr', 'employee');
        $hrName  = $conversation->hr->firstname . ' ' . $conversation->hr->lastname;
        $empName = $conversation->employee->firstname . ' ' . $conversation->employee->lastname;

        $messages = $query->get()->sortBy('created_at')->values()
            ->map(fn($m) => [
                'id'          => $m->id,
                'body'        => $m->body,
                'sender_id'   => $m->sender_id,
                'sender_role' => $m->sender_role,
                'sender_name' => $m->sender_role === 'hr' ? $hrName : $empName,
                'is_read'     => $m->is_read,
                'created_at'  => $m->created_at->format('h:i A'),
            ]);

        return response()->json($messages);
    }
}
