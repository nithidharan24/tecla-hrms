<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Traits\BuildsAnnouncementItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use App\Models\Announcement;

class DashboardController extends Controller
{
    use BuildsAnnouncementItems;
    public function index()
{
    // Fetch the total number of projects, clients, tasks, and employees
    $totalProjects = DB::table('projects')->where('deleted_at', 0)->count();
    $totalClients = DB::table('clients')->where('deleted_at', 0)->count();
    $totalTasksCount = DB::table('tasks')->where('deleted_at', 0)->count();
    $totalEmployees = DB::table('allemployees')->where('deleted_at', 0)->count();
    $activeEmployees = DB::table('allemployees')->where('deleted_at', 0)->where('status', 'active')->count();
    $newJoinersThisMonth = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereYear('joiningdate', now()->year)
        ->whereMonth('joiningdate', now()->month)
        ->count();
    $employeesOnNoticePeriod = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereIn('status', ['notice', 'notice period', 'on_notice', 'resigned'])
        ->count();
    $employeesExitingThisMonth = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereIn('status', ['inactive', 'terminated', 'exit', 'exited'])
        ->whereYear('updated_at', now()->year)
        ->whereMonth('updated_at', now()->month)
        ->count();
    $departmentEmployeeCounts = DB::table('allemployees as ae')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('COUNT(*) as count'))
        ->where('ae.deleted_at', 0)
        ->groupBy('d.department')
        ->orderBy('count', 'desc')
        ->limit(8)
        ->get();

    // Fetch the count of newly added employees (e.g., employees added in the last 7 days)
    $newEmployees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('created_at', '>=', now()->subDays(7))
        ->count();

    // PROMOTION MANAGEMENT DATA - NEW ADDITION

    // Total Promotions
    $totalPromotions = DB::table('promotions')->count();
    
    // Promotions This Month
    $promotionsThisMonth = DB::table('promotions')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
        
    // Promotions This Year
    $promotionsThisYear = DB::table('promotions')
        ->whereYear('created_at', now()->year)
        ->count();
        
    // Recent Promotions (last 30 days)
    $recentPromotionsCount = DB::table('promotions')
        ->where('created_at', '>=', now()->subDays(30))
        ->count();
        
    // Current vs Previous Month Promotions
    $currentMonthPromotions = DB::table('promotions')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
        
    $previousMonthPromotions = DB::table('promotions')
        ->whereYear('created_at', now()->subMonth()->year)
        ->whereMonth('created_at', now()->subMonth()->month)
        ->count();
        
    // Calculate percentage change for promotions
    $promotionsPercentageChange = $previousMonthPromotions > 0 
        ? (($currentMonthPromotions - $previousMonthPromotions) / $previousMonthPromotions) * 100 
        : 0;

