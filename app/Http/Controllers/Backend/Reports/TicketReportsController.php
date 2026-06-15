<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class TicketReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');

        // Base query for reports
        $baseQuery = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id');

        // Apply role-based filtering
        if ($role === 'employee') {
            $baseQuery->where(function($q) use ($employeeId) {
                $q->where('tickets.assign_1', $employeeId)
                  ->orWhere('tickets.assign_2', $employeeId)
                  ->orWhere('tickets.assign_3', $employeeId)
                  ->orWhere('tickets.created_by', $employeeId);
            });
        }

        // Get summary statistics
        $summaryStats = $this->getSummaryStatistics($baseQuery, $request);
        
        // Get priority breakdown
        $priorityStats = $this->getPriorityStatistics($baseQuery, $request);
        
        // Get status breakdown
        $statusStats = $this->getStatusStatistics($baseQuery, $request);
        
        // Get monthly trends
        
        // Get employee performance (admin only)
        $employeeStats = [];
        if ($role === 'admin') {
            $employeeStats = $this->getEmployeeStatistics($baseQuery, $request);
        }

        // Get all employees for filters
        $employees = DB::table('allemployees')
            ->select('id', 'firstname', 'lastname')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->get();

        return view('hrms.hr.Reports.Tickets.index', compact(
            'summaryStats',
            'priorityStats',
            'statusStats',
            'employeeStats',
            'employees',
            'role'
        ));
    }

    /**
     * Get summary statistics
     */
 


    /**
     * Get priority statistics
     */
    private function getPriorityStatistics($baseQuery, $request)
    {
        $query = clone $baseQuery;
        $this->applyDateFilters($query, $request);
        
        return $query->select('tickets.priority', DB::raw('COUNT(*) as count'))
            ->groupBy('tickets.priority')
            ->get()
            ->keyBy('priority');
    }

    /**
     * Get status statistics
     */
    private function getStatusStatistics($baseQuery, $request)
    {
        $query = clone $baseQuery;
        $this->applyDateFilters($query, $request);
        
        return $query->select('tickets.status', DB::raw('COUNT(*) as count'))
            ->groupBy('tickets.status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Get monthly trends - FIXED WITH PROPER GROUP BY
     */

    /**
     * Get employee statistics (admin only)
     */
    private function getEmployeeStatistics($baseQuery, $request)
    {
        $query = clone $baseQuery;
        $this->applyDateFilters($query, $request);
        
        // Get statistics for assigned employees
        $assignedStats = $query->select(
                'assign1.id',
                'assign1.firstname',
                'assign1.lastname',
                DB::raw('COUNT(*) as assigned_tickets'),
                DB::raw('SUM(CASE WHEN tickets.status = "closed" THEN 1 ELSE 0 END) as closed_tickets'),
                DB::raw('AVG(CASE WHEN tickets.status = "closed" THEN TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.updated_at) END) as avg_resolution_hours')
            )
            ->whereNotNull('tickets.assign_1')
            ->groupBy('assign1.id', 'assign1.firstname', 'assign1.lastname')
            ->get();

        // Get statistics for ticket creators
        $creatorStats = DB::table('tickets')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
            ->select(
                'creator.id',
                'creator.firstname',
                'creator.lastname',
                DB::raw('COUNT(*) as created_tickets')
            )
            ->groupBy('creator.id', 'creator.firstname', 'creator.lastname')
            ->get();

        return [
            'assigned' => $assignedStats,
            'creators' => $creatorStats
        ];
    }

    /**
     * Apply date filters to query
     */
    private function applyDateFilters($query, $request)
    {
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            $query->where('tickets.created_at', '>=', $startDate);
        }
        
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
            $query->where('tickets.created_at', '<=', $endDate);
        }
    }

    /**
     * Generate detailed report
     */
    public function detailedReport(Request $request)
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');

        $query = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
            ->leftJoin('assets_company', 'tickets.asset_id', '=', 'assets_company.id')
            ->select(
                'tickets.*',
                'assign1.firstname as assign1_firstname',
                'assign1.lastname as assign1_lastname',
                'assign2.firstname as assign2_firstname',
                'assign2.lastname as assign2_lastname',
                'assign3.firstname as assign3_firstname',
                'assign3.lastname as assign3_lastname',
                'creator.firstname as creator_firstname',
                'creator.lastname as creator_lastname',
                'assets_company.asset_name',
                DB::raw('TIMESTAMPDIFF(HOUR, tickets.created_at, COALESCE(tickets.updated_at, NOW())) as hours_elapsed')
            );

        // Apply role-based filtering
        if ($role === 'employee') {
            $query->where(function($q) use ($employeeId) {
                $q->where('tickets.assign_1', $employeeId)
                  ->orWhere('tickets.assign_2', $employeeId)
                  ->orWhere('tickets.assign_3', $employeeId)
                  ->orWhere('tickets.created_by', $employeeId);
            });
        }

        // Apply filters
        $this->applyReportFilters($query, $request);

        $tickets = $query->orderBy('tickets.created_at', 'desc')->get();

        // Get filter options
        $employees = DB::table('allemployees')
            ->select('id', 'firstname', 'lastname')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->get();

        return view('hrms.Hr.Reports..Tickets.detailed', compact('tickets', 'employees', 'role'));
    }
