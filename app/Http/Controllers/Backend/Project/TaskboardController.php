<?php

namespace App\Http\Controllers\Backend\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $role = session()->get('role'); 
    $employeeId = session()->get('user_id'); // allemployees.id

    $employee = null;
    if ($role === 'employee' && $employeeId) {
        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('department', 'branch_id')
            ->first();
    }

    // Get all projects
    $allProjects = DB::table('projects')->get();
    $projects = collect();

    foreach ($allProjects as $project) {
        $includeProject = true;

        if ($role === 'employee' && $employee) {
            $includeProject = false;

            // Check if logged-in employee is part of this project's team
            $teamIds = array_filter(explode(',', $project->team));
            if (in_array($employeeId, $teamIds)) {
                $includeProject = true;
            }
        }

        if ($includeProject) {
            // ===== Tasks =====
            $tasks = DB::table('tasks')->where('projects', $project->projectid)->get();

            $project->totalTasks = $tasks->count();
            $project->pendingTasks = $tasks->where('status', 'pending')->count();
            $project->inProgressTasks = $tasks->where('status', 'in_progress')->count();
            $project->completedTasks = $tasks->where('status', 'completed')->count();

            $project->progressPercentage = $project->totalTasks > 0 
                ? round(($project->completedTasks / $project->totalTasks) * 100, 1) 
                : 0;

            // ===== Team Members =====
            $teamIds = array_filter(explode(',', $project->team));
            $teamMembersQuery = DB::table('allemployees')
                ->whereIn('id', $teamIds);

            if ($role === 'employee' && $employee) {
                if ($employee->department) {
                    $teamMembersQuery->where('department', $employee->department);
                }
                if ($employee->branch_id) {
                    $teamMembersQuery->where('branch_id', $employee->branch_id);
                }
            }

            $teamMembers = $teamMembersQuery
                ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                ->pluck('name')
                ->toArray();

            if (!empty($teamMembers)) {
                $formattedNames = '';
                foreach ($teamMembers as $index => $name) {
                    $formattedNames .= ($index + 1) . '. ' . $name . '<br>';
                }
                $project->teamNames = $formattedNames;
            } else {
                $project->teamNames = 'N/A';
            }

            $projects->push($project);
        }
    }

    return view('hrms.Employee.Taskboard.index', compact('projects'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Get the project ID from the request
        $project_id = $request->query('projectid');
    
        // Fetch the project from the database using the project ID
        $project = DB::table('projects')->where('id', $project_id)->first();
    
        // Check if the project exists
        if (!$project) {
            return redirect()->route('taskboard.index')->with('error', 'Project not found');
        }
    
      // ✅ Fetch project leader name from allemployees using projectleader ID (only active employees)
$leader = DB::table('allemployees')
->select(DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
->where('id', $project->projectleader)
->where('deleted_at', 0)
->first();
$testers = DB::table('allemployees')
    ->join('department', 'department.id', '=', 'allemployees.department')
    ->where('department.department', 'Testing')
    ->where('allemployees.deleted_at', 0)
    ->select(
        'allemployees.id', 
        'allemployees.firstname', 
        'allemployees.lastname', 
        'department.department as department_name'
    )
    ->get();


    
        // Add leader name to project object dynamically
        $project->leader_name = $leader ? $leader->full_name : 'N/A';
    
        // Fetch tasks related to the project with employee information
        $tasks = DB::table('tasks')
            ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
            ->where('tasks.projects', $project->projectid)
            ->select(
                'tasks.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'allemployees.employeeid'
            )
            ->orderBy('tasks.created_at', 'desc')
            ->get();
    
        // Calculate task counts
        $totalTasks = $tasks->count();
        $pendingTasks = $tasks->where('status', 'pending')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
    
        // Separate tasks by status for display
        $pendingTasksList = $tasks->where('status', 'pending');
        $inProgressTasksList = $tasks->where('status', 'in_progress');
        $completedTasksList = $tasks->where('status', 'completed');
    
        // Pass all data to the view
        return view('hrms.Employee.Taskboard.create', compact(
            'project', 
            'totalTasks', 
            'pendingTasks', 
            'inProgressTasks', 
            'completedTasks',
            'pendingTasksList',
            'inProgressTasksList',
            'testers',
            'completedTasksList'
        ));
    }
    
    /**
     * Get task statistics for a project
     *
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    public function getProjectStats($projectId)
    {
        $project = DB::table('projects')->where('id', $projectId)->first();
        
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $tasks = DB::table('tasks')->where('projects', $project->projectid)->get();
        
        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->where('due_date', '<', now())
                             ->whereIn('status', ['pending', 'in_progress'])
                             ->count(),
        ];

        $stats['progress_percentage'] = $stats['total'] > 0 
            ? round(($stats['completed'] / $stats['total']) * 100, 1) 
            : 0;

        return response()->json($stats);
    }

    /**
     * Get tasks by status for a project
     *
     * @param  int  $projectId
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function getTasksByStatus($projectId, $status)
    {
        $project = DB::table('projects')->where('id', $projectId)->first();
        
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $tasks = DB::table('tasks')
            ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
            ->where('tasks.projects', $project->projectid)
            ->where('tasks.status', $status)
            ->select(
                'tasks.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'allemployees.employeeid'
            )
            ->orderBy('tasks.due_date', 'asc')
            ->get();

        return response()->json($tasks);
    }
    public function assignTester(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'tester_id' => 'required|integer',
        ]);
    
        // ✅ Check if a tester is already assigned for this project
        $existingAssignment = DB::table('testing_tickets')
            ->where('project_id', $request->project_id)
            ->first();
    
        if ($existingAssignment) {
            return back()->with('error', 'A tester has already been assigned to this project!');
        }
    
        // ✅ If not assigned, insert a new record
        DB::table('testing_tickets')->insert([
            'project_id' => $request->project_id,
            'tester_id' => $request->tester_id,
            'assigned_at' => now(),
        ]);
    
        return back()->with('success', 'Tester assigned successfully to the project!');
    }
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}