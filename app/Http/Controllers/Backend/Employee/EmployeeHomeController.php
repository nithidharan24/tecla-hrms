<?php
namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Attendance\AttendanceController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeHomeController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        // Create instance of AttendanceController
        $attendanceController = new AttendanceController();
        
        // Get attendance data for today
        $today = now()->format('Y-m-d');
        
        // Get today's attendance records
        $todayAttendance = \DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'asc')
            ->get();

        // Get current schedule with shift details including days_of_week
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        
        // Check if user has already punched in today
        $hasPunchedIn = $todayAttendance->isNotEmpty() && 
                       is_null($todayAttendance->last()->punch_out);
        
        // Get break status
        $breakStatus = $this->getCurrentBreakStatus($employeeId, $today);
        
        // Check attendance restrictions
        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        
        // Check active permissions
        $hasActivePermission = $this->hasActivePermission($employeeId, $today);
        
        // Calculate today's stats
        $todayStats = $this->calculateDailyStats($todayAttendance, $currentSchedule);
        $feeds = $this->getEmployeeFeeds($employeeId);

        // Get reporting manager
        $manager = $this->getReportingManager($employeeId);
        $employee = $this->getEmployeeFullData($employeeId);
        $profileExtras = $this->getEmployeeProfileExtras($employeeId);
        $leaveSummary = $this->getEmployeeLeaveSummary($employeeId);
        $month = $request->get('month', now()->format('Y-m'));

        // Get calendar data with shift days applied
        $calendarData = $this->getAttendanceCalendarData($employeeId, $month);
        
        // Get working days info for the current schedule
        $workingDays = [];
        if ($currentSchedule && isset($currentSchedule->days_of_week)) {
            $workingDays = array_map('trim', explode(',', $currentSchedule->days_of_week));
        }
        
        $tasks = DB::table('tasks')
            ->where('assigned_to', $employeeId)
            ->where('deleted_at', 0)
            ->orderBy('due_date', 'asc')
            ->select(
                'id',
                'projects',
                'task',
                'priority',
                'status',
                'due_date'
            )
            ->get();

        return view('hrms.Employee.Home.index', array_merge([
            'todayAttendance' => $todayAttendance,
            'todayStats' => $todayStats,
            'currentSchedule' => $currentSchedule,
            'workingDays' => $workingDays,
            'hasPunchedIn' => $hasPunchedIn,
            'isRestrictedDay' => $restriction['restricted'] ?? false,
            'restrictionReason' => $restriction['reason'] ?? null,
            'breakStatus' => $breakStatus,
            'hasActivePermission' => $hasActivePermission,
            'feeds' => $feeds,
            'employee' => $employee,
            'manager' => $manager,
            'calendarData' => $calendarData,
            'month' => $month,
            'tasks' => $tasks,
            'leaveInfo'     => $leaveSummary['leaveInfo'],
            'leaveBalances' => $leaveSummary['leaveBalances'],
            'leaveBalance'  => $leaveSummary['leaveBalance'],
        ], $profileExtras));
    }

    /**
     * Get current schedule for employee with proper day checking
     */
    private function getCurrentScheduleForEmployee($employeeId, $date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = strtolower($carbonDate->format('l')); // Get full day name (monday, tuesday, etc.)
        $dayShort = strtolower(substr($dayOfWeek, 0, 3)); // mon, tue, wed, etc.
        $dayNumber = $carbonDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        
        // Get all potential schedules for this employee
        $schedules = \DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->join('department', 'schedule.department_id', '=', 'department.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.schedule_start_date', '<=', $date)
            ->where(function($query) use ($date) {
                $query->where(function($q) use ($date) {
                    $q->where('schedule.repeat_every_week', '>', 0);
                })->orWhere(function($q) use ($date) {
                    $q->where('schedule.repeat_every_week', 0)
                      ->where('schedule.schedule_end_date', '>=', $date);
                });
            })
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'shifts.days_of_week',
                'department.department'
            )
            ->orderBy('schedule.repeat_every_week', 'desc')
            ->orderBy('schedule.schedule_start_date', 'desc')
            ->get();

        // Find the first schedule where the current day is in days_of_week
        foreach ($schedules as $schedule) {
            if (empty($schedule->days_of_week)) {
                continue;
            }

            $workingDays = array_map('strtolower', 
                array_map('trim', 
                    explode(',', $schedule->days_of_week)
                )
            );

            // Check if current day matches any working day format
            foreach ($workingDays as $workingDay) {
                if ($workingDay === $dayOfWeek || 
                    $workingDay === $dayShort || 
                    $workingDay === (string)$dayNumber ||
                    ($dayNumber == 0 && in_array($workingDay, ['sunday', 'sun', '0'])) ||
                    ($dayNumber == 6 && in_array($workingDay, ['saturday', 'sat', '6']))) {
                    return $schedule;
                }
            }
        }

        // If no schedule found for today, return the first schedule (for fallback)
        return $schedules->first();
    }

    /**
     * Get attendance calendar data with shift days properly applied
     */
    private function getAttendanceCalendarData($employeeId, $month)
    {
        $start = Carbon::parse($month)->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end   = Carbon::parse($month)->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendar = [];

        /** 1️⃣ Holidays */
        $holidays = DB::table('holidays')
            ->whereBetween('holidaydate', [$start, $end])
            ->get()
            ->keyBy('holidaydate');

        /** 2️⃣ Leaves */
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $end)
            ->whereDate('to_date', '>=', $start)
            ->get();

        /** 3️⃣ Schedules with shift details */
        $schedules = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->whereDate('schedule.schedule_start_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->where('schedule.repeat_every_week', 1)
                  ->orWhereDate('schedule.schedule_end_date', '>=', $start);
            })
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.days_of_week'
            )
            ->orderBy('schedule.repeat_every_week', 'desc')
            ->orderBy('schedule.schedule_start_date', 'desc')
            ->get();

        /** Loop each day */
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $d = $date->format('Y-m-d');
            $dayOfWeek = strtolower($date->format('l')); // Full day name
            $dayShort = strtolower(substr($dayOfWeek, 0, 3)); // Short name
            $dayNumber = $date->dayOfWeek; // 0-6

            /** Holiday */
            if (isset($holidays[$d])) {
                $calendar[$d] = [
                    'type' => 'holiday',
                    'title' => $holidays[$d]->title,
                    'is_working_day' => false
                ];
                continue;
            }

            /** Leave */
            $onLeave = false;
            foreach ($leaves as $leave) {
                if ($date->between(
                    Carbon::parse($leave->from_date),
                    Carbon::parse($leave->to_date)
                )) {
                    $calendar[$d] = [
                        'type' => 'leave',
                        'leave_type' => $leave->leave_type,
                        'is_working_day' => true
                    ];
                    $onLeave = true;
                    break;
                }
            }
            
            if ($onLeave) {
                continue;
            }

            /** Check if it's a working day based on schedule */
            $isWorkingDay = false;
            $shiftInfo = null;

            foreach ($schedules as $schedule) {
                // Check if this schedule applies to current date
                $scheduleStart = Carbon::parse($schedule->schedule_start_date);
                
                if ($schedule->repeat_every_week == 1) {
                    // For weekly repeating schedules
                    if ($date->gte($scheduleStart)) {
                        // Check if current day is in working days
                        $workingDays = array_map('strtolower', 
                            array_map('trim', 
                                explode(',', $schedule->days_of_week ?? '')
                            )
                        );
                        
                        // Check all possible day formats
                        foreach ($workingDays as $workingDay) {
                            if ($workingDay === $dayOfWeek || 
                                $workingDay === $dayShort || 
                                $workingDay === (string)$dayNumber ||
                                ($dayNumber == 0 && in_array($workingDay, ['sunday', 'sun', '0'])) ||
                                ($dayNumber == 6 && in_array($workingDay, ['saturday', 'sat', '6']))) {
                                $isWorkingDay = true;
                                $shiftInfo = $schedule;
                                break 2;
                            }
                        }
                    }
                } else {
                    // For fixed date range schedules
                    if ($date->between(
                        $scheduleStart,
                        Carbon::parse($schedule->schedule_end_date)
                    )) {
                        $isWorkingDay = true;
                        $shiftInfo = $schedule;
                        break;
                    }
                }
            }

            // Set calendar entry based on working day status
            if ($isWorkingDay && $shiftInfo) {
                $calendar[$d] = [
                    'type' => 'shift',
                    'shift_name' => $shiftInfo->shift_name,
                    'start' => Carbon::parse($shiftInfo->start_time)->format('h:i A'),
                    'end' => Carbon::parse($shiftInfo->end_time)->format('h:i A'),
                    'is_working_day' => true
                ];
            } else {
                // Not a working day
                $calendar[$d] = [
                    'type' => 'off',
                    'title' => 'Day Off',
                    'is_working_day' => false
                ];
            }
        }

        return $calendar;
    }

    /**
     * Check if a specific day is in the shift's working days
     */
    private function isWorkingDay($daysOfWeek, $date)
    {
        if (empty($daysOfWeek)) {
            return false;
        }
        
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = strtolower($carbonDate->format('l')); // Full day name
        $dayShort = strtolower(substr($dayOfWeek, 0, 3)); // Short name
        $dayNumber = $carbonDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        
        $workingDays = array_map('strtolower', 
            array_map('trim', 
                explode(',', $daysOfWeek)
            )
        );
        
        foreach ($workingDays as $workingDay) {
            if ($workingDay === $dayOfWeek || 
                $workingDay === $dayShort || 
                $workingDay === (string)$dayNumber ||
                ($dayNumber == 0 && in_array($workingDay, ['sunday', 'sun', '0'])) ||
                ($dayNumber == 6 && in_array($workingDay, ['saturday', 'sat', '6']))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check attendance restriction
     */
    private function checkAttendanceRestriction($employeeId, $date)
    {
        // First check if it's a working day based on schedule
        $schedule = $this->getCurrentScheduleForEmployee($employeeId, $date);
        
        if (!$schedule) {
            return [
                'restricted' => true,
                'reason' => 'No schedule assigned for this day'
            ];
        }
        
        // Check if today is a working day based on shift days
        if (!$this->isWorkingDay($schedule->days_of_week, $date)) {
            return [
                'restricted' => true,
                'reason' => 'Not a scheduled working day'
            ];
        }

        // Check if employee is on approved leave
        $onLeave = \DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();

        if ($onLeave) {
            return [
                'restricted' => true,
                'reason' => 'On approved leave (' . $onLeave->leave_type . ')'
            ];
        }

        $carbonDate = \Carbon\Carbon::parse($date);
        
        // Check if it's a holiday
        $holiday = \DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->first();
            
        if ($holiday) {
            return [
                'restricted' => true,
                'reason' => 'Holiday: ' . $holiday->title
            ];
        }

        return [
            'restricted' => false,
            'reason' => null
        ];
    }

    /**
     * Check active permission
     */
    private function hasActivePermission($employeeId, $date)
    {
        $now = now()->setTimezone('Asia/Kolkata');
        $currentTime = $now->format('H:i:s');
        
        $activePermission = \DB::table('employee_permissions')
            ->where('employee_id', $employeeId)
            ->where('permission_date', $date)
            ->where('status', 'approved')
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();

        if ($activePermission) {
            $startTime = \Carbon\Carbon::parse($activePermission->start_time)->format('h:i A');
            $endTime = \Carbon\Carbon::parse($activePermission->end_time)->format('h:i A');
            $duration = $activePermission->duration;
            
            return [
                'has_permission' => true,
                'permission' => $activePermission,
                'message' => "You have approved permission from {$startTime} to {$endTime} ({$duration} hours).",
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $duration,
                'reason' => $activePermission->permission_reason
            ];
        }

        return [
            'has_permission' => false,
            'permission' => null,
            'message' => null
        ];
    }

    /**
     * Get current break status
     */
    private function getCurrentBreakStatus($employeeId, $date)
    {
        $attendance = \DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return [
                'can_take_break' => false,
                'is_on_break' => false,
                'allocated_break_time' => 0,
                'total_break_taken' => 0,
                'remaining_break_time' => 0,
                'break_sessions' => [],
                'current_break_start' => null
            ];
        }

        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $date);
        $allocatedBreakTime = $currentSchedule ? $currentSchedule->break_time : 60;
        
        $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);
        
        $completedBreakTime = 0;
        $activeBreak = null;
        $currentBreakDuration = 0;
        
        foreach ($breakSessions as $session) {
            if (!isset($session['end_time']) || $session['end_time'] === null) {
                $activeBreak = $session;
                $breakStart = \Carbon\Carbon::parse($session['start_time'], 'Asia/Kolkata');
                $now = \Carbon\Carbon::now('Asia/Kolkata');
                $currentBreakDuration = $breakStart->diffInMinutes($now);
            } else {
                if (isset($session['duration'])) {
                    $completedBreakTime += $session['duration'];
                }
            }
        }
        
        $totalBreakTaken = $completedBreakTime + $currentBreakDuration;
        $remainingBreakTime = max(0, $allocatedBreakTime - $totalBreakTaken);

        return [
            'can_take_break' => $remainingBreakTime > 0 && !$activeBreak,
            'is_on_break' => !is_null($activeBreak),
            'allocated_break_time' => $allocatedBreakTime,
            'total_break_taken' => $totalBreakTaken,
            'remaining_break_time' => $remainingBreakTime,
            'current_break_start' => $activeBreak ? $activeBreak['start_time'] : null
        ];
    }

    /**
     * Calculate daily stats
     */
    private function calculateDailyStats($attendanceRecords, $currentSchedule = null)
    {
        $stats = [
            'total_hours' => 0,
            'scheduled_hours' => 0,
        ];

        if ($currentSchedule) {
            $start = \Carbon\Carbon::parse($currentSchedule->start_time);
            $end = \Carbon\Carbon::parse($currentSchedule->end_time);
            $breakTime = $currentSchedule->break_time ?? 0;
            $stats['scheduled_hours'] = ($start->diffInMinutes($end) - $breakTime) / 60;
        }

        foreach ($attendanceRecords as $record) {
            if ($record->punch_out) {
                $stats['total_hours'] += $record->working_hours ?? 0;
            } else {
                // For active session, calculate hours from punch_in to now
                $punchIn = \Carbon\Carbon::parse($record->punch_in);
                $now = now();
                $totalMinutes = $punchIn->diffInMinutes($now);
                $totalBreakTaken = $record->total_break_taken ?? 0;
                $stats['total_hours'] = max(0, $totalMinutes - $totalBreakTaken) / 60;
            }
        }

        return $stats;
    }

    /**
     * Get reporting manager
     */
    private function getReportingManager($employeeId)
    {
        $employee = \DB::table('allemployees')
            ->where('id', $employeeId)
            ->first();

        // Check if employee exists and has a manager_id
        if (!$employee || !$employee->manager_id) {
            return null;
        }

        return \DB::table('allemployees')
            ->where('id', $employee->manager_id)
            ->first();
    }

    /**
     * Get employee feeds (birthdays, anniversaries, wishes)
     */
    private function getEmployeeFeeds($employeeId)
    {
        $today = now()->format('m-d');

        // 🎂 Birthdays today
        $birthdays = \DB::table('employee_profile_main as ep')
            ->join('allemployees as e', 'e.id', '=', 'ep.employee_id')
            ->whereRaw("DATE_FORMAT(ep.birthday, '%m-%d') = ?", [$today])
            ->select('e.id', 'e.firstname', 'e.lastname', 'ep.birthday')
            ->get();

        // 💍 Work anniversaries today
        $anniversaries = \DB::table('employee_profile_main as ep')
            ->join('allemployees as e', 'e.id', '=', 'ep.employee_id')
            ->whereRaw("DATE_FORMAT(ep.date_of_joining, '%m-%d') = ?", [$today])
            ->select('e.id', 'e.firstname', 'e.lastname', 'ep.date_of_joining')
            ->get();

        // 💬 Wishes received by logged-in employee
        $wishes = \DB::table('employee_feed_posts as fp')
            ->leftJoin('allemployees as u', 'u.id', '=', 'fp.posted_by')
            ->where('fp.employee_id', $employeeId)
            ->orderBy('fp.created_at', 'desc')
            ->select(
                'fp.*',
                \DB::raw("CONCAT(u.firstname,' ',u.lastname) as sender_name")
            )
            ->get();

        // Convert created_at to Carbon instances
        foreach ($wishes as $wish) {
            $wish->created_at = \Carbon\Carbon::parse($wish->created_at);
        }

        return compact('birthdays', 'anniversaries', 'wishes');
    }

    /**
     * Send wish to employee
     */
    public function sendWish(Request $request)
    {
        \DB::table('employee_feed_posts')->insert([
            'employee_id' => $request->employee_id,
            'posted_by'   => Session::get('user_id'),
            'type'        => 'wish',
            'message'     => $request->message,
            'created_at'  => now()
        ]);

        return back()->with('success', 'Wish sent successfully!');
    }

    /**
     * Get employee full data with all related information
     */
    private function getEmployeeFullData($id)
    {
        return DB::table('allemployees')
            ->join('department', 'allemployees.department', '=', 'department.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
            ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
            ->leftJoin('employee_personal_informations', 'allemployees.id', '=', 'employee_personal_informations.employee_id')
            ->leftJoin('employee_emergency_contact', 'allemployees.id', '=', 'employee_emergency_contact.employee_id')
            ->leftJoin('employee_bank_informations', 'allemployees.id', '=', 'employee_bank_informations.employee_id')
            ->select(
                'allemployees.*',
                'employee_personal_informations.passport_no',
                'employee_personal_informations.passport_exp_date',
                'employee_personal_informations.tel',
                'employee_personal_informations.nationality',
                'employee_personal_informations.religion',
                'employee_personal_informations.marital_status',
                'employee_emergency_contact.primary_name',
                'employee_emergency_contact.relationship',
                'employee_emergency_contact.phone',
                'employee_emergency_contact.secondary_name',
                'employee_emergency_contact.secondary_relationship',
                'employee_emergency_contact.secondary_phone',
                'employee_bank_informations.bank_name',
                'employee_bank_informations.bank_account_no',
                'employee_bank_informations.ifsc_code',
                'employee_bank_informations.pan_no'
            )
            ->where('allemployees.id', $id)
            ->first();
    }

    /**
     * Get employee profile extras (family, education, experience)
     */
    private function getEmployeeProfileExtras($employeeId)
    {
        return [
            'familyMembers' => DB::table('employee_family_informations')
                ->where('employee_id', $employeeId)->get(),
            'family_info_edited' => DB::table('employee_family_informations')
                ->where('employee_id', $employeeId)->value('is_edited'),
            'educationInfos' => DB::table('employee_education_informations')
                ->where('employee_id', $employeeId)->get(),
            'education_info_edited' => DB::table('employee_education_informations')
                ->where('employee_id', $employeeId)->value('is_edited'),
            'experienceInfos' => DB::table('employee_experience_informations')
                ->where('employee_id', $employeeId)->get(),
            'experience_info_edited' => DB::table('employee_experience_informations')
                ->where('employee_id', $employeeId)->value('is_edited'),
        ];
    }

    /**
     * Get employee leave summary
     */
    private function getEmployeeLeaveSummary($employeeId)
    {
        $leaveInfo       = DB::table('employee_leave_information')->where('employee_id', $employeeId)->first();
        $annualSettings  = DB::table('annual_leaves')->first();
        $medicalSettings = DB::table('medical_leaves')->first();

        // Authoritative allocation per employee
        $defaultAllocation = [
            'Casual Leave'    => (int)($leaveInfo->casual_leaves          ?? $annualSettings->days             ?? 0),
            'Sick'            => (int)($leaveInfo->sick_leaves             ?? $medicalSettings->sick            ?? 0),
            'Hospitalisation' => (int)($leaveInfo->hospitalization_leaves ?? $medicalSettings->hospitalisation ?? 0),
            'Maternity Leave' => (int)($leaveInfo->maternity_leaves       ?? $medicalSettings->maternity       ?? 0),
            'Paternity Leave' => (int)($leaveInfo->paternity_leaves       ?? $medicalSettings->paternity       ?? 0),
        ];

        $existing = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->get()
            ->keyBy('leave_type');

        // Auto-create missing balance rows
        foreach ($defaultAllocation as $type => $allocDays) {
            if (!isset($existing[$type]) && $allocDays > 0) {
                DB::table('employee_leave_balances')->insert([
                    'employee_id'    => $employeeId,
                    'leave_type'     => $type,
                    'allocated_days' => $allocDays,
                    'used_days'      => 0,
                    'remaining_days' => $allocDays,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } elseif (isset($existing[$type]) && (int)$existing[$type]->allocated_days !== $allocDays && $allocDays > 0) {
                DB::table('employee_leave_balances')
                    ->where('employee_id', $employeeId)
                    ->where('leave_type', $type)
                    ->update(['allocated_days' => $allocDays, 'updated_at' => now()]);
            }
        }

        // Re-fetch after auto-create
        $leaveBalances = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->get()
            ->keyBy('leave_type');

        // Paid/LOP from employee_leaves (source of truth)
        $paidRows = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->select('leave_type', DB::raw('SUM(paid_days) as total_paid'), DB::raw('SUM(lop_days) as total_lop'))
            ->groupBy('leave_type')
            ->get()
            ->keyBy('leave_type');

        $leaveTypeMap = [
            'Casual Leave'    => 'Casual Leave',
            'Sick Leave'      => 'Sick',
            'Hospitalisation' => 'Hospitalisation',
            'Maternity Leave' => 'Maternity Leave',
            'Paternity Leave' => 'Paternity Leave',
        ];

        $leaveBalance = [];
        foreach ($leaveTypeMap as $display => $dbKey) {
            $bal       = $leaveBalances[$dbKey] ?? $leaveBalances[$display] ?? null;
            $allocated = $bal ? (int)$bal->allocated_days : ($defaultAllocation[$dbKey] ?? 0);
            $remaining = $bal ? (int)$bal->remaining_days : $allocated;
            $row       = $paidRows[$dbKey] ?? $paidRows[$display] ?? null;
            $paid      = $row ? (int)$row->total_paid : 0;
            $lop       = $row ? (int)$row->total_lop  : 0;
            $leaveBalance[$display] = [
                'allocated' => $allocated,
                'paid'      => $paid,
                'lop'       => $lop,
                'remaining' => $remaining,
            ];
        }

        return [
            'leaveInfo'     => $leaveInfo,
            'leaveBalances' => $leaveBalances,
            'leaveBalance'  => $leaveBalance,
        ];
    }
}