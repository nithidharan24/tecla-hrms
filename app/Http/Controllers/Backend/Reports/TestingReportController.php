<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class TestingReportController extends Controller
{
    /**
     * Display the testing report dashboard with filters
     */
    public function index(Request $request)
    {
        // Get current user role and ID
        $role = session('role', 'employee');
        $employeeId = session('user_id');
        $branchId = session('branch_id');

        // Build base query for statistics
        $statsQuery = $this->buildBaseQuery($request, $role, $employeeId, $branchId);

        // Get statistics for dashboard
        $totalTickets = $statsQuery->count();
        $openTickets = (clone $statsQuery)->where('status', 'Open')->count();
        $inProgressTickets = (clone $statsQuery)->where('status', 'In Progress')->count();
        $resolvedTickets = (clone $statsQuery)->where('status', 'Resolved')->count();
        $closedTickets = (clone $statsQuery)->where('status', 'Closed')->count();

        // Get priority breakdown
        $priorityStats = (clone $statsQuery)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        // Get recent tickets for preview
        $recentTickets = $this->buildBaseQuery($request, $role, $employeeId, $branchId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get branch name if admin
        $branches = [];
        if ($role === 'admin') {
            $branches = DB::table('branches')->get();
        }

        return view('hrms.hr.Reports.testing.index', compact(
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'closedTickets',
            'priorityStats',
            'recentTickets',
            'branches'
        ));
    }

    /**
     * Generate detailed testing report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:summary,detailed,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Open,In Progress,Resolved,Closed',
            'priority' => 'nullable|in:High,Medium,Low',
            'format' => 'required|in:html,pdf,csv'
        ]);

        // Get filters
        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'priority' => $request->priority
        ];

        // Get current user role and ID
        $role = session('role', 'employee');
        $employeeId = session('user_id');
        $branchId = session('branch_id');

        // Build query based on filters
        $query = $this->buildBaseQuery($request, $role, $employeeId, $branchId)
            ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
            ->leftJoin('allemployees as assigned', 'testing_tickets.assigned_to', '=', 'assigned.id')
            ->leftJoin('allemployees as creator', 'testing_tickets.created_by', '=', 'creator.id')
            ->select(
                'testing_tickets.*',
                'projects.projectname',
                DB::raw("CONCAT(assigned.firstname, ' ', assigned.lastname) as assigned_name"),
                DB::raw("CONCAT(creator.firstname, ' ', creator.lastname) as creator_name")
            );

        // Handle different report types
        if ($request->report_type === 'summary') {
            $data = $this->generateSummaryData($query);
            $view = 'hrms.hr.Reports.testing.partials.summary';
        } elseif ($request->report_type === 'detailed') {
            $data = $query->orderBy('created_at', 'desc')->get();
            $view = 'hrms.hr.Reports.testing.partials.detailed';
        } else {
            $data = $query->orderBy('created_at', 'desc')->get();
            $view = 'hrms.hr.Reports.testing.partials.custom';
        }

        // Generate the report in requested format
        if ($request->format === 'pdf') {
            return $this->generatePdfReport(
                'hrms.hr.Reports.testing.pdf_template',
                $data,
                $request->report_type,
                $filters
            )->download('testing_report_' . now()->format('YmdHis') . '.pdf');
        } elseif ($request->format === 'csv') {
            return $this->generateCsv($data, $request->report_type);
        }

        // HTML format - return view
        return view($view, compact('data', 'filters'));
    }

    /**
     * Build base query for reports with common filters
     */
    private function buildBaseQuery(Request $request, $role, $employeeId, $branchId = null)
    {
        $query = DB::table('testing_tickets')
            ->where('testing_tickets.deleted_at', 0);

        // Apply role-based filtering
        if ($role === 'employee') {
            $query->where('testing_tickets.branch_id', $branchId)
                ->where(function($q) use ($employeeId) {
                    $q->where('testing_tickets.created_by', $employeeId)
                      ->orWhere('testing_tickets.assigned_to', $employeeId);
                });
        } elseif ($role === 'admin' && $request->filled('branch_id')) {
            $query->where('testing_tickets.branch_id', $request->branch_id);
        }

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('testing_tickets.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('testing_tickets.created_at', '<=', $request->end_date);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('testing_tickets.status', $request->status);
        }

        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('testing_tickets.priority', $request->priority);
        }

        return $query;
    }

    /**
     * Generate summary data for the report
     */
  private function generateSummaryData($query)
{
    // Status breakdown - specify table name
    $statusData = (clone $query)
        ->select('testing_tickets.status', DB::raw('count(*) as count'))
        ->groupBy('testing_tickets.status')
        ->get();

    // Priority breakdown - specify table name
    $priorityData = (clone $query)
        ->select('testing_tickets.priority', DB::raw('count(*) as count'))
        ->groupBy('testing_tickets.priority')
        ->get();

    // Monthly trend - specify table name
    $monthlyTrend = (clone $query)
        ->select(
            DB::raw('YEAR(testing_tickets.created_at) as year'),
            DB::raw('MONTH(testing_tickets.created_at) as month'),
            DB::raw('count(*) as count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    // Average resolution time - specify table name
    $resolutionStats = (clone $query)
        ->where('testing_tickets.status', 'Closed')
        ->select(
            DB::raw('AVG(TIMESTAMPDIFF(HOUR, testing_tickets.created_at, testing_tickets.updated_at)) as avg_hours'),
            DB::raw('MIN(TIMESTAMPDIFF(HOUR, testing_tickets.created_at, testing_tickets.updated_at)) as min_hours'),
            DB::raw('MAX(TIMESTAMPDIFF(HOUR, testing_tickets.created_at, testing_tickets.updated_at)) as max_hours')
        )
        ->first();

    return [
        'statusData' => $statusData,
        'priorityData' => $priorityData,
        'monthlyTrend' => $monthlyTrend,
        'resolutionStats' => $resolutionStats,
        'totalTickets' => $query->count()
    ];
}

    /**
     * Generate CSV file for the report
     */
    private function generateCsv($data, $reportType)
    {
        $fileName = 'testing_report_' . now()->format('YmdHis') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data, $reportType) {
            $file = fopen('php://output', 'w');
            
            if ($reportType === 'summary') {
                fputcsv($file, ['Report Type', 'Summary Testing Ticket Report']);
                fputcsv($file, ['Generated Date', now()->toDateTimeString()]);
                fputcsv($file, []);
                
                fputcsv($file, ['Status', 'Count']);
                foreach ($data['statusData'] as $row) {
                    fputcsv($file, [$row->status, $row->count]);
                }
                fputcsv($file, []);
                
                fputcsv($file, ['Priority', 'Count']);
                foreach ($data['priorityData'] as $row) {
                    fputcsv($file, [$row->priority, $row->count]);
                }
                fputcsv($file, []);
                
                fputcsv($file, ['Resolution Time (Hours)', 'Value']);
                fputcsv($file, ['Average', $data['resolutionStats']->avg_hours]);
                fputcsv($file, ['Minimum', $data['resolutionStats']->min_hours]);
                fputcsv($file, ['Maximum', $data['resolutionStats']->max_hours]);
            } else {
                $headers = [
                    'Ticket ID', 'Project', 'Description', 'Priority', 'Status',
                    'Assigned To', 'Created By', 'Created At', 'Updated At', 'Branch'
                ];
                
                fputcsv($file, $headers);
                
                foreach ($data as $ticket) {
                    $branch = DB::table('branches')->where('id', $ticket->branch_id)->first();
                    $branchName = $branch ? $branch->name : 'N/A';
                    
                    fputcsv($file, [
                        $ticket->testing_ticket_id,
                        $ticket->projectname ?? 'N/A',
                        $ticket->description,
                        $ticket->priority,
                        $ticket->status,
                        $ticket->assigned_name ?? 'Unassigned',
                        $ticket->creator_name ?? 'System',
                        $ticket->created_at,
                        $ticket->updated_at,
                        $branchName
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate testing ticket metrics for dashboard widgets
     */
    public function getMetrics(Request $request)
    {
        // Get current user role and ID
        $role = session('role', 'employee');
        $employeeId = session('user_id');
        $branchId = session('branch_id');

        $query = $this->buildBaseQuery($request, $role, $employeeId, $branchId);

        // Get status counts
        $statusCounts = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Get priority counts
        $priorityCounts = (clone $query)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority');

        // Get monthly trend data
        $monthlyTrend = (clone $query)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'statusCounts' => $statusCounts,
            'priorityCounts' => $priorityCounts,
            'monthlyTrend' => $monthlyTrend
        ]);
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($view, $data, $reportType, $filters)
    {
        $pdf = PDF::loadView($view, [
            'data' => $data,
            'reportType' => $reportType,
            'filters' => $filters,
            'totalRecords' => is_array($data) ? ($data['totalTickets'] ?? 0) : $data->count()
        ]);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('dpi', 96);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }
}