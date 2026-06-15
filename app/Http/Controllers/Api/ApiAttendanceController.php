<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ApiAttendanceController extends Controller
{
    /**
     * Punch In API.
     */
    public function punchIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $now = now()->setTimezone('Asia/Kolkata');
        $today = $now->format('Y-m-d');

        // Check employee exists
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        // Check attendance restrictions
        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        if ($restriction['restricted']) {
            return response()->json([
                'success' => false,
                'message' => 'Punch in is not allowed today. Reason: ' . $restriction['reason']
            ], 403);
        }

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot punch in during permission hours. ' . $permissionCheck['message']
            ], 403);
        }

        // Check if already punched in today
        $lastPunch = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'desc')
            ->first();

        if ($lastPunch && !$lastPunch->punch_out) {
            return response()->json([
                'success' => false,
                'message' => 'You have already punched in today!'
            ], 400);
        }

        // Get current schedule for validation
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        
        // Check if punch in is within allowed time
        $punchInValidation = $this->validatePunchInTime($now, $currentSchedule);
        
        // Create new attendance record
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

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'attendance_id' => $attendanceId,
                'punch_in' => $now->format('Y-m-d H:i:s'),
                'status' => $attendanceData['status'] ?? 'on_time'
            ]
        ], 200);
    }

    /**
     * Punch Out API.
     */
    public function punchOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $now = now()->setTimezone('Asia/Kolkata');
        $today = $now->format('Y-m-d');

        // Check attendance restrictions
        $restriction = $this->checkAttendanceRestriction($employeeId, $today);
        if ($restriction['restricted']) {
            return response()->json([
                'success' => false,
                'message' => 'Punch out is not allowed today. Reason: ' . $restriction['reason']
            ], 403);
        }

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot punch out during permission hours. ' . $permissionCheck['message']
            ], 403);
        }

        // Get the active punch in record
        $lastPunch = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->orderBy('punch_in', 'desc')
            ->first();

        if (!$lastPunch) {
            return response()->json([
                'success' => false,
                'message' => 'No active punch in found for today!'
            ], 400);
        }

        // Check if currently on break
        $breakSessions = json_decode($lastPunch->break_sessions ?? '[]', true);
        $activeBreak = collect($breakSessions)->where('end_time', null)->first();

        if ($activeBreak) {
            return response()->json([
                'success' => false,
                'message' => 'Please end your current break before punching out!'
            ], 400);
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

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'attendance_id' => $lastPunch->id,
                'punch_out' => $now->format('Y-m-d H:i:s'),
                'working_hours' => round($workingHours, 2),
                'overtime_hours' => $overtimeHours
            ]
        ], 200);
    }

    /**
     * Start Break API.
     */
    public function startBreak(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $today = now()->format('Y-m-d');
        $now = now()->setTimezone('Asia/Kolkata');

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start break during permission hours. ' . $permissionCheck['message']
            ], 403);
        }

        // Get current attendance record
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active attendance session found!'
            ], 400);
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
            return response()->json([
                'success' => false,
                'message' => 'You are already on break!'
            ], 400);
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
            return response()->json([
                'success' => false,
                'message' => 'You have exhausted your break time for today!'
            ], 400);
        }

        // Start new break session
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

        return response()->json([
            'success' => true,
            'message' => 'Break started successfully.',
            'data' => [
                'start_time' => $now->format('Y-m-d H:i:s'),
                'remaining_break_time' => $remainingBreakTime
            ]
        ], 200);
    }

    /**
     * End Break API.
     */
    public function endBreak(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $today = now()->format('Y-m-d');
        $now = now()->setTimezone('Asia/Kolkata');

        // Check if employee has active permission
        $permissionCheck = $this->hasActivePermission($employeeId, $today);
        if ($permissionCheck['has_permission']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot end break during permission hours. ' . $permissionCheck['message']
            ], 403);
        }

        // Get current attendance record
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active attendance session found!'
            ], 400);
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
            return response()->json([
                'success' => false,
                'message' => 'No active break session found!'
            ], 400);
        }

        // Calculate break duration
        $breakStart = Carbon::parse($breakSessions[$activeBreakIndex]['start_time'], 'Asia/Kolkata');
        $breakDuration = $breakStart->diffInMinutes($now);
        
        if ($breakDuration < 1 && !$breakStart->equalTo($now)) {
            $breakDuration = 1;
        }

        // Update break session
        $breakSessions[$activeBreakIndex]['end_time'] = $now->format('Y-m-d H:i:s');
        $breakSessions[$activeBreakIndex]['duration'] = $breakDuration;

        // Calculate total break time taken
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

        return response()->json([
            'success' => true,
            'message' => 'Break ended successfully.',
            'data' => [
                'end_time' => $now->format('Y-m-d H:i:s'),
                'duration' => $breakDuration,
                'total_break_taken' => $totalBreakTaken
            ]
        ], 200);
    }

    /**
     * Get Break Status API.
     */
    public function getBreakStatus(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $request->employee_id;
        $today = now()->format('Y-m-d');
        
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNull('punch_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => true,
                'data' => [
                    'can_take_break' => false,
                    'is_on_break' => false,
                    'allocated_break_time' => 0,
                    'total_break_taken' => 0,
                    'remaining_break_time' => 0,
                    'break_sessions' => [],
                    'current_break_start' => null,
                    'current_break_duration' => 0
                ]
            ], 200);
        }

        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);
        $allocatedBreakTime = $currentSchedule ? $currentSchedule->break_time : 60;
        
        $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);
        
        $completedBreakTime = 0;
        $activeBreak = null;
        $currentBreakDuration = 0;
        
        foreach ($breakSessions as $session) {
            if (!isset($session['end_time']) || $session['end_time'] === null) {
                $activeBreak = $session;
                $breakStart = Carbon::parse($session['start_time'], 'Asia/Kolkata');
                $currentBreakDuration = $breakStart->diffInMinutes(Carbon::now('Asia/Kolkata'));
            } else {
                if (isset($session['duration'])) {
                    $completedBreakTime += $session['duration'];
                }
            }
        }
        
        $totalBreakTaken = $completedBreakTime + $currentBreakDuration;
        $remainingBreakTime = max(0, $allocatedBreakTime - $totalBreakTaken);

        return response()->json([
            'success' => true,
            'data' => [
                'can_take_break' => $remainingBreakTime > 0 && !$activeBreak,
                'is_on_break' => !is_null($activeBreak),
                'allocated_break_time' => $allocatedBreakTime,
                'total_break_taken' => $totalBreakTaken,
                'completed_break_time' => $completedBreakTime,
                'current_break_duration' => $currentBreakDuration,
                'remaining_break_time' => $remainingBreakTime,
                'break_sessions' => $breakSessions,
                'current_break_start' => $activeBreak ? $activeBreak['start_time'] : null
            ]
        ], 200);
    }

    /**
     * Get Attendance History API.
     */
    public function history(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $employeeId = $request->employee_id;
        $limit = (int) $request->input('limit', 30);

        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $query = DB::table('attendances')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->where('attendances.employee_id', $employeeId);

        if ($request->filled('date_from')) {
            $query->whereDate('attendances.date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('attendances.date', '<=', $request->date_to);
        }

        $records = $query
            ->orderBy('attendances.date', 'desc')
            ->orderBy('attendances.punch_in', 'desc')
            ->limit($limit)
            ->select(
                'attendances.*',
                'shifts.shift_name',
                'shifts.start_time as shift_start_time',
                'shifts.end_time as shift_end_time'
            )
            ->get();

        $history = $records->map(function ($attendance) {
            $punchIn = $attendance->punch_in ? Carbon::parse($attendance->punch_in) : null;
            $punchOut = $attendance->punch_out ? Carbon::parse($attendance->punch_out) : null;
            $breakSessions = json_decode($attendance->break_sessions ?? '[]', true);

            return [
                'attendance_id' => $attendance->id,
                'date' => $attendance->date,
                'day' => Carbon::parse($attendance->date)->format('l'),
                'punch_in' => $punchIn ? $punchIn->format('h:i A') : null,
                'punch_out' => $punchOut ? $punchOut->format('h:i A') : null,
                'punch_in_datetime' => $punchIn ? $punchIn->format('Y-m-d H:i:s') : null,
                'punch_out_datetime' => $punchOut ? $punchOut->format('Y-m-d H:i:s') : null,
                'working_hours' => isset($attendance->working_hours) ? round((float) $attendance->working_hours, 2) : 0,
                'actual_working_minutes' => $attendance->actual_working_minutes ?? 0,
                'total_break_taken' => $attendance->total_break_taken ?? 0,
                'overtime_hours' => isset($attendance->overtime_hours) ? round((float) $attendance->overtime_hours, 2) : 0,
                'overtime_minutes' => $attendance->overtime_minutes ?? 0,
                'undertime_minutes' => $attendance->undertime_minutes ?? 0,
                'status' => $attendance->status ?? 'present',
                'shift' => [
                    'id' => $attendance->shift_id ?? null,
                    'name' => $attendance->shift_name ?? null,
                    'start_time' => $attendance->shift_start_time ? Carbon::parse($attendance->shift_start_time)->format('h:i A') : null,
                    'end_time' => $attendance->shift_end_time ? Carbon::parse($attendance->shift_end_time)->format('h:i A') : null,
                ],
                'break_sessions' => is_array($breakSessions) ? $breakSessions : [],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code ?? $employee->employeeid ?? null,
                    'first_name' => $employee->first_name ?? $employee->firstname ?? null,
                    'last_name' => $employee->last_name ?? $employee->lastname ?? null,
                ],
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'limit' => $limit,
                ],
                'history' => $history,
            ]
        ], 200);
    }

    /* Helper Methods */

    private function getCurrentScheduleForEmployee($employeeId, $date)
    {
        return DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->join('department', 'schedule.department_id', '=', 'department.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.schedule_start_date', '<=', $date)
            ->where(function($query) use ($date) {
                $query->where(function($q) {
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
            ->first();
    }

    private function hasActivePermission($employeeId, $date)
    {
        $now = now()->setTimezone('Asia/Kolkata');
        $currentTime = $now->format('H:i:s');
        
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
            
            return [
                'has_permission' => true,
                'message' => "Approved permission from {$startTime} to {$endTime}."
            ];
        }

        return [
            'has_permission' => false,
            'message' => null
        ];
    }

    private function checkAttendanceRestriction($employeeId, $date)
    {
        $onLeave = DB::table('employee_leaves')
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

        $holiday = DB::table('holidays')->whereDate('holidaydate', $date)->first();
        if ($holiday) {
            return [
                'restricted' => true,
                'reason' => 'Holiday: ' . $holiday->title
            ];
        }
        
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $date);
        if (!$currentSchedule) {
            return [
                'restricted' => true,
                'reason' => 'No active schedule found for today'
            ];
        }
        
        $carbonDate = Carbon::parse($date);
        $dayOfWeekFull = $carbonDate->format('l');
        $dayOfWeekShort = $carbonDate->format('D');
        $dayOfWeekNumber = $carbonDate->format('N');
        
        $workingDaysString = $currentSchedule->days_of_week;
        if (empty($workingDaysString)) {
            return [
                'restricted' => true,
                'reason' => 'No working days defined for your shift'
            ];
        }
        
        $workingDays = array_map('trim', explode(',', $workingDaysString));
        $isWorkingDay = false;
        
        foreach ($workingDays as $workingDay) {
            if (strtolower($workingDay) === strtolower($dayOfWeekFull) ||
                strtolower($workingDay) === strtolower($dayOfWeekShort) ||
                (is_numeric($workingDay) && intval($workingDay) == $dayOfWeekNumber)) {
                $isWorkingDay = true;
                break;
            }
        }
        
        if (!$isWorkingDay) {
            return [
                'restricted' => true,
                'reason' => 'Not a working day according to shift schedule'
            ];
        }
        
        return [
            'restricted' => false,
            'reason' => null
        ];
    }

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

    private function logAttendanceActivity($employeeId, $action, $attendanceId, $validation = null)
    {
        DB::table('attendance_logs')->insert([
            'employee_id' => $employeeId,
            'attendance_id' => $attendanceId,
            'action' => $action,
            'status' => $validation['status'] ?? null,
            'message' => $validation['message'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent() ?? 'API',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function calculateAndStoreOvertime($employeeId, $date, $overtimeHours, $schedule = null)
    {
        if ($overtimeHours < 1) {
            return;
        }

        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return;
        }

        $overtimeRate = DB::table('overtimes')
            ->where('hierarchy_id', $employee->hierarchy_id)
            ->first();

        if (!$overtimeRate) {
            return;
        }

        $overtimeAmount = $overtimeHours * $overtimeRate->rate;

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
}
