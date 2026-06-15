<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrganizationReportsController extends Controller
{
    public function resourceAvailability(Request $request)
    {
        // Month Filter
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        // Branch & Employee Filter
        $branches = DB::table('branches')->get();
        $selectedBranch = $request->get('branch', 'all');
        $selectedEmployee = $request->get('employee', 'all');

        // Build Date List
        $dates = [];
        $loop = $start->copy();
        while ($loop->lte($end)) {
            $dates[] = [
                "full" => $loop->format('Y-m-d'),
                "day"  => $loop->format('d'),
                "dow"  => $loop->format('D')
            ];
            $loop->addDay();
        }

        // Fetch Employees (with branch filter)
        $employeesQuery = DB::table('allemployees')
            ->select('id','firstname','lastname','employeeid','profile_image','branch_id')
            ->orderBy('employeeid');

        if ($selectedBranch !== 'all') {
            $employeesQuery->where('branch_id', $selectedBranch);
        }

        $employees = $employeesQuery->get();

        // If a specific employee selected, filter the collection (Collection helper)
        if ($selectedEmployee !== 'all') {
            $employees = $employees->where('id', $selectedEmployee)->values();
        }

        // Build Availability Matrix
        $availability = [];
        foreach ($employees as $emp) {
            foreach ($dates as $d) {
                $dateStr = $d["full"];
                $carbon = Carbon::parse($dateStr);

                // FUTURE DATE
                if ($carbon->isFuture()) {
                    if ($this->isHoliday($dateStr)) {
                        $availability[$emp->id][$dateStr] = "H";
                    } elseif ($this->isWeekendForEmployee($emp->id, $carbon)) {
                        $availability[$emp->id][$dateStr] = "W";
                    } else {
                        $availability[$emp->id][$dateStr] = "-"; // future working
                    }
                    continue;
                }

                // HOLIDAY
                if ($this->isHoliday($dateStr)) {
                    $availability[$emp->id][$dateStr] = "H";
                    continue;
                }

                // WEEKEND (use employee-specific weekend if you have a function)
                if ($this->isWeekendForEmployee($emp->id, $carbon)) {
                    $availability[$emp->id][$dateStr] = "W";
                    continue;
                }

                // LEAVE
                $leave = DB::table('employee_leaves')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->where('from_date', '<=', $dateStr)
                    ->where('to_date', '>=', $dateStr)
                    ->first();

                if ($leave) {
                    $availability[$emp->id][$dateStr] = "L";
                } else {
                    $availability[$emp->id][$dateStr] = ""; // Working day (present / no mark)
                }
            }
        }

        // Build chart data (counts per employee)
        $labels = [];
        $countsL = [];
        $countsH = [];
        $countsW = [];
        $countsWorking = [];

        foreach ($employees as $emp) {
            $labels[] = $emp->firstname . ' ' . $emp->lastname . " (" . $emp->employeeid . ")";
            $l = $h = $w = $wrk = 0;

            foreach ($dates as $d) {
                $dateStr = $d['full'];
                $val = $availability[$emp->id][$dateStr] ?? null;

                // Ignore future '-' values from chart counts
                if ($val === '-') continue;

                if ($val === 'L') $l++;
                elseif ($val === 'H') $h++;
                elseif ($val === 'W') $w++;
                elseif ($val === '') $wrk++;
                // else unknown - ignore
            }

            $countsL[] = $l;
            $countsH[] = $h;
            $countsW[] = $w;
            $countsWorking[] = $wrk;
        }

        $chartData = [
            'labels' => $labels,
            'leave' => $countsL,
            'holiday' => $countsH,
            'weekend' => $countsW,
            'working' => $countsWorking,
        ];

        return view('hrms.Employee.Reports.Organization.resource_availability.index', [
            'dates' => $dates,
            'employees' => $employees,
            'availability' => $availability,
            'selectedMonth' => $selectedMonth,
            'branches' => $branches,
            'selectedBranch' => $selectedBranch,
            'selectedEmployee' => $selectedEmployee,
            'chartData' => $chartData
        ]);
    }

    private function isHoliday($date)
    {
        return DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->exists();
    }
public function exportCSV(Request $request)
{
    // Get filters from request
    $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
    $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
    $end = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
    
    // Build dates array (same logic as your main method)
    $dates = [];
    $loop = $start->copy();
    while ($loop->lte($end)) {
        $dates[] = [
            "full" => $loop->format('Y-m-d'),
            "day" => $loop->format('d'),
            "dow" => $loop->format('D')
        ];
        $loop->addDay();
    }
    
    // Get employees with filters (same logic)
    $selectedBranch = $request->get('branch', 'all');
    $selectedEmployee = $request->get('employee', 'all');
    
    $employeesQuery = DB::table('allemployees')
        ->select('id','firstname','lastname','employeeid','branch_id')
        ->orderBy('employeeid');
    
    if ($selectedBranch !== 'all') {
        $employeesQuery->where('branch_id', $selectedBranch);
    }
    
    if ($selectedEmployee !== 'all') {
        $employeesQuery->where('id', $selectedEmployee);
    }
    
    $employees = $employeesQuery->get();
    $branches = DB::table('branches')->get();
    
    // Prepare CSV content
    $csvData = [];
    
    // Header row
    $header = ['Employee ID', 'Employee Name', 'Branch'];
    foreach ($dates as $date) {
        $header[] = $date['day'] . ' ' . $date['dow'];
    }
    $csvData[] = $header;
    
    // Data rows
    foreach ($employees as $emp) {
        $row = [
            $emp->employeeid,
            $emp->firstname . ' ' . $emp->lastname,
            $branches->firstWhere('id', $emp->branch_id)->name ?? 'N/A'
        ];
        
        foreach ($dates as $d) {
            $dateStr = $d['full'];
            $carbon = Carbon::parse($dateStr);
            $status = '';
            
            // Future date
            if ($carbon->isFuture()) {
                if ($this->isHoliday($dateStr)) {
                    $status = 'Holiday';
                } elseif ($this->isWeekendForEmployee($emp->id, $carbon)) {
                    $status = 'Weekend';
                } else {
                    $status = 'Future';
                }
            }
            // Holiday
            elseif ($this->isHoliday($dateStr)) {
                $status = 'Holiday';
            }
            // Weekend
            elseif ($this->isWeekendForEmployee($emp->id, $carbon)) {
                $status = 'Weekend';
            }
            // Leave
            elseif (DB::table('employee_leaves')
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->where('from_date', '<=', $dateStr)
                ->where('to_date', '>=', $dateStr)
                ->exists()) {
                $status = 'Leave';
            }
            // Working
            else {
                $status = 'Working';
            }
            
            $row[] = $status;
        }
        
        $csvData[] = $row;
    }
    
    // Generate CSV file
    $filename = 'resource_availability_' . $selectedMonth . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");
        
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}
    // If you already have this function in another controller/trait, keep that one instead.
    private function isWeekendForEmployee($employeeId, $carbon)
    {
        // Default weekend logic: Saturday & Sunday
        return in_array($carbon->format('N'), [6,7]);
    }

   public function organizationLeaveBalance(Request $request)
{
    // Branch Filter
    $selectedBranch = $request->branch ?? 'all';

    // Fetch all branches
    $branches = DB::table('branches')->select('id','name')->get();

    // Fetch employees (filtered by branch)
    $employeesQuery = DB::table('allemployees')
        ->where('status', 'active')
        ->select('id','firstname','lastname','employeeid','department','branch_id')
        ->orderBy('employeeid');

    if ($selectedBranch !== 'all') {
        $employeesQuery->where('branch_id', $selectedBranch);
    }

    $employees = $employeesQuery->get();

    // REMOVED: auto-select first employee
    // Instead, check if employee_id is provided in request
    $selectedEmployeeId = $request->employee_id ?? null;

    // If no employee selected or employee doesn't exist, show empty state
    $employee = null;
    $summary = [];

    if ($selectedEmployeeId && $employees->contains('id', $selectedEmployeeId)) {
        $employee = DB::table('allemployees')->where('id', $selectedEmployeeId)->first();

        // Leave settings
        $annual = DB::table('annual_leaves')->first();
        $medical = DB::table('medical_leaves')->first();
        $lop = DB::table('lop_leaves')->first();

        $leaveTypes = [
            "Casual Leave"     => $annual->days ?? 0,
            "Sick"             => $medical->sick ?? 0,
            "Hospitalisation"  => $medical->hospitalisation ?? 0,
            "Maternity Leave"  => $medical->maternity ?? 0,
            "Paternity Leave"  => $medical->paternity ?? 0,
            "LOP"              => $lop->days ?? 0,
        ];

        // Used leaves
        $used = DB::table('employee_leave_balances')
            ->where('employee_id', $selectedEmployeeId)
            ->pluck('used_days', 'leave_type')
            ->toArray();

        // Build summary
        foreach ($leaveTypes as $type => $total) {
            $summary[] = [
                "type" => $type,
                "total" => $total,
                "used" => $used[$type] ?? 0,
                "remaining" => $total - ($used[$type] ?? 0),
            ];
        }
    }

    return view('hrms.Employee.Reports.Organization.LeaveBalance.index', [
        'branches' => $branches,
        'employees' => $employees,
        'summary' => $summary,
        'employee' => $employee,
        'selectedEmployeeId' => $selectedEmployeeId,
        'selectedBranch' => $selectedBranch
    ]);
}

public function organizationLeaveBalanceCSV(Request $request)
{
    $employeeId = $request->employee_id;
    $branchId = $request->branch ?? 'all';
    $departmentId = $request->department ?? 'all';
    
    // Fetch employee details
    $employee = DB::table('allemployees')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->select(
            'allemployees.*',
            'branches.name as branch_name',
            'department.department as dept_name'
        )
        ->where('allemployees.id', $employeeId)
        ->first();
    
    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }
    
    // Fetch leave settings
    $annual = DB::table('annual_leaves')->first();
    $medical = DB::table('medical_leaves')->first();
    $lop = DB::table('lop_leaves')->first();
    
    $leaveTypes = [
        "Casual Leave" => $annual->days ?? 0,
        "Sick" => $medical->sick ?? 0,
        "Hospitalisation" => $medical->hospitalisation ?? 0,
        "Maternity Leave" => $medical->maternity ?? 0,
        "Paternity Leave" => $medical->paternity ?? 0,
        "LOP" => $lop->days ?? 0,
    ];
    
    // Fetch used leaves from employee_leave_balances
    $usedLeaves = DB::table('employee_leave_balances')
        ->where('employee_id', $employeeId)
        ->pluck('used_days', 'leave_type')
        ->toArray();
    
    // Build summary array
    $summary = [];
    foreach ($leaveTypes as $type => $total) {
        $used = $usedLeaves[$type] ?? 0;
        $summary[] = [
            'type' => $type,
            'total' => $total,
            'used' => $used,
            'remaining' => $total - $used,
        ];
    }
    
    $filename = "Leave_Balance_" . $employee->employeeid . "_" . date('Y-m-d') . ".csv";
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];
    
    return response()->stream(function() use ($employee, $summary, $branchId, $departmentId) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");
        
        // Report Header
        fputcsv($file, ['LEAVE BALANCE REPORT']);
        fputcsv($file, []);
        fputcsv($file, ['Employee Name:', $employee->firstname . ' ' . $employee->lastname]);
        fputcsv($file, ['Employee ID:', $employee->employeeid]);
        fputcsv($file, ['Branch:', $employee->branch_name ?? 'N/A']);
        fputcsv($file, ['Department:', $employee->dept_name ?? $employee->department ?? 'N/A']);
        fputcsv($file, ['Generated On:', date('Y-m-d H:i:s')]);
        fputcsv($file, []);
        
        // Column Headers
        fputcsv($file, ['Leave Type', 'Total Days', 'Used Days', 'Remaining Days']);
        
        // Data Rows
        foreach ($summary as $row) {
            fputcsv($file, [
                $row['type'],
                $row['total'],
                $row['used'],
                $row['remaining']
            ]);
        }
        
        // Summary Row
        fputcsv($file, []);
        $totalDays = array_sum(array_column($summary, 'total'));
        $totalUsed = array_sum(array_column($summary, 'used'));
        $totalRemaining = $totalDays - $totalUsed;
        
        fputcsv($file, ['SUMMARY', '', '', '']);
        fputcsv($file, ['Total Leave Days:', $totalDays, '', '']);
        fputcsv($file, ['Total Used:', $totalUsed, '', '']);
        fputcsv($file, ['Total Remaining:', $totalRemaining, '', '']);
        
        fclose($file);
    }, 200, $headers);
}
 public function organizationDailyLeaveStatus(Request $request)
    {
        $date = $request->date ?? Carbon::now()->format('Y-m-d');
        $selectedBranch = $request->branch ?? 'all';
        $selectedDepartment = $request->department ?? 'all';

        $branches = DB::table('branches')->select('id','name')->get();
        $departments = DB::table('department')->select('id','department')->get();

        $employeesQuery = DB::table('allemployees')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.branch_id',
                'allemployees.department',
                'branches.name as branch_name',
                'department.department as dept_name'
            );

        if ($selectedBranch !== 'all') {
            $employeesQuery->where('allemployees.branch_id', $selectedBranch);
        }

        if ($selectedDepartment !== 'all') {
            $employeesQuery->where('allemployees.department', $selectedDepartment);
        }

        $employees = $employeesQuery->orderBy('allemployees.employeeid')->get();
        $carbon = Carbon::parse($date);

        $results = [];

        foreach ($employees as $emp) {

            $leave = DB::table('employee_leaves')
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->where('from_date', '<=', $date)
                ->where('to_date', '>=', $date)
                ->first();

            $holiday = DB::table('holidays')->whereDate('holidaydate', $date)->exists();

            $weekend = $this->isWeekendForEmployee($emp->id, $carbon);

            $attendance = DB::table('attendances')
                ->where('employee_id', $emp->id)
                ->whereDate('date', $date)
                ->whereNotNull('punch_in')
                ->first();

            if ($carbon->isFuture())            $status = "Future";
            elseif ($leave)                     $status = "Leave";
            elseif ($holiday)                   $status = "Holiday";
            elseif ($weekend)                   $status = "Weekend";
            elseif ($attendance)                $status = "Present";
            else                                 $status = "Absent";

            $results[] = [
                'employee'   => $emp,
                'branch'     => $emp->branch_name,
                'department' => $emp->dept_name,
                'status'     => $status
            ];
        }

        $summary = [
            'present' => collect($results)->where('status','Present')->count(),
            'leave'   => collect($results)->where('status','Leave')->count(),
            'holiday' => collect($results)->where('status','Holiday')->count(),
            'weekend' => collect($results)->where('status','Weekend')->count(),
            'absent'  => collect($results)->where('status','Absent')->count(),
            'future'  => collect($results)->where('status','Future')->count(),
        ];

        return view('hrms.Employee.Reports.Organization.daily-leave-status.index', compact(
            'branches',
            'departments',
            'selectedBranch',
            'selectedDepartment',
            'results',
            'summary',
            'date'
        ));
    }

    public function organizationDailyLeaveStatusCSV(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $branch = $request->branch ?? 'all';
        $department = $request->department ?? 'all';

        $filename = "Organization-Daily-Leave-Status-$date.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        return response()->stream(function() use ($date, $branch, $department) {

            $file = fopen('php://output', 'w');

            fputcsv($file, ["Employee", "Emp ID", "Branch", "Department", "Status", "Date"]);

            $employeesQuery = DB::table('allemployees')
                ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->select(
                    'allemployees.*',
                    'branches.name as branch_name',
                    'department.department as dept_name'
                );

            if ($branch !== 'all') {
                $employeesQuery->where('allemployees.branch_id', $branch);
            }

            if ($department !== 'all') {
                $employeesQuery->where('allemployees.department', $department);
            }

            $employees = $employeesQuery->get();

            foreach ($employees as $emp) {

                $leave = DB::table('employee_leaves')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->where('from_date', '<=', $date)
                    ->where('to_date', '>=', $date)
                    ->first();

                $holiday = DB::table('holidays')->whereDate('holidaydate', $date)->exists();
                $weekend = false;

                $attendance = DB::table('attendances')
                    ->where('employee_id', $emp->id)
                    ->whereDate('date', $date)
                    ->whereNotNull('punch_in')
                    ->first();

                if (Carbon::parse($date)->isFuture()) $status = "Future";
                elseif ($leave)                       $status = "Leave";
                elseif ($holiday)                     $status = "Holiday";
                elseif ($weekend)                     $status = "Weekend";
                elseif ($attendance)                  $status = "Present";
                else                                  $status = "Absent";

                fputcsv($file, [
                    $emp->firstname . " " . $emp->lastname,
                    $emp->employeeid,
                    $emp->branch_name,
                    $emp->dept_name,
                    $status,
                    $date
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

   
/**
 * Organization - Present / Absent (single employee view)
 */
public function organizationPresentAbsent(Request $request)
{
    $filter = $request->get('filter', 'monthly'); // monthly | weekly | custom
    $selectedBranch = $request->get('branch', 'all');
    $selectedDepartment = $request->get('department', 'all');
    $selectedEmployeeId = $request->get('employee', 'all');

    // Branches & departments for filters
    $branches = DB::table('branches')->select('id','name')->get();
    $departments = DB::table('department')->select('id','department')->get();

    // Employee dropdown list (filtered by branch & department)
    $employeesQuery = DB::table('allemployees')
        ->select('id','firstname','lastname','employeeid','branch_id','department')
        ->where('status', 'active');

    if ($selectedBranch !== 'all') $employeesQuery->where('branch_id', $selectedBranch);
    if ($selectedDepartment !== 'all') $employeesQuery->where('department', $selectedDepartment);

    $employees = $employeesQuery->orderBy('employeeid')->get();

    // Default selected employee = first from filtered list
    if ($selectedEmployeeId === 'all') {
        $selectedEmployeeId = $employees->first()->id ?? null;
    }

    $employee = $selectedEmployeeId ? DB::table('allemployees')->where('id', $selectedEmployeeId)->first() : null;

    // Build date range based on filter
    if ($filter === 'monthly') {
        $selectedMonth = $request->get('month', Carbon::now()->format('m'));
        $selectedYear = $request->get('year', Carbon::now()->format('Y'));
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
    } elseif ($filter === 'weekly') {
        $startDate = $request->get('week_start') ? Carbon::parse($request->get('week_start'))->startOfDay() : Carbon::now()->startOfWeek()->startOfDay();
        $endDate = $startDate->copy()->endOfWeek()->endOfDay();
    } else { // custom
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
    }

    // Build dateLabels array for the table (same shape used in your earlier code)
    $dateLabels = [];
    $loop = $startDate->copy();
    while ($loop->lte($endDate)) {
        $dateLabels[] = [
            'full' => $loop->format('Y-m-d'),
            'date' => $loop->format('d-M'),
            'day'  => $loop->format('D'),
            'day_full' => $loop->format('l'),
        ];
        $loop->addDay();
    }

    // Build status map for selected employee (P/A/W/H/F)
    $status = [];
    if ($employee) {
        foreach ($dateLabels as $d) {
            $dt = $d['full'];
            $carbon = Carbon::parse($dt);

            // Future
            if ($carbon->isFuture()) {
                if ($this->isHoliday($dt)) $status[$dt] = 'H';
                elseif ($this->isWeekendForEmployee($employee->id, $carbon)) $status[$dt] = 'W';
                else $status[$dt] = '-';
                continue;
            }

            // Holiday
            if ($this->isHoliday($dt)) { $status[$dt] = 'H'; continue; }

            // Weekend
            if ($this->isWeekendForEmployee($employee->id, $carbon)) { $status[$dt] = 'W'; continue; }

            // Present / Absent (attendance check)
            $att = DB::table('attendances')
                ->where('employee_id', $employee->id)
                ->whereDate('date', $dt)
                ->whereNotNull('punch_in')
                ->first();

            $status[$dt] = $att ? 'P' : 'A';
        }
    }

    // Aggregated counts for chart
    $counts = ['P'=>0,'A'=>0,'W'=>0,'H'=>0,'F'=>0];
    foreach ($dateLabels as $d) {
        $dt = $d['full'];
        $s = $status[$dt] ?? '-';
        if ($s === 'P') $counts['P']++;
        elseif ($s === 'A') $counts['A']++;
        elseif ($s === 'W') $counts['W']++;
        elseif ($s === 'H') $counts['H']++;
        else $counts['F']++;
    }

    return view('hrms.Employee.Reports.Organization.present-absent.index', [
        'branches' => $branches,
        'departments' => $departments,
        'employees' => $employees,
        'employee' => $employee,
        'selectedBranch' => $selectedBranch,
        'selectedDepartment' => $selectedDepartment,
        'selectedEmployeeId' => $selectedEmployeeId,
        'filter' => $filter,
        'dateLabels' => $dateLabels,
        'status' => $status,
        'counts' => $counts,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
    ]);
}

/**
 * CSV export for Organization Present/Absent (single employee or filtered employee list)
 */
public function organizationPresentAbsentCSV(Request $request)
{
    $filter = $request->get('filter', 'monthly');
    $selectedBranch = $request->get('branch', 'all');
    $selectedDepartment = $request->get('department', 'all');
    $selectedEmployeeId = $request->get('employee', 'all');

    // Build date range same as index()
    if ($filter === 'monthly') {
        $selectedMonth = $request->get('month', Carbon::now()->format('m'));
        $selectedYear = $request->get('year', Carbon::now()->format('Y'));
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
    } elseif ($filter === 'weekly') {
        $startDate = $request->get('week_start') ? Carbon::parse($request->get('week_start'))->startOfDay() : Carbon::now()->startOfWeek()->startOfDay();
        $endDate = $startDate->copy()->endOfWeek()->endOfDay();
    } else {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
    }

    $filename = 'Org_PresentAbsent_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    return response()->stream(function() use ($startDate, $endDate, $selectedBranch, $selectedDepartment, $selectedEmployeeId) {
        $out = fopen('php://output', 'w');
        // BOM for Excel
        fwrite($out, "\xEF\xBB\xBF");

        // header
        fputcsv($out, ['Employee', 'Emp ID', 'Branch', 'Department', 'Date', 'Day', 'Status']);

        // employees to export (apply branch/department filter)
        $empQ = DB::table('allemployees')->select('id','firstname','lastname','employeeid','branch_id','department')->where('status','active');
        if ($selectedBranch !== 'all') $empQ->where('branch_id', $selectedBranch);
        if ($selectedDepartment !== 'all') $empQ->where('department', $selectedDepartment);
        if ($selectedEmployeeId !== 'all') $empQ->where('id', $selectedEmployeeId);
        $employees = $empQ->orderBy('employeeid')->get();

        // prefetch branches/departments map
        $branches = DB::table('branches')->select('id','name')->get()->keyBy('id');
        $departments = DB::table('department')->select('id','department')->get()->keyBy('id');

        // for each employee and each date, determine status and write row
        $loop = $startDate->copy();
        $dates = [];
        while ($loop->lte($endDate)) {
            $dates[] = $loop->format('Y-m-d');
            $loop->addDay();
        }

        foreach ($employees as $emp) {
            foreach ($dates as $dt) {
                $carbon = Carbon::parse($dt);

                if ($carbon->isFuture()) {
                    if ($this->isHoliday($dt)) $status = 'Holiday';
                    elseif ($this->isWeekendForEmployee($emp->id, $carbon)) $status = 'Weekend';
                    else $status = 'Future';
                } elseif ($this->isHoliday($dt)) {
                    $status = 'Holiday';
                } elseif ($this->isWeekendForEmployee($emp->id, $carbon)) {
                    $status = 'Weekend';
                } else {
                    $att = DB::table('attendances')
                        ->where('employee_id', $emp->id)
                        ->whereDate('date', $dt)
                        ->whereNotNull('punch_in')
                        ->first();
                    $status = $att ? 'Present' : 'Absent';
                }

                fputcsv($out, [
                    $emp->firstname . ' ' . $emp->lastname,
                    $emp->employeeid,
                    $branches[$emp->branch_id]->name ?? 'N/A',
                    $emp->department ?? ($departments[$emp->department]->department ?? 'N/A'),
                    $dt,
                    Carbon::parse($dt)->format('D'),
                    $status
                ]);
            }
        }

        fclose($out);
    }, 200, $headers);
}
 public function organizationPresenceHours(Request $request)
    {
        $dateFilter = $request->get('filter', 'last7'); // last7 / this_week / last_week / this_month / custom
        $selectedBranch = $request->get('branch', 'all');
        $selectedDepartment = $request->get('department', 'all');
        $selectedEmployee = $request->get('employee', 'all');

        // Branches & Departments for filters
        $branches = DB::table('branches')->select('id','name')->get();
        $departments = DB::table('department')->select('id','department')->get();

        // Determine date range
        switch ($dateFilter) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end   = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $start = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->subDays(6);
                $end   = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now();
                break;
            case 'last7':
            default:
                $end = Carbon::now()->endOfDay();
                $start = Carbon::now()->subDays(6)->startOfDay();
                break;
        }

        // Build dates array (ordered)
        $dates = [];
        $loop = $start->copy();
        while ($loop->lte($end)) {
            $dates[] = [
                'full' => $loop->format('Y-m-d'),
                'label' => $loop->format('d-M'),
                'day' => $loop->format('D')
            ];
            $loop->addDay();
        }

        // Employees query (filter branch/department)
        $employeesQuery = DB::table('allemployees')
            ->select('id','firstname','lastname','employeeid','branch_id','department')
            ->where('status','active');

        if ($selectedBranch !== 'all') $employeesQuery->where('branch_id', $selectedBranch);
        if ($selectedDepartment !== 'all') $employeesQuery->where('department', $selectedDepartment);

        // if single employee is requested
        if ($selectedEmployee !== 'all') {
            $employeesQuery->where('id', $selectedEmployee);
        }

        $employees = $employeesQuery->orderBy('employeeid')->get();

        // Preload branches map (for display)
        $branchesMap = DB::table('branches')->pluck('name','id');

        // Build matrix & totals
        $matrix = []; // [employeeId]['dates'][date] => row data
        $summaryTotals = [
            'total_hours_minutes' => 0,
            'payable_minutes' => 0,
            'present_minutes' => 0,
            'holiday_minutes' => 0,
            'weekend_minutes' => 0,
        ];

        foreach ($employees as $emp) {
            $empRow = [
                'employee' => $emp,
                'dates' => [],
                'row_total_minutes' => 0,
                'row_payable_minutes' => 0
            ];

            foreach ($dates as $d) {
                $dateStr = $d['full'];
                $carbon = Carbon::parse($dateStr);

                // Holiday
                $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
                // Weekend (use function)
                $isWeekend = $this->isWeekendForEmployee($emp->id, $carbon);

                // Attendance record (single most recent)
                $att = DB::table('attendances')
                    ->where('employee_id', $emp->id)
                    ->whereDate('date', $dateStr)
                    ->latest('id')
                    ->first();

                $status = 'Absent';
                $firstIn = '-';
                $lastOut = '-';
                $minutes = 0;
                $payableMinutes = 0;
                $shiftName = null;

                if ($carbon->isFuture()) {
                    // future
                    if ($isHoliday) $status = 'Holiday';
                    elseif ($isWeekend) $status = 'Weekend';
                    else $status = 'Future';
                } else {
                    if ($att && $att->punch_in) {
                        $status = 'Present';
                        $firstIn = $att->punch_in ? Carbon::parse($att->punch_in)->format('h:i A') : '-';
                        $lastOut = $att->punch_out ? Carbon::parse($att->punch_out)->format('h:i A') : '-';

                        $minutes = (int)($att->actual_working_minutes ?? 0);

                        // fallback if actual_working_minutes not set but punch_in/out exist
                        if (!$minutes && $att->punch_in && $att->punch_out) {
                            $minutes = Carbon::parse($att->punch_in)->diffInMinutes(Carbon::parse($att->punch_out));
                        }

                        $payableMinutes = (int)($att->scheduled_hours ? ($att->scheduled_hours * 60) : $minutes);

                        if (!empty($att->shift_id)) {
                            $sh = DB::table('shifts')->where('id',$att->shift_id)->first();
                            if ($sh) $shiftName = $sh->shift_name;
                        }
                    } elseif ($isHoliday) {
                        $status = 'Holiday';
                        $payableMinutes = 8 * 60;
                    } elseif ($isWeekend) {
                        $status = 'Weekend';
                        $payableMinutes = 8 * 60;
                    } else {
                        $status = 'Absent';
                    }
                }

                // accumulate row totals
                $empRow['dates'][$dateStr] = [
                    'status' => $status,
                    'first_in' => $firstIn,
                    'last_out' => $lastOut,
                    'minutes' => $minutes,
                    'payable_minutes' => $payableMinutes,
                    'shift' => $shiftName
                ];

                $empRow['row_total_minutes'] += $minutes;
                $empRow['row_payable_minutes'] += $payableMinutes;

                // accumulate global summary
                $summaryTotals['total_hours_minutes'] += $minutes;
                $summaryTotals['payable_minutes'] += $payableMinutes;
                if ($status === 'Present') $summaryTotals['present_minutes'] += $minutes;
                if ($status === 'Holiday') $summaryTotals['holiday_minutes'] += $payableMinutes;
                if ($status === 'Weekend') $summaryTotals['weekend_minutes'] += $payableMinutes;
            }

            $matrix[$emp->id] = $empRow;
        }

        // Convert summary minutes to HH:MM for display helper
        $toHHMM = function($m) {
            $h = floor($m/60);
            $mm = $m % 60;
            return sprintf("%02d:%02d", $h, $mm);
        };

        return view('hrms.Employee.Reports.Organization.PresenceHours.index', [
            'branches' => $branches,
            'departments' => $departments,
            'selectedBranch' => $selectedBranch,
            'selectedDepartment' => $selectedDepartment,
            'selectedEmployee' => $selectedEmployee,
            'filter' => $dateFilter,
            'startDate' => $start,
            'endDate' => $end,
            'dates' => $dates,
            'employees' => $employees,
            'matrix' => $matrix,
            'summaryTotals' => $summaryTotals,
            'summaryTotalsHH' => [
                'total_hours' => $toHHMM($summaryTotals['total_hours_minutes']),
                'payable_hours' => $toHHMM($summaryTotals['payable_minutes']),
                'present_hours'=> $toHHMM($summaryTotals['present_minutes']),
                'holiday_hours'=> $toHHMM($summaryTotals['holiday_minutes']),
                'weekend_hours'=> $toHHMM($summaryTotals['weekend_minutes']),
            ]
        ]);
    }

    /**
     * Server-side CSV export for Organization Presence Hours (Format C: Status + Hours)
     * URL: call via action([OrganizationReportsController::class,'organizationPresenceHoursCSV'], request()->query())
     */
    public function organizationPresenceHoursCSV(Request $request)
    {
        $dateFilter = $request->get('filter', 'last7');
        $selectedBranch = $request->get('branch', 'all');
        $selectedDepartment = $request->get('department', 'all');
        $selectedEmployee = $request->get('employee', 'all');

        // date range logic (same as above)
        switch ($dateFilter) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end   = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $start = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->subDays(6);
                $end   = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now();
                break;
            case 'last7':
            default:
                $end = Carbon::now()->endOfDay();
                $start = Carbon::now()->subDays(6)->startOfDay();
                break;
        }

        // Build dates array
        $dates = [];
        $loop = $start->copy();
        while ($loop->lte($end)) {
            $dates[] = $loop->format('Y-m-d');
            $loop->addDay();
        }

        // Employees query with filters
        $employeesQuery = DB::table('allemployees')->select('id','firstname','lastname','employeeid','branch_id','department')
            ->where('status','active');

        if ($selectedBranch !== 'all') $employeesQuery->where('branch_id', $selectedBranch);
        if ($selectedDepartment !== 'all') $employeesQuery->where('department', $selectedDepartment);
        if ($selectedEmployee !== 'all') $employeesQuery->where('id', $selectedEmployee);

        $employees = $employeesQuery->orderBy('employeeid')->get();
        $branchesMap = DB::table('branches')->pluck('name','id');

        $filename = 'org_presence_hours_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($employees, $dates, $branchesMap) {
            $fh = fopen('php://output', 'w');
            // BOM for Excel compatibility
            fwrite($fh, "\xEF\xBB\xBF");

            // Header row
            $header = ['Employee Name','Emp ID','Branch','Department'];
            foreach ($dates as $d) {
                $header[] = $d;
            }
            $header[] = 'Total Hours';
            $header[] = 'Payable Hours';
            fputcsv($fh, $header);

            // Per employee rows
            foreach ($employees as $emp) {
                $row = [
                    $emp->firstname . ' ' . $emp->lastname,
                    $emp->employeeid,
                    $branchesMap[$emp->branch_id] ?? 'N/A',
                    $emp->department ?? ''
                ];

                $rowTotalMinutes = 0;
                $rowPayableMinutes = 0;

                foreach ($dates as $dateStr) {
                    $carbon = Carbon::parse($dateStr);
                    $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
                    $isWeekend = $this->isWeekendForEmployee($emp->id, $carbon);

                    $att = DB::table('attendances')
                        ->where('employee_id', $emp->id)
                        ->whereDate('date', $dateStr)
                        ->latest('id')
                        ->first();

                    $cell = '';
                    $minutes = 0;
                    $payable = 0;

                    if ($carbon->isFuture()) {
                        if ($isHoliday) $cell = 'Holiday (08:00)';
                        elseif ($isWeekend) $cell = 'Weekend (08:00)';
                        else $cell = 'Future';
                    } else {
                        if ($att && $att->punch_in) {
                            $minutes = (int)($att->actual_working_minutes ?? 0);
                            if (!$minutes && $att->punch_in && $att->punch_out) {
                                $minutes = Carbon::parse($att->punch_in)->diffInMinutes(Carbon::parse($att->punch_out));
                            }
                            $payable = (int)($att->scheduled_hours ? ($att->scheduled_hours * 60) : $minutes);
                            $cell = 'Present (' . $this->convertMinutes($minutes) . ')';
                        } elseif ($isHoliday) {
                            $cell = 'Holiday (08:00)';
                            $payable = 8*60;
                        } elseif ($isWeekend) {
                            $cell = 'Weekend (08:00)';
                            $payable = 8*60;
                        } else {
                            $cell = 'Absent (00:00)';
                        }
                    }

                    $row[] = $cell;
                    $rowTotalMinutes += $minutes;
                    $rowPayableMinutes += $payable;
                }

                $row[] = $this->convertMinutes($rowTotalMinutes);
                $row[] = $this->convertMinutes($rowPayableMinutes);

                fputcsv($fh, $row);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper: determine weekend for employee - fallback to Saturday & Sunday
     */
   

    /**
     * Convert minutes integer to HH:MM
     */
  public static function convertMinutes($m)
{
    $m = (int)$m;
    $h = floor($m / 60);
    $mm = $m % 60;
    return sprintf('%02d:%02d', $h, $mm);
}
public function organizationEarlyLate(Request $request)
{
    $filter = $request->get('filter', 'last7');
    $branch = $request->get('branch', 'all');
    $department = $request->get('department', 'all');

    $branches = DB::table('branches')->select('id','name')->get();
    $departments = DB::table('department')->select('id','department')->get();

    // 1️⃣ Date Range
    switch ($filter) {
        case 'this_week':
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
            break;
        case 'last_week':
            $start = Carbon::now()->subWeek()->startOfWeek();
            $end   = Carbon::now()->subWeek()->endOfWeek();
            break;
        case 'this_month':
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
            break;
        default:
            $end   = Carbon::now()->endOfDay();
            $start = Carbon::now()->subDays(6)->startOfDay();
            break;
    }

    // Create all dates
    $dates = [];
    $loop = $start->copy();
    while ($loop->lte($end)) {
        $dates[] = $loop->format('Y-m-d');
        $loop->addDay();
    }

    // 2️⃣ Employees
    $empQuery = DB::table('allemployees')
        ->select('id','firstname','lastname','employeeid','branch_id','department');

    if ($branch !== 'all') {
        $empQuery->where('branch_id', $branch);
    }
    if ($department !== 'all') {
        $empQuery->where('department', $department);
    }

    $employees = $empQuery->orderBy('employeeid')->get();
    $branchesMap = DB::table('branches')->pluck('name','id');
    $deptMap = DB::table('department')->pluck('department','id');

    // 3️⃣ Build Giant Table Rows
    $rows = [];

    foreach ($employees as $emp) {
        foreach ($dates as $d) {

            $att = DB::table('attendances')
                ->leftJoin('shifts','attendances.shift_id','=','shifts.id')
                ->where('employee_id',$emp->id)
                ->whereDate('attendances.date',$d)
                ->select(
                    'attendances.*',
                    'shifts.shift_name',
                    'shifts.start_time',
                    'shifts.end_time'
                )
                ->first();

            $firstIn = $att && $att->punch_in ? Carbon::parse($att->punch_in) : null;
            $lastOut = $att && $att->punch_out ? Carbon::parse($att->punch_out) : null;

            $shiftStart = $att && $att->start_time ? Carbon::parse($att->start_time) : null;
            $shiftEnd   = $att && $att->end_time ? Carbon::parse($att->end_time) : null;

            $entryEarly = $entryLate = $exitEarly = $exitLate = "-";
            $totalHours = "-";

            // Entry Early / Late
            if ($firstIn && $shiftStart) {
                if ($firstIn->lt($shiftStart)) {
                    $entryEarly = $shiftStart->diff($firstIn)->format('%H:%I');
                } elseif ($firstIn->gt($shiftStart)) {
                    $entryLate = $firstIn->diff($shiftStart)->format('%H:%I');
                }
            }

            // Exit Early / Late
            if ($lastOut && $shiftEnd) {
                if ($lastOut->lt($shiftEnd)) {
                    $exitEarly = $shiftEnd->diff($lastOut)->format('%H:%I');
                } elseif ($lastOut->gt($shiftEnd)) {
                    $exitLate = $lastOut->diff($shiftEnd)->format('%H:%I');
                }
            }

            // Total
            if ($firstIn && $lastOut) {
                $totalHours = $firstIn->diff($lastOut)->format('%H:%I');
            }

            $rows[] = [
                'emp' => $emp,
                'date' => $d,
                'first_in' => $firstIn ? $firstIn->format('h:i A') : '-',
                'last_out' => $lastOut ? $lastOut->format('h:i A') : '-',
                'entry_early' => $entryEarly,
                'entry_late' => $entryLate,
                'exit_early' => $exitEarly,
                'exit_late' => $exitLate,
                'net_hours' => $totalHours,
                'shift' => $att->shift_name ?? '-',
                'branch' => $branchesMap[$emp->branch_id] ?? 'N/A',
                'department' => $deptMap[$emp->department] ?? 'N/A'
            ];
        }
    }

    return view('hrms.Employee.Reports.Organization.earlylate.index', [
        'branches' => $branches,
        'departments' => $departments,
        'selectedBranch' => $branch,
        'selectedDepartment' => $department,
        'filter' => $filter,
        'rows' => $rows,
        'dates' => $dates,
        'startDate' => $start->format('Y-m-d'),
        'endDate'   => $end->format('Y-m-d')
    ]);
}
public function organizationEarlyLateCSV(Request $request)
{
    $branch = $request->branch ?? 'all';
    $department = $request->department ?? 'all';

    // SAME rows generation
    $rows = session('org_early_late_rows', []);

    $filename = "Organization_EarlyLate_Report.csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$filename\"",
    ];

    return response()->stream(function() use ($rows) {
        $file = fopen('php://output', 'w');

        fputcsv($file, [
            "Employee", "EmpID", "Branch", "Department",
            "Date", "IN", "OUT", "Entry Early", "Entry Late",
            "Exit Early", "Exit Late", "Net Hours", "Shift"
        ]);

        foreach ($rows as $r) {
            fputcsv($file, [
                $r['emp']->firstname . " " . $r['emp']->lastname,
                $r['emp']->employeeid,
                $r['branch'],
                $r['department'],
                $r['date'],
                $r['first_in'],
                $r['last_out'],
                $r['entry_early'],
                $r['entry_late'],
                $r['exit_early'],
                $r['exit_late'],
                $r['net_hours'],
                $r['shift'],
            ]);
        }

        fclose($file);
    }, 200, $headers);
}
  /**
     * Organization Weekly Report (Employee -> Project -> Date cells)
     * Filters: branch, department, start_date, end_date
     */
    public function organizationWeeklyReport(Request $request)
    {
        $branch     = $request->get('branch', 'all');
        $department = $request->get('department', 'all');

        // Date range (default last 7 days)
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(6)->startOfDay();
        $end   = $request->get('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay()   : Carbon::now()->endOfDay();

        if ($end->lt($start)) {
            // swap if user passed reversed dates
            [$start, $end] = [$end, $start];
        }

        // Build ordered list of dates between start and end (Y-m-d)
        $dates = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        // Filters data for selects
        $branches = DB::table('branches')->select('id','name')->orderBy('name')->get();
        $departments = DB::table('department')->select('id','department')->orderBy('department')->get();

        // Employees query (apply branch/department filter)
        $employeesQuery = DB::table('allemployees')
            ->select('id','firstname','lastname','employeeid','branch_id','department')
            ->where('status','active');

        if ($branch !== 'all') $employeesQuery->where('branch_id', $branch);
        if ($department !== 'all') $employeesQuery->where('department', $department);

        $employees = $employeesQuery->orderBy('employeeid')->get();

        // If no employees, render empty view
        if ($employees->isEmpty()) {
            return view('hrms.Employee.Reports.Organization.weekly-report.index', compact(
                'branches','departments','branch','department','dates','employees'
            ))->with([
                'matrix' => [], 'summary' => [], 'start' => $start, 'end' => $end
            ]);
        }

        // Preload timesheet rows for employees within date range
        // We'll fetch timesheet entries for these employees and dates
        $employeeIds = $employees->pluck('id')->toArray();

        $timesheetRows = DB::table('timesheet')
            ->leftJoin('projects','timesheet.project_id','=','projects.id')
            ->whereIn('timesheet.employee_id', $employeeIds)
            ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$dates[0], end($dates)])
            ->select(
                'timesheet.*',
                'projects.projectname as project_name'
            )
            ->get();

        // Build matrix: [employeeId] => [
        //     'employee' => (object),
        //     'projects' => [ projectLabel => ['daily' => [date=>minutes], 'total'=>minutes] ],
        //     'row_total' => minutes
        // ]
        $matrix = [];
        $projectsSet = []; // keep full list of project labels to ensure consistent order if needed

        // Initialize matrix structure for all employees
        foreach ($employees as $emp) {
            $matrix[$emp->id] = [
                'employee' => $emp,
                'projects' => [],
                'row_total' => 0
            ];
        }

        // Populate projects/minutes from timesheetRows
        foreach ($timesheetRows as $row) {
            $empId = $row->employee_id;
            $projLabel = $row->project_name ?? 'Unassigned Project';

            // compute minutes from available start/end datetimes or fallback to duration field if present
            try {
                if ($row->start_date && $row->start_time && $row->end_date && $row->end_time) {
                    $startDT = Carbon::parse($row->start_date . ' ' . $row->start_time);
                    $endDT   = Carbon::parse($row->end_date   . ' ' . $row->end_time);
                    if ($endDT->lt($startDT)) $endDT->addDay(); // assume next day when end < start
                    $minutes = $startDT->diffInMinutes($endDT);
                    $cellDate = $startDT->toDateString();
                } else {
                    // If times not complete, try using minutes/duration column if exists
                    $minutes = (int)($row->minutes ?? $row->duration_minutes ?? 0);
                    // fallback to start_date as cell date
                    $cellDate = $row->start_date ? Carbon::parse($row->start_date)->toDateString() : null;
                }
            } catch (\Exception $ex) {
                continue; // ignore malformed row
            }

            if (!$cellDate) continue;
            if (!in_array($cellDate, $dates)) {
                // if start date out of range but end date in range, try end date
                $endCell = Carbon::parse($row->end_date ?? $cellDate)->toDateString();
                if (in_array($endCell, $dates)) $cellDate = $endCell;
                else continue;
            }

            // ensure entry exists
            if (!isset($matrix[$empId]['projects'][$projLabel])) {
                // initialize daily zeros
                $daily = [];
                foreach ($dates as $d) $daily[$d] = 0;
                $matrix[$empId]['projects'][$projLabel] = [
                    'daily' => $daily,
                    'total' => 0
                ];
            }

            // add minutes
            $matrix[$empId]['projects'][$projLabel]['daily'][$cellDate] += $minutes;
            $matrix[$empId]['projects'][$projLabel]['total'] += $minutes;
            $matrix[$empId]['row_total'] += $minutes;

            $projectsSet[$projLabel] = true;
        }

        // Optionally ensure employees with no projects still have an empty project row
        foreach ($matrix as $empId => &$empRow) {
            if (empty($empRow['projects'])) {
                $projLabel = 'Unassigned Project';
                $daily = [];
                foreach ($dates as $d) $daily[$d] = 0;
                $empRow['projects'][$projLabel] = [
                    'daily' => $daily,
                    'total' => 0
                ];
            }
        }
        unset($empRow);

        // Build summary totals (org-level)
        $summary = [
            'total_minutes' => 0,
            'by_date_minutes' => array_fill_keys($dates, 0)
        ];

        foreach ($matrix as $empRow) {
            foreach ($empRow['projects'] as $proj) {
                foreach ($proj['daily'] as $d => $mins) {
                    $summary['total_minutes'] += $mins;
                    $summary['by_date_minutes'][$d] += $mins;
                }
            }
        }

        // Save last used report rows to session for CSV
        session([
            'org_weekly_matrix' => $matrix,
            'org_weekly_dates'  => $dates,
            'org_weekly_filters' => ['branch'=>$branch,'department'=>$department,'start'=>$start->toDateString(),'end'=>$end->toDateString()]
        ]);

        return view('hrms.Employee.Reports.Organization.weekly-report.index', compact(
            'branches','departments','branch','department','dates','employees','matrix','summary','start','end'
        ));
    }

    /**
     * CSV Export — Organization Weekly Report (Group by employee)
     */
    public function organizationWeeklyCSV(Request $request)
    {
        // Read matrix from session (generated by organizationWeeklyReport)
        $matrix = session('org_weekly_matrix', []);
        $dates  = session('org_weekly_dates', []);
        $filters = session('org_weekly_filters', []);

        $start = $filters['start'] ?? now()->subDays(6)->toDateString();
        $end   = $filters['end']   ?? now()->toDateString();

        $filename = "org_weekly_report_{$start}_to_{$end}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($matrix, $dates) {
            $fh = fopen('php://output', 'w');
            // BOM for Excel
            fwrite($fh, "\xEF\xBB\xBF");

            // Header information
            fputcsv($fh, ['ORGANIZATION WEEKLY REPORT']);
            fputcsv($fh, ['Generated At', Carbon::now()->toDateTimeString()]);
            fputcsv($fh, []);

            // We'll output in grouped style: Employee header row then project rows
            foreach ($matrix as $empRow) {
                $emp = $empRow['employee'];
                // Employee header row
                fputcsv($fh, [ "Employee:", $emp->employeeid, $emp->firstname . ' ' . $emp->lastname ]);
                // Columns header
                $cols = array_merge(['Project'], $dates, ['Total (HH:MM)']);
                fputcsv($fh, $cols);

                foreach ($empRow['projects'] as $projLabel => $proj) {
                    $row = [$projLabel];
                    $rowMinutes = 0;
                    foreach ($dates as $d) {
                        $mins = (int)($proj['daily'][$d] ?? 0);
                        $row[] = self::minutesToHHMM($mins);
                        $rowMinutes += $mins;
                    }
                    $row[] = self::minutesToHHMM($rowMinutes);
                    fputcsv($fh, $row);
                }

                // blank line between employees
                fputcsv($fh, []);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Convert minutes to HH:MM (public static so blade or CSV can call it)
     */
    public static function minutesToHHMM($minutes)
    {
        $minutes = (int)$minutes;
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    /**
     * Determine weekend for employee (reuse logic common in your app)
     */
 public function organizationJobStatusReport(Request $request)
{
    // Filters
    $branch = $request->branch ?? 'all';
    $department = $request->department ?? 'all';
    $from = $request->from ?? Carbon::now()->startOfWeek()->toDateString();
    $to   = $request->to   ?? Carbon::now()->endOfWeek()->toDateString();
    $statusFilter = $request->status ?? 'all';

    // Branch & Department lists
    $branches = DB::table('branches')->select('id','name')->get();
    $departments = DB::table('department')->select('id','department')->get();

    // ================= MAIN JOB STATUS QUERY ===================
    $query = DB::table('timesheet')
        ->leftJoin('tasks', 'timesheet.task_id', '=', 'tasks.id')
        ->leftJoin('projects', 'timesheet.project_id', '=', 'projects.projectid')
        ->leftJoin('allemployees', 'timesheet.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id') // Join with department table
        ->select(
            'timesheet.*',
            'tasks.task as task_name',
            'tasks.status as task_status',
            'projects.projectname',
            'projects.projectid as project_code',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.employeeid',
            'allemployees.branch_id',
            'allemployees.department',
            'department.department as department_name' // Get department name
        )
        ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$from, $to]);

    if ($branch !== 'all')
        $query->where('allemployees.branch_id', $branch);

    if ($department !== 'all')
        $query->where('allemployees.department', $department);

    if ($statusFilter !== 'all')
        $query->where('tasks.status', $statusFilter);

    $rows = $query->orderBy('timesheet.id')->get();

    // ================= BUILD SIMPLE REPORT ======================
    $simpleReport = [];

    foreach ($rows as $row) {

        if (!$row->start_time || !$row->end_time) continue;

        $startDate = Carbon::parse($row->start_date)->format('Y-m-d');
        $startTime = Carbon::parse($row->start_time)->format('H:i:s');
        $endDate   = Carbon::parse($row->end_date)->format('Y-m-d');
        $endTime   = Carbon::parse($row->end_time)->format('H:i:s');

        $start = Carbon::parse("$startDate $startTime");
        $end   = Carbon::parse("$endDate $endTime");

        // If end time is less than start time, assume it's the next day
        if ($end->lt($start)) {
            $end->addDay();
        }

        $mins = $start->diffInMinutes($end);
        $hours = floor($mins / 60);
        $minutes = $mins % 60;
        $total = sprintf('%02d:%02d', $hours, $minutes);

        // Get branch name
        $branchName = 'N/A';
        if ($row->branch_id) {
            $branchData = DB::table('branches')->where('id', $row->branch_id)->first();
            $branchName = $branchData ? $branchData->name : 'N/A';
        }

        $simpleReport[] = [
            'employee'     => $row->employeeid . " - " . $row->firstname . " " . $row->lastname,
            'branch'       => $branchName,
            'department'   => $row->department_name ?? ($row->department ?? '-'), // Use department_name from join
            'project'      => $row->projectname ?? 'Unnamed Project',
            'project_code' => $row->project_code ?? '',
            'task'         => $row->task_name ?? 'Unnamed Task',
            'status'       => $row->task_status ?? 'no_status',
            'start_full'   => $start->format('Y-m-d H:i'),
            'end_full'     => $end->format('Y-m-d H:i'),
            'total'        => $total,
        ];
    }

    // get all unique statuses for filter dropdown
    $allStatuses = DB::table('tasks')->select('status')->distinct()->pluck('status')->toArray();

    return view('hrms.Employee.Reports.Organization.job-status.index', [
        'branches' => $branches,
        'departments' => $departments,
        'branch' => $branch,
        'department' => $department,
        'from' => $from,
        'to'   => $to,
        'statusFilter' => $statusFilter,
        'allStatuses' => $allStatuses,
        'simpleReport' => $simpleReport,
    ]);
}
public function organizationJobStatusCSV(Request $request)
{
    $branch = $request->branch ?? 'all';
    $department = $request->department ?? 'all';
    $from = $request->from;
    $to = $request->to;
    $status = $request->status ?? 'all';

    $filename = "Organization-Job-Status-$from-to-$to.csv";

    // Get data returned by main report function
    $view = $this->organizationJobStatusReport($request);
    $data = $view->getData();

    // The only variable needed
    $rows = $data['simpleReport'];

    $headers = [
        'Content-Type' => 'text/csv',
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    return response()->stream(function () use ($rows, $branch, $department, $from, $to) {

        $file = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");

        // Report title
        fputcsv($file, ['ORGANIZATION JOB STATUS REPORT']);
        fputcsv($file, ["Branch:", $branch]);
        fputcsv($file, ["Department:", $department]);
        fputcsv($file, ["Period:", "$from to $to"]);
        fputcsv($file, []); // empty row

        // Table Header
        fputcsv($file, [
            'Employee',
            'Branch',
            'Department',
            'Project',
            'Task',
            'Status',
            'Start DateTime',
            'End DateTime',
            'Total',
        ]);

        // Table Rows
        foreach ($rows as $r) {
            // Get department name instead of ID
            $departmentName = $r['department'];
            
            // If department is an ID, fetch the actual department name
            if (is_numeric($departmentName) && $departmentName !== '-') {
                $deptData = DB::table('department')->where('id', $departmentName)->first();
                $departmentName = $deptData ? $deptData->department : $departmentName;
            }
            
            fputcsv($file, [
                $r['employee'],
                $r['branch'],
                $departmentName, // Use the department name instead of ID
                $r['project'],
                $r['task'],
                $r['status'],
                $r['start_full'],
                $r['end_full'],
                $r['total'],
            ]);
        }

        fclose($file);

    }, 200, $headers);
}
public function organizationProjectStatusReport(Request $request)
{
    $branch     = $request->branch ?? 'all';
    $department = $request->department ?? 'all';
    $employee   = $request->employee ?? 'all';
    $project    = $request->project ?? 'all';
    $status     = $request->status ?? 'all';

    $from = $request->from ?? Carbon::now()->startOfMonth()->toDateString();
    $to   = $request->to   ?? Carbon::now()->endOfMonth()->toDateString();

    // Filter lists
    $branches = DB::table('branches')->select('id','name')->get();
    $departments = DB::table('department')->select('id','department')->get();
    $allEmployees = DB::table('allemployees')->select('id','employeeid','firstname','lastname')->orderBy('employeeid')->get();
    $allProjects = DB::table('projects')->select('id','projectname')->get();
    $allStatuses = DB::table('projects')->select('status')->distinct()->pluck('status')->toArray();

    // Main Query
    $query = DB::table('timesheet')
        ->leftJoin('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->select(
            'timesheet.*',
            'projects.projectname',
            'projects.status as project_status',
            'allemployees.employeeid',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.branch_id',
            'allemployees.department'
        )
        ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$from, $to]);


    if ($branch !== 'all')     $query->where('allemployees.branch_id', $branch);
    if ($department !== 'all') $query->where('allemployees.department', $department);
    if ($employee !== 'all')   $query->where('allemployees.id', $employee);
    if ($project !== 'all')    $query->where('projects.id', $project);
    if ($status !== 'all')     $query->where('projects.status', $status);

    $rows = $query->orderBy('timesheet.id')->get();

    // ===================== BUILD REPORT =====================
    $projectTotals = [];
    $projectEmployeeHours = [];  
    $chartProjects = [];

    foreach ($rows as $row) {

        if (!$row->start_time || !$row->end_time) continue;

        // Clean values
        $start = Carbon::parse(substr($row->start_date,0,10) . " " . substr($row->start_time,0,8));
        $end   = Carbon::parse(substr($row->end_date,0,10)   . " " . substr($row->end_time,0,8));
        if ($end->lt($start)) $end->addDay();

        $mins = $start->diffInMinutes($end);

        $projectName = $row->projectname;
        $employeeName = $row->employeeid . " - " . $row->firstname . " " . $row->lastname;
        $branchName = DB::table('branches')->where('id', $row->branch_id)->value('name');

        // PROJECT TOTALS
        if (!isset($projectTotals[$projectName])) {
            $projectTotals[$projectName] = [
                'project' => $projectName,
                'branch' => $branchName,
                'department' => $row->department,
                'status' => $row->project_status,
                'total_minutes' => 0
            ];
        }
        $projectTotals[$projectName]['total_minutes'] += $mins;

        // EMPLOYEE HOURS
        if (!isset($projectEmployeeHours[$projectName])) {
            $projectEmployeeHours[$projectName] = [];
        }
        if (!isset($projectEmployeeHours[$projectName][$employeeName])) {
            $projectEmployeeHours[$projectName][$employeeName] = 0;
        }
        $projectEmployeeHours[$projectName][$employeeName] += $mins;
    }

    // Format totals and chart values
    foreach ($projectTotals as $p => $x) {
        $chartProjects[$p] = $x['total_minutes']; // only minutes

        // Format HH:MM
        $projectTotals[$p]['total'] = sprintf('%02d:%02d',
            floor($x['total_minutes']/60),
            $x['total_minutes']%60
        );
    }

    // Return view
    return view('hrms.Employee.Reports.Organization.project-status.index', [
        'branches' => $branches,
        'departments' => $departments,
        'allEmployees' => $allEmployees,
        'allProjects' => $allProjects,
        'allStatuses' => $allStatuses,

        'branch' => $branch,
        'department' => $department,
        'employee' => $employee,
        'project' => $project,
        'status' => $status,
        'from' => $from,
        'to' => $to,

        'projectTotals' => $projectTotals,
        'projectEmployeeHours' => $projectEmployeeHours,
        'chartProjects' => $chartProjects
    ]);
}





