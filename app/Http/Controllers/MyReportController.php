<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
class MyReportController extends Controller
{
    public function reportsIndex()
{
    return view('hrms.Employee.Reports.index');
}
public function organizationReports()
{
    return view('hrms.Employee.Reports.Organization.index');
}

    public function careerHistory()
{
    $employeeId = Session::get('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Session expired! Please login again.');
    }

    // Fetch employee with designation, department, branch names
    $employee = DB::table('allemployees')
        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->select(
            'allemployees.*',
            'designation.designation as designation_name',
            'department.department as department_name',
            'branches.name as branch_name'
        )
        ->where('allemployees.id', $employeeId)
        ->first();

    if (!$employee) {
        return back()->with('error', 'Employee not found');
    }

    $events = [];

    // 1) JOINING EVENT
    if (!empty($employee->joiningdate)) {
        $events[] = [
            'date' => $employee->joiningdate,
            'title' => 'Joined the Company',
            'description' => "Joined as <b>{$employee->designation_name}</b> in <b>{$employee->department_name}</b> department.",
            'type' => 'join'
        ];
    }

    // 2) PROMOTION TIMELINE
    $promotions = DB::table('promotions')
        ->join('designation as old', 'promotions.promotion_from', '=', 'old.id')
        ->join('designation as new', 'promotions.promotion_to', '=', 'new.id')
        ->leftJoin('department', 'promotions.department_id', '=', 'department.id')
        ->where('promotions.employee_id', $employee->employeeid)
        ->orderBy('promotions.promotion_date', 'DESC')
        ->select(
            'promotions.promotion_date',
            'old.designation as old_role',
            'new.designation as new_role',
            'department.department as dept_name'
        )
        ->get();

    foreach ($promotions as $p) {
        $events[] = [
            'date' => $p->promotion_date,
            'title' => "Promoted to {$p->new_role}",
            'description' => "Role changed from <b>{$p->old_role}</b> to <b>{$p->new_role}</b> in <b>{$p->dept_name}</b> department.",
            'type' => 'promotion'
        ];
    }

    // Sort all events by date
    usort($events, function ($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
    });

