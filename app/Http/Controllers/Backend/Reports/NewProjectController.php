<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewProjectController extends Controller
{
    public function index()
    {
        // Clients for Project Summary filter
        $clients = DB::table('projects')
            ->where('deleted_at', 0)
            ->whereNotNull('client')
            ->select('client')
            ->distinct()
            ->orderBy('client')
            ->get();

        // Employees for both tabs (PM and Assigned To)
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->orderBy('firstname')
            ->get();

        // Projects for Task Summary filter
        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->select('projectid', 'projectname')
            ->orderBy('projectname')
            ->get();

        return view('hrms.hr.Reports.new_project-reports.index', compact('clients', 'employees', 'projects'));
    }

    public function getProjectSummaryReport(Request $request)
    {
        try {
            // Total records (no filters)
            $base = DB::table('projects')->where('deleted_at', 0);
            $recordsTotal = (clone $base)->count();

            // Apply filters
            $query = (clone $base)->select(
                'id',
                DB::raw('projectid as projectId'),
                'projectname',
                'client',
                'startdate',
                'enddate',
                'priority',
                'status',
                'totalhours',
                'projectleader',
                'team'
            );

            if ($request->filled('project_name')) {
                $query->where('projectname', 'like', '%'.$request->project_name.'%');
            }
            if ($request->filled('client')) {
                $query->where('client', $request->client);
            }
            if ($request->filled('project_manager')) {
                $query->where('projectleader', $request->project_manager);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('startdate', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('enddate', '<=', $request->end_date);
            }
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            $recordsFiltered = (clone $query)->count();

            // Ordering guards (order only by real DB columns)
            $allowedOrder = [
                'projectId' => 'projectid',
                'projectname' => 'projectname',
                'client' => 'client',
                'startdate' => 'startdate',
                'enddate' => 'enddate',
                'status' => 'status',
                'priority' => 'priority',
                'totalhours' => 'totalhours',
            ];
            if ($request->has('order')) {
                $requested = $request->columns[$request->order[0]['column']]['data'] ?? 'projectname';
                $dir = $request->order[0]['dir'] ?? 'asc';
                $col = $allowedOrder[$requested] ?? 'projectname';
                $query->orderBy($col, $dir);
            } else {
                $query->orderBy('projectname', 'asc');
            }

            // Paging
            if ($request->has('start') && $request->has('length')) {
                $query->offset((int) $request->start)->limit((int) $request->length);
            }

            $projects = $query->get();

            // Hydrate leader_name and team_names
            foreach ($projects as $project) {
                $leader = DB::table('allemployees')
                    ->where('id', $project->projectleader)
                    ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                    ->first();
                $project->leader_name = $leader->name ?? 'N/A';

                $teamIds = array_filter(array_map('trim', explode(',', (string) $project->team)));
                if (!empty($teamIds)) {
                    $teamMembers = DB::table('allemployees')
                        ->whereIn('id', $teamIds)
                        ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                        ->pluck('name')
                        ->toArray();
                    $project->team_names = implode(', ', $teamMembers);
                } else {
                    $project->team_names = 'N/A';
                }
            }

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $projects,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getProjectSummaryReport: '.$e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function exportProjectSummary(Request $request)
    {
        try {
            $query = DB::table('projects')
                ->where('deleted_at', 0)
                ->select(
                    DB::raw('projectid as projectId'),
                    'projectname',
                    'client',
                    'startdate',
                    'enddate',
                    'priority',
                    'status',
                    'totalhours',
                    'rate',
                    'description'
                );

            // Filters
            if ($request->filled('project_name')) {
                $query->where('projectname', 'like', '%'.$request->project_name.'%');
            }
            if ($request->filled('client')) {
                $query->where('client', $request->client);
            }
            if ($request->filled('project_manager')) {
                $query->where('projectleader', $request->project_manager);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('startdate', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('enddate', '<=', $request->end_date);
            }
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            $projects = $query->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="project_summary_report_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($projects) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Project ID','Project Name','Client','Start Date','End Date','Priority','Status','Total Hours']);
                foreach ($projects as $p) {
                    fputcsv($file, [
                        $p->projectId,
                        $p->projectname,
                        $p->client,
                        $p->startdate,
                        $p->enddate,
                        $p->priority,
                        $p->status,
                        $p->totalhours,
                        $p->rate,
                     
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error in exportProjectSummary: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getTaskSummaryReport(Request $request)
    {
        try {
            // Totals
            $base = DB::table('tasks')->where('tasks.deleted_at', 0);
            $recordsTotal = (clone $base)->count();

            $query = (clone $base)
                ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
                ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
                ->select(
                    'tasks.id',
                    DB::raw('tasks.task as task_name'),
                    'tasks.description',
                    'tasks.due_date',
                    'tasks.priority',
                    'tasks.status',
                    'tasks.created_at',
                    'projects.projectname as project_name',
                    DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as assigned_to_name")
                );

            // Filters
            if ($request->filled('project')) {
                $query->where('tasks.projects', $request->project);
            }
            if ($request->filled('task_name')) {
                $query->where('tasks.task', 'like', '%'.$request->task_name.'%');
            }
            if ($request->filled('assigned_to')) {
                $query->where('tasks.assigned_to', $request->assigned_to);
            }
            if ($request->filled('status')) {
                $query->where('tasks.status', $request->status);
            }
            if ($request->filled('due_date')) {
                $query->whereDate('tasks.due_date', $request->due_date);
            }
            if ($request->filled('priority')) {
                $query->where('tasks.priority', $request->priority);
            }

            $recordsFiltered = (clone $query)->count();

            // Ordering guards
            if ($request->has('order')) {
                $requested = $request->columns[$request->order[0]['column']]['data'] ?? 'created_at';
                $dir = $request->order[0]['dir'] ?? 'desc';

                switch ($requested) {
                    case 'project_name':
                        $query->orderBy('projects.projectname', $dir);
                        break;
                    case 'task_name':
                        $query->orderBy('tasks.task', $dir);
                        break;
                    case 'assigned_to_name':
                        $query->orderBy('allemployees.firstname', $dir)
                              ->orderBy('allemployees.lastname', $dir);
                        break;
                    case 'status':
                        $query->orderBy('tasks.status', $dir);
                        break;
                    case 'due_date':
                        $query->orderBy('tasks.due_date', $dir);
                        break;
                    case 'priority':
                        $query->orderBy('tasks.priority', $dir);
                        break;
                    case 'created_at':
                    default:
                        $query->orderBy('tasks.created_at', 'desc');
                        break;
                }
            } else {
                $query->orderBy('tasks.created_at', 'desc');
            }

            // Paging
            if ($request->has('start') && $request->has('length')) {
                $query->offset((int) $request->start)->limit((int) $request->length);
            }

            $tasks = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $tasks,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaskSummaryReport: '.$e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function exportTaskSummary(Request $request)
    {
        try {
            $query = DB::table('tasks')
                ->where('tasks.deleted_at', 0)
                ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
                ->leftJoin('allemployees', 'tasks.assigned_to', '=', 'allemployees.id')
                ->select(
                    'projects.projectname as project_name',
                    DB::raw('tasks.task as task_name'),
                    DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as assigned_to"),
                    'tasks.status',
                    'tasks.due_date',
                    'tasks.priority',
                    'tasks.description'
                );

            // Filters
            if ($request->filled('project')) {
                $query->where('tasks.projects', $request->project);
            }
            if ($request->filled('task_name')) {
                $query->where('tasks.task', 'like', '%'.$request->task_name.'%');
            }
            if ($request->filled('assigned_to')) {
                $query->where('tasks.assigned_to', $request->assigned_to);
            }
            if ($request->filled('status')) {
                $query->where('tasks.status', $request->status);
            }
            if ($request->filled('due_date')) {
                $query->whereDate('tasks.due_date', $request->due_date);
            }
            if ($request->filled('priority')) {
                $query->where('tasks.priority', $request->priority);
            }

            $tasks = $query->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="task_summary_report_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($tasks) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Project','Task Name','Assigned To','Status','Due Date','Priority','Description']);
                foreach ($tasks as $t) {
                    fputcsv($file, [
                        $t->project_name,
                        $t->task_name,
                        $t->assigned_to,
                        $t->status,
                        $t->due_date,
                        $t->priority,
                        $t->description,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error in exportTaskSummary: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }
    public function getTaskBoardReport(Request $request)
{
    try {
        // Total projects (no filters)
        $base = DB::table('projects')->where('deleted_at', 0);
        $recordsTotal = (clone $base)->count();

        // Aggregated query: projects + tasks
        $query = DB::table('projects')
            ->where('projects.deleted_at', 0)
            ->leftJoin('tasks', function ($join) {
                $join->on('tasks.projects', '=', 'projects.projectid')
                     ->where('tasks.deleted_at', 0);
            })
            ->select(
                'projects.id',
                DB::raw('projects.projectid as projectId'),
                'projects.projectname',
                'projects.client',
                'projects.projectleader',
                'projects.team',
                'projects.startdate',
                'projects.enddate',
                // Aggregations
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw("SUM(CASE WHEN tasks.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks"),
                DB::raw("SUM(CASE WHEN tasks.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks"),
                DB::raw("SUM(CASE WHEN tasks.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks"),
                DB::raw("ROUND(
                    IF(COUNT(tasks.id) > 0, 
                        SUM(CASE WHEN tasks.status = 'completed' THEN 1 ELSE 0 END) / COUNT(tasks.id) * 100, 
                        0
                    ), 1
                ) as progress_percentage")
            );

        // Project-level filters
        if ($request->filled('project_name')) {
            $query->where('projects.projectname', 'like', '%'.$request->project_name.'%');
        }
        if ($request->filled('client')) {
            $query->where('projects.client', $request->client);
        }
        if ($request->filled('project_manager')) {
            $query->where('projects.projectleader', $request->project_manager);
        }

        // Task-level filters (affect counts)
        if ($request->filled('assigned_to')) {
            $query->where('tasks.assigned_to', $request->assigned_to);
        }
        if ($request->filled('task_status')) {
            $query->where('tasks.status', $request->task_status);
        }
        if ($request->filled('priority')) {
            $query->where('tasks.priority', $request->priority);
        }

        // Important: group by project to aggregate
        $query->groupBy(
            'projects.id',
            'projects.projectid',
            'projects.projectname',
            'projects.client',
            'projects.projectleader',
            'projects.team',
            'projects.startdate',
            'projects.enddate'
        );

        // Count after filters (groups)
        $recordsFiltered = (clone $query)->get()->count();

        // Ordering guards
        $allowedOrder = [
            'projectId'            => 'projects.projectid',
            'projectname'          => 'projects.projectname',
            'client'               => 'projects.client',
            'total_tasks'          => 'total_tasks',
            'pending_tasks'        => 'pending_tasks',
            'in_progress_tasks'    => 'in_progress_tasks',
            'completed_tasks'      => 'completed_tasks',
            'progress_percentage'  => 'progress_percentage',
            'enddate'              => 'projects.enddate',
            'startdate'            => 'projects.startdate',
        ];

        if ($request->has('order')) {
            $requested = $request->columns[$request->order[0]['column']]['data'] ?? 'projectname';
            $dir = $request->order[0]['dir'] ?? 'asc';
            $col = $allowedOrder[$requested] ?? 'projects.projectname';

            // order by alias or real column
            if (in_array($col, ['total_tasks','pending_tasks','in_progress_tasks','completed_tasks','progress_percentage'])) {
                $query->orderBy(DB::raw($col), $dir);
            } else {
                $query->orderBy($col, $dir);
            }
        } else {
            $query->orderBy('projects.projectname', 'asc');
        }

        // Paging
        if ($request->has('start') && $request->has('length')) {
            $query->offset((int) $request->start)->limit((int) $request->length);
        }

        $rows = $query->get();

        // Hydrate PM name and team names
        foreach ($rows as $row) {
            // Leader name
            $leader = DB::table('allemployees')
                ->where('id', $row->projectleader)
                ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                ->first();
            $row->leader_name = $leader->name ?? 'N/A';

            // Team names
            $teamIds = array_filter(array_map('trim', explode(',', (string) $row->team)));
            if (!empty($teamIds)) {
                $teamMembers = DB::table('allemployees')
                    ->whereIn('id', $teamIds)
                    ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                    ->pluck('name')
                    ->toArray();
                $row->team_names = implode(', ', $teamMembers);
            } else {
                $row->team_names = 'N/A';
            }
        }

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    } catch (\Exception $e) {
        Log::error('Error in getTaskBoardReport: '.$e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage(),
        ]);
    }
}

public function exportTaskBoardReport(Request $request)
{
    try {
        $query = DB::table('projects')
            ->where('projects.deleted_at', 0)
            ->leftJoin('tasks', function ($join) {
                $join->on('tasks.projects', '=', 'projects.projectid')
                     ->where('tasks.deleted_at', 0);
            })
            ->select(
                DB::raw('projects.projectid as projectId'),
                'projects.projectname',
                'projects.client',
                'projects.projectleader',
                'projects.team',
                'projects.startdate',
                'projects.enddate',
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw("SUM(CASE WHEN tasks.status = 'pending' THEN 1 ELSE 0 END) as pending_tasks"),
                DB::raw("SUM(CASE WHEN tasks.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks"),
                DB::raw("SUM(CASE WHEN tasks.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks"),
                DB::raw("ROUND(
                    IF(COUNT(tasks.id) > 0, 
                        SUM(CASE WHEN tasks.status = 'completed' THEN 1 ELSE 0 END) / COUNT(tasks.id) * 100, 
                        0
                    ), 1
                ) as progress_percentage")
            );

        // Project filters
        if ($request->filled('project_name')) {
            $query->where('projects.projectname', 'like', '%'.$request->project_name.'%');
        }
        if ($request->filled('client')) {
            $query->where('projects.client', $request->client);
        }
        if ($request->filled('project_manager')) {
            $query->where('projects.projectleader', $request->project_manager);
        }

        // Task filters
        if ($request->filled('assigned_to')) {
            $query->where('tasks.assigned_to', $request->assigned_to);
        }
        if ($request->filled('task_status')) {
            $query->where('tasks.status', $request->task_status);
        }
        if ($request->filled('priority')) {
            $query->where('tasks.priority', $request->priority);
        }

        $query->groupBy(
            'projects.projectid',
            'projects.projectname',
            'projects.client',
            'projects.projectleader',
            'projects.team',
            'projects.startdate',
            'projects.enddate'
        );

        $projects = $query->get();

        // Pre-hydrate leader and team names
        foreach ($projects as $p) {
            $leader = DB::table('allemployees')
                ->where('id', $p->projectleader)
                ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                ->first();
            $p->leader_name = $leader->name ?? 'N/A';

            $teamIds = array_filter(array_map('trim', explode(',', (string) $p->team)));
            if (!empty($teamIds)) {
                $teamMembers = DB::table('allemployees')
                    ->whereIn('id', $teamIds)
                    ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                    ->pluck('name')
                    ->toArray();
                $p->team_names = implode(', ', $teamMembers);
            } else {
                $p->team_names = 'N/A';
            }
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="task_board_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Project ID','Project Name','Client','Project Manager','Team',
                'Total Tasks','Pending','In Progress','Completed','Progress %',
                'Start Date','End Date'
            ]);

            foreach ($projects as $p) {
                fputcsv($file, [
                    $p->projectId,
                    $p->projectname,
                    $p->client,
                    $p->leader_name,
                    $p->team_names,
                    $p->total_tasks,
                    $p->pending_tasks,
                    $p->in_progress_tasks,
                    $p->completed_tasks,
                    $p->progress_percentage,
                    $p->startdate,
                    $p->enddate,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        Log::error('Error in exportTaskBoardReport: '.$e->getMessage());
        return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
    }
}
}