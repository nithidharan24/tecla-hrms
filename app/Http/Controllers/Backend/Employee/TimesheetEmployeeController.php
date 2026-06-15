<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\Holiday;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TimesheetEmployeeController extends Controller
{
    /**
     * Display timesheets based on user role
     */
    public function index()
{
    // Check if user is logged in via session
    if (!Session::has('user_id') && !Session::has('admin_id')) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $role = Session::get('role');
    $userId = Session::get('user_id') ?? Session::get('admin_id');
    
    // Base query
    $query = Timesheet::with('project');
    
    // If employee, show only their timesheets
    if ($role === 'employee') {
        $query->where('employee_id', $userId);
    }
    
    // Get timesheets
    $timesheets = $query->orderBy('week_start', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    // If admin, manually attach employee data
    if ($role === 'admin') {
        foreach ($timesheets as $timesheet) {
            $employee = DB::table('allemployees')
                ->where('id', $timesheet->employee_id)
                ->first();
            $timesheet->employee_name = $employee ? ($employee->firstname . ' ' . $employee->lastname) : 'N/A';
            $timesheet->employee_id_display = $employee ? $employee->employeeid : 'N/A';
        }
    }
    
    return view('hrms.employee.timesheet-employee.index', compact('timesheets', 'role'));
}
    
    /**
     * Show create timesheet form (only for employees)
     */
public function create(Request $request)
{
    // Only employees can create timesheets
    if (!Session::has('user_id') || Session::get('role') !== 'employee') {
        return redirect()->route('login')->with('error', 'Only employees can create timesheets');
    }
    
    try {
        $employeeId = Session::get('user_id');
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        
        if (!$employee) {
            return redirect()->route('timesheet.index')->with('error', 'Employee not found.');
        }
        
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
        
        // Get projects assigned to the employee based on actual table columns
        $projects = Project::where(function($query) use ($employeeId) {
            // Check if employee is in team (FIND_IN_SET works with comma-separated values)
            $query->whereRaw("FIND_IN_SET(?, team)", [$employeeId])
                  // Check if employee is project leader
                  ->orWhere('projectleader', $employeeId);
        })
        ->where('status', '!=', 'Completed') // Optional: exclude completed projects
        ->orderBy('projectname')
        ->get();
        
        // If no projects found, get all active projects
        if ($projects->isEmpty()) {
            $projects = Project::where('status', '!=', 'Completed')
                ->orWhereNull('status')
                ->orderBy('projectname')
                ->limit(20)
                ->get();
        }
        
        // Get holidays for the week
        try {
            $holidays = Holiday::where(function($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('holidaydate', [$weekStart, $weekEnd])
                      ->orWhereBetween('date_holiday', [$weekStart, $weekEnd]);
            })->get();
        } catch (\Exception $e) {
            $holidays = collect([]);
            Log::warning('Holiday query failed: ' . $e->getMessage());
        }
        
        // Get trainings for the employee
        try {
            $trainings = Training::where('employees', 'LIKE', '%' . $employeeId . '%')
                ->where(function($query) use ($weekStart, $weekEnd) {
                    $query->whereBetween('start_date', [$weekStart, $weekEnd])
                          ->orWhereBetween('end_date', [$weekStart, $weekEnd]);
                })
                ->get();
        } catch (\Exception $e) {
            $trainings = collect([]);
            Log::warning('Training query failed: ' . $e->getMessage());
        }
        
        // Generate week days
        $weekDays = [];
        $currentDay = Carbon::parse($weekStart);
        
        for ($i = 0; $i < 7; $i++) {
            $isWeekend = $currentDay->isWeekend();
            $isHoliday = $this->checkHoliday($currentDay, $holidays);
            
            $weekDays[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->format('D'),
                'day_full' => $currentDay->format('l'),
                'is_weekend' => $isWeekend,
                'is_holiday' => $isHoliday,
                'is_disabled' => $isWeekend || $isHoliday
            ];
            $currentDay->addDay();
        }
        
        // Get existing timesheet entries for this week
        $existingEntries = Timesheet::where('employee_id', $employeeId)
            ->where('week_start', $weekStart)
            ->get()
            ->keyBy(function($item) {
                return $item->project_id;
            });
        
        return view('hrms.employee.timesheet-employee.create', compact(
            'projects', 
            'weekDays', 
            'weekStart', 
            'weekEnd',
            'holidays',
            'trainings',
            'existingEntries'
        ));
        
    } catch (\Exception $e) {
        Log::error('Create page error: ' . $e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
        
        return redirect()->route('timesheet.index')
            ->with('error', 'Error loading create page. Please try again.');
    }
}
    
    /**
     * Store timesheet entries
     */
    public function store(Request $request)
    {
        // Only employees can store timesheets
        if (!Session::has('user_id') || Session::get('role') !== 'employee') {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        try {
            $employeeId = Session::get('user_id');
            $weekStart = $request->week_start;
            
            // Validate request
            if (!$request->has('entries')) {
                return redirect()->back()->with('error', 'No entries found');
            }
            
            DB::beginTransaction();
            
            // Delete existing pending entries for this week
            Timesheet::where('employee_id', $employeeId)
                ->where('week_start', $weekStart)
                ->where('status', 'pending')
                ->delete();
            
            $entriesSaved = 0;
            
            // Group entries by project to calculate total hours per project for the week
            $projectHours = [];
            
            foreach ($request->entries as $date => $projects) {
                foreach ($projects as $projectId => $hours) {
                    if (is_numeric($hours) && $hours > 0) {
                        if (!isset($projectHours[$projectId])) {
                            $projectHours[$projectId] = 0;
                        }
                        $projectHours[$projectId] += floatval($hours);
                    }
                }
            }
            
            // Save one record per project with total weekly hours
            foreach ($projectHours as $projectId => $totalHours) {
                Timesheet::create([
                    'employee_id' => $employeeId,
                    'project_id' => $projectId,
                    'date' => $weekStart,
                    'hours' => $totalHours,
                    'comments' => $request->comments ?? '',
                    'status' => 'pending',
                    'week_start' => $weekStart
                ]);
                $entriesSaved++;
            }
            
            DB::commit();
            
            return redirect()->route('timesheet.index')
                ->with('success', "Timesheet saved successfully! ($entriesSaved project entries)");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Timesheet save error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error saving timesheet: ' . $e->getMessage());
        }
    }
    

public function edit($id)
{
    // Check if user is logged in via session
    if (!Session::has('user_id') || Session::get('role') !== 'employee') {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $employeeId = Session::get('user_id');
    
    // Get the timesheet record
    $timesheet = Timesheet::where('employee_id', $employeeId)
        ->where('id', $id)
        ->where('status', 'pending')
        ->first();
    
    if (!$timesheet) {
        return redirect()->route('timesheet.index')
            ->with('error', 'Timesheet not found or cannot be edited');
    }
    
    // Get week start and end
    $weekStart = $timesheet->week_start;
    $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
    
    // Get all projects for the employee (same as create page)
    $projects = Project::where(function($query) use ($employeeId) {
        $query->whereRaw("FIND_IN_SET(?, team)", [$employeeId])
              ->orWhere('projectleader', $employeeId);
    })
    ->where('status', '!=', 'Completed')
    ->orderBy('projectname')
    ->get();
    
    // If no projects found, get all active projects
    if ($projects->isEmpty()) {
        $projects = Project::where('status', '!=', 'Completed')
            ->orWhereNull('status')
            ->orderBy('projectname')
            ->limit(20)
            ->get();
    }
    
    // Get holidays for the week
    try {
        $holidays = Holiday::where(function($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('holidaydate', [$weekStart, $weekEnd])
                  ->orWhereBetween('date_holiday', [$weekStart, $weekEnd]);
        })->get();
    } catch (\Exception $e) {
        $holidays = collect([]);
    }
    
    // Get trainings for the employee
    try {
        $trainings = Training::where('employees', 'LIKE', '%' . $employeeId . '%')
            ->where(function($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('start_date', [$weekStart, $weekEnd])
                      ->orWhereBetween('end_date', [$weekStart, $weekEnd]);
            })
            ->get();
    } catch (\Exception $e) {
        $trainings = collect([]);
    }
    
    // Generate week days
    $weekDays = [];
    $currentDay = Carbon::parse($weekStart);
    
    for ($i = 0; $i < 7; $i++) {
        $isWeekend = $currentDay->isWeekend();
        $isHoliday = $this->checkHoliday($currentDay, $holidays);
        
        $weekDays[] = [
            'date' => $currentDay->format('Y-m-d'),
            'day' => $currentDay->format('D'),
            'day_full' => $currentDay->format('l'),
            'is_weekend' => $isWeekend,
            'is_holiday' => $isHoliday,
            'is_disabled' => $isWeekend || $isHoliday
        ];
        $currentDay->addDay();
    }
    
    // Get ALL timesheet entries for this week to populate daily hours
    // Since you store weekly totals, we need to distribute them across days
    // For now, we'll just show the weekly total in a note
    
    // For a better UX, we can create a daily entries array
    // This assumes you have a way to know which days were worked
    // For now, we'll just create a structure for the view
    
    $dailyEntries = [];
    
    // Get all timesheet entries for this week
    $weekEntries = Timesheet::where('employee_id', $employeeId)
        ->where('week_start', $weekStart)
        ->get();
    
    // For each project, we need to know the daily hours
    // Since you store weekly totals, we need to distribute them
    // This is a simplification - you might want to store daily hours instead
    foreach ($weekEntries as $entry) {
        $dailyEntries[$entry->project_id] = [];
        
        // Distribute total hours across working days
        $workingDays = collect($weekDays)->filter(function($day) {
            return !$day['is_disabled'];
        })->values();
        
        $workingDaysCount = $workingDays->count();
        
        if ($workingDaysCount > 0) {
            // Distribute hours evenly across working days
            $hoursPerDay = $entry->hours / $workingDaysCount;
            
            foreach ($workingDays as $index => $day) {
                $dailyEntries[$entry->project_id][$day['date']] = round($hoursPerDay, 1);
            }
        }
    }
    
    return view('hrms.employee.timesheet-employee.edit', compact(
        'timesheet',
        'projects', 
        'weekDays', 
        'weekStart', 
        'weekEnd',
        'holidays',
        'trainings',
        'dailyEntries'
    ));
}
    
    /**
     * Update timesheet (reuses store logic)
     */
 /**
 * Update timesheet (weekly view)
 */
public function update(Request $request, $id)
{
    // Check if user is logged in via session
    if (!Session::has('user_id') || Session::get('role') !== 'employee') {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $employeeId = Session::get('user_id');
    $weekStart = $request->week_start;
    
    // Validate request
    if (!$request->has('entries')) {
        return redirect()->back()->with('error', 'No entries found');
    }
    
    DB::beginTransaction();
    
    try {
        // Delete existing pending entries for this week
        Timesheet::where('employee_id', $employeeId)
            ->where('week_start', $weekStart)
            ->where('status', 'pending')
            ->delete();
        
        $entriesSaved = 0;
        
        // Group entries by project to calculate total hours per project for the week
        $projectHours = [];
        
        foreach ($request->entries as $date => $projects) {
            foreach ($projects as $projectId => $hours) {
                if (is_numeric($hours) && $hours > 0) {
                    if (!isset($projectHours[$projectId])) {
                        $projectHours[$projectId] = 0;
                    }
                    $projectHours[$projectId] += floatval($hours);
                }
            }
        }
        
        // Save one record per project with total weekly hours
        foreach ($projectHours as $projectId => $totalHours) {
            Timesheet::create([
                'employee_id' => $employeeId,
                'project_id' => $projectId,
                'date' => $weekStart,
                'hours' => $totalHours,
                'comments' => $request->comments ?? '',
                'status' => 'pending',
                'week_start' => $weekStart
            ]);
            $entriesSaved++;
        }
        
        DB::commit();
        
        return redirect()->route('timesheet.index')
            ->with('success', "Timesheet updated successfully! ($entriesSaved project entries)");
        
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Timesheet update error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error updating timesheet: ' . $e->getMessage());
    }
}
    /**
     * Delete timesheet (only if pending)
     */
    public function destroy($id)
    {
        // Check if user is logged in
        if (!Session::has('user_id') && !Session::has('admin_id')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        $role = Session::get('role');
        $userId = Session::get('user_id') ?? Session::get('admin_id');
        
        $query = Timesheet::where('id', $id);
        
        // Employees can only delete their own pending timesheets
        if ($role === 'employee') {
            $query->where('employee_id', $userId)
                  ->where('status', 'pending');
        }
        
        $timesheet = $query->first();
        
        if ($timesheet) {
            $timesheet->delete();
            return redirect()->route('timesheet.index')
                ->with('success', 'Timesheet deleted successfully.');
        }
        
        return redirect()->route('timesheet.index')
            ->with('error', 'Timesheet not found or cannot be deleted.');
    }
    
    /**
     * Approve timesheet (admin only)
     */
   /**
 * Approve timesheet (admin only)
 */
/**
 * Approve timesheet (admin only)
 */
public function approve($id)
{
    // Only admins can approve
    if (!Session::has('admin_id') || Session::get('role') !== 'admin') {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        return redirect()->back()->with('error', 'Unauthorized action.');
    }
    
    try {
        $timesheet = Timesheet::findOrFail($id);
        
        // Update status
        $timesheet->update([
            'status' => 'approved',
            'approved_by' => Session::get('admin_id'),
            'approved_at' => now()
        ]);
        
        // Send email notification to employee
        $this->sendApprovalEmail($timesheet, 'approved');
        
        // Always return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Timesheet approved successfully.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Timesheet approved successfully.');
        
    } catch (\Exception $e) {
        Log::error('Approval error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving timesheet: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error approving timesheet: ' . $e->getMessage());
    }
}

/**
 * Reject timesheet (admin only)
 */
public function reject(Request $request, $id)
{
    // Only admins can reject
    if (!Session::has('admin_id') || Session::get('role') !== 'admin') {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        return redirect()->back()->with('error', 'Unauthorized action.');
    }
    
    $request->validate([
        'rejection_reason' => 'required|string|max:500'
    ]);
    
    try {
        $timesheet = Timesheet::with('employee', 'project')->findOrFail($id);
        
        // Update status
        $timesheet->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Session::get('admin_id'),
            'approved_at' => now()
        ]);
        
        // Send email notification to employee
        $this->sendApprovalEmail($timesheet, 'rejected', $request->rejection_reason);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Timesheet rejected successfully.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Timesheet rejected successfully.');
        
    } catch (\Exception $e) {
        Log::error('Rejection error: ' . $e->getMessage());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting timesheet: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Error rejecting timesheet.');
    }
}
    
    /**
     * Bulk approve timesheets (admin only)
     */
   /**
 * Bulk approve timesheets (admin only)
 */
public function bulkApprove(Request $request)
{
    // Only admins can bulk approve
    if (!Session::has('admin_id') || Session::get('role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
    
    $request->validate([
        'timesheet_ids' => 'required|array',
        'timesheet_ids.*' => 'exists:timesheets,id'
    ]);
    
    try {
        $timesheets = Timesheet::with('employee', 'project')
            ->whereIn('id', $request->timesheet_ids)
            ->get();
        
        $approvedCount = 0;
        $errors = [];
        
        foreach ($timesheets as $timesheet) {
            try {
                $timesheet->update([
                    'status' => 'approved',
                    'approved_by' => Session::get('admin_id'),
                    'approved_at' => now()
                ]);
                
                // Send email to each employee
                $this->sendApprovalEmail($timesheet, 'approved');
                $approvedCount++;
                
            } catch (\Exception $e) {
                $errors[] = "Timesheet ID {$timesheet->id}: " . $e->getMessage();
                Log::error("Bulk approve error for timesheet {$timesheet->id}: " . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$approvedCount} timesheets approved successfully.",
            'errors' => $errors
        ]);
        
    } catch (\Exception $e) {
        Log::error('Bulk approve error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error processing request: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Send email notification for approval/rejection
     */
/**
 * Send email notification for approval/rejection
 */
/**
 * Send email notification for approval/rejection
 */
private function sendApprovalEmail($timesheet, $status, $reason = null)
{
    try {
        // Get employee details directly from database
        $employee = DB::table('allemployees')
            ->where('id', $timesheet->employee_id)
            ->first();
        
        if (!$employee || !$employee->email) {
            Log::warning('Employee email not found for timesheet ID: ' . $timesheet->id);
            return;
        }
        
        $employeeEmail = $employee->email;
        $employeeName = $employee->firstname . ' ' . $employee->lastname;
        
        // Get project details
        $project = DB::table('projects')
            ->where('id', $timesheet->project_id)
            ->first();
        
        $projectName = $project ? ($project->projectname ?? $project->name ?? 'N/A') : 'N/A';
        
        $subject = $status === 'approved' 
            ? 'Your Timesheet Has Been Approved' 
            : 'Your Timesheet Has Been Rejected';
        
        // Format dates
        $weekStartFormatted = date('M d, Y', strtotime($timesheet->week_start));
        $weekEndFormatted = date('M d, Y', strtotime($timesheet->week_start . ' +6 days'));
        
        // Build HTML email
        $message = "
        <html>
        <head>
            <title>Timesheet Status Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4e73df; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; background-color: #f8f9fc; border: 1px solid #e3e6f0; }
                .details { background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4e73df; }
                .details li { margin-bottom: 10px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
                .approved { background-color: #1cc88a; color: white; }
                .rejected { background-color: #e74a3b; color: white; }
                .reason-box { background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0; color: #721c24; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Timesheet Status Update</h2>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$employeeName}</strong>,</p>
                    <p>Your timesheet for the week <strong>{$weekStartFormatted} - {$weekEndFormatted}</strong> has been 
                    <span class='badge " . ($status === 'approved' ? 'approved' : 'rejected') . "'>" . strtoupper($status) . "</span>.</p>
                    
                    <div class='details'>
                        <h3>Timesheet Details:</h3>
                        <ul>
                            <li><strong>Week:</strong> {$weekStartFormatted} - {$weekEndFormatted}</li>
                            <li><strong>Project:</strong> {$projectName}</li>
                            <li><strong>Total Hours:</strong> {$timesheet->hours}</li>
                            <li><strong>Comments:</strong> " . ($timesheet->comments ?: 'No comments') . "</li>
                        </ul>
                    </div>";
        
        if ($reason) {
            $message .= "<div class='reason-box'>
                            <strong>Rejection Reason:</strong><br>
                            {$reason}
                        </div>";
        }
        
        $message .= "
                    <p>You can view your timesheet by logging into the HRMS portal.</p>
                    <p>Thank you,<br><strong>HRMS Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message, please do not reply to this email.</p>
                    <p>&copy; " . date('Y') . " HRMS. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Send email
        Mail::html($message, function ($mail) use ($employeeEmail, $subject) {
            $mail->to($employeeEmail)
                 ->subject($subject)
                 ->from(config('mail.from.address'), config('mail.from.name'));
        });
        
        Log::info("Timesheet {$status} email sent successfully to {$employeeEmail}");
        
    } catch (\Exception $e) {
        Log::error("Failed to send timesheet {$status} email: " . $e->getMessage());
    }
}
    
    /**
     * Check if date is holiday
     */
    private function checkHoliday($date, $holidays)
    {
        foreach ($holidays as $holiday) {
            if (isset($holiday->holidaydate) && $date->isSameDay(Carbon::parse($holiday->holidaydate))) {
                return true;
            }
            if (isset($holiday->date_holiday) && $date->isSameDay(Carbon::parse($holiday->date_holiday))) {
                return true;
            }
        }
        return false;
    }
}