    return view('hrms.Employee.Reports.CareerHistory.index', compact('employee', 'events'));
}

  
  public function leaveBalance()
{
    $employeeId = session('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // Employee details
    $employee = DB::table('allemployees')->where('id', $employeeId)->first();

    // TOTAL leave settings
    $annual = DB::table('annual_leaves')->first();
    $medical = DB::table('medical_leaves')->first();
    $lop = DB::table('lop_leaves')->first();

    // Used leaves from employee_leave_balances
    $used = DB::table('employee_leave_balances')
        ->where('employee_id', $employeeId)
        ->pluck('used_days', 'leave_type')
        ->toArray();

    // Build final leave summary
    $summary = [
        [
            "type" => "Casual Leave",
            "total" => $annual->days ?? 0,
            "used" => $used['Casual Leave'] ?? 0,
            "remaining" => ($annual->days ?? 0) - ($used['Casual Leave'] ?? 0),
        ],
        [
            "type" => "Sick",
            "total" => $medical->sick ?? 0,
            "used" => $used['Sick'] ?? 0,
            "remaining" => ($medical->sick ?? 0) - ($used['Sick'] ?? 0),
        ],
        [
            "type" => "Hospitalisation",
            "total" => $medical->hospitalisation ?? 0,
            "used" => $used['Hospitalisation'] ?? 0,
            "remaining" => ($medical->hospitalisation ?? 0) - ($used['Hospitalisation'] ?? 0),
        ],
        [
            "type" => "Maternity Leave",
            "total" => $medical->maternity ?? 0,
            "used" => $used['Maternity Leave'] ?? 0,
            "remaining" => ($medical->maternity ?? 0) - ($used['Maternity Leave'] ?? 0),
        ],
        [
            "type" => "Paternity Leave",
            "total" => $medical->paternity ?? 0,
            "used" => $used['Paternity Leave'] ?? 0,
            "remaining" => ($medical->paternity ?? 0) - ($used['Paternity Leave'] ?? 0),
        ],
        [
            "type" => "LOP",
            "total" => $lop->days ?? 0,
            "used" => $used['LOP'] ?? 0,
            "remaining" => ($lop->days ?? 0) - ($used['LOP'] ?? 0),
        ],
    ];

    return view('hrms.Employee.Reports.LeaveBalance.index', compact('employee', 'summary'));
}
public function leaveBalanceHistory($leaveType)
{
    $employeeId = session('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // Fetch employee
    $employee = DB::table('allemployees')->where('id', $employeeId)->first();

    // Fetch allocated from settings tables
    $allocated = 0;

    if ($leaveType == 'Casual Leave') {
        $allocated = DB::table('annual_leaves')->value('days');
    }
    elseif ($leaveType == 'Sick') {
        $allocated = DB::table('medical_leaves')->value('sick');
    }
    elseif ($leaveType == 'Hospitalisation') {
        $allocated = DB::table('medical_leaves')->value('hospitalisation');
    }
    elseif ($leaveType == 'Maternity Leave') {
        $allocated = DB::table('medical_leaves')->value('maternity');
    }
    elseif ($leaveType == 'Paternity Leave') {
        $allocated = DB::table('medical_leaves')->value('paternity');
    }
    elseif ($leaveType == 'LOP') {
        $allocated = DB::table('lop_leaves')->value('days');
    }

    // Fetch USED leaves (booked)
    $booked = DB::table('employee_leaves')
        ->where('employee_id', $employeeId)
        ->where('leave_type', $leaveType)
        ->where('status', 'approved')
        ->sum('no_of_days');

    $remaining = $allocated - $booked;

    // History (only approved leaves)
    $history = DB::table('employee_leaves')
        ->where('employee_id', $employeeId)
        ->where('leave_type', $leaveType)
        ->orderBy('from_date', 'desc')
        ->get();

    return view(
        'hrms.Employee.Reports.LeaveBalance.history-modal',
        compact('employee', 'leaveType', 'allocated', 'booked', 'remaining', 'history')
    );
}
public function earlyLateReport(Request $request)
{
    $employeeId = session('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // DATE RANGE DEFAULT (THIS WEEK)
    $startDate = $request->start_date ?? now()->startOfWeek()->format('Y-m-d');
    $endDate   = $request->end_date ?? now()->endOfWeek()->format('Y-m-d');

    // EMPLOYEE DETAILS
    $employee = DB::table('allemployees')->where('id', $employeeId)->first();

    // FETCH ATTENDANCE WITH SHIFT DETAILS
    $records = DB::table('attendances')
        ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
        ->where('attendances.employee_id', $employeeId)
        ->whereBetween('attendances.date', [$startDate, $endDate])
        ->orderBy('attendances.date', 'asc')
        ->select(
            'attendances.*',
            'shifts.shift_name',
            'shifts.start_time as shift_start',
            'shifts.end_time as shift_end'
        )
        ->get();

    // PROCESS REPORT ROWS
    $report = [];

    foreach ($records as $r) {

        $firstIn = $r->punch_in ? Carbon::parse($r->punch_in) : null;
        $lastOut = $r->punch_out ? Carbon::parse($r->punch_out) : null;

        $shiftStart = $r->shift_start ? Carbon::parse($r->shift_start) : null;
        $shiftEnd   = $r->shift_end ? Carbon::parse($r->shift_end) : null;

        // EARLY / LATE ENTRY
        $entryEarly = '-';
        $entryLate = '-';
        if ($firstIn && $shiftStart) {
            if ($firstIn->lt($shiftStart)) {
                $entryEarly = $shiftStart->diff($firstIn)->format('%H:%I');
            } elseif ($firstIn->gt($shiftStart)) {
                $entryLate = $firstIn->diff($shiftStart)->format('%H:%I');
            }
        }

        // EARLY / LATE EXIT
        $exitEarly = '-';
        $exitLate = '-';
        if ($lastOut && $shiftEnd) {
            if ($lastOut->lt($shiftEnd)) {
                $exitEarly = $shiftEnd->diff($lastOut)->format('%H:%I');
            } else {
                $exitLate = $lastOut->diff($shiftEnd)->format('%H:%I');
            }
        }

        // TOTAL HOURS
        $totalHours = '-';
        if ($firstIn && $lastOut) {
            $totalHours = $firstIn->diff($lastOut)->format('%H:%I');
        }

        // NET HOURS = (exit late - entry late)
        $netHours = '-';
        if ($totalHours !== '-') {
            $netHours = $totalHours;
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
            'net_hours' => $netHours,
            'shift' => $r->shift_name ?? '-'
        ];
    }

    return view('hrms.Employee.Reports.Attendance.early-late', compact(
        'employee', 'report', 'startDate', 'endDate'
    ));
}
 // show present/absent grid with filter
    public function presentAbsentStatus(Request $request)
    {
        $employeeId = Session::get('user_id');
        if (!$employeeId) return redirect()->route('login')->withErrors('Please login.');

        $employee = DB::table('allemployees')->where('id', $employeeId)->first();

        // Filter type: monthly, weekly, custom
        $filter = $request->get('filter', 'monthly');

        // Determine date range based on filter
        if ($filter === 'monthly') {
            $month = $request->get('month', Carbon::now()->month);
            $year  = $request->get('year', Carbon::now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate   = $startDate->copy()->endOfMonth();
        } elseif ($filter === 'weekly') {
            // weekly expects a week_start parameter (Y-m-d) OR compute current week Mon-Sun
            if ($request->has('week_start')) {
                $startDate = Carbon::parse($request->get('week_start'))->startOfDay();
            } else {
                // default to current week's Monday
                $startDate = Carbon::now()->startOfWeek(); // Monday
            }
            $endDate = $startDate->copy()->endOfWeek(); // Sunday
        } else { // custom
            $startDate = $request->has('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
            $endDate   = $request->has('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();
            // safety: max range 365 days
            if ($endDate->diffInDays($startDate) > 365) {
                $endDate = $startDate->copy()->addDays(365);
            }
        }

        // Build date labels for header
        $dateLabels = [];
        $period = new \DatePeriod(
            $startDate->toDateTimeString() ? new \DateTime($startDate->format('Y-m-d')) : new \DateTime(),
            new \DateInterval('P1D'),
            (new \DateTime($endDate->format('Y-m-d')))->modify('+1 day')
        );
        foreach ($period as $d) {
            $c = Carbon::instance($d);
            $dateLabels[] = [
                'date' => $c->format('d-M'),
                'full' => $c->format('Y-m-d'),
                'day'  => $c->format('D'),
                'day_full' => $c->format('l'),
            ];
        }

        // Build day status map: for each date, compute P/A/W/H/-
        $status = [];
        foreach ($dateLabels as $d) {
            $dateStr = $d['full'];
            $carbonDay = Carbon::parse($dateStr);

            // If future date (strictly after today) show '-'
            if ($carbonDay->isAfter(Carbon::now()->startOfDay())) {
                // But still mark if weekend or holiday (user asked future weekend/holiday must display)
                $isWeekend = $this->isWeekendForEmployee($employeeId, $carbonDay);
                $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
                if ($isHoliday) {
                    $status[$dateStr] = 'H';
                } elseif ($isWeekend) {
                    $status[$dateStr] = 'W';
                } else {
                    $status[$dateStr] = '-';
                }
                continue;
            }

            // Past or today => check holiday first
            $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
            if ($isHoliday) {
                $status[$dateStr] = 'H';
                continue;
            }

            // Weekend depends on the employee shift (days_of_week in shifts)
            $isWeekend = $this->isWeekendForEmployee($employeeId, $carbonDay);
            if ($isWeekend) {
                $status[$dateStr] = 'W';
                continue;
            }

            // Check attendance record (use attendances table)
            $att = DB::table('attendances')
                ->where('employee_id', $employeeId)
                ->whereDate('date', $dateStr)
                ->whereNotNull('punch_in')
                ->first();

            if ($att) {
                $status[$dateStr] = 'P';
            } else {
                $status[$dateStr] = 'A';
            }
        }

        // prepare month navigation variables
        $prevMonthStart = (clone $startDate)->subMonthNoOverflow();
        $nextMonthStart = (clone $startDate)->addMonthNoOverflow();

        return view('hrms.Employee.Reports.PresentAbsent.index', [
            'employee'   => $employee,
            'dateLabels' => $dateLabels,
            'status'     => $status,
            'filter'     => $filter,
            'startDate'  => $startDate->format('Y-m-d'),
            'endDate'    => $endDate->format('Y-m-d'),
            'prevMonth'  => $prevMonthStart->format('m'),
            'prevYear'   => $prevMonthStart->format('Y'),
            'nextMonth'  => $nextMonthStart->format('m'),
            'nextYear'   => $nextMonthStart->format('Y'),
            'monthName'  => $startDate->format('F'),
            'year'       => $startDate->format('Y'),
        ]);
    }

    // helper: determine if date is weekend for employee's shift
    private function isWeekendForEmployee($employeeId, Carbon $date)
    {
        // Fetch the current schedule (similar logic you already have)
        $schedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.schedule_start_date', '<=', $date->format('Y-m-d'))
            ->where(function($query) use ($date) {
                $query->where('schedule.repeat_every_week', '>', 0)
                      ->orWhere(function($q) use ($date) {
                          $q->where('schedule.repeat_every_week', 0)
                            ->where('schedule.schedule_end_date', '>=', $date->format('Y-m-d'));
                      });
            })
            ->select('shifts.days_of_week')
            ->orderBy('schedule.repeat_every_week', 'desc')
            ->orderBy('schedule.schedule_start_date', 'desc')
            ->first();

        if (!$schedule || empty($schedule->days_of_week)) {
            // if no schedule found, assume standard weekend Sat/Sun
            return in_array($date->format('l'), ['Saturday', 'Sunday']);
        }

        $workingDays = array_map('trim', explode(',', $schedule->days_of_week));
        // if day is not in workingDays => weekend
        foreach ($workingDays as $wd) {
            if (strtolower($wd) === strtolower($date->format('l')) ||
                strtolower($wd) === strtolower($date->format('D')) ||
                (is_numeric($wd) && intval($wd) == $date->format('N'))) {
                return false; // it's a working day
            }
        }
        return true; // not found => weekend
    }

    // CSV download for the current filter/date range
    public function presentAbsentDownload(Request $request)
    {
        // reuse logic to create date range & status map. We'll call presentAbsentStatus helper style by copying minimal required logic.
        $employeeId = Session::get('user_id');
        if (!$employeeId) return redirect()->route('login')->withErrors('Please login.');

        // Duplicate same date range logic as above (monthly/weekly/custom)
        $filter = $request->get('filter', 'monthly');

        if ($filter === 'monthly') {
            $month = $request->get('month', Carbon::now()->month);
            $year  = $request->get('year', Carbon::now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate   = $startDate->copy()->endOfMonth();
        } elseif ($filter === 'weekly') {
            $startDate = $request->has('week_start') ? Carbon::parse($request->get('week_start'))->startOfDay() : Carbon::now()->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
        } else {
            $startDate = $request->has('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
            $endDate   = $request->has('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfMonth();
        }

        // Build date list
        $dates = [];
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        // Build status map as earlier
        $statusMap = [];
        foreach ($dates as $dateStr) {
            $c = Carbon::parse($dateStr);
            if ($c->isAfter(Carbon::now()->startOfDay())) {
                $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
                $isWeekend = $this->isWeekendForEmployee($employeeId, $c);
                if ($isHoliday) $statusMap[$dateStr] = 'H';
                elseif ($isWeekend) $statusMap[$dateStr] = 'W';
                else $statusMap[$dateStr] = '-';
                continue;
            }
            if (DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists()) {
                $statusMap[$dateStr] = 'H'; continue;
            }
            if ($this->isWeekendForEmployee($employeeId, $c)) {
                $statusMap[$dateStr] = 'W'; continue;
            }
            $att = DB::table('attendances')->where('employee_id', $employeeId)->whereDate('date', $dateStr)->whereNotNull('punch_in')->first();
            $statusMap[$dateStr] = $att ? 'P' : 'A';
        }

        // Stream CSV
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        $filename = "present_absent_{$employee->employeeid}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}.csv";

        $response = new StreamedResponse(function() use ($statusMap, $employee, $dates) {
            $handle = fopen('php://output', 'w');
            // header
            fputcsv($handle, ["Employee ID", "Name", "Date", "Day", "Status"]);

            foreach ($dates as $dateStr) {
                $d = Carbon::parse($dateStr);
                $status = $statusMap[$dateStr] ?? '-';
                fputcsv($handle, [
                    $employee->employeeid,
                    trim($employee->firstname . ' ' . $employee->lastname),
                    $d->format('Y-m-d'),
                    $d->format('l'),
                    $status
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

        return $response;
    }
  

public function presenceHours(Request $request)
{
    $employeeId = session('user_id');
    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    $employee = DB::table('allemployees')->where('id', $employeeId)->first();
    $filter = $request->filter ?? 'last7';

    // Set date range based on filter
    switch($filter) {
        case 'this_week':
            $startDate = Carbon::now()->startOfWeek(); // Monday
            $endDate = Carbon::now()->endOfWeek();     // Sunday
            break;
        case 'last_week':
            $startDate = Carbon::now()->subWeek()->startOfWeek();
            $endDate = Carbon::now()->subWeek()->endOfWeek();
            break;
        case 'this_month':
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            break;
        case 'last7':
        default:
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            break;
    }

    /* ===================================================
       BUILD DATE RANGE AND FETCH ATTENDANCE PER DAY
       =================================================== */

    $rows = [];
    $totalMinutes = $totalPayableMinutes = $presentMinutes = $holidayMinutes = $weekendMinutes = 0;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

        $dateStr = $date->format('Y-m-d');

        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $dateStr)
            ->first();

        $isHoliday = DB::table('holidays')->whereDate('holidaydate', $dateStr)->exists();
        $isWeekend = $date->isWeekend();
        $future = $date->gt(Carbon::today());

        /* DEFAULT VALUES */
        $firstIn = $lastOut = "-";
        $totalHours = "00:00";
        $payableHours = "00:00";
        $status = "-";
        $shiftName = "General";

        if ($attendance) {

            /* Punch In/Out */
            $firstIn = $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('h:i A') : "-";
            $lastOut = $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('h:i A') : "-";

            /* Working Minutes */
            $minutes = $attendance->actual_working_minutes ?? ($attendance->working_hours * 60);
            $payableMinutes = $attendance->scheduled_hours ? $attendance->scheduled_hours * 60 : $minutes;

            $totalHours   = $this->minutesToTime($minutes);
            $payableHours = $this->minutesToTime($payableMinutes);

            $status = "Present";
            $totalMinutes += $minutes;
            $totalPayableMinutes += $payableMinutes;
            $presentMinutes += $minutes;

            /* Shift Name */
            if ($attendance->shift_id) {
                $shift = DB::table('shifts')->where('id', $attendance->shift_id)->first();
                if ($shift) $shiftName = $shift->shift_name;
            }

        } else {

            if ($isHoliday) {
                $status = "Holiday";
                $holidayMinutes += (8 * 60); // Fixed 8 hours for holidays
                $payableHours = "08:00";

            } elseif ($isWeekend) {
                $status = "Weekend";
                $weekendMinutes += (8 * 60); // Fixed weekend payable
                $payableHours = "08:00";

            } elseif ($future) {
                $status = "-";

            } else {
                $status = "Absent";
            }
        }

        $rows[] = [
            'date' => $date->format('D, d-M-Y'),
            'date_raw' => $dateStr,
            'first_in' => $firstIn,
            'last_out' => $lastOut,
            'total_hours' => $totalHours,
            'payable_hours' => $payableHours,
            'status' => $status,
            'shift' => $shiftName,
        ];
    }

    /* TOTALS */
    $totalHours = $this->minutesToTime($totalMinutes);
    $totalPayable = $this->minutesToTime($totalPayableMinutes);
    $presentHours = $this->minutesToTime($presentMinutes);
    $holidayHours = $this->minutesToTime($holidayMinutes);
    $weekendHours = $this->minutesToTime($weekendMinutes);

    // Store rows in session for CSV/PDF export
    session(['presence_rows' => $rows]);

    return view('hrms.Employee.Reports.PresenceHours.index', compact(
        'employee', 'rows', 'filter', 'startDate', 'endDate',
        'totalHours', 'totalPayable', 'presentHours',
        'holidayHours', 'weekendHours'
    ));
}


/* Helper function */
private function minutesToTime($mins)
{
    $h = floor($mins / 60);
    $m = $mins % 60;
    return sprintf("%02d:%02d", $h, $m);
}
public function presenceHoursDownload(Request $request)
{
    $employeeId = session('user_id');
    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    $employee = DB::table('allemployees')->where('id', $employeeId)->first();
    $endDate = Carbon::now()->endOfDay();
    $startDate = Carbon::now()->subDays(6)->startOfDay();
    
    $filename = "presence_hours_" . $employee->employeeid . "_" . $startDate->format('Ymd') . "_to_" . $endDate->format('Ymd') . ".csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($employee, $startDate, $endDate) {
        $handle = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");
        
        // Header with employee info
        fputcsv($handle, ["Presence Hours Report - Last 7 Days"]);
        fputcsv($handle, ["Employee ID: " . $employee->employeeid]);
        fputcsv($handle, ["Name: " . trim($employee->firstname . ' ' . $employee->lastname)]);
        fputcsv($handle, ["Period: " . $startDate->format('d-M-Y') . " to " . $endDate->format('d-M-Y')]);
        fputcsv($handle, ["Generated: " . Carbon::now()->format('d-M-Y h:i A')]);
        fputcsv($handle, []); // Empty row
        
        // Column headers
        fputcsv($handle, [
            "Date",
            "Day",
            "First In",
            "Last Out",
            "Total Hours",
            "Payable Hours",
            "Status",
            "Shift"
        ]);

        // Get data rows from session or regenerate
        $rows = session('presence_rows', []);
        foreach ($rows as $r) {
            $date = Carbon::parse($r['date_raw']);
            fputcsv($handle, [
                $date->format('d-M-Y'),
                $date->format('l'),
                $r['first_in'],
                $r['last_out'],
                $r['total_hours'],
                $r['payable_hours'],
                $r['status'],
                $r['shift']
            ]);
        }

        // Summary section
        fputcsv($handle, []);
        fputcsv($handle, ["SUMMARY"]);
        fputcsv($handle, ["Total Working Hours", "", "", "", "", "", "", ""]);
        fputcsv($handle, ["Payable Hours", "", "", "", "", "", "", ""]);
        fputcsv($handle, ["Present Days", "", "", "", "", "", "", ""]);
        fputcsv($handle, ["Holidays", "", "", "", "", "", "", ""]);
        fputcsv($handle, ["Weekends", "", "", "", "", "", "", ""]);

        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}
public function presenceHoursPDF(Request $request)
{
    $employeeId = session('user_id');
    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    $employee = DB::table('allemployees')->where('id', $employeeId)->first();
    $rows = session('presence_rows', []);
    
    $endDate = Carbon::now()->endOfDay();
    $startDate = Carbon::now()->subDays(6)->startOfDay();
    
    // Calculate totals
    $totals = [
        'present' => 0,
        'holiday' => 0,
        'weekend' => 0,
        'absent' => 0
    ];
    
    foreach ($rows as $r) {
        switch ($r['status']) {
            case 'Present': $totals['present']++; break;
            case 'Holiday': $totals['holiday']++; break;
            case 'Weekend': $totals['weekend']++; break;
            case 'Absent': $totals['absent']++; break;
        }
    }

    $pdf = \PDF::loadView('hrms.Employee.Reports.PresenceHours.pdf', [
        'employee' => $employee,
        'rows' => $rows,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totals' => $totals,
        'generatedDate' => Carbon::now()->format('d-M-Y h:i A')
    ]);

    $filename = "presence_hours_" . $employee->employeeid . "_" . $startDate->format('Ymd') . "_to_" . $endDate->format('Ymd') . ".pdf";

    return $pdf->download($filename);
}
   public function weeklyReport(Request $request)
    {
        // 1. Resolve date range (defaults to current week)
        $startOfWeek = $request->query('from') ?? Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek   = $request->query('to')   ?? Carbon::now()->endOfWeek()->toDateString();

        $start = Carbon::parse($startOfWeek)->startOfDay();
        $end   = Carbon::parse($endOfWeek)->endOfDay();

        // Build list of dates (Y-m-d strings) for table header & chart labels
        $dates = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        // 2. Fetch timesheet rows for the date range with joins and explicit column aliasing
        // Use DATE(...) on start_date to filter by date portion in case the DB field is datetime.
        $rows = DB::table('timesheet')
            ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
            ->join('projects', 'projects.id', '=', 'timesheet.project_id')
            ->leftJoin('clients', 'clients.id', '=', 'projects.client')
            ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
            ->select(
                'timesheet.*',
                'timesheet.id as timesheet_row_id',
                'allemployees.firstname',
                'allemployees.lastname',
                'projects.id as project_db_id',
                'projects.projectname',
                'clients.first_name as clientname',
                'tasks.id as task_db_id',
                'tasks.task as task_name'
            )
            // Ensure we compare only the date portion
            ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$dates[0], $dates[count($dates)-1]])
            ->orderBy('timesheet.employee_id')
            ->orderBy('projects.id')
            ->orderBy('tasks.id')
            ->get();

        // 3. Build report structure
        // report[employeeId] => ['employee' => 'Name', 'projects' => [ projectId => [ 'project_name', 'client_name', 'tasks' => [ taskId => ['task_name','daily'=>[date=>HH:MM],'total'=>HH:MM] ] ] ] ]
        $report = [];

        foreach ($rows as $row) {
            $empId  = $row->employee_id;
            $projId = $row->project_db_id;
            $taskId = $row->task_db_id; // may be null if task missing

            // employee init
            if (!isset($report[$empId])) {
                $report[$empId] = [
                    'employee' => trim($row->firstname . ' ' . $row->lastname),
                    'projects' => []
                ];
            }

            // project init (use project id; fallback label if missing)
            $projKey = $projId ?? 'proj_unknown_' . ($row->timesheet_row_id ?? uniqid());
            if (!isset($report[$empId]['projects'][$projKey])) {
                $report[$empId]['projects'][$projKey] = [
                    'client_name'  => $row->clientname ?? 'N/A',
                    'project_name' => $row->projectname ?? 'Unnamed Project',
                    'tasks'        => []
                ];
            }

            // task init
            // If task_id is null, use unique key based on timesheet_row_id to avoid collisions
            $taskKey = $taskId ?? 'task_unknown_' . $row->timesheet_row_id;
            if (!isset($report[$empId]['projects'][$projKey]['tasks'][$taskKey])) {
                // prepare zeroed daily array
                $daily = [];
                foreach ($dates as $d) {
                    $daily[$d] = "00:00";
                }

                $report[$empId]['projects'][$projKey]['tasks'][$taskKey] = [
                    'task_name' => $row->task_name ?? 'Unnamed Task',
                    'daily'     => $daily,
                    'total'     => "00:00"
                ];
            }

            // 4. Calculate minutes for this timesheet row and accumulate into daily cell
            // Only if we have valid dates/times in the row
            if ($row->start_date && $row->end_date && $row->start_time !== null && $row->end_time !== null) {
                // Normalize start/end date/time safely
                try {
                    // Some DBs store start_date with time; Carbon handles it
                    $cleanStartDate = Carbon::parse($row->start_date)->toDateString();
                    $cleanStartTime = trim(substr($row->start_time, 0, 8));
                    $cleanEndDate   = Carbon::parse($row->end_date)->toDateString();
                    $cleanEndTime   = trim(substr($row->end_time, 0, 8));

                    $startDT = Carbon::parse($cleanStartDate . ' ' . $cleanStartTime);
                    $endDT   = Carbon::parse($cleanEndDate . ' ' . $cleanEndTime);

                    // If end is before start, skip (or you might want to handle cross-midnight)
                    $minutes = $startDT->diffInMinutes($endDT);

                    // Only accumulate if the date is within our requested date range header
                    if (in_array($cleanStartDate, $dates)) {
                        // read existing time (HH:MM) and convert to minutes
                        $existing = $report[$empId]['projects'][$projKey]['tasks'][$taskKey]['daily'][$cleanStartDate] ?? "00:00";
                        list($eh, $em) = explode(':', $existing);
                        $existingMinutes = ($eh * 60) + $em;

                        $totalMinutesForCell = $existingMinutes + $minutes;
                        $report[$empId]['projects'][$projKey]['tasks'][$taskKey]['daily'][$cleanStartDate] = sprintf('%02d:%02d', floor($totalMinutesForCell / 60), $totalMinutesForCell % 60);
                    }
                } catch (\Exception $ex) {
                    // ignore malformed rows but don't break report generation
                    continue;
                }
            }
        } // end foreach rows

        // 5. Calculate totals per task (sum their daily minutes)
        foreach ($report as $empId => &$emp) {
            foreach ($emp['projects'] as $projKey => &$proj) {
                foreach ($proj['tasks'] as $taskKey => &$task) {
                    $totalMinutes = 0;
                    foreach ($task['daily'] as $d => $timeStr) {
                        if ($timeStr === '00:00' || !$timeStr) continue;
                        list($h, $m) = explode(':', $timeStr);
                        $totalMinutes += ($h * 60) + $m;
                    }
                    $task['total'] = sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
                }
            }
        }
        unset($emp, $proj, $task); // break references

        // 6. Prepare chart aggregates:
        // A. dailyHours (total hours per day across all employees/projects)
        $dailyHours = [];
        foreach ($dates as $d) {
            $minutes = 0;
            foreach ($report as $emp) {
                foreach ($emp['projects'] as $proj) {
                    foreach ($proj['tasks'] as $task) {
                        $timeStr = $task['daily'][$d] ?? '00:00';
                        list($h, $m) = explode(':', $timeStr);
                        $minutes += ($h * 60) + $m;
                    }
                }
            }
            // round hours to 2 decimals for chart
            $dailyHours[] = round($minutes / 60, 2);
        }

        // B. project-wise totals (in hours)
        $projectMinutes = [];
        foreach ($report as $emp) {
            foreach ($emp['projects'] as $proj) {
                $label = $proj['project_name'] ?? 'Unnamed Project';
                if (!isset($projectMinutes[$label])) {
                    $projectMinutes[$label] = 0;
                }
                foreach ($proj['tasks'] as $task) {
                    list($h, $m) = explode(':', $task['total']);
                    $projectMinutes[$label] += ($h * 60) + $m;
                }
            }
        }
        $projectLabels = array_keys($projectMinutes);
        $projectHours  = array_map(fn($minutes) => round($minutes / 60, 2), array_values($projectMinutes));

        // 7. Return view
        return view('hrms.Employee.Reports.weekly-report.index', [
            'dates'         => $dates,
            'report'        => $report,
            'from'          => $startOfWeek,
            'to'            => $endOfWeek,
            'dailyHours'    => $dailyHours,
            'projectLabels' => $projectLabels,
            'projectHours'  => $projectHours,
        ]);
    }
  public function jobStatusReport(Request $request)
{
    $startOfWeek = $request->query('from') ?? Carbon::now()->startOfWeek()->toDateString();
    $endOfWeek   = $request->query('to')   ?? Carbon::now()->endOfWeek()->toDateString();

    $statusFilter = $request->query('status');
    $projectFilter = $request->query('project');

    $query = DB::table('timesheet')
        ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->join('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->select(
            'timesheet.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'projects.id as project_id',
            'projects.projectname',
            'tasks.task as task_name',
            'tasks.status as task_status'
        )
        ->whereBetween(DB::raw('DATE(timesheet.start_date)'), [$startOfWeek, $endOfWeek]);

    if ($statusFilter && $statusFilter !== 'all') {
        $query->where('tasks.status', $statusFilter);
    }

    if ($projectFilter && $projectFilter !== 'all') {
        $query->where('projects.id', $projectFilter);
    }

    $rows = $query->orderBy('timesheet.id')->get();

    $simpleReport = [];
    $dailyHours = [];
    $projectHours = [];

    foreach ($rows as $row) {
        if (!$row->start_time || !$row->end_time) continue;

        $cleanStartDate = Carbon::parse($row->start_date)->format('Y-m-d');
        $cleanEndDate   = Carbon::parse($row->end_date)->format('Y-m-d');

        $cleanStartTime = substr($row->start_time, 0, 8);
        $cleanEndTime   = substr($row->end_time, 0, 8);

        $startDT = Carbon::parse("$cleanStartDate $cleanStartTime");
        $endDT   = Carbon::parse("$cleanEndDate $cleanEndTime");

        $mins = $startDT->diffInMinutes($endDT);
        $total = sprintf('%02d:%02d', floor($mins/60), $mins%60);

        // For simple table view
        $simpleReport[] = [
            'project'    => $row->projectname ?? 'Unnamed Project',
            'task'       => $row->task_name ?? 'Unnamed Task',
            'status'     => $row->task_status ?? 'no_status',
            'start_time' => substr($row->start_time, 0, 5),
            'end_time'   => substr($row->end_time, 0, 5),
            'total'      => $total,
        ];

        // For chart: group by date
        $day = Carbon::parse($row->start_date)->format('M d');
        $dailyHours[$day] = ($dailyHours[$day] ?? 0) + round($mins / 60, 2);

        // For project chart
        $projectHours[$row->projectname] = ($projectHours[$row->projectname] ?? 0) + round($mins / 60, 2);
    }

    // Dropdown list
    $allStatuses = DB::table('tasks')->select('status')->distinct()->pluck('status')->toArray();
    $allProjects = DB::table('projects')->select('id', 'projectname')->get();

    return view('hrms.Employee.Reports.job-status.index', [
        'simpleReport' => $simpleReport,
        'from' => $startOfWeek,
        'to' => $endOfWeek,
        'statusFilter' => $statusFilter,
        'projectFilter' => $projectFilter,
        'allStatuses' => $allStatuses,
        'allProjects' => $allProjects,
        'dailyLabels' => array_keys($dailyHours),
        'dailyValues' => array_values($dailyHours),
        'projectLabels' => array_keys($projectHours),
        'projectValues' => array_values($projectHours),
    ]);
}
public function projectStatusReport(Request $request)
{
    $start = $request->query('from') ?? Carbon::now()->startOfMonth()->toDateString();
    $end   = $request->query('to')   ?? Carbon::now()->endOfMonth()->toDateString();

    $statusFilter  = $request->query('status');
    $projectFilter = $request->query('project');

    $query = DB::table('timesheet')
        ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->join('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->select(
            'timesheet.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'projects.id as project_id',
            'projects.projectname',
            'projects.status as project_status',   // NEW
            'tasks.task as task_name',
            'tasks.status as task_status'
        )
        ->whereBetween(DB::raw("DATE(timesheet.start_date)"), [$start, $end]);

    // FILTER BY PROJECT STATUS
    if ($statusFilter && $statusFilter !== 'all') {
        $query->where('projects.status', $statusFilter);
    }

    // FILTER BY PROJECT
    if ($projectFilter && $projectFilter !== 'all') {
        $query->where('projects.id', $projectFilter);
    }

    // FIX ambiguous sorting
    $rows = $query
        ->orderBy('timesheet.employee_id','asc')
        ->orderBy('projects.id','asc')
        ->get();

    $report = [];

    foreach ($rows as $row) {

        if (!$row->start_time || !$row->end_time) continue;

        $employee = trim($row->firstname . ' ' . $row->lastname);
        $project  = $row->projectname;
        $status   = $row->project_status ?? 'Unknown';  // NEW

        // Clean timestamps
        $cleanStartDate = Carbon::parse($row->start_date)->format('Y-m-d');
        $cleanEndDate   = Carbon::parse($row->end_date)->format('Y-m-d');
        $cleanStartTime = substr($row->start_time, 0, 8);
        $cleanEndTime   = substr($row->end_time, 0, 8);

        $startDT = Carbon::parse("$cleanStartDate $cleanStartTime");
        $endDT   = Carbon::parse("$cleanEndDate $cleanEndTime");

        $mins = $startDT->diffInMinutes($endDT);

        // group employee → project → project_status
        $report[$employee][$project][$status] =
            ($report[$employee][$project][$status] ?? 0) + $mins;
    }

    // FETCH PROJECT STATUS LIST
    $allProjectStatuses = DB::table('projects')
        ->select('status')
        ->distinct()
        ->whereNotNull('status')
        ->pluck('status');

    $allProjects = DB::table('projects')->select('id','projectname')->get();

    return view('hrms.Employee.Reports.project-status.index', [
        'report'             => $report,
        'from'               => $start,
        'to'                 => $end,
        'statusFilter'       => $statusFilter,
        'projectFilter'      => $projectFilter,
        'allProjectStatuses' => $allProjectStatuses,   // NEW
        'allProjects'        => $allProjects,
    ]);
}
public function scheduledVsWorkedReport(Request $request)
{
    $from = $request->query('from') ?? Carbon::now()->startOfMonth()->toDateString();
    $to   = $request->query('to')   ?? Carbon::now()->endOfMonth()->toDateString();

    $rows = DB::table('timesheet')
        ->join('allemployees', 'allemployees.id', '=', 'timesheet.employee_id')
        ->join('projects', 'projects.id', '=', 'timesheet.project_id')
        ->leftJoin('tasks', 'tasks.id', '=', 'timesheet.task_id')
        ->select(
            'allemployees.firstname',
            'allemployees.lastname',
            'projects.projectname',
            'tasks.task as task_name',
            'tasks.due_date',
            'tasks.updated_at as completed_date',
            'timesheet.start_date',
            'timesheet.end_date',
            'timesheet.start_time',
            'timesheet.end_time'
        )
        ->whereBetween(DB::raw("DATE(timesheet.start_date)"), [$from, $to])
        ->get();

    $report = [];

    foreach ($rows as $row) {

        $employee = trim($row->firstname . ' ' . $row->lastname);

        // ------------------------------------------
        // WORKED HOURS
        // ------------------------------------------
        $worked = "00:00";
        $workedMinutes = 0;

        if ($row->start_date && $row->end_date && $row->start_time && $row->end_time) {

            // Normalize date-only values
            $startDate = Carbon::parse($row->start_date)->format('Y-m-d');
            $endDate   = Carbon::parse($row->end_date)->format('Y-m-d');

            // Normalize time-only values
            $startTime = Carbon::parse($row->start_time)->format('H:i:s');
            $endTime   = Carbon::parse($row->end_time)->format('H:i:s');

            // Merge normalized date + time
            $start = Carbon::parse("$startDate $startTime");
            $end   = Carbon::parse("$endDate $endTime");

            // Calculate worked minutes
            $workedMinutes = $start->diffInMinutes($end);
            $worked = sprintf("%02d:%02d", floor($workedMinutes / 60), $workedMinutes % 60);
        }

        // ------------------------------------------
        // SCHEDULED HOURS (start_date → due_date)
        // ------------------------------------------
        $scheduled = "00:00";

        if ($row->start_date && $row->due_date) {
            $startDate = Carbon::parse($row->start_date)->startOfDay();
            $dueDate   = Carbon::parse($row->due_date)->endOfDay();

            $scheduledMinutes = $startDate->diffInMinutes($dueDate);
            $scheduled = sprintf("%02d:%02d", floor($scheduledMinutes / 60), $scheduledMinutes % 60);
        }

        // ------------------------------------------
        // STATUS
        // ------------------------------------------
        if (!$row->due_date) {
            $status = "No Schedule";
        } elseif (!$row->completed_date) {
            $status = "Pending";
        } else {
            $status = Carbon::parse($row->completed_date)->gt(Carbon::parse($row->due_date))
                ? "Late"
                : "On Time";
        }

        // ------------------------------------------
        // ADD TO REPORT
        // ------------------------------------------
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
    }

    return view('hrms.Employee.Reports.scheduled-worked.index', [
        'report' => $report,
        'from'   => $from,
        'to'     => $to
    ]);
}
public function myGoalReport(Request $request)
{
    $employeeId = session('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    // Date filter
    $from = $request->from ?? Carbon::now()->startOfYear()->toDateString();
    $to   = $request->to   ?? Carbon::now()->endOfYear()->toDateString();

    // Status filter
    $statusFilter = $request->status ?? 'all';

    // Status available in goals
    $allStatuses = [
        'Not Started',
        'In Progress',
        'On Hold',
        'Completed',
        'Cancelled'
    ];

    // Base Query
    $query = DB::table('goals')
        ->leftJoin('department', 'goals.department_id', '=', 'department.id')
        ->select(
            'goals.*',
            'department.department as department_name'
        )
        ->where('goals.deleted_at', 0)
        ->where('goals.assigned_to', $employeeId)   // 🔥 Only user's goals
        ->whereBetween('goals.start_date', [$from, $to]);

    // Status filter apply
    if ($statusFilter !== 'all') {
        $query->where('goals.status', $statusFilter);
    }

    $goals = $query->orderBy('goals.start_date', 'desc')->get();

    return view('hrms.Employee.Reports.goal-report.index', compact(
        'goals',
        'from',
        'to',
        'allStatuses',
        'statusFilter'
    ));
}


}