<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InterviewFeedbackController extends Controller
{
    public function create($interviewId)
    {
        $interview = DB::table('interviews')
            ->join('candidates', 'interviews.candidate_id', '=', 'candidates.id')
            ->join('managejobs', 'interviews.job_id', '=', 'managejobs.id')
            ->select('interviews.*', 'candidates.first_name', 'candidates.last_name', 'candidates.email', 'managejobs.job_title')
            ->where('interviews.id', $interviewId)
            ->first();

        if (!$interview) {
            return redirect()->back()->with('error', 'Interview not found.');
        }

        if ($interview->feedback_submitted) {
            return redirect()->back()->with('error', 'Feedback already submitted for this interview.');
        }

        return view('hrms.Jobs.interviews.feedback-form', compact('interview'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'interview_id' => 'required|exists:interviews,id',
            'candidate_id' => 'required|exists:candidates,id',
            'interview_round' => 'required|string',
            'technical_score' => 'required|integer|min:1|max:10',
            'communication_score' => 'required|integer|min:1|max:10',
            'problem_solving_score' => 'required|integer|min:1|max:10',
            'domain_knowledge_score' => 'required|integer|min:1|max:10',
            'overall_rating' => 'required|integer|min:1|max:10',
            'strengths' => 'required|string',
            'weaknesses' => 'required|string',
            'interview_notes' => 'required|string',
            'recommendation' => 'required|in:strongly_recommended,recommended,maybe,not_recommended,reject'
        ]);

        $averageScore = ($validated['technical_score'] + $validated['communication_score'] + 
                         $validated['problem_solving_score'] + $validated['domain_knowledge_score'] + 
                         $validated['overall_rating']) / 5;

        DB::table('interview_feedbacks')->insert([
            'interview_id' => $validated['interview_id'],
            'candidate_id' => $validated['candidate_id'],
            'interview_round' => $validated['interview_round'],
            'technical_score' => $validated['technical_score'],
            'communication_score' => $validated['communication_score'],
            'problem_solving_score' => $validated['problem_solving_score'],
            'domain_knowledge_score' => $validated['domain_knowledge_score'],
            'overall_rating' => $validated['overall_rating'],
            'average_score' => $averageScore,
            'strengths' => $validated['strengths'],
            'weaknesses' => $validated['weaknesses'],
            'interview_notes' => $validated['interview_notes'],
            'recommendation' => $validated['recommendation'],
            'submitted_by' => Auth::id() ?? session('user_id'),
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('interviews')->where('id', $validated['interview_id'])->update([
            'feedback_submitted' => true,
            'status' => 'completed',
            'updated_at' => now()
        ]);

        DB::table('candidates')->where('id', $validated['candidate_id'])->update([
            $validated['interview_round'] => 'feedback_submitted',
            'updated_at' => now()
        ]);
 
        $this->logActivity($validated['candidate_id'], 'Interview Feedback Submitted', 
            'Feedback submitted for ' . ucwords(str_replace('_', ' ', $validated['interview_round'])));

        return redirect()->route('recruitment.index', ['tab' => 'add-resume'])
            ->with('success', 'Interview feedback submitted successfully!');
    }

    public function view($interviewId)
    {
        $feedback = DB::table('interview_feedbacks')
            ->join('interviews', 'interview_feedbacks.interview_id', '=', 'interviews.id')
            ->join('candidates', 'interview_feedbacks.candidate_id', '=', 'candidates.id')
            ->select('interview_feedbacks.*', 'candidates.first_name', 'candidates.last_name', 'interviews.interview_datetime')
            ->where('interview_feedbacks.interview_id', $interviewId)
            ->first();

        if (!$feedback) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }

        return view('hrms.Jobs.interviews.view-feedback', compact('feedback'));
    }
public function candidateSummary($candidateId)
{
    $candidate = DB::table('candidates')->where('id', $candidateId)->first();
    
    if (!$candidate) {
        return redirect()->back()->with('error', 'Candidate not found.');
    }

    // Get all interviews for this candidate
    $interviews = DB::table('interviews')
        ->where('candidate_id', $candidateId)
        ->orderBy('created_at', 'asc')
        ->get();

    // Calculate overall average from available score columns
    $totalScore = 0;
    $count = 0;
    foreach ($interviews as $interview) {
        $score = $interview->total_marks ?? $interview->marks ?? $interview->score ?? $interview->rating ?? null;
        if ($score !== null) {
            $totalScore += $score;
            $count++;
        }
    }
    $overallAverage = $count > 0 ? round($totalScore / $count, 1) : 0;

    $summary = [
        'overall_average' => $overallAverage
    ];

    return view('hrms.Jobs.candidates.evaluation-summary', compact('candidate', 'interviews', 'summary'));
}

private function getRecommendationFromScore($score)
{
    if ($score === null) return 'pending';
    if ($score >= 8) return 'strongly_recommended';
    if ($score >= 6) return 'recommended';
    if ($score >= 4) return 'maybe';
    return 'not_recommended';
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
