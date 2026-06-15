<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;


class AdminAttendanceController extends Controller
{
    public function index(Request $request)
{
    // Default to current month if no month/year selected
    $month = (int) $request->input('month', date('m'));
    $year = (int) $request->input('year', date('Y'));
    $departmentId = $request->input('department_id');
    $employeeId = $request->input('employee_id');

    $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
    $monthEnd = (clone $monthStart)->endOfMonth()->endOfDay();

    // Get department filter based on user role
    $departmentFilter = getEmployeeDepartmentFilter();
    $branchFilter = getAdminBranchFilter();
    $managerFilter = getManagerTeamFilter();

    // Base employees query - CRITICAL: Apply all filters consistently here
    $employeesQuery = DB::table('allemployees')
        ->leftJoin('schedule', function ($join) use ($monthStart, $monthEnd) {
            $join->on('allemployees.id', '=', 'schedule.employee_id')
                ->where('schedule.deleted_at', 0)
                ->where('schedule.is_current', 1)
                ->where(function($q) use ($monthStart, $monthEnd) {
                    $q->whereDate('schedule.schedule_end_date', '>=', $monthStart->toDateString())
                      ->whereDate('schedule.schedule_start_date', '<=', $monthEnd->toDateString());
                });
        })
        ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id') // Fixed join
        ->where('allemployees.deleted_at', 0)
        ->where('allemployees.status', 'active')
        ->select(
            'allemployees.*',
            'shifts.shift_name',
            'shifts.start_time',
            'shifts.end_time',
            'shifts.days_of_week',
            'department.department',
            'schedule.id as schedule_id'
        );

    // Apply role-based filters in correct priority
    if ($managerFilter) {
        // Manager can only see their team
        $employeesQuery->where('allemployees.manager_id', $managerFilter);
    } elseif ($branchFilter) {
        // Branch admin can see their branch
        $employeesQuery->where('allemployees.branch_id', $branchFilter);
    } elseif ($departmentFilter) {
        // Department head can see their department
        $employeesQuery->where('allemployees.department', $departmentFilter);
    }

    // Build employee dropdown options from the same visibility scope
    $employeeOptionsQuery = clone $employeesQuery;

    // Apply additional department filter if selected
    if ($departmentId) {
        $employeesQuery->where('allemployees.department', $departmentId);
        $employeeOptionsQuery->where('allemployees.department', $departmentId);
    }

    if ($employeeId) {
        $employeesQuery->where('allemployees.id', $employeeId);
    }

    $employeeOptions = $employeeOptionsQuery
        ->select('allemployees.id', 'allemployees.firstname', 'allemployees.lastname')
        ->orderBy('allemployees.firstname')
        ->get();

    $employees = $employeesQuery->orderBy('allemployees.firstname')->get();

    // Get ALL schedules for the selected month for working day calculations
    $schedulesByEmployee = DB::table('schedule')
        ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->where('schedule.deleted_at', 0)
        ->where(function($query) use ($monthStart, $monthEnd) {
            // Include schedules that overlap with the selected month
            $query->where(function($q) use ($monthStart, $monthEnd) {
                $q->whereDate('schedule.schedule_end_date', '>=', $monthStart->toDateString())
                  ->whereDate('schedule.schedule_start_date', '<=', $monthEnd->toDateString());
            })->orWhere(function($q) use ($monthEnd) {
                // Include weekly repeating schedules that started before month end
                $q->where('schedule.repeat_every_week', '>', 0)
                  ->whereDate('schedule.schedule_start_date', '<=', $monthEnd->toDateString());
            });
        })
        ->select(
            'schedule.id',
            'schedule.employee_id',
            'schedule.schedule_start_date',
            'schedule.schedule_end_date',
            'schedule.repeat_every_week',
            'schedule.shift_id',
            'shifts.shift_name',
            'shifts.start_time',
            'shifts.end_time',
            'shifts.days_of_week'
        )
        ->orderBy('schedule.schedule_start_date')
        ->get()
        ->groupBy('employee_id');

    // Get company holidays
    $holidays = DB::table('holidays')
        ->whereMonth('holidaydate', $month)
        ->whereYear('holidaydate', $year)
        ->get()
        ->keyBy(function ($item) {
            return date('d', strtotime($item->holidaydate));
        });

    // Get employee leaves
    $employeeLeaves = DB::table('employee_leaves')
        ->where('status', 'approved')
        ->where(function ($query) use ($monthStart, $monthEnd) {
            $query->whereDate('from_date', '<=', $monthEnd->toDateString())
                ->whereDate('to_date', '>=', $monthStart->toDateString());
        })
        ->get()
        ->groupBy('employee_id');

    // Get attendance data for filtered employees only
    $employeeIds = $employees->pluck('id')->toArray();
    $attendanceData = [];
    
    if (!empty($employeeIds)) {
        $attendances = DB::table('attendances')
            ->leftJoin('schedule', 'attendances.schedule_id', '=', 'schedule.id')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->whereIn('attendances.employee_id', $employeeIds)
            ->whereMonth('attendances.date', $month)
            ->whereYear('attendances.date', $year)
            ->select(
                'attendances.*',
                'shifts.shift_name',
                'shifts.start_time as scheduled_start',
                'shifts.end_time as scheduled_end'
            )
            ->get()
            ->groupBy('employee_id');

        foreach ($attendances as $empId => $empAttendances) {
            $attendanceData[$empId] = $empAttendances->keyBy(function ($item) {
                return date('d', strtotime($item->date));
            });
        }
    }

    // Initialize attendance data for all filtered employees
    foreach ($employeeIds as $empId) {
        if (!isset($attendanceData[$empId])) {
            $attendanceData[$empId] = collect();
        }
    }

    // Get departments for filter dropdown
    $departments = DB::table('department')->select('id', 'department')->get();

    // Get days in month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Calculate summary statistics - use ONLY filtered employees
    $summaryStats = $this->calculateSummaryStats(
        $employees, 
        $attendanceData, 
        $holidays, 
        $month, 
        $year, 
        $schedulesByEmployee
    );

    return view('hrms.attendance.admin.index', [
        'employees' => $employees,
        'attendanceData' => $attendanceData,
        'holidays' => $holidays,
        'employeeLeaves' => $employeeLeaves,
        'departments' => $departments,
        'employeeOptions' => $employeeOptions,
        'month' => $month,
        'year' => $year,
        'daysInMonth' => $daysInMonth,
        'monthName' => date('F', mktime(0, 0, 0, $month, 1)),
        'selectedDepartment' => $departmentId,
        'selectedEmployee' => $employeeId,
        'summaryStats' => $summaryStats,
        'schedulesByEmployee' => $schedulesByEmployee,
    ]);
}

