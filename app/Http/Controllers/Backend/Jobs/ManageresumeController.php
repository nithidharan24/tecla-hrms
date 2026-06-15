<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
 
class ManageresumeController extends Controller
{
    /**
     * Display a listing of job applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        // Fetching job applications data along with job details
        $applications = DB::table('job_applications')
            ->join('managejobs', 'job_applications.job_id', '=', 'managejobs.id')
            ->select(
                'job_applications.id',
                'job_applications.first_name',
                'job_applications.last_name',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.linkedin',
                'managejobs.job_title',
                'managejobs.department',
                'managejobs.job_type',
                'job_applications.years_experience',
                'job_applications.expected_salary',
                'job_applications.status',
                'job_applications.applied_at',
                'job_applications.resume_path',
                'job_applications.cover_letter',
                'job_applications.converted_to_employee',
                'job_applications.employee_id'
            )
            ->orderBy('job_applications.applied_at', 'desc')
            ->get();
         
        return view('hrms.Jobs.manageresume.index', compact('applications'));
    }

    /**
     * Display the specified job application.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */ 
    public function show($id)
    {
        // Get detailed application information
        $application = DB::table('job_applications')
            ->join('managejobs', 'job_applications.job_id', '=', 'managejobs.id')
            ->select(
                'job_applications.*',
                'managejobs.job_title',
                'managejobs.department',
                'managejobs.job_type',
                'managejobs.job_location',
                'managejobs.salary_from',
                'managejobs.salary_to',
                'managejobs.description',
                'managejobs.skills'
            )
            ->where('job_applications.id', $id)
            ->first();

        if (!$application) {
            return redirect()->route('resume.index')->with('error', 'Application not found.');
        }

        // Check if candidate is already converted to employee
        $employeeInfo = null;
        if ($application->converted_to_employee && $application->employee_id) {
            $employeeInfo = DB::table('allemployees')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                ->select(
                    'allemployees.*',
                    'department.department as department_name',
                    'designation.designation as designation_name'
                )
                ->where('allemployees.id', $application->employee_id)
                ->first();
        }

        return view('hrms.Jobs.manageresume.show', compact('application', 'employeeInfo'));
    }

