<?php
namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PDF; // Import PDF facade

class LeaveReportController extends Controller
{
   public function index(Request $request)
{
    // Get filters from the request
    $employee = $request->get('employee');
    $department = $request->get('department');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    // Fetch records from admin_leaves and join with department
    $adminLeaves = DB::table('admin_leaves')
        ->leftJoin('department', 'admin_leaves.id', '=', 'department.id') // Adjusted to match your database structure
        ->when($employee, function ($query, $employee) {
            return $query->where('admin_leaves.employee_name', 'LIKE', "%{$employee}%");
        })
        ->when($department, function ($query, $department) {
            return $query->where('department.department', $department); // Filter by department
        })
        ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
            return $query->whereBetween('admin_leaves.from_date', [$from_date, $to_date]);
        })
        ->select(
            'admin_leaves.employee_name',
            'admin_leaves.from_date',
            'department.department as department_name', // Select department name from 'department' table
            'admin_leaves.leave_type',
            'admin_leaves.no_of_days'
        )
        ->get();

    // Fetch departments for the select dropdown
    $departments = DB::table('department')->pluck('department', 'id'); // Get departments as key-value pairs

    // Fetch records from annual_leaves for additional details
    $annualLeaves = DB::table('annual_leaves')
        ->select(
            'days as total_leaves',
            'carry_forward'
        )
        ->get();

    // Pass both datasets to the view
    return view('hrms.hr.Reports.leave-reports.index', compact('adminLeaves', 'annualLeaves', 'departments'));
}

public function pdf(Request $request)
{
    // Get filters from the request
    $employee = $request->get('employee');
    $department = $request->get('department');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    // Fetch records from admin_leaves and join with department
    $adminLeaves = DB::table('admin_leaves')
        ->leftJoin('department', 'admin_leaves.id', '=', 'department.id')
        ->when($employee, function ($query, $employee) {
            return $query->where('admin_leaves.employee_name', 'LIKE', "%{$employee}%");
        })
        ->when($department, function ($query, $department) {
            return $query->where('department.department', $department);
        })
        ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
            return $query->whereBetween('admin_leaves.from_date', [$from_date, $to_date]);
        })
        ->select(
            'admin_leaves.employee_name',
            'admin_leaves.from_date',
            'department.department as department_name',
            'admin_leaves.leave_type',
            'admin_leaves.no_of_days'
        )
        ->get();

    // Load the view for PDF
    $pdf = PDF::loadView('hrms.hr.Reports.leave-reports.pdf', compact('adminLeaves'));

    // Generate a unique PDF filename
    return $pdf->download('Leave_Report_' . time() . rand(99, 9999) . '.pdf');
}

public function csv(Request $request)
{
    // Get filters from the request (same as PDF method)
    $employee = $request->get('employee');
    $department = $request->get('department');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    // Fetch records (same query as PDF method)
    $adminLeaves = DB::table('admin_leaves')
        ->leftJoin('department', 'admin_leaves.id', '=', 'department.id')
        ->when($employee, function ($query, $employee) {
            return $query->where('admin_leaves.employee_name', 'LIKE', "%{$employee}%");
        })
        ->when($department, function ($query, $department) {
            return $query->where('department.department', $department);
        })
        ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
            return $query->whereBetween('admin_leaves.from_date', [$from_date, $to_date]);
        })
        ->select(
            'admin_leaves.employee_name',
            'admin_leaves.from_date',
            'department.department as department_name',
            'admin_leaves.leave_type',
            'admin_leaves.no_of_days'
        )
        ->get();

    // Get annual leaves data for calculations
    $annualLeaves = DB::table('annual_leaves')
        ->select('days as total_leaves', 'carry_forward')
        ->first();

    // Generate CSV
    $filename = 'Leave_Report_' . time() . rand(99, 9999) . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($adminLeaves, $annualLeaves) {
        $file = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($file, [
            'Employee',
            'Date',
            'Department',
            'Leave Type',
            'No. of Days',
            'Remaining Leave',
            'Total Leaves',
            'Total Leave Taken',
            'Leave Carry Forward'
        ]);

        // Add data rows
        foreach ($adminLeaves as $leave) {
            $remainingLeave = $annualLeaves->total_leaves - $leave->no_of_days;
            
            fputcsv($file, [
                $leave->employee_name,
                \Carbon\Carbon::parse($leave->from_date)->format('d M Y'),
                $leave->department_name,
                $leave->leave_type,
                $leave->no_of_days,
                $remainingLeave,
                $annualLeaves->total_leaves,
                $leave->no_of_days,
                $annualLeaves->carry_forward
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}