<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OvertimeController extends Controller
{
    /**
     * Display overtime records for approval
     */
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $status = $request->input('status', 'all');
        $employeeId = $request->input('employee_id');

        $query = DB::table('employee_overtime')
            ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
            ->leftJoin('hierarchies', 'employee_overtime.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->select(
                'employee_overtime.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.designation',
                'hierarchies.hierarchy_name',
                'department.department',
                'approver.firstname as approver_firstname',
                'approver.lastname as approver_lastname'
            )
            ->whereMonth('employee_overtime.overtime_date', $month)
            ->whereYear('employee_overtime.overtime_date', $year);

        if ($status !== 'all') {
            $query->where('employee_overtime.status', $status);
        }

        if ($employeeId) {
            $query->where('employee_overtime.employee_id', $employeeId);
        }

        $overtimeRecords = $query->orderBy('employee_overtime.overtime_date', 'desc')->get();

        // Get employees for filter
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('firstname')
            ->get();

        // Calculate summary statistics
        $summaryStats = $this->calculateOvertimeSummary($month, $year);

        return view('hrms.attendance.overtime.index', [
            'overtimeRecords' => $overtimeRecords,
            'employees' => $employees,
            'month' => $month,
            'year' => $year,
            'selectedStatus' => $status,
            'selectedEmployee' => $employeeId,
            'monthName' => date('F', mktime(0, 0, 0, $month, 1)),
            'summaryStats' => $summaryStats
        ]);
    }

    /**
     * Approve overtime
     */
    public function approve(Request $request, $id)
    {
        $approverId = Session::get('user_id');
    
        DB::table('employee_overtime')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
                'remarks' => $request->remarks, // optional
                'updated_at' => now()
            ]);
    
        return redirect()->back()->with('success', 'Overtime approved successfully.');
    }
    
    public function reject(Request $request, $id)
{
    $approverId = Session::get('user_id');

    $request->validate([
        'remarks' => 'required|string|max:500'
    ]);

    DB::table('employee_overtime')
        ->where('id', $id)
        ->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'remarks' => $request->remarks,
            'updated_at' => now()
        ]);

    return redirect()->back()->with('success', 'Overtime rejected successfully.');
}

    public function bulkApprove(Request $request)
    {
        $approverId = Session::get('user_id');
        $overtimeIds = $request->input('overtime_ids', []);
        
        if (empty($overtimeIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No overtime records selected'
            ], 400);
        }

        $updated = DB::table('employee_overtime')
            ->whereIn('id', $overtimeIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
                'remarks' => 'Bulk approved',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} overtime records approved successfully"
        ]);
    }

    /**
     * Calculate overtime summary statistics
     */
    private function calculateOvertimeSummary($month, $year)
    {
        $baseQuery = DB::table('employee_overtime')
            ->whereMonth('overtime_date', $month)
            ->whereYear('overtime_date', $year);

        return [
            'total_records' => $baseQuery->count(),
            'pending_records' => $baseQuery->where('status', 'pending')->count(),
            'approved_records' => $baseQuery->where('status', 'approved')->count(),
            'rejected_records' => $baseQuery->where('status', 'rejected')->count(),
            'total_overtime_hours' => $baseQuery->sum('overtime_hours'),
            'total_overtime_amount' => $baseQuery->where('status', 'approved')->sum('overtime_amount'),
            'pending_amount' => $baseQuery->where('status', 'pending')->sum('overtime_amount')
        ];
    }

    /**
     * Get overtime details for modal
     */
    public function getOvertimeDetails($id)
    {
        $overtime = DB::table('employee_overtime')
            ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
            ->leftJoin('hierarchies', 'employee_overtime.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
            ->leftJoin('attendances', function($join) {
                $join->on('employee_overtime.employee_id', '=', 'attendances.employee_id')
                     ->on('employee_overtime.overtime_date', '=', 'attendances.date');
            })
            ->select(
                'employee_overtime.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.designation',
                'hierarchies.hierarchy_name',
                'approver.firstname as approver_firstname',
                'approver.lastname as approver_lastname',
                'attendances.punch_in',
                'attendances.punch_out',
                'attendances.working_hours'
            )
            ->where('employee_overtime.id', $id)
            ->first();

        if (!$overtime) {
            return response()->json(['error' => 'Overtime record not found'], 404);
        }

        $html = view('hrms.attendance.overtime.details', compact('overtime'))->render();
        
        return response($html);
    }
/**
 * Export overtime records
 */
public function export(Request $request)
{
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));
    $status = $request->input('status', 'all');
    $employeeId = $request->input('employee_id');

    $query = DB::table('employee_overtime')
        ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
        ->leftJoin('hierarchies', 'employee_overtime.hierarchy_id', '=', 'hierarchies.id')
        ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->select(
            'allemployees.employeeid',
            'allemployees.firstname',
            'allemployees.lastname',
            'department.department',
            'allemployees.designation',
            'employee_overtime.overtime_date',
            'employee_overtime.overtime_hours',
            'employee_overtime.overtime_rate',
            'employee_overtime.overtime_amount',
            'employee_overtime.status',
            'employee_overtime.remarks',
            'hierarchies.hierarchy_name',
            DB::raw("CONCAT(approver.firstname, ' ', approver.lastname) as approver_name"),
            'employee_overtime.approved_at'
        )
        ->whereMonth('employee_overtime.overtime_date', $month)
        ->whereYear('employee_overtime.overtime_date', $year);

    if ($status !== 'all') {
        $query->where('employee_overtime.status', $status);
    }

    if ($employeeId) {
        $query->where('employee_overtime.employee_id', $employeeId);
    }

    $records = $query->orderBy('employee_overtime.overtime_date', 'desc')->get();

    $fileName = "overtime_records_{$month}_{$year}.csv";

    $headers = array(
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"      => "must-revalidate, post-check=0, pre-check=0",
        "Expires"            => "0"
    );

    $columns = [
        'Employee ID', 'First Name', 'Last Name', 'Department', 'Designation', 
        'Overtime Date', 'Overtime Hours', 'Rate (₹/hr)', 'Amount (₹)', 
        'Status', 'Remarks', 'Approval Hierarchy', 'Approved By', 'Approved At'
    ];

    $callback = function() use($records, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($records as $record) {
            fputcsv($file, [
                $record->employeeid,
                $record->firstname,
                $record->lastname,
                $record->department,
                $record->designation,
                $record->overtime_date,
                $record->overtime_hours,
                $record->overtime_rate,
                $record->overtime_amount,
                ucfirst($record->status),
                $record->remarks,
                $record->hierarchy_name,
                $record->approver_name,
                $record->approved_at
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    
}