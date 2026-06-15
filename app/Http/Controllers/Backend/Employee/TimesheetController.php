<?php
namespace App\Http\Controllers\Backend\Employee; 


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();
    
        
        // Build base query
        $timesheetQuery = DB::table('timesheet')
            ->leftJoin('allemployees', 'timesheet.employee_id', '=', 'allemployees.id')
            ->leftJoin('projects', 'timesheet.project_id', '=', 'projects.projectid')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'timesheet.*',
                'allemployees.profile_image', 
                'allemployees.firstname', 
                'allemployees.lastname',
                'allemployees.designation as designation_id', 
                'projects.projectname', 
                'projects.enddate', 
                'projects.totalhours',
                'designation.designation'
            )
          ;
    
        
    

if ($branchFilter) {
    $timesheetQuery->where('allemployees.branch_id', $branchFilter);
}

if ($departmentFilter) {
    $timesheetQuery->where('allemployees.department', $departmentFilter);
}

    
// Manager filter — only show their team
if ($managerFilter) {
    $timesheetQuery->where('allemployees.manager_id', $managerFilter);
}
        // Execute query
        $timesheets = $timesheetQuery->get();
    
        return view('hrms.Employee.Timesheets.index', compact('timesheets'));
    }
    
     


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($employeeId = null)
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname')
            ->get();
    
        // If an employee is selected, filter projects for that employee
        if ($employeeId) {
            $projects = DB::table('projects')
                ->where('deleted_at', 0)
                ->where('status', 'active')
                ->where(function($query) use ($employeeId) {
                    $query->where('projectleader', $employeeId)
                          ->orWhere('team', 'like', '%'.$employeeId.'%');
                })
                ->select('projectid', 'projectname')
                ->get();
        } else {
            $projects = DB::table('projects')
                ->where('deleted_at', 0)
                ->where('status', 'active')
                ->select('projectid', 'projectname')
                ->get();
        }
    
        return view('hrms.Employee.Timesheets.create', compact('employees', 'projects', 'employeeId'));
    }

    public function getProjectDetails($projectId)
    {
        $project = DB::table('projects')
                    ->where('projectid', $projectId)  // Change 'id' to 'projectid'
                    ->select('totalhours', 'enddate')
                    ->first();

        return response()->json($project);
    }
    public function getEmployeeProjects($employeeId)
    {
        $projects = DB::table('projects')
            ->where(function($query) use ($employeeId) {
                $query->where('projectleader', $employeeId)
                      ->orWhere('team', 'like', '%,'.$employeeId.',%')   // inside
                      ->orWhere('team', 'like', $employeeId.',%')        // starts with
                      ->orWhere('team', 'like', '%,'.$employeeId)        // ends with
                      ->orWhere('team', '=', $employeeId);               // exact match
            })
            ->select('projectid', 'projectname')
            ->get();
    
        return response()->json($projects);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
     {
         DB::table('timesheet')->insert([
             'employee_id' => $request->employee,
             'project_id' => $request->project,
             'deadline' => null,
             'total_hours' => null,
             'remaining_hours' => null,
             'assigned_date' => $request->assigned_date,
             'assigned_hours' => $request->assigned_hours,
             'description' => $request->description,
             'created_at' => now(),
             'updated_at' => now(),
         ]);
     
         return redirect()->route('timesheet.index')->with('success', 'Timesheet added successfully');
     }
   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch the specific timesheet by its ID
        $timesheet = DB::table('timesheet')
                    ->where('id', $id)
                    ->first();

        // Fetch the list of employees and projects as in the create method
        $employees = DB::table('allemployees')
                    ->where('deleted_at', 0)
                    ->where('status', 'active')
                    ->get();
        
        $projects = DB::table('projects')
                    ->where('deleted_at', 0)
                    ->where('status', 'active')
                    ->get();
        
        // Pass the data to the view
        return view('hrms.Employee.Timesheets.edit', compact('timesheet', 'employees', 'projects'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::table('timesheet')->where('id', $id)->update([
            'employee_id' => $request->employee,
            'project_id' => $request->project,
            'deadline' => null,
             'total_hours' => null,
             'remaining_hours' => null,
            'assigned_date' => $request->assigned_date,
            'assigned_hours' => $request->assigned_hours,
            'description' => $request->description,
            'updated_at' => now(),
        ]);
    
        return redirect()->route('timesheet.index')->with('success', 'Timesheet updated successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Permanently delete the timesheet
        DB::table('timesheet')->where('id', $id)->delete();
    
        // Redirect back with success message
        return redirect()->route('timesheet.index')->with('success', 'Timesheet deleted successfully.');
    }
// Start a new timesheet entry (creates new row each time)
public function start(Request $request)
{
    $request->validate([
        'task_id' => 'required|integer',
    ]);

    $employeeId = session()->get('user_id');
    if (!$employeeId) {
        return redirect()->back()->with('error', 'You must be logged in as an employee.');
    }

    // Check if task exists and is assigned to employee
    $task = DB::table('tasks')
        ->where('id', $request->task_id)
        ->where('assigned_to', $employeeId)
        ->first();

    if (!$task) {
        return redirect()->back()->with('error', 'Task not found or not assigned to you.');
    }

    // Check if employee already has an active timesheet for any task
    $activeTimesheet = DB::table('timesheet')
        ->where('employee_id', $employeeId)
        ->whereNull('end_time')
        ->first();

    if ($activeTimesheet) {
        return redirect()->back()->with('error', 'You already have an active timesheet. Please complete it first before starting a new one.');
    }

    $now = Carbon::now();

    // Create new timesheet entry
    $timesheetId = DB::table('timesheet')->insertGetId([
        'employee_id' => $employeeId,
        'task_id' => $request->task_id,
        'project_id' => $task->projects, // Assuming tasks table has 'projects' column
        'start_date' => $now->toDateString(),
        'start_time' => $now->format('H:i:s'),
        'total_hours' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    // Update task status to in progress if it's pending
    if ($task->status === 'pending') {
        DB::table('tasks')
            ->where('id', $request->task_id)
            ->update([
                'status' => 'in_progress',
                'updated_at' => $now,
            ]);
    }
    $this->logTimesheetActivity(
        $timesheetId,
        $request->task_id,
        'started',
        'Timesheet started'
    );
    
    return redirect()->back()->with('success', 'New timesheet session started successfully.');
}
// Complete the current timesheet
public function complete(Request $request)
{
    $request->validate([
        'timesheet_id' => 'required|integer',
    ]);

    $employeeId = session()->get('user_id');
    if (!$employeeId) {
        return redirect()->back()->with('error', 'You must be logged in as an employee.');
    }

    // Get the active timesheet
    $timesheet = DB::table('timesheet')
        ->where('id', $request->timesheet_id)
        ->where('employee_id', $employeeId)
        ->whereNull('end_time')
        ->first();

    if (!$timesheet) {
        return redirect()->back()->with('error', 'Active timesheet not found or already completed.');
    }

    $now = Carbon::now();
    
    // Fix: Clean the date and time values before parsing
    $startDate = $this->cleanDateValue($timesheet->start_date);
    $startTime = $this->cleanTimeValue($timesheet->start_time);
    
    try {
        // Parse date and time separately
        $start = Carbon::parse($startDate . ' ' . $startTime);
    } catch (\Exception $e) {
        // Fallback to using created_at if parsing fails
        $start = Carbon::parse($timesheet->created_at);
    }
    
    $minutes = $start->diffInMinutes($now);
    $hours = round($minutes / 60, 2);

    // Get total break time for this timesheet
    $breakSeconds = DB::table('breaks')
        ->where('timesheet_id', $timesheet->id)
        ->sum('break_duration_seconds');
    
    $breakHours = $breakSeconds / 3600;
    $netHours = max(0, $hours - $breakHours);

    // Complete the timesheet
    DB::table('timesheet')
        ->where('id', $timesheet->id)
        ->update([
            'end_date' => $now->toDateString(),
            'end_time' => $now->format('H:i:s'),
            'total_hours' => $netHours,
            'updated_at' => $now,
        ]);

    // Check if there are other active timesheets for this task
    $otherActive = DB::table('timesheet')
        ->where('task_id', $timesheet->task_id)
        ->where('employee_id', $employeeId)
        ->whereNull('end_time')
        ->where('id', '!=', $timesheet->id)
        ->exists();

    // If no other active timesheets, mark task as completed
    if (!$otherActive) {
        DB::table('tasks')
            ->where('id', $timesheet->task_id)
            ->update([
                'status' => 'completed',
                'updated_at' => $now,
            ]);
    }
    $this->logTimesheetActivity(
        $timesheet->id,
        $timesheet->task_id,
        'completed',
        'Timesheet completed'
    );
    

    return redirect()->back()->with('success', "Timesheet completed. Net hours: {$netHours}");
}

// Helper method to clean date values (remove microseconds)
private function cleanDateValue($dateValue)
{
    if (!$dateValue) {
        return null;
    }
    
    // Remove everything after the space (including microseconds)
    if (strpos($dateValue, ' ') !== false) {
        $dateValue = explode(' ', $dateValue)[0];
    }
    
    // Remove microseconds if present in date string
    if (strpos($dateValue, '.') !== false) {
        $dateValue = explode('.', $dateValue)[0];
    }
    
    return $dateValue;
}

// Helper method to clean time values (remove microseconds)
private function cleanTimeValue($timeValue)
{
    if (!$timeValue) {
        return null;
    }
    
    // If time contains microseconds, remove them
    if (strpos($timeValue, '.') !== false) {
        $timeValue = explode('.', $timeValue)[0];
    }
    
    // Extract just the time part (HH:MM:SS)
    $timeValue = substr($timeValue, 0, 8);
    
    return $timeValue;
}
public function startBreak(Request $request)
{
    try {
        $employeeId = session()->get('user_id');
        $timesheetId = $request->timesheet_id;

        // Check if timesheet exists and belongs to employee
        $timesheet = DB::table('timesheet')
            ->where('id', $timesheetId)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$timesheet) {
            return response()->json(['error' => 'Timesheet not found'], 404);
        }

        // Check if there's already an active break
        $activeBreak = DB::table('breaks')
            ->where('timesheet_id', $timesheetId)
            ->whereNull('break_end')
            ->first();

        if ($activeBreak) {
            return response()->json(['error' => 'Break already started'], 400);
        }

        // Start new break
        $breakId = DB::table('breaks')->insertGetId([
            'timesheet_id' => $timesheetId,
            'break_start' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->logTimesheetActivity(
            $timesheetId,
            $timesheet->task_id,
            'break_started',
            'Break started'
        );
        

        return response()->json([
            'success' => true,
            'break_id' => $breakId,
            'message' => 'Break started successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to start break: ' . $e->getMessage()], 500);
    }
}

public function endBreak(Request $request)
{
    try {
        $employeeId = session()->get('user_id');
        $timesheetId = $request->timesheet_id;

        // Check if timesheet exists and belongs to employee
        $timesheet = DB::table('timesheet')
            ->where('id', $timesheetId)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$timesheet) {
            return response()->json(['error' => 'Timesheet not found'], 404);
        }

        // Get active break
        $activeBreak = DB::table('breaks')
            ->where('timesheet_id', $timesheetId)
            ->whereNull('break_end')
            ->first();

        if (!$activeBreak) {
            return response()->json(['error' => 'No active break found'], 400);
        }

        // Calculate break duration
        $breakEnd = now();
        $breakStart = Carbon::parse($activeBreak->break_start);
        $breakDuration = $breakEnd->diffInSeconds($breakStart);

        // Update break
        DB::table('breaks')
            ->where('id', $activeBreak->id)
            ->update([
                'break_end' => $breakEnd,
                'break_duration_seconds' => $breakDuration,
                'updated_at' => now(),
            ]);
            $this->logTimesheetActivity(
                $timesheetId,
                $timesheet->task_id,
                'break_ended',
                'Break ended'
            );
            

        return response()->json([
            'success' => true,
            'break_duration' => $breakDuration,
            'message' => 'Break ended successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to end break: ' . $e->getMessage()], 500);
    }
}
private function logTimesheetActivity($timesheetId, $taskId, $action, $customText)
{
    $employeeId = session('user_id');

    $emp = DB::table('allemployees')
        ->where('id', $employeeId)
        ->select('firstname', 'lastname')
        ->first();

    $employeeName = $emp
        ? trim($emp->firstname . ' ' . $emp->lastname)
        : 'Employee';

    DB::table('timesheet_activity_log')->insert([
        'timesheet_id' => $timesheetId,
        'task_id'      => $taskId,
        'action'       => $action,
        'performed_by' => 'employee',
        'user_id'      => $employeeId,
        'description'  => "{$customText} by {$employeeName}",
        'created_at'   => now(),
    ]);
}

}
