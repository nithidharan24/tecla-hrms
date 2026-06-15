<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AttendanceController extends Controller
{
   

    /**
     * Get current schedule for employee on given date with break time
     */
    /**
 * Get current schedule for employee on given date with break time
 */
/**
 * Get current schedule for employee on given date with break time
 */
private function getCurrentScheduleForEmployee($employeeId, $date)
{
    return DB::table('schedule')
        ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->join('department', 'schedule.department_id', '=', 'department.id')
        ->where('schedule.employee_id', $employeeId)
        ->where('schedule.deleted_at', 0)
        ->where('schedule.schedule_start_date', '<=', $date)
        ->where(function($query) use ($date) {
            // For weekly repeating schedules, ignore end date completely
            // For non-repeating schedules, check end date
            $query->where(function($q) use ($date) {
                $q->where('schedule.repeat_every_week', '>', 0);
            })->orWhere(function($q) use ($date) {
                $q->where('schedule.repeat_every_week', 0)
                  ->where('schedule.schedule_end_date', '>=', $date);
            });
        })
        // Remove the is_current check as it might be preventing weekly repeating schedules from being found
        ->select(
            'schedule.*',
            'shifts.shift_name',
            'shifts.start_time',
            'shifts.end_time',
            'shifts.break_time',
            'shifts.days_of_week',
            'department.department'
        )
        ->orderBy('schedule.repeat_every_week', 'desc') // Prioritize weekly repeating schedules
        ->orderBy('schedule.schedule_start_date', 'desc') // Get the most recent schedule
        ->first();
}
    /**
     * Check if employee has active permission during current time
     */
    private function hasActivePermission($employeeId, $date)
    {
        $now = now()->setTimezone('Asia/Kolkata');
        $currentTime = $now->format('H:i:s');
        
        // Check for approved permissions for today
        $activePermission = DB::table('employee_permissions')
            ->where('employee_id', $employeeId)
            ->where('permission_date', $date)
            ->where('status', 'approved')
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();

        if ($activePermission) {
            $startTime = Carbon::parse($activePermission->start_time)->format('h:i A');
            $endTime = Carbon::parse($activePermission->end_time)->format('h:i A');
            $duration = $activePermission->duration;
            
            return [
                'has_permission' => true,
                'permission' => $activePermission,
                'message' => "You have approved permission from {$startTime} to {$endTime} ({$duration} hours). Punch in/out is not allowed during this time.",
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
     * Check if attendance is restricted for employee on given date
     */
   /**
 * Check if attendance is restricted for employee on given date
 */
/**
 * Check if attendance is restricted for employee on given date
 */
private function checkAttendanceRestriction($employeeId, $date)
{
    // Check if employee is on approved leave
    $onLeave = DB::table('employee_leaves')
        ->where('employee_id', $employeeId)
        ->where('status', 'approved')
        ->where('from_date', '<=', $date)
        ->where('to_date', '>=', $date)
        ->first();

    if ($onLeave) {
        return [
            'restricted' => true,
            'reason' => 'On approved leave (' . $onLeave->leave_type . ') from ' .
                        Carbon::parse($onLeave->from_date)->format('M d') . ' to ' .
                        Carbon::parse($onLeave->to_date)->format('M d'),
            'type' => 'on_leave'
        ];
    }

    $carbonDate = Carbon::parse($date);
    
    // Check if it's a holiday
    $holiday = DB::table('holidays')
        ->whereDate('holidaydate', $date)
        ->first();
        
    if ($holiday) {
        return [
            'restricted' => true,
            'reason' => 'Holiday: ' . $holiday->title,
            'type' => 'holiday'
        ];
    }
    
    // Get employee's current schedule
    $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $date);
    
    if (!$currentSchedule) {
        return [
            'restricted' => true,
            'reason' => 'No active schedule found for today',
            'type' => 'no_schedule'
        ];
    }
    
    // For weekly repeating schedules, we only check if start date has passed
    // End date is completely ignored for weekly repeating schedules
    $scheduleStart = Carbon::parse($currentSchedule->schedule_start_date);
    if ($carbonDate->lt($scheduleStart)) {
        return [
            'restricted' => true,
            'reason' => 'Schedule starts on ' . $scheduleStart->format('M d, Y'),
            'type' => 'schedule_not_started'
        ];
    }
    
    // Check working days
    $dayOfWeekFull = $carbonDate->format('l');
    $dayOfWeekShort = $carbonDate->format('D');
    $dayOfWeekNumber = $carbonDate->format('N');
    
    $workingDaysString = $currentSchedule->days_of_week;
    
    if (empty($workingDaysString)) {
        return [
            'restricted' => true,
            'reason' => 'No working days defined for your shift (' . $currentSchedule->shift_name . ')',
            'type' => 'no_working_days'
        ];
    }
    
    $workingDays = array_map('trim', explode(',', $workingDaysString));
    $isWorkingDay = false;
    
    foreach ($workingDays as $workingDay) {
        $workingDay = trim($workingDay);
        
        if (strtolower($workingDay) === strtolower($dayOfWeekFull) ||
            strtolower($workingDay) === strtolower($dayOfWeekShort) ||
            (is_numeric($workingDay) && intval($workingDay) == $dayOfWeekNumber) ||
            ($dayOfWeekNumber == 7 && (intval($workingDay) == 0 || intval($workingDay) == 7))) {
            $isWorkingDay = true;
            break;
        }
    }
    
    if (!$isWorkingDay) {
        return [
            'restricted' => true,
            'reason' => 'Not a working day according to your shift schedule (' . $currentSchedule->shift_name . '). Working days: ' . $workingDaysString,
            'type' => 'non_working_day'
        ];
    }
    
    return [
        'restricted' => false,
        'reason' => null,
        'type' => null
    ];
}
    public function punchIn(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        $now = now()->setTimezone('Asia/Kolkata');
        $today = $now->format('Y-m-d');

        // Check attendance restrictions
        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        if ($restriction['restricted']) {
            return redirect()->back()->with('error', 'Punch in is not allowed today. Reason: ' . $restriction['reason']);
        }

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return redirect()->back()->with('error', 
                'Cannot punch in during permission hours. ' . $permissionCheck['message']);
        }

        // Check if already punched in today
        $lastPunch = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'desc')
            ->first();

        if ($lastPunch && !$lastPunch->punch_out) {
            return redirect()->back()->with('error', 'You have already punched in today!');
        }

        // Get current schedule for validation
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        
        // Check if punch in is within allowed time
        $punchInValidation = $this->validatePunchInTime($now, $currentSchedule);
        
        // Create new attendance record with break tracking
        $attendanceData = [
            'employee_id' => $employeeId,
            'date' => $today,
            'punch_in' => $now,
            'total_break_taken' => 0,
            'break_sessions' => json_encode([]),
            'created_at' => $now,
            'updated_at' => $now
        ];

        // Add schedule and shift info if available
        if ($currentSchedule) {
            $attendanceData['schedule_id'] = $currentSchedule->id;
            $attendanceData['shift_id'] = $currentSchedule->shift_id;
            $attendanceData['scheduled_start'] = $currentSchedule->start_time;
            $attendanceData['scheduled_end'] = $currentSchedule->end_time;
            $attendanceData['allocated_break_time'] = $currentSchedule->break_time;
        }

        if (isset($punchInValidation['status'])) {
            $attendanceData['status'] = $punchInValidation['status'];
        }

        $attendanceId = DB::table('attendances')->insertGetId($attendanceData);

        $this->logAttendanceActivity($employeeId, 'punch_in', $attendanceId, $punchInValidation);

        $message = 'Punched in successfully at ' . $now->format('h:i A');
        if (isset($punchInValidation['message'])) {
            $message .= ' (' . $punchInValidation['message'] . ')';
        }

        return redirect()->back()->with('success', $message);
    }

    public function punchOut(Request $request)
    {
        $employeeId = Session::get('user_id');
        $now = now()->setTimezone('Asia/Kolkata');
        $today = $now->format('Y-m-d');
    
        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        if ($restriction['restricted']) {
            return redirect()->back()->with('error', 'Punch out is not allowed today. Reason: ' . $restriction['reason']);
        }

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return redirect()->back()->with('error', 
                'Cannot punch out during permission hours. ' . $permissionCheck['message']);
        }
    
        $lastPunch = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->orderBy('punch_in', 'desc')
            ->first();
    
        if (!$lastPunch) {
            return redirect()->back()->with('error', 'No active punch in found!');
        }
    
        $breakSessions = json_decode($lastPunch->break_sessions ?? '[]', true);
        $activeBreak = collect($breakSessions)->where('end_time', null)->first();
    
        if ($activeBreak) {
            return redirect()->back()->with('error', 'Please end your current break before punching out!');
        }
    
        $punchIn = Carbon::parse($lastPunch->punch_in, 'Asia/Kolkata');
        $totalMinutes = $punchIn->diffInMinutes($now);
    
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        $allocatedBreakTime = $currentSchedule ? $currentSchedule->break_time : 0;
        $totalBreakTaken = $lastPunch->total_break_taken ?? 0;
        $actualWorkingMinutes = $totalMinutes - $totalBreakTaken;
        $workingHours = max(0, $actualWorkingMinutes) / 60;
    
        // Calculate scheduled working minutes
        $scheduledMinutes = 0;
        if ($currentSchedule) {
            $scheduledStart = Carbon::parse($currentSchedule->start_time);
            $scheduledEnd = Carbon::parse($currentSchedule->end_time);
            $scheduledMinutes = $scheduledStart->diffInMinutes($scheduledEnd) - $allocatedBreakTime;
        }
    
        // Overtime calculation
        $overtimeMinutes = max(0, $actualWorkingMinutes - $scheduledMinutes);
        $overtimeHours = floor($overtimeMinutes / 60);
        $overtimeMinutes = $overtimeHours > 0 ? $overtimeHours * 60 : 0;
    
        $punchOutValidation = $this->validatePunchOutTime($punchIn, $now, $currentSchedule);
    
        $updateData = [
            'punch_out' => $now,
            'working_hours' => $workingHours,
            'actual_working_minutes' => $actualWorkingMinutes,
            'total_break_taken' => $totalBreakTaken,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_hours' => $overtimeHours,
            'updated_at' => $now
        ];
    
        if (isset($punchOutValidation['status'])) {
            $updateData['status'] = $punchOutValidation['status'];
        }
    
        if ($currentSchedule) {
            $updateData['scheduled_hours'] = $scheduledMinutes / 60;
            $updateData['undertime_minutes'] = max(0, $scheduledMinutes - $actualWorkingMinutes);
        }
    
        DB::table('attendances')
            ->where('id', $lastPunch->id)
            ->update($updateData);
    
        if ($overtimeHours > 0) {
            $this->calculateAndStoreOvertime($employeeId, $today, $overtimeHours, $currentSchedule);
        }
    
        $this->logAttendanceActivity($employeeId, 'punch_out', $lastPunch->id, $punchOutValidation);
    
        $message = 'Punched out successfully at ' . $now->format('h:i A');
        if ($overtimeHours > 0) {
            $message .= " (Overtime: {$overtimeHours} hours)";
        }
        if (isset($punchOutValidation['message'])) {
            $message .= ' (' . $punchOutValidation['message'] . ')';
        }
    
        return redirect()->back()->with('success', $message);
    }

    /**
     * Start break session
     */
    public function startBreak(Request $request)
    {
        $employeeId = Session::get('user_id');
        $today = now()->format('Y-m-d');
        $now = now()->setTimezone('Asia/Kolkata');

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return redirect()->back()->with('error', 
                'Cannot start break during permission hours. ' . $permissionCheck['message']);
        }

        // Get current attendance record
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active attendance session found!');
        }

        // Get current schedule for break time limit
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        $allocatedBreakTime = $currentSchedule ? $currentSchedule->break_time : 60;

        // Check if already on break
        $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);
        $activeBreak = null;
        
        foreach ($breakSessions as $session) {
            if (!isset($session['end_time']) || $session['end_time'] === null) {
                $activeBreak = $session;
                break;
            }
        }
        
        if ($activeBreak) {
            return redirect()->back()->with('error', 'You are already on break!');
        }

        // Calculate total completed break time
        $completedBreakTime = 0;
        foreach ($breakSessions as $session) {
            if (isset($session['end_time']) && $session['end_time'] !== null && isset($session['duration'])) {
                $completedBreakTime += $session['duration'];
            }
        }

        $remainingBreakTime = $allocatedBreakTime - $completedBreakTime;
        
        if ($remainingBreakTime <= 0) {
            return redirect()->back()->with('error', 'You have exhausted your break time for today!');
        }

        // Start new break session with proper timestamp
        $breakSessions[] = [
            'start_time' => $now->format('Y-m-d H:i:s'),
            'end_time' => null,
            'duration' => 0,
            'session_id' => uniqid('break_', true)
        ];

        DB::table('attendances')
            ->where('id', $attendance->id)
            ->update([
                'break_sessions' => json_encode($breakSessions),
                'updated_at' => $now
            ]);

        $this->logAttendanceActivity($employeeId, 'break_start', $attendance->id);

        return redirect()->back()->with('success', 'Break started at ' . $now->format('h:i A') . '. Remaining break time: ' . $remainingBreakTime . ' minutes');
    }

    /**
     * End break session
     */
    public function endBreak(Request $request)
    {
        $employeeId = Session::get('user_id');
        $today = now()->format('Y-m-d');
        $now = now()->setTimezone('Asia/Kolkata');

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return redirect()->back()->with('error', 
                'Cannot end break during permission hours. ' . $permissionCheck['message']);
        }

        // Get current attendance record
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active attendance session found!');
        }

        // Get break sessions
        $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);
        $activeBreakIndex = null;
        
        foreach ($breakSessions as $index => $session) {
            if (!isset($session['end_time']) || $session['end_time'] === null) {
                $activeBreakIndex = $index;
                break;
            }
        }

        if ($activeBreakIndex === null) {
            return redirect()->back()->with('error', 'No active break session found!');
        }

        // Calculate break duration with proper timezone handling
        $breakStart = Carbon::parse($breakSessions[$activeBreakIndex]['start_time'], 'Asia/Kolkata');
        $breakEnd = $now;
        
        // Ensure we're comparing times in the same timezone
        $breakDuration = $breakStart->diffInMinutes($breakEnd);
        
        // Minimum 1 minute if there's any time difference
        if ($breakDuration < 1 && !$breakStart->equalTo($breakEnd)) {
            $breakDuration = 1;
        }

        // Update the break session
        $breakSessions[$activeBreakIndex]['end_time'] = $now->format('Y-m-d H:i:s');
        $breakSessions[$activeBreakIndex]['duration'] = $breakDuration;

        // Calculate total break time taken from all completed sessions
        $totalBreakTaken = 0;
        foreach ($breakSessions as $session) {
            if (isset($session['end_time']) && $session['end_time'] !== null && isset($session['duration'])) {
                $totalBreakTaken += $session['duration'];
            }
        }

        DB::table('attendances')
            ->where('id', $attendance->id)
            ->update([
                'break_sessions' => json_encode($breakSessions),
                'total_break_taken' => $totalBreakTaken,
                'updated_at' => $now
            ]);

        $this->logAttendanceActivity($employeeId, 'break_end', $attendance->id);

        return redirect()->back()->with('success', 
            'Break ended at ' . $now->format('h:i A') . ' (Duration: ' . $breakDuration . ' minutes)');
    }

    /**
     * Get current break status
     */
    private function getCurrentBreakStatus($employeeId, $date)
    {
        $attendance = DB::table('attendances')
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
                'current_break_start' => null,
                'current_break_duration' => 0
            ];
        }

        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $date);
        $allocatedBreakTime = $currentSchedule ? $currentSchedule->break_time : 60;
        
        $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);
        
        // Calculate completed break time and find active break
        $completedBreakTime = 0;
        $activeBreak = null;
        $currentBreakDuration = 0;
        
        foreach ($breakSessions as $session) {
            if (!isset($session['end_time']) || $session['end_time'] === null) {
                // Active break session
                $activeBreak = $session;
                $breakStart = Carbon::parse($session['start_time'], 'Asia/Kolkata');
                $now = Carbon::now('Asia/Kolkata');
                $currentBreakDuration = $breakStart->diffInMinutes($now);
            } else {
                // Completed break session
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
            'completed_break_time' => $completedBreakTime,
            'current_break_duration' => $currentBreakDuration,
            'remaining_break_time' => $remainingBreakTime,
            'break_sessions' => $this->formatBreakSessions($breakSessions),
            'current_break_start' => $activeBreak ? $activeBreak['start_time'] : null,
            'current_break_start_formatted' => $activeBreak ? 
                Carbon::parse($activeBreak['start_time'], 'Asia/Kolkata')->format('h:i A') : null
        ];
    }

    /**
     * Format break sessions for display
     */
    private function formatBreakSessions($breakSessions)
    {
        $formatted = [];
        $now = Carbon::now('Asia/Kolkata');
        
        foreach ($breakSessions as $session) {
            $startTime = Carbon::parse($session['start_time'], 'Asia/Kolkata');
            $isActive = !isset($session['end_time']) || $session['end_time'] === null;
            
            if ($isActive) {
                // Calculate real-time duration for active session
                $duration = $startTime->diffInMinutes($now);
                $endTimeFormatted = 'Ongoing';
            } else {
                // Use stored duration for completed sessions
                $duration = $session['duration'] ?? 0;
                $endTime = Carbon::parse($session['end_time'], 'Asia/Kolkata');
                $endTimeFormatted = $endTime->format('h:i A');
            }
            
            $formatted[] = [
                'start_time' => $startTime->format('h:i A'),
                'end_time' => $endTimeFormatted,
                'duration' => $duration,
                'duration_formatted' => $this->formatBreakDuration($duration),
                'is_active' => $isActive
            ];
        }
        
        return $formatted;
    }

    /**
     * Get break status API endpoint for real-time updates
     */
    public function getBreakStatus()
    {
        $employeeId = Session::get('user_id');
        $today = now()->format('Y-m-d');
        $breakStatus = $this->getCurrentBreakStatus($employeeId, $today);
        
        return response()->json([
            'success' => true,
            'data' => $breakStatus,
            'current_time' => now()->setTimezone('Asia/Kolkata')->format('h:i A'),
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Calculate and store overtime
     */
    private function calculateAndStoreOvertime($employeeId, $date, $overtimeHours, $schedule = null)
    {
        if ($overtimeHours < 1) {
            return;
        }

        // Get employee hierarchy for overtime rate
        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->first();

        if (!$employee) {
            return;
        }

        // Get overtime rate based on hierarchy
        $overtimeRate = $this->getOvertimeRateByHierarchy($employee->hierarchy_id ?? null);

        if (!$overtimeRate) {
            return;
        }

        // Calculate overtime amount
        $overtimeAmount = $overtimeHours * $overtimeRate->rate;

        // Check if overtime record already exists for this date
        $existingOvertime = DB::table('employee_overtime')
            ->where('employee_id', $employeeId)
            ->where('overtime_date', $date)
            ->first();

        $overtimeData = [
            'employee_id' => $employeeId,
            'overtime_date' => $date,
            'overtime_hours' => $overtimeHours,
            'overtime_rate' => $overtimeRate->rate,
            'overtime_amount' => round($overtimeAmount, 2),
            'hierarchy_id' => $employee->hierarchy_id,
            'status' => 'pending',
            'updated_at' => now()
        ];

        if ($existingOvertime) {
            DB::table('employee_overtime')
                ->where('id', $existingOvertime->id)
                ->update($overtimeData);
        } else {
            $overtimeData['created_at'] = now();
            DB::table('employee_overtime')->insert($overtimeData);
        }
    }

    /**
     * Get overtime rate by hierarchy
     */
    private function getOvertimeRateByHierarchy($hierarchyId)
    {
        if (!$hierarchyId) {
            return DB::table('overtimes')
                ->whereNull('hierarchy_id')
                ->orWhere('hierarchy_id', 0)
                ->first();
        }

        return DB::table('overtimes')
            ->where('hierarchy_id', $hierarchyId)
            ->first();
    }

    /**
     * Get employee overtime records
     */
    private function getEmployeeOvertimeRecords($employeeId)
    {
        return DB::table('employee_overtime')
            ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
            ->where('employee_overtime.employee_id', $employeeId)
            ->whereMonth('employee_overtime.overtime_date', now()->month)
            ->whereYear('employee_overtime.overtime_date', now()->year)
            ->select(
                'employee_overtime.*',
                'approver.firstname as approver_firstname',
                'approver.lastname as approver_lastname'
            )
            ->orderBy('employee_overtime.overtime_date', 'desc')
            ->get();
    }

    /**
     * Validate punch in time
     */
    private function validatePunchInTime($punchInTime, $schedule)
    {
        if (!$schedule) {
            return ['status' => 'no_schedule', 'message' => 'No schedule found'];
        }

        $scheduledStart = Carbon::parse($schedule->start_time);
        $punchInTimeOnly = Carbon::parse($punchInTime->format('H:i:s'));
        
        $graceMinutes = 15;
        $lateThreshold = $scheduledStart->copy()->addMinutes($graceMinutes);
        
        if ($punchInTimeOnly->lte($scheduledStart)) {
            return ['status' => 'early', 'message' => 'Early arrival'];
        } elseif ($punchInTimeOnly->lte($lateThreshold)) {
            return ['status' => 'on_time', 'message' => 'On time'];
        } else {
            $minutesLate = $scheduledStart->diffInMinutes($punchInTimeOnly);
            return ['status' => 'late', 'message' => "Late by {$minutesLate} minutes"];
        }
    }

    /**
     * Validate punch out time
     */
    private function validatePunchOutTime($punchIn, $punchOut, $schedule)
    {
        if (!$schedule) {
            return ['status' => 'no_schedule', 'message' => 'No schedule found'];
        }

        $scheduledEnd = Carbon::parse($schedule->end_time);
        $punchOutTimeOnly = Carbon::parse($punchOut->format('H:i:s'));
        
        $scheduledStart = Carbon::parse($schedule->start_time);
        $expectedWorkingMinutes = $scheduledStart->diffInMinutes($scheduledEnd) - ($schedule->break_time ?? 0);
        $actualWorkingMinutes = $punchIn->diffInMinutes($punchOut) - ($schedule->break_time ?? 0);
        
        if ($punchOutTimeOnly->lt($scheduledEnd)) {
            $minutesEarly = $punchOutTimeOnly->diffInMinutes($scheduledEnd);
            return ['status' => 'early_departure', 'message' => "Left {$minutesEarly} minutes early"];
        } elseif ($actualWorkingMinutes >= $expectedWorkingMinutes) {
            return ['status' => 'completed', 'message' => 'Full day completed'];
        } else {
            $overtimeMinutes = $actualWorkingMinutes - $expectedWorkingMinutes;
            return ['status' => 'overtime', 'message' => "Overtime: {$overtimeMinutes} minutes"];
        }
    }

    /**
     * Log attendance activity
     */
    private function logAttendanceActivity($employeeId, $action, $attendanceId, $validation = null)
    {
        DB::table('attendance_logs')->insert([
            'employee_id' => $employeeId,
            'attendance_id' => $attendanceId,
            'action' => $action,
            'status' => $validation['status'] ?? null,
            'message' => $validation['message'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Calculate daily stats
     */
    public function calculateDailyStats($attendanceRecords, $currentSchedule = null)
    {
        $stats = [
            'total_hours' => 0,
            'last_punch_in' => null,
            'last_punch_out' => null,
            'scheduled_hours' => 0,
            'status' => 'not_started',
            'overtime_hours' => 0,
            'undertime_hours' => 0
        ];

        if ($currentSchedule) {
            $start = Carbon::parse($currentSchedule->start_time);
            $end = Carbon::parse($currentSchedule->end_time);
            $breakTime = $currentSchedule->break_time ?? 0;
            $stats['scheduled_hours'] = ($start->diffInMinutes($end) - $breakTime) / 60;
        }

        foreach ($attendanceRecords as $record) {
            if ($record->punch_out) {
                $stats['total_hours'] += $record->working_hours ?? 0;
                $stats['status'] = 'completed';
                
                if (isset($record->overtime_minutes)) {
                    $stats['overtime_hours'] += $record->overtime_minutes / 60;
                }
                if (isset($record->undertime_minutes)) {
                    $stats['undertime_hours'] += $record->undertime_minutes / 60;
                }
            } else {
                $stats['status'] = 'in_progress';
            }
            
            if ($record->punch_in) {
                $stats['last_punch_in'] = $record->punch_in;
            }
            if ($record->punch_out) {
                $stats['last_punch_out'] = $record->punch_out;
            }
        }

        return $stats;
    }

    /**
     * Get monthly stats
     */
    /**
     * Get monthly stats
     */
    private function getMonthlyStats($employeeId)
    {
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');
        
        $monthlyAttendance = DB::table('attendances')
            ->leftJoin('schedule', 'attendances.schedule_id', '=', 'schedule.id')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->where('attendances.employee_id', $employeeId)
            ->whereMonth('attendances.date', $currentMonth)
            ->whereYear('attendances.date', $currentYear)
            ->whereNotNull('attendances.punch_out')
            ->select('attendances.*', 'shifts.start_time', 'shifts.end_time', 'shifts.break_time')
            ->get();

        $stats = [
            'total_hours' => 0,
            'working_days' => 0,
            'scheduled_hours' => 0,
            'on_time_days' => 0,
            'late_days' => 0,
            'early_departures' => 0,
            'expected_monthly_hours' => 0,
            'remaining_hours' => 0,
            'expected_weekly_hours' => 48,
            'overtime_hours' => 0,
            'undertime_hours' => 0,
            'attendance_percentage' => 0
        ];

        foreach ($monthlyAttendance as $record) {
            $stats['total_hours'] += $record->working_hours ?? 0;
            
            if ($record->start_time && $record->end_time) {
                $start = Carbon::parse($record->start_time);
                $end = Carbon::parse($record->end_time);
                $breakTime = $record->break_time ?? 0;
                $stats['scheduled_hours'] += ($start->diffInMinutes($end) - $breakTime) / 60;
            }
            
            if (isset($record->overtime_minutes)) {
                $stats['overtime_hours'] += $record->overtime_minutes / 60;
            }
            if (isset($record->undertime_minutes)) {
                $stats['undertime_hours'] += $record->undertime_minutes / 60;
            }
            
            $status = $record->status ?? 'on_time';
            switch ($status) {
                case 'on_time':
                case 'early':
                    $stats['on_time_days']++;
                    break;
                case 'late':
                    $stats['late_days']++;
                    break;
                case 'early_departure':
                    $stats['early_departures']++;
                    break;
            }
        }
        
        $stats['working_days'] = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->whereNotNull('punch_out')
            ->distinct('date')
            ->count('date');

        $totalWorkingDaysInMonth = $this->getWorkingDaysInMonth($employeeId, $currentMonth, $currentYear);
        $stats['expected_monthly_hours'] = $totalWorkingDaysInMonth * 8;
        $stats['remaining_hours'] = max(0, $stats['expected_monthly_hours'] - $stats['total_hours']);
        $stats['attendance_percentage'] = $totalWorkingDaysInMonth > 0 ? 
            round(($stats['working_days'] / $totalWorkingDaysInMonth) * 100, 2) : 0;

        return $stats;
    }

    /**
     * Get working days in month
     */
    /**
 * Get working days in month
 */
private function getWorkingDaysInMonth($employeeId, $month, $year)
{
    $startDate = Carbon::create($year, $month, 1);
    $endDate = $startDate->copy()->endOfMonth();
    $workingDays = 0;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $restriction = $this->checkAttendanceRestriction($employeeId, $date->format('Y-m-d'));
        if (!$restriction['restricted']) {
            $workingDays++;
        }
    }

    return $workingDays;
}

    /**
     * Search attendance records
     */
    public function search(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        $today = now()->format('Y-m-d');
        $todayAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'asc')
            ->get();

        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        $todayStats = $this->calculateDailyStats($todayAttendance, $currentSchedule);
        $monthlyStats = $this->getMonthlyStats($employeeId);

        $query = DB::table('attendances')
            ->leftJoin('schedule', 'attendances.schedule_id', '=', 'schedule.id')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->where('attendances.employee_id', $employeeId)
            ->select(
                'attendances.*',
                'shifts.shift_name',
                'shifts.start_time as scheduled_start',
                'shifts.end_time as scheduled_end'
            );

        if ($request->date) {
            $query->whereDate('attendances.date', $request->date);
        }

        if ($request->month) {
            $query->whereMonth('attendances.date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('attendances.date', $request->year);
        }

        $searchResults = $query->orderBy('attendances.date', 'desc')
            ->orderBy('attendances.punch_in', 'asc')
            ->get();

        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        $isRestrictedDay = $restriction['restricted'];
        $restrictionReason = $restriction['reason'];

        $breakStatus = $this->getCurrentBreakStatus($employeeId, $today);
        $employeeOvertimeRecords = $this->getEmployeeOvertimeRecords($employeeId);

        return view('hrms.attendance.index', [
            'searchResults' => $searchResults,
            'todayAttendance' => $todayAttendance,
            'todayStats' => $todayStats,
            'monthlyStats' => $monthlyStats,
            'recentActivities' => $todayAttendance->take(6),
            'currentSchedule' => $currentSchedule,
            'isRestrictedDay' => $isRestrictedDay,
            'restrictionReason' => $restrictionReason,
            'breakStatus' => $breakStatus,
            'employeeOvertimeRecords' => $employeeOvertimeRecords,
            'controller' => $this
        ]);
    }

    /**
     * Format duration helper
     */
    public function formatDuration($hours)
    {
        $totalSeconds = $hours * 3600;
        
        if ($totalSeconds < 60) {
            return round($totalSeconds) . ' sec';
        }
        
        if ($totalSeconds < 3600) {
            $minutes = round($totalSeconds / 60);
            return $minutes . ' min';
        }
        
        return number_format($hours, 2) . ' hrs';
    }

    /**
     * Format break duration for display
     */
    private function formatBreakDuration($minutes)
    {
        if ($minutes < 1) {
            return '0 min';
        }
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes === 0) {
            return $hours . ' hr' . ($hours > 1 ? 's' : '');
        }
        
        return $hours . ' hr' . ($hours > 1 ? 's' : '') . ' ' . $remainingMinutes . ' min';
    }
// Add these methods to your AttendanceController

/**
 * Get attendance data for calendar view
 */
public function getCalendarData(Request $request)
{
    $employeeId = Session::get('user_id');
    $month = $request->month ?? now()->month;
    $year = $request->year ?? now()->year;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $attendanceRecords = DB::table('attendances')
        ->where('employee_id', $employeeId)
        ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->orderBy('date', 'asc')
        ->get();

    $calendarData = [];
    $currentDate = $startDate->copy();
    
    while ($currentDate->lte($endDate)) {
        $dateStr = $currentDate->format('Y-m-d');
        $record = $attendanceRecords->where('date', $dateStr)->first();
        
        // Check restrictions
        $restriction = $this->checkAttendanceRestriction($employeeId, $dateStr);
        
        // Get day status
        $status = $this->getDayStatus($record, $restriction, $currentDate);
        
        $calendarData[] = [
            'date' => $dateStr,
            'day' => $currentDate->format('d'),
            'day_name' => $currentDate->format('D'),
            'is_today' => $currentDate->isToday(),
            'record' => $record,
            'status' => $status,
            'restricted' => $restriction['restricted'],
            'restriction_reason' => $restriction['reason'],
            'punch_in' => $record->punch_in ?? null,
            'punch_out' => $record->punch_out ?? null,
            'working_hours' => $record->working_hours ?? 0,
            'overtime' => $record->overtime_hours ?? 0,
            'break_time' => $record->total_break_taken ?? 0,
        ];
        
        $currentDate->addDay();
    }

    return response()->json([
        'success' => true,
        'data' => $calendarData,
        'month' => $startDate->format('F Y'),
        'total_days' => count($calendarData)
    ]);
}

/**
 * Get day status for calendar
 */
private function getDayStatus($record, $restriction, $date)
{
    if ($restriction['restricted']) {
        switch ($restriction['type']) {
            case 'holiday':
                return 'holiday';
            case 'on_leave':
                return 'leave';
            case 'non_working_day':
            case 'weekend':
                return 'weekend';
            default:
                return 'restricted';
        }
    }

    if ($record) {
        if ($record->punch_out) {
            return 'completed';
        } elseif ($record->punch_in) {
            return 'in_progress';
        }
    }

    if ($date->isPast()) {
        return 'absent';
    }

    return 'upcoming';
}

/**
 * Get filtered attendance records
 */
public function getFilteredRecords(Request $request)
{
    $employeeId = Session::get('user_id');
    
    $query = DB::table('attendances')
        ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
        ->where('attendances.employee_id', $employeeId);

    // Date filters
    if ($request->has('date')) {
        $query->whereDate('attendances.date', $request->date);
    }
    
    if ($request->has('month')) {
        $query->whereMonth('attendances.date', $request->month);
    }
    
    if ($request->has('year')) {
        $query->whereYear('attendances.date', $request->year);
    }
    
    if ($request->has('status')) {
        if ($request->status === 'present') {
            $query->whereNotNull('attendances.punch_out');
        } elseif ($request->status === 'absent') {
            $query->whereNull('attendances.punch_in');
        } elseif ($request->status === 'late') {
            $query->where('attendances.status', 'late');
        }
    }

    $records = $query->select(
            'attendances.*',
            'shifts.shift_name',
            'shifts.start_time',
            'shifts.end_time'
        )
        ->orderBy('attendances.date', 'desc')
        ->orderBy('attendances.punch_in', 'asc')
        ->get();

    return response()->json([
        'success' => true,
        'records' => $records,
        'total' => $records->count()
    ]);
}
/**
 * Show manual attendance request form
 */
public function showManualRequestForm()
{
    $employeeId = Session::get('user_id');
    
    $pendingRequests = DB::table('manual_attendance_requests')
        ->where('employee_id', $employeeId)
        ->whereIn('status', ['pending', 'approved'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    $processedRequests = DB::table('manual_attendance_requests')
        ->where('employee_id', $employeeId)
        ->whereIn('status', ['processed', 'rejected'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    return view('hrms.attendance.manual-request', [
        'pendingManualRequests' => $pendingRequests,
        'processedManualRequests' => $processedRequests
    ]);
}

/**
 * Submit manual attendance request
 */
public function submitManualRequest(Request $request)
{
    $employeeId = Session::get('user_id');
    
    $request->validate([
        'date' => 'required|date|before_or_equal:today',
        'type' => 'required|in:missed_punch_in,missed_punch_out,late_entry,correction',
        'reason' => 'required|string|min:10|max:500',
        'requested_punch_in' => 'nullable|date_format:H:i',
        'requested_punch_out' => 'nullable|date_format:H:i',
        'proof_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx'
    ]);
    
    // Check if date is a working day
    $restriction = $this->checkAttendanceRestriction($employeeId, $request->date);
    if ($restriction['restricted']) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot request manual attendance for a non-working day: ' . $restriction['reason']
        ], 400);
    }
    
    // Check for existing attendance record
    $existingAttendance = DB::table('attendances')
        ->where('employee_id', $employeeId)
        ->whereDate('date', $request->date)
        ->first();
    
    // Validate based on request type
    switch ($request->type) {
        case 'missed_punch_in':
            if ($existingAttendance && $existingAttendance->punch_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Punch in already exists for this date'
                ], 400);
            }
            break;
            
        case 'missed_punch_out':
            if (!$existingAttendance || !$existingAttendance->punch_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'No punch in found for this date'
                ], 400);
            }
            if ($existingAttendance->punch_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Punch out already exists for this date'
                ], 400);
            }
            break;
            
        case 'correction':
            if (!$existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance record found for this date'
                ], 400);
            }
            break;
    }
    
    // Check for existing pending request for same date and type
    $existingRequest = DB::table('manual_attendance_requests')
        ->where('employee_id', $employeeId)
        ->where('date', $request->date)
        ->where('type', $request->type)
        ->whereIn('status', ['pending', 'approved'])
        ->first();
    
    if ($existingRequest) {
        return response()->json([
            'success' => false,
            'message' => 'You already have a pending request for this date'
        ], 400);
    }
    
    // Handle file upload
    $proofFilePath = null;
    if ($request->hasFile('proof_file')) {
        $file = $request->file('proof_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/manual-attendance-proofs', $fileName);
        $proofFilePath = 'manual-attendance-proofs/' . $fileName;
    }
    
    // Get employee details
    $employee = DB::table('allemployees')->find($employeeId);
    
    // Create request
    DB::table('manual_attendance_requests')->insert([
        'employee_id' => $employeeId,
        'date' => $request->date,
        'type' => $request->type,
        'requested_punch_in' => $request->requested_punch_in,
        'requested_punch_out' => $request->requested_punch_out,
        'reason' => $request->reason,
        'proof_file' => $proofFilePath,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    // Log activity
    $this->logManualRequestActivity($employeeId, 'submitted', $request->all());
    
    return response()->json([
        'success' => true,
        'message' => 'Manual attendance request submitted successfully'
    ]);
}

/**
 * Get employee's manual attendance requests
 */
public function getEmployeeRequests()
{
    $employeeId = Session::get('user_id');
    
    $requests = DB::table('manual_attendance_requests')
        ->leftJoin('allemployees as approver', 'manual_attendance_requests.approved_by', '=', 'approver.id')
        ->where('manual_attendance_requests.employee_id', $employeeId)
        ->orderBy('manual_attendance_requests.created_at', 'desc')
        ->select(
            'manual_attendance_requests.*',
            'approver.firstname as approver_firstname',
            'approver.lastname as approver_lastname'
        )
        ->paginate(10);
    
    return view('hrms.attendance.manual-requests-list', [
        'requests' => $requests
    ]);
}

private function logManualRequestActivity($employeeId, $action, $data = null)
{
    DB::table('manual_request_logs')->insert([
        'employee_id' => $employeeId,
        'action' => $action,
        'data' => $data ? json_encode($data) : null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
    public function requestManualAttendance(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        $request->validate([
            'request_type' => 'required|in:punch_in,punch_out',
            'request_date' => 'required|date',
            'request_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500',
            'attach_proof' => 'nullable|boolean',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
        ]);

        // Check if request already exists for same date and type
        $existingRequest = DB::table('attendance_requests')
            ->where('employee_id', $employeeId)
            ->where('request_date', $request->request_date)
            ->where('request_type', $request->request_type)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a ' . $request->request_type . ' request for this date'
            ], 400);
        }

        // Handle file upload if provided
        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $fileName = time() . '_' . $employeeId . '_' . $file->getClientOriginalName();
            $proofPath = $file->storeAs('attendance_proofs', $fileName, 'public');
        }

        // Create attendance request
        $requestData = [
            'employee_id' => $employeeId,
            'request_type' => $request->request_type,
            'request_date' => $request->request_date,
            'request_time' => $request->request_time,
            'reason' => $request->reason,
            'proof_file' => $proofPath,
            'status' => 'pending',
            'requested_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Get employee name
        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('firstname', 'lastname')
            ->first();

        if ($employee) {
            $requestData['employee_name'] = $employee->firstname . ' ' . $employee->lastname;
        }

        $requestId = DB::table('attendance_requests')->insertGetId($requestData);

        // Log the request
        $this->logAttendanceActivity($employeeId, 'manual_request_' . $request->request_type, null, [
            'status' => 'pending',
            'message' => 'Requested manual ' . $request->request_type . ' for ' . $request->request_date
        ]);

        // Notify admin (you can implement notification system here)

        return response()->json([
            'success' => true,
            'message' => 'Manual attendance request submitted successfully. Admin will review it.',
            'request_id' => $requestId
        ]);
    }

    /**
     * Cancel attendance request
     */
    public function cancelRequest(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        $request->validate([
            'request_id' => 'required|exists:attendance_requests,id'
        ]);

        $attendanceRequest = DB::table('attendance_requests')
            ->where('id', $request->request_id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$attendanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found or unauthorized'
            ], 404);
        }

        if ($attendanceRequest->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a request that is not pending'
            ], 400);
        }

        // Delete proof file if exists
        if ($attendanceRequest->proof_file) {
            Storage::disk('public')->delete($attendanceRequest->proof_file);
        }

        // Update request status to cancelled
        DB::table('attendance_requests')
            ->where('id', $request->request_id)
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'updated_at' => now()
            ]);

        // Log the cancellation
        $this->logAttendanceActivity($employeeId, 'request_cancelled', null, [
            'status' => 'cancelled',
            'message' => 'Cancelled manual attendance request'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully'
        ]);
    }

    /**
     * Get pending attendance requests for employee
     */
    private function getPendingRequests($employeeId)
    {
        return DB::table('attendance_requests')
            ->where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->orderBy('request_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all attendance requests for employee
     */
    private function getAllAttendanceRequests($employeeId)
    {
        return DB::table('attendance_requests')
            ->where('employee_id', $employeeId)
            ->orderBy('request_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update index method to include requests
     */
    public function index()
    {
        $employeeId = Session::get('user_id');
        
        // Get today's attendance data with break tracking
        $today = now()->format('Y-m-d');
        $todayAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'asc')
            ->get();

        // Get employee's current schedule and shift information with break time
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        
        // Calculate totals with break time consideration
        $todayStats = $this->calculateDailyStats($todayAttendance, $currentSchedule);
        
        // Get monthly data for statistics
        $monthlyStats = $this->getMonthlyStats($employeeId);
        
        // Get pending requests
        $pendingRequests = $this->getPendingRequests($employeeId);
        
        // Get all attendance requests
        $attendanceRequests = $this->getAllAttendanceRequests($employeeId);
        
        // Get recent activities
        $recentActivities = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->orderBy('date', 'desc')
            ->orderBy('punch_in', 'desc')
            ->limit(6)
            ->get();

        // Check attendance restrictions
        $attendanceRestriction = $this->checkAttendanceRestriction($employeeId, $today);
        $isRestrictedDay = $attendanceRestriction['restricted'];
        $restrictionReason = $attendanceRestriction['reason'];

        // Get break status for today
        $breakStatus = $this->getCurrentBreakStatus($employeeId, $today);

        // Get overtime records for current month
        $employeeOvertimeRecords = $this->getEmployeeOvertimeRecords($employeeId);

        // Check if employee has permission during current time
        $hasActivePermission = $this->hasActivePermission($employeeId, $today);

        return view('hrms.attendance.index', [
            'todayAttendance' => $todayAttendance,
            'todayStats' => $todayStats,
            'monthlyStats' => $monthlyStats,
            'recentActivities' => $recentActivities,
            'currentSchedule' => $currentSchedule,
            'isRestrictedDay' => $isRestrictedDay,
            'restrictionReason' => $restrictionReason,
            'breakStatus' => $breakStatus,
            'employeeOvertimeRecords' => $employeeOvertimeRecords,
            'hasActivePermission' => $hasActivePermission,
            'pendingRequests' => $pendingRequests,
            'attendanceRequests' => $attendanceRequests,
            'controller' => $this
        ]);
    }
 
}