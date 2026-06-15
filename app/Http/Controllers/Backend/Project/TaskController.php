<?php

namespace App\Http\Controllers\Backend\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index($returnData = false)
    {
        $role = session()->get('role');
        $employeeId = session()->get('user_id'); // employee normal ID
    
        $query = DB::table('projects')
            ->where('deleted_at', 0);
    
        // ========================
        // ROLE BASED FILTERS
        // ========================
    
        if ($role === 'admin' || $role === 'manager') {
            // Manager and Admin see all projects
        } else {
            // Check if user is a Team Lead
            $isTeamLead = DB::table('allemployees')->where('team_lead_id', $employeeId)->exists();
            
            if ($isTeamLead) {
                // Team Lead: show department projects only (projects where leader is in the same department)
                $empDept = DB::table('allemployees')->where('id', $employeeId)->value('department');
                if ($empDept) {
                    $query->whereExists(function($subq) use ($empDept) {
                        $subq->select(DB::raw(1))
                             ->from('allemployees as leader')
                             ->whereColumn('leader.id', 'projects.projectleader')
                             ->where('leader.department', $empDept);
                    });
                }
            } else {
                // Regular Employee: show only projects assigned to him
                $query->where(function ($q) use ($employeeId) {
                    $q->where('projectleader', $employeeId) // employee is leader
                      ->orWhereRaw("FIND_IN_SET(?, team)", [$employeeId]); // employee in team
                });
            }
        }

        // ========================
        if (request()->has('project') && request()->project != "") {
            $query->where('projectid', request()->project);
        }
    
        // ========================
        if (request()->has('leader') && request()->leader != "") {
            $query->where('projectleader', request()->leader);
        }
    
        $tasks = $query->get();
    
        foreach ($tasks as $project) {
    
            // Leader Name
            $project->leaderName = DB::table('allemployees')
                ->where('id', $project->projectleader)
                ->value(DB::raw("CONCAT(firstname,' ',lastname)")) ?? 'N/A';
    
            // Build main task query
            $taskQuery = DB::table('tasks')
                ->where('projects', $project->projectid);
    
            // FILTER: PRIORITY
            if (request()->has('priority') && request()->priority != "") {
                $taskQuery->where('priority', request()->priority);
            }
    
            // COUNTS
            $project->total_tasks = $taskQuery->count();
            $project->completed_tasks = (clone $taskQuery)->where('status', 'completed')->count();
            $project->pending_tasks = (clone $taskQuery)->where('status', 'pending')->count();
            $project->inprogress_tasks = (clone $taskQuery)->where('status', 'in_progress')->count();

            $project->progressPercentage = $project->total_tasks > 0 
                ? round(($project->completed_tasks / $project->total_tasks) * 100, 1) 
                : 0;
        }
    
        if ($returnData) return $tasks;
    
        return view('hrms.Employee.Task.index', compact('tasks'));
    }
    
    
    /**
     * Alternative index method using JOIN (more efficient)
     */
    public function indexWithJoin()
    {
        try {
            $departmentFilter = getEmployeeDepartmentFilter();
            $branchFilter = getAdminBranchFilter();

            // Using JOIN approach for better performance
            $projectsQuery = DB::table('projects')
                ->leftJoin('allemployees as leader', function($join) {
                    $join->on('projects.projectleader', '=', 'leader.id')
                         ->where('leader.deleted_at', '=', 0);
                })
                ->where('projects.deleted_at', 0)
                ->whereIn('projects.status', ['active', 'initiated', 'planned', 'pending']);

            // Apply branch/department filter
            if ($branchFilter || $departmentFilter) {
                if ($branchFilter) {
                    $projectsQuery->where('leader.branch_id', $branchFilter);
                } elseif ($departmentFilter) {
                    $projectsQuery->where('leader.department', $departmentFilter);
                }
            }

            $projects = $projectsQuery
                ->select(
                    'projects.*',
                    'leader.id as leader_id',
                    'leader.firstname as leader_firstname',
                    'leader.lastname as leader_lastname',
                    DB::raw("CONCAT(COALESCE(leader.firstname, ''), ' ', COALESCE(leader.lastname, '')) as leaderName")
                )
                ->get();

            foreach ($projects as $project) {
                // Clean up leader name
                $project->leaderName = trim($project->leaderName);
                if (empty($project->leaderName)) {
                    $project->leaderName = 'Leader Not Found (ID: ' . $project->projectleader . ')';
                }

                // Handle team members with branch/department filter
                if (!empty($project->team)) {
                    $teamIds = array_filter(array_map('trim', explode(',', $project->team)));
                    
                    if (!empty($teamIds)) {
                        $teamMembersQuery = DB::table('allemployees')
                            ->whereIn('id', $teamIds)
                            ->where('deleted_at', 0);

                        if ($branchFilter) {
                            $teamMembersQuery->where('branch_id', $branchFilter);
                        } elseif ($departmentFilter) {
                            $teamMembersQuery->where('department', $departmentFilter);
                        }

                        $teamMembers = $teamMembersQuery
                            ->select(DB::raw("CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')) as full_name"))
                            ->get();

                        if ($teamMembers->count() > 0) {
                            $formattedNames = '';
                            foreach ($teamMembers as $index => $member) {
                                $memberName = trim($member->full_name);
                                $formattedNames .= ($index + 1) . '. ' . $memberName . '<br>';
                            }
                            $project->teamNames = $formattedNames;
                        } else {
                            $project->teamNames = 'No team members found';
                        }
                    } else {
                        $project->teamNames = 'No valid team IDs';
                    }
                } else {
                    $project->teamNames = 'No team assigned';
                }

                // Task counts
                $projectTasks = DB::table('tasks')->where('projects', $project->projectid)->get();
                $project->total_tasks = $projectTasks->count();
                $project->completed_tasks = $projectTasks->where('status', 'completed')->count();
                $project->pending_tasks = $projectTasks->where('status', 'pending')->count();
                $project->inprogress_tasks = $projectTasks->where('status', 'in_progress')->count();
                $project->progressPercentage = $project->total_tasks > 0 ? round(($project->completed_tasks / $project->total_tasks) * 100, 1) : 0;
            }

            return view('hrms.Employee.Task.index', compact('projects'));
            
        } catch (\Exception $e) {
            Log::error('Error in TaskController indexWithJoin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading projects: ' . $e->getMessage());
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
{
    $project_id = $request->get('projectid');
    
    $project = DB::table('projects')->where('id', $project_id)->first();
    
    if (!$project) {
        return redirect()->route('projects.index')->with('error', 'Project not found');
    }

    // Get project leader name
    $leader = DB::table('allemployees')
        ->where('id', $project->projectleader)
        ->where('deleted_at', 0)
        ->select(DB::raw("CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')) as full_name"))
        ->first();
        
    $project->leaderName = $leader ? trim($leader->full_name) : 'Leader Not Found';

    // Get team members (only those in the project team)
    $teamIds = array_filter(explode(',', $project->team));
    
    $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereIn('id', $teamIds)
        ->select('id', 'firstname', 'lastname', 'employeeid', 'designation')
        ->orderBy('firstname')
        ->get();
    
    $tasks = DB::table('tasks')
        ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
        ->where('tasks.projects', $project->projectid)
        ->select(
            'tasks.*',
            DB::raw("CONCAT(COALESCE(allemployees.firstname, ''), ' ', COALESCE(allemployees.lastname, '')) as employee_name")
        )
        ->orderBy('tasks.created_at', 'desc')
        ->get();
    
    return view('hrms.Employee.Task.create', compact('project', 'employees', 'tasks'));
}

 
    public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'task' => 'required|string|max:255',
        'description' => 'required|string',
        'projectid' => 'required|integer',
        'assigned_to' => 'required|integer|exists:allemployees,id',
        'priority' => 'required|string|in:low,medium,high,urgent',
        'due_date' => 'required|date|after_or_equal:today',
    ]);

    // Retrieve the project
    $project = DB::table('projects')->where('id', $request->projectid)->first();
    if (!$project) {
        return redirect()->route('projects.index')->with('error', 'Project not found');
    }

    // Additional validation: Check if the assigned employee is part of the project team
    $teamIds = array_filter(explode(',', $project->team));
    if (!in_array($request->assigned_to, $teamIds)) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'The selected employee is not part of this project team');
    }

    // Insert the task & GET THE ID
    $taskId = DB::table('tasks')->insertGetId([
        'task' => $request->task,
        'description' => $request->description,
        'projects' => $project->projectid,
        'assigned_to' => $request->assigned_to,
        'priority' => $request->priority,
        'due_date' => $request->due_date,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $assignedEmployee = DB::table('allemployees')
    ->where('id', $request->assigned_to)
    ->select(DB::raw("CONCAT(firstname,' ',lastname) as full_name"))
    ->first();

$employeeName = $assignedEmployee ? $assignedEmployee->full_name : 'Employee';

$this->logActivity(
    $taskId,
    'created',
    "Task created and assigned to {$employeeName}"
);


    // ==============================
    // 📧 SEND EMAIL TO EMPLOYEE
    // ==============================
    $employee = DB::table('allemployees')
        ->where('id', $request->assigned_to)
        ->select('email', 'firstname', 'lastname')
        ->first();

    if ($employee && $employee->email) {

        $taskName = $request->task;
        $projectName = $project->projectname ?? 'Project';
        $dueDate = $request->due_date;

        $message = "
Hello {$employee->firstname} {$employee->lastname},

A new task has been assigned to you.

Task: {$taskName}
Project: {$projectName}
Priority: {$request->priority}
Due Date: {$dueDate}

Description:
{$request->description}

Please check the task panel for more details.

Regards,
Admin
";

        \Mail::raw($message, function ($mail) use ($employee, $taskName) {
            $mail->to($employee->email)
                 ->subject("New Task Assigned: {$taskName}");
        });
    }

    return redirect()->route('tasks.create', ['projectid' => $project->id])
        ->with('success', 'Task assigned successfully!');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Fetch task with employee and project information
        $task = DB::table('tasks')
            ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
            ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
            ->where('tasks.id', $id)
            ->select(
                'tasks.*',
                DB::raw("CONCAT(COALESCE(allemployees.firstname, ''), ' ', COALESCE(allemployees.lastname, '')) as employee_name"),
                'allemployees.employeeid',
                'projects.projectname'
            )
            ->first();

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        return view('hrms.Employee.Task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
{
    // Fetch the task
    $task = DB::table('tasks')->where('id', $id)->first();
    
    if (!$task) {
        return redirect()->route('tasks.index')->with('error', 'Task not found');
    }

    // Fetch the project
    $project = DB::table('projects')
        ->leftJoin('allemployees as leader', 'projects.projectleader', '=', 'leader.id')
        ->where('projects.projectid', $task->projects)
        ->select(
            'projects.*',
            DB::raw('CONCAT(COALESCE(leader.firstname, ""), " ", COALESCE(leader.lastname, "")) as leaderName')
        )
        ->first();

    // Get team members (only those in the project team)
    $teamIds = array_filter(explode(',', $project->team));
    
    $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereIn('id', $teamIds)
        ->select('id', 'firstname', 'lastname', 'employeeid')
        ->orderBy('firstname')
        ->get();

    return view('hrms.Employee.Task.edit', compact('task', 'employees', 'project'));
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
        // Validate the incoming request
        $request->validate([
            'task' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|integer|exists:allemployees,id',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'due_date' => 'required|date',
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        // Update the task
        $updated = DB::table('tasks')->where('id', $id)->update([
            'task' => $request->task,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'updated_at' => now(),
        ]);
        $this->logActivity($id, 'updated', 'Task updated');


        if (!$updated) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        return redirect()->route('time-tracker.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check task exists first
        $task = DB::table('tasks')->where('id', $id)->first();
    
        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }
    
        // Delete task
        DB::table('tasks')->where('id', $id)->delete();
    
        // Log activity (actor handled inside logActivity)
        $this->logActivity(
            $id,
            'deleted',
            'Task deleted'
        );
    
        return redirect()->back()->with('success', 'Task deleted successfully');
    }
    

    /**
     * Update task status - now supports three statuses: pending, in_progress, completed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        // Fetch the task from the database
        $task = DB::table('tasks')->where('id', $id)->first();

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        // Cycle through statuses: pending -> in_progress -> completed -> pending
        $statusCycle = [
            'pending' => 'in_progress',
            'in_progress' => 'completed',
            'completed' => 'pending'
        ];

        $newStatus = $statusCycle[$task->status] ?? 'pending';

        // Update the status of the task
        DB::table('tasks')->where('id', $id)->update([
            'status' => $newStatus, 
            'updated_at' => now()
        ]);
        $this->logActivity($id, 'status_changed', "Status changed to $newStatus");


        return redirect()->back()->with('success', 'Task status updated successfully');
    }

    /**
     * Get tasks by employee
     *
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function getTasksByEmployee($employeeId)
    {
        $tasks = DB::table('tasks')
            ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
            ->where('tasks.assigned_to', $employeeId)
            ->select(
                'tasks.*',
                'projects.projectname'
            )
            ->orderBy('tasks.due_date', 'asc')
            ->get();

        return response()->json($tasks);
    }

 // Helper method to format seconds
 private function formatSeconds($seconds)
 {
     $hours = floor($seconds / 3600);
     $minutes = floor(($seconds % 3600) / 60);
     $secs = $seconds % 60;
     
     $result = '';
     if ($hours > 0) $result .= $hours . 'h ';
     if ($minutes > 0) $result .= $minutes . 'm ';
     if ($secs > 0 || ($hours === 0 && $minutes === 0)) $result .= $secs . 's';
     
     return trim($result);
 }
 
 public function myTasks(Request $request)
 {
     $employeeId = session()->get('user_id');
     if (!$employeeId) {
         return [
             'tasks' => [],
             'employeeName' => '',
             'timesheetsByTask' => [],
             'breaks' => [],
         ];
     }
 
     // Employee Name
     $emp = DB::table('allemployees')
         ->where('id', $employeeId)
         ->select('firstname', 'lastname')
         ->first();
 
     // Base Query
     $query = DB::table('tasks')
         ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
         ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
         ->where('tasks.assigned_to', $employeeId);
 
     // Default view shows pending work plus tasks currently being tracked.
     if (!$request->filled('status')) {
         $query->whereIn('tasks.status', ['pending', 'in_progress']);
     }
 
     // Apply Filters
     if ($request->filled('status')) {
         $query->where('tasks.status', $request->status);
     }
 
     if ($request->priority != "") {
         $query->where('tasks.priority', $request->priority);
     }
 
     if ($request->project != "") {
         $query->where('tasks.projects', $request->project);
     }
 
     // Final Task List
     $tasks = $query->select(
             'tasks.*',
             'projects.id as project_db_id',
             'projects.projectname',
             'allemployees.firstname as emp_first',
             'allemployees.lastname as emp_last'
         )
         ->orderBy('tasks.due_date', 'asc')
         ->get();
 
     // Get all timesheets for these tasks (grouped by task_id)
     $taskIds = $tasks->pluck('id')->values();
     
     $allTimesheets = DB::table('timesheet')
         ->where('employee_id', $employeeId)
         ->whereIn('task_id', $taskIds)
         ->orderBy('start_date', 'asc')
         ->orderBy('start_time', 'asc')
         ->get()
         ->groupBy('task_id');
 
     // Get all breaks for all timesheets
     $timesheetIds = $allTimesheets->flatten()->pluck('id')->unique()->values();
     $breaks = [];
     
     if ($timesheetIds->count() > 0) {
         $breaksData = DB::table('breaks')
             ->whereIn('timesheet_id', $timesheetIds)
             ->get()
             ->groupBy('timesheet_id');
         
         $breaks = $breaksData->toArray();
     }
 
     return [
         'tasks' => $tasks,
         'employeeName' => $emp ? trim(($emp->firstname ?? '') . ' ' . ($emp->lastname ?? '')) : 'Me',
         'timesheetsByTask' => $allTimesheets,
         'breaks' => $breaks,
     ];
 }
 

public function employeeProjectTasks(Request $request)
{
    $query = DB::table('timesheet')
        ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->join('projects', 'projects.id', '=', 'timesheet.project_id')
        ->join('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->leftJoin('breaks', 'breaks.timesheet_id', '=', 'timesheet.id')
        ->select(
            'allemployees.firstname as employee_name',
            'projects.projectname',
            'tasks.task as task_name',
            'tasks.description as task_description',
            'tasks.due_date',
            'timesheet.start_date',
            'timesheet.start_time',
            'timesheet.end_date',
            'timesheet.end_time',
            'tasks.status',
            DB::raw('IFNULL(SUM(breaks.break_duration_seconds), 0) as total_break_seconds'),
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT("Start: ", breaks.break_start, " - End: ", breaks.break_end) SEPARATOR ", ") as break_details'),
            DB::raw('TIMESTAMPDIFF(SECOND, CONCAT(timesheet.start_date, " ", timesheet.start_time), CONCAT(timesheet.end_date, " ", timesheet.end_time)) as total_work_seconds')
        )
        ->groupBy(
            'timesheet.id',
            'allemployees.firstname',
            'projects.projectname',
            'tasks.task',
            'tasks.description',
            'tasks.due_date',
            'timesheet.start_date',
            'timesheet.start_time',
            'timesheet.end_date',
            'timesheet.end_time',
            'tasks.status'
        );

    // ✅ Apply Filters
    if ($request->employee_id) {
        $query->where('timesheet.employee_id', $request->employee_id);
    }

    if ($request->project_id) {
        $query->where('timesheet.project_id', $request->project_id);
    }

    if ($request->from_date && $request->to_date) {
        $query->whereBetween('timesheet.start_date', [$request->from_date, $request->to_date]);
    }

    $taskReports = $query->orderBy('timesheet.start_date', 'desc')->get();

    // Convert seconds into readable time
    foreach ($taskReports as $report) {
        $report->total_work_time = gmdate("H:i:s", $report->total_work_seconds);
        $report->total_break_time = gmdate("H:i:s", $report->total_break_seconds);
    }

   // For dropdowns - only active employees (deleted_at = 0 or NULL)
$employees = DB::table('allemployees')
->select('id', 'firstname')
->where(function ($query) {
    $query->where('deleted_at', 0)
          ->orWhereNull('deleted_at');
})
->get();

    $projects = DB::table('projects')->select('id', 'projectname')->get();

    return view('hrms.Employee.Task.employee_project_tasks', compact('taskReports', 'employees', 'projects'));
}
private function logActivity($taskId, $action, $description = null)
{
    $role = session('role');

    if ($role === 'employee') {
        $userId = session('user_id');
        $performedBy = 'employee';
    } else {
        $userId = session('admin_id');
        $performedBy = 'admin';
    }

    // 🔹 Fetch employee/admin name
    if ($performedBy === 'employee') {
        $user = DB::table('allemployees')
            ->where('id', $userId)
            ->select(DB::raw("CONCAT(firstname,' ',lastname) as full_name"))
            ->first();

        $actorName = $user ? $user->full_name : 'Employee';
    } else {
        $actorName = 'Admin';
    }

    // 🔹 Append name into description
    $finalDescription = $description
        ? $description . ' by ' . $actorName
        : ucfirst($action) . ' by ' . $actorName;

    DB::table('task_activity_log')->insert([
        'task_id'      => $taskId,
        'action'       => $action,
        'performed_by' => $performedBy,
        'user_id'      => $userId,
        'description'  => $finalDescription,
        'created_at'   => now()
    ]);
}


}
