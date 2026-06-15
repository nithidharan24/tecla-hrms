<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Traits\BuildsAnnouncementItems;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    use BuildsAnnouncementItems;
    public function index()
    {
        $userId = Session::get('user_id');
        $employeeId = Session::get('employee_id');
        
        // Get basic employee info
        $employee = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.employeeid', $employeeId)
            ->select(
                'allemployees.*',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->first();
            
        // If employee not found, redirect with error
        if (!$employee) {
            return redirect()->route('login')->withErrors('Employee not found.');
        }

        // Get current user ID for community features
        $currentUserId = $this->getCurrentUserId();

        return view('hrms.Employee.dashboard.index', [
            'employee' => $employee,
            'upcomingHolidays' => $this->getUpcomingHolidays(),
            'leaveStats' => $this->getEmployeeLeaveStats($userId),
            'recentLeaves' => $this->getRecentLeaves($userId),
            'ticketStats' => $this->getEmployeeTicketStats($userId),
            'recentTickets' => $this->getRecentTickets($userId),
            'projectStats' => $this->getEmployeeProjectStats($employeeId),
            'recentProjects' => $this->getRecentProjects($employeeId),
            'taskStats' => $this->getEmployeeTaskStats($userId),
            'employeeSchedule' => $this->getEmployeeSchedule($employeeId),
            'todaysShift' => $this->getTodaysShift($employeeId),
            'todayAttendance' => $this->getTodayAttendance($currentUserId),
            'weeklySchedule' => $this->getWeeklySchedule($employeeId),
            'upcomingScheduleChanges' => $this->getUpcomingScheduleChanges($employeeId),
            'nextSchedule' => $this->getNextSchedule($employeeId),
            'scheduleTransition' => $this->getScheduleTransition($employeeId),
            'employeeOvertime' => $this->getEmployeeOvertimeSummary($employeeId),
            
            // NEW: Personal Community Data
            'myBirthdayWishes' => $this->getMyBirthdayWishes($currentUserId),
            'myAnniversaryWishes' => $this->getMyAnniversaryWishes($currentUserId),
            'myPersonalAchievements' => $this->getMyPersonalAchievements($currentUserId),
            'achievementCongratulations' => $this->getAchievementCongratulations($currentUserId),
            'isBirthdayToday' => $this->isBirthdayToday($currentUserId),
            'isAnniversaryToday' => $this->isAnniversaryToday($currentUserId),
            'communityStats' => $this->getMyCommunityStats($currentUserId),
            'announcementItems' => $this->buildAnnouncementItems(),
        ]);
    }

    /**
     * Get current user ID for community features
     */
    private function getCurrentUserId()
    {
        $role = Session::get('role');
    
        if ($role === 'admin') {
            return Session::get('admin_id');
        } elseif ($role === 'employee') {
            $userId = Session::get('user_id');
            $email = Session::get('email');
        
            // First try by user_id
            if ($userId) {
                $user = DB::table('allemployees')->where('id', $userId)->where('deleted_at', 0)->first();
                if ($user) {
                    return $user->id;
                }
            }
        
            // Fallback: try by email
            if ($email) {
                $user = DB::table('allemployees')->where('email', $email)->where('deleted_at', 0)->first();
                if ($user) {
                    Session::put('user_id', $user->id);
                    return $user->id;
                }
            }
        
            // Last resort: try by employeeid if available
            $employeeId = Session::get('employee_id');
            if ($employeeId) {
                $user = DB::table('allemployees')->where('employeeid', $employeeId)->where('deleted_at', 0)->first();
                if ($user) {
                    Session::put('user_id', $user->id);
                    return $user->id;
                }
            }
        
        } elseif ($role === 'client') {
            return Session::get('user_id');
        }
    
        return null;
    }

    /**
     * Get birthday wishes received by current user today
     */
    protected function getMyBirthdayWishes($userId)
    {
        if (!$userId) return collect();

        return DB::table('birthday_wishes')
            ->join('allemployees as sender', 'birthday_wishes.sender_id', '=', 'sender.id')
            ->leftJoin('department as sender_dept', 'sender.department', '=', 'sender_dept.id')
            ->where('birthday_wishes.employee_id', $userId)
            ->where('birthday_wishes.wish_type', 'birthday')
            ->whereDate('birthday_wishes.created_at', Carbon::today())
            ->select(
                'birthday_wishes.id',
                'birthday_wishes.message',
                'birthday_wishes.created_at',
                'sender.firstname as sender_firstname',
                'sender.lastname as sender_lastname',
                'sender.profile_image as sender_image',
                'sender_dept.department as sender_department'
            )
            ->orderBy('birthday_wishes.created_at', 'desc')
            ->get()
            ->map(function($wish) {
                $wish->sender_name = "{$wish->sender_firstname} {$wish->sender_lastname}";
                $wish->time_ago = Carbon::parse($wish->created_at)->diffForHumans();
                return $wish;
            });
    }

    /**
     * Get anniversary wishes received by current user today
     */
    protected function getMyAnniversaryWishes($userId)
    {
        if (!$userId) return collect();

        return DB::table('birthday_wishes')
            ->join('allemployees as sender', 'birthday_wishes.sender_id', '=', 'sender.id')
            ->leftJoin('department as sender_dept', 'sender.department', '=', 'sender_dept.id')
            ->where('birthday_wishes.employee_id', $userId)
            ->where('birthday_wishes.wish_type', 'anniversary')
            ->whereDate('birthday_wishes.created_at', Carbon::today())
            ->select(
                'birthday_wishes.id',
                'birthday_wishes.message',
                'birthday_wishes.created_at',
                'sender.firstname as sender_firstname',
                'sender.lastname as sender_lastname',
                'sender.profile_image as sender_image',
                'sender_dept.department as sender_department'
            )
            ->orderBy('birthday_wishes.created_at', 'desc')
            ->get()
            ->map(function($wish) {
                $wish->sender_name = "{$wish->sender_firstname} {$wish->sender_lastname}";
                $wish->time_ago = Carbon::parse($wish->created_at)->diffForHumans();
                return $wish;
            });
    }

    /**
     * Get personal achievements uploaded by current user
     */
    protected function getMyPersonalAchievements($userId, $limit = 5)
    {
        if (!$userId) return collect();

        return DB::table('personal_achievements')
            ->where('employee_id', $userId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($achievement) use ($userId) {
                // Get congratulations count for this achievement
                $achievement->congratulations_count = DB::table('achievement_congratulations')
                    ->where('achievement_id', $achievement->id)
                    ->count();
                
                $achievement->time_ago = Carbon::parse($achievement->created_at)->diffForHumans();
                return $achievement;
            });
    }

    /**
     * Get congratulations received on achievements
     */
    protected function getAchievementCongratulations($userId, $limit = 10)
    {
        if (!$userId) return collect();

        return DB::table('achievement_congratulations')
            ->join('personal_achievements', 'achievement_congratulations.achievement_id', '=', 'personal_achievements.id')
            ->join('allemployees as sender', 'achievement_congratulations.sender_id', '=', 'sender.id')
            ->leftJoin('department as sender_dept', 'sender.department', '=', 'sender_dept.id')
            ->where('achievement_congratulations.receiver_id', $userId)
            ->select(
                'achievement_congratulations.*',
                'personal_achievements.title as achievement_title',
                'personal_achievements.icon as achievement_icon',
                'sender.firstname as sender_firstname',
                'sender.lastname as sender_lastname',
                'sender.profile_image as sender_image',
                'sender_dept.department as sender_department'
            )
            ->orderBy('achievement_congratulations.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($congrats) {
                $congrats->sender_name = "{$congrats->sender_firstname} {$congrats->sender_lastname}";
                $congrats->time_ago = Carbon::parse($congrats->created_at)->diffForHumans();
                return $congrats;
            });
    }

    /**
     * Check if today is current user's birthday
     */
    protected function isBirthdayToday($userId)
    {
        if (!$userId) return false;

        $today = Carbon::now();
        
        $employee = DB::table('allemployees')
            ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
            ->where('allemployees.id', $userId)
            ->where('allemployees.deleted_at', 0)
            ->whereNotNull('employee_profile_main.birthday')
            ->select('employee_profile_main.birthday')
            ->first();

        if (!$employee || !$employee->birthday) {
            return false;
        }

        return Carbon::parse($employee->birthday)->format('m-d') === $today->format('m-d');
    }

    /**
     * Check if today is current user's work anniversary
     */
    protected function isAnniversaryToday($userId)
    {
        if (!$userId) return false;

        $today = Carbon::now();
        $oneYearAgo = $today->copy()->subYear();
        
        $employee = DB::table('allemployees')
            ->where('id', $userId)
            ->where('deleted_at', 0)
            ->whereNotNull('joiningdate')
            ->where('joiningdate', '<=', $oneYearAgo)
            ->select('joiningdate')
            ->first();

        if (!$employee || !$employee->joiningdate) {
            return false;
        }

        return Carbon::parse($employee->joiningdate)->format('m-d') === $today->format('m-d');
    }

    /**
     * Get community statistics for current user
     */
    protected function getMyCommunityStats($userId)
    {
        if (!$userId) {
            return [
                'wishes_received_today' => 0,
                'wishes_sent_today' => 0,
                'total_achievements' => 0,
                'congratulations_received' => 0,
                'congratulations_sent' => 0
            ];
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'wishes_received_today' => DB::table('birthday_wishes')
                ->where('employee_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
                
            'wishes_sent_today' => DB::table('birthday_wishes')
                ->where('sender_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
                
            'total_achievements' => DB::table('personal_achievements')
                ->where('employee_id', $userId)
                ->where('status', 'approved')
                ->count(),
                
            'congratulations_received' => DB::table('achievement_congratulations')
                ->where('receiver_id', $userId)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
                
            'congratulations_sent' => DB::table('achievement_congratulations')
                ->where('sender_id', $userId)
                ->where('created_at', '>=', $thisMonth)
                ->count()
        ];
    }

    protected function getEmployeeByCode($employeeId)
    {
        return DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();
    }

    protected function dayMatchesSchedule($daysOfWeek, Carbon $date)
    {
        if (empty($daysOfWeek)) {
            return false;
        }

        $dayOfWeek = strtolower($date->format('l'));
        $dayShort = strtolower($date->format('D'));
        $dayNumber = (string) $date->dayOfWeek;
        $isoDayNumber = (string) $date->format('N');
        $workingDays = array_map('strtolower', array_map('trim', explode(',', $daysOfWeek)));

        foreach ($workingDays as $workingDay) {
            if (
                $workingDay === $dayOfWeek ||
                $workingDay === $dayShort ||
                $workingDay === $dayNumber ||
                $workingDay === $isoDayNumber ||
                ($date->dayOfWeek === 0 && in_array($workingDay, ['sunday', 'sun', '0', '7'], true)) ||
                ($date->dayOfWeek === 6 && in_array($workingDay, ['saturday', 'sat', '6'], true))
            ) {
                return true;
            }
        }

        return false;
    }

    protected function getScheduleForDate($employeeId, Carbon $date, $mustMatchDay = false)
    {
        $employee = $this->getEmployeeByCode($employeeId);

        if (!$employee) {
            return null;
        }

        $dateString = $date->format('Y-m-d');

        $schedules = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->where('schedule.employee_id', $employee->id)
            ->where('schedule.deleted_at', 0)
            ->where('shifts.deleted_at', 0)
            ->whereDate('schedule.schedule_start_date', '<=', $dateString)
            ->where(function ($query) use ($dateString) {
                $query->where('schedule.repeat_every_week', '>', 0)
                    ->orWhere(function ($q) use ($dateString) {
                        $q->where(function ($inner) {
                            $inner->where('schedule.repeat_every_week', 0)
                                ->orWhereNull('schedule.repeat_every_week');
                        })->whereDate('schedule.schedule_end_date', '>=', $dateString);
                    });
            })
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'shifts.days_of_week',
                'department.department as department_name'
            )
            ->orderBy('schedule.repeat_every_week', 'desc')
            ->orderBy('schedule.schedule_start_date', 'desc')
            ->get();

        foreach ($schedules as $schedule) {
            if ($this->dayMatchesSchedule($schedule->days_of_week, $date)) {
                return $this->decorateSchedule($schedule, $date);
            }
        }

        if (!$mustMatchDay && $schedules->isNotEmpty()) {
            return $this->decorateSchedule($schedules->first(), $date);
        }

        return null;
    }

    protected function decorateSchedule($schedule, Carbon $date)
    {
        $schedule->start_time_formatted = Carbon::parse($schedule->start_time)->format('h:i A');
        $schedule->end_time_formatted = Carbon::parse($schedule->end_time)->format('h:i A');

        $start = $date->copy()->setTimeFromTimeString($schedule->start_time);
        $end = $date->copy()->setTimeFromTimeString($schedule->end_time);
        if ($end->lt($start)) {
            $end->addDay();
        }

        $totalMinutes = max(0, $start->diffInMinutes($end) - (int) ($schedule->break_time ?? 0));
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $schedule->duration = $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' mins' : '0 mins');
        $schedule->shift_duration = $hours . ' hrs' . ($minutes > 0 ? ' ' . $minutes . ' mins' : '');
        $schedule->break_duration = ((int) ($schedule->break_time ?? 0)) . ' minutes';

        $dayNames = [
            'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed',
            'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun',
            'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed',
            'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun',
            '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu',
            '5' => 'Fri', '6' => 'Sat', '0' => 'Sun', '7' => 'Sun',
        ];

        $days = array_filter(array_map('trim', explode(',', $schedule->days_of_week ?? '')));
        $formattedDays = [];
        foreach ($days as $day) {
            $key = strtolower($day);
            $formattedDays[] = $dayNames[$key] ?? ucfirst($key);
        }
        $schedule->days_formatted = $formattedDays ? implode(', ', array_unique($formattedDays)) : 'Not specified';

        if (!empty($schedule->schedule_end_date)) {
            $endDate = Carbon::parse($schedule->schedule_end_date);
            $schedule->is_ending_soon = (int) ($schedule->repeat_every_week ?? 0) === 0 && $endDate->diffInDays($date) <= 7;
            $schedule->days_remaining = $date->diffInDays($endDate, false);
        } else {
            $schedule->is_ending_soon = false;
            $schedule->days_remaining = null;
        }

        return $schedule;
    }

    protected function getTodayAttendance($userId)
    {
        if (!$userId) {
            return null;
        }

        return DB::table('attendances')
            ->where('employee_id', $userId)
            ->whereDate('date', Carbon::today())
            ->orderBy('punch_in', 'desc')
            ->first();
    }

    // Keep all existing methods unchanged...
    protected function getEmployeeSchedule($employeeId)
    {
        return $this->getScheduleForDate($employeeId, Carbon::today(), false);
    }

    protected function getTodaysShift($employeeId)
    {
        $today = Carbon::today();
        $now = Carbon::now();

        $schedule = $this->getScheduleForDate($employeeId, $today, true);

        $employee = $this->getEmployeeByCode($employeeId);
        $attendance = $employee ? DB::table('attendances')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->select(
                'attendances.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'shifts.days_of_week'
            )
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->orderBy('punch_in', 'desc')
            ->first() : null;

        if (!$schedule && $attendance && $attendance->punch_in) {
            $schedule = (object) [
                'shift_name' => $attendance->shift_name ?? 'Today Shift',
                'start_time' => $attendance->scheduled_start ?? $attendance->start_time ?? Carbon::parse($attendance->punch_in)->format('H:i:s'),
                'end_time' => $attendance->scheduled_end ?? $attendance->end_time ?? Carbon::parse($attendance->punch_in)->copy()->addHours(8)->format('H:i:s'),
                'break_time' => $attendance->allocated_break_time ?? $attendance->break_time ?? 0,
                'days_of_week' => $attendance->days_of_week ?? strtolower($today->format('l')),
                'schedule_end_date' => $today->format('Y-m-d'),
                'repeat_every_week' => 0,
            ];
            $schedule = $this->decorateSchedule($schedule, $today);
        }

        if (!$schedule) {
            return null;
        }

        $startTime = $today->copy()->setTimeFromTimeString($schedule->start_time);
        $endTime = $today->copy()->setTimeFromTimeString($schedule->end_time);
        
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        $schedule->punched_in_at = $attendance && $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('h:i A') : null;
        $schedule->punched_out_at = $attendance && $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('h:i A') : null;
        $schedule->has_punched_in = (bool) ($attendance && $attendance->punch_in);
        
        if ($attendance && $attendance->punch_in && !$attendance->punch_out) {
            $schedule->status = 'in_progress';
            $minutesLeft = max(0, $now->diffInMinutes($endTime, false));
            $schedule->time_until = $minutesLeft > 0
                ? ($minutesLeft < 60 ? 'Ends in ' . $minutesLeft . ' minutes' : 'Ends ' . $now->diffForHumans($endTime))
                : 'Active beyond scheduled end';
        } elseif ($attendance && $attendance->punch_out) {
            $schedule->status = 'completed';
            $schedule->time_until = 'Punched out at ' . $schedule->punched_out_at;
        } elseif ($now->lt($startTime)) {
            $schedule->status = 'upcoming';
            $minutesUntil = $now->diffInMinutes($startTime);
            if ($minutesUntil < 60) {
                $schedule->time_until = 'Starts in ' . $minutesUntil . ' minutes';
            } else {
                $schedule->time_until = 'Starts ' . $now->diffForHumans($startTime);
            }
        } elseif ($now->between($startTime, $endTime)) {
            $schedule->status = 'in_progress';
            $minutesLeft = $now->diffInMinutes($endTime);
            if ($minutesLeft < 60) {
                $schedule->time_until = 'Ends in ' . $minutesLeft . ' minutes';
            } else {
                $schedule->time_until = 'Ends ' . $now->diffForHumans($endTime);
            }
        } else {
            $schedule->status = 'completed';
            $schedule->time_until = 'Completed today';
        }
        
        return $schedule;
    }

    /**
     * Get weekly schedule - ENHANCED VERSION
     */
    protected function getWeeklySchedule($employeeId)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return [];
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $weeklySchedule = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $schedule = $this->getScheduleForDate($employeeId, $currentDay, true);
            
            $daySchedule = [
                'day_name' => $currentDay->format('D'),
                'date' => $currentDay->format('M d'),
                'full_date' => $currentDay->format('Y-m-d'),
                'is_today' => $currentDay->isToday(),
                'is_past' => $currentDay->lt(Carbon::today()),
                'has_shift' => false,
                'start_time' => null,
                'end_time' => null,
                'duration' => null,
                'shift_name' => null
            ];

            if ($schedule) {
                $daySchedule['has_shift'] = true;
                $daySchedule['start_time'] = Carbon::parse($schedule->start_time)->format('h:i A');
                $daySchedule['end_time'] = Carbon::parse($schedule->end_time)->format('h:i A');
                $daySchedule['shift_name'] = $schedule->shift_name;
                $daySchedule['duration'] = $schedule->shift_duration;
            }

            $weeklySchedule[] = $daySchedule;
        }

        return $weeklySchedule;
    }

    /**
     * Get next upcoming schedule - FIXED VERSION
     */
    protected function getNextSchedule($employeeId)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return null;
        }

        $today = Carbon::today();
        
        // Get the next schedule that starts after today or current schedule ending soon
        $nextSchedule = DB::table('schedule')
            ->where('schedule.employee_id', $employee->id)
            ->where('schedule.deleted_at', 0)
            ->where(function($query) use ($today) {
                // Either starts in the future, or current schedule ending within 7 days
                $query->where('schedule.schedule_start_date', '>', $today)
                      ->orWhere(function($q) use ($today) {
                          $q->where('schedule.schedule_start_date', '<=', $today)
                            ->where('schedule.schedule_end_date', '>=', $today)
                            ->where('schedule.schedule_end_date', '<=', $today->copy()->addDays(7));
                      });
            })
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->join('department', 'schedule.department_id', '=', 'department.id')
            ->where('shifts.deleted_at', 0)
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'shifts.days_of_week',
                'department.department as department_name'
            )
            ->orderBy('schedule.schedule_start_date', 'asc')
            ->first();

        if ($nextSchedule) {
            // Format the schedule data
            $nextSchedule->start_time_formatted = Carbon::parse($nextSchedule->start_time)->format('h:i A');
            $nextSchedule->end_time_formatted = Carbon::parse($nextSchedule->end_time)->format('h:i A');
            
            // Calculate duration
            $start = Carbon::parse($nextSchedule->start_time);
            $end = Carbon::parse($nextSchedule->end_time);
            if ($end->lt($start)) {
                $end->addDay();
            }
            $duration = $end->diff($start);
            $totalMinutes = ($duration->h * 60) + $duration->i - ($nextSchedule->break_time ?? 0);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $nextSchedule->duration = $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' mins' : '');
            
            // Format dates
            $startDate = Carbon::parse($nextSchedule->schedule_start_date);
            $endDate = Carbon::parse($nextSchedule->schedule_end_date);
            
            $nextSchedule->formatted_start_date = $startDate->format('M d, Y');
            $nextSchedule->formatted_end_date = $endDate->format('M d, Y');
            
            // Calculate days until start
            $nextSchedule->days_until_start = $today->diffInDays($startDate, false);
            $nextSchedule->starts_in = $startDate->diffForHumans();
            
            // Determine schedule status
            if ($startDate->gt($today)) {
                $nextSchedule->status = 'upcoming';
                $nextSchedule->status_text = 'Starts ' . $nextSchedule->starts_in;
            } elseif ($startDate->lte($today) && $endDate->gte($today)) {
                $nextSchedule->status = 'current_ending';
                $nextSchedule->status_text = 'Current schedule ending ' . $endDate->diffForHumans();
            }
            
            // Format working days
            if ($nextSchedule->days_of_week) {
                $days = array_map('trim', explode(',', $nextSchedule->days_of_week));
                $dayNames = [
                    'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 
                    'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun',
                    'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 
                    'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun',
                    '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', 
                    '5' => 'Fri', '6' => 'Sat', '0' => 'Sun', '7' => 'Sun'
                ];
                
                $formattedDays = [];
                foreach ($days as $day) {
                    $day = strtolower(trim($day));
                    $formattedDays[] = $dayNames[$day] ?? ucfirst($day);
                }
                $nextSchedule->days_formatted = implode(', ', array_unique($formattedDays));
            }
            
            // Check if this is a schedule change (different from current)
            $currentSchedule = $this->getEmployeeSchedule($employeeId);
            $nextSchedule->is_schedule_change = $currentSchedule && 
                ($currentSchedule->shift_id != $nextSchedule->shift_id || 
                 $currentSchedule->days_of_week != $nextSchedule->days_of_week);
        }

        return $nextSchedule;
    }

    /**
     * Get schedule transition information - FIXED VERSION
     */
    protected function getScheduleTransition($employeeId)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return null;
        }

        $today = Carbon::today();
        
        // Get current schedule
        $currentSchedule = DB::table('schedule')
            ->where('schedule.employee_id', $employee->id)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.schedule_start_date', '<=', $today)
            ->where('schedule.schedule_end_date', '>=', $today)
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('shifts.deleted_at', 0)
            ->select(
                'schedule.*',
                'shifts.shift_name as current_shift_name',
                'shifts.start_time as current_start_time',
                'shifts.end_time as current_end_time'
            )
            ->first();

        // Get next schedule
        $nextSchedule = DB::table('schedule')
            ->where('schedule.employee_id', $employee->id)
            ->where('schedule.schedule_start_date', '>', $today)
            ->where('schedule.deleted_at', 0)
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('shifts.deleted_at', 0)
            ->select(
                'schedule.*',
                'shifts.shift_name as next_shift_name',
                'shifts.start_time as next_start_time',
                'shifts.end_time as next_end_time'
            )
            ->orderBy('schedule.schedule_start_date', 'asc')
            ->first();

        if ($currentSchedule && $nextSchedule) {
            $currentEndDate = Carbon::parse($currentSchedule->schedule_end_date);
            $nextStartDate = Carbon::parse($nextSchedule->schedule_start_date);
            
            // Check if there's a gap between schedules
            $hasGap = $nextStartDate->gt($currentEndDate->copy()->addDay());
            $gapDays = $hasGap ? $currentEndDate->diffInDays($nextStartDate) - 1 : 0;
            
            // Check if schedules are different
            $isDifferentShift = $currentSchedule->shift_id != $nextSchedule->shift_id;
            
            return [
                'has_transition' => true,
                'current_ends' => $currentEndDate->format('M d, Y'),
                'next_starts' => $nextStartDate->format('M d, Y'),
                'has_gap' => $hasGap,
                'gap_days' => $gapDays,
                'is_different_shift' => $isDifferentShift,
                'current_shift' => $currentSchedule->current_shift_name,
                'next_shift' => $nextSchedule->next_shift_name,
                'days_until_transition' => $today->diffInDays($currentEndDate, false)
            ];
        }

        return null;
    }

    /**
     * Get upcoming schedule changes - FIXED VERSION
     */
    protected function getUpcomingScheduleChanges($employeeId)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return [];
        }

        $today = Carbon::today();
        $futureDate = $today->copy()->addDays(30); // Look 30 days ahead

        // Get upcoming new schedules
        $upcomingSchedules = DB::table('schedule')
            ->where('schedule.employee_id', $employee->id)
            ->where('schedule.schedule_start_date', '>', $today)
            ->where('schedule.schedule_start_date', '<=', $futureDate)
            ->where('schedule.deleted_at', 0)
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('shifts.deleted_at', 0)
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time'
            )
            ->orderBy('schedule.schedule_start_date', 'asc')
            ->get();

        // Add type information and formatting
        foreach ($upcomingSchedules as $schedule) {
            $schedule->type = 'new';
            $schedule->start_time_formatted = Carbon::parse($schedule->start_time)->format('h:i A');
            $schedule->end_time_formatted = Carbon::parse($schedule->end_time)->format('h:i A');
            $schedule->formatted_start_date = Carbon::parse($schedule->schedule_start_date)->format('M d, Y');
            $schedule->days_until = Carbon::today()->diffInDays(Carbon::parse($schedule->schedule_start_date), false);
        }

        return $upcomingSchedules;
    }

    // Keep all other existing methods unchanged...
    protected function getEmployeeOvertimeSummary($employeeId)
    {
        $totalOvertime = DB::table('overtime')
            ->where('employee_name', $employeeId)
            ->where(function($q) {
                $q->where('deleted_at', '!=', 1)->orWhereNull('deleted_at');
            })
            ->sum('overtime_hours');

        $pending = DB::table('overtime')
            ->where('employee_name', $employeeId)
            ->where('status', 'Pending')
            ->where(function($q) {
                $q->where('deleted_at', '!=', 1)->orWhereNull('deleted_at');
            })
            ->count();

        $approved = DB::table('overtime')
            ->where('employee_name', $employeeId)
            ->where('status', 'Approved')
            ->where(function($q) {
                $q->where('deleted_at', '!=', 1)->orWhereNull('deleted_at');
            })
            ->count();

        $rejected = DB::table('overtime')
            ->where('employee_name', $employeeId)
            ->where('status', 'Rejected')
            ->where(function($q) {
                $q->where('deleted_at', '!=', 1)->orWhereNull('deleted_at');
            })
            ->count();

        return [
            'total_hours' => $totalOvertime ?? 0,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    protected function getEmployeeProjectStats($employeeId)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return [
                'total' => 0,
                'active' => 0,
                'completed' => 0
            ];
        }

        return [
            'total' => DB::table('projects')
                ->where('deleted_at', 0)
                ->where(function($query) use ($employee) {
                    $query->where('projectleader', $employee->id)
                          ->orWhereRaw("FIND_IN_SET(?, team)", [$employee->id]);
                })
                ->count(),
                        
            'active' => DB::table('projects')
                ->where('deleted_at', 0)
                ->where('status', 'active')
                ->where(function($query) use ($employee) {
                    $query->where('projectleader', $employee->id)
                          ->orWhereRaw("FIND_IN_SET(?, team)", [$employee->id]);
                })
                ->count(),
                        
            'completed' => DB::table('projects')
                ->where('deleted_at', 0)
                ->where('status', 'completed')
                ->where(function($query) use ($employee) {
                    $query->where('projectleader', $employee->id)
                          ->orWhereRaw("FIND_IN_SET(?, team)", [$employee->id]);
                })
                ->count(),
        ];
    }

    protected function getEmployeeTaskStats($userId)
    {
        if (!$userId) return [
            'total' => 0, 'pending' => 0, 'in_progress' => 0, 'completed' => 0
        ];
        
        return [
            'total' => DB::table('tasks')->where('assigned_to', $userId)->count(),
            'pending' => DB::table('tasks')->where('assigned_to', $userId)->where('status', 'pending')->count(),
            'in_progress' => DB::table('tasks')->where('assigned_to', $userId)->where('status', 'in_progress')->count(),
            'completed' => DB::table('tasks')->where('assigned_to', $userId)->where('status', 'completed')->count(),
        ];
    }

    protected function getRecentProjects($employeeId, $limit = 3)
    {
        // First get the internal employee ID
        $employee = DB::table('allemployees')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return collect();
        }

        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->where('status', 'active')  // Only active projects
            ->where(function($query) use ($employee) {
                $query->where('projectleader', $employee->id)
                      ->orWhereRaw("FIND_IN_SET(?, team)", [$employee->id]);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Format team data
        foreach ($projects as $project) {
            $teamIds = array_filter(explode(',', $project->team ?? ''));
            $teamMembers = [];
            if (!empty($teamIds)) {
                $teamMembers = DB::table('allemployees')
                    ->whereIn('id', $teamIds)
                    ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                    ->pluck('name')
                    ->toArray();
            }
            $project->teamMembers = $teamMembers;
            $project->isLeader = ($project->projectleader == $employee->id);
            
            // Add task statistics for the project
            $projectTasks = DB::table('tasks')->where('projects', $project->projectid)->get();
            $project->totalTasks = $projectTasks->count();
            $project->completedTasks = $projectTasks->where('status', 'completed')->count();
            $project->pendingTasks = $projectTasks->where('status', 'pending')->count();
            $project->inProgressTasks = $projectTasks->where('status', 'in_progress')->count();
            $project->progressPercentage = $project->totalTasks > 0 
                ? round(($project->completedTasks / $project->totalTasks) * 100) 
                : 0;
        }

        return $projects;
    }

    protected function getUpcomingHolidays()
    {
        return DB::table('holidays')
            ->where('holidaydate', '>=', now()->format('Y-m-d'))
            ->orderBy('holidaydate', 'asc')
            ->limit(5)
            ->get()
            ->map(function($holiday) {
                $holiday->formatted_date = Carbon::parse($holiday->holidaydate)->format('d M Y');
                $holiday->day = Carbon::parse($holiday->holidaydate)->format('l');
                $holiday->is_next = false;
                return $holiday;
            })
            ->map(function($holiday, $key) {
                if ($key === 0) {
                    $holiday->is_next = true;
                }
                return $holiday;
            });
    }

    protected function getEmployeeLeaveStats($userId)
    {
        $currentYear = date('Y');
                
        $pendingLeaves = DB::table('employee_leaves')
            ->where('employee_id', $userId)
            ->where('status', 'pending')
            ->count();
                    
        $approvedLeaves = DB::table('employee_leaves')
            ->where('employee_id', $userId)
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->count();
                    
        $rejectedLeaves = DB::table('employee_leaves')
            ->where('employee_id', $userId)
            ->where('status', 'rejected')
            ->whereYear('from_date', $currentYear)
            ->count();

        // Get remaining casual leaves
        $totalAnnualLeaves = DB::table('annual_leaves')->value('days') ?? 0;
        $takenAnnualLeaves = DB::table('employee_leaves')
            ->where('employee_id', $userId)
            ->where('leave_type', 'Casual Leave')
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->sum('no_of_days');

        $remainingLeaves = $totalAnnualLeaves - $takenAnnualLeaves;

        return [
            'pending' => $pendingLeaves,
            'approved' => $approvedLeaves,
            'rejected' => $rejectedLeaves,
            'remaining' => $remainingLeaves > 0 ? $remainingLeaves : 0,
            'totalAnnual' => $totalAnnualLeaves,
        ];
    }

    protected function getRecentLeaves($userId, $limit = 5)
    {
        return DB::table('employee_leaves')
            ->where('employee_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($leave) {
                $leave->formatted_from = Carbon::parse($leave->from_date)->format('d M Y');
                $leave->formatted_to = Carbon::parse($leave->to_date)->format('d M Y');
                return $leave;
            });
    }

    protected function getEmployeeTicketStats($userId)
    {
        return [
            'total' => DB::table('tickets')
                ->where(function($q) use ($userId) {
                    $q->where('raised_by', $userId);
                })
                ->count(),
                            
            'open' => DB::table('tickets')
                ->whereIn('states', ['new', 'open', 'in progress', 'on hold', 'reopened'])
                ->where(function($q) use ($userId) {
                    $q->where('raised_by', $userId);
                })
                ->count(),
                            
            'closed' => DB::table('tickets')
                ->where('states', 'closed')
                ->where(function($q) use ($userId) {
                    $q->where('raised_by', $userId);
                })
                ->count(),
        ];
    }

    protected function getRecentTickets($userId, $limit = 3)
    {
        return DB::table('tickets')
            ->leftJoin('allemployees as creator', 'tickets.raised_by', '=', 'creator.id')
            ->select(
                'tickets.*',
                'creator.firstname as creator_firstname',
                'creator.lastname as creator_lastname'
            )
            ->where(function($q) use ($userId) {
                $q->where('raised_by', $userId);
            })
            ->orderBy('tickets.created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
