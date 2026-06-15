<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all open jobs from managejobs table
        $jobs = DB::table('managejobs')
            ->where('status', 'open')
            ->where('deleted_at', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get departments for filters
        $departments = DB::table('department')->get();

        // Get unique job types for filters
        $jobTypes = DB::table('managejobs')
            ->where('status', 'open')
            ->where('deleted_at', 0)
            ->distinct()
            ->pluck('job_type')
            ->filter(); // Remove null values

        // Get unique experience levels from database
        $experienceLevels = DB::table('managejobs')
            ->where('status', 'open')
            ->where('deleted_at', 0)
            ->distinct()
            ->pluck('experience')
            ->filter(); // Remove null values

        // Get unique locations for filters
        $locations = DB::table('managejobs')
            ->where('status', 'open')
            ->where('deleted_at', 0)
            ->distinct()
            ->pluck('job_location')
            ->filter(); // Remove null values

        return view('hrms.Jobs.career.index', compact('jobs', 'departments', 'jobTypes', 'locations', 'experienceLevels'));
    }

    /**
     * Filter jobs based on search criteria
     */
    public function filter(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('Filter request received:', $request->all());

        $query = DB::table('managejobs')
            ->where('status', 'open')
            ->where('deleted_at', 0);

        // Search by job title, skills, or description (case-insensitive)
        if ($request->filled('search') && trim($request->search) !== '') {
            $search = trim($request->search);
            \Log::info('Applying search filter:', ['search' => $search]);
            
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(job_title) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(skills) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // Filter by location (case-insensitive)
        if ($request->filled('location') && trim($request->location) !== '') {
            $location = trim($request->location);
            \Log::info('Applying location filter:', ['location' => $location]);
            
            $query->whereRaw('LOWER(job_location) LIKE ?', ['%' . strtolower($location) . '%']);
        }

        // Filter by department
        if ($request->filled('department') && $request->department !== 'All') {
            \Log::info('Applying department filter:', ['department' => $request->department]);
            $query->where('department', $request->department);
        }

        // Filter by job type
        if ($request->filled('job_type') && $request->job_type !== 'All') {
            \Log::info('Applying job_type filter:', ['job_type' => $request->job_type]);
            $query->where('job_type', $request->job_type);
        }

        // Filter by experience level (using actual database values)
        if ($request->filled('experience') && $request->experience !== 'All') {
            \Log::info('Applying experience filter:', ['experience' => $request->experience]);
            $query->where('experience', $request->experience);
        }

        $jobs = $query->orderBy('created_at', 'desc')->get();
        
        \Log::info('Filter results:', ['count' => $jobs->count()]);

        return response()->json($jobs);
    }

    /**
     * Get job details
     */
    public function show($id)
    {
        $job = DB::table('managejobs')
            ->where('id', $id)
            ->where('status', 'open')
            ->where('deleted_at', 0)
            ->first();

        if (!$job) {
            abort(404, 'Job not found');
        }

        return response()->json($job);
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:managejobs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'linkedin' => 'nullable|url|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'cover_letter' => 'required|string|max:2000',
            'years_experience' => 'nullable|string|max:50',
            'expected_salary' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if job exists and is still open
            $job = DB::table('managejobs')
                ->where('id', $request->job_id)
                ->where('status', 'open')
                ->where('deleted_at', 0)
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or no longer available'
                ], 404);
            }

            // Handle file upload
            $resumePath = null;
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $resumePath = $file->storeAs('resumes', $fileName, 'public');
            }

            // Save application to database
            $applicationId = DB::table('job_applications')->insertGetId([
                'job_id' => $request->job_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'linkedin' => $request->linkedin,
                'resume_path' => $resumePath,
                'cover_letter' => $request->cover_letter,
                'years_experience' => $request->years_experience,
                'expected_salary' => $request->expected_salary,
                'status' => 'pending',
                'applied_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log the application
            \Log::info('Job application submitted:', [
                'application_id' => $applicationId,
                'job_id' => $request->job_id,
                'applicant_email' => $request->email
            ]);

            // You can add email notification logic here
            // $this->sendApplicationNotification($applicationId);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully! We will review your application and get back to you soon.',
                'application_id' => $applicationId
            ]);

        } catch (\Exception $e) {
            \Log::error('Job application error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.'
            ], 500);
        }
    }

    /**
     * Send application notification (optional)
     */
    private function sendApplicationNotification($applicationId)
    {
        // Add your email notification logic here
        // You can use Laravel's Mail facade to send emails
        // to both the applicant and HR team
    }
}