public function organizationProjectStatusCSV(Request $request)
{
    // --- Read filters from request ---
    $branch     = $request->branch ?? 'all';
    $department = $request->department ?? 'all';
    $employee   = $request->employee ?? 'all';
    $from       = $request->from;
    $to         = $request->to;
    $project    = $request->project ?? 'all';
    $status     = $request->status ?? 'all';

    // --- Re-run report logic to get prepared data ---
    $data = $this->organizationProjectStatusReport($request)->getData();

    $projectEmployeeHours = $data['projectEmployeeHours'];   // employee mins per project
    $projectTotals        = $data['projectTotals'];          // total mins per project

    $filename = "Organization-Project-Status-$from-to-$to.csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    return response()->stream(function () use ($projectEmployeeHours, $projectTotals) {

        $file = fopen('php://output', 'w');

        // CSV Header Row
        fputcsv($file, [
            "Project",
            "Branch",
            "Department",
            "Employee",
            "Employee Hours (HH:MM)",
            "Project Total (HH:MM)",
            "Project Status"
        ]);

        foreach ($projectEmployeeHours as $project => $emps) {

            $projTotal = $projectTotals[$project]['total_minutes'] ?? 0;
            $projBranch = $projectTotals[$project]['branch'] ?? '-';
            $projDept   = $projectTotals[$project]['department'] ?? '-';
            $projStatus = $projectTotals[$project]['status'] ?? '-';

            $projTotalFmt = sprintf('%02d:%02d', floor($projTotal / 60), $projTotal % 60);

            foreach ($emps as $emp => $mins) {

                $empFmt = sprintf('%02d:%02d', floor($mins / 60), $mins % 60);

                fputcsv($file, [
                    $project,
                    $projBranch,
                    $projDept,
                    $emp,
                    $empFmt,
                    $projTotalFmt,
                    ucfirst($projStatus)
                ]);
            }
        }

        fclose($file);

    }, 200, $headers);
}


}
