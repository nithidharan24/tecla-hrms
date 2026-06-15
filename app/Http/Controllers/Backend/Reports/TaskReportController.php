<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PDF;

class TaskReportController extends Controller
{
    public function index(Request $request)
    {
        $tasksQuery = DB::table('timesheet')
            ->leftJoin('tasks', 'timesheet.task_id', '=', 'tasks.id')
            ->leftJoin('allemployees as assignee', 'tasks.assigned_to', '=', 'assignee.id')
            ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid') // Join with projects
            ->select(
                'timesheet.*',
                'tasks.task as task_name',
                'tasks.status as task_status',
                'tasks.priority as task_priority',
                'tasks.projects as project_id',
                'projects.projectname',
                DB::raw("CONCAT(assignee.firstname, ' ', assignee.lastname) as assigned_to_name")
            );

        // Apply filters if any
        if ($request->filled('project')) {
            // Fix: Use 'tasks.projects' instead of 'tasks.project'
            $tasksQuery->where('tasks.projects', $request->project);
        }
        
        if ($request->filled('status')) {
            $tasksQuery->where('tasks.status', $request->status);
        }

        $tasks = $tasksQuery->get();
        
        // Get projects for the filter dropdown
        $projects = DB::table('projects')
            ->select('projectid', 'projectname')
            ->where('deleted_at', 0)
            ->get();

        return view('hrms.hr.Reports.tasks-reports.index', compact('tasks', 'projects'));
    }

    public function exportCSV(Request $request)
    {
        $tasks = $this->getFilteredTasks($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="task_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'S.No',
                'Task Name',
                'Project',
                'Start Date',
                'End Date',
                'Status',
                'Priority',
                'Assigned To'
            ]);

            // Add data rows
            foreach ($tasks as $index => $task) {
                fputcsv($file, [
                    $index + 1,
                    $task->task_name ?? $task->task,
                    $task->projectname ?? 'N/A',
                    isset($task->start_date) ? date('d-m-Y', strtotime($task->start_date)) : 'N/A',
                    isset($task->end_date) ? date('d-m-Y', strtotime($task->end_date)) : 'N/A',
                    ucfirst($task->task_status ?? $task->status),
                    ucfirst($task->task_priority ?? $task->priority),
                    $task->assigned_to_name ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $tasks = $this->getFilteredTasks($request);

        // Get project name for filter display
        $projectName = null;
        if ($request->filled('project')) {
            $projectName = DB::table('projects')
                ->where('projectid', $request->project)
                ->value('projectname');
        }

        $data = [
            'title' => 'Task Report - ' . date('Y-m-d'),
            'tasks' => $tasks,
            'filters' => [
                'project' => $projectName,
                'status' => $request->status
            ]
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.tasks-reports.pdf', $data);
        
        return $pdf->download('task_report_' . date('Y-m-d') . '.pdf');
    }
private function getFilteredTasks(Request $request)
{
    $query = DB::table('timesheet')
        ->leftJoin('tasks', 'timesheet.task_id', '=', 'tasks.id')
        ->leftJoin('projects', 'tasks.projects', '=', 'projects.projectid')
        ->leftJoin('allemployees as assignee', 'tasks.assigned_to', '=', 'assignee.id')
        ->select(
            'tasks.id',
            'tasks.task',
            'tasks.status',
            'tasks.priority',

            // ✅ REAL START & END FROM TIMESHEET
            'timesheet.start_date',
            'timesheet.end_date',

            'projects.projectname',

            DB::raw("CONCAT(assignee.firstname, ' ', assignee.lastname) as assigned_to_name")
        );

    if ($request->filled('project')) {
        $query->where('tasks.projects', $request->project);
    }

    if ($request->filled('status')) {
        $query->where('tasks.status', $request->status);
    }

    return $query->get();
}
}