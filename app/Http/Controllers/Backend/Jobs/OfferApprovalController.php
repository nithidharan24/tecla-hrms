<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OfferApprovalController extends Controller
{
    public function createDraft($candidateId)
    {
        $candidate = DB::table('candidates')
            ->where('id', $candidateId)
            ->first();

        if (!$candidate) {
            return redirect()->back()->with('error', 'Candidate not found.');
        }

        $allRoundsCompleted = $this->checkAllRoundsCompleted($candidateId);
        
        if (!$allRoundsCompleted) {
            return redirect()->back()->with('error', 'All interview rounds must be completed with feedback before creating offer.');
        }

        $existingOffer = DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->first();
        
        if ($existingOffer) {
            return redirect()->back()->with('error', 'Offer already exists for this candidate.');
        }

        DB::table('candidate_offer_approvals')->insert([
            'candidate_id' => $candidateId,
            'offer_status' => 'draft',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'status' => 'selected',
            'offer_status' => 'draft',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Draft Created', 'Offer draft created for candidate');

        return redirect()->back()->with('success', 'Offer draft created successfully!');
    }

    public function submitForManagerApproval($candidateId)
    {
        $offer = DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->first();
        
        if (!$offer || $offer->offer_status != 'draft') {
            return redirect()->back()->with('error', 'Offer draft not found or already submitted.');
        }

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'pending_manager',
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'pending_manager',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Pending Manager Approval', 'Offer submitted for manager approval');

        return redirect()->back()->with('success', 'Offer submitted for manager approval!');
    }

    public function managerApprove(Request $request, $candidateId)
    {
        $request->validate([
            'manager_remarks' => 'nullable|string'
        ]);

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'pending_hr',
            'manager_approved_by' => Auth::id() ?? session('user_id'),
            'manager_approved_at' => now(),
            'manager_remarks' => $request->manager_remarks,
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'pending_hr',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Manager Approved', $request->manager_remarks ?? 'Manager approved the offer');

        return redirect()->back()->with('success', 'Offer approved by manager! Now pending HR approval.');
    }

    public function managerReject(Request $request, $candidateId)
    {
        $request->validate([
            'manager_remarks' => 'required|string'
        ]);

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'manager_rejected',
            'manager_approved_by' => Auth::id() ?? session('user_id'),
            'manager_approved_at' => now(),
            'manager_remarks' => $request->manager_remarks,
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'manager_rejected',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Manager Rejected', $request->manager_remarks);

        return redirect()->back()->with('success', 'Offer rejected by manager.');
    }

    public function hrApprove(Request $request, $candidateId)
    {
        $request->validate([
            'hr_remarks' => 'nullable|string'
        ]);

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'hr_approved',
            'hr_approved_by' => Auth::id() ?? session('user_id'),
            'hr_approved_at' => now(),
            'hr_remarks' => $request->hr_remarks,
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'hr_approved',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer HR Approved', $request->hr_remarks ?? 'HR approved the offer');

        return redirect()->back()->with('success', 'Offer approved by HR! Offer letter can now be generated.');
    }

    public function hrReject(Request $request, $candidateId)
    {
        $request->validate([
            'hr_remarks' => 'required|string'
        ]);

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'hr_rejected',
            'hr_approved_by' => Auth::id() ?? session('user_id'),
            'hr_approved_at' => now(),
            'hr_remarks' => $request->hr_remarks,
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'hr_rejected',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer HR Rejected', $request->hr_remarks);

        return redirect()->back()->with('success', 'Offer rejected by HR.');
    }

    public function sendOffer($candidateId)
    {
        $offer = DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->first();
        
        if (!$offer || $offer->offer_status != 'hr_approved') {
            return redirect()->back()->with('error', 'Offer must be approved by both manager and HR before sending.');
        }

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => 'offer_sent',
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => 'offer_sent',
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Sent', 'Offer letter sent to candidate');

        return redirect()->back()->with('success', 'Offer letter sent to candidate!');
    }

    public function updateOfferResponse(Request $request, $candidateId)
    {
        $request->validate([
            'offer_status' => 'required|in:offer_accepted,offer_rejected,offer_expired,joining_deferred',
            'response_date' => 'required|date',
            'expected_joining_date' => 'nullable|date',
            'candidate_comments' => 'nullable|string',
            'hr_remarks' => 'nullable|string'
        ]);

        DB::table('candidate_offer_approvals')->where('candidate_id', $candidateId)->update([
            'offer_status' => $request->offer_status,
            'response_date' => $request->response_date,
            'expected_joining_date' => $request->expected_joining_date,
            'candidate_comments' => $request->candidate_comments,
            'hr_remarks' => $request->hr_remarks,
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $candidateId)->update([
            'offer_status' => $request->offer_status,
            'updated_at' => now()
        ]);

        $this->logActivity($candidateId, 'Offer Response Updated', 
            'Offer status: ' . ucwords(str_replace('_', ' ', $request->offer_status)));

        return redirect()->back()->with('success', 'Offer response updated successfully!');
    }

    private function checkAllRoundsCompleted($candidateId)
    {
        $candidate = DB::table('candidates')->where('id', $candidateId)->first();
        
        $rounds = ['hr_interview_status', 'technical_interview_status', 'manager_round_status', 'final_round_status'];
        
        foreach ($rounds as $round) {
            if ($candidate->$round === 'scheduled' || $candidate->$round === 'pending') {
                return false;
            }
        }

        $interviews = DB::table('interviews')->where('candidate_id', $candidateId)->get();
        
        foreach ($interviews as $interview) {
            if ($interview->status !== 'completed' || !$interview->feedback_submitted) {
                return false;
            }
        }

        return true;
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
