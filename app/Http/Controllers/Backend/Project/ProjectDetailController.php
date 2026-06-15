<?php

namespace App\Http\Controllers\Backend\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ProjectDetailController extends Controller
{
   public function index($projectid)
{
    // First, get the project without joins to debug
    $project = DB::table('projects')
        ->where('projectid', $projectid)
        ->first();

    if (!$project) {
        abort(404, "Project not found");
    }

    // Debug: Check what's stored in client field
    // dd($project->client); // Uncomment to see what's in the client field

    // Get leader name
    $leader = DB::table('allemployees')
        ->where('id', $project->projectleader)
        ->select(DB::raw("CONCAT(firstname,' ',lastname) as leader_name"))
        ->first();
    
    $project->leader_name = $leader ? $leader->leader_name : 'Leader Not Found';

    // Get client name - handle multiple scenarios
    $clientName = 'No Client Assigned';
    
    if (!empty($project->client)) {
        // First try: Check if client is a numeric ID
        if (is_numeric($project->client)) {
            $client = DB::table('clients')
                ->where('id', (int)$project->client)
                ->select(DB::raw("CONCAT(first_name,' ',last_name) as name"))
                ->first();
            
            if ($client) {
                $clientName = $client->name;
            } else {
                // If no client found by ID, check if it's a name stored directly
                $clientName = $project->client;
            }
        } else {
            // Client field contains a name directly
            $clientName = $project->client;
        }
    }
    
    $project->client_name = $clientName;

    // Count project users
    $teamList = $project->team ? explode(',', $project->team) : [];
    $project->project_users_count = count(array_filter($teamList));

    // Ensure description exists
    $project->description = $project->description ?? '';

    // Fetch tasks of project
    $tasks = DB::table('tasks')
        ->where('projects', $projectid)
        ->get();

    // Task counts
    $totalTasks = $tasks->count();
    $completedTasks = $tasks->where('status', 'completed')->count();
    $inProgressTasks = $tasks->where('status', 'in-progress')->count();
    $pendingTasks = $tasks->where('status', 'pending')->count();

    // Overdue tasks
    $overdueTasks = $tasks->filter(function($t){
        return $t->due_date < now()->toDateString() && $t->status !== 'completed';
    })->count();

    // Task delay calculation
    $taskDelays = $tasks->map(function($t){
        if ($t->status !== 'completed') return null;

        $due = \Carbon\Carbon::parse($t->due_date);
        $completed = \Carbon\Carbon::parse($t->updated_at);

        return $completed->diffInDays($due, false);
    });

    $averageDelay = round($taskDelays->filter()->avg(), 2);

    // Get users in project
    $projectUsers = DB::table('allemployees')
        ->whereIn('id', explode(',', $project->team))
        ->get();

    // -----------------------------
    // BUILD USER PERFORMANCE STATS
    // -----------------------------
    $userStats = [];

    foreach ($projectUsers as $user) {

        $userTasks = $tasks->where('assigned_to', $user->id);

        // FIXED: timesheet stores project_id = PRO-0001
        $timeLogs = DB::table('timesheet')
            ->where('project_id', $project->projectid)
            ->where('employee_id', $user->id)
            ->get();

        // FIXED: break table join also must match PRO-0001
        $breaks = DB::table('breaks')
            ->join('timesheet', 'breaks.timesheet_id', '=', 'timesheet.id')
            ->where('timesheet.employee_id', $user->id)
            ->where('timesheet.project_id', $project->projectid)
            ->sum('break_duration_seconds');

        // Logged hours calculation
        $totalLoggedSeconds = 0;

        foreach ($timeLogs as $log) {

            if (!$log->start_time || !$log->end_time) continue;
        
            // Convert only time
            $start = strtotime($log->start_time);
            $end   = strtotime($log->end_time);
        
            $sessionSeconds = $end - $start;
        
            // Break seconds for this timesheet
            $breakSeconds = DB::table('breaks')
                ->where('timesheet_id', $log->id)
                ->sum('break_duration_seconds');
        
            $actualSeconds = max(0, $sessionSeconds - $breakSeconds);
        
            $totalLoggedSeconds += $actualSeconds;
        }

        // Add stats
        $userStats[] = [
            'name' => $user->firstname . ' ' . $user->lastname,
            'total_tasks' => $userTasks->count(),
            'completed'   => $userTasks->where('status', 'completed')->count(),
            'pending'     => $userTasks->where('status', 'pending')->count(),
            'overdue'     => $userTasks->filter(fn($t) =>
                $t->due_date < date('Y-m-d') && $t->status != 'completed'
            )->count(),
            'logged_hours' => round($totalLoggedSeconds / 3600, 2),
            'break_hours'  => round($breaks / 3600, 2),
        ];
    }

    // ==========================
    // WEEKLY LOGGED HOURS GRAPH
    // ==========================

    // Initialize week structure
    $weeklyHours = [
        'Mon' => 0,
        'Tue' => 0,
        'Wed' => 0,
        'Thu' => 0,
        'Fri' => 0,
        'Sat' => 0,
        'Sun' => 0
    ];

    // Fetch all timesheet logs for the project (all employees)
    $weeklyLogs = DB::table('timesheet')
        ->where('project_id', $project->projectid)
        ->get();

    foreach ($weeklyLogs as $log) {

        if (!$log->start_time || !$log->end_time) continue;

        // Convert only time
        $start = strtotime($log->start_time);
        $end   = strtotime($log->end_time);

        $sessionSeconds = $end - $start;

        // Break seconds for this timesheet
        $breakSeconds = DB::table('breaks')
            ->where('timesheet_id', $log->id)
            ->sum('break_duration_seconds');

        $actualSeconds = max(0, $sessionSeconds - $breakSeconds);

        // Determine day of week
        $day = date('D', strtotime($log->created_at ?: $log->start_date));

        if (array_key_exists($day, $weeklyHours)) {
            $weeklyHours[$day] += round($actualSeconds / 3600, 2);
        }
    }

    $teamIds = array_filter(explode(',', $project->team));

    $projectTeam = DB::table('allemployees')
        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
        ->whereIn('allemployees.id', $teamIds)
        ->select(
            'allemployees.id',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.profile_image',
            'designation.designation as designation_name'
        )
        ->get()
        ->map(function ($user) use ($project) {
            $user->is_leader = $user->id == $project->projectleader;
            return $user;
        });

    // ======================
    // ACTIVITY LOGS
    // ======================

    $projectLogs = DB::table('project_activity_log')
        ->leftJoin('allemployees', 'project_activity_log.user_id', '=', 'allemployees.id')
        ->where('project_activity_log.project_id', $project->projectid)
        ->select(
            'project_activity_log.action',
            'project_activity_log.description',
            'project_activity_log.created_at',
            DB::raw("
                CASE 
                    WHEN project_activity_log.performed_by = 'admin' 
                    THEN 'Admin'
                    ELSE CONCAT(allemployees.firstname,' ',allemployees.lastname)
                END AS actor_name
            "),
            DB::raw("NULL AS task_name")
        );

    $taskLogs = DB::table('task_activity_log')
        ->leftJoin('tasks', 'task_activity_log.task_id', '=', 'tasks.id')
        ->leftJoin('allemployees', 'task_activity_log.user_id', '=', 'allemployees.id')
        ->where('tasks.projects', $project->projectid)
        ->select(
            'task_activity_log.action',
            'task_activity_log.description',
            'task_activity_log.created_at',
            DB::raw("
                CASE 
                    WHEN task_activity_log.performed_by = 'admin'
                    THEN 'Admin'
                    ELSE CONCAT(allemployees.firstname,' ',allemployees.lastname)
                END AS actor_name
            "),
            DB::raw("tasks.task AS task_name")
        );

    // ======================
    // TIMESHEET ACTIVITY LOG
    // ======================
    $timesheetLogs = DB::table('timesheet_activity_log')
        ->leftJoin('tasks', 'timesheet_activity_log.task_id', '=', 'tasks.id')
        ->leftJoin('allemployees', 'timesheet_activity_log.user_id', '=', 'allemployees.id')
        ->where('tasks.projects', $project->projectid)
        ->select(
            'timesheet_activity_log.action',
            'timesheet_activity_log.description',
            'timesheet_activity_log.created_at',
            DB::raw("
                CASE 
                    WHEN timesheet_activity_log.performed_by = 'admin'
                    THEN 'Admin'
                    ELSE CONCAT(allemployees.firstname,' ',allemployees.lastname)
                END AS actor_name
            "),
            DB::raw("tasks.task AS task_name")
        );

    $activityLogs = $projectLogs
        ->unionAll($taskLogs)
        ->unionAll($timesheetLogs)
        ->orderBy('created_at', 'desc')
        ->get();

    // -----------------------------
    // EMPLOYEE PERFORMANCE SUMMARY
    // -----------------------------
    $topPerformer = collect($userStats)->sortByDesc('logged_hours')->first();
    $mostBreaks   = collect($userStats)->sortByDesc('break_hours')->first();
    $mostOverdue  = collect($userStats)->sortByDesc('overdue')->first();
    $bestOnTime   = collect($userStats)->sortByDesc(fn($u) => $u['completed'] - $u['overdue'])->first();
    $leastActive  = collect($userStats)->sortBy('logged_hours')->first();

    return view('hrms.Employee.Project.project_details', compact(
        'project',
        'projectUsers',
        'projectTeam',
        'totalTasks', 'completedTasks', 'pendingTasks', 'inProgressTasks', 'overdueTasks',
        'averageDelay', 'userStats',
        'topPerformer', 'mostBreaks', 'mostOverdue', 'bestOnTime', 'leastActive',
        'weeklyHours',
        'activityLogs'
    ));
}
    
    
    
    

public function loadTasks(Request $request, $projectid)
{
    $query = DB::table('tasks')
        ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
        ->where('tasks.projects', $projectid)
        ->select(
            'tasks.id',
            'tasks.task as task_name',
            'tasks.due_date',
            'tasks.status',
            DB::raw("CONCAT(allemployees.firstname,' ', allemployees.lastname) as employee"),

            // attempts = number of timesheet rows
            DB::raw("(SELECT COUNT(*) FROM timesheet 
                        WHERE timesheet.task_id = tasks.id) AS attempts"),

            // latest start time
            DB::raw("(SELECT MAX(start_time) FROM timesheet 
                        WHERE timesheet.task_id = tasks.id) AS start_time"),

            // latest end time
            DB::raw("(SELECT MAX(end_time) FROM timesheet 
                        WHERE timesheet.task_id = tasks.id) AS end_time"),

            // total break minutes
            DB::raw("(SELECT SUM(break_duration_seconds)/60 
                     FROM breaks 
                     JOIN timesheet ON breaks.timesheet_id = timesheet.id
                     WHERE timesheet.task_id = tasks.id) AS break_minutes")
        );

    // Filters
    if ($request->employee_id) {
        $query->where('tasks.assigned_to', $request->employee_id);
    }

    if ($request->status) {
        $query->where('tasks.status', $request->status);
    }

    if ($request->due_date) {
        $query->whereDate('tasks.due_date', $request->due_date);
    }

    if ($request->task_name) {
        $query->where('tasks.task', 'LIKE', "%{$request->task_name}%");
    }

    $tasks = $query->get();

    foreach ($tasks as $t) {
        $t->status_color = match($t->status) {
            'completed' => 'success',
            'in-progress' => 'primary',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    return response()->json($tasks);
}



public function loadFeeds($projectid)
{
    $project = DB::table('projects')->where('projectid', $projectid)->first();

    $feeds = DB::table('project_feeds')
        ->leftJoin('allemployees', 'project_feeds.user_id', '=', 'allemployees.id')
        ->select(
            'project_feeds.*',
            DB::raw("CASE 
                        WHEN project_feeds.role = 'admin' 
                        THEN 'Admin'
                        ELSE CONCAT(allemployees.firstname, ' ', allemployees.lastname)
                    END as sender_name")
        )
        ->where('project_id', $project->id)
        ->orderBy('project_feeds.created_at', 'asc')
        ->get();

    return response()->json($feeds);
}


public function sendFeed(Request $request)
{
    $project = DB::table('projects')->where('projectid', $request->projectid)->first();

    $role = session('role');
    $userId = session('user_id');

    // Fetch name
    if ($role === 'admin') {
        $name = "Admin";
    } else {
        $emp = DB::table('allemployees')->where('id', $userId)->first();
        $name = $emp->firstname . ' ' . $emp->lastname;
    }

    DB::table('project_feeds')->insert([
        'project_id' => $project->id,
        'user_id' => $role === 'admin' ? null : $userId,
        'role' => $role,
        'message' => $request->message,
        'created_at' => now()
    ]);

    return response()->json(['name' => $name, 'message' => $request->message]);
}
public function downloadProjectFile($projectid)
{
    // Get project
    $project = DB::table('projects')
        ->where('projectid', $projectid)
        ->first();

    if (!$project || !$project->projectfile) {
        abort(404, 'File not found');
    }

    // File path stored in DB
    $relativePath = $project->projectfile; 
    // example: uploads/projects/filename.jpg

    // Full storage path
    $fullPath = storage_path('app/public/' . $relativePath);

    if (!file_exists($fullPath)) {
        abort(404, 'File does not exist on server');
    }

    // Download file
    return response()->download($fullPath);
}

   
}