    public function daily(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $employeeId = $request->input('employee_id');
        $departmentId = $request->input('department_id');
        $status = $request->input('status'); // present, absent, late, etc.

        // Get all employees for dropdown
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->orderBy('firstname')
            ->get();

        // Get departments for filter
        $departments = DB::table('department')->select('id', 'department')->get();

        // Base query with schedule and shift information
        $query = DB::table('attendances')
            ->rightJoin('allemployees', 'attendances.employee_id', '=', 'allemployees.id')
            ->leftJoin('schedule', function ($join) use ($date) {
                $join->on('allemployees.id', '=', 'schedule.employee_id')
                    ->where('schedule.deleted_at', 0)
                    ->where('schedule.is_current', 1)
                    ->whereRaw("? BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date", [$date]);
            })
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->select(
                'attendances.*',
                'allemployees.id as employee_id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.designation',
                'allemployees.profile_image',
                'shifts.shift_name',
                'shifts.start_time as scheduled_start',
                'shifts.end_time as scheduled_end',
                'shifts.days_of_week',
                'department.department',
                'schedule.id as schedule_id'
            );

        // Apply date filter
        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('attendances.date', $date)
                    ->orWhereNull('attendances.date');
            });
        }

        // Apply employee filter if selected
        if ($employeeId) {
            $query->where('allemployees.id', $employeeId);
        }

        // Apply department filter if selected
        if ($departmentId) {
            $query->where('schedule.department_id', $departmentId);
        }

        $attendance = $query->orderBy('allemployees.firstname')->get();

        // Add calculated fields and filter by status
        $attendance = $attendance->map(function ($record) use ($date) {
            // Check if employee should be working today
            $record->should_work_today = $this->shouldEmployeeWorkToday($record, $date);
            $record->is_on_leave = $this->isEmployeeOnLeave($record->employee_id, $date);

            // Determine attendance status
            if ($record->is_on_leave) {
                $record->attendance_status = 'on_leave';
            } elseif (!$record->should_work_today) {
                $record->attendance_status = 'weekend';
            } elseif ($record->punch_in && $record->punch_out) {
                $record->attendance_status = 'present';
            } elseif ($record->punch_in) {
                $record->attendance_status = 'partial';
            } else {
                $record->attendance_status = 'absent';
            }

            // Calculate if employee was late/early
            if ($record->scheduled_start && $record->punch_in) {
                $scheduledStart = Carbon::parse($record->scheduled_start);
                $actualPunchIn = Carbon::parse($record->punch_in);

                $record->punch_in_status = 'on_time';
                $record->minutes_difference = 0;

                if ($actualPunchIn->gt($scheduledStart->addMinutes(15))) {
                    $record->punch_in_status = 'late';
                    $record->minutes_difference = $scheduledStart->diffInMinutes($actualPunchIn);
                    $record->attendance_status = 'late';
                } elseif ($actualPunchIn->lt($scheduledStart->subMinutes(15))) {
                    $record->punch_in_status = 'early';
                    $record->minutes_difference = $actualPunchIn->diffInMinutes($scheduledStart);
                }
            }

            // Calculate overtime/undertime
            if ($record->scheduled_start && $record->scheduled_end && $record->working_hours) {
                $scheduledStart = Carbon::parse($record->scheduled_start);
                $scheduledEnd = Carbon::parse($record->scheduled_end);
                $scheduledHours = $scheduledStart->diffInHours($scheduledEnd);

                $record->scheduled_hours = $scheduledHours;
                $record->overtime_hours = max(0, $record->working_hours - $scheduledHours);
                $record->undertime_hours = max(0, $scheduledHours - $record->working_hours);
            }

            return $record;
        });

        // Filter by status if selected
        if ($status) {
            $attendance = $attendance->where('attendance_status', $status);
        }

        // Calculate daily summary
        $dailySummary = $this->calculateDailySummary($attendance);

        return view('hrms.attendance.admin.daily', [
            'attendance' => $attendance,
            'employees' => $employees,
            'departments' => $departments,
            'date' => $date,
            'selectedEmployee' => $employeeId,
            'selectedDepartment' => $departmentId,
            'selectedStatus' => $status,
            'formattedDate' => date('F j, Y', strtotime($date)),
            'dailySummary' => $dailySummary
        ]);
    }

    /**
     * Check if employee should work on given date based on schedule
     */
    private function shouldEmployeeWorkToday($employee, $date)
    {
        // Check if it's a company holiday
        $holiday = DB::table('holidays')->whereDate('holidaydate', $date)->first();
        if ($holiday) {
            return false;
        }

        // Check if employee has a schedule
        if (!$employee->schedule_id || !$employee->days_of_week) {
            return false;
        }

        // Check if today is in working days
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        $workingDays = array_map('strtolower', array_map('trim', explode(',', $employee->days_of_week)));

        return in_array($dayOfWeek, $workingDays);
    }

    /**
     * Check if employee is on leave on given date
     */
    private function isEmployeeOnLeave($employeeId, $date)
    {
        return DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->exists();
    }

    /**
     * Get employee leave details for a specific date
     */
    private function getEmployeeLeaveForDate($employeeId, $date)
    {
        return DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();
    }

    /**
     * Calculate daily summary statistics
     */
    private function calculateDailySummary($attendance)
    {
        $summary = [
            'total_employees' => $attendance->count(),
            'scheduled_employees' => $attendance->where('should_work_today', true)->where('is_on_leave', false)->count(),
            'present' => $attendance->where('attendance_status', 'present')->count(),
            'absent' => $attendance->where('attendance_status', 'absent')->count(),
            'late' => $attendance->where('attendance_status', 'late')->count(),
            'partial' => $attendance->where('attendance_status', 'partial')->count(),
            'on_leave' => $attendance->where('attendance_status', 'on_leave')->count(),
            'weekend' => $attendance->where('attendance_status', 'weekend')->count(),
        ];

        $summary['attendance_rate'] = $summary['scheduled_employees'] > 0 ?
            round((($summary['present'] + $summary['late']) / $summary['scheduled_employees']) * 100, 2) : 0;

        return $summary;
    }

    /**
 * Find schedule for a specific date
 */
