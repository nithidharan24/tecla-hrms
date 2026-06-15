<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
 
class ShortlistController extends Controller
{
    public function index(Request $request)
    {
        $branchId = getAdminBranchFilter();
        
        $query = Candidate::whereIn('status', [
            'shortlisted',
            'telephonic_scheduled',
            'telephonic_completed',
            'interview_scheduled',
            'interview_completed'
        ])->with('interviews');

        // Apply branch filter
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('position_applied', 'LIKE', "%{$search}%");
            });
        }

        // Apply position filter
        if ($request->has('position') && $request->position != '') {
            $query->where('position_applied', $request->position);
        }

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Apply interview status filter
        if ($request->has('interview_status') && $request->interview_status != '') {
            if ($request->interview_status === 'no_interview') {
                $query->whereDoesntHave('interviews');
            } else {
                $query->whereHas('interviews', function($q) use ($request) {
                    $q->where('status', $request->interview_status);
                });
            }
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Get unique positions for filter dropdown
        $positions = Candidate::whereIn('status', [
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

        $shortlistedCandidates = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('hrms.Jobs.shortlist.index', compact('shortlistedCandidates', 'positions'));
    }

    // ... rest of your existing methods remain the same ...
 public function scheduleInterview($id)
{
    $candidate = Candidate::findOrFail($id);

    $job = DB::table('managejobs')
        ->where('job_title', $candidate->position_applied)
        ->first();

    if (!$job) {
        $job = (object)[
            'id' => 0,
            'job_title' => $candidate->position_applied,
            'department' => 'Not Specified'
        ];
    }

    $interviews = Interview::where('candidate_id', $id)->orderBy('created_at', 'asc')->get();

    return view(
        'hrms.Jobs.shortlist.schedule-interview',
        compact('candidate', 'job', 'interviews')
    );
}

public function getInterviewersByRound(Request $request)
{
    try {
        Log::info('getInterviewersByRound called', [
            'round' => $request->round,
            'job_department' => $request->job_department
        ]);
        
        $round = $request->round;
        $jobDepartment = $request->job_department;
        
        $interviewers = collect();
        
        if ($round === 'hr_interview_status') {
            Log::info('Fetching HR interviewers');
            
            $hrDept = DB::table('department')
                ->where('department', 'LIKE', '%HR%')
                ->orWhere('department', 'LIKE', '%Human Resource%')
                ->first();
            
            Log::info('HR Department found', ['dept' => $hrDept]);
            
            if ($hrDept) {
                $interviewers = DB::table('allemployees')
                    ->where('department', $hrDept->id)
                    ->where('status', 'active')
                    ->where('deleted_at', 0)
                    ->select('id', 'firstname', 'lastname', 'email', 'employeeid')
                    ->get();
                    
                Log::info('HR Interviewers fetched', ['count' => $interviewers->count()]);
            }
        } elseif (in_array($round, ['technical_interview_status', 'manager_round_status'])) {
            Log::info('Fetching Technical/Manager interviewers', ['job_department' => $jobDepartment]);
            
            if ($jobDepartment && $jobDepartment != 'Not Specified') {
                $dept = DB::table('department')
                    ->where('department', $jobDepartment)
                    ->first();
                
                Log::info('Job Department found', ['dept' => $dept]);
                
                if ($dept) {
                    $employees = DB::table('allemployees')
                        ->where('department', $dept->id)
                        ->where('status', 'active')
                        ->where('deleted_at', 0)
                        ->select('id', 'firstname', 'lastname', 'email', 'employeeid', 'hierarchy_id')
                        ->get();
                    
                    Log::info('Employees fetched from department', ['count' => $employees->count()]);
                    
                    foreach ($employees as $emp) {
                        $empData = (object)[
                            'id' => $emp->id,
                            'firstname' => $emp->firstname,
                            'lastname' => $emp->lastname,
                            'email' => $emp->email,
                            'employeeid' => $emp->employeeid,
                            'role' => null
                        ];
                        
                        if (!empty($emp->hierarchy_id)) {
                           $hierarchy = DB::table('hierarchies')
    ->where('id', $emp->hierarchy_id)
    ->first();
                            
                            if ($hierarchy) {
                                $empData->role = $hierarchy->hierarchy_level;
                            }
                        }
                        
                        $interviewers->push($empData);
                    }
                    
                    Log::info('Interviewers prepared', ['count' => $interviewers->count()]);
                } else {
                    Log::warning('Department not found for job department', ['job_department' => $jobDepartment]);
                }
            } else {
                Log::warning('Job department is empty or Not Specified');
            }
        } elseif ($round === 'final_round_status') {
            Log::info('Fetching Final Round interviewers');
            
            $mgmtDept = DB::table('department')
                ->where('department', 'LIKE', '%Management%')
                ->orWhere('department', 'LIKE', '%Executive%')
                ->orWhere('department', 'LIKE', '%Director%')
                ->first();
            
            Log::info('Management Department found', ['dept' => $mgmtDept]);
            
            if ($mgmtDept) {
                $interviewers = DB::table('allemployees')
                    ->where('department', $mgmtDept->id)
                    ->where('status', 'active')
                    ->where('deleted_at', 0)
                    ->select('id', 'firstname', 'lastname', 'email', 'employeeid')
                    ->get();
            } else {
                Log::info('Management department not found, using hierarchy fallback');
                
                $employees = DB::table('allemployees')
                    ->where('status', 'active')
                    ->where('deleted_at', 0)
                    ->get();
                
                foreach ($employees as $emp) {
                    if (!empty($emp->hierarchy_id)) {
                   $hierarchy = DB::table('hierarchies')
    ->where('id', $emp->hierarchy_id)
    ->first();
                        
                        if ($hierarchy && in_array($hierarchy->hierarchy_level, ['CEO', 'Director', 'VP', 'General Manager'])) {
                            $interviewers->push((object)[
                                'id' => $emp->id,
                                'firstname' => $emp->firstname,
                                'lastname' => $emp->lastname,
                                'email' => $emp->email,
                                'employeeid' => $emp->employeeid,
                                'role' => $hierarchy->hierarchy_level
                            ]);
                        }
                    }
                }
            }
            
            Log::info('Final Round Interviewers fetched', ['count' => $interviewers->count()]);
        }
        
        // If no interviewers found, return all active employees as fallback
        if ($interviewers->isEmpty()) {
            Log::warning('No interviewers found, using fallback');
            
            $interviewers = DB::table('allemployees')
                ->where('status', 'active')
                ->where('deleted_at', 0)
                ->select('id', 'firstname', 'lastname', 'email', 'employeeid')
                ->orderBy('firstname')
                ->get();
                
            Log::info('Fallback interviewers', ['count' => $interviewers->count()]);
        }
        
        Log::info('Returning interviewers', ['total' => $interviewers->count()]);
        
        return response()->json([
            'success' => true,
            'interviewers' => $interviewers
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getInterviewersByRound', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error fetching interviewers: ' . $e->getMessage()
        ], 500);
    }
}

    public function storeInterview(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_id' => 'required',
            'interviewer_employee_id' => 'required',
            'interviewer_email' => 'required|email',
            'interview_datetime' => 'required|date|after:now',
            'interview_type' => 'required|in:telephonic,face_to_face,video_call',
            'interview_round' => 'required|in:hr_interview_status,technical_interview_status,manager_round_status,final_round_status',
            'availability_date' => 'required|date',
            'availability_time_slot' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $employee = DB::table('allemployees')->where('id', $request->interviewer_employee_id)->first();
        $interviewerName = $employee ? $employee->firstname . ' ' . $employee->lastname : 'Unknown';

        $branchId = Session::get('branch_id');
        Interview::create([
            'candidate_id' => $request->candidate_id,
            'job_id' => $request->job_id,
            'interviewer_name' => $interviewerName,
            'interviewer_email' => $request->interviewer_email,
            'interview_datetime' => $request->interview_datetime,
            'interview_type' => $request->interview_type,
            'interview_round' => $request->interview_round,
            'availability_date' => $request->availability_date,
            'availability_time_slot' => $request->availability_time_slot,
            'branch_id' => $branchId,
            'notes' => $request->notes,
            'status' => 'scheduled'
        ]);

        $candidate = Candidate::find($request->candidate_id);
        $candidate->update([
            $request->interview_round => 'scheduled',
            'status' => $request->interview_type === 'telephonic' ? 'telephonic_scheduled' : 'interview_scheduled'
        ]);

        try {
            $roundLabel = ucwords(str_replace(['_', 'status'], [' ', ''], $request->interview_round));
            $interviewType = ucfirst(str_replace('_', ' ', $request->interview_type));
            Mail::raw(
                "Dear {$candidate->first_name},\n\nYour {$roundLabel} interview has been scheduled.\n\nDate & Time: {$request->interview_datetime}\nType: {$interviewType}\nInterviewer: {$interviewerName}\n\nBest Regards,\nHR Team",
                function ($message) use ($candidate, $roundLabel) {
                    $message->to($candidate->email)->subject($roundLabel . ' Interview Scheduled');
                }
            );

            Mail::raw(
                "Dear {$interviewerName},\n\nAn interview has been scheduled with {$candidate->first_name} {$candidate->last_name}.\n\nRound: {$roundLabel}\nDate & Time: {$request->interview_datetime}\nType: {$interviewType}\nCandidate Email: {$candidate->email}\nPosition: {$candidate->position_applied}\n\nBest Regards,\nHR Team",
                function ($message) use ($request, $roundLabel) {
                    $message->to($request->interviewer_email)->subject($roundLabel . ' Interview Assigned');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send interview scheduled email: ' . $e->getMessage());
        }

        return redirect()->route('shortlist.schedule-interview', $request->candidate_id)->with('success', 'Interview scheduled successfully!');
    }

   public function updateInterviewStatus(Request $request, $id)
{
    $interview = Interview::findOrFail($id);

    $updateData = [
        'status' => $request->status,
    ];

    if ($request->filled('rating')) {
        $updateData['rating'] = $request->rating;
    }

    if ($request->filled('feedback')) {
        $updateData['feedback'] = $request->feedback;
    }

    if ($request->filled('total_marks')) {
        $updateData['total_marks'] = $request->total_marks;
    }

    $interview->update($updateData);

    // Update Candidate Status
    $candidate = $interview->candidate;

    if ($request->status === 'completed') {

        $roundUpdate = [];

        if (!empty($interview->interview_round)) {
            $roundUpdate[$interview->interview_round] = 'completed';
        }

        $roundUpdate['status'] =
            $interview->interview_type === 'telephonic'
            ? 'telephonic_completed'
            : 'interview_completed';

        $candidate->update($roundUpdate);
    }

    // Send Status Update Email
    try {

        $job = DB::table('managejobs')
            ->where('id', $interview->job_id)
            ->first();

        $jobTitle = $job ? $job->job_title : 'the position';

        $subject = 'Interview Status Update - ' . ucfirst($request->status);

        $body = "Dear {$candidate->first_name},\n\n";
        $body .= "Your interview status for {$jobTitle} has been updated.\n\n";
        $body .= "Current Status: " . ucfirst($request->status) . "\n\n";

        if ($request->status === 'completed') {
            $body .= "Thank you for attending the interview.\n\n";

        } elseif ($request->status === 'cancelled') {

            $body .= "We regret to inform you that your interview has been cancelled.\n\n";

        } elseif ($request->status === 'rescheduled') {

            $body .= "Your interview has been rescheduled. Updated details will be shared shortly.\n\n";

        } else {

            $body .= "Interview Date & Time: {$interview->interview_datetime}\n";
            $body .= "Interview Type: " . ucfirst(str_replace('_', ' ', $interview->interview_type)) . "\n";
            $body .= "Interviewer: {$interview->interviewer_name}\n\n";
        }

        $body .= "Best Regards,\nHR Team";

        Mail::raw($body, function ($message) use ($candidate, $subject) {
            $message->to($candidate->email)
                    ->subject($subject);
        });

        Log::info('Interview status email sent to: ' . $candidate->email);

    } catch (\Exception $e) {

        Log::error(
            'Failed to send interview status email: ' .
            $e->getMessage()
        );
    }

    $successMessage = ($request->has('feedback') || $request->has('rating') || $request->has('total_marks'))
        ? 'Interview feedback saved successfully!'
        : 'Interview status updated successfully!';

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => $successMessage
        ]);
    }

    return redirect()->back()->with(
        'success',
        $successMessage
    );
}
    public function viewResume($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);

            if (!$candidate->resume_path) {
                Log::error('Resume path is empty', ['candidate_id' => $id]);
                return redirect()->route('shortlist.index')->with('error', 'Resume file not found.');
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
                Log::error('Resume file not found in any path', [
                    'candidate_id' => $id,
                    'resume_path' => $candidate->resume_path,
                    'checked_paths' => $possiblePaths
                ]);
                return redirect()->route('shortlist.index')->with('error', 'Resume file not found on server.');
            }

            $fileName = basename($filePath);
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);

            $isDownload = request()->has('download') && request()->get('download') == '1';

            $candidateName = $candidate->first_name . '_' . $candidate->last_name;
            $downloadFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidateName) . '_Resume.' . $fileExtension;

            Log::info('Serving resume file', [
                'candidate_id' => $id,
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'mime_type' => $mimeType,
                'is_download' => $isDownload
            ]);

            if ($isDownload) {
                return Response::download($filePath, $downloadFileName, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'attachment; filename="' . $downloadFileName . '"'
                ]);
            } else {
                return Response::file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $downloadFileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error serving resume file', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('recruitment.index')->with('error', 'Error accessing resume file: ' . $e->getMessage());
        }
    }

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
}
