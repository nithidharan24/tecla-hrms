<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimesheetReportController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $employeeId = $request->query('employee_id');
        $projectId  = $request->query('project_id'); // projectid from projects table
        $from       = $request->query('from');       // assigned_date from
        $to         = $request->query('to');         // assigned_date to
        $keyword    = $request->query('keyword');    // search in description

        // Dropdown data
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'lastname']);

        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->where('status', ['Initiated','Planned','Active','On Hold','Pending','Review','Completed'])
            
            ->orderBy('projectname')
            ->get(['projectid', 'projectname']);

        // Base query
        $query = DB::table('timesheet')
            ->leftJoin('allemployees', 'timesheet.employee_id', '=', 'allemployees.id')
            ->leftJoin('projects', 'timesheet.project_id', '=', 'projects.projectid')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('timesheet.deleted_at', 0)
            
            ->where('projects.deleted_at', 0)
            ->select(
                'timesheet.id',
                'timesheet.employee_id',
                'timesheet.project_id',
                'timesheet.assigned_date',
                'timesheet.assigned_hours',
                'timesheet.total_hours',
                'timesheet.remaining_hours',
                'timesheet.deadline',
                'timesheet.description',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'designation.designation',
                'projects.projectname'
            );

        if ($employeeId) {
            $query->where('timesheet.employee_id', $employeeId);
        }
        if ($projectId) {
            $query->where('timesheet.project_id', $projectId);
        }
        if ($from) {
            $query->whereDate('timesheet.assigned_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('timesheet.assigned_date', '<=', $to);
        }
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('timesheet.description', 'like', '%' . $keyword . '%')
                  ->orWhere('projects.projectname', 'like', '%' . $keyword . '%')
                  ->orWhere(DB::raw("CONCAT(allemployees.firstname,' ',allemployees.lastname)"), 'like', '%' . $keyword . '%');
            });
        }

        $timesheets = $query->orderBy('timesheet.assigned_date', 'desc')->get();

        return view('hrms.hr.Reports.timesheet-reports.index', compact('timesheets', 'employees', 'projects'));
    }

    public function exportCsv(Request $request)
    {
        try {
            $rows = $this->fetchForExport($request);

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="timesheet_report_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'Employee',
                    'Designation',
                    'Project',
                    'Assigned Date',
                    'Assigned Hours',
                    'Total Hours',
                    'Remaining Hours',
                    'Deadline',
                    'Description',
                ]);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')),
                        $r->designation ?? 'N/A',
                        $r->projectname ?? 'N/A',
                        $r->assigned_date,
                        $r->assigned_hours,
                        $r->total_hours,
                        $r->remaining_hours,
                        $r->deadline,
                        $r->description,
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            Log::error('Timesheet CSV export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export CSV: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $rows = $this->fetchForExport($request);

            // barryvdh/laravel-dompdf is commonly used; adjust facade alias if needed.
            if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return redirect()->back()->with('error', 'PDF export not available. Please install barryvdh/laravel-dompdf.');
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('hrms.hr.Reports.timesheet-reports.pdf', [
                'rows' => $rows,
                'exportedAt' => now()->format('d-m-Y H:i'),
            ])->setPaper('a4', 'landscape');

            return $pdf->download('timesheet_report_' . date('Y-m-d') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Timesheet PDF export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    private function fetchForExport(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $projectId  = $request->query('project_id');
        $from       = $request->query('from');
        $to         = $request->query('to');
        $keyword    = $request->query('keyword');

        $query = DB::table('timesheet')
            ->leftJoin('allemployees', 'timesheet.employee_id', '=', 'allemployees.id')
            ->leftJoin('projects', 'timesheet.project_id', '=', 'projects.projectid')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('timesheet.deleted_at', 0)
            ->where('allemployees.deleted_at', 0)
            ->where('projects.deleted_at', 0)
            ->select(
                'timesheet.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'designation.designation',
                'projects.projectname'
            );

        if ($employeeId) {
            $query->where('timesheet.employee_id', $employeeId);
        }
        if ($projectId) {
            $query->where('timesheet.project_id', $projectId);
        }
        if ($from) {
            $query->whereDate('timesheet.assigned_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('timesheet.assigned_date', '<=', $to);
        }
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('timesheet.description', 'like', '%' . $keyword . '%')
                  ->orWhere('projects.projectname', 'like', '%' . $keyword . '%')
                  ->orWhere(DB::raw("CONCAT(allemployees.firstname,' ',allemployees.lastname)"), 'like', '%' . $keyword . '%');
            });
        }

        return $query->orderBy('timesheet.assigned_date', 'desc')->get();
    }
}