    // Monthly Promotion Trend (Last 6 months)
    $promotionTrendData = [];
    $promotionTrendLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = DB::table('promotions')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $promotionTrendData[] = $count;
        $promotionTrendLabels[] = $date->format('M Y');
    }

    // Promotions by Department
    $promotionsByDepartment = DB::table('promotions as p')
        ->join('department as d', 'p.department_id', '=', 'd.id')
        ->select('d.department', DB::raw('COUNT(*) as count'))
        ->groupBy('d.department', 'd.id')
        ->orderBy('count', 'desc')
        ->limit(6)
        ->get();

    // Recent Promotions with Employee Details
    $recentPromotions = DB::table('promotions as p')
        ->join('allemployees as ae', 'p.employee_id', '=', 'ae.employeeid')
        ->join('department as d', 'p.department_id', '=', 'd.id')
        ->join('designation as from_des', 'p.promotion_from', '=', 'from_des.id')
        ->join('designation as to_des', 'p.promotion_to', '=', 'to_des.id')
        ->select(
            'p.*',
            'ae.firstname',
            'ae.lastname',
            'ae.employeeid',
            'd.department',
            'from_des.designation as from_designation',
            'to_des.designation as to_designation'
        )
        ->where('ae.deleted_at', 0)
        ->orderBy('p.created_at', 'desc')
        ->limit(5)
        ->get();

    // Promotion Rate (promotions per employee ratio)
    $promotionRate = $totalEmployees > 0 ? round(($totalPromotions / $totalEmployees) * 100, 1) : 0;

    // Average Time Between Promotions (in months)
    $avgTimeBetweenPromotions = DB::table('promotions')
        ->selectRaw('AVG(TIMESTAMPDIFF(MONTH, created_at, promotion_date)) as avg_months')
        ->value('avg_months') ?? 0;

    // Most Promoted Departments
    $mostPromotedDepartments = DB::table('promotions as p')
        ->join('department as d', 'p.department_id', '=', 'd.id')
        ->select('d.department', DB::raw('COUNT(*) as promotion_count'))
        ->groupBy('d.department', 'd.id')
        ->orderBy('promotion_count', 'desc')
        ->limit(3)
        ->get();

    // GOAL MANAGEMENT DATA

    // Total Goals and Goal Types
    $totalGoalTypes = DB::table('goal_types')->where('deleted_at', 0)->count();
    $totalGoalTracks = DB::table('goal_tracks')->count();
    $activeGoals = DB::table('goal_tracks')->where('status', 'active')->count();
    
    // Goal Progress Statistics
    $completedGoals = DB::table('goal_tracks')
        ->where('progress', 100)
        ->count();
    $inProgressGoals = DB::table('goal_tracks')
        ->where('progress', '>', 0)
        ->where('progress', '<', 100)
        ->count();
    $notStartedGoals = DB::table('goal_tracks')
        ->where('progress', 0)
        ->count();
        
    // Average Goal Progress
    $averageGoalProgress = DB::table('goal_tracks')
        ->avg('progress') ?? 0;
        
    // Goals Due This Month
    $goalsDueThisMonth = DB::table('goal_tracks')
        ->whereYear('end_date', now()->year)
        ->whereMonth('end_date', now()->month)
        ->count();
        
    // Overdue Goals
    $overdueGoals = DB::table('goal_tracks')
        ->where('end_date', '<', now())
        ->where('progress', '<', 100)
        ->count();

    // Monthly Goal Creation Trend (Last 6 months)
    $goalTrendData = [];
    $goalTrendLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = DB::table('goal_tracks')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $goalTrendData[] = $count;
        $goalTrendLabels[] = $date->format('M Y');
    }
    
    // Goal Progress Distribution for Chart
    $goalProgressData = [
        'completed' => $completedGoals,
        'in_progress' => $inProgressGoals,
        'not_started' => $notStartedGoals,
        'overdue' => $overdueGoals
    ];
    
    // Goal Types Distribution
    $goalTypeDistribution = DB::table('goal_tracks as gt')
        ->join('goal_types as gtype', 'gt.goal', '=', 'gtype.id')
        ->select('gtype.goal', DB::raw('COUNT(*) as count'))
        ->groupBy('gtype.goal', 'gtype.id')
        ->get();
        
    // Monthly Goal Completion Rate (Last 6 months)
    $completionRateData = [];
    $completionRateLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $totalGoalsInMonth = DB::table('goal_tracks')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $completedInMonth = DB::table('goal_tracks')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->where('progress', 100)
            ->count();
        $rate = $totalGoalsInMonth > 0 ? round(($completedInMonth / $totalGoalsInMonth) * 100, 1) : 0;
        $completionRateData[] = $rate;
        $completionRateLabels[] = $date->format('M Y');
    }
    
    // Current vs Previous Month Goals
    $currentMonthGoals = DB::table('goal_tracks')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
        
    $previousMonthGoals = DB::table('goal_tracks')
        ->whereYear('created_at', now()->subMonth()->year)
        ->whereMonth('created_at', now()->subMonth()->month)
        ->count();
        
    // Calculate percentage change for goals
    $goalsPercentageChange = $previousMonthGoals > 0 
        ? (($currentMonthGoals - $previousMonthGoals) / $previousMonthGoals) * 100 
        : 0;

    // Current vs Previous Month Goal Completion
    $currentMonthCompleted = DB::table('goal_tracks')
        ->whereYear('end_date', now()->year)
        ->whereMonth('end_date', now()->month)
        ->where('progress', 100)
        ->count();
        
    $previousMonthCompleted = DB::table('goal_tracks')
        ->whereYear('end_date', now()->subMonth()->year)
        ->whereMonth('end_date', now()->subMonth()->month)
        ->where('progress', 100)
        ->count();
        
    // Calculate percentage change for completions
    $completionPercentageChange = $previousMonthCompleted > 0 
        ? (($currentMonthCompleted - $previousMonthCompleted) / $previousMonthCompleted) * 100 
        : 0;

    // HOLIDAY MANAGEMENT DATA
    
    // Total Holidays
    $totalHolidays = DB::table('holidays')->count();
    
    // Upcoming Holidays (next 30 days)
    $upcomingHolidays = DB::table('holidays')
        ->where('holidaydate', '>=', now()->format('Y-m-d'))
        ->where('holidaydate', '<=', now()->addDays(30)->format('Y-m-d'))
        ->orderBy('holidaydate', 'asc')
        ->get();
        
    // Holidays This Month
    $holidaysThisMonth = DB::table('holidays')
        ->whereYear('holidaydate', now()->year)
        ->whereMonth('holidaydate', now()->month)
        ->count();
        
    // Next Holiday
    $nextHoliday = DB::table('holidays')
        ->where('holidaydate', '>=', now()->format('Y-m-d'))
        ->orderBy('holidaydate', 'asc')
        ->first();
        
    // Recent Holidays (last 30 days)
    $recentHolidays = DB::table('holidays')
        ->where('holidaydate', '>=', now()->subDays(30)->format('Y-m-d'))
        ->where('holidaydate', '<', now()->format('Y-m-d'))
        ->orderBy('holidaydate', 'desc')
        ->limit(5)
        ->get();
        
    // Holiday Distribution by Month (Current Year)
    $holidayMonthlyData = [];
    $holidayMonthlyLabels = [];
    for ($i = 1; $i <= 12; $i++) {
        $count = DB::table('holidays')
            ->whereYear('holidaydate', now()->year)
            ->whereMonth('holidaydate', $i)
            ->count();
        $holidayMonthlyData[] = $count;
        $holidayMonthlyLabels[] = Carbon::create()->month($i)->format('M');
    }
    
    // Days until next holiday
    $daysUntilNextHoliday = $nextHoliday ? 
        Carbon::parse($nextHoliday->holidaydate)->diffInDays(now()) : null;

    // PERFORMANCE INDICATOR DATA
    
    // Total Performance Indicators
    $totalPerformanceIndicators = DB::table('performance_indicator')
        ->where('deleted_at', 0)
        ->count();
        
    // Active Performance Indicators
    $activePerformanceIndicators = DB::table('performance_indicator')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->count();
        
    // Inactive Performance Indicators
    $inactivePerformanceIndicators = DB::table('performance_indicator')
        ->where('deleted_at', 0)
        ->where('status', 'inactive')
        ->count();
        
    // Performance Indicators by Designation
    $performanceIndicatorsByDesignation = DB::table('performance_indicator as pi')
        ->leftJoin('designation as d', 'pi.designation_id', '=', 'd.id')
        ->select('d.designation', DB::raw('COUNT(*) as count'))
        ->where('pi.deleted_at', 0)
        ->where('d.deleted_at', 0)
        ->groupBy('d.designation', 'd.id')
        ->limit(6)
        ->get();
        
    // Recent Performance Indicators
    $recentPerformanceIndicators = DB::table('performance_indicator as pi')
        ->leftJoin('designation as d', 'pi.designation_id', '=', 'd.id')
        ->select(
            'pi.*',
            'd.designation'
        )
        ->where('pi.deleted_at', 0)
        ->where('d.deleted_at', 0)
        ->orderBy('pi.created_at', 'desc')
        ->limit(5)
        ->get();
        
    // Performance Indicator Status Distribution
    $performanceIndicatorStatusData = [
        'active' => $activePerformanceIndicators,
        'inactive' => $inactivePerformanceIndicators
    ];

    // Monthly Performance Indicator Creation Trend (Last 6 months)
    $performanceIndicatorTrendData = [];
    $performanceIndicatorTrendLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = DB::table('performance_indicator')
            ->where('deleted_at', 0)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $performanceIndicatorTrendData[] = $count;
        $performanceIndicatorTrendLabels[] = $date->format('M Y');
    }

    // PERFORMANCE REVIEW DATA
    
    // Total Performance Reviews
    $totalPerformanceReviews = DB::table('performance_review_basic_infos')
        ->where('deleted_at', 0)
        ->count();
        
    // Performance Reviews This Month
    $performanceReviewsThisMonth = DB::table('performance_review_basic_infos')
        ->where('deleted_at', 0)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
        
    // Performance Reviews This Year
    $performanceReviewsThisYear = DB::table('performance_review_basic_infos')
        ->where('deleted_at', 0)
        ->whereYear('created_at', now()->year)
        ->count();
        
    // Pending Performance Reviews (assuming reviews without signatures are pending)
    $pendingPerformanceReviews = DB::table('performance_review_basic_infos as prbi')
        ->leftJoin('performance_review_signatures as prs', 'prbi.id', '=', 'prs.review_id')
        ->where('prbi.deleted_at', 0)
        ->whereNull('prs.hrd_signature')
        ->count();
        
    // Recent Performance Reviews
    $recentPerformanceReviews = DB::table('performance_review_basic_infos as prbi')
        ->leftJoin('allemployees as ae', 'prbi.employee_name', '=', 'ae.id')
        ->leftJoin('designation as d', 'prbi.designation_id', '=', 'd.id')
        ->leftJoin('department as dept', 'prbi.department_id', '=', 'dept.id')
        ->select(
            'prbi.*',
            'ae.firstname',
            'ae.lastname',
            'd.designation',
            'dept.department'
        )
        ->where('prbi.deleted_at', 0)
        ->where('ae.deleted_at', 0)
        ->orderBy('prbi.created_at', 'desc')
        ->limit(5)
        ->get();
        
    // Performance Review Status Distribution
    $performanceReviewStatusData = [
        'completed' => $totalPerformanceReviews - $pendingPerformanceReviews,
        'pending' => $pendingPerformanceReviews
    ];

    // Monthly Performance Review Creation Trend (Last 6 months)
    $performanceReviewTrendData = [];
    $performanceReviewTrendLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = DB::table('performance_review_basic_infos')
            ->where('deleted_at', 0)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
        $performanceReviewTrendData[] = $count;
        $performanceReviewTrendLabels[] = $date->format('M Y');
    }
    
    // Performance Reviews by Department
    $performanceReviewsByDepartment = DB::table('performance_review_basic_infos as prbi')
        ->leftJoin('department as dept', 'prbi.department_id', '=', 'dept.id')
        ->select('dept.department', DB::raw('COUNT(*) as count'))
        ->where('prbi.deleted_at', 0)
        ->groupBy('dept.department', 'dept.id')
        ->limit(6)
        ->get();

    // ATTENDANCE MANAGEMENT DATA - FIXED TO USE ACTUAL SCHEDULE TIMES
    
    // Today's Date
    $today = now()->format('Y-m-d');
    
    // Get first punch-in per employee today (deduplicated)
    $firstPunchins = DB::table('attendances')
        ->select('employee_id', DB::raw('MIN(punch_in) as first_punch_in'), DB::raw('MAX(punch_out) as last_punch_out'))
        ->whereDate('date', $today)
        ->whereNotNull('punch_in')
        ->groupBy('employee_id');

    // Get all active employees with their attendance (LEFT JOIN to include absentees)
    $todayAttendance = DB::table('allemployees as ae')
        ->leftJoinSub($firstPunchins, 'fp', 'ae.id', '=', 'fp.employee_id')
        ->leftJoin('schedule as s', function($join) use ($today) {
            $join->on('ae.id', '=', 's.employee_id')
                ->where('s.schedule_start_date', '<=', $today)
                ->where('s.schedule_end_date', '>=', $today)
                ->where('s.publish', 1)
                ->where('s.deleted_at', 0)
                ->where('s.is_current', 1);
        })
        ->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
        ->select(
            'ae.firstname',
            'ae.lastname',
            'd.department',
            'des.designation',
            'fp.first_punch_in as punch_in',
            'fp.last_punch_out as punch_out',
            'sh.start_time as scheduled_start',
            'sh.end_time as scheduled_end'
        )
        ->where('ae.deleted_at', 0)
        ->where('ae.status', 'active')
        ->orderByRaw('fp.first_punch_in IS NULL ASC')
        ->orderBy('fp.first_punch_in', 'desc')
        ->get();

    // Process attendance status based on actual schedule
    $todayAttendance->each(function($attendance) {
        if (!$attendance->punch_in) {
            $attendance->status = 'Absent';
            $attendance->is_late = false;
            $attendance->late_minutes = 0;
            return;
        }

        $checkInTime = \Carbon\Carbon::parse($attendance->punch_in);

        if ($attendance->scheduled_start) {
            $scheduledStart = \Carbon\Carbon::parse($attendance->scheduled_start);
            $gracePeriodEnd = $scheduledStart->copy()->addMinutes(5);

            if ($checkInTime->lte($gracePeriodEnd)) {
                $attendance->status = 'On Time';
                $attendance->is_late = false;
                $attendance->late_minutes = 0;
            } else {
                $attendance->status = 'Late';
                $attendance->is_late = true;
                $attendance->late_minutes = $checkInTime->diffInMinutes($scheduledStart);
            }
        } else {
            $defaultStart = \Carbon\Carbon::parse($attendance->punch_in)->startOfDay()->setTime(9, 30);
            $gracePeriodEnd = $defaultStart->copy()->addMinutes(5);

            if ($checkInTime->lte($gracePeriodEnd)) {
                $attendance->status = 'On Time';
                $attendance->is_late = false;
                $attendance->late_minutes = 0;
            } else {
                $attendance->status = 'Late';
                $attendance->is_late = true;
                $attendance->late_minutes = $checkInTime->diffInMinutes($defaultStart);
            }
        }
    });

    // Update other attendance metrics using actual schedule times
    $presentToday = $todayAttendance->whereNotNull('punch_in')->count();
    $absentToday = $todayAttendance->whereNull('punch_in')->count();

    // Collections for dashboard popup modals
    $presentEmployees = $todayAttendance->filter(fn($a) => !is_null($a->punch_in))->values();
    $absentEmployees  = $todayAttendance->filter(fn($a) => is_null($a->punch_in))->values();
    $lateEmployees    = $todayAttendance->filter(fn($a) => $a->is_late && !is_null($a->punch_in))->values();

    // Late arrivals based on actual schedule times (only present employees)
    $lateArrivalsToday = $todayAttendance->where('is_late', true)->whereNotNull('punch_in')->count();

    // Early departures - compare with scheduled end time
    $earlyDeparturesToday = DB::table('attendances as a')
        ->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')
        ->leftJoin('schedule as s', function($join) use ($today) {
            $join->on('ae.id', '=', 's.employee_id')
                ->where('s.schedule_start_date', '<=', $today)
                ->where('s.schedule_end_date', '>=', $today)
                ->where('s.publish', 1)
                ->where('s.deleted_at', 0)
                ->where('s.is_current', 1);
        })
        ->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')
        ->whereDate('a.date', $today)
        ->whereNotNull('a.punch_in')
        ->whereNotNull('a.punch_out')
        ->whereNotNull('sh.end_time')
        ->whereRaw('TIME(a.punch_out) < TIME(sh.end_time)')
        ->count();
        
    // Average Attendance Rate (Last 30 days)
    $attendanceRate = 0;
    if ($totalEmployees > 0) {
        $totalPossibleAttendance = $totalEmployees * 30; // 30 days
        $actualAttendance = DB::table('attendances')
            ->whereDate('date', '>=', now()->subDays(30))
            ->whereNotNull('punch_in')
            ->count();
        $attendanceRate = round(($actualAttendance / $totalPossibleAttendance) * 100, 1);
    }
    
    // Punctuality Rate - based on actual schedule
    $punctualityRate = 0;
    if ($presentToday > 0) {
        $onTimeToday = $todayAttendance->where('status', 'On Time')->count();
        $punctualityRate = round(($onTimeToday / $presentToday) * 100, 1);
    }
    
    // Overtime Hours Today
    $overtimeHoursToday = DB::table('attendances')
        ->whereDate('date', $today)
        ->whereNotNull('punch_in')
        ->whereNotNull('punch_out')
        ->whereRaw('TIMESTAMPDIFF(HOUR, punch_in, punch_out) > 8')
        ->sum(DB::raw('GREATEST(0, TIMESTAMPDIFF(HOUR, punch_in, punch_out) - 8)'));
        
    // Weekly Attendance Trend (Last 7 days)
    $weeklyAttendanceData = [];
    $weeklyAttendanceLabels = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $count = DB::table('attendances')
            ->whereDate('date', $date->format('Y-m-d'))
            ->whereNotNull('punch_in')
            ->distinct('employee_id')
            ->count();
        $weeklyAttendanceData[] = $count;
        $weeklyAttendanceLabels[] = $date->format('M d');
    }
    
    // Monthly Attendance Trend (Last 6 months)
    $monthlyAttendanceData = [];
    $monthlyAttendanceLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $daysInMonth = $date->daysInMonth;
        $totalPossible = $totalEmployees * $daysInMonth;
        $actualAttendance = DB::table('attendances')
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->whereNotNull('punch_in')
            ->count();
        $rate = $totalPossible > 0 ? round(($actualAttendance / $totalPossible) * 100, 1) : 0;
        $monthlyAttendanceData[] = $rate;
        $monthlyAttendanceLabels[] = $date->format('M Y');
    }
    
    // Department-wise Attendance Labels and Data
    $departmentAttendance = DB::table('allemployees as ae')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->leftJoin('attendances as a', function ($join) use ($today) {
            $join->on('ae.id', '=', 'a.employee_id')
                ->whereDate('a.date', $today)
                ->whereNotNull('a.punch_in');
        })
        ->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('COUNT(DISTINCT a.employee_id) as present_count'))
        ->where('ae.deleted_at', 0)
        ->groupBy('d.department')
        ->orderBy('present_count', 'desc')
        ->limit(6)
        ->get();
    $departmentAttendanceLabels = $departmentAttendance->pluck('department')->toArray();
    $departmentAttendanceData = $departmentAttendance->pluck('present_count')->toArray();
    
    // Recent Attendance Records
    $recentAttendance = DB::table('attendances as a')
        ->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
        ->select(
            'ae.firstname',
            'ae.lastname',
            'd.department',
            'des.designation',
            'a.punch_in',
            'a.punch_out',
            'a.date'
        )
        ->where('ae.deleted_at', 0)
        ->orderBy('a.date', 'desc')
        ->orderBy('a.punch_in', 'desc')
        ->limit(3) 
        ->get();

    // // TICKETS MANAGEMENT DATA
    
    // // Get current user role and ID for ticket filtering
    // $employeeId = Session::get('user_id');
    // $role = Session::get('role');
    
    // // Base query for tickets based on role
    // $ticketQuery = DB::table('tickets');
    
    // // If employee, only count their assigned tickets OR tickets they created
    // if ($role === 'employee') {
    //     $ticketQuery->where(function($q) use ($employeeId) {
    //         $q->where('assigned_to', $employeeId);
    //     });
    // }
    
    // // Total Tickets
    // $totalTickets = (clone $ticketQuery)->count();
    
    // // Tickets by Status
    // $newTickets = (clone $ticketQuery)->where('states', 'new')->count();
    // $openTickets = (clone $ticketQuery)->where('states', 'open')->count();
    // $inProgressTickets = (clone $ticketQuery)->where('states', 'in progress')->count();
    // $closedTickets = (clone $ticketQuery)->where('states', 'closed')->count();
    // $onHoldTickets = (clone $ticketQuery)->where('states', 'on hold')->count();
    // $cancelledTickets = (clone $ticketQuery)->where('states', 'cancelled')->count();
    // $reopenedTickets = (clone $ticketQuery)->where('states', 'reopened')->count();
    
    // // Tickets by Priority
    // $highPriorityTickets = (clone $ticketQuery)->where('priority', 'High')->count();
    // $mediumPriorityTickets = (clone $ticketQuery)->where('priority', 'Medium')->count();
    // $lowPriorityTickets = (clone $ticketQuery)->where('priority', 'Low')->count();
    
    // // Tickets This Month
    // $ticketsThisMonth = (clone $ticketQuery)
    //     ->whereYear('created_at', now()->year)
    //     ->whereMonth('created_at', now()->month)
    //     ->count();
        
    // // Tickets This Week
    // $ticketsThisWeek = (clone $ticketQuery)
    //     ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
    //     ->count();
        
    // // Tickets Today
    // $ticketsToday = (clone $ticketQuery)
    //     ->whereDate('created_at', now()->format('Y-m-d'))
    //     ->count();
        
    // // Average Resolution Time (in days) for closed tickets
    // $avgResolutionTime = DB::table('tickets')
    //     ->where('states', 'closed')
    //     ->whereNotNull('updated_at')
    //     ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
    //     ->value('avg_days') ?? 0;
        
    // // Tickets Status Distribution for Chart
    // $ticketStatusData = [
    //     'new' => $newTickets,
    //     'open' => $openTickets,
    //     'in_progress' => $inProgressTickets,
    //     'on_hold' => $onHoldTickets,
    //     'closed' => $closedTickets,
    //     'cancelled' => $cancelledTickets,
    //     'reopened' => $reopenedTickets
    // ];
    
    // // Tickets Priority Distribution for Chart
    // $ticketPriorityData = [
    //     'high' => $highPriorityTickets,
    //     'medium' => $mediumPriorityTickets,
    //     'low' => $lowPriorityTickets
    // ];
    
    // // Monthly Ticket Creation Trend (Last 6 months)
    // $ticketTrendData = [];
    // $ticketTrendLabels = [];
    // for ($i = 5; $i >= 0; $i--) {
    //     $date = now()->subMonths($i);
    //     $query = DB::table('tickets')
    //         ->whereYear('created_at', $date->year)
    //         ->whereMonth('created_at', $date->month);
            
    //     // Apply role-based filtering
    //     if ($role === 'employee') {
    //         $query->where(function($q) use ($employeeId) {
    //             $q->where('assigned_to', $employeeId);
    //         });
    //     }
        
    //     $count = $query->count();
    //     $ticketTrendData[] = $count;
    //     $ticketTrendLabels[] = $date->format('M Y');
    // }
    
    // // Recent Tickets with Employee Details
    // $recentTicketsQuery = DB::table('tickets')
    //     ->leftJoin('allemployees as creator', 'tickets.raised_by', '=', 'creator.id')
    //     ->leftJoin('allemployees as assign1', 'tickets.assigned_to', '=', 'assign1.id')
    //     ->select(
    //         'tickets.*',
    //         'creator.firstname as creator_firstname',
    //         'creator.lastname as creator_lastname',
    //         'assign1.firstname as assign1_firstname',
    //         'assign1.lastname as assign1_lastname'
    //     );
        
    // // Apply role-based filtering for recent tickets
    // if ($role === 'employee') {
    //     $recentTicketsQuery->where(function($q) use ($employeeId) {
    //         $q->where('tickets.assigned_to', $employeeId);
    //     });
    // }
    
    // $recentTickets = $recentTicketsQuery
    //     ->orderBy('tickets.created_at', 'desc')
    //     ->limit(3)
    //     ->get();
        
    // // Urgent Tickets (High priority and not closed)
    // $urgentTicketsQuery = DB::table('tickets')
    //     ->leftJoin('allemployees as creator', 'tickets.raised_by', '=', 'creator.id')
    //     ->leftJoin('allemployees as assign1', 'tickets.assigned_to', '=', 'assigned_to.id')
    //     ->select(
    //         'tickets.*',
    //         'creator.firstname as creator_firstname',
    //         'creator.lastname as creator_lastname',
    //         'assign1.firstname as assign1_firstname',
    //         'assign1.lastname as assign1_lastname'
    //     )
    //     ->where('tickets.priority', 'High')
    //     ->whereNotIn('tickets.states', ['closed', 'cancelled']);
        
    // // Apply role-based filtering for urgent tickets
    // if ($role === 'employee') {
    //     $urgentTicketsQuery->where(function($q) use ($employeeId) {
    //         $q->where('tickets.assigned_to', $employeeId);
    //     });
    // }
    
    // $urgentTickets = $urgentTicketsQuery
    //     ->orderBy('tickets.created_at', 'desc')
    //     ->limit(5)
    //     ->get();
        
    // // Ticket Resolution Rate
    // $totalResolvedTickets = $closedTickets;
    // $ticketResolutionRate = $totalTickets > 0 ? round(($totalResolvedTickets / $totalTickets) * 100, 1) : 0;
    
    // // Current vs Previous Month Tickets
    // $currentMonthTickets = (clone $ticketQuery)
    //     ->whereYear('created_at', now()->year)
    //     ->whereMonth('created_at', now()->month)
    //     ->count();
        
    // $previousMonthTickets = (clone $ticketQuery)
    //     ->whereYear('created_at', now()->subMonth()->year)
    //     ->whereMonth('created_at', now()->subMonth()->month)
    //     ->count();
        
    // // Calculate percentage change for tickets
    // $ticketsPercentageChange = $previousMonthTickets > 0 
    //     ? (($currentMonthTickets - $previousMonthTickets) / $previousMonthTickets) * 100 
    //     : 0;

    // Fetch today's leave count
    $todayLeave = DB::table('employee_leaves')
        ->where('status', 'approved')
        ->whereDate('from_date', '<=', now()->format('Y-m-d'))
        ->whereDate('to_date', '>=', now()->format('Y-m-d'))
        ->count();

    // Fetch pending invoices count
    $pendingInvoices = DB::table('invoices')
        ->where('status', 'pending')
        ->count();

    $pendingLeaveRequests = DB::table('employee_leaves')->where('status', 'pending')->count();
    $approvedLeaves = DB::table('employee_leaves')->where('status', 'approved')->count();
    $rejectedLeaves = DB::table('employee_leaves')->whereIn('status', ['rejected', 'declined'])->count();
    $leaveTrendLabels = [];
    $leaveTrendData = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $leaveTrendLabels[] = $date->format('M Y');
        $leaveTrendData[] = DB::table('employee_leaves')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
    }

    $openPositions = DB::table('managejobs')->where('status', 'open')->sum('vacancies');
    $candidatesApplied = DB::table('candidate')->where('deleted_at', 0)->count();
    $interviewsScheduled = DB::table('interviews')
        ->where('interview_datetime', '>=', now())
        ->count();
    $interviewsToday = DB::table('interviews')
        ->whereDate('interview_datetime', now()->format('Y-m-d'))
        ->count();
    $offersReleased = DB::table('offer_letters')->where('is_active', 1)->count();
    $candidatesJoined = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereIn('source_type', ['candidate', 'recruitment'])
        ->count();
    $offerAcceptanceRate = $offersReleased > 0 ? round(($candidatesJoined / $offersReleased) * 100, 1) : 0;

    $payrollStatus = DB::table('employee_salaries')->where('approval_status', 'pending')->exists() ? 'Pending Approval' : 'Ready';
    $totalSalaryProcessed = DB::table('employee_salaries')->where('approval_status', 'approved')->sum('net_salary');
    $upcomingPayrollDate = now()->endOfMonth()->format('d M Y');
    $statutoryDeductions = DB::table('employee_salaries')->sum(DB::raw('COALESCE(tds,0) + COALESCE(esi,0) + COALESCE(pf,0) + COALESCE(tax,0) + COALESCE(welfare,0)'));
    $pendingSalaryApprovals = DB::table('employee_salaries')->where('approval_status', 'pending')->count();
    $payrollCostByDepartment = DB::table('employee_salaries as es')
        ->join('allemployees as ae', 'es.employee_id', '=', 'ae.id')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('SUM(es.net_salary) as amount'))
        ->where('ae.deleted_at', 0)
        ->groupBy('d.department')
        ->orderBy('amount', 'desc')
        ->limit(8)
        ->get();

    $assetsAssigned = DB::table('assets_assignment')->where('deleted_at', 0)->where('status', 'assigned')->count();
    $assetsDueForReturn = DB::table('assets_assignment')
        ->where('deleted_at', 0)
        ->where('status', 'assigned')
        ->whereNotNull('return_date')
        ->whereDate('return_date', '<=', now()->addDays(7)->format('Y-m-d'))
        ->count();
    $lostDamagedAssets = DB::table('assets_company')
        ->where('deleted_at', 0)
        ->whereIn('condition', ['lost', 'damaged'])
        ->count();

    $attritionRate = $totalEmployees > 0 ? round(($employeesExitingThisMonth / $totalEmployees) * 100, 1) : 0;
    $employeeTurnoverRate = $attritionRate;
    $absenteeismRate = $totalEmployees > 0 ? round(($absentToday / $totalEmployees) * 100, 1) : 0;
    $headcountGrowth = $totalEmployees > 0 ? round(($newJoinersThisMonth / $totalEmployees) * 100, 1) : 0;
    $costPerHire = $candidatesJoined > 0 ? round($totalSalaryProcessed / $candidatesJoined, 2) : 0;
    $goalCompletionRate = $totalGoalTracks > 0 ? round(($completedGoals / $totalGoalTracks) * 100, 1) : 0;
    $kpiAchievement = round($averageGoalProgress, 1);
    $birthdaysThisMonth = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->when(Schema::hasColumn('allemployees', 'date_of_birth'), function ($query) {
            $query->whereMonth('date_of_birth', now()->month);
        }, function ($query) {
            $query->whereRaw('1 = 0');
        })
        ->count();
    $workAnniversariesThisMonth = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->whereMonth('joiningdate', now()->month)
        ->count();
    $policyUpdates = Schema::hasTable('policies') ? DB::table('policies')->count() : 0;
    $pendingApprovals = $pendingLeaveRequests + $pendingSalaryApprovals + $pendingInvoices + $pendingPerformanceReviews;

    // Pending approvals detail lists for dashboard popup
    $pendingLeavesList = DB::table('employee_leaves as el')
        ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->select(
            'el.id',
            'ae.firstname', 'ae.lastname',
            DB::raw('COALESCE(d.department, "Unassigned") as department'),
            'el.leave_type', 'el.from_date', 'el.to_date', 'el.no_of_days', 'el.leave_reason'
        )
        ->where('el.status', 'pending')
        ->orderBy('el.created_at', 'desc')
        ->limit(30)
        ->get();

    $pendingPermissionsList = Schema::hasTable('employee_permissions')
        ? DB::table('employee_permissions as ep')
            ->join('allemployees as ae', 'ep.employee_id', '=', 'ae.id')
            ->leftJoin('department as d', 'ae.department', '=', 'd.id')
            ->select(
                'ep.id',
                'ae.firstname', 'ae.lastname',
                DB::raw('COALESCE(d.department, "Unassigned") as department'),
                'ep.permission_date', 'ep.start_time', 'ep.end_time', 'ep.duration', 'ep.permission_reason'
            )
            ->where('ep.status', 'pending')
            ->orderBy('ep.created_at', 'desc')
            ->limit(30)
            ->get()
        : collect();

    $pendingSalariesList = DB::table('employee_salaries as es')
        ->join('allemployees as ae', 'es.employee_id', '=', 'ae.id')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->select(
            'es.id',
            'ae.firstname', 'ae.lastname',
            DB::raw('COALESCE(d.department, "Unassigned") as department'),
            'es.net_salary', 'es.approval_status', 'es.created_at'
        )
        ->where('es.approval_status', 'pending')
        ->orderBy('es.created_at', 'desc')
        ->limit(30)
        ->get();

    $pendingInvoicesList = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'i.id', 'i.invoice_id', 'i.due_date', 'i.grant_amt',
            DB::raw("CASE WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) ELSE i.client END AS client_name")
        )
        ->where('i.status', 'pending')
        ->orderBy('i.created_at', 'desc')
        ->limit(30)
        ->get();

    // Fetch task statistics
    $completedTasks = DB::table('tasks')
        ->where('deleted_at', 0)
        ->where('status', 'completed')
        ->count();

    $pendingTasks = DB::table('tasks')
        ->where('deleted_at', 0)
        ->where('status', 'pending')
        ->count();

    // Fetch the latest invoices
    $latestInvoices = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'i.invoice_id',
            'i.due_date',
            'i.grant_amt',
            'i.status',
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->orderBy('i.created_at', 'desc')
        ->limit(3)
        ->get();

    // Fetch the latest clients
    $latestClients = DB::table('clients')
        ->where('deleted_at', 0)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Fetch the latest projects with task counts
    $latestProjects = DB::table('projects as p')
        ->leftJoin('tasks as t', 'p.projectid', '=', 't.projects')
        ->select(
            'p.projectid',
            'p.projectname',
            'p.created_at',
            DB::raw('COUNT(t.id) as total_tasks'),
            DB::raw('SUM(CASE WHEN t.status = "completed" THEN 1 ELSE 0 END) as completed_tasks'),
            DB::raw('SUM(CASE WHEN t.status = "pending" THEN 1 ELSE 0 END) as pending_tasks')
        )
        ->where('p.deleted_at', 0)
        ->groupBy('p.projectid', 'p.projectname', 'p.created_at')
        ->orderBy('p.created_at', 'desc')
        ->limit(5)
        ->get();
        
    // Fetch Recent Goal Tracks with Progress
    $recentGoalTracks = DB::table('goal_tracks as gt')
        ->leftJoin('goal_types as gtype', 'gt.goal', '=', 'gtype.id')
        ->select(
            'gt.id',
            'gt.subject',
            'gt.progress',
            'gt.start_date',
            'gt.end_date',
            'gt.status',
            'gtype.goal as goal_type'
        )
        ->orderBy('gt.created_at', 'desc')
        ->limit(5)
        ->get();

    // NEW LOGIC: Filterable Attendance Status (Early Birds)
    $attendanceFilter = request('attendance_filter', 'today');
    if ($attendanceFilter === 'yesterday') {
        $startDate = now()->subDay()->format('Y-m-d');
        $endDate = now()->subDay()->format('Y-m-d');
    } elseif ($attendanceFilter === 'past7days') {
        $startDate = now()->subDays(6)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
    } elseif ($attendanceFilter === 'last1month') {
        $startDate = now()->subMonth()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
    } else {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
    }
    
    $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

    $checkinPunchins = DB::table('attendances')
        ->select('employee_id', DB::raw('MIN(punch_in) as first_punch_in'), 'date')
        ->whereBetween('date', [$startDate, $endDate])
        ->whereNotNull('punch_in')
        ->groupBy('employee_id', 'date');

    $allActiveEmployeesCheckin = DB::table('allemployees as ae')
        ->leftJoin('department as d', 'ae.department', '=', 'd.id')
        ->leftJoinSub($checkinPunchins, 'fp', 'ae.id', '=', 'fp.employee_id')
        ->select(
            'ae.id',
            'ae.firstname',
            'ae.lastname',
            DB::raw('COALESCE(d.department, "Unassigned") as dept_name'),
            'fp.first_punch_in',
            'fp.date'
        )
        ->where('ae.status', 'active')
        ->where('ae.deleted_at', 0)
        ->get();

    $deptAttendanceStats = collect();
    foreach ($allActiveEmployeesCheckin->groupBy('dept_name') as $deptName => $employeesRecords) {
        $uniqueEmployeesCount = $employeesRecords->unique('id')->count();
        $totalPossible = $uniqueEmployeesCount * $daysDiff;
        $punchedInEmployees = $employeesRecords->whereNotNull('first_punch_in');
        $totalPunchins = $punchedInEmployees->count();
        $percentage = $totalPossible > 0 ? round(($totalPunchins / $totalPossible) * 100) : 0;
        
        $earlyBirds = $punchedInEmployees->filter(function($emp) {
            return Carbon::parse($emp->first_punch_in)->format('H:i:s') < '09:30:00';
        });
        
        $earlyBirdSummaries = $earlyBirds->groupBy('id')->map(function($records) {
            $firstRecord = $records->first();
            return (object)[
                'firstname' => $firstRecord->firstname,
                'lastname' => $firstRecord->lastname,
                'count' => $records->count(),
                'latest_time' => Carbon::parse($records->sortByDesc('first_punch_in')->first()->first_punch_in)->format('h:i A')
            ];
        })->sortByDesc('count');

        if ($uniqueEmployeesCount > 0) {
            $deptAttendanceStats->push((object)[
                'dept_name' => $deptName,
                'total_employees' => $uniqueEmployeesCount,
                'total_possible' => $totalPossible,
                'total_punchins' => $totalPunchins,
                'percentage' => $percentage,
                'early_bird_list' => $earlyBirdSummaries
            ]);
        }
    }
    $deptAttendanceStats = $deptAttendanceStats->sortByDesc('percentage');

    $announcementItems = $this->buildAnnouncementItems();

    return view('hrms.admin.dashboard.index', compact(
        'totalProjects',
        'totalClients',
        'totalTasksCount',
        'totalEmployees',
        'activeEmployees',
        'newJoinersThisMonth',
        'employeesOnNoticePeriod',
        'employeesExitingThisMonth',
        'departmentEmployeeCounts',
        'newEmployees',
        
        // Promotion Management Data - NEW ADDITION
        'totalPromotions',
        'promotionsThisMonth',
        'promotionsThisYear',
        'recentPromotionsCount',
        'currentMonthPromotions',
        'previousMonthPromotions',
        'promotionsPercentageChange',
        'promotionTrendData',
        'promotionTrendLabels',
        'promotionsByDepartment',
        'recentPromotions',
        'promotionRate',
        'avgTimeBetweenPromotions',
        'mostPromotedDepartments',
        
        // Goal Management Data
        'totalGoalTypes',
        'totalGoalTracks',
        'activeGoals',
        'completedGoals',
        'inProgressGoals',
        'notStartedGoals',
        'averageGoalProgress',
        'goalsDueThisMonth',
        'overdueGoals',
        'currentMonthGoals',
        'previousMonthGoals',
        'goalsPercentageChange',
        'currentMonthCompleted',
        'previousMonthCompleted',
        'completionPercentageChange',
        
        // Chart Data
        'goalTrendData',
        'goalTrendLabels',
        'goalProgressData',
        'goalTypeDistribution',
        'completionRateData',
        'completionRateLabels',
        
        // Holiday Management Data
        'totalHolidays',
        'upcomingHolidays',
        'holidaysThisMonth',
        'nextHoliday',
        'recentHolidays',
        'holidayMonthlyData',
        'holidayMonthlyLabels',
        'daysUntilNextHoliday',
        
        // Performance Indicator Data
        'totalPerformanceIndicators',
        'activePerformanceIndicators',
        'inactivePerformanceIndicators',
        'performanceIndicatorsByDesignation',
        'recentPerformanceIndicators',
        'performanceIndicatorStatusData',
        'performanceIndicatorTrendData',
        'performanceIndicatorTrendLabels',
        
        // Performance Review Data
        'totalPerformanceReviews',
        'performanceReviewsThisMonth',
        'performanceReviewsThisYear',
        'pendingPerformanceReviews',
        'recentPerformanceReviews',
        'performanceReviewStatusData',
        'performanceReviewTrendData',
        'performanceReviewTrendLabels',
        'performanceReviewsByDepartment',
        
        // Attendance Management Data - FIXED TO USE ACTUAL SCHEDULE TIMES
        'presentEmployees',
        'absentEmployees',
        'lateEmployees',
        'presentToday',
        'absentToday',
        'lateArrivalsToday',
        'earlyDeparturesToday',
        'attendanceRate',
        'punctualityRate',
        'overtimeHoursToday',
        'weeklyAttendanceData',
        'weeklyAttendanceLabels',
        'monthlyAttendanceData',
        'monthlyAttendanceLabels',
        'departmentAttendanceLabels',
        'departmentAttendanceData',
        'todayAttendance',
        'recentAttendance',
        
        // // Tickets Management Data
        // 'totalTickets',
        // 'newTickets',
        // 'openTickets',
        // 'inProgressTickets',
        // 'closedTickets',
        // 'onHoldTickets',
        // 'cancelledTickets',
        // 'reopenedTickets',
        // 'highPriorityTickets',
        // 'mediumPriorityTickets',
        // 'lowPriorityTickets',
        // 'ticketsThisMonth',
        // 'ticketsThisWeek',
        // 'ticketsToday',
        // 'avgResolutionTime',
        // 'ticketStatusData',
        // 'ticketPriorityData',
        // 'ticketTrendData',
        // 'ticketTrendLabels',
        // 'recentTickets',
        // 'urgentTickets',
        // 'ticketResolutionRate',
        // 'currentMonthTickets',
        // 'previousMonthTickets',
        // 'ticketsPercentageChange',
        // 'role',
        
        // Pending approvals detail lists
        'pendingLeavesList',
        'pendingPermissionsList',
        'pendingSalariesList',
        'pendingInvoicesList',

        // Other existing data
        'todayLeave',
        'pendingLeaveRequests',
        'approvedLeaves',
        'rejectedLeaves',
        'leaveTrendLabels',
        'leaveTrendData',
        'openPositions',
        'candidatesApplied',
        'interviewsScheduled',
        'interviewsToday',
        'offersReleased',
        'candidatesJoined',
        'offerAcceptanceRate',
        'payrollStatus',
        'totalSalaryProcessed',
        'upcomingPayrollDate',
        'statutoryDeductions',
        'pendingSalaryApprovals',
        'payrollCostByDepartment',
        'assetsAssigned',
        'assetsDueForReturn',
        'lostDamagedAssets',
        'attritionRate',
        'employeeTurnoverRate',
        'absenteeismRate',
        'headcountGrowth',
        'costPerHire',
        'goalCompletionRate',
        'kpiAchievement',
        'birthdaysThisMonth',
        'workAnniversariesThisMonth',
        'policyUpdates',
        'pendingApprovals',
        'pendingInvoices',
        'completedTasks',
        'pendingTasks',
        'latestInvoices',
        'latestClients',
        'latestProjects',
        'recentGoalTracks',
        'attendanceFilter',
        'deptAttendanceStats',
        'daysDiff',
        'announcementItems'
    ));
}

