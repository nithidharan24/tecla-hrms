<?php

namespace App\Http\Controllers\Backend\Tickets;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class TicketsController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        // Build the query - Fixed table joins and column references
        $query = DB::table('tickets as t')
            ->leftJoin('allemployees as raiser', 't.raised_by', '=', 'raiser.id')
            ->leftJoin('allemployees as assignee', 't.assigned_to', '=', 'assignee.id')
            ->leftJoin('department as dept_table', 'raiser.department', '=', 'dept_table.id')
            ->leftJoin('designation as desig_table', 'assignee.designation', '=', 'desig_table.id')
            ->select(
                't.*',
                'raiser.firstname as raiser_firstname',
                'raiser.lastname as raiser_lastname',
                'raiser.profile_image as raiser_image',
                'assignee.firstname as assignee_firstname',
                'assignee.lastname as assignee_lastname',
                'assignee.profile_image as assignee_image',
                'desig_table.designation as assignee_designation_name', // Using designation column
                'dept_table.department as department_name' // Fixed: using department column instead of name
            );

        // If employee, only show tickets raised by them
        if ($role === 'employee') {
            $query->where('t.raised_by', $employeeId);
        }

        // Apply filters
        if ($request->filled('ticket_id')) {
            $query->where('t.ticket_id', 'like', '%' . $request->ticket_id . '%');
        }

        if ($request->filled('title')) {
            $query->where('t.title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('category')) {
            $query->where('t.category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('t.priority', $request->priority);
        }

        if ($request->filled('states')) {
            $query->where('t.states', $request->states);
        }

        if ($request->filled('assigned_to')) {
            $query->where('t.assigned_to', $request->assigned_to);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('t.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('t.created_at', '<=', $request->end_date);
        }

        // Order by latest first
        $query->orderBy('t.created_at', 'desc');
        
        $tickets = $query->get();

        // Get counts for dashboard
        $allTicketsCount = $tickets->count();
        $openTicketsCount = $tickets->where('states', 'Open')->count();
        $inProgressTicketsCount = $tickets->where('states', 'In progress')->count();
        $resolvedTicketsCount = $tickets->where('states', 'Resolved')->count();
        $closedTicketsCount = $tickets->where('states', 'Closed')->count();

        // Get support team employees (with support-related designations)
        $supportTeam = DB::table('allemployees as emp')
            ->join('designation as deg', 'emp.designation', '=', 'deg.id')
            ->where('emp.deleted_at', 0)
            ->where('emp.status', 'active')
            ->where(function($q) {
                $q->where('deg.designation', 'LIKE', '%support%')
                  ->orWhere('deg.designation', 'LIKE', '%technician%')
                  ->orWhere('deg.designation', 'LIKE', '%engineer%')
                  ->orWhere('deg.designation', 'LIKE', '%admin%')
                  ->orWhere('deg.designation', 'LIKE', '%manager%')
                  ->orWhere('deg.designation', 'LIKE', '%specialist%');
            })
            ->select(
                'emp.id',
                'emp.firstname',
                'emp.lastname',
                'emp.email',
                'deg.designation as designation_name'
            )
            ->orderBy('emp.firstname')
            ->get();

        return view('hrms.Employee.Tickets.index', compact(
            'tickets',
            'allTicketsCount',
            'openTicketsCount',
            'inProgressTicketsCount',
            'resolvedTicketsCount',
            'closedTicketsCount',
            'role',
            'supportTeam'
        ));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create() 
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        // Get all employees for dropdown (for raised_by)
        $allEmployees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'department')
            ->orderBy('firstname')
            ->get();

        // Get support team for assigned_to (based on designation)
        $supportTeam = DB::table('allemployees as emp')
            ->join('designation as deg', 'emp.designation', '=', 'deg.id')
            ->where('emp.deleted_at', 0)
            ->where('emp.status', 'active')
            ->where(function($q) {
                $q->where('deg.designation', 'LIKE', '%support%')
                  ->orWhere('deg.designation', 'LIKE', '%technician%')
                  ->orWhere('deg.designation', 'LIKE', '%engineer%')
                  ->orWhere('deg.designation', 'LIKE', '%admin%')
                  ->orWhere('deg.designation', 'LIKE', '%manager%')
                  ->orWhere('deg.designation', 'LIKE', '%specialist%');
            })
            ->select(
                'emp.id',
                'emp.firstname',
                'emp.lastname',
                'emp.email',
                'deg.designation as designation_name'
            )
            ->orderBy('emp.firstname')
            ->get();

        return view('hrms.Employee.Tickets.create', compact('allEmployees', 'supportTeam', 'role', 'employeeId'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:Hardware,Software,Network,HR Access',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'raised_by' => 'required|exists:allemployees,id',
            'assigned_to' => 'nullable|exists:allemployees,id',
            'uploaded_files' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,doc,xls,xlsx,txt|max:10240'
        ]);

        // Get department name of raised_by employee
        $raiser = DB::table('allemployees')
            ->where('id', $request->raised_by)
            ->first();

        $departmentName = null;
        if ($raiser && $raiser->department) {
            $dept = DB::table('department')
                ->where('id', $raiser->department)
                ->first();
            $departmentName = $dept ? $dept->department : null; // Fixed: using department column
        }

        $ticketData = [
            'ticket_id' => $this->generateUniqueTicketId(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'states' => 'Open',
            'raised_by' => $request->raised_by,
            'assigned_to' => $request->assigned_to,
            'dept' => $departmentName,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Handle file upload
        if ($request->hasFile('uploaded_files')) {
            $file = $request->file('uploaded_files');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $fileName, 'public');
            $ticketData['uploaded_files'] = $filePath;
        }

        // Insert ticket
        $ticketId = DB::table('tickets')->insertGetId($ticketData);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully. Ticket ID: ' . $ticketData['ticket_id']);
    }

    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        $ticket = DB::table('tickets as t')
            ->leftJoin('allemployees as raiser', 't.raised_by', '=', 'raiser.id')
            ->leftJoin('allemployees as assignee', 't.assigned_to', '=', 'assignee.id')
            ->leftJoin('department as dept_table', 'raiser.department', '=', 'dept_table.id')
            ->leftJoin('designation as desig_table', 'assignee.designation', '=', 'desig_table.id')
            ->select(
                't.*',
                'raiser.firstname as raiser_firstname',
                'raiser.lastname as raiser_lastname',
                'raiser.email as raiser_email',
                'raiser.profile_image as raiser_image',
                'assignee.firstname as assignee_firstname',
                'assignee.lastname as assignee_lastname',
                'assignee.email as assignee_email',
                'assignee.profile_image as assignee_image',
                'desig_table.designation as assignee_designation_name',
                'dept_table.department as department_name' // Fixed: using department column
            )
            ->where('t.id', $id)
            ->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permission - employee can only view their own tickets
        if ($role === 'employee' && $ticket->raised_by != $employeeId) {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied.');
        }

        return view('hrms.Employee.Tickets.show', compact('ticket', 'role'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
   /**
 * Show the form for editing the specified ticket.
 */
public function edit($id)
{
    $employeeId = Session::get('user_id');
    $role = Session::get('role');

    $ticket = DB::table('tickets')->where('id', $id)->first();

    if (!$ticket) {
        return redirect()->route('tickets.index')
            ->with('error', 'Ticket not found.');
    }

    // Check permission - employee can only edit their own tickets and only if not Resolved/Closed
    if ($role === 'employee') {
        if ($ticket->raised_by != $employeeId) {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied.');
        }
        
        if (in_array($ticket->states, ['Resolved', 'Closed'])) {
            return redirect()->route('tickets.index')
                ->with('error', 'Cannot edit resolved or closed tickets.');
        }
    }

    // Get all employees for dropdown (INCLUDING department field)
    $allEmployees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->select('id', 'firstname', 'lastname', 'department') // Added 'department' here
        ->orderBy('firstname')
        ->get();

    // Get support team for assigned_to
    $supportTeam = DB::table('allemployees as emp')
        ->join('designation as deg', 'emp.designation', '=', 'deg.id')
        ->where('emp.deleted_at', 0)
        ->where('emp.status', 'active')
        ->where(function($q) {
            $q->where('deg.designation', 'LIKE', '%support%')
              ->orWhere('deg.designation', 'LIKE', '%technician%')
              ->orWhere('deg.designation', 'LIKE', '%engineer%')
              ->orWhere('deg.designation', 'LIKE', '%admin%')
              ->orWhere('deg.designation', 'LIKE', '%manager%')
              ->orWhere('deg.designation', 'LIKE', '%specialist%');
        })
        ->select(
            'emp.id',
            'emp.firstname',
            'emp.lastname',
            'deg.designation as designation_name'
        )
        ->orderBy('emp.firstname')
        ->get();

    return view('hrms.Employee.Tickets.edit', compact('ticket', 'allEmployees', 'supportTeam', 'role'));
}
    /**
     * Update the specified ticket.
     */
    public function update(Request $request, $id)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        $ticket = DB::table('tickets')->where('id', $id)->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permission
        if ($role === 'employee') {
            if ($ticket->raised_by != $employeeId) {
                return redirect()->route('tickets.index')
                    ->with('error', 'Access denied.');
            }
            
            if (in_array($ticket->states, ['Resolved', 'Closed'])) {
                return redirect()->route('tickets.index')
                    ->with('error', 'Cannot update resolved or closed tickets.');
            }
        }

        // Validate based on role
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'uploaded_files' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,doc,xls,xlsx,txt|max:10240'
        ];

        if ($role === 'admin' || $role === 'support') {
            $rules['category'] = 'required|in:Hardware,Software,Network,HR Access';
            $rules['priority'] = 'required|in:Low,Medium,High,Critical';
            $rules['states'] = 'required|in:Open,In progress,Waiting,Resolved,Closed';
            $rules['assigned_to'] = 'nullable|exists:allemployees,id';
        }

        $request->validate($rules);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'updated_at' => now(),
        ];

        // Admin/Support can update these fields
        if ($role === 'admin' || $role === 'support') {
            $updateData['category'] = $request->category;
            $updateData['priority'] = $request->priority;
            $updateData['states'] = $request->states;
            $updateData['assigned_to'] = $request->assigned_to;
        }

        // Handle file upload
        if ($request->hasFile('uploaded_files')) {
            // Delete old file if exists
            if ($ticket->uploaded_files) {
                Storage::disk('public')->delete($ticket->uploaded_files);
            }

            $file = $request->file('uploaded_files');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $fileName, 'public');
            $updateData['uploaded_files'] = $filePath;
        }

        DB::table('tickets')->where('id', $id)->update($updateData);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified ticket.
     */
    public function destroy($id)
    {
        $role = Session::get('role');
        
        if ($role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied. Only administrators can delete tickets.');
        }

        $ticket = DB::table('tickets')->where('id', $id)->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Delete uploaded file if exists
        if ($ticket->uploaded_files) {
            Storage::disk('public')->delete($ticket->uploaded_files);
        }

        DB::table('tickets')->where('id', $id)->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Download ticket attachment
     */
    public function download($id)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        $ticket = DB::table('tickets')->where('id', $id)->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permission
        if ($role === 'employee' && $ticket->raised_by != $employeeId) {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied.');
        }

        if (!$ticket->uploaded_files) {
            return redirect()->back()
                ->with('error', 'No attachment found.');
        }

        $filePath = storage_path('app/public/' . $ticket->uploaded_files);

        if (!file_exists($filePath)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        $fileName = basename($ticket->uploaded_files);
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Generate unique ticket ID
     */
    private function generateUniqueTicketId()
    {
        $datePart = now()->format('Ymd');
        $prefix = 'TKT-' . $datePart . '-';

        $latestTicket = DB::table('tickets')
            ->where('ticket_id', 'like', $prefix . '%')
            ->orderBy('ticket_id', 'desc')
            ->first();

        if ($latestTicket) {
            $lastNumber = (int)substr($latestTicket->ticket_id, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

   
    /**
     * Update ticket state via AJAX.
     */
    public function updateState(Request $request, $id)
    {
        Log::info('AJAX Update State Request', [
            'ticket_id' => $id,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Check permission
            $role = Session::get('role');
            Log::info('User role for state update', ['role' => $role]);

            if (!in_array($role, ['admin', 'support'])) {
                Log::warning('State update permission denied', ['role' => $role]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only administrators and support staff can update ticket state.'
                ], 403);
            }

            $request->validate([
                'states' => 'required|in:Open,In progress,Waiting,Resolved,Closed'
            ]);

            Log::info('State update validation passed', ['states' => $request->states]);

            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                Log::warning('Ticket not found for state update', ['ticket_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            Log::info('Current ticket state', ['old_state' => $ticket->states]);

            // Check if the 'states' column exists
            $columns = DB::getSchemaBuilder()->getColumnListing('tickets');
            Log::info('Tickets table columns', ['columns' => $columns]);

            $updateResult = DB::table('tickets')
                ->where('id', $id)
                ->update([
                    'states' => $request->states,
                    'updated_at' => now()
                ]);

            Log::info('Database update result', ['rows_affected' => $updateResult]);

            // Verify the update
            $updatedTicket = DB::table('tickets')->where('id', $id)->first();
            Log::info('Ticket after update', ['new_state' => $updatedTicket->states]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket state updated successfully.',
                'old_state' => $ticket->states,
                'new_state' => $request->states
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in state update', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating ticket state: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket state: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket priority via AJAX.
     */
    public function updatePriority(Request $request, $id)
    {
        Log::info('AJAX Update Priority Request', [
            'ticket_id' => $id,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        try {
            // Check permission
            $role = Session::get('role');
            Log::info('User role for priority update', ['role' => $role]);

            if (!in_array($role, ['admin', 'support'])) {
                Log::warning('Priority update permission denied', ['role' => $role]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only administrators and support staff can update ticket priority.'
                ], 403);
            }

            $request->validate([
                'priority' => 'required|in:Low,Medium,High,Critical'
            ]);

            Log::info('Priority update validation passed', ['priority' => $request->priority]);

            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                Log::warning('Ticket not found for priority update', ['ticket_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            Log::info('Current ticket priority', ['old_priority' => $ticket->priority]);

            $updateResult = DB::table('tickets')
                ->where('id', $id)
                ->update([
                    'priority' => $request->priority,
                    'updated_at' => now()
                ]);

            Log::info('Database update result', ['rows_affected' => $updateResult]);

            // Verify the update
            $updatedTicket = DB::table('tickets')->where('id', $id)->first();
            Log::info('Ticket after update', ['new_priority' => $updatedTicket->priority]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket priority updated successfully.',
                'old_priority' => $ticket->priority,
                'new_priority' => $request->priority
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in priority update', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating ticket priority: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket priority: ' . $e->getMessage()
            ], 500);
        }
    }

}