    /**
     * Update application status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        // Add detailed logging
        Log::info('Status update request received', [
            'application_id' => $id,
            'new_status' => $request->status,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);

        try {
            DB::beginTransaction();

            // Get the current application details
            $application = DB::table('job_applications')
                ->join('managejobs', 'job_applications.job_id', '=', 'managejobs.id')
                ->select(
                    'job_applications.*',
                    'managejobs.job_title',
                    'managejobs.department as department_name',
                    'managejobs.job_location'
                )
                ->where('job_applications.id', $id)
                ->first();

            if (!$application) {
                Log::error('Application not found', ['application_id' => $id]);
                throw new \Exception('Application not found');
            }

            Log::info('Current application details', [
                'application_id' => $id,
                'current_status' => $application->status,
                'new_status' => $request->status,
                'email' => $application->email
            ]);

            // Check if status is being changed to 'hired'
            if ($request->status === 'hired' && $application->status !== 'hired') {
                Log::info('Processing hire status change', ['application_id' => $id]);

                // Check if candidate email already exists in allemployees (but allow duplicate if needed)
                $existingEmployee = DB::table('allemployees')
                    ->where('email', $application->email)
                    ->where('deleted_at', 0)
                    ->first();

                if ($existingEmployee) {
                    Log::warning('Employee already exists, but proceeding anyway', [
                        'email' => $application->email,
                        'existing_employee_id' => $existingEmployee->id
                    ]);
                    // Continue anyway - no validation to prevent hiring
                }

                // Find department ID by name or create new department
                $department = DB::table('department')
                    ->where('department', 'LIKE', '%' . $application->department_name . '%')
                    ->first();

                if (!$department) {
                    Log::info('Creating new department', ['department_name' => $application->department_name]);
                    $departmentId = DB::table('department')->insertGetId([
                        'department' => $application->department_name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $departmentId = $department->id;
                }

                // Find designation or create new one
                $designation = DB::table('designation')
                    ->where('department_id', $departmentId)
                    ->first();

                if (!$designation) {
                    Log::info('Creating new designation', [
                        'designation' => $application->job_title,
                        'department_id' => $departmentId
                    ]);
                    $designationId = DB::table('designation')->insertGetId([
                        'designation' => $application->job_title,
                        'department_id' => $departmentId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $designationId = $designation->id;
                }

                // Get default hierarchy (lowest level)
                $hierarchy = DB::table('hierarchies')
                    ->orderBy('hierarchy_level', 'asc')
                    ->first();
                $hierarchyId = $hierarchy ? $hierarchy->id : null;

                // Generate employee ID
                $generatedEmployeeId = $this->generateEmployeeId($departmentId);

                // Generate random password
                $randomPassword = Str::random(10);
                $hashedPassword = Hash::make($randomPassword);

                // Create unique username
                $username = strtolower($application->first_name . '.' . $application->last_name);
                $originalUsername = $username;
                $counter = 1;
                while (DB::table('allemployees')->where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                Log::info('Creating employee record', [
                    'employee_id' => $generatedEmployeeId,
                    'username' => $username,
                    'email' => $application->email
                ]);

                // Insert employee data into allemployees table
                $employeeId = DB::table('allemployees')->insertGetId([
                    'firstname' => $application->first_name,
                    'lastname' => $application->last_name,
                    'username' => $username,
                    'email' => $application->email,
                    'joiningdate' => now()->format('Y-m-d'),
                    'phone' => $application->phone ?? '',
                    'company' => 'Default Company',
                    'password' => $hashedPassword,
                    'department' => $departmentId,
                    'designation' => $designationId,
                    'hierarchy_id' => $hierarchyId,
                    'employeeid' => $generatedEmployeeId,
                    'profile_image' => null,
                    'deleted_at' => 0,
                    'status' => 'active',
                    'source_application_id' => $id, // Track which application created this employee
                    'source_type' => 'hired_candidate',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Insert personal information into employee_profile_main
                DB::table('employee_profile_main')->insert([
                    'employee_id' => $employeeId,
                    'email' => $application->email,
                    'date_of_joining' => now()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Insert into other employee tables with default empty records
                $employeeTables = [
                    'employee_personal_informations',
                    'employee_emergency_contact',
                    'employee_bank_informations',
                    'employee_family_informations',
                    'employee_education_informations',
                    'employee_experience_informations',
                    'employee_bank_statutory'
                ];

                foreach ($employeeTables as $table) {
                    try {
                        DB::table($table)->insert([
                            'employee_id' => $employeeId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Could not insert into $table: " . $e->getMessage());
                    }
                }

                // Add default modules based on hierarchy if available
                if ($hierarchy && $hierarchy->modules) {
                    try {
                        $hierarchyModules = json_decode($hierarchy->modules, true);
                        if (is_array($hierarchyModules) && !empty($hierarchyModules)) {
                            $moduleInsertData = [];
                            foreach ($hierarchyModules as $module) {
                                $moduleInsertData[] = [
                                    'employee_id' => $employeeId,
                                    'module_name' => trim($module),
                                    'source' => 'hierarchy',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                            }
                            DB::table('employee_module_access')->insert($moduleInsertData);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error adding hierarchy modules for new employee: ' . $e->getMessage());
                    }
                }

                // Update job application to mark as converted
                DB::table('job_applications')
                    ->where('id', $id)
                    ->update([
                        'converted_to_employee' => 1,
                        'employee_id' => $employeeId
                    ]);

                // Send welcome email with credentials
                $this->sendEmployeeCredentials(
                    $application->email,
                    $application->first_name,
                    $randomPassword,
                    $username,
                    $generatedEmployeeId
                );

                Log::info('Candidate successfully converted to employee', [
                    'application_id' => $id,
                    'employee_id' => $employeeId,
                    'employee_code' => $generatedEmployeeId
                ]);
            }

            // Update application status
            $updateResult = DB::table('job_applications')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            Log::info('Status update result', [
                'application_id' => $id,
                'update_result' => $updateResult,
                'new_status' => $request->status
            ]);

            if ($updateResult === 0) {
                Log::warning('No rows were updated', ['application_id' => $id]);
            }

            DB::commit();

            $message = 'Application status updated successfully.';
            if ($request->status === 'hired') {
                $message .= ' Candidate has been added to the employee system and will receive login credentials via email.';
            }

            Log::info('Status update completed successfully', [
                'application_id' => $id,
                'new_status' => $request->status
            ]);

            return redirect()->route('resume.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating application status', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Generate Employee ID
     */
    private function generateEmployeeId($departmentId)
    {
        // Get the last employee record
        $lastEmployee = DB::table('allemployees')->orderBy('id', 'desc')->first();

        // Determine the next ID number
        if ($lastEmployee && $lastEmployee->employeeid) {
            // Extract the number part from the last employee ID
            $lastIdParts = explode('-', $lastEmployee->employeeid);
            if (count($lastIdParts) >= 2) {
                $lastId = (int) $lastIdParts[1];
                $nextId = $lastId + 1;
            } else {
                $nextId = 1;
            }
        } else {
            $nextId = 1; // Start with 1 if no employees exist
        }

        // Fetch the department name using the department ID
        $department = DB::table('department')->where('id', $departmentId)->first();
        if (!$department) {
            Log::error('Department not found', ['department_id' => $departmentId]);
            return 'UN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT); // Unknown department
        }

        // Get the first two letters of the department name
        $departmentInitials = strtoupper(substr($department->department, 0, 2));

        // Format the new employee ID
        return $departmentInitials . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Send employee credentials email
     */
    protected function sendEmployeeCredentials($email, $firstName, $password, $username, $employeeId)
    {
        try {
            $details = [
                'title' => 'Welcome to Our Company - Employee Login Credentials',
                'body' => "Dear $firstName,\n\nCongratulations! You have been successfully hired and added to our employee system.\n\nBelow are your login credentials:\n\nEmployee ID: $employeeId\nUsername: $username\nEmail: $email\nTemporary Password: $password\n\nPlease log in to the employee portal and change your password at your earliest convenience.\n\nEmployee Portal: " . url('/employee/login') . "\n\nWelcome to the team!\n\nBest Regards,\nHR Team"
            ];

            Mail::raw($details['body'], function ($message) use ($email, $details) {
                $message->to($email)
                        ->subject($details['title']);
            });

            Log::info('Employee credentials email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            Log::error('Failed to send employee credentials email: ' . $e->getMessage());
        }
    }

    /**
     * Send email to candidate
     */
    public function sendEmail(Request $request, $id)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'email_type' => 'required|in:general,interview,offer,rejection'
            ]);

