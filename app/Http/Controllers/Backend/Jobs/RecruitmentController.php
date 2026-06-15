<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Onboarding;
use App\Models\CandidateSalaryStructure;
use App\Models\OfferLetter;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use App\Mail\OfferLetterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;
 
class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        // Get the branch filter for the current admin
        $branchId = getAdminBranchFilter();
        
        // ============ REGULAR CANDIDATES QUERY ============
        $candidateQuery = Candidate::query();
        
        // Apply branch filter for candidates
        if ($branchId) {
            $candidateQuery->where('branch_id', $branchId);
        }
        
        // Apply search filter for candidates
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $candidateQuery->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('position_applied', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply position filter for candidates
        if ($request->has('position') && $request->position != '') {
            $candidateQuery->where('position_applied', $request->position);
        }
        
        // Apply status filter for candidates
        if ($request->has('status') && $request->status != '') {
            $candidateQuery->where('status', $request->status);
        }
        
        // Apply experience filter for candidates
        if ($request->has('experience') && $request->experience != '') {
            $candidateQuery->where('experience_years', '>=', $request->experience);
        }
        
        // Apply date range filter for candidates
        if ($request->has('start_date') && $request->start_date != '') {
            $candidateQuery->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $candidateQuery->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Get unique positions for filter dropdown
        $positions = Candidate::select('position_applied')
            ->distinct()
            ->whereNotNull('position_applied')
            ->where('position_applied', '!=', '')
            ->orderBy('position_applied')
            ->get();
        
        $employeesQuery = DB::table('allemployees')
            ->select('id', 'firstname', 'lastname', 'employeeid', 'email')
            ->where('deleted_at', 0)
            ->orderBy('firstname');

        if ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }

        $employees = $employeesQuery->get();

        // Paginate candidates
        $candidates = $candidateQuery->orderBy('created_at', 'desc')->paginate(10);
        
        // ============ SHORTLISTED CANDIDATES QUERY ============
        $shortlistQuery = Candidate::whereIn('status', [
            'shortlisted',
            'telephonic_scheduled',
            'telephonic_completed',
            'interview_scheduled',
            'interview_completed'
        ])->with('interviews');
        
        // Apply branch filter for shortlisted candidates
        if ($branchId) {
            $shortlistQuery->where('branch_id', $branchId);
        }
        
        // Apply search filter for shortlisted candidates
        if ($request->has('shortlist_search') && $request->shortlist_search != '') {
            $search = $request->shortlist_search;
            $shortlistQuery->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('position_applied', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply position filter for shortlisted candidates
        if ($request->has('shortlist_position') && $request->shortlist_position != '') {
            $shortlistQuery->where('position_applied', $request->shortlist_position);
        }
        
        // Apply status filter for shortlisted candidates
        if ($request->has('shortlist_status') && $request->shortlist_status != '') {
            $shortlistQuery->where('status', $request->shortlist_status);
        }
        
        // Apply interview status filter for shortlisted candidates
        if ($request->has('shortlist_interview_status') && $request->shortlist_interview_status != '') {
            if ($request->shortlist_interview_status === 'no_interview') {
                $shortlistQuery->whereDoesntHave('interviews');
            } else {
                $shortlistQuery->whereHas('interviews', function($q) use ($request) {
                    $q->where('status', $request->shortlist_interview_status);
                });
            }
        }
        
        // Apply date range filter for shortlisted candidates
        if ($request->has('shortlist_start_date') && $request->shortlist_start_date != '') {
            $shortlistQuery->whereDate('created_at', '>=', $request->shortlist_start_date);
        }
        
        if ($request->has('shortlist_end_date') && $request->shortlist_end_date != '') {
            $shortlistQuery->whereDate('created_at', '<=', $request->shortlist_end_date);
        }
        
        // Get unique positions for shortlist filter dropdown
        $shortlistPositions = Candidate::whereIn('status', [
            'shortlisted',
            'telephonic_scheduled',
            'telephonic_completed',
            'interview_scheduled',
            'interview_completed'
        ])
            ->select('position_applied')
            ->distinct()
            ->whereNotNull('position_applied')
            ->where('position_applied', '!=', '')
            ->orderBy('position_applied')
            ->get();
        
        // Paginate shortlisted candidates
        $shortlistedCandidates = $shortlistQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'shortlist_page');
        
        // ============ ONBOARDING QUERY ============
        $onboardingQuery = Onboarding::query();
        
        // Apply search filter for onboarding
        if ($request->has('onboarding_search') && $request->onboarding_search != '') {
            $search = $request->onboarding_search;
            $onboardingQuery->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('personal_email_id', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('emergency_contact_name', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply gender filter for onboarding
        if ($request->has('onboarding_gender') && $request->onboarding_gender != '') {
            $onboardingQuery->where('gender', $request->onboarding_gender);
        }
        
        // Get per page value for onboarding
        $onboardingPerPage = $request->has('onboarding_per_page') ? $request->onboarding_per_page : 10;
        
        // Paginate onboarding records
        $onboardings = $onboardingQuery->orderBy('created_at', 'desc')->paginate($onboardingPerPage, ['*'], 'onboarding_page');
        
     // ============ JOBS QUERY ============
$jobsQuery = DB::table('managejobs')
    ->leftJoin('department', 'managejobs.department', '=', 'department.id')  // Join to get department name
    ->select(
        'managejobs.*',
        'department.department as department_name'  // Select the department name from the department table
    );

// Apply branch filter for jobs
if ($branchId) {
    $jobsQuery->where('managejobs.branch_id', $branchId);
}

// Apply search filter for jobs
if ($request->has('job_search') && $request->job_search != '') {
    $search = $request->job_search;
    $jobsQuery->where(function($q) use ($search) {
        $q->where('managejobs.job_title', 'LIKE', "%{$search}%")
          ->orWhere('managejobs.department', 'LIKE', "%{$search}%")
          ->orWhere('department.department', 'LIKE', "%{$search}%")  // Search in department name
          ->orWhere('managejobs.job_location', 'LIKE', "%{$search}%");
    });
}

// Apply department filter for jobs (filter by department name)
if ($request->has('job_department') && $request->job_department != '') {
    $jobsQuery->where('department.department', $request->job_department);
}

// Apply job type filter
if ($request->has('job_type') && $request->job_type != '') {
    $jobsQuery->where('managejobs.job_type', $request->job_type);
}

// Apply status filter for jobs
if ($request->has('job_status') && $request->job_status != '') {
    $jobsQuery->where('managejobs.status', $request->job_status);
}

// Apply date range filter for jobs
if ($request->has('job_start_date') && $request->job_start_date != '') {
    $jobsQuery->whereDate('managejobs.start_date', '>=', $request->job_start_date);
}

if ($request->has('job_end_date') && $request->job_end_date != '') {
    $jobsQuery->whereDate('managejobs.end_date', '<=', $request->job_end_date);
}

// Get unique departments for filter dropdown (from department table)
$departments = DB::table('department')
    ->select('department')
    ->distinct()
    ->whereNotNull('department')
    ->where('department', '!=', '')
    ->orderBy('department')
    ->get();

$today = now()->toDateString();
$jobsQuery->select(
        'managejobs.*',
        'department.department as department_name'
    )
    ->selectSub(function ($query) {
        $query->from('candidates')
            ->selectRaw('COUNT(*)')
            ->whereColumn('candidates.position_applied', 'managejobs.job_title');
    }, 'candidates_count')
    ->selectSub(function ($query) use ($today) {
        $query->from('candidates')
            ->selectRaw('COUNT(*)')
            ->whereColumn('candidates.position_applied', 'managejobs.job_title')
            ->whereDate('candidates.created_at', $today);
    }, 'new_candidates_today_count');

// Paginate jobs
$manageJobs = $jobsQuery->orderBy('managejobs.created_at', 'desc')->paginate(10, ['*'], 'jobs_page');
// ============ JOB VACANCY REQUESTS QUERY ============
$jobRequestsQuery = DB::table('job_vacancy_requests')
    ->leftJoin('department', 'job_vacancy_requests.department', '=', 'department.id')
    ->select(
        'job_vacancy_requests.*',
        'department.department as department_name'
    );

if ($branchId) {
    $jobRequestsQuery->where('job_vacancy_requests.branch_id', $branchId);
}

if ($request->has('job_request_search') && $request->job_request_search != '') {
    $search = $request->job_request_search;
    $jobRequestsQuery->where(function($q) use ($search) {
        $q->where('job_vacancy_requests.job_title', 'LIKE', "%{$search}%")
          ->orWhere('department.department', 'LIKE', "%{$search}%")
          ->orWhere('job_vacancy_requests.job_location', 'LIKE', "%{$search}%");
    });
}

if ($request->has('job_request_status') && $request->job_request_status != '') {
    $jobRequestsQuery->where('job_vacancy_requests.approval_status', $request->job_request_status);
}

if ($request->has('job_request_hr_status') && $request->job_request_hr_status != '') {
    $jobRequestsQuery->where('job_vacancy_requests.hr_approval_status', $request->job_request_hr_status);
}

$jobVacancyRequests = $jobRequestsQuery->orderBy('job_vacancy_requests.created_at', 'desc')->paginate(10, ['*'], 'job_requests_page');
        
        // ============ SELECTED CANDIDATES QUERY ============
        $selectedQuery = Candidate::where('status', 'selected')
            ->with('salaryStructure');
        
        if ($branchId) {
            $selectedQuery->where('branch_id', $branchId);
        }

        if ($request->has('selected_search') && $request->selected_search != '') {
            $search = $request->selected_search;
            $selectedQuery->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('position_applied', 'LIKE', "%{$search}%");
            });
        }
        $selectedCandidates = $selectedQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'selected_page');

        // ============ OVERVIEW / ANALYTICS STATS ============
        $totalCandidatesCountQuery = Candidate::query();
        $shortlistedCountQuery = Candidate::whereIn('status', [
            'shortlisted', 'telephonic_scheduled', 'telephonic_completed', 'interview_scheduled', 'interview_completed'
        ]);
        $interviewedCountQuery = Candidate::whereIn('status', [
            'telephonic_completed', 'interview_scheduled', 'interview_completed', 'selected', 'rejected'
        ]);
        $selectedCountQuery = Candidate::where('status', 'selected');
        $rejectedCountQuery = Candidate::where('status', 'rejected');
        
        $hrRoundQuery = Candidate::whereNotNull('hr_interview_status');
        $technicalRoundQuery = Candidate::whereNotNull('technical_interview_status');
        $managerRoundQuery = Candidate::whereNotNull('manager_round_status');
        $finalRoundQuery = Candidate::whereNotNull('final_round_status');
        
        if ($branchId) {
            $totalCandidatesCountQuery->where('branch_id', $branchId);
            $shortlistedCountQuery->where('branch_id', $branchId);
            $interviewedCountQuery->where('branch_id', $branchId);
            $selectedCountQuery->where('branch_id', $branchId);
            $rejectedCountQuery->where('branch_id', $branchId);
            $hrRoundQuery->where('branch_id', $branchId);
            $technicalRoundQuery->where('branch_id', $branchId);
            $managerRoundQuery->where('branch_id', $branchId);
            $finalRoundQuery->where('branch_id', $branchId);
        }
        
        $stats = [
            'total_candidates' => $totalCandidatesCountQuery->count(),
            'shortlisted' => $shortlistedCountQuery->count(),
            'interviewed' => $interviewedCountQuery->count(),
            'selected' => $selectedCountQuery->count(),
            'rejected' => $rejectedCountQuery->count(),
            'hr_round' => $hrRoundQuery->count(),
            'technical_round' => $technicalRoundQuery->count(),
            'manager_round' => $managerRoundQuery->count(),
            'final_round' => $finalRoundQuery->count(),
        ];
        
        // Offer Stats
        $offerLettersQuery = Candidate::where('status', 'selected')->with('salaryStructure');
        if ($branchId) {
            $offerLettersQuery->where('branch_id', $branchId);
        }
        $selectedCandidatesForStats = $offerLettersQuery->get();
        
        $stats['salary_set'] = $selectedCandidatesForStats->filter(function($c) {
            return $c->salaryStructure !== null;
        })->count();
        
        $stats['offers_sent'] = $selectedCandidatesForStats->filter(function($c) {
            return $c->salaryStructure && $c->salaryStructure->offer_letter_sent;
        })->count();
        
        $stats['offers_pending'] = $stats['selected'] - $stats['offers_sent'];
        
        // Active Jobs & Vacancies
        $activeJobsQuery = DB::table('managejobs')->where('status', 'active');
        if ($branchId) {
            $activeJobsQuery->where('branch_id', $branchId);
        }
        $stats['active_jobs'] = $activeJobsQuery->count();
        $stats['total_vacancies'] = $activeJobsQuery->sum('vacancies');
        
        // 6-Month Application Trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthName = now()->subMonths($i)->format('M');
            $monthNum = now()->subMonths($i)->format('m');
            $yearNum = now()->subMonths($i)->format('Y');
            
            $q = Candidate::whereMonth('created_at', $monthNum)->whereYear('created_at', $yearNum);
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
            $monthlyTrend[$monthName] = $q->count();
        }
        $stats['monthly_trend'] = $monthlyTrend;
        
        // Recent candidates
        $recentCandidatesQuery = Candidate::query()->orderBy('created_at', 'desc')->limit(5);
        if ($branchId) {
            $recentCandidatesQuery->where('branch_id', $branchId);
        }
        $stats['recent_candidates'] = $recentCandidatesQuery->get();

        // ============ RECRUITMENT ACTIVITY LOGS QUERY ============
        $activityLogsQuery = DB::table('recruitment_activity_logs')
            ->leftJoin('candidates', 'recruitment_activity_logs.candidate_id', '=', 'candidates.id')
            ->leftJoin('users', 'recruitment_activity_logs.user_id', '=', 'users.id')
            ->select(
                'recruitment_activity_logs.*',
                'candidates.first_name as candidate_first_name',
                'candidates.last_name as candidate_last_name',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as performer_name")
            );

        if ($branchId) {
            $activityLogsQuery->where(function($q) use ($branchId) {
                $q->where('candidates.branch_id', $branchId)
                  ->orWhereNull('recruitment_activity_logs.candidate_id');
            });
        }

        // Apply filters
        if ($request->has('activity_search') && $request->activity_search != '') {
            $search = $request->activity_search;
            $activityLogsQuery->where(function($q) use ($search) {
                $q->where('candidates.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('candidates.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('recruitment_activity_logs.action', 'LIKE', "%{$search}%")
                  ->orWhere('recruitment_activity_logs.remarks', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('activity_start_date') && $request->activity_start_date != '') {
            $activityLogsQuery->whereDate('recruitment_activity_logs.created_at', '>=', $request->activity_start_date);
        }

        if ($request->has('activity_end_date') && $request->activity_end_date != '') {
            $activityLogsQuery->whereDate('recruitment_activity_logs.created_at', '<=', $request->activity_end_date);
        }

        $activityLogs = $activityLogsQuery->orderBy('recruitment_activity_logs.created_at', 'desc')
            ->paginate(10, ['*'], 'activity_page');

        // ============ GET PERMISSIONS ============
        $permissions = $this->getUserPermissions();
        $jobPermissions = \App\Helpers\PermissionHelper::getPermissions('Manage Jobs');
        $shortlistPermissions = \App\Helpers\PermissionHelper::getPermissions('Shortlist Candidates');
        
        return view('hrms.Jobs.recruitment.index', compact(
            'candidates', 
            'positions', 
            'permissions',
            'shortlistedCandidates',
            'shortlistPositions',
            'shortlistPermissions',
            'onboardings', // Add this
            'manageJobs',
            'departments',
            'jobPermissions',
            'jobVacancyRequests',
            'employees',
            'selectedCandidates',
            'stats',
            'activityLogs'
        ));
    }
    
    /**
     * Get user permissions for recruitment actions
     */
    private function getUserPermissions()
    {
        // This is a placeholder - implement based on your permission system
        return (object)[
            'can_edit' => true,
            'can_delete' => true,
            'can_view_resume' => true,
            'can_update_status' => true,
        ];
    }
    
    // ============ SHORTLIST SPECIFIC METHODS ============
    
    /**
     * Schedule interview for shortlisted candidate
     */
    public function scheduleInterview($id)
    {
        $candidate = Candidate::findOrFail($id);
        
        // Get job details based on candidate's applied position
        $job = DB::table('managejobs')
            ->where('job_title', $candidate->position_applied)
            ->first();
        
        // If no job found, create a default job ID
        if (!$job) {
            $job = (object)[
                'id' => 0,
                'job_title' => $candidate->position_applied,
                'department' => 'Not Specified'
            ];
        }
        
        // Get department from job
        $department = $job->department ?? null;
        
        // Get active employees from the same department
        $interviewers = collect();
        if ($department && $department != 'Not Specified') {
            $departmentRecord = DB::table('department')
                ->where('department', $department)
                ->first();
            
            if ($departmentRecord) {
                $interviewers = DB::table('allemployees')
                    ->where('department', $departmentRecord->id)
                    ->where('deleted_at', 0)
                    ->where('status', 'active')
                    ->select('id', 'firstname', 'lastname', 'email', 'employeeid')
                    ->get();
            }
        }
        
        // If no interviewers found in department, get all active employees
        if ($interviewers->isEmpty()) {
            $interviewers = DB::table('allemployees')
                ->where('deleted_at', 0)
                ->where('status', 'active')
                ->select('id', 'firstname', 'lastname', 'email', 'employeeid')
                ->orderBy('firstname')
                ->get();
        }
        
        return view('hrms.Jobs.shortlist.schedule-interview', compact('candidate', 'job', 'interviewers'));
    }
    
    /**
     * Store interview details
     */
    public function storeInterview(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_id' => 'required|exists:managejobs,id',
            'interviewer_employee_id' => 'required|exists:allemployees,id',
            'interview_datetime' => 'required|date|after:now',
            'interview_type' => 'required|in:telephonic,face_to_face,video_call',
            'interview_round' => 'required|in:hr_interview_status,technical_interview_status,manager_round_status,final_round_status',
            'availability_date' => 'required|date',
            'availability_time_slot' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        // Get interviewer details from employee master
        $interviewer = DB::table('allemployees')
            ->where('id', $request->interviewer_employee_id)
            ->first();
        
        if (!$interviewer) {
            return redirect()->back()->with('error', 'Interviewer not found.');
        }
        
        $interviewerName = $interviewer->firstname . ' ' . $interviewer->lastname;
        $interviewerEmail = $interviewer->email;
        
        $branchId = session('branch_id');
        
        Interview::create([
            'candidate_id' => $request->candidate_id,
            'job_id' => $request->job_id,
            'interviewer_name' => $interviewerName,
            'interviewer_email' => $interviewerEmail,
            'interviewer_employee_id' => $request->interviewer_employee_id,
            'interview_datetime' => $request->interview_datetime,
            'interview_type' => $request->interview_type,
            'interview_round' => $request->interview_round,
            'availability_date' => $request->availability_date,
            'availability_time_slot' => $request->availability_time_slot,
            'branch_id' => $branchId,
            'notes' => $request->notes,
            'status' => 'scheduled',
            'feedback_submitted' => false
        ]);
        
        // Update candidate status
        $candidate = Candidate::find($request->candidate_id);
        $candidate->update([
            $request->interview_round => 'scheduled',
            'status' => $request->interview_type === 'telephonic'
                ? 'telephonic_scheduled'
                : 'interview_scheduled'
        ]);

        // Log activity
        $this->logActivity($request->candidate_id, 'Interview Scheduled', 
            ucwords(str_replace(['_', 'status'], [' ', ''], $request->interview_round)) . ' scheduled on ' . $request->interview_datetime);
        
        try {
            $roundLabel = ucwords(str_replace(['_', 'status'], [' ', ''], $request->interview_round));
            Mail::raw(
                "Dear {$candidate->first_name},\n\nYour {$roundLabel} interview has been scheduled.\n\nDate & Time: {$request->interview_datetime}\nType: " . ucfirst(str_replace('_', ' ', $request->interview_type)) . "\nInterviewer: {$interviewerName}\n\nBest Regards,\nHR Team",
                function ($message) use ($candidate, $roundLabel) {
                    $message->to($candidate->email)
                        ->subject($roundLabel . ' Interview Scheduled');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send interview scheduled email: ' . $e->getMessage());
        }
        
        return redirect()->route('recruitment.index', ['tab' => 'add-resume'])->with('success', 'Interview scheduled successfully!');
    }
    
    /**
     * Update interview status
     */
    public function updateInterviewStatus(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'rating' => $request->rating,
            'feedback' => $request->feedback
        ];
        
        if ($request->has('total_marks')) {
            $updateData['total_marks'] = $request->total_marks;
        }
        
        $interview->update($updateData);
        
        // Update candidate status
        $candidate = $interview->candidate;
        if ($request->status === 'completed') {
            $roundUpdate = [];
            if ($interview->interview_round) {
                $roundUpdate[$interview->interview_round] = 'completed';
            }
            $roundUpdate['status'] = $interview->interview_type === 'telephonic'
                ? 'telephonic_completed'
                : 'interview_completed';
            $candidate->update($roundUpdate);
        }
        
        // Send Email to candidate
        try {
            $job = DB::table('managejobs')->where('id', $interview->job_id)->first();
            $jobTitle = $job ? $job->job_title : 'the position';
            $subject = 'Interview Status Update: ' . ucfirst($request->status);
            
            $body = "Dear {$candidate->first_name},\n\nYour interview status for {$jobTitle} has been updated to: " . ucfirst($request->status) . ".\n\n";
            
            if ($request->status === 'completed') {
                if ($request->has('total_marks') && $request->total_marks != '') {
                    $body .= "Total Marks: {$request->total_marks}\n\n";
                }
                $body .= "Thank you for attending the interview. We will get back to you shortly.\n\n";
            } elseif ($request->status === 'cancelled') {
                $body .= "We regret to inform you that your interview has been cancelled.\n\n";
            } elseif ($request->status === 'rescheduled') {
                $body .= "Your interview has been rescheduled. We will contact you with the new details.\n\n";
            } else {
                $body .= "Interview Details:\nDate & Time: {$interview->interview_datetime}\nType: " . ucfirst(str_replace('_', ' ', $interview->interview_type)) . "\nInterviewer: {$interview->interviewer_name}\n\n";
            }
            
            $body .= "Best Regards,\nHR Team";

            Mail::raw($body, function ($message) use ($candidate, $subject) {
                $message->to($candidate->email)->subject($subject);
            });

            Log::info('Interview status email sent successfully to: ' . $candidate->email);
        } catch (\Exception $e) {
            Log::error('Failed to send interview status email: ' . $e->getMessage());
        }
        
        return response()->json(['success' => true, 'message' => 'Interview status updated successfully!']);
    }
    
    /**
     * View resume for shortlisted candidate
     */
    public function viewResume($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            
            if (!$candidate->resume_path) {
                return redirect()->route('recruitment.index', ['tab' => 'shortlist'])->with('error', 'Resume file not found.');
            }
            
            $possiblePaths = [
                storage_path('app/public/' . $candidate->resume_path),
                storage_path('app/' . $candidate->resume_path),
                public_path('storage/' . $candidate->resume_path),
                public_path($candidate->resume_path)
            ];
            
            $filePath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }
            
            if (!$filePath) {
                return redirect()->route('recruitment.index', ['tab' => 'shortlist'])->with('error', 'Resume file not found on server.');
            }
            
            $fileName = basename($filePath);
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);
            
            $isDownload = request()->has('download') && request()->get('download') == '1';
            
            $candidateName = $candidate->first_name . '_' . $candidate->last_name;
            $downloadFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidateName) . '_Resume.' . $fileExtension;
            
            if ($isDownload) {
                return response()->download($filePath, $downloadFileName, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'attachment; filename="' . $downloadFileName . '"'
                ]);
            } else {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $downloadFileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->route('recruitment.index', ['tab' => 'shortlist'])->with('error', 'Error accessing resume file: ' . $e->getMessage());
        }
    }
    
    /**
     * Get MIME type for file extension
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    /**
     * Get recruitment dashboard statistics
     */
    public function getDashboardStats()
    {
        $branchId = getAdminBranchFilter();
        
        $query = DB::table('candidates');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $stats = [
            'total_candidates' => $query->count(),
            'interviews_scheduled' => DB::table('interviews')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'scheduled')->count(),
            'interviews_completed' => DB::table('interviews')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'completed')->count(),
            'candidates_selected' => (clone $query)->where('status', 'selected')->count(),
            'candidates_rejected' => (clone $query)->where('status', 'rejected')->count(),
            'offers_pending_approval' => DB::table('candidate_offer_approvals')
                ->whereIn('offer_status', ['pending_manager', 'pending_hr'])->count(),
            'offers_sent' => DB::table('candidate_offer_approvals')
                ->where('offer_status', 'offer_sent')->count(),
            'offers_accepted' => DB::table('candidate_offer_approvals')
                ->where('offer_status', 'offer_accepted')->count(),
            'offers_rejected' => DB::table('candidate_offer_approvals')
                ->where('offer_status', 'offer_rejected')->count()
        ];
        
        return response()->json($stats);
    }
    
    public function selectCandidate($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status' => 'selected']);
        
        $this->logActivity($candidate->id, 'Selected', 'Candidate selected for salary and offer letter.');
        
        return redirect()->back()->with('success', 'Candidate selected successfully!');
    }

    public function rejectCandidate(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update([
            'status' => 'rejected',
            'rejection_remarks' => $request->input('remarks')
        ]);
        
        $this->logActivity($candidate->id, 'Rejected', 'Candidate rejected. Remarks: ' . $request->input('remarks'));
        
        return redirect()->back()->with('success', 'Candidate rejected.');
    }

    public function setSalaryForm($candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        
        // Fetch existing salary structure if already set
        $salaryStructure = DB::table('candidate_salary_structures')
            ->where('candidate_id', $candidateId)
            ->first();
            
        return view('hrms.Jobs.recruitment.set-salary', compact('candidate', 'salaryStructure'));
    }

    public function storeSalary(Request $request, $candidateId)
    {
        $request->validate([
            'gross_salary' => 'required|numeric|min:0',
            'basic_salary' => 'required|numeric|min:0',
            'da_amount' => 'nullable|numeric|min:0',
            'hra_amount' => 'nullable|numeric|min:0',
            'conveyance' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'pf_amount' => 'nullable|numeric|min:0',
            'esi_amount' => 'nullable|numeric|min:0',
            'professional_tax' => 'nullable|numeric|min:0',
            'welfare_fund' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
        ]);

        DB::table('candidate_salary_structures')->updateOrInsert(
            ['candidate_id' => $candidateId],
            [
                'gross_salary' => $request->gross_salary,
                'basic_salary' => $request->basic_salary,
                'da_amount' => $request->da_amount ?? 0,
                'hra_amount' => $request->hra_amount ?? 0,
                'conveyance' => $request->conveyance ?? 0,
                'special_allowance' => $request->special_allowance ?? 0,
                'medical_allowance' => $request->medical_allowance ?? 0,
                'pf_amount' => $request->pf_amount ?? 0,
                'esi_amount' => $request->esi_amount ?? 0,
                'professional_tax' => $request->professional_tax ?? 0,
                'welfare_fund' => $request->welfare_fund ?? 0,
                'tds' => $request->tds ?? 0,
                'net_salary' => $request->net_salary,
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())')
            ]
        );

        $this->logActivity($candidateId, 'Salary Configured', 'Salary structure set for candidate. Gross: ' . $request->gross_salary);

        return redirect()->route('recruitment.index', ['tab' => 'offer-letter'])->with('success', 'Salary structure saved successfully!');
    }

    public function viewSalary($candidateId)
    {
        $salary = DB::table('candidate_salary_structures')
            ->where('candidate_id', $candidateId)
            ->first();
            
        if (!$salary) {
            return response()->json(['success' => false, 'message' => 'Salary structure not set.']);
        }
        
        return response()->json(['success' => true, 'data' => $salary]);
    }

    public function sendOfferLetter($candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);
        
        $template = OfferLetter::where('is_active', true)->first();
        if (!$template) {
            return redirect()->back()->with('error', 'No active offer letter template found. Please create and activate one in Settings first.');
        }

        $salary = DB::table('candidate_salary_structures')
            ->where('candidate_id', $candidateId)
            ->first();
            
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary structure is not configured for this candidate.');
        }

        try {
            $settings = $this->getCompanySettings();
            
            $employee = (object)[
                'firstname' => $candidate->first_name,
                'lastname' => $candidate->last_name,
                'email' => $candidate->email,
                'position_applied' => $candidate->position_applied,
            ];
            
            $annualGross = $salary->gross_salary * 12;
            $annualBasic = $salary->basic_salary * 12;
            $annualDa = $salary->da_amount * 12;
            $annualHra = $salary->hra_amount * 12;
            $annualConveyance = $salary->conveyance * 12;
            $annualSpecial = $salary->special_allowance * 12;
            $annualMedical = $salary->medical_allowance * 12;
            $annualPf = $salary->pf_amount * 12;
            $annualEsi = $salary->esi_amount * 12;
            $annualProfTax = $salary->professional_tax * 12;
            $annualWelfare = $salary->welfare_fund * 12;
            $annualTds = $salary->tds * 12;
            $annualNet = $salary->net_salary * 12;

            $ctcMonthly = $salary->gross_salary;
            $ctcAnnual = $ctcMonthly * 12;
            $ctcWords = $this->numberToWords($ctcAnnual);

            $appointment = (object)[
                'designation' => $candidate->position_applied ?? 'Role',
                'joining_date' => now()->addDays(15)->format('d/m/Y'),
                'work_location' => $candidate->address ?? 'Office',
                'department' => $candidate->department ?? 'Development',
                'employment_type' => 'Full Time',
                'reporting_to' => 'HR Manager',
                'probation_period' => '6 Months',
                'notice_period' => '30 Days',
                
                'annual_ctc' => number_format($ctcAnnual, 2),
                'ctc_annual' => number_format($ctcAnnual, 2),
                'ctc_words' => $ctcWords,
                
                'basic_monthly' => number_format($salary->basic_salary, 2),
                'basic_annual' => number_format($annualBasic, 2),
                
                'hra_monthly' => number_format($salary->hra_amount, 2),
                'hra_annual' => number_format($annualHra, 2),
                
                'cca_monthly' => number_format($salary->conveyance, 2),
                'cca_annual' => number_format($annualConveyance, 2),
                
                'special_allowance_monthly' => number_format($salary->special_allowance, 2),
                'special_allowance_annual' => number_format($annualSpecial, 2),
                
                'statutory_bonus_monthly' => number_format($salary->medical_allowance, 2),
                'statutory_bonus_annual' => number_format($annualMedical, 2),
                
                'training_allowance_monthly' => '0.00',
                'training_allowance_annual' => '0.00',
                'vpp_monthly' => '0.00',
                'vpp_annual' => '0.00',
                'pf_employer_monthly' => '0.00',
                'pf_employer_annual' => '0.00',
                'esi_employer_monthly' => '0.00',
                'esi_employer_annual' => '0.00',
                
                'gross_monthly' => number_format($salary->gross_salary, 2),
                'gross_annual' => number_format($annualGross, 2),
                
                'pf_employee_monthly' => number_format($salary->pf_amount, 2),
                'pf_employee_annual' => number_format($annualPf, 2),
                
                'esi_employee_monthly' => number_format($salary->esi_amount, 2),
                'esi_employee_annual' => number_format($annualEsi, 2),
                
                'staff_welfare_monthly' => number_format($salary->welfare_fund, 2),
                'staff_welfare_annual' => number_format($annualWelfare, 2),
                
                'prof_tax_monthly' => number_format($salary->professional_tax, 2),
                'prof_tax_annual' => number_format($annualProfTax, 2),
                
                'tds_monthly' => number_format($salary->tds, 2),
                'tds_annual' => number_format($annualTds, 2),
                
                'net_income_monthly' => number_format($salary->net_salary, 2),
                'net_income_annual' => number_format($annualNet, 2),
                
                'ctc_monthly' => number_format($ctcMonthly, 2),
            ];

            $data = array_merge($settings, $this->getOfferSignatureData(), compact('employee', 'appointment'));
            if (!empty($settings['logo'])) {
                $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
                $data['logo'] = $settings['logo'];
            }

            $html = $this->renderBladeString($template->content, $data);
            $pdfBinary = $this->pdfFromHtml($html);

            // Send mail using OfferLetterMail
            Mail::to($candidate->email)->send(new OfferLetterMail($employee, $appointment, $pdfBinary, $settings));

            // Mark offer sent
            DB::table('candidate_salary_structures')
                ->where('candidate_id', $candidateId)
                ->update([
                    'offer_letter_sent' => true,
                    'offer_letter_sent_at' => now()
                ]);

            $this->logActivity($candidateId, 'Offer Letter Sent', 'Offer letter successfully generated and sent to ' . $candidate->email);

            return redirect()->back()->with('success', 'Offer letter sent successfully to ' . $candidate->email);

        } catch (\Exception $e) {
            Log::error('Failed to send offer letter: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending offer letter: ' . $e->getMessage());
        }
    }

    private function getCompanySettings()
    {
        $generalSettings = GeneralSetting::first();
        $logoSetting = LogoSetting::first();
        $logo = null;
        
        if ($logoSetting && !empty($logoSetting->logo)) {
            $logo = $logoSetting->logo;
        }
        
        if ($logo && !file_exists(public_path($logo))) {
            $possiblePaths = [
                $logo,
                'uploads/' . $logo,
                'storage/' . $logo,
                'images/' . $logo,
                'assets/images/' . $logo,
                'logo/' . $logo,
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists(public_path($path))) {
                    $logo = $path;
                    break;
                }
            }
            if (!file_exists(public_path($logo))) {
                $logo = null;
            }
        }

        return [
            'companyName' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail' => $generalSettings->contact_email ?? '',
            'companyPhone' => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->contact_address ?? '',
            'gm_name' => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title' => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo' => $logo,
        ];
    }

    private function getOfferSignatureData(): array
    {
        $signatureData = [
            'offerSignaturePath' => null,
            'offerSignaturePublicPath' => null,
            'offerSignatureDataUri' => null,
        ];

        if (!Schema::hasTable('letter_signatures')) {
            return $signatureData;
        }

        $signature = DB::table('letter_signatures')
            ->where('letter_type', 'offer_letter')
            ->first();

        if (!$signature || empty($signature->signature_path)) {
            return $signatureData;
        }

        $publicPath = public_path($signature->signature_path);

        if (!File::exists($publicPath)) {
            return $signatureData;
        }

        $mimeType = $signature->mime_type ?: File::mimeType($publicPath);

        return [
            'offerSignaturePath' => $signature->signature_path,
            'offerSignaturePublicPath' => $publicPath,
            'offerSignatureDataUri' => 'data:' . $mimeType . ';base64,' . base64_encode(File::get($publicPath)),
        ];
    }

    private function injectOfferSignature(string $html, ?string $signatureDataUri): string
    {
        if (
            empty($signatureDataUri) ||
            strpos($html, 'offer-signature-img') !== false ||
            strpos($html, 'authorized-signature') !== false
        ) {
            return $html;
        }

        $signatureHtml = '<style>.dynamic-signature + .sig-name{margin-top:4px !important;}</style>'
            . '<div class="dynamic-signature" style="height:46px;margin:8px 0 0;display:flex;align-items:flex-end;">'
            . '<img class="offer-signature-img" src="' . $signatureDataUri . '" alt="Authorized Signature" style="max-height:44px;max-width:170px;object-fit:contain;display:block;">'
            . '</div>';

        $updatedHtml = preg_replace('/(<div\s+class=(["\'])sig-name\2[^>]*>)/i', $signatureHtml . '$1', $html, 1);

        return $updatedHtml ?: $html;
    }

    protected function prepareTemplateForRender(string $rawContent): string
    {
        $content = str_replace('@{{', '{{', $rawContent);
        $content = str_replace('}}', '}}', $content);
        return $content;
    }

    protected function renderBladeString(string $content, array $data = []): string
    {
        $content = $this->prepareTemplateForRender($content);
        $html = Blade::render($content, $data);

        return $this->injectOfferSignature($html, $data['offerSignatureDataUri'] ?? null);
    }

    protected function pdfFromHtml(string $html)
    {
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->output();
    }

    private function numberToWords($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . ($paise ? 'and ' . $paise : '') . 'Only';
    }

    /**
     * Log recruitment activity
     */
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