private function findScheduleForDate($employeeSchedules, string $currentDate)
{
    if (!$employeeSchedules) {
        return null;
    }
    
    foreach ($employeeSchedules as $sch) {
        // For weekly repeating schedules
        if ($sch->repeat_every_week) {
            if ($currentDate >= $sch->schedule_start_date) {
                return $sch;
            }
        } 
        // For regular schedules
        else {
            if ($currentDate >= $sch->schedule_start_date && $currentDate <= $sch->schedule_end_date) {
                return $sch;
            }
        }
    }
    return null;
}

/**
 * Check if a date is a working weekday based on schedule days
 */
private function isWorkingWeekday(string $currentDate, ?string $daysOfWeek): bool
{
    if (!$daysOfWeek || trim($daysOfWeek) === '') {
        return false;
    }
    
    $tokens = array_map(function ($v) {
        return strtolower(trim($v));
    }, explode(',', $daysOfWeek));

    $dayFull = strtolower(date('l', strtotime($currentDate))); // monday
    $dayShort = strtolower(date('D', strtotime($currentDate))); // mon
    $dayNum = (string) date('N', strtotime($currentDate)); // 1-7 (Monday=1)
    
    // Handle Sunday representation
    $isSunday = $dayNum === '7';
    $dayNumSundayZero = $dayNum === '7' ? '0' : $dayNum;

    return in_array($dayFull, $tokens, true)
        || in_array($dayShort, $tokens, true)
        || in_array($dayNum, $tokens, true)
        || ($isSunday && in_array('0', $tokens, true))
        || in_array($dayNumSundayZero, $tokens, true);
}

    /**
     * Calculate summary statistics for monthly view
     * Honor schedule date ranges per day and use that schedule's days_of_week.
     */
   /**
 * Calculate summary statistics for monthly view
 * Honor schedule date ranges per day and use that schedule's days_of_week.
 */
/**
 * Calculate summary statistics for monthly view
 * Only counts working days based on schedule and days_of_week
 */
