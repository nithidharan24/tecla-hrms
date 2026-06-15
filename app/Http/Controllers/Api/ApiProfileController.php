<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiProfileController extends Controller
{
    /**
     * Get all profile details for an employee.
     */
    public function show(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer', // Database auto-increment ID
        ]);

        $employeeId = $request->employee_id;

        // 1. Fetch main employee details
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        // Fetch department and designation names
        $department = DB::table('department')->where('id', $employee->department)->first();
        $designation = DB::table('designation')->where('id', $employee->designation)->first();

        $employee->department_name = $department ? $department->department : null;
        $employee->designation_name = $designation ? $designation->designation : null;

        // Remove sensitive fields
        unset($employee->password);
        unset($employee->remember_token);

        // 2. Fetch basic profile details
        $profileBasic = DB::table('employee_profile_main')->where('employee_id', $employeeId)->first() ?? (object) [
            'birthday' => null,
            'gender' => null,
            'address' => null,
            'state' => null,
            'country' => null,
            'pin_code' => null,
        ];

        // 3. Fetch personal information
        $personalInfo = DB::table('employee_personal_informations')->where('employee_id', $employeeId)->first() ?? (object) [
            'passport_no' => null,
            'passport_exp_date' => null,
            'tel' => null,
            'nationality' => null,
            'religion' => null,
            'marital_status' => null,
            'employment_of_spouse' => null,
            'no_of_children' => 0,
        ];

        // 4. Fetch emergency contact details
        $emergencyContact = DB::table('employee_emergency_contact')->where('employee_id', $employeeId)->first() ?? (object) [
            'primary_name' => null,
            'relationship' => null,
            'phone' => null,
            'secondary_name' => null,
            'secondary_relationship' => null,
            'secondary_phone' => null,
        ];

        // 5. Fetch bank information
        $bankInfo = DB::table('employee_bank_informations')->where('employee_id', $employeeId)->first() ?? (object) [
            'bank_name' => null,
            'bank_account_no' => null,
            'ifsc_code' => null,
            'pan_no' => null,
        ];

        // 6. Fetch bank statutory details (salary, PF, ESI)
        $bankStatutory = DB::table('employee_bank_statutory')->where('employee_id', $employeeId)->first() ?? (object) [
            'salary_basis' => 'Monthly',
            'salary_amount' => 0,
            'payment_type' => 'Bank transfer',
            'pf_contribution' => 'No',
            'pf_no' => null,
            'employee_pf_rate' => '0%',
            'additional_rate' => '0%',
            'total_rate' => '0%',
            'esi_contribution' => 'No',
            'esi_no' => null,
            'employee_esi_rate' => '0%',
            'esi_additional_rate' => '0%',
            'total_esi_rate' => '0%',
        ];

        // 7. Fetch family members
        $familyMembers = DB::table('employee_family_informations')->where('employee_id', $employeeId)->get();

        // 8. Fetch education information
        $education = DB::table('employee_education_informations')->where('employee_id', $employeeId)->get();

        // 9. Fetch experience information
        $experience = DB::table('employee_experience_informations')->where('employee_id', $employeeId)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => $employee,
                'profile_basic' => $profileBasic,
                'personal_info' => $personalInfo,
                'emergency_contact' => $emergencyContact,
                'bank_info' => $bankInfo,
                'bank_statutory' => $bankStatutory,
                'family_members' => $familyMembers,
                'education' => $education,
                'experience' => $experience,
            ]
        ], 200);
    }

    /**
     * Get employee dashboard details including punch in/out and schedule information.
     */
    public function dashboardDetails(Request $request)
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

        // 1. Fetch today's attendance record
        $todayAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'desc')
            ->first();

        // 2. Fetch current schedule for the employee
        $currentSchedule = $this->getCurrentScheduleForEmployee($employeeId, $today);

        // 3. Check if employee is on leave today
        $onLeave = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->first();

        // 4. Check if today is a holiday
        $holiday = DB::table('holidays')
            ->whereDate('holidaydate', $today)
            ->first();

        // 5. Fetch recent attendance history (last 7 days)
        $attendanceHistory = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->where('date', '<=', $today)
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // 6. Fetch weekly summary (current week)
        $weekStart = Carbon::parse($today)->startOfWeek();
        $weekEnd = Carbon::parse($today)->endOfWeek();
        
        $weeklySummary = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN punch_in IS NOT NULL AND punch_out IS NOT NULL THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_days,
                COALESCE(SUM(working_hours), 0) as total_hours,
                COALESCE(SUM(overtime_hours), 0) as total_overtime_hours
            ')
            ->first();

        // 7. Fetch monthly summary (current month)
        $monthStart = Carbon::parse($today)->startOfMonth();
        $monthEnd = Carbon::parse($today)->endOfMonth();
        
        $monthlySummary = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN punch_in IS NOT NULL AND punch_out IS NOT NULL THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_days,
                COALESCE(SUM(working_hours), 0) as total_hours,
                COALESCE(SUM(overtime_hours), 0) as total_overtime_hours
            ')
            ->first();

        // 8. Get employee's leave balance
        $leaveBalance = $this->getLeaveBalance($employeeId);

        // Prepare today's attendance data
        $todayData = null;
        $punchStatus = 'not_punched';
        
        if ($todayAttendance) {
            $punchStatus = 'punched_in';
            if ($todayAttendance->punch_out) {
                $punchStatus = 'punched_out';
            }

            // ── Break details ──────────────────────────────────
            $currentSchedule2 = $currentSchedule; // alias for clarity
            $allocatedBreakTime = $currentSchedule2 ? (int) $currentSchedule2->break_time : 60;
            $breakSessions = json_decode($todayAttendance->break_sessions ?? '[]', true);
            if (!is_array($breakSessions)) $breakSessions = [];

            $completedBreakTime  = 0;
            $activeBreak         = null;
            $currentBreakDuration = 0;

            foreach ($breakSessions as $session) {
                if (empty($session['end_time'])) {
                    $activeBreak = $session;
                    $breakStart  = Carbon::parse($session['start_time'], 'Asia/Kolkata');
                    $currentBreakDuration = $breakStart->diffInMinutes(Carbon::now('Asia/Kolkata'));
                } else {
                    $completedBreakTime += (int) ($session['duration'] ?? 0);
                }
            }

            $totalBreakTaken    = $completedBreakTime + $currentBreakDuration;
            $remainingBreakTime = max(0, $allocatedBreakTime - $totalBreakTaken);
            $isOnBreak          = !is_null($activeBreak);

            // Format break sessions for response
            $formattedBreakSessions = array_map(function ($s) {
                return [
                    'session_id'  => $s['session_id'] ?? null,
                    'start_time'  => isset($s['start_time'])
                        ? Carbon::parse($s['start_time'])->format('h:i A') : null,
                    'end_time'    => isset($s['end_time']) && $s['end_time']
                        ? Carbon::parse($s['end_time'])->format('h:i A') : null,
                    'duration'    => (int) ($s['duration'] ?? 0),
                ];
            }, $breakSessions);

            $todayData = [
                'date'             => $todayAttendance->date,
                'punch_in'         => $todayAttendance->punch_in  ? Carbon::parse($todayAttendance->punch_in)->format('h:i A')  : null,
                'punch_out'        => $todayAttendance->punch_out ? Carbon::parse($todayAttendance->punch_out)->format('h:i A') : null,
                'working_hours'    => $todayAttendance->working_hours ? round($todayAttendance->working_hours, 2) : null,
                'status'           => $todayAttendance->status ?? null,
                'shift_name'       => $currentSchedule ? $currentSchedule->shift_name : null,
                'scheduled_start'  => $currentSchedule ? Carbon::parse($currentSchedule->start_time)->format('h:i A') : null,
                'scheduled_end'    => $currentSchedule ? Carbon::parse($currentSchedule->end_time)->format('h:i A') : null,
                'break_time'       => $allocatedBreakTime,
                // Break details
                'break' => [
                    'is_on_break'             => $isOnBreak,
                    'allocated_break_time'    => $allocatedBreakTime,
                    'completed_break_time'    => $completedBreakTime,
                    'current_break_duration'  => $currentBreakDuration,
                    'total_break_taken'       => $totalBreakTaken,
                    'remaining_break_time'    => $remainingBreakTime,
                    'can_take_break'          => $remainingBreakTime > 0 && !$isOnBreak && !$todayAttendance->punch_out,
                    'current_break_start'     => $activeBreak
                        ? Carbon::parse($activeBreak['start_time'])->format('h:i A') : null,
                    'current_break_start_raw' => $activeBreak ? $activeBreak['start_time'] : null,
                    'break_sessions'          => $formattedBreakSessions,
                    'total_sessions'          => count($breakSessions),
                ],
            ];
        }

        // Prepare schedule data
        $scheduleData = null;
        if ($currentSchedule) {
            $scheduleData = [
                'shift_name' => $currentSchedule->shift_name,
                'start_time' => Carbon::parse($currentSchedule->start_time)->format('h:i A'),
                'end_time' => Carbon::parse($currentSchedule->end_time)->format('h:i A'),
                'break_time_minutes' => $currentSchedule->break_time,
                'working_days' => $currentSchedule->days_of_week,
                'schedule_start_date' => $currentSchedule->schedule_start_date,
                'schedule_end_date' => $currentSchedule->schedule_end_date,
                'department' => $currentSchedule->department ?? null,
            ];
        }

        // Determine day status
        $dayStatus = 'working_day';
        $dayStatusMessage = null;
        
        if ($holiday) {
            $dayStatus = 'holiday';
            $dayStatusMessage = $holiday->title;
        } elseif ($onLeave) {
            $dayStatus = 'on_leave';
            $dayStatusMessage = $onLeave->leave_type;
        } elseif (!$currentSchedule) {
            $dayStatus = 'no_schedule';
            $dayStatusMessage = 'No schedule assigned for today';
        } else {
            // Check if today is a working day based on shift
            $carbonDate = Carbon::parse($today);
            $dayOfWeek = $carbonDate->format('l');
            $workingDays = array_map('trim', explode(',', $currentSchedule->days_of_week));
            
            $isWorkingDay = false;
            foreach ($workingDays as $workingDay) {
                if (strtolower($workingDay) === strtolower($dayOfWeek)) {
                    $isWorkingDay = true;
                    break;
                }
            }
            
            if (!$isWorkingDay) {
                $dayStatus = 'weekend';
                $dayStatusMessage = 'Non-working day according to shift';
            }
        }

        // Format attendance history
        $formattedHistory = [];
        foreach ($attendanceHistory as $attendance) {
            $formattedHistory[] = [
                'date' => $attendance->date,
                'punch_in' => $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('h:i A') : null,
                'punch_out' => $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('h:i A') : null,
                'working_hours' => $attendance->working_hours ? round($attendance->working_hours, 2) : 0,
                'status' => $attendance->status ?? 'present',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code ?? $employee->employeeid ?? null,
                    'first_name' => $employee->first_name ?? $employee->firstname ?? null,
                    'last_name' => $employee->last_name ?? $employee->lastname ?? null,
                    'department' => $employee->department,
                    'designation' => $employee->designation,
                ],
                'today' => [
                    'date'               => $today,
                    'day_status'         => $dayStatus,
                    'day_status_message' => $dayStatusMessage,
                    'attendance'         => $todayData,
                    'punch_status'       => $punchStatus,
                    'schedule'           => $scheduleData,
                    'break_status'       => $todayData ? $todayData['break'] : [
                        'is_on_break'            => false,
                        'allocated_break_time'   => $currentSchedule ? (int) $currentSchedule->break_time : 60,
                        'completed_break_time'   => 0,
                        'current_break_duration' => 0,
                        'total_break_taken'      => 0,
                        'remaining_break_time'   => $currentSchedule ? (int) $currentSchedule->break_time : 60,
                        'can_take_break'         => false,
                        'current_break_start'    => null,
                        'current_break_start_raw'=> null,
                        'break_sessions'         => [],
                        'total_sessions'         => 0,
                    ],
                ],
                'weekly_summary' => [
                    'week_start' => $weekStart->format('Y-m-d'),
                    'week_end' => $weekEnd->format('Y-m-d'),
                    'total_days' => $weeklySummary->total_days ?? 0,
                    'present_days' => $weeklySummary->present_days ?? 0,
                    'late_days' => $weeklySummary->late_days ?? 0,
                    'total_hours' => round($weeklySummary->total_hours ?? 0, 2),
                    'total_overtime_hours' => round($weeklySummary->total_overtime_hours ?? 0, 2),
                ],
                'monthly_summary' => [
                    'month_start' => $monthStart->format('Y-m-d'),
                    'month_end' => $monthEnd->format('Y-m-d'),
                    'total_days' => $monthlySummary->total_days ?? 0,
                    'present_days' => $monthlySummary->present_days ?? 0,
                    'late_days' => $monthlySummary->late_days ?? 0,
                    'total_hours' => round($monthlySummary->total_hours ?? 0, 2),
                    'total_overtime_hours' => round($monthlySummary->total_overtime_hours ?? 0, 2),
                ],
                'leave_balance' => $leaveBalance,
                'attendance_history' => $formattedHistory,
            ]
        ], 200);
    }

    /**
     * Get current schedule for an employee on a specific date.
     */
    private function getCurrentScheduleForEmployee($employeeId, $date)
    {
        return DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
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

    /**
     * Get employee's leave balance.
     */
    private function getLeaveBalance($employeeId)
    {
        // Get leave entitlements for the employee's department/designation
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return [
                'casual_leave' => 0,
                'sick_leave' => 0,
                'earned_leave' => 0,
                'paid_leave' => 0,
            ];
        }

        // Fetch leave balance from employee_leave_balances table if exists
        $leaveBalance = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->first();

        if ($leaveBalance) {
            return [
                'casual_leave' => $leaveBalance->casual_leave ?? 0,
                'sick_leave' => $leaveBalance->sick_leave ?? 0,
                'earned_leave' => $leaveBalance->earned_leave ?? 0,
                'paid_leave' => $leaveBalance->paid_leave ?? 0,
            ];
        }

        // Default leave balances
        return [
            'casual_leave' => 12,
            'sick_leave' => 12,
            'earned_leave' => 30,
            'paid_leave' => 0,
        ];
    }
}