/**
 * Export report to PDF
 */
public function exportPdf(Request $request)
{
    $role = Session::get('role');

    if ($role !== 'admin') {
        return redirect()->back()->with('error', 'Access denied. Only administrators can export reports.');
    }

    try {
        // Get all the data needed for the report
        $baseQuery = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id');

        $summaryStats = $this->getSummaryStatistics($baseQuery, $request);
        $priorityStats = $this->getPriorityStatistics($baseQuery, $request);
        $statusStats = $this->getStatusStatistics($baseQuery, $request);
        
        $employeeStats = [];
        if ($role === 'admin') {
            $employeeStats = $this->getEmployeeStatistics($baseQuery, $request);
        }

        // Get detailed ticket data
        $detailedQuery = clone $baseQuery;
        $this->applyReportFilters($detailedQuery, $request);
        $tickets = $detailedQuery->select(
            'tickets.*',
            'assign1.firstname as assign1_firstname',
            'assign1.lastname as assign1_lastname',
            'creator.firstname as creator_firstname',
            'creator.lastname as creator_lastname'
        )->orderBy('tickets.created_at', 'desc')->get();

        // Generate dates for filename
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->format('Y-m-d') : 'all';
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('Y-m-d') : 'all';

        // Load the view and generate PDF
        $pdf = PDF::loadView('hrms.hr.Reports.Tickets.pdf', [
            'summaryStats' => $summaryStats,
            'priorityStats' => $priorityStats,
            'statusStats' => $statusStats,
            'employeeStats' => $employeeStats,
            'tickets' => $tickets,
            'startDate' => $request->filled('start_date') ? $request->start_date : null,
            'endDate' => $request->filled('end_date') ? $request->end_date : null,
            'now' => now()->format('Y-m-d H:i:s')
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');

        // Download the PDF
        return $pdf->download("tickets_report_{$startDate}_to_{$endDate}.pdf");

    } catch (\Exception $e) {
        Log::error('Error exporting PDF report: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to generate PDF report.');
    }
}
    /**
     * Apply filters to report query
     */
    private function applyReportFilters($query, $request)
    {
        if ($request->filled('status')) {
            $query->where('tickets.status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('tickets.priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where(function($q) use ($request) {
                $q->where('tickets.assign_1', $request->assigned_to)
                  ->orWhere('tickets.assign_2', $request->assigned_to)
                  ->orWhere('tickets.assign_3', $request->assigned_to);
            });
        }

        if ($request->filled('created_by')) {
            $query->where('tickets.created_by', $request->created_by);
        }

        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            $query->where('tickets.created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
            $query->where('tickets.created_at', '<=', $endDate);
        }

        if ($request->filled('ticket_subject')) {
            $query->where('tickets.ticket_subject', 'like', '%' . $request->ticket_subject . '%');
        }
    }

    /**
     * Export report to CSV
     */
    public function exportCsv(Request $request)
    {
        $role = Session::get('role');

        if ($role !== 'admin') {
            return redirect()->back()->with('error', 'Access denied. Only administrators can export reports.');
        }

        try {
            $query = DB::table('tickets')
                ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
                ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
                ->leftJoin('assets_company', 'tickets.asset_id', '=', 'assets_company.id')
                ->select(
                    'tickets.ticket_id',
                    'tickets.ticket_subject',
                    'tickets.description',
                    'tickets.status',
                    'tickets.priority',
                    'tickets.created_at',
                    'tickets.updated_at',
                    'creator.firstname as creator_firstname',
                    'creator.lastname as creator_lastname',
                    'assign1.firstname as assignee_firstname',
                    'assign1.lastname as assignee_lastname',
                    'assets_company.asset_name',
                    DB::raw('TIMESTAMPDIFF(HOUR, tickets.created_at, COALESCE(tickets.updated_at, NOW())) as resolution_hours')
                );

            $this->applyReportFilters($query, $request);
            $tickets = $query->get();

            $filename = 'tickets_report_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($tickets) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'Ticket ID',
                    'Subject',
                    'Description',
                    'Status',
                    'Priority',
                    'Created Date',
                    'Updated Date',
                    'Creator',
                    'Assignee',
                    'Asset',
                    'Resolution Hours'
                ]);

                // Add data rows
                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->ticket_id,
                        $ticket->ticket_subject,
                        strip_tags($ticket->description),
                        $ticket->status,
                        $ticket->priority,
                        $ticket->created_at,
                        $ticket->updated_at,
                        $ticket->creator_firstname . ' ' . $ticket->creator_lastname,
                        $ticket->assignee_firstname . ' ' . $ticket->assignee_lastname,
                        $ticket->asset_name,
                        $ticket->resolution_hours
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting tickets report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export report.');
        }
    }

    /**
     * Get performance metrics for a specific employee
     */
   

    /**
     * Get ticket analytics data for charts (AJAX)
     */
    public function getAnalyticsData(Request $request)
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');

        try {
            $baseQuery = DB::table('tickets');

            // Apply role-based filtering
            if ($role === 'employee') {
                $baseQuery->where(function($q) use ($employeeId) {
                    $q->where('assign_1', $employeeId)
                      ->orWhere('assign_2', $employeeId)
                      ->orWhere('assign_3', $employeeId)
                      ->orWhere('created_by', $employeeId);
                });
            }

            $type = $request->get('type', 'status');
            
            switch ($type) {
                case 'status':
                    $data = (clone $baseQuery)
                        ->select('status', DB::raw('COUNT(*) as count'))
                        ->groupBy('status')
                        ->get();
                    break;
                    
                case 'priority':
                    $data = (clone $baseQuery)
                        ->select('priority', DB::raw('COUNT(*) as count'))
                        ->groupBy('priority')
                        ->get();
                    break;
                    
                case 'monthly':
                    $sixMonthsAgo = Carbon::now()->subMonths(6)->format('Y-m-d H:i:s');
                    $data = (clone $baseQuery)
                        ->selectRaw('
                            DATE_FORMAT(created_at, "%Y-%m") as month,
                            COUNT(*) as count
                        ')
                        ->where('created_at', '>=', $sixMonthsAgo)
                        ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
                        ->orderByRaw('DATE_FORMAT(created_at, "%Y-%m") ASC')
                        ->get();
                    break;
                    
                default:
                    $data = [];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting analytics data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load analytics data'
            ], 500);
        }
    }
 