private function calculateSummaryStats($employees, $attendanceData, $holidays, $month, $year, $schedulesByEmployee)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $totalWorkingDays = 0;
    $totalPresentDays = 0;
    $totalLateDays = 0;
    $totalAbsentDays = 0;
    $totalOvertimeHours = 0;
    $totalLeaveDays = 0;

    foreach ($employees as $employee) {
        $employeeWorkingDays = 0;
        $employeePresentDays = 0;
        $employeeLateDays = 0;
        $employeeOvertimeHours = 0;
        $employeeLeaveDays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
            
            $isCompanyHoliday = isset($holidays[$dayKey]);
            $isEmployeeOnLeave = $this->isEmployeeOnLeave($employee->id, $currentDate);

            // Count leave days
            if ($isEmployeeOnLeave) {
                $employeeLeaveDays++;
                $totalLeaveDays++;
            }

            // Find schedule for this date
            $empSchedules = $schedulesByEmployee->get($employee->id) ?? collect();
            $activeSchedule = $this->findScheduleForDate($empSchedules, $currentDate);

            // Check if employee should work today
            $shouldWork = false;
            if (!$isCompanyHoliday && !$isEmployeeOnLeave && $activeSchedule) {
                $shouldWork = $this->isWorkingWeekday($currentDate, $activeSchedule->days_of_week);
            }

            if ($shouldWork) {
                $employeeWorkingDays++;
                $totalWorkingDays++;

                // Check attendance
                $attendance = $attendanceData[$employee->id][$dayKey] ?? null;

                if ($attendance && $attendance->punch_in && $attendance->punch_out) {
                    $employeePresentDays++;
                    $totalPresentDays++;
                    
                    // Check if late
                    if ($attendance->status === 'late') {
                        $employeeLateDays++;
                        $totalLateDays++;
                    }
                    
                    // Add overtime hours
                    $employeeOvertimeHours += $attendance->overtime_hours ?? 0;
                    $totalOvertimeHours += $attendance->overtime_hours ?? 0;
                } else {
                    $totalAbsentDays++;
                }
            }
        }
    }

    // Calculate rates
    $overallAttendanceRate = $totalWorkingDays > 0 
        ? round(($totalPresentDays / $totalWorkingDays) * 100, 2) 
        : 0;
    
    // Calculate average working days per employee
    $avgWorkingDaysPerEmployee = count($employees) > 0 
        ? round($totalWorkingDays / count($employees), 1)
        : 0;

    return [
        'total_employees' => count($employees),
        'total_working_days' => $totalWorkingDays,
        'total_present_days' => $totalPresentDays,
        'total_absent_days' => $totalAbsentDays,
        'total_late_days' => $totalLateDays,
        'total_overtime_hours' => round($totalOvertimeHours, 1),
        'total_leave_days' => $totalLeaveDays,
        'avg_working_days_per_employee' => $avgWorkingDaysPerEmployee,
        'overall_attendance_rate' => $overallAttendanceRate,
    ];
}
    /**
     * Add employee leave
     */
    public function addEmployeeLeave(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:allemployees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|string',
            'leave_reason' => 'nullable|string'
        ]);

        // Get employee name
        $employee = DB::table('allemployees')->find($request->employee_id);

        // Calculate number of days
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $numberOfDays = $fromDate->diffInDays($toDate) + 1;

        DB::table('employee_leaves')->insert([
            'employee_id' => $request->employee_id,
            'employee_name' => $employee->firstname . ' ' . $employee->lastname,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'no_of_days' => $numberOfDays,
            'leave_type' => $request->leave_type,
            'leave_reason' => $request->leave_reason,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Employee leave added successfully']);
    }

    /**
     * Remove employee leave
     */
    public function removeEmployeeLeave(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|exists:employee_leaves,id'
        ]);

        DB::table('employee_leaves')
            ->where('id', $request->leave_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Employee leave removed successfully']);
    }

    /**
     * Get leave details for a specific employee and date
     */
    public function getLeaveDetails($employeeId, $date)
    {
        $employee = DB::table('allemployees')->find($employeeId);
        $leave = $this->getEmployeeLeaveForDate($employeeId, $date);

        $html = view('hrms.attendance.admin.leave-details', compact('employee', 'leave', 'date'))->render();

        return response($html);
    }

    /**
     * Get attendance summary for an employee
     */
    public function getEmployeeAttendanceSummary(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required'], 400);
        }

        // Get employee's schedules for the month
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth()->endOfDay();

        $schedules = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->whereDate('schedule_end_date', '>=', $monthStart->toDateString())
            ->whereDate('schedule_start_date', '<=', $monthEnd->toDateString())
            ->select('schedule.*', 'shifts.*')
            ->get();

        // Get attendance records for the month
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Calculate summary statistics
        $summary = [
            'total_working_days' => 0,
            'present_days' => $attendance->where('punch_out', '!=', null)->count(),
            'absent_days' => 0,
            'late_days' => $attendance->where('status', 'late')->count(),
            'early_departures' => $attendance->where('status', 'early_departure')->count(),
            'total_hours_worked' => $attendance->sum('working_hours'),
            'total_scheduled_hours' => 0,
            'overtime_hours' => $attendance->sum('overtime_minutes') / 60,
            'undertime_hours' => $attendance->sum('undertime_minutes') / 60
        ];

        return response()->json([
            'summary' => $summary,
            'schedules' => $schedules,
            'attendance' => $attendance
        ]);
    }

    public function bulkOperations(Request $request)
    {
        $operation = $request->input('operation'); // mark_present, mark_absent, etc.
        $employeeIds = $request->input('employee_ids', []);
        $date = $request->input('date');

        // Implementation for bulk operations

        return response()->json(['message' => 'Bulk operation completed']);
    }

    public function getAttendanceDetails($employeeId, $date)
    {
        $employee = DB::table('allemployees')->find($employeeId);
        $attendance = DB::table('attendances')
            ->leftJoin('schedule', 'attendances.schedule_id', '=', 'schedule.id')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->where('attendances.employee_id', $employeeId)
            ->whereDate('attendances.date', $date)
            ->select('attendances.*', 'shifts.shift_name', 'shifts.start_time', 'shifts.end_time')
            ->first();

        // Check if employee is on leave
        $leave = $this->getEmployeeLeaveForDate($employeeId, $date);

        $html = view('hrms.attendance.admin.details', compact('employee', 'attendance', 'date', 'leave'))->render();

        return response($html);
    }

    public function debugEmployeeShifts(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $employeeId = $request->input('employee_id');

        $query = DB::table('allemployees')
            ->leftJoin('schedule', function ($join) use ($date) {
                $join->on('allemployees.id', '=', 'schedule.employee_id')
                    ->where('schedule.deleted_at', 0)
                    ->where('schedule.is_current', 1)
                    ->whereRaw("? BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date", [$date]);
            })
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'shifts.shift_name',
                'shifts.days_of_week',
                'department.department',
                'schedule.id as schedule_id'
            );

        if ($employeeId) {
            $query->where('allemployees.id', $employeeId);
        }

        $employees = $query->get();

        $debugData = [];
        $carbonDate = Carbon::parse($date);

        foreach ($employees as $employee) {
            $dayOfWeek = $carbonDate->format('N'); // 1-7 (Monday = 1, Sunday = 7)
            $dayName = strtolower($carbonDate->format('l')); // monday, tuesday, etc.
            $dayShort = strtolower($carbonDate->format('D')); // mon, tue, etc.

            $workingDays = [];
            $shouldWork = false;
            $isOnLeave = $this->isEmployeeOnLeave($employee->id, $date);

            if ($employee->days_of_week) {
                $workingDays = array_map('trim', explode(',', $employee->days_of_week));
                $workingDaysLower = array_map('strtolower', $workingDays);

                // Check various formats
                $shouldWork = in_array($dayName, $workingDaysLower) ||
                    in_array($dayShort, $workingDaysLower) ||
                    in_array((string)$dayOfWeek, $workingDays) ||
                    ($dayOfWeek == 7 && (in_array('0', $workingDays) || in_array('7', $workingDays)));
            }

            $debugData[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->firstname . ' ' . $employee->lastname,
                'shift_name' => $employee->shift_name,
                'department' => $employee->department,
                'schedule_id' => $employee->schedule_id,
                'date' => $date,
                'day_of_week_number' => $dayOfWeek,
                'day_name' => $dayName,
                'day_short' => $dayShort,
                'working_days_raw' => $employee->days_of_week,
                'working_days_array' => $workingDays,
                'should_work_today' => $shouldWork,
                'is_on_leave' => $isOnLeave,
                'is_sunday' => $dayOfWeek == 7,
                'has_schedule' => !is_null($employee->schedule_id)
            ];
        }

        return response()->json([
            'date' => $date,
            'day_info' => [
                'day_of_week_number' => $carbonDate->format('N'),
                'day_name' => $carbonDate->format('l'),
                'day_short' => $carbonDate->format('D'),
            ],
            'employees' => $debugData
        ]);
    }

    public function export(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            $departmentId = $request->input('department_id');
            $format = $request->input('format', 'excel'); // excel, csv, pdf

            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $fileName = "attendance_{$monthName}_{$year}";

            // Add department name to filename if filtered
            if ($departmentId) {
                $department = DB::table('department')->find($departmentId);
                if ($department) {
                    $fileName .= "_{$department->department}";
                }
            }

            switch ($format) {
                case 'csv':
                    return Excel::download(
                        new AttendanceExport($month, $year, $departmentId),
                        $fileName . '.csv',
                        \Maatwebsite\Excel\Excel::CSV
                    );

                case 'pdf':
                    return Excel::download(
                        new AttendanceExport($month, $year, $departmentId),
                        $fileName . '.pdf',
                        \Maatwebsite\Excel\Excel::DOMPDF
                    );

                case 'excel':
                default:
                    return Excel::download(
                        new AttendanceExport($month, $year, $departmentId),
                        $fileName . '.xlsx'
                    );
            }
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Export failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function exportDaily(Request $request)
    {
        try {
            $date = $request->input('date', date('Y-m-d'));
            $departmentId = $request->input('department_id');
            $format = $request->input('format', 'excel');

            $fileName = "daily_attendance_" . date('Y_m_d', strtotime($date));

            // Get daily attendance data
            $query = DB::table('attendances')
                ->rightJoin('allemployees', 'attendances.employee_id', '=', 'allemployees.id')
                ->leftJoin('schedule', function ($join) use ($date) {
                    $join->on('allemployees.id', '=', 'schedule.employee_id')
                        ->where('schedule.deleted_at', 0)
                        ->where('schedule.is_current', 1)
                        ->whereRaw("? BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date", [$date]);
                })
                ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
                ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
                ->where('allemployees.deleted_at', 0)
                ->where('allemployees.status', 'active')
                ->select(
                    'attendances.*',
                    'allemployees.id as employee_id',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.employeeid',
                    'allemployees.designation',
                    'shifts.shift_name',
                    'shifts.start_time as scheduled_start',
                    'shifts.end_time as scheduled_end',
                    'department.department'
                );

            if ($departmentId) {
                $query->where('schedule.department_id', $departmentId);
            }

            $query->where(function ($q) use ($date) {
                $q->whereDate('attendances.date', $date)
                    ->orWhereNull('attendances.date');
            });

            $attendance = $query->orderBy('allemployees.firstname')->get();

            // Prepare data for export
            $exportData = [];
            $headers = [
                'Employee Name',
                'Employee ID',
                'Department',
                'Shift',
                'Scheduled In',
                'Scheduled Out',
                'Actual In',
                'Actual Out',
                'Working Hours',
                'Status',
                'Late Minutes',
                'Overtime Hours'
            ];

            foreach ($attendance as $record) {
                $lateMinutes = 0;
                $overtimeHours = 0;

                // Calculate late minutes
                if ($record->scheduled_start && $record->punch_in) {
                    $scheduledStart = Carbon::parse($record->scheduled_start);
                    $actualPunchIn = Carbon::parse($record->punch_in);
                    if ($actualPunchIn->gt($scheduledStart)) {
                        $lateMinutes = $scheduledStart->diffInMinutes($actualPunchIn);
                    }
                }

                // Calculate overtime
                if ($record->working_hours && $record->scheduled_start && $record->scheduled_end) {
                    $scheduledHours = Carbon::parse($record->scheduled_start)->diffInHours(Carbon::parse($record->scheduled_end));
                    $overtimeHours = max(0, $record->working_hours - $scheduledHours);
                }

                $status = 'Absent';
                if ($record->punch_in && $record->punch_out) {
                    $status = 'Present';
                    if ($lateMinutes > 15) {
                        $status = 'Late';
                    }
                } elseif ($record->punch_in) {
                    $status = 'Partial';
                }

                $exportData[] = [
                    $record->firstname . ' ' . $record->lastname,
                    $record->employeeid ?? 'N/A',
                    $record->department ?? 'N/A',
                    $record->shift_name ?? 'N/A',
                    $record->scheduled_start ? date('H:i', strtotime($record->scheduled_start)) : 'N/A',
                    $record->scheduled_end ? date('H:i', strtotime($record->scheduled_end)) : 'N/A',
                    $record->punch_in ? date('H:i', strtotime($record->punch_in)) : 'N/A',
                    $record->punch_out ? date('H:i', strtotime($record->punch_out)) : 'N/A',
                    $record->working_hours ? number_format($record->working_hours, 2) : '0.00',
                    $status,
                    $lateMinutes,
                    number_format($overtimeHours, 2)
                ];
            }

            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'daily_attendance');
            $handle = fopen($tempFile, 'w');

            // Write headers
            fputcsv($handle, $headers);

            // Write data
            foreach ($exportData as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);

            // Return file download
            return Response::download($tempFile, $fileName . '.csv', [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Daily export error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Export failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
 * Show pending manual attendance requests
 */
public function pendingManualRequests(Request $request)
{
    $departmentId = $request->input('department_id');
    $status = $request->input('status', 'pending');
    $date = $request->input('date');
    
    $query = DB::table('manual_attendance_requests')
        ->join('allemployees', 'manual_attendance_requests.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('schedule', function ($join) {
            $join->on('allemployees.id', '=', 'schedule.employee_id')
                ->where('schedule.deleted_at', 0)
                ->where('schedule.is_current', 1);
        })
        ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->where('allemployees.deleted_at', 0)
        ->where('allemployees.status', 'active')
        ->select(
            'manual_attendance_requests.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.employeeid',
            'allemployees.designation',
            'department.department',
            'shifts.shift_name',
            'shifts.start_time as scheduled_start',
            'shifts.end_time as scheduled_end'
        );
    
    // Apply filters
    if ($status) {
        $query->where('manual_attendance_requests.status', $status);
    }
    
    if ($departmentId) {
        $query->where('allemployees.department', $departmentId);
    }
    
    if ($date) {
        $query->whereDate('manual_attendance_requests.date', $date);
    }
    
    $requests = $query->orderBy('manual_attendance_requests.created_at', 'desc')->paginate(20);
    
    // Get departments for filter
    $departments = DB::table('department')->select('id', 'department')->get();
    
    return view('hrms.attendance.admin.manual-requests', [
        'requests' => $requests,
        'departments' => $departments,
        'selectedDepartment' => $departmentId,
        'selectedStatus' => $status,
        'selectedDate' => $date
    ]);
}

/**
 * Show request details for admin
 */
public function showRequestDetails($id)
{
    $request = DB::table('manual_attendance_requests')
        ->join('allemployees', 'manual_attendance_requests.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('schedule', function ($join) use ($request) {
            $join->on('allemployees.id', '=', 'schedule.employee_id')
                ->where('schedule.deleted_at', 0)
                ->whereRaw('? BETWEEN schedule.schedule_start_date AND schedule.schedule_end_date', [$request->date]);
        })
        ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->leftJoin('attendances', function ($join) use ($request) {
            $join->on('manual_attendance_requests.employee_id', '=', 'attendances.employee_id')
                ->whereDate('attendances.date', 'manual_attendance_requests.date');
        })
        ->where('manual_attendance_requests.id', $id)
        ->select(
            'manual_attendance_requests.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.employeeid',
            'allemployees.designation',
            'department.department',
            'shifts.shift_name',
            'shifts.start_time as scheduled_start',
            'shifts.end_time as scheduled_end',
            'shifts.break_time',
            'attendances.punch_in as existing_punch_in',
            'attendances.punch_out as existing_punch_out',
            'attendances.working_hours as existing_working_hours'
        )
        ->first();
    
    if (!$request) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    return response()->json([
        'success' => true,
        'data' => $request
    ]);
}

/**
 * Approve manual attendance request
 */
public function approveManualRequest(Request $request, $id)
{
    $adminId = auth()->id() ?? Session::get('user_id');
    
    $manualRequest = DB::table('manual_attendance_requests')->find($id);
    
    if (!$manualRequest) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    if ($manualRequest->status !== 'pending') {
        return response()->json(['error' => 'Request already processed'], 400);
    }
    
    // Update request status
    DB::table('manual_attendance_requests')
        ->where('id', $id)
        ->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $request->input('notes'),
            'updated_at' => now()
        ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Request approved successfully'
    ]);
}