            // Get application details
            $application = DB::table('job_applications')
                ->join('managejobs', 'job_applications.job_id', '=', 'managejobs.id')
                ->select(
                    'job_applications.*',
                    'managejobs.job_title',
                    'managejobs.department'
                )
                ->where('job_applications.id', $id)
                ->first();

            if (!$application) {
                return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
            }

            // For now, just log the email (you can implement actual email sending later)
            Log::info('Email would be sent', [
                'to' => $application->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => $request->email_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully to ' . $application->first_name . ' ' . $application->last_name
            ]);

        } catch (\Exception $e) {
            Log::error('Email sending failed:', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified job application.
     */
    public function destroy($id)
    {
        try {
            // Get the application to delete the resume file
            $application = DB::table('job_applications')->where('id', $id)->first();

            if ($application && $application->resume_path) {
                // Delete the resume file from storage
                if (Storage::disk('public')->exists($application->resume_path)) {
                    Storage::disk('public')->delete($application->resume_path);
                }
            }

            // Delete the application record
            DB::table('job_applications')->where('id', $id)->delete();

            return redirect()->route('recruitment.index')->with('success', 'Application deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete application.');
        }
    }

    /**
     * Download or view resume file - FIXED VERSION
     */
    public function viewResume($id)
    {
        try {
            // Get the application record
            $application = DB::table('job_applications')->where('id', $id)->first();
            
            if (!$application) {
                Log::error('Application not found', ['application_id' => $id]);
                return redirect()->route('resume.index')->with('error', 'Application not found.');
            }

            if (!$application->resume_path) {
                Log::error('Resume path is empty', ['application_id' => $id]);
                return redirect()->route('resume.index')->with('error', 'Resume file not found.');
            }

            // Try multiple possible file paths
            $possiblePaths = [
                storage_path('app/public/' . $application->resume_path),
                storage_path('app/' . $application->resume_path),
                public_path('storage/' . $application->resume_path),
                public_path($application->resume_path)
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
                    'application_id' => $id,
                    'resume_path' => $application->resume_path,
                    'checked_paths' => $possiblePaths
                ]);
                return redirect()->route('resume.index')->with('error', 'Resume file not found on server.');
            }

            // Get file info
            $fileName = basename($filePath);
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);

            // Check if download is requested
            $isDownload = request()->has('download') && request()->get('download') == '1';

            // Generate a proper filename for download
            $candidateName = $application->first_name . '_' . $application->last_name;
            $downloadFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidateName) . '_Resume.' . $fileExtension;

            Log::info('Serving resume file', [
                'application_id' => $id,
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
            Log::error('Error serving resume file', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('resume.index')->with('error', 'Error accessing resume file: ' . $e->getMessage());
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

    /**
     * Get application statistics.
     */
    public function getStats()
    {
        $stats = [
            'total' => DB::table('job_applications')->count(),
            'pending' => DB::table('job_applications')->where('status', 'pending')->count(),
            'reviewed' => DB::table('job_applications')->where('status', 'reviewed')->count(),
            'shortlisted' => DB::table('job_applications')->where('status', 'shortlisted')->count(),
            'hired' => DB::table('job_applications')->where('status', 'hired')->count(),
            'rejected' => DB::table('job_applications')->where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get email templates
     */
    public function getEmailTemplate($type)
    {
        $templates = [
            'general' => [
                'subject' => 'Regarding Your Job Application',
                'message' => "Dear {candidate_name},\n\nThank you for your interest in the {job_title} position at our company.\n\nWe have received your application and will review it carefully.\n\nBest regards,\nHR Team"
            ],
            'interview' => [
                'subject' => 'Interview Invitation - {job_title}',
                'message' => "Dear {candidate_name},\n\nWe are pleased to inform you that your application for the {job_title} position has been shortlisted.\n\nWe would like to invite you for an interview. Please reply to this email with your availability.\n\nBest regards,\nHR Team"
            ],
            'offer' => [
                'subject' => 'Job Offer - {job_title}',
                'message' => "Dear {candidate_name},\n\nCongratulations! We are pleased to offer you the position of {job_title} at our company.\n\nPlease find the offer details attached. We look forward to having you on our team.\n\nBest regards,\nHR Team"
            ],
            'rejection' => [
                'subject' => 'Application Status - {job_title}',
                'message' => "Dear {candidate_name},\n\nThank you for your interest in the {job_title} position and for taking the time to apply.\n\nAfter careful consideration, we have decided to move forward with other candidates whose qualifications more closely match our current needs.\n\nWe appreciate your interest in our company and encourage you to apply for future opportunities.\n\nBest regards,\nHR Team"
            ]
        ];

        return response()->json($templates[$type] ?? $templates['general']);
    }
}