public function employeePerformance(Request $request, $employeeId)
    {
        // Get employee data first and check if exists
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        
        if (!$employee) {
            return redirect()->route('ticket.reports.index')
                ->with('error', 'Employee not found.');
        }

        // Parse dates
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()));

        // Base ticket query
        $baseQuery = DB::table('tickets')
            ->where(function($q) use ($employeeId) {
                $q->where('assign_1', $employeeId)
                  ->orWhere('assign_2', $employeeId)
                  ->orWhere('assign_3', $employeeId)
                  ->orWhere('created_by', $employeeId);
            })
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        // Get summary statistics
        $summary = $this->getSummaryStatistics($baseQuery, $request);
        
        // Get assigned ticket statistics
        $assignedStats = $this->getEmployeeAssignedStats($employeeId, $startDate, $endDate);
        
        // Get created ticket statistics
        $createdStats = $this->getEmployeeCreatedStats($employeeId, $startDate, $endDate);
        
        // Get recent tickets
        $recentTickets = $this->getEmployeeRecentTickets($employeeId, $startDate, $endDate);

        return view('hrms.hr.Reports.Tickets.employee-performance', [
            'employee' => $employee,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $summary,
            'assignedStats' => $assignedStats,
            'createdStats' => $createdStats,
            'recentTickets' => $recentTickets,
        ]);
    }

    private function getEmployeeAssignedStats($employeeId, $startDate, $endDate)
    {
        $assignedQuery = DB::table('tickets')
            ->where(function($q) use ($employeeId) {
                $q->where('assign_1', $employeeId)
                  ->orWhere('assign_2', $employeeId)
                  ->orWhere('assign_3', $employeeId);
            })
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        $totalAssigned = $assignedQuery->count();
        $closedTickets = (clone $assignedQuery)->where('status', 'closed')->count();
        $openTickets = (clone $assignedQuery)->where('status', 'open')->count();
        $inProgressTickets = (clone $assignedQuery)->where('status', 'in progress')->count();
        
        $avgResolutionTime = (clone $assignedQuery)
            ->where('status', 'closed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->first();

        return (object) [
            'total_assigned' => $totalAssigned,
            'closed_tickets' => $closedTickets,
            'open_tickets' => $openTickets,
            'in_progress_tickets' => $inProgressTickets,
            'avg_resolution_hours' => round($avgResolutionTime->avg_hours ?? 0, 2)
        ];
    }

    private function getEmployeeCreatedStats($employeeId, $startDate, $endDate)
    {
        $createdQuery = DB::table('tickets')
            ->where('created_by', $employeeId)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        $totalCreated = $createdQuery->count();
        $resolvedCreated = (clone $createdQuery)->where('status', 'closed')->count();
        $highPriorityCreated = (clone $createdQuery)->where('priority', 'High')->count();

        return (object) [
            'total_created' => $totalCreated,
            'resolved_created' => $resolvedCreated,
            'high_priority_created' => $highPriorityCreated
        ];
    }

  private function getEmployeeRecentTickets($employeeId, $startDate, $endDate)
{
    return DB::table('tickets')
        ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
        ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
        ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
        ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
        ->select(
            'tickets.*',
            'creator.firstname as creator_firstname',
            'creator.lastname as creator_lastname',
            'assign1.firstname as assign1_firstname',
            'assign1.lastname as assign1_lastname',
            'assign2.firstname as assign2_firstname',
            'assign2.lastname as assign2_lastname',
            'assign3.firstname as assign3_firstname',
            'assign3.lastname as assign3_lastname'
        )
        ->whereBetween('tickets.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
        ->orderBy('tickets.updated_at', 'desc')
        ->limit(10)
        ->get();
}

   private function getSummaryStatistics($baseQuery, $request)
{
    $query = clone $baseQuery;

    $totalTickets = $query->count();
    $openTickets = (clone $query)->where('tickets.status', 'open')->count();
    $closedTickets = (clone $query)->where('tickets.status', 'closed')->count();
    $inProgressTickets = (clone $query)->where('tickets.status', 'in progress')->count();
    $newTickets = (clone $query)->where('tickets.status', 'new')->count();

    $avgResolutionTime = (clone $query)
        ->where('tickets.status', 'closed')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.updated_at)) as avg_hours')
        ->first();

    return [
        'total' => $totalTickets,
        'open' => $openTickets,
        'closed' => $closedTickets,
        'in_progress' => $inProgressTickets,
        'new' => $newTickets,
        'avg_resolution_hours' => round($avgResolutionTime->avg_hours ?? 0, 2)
    ];
}


}