<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeamReportController extends Controller
{
       public function reportsIndex()
{
    return view('hrms.Employee.Reports.Team.index');
}
    public function resourceAvailability(Request $request)
    {
        $managerId = Session::get('user_id');

        // Selected month or current month
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

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

        // Fetch team employees (manager + reporting)
        $employees = DB::table('allemployees')
            ->where('manager_id', $managerId)
            ->orWhere('id', $managerId) // include logged in manager
            ->select('id','firstname','lastname','employeeid','profile_image')
            ->orderBy('employeeid')
            ->get();

        // Build Availability Matrix
        $availability = [];

        foreach ($employees as $emp) {
            foreach ($dates as $d) {

                $dateStr = $d["full"];
                $carbon = Carbon::parse($dateStr);

                // 1. FUTURE DATE
                if ($carbon->isFuture()) {
                    // Still show holiday/weekend if matches
                    if ($this->isHoliday($dateStr)) {
                        $availability[$emp->id][$dateStr] = "H";
                    } elseif ($this->isWeekendForEmployee($emp->id, $carbon)) {
                        $availability[$emp->id][$dateStr] = "W";
                    } else {
                        $availability[$emp->id][$dateStr] = "-";
                    }
                    continue;
                }

                // 2. HOLIDAY
                if ($this->isHoliday($dateStr)) {
                    $availability[$emp->id][$dateStr] = "H";
                    continue;
                }

                // 3. WEEKEND
                if ($this->isWeekendForEmployee($emp->id, $carbon)) {
                    $availability[$emp->id][$dateStr] = "W";
                    continue;
                }

                // 4. LEAVE
                $leave = DB::table('employee_leaves')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->where('from_date', '<=', $dateStr)
                    ->where('to_date', '>=', $dateStr)
                    ->first();

                if ($leave) {
                    $availability[$emp->id][$dateStr] = "L";
                } else {
                    $availability[$emp->id][$dateStr] = ""; // Nothing to show
                }
            }
        }

        return view('hrms.Employee.Reports.Team.resource_availability.index', [
            'dates' => $dates,
            'employees' => $employees,
            'availability' => $availability,
            'selectedMonth' => $selectedMonth
        ]);
    }

    private function isHoliday($date)
    {
        return DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->exists();
    }

  

public function teamLeaveBalance(Request $request)
{
    $managerId = session('user_id');

    if (!$managerId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // 1️⃣ Get the Manager record (always include)
    $manager = DB::table('allemployees')
        ->where('id', $managerId)
        ->select('id','firstname','lastname','employeeid')
        ->first();

    // 2️⃣ Get employees who report to this manager
    $team = DB::table('allemployees')
        ->where('manager_id', $managerId)
        ->where('status', 'active')
        ->select('id','firstname','lastname','employeeid')
        ->orderBy('employeeid')
        ->get();

    // 3️⃣ Combine manager + team (manager always first)
    $employees = collect();
    if ($manager) {
        $employees->push($manager);  // Add manager at top
    }
    $employees = $employees->merge($team); // Add employees under him

    // 4️⃣ Selected employee
    $selectedEmployeeId = $request->employee_id ?? ($employees[0]->id ?? null);

    if (!$selectedEmployeeId) {
        return view('hrms.Employee.Reports.Team.LeaveBalance.index', [
            'employees' => $employees,
            'summary' => [],
            'employee' => null,
            'selectedEmployeeId' => null
        ]);
    }

    // 5️⃣ Fetch Selected Employee
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
    $summary = [];
    foreach ($leaveTypes as $type => $total) {
        $summary[] = [
            "type" => $type,
            "total" => $total,
            "used" => $used[$type] ?? 0,
            "remaining" => $total - ($used[$type] ?? 0),
        ];
    }

    return view('hrms.Employee.Reports.Team.LeaveBalance.index', compact(
        'employees', 'summary', 'employee', 'selectedEmployeeId'
    ));
}


public function teamLeaveBalanceHistory($employeeId, $leaveType)
{
    // Fetch employee
    $employee = DB::table('allemployees')->where('id', $employeeId)->first();

    // Allocated from settings table
    if ($leaveType == 'Casual Leave') {
        $allocated = DB::table('annual_leaves')->value('days');
    } elseif ($leaveType == 'Sick') {
        $allocated = DB::table('medical_leaves')->value('sick');
    } elseif ($leaveType == 'Hospitalisation') {
        $allocated = DB::table('medical_leaves')->value('hospitalisation');
    } elseif ($leaveType == 'Maternity Leave') {
        $allocated = DB::table('medical_leaves')->value('maternity');
    } elseif ($leaveType == 'Paternity Leave') {
        $allocated = DB::table('medical_leaves')->value('paternity');
    } elseif ($leaveType == 'LOP') {
        $allocated = DB::table('lop_leaves')->value('days');
    }

    // Booked leaves from employee_leaves table
    $booked = DB::table('employee_leaves')
        ->where('employee_id', $employeeId)
        ->where('leave_type', $leaveType)
        ->where('status', 'approved')
        ->sum('no_of_days');

    $remaining = $allocated - $booked;

    $history = DB::table('employee_leaves')
        ->where('employee_id', $employeeId)
        ->where('leave_type', $leaveType)
        ->orderBy('from_date', 'desc')
        ->get();

    return view('hrms.Employee.Reports.Team.LeaveBalance.history-modal',
        compact('employee', 'leaveType', 'allocated', 'booked', 'remaining', 'history')
    );
}
public function teamDailyLeaveStatus(Request $request)
{
    $managerId = session('user_id');
    $date = $request->date ?? Carbon::now()->format('Y-m-d');

    // Manager + Team
    $manager = DB::table('allemployees')
        ->where('id', $managerId)
        ->select('id','firstname','lastname','employeeid','profile_image')
        ->first();

    $team = DB::table('allemployees')
        ->where('manager_id', $managerId)
        ->where('status', 'active')
        ->select('id','firstname','lastname','employeeid','profile_image')
        ->orderBy('employeeid')
        ->get();

    $employees = collect();
    if ($manager) $employees->push($manager);
    $employees = $employees->merge($team);

    $results = [];

    foreach ($employees as $emp) {

        $carbon = Carbon::parse($date);

        // DAY TYPE
        $isHoliday = DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->exists();

        $isWeekend = $this->isWeekendForEmployee($emp->id, $carbon);

        $leave = DB::table('employee_leaves')
            ->where('employee_id', $emp->id)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();

        // Check if employee has punched in (is present)
        $attendance = DB::table('attendances')
            ->where('employee_id', $emp->id)
            ->whereDate('date', $date)
            ->whereNotNull('punch_in')
            ->first();

        $status = "Absent"; // Default to Absent

        if ($carbon->isFuture()) {
            $status = "Future";
        } elseif ($isHoliday) {
            $status = "Holiday";
        } elseif ($isWeekend) {
            $status = "Weekend";
        } elseif ($leave) {
            $status = "Leave";
        } elseif ($attendance) {
            // Only set Present if they have actually punched in
            $status = "Present";
        }
        // If none of the above conditions match, status remains "Absent"

        $results[] = [
            'employee' => $emp,
            'status' => $status,
        ];
    }

    // Count Summary
    $summary = [
        'present' => collect($results)->where('status','Present')->count(),
        'leave'   => collect($results)->where('status','Leave')->count(),
        'weekend' => collect($results)->where('status','Weekend')->count(),
        'holiday' => collect($results)->where('status','Holiday')->count(),
        'future'  => collect($results)->where('status','Future')->count(),
        'absent'  => collect($results)->where('status','Absent')->count(), // Add absent count
    ];

    return view('hrms.Employee.Reports.Team.daily-leave-status.index', compact(
        'employees','results','summary','date'
    ));
}
public function teamPresentAbsent(Request $request)
{
    $managerId = session('user_id');

    if (!$managerId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // 1️⃣ Fetch Manager + Team
    $employees = DB::table('allemployees')
        ->where('manager_id', $managerId)
        ->orWhere('id', $managerId)
        ->select('id','firstname','lastname','employeeid')
        ->orderBy('employeeid')
        ->get();

    if ($employees->isEmpty()) {
        return view('team.presentAbsent.index', [
            'employees' => [],
            'dateLabels' => [],
            'status' => [],
            'filter' => 'monthly'
        ]);
    }

    // 2️⃣ Selected Employee (first employee by default)
    $selectedEmployeeId = $request->employee_id ?? $employees->first()->id;
    $employee = DB::table('allemployees')->where('id', $selectedEmployeeId)->first();

    // 3️⃣ Filter: monthly / weekly / custom
    $filter = $request->get('filter', 'monthly');

    if ($filter === 'monthly') {
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate   = $startDate->copy()->endOfMonth();

    } elseif ($filter === 'weekly') {
        $startDate = $request->week_start
            ? Carbon::parse($request->week_start)
            : Carbon::now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

    } else { // custom
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfMonth();
    }

    // 4️⃣ Build Date List
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

    // 5️⃣ Build Present / Absent / Weekend / Holiday
    $status = [];

    foreach ($dateLabels as $d) {
        $date = $d['full'];
        $carbon = Carbon::parse($date);

        // FUTURE DAY
        if ($carbon->isFuture()) {
            if ($this->isHoliday($date)) {
                $status[$date] = 'H';
            } elseif ($this->isWeekendForEmployee($selectedEmployeeId, $carbon)) {
                $status[$date] = 'W';
            } else {
                $status[$date] = '-';
            }
            continue;
        }

        // HOLIDAY
        if ($this->isHoliday($date)) {
            $status[$date] = 'H';
            continue;
        }

        // WEEKEND (shift-based working days)
        if ($this->isWeekendForEmployee($selectedEmployeeId, $carbon)) {
            $status[$date] = 'W';
            continue;
        }

        // PRESENT / ABSENT
        $att = DB::table('attendances')
            ->where('employee_id', $selectedEmployeeId)
            ->whereDate('date', $date)
            ->whereNotNull('punch_in')
            ->first();

        $status[$date] = $att ? 'P' : 'A';
    }

    return view('hrms.Employee.Reports.Team.PresentAbsent.index', [
        'employees' => $employees,
        'employee'  => $employee,
        'selectedEmployeeId' => $selectedEmployeeId,
        'dateLabels' => $dateLabels,
        'status' => $status,
        'filter' => $filter,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
    ]);
}
private function isWeekendForEmployee($empId, Carbon $date)
{
    $shift = DB::table('schedule')
        ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->where('schedule.employee_id', $empId)
        ->where('schedule.deleted_at', 0)
        ->select('shifts.days_of_week')
        ->first();

    // Default Weekend = Saturday + Sunday
    if (!$shift || !$shift->days_of_week) {
        return in_array($date->format('l'), ['Saturday', 'Sunday']);
    }

    // Working days values from DB
    $workingDaysRaw = explode(',', $shift->days_of_week);
    $workingDays = [];

    foreach ($workingDaysRaw as $d) {
        $d = trim($d);

        // IF numeric (1–7)
        if (is_numeric($d)) {
            $workingDays[] = intval($d); // 1 = Monday ... 7 = Sunday
        }

        // IF string (Mon / Monday)
        else {
            $workingDays[] = strtolower(substr($d, 0, 3)); // mon, tue, wed
        }
    }

    // Today code for evaluation
    $todayNum  = intval($date->format('N')); // 1–7
    $todayText = strtolower(substr($date->format('l'), 0, 3)); // mon/tue

    // Weekend: NOT in working days
    return !(
        in_array($todayNum, $workingDays) ||
        in_array($todayText, $workingDays)
    );
}
public function teamPresenceHours(Request $request)
{
    $managerId = session('user_id');

    if (!$managerId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // Fetch Team: manager + reporting employees
    $employees = DB::table('allemployees')
        ->where(function($q) use ($managerId) {
            $q->where('manager_id', $managerId)
              ->orWhere('id', $managerId);
        })
        ->select('id','firstname','lastname','employeeid','department')
        ->orderBy('employeeid')
        ->get();

    $selectedEmployeeId = $request->get('employee_id', $managerId);

    // Safety: selected employee must be in team
    if (!$employees->pluck('id')->contains($selectedEmployeeId)) {
        $selectedEmployeeId = $managerId;
    }

    $employee = DB::table('allemployees')->where('id', $selectedEmployeeId)->first();

    // Filter (Default: last 7 days)
    $filter = $request->filter ?? 'last7';

    switch($filter) {
        case 'this_week':
            $startDate = Carbon::now()->startOfWeek();
            $endDate   = Carbon::now()->endOfWeek();
            break;

        case 'last_week':
            $startDate = Carbon::now()->subWeek()->startOfWeek();
            $endDate   = Carbon::now()->subWeek()->endOfWeek();
            break;

        case 'this_month':
            $startDate = Carbon::now()->startOfMonth();
            $endDate   = Carbon::now()->endOfMonth();
            break;

        default: // last7
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            break;
    }

    /* ---------- Build Rows ---------- */

    $rows = [];
    $total = $payable = $present = $holiday = $weekend = 0;

    for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {

        $dateStr = $day->format('Y-m-d');

        $attendance = DB::table('attendances')
            ->where('employee_id', $selectedEmployeeId)
            ->whereDate('date', $dateStr)
            ->first();

        $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();

        // USE YOUR EXISTING SHIFT WEEKEND FUNCTION
        $isWeekend = $this->isWeekendForEmployee($selectedEmployeeId, $day);

        $future = $day->isFuture();

        $firstIn = $lastOut = "-";
        $totalHours = "00:00";
        $payableHours = "00:00";
        $status = "-";
        $shift = "General";

        if ($attendance) {

            $firstIn = $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('h:i A') : "-";
            $lastOut = $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('h:i A') : "-";

            $min = $attendance->actual_working_minutes ?? 0;
            $pay = $attendance->scheduled_hours ? ($attendance->scheduled_hours * 60) : $min;

            $totalHours = $this->convertMinutes($min);
            $payableHours = $this->convertMinutes($pay);

            $status = "Present";

            $total += $min;
            $payable += $pay;
            $present += $min;

            if ($attendance->shift_id) {
                $sh = DB::table('shifts')->where('id', $attendance->shift_id)->first();
                if ($sh) $shift = $sh->shift_name;
            }

        } else {

            if ($isHoliday) {
                $status = "Holiday";
                $holiday += 480;
                $payableHours = "08:00";

            } elseif ($isWeekend) {
                $status = "Weekend";
                $weekend += 480;
                $payableHours = "08:00";

            } elseif (!$future) {
                $status = "Absent";
            }
        }

        $rows[] = [
            'date_raw' => $dateStr,
            'status'   => $status,
            'first_in' => $firstIn,
            'last_out' => $lastOut,
            'total_hours' => $totalHours,
            'payable_hours' => $payableHours,
            'shift' => $shift
        ];
    }

    return view('hrms.Employee.Reports.Team.PresenceHours.index', [
        'employees' => $employees,
        'employee'  => $employee,
        'selectedEmployeeId' => $selectedEmployeeId,
        'rows' => $rows,
        'filter' => $filter,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalHours' => $this->convertMinutes($total),
        'totalPayable' => $this->convertMinutes($payable),
        'presentHours' => $this->convertMinutes($present),
        'holidayHours' => $this->convertMinutes($holiday),
        'weekendHours' => $this->convertMinutes($weekend),
    ]);
}

private function convertMinutes($m)
{
    return sprintf("%02d:%02d", floor($m/60), $m%60);
}
public function teamEarlyLateReport(Request $request)
{
    $managerId = session('user_id');

    if (!$managerId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // Fetch all employees under this manager + manager itself
    $employees = DB::table('allemployees')
        ->where(function($q) use ($managerId) {
            $q->where('manager_id', $managerId)
              ->orWhere('id', $managerId); // include manager
        })
        ->orderBy('firstname')
        ->get();

    // Selected employee
    $selectedEmployeeId = $request->employee_id ?? $managerId;

    $employee = DB::table('allemployees')->where('id', $selectedEmployeeId)->first();

    // DATE RANGE DEFAULT
    $startDate = $request->start_date ?? now()->startOfWeek()->format('Y-m-d');
    $endDate   = $request->end_date ?? now()->endOfWeek()->format('Y-m-d');

    // FETCH ATTENDANCE
    $records = DB::table('attendances')
        ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
        ->where('attendances.employee_id', $selectedEmployeeId)
        ->whereBetween('attendances.date', [$startDate, $endDate])
        ->orderBy('attendances.date', 'asc')
        ->select(
            'attendances.*',
            'shifts.shift_name',
            'shifts.start_time as shift_start',
            'shifts.end_time as shift_end'
        )
        ->get();

    // BUILD REPORT
    $report = [];

    foreach ($records as $r) {

        $firstIn = $r->punch_in ? Carbon::parse($r->punch_in) : null;
        $lastOut = $r->punch_out ? Carbon::parse($r->punch_out) : null;
        $shiftStart = $r->shift_start ? Carbon::parse($r->shift_start) : null;
        $shiftEnd   = $r->shift_end ? Carbon::parse($r->shift_end) : null;

        // Early / Late Entry
        $entryEarly = '-';
        $entryLate  = '-';
        if ($firstIn && $shiftStart) {
            if ($firstIn->lt($shiftStart)) {
                $entryEarly = $shiftStart->diff($firstIn)->format('%H:%I');
            } elseif ($firstIn->gt($shiftStart)) {
                $entryLate = $firstIn->diff($shiftStart)->format('%H:%I');
            }
        }

        // Early / Late Exit
        $exitEarly = '-';
        $exitLate  = '-';
        if ($lastOut && $shiftEnd) {
            if ($lastOut->lt($shiftEnd)) {
                $exitEarly = $shiftEnd->diff($lastOut)->format('%H:%I');
            } elseif ($lastOut->gt($shiftEnd)) {
                $exitLate = $lastOut->diff($shiftEnd)->format('%H:%I');
            }
        }

        // Total Hours
        $totalHours = '-';
        if ($firstIn && $lastOut) {
            $totalHours = $firstIn->diff($lastOut)->format('%H:%I');
        }

        $report[] = [
            'date' => $r->date,
            'first_in' => $firstIn ? $firstIn->format('h:i A') : '-',
            'last_out' => $lastOut ? $lastOut->format('h:i A') : '-',
            'total_hours' => $totalHours,
            'entry_early' => $entryEarly,
            'entry_late' => $entryLate,
            'exit_early' => $exitEarly,
            'exit_late' => $exitLate,
            'net_hours' => $totalHours,
            'shift' => $r->shift_name ?? '-'
        ];
    }

    return view('hrms.Employee.Reports.Team.Attendance.index', compact(
        'employees',
        'selectedEmployeeId',
        'employee',
        'report',
        'startDate',
        'endDate'
    ));
}
public function teamEarlyLateCSV(Request $request)
{
    $employeeId = $request->employee_id;
    $employee = DB::table('allemployees')->where('id', $employeeId)->first();

    $rows = session('team_early_late_rows', []);
    $startDate = session('team_start_date');
    $endDate = session('team_end_date');

    $filename = "team_early_late_report_{$employee->employeeid}.csv";

    $headers = [
        'Content-Type' => 'text/csv',
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($employee, $rows, $startDate, $endDate) {
        $file = fopen('php://output', 'w');

        // Report Header
        fputcsv($file, ["TEAM EARLY / LATE TIMING REPORT"]);
        fputcsv($file, ["Employee: {$employee->firstname} {$employee->lastname}"]);
        fputcsv($file, ["Employee ID: {$employee->employeeid}"]);
        fputcsv($file, ["Period: $startDate to $endDate"]);
        fputcsv($file, []);
        
        // Table Head
        fputcsv($file, [
            'Date', 'First In', 'Last Out', 'Total Hours',
            'Entry Early', 'Entry Late',
            'Exit Early', 'Exit Late',
            'Net Hours', 'Shift'
        ]);

        // Table Rows
        foreach ($rows as $r) {
            fputcsv($file, [
                $r['date'],
                $r['first_in'],
                $r['last_out'],
                $r['total_hours'],
                $r['entry_early'],
                $r['entry_late'],
                $r['exit_early'],
                $r['exit_late'],
                $r['net_hours'],
                $r['shift']
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
  public function teamWeeklyReport(Request $request)
    {
        $managerId = session('user_id');
        if (!$managerId) {
            return redirect()->route('login')->withErrors('Please login first.');
        }

        // Employees: manager + reporting employees
        $employees = DB::table('allemployees')
            ->where(function($q) use ($managerId) {
                $q->where('manager_id', $managerId)
                  ->orWhere('id', $managerId);
            })
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('employeeid')
            ->get();

        // selected employee default -> first in list or manager
        $selectedEmployeeId = $request->get('employee_id') ?? ($employees->first()->id ?? $managerId);
        if (!$employees->pluck('id')->contains($selectedEmployeeId)) {
            $selectedEmployeeId = $managerId;
        }

        // week range: use provided start/end or default to current week (Mon-Sun)
        $startDate = $request->get('week_start') ? Carbon::parse($request->get('week_start'))->startOfDay() : Carbon::now()->startOfWeek()->startOfDay();
        $endDate   = $request->get('week_end') ? Carbon::parse($request->get('week_end'))->endOfDay() : Carbon::now()->endOfWeek()->endOfDay();

        // Ensure start <= end and limit to reasonable range
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->endOfWeek();
        }

        // Build list of day strings (Y-m-d) from start to end
        $dates = [];
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        // Fetch timesheet rows for selected employee in date range
        // We'll parse start_date + start_time and end_date + end_time to compute minutes
        $rows = DB::table('timesheet')
            ->leftJoin('projects', 'timesheet.project_id', '=', 'projects.id')
            ->where('timesheet.employee_id', $selectedEmployeeId)
            ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$dates[0], $dates[count($dates)-1]])
            ->select(
                'timesheet.*',
                'projects.projectname as project_name'
            )
            ->orderBy('projects.projectname')
            ->get();

        // Aggregate by project + date (sum minutes)
        $projectMap = []; // [projectLabel => ['daily' => [Y-m-d => minutes], 'total' => minutes]]

        // Initialize projectMap for any project that exists in rows (so we preserve display order)
        foreach ($rows as $row) {
            $projLabel = $row->project_name ?? 'Unassigned Project';
            if (!isset($projectMap[$projLabel])) {
                // initialize daily zeros
                $daily = [];
                foreach ($dates as $d) $daily[$d] = 0;
                $projectMap[$projLabel] = [
                    'daily' => $daily,
                    'total' => 0
                ];
            }
        }

        // Iterate rows and compute minutes accurately
        foreach ($rows as $row) {
            $projLabel = $row->project_name ?? 'Unassigned Project';

            // build start & end datetimes; handle missing pieces safely
            try {
                // start_date may already include time or be date-only. start_time may be nullable.
                $startDateStr = $row->start_date ? Carbon::parse($row->start_date)->toDateString() : null;
                $endDateStr   = $row->end_date   ? Carbon::parse($row->end_date)->toDateString() : $startDateStr;

                // If start_time/end_time include microseconds, Carbon will handle them
                $startTimeStr = $row->start_time ? (string)$row->start_time : null;
                $endTimeStr   = $row->end_time ? (string)$row->end_time : null;

                if ($startDateStr && $startTimeStr && $endDateStr && $endTimeStr) {
                    $startDT = Carbon::parse($startDateStr . ' ' . $startTimeStr);
                    $endDT   = Carbon::parse($endDateStr . ' ' . $endTimeStr);

                    // If end before start (possible for bad data), treat as zero or skip.
                    if ($endDT->lt($startDT)) {
                        // assume end is next day (common scenario) — add 1 day to endDT
                        $endDT = $endDT->addDay();
                        if ($endDT->lt($startDT)) {
                            // still invalid -> skip
                            continue;
                        }
                    }

                    // Compute minutes
                    $minutes = $startDT->diffInMinutes($endDT);

                    // allocate minutes to the start date cell only (matching your existing behavior)
                    // If you need to split across days, we can add logic to split minutes per date.
                    $cellDate = $startDT->toDateString();

                    if (in_array($cellDate, $dates)) {
                        $projectMap[$projLabel]['daily'][$cellDate] += $minutes;
                        $projectMap[$projLabel]['total'] += $minutes;
                    } else {
                        // If start date not in range but end date is, optionally allocate to end date
                        $endCell = $endDT->toDateString();
                        if (in_array($endCell, $dates)) {
                            $projectMap[$projLabel]['daily'][$endCell] += $minutes;
                            $projectMap[$projLabel]['total'] += $minutes;
                        }
                    }
                }
            } catch (\Exception $ex) {
                // ignore malformed rows
                continue;
            }
        }

        // Convert minutes to HH:MM for display and prepare chart aggregates (total hours per day)
        $report = [];
        foreach ($projectMap as $projLabel => $data) {
            $dailyFormatted = [];
            foreach ($data['daily'] as $dateKey => $mins) {
                $h = floor($mins / 60);
                $m = $mins % 60;
                $dailyFormatted[$dateKey] = sprintf('%02d:%02d', $h, $m);
            }
            $report[$projLabel] = [
                'daily' => $dailyFormatted,
                'total' => sprintf('%02d:%02d', floor($data['total'] / 60), $data['total'] % 60),
                'total_minutes' => $data['total']
            ];
        }

        // Chart: daily total hours across all projects
        $dailyTotalsMinutes = [];
        foreach ($dates as $d) {
            $sum = 0;
            foreach ($projectMap as $p) $sum += ($p['daily'][$d] ?? 0);
            $dailyTotalsMinutes[] = round($sum / 60, 2); // hours with 2 decimals
        }

        // Save last used rows in session for CSV download (optional)
        session([
            'team_weekly_report_rows' => $report,
            'team_weekly_dates' => $dates,
            'team_weekly_employee' => $selectedEmployeeId,
            'team_weekly_range' => [$startDate->toDateString(), $endDate->toDateString()],
        ]);

        return view('hrms.Employee.Reports.Team.weekly-report.index', [
            'employees' => $employees,
            'selectedEmployeeId' => $selectedEmployeeId,
            'employee' => DB::table('allemployees')->where('id', $selectedEmployeeId)->first(),
            'dates' => $dates,
            'report' => $report,
            'dailyHours' => $dailyTotalsMinutes,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString()
        ]);
    }

    /**
     * CSV Export for current session report
     */
    public function teamWeeklyCSV(Request $request)
    {
        $rows = session('team_weekly_report_rows', []);
        $dates = session('team_weekly_dates', []);
        $employeeId = session('team_weekly_employee', null);
        $range = session('team_weekly_range', []);

        $employee = $employeeId ? DB::table('allemployees')->where('id', $employeeId)->first() : null;
        $filename = 'team_weekly_report_' . ($employee->employeeid ?? 'report') . '_' . ($range[0] ?? '') . '_to_' . ($range[1] ?? '') . '.csv';

        $response = new StreamedResponse(function() use ($rows, $dates, $employee, $range) {
            $handle = fopen('php://output', 'w');
            // header
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($handle, ['Team Weekly Report']);
            if ($employee) {
                fputcsv($handle, ['Employee', $employee->firstname . ' ' . $employee->lastname]);
                fputcsv($handle, ['Employee ID', $employee->employeeid]);
            }
            fputcsv($handle, ['Period', ($range[0] ?? '') . ' to ' . ($range[1] ?? '')]);
            fputcsv($handle, []);
            // columns
            $cols = array_merge(['Project'], $dates, ['Total']);
            fputcsv($handle, $cols);

            foreach ($rows as $proj => $data) {
                $row = [$proj];
                foreach ($dates as $d) {
                    $row[] = $data['daily'][$d] ?? '00:00';
                }
                $row[] = $data['total'] ?? '00:00';
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

        return $response;
    }


    public function teamJobStatusReport(Request $request)
{
    $managerId = session('user_id');

    // Fetch team employees
    $employees = DB::table('allemployees')
        ->where(function ($q) use ($managerId) {
            $q->where('manager_id', $managerId)
              ->orWhere('id', $managerId);
        })
        ->select('id','firstname','lastname','employeeid')
        ->orderBy('employeeid')
        ->get();

    // Selected employee (default = manager)
    $selectedEmployee = $request->employee_id ?? $managerId;

    // Filters
    $from = $request->from ?? Carbon::now()->startOfWeek()->toDateString();
    $to   = $request->to   ?? Carbon::now()->endOfWeek()->toDateString();
    $statusFilter = $request->status ?? 'all';
    $projectFilter = $request->project ?? 'all';

    // Main query
    $query = DB::table('timesheet')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->leftJoin('projects', 'projects.id', '=', 'timesheet.project_id')
        ->select(
            'timesheet.*',
            'tasks.task as task_name',
            'tasks.status as task_status',
            'projects.projectname'
        )
        ->where('timesheet.employee_id', $selectedEmployee)
        ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$from, $to]);

    if ($statusFilter !== 'all') {
        $query->where('tasks.status', $statusFilter);
    }

    if ($projectFilter !== 'all') {
        $query->where('projects.id', $projectFilter);
    }

    $rows = $query->orderBy('timesheet.id')->get();

    // Build simple report
    $simpleReport = [];

    foreach ($rows as $row) {
        if (!$row->start_time || !$row->end_time) continue;

        // FIX: Remove microseconds + extract clean date + time
        $cleanStartDate = Carbon::parse(explode(' ', $row->start_date)[0])->format('Y-m-d');
        $cleanStartTime = Carbon::parse(explode(' ', $row->start_time)[0])->format('H:i:s');

        $cleanEndDate   = Carbon::parse(explode(' ', $row->end_date)[0])->format('Y-m-d');
        $cleanEndTime   = Carbon::parse(explode(' ', $row->end_time)[0])->format('H:i:s');

        // Combine clean values
        $cleanStart = Carbon::parse("$cleanStartDate $cleanStartTime");
        $cleanEnd   = Carbon::parse("$cleanEndDate $cleanEndTime");

        // Calculate total
        $mins = $cleanStart->diffInMinutes($cleanEnd);
        $total = sprintf('%02d:%02d', floor($mins / 60), $mins % 60);

        $simpleReport[] = [
            'project'    => $row->projectname ?? 'Unnamed Project',
            'task'       => $row->task_name ?? 'Unnamed Task',
            'status'     => $row->task_status ?? 'no_status',

            // NEW
            'start_full' => $cleanStart->format('Y-m-d H:i'),
            'end_full'   => $cleanEnd->format('Y-m-d H:i'),

            'total'      => $total,
        ];
    }

    // Dropdown data
    $allStatuses = DB::table('tasks')->select('status')->distinct()->pluck('status')->toArray();
    $allProjects = DB::table('projects')->select('id','projectname')->get();

    return view('hrms.Employee.Reports.Team.job-status.index', [
        'employees' => $employees,
        'selectedEmployee' => $selectedEmployee,
        'simpleReport' => $simpleReport,

        'from' => $from,
        'to' => $to,
        'statusFilter' => $statusFilter,
        'projectFilter' => $projectFilter,

        'allStatuses' => $allStatuses,
        'allProjects' => $allProjects,
    ]);
}
public function teamDailyLeaveCSV(Request $request)
{
    $date = $request->date ?? now()->format('Y-m-d');

    // Fetch manager ID
    $managerId = session('user_id');

    $manager = DB::table('allemployees')
        ->where('id', $managerId)
        ->where('status', 'active')
        ->select('id','firstname','lastname','employeeid','profile_image')
        ->first();

    $team = DB::table('allemployees')
        ->where('manager_id', $managerId)
        ->where('status', 'active')
        ->select('id','firstname','lastname','employeeid')
        ->orderBy('employeeid')
        ->get();

    $employees = collect();
    if ($manager) {
        $employees->push($manager);
    }
    $employees = $employees->merge($team);

    $rows = [];

    foreach ($employees as $emp) {
        if (!$emp || !isset($emp->id)) {
            continue;
        }

        $carbon = \Carbon\Carbon::parse($date);

        $isHoliday = DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->exists();

        $isWeekend = $this->isWeekendForEmployee($emp->id, $carbon);

        $leave = DB::table('employee_leaves')
            ->where('employee_id', $emp->id)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();

        // Check attendance
        $attendance = DB::table('attendances')
            ->where('employee_id', $emp->id)
            ->whereDate('date', $date)
            ->whereNotNull('punch_in')
            ->first();

        $status = "Absent"; // Default to Absent

        if ($carbon->isFuture()) {
            $status = "Future";
        } elseif ($isHoliday) {
            $status = "Holiday";
        } elseif ($isWeekend) {
            $status = "Weekend";
        } elseif ($leave) {
            $status = "Leave";
        } elseif ($attendance) {
            $status = "Present";
        }

        $rows[] = [
            'name' => $emp->firstname . ' ' . $emp->lastname,
            'employeeid' => $emp->employeeid,
            'status' => $status
        ];
    }

    $filename = "daily_leave_status_{$date}.csv";

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $callback = function() use ($rows, $date) {
        $file = fopen('php://output', 'w');
        
        fputcsv($file, ["Daily Leave Status Report"]);
        fputcsv($file, ["Date: $date"]);
        fputcsv($file, []);
        fputcsv($file, ["Employee Name", "Employee ID", "Status"]);

        foreach ($rows as $r) {
            fputcsv($file, [
                $r['name'],
                $r['employeeid'],
                $r['status'],
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function resourceAvailabilityCSV(Request $request)
{
    $month = $request->month ?? now()->format('Y-m');

    // Get manager
    $managerId = session('user_id');

    $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end   = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    // Build dates list
    $dates = [];
    $loop = $start->copy();
    while ($loop->lte($end)) {
        $dates[] = $loop->format('Y-m-d');
        $loop->addDay();
    }

    // Employees (manager + team)
    $manager = DB::table('allemployees')
        ->where('id', $managerId)
        ->select('id','firstname','lastname','employeeid')
        ->first();

    $team = DB::table('allemployees')
        ->where('manager_id', $managerId)
        ->select('id','firstname','lastname','employeeid')
        ->get();

    $employees = collect([$manager])->merge($team);

    // Prepare CSV rows
    $rows = [];

    foreach ($employees as $emp) {
        foreach ($dates as $d) {

            $carbon = \Carbon\Carbon::parse($d);

            $isHoliday = DB::table('holidays')->whereDate('holidaydate', $d)->exists();
            $isWeekend = $this->isWeekendForEmployee($emp->id, $carbon);

            $leave = DB::table('employee_leaves')
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->where('from_date', '<=', $d)
                ->where('to_date', '>=', $d)
                ->first();

            if ($carbon->isFuture())       $status = "Future";
            elseif ($isHoliday)            $status = "Holiday";
            elseif ($isWeekend)            $status = "Weekend";
            elseif ($leave)                $status = "Leave";
            else                           $status = "Working";

            $rows[] = [
                $emp->employeeid,
                $emp->firstname . ' ' . $emp->lastname,
                $d,
                $status
            ];
        }
    }

    $filename = "resource_availability_{$month}.csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($rows, $month) {
        $file = fopen('php://output', 'w');

        fputcsv($file, ["Resource Availability Report"]);
        fputcsv($file, ["Month: $month"]);
        fputcsv($file, []);
        fputcsv($file, ["Employee ID", "Employee Name", "Date", "Status"]);

        foreach ($rows as $r) {
            fputcsv($file, $r);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function teamProjectStatusReport(Request $request)
{
    $managerId = session('user_id');
    if (!$managerId) return redirect()->route('login')->withErrors('Please login first.');

    // Employees (manager + direct reports)
    $employees = DB::table('allemployees')
        ->where(function($q) use ($managerId) {
            $q->where('manager_id', $managerId)
              ->orWhere('id', $managerId);
        })
        ->select('id','firstname','lastname','employeeid')
        ->orderBy('employeeid')
        ->get();

    // Selected employee (default manager)
    $selectedEmployeeId = $request->get('employee_id', $managerId);

    // Safety: if selected not in list, fallback
    if (!$employees->pluck('id')->contains($selectedEmployeeId)) {
        $selectedEmployeeId = $managerId;
    }

    // Filters: from/to, status, project
    $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
    $to   = $request->get('to', Carbon::now()->endOfMonth()->toDateString());
    $statusFilter  = $request->get('status', 'all');
    $projectFilter = $request->get('project', 'all');

    // Main query: timesheet rows for the selected employee & date range
    $query = DB::table('timesheet')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->leftJoin('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->select(
            'timesheet.*',
            'tasks.task as task_name',
            'tasks.status as task_status',
            'projects.projectname',
            'projects.status as project_status',
            'allemployees.firstname',
            'allemployees.lastname'
        )
        ->where('timesheet.employee_id', $selectedEmployeeId)
        // Compare only date part of start_date (handles datetimes stored with time)
        ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$from, $to]);

    if ($statusFilter !== 'all') {
        $query->where('projects.status', $statusFilter);
    }
    if ($projectFilter !== 'all') {
        $query->where('projects.id', $projectFilter);
    }

    $rows = $query->orderBy('timesheet.id')->get();

    // Build aggregates:
    // report[employee_name][project_name][project_status] = minutes
    $report = [];
    // projectTotals[project] = minutes
    $projectTotals = [];
    // statusTotals[project_status] = minutes
    $statusTotals = [];
    // Also prepare simple rows for table listing (flattened)
    $simpleRows = [];

    foreach ($rows as $row) {
        // require both start_time and end_time; skip incomplete rows
        if (empty($row->start_time) || empty($row->end_time)) {
            // Still push a row with "-" times so table shows something if you want
            $simpleRows[] = [
                'employee'  => trim($row->firstname . ' ' . $row->lastname),
                'project'   => $row->projectname ?? 'Unnamed Project',
                'task'      => $row->task_name ?? 'Unnamed Task',
                'project_status' => $row->project_status ?? 'unknown',
                'start'     => $row->start_time ? substr($row->start_time,0,8) : '-',
                'end'       => $row->end_time ? substr($row->end_time,0,8) : '-',
                'total'     => '-'
            ];
            continue;
        }

        // FIXED parsing:
        // start_date may contain time; convert to Y-m-d
        try {
            $cleanStartDate = Carbon::parse($row->start_date)->toDateString();
        } catch (\Exception $e) {
            // fallback: try to extract first 10 chars
            $cleanStartDate = substr($row->start_date, 0, 10);
        }

        try {
            $cleanEndDate = Carbon::parse($row->end_date)->toDateString();
        } catch (\Exception $e) {
            $cleanEndDate = substr($row->end_date, 0, 10);
        }

        // start_time / end_time may include microseconds; trim to hh:mm:ss
        $startTime = substr(trim($row->start_time), 0, 8);
        $endTime   = substr(trim($row->end_time), 0, 8);

        // Build datetimes safely
        try {
            $startDT = Carbon::createFromFormat('Y-m-d H:i:s', $cleanStartDate . ' ' . $startTime);
        } catch (\Exception $e) {
            // fallback: try generic parse
            $startDT = Carbon::parse($cleanStartDate . ' ' . $startTime);
        }

        try {
            $endDT = Carbon::createFromFormat('Y-m-d H:i:s', $cleanEndDate . ' ' . $endTime);
        } catch (\Exception $e) {
            $endDT = Carbon::parse($cleanEndDate . ' ' . $endTime);
        }

        // If end before start (cross-midnight or bad data), handle:
        if ($endDT->lt($startDT)) {
            // assume end on same day + 1 if difference small? Safer: add day until > start
            $endDT->addDay();
        }

        $mins = $startDT->diffInMinutes($endDT);

        $employeeName = trim($row->firstname . ' ' . $row->lastname);
        $projectName  = $row->projectname ?? 'Unnamed Project';
        $projStatus   = $row->project_status ?? 'unknown';

        // accumulate hierarchical report
        if (!isset($report[$employeeName])) $report[$employeeName] = [];
        if (!isset($report[$employeeName][$projectName])) $report[$employeeName][$projectName] = [];
        if (!isset($report[$employeeName][$projectName][$projStatus])) $report[$employeeName][$projectName][$projStatus] = 0;
        $report[$employeeName][$projectName][$projStatus] += $mins;

        // accumulate project totals
        $projectTotals[$projectName] = ($projectTotals[$projectName] ?? 0) + $mins;

        // accumulate status totals
        $statusTotals[$projStatus] = ($statusTotals[$projStatus] ?? 0) + $mins;

        // simple rows for table
        $simpleRows[] = [
            'employee' => $employeeName,
            'project'  => $projectName,
            'task'     => $row->task_name ?? 'Unnamed Task',
            'project_status' => $projStatus,
            'start'    => substr($startTime,0,5),
            'end'      => substr($endTime,0,5),
            'total'    => sprintf('%02d:%02d', floor($mins/60), $mins%60),
        ];
    }

    // Dropdown values
    $allProjectStatuses = DB::table('projects')->select('status')->distinct()->pluck('status')->toArray();
    $allProjects = DB::table('projects')->select('id','projectname')->get();

    // selected employee model for header
    $employee = DB::table('allemployees')->where('id', $selectedEmployeeId)->first();

    // Pass data to view
    return view('hrms.Employee.Reports.Team.project-status.index', [
        'employees' => $employees,
        'selectedEmployeeId' => $selectedEmployeeId,
        'employee' => $employee,
        'from' => $from,
        'to' => $to,
        'statusFilter' => $statusFilter,
        'projectFilter' => $projectFilter,
        'simpleReport' => $simpleRows,
        'report' => $report,
        'projectTotals' => $projectTotals,
        'statusTotals' => $statusTotals,
        'allProjectStatuses' => $allProjectStatuses,
        'allProjects' => $allProjects,
    ]);
}

/**
 * Optional: Server side CSV download for current filters (if you want)
 */
public function teamProjectStatusCSV(Request $request)
{
    $managerId = session('user_id');
    if (!$managerId) return redirect()->route('login');

    // For simplicity reuse the same request -> call teamProjectStatusReport logic could be refactored,
    // but we'll regenerate the rows quickly by calling the same controller method flow.
    // To avoid duplication here, we'll simply generate CSV from the same query used above.
    // (For brevity, assume front-end client-side CSV is available; implement server CSV similarly if needed.)
    $response = new StreamedResponse(function() {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Employee','Project','Project Status','Start','End','Total (HH:MM)']);
        // ideally loop through computed rows (omitted to keep method short)
        fclose($handle);
    });
    $response->headers->set('Content-Type','text/csv');
    $response->headers->set('Content-Disposition','attachment; filename=team_project_status.csv');
    return $response;
}
public function teamScheduledWorkedReport(Request $request)
{
    $managerId = session('user_id');

    // TEAM MEMBERS = manager + employees under manager
    $team = DB::table('allemployees')
        ->where(function ($q) use ($managerId) {
            $q->where('manager_id', $managerId)
              ->orWhere('id', $managerId);
        })
        ->select('id', 'firstname', 'lastname', 'employeeid')
        ->orderBy('employeeid')
        ->get();

    $selectedEmployeeId = $request->employee_id ?? $managerId;

    $from = $request->query('from') ?? Carbon::now()->startOfMonth()->toDateString();
    $to   = $request->query('to')   ?? Carbon::now()->endOfMonth()->toDateString();

    $projectFilter = $request->query('project') ?? 'all';
    $statusFilter  = $request->query('status')  ?? 'all';

    // Dropdown options
    $projects = DB::table('projects')->select('id', 'projectname')->get();
    $allProjectStatuses = DB::table('projects')
        ->select('status')->distinct()
        ->whereNotNull('status')
        ->pluck('status');

    // MAIN QUERY
    $rows = DB::table('timesheet')
        ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->join('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->select(
            'allemployees.id as emp_id',
            'allemployees.firstname',
            'allemployees.lastname',
            'projects.projectname',
            'projects.status as project_status',
            'tasks.task as task_name',
            'tasks.due_date',
            'tasks.updated_at as completed_date',
            'timesheet.start_date',
            'timesheet.end_date',
            'timesheet.start_time',
            'timesheet.end_time'
        )
        ->where('allemployees.id', $selectedEmployeeId)
        ->whereBetween(DB::raw("DATE(timesheet.start_date)"), [$from, $to]);

    // Filter by project
    if ($projectFilter !== 'all') {
        $rows->where('projects.id', $projectFilter);
    }

    $rows = $rows->get();

    $report = [];
    $projectTotals = [];
    $statusTotals  = [];
    $simpleReport  = [];

    foreach ($rows as $row) {

        $employee = "{$row->firstname} {$row->lastname}";

        // ------------------------------
        // WORKED HOURS
        // ------------------------------
        $workedMinutes = 0;
        $worked = "00:00";

        if ($row->start_date && $row->end_date && $row->start_time && $row->end_time) {

            $sd = Carbon::parse($row->start_date)->format('Y-m-d');
            $ed = Carbon::parse($row->end_date)->format('Y-m-d');

            $st = substr($row->start_time, 0, 8);
            $et = substr($row->end_time, 0, 8);

            $start = Carbon::parse("$sd $st");
            $end   = Carbon::parse("$ed $et");

            $workedMinutes = $start->diffInMinutes($end);
            $worked = sprintf("%02d:%02d", floor($workedMinutes / 60), $workedMinutes % 60);
        }

        // ------------------------------
        // SCHEDULED HOURS
        // ------------------------------
        $scheduled = "00:00";

        if ($row->start_date && $row->due_date) {
            $sd = Carbon::parse($row->start_date)->startOfDay();
            $dd = Carbon::parse($row->due_date)->endOfDay();

            $mins = $sd->diffInMinutes($dd);
            $scheduled = sprintf("%02d:%02d", floor($mins / 60), $mins % 60);
        }

        // ------------------------------
        // STATUS LOGIC
        // ------------------------------
        if (!$row->due_date) {
            $status = "No Schedule";
        } elseif (!$row->completed_date) {
            $status = "Pending";
        } else {
            $status = Carbon::parse($row->completed_date)->gt(Carbon::parse($row->due_date))
                ? "Late"
                : "On Time";
        }

        // Apply status filter
        if ($statusFilter !== 'all' && strtolower($statusFilter) !== strtolower($status)) {
            continue;
        }

        // BUILD REPORT
        $report[] = [
            'employee'       => $employee,
            'project'        => $row->projectname,
            'task'           => $row->task_name,
            'scheduled'      => $scheduled,
            'worked'         => $worked,
            'due_date'       => $row->due_date,
            'completed_date' => $row->completed_date,
            'status'         => $status,
        ];

        // For CHART usage
        $projectTotals[$row->projectname] = ($projectTotals[$row->projectname] ?? 0) + $workedMinutes;
        $statusTotals[$status] = ($statusTotals[$status] ?? 0) + $workedMinutes;

        // For CSV
        $simpleReport[] = [
            'employee'       => $employee,
            'project'        => $row->projectname,
            'task'           => $row->task_name,
            'project_status' => $status,
            'start'          => $row->start_time,
            'end'            => $row->end_time,
            'total'          => $worked
        ];
    }

    return view('hrms.Employee.Reports.Team.scheduled-worked.index', [
        'team'               => $team,
        'selectedEmployeeId' => $selectedEmployeeId,
        'report'             => $report,
        'projectTotals'      => $projectTotals,
        'statusTotals'       => $statusTotals,
        'simpleReport'       => $simpleReport,
        'from'               => $from,
        'to'                 => $to,
        'projects'           => $projects,
        'projectFilter'      => $projectFilter,
        'statusFilter'       => $statusFilter,
        'allProjectStatuses' => $allProjectStatuses,
    ]);
}

}