public function processApproval(Request $request)
{
    $type   = $request->input('type');
    $id     = (int) $request->input('id');
    $action = $request->input('action');

    try {
        switch ($type) {

            case 'leave':
                $leave = DB::table('employee_leaves')->where('id', $id)->first();
                if (!$leave) {
                    return response()->json(['success' => false, 'message' => 'Leave not found'], 404);
                }
                if (!in_array($action, ['approved', 'declined', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                // Reverse prior deduction if changing away from approved
                if ($leave->status === 'approved' && $action !== 'approved') {
                    DB::table('employee_leave_balances')
                        ->where('employee_id', $leave->employee_id)
                        ->where('leave_type', $leave->leave_type)
                        ->update([
                            'used_days'      => DB::raw('GREATEST(0, used_days - ' . (float)$leave->no_of_days . ')'),
                            'remaining_days' => DB::raw('remaining_days + ' . (float)$leave->no_of_days),
                            'updated_at'     => now(),
                        ]);
                }
                // Deduct when newly approving
                if ($action === 'approved' && $leave->status !== 'approved') {
                    DB::table('employee_leave_balances')
                        ->where('employee_id', $leave->employee_id)
                        ->where('leave_type', $leave->leave_type)
                        ->update([
                            'used_days'      => DB::raw('used_days + ' . (float)$leave->no_of_days),
                            'remaining_days' => DB::raw('GREATEST(0, remaining_days - ' . (float)$leave->no_of_days . ')'),
                            'updated_at'     => now(),
                        ]);
                }
                DB::table('employee_leaves')->where('id', $id)->update(['status' => $action]);
                break;

            case 'permission':
                $perm = DB::table('employee_permissions')->where('id', $id)->first();
                if (!$perm) {
                    return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
                }
                if (!in_array($action, ['approved', 'declined', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                DB::table('employee_permissions')->where('id', $id)->update(['status' => $action]);
                break;

            case 'salary':
                $salary = DB::table('employee_salaries')->where('id', $id)->first();
                if (!$salary) {
                    return response()->json(['success' => false, 'message' => 'Salary record not found'], 404);
                }
                if (!in_array($action, ['approved', 'hold', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                $salaryUpdate = ['approval_status' => $action];
                if ($action !== 'approved' && Schema::hasColumn('employee_salaries', 'release_status')) {
                    $salaryUpdate['release_status'] = 'hold';
                }
                DB::table('employee_salaries')->where('id', $id)->update($salaryUpdate);
                break;

            case 'invoice':
                $invoiceStrId = $request->input('invoice_str_id');
                if (!$invoiceStrId) {
                    return response()->json(['success' => false, 'message' => 'Invoice ID missing'], 422);
                }
                if (!in_array($action, ['approved', 'rejected', 'sent', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                $rows = DB::table('invoices')->where('invoice_id', $invoiceStrId)->update(['status' => $action]);
                if (!$rows) {
                    return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
                }
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Unknown approval type'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Updated successfully']);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}