/**
 * Process manual attendance (create/update attendance record)
 */
public function processManualRequest(Request $request, $id)
{
    $adminId = auth()->id() ?? Session::get('user_id');
    
    $manualRequest = DB::table('manual_attendance_requests')->find($id);
    
    if (!$manualRequest) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    if ($manualRequest->status !== 'approved') {
        return response()->json(['error' => 'Request must be approved first'], 400);
    }
    
    // Get employee's schedule for that date
    $employeeSchedule = DB::table('schedule')
        ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->where('schedule.employee_id', $manualRequest->employee_id)
        ->where('schedule.deleted_at', 0)
        ->whereDate('schedule_start_date', '<=', $manualRequest->date)
        ->whereDate('schedule_end_date', '>=', $manualRequest->date)
        ->select('schedule.*', 'shifts.*')
        ->first();
    
    // Get existing attendance record
    $existingAttendance = DB::table('attendances')
        ->where('employee_id', $manualRequest->employee_id)
        ->whereDate('date', $manualRequest->date)
        ->first();
    
    $now = now();
    
    // Process based on request type
    switch ($manualRequest->type) {
        case 'missed_punch_in':
            $attendanceData = [
                'employee_id' => $manualRequest->employee_id,
                'date' => $manualRequest->date,
                'punch_in' => $manualRequest->date . ' ' . $manualRequest->requested_punch_in,
                'status' => $this->determinePunchInStatus($manualRequest->requested_punch_in, $employeeSchedule),
                'created_at' => $now,
                'updated_at' => $now
            ];
            
            if ($employeeSchedule) {
                $attendanceData['schedule_id'] = $employeeSchedule->id;
                $attendanceData['shift_id'] = $employeeSchedule->shift_id;
                $attendanceData['scheduled_start'] = $employeeSchedule->start_time;
                $attendanceData['scheduled_end'] = $employeeSchedule->end_time;
                $attendanceData['allocated_break_time'] = $employeeSchedule->break_time;
            }
            
            DB::table('attendances')->insert($attendanceData);
            break;
            
        case 'missed_punch_out':
            if (!$existingAttendance) {
                return response()->json(['error' => 'No attendance record found'], 400);
            }
            
            $punchOutTime = $manualRequest->date . ' ' . $manualRequest->requested_punch_out;
            
            // Calculate working hours
            $punchIn = Carbon::parse($existingAttendance->punch_in);
            $punchOut = Carbon::parse($punchOutTime);
            $totalMinutes = $punchIn->diffInMinutes($punchOut);
            
            $totalBreakTaken = $existingAttendance->total_break_taken ?? 0;
            $actualWorkingMinutes = $totalMinutes - $totalBreakTaken;
            $workingHours = max(0, $actualWorkingMinutes) / 60;
            
            // Calculate overtime
            $overtimeMinutes = 0;
            $overtimeHours = 0;
            if ($employeeSchedule) {
                $scheduledStart = Carbon::parse($employeeSchedule->start_time);
                $scheduledEnd = Carbon::parse($employeeSchedule->end_time);
                $scheduledMinutes = $scheduledStart->diffInMinutes($scheduledEnd) - ($employeeSchedule->break_time ?? 0);
                $overtimeMinutes = max(0, $actualWorkingMinutes - $scheduledMinutes);
                $overtimeHours = $overtimeMinutes / 60;
            }
            
            $updateData = [
                'punch_out' => $punchOutTime,
                'working_hours' => $workingHours,
                'actual_working_minutes' => $actualWorkingMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'overtime_hours' => $overtimeHours,
                'updated_at' => $now
            ];
            
            DB::table('attendances')
                ->where('id', $existingAttendance->id)
                ->update($updateData);
            
            if ($overtimeHours > 0) {
                $this->calculateAndStoreOvertime($manualRequest->employee_id, $manualRequest->date, $overtimeHours, $employeeSchedule);
            }
            break;
            
        case 'correction':
            if (!$existingAttendance) {
                return response()->json(['error' => 'No attendance record found'], 400);
            }
            
            $updateData = ['updated_at' => $now];
            
            if ($manualRequest->requested_punch_in) {
                $updateData['punch_in'] = $manualRequest->date . ' ' . $manualRequest->requested_punch_in;
                $updateData['status'] = $this->determinePunchInStatus($manualRequest->requested_punch_in, $employeeSchedule);
            }
            
            if ($manualRequest->requested_punch_out) {
                $updateData['punch_out'] = $manualRequest->date . ' ' . $manualRequest->requested_punch_out;
                
                // Recalculate working hours if both times are provided
                if ($manualRequest->requested_punch_in && $manualRequest->requested_punch_out) {
                    $punchIn = Carbon::parse($manualRequest->date . ' ' . $manualRequest->requested_punch_in);
                    $punchOut = Carbon::parse($manualRequest->date . ' ' . $manualRequest->requested_punch_out);
                    $totalMinutes = $punchIn->diffInMinutes($punchOut);
                    $totalBreakTaken = $existingAttendance->total_break_taken ?? 0;
                    $actualWorkingMinutes = $totalMinutes - $totalBreakTaken;
                    $workingHours = max(0, $actualWorkingMinutes) / 60;
                    
                    $updateData['working_hours'] = $workingHours;
                    $updateData['actual_working_minutes'] = $actualWorkingMinutes;
                    
                    // Recalculate overtime
                    if ($employeeSchedule) {
                        $scheduledStart = Carbon::parse($employeeSchedule->start_time);
                        $scheduledEnd = Carbon::parse($employeeSchedule->end_time);
                        $scheduledMinutes = $scheduledStart->diffInMinutes($scheduledEnd) - ($employeeSchedule->break_time ?? 0);
                        $overtimeMinutes = max(0, $actualWorkingMinutes - $scheduledMinutes);
                        $overtimeHours = $overtimeMinutes / 60;
                        
                        $updateData['overtime_minutes'] = $overtimeMinutes;
                        $updateData['overtime_hours'] = $overtimeHours;
                        
                        if ($overtimeHours > 0) {
                            $this->calculateAndStoreOvertime($manualRequest->employee_id, $manualRequest->date, $overtimeHours, $employeeSchedule);
                        }
                    }
                }
            }
            
            DB::table('attendances')
                ->where('id', $existingAttendance->id)
                ->update($updateData);
            break;
            
        case 'late_entry':
            if ($existingAttendance) {
                return response()->json(['error' => 'Attendance record already exists'], 400);
            }
            
            $attendanceData = [
                'employee_id' => $manualRequest->employee_id,
                'date' => $manualRequest->date,
                'punch_in' => $manualRequest->date . ' ' . $manualRequest->requested_punch_in,
                'status' => 'late',
                'created_at' => $now,
                'updated_at' => $now
            ];
            
            if ($employeeSchedule) {
                $attendanceData['schedule_id'] = $employeeSchedule->id;
                $attendanceData['shift_id'] = $employeeSchedule->shift_id;
                $attendanceData['scheduled_start'] = $employeeSchedule->start_time;
                $attendanceData['scheduled_end'] = $employeeSchedule->end_time;
                $attendanceData['allocated_break_time'] = $employeeSchedule->break_time;
            }
            
            DB::table('attendances')->insert($attendanceData);
            break;
    }
    
    // Update request status to processed
    DB::table('manual_attendance_requests')
        ->where('id', $id)
        ->update([
            'status' => 'processed',
            'updated_at' => $now
        ]);
    
    // Log the processing
    $this->logManualRequestProcessing($manualRequest->employee_id, $adminId, $manualRequest->type);
    
    return response()->json([
        'success' => true,
        'message' => 'Attendance processed successfully'
    ]);
}

