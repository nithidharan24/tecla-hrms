<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CandidateStatusController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'rejection_remarks' => 'required_if:status,rejected|nullable|string'
        ]);

        $candidate = DB::table('candidates')->where('id', $id)->first();

        if (!$candidate) {
            return response()->json(['success' => false, 'message' => 'Candidate not found.'], 404);
        }

        if ($request->status === 'selected') {
            $validation = $this->validateSelection($id);
            if (!$validation['valid']) {
                return response()->json(['success' => false, 'message' => $validation['message']], 400);
            }
        }

        $updateData = [
            'status' => $request->status,
            'updated_at' => now()
        ];

        if ($request->status === 'rejected' && $request->rejection_remarks) {
            $updateData['rejection_remarks'] = $request->rejection_remarks;
        }

        DB::table('candidates')->where('id', $id)->update($updateData);

        $this->logActivity($id, 'Status Updated', "Status changed to: {$request->status}");

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    public function updateRoundStatus(Request $request, $id)
    {
        $request->validate([
            'round' => 'required|in:hr_interview_status,technical_interview_status,manager_round_status,final_round_status',
            'status' => 'required|string'
        ]);

        $candidate = DB::table('candidates')->where('id', $id)->first();

        if (!$candidate) {
            return response()->json(['success' => false, 'message' => 'Candidate not found.'], 404);
        }

        $interview = DB::table('interviews')
            ->where('candidate_id', $id)
            ->where('interview_round', $request->round)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($interview && !$interview->feedback_submitted && $request->status === 'completed') {
            return response()->json([
                'success' => false, 
                'message' => 'Please submit feedback before marking the round as completed.'
            ], 400);
        }

        DB::table('candidates')->where('id', $id)->update([
            $request->round => $request->status,
            'updated_at' => now()
        ]);

        $roundName = ucwords(str_replace(['_', 'status'], [' ', ''], $request->round));
        $this->logActivity($id, 'Round Status Updated', "{$roundName} status: {$request->status}");

        return response()->json(['success' => true, 'message' => 'Round status updated successfully!']);
    }

    private function validateSelection($candidateId)
    {
        $candidate = DB::table('candidates')->where('id', $candidateId)->first();

        $rounds = ['hr_interview_status', 'technical_interview_status', 'manager_round_status', 'final_round_status'];
        
        foreach ($rounds as $round) {
            if ($candidate->$round !== 'feedback_submitted' && $candidate->$round !== 'completed') {
                return [
                    'valid' => false,
                    'message' => 'All interview rounds must be completed with feedback before selection.'
                ];
            }
        }

        $interviews = DB::table('interviews')->where('candidate_id', $candidateId)->get();
        
        foreach ($interviews as $interview) {
            if (!$interview->feedback_submitted) {
                return [
                    'valid' => false,
                    'message' => 'All interviews must have feedback submitted before selection.'
                ];
            }
        }

        return ['valid' => true];
    }

    private function logActivity($candidateId, $action, $remarks = null)
    {
        DB::table('recruitment_activity_logs')->insert([
            'candidate_id' => $candidateId,
            'user_id' => Auth::id() ?? session('user_id'),
            'action' => $action,
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
