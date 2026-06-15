<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class InterviewProcessController extends Controller
{
    public function index()
    {
        $departments = DB::table('department')->get();
        return view('hrms.hr.Reports.Interview-Process.interview-process', compact('departments'));
    }

    public function getJobPostingReport(Request $request)
    {
        try {
            $query = DB::table('managejobs');

            if ($request->filled('job_title')) {
                $query->where('job_title', 'like', '%'.$request->job_title.'%');
            }
            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }
            if ($request->filled('location')) {
                $query->where('job_location', $request->location);
            }
            if ($request->filled('date_posted')) {
                $query->whereDate('created_at', $request->date_posted);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = $query->count();

            if ($request->has('start') && $request->has('length')) {
                $query->offset($request->start)->limit($request->length);
            }

            if ($request->has('order')) {
                $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                $orderDirection = $request->order[0]['dir'];
                $query->orderBy($orderColumn, $orderDirection);
            }

            $jobs = $query->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $jobs
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getJobPostingReport: '.$e->getMessage());
            return response()->json([
                'draw' => $request->draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportJobPosting(Request $request)
    {
        try {
            $query = DB::table('managejobs');

            if ($request->filled('job_title')) {
                $query->where('job_title', 'like', '%'.$request->job_title.'%');
            }
            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }
            if ($request->filled('location')) {
                $query->where('job_location', $request->location);
            }
            if ($request->filled('date_posted')) {
                $query->whereDate('created_at', $request->date_posted);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $jobs = $query->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="job_posting_report_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($jobs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Job Title','Department','Location','Vacancies','Posted Date','End Date','Status']);
                foreach ($jobs as $job) {
                    $postedDate = $job->created_at ? date('d/m/Y', strtotime($job->created_at)) : '';
                    $endDate = $job->end_date ? date('d/m/Y', strtotime($job->end_date)) : '';
                    fputcsv($file, [
                        $job->job_title,
                        $job->department,
                        $job->job_location,
                        $job->vacancies,
                        $postedDate,
                        $endDate,
                        $job->status
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportJobPosting: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getInterviewSchedulingReport(Request $request)
    {
        try {
            $query = DB::table('interviews')
                ->join('candidates', 'interviews.candidate_id', '=', 'candidates.id')
                ->join('managejobs', 'interviews.job_id', '=', 'managejobs.id')
                ->select(
                    'managejobs.job_title',
                    DB::raw("CONCAT(candidates.first_name, ' ', candidates.last_name) as candidate_name"),
                    'interviews.interviewer_name',
                    'interviews.interview_type',
                    'interviews.interview_datetime',
                    'interviews.status',
                    'interviews.rating',
                    'interviews.notes'
                );

            if ($request->filled('job_title')) {
                $query->where('managejobs.job_title', 'like', '%'.$request->job_title.'%');
            }
            if ($request->filled('candidate_name')) {
                $query->where(DB::raw("CONCAT(candidates.first_name, ' ', candidates.last_name)"), 'like', '%'.$request->candidate_name.'%');
            }
            if ($request->filled('interviewer_name')) {
                $query->where('interviews.interviewer_name', 'like', '%'.$request->interviewer_name.'%');
            }
            if ($request->filled('interview_mode')) {
                $query->where('interviews.interview_type', $request->interview_mode);
            }
            if ($request->filled('interview_date_from')) {
                $query->whereDate('interviews.interview_datetime', '>=', $request->interview_date_from);
            }
            if ($request->filled('interview_date_to')) {
                $query->whereDate('interviews.interview_datetime', '<=', $request->interview_date_to);
            }
            if ($request->filled('interview_status')) {
                $query->where('interviews.status', $request->interview_status);
            }

            $totalRecords = $query->count();

            if ($request->has('start') && $request->has('length')) {
                $query->offset($request->start)->limit($request->length);
            }

            if ($request->has('order')) {
                $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                $orderDirection = $request->order[0]['dir'];
                $query->orderBy($orderColumn, $orderDirection);
            }

            $interviews = $query->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $interviews
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getInterviewSchedulingReport: '.$e->getMessage());
            return response()->json([
                'draw' => $request->draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportInterviewScheduling(Request $request)
    {
        try {
            $query = DB::table('interviews')
                ->join('candidates', 'interviews.candidate_id', '=', 'candidates.id')
                ->join('managejobs', 'interviews.job_id', '=', 'managejobs.id')
                ->select(
                    'managejobs.job_title',
                    DB::raw("CONCAT(candidates.first_name, ' ', candidates.last_name) as candidate_name"),
                    'interviews.interviewer_name',
                    'interviews.interview_type',
                    'interviews.interview_datetime',
                    'interviews.status'
                );

            if ($request->filled('job_title')) {
                $query->where('managejobs.job_title', 'like', '%'.$request->job_title.'%');
            }
            if ($request->filled('candidate_name')) {
                $query->where(DB::raw("CONCAT(candidates.first_name, ' ', candidates.last_name)"), 'like', '%'.$request->candidate_name.'%');
            }
            if ($request->filled('interviewer_name')) {
                $query->where('interviews.interviewer_name', 'like', '%'.$request->interviewer_name.'%');
            }
            if ($request->filled('interview_mode')) {
                $query->where('interviews.interview_type', $request->interview_mode);
            }
            if ($request->filled('interview_date_from')) {
                $query->whereDate('interviews.interview_datetime', '>=', $request->interview_date_from);
            }
            if ($request->filled('interview_date_to')) {
                $query->whereDate('interviews.interview_datetime', '<=', $request->interview_date_to);
            }
            if ($request->filled('interview_status')) {
                $query->where('interviews.status', $request->interview_status);
            }

            $interviews = $query->get();

            $filename = 'interview_scheduling_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($interviews) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Job Title','Candidate Name','Interviewer Name','Interview Mode','Interview Date','Status']);
                foreach ($interviews as $interview) {
                    $interviewDate = $interview->interview_datetime ? date('d/m/Y H:i', strtotime($interview->interview_datetime)) : '';
                    fputcsv($file, [
                        $interview->job_title,
                        $interview->candidate_name,
                        $interview->interviewer_name,
                        ucwords(str_replace('_', ' ', $interview->interview_type)),
                        $interviewDate,
                        ucfirst($interview->status)
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportInterviewScheduling: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getResumeManagementReport(Request $request)
    {
        try {
            $query = DB::table('candidates');

            if ($request->filled('position_applied')) {
                $query->where('position_applied', 'like', '%'.$request->position_applied.'%');
            }
            if ($request->filled('candidate_name')) {
                $query->where(function($q) use ($request) {
                    $q->where('first_name', 'like', '%'.$request->candidate_name.'%')
                      ->orWhere('last_name', 'like', '%'.$request->candidate_name.'%');
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = $query->count();

            if ($request->has('start') && $request->has('length')) {
                $query->offset($request->start)->limit($request->length);
            }

            if ($request->has('order')) {
                $orderColumn = $request->columns[$request->order[0]['column']]['data'];
                $orderDirection = $request->order[0]['dir'];
                if ($orderColumn === 'full_name') {
                    $query->orderBy('first_name', $orderDirection)
                          ->orderBy('last_name', $orderDirection);
                } else {
                    $query->orderBy($orderColumn, $orderDirection);
                }
            }

            $resumes = $query->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $resumes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getResumeManagementReport: '.$e->getMessage());
            return response()->json([
                'draw' => $request->draw ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportResumeManagement(Request $request)
    {
        try {
            $query = Candidate::query();

            if ($request->filled('position_applied')) {
                $query->where('position_applied', 'like', '%'.$request->position_applied.'%');
            }
            if ($request->filled('candidate_name')) {
                $query->where(function($q) use ($request) {
                    $q->where('first_name', 'like', '%'.$request->candidate_name.'%')
                      ->orWhere('last_name', 'like', '%'.$request->candidate_name.'%');
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $resumes = $query->get();

            $filename = 'resume_management_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($resumes) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Job Title','Candidate Name','Email','Phone','Date Received','Status','Resume Available']);
                foreach ($resumes as $resume) {
                    $dateReceived = $resume->created_at ? date('d/m/Y', strtotime($resume->created_at)) : '';
                    fputcsv($file, [
                        $resume->position_applied,
                        $resume->first_name.' '.$resume->last_name,
                        $resume->email,
                        $resume->phone,
                        $dateReceived,
                        ucwords(str_replace('_', ' ', $resume->status)),
                        $resume->resume_path ? 'Yes' : 'No'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportResumeManagement: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    // NEW: Candidate Shortlisting (ONLY status = shortlisted) with filters
    public function getCandidateShortlistingReport(Request $request)
    {
        try {
            $base = DB::table('candidates')->where('status', 'shortlisted');

            // Filters
            if ($request->filled('position_applied')) {
                $base->where('position_applied', 'like', '%'.$request->position_applied.'%');
            }
            if ($request->filled('candidate_name')) {
                $name = $request->candidate_name;
                $base->where(function($q) use ($name) {
                    $q->where('first_name', 'like', '%'.$name.'%')
                      ->orWhere('last_name', 'like', '%'.$name.'%');
                });
            }
            if ($request->filled('date_from')) {
                $base->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $base->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('has_resume')) {
                if ($request->has_resume === 'yes') {
                    $base->whereNotNull('resume_path')->where('resume_path', '!=', '');
                } elseif ($request->has_resume === 'no') {
                    $base->where(function($q){
                        $q->whereNull('resume_path')->orWhere('resume_path', '');
                    });
                }
            }

            $totalRecords = $base->count();

            // Clone for data retrieval
            $query = DB::table('candidates')->where('status', 'shortlisted');

            // Reapply filters to the data query
            if ($request->filled('position_applied')) {
                $query->where('position_applied', 'like', '%'.$request->position_applied.'%');
            }
            if ($request->filled('candidate_name')) {
                $name = $request->candidate_name;
                $query->where(function($q) use ($name) {
                    $q->where('first_name', 'like', '%'.$name.'%')
                      ->orWhere('last_name', 'like', '%'.$name.'%');
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('has_resume')) {
                if ($request->has_resume === 'yes') {
                    $query->whereNotNull('resume_path')->where('resume_path', '!=', '');
                } elseif ($request->has_resume === 'no') {
                    $query->where(function($q){
                        $q->whereNull('resume_path')->orWhere('resume_path', '');
                    });
                }
            }

            // Ordering
            if ($request->has('order')) {
                $orderColumn = $request->columns[$request->order[0]['column']]['data'] ?? 'created_at';
                $orderDirection = $request->order[0]['dir'] ?? 'desc';
                if ($orderColumn === 'full_name') {
                    $query->orderBy('first_name', $orderDirection)
                          ->orderBy('last_name', $orderDirection);
                } else {
                    $query->orderBy($orderColumn, $orderDirection);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            if ($request->has('start') && $request->has('length')) {
                $query->offset((int)$request->start)->limit((int)$request->length);
            }

            $rows = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCandidateShortlistingReport: '.$e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    // NEW: CSV export for shortlisted candidates with the same filters
    public function exportCandidateShortlisting(Request $request)
    {
        try {
            $query = DB::table('candidates')->where('status', 'shortlisted');

            if ($request->filled('position_applied')) {
                $query->where('position_applied', 'like', '%'.$request->position_applied.'%');
            }
            if ($request->filled('candidate_name')) {
                $name = $request->candidate_name;
                $query->where(function($q) use ($name) {
                    $q->where('first_name', 'like', '%'.$name.'%')
                      ->orWhere('last_name', 'like', '%'.$name.'%');
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('has_resume')) {
                if ($request->has_resume === 'yes') {
                    $query->whereNotNull('resume_path')->where('resume_path', '!=', '');
                } elseif ($request->has_resume === 'no') {
                    $query->where(function($q){
                        $q->whereNull('resume_path')->orWhere('resume_path', '');
                    });
                }
            }

            $rows = $query->orderBy('created_at', 'desc')->get();

            $filename = 'candidate_shortlisting_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($rows) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM
                fputcsv($out, ['Job Title','Candidate Name','Email','Phone','Date Received','Status','Resume Available']);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->position_applied,
                        trim(($r->first_name ?? '').' '.($r->last_name ?? '')),
                        $r->email,
                        $r->phone,
                        $r->created_at ? date('d/m/Y', strtotime($r->created_at)) : '',
                        'Shortlisted',
                        $r->resume_path ? 'Yes' : 'No',
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportCandidateShortlisting: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }


    public function viewResume($id)
    {
        try {
            // Get the candidate record
            $candidate = Candidate::findOrFail($id);

            if (!$candidate->resume_path) {
                Log::error('Resume path is empty', ['candidate_id' => $id]);
                return redirect()->back()->with('error', 'Resume file not found.');
            }

            // Try multiple possible file paths
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

            // If file not found in any of the paths, log the issue
            if (!$filePath) {
                Log::error('Resume file not found in any path', [
                    'candidate_id' => $id,
                    'resume_path' => $candidate->resume_path,
                    'checked_paths' => $possiblePaths
                ]);
                return redirect()->back()->with('error', 'Resume file not found on server.');
            }

            // Get file info
            $fileName = basename($filePath);
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);

            // Check if download is requested
            $isDownload = request()->has('download') && request()->get('download') == '1';

            // Generate a proper filename for download
            $candidateName = $candidate->first_name . '_' . $candidate->last_name;
            $downloadFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidateName) . '_Resume.' . $fileExtension;

            Log::info('Serving resume file from interview reports', [
                'candidate_id' => $id,
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'mime_type' => $mimeType,
                'is_download' => $isDownload
            ]);

            // Return file response with proper headers
            if ($isDownload) {
                // Force download
                return Response::download($filePath, $downloadFileName, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'attachment; filename="' . $downloadFileName . '"'
                ]);
            } else {
                // Display in browser (for PDFs and images)
                return Response::file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $downloadFileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error serving resume file from interview reports', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error accessing resume file: ' . $e->getMessage());
        }
    }

    /**
     * Get MIME type based on file extension
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



}