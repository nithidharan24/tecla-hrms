<?php

namespace App\Http\Controllers\Backend\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $today = date('Y-m-d');
    
        // Total number of employees
        $totalEmployees = DB::table('allemployees')->count();
    
        // Count of employees who have punched in today
        $todayPresent = DB::table('attendances')
            ->whereDate('date', $today)
            ->where('punch_in', '!=', '0000-00-00 00:00:00')
            ->count();
    
        // Absent employees = total - present
        $todayAbsent = $totalEmployees - $todayPresent;
    
        // Build query to fetch employee status
        $attendanceQuery = DB::table('allemployees as ae')
            ->leftJoin('attendances as at', function ($join) use ($today) {
                $join->on('ae.id', '=', 'at.employee_id')
                     ->whereDate('at.date', '=', $today)
                     ->where('at.punch_in', '!=', '0000-00-00 00:00:00');
            })
            ->leftJoin('department as d', 'ae.department', '=', 'd.id')
            ->select(
                'ae.id as employee_id',
                DB::raw("CONCAT(ae.firstname, ' ', ae.lastname) AS employee_name"),
                DB::raw("DATE(NOW()) AS date"),
                'd.department as department',
                DB::raw("IF(at.id IS NOT NULL, 'Present', 'Absent') AS status")
            );
    
        // Apply filters if provided
        if ($request->filled('employee_name')) {
            $attendanceQuery->where(DB::raw("CONCAT(ae.firstname, ' ', ae.lastname)"), 'LIKE', '%' . $request->employee_name . '%');
        }
        if ($request->filled('department')) {
            $attendanceQuery->where('d.department', '=', $request->department);
        }
        if ($request->filled('from_date')) {
            $attendanceQuery->whereDate('at.date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $attendanceQuery->whereDate('at.date', '<=', $request->to_date);
        }
    
        // Export logic (unchanged)
        if ($request->has('export')) {
            $leaves = $attendanceQuery->get();
            if ($request->export == 'pdf') {
                return $this->exportPDF($leaves, $totalEmployees, $todayPresent, $todayAbsent);
            } elseif ($request->export == 'csv') {
                return $this->exportCSV($leaves);
            }
        }
    
        // Get results
        $leaves = $attendanceQuery->get();
    
        return view('hrms.hr.Reports.daily-reports.index', compact('totalEmployees', 'todayAbsent', 'todayPresent', 'leaves'));
    }
    
    /**
     * Export data as PDF
     */
    protected function exportPDF($leaves, $totalEmployees, $todayPresent, $todayAbsent)
    {
        $pdf = Pdf::loadView('hrms.hr.Reports.daily-reports.pdf', [
            'leaves' => $leaves,
            'totalEmployees' => $totalEmployees,
            'todayPresent' => $todayPresent,
            'todayAbsent' => $todayAbsent,
            'date' => date('Y-m-d')
        ]);
        
        return $pdf->download('daily-report-'.date('d-m-Y').'.pdf');
    }
    
    /**
     * Export data as CSV
     */
  /**
 * Export data as CSV
 */
protected function exportCSV($leaves)
{
    $fileName = 'daily-report-'.date('d-m-Y').'.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"             => "no-cache",
        "Cache-Control"      => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Remove 'Employee ID' from columns
    $columns = ['Employee Name', 'Date', 'Department', 'Status'];

    $callback = function() use($leaves, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leaves as $leave) {
            $row = [
                $leave->employee_name,
                \Carbon\Carbon::parse($leave->date)->format('d-m-y'),
                $leave->department,
                $leave->status
            ];

            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}