/**
 * Reject manual attendance request
 */
public function rejectManualRequest(Request $request, $id)
{
    $adminId = auth()->id() ?? Session::get('user_id');
    
    $manualRequest = DB::table('manual_attendance_requests')->find($id);
    
    if (!$manualRequest) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    if ($manualRequest->status !== 'pending') {
        return response()->json(['error' => 'Request already processed'], 400);
    }
    
    DB::table('manual_attendance_requests')
        ->where('id', $id)
        ->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
            'approved_by' => $adminId,
            'approved_at' => now(),
            'updated_at' => now()
        ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Request rejected successfully'
    ]);
}

private function determinePunchInStatus($punchInTime, $schedule)
{
    if (!$schedule) {
        return 'no_schedule';
    }
    
    $punchIn = Carbon::parse($punchInTime);
    $scheduledStart = Carbon::parse($schedule->start_time);
    
    if ($punchIn->lte($scheduledStart)) {
        return 'early';
    } elseif ($punchIn->lte($scheduledStart->copy()->addMinutes(15))) {
        return 'on_time';
    } else {
        return 'late';
    }
}

private function logManualRequestProcessing($employeeId, $adminId, $type)
{
    DB::table('attendance_processing_logs')->insert([
        'employee_id' => $employeeId,
        'processed_by' => $adminId,
        'action_type' => 'manual_attendance',
        'request_type' => $type,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
/**
 * Mark manual attendance (direct manual entry by admin)
 */
public function markManualAttendance(Request $request)
{
    try {
        $request->validate([
            'employee_id' => 'required|exists:allemployees,id',
            'date' => 'required|date',
            'punch_in' => 'required|date_format:H:i',
            'punch_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,half_day',
            'notes' => 'nullable|string'
        ]);

        $employeeId = $request->employee_id;
        $date = $request->date;
        $punchInTime = $request->punch_in;
        $punchOutTime = $request->punch_out;
        $status = $request->status;
        $notes = $request->notes;

        // Check if attendance already exists for this date
        $existingAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already exists for this date. Use edit instead.'
            ], 400);
        }

        // Get employee's schedule for that date
        $employeeSchedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->whereDate('schedule_start_date', '<=', $date)
            ->whereDate('schedule_end_date', '>=', $date)
            ->select('schedule.*', 'shifts.*')
            ->first();

        $now = now();
        
        // Prepare attendance data
        $attendanceData = [
            'employee_id' => $employeeId,
            'date' => $date,
            'punch_in' => $date . ' ' . $punchInTime,
            'status' => $status,
            'notes' => $notes,
            'created_by' => auth()->id() ?? Session::get('user_id'),
            'created_at' => $now,
            'updated_at' => $now
        ];

        // Add schedule information if available
        if ($employeeSchedule) {
            $attendanceData['schedule_id'] = $employeeSchedule->id;
            $attendanceData['shift_id'] = $employeeSchedule->shift_id;
            $attendanceData['scheduled_start'] = $employeeSchedule->start_time;
            $attendanceData['scheduled_end'] = $employeeSchedule->end_time;
            $attendanceData['allocated_break_time'] = $employeeSchedule->break_time ?? 0;
        }

        // If punch out is provided, calculate working hours
        if ($punchOutTime) {
            $attendanceData['punch_out'] = $date . ' ' . $punchOutTime;
            
            // Calculate working hours
            $punchIn = Carbon::parse($attendanceData['punch_in']);
            $punchOut = Carbon::parse($attendanceData['punch_out']);
            $totalMinutes = $punchIn->diffInMinutes($punchOut);
            $breakTime = $employeeSchedule->break_time ?? 0;
            $actualWorkingMinutes = max(0, $totalMinutes - $breakTime);
            
            $attendanceData['working_hours'] = $actualWorkingMinutes / 60;
            $attendanceData['actual_working_minutes'] = $actualWorkingMinutes;
            
            // Calculate overtime if schedule exists
            if ($employeeSchedule) {
                $scheduledStart = Carbon::parse($employeeSchedule->start_time);
                $scheduledEnd = Carbon::parse($employeeSchedule->end_time);
                $scheduledMinutes = $scheduledStart->diffInMinutes($scheduledEnd) - ($employeeSchedule->break_time ?? 0);
                
                $overtimeMinutes = max(0, $actualWorkingMinutes - $scheduledMinutes);
                $attendanceData['overtime_minutes'] = $overtimeMinutes;
                $attendanceData['overtime_hours'] = $overtimeMinutes / 60;
                
                if ($overtimeMinutes > 0) {
                    $this->calculateAndStoreOvertime($employeeId, $date, $overtimeMinutes / 60, $employeeSchedule);
                }
            }
        }

        // Insert attendance record
        $attendanceId = DB::table('attendances')->insertGetId($attendanceData);

        // Log the manual attendance marking
        $this->logManualAttendance($employeeId, $date, $attendanceId);

        return response()->json([
            'success' => true,
            'message' => 'Manual attendance marked successfully',
            'attendance_id' => $attendanceId
        ]);

    } catch (\Exception $e) {
        \Log::error('Mark manual attendance error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to mark attendance: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Log manual attendance marking
 */
private function logManualAttendance($employeeId, $date, $attendanceId)
{
    DB::table('attendance_processing_logs')->insert([
        'employee_id' => $employeeId,
        'attendance_id' => $attendanceId,
        'processed_by' => auth()->id() ?? Session::get('user_id'),
        'action_type' => 'manual_marking',
        'date' => $date,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}

/**
 * Calculate and store overtime records
 */
private function calculateAndStoreOvertime($employeeId, $date, $overtimeHours, $schedule)
{
    DB::table('overtime_records')->insert([
        'employee_id' => $employeeId,
        'date' => $date,
        'schedule_id' => $schedule->id ?? null,
        'shift_id' => $schedule->shift_id ?? null,
        'overtime_hours' => $overtimeHours,
        'status' => 'approved',
        'approved_by' => auth()->id() ?? Session::get('user_id'),
        'approved_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
}
