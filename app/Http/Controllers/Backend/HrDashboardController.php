<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Traits\BuildsAnnouncementItems;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

class HrDashboardController extends Controller
{
    use BuildsAnnouncementItems;
    public function index()
    {
        $totalProjects = DB::table('projects')->where('deleted_at', 0)->count();
        $totalClients = DB::table('clients')->where('deleted_at', 0)->count();
        $totalTasksCount = DB::table('tasks')->where('deleted_at', 0)->count();
        $totalEmployees = DB::table('allemployees')->where('deleted_at', 0)->count();
        $activeEmployees = DB::table('allemployees')->where('deleted_at', 0)->where('status', 'active')->count();
        $newJoinersThisMonth = DB::table('allemployees')->where('deleted_at', 0)->whereYear('joiningdate', now()->year)->whereMonth('joiningdate', now()->month)->count();
        $employeesOnNoticePeriod = DB::table('allemployees')->where('deleted_at', 0)->whereIn('status', ['notice', 'notice period', 'on_notice', 'resigned'])->count();
        $employeesExitingThisMonth = DB::table('allemployees')->where('deleted_at', 0)->whereIn('status', ['inactive', 'terminated', 'exit', 'exited'])->whereYear('updated_at', now()->year)->whereMonth('updated_at', now()->month)->count();
        $departmentEmployeeCounts = DB::table('allemployees as ae')->leftJoin('department as d', 'ae.department', '=', 'd.id')->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('COUNT(*) as count'))->where('ae.deleted_at', 0)->groupBy('d.department')->orderBy('count', 'desc')->limit(8)->get();
        $newEmployees = DB::table('allemployees')->where('deleted_at', 0)->where('created_at', '>=', now()->subDays(7))->count();

        // Promotions
        $totalPromotions = DB::table('promotions')->count();
        $promotionsThisMonth = DB::table('promotions')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
        $promotionsThisYear = DB::table('promotions')->whereYear('created_at', now()->year)->count();
        $recentPromotionsCount = DB::table('promotions')->where('created_at', '>=', now()->subDays(30))->count();
        $currentMonthPromotions = $promotionsThisMonth;
        $previousMonthPromotions = DB::table('promotions')->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->count();
        $promotionsPercentageChange = $previousMonthPromotions > 0 ? (($currentMonthPromotions - $previousMonthPromotions) / $previousMonthPromotions) * 100 : 0;
        $promotionTrendData = [];
        $promotionTrendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $promotionTrendData[] = DB::table('promotions')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $promotionTrendLabels[] = $date->format('M Y');
        }
        $promotionsByDepartment = DB::table('promotions as p')->join('department as d', 'p.department_id', '=', 'd.id')->select('d.department', DB::raw('COUNT(*) as count'))->groupBy('d.department', 'd.id')->orderBy('count', 'desc')->limit(6)->get();
        $recentPromotions = DB::table('promotions as p')->join('allemployees as ae', 'p.employee_id', '=', 'ae.employeeid')->join('department as d', 'p.department_id', '=', 'd.id')->join('designation as from_des', 'p.promotion_from', '=', 'from_des.id')->join('designation as to_des', 'p.promotion_to', '=', 'to_des.id')->select('p.*', 'ae.firstname', 'ae.lastname', 'ae.employeeid', 'd.department', 'from_des.designation as from_designation', 'to_des.designation as to_designation')->where('ae.deleted_at', 0)->orderBy('p.created_at', 'desc')->limit(5)->get();
        $promotionRate = $totalEmployees > 0 ? round(($totalPromotions / $totalEmployees) * 100, 1) : 0;
        $avgTimeBetweenPromotions = DB::table('promotions')->selectRaw('AVG(TIMESTAMPDIFF(MONTH, created_at, promotion_date)) as avg_months')->value('avg_months') ?? 0;
        $mostPromotedDepartments = DB::table('promotions as p')->join('department as d', 'p.department_id', '=', 'd.id')->select('d.department', DB::raw('COUNT(*) as promotion_count'))->groupBy('d.department', 'd.id')->orderBy('promotion_count', 'desc')->limit(3)->get();

        // Goals
        $totalGoalTypes = DB::table('goal_types')->where('deleted_at', 0)->count();
        $totalGoalTracks = DB::table('goal_tracks')->count();
        $activeGoals = DB::table('goal_tracks')->where('status', 'active')->count();
        $completedGoals = DB::table('goal_tracks')->where('progress', 100)->count();
        $inProgressGoals = DB::table('goal_tracks')->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $notStartedGoals = DB::table('goal_tracks')->where('progress', 0)->count();
        $averageGoalProgress = DB::table('goal_tracks')->avg('progress') ?? 0;
        $goalsDueThisMonth = DB::table('goal_tracks')->whereYear('end_date', now()->year)->whereMonth('end_date', now()->month)->count();
        $overdueGoals = DB::table('goal_tracks')->where('end_date', '<', now())->where('progress', '<', 100)->count();
        $goalTrendData = [];
        $goalTrendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $goalTrendData[] = DB::table('goal_tracks')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $goalTrendLabels[] = $date->format('M Y');
        }
        $goalProgressData = ['completed' => $completedGoals, 'in_progress' => $inProgressGoals, 'not_started' => $notStartedGoals, 'overdue' => $overdueGoals];
        $goalTypeDistribution = DB::table('goal_tracks as gt')->join('goal_types as gtype', 'gt.goal', '=', 'gtype.id')->select('gtype.goal', DB::raw('COUNT(*) as count'))->groupBy('gtype.goal', 'gtype.id')->get();
        $completionRateData = [];
        $completionRateLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = DB::table('goal_tracks')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $done = DB::table('goal_tracks')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->where('progress', 100)->count();
            $completionRateData[] = $total > 0 ? round(($done / $total) * 100, 1) : 0;
            $completionRateLabels[] = $date->format('M Y');
        }
        $currentMonthGoals = DB::table('goal_tracks')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
        $previousMonthGoals = DB::table('goal_tracks')->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->count();
        $goalsPercentageChange = $previousMonthGoals > 0 ? (($currentMonthGoals - $previousMonthGoals) / $previousMonthGoals) * 100 : 0;
        $currentMonthCompleted = DB::table('goal_tracks')->whereYear('end_date', now()->year)->whereMonth('end_date', now()->month)->where('progress', 100)->count();
        $previousMonthCompleted = DB::table('goal_tracks')->whereYear('end_date', now()->subMonth()->year)->whereMonth('end_date', now()->subMonth()->month)->where('progress', 100)->count();
        $completionPercentageChange = $previousMonthCompleted > 0 ? (($currentMonthCompleted - $previousMonthCompleted) / $previousMonthCompleted) * 100 : 0;

        // Holidays
        $totalHolidays = DB::table('holidays')->count();
        $upcomingHolidays = DB::table('holidays')->where('holidaydate', '>=', now()->format('Y-m-d'))->where('holidaydate', '<=', now()->addDays(30)->format('Y-m-d'))->orderBy('holidaydate', 'asc')->get();
        $holidaysThisMonth = DB::table('holidays')->whereYear('holidaydate', now()->year)->whereMonth('holidaydate', now()->month)->count();
        $nextHoliday = DB::table('holidays')->where('holidaydate', '>=', now()->format('Y-m-d'))->orderBy('holidaydate', 'asc')->first();
        $recentHolidays = DB::table('holidays')->where('holidaydate', '>=', now()->subDays(30)->format('Y-m-d'))->where('holidaydate', '<', now()->format('Y-m-d'))->orderBy('holidaydate', 'desc')->limit(5)->get();
        $holidayMonthlyData = [];
        $holidayMonthlyLabels = [];
        for ($i = 1; $i <= 12; $i++) {
            $holidayMonthlyData[] = DB::table('holidays')->whereYear('holidaydate', now()->year)->whereMonth('holidaydate', $i)->count();
            $holidayMonthlyLabels[] = Carbon::create()->month($i)->format('M');
        }
        $daysUntilNextHoliday = $nextHoliday ? Carbon::parse($nextHoliday->holidaydate)->diffInDays(now()) : null;

        // Performance Indicators
        $totalPerformanceIndicators = DB::table('performance_indicator')->where('deleted_at', 0)->count();
        $activePerformanceIndicators = DB::table('performance_indicator')->where('deleted_at', 0)->where('status', 'active')->count();
        $inactivePerformanceIndicators = DB::table('performance_indicator')->where('deleted_at', 0)->where('status', 'inactive')->count();
        $performanceIndicatorsByDesignation = DB::table('performance_indicator as pi')->leftJoin('designation as d', 'pi.designation_id', '=', 'd.id')->select('d.designation', DB::raw('COUNT(*) as count'))->where('pi.deleted_at', 0)->where('d.deleted_at', 0)->groupBy('d.designation', 'd.id')->limit(6)->get();
        $recentPerformanceIndicators = DB::table('performance_indicator as pi')->leftJoin('designation as d', 'pi.designation_id', '=', 'd.id')->select('pi.*', 'd.designation')->where('pi.deleted_at', 0)->where('d.deleted_at', 0)->orderBy('pi.created_at', 'desc')->limit(5)->get();
        $performanceIndicatorStatusData = ['active' => $activePerformanceIndicators, 'inactive' => $inactivePerformanceIndicators];
        $performanceIndicatorTrendData = [];
        $performanceIndicatorTrendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $performanceIndicatorTrendData[] = DB::table('performance_indicator')->where('deleted_at', 0)->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $performanceIndicatorTrendLabels[] = $date->format('M Y');
        }

        // Performance Reviews
        $totalPerformanceReviews = DB::table('performance_review_basic_infos')->where('deleted_at', 0)->count();
        $performanceReviewsThisMonth = DB::table('performance_review_basic_infos')->where('deleted_at', 0)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
        $performanceReviewsThisYear = DB::table('performance_review_basic_infos')->where('deleted_at', 0)->whereYear('created_at', now()->year)->count();
        $pendingPerformanceReviews = DB::table('performance_review_basic_infos as prbi')->leftJoin('performance_review_signatures as prs', 'prbi.id', '=', 'prs.review_id')->where('prbi.deleted_at', 0)->whereNull('prs.hrd_signature')->count();
        $recentPerformanceReviews = DB::table('performance_review_basic_infos as prbi')->leftJoin('allemployees as ae', 'prbi.employee_name', '=', 'ae.id')->leftJoin('designation as d', 'prbi.designation_id', '=', 'd.id')->leftJoin('department as dept', 'prbi.department_id', '=', 'dept.id')->select('prbi.*', 'ae.firstname', 'ae.lastname', 'd.designation', 'dept.department')->where('prbi.deleted_at', 0)->where('ae.deleted_at', 0)->orderBy('prbi.created_at', 'desc')->limit(5)->get();
        $performanceReviewStatusData = ['completed' => $totalPerformanceReviews - $pendingPerformanceReviews, 'pending' => $pendingPerformanceReviews];
        $performanceReviewTrendData = [];
        $performanceReviewTrendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $performanceReviewTrendData[] = DB::table('performance_review_basic_infos')->where('deleted_at', 0)->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $performanceReviewTrendLabels[] = $date->format('M Y');
        }
        $performanceReviewsByDepartment = DB::table('performance_review_basic_infos as prbi')->leftJoin('department as dept', 'prbi.department_id', '=', 'dept.id')->select('dept.department', DB::raw('COUNT(*) as count'))->where('prbi.deleted_at', 0)->groupBy('dept.department', 'dept.id')->limit(6)->get();

        // Attendance
        $today = now()->format('Y-m-d');
        $firstPunchins = DB::table('attendances')->select('employee_id', DB::raw('MIN(punch_in) as first_punch_in'), DB::raw('MAX(punch_out) as last_punch_out'))->whereDate('date', $today)->whereNotNull('punch_in')->groupBy('employee_id');
        $todayAttendance = DB::table('allemployees as ae')->leftJoinSub($firstPunchins, 'fp', 'ae.id', '=', 'fp.employee_id')->leftJoin('schedule as s', function($join) use ($today) { $join->on('ae.id', '=', 's.employee_id')->where('s.schedule_start_date', '<=', $today)->where('s.schedule_end_date', '>=', $today)->where('s.publish', 1)->where('s.deleted_at', 0)->where('s.is_current', 1); })->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')->leftJoin('department as d', 'ae.department', '=', 'd.id')->leftJoin('designation as des', 'ae.designation', '=', 'des.id')->select('ae.firstname', 'ae.lastname', 'd.department', 'des.designation', 'fp.first_punch_in as punch_in', 'fp.last_punch_out as punch_out', 'sh.start_time as scheduled_start', 'sh.end_time as scheduled_end')->where('ae.deleted_at', 0)->where('ae.status', 'active')->orderByRaw('fp.first_punch_in IS NULL ASC')->orderBy('fp.first_punch_in', 'desc')->limit(20)->get();
        $todayAttendance->each(function($attendance) {
            if (!$attendance->punch_in) { $attendance->status = 'Absent'; $attendance->is_late = false; $attendance->late_minutes = 0; return; }
            $checkInTime = Carbon::parse($attendance->punch_in);
            $scheduledStart = $attendance->scheduled_start ? Carbon::parse($attendance->scheduled_start) : Carbon::parse($attendance->punch_in)->startOfDay()->setTime(9, 30);
            $gracePeriodEnd = $scheduledStart->copy()->addMinutes(5);
            if ($checkInTime->lte($gracePeriodEnd)) { $attendance->status = 'On Time'; $attendance->is_late = false; $attendance->late_minutes = 0; }
            else { $attendance->status = 'Late'; $attendance->is_late = true; $attendance->late_minutes = $checkInTime->diffInMinutes($scheduledStart); }
        });
        $presentToday = $todayAttendance->whereNotNull('punch_in')->count();
        $absentToday = $todayAttendance->whereNull('punch_in')->count();
        $lateArrivalsToday = $todayAttendance->where('is_late', true)->whereNotNull('punch_in')->count();
        $earlyDeparturesToday = DB::table('attendances as a')->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')->leftJoin('schedule as s', function($join) use ($today) { $join->on('ae.id', '=', 's.employee_id')->where('s.schedule_start_date', '<=', $today)->where('s.schedule_end_date', '>=', $today)->where('s.publish', 1)->where('s.deleted_at', 0)->where('s.is_current', 1); })->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')->whereDate('a.date', $today)->whereNotNull('a.punch_in')->whereNotNull('a.punch_out')->whereNotNull('sh.end_time')->whereRaw('TIME(a.punch_out) < TIME(sh.end_time)')->count();
        $attendanceRate = $totalEmployees > 0 ? round((DB::table('attendances')->whereDate('date', '>=', now()->subDays(30))->whereNotNull('punch_in')->count() / ($totalEmployees * 30)) * 100, 1) : 0;
        $punctualityRate = $presentToday > 0 ? round(($todayAttendance->where('status', 'On Time')->count() / $presentToday) * 100, 1) : 0;
        $overtimeHoursToday = DB::table('attendances')->whereDate('date', $today)->whereNotNull('punch_in')->whereNotNull('punch_out')->whereRaw('TIMESTAMPDIFF(HOUR, punch_in, punch_out) > 8')->sum(DB::raw('GREATEST(0, TIMESTAMPDIFF(HOUR, punch_in, punch_out) - 8)'));
        $weeklyAttendanceData = [];
        $weeklyAttendanceLabels = [];
        for ($i = 6; $i >= 0; $i--) { $date = now()->subDays($i); $weeklyAttendanceData[] = DB::table('attendances')->whereDate('date', $date->format('Y-m-d'))->whereNotNull('punch_in')->distinct('employee_id')->count(); $weeklyAttendanceLabels[] = $date->format('M d'); }
        $monthlyAttendanceData = [];
        $monthlyAttendanceLabels = [];
        for ($i = 5; $i >= 0; $i--) { $date = now()->subMonths($i); $total = $totalEmployees * $date->daysInMonth; $actual = DB::table('attendances')->whereYear('date', $date->year)->whereMonth('date', $date->month)->whereNotNull('punch_in')->count(); $monthlyAttendanceData[] = $total > 0 ? round(($actual / $total) * 100, 1) : 0; $monthlyAttendanceLabels[] = $date->format('M Y'); }
        $departmentAttendance = DB::table('allemployees as ae')->leftJoin('department as d', 'ae.department', '=', 'd.id')->leftJoin('attendances as a', function ($join) use ($today) { $join->on('ae.id', '=', 'a.employee_id')->whereDate('a.date', $today)->whereNotNull('a.punch_in'); })->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('COUNT(DISTINCT a.employee_id) as present_count'))->where('ae.deleted_at', 0)->groupBy('d.department')->orderBy('present_count', 'desc')->limit(6)->get();
        $departmentAttendanceLabels = $departmentAttendance->pluck('department')->toArray();
        $departmentAttendanceData = $departmentAttendance->pluck('present_count')->toArray();
        $recentAttendance = DB::table('attendances as a')->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')->leftJoin('department as d', 'ae.department', '=', 'd.id')->leftJoin('designation as des', 'ae.designation', '=', 'des.id')->select('ae.firstname', 'ae.lastname', 'd.department', 'des.designation', 'a.punch_in', 'a.punch_out', 'a.date')->where('ae.deleted_at', 0)->orderBy('a.date', 'desc')->orderBy('a.punch_in', 'desc')->limit(3)->get();

        // Leave
        $todayLeave = DB::table('employee_leaves')->where('status', 'approved')->whereDate('from_date', '<=', now()->format('Y-m-d'))->whereDate('to_date', '>=', now()->format('Y-m-d'))->count();
        $pendingInvoices = DB::table('invoices')->where('status', 'pending')->count();
        $pendingLeaveRequests = DB::table('employee_leaves')->where('status', 'pending')->count();
        $approvedLeaves = DB::table('employee_leaves')->where('status', 'approved')->count();
        $rejectedLeaves = DB::table('employee_leaves')->whereIn('status', ['rejected', 'declined'])->count();
        $leaveTrendLabels = [];
        $leaveTrendData = [];
        for ($i = 5; $i >= 0; $i--) { $date = now()->subMonths($i); $leaveTrendLabels[] = $date->format('M Y'); $leaveTrendData[] = DB::table('employee_leaves')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(); }

        // Recruitment
        $openPositions = DB::table('managejobs')->where('status', 'open')->sum('vacancies');
        $candidatesApplied = DB::table('candidate')->where('deleted_at', 0)->count();
        $interviewsScheduled = DB::table('interviews')->where('interview_datetime', '>=', now())->count();
        $interviewsToday = DB::table('interviews')->whereDate('interview_datetime', now()->format('Y-m-d'))->count();
        $offersReleased = DB::table('offer_letters')->where('is_active', 1)->count();
        $candidatesJoined = DB::table('allemployees')->where('deleted_at', 0)->whereIn('source_type', ['candidate', 'recruitment'])->count();
        $offerAcceptanceRate = $offersReleased > 0 ? round(($candidatesJoined / $offersReleased) * 100, 1) : 0;

        // Payroll
        $payrollStatus = DB::table('employee_salaries')->where('approval_status', 'pending')->exists() ? 'Pending Approval' : 'Ready';
        $totalSalaryProcessed = DB::table('employee_salaries')->where('approval_status', 'approved')->sum('net_salary');
        $upcomingPayrollDate = now()->endOfMonth()->format('d M Y');
        $statutoryDeductions = DB::table('employee_salaries')->sum(DB::raw('COALESCE(tds,0) + COALESCE(esi,0) + COALESCE(pf,0) + COALESCE(tax,0) + COALESCE(welfare,0)'));
        $pendingSalaryApprovals = DB::table('employee_salaries')->where('approval_status', 'pending')->count();
        $payrollCostByDepartment = DB::table('employee_salaries as es')->join('allemployees as ae', 'es.employee_id', '=', 'ae.id')->leftJoin('department as d', 'ae.department', '=', 'd.id')->select(DB::raw('COALESCE(d.department, "Unassigned") as department'), DB::raw('SUM(es.net_salary) as amount'))->where('ae.deleted_at', 0)->groupBy('d.department')->orderBy('amount', 'desc')->limit(8)->get();

        // Assets
        $assetsAssigned = DB::table('assets_assignment')->where('deleted_at', 0)->where('status', 'assigned')->count();
        $assetsDueForReturn = DB::table('assets_assignment')->where('deleted_at', 0)->where('status', 'assigned')->whereNotNull('return_date')->whereDate('return_date', '<=', now()->addDays(7)->format('Y-m-d'))->count();
        $lostDamagedAssets = DB::table('assets_company')->where('deleted_at', 0)->whereIn('condition', ['lost', 'damaged'])->count();

        // HR Analytics
        $attritionRate = $totalEmployees > 0 ? round(($employeesExitingThisMonth / $totalEmployees) * 100, 1) : 0;
        $employeeTurnoverRate = $attritionRate;
        $absenteeismRate = $totalEmployees > 0 ? round(($absentToday / $totalEmployees) * 100, 1) : 0;
        $headcountGrowth = $totalEmployees > 0 ? round(($newJoinersThisMonth / $totalEmployees) * 100, 1) : 0;
        $costPerHire = $candidatesJoined > 0 ? round($totalSalaryProcessed / $candidatesJoined, 2) : 0;
        $goalCompletionRate = $totalGoalTracks > 0 ? round(($completedGoals / $totalGoalTracks) * 100, 1) : 0;
        $kpiAchievement = round($averageGoalProgress, 1);
        $birthdaysThisMonth = DB::table('allemployees')->where('deleted_at', 0)->when(Schema::hasColumn('allemployees', 'date_of_birth'), fn($q) => $q->whereMonth('date_of_birth', now()->month), fn($q) => $q->whereRaw('1 = 0'))->count();
        $workAnniversariesThisMonth = DB::table('allemployees')->where('deleted_at', 0)->whereMonth('joiningdate', now()->month)->count();
        $policyUpdates = Schema::hasTable('policies') ? DB::table('policies')->count() : 0;
        $pendingApprovals = $pendingLeaveRequests + $pendingSalaryApprovals + $pendingInvoices + $pendingPerformanceReviews;

        // Tasks & Projects
        $completedTasks = DB::table('tasks')->where('deleted_at', 0)->where('status', 'completed')->count();
        $pendingTasks = DB::table('tasks')->where('deleted_at', 0)->where('status', 'pending')->count();
        $latestInvoices = DB::table('invoices as i')->leftJoin('clients as c', 'i.client', '=', 'c.client_id')->select('i.invoice_id', 'i.due_date', 'i.grant_amt', 'i.status', DB::raw("CASE WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) ELSE i.client END AS client_name"))->orderBy('i.created_at', 'desc')->limit(3)->get();
        $latestClients = DB::table('clients')->where('deleted_at', 0)->orderBy('created_at', 'desc')->limit(5)->get();
        $latestProjects = DB::table('projects as p')->leftJoin('tasks as t', 'p.projectid', '=', 't.projects')->select('p.projectid', 'p.projectname', 'p.created_at', DB::raw('COUNT(t.id) as total_tasks'), DB::raw('SUM(CASE WHEN t.status = "completed" THEN 1 ELSE 0 END) as completed_tasks'), DB::raw('SUM(CASE WHEN t.status = "pending" THEN 1 ELSE 0 END) as pending_tasks'))->where('p.deleted_at', 0)->groupBy('p.projectid', 'p.projectname', 'p.created_at')->orderBy('p.created_at', 'desc')->limit(5)->get();
        $recentGoalTracks = DB::table('goal_tracks as gt')->leftJoin('goal_types as gtype', 'gt.goal', '=', 'gtype.id')->select('gt.id', 'gt.subject', 'gt.progress', 'gt.start_date', 'gt.end_date', 'gt.status', 'gtype.goal as goal_type')->orderBy('gt.created_at', 'desc')->limit(5)->get();

        // Attendance filter
        $attendanceFilter = request('attendance_filter', 'today');
        if ($attendanceFilter === 'yesterday') { $startDate = now()->subDay()->format('Y-m-d'); $endDate = now()->subDay()->format('Y-m-d'); }
        elseif ($attendanceFilter === 'past7days') { $startDate = now()->subDays(6)->format('Y-m-d'); $endDate = now()->format('Y-m-d'); }
        elseif ($attendanceFilter === 'last1month') { $startDate = now()->subMonth()->format('Y-m-d'); $endDate = now()->format('Y-m-d'); }
        else { $startDate = now()->format('Y-m-d'); $endDate = now()->format('Y-m-d'); }
        $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $checkinPunchins = DB::table('attendances')->select('employee_id', DB::raw('MIN(punch_in) as first_punch_in'), 'date')->whereBetween('date', [$startDate, $endDate])->whereNotNull('punch_in')->groupBy('employee_id', 'date');
        $allActiveEmployeesCheckin = DB::table('allemployees as ae')->leftJoin('department as d', 'ae.department', '=', 'd.id')->leftJoinSub($checkinPunchins, 'fp', 'ae.id', '=', 'fp.employee_id')->select('ae.id', 'ae.firstname', 'ae.lastname', DB::raw('COALESCE(d.department, "Unassigned") as dept_name'), 'fp.first_punch_in', 'fp.date')->where('ae.status', 'active')->where('ae.deleted_at', 0)->get();
        $deptAttendanceStats = collect();
        foreach ($allActiveEmployeesCheckin->groupBy('dept_name') as $deptName => $employeesRecords) {
            $uniqueEmployeesCount = $employeesRecords->unique('id')->count();
            $totalPossible = $uniqueEmployeesCount * $daysDiff;
            $punchedInEmployees = $employeesRecords->whereNotNull('first_punch_in');
            $totalPunchins = $punchedInEmployees->count();
            $percentage = $totalPossible > 0 ? round(($totalPunchins / $totalPossible) * 100) : 0;
            $earlyBirds = $punchedInEmployees->filter(fn($emp) => Carbon::parse($emp->first_punch_in)->format('H:i:s') < '09:30:00');
            $earlyBirdSummaries = $earlyBirds->groupBy('id')->map(function($records) { $first = $records->first(); return (object)['firstname' => $first->firstname, 'lastname' => $first->lastname, 'count' => $records->count(), 'latest_time' => Carbon::parse($records->sortByDesc('first_punch_in')->first()->first_punch_in)->format('h:i A')]; })->sortByDesc('count');
            if ($uniqueEmployeesCount > 0) {
                $deptAttendanceStats->push((object)['dept_name' => $deptName, 'total_employees' => $uniqueEmployeesCount, 'total_possible' => $totalPossible, 'total_punchins' => $totalPunchins, 'percentage' => $percentage, 'early_bird_list' => $earlyBirdSummaries]);
            }
        }
        $deptAttendanceStats = $deptAttendanceStats->sortByDesc('percentage');

        $announcementItems = $this->buildAnnouncementItems();

        return view('hrms.admin.hrdashboard.index', compact(
            'totalProjects', 'totalClients', 'totalTasksCount', 'totalEmployees', 'activeEmployees',
            'newJoinersThisMonth', 'employeesOnNoticePeriod', 'employeesExitingThisMonth',
            'departmentEmployeeCounts', 'newEmployees',
            'totalPromotions', 'promotionsThisMonth', 'promotionsThisYear', 'recentPromotionsCount',
            'currentMonthPromotions', 'previousMonthPromotions', 'promotionsPercentageChange',
            'promotionTrendData', 'promotionTrendLabels', 'promotionsByDepartment', 'recentPromotions',
            'promotionRate', 'avgTimeBetweenPromotions', 'mostPromotedDepartments',
            'totalGoalTypes', 'totalGoalTracks', 'activeGoals', 'completedGoals', 'inProgressGoals',
            'notStartedGoals', 'averageGoalProgress', 'goalsDueThisMonth', 'overdueGoals',
            'currentMonthGoals', 'previousMonthGoals', 'goalsPercentageChange',
            'currentMonthCompleted', 'previousMonthCompleted', 'completionPercentageChange',
            'goalTrendData', 'goalTrendLabels', 'goalProgressData', 'goalTypeDistribution',
            'completionRateData', 'completionRateLabels',
            'totalHolidays', 'upcomingHolidays', 'holidaysThisMonth', 'nextHoliday',
            'recentHolidays', 'holidayMonthlyData', 'holidayMonthlyLabels', 'daysUntilNextHoliday',
            'totalPerformanceIndicators', 'activePerformanceIndicators', 'inactivePerformanceIndicators',
            'performanceIndicatorsByDesignation', 'recentPerformanceIndicators',
            'performanceIndicatorStatusData', 'performanceIndicatorTrendData', 'performanceIndicatorTrendLabels',
            'totalPerformanceReviews', 'performanceReviewsThisMonth', 'performanceReviewsThisYear',
            'pendingPerformanceReviews', 'recentPerformanceReviews', 'performanceReviewStatusData',
            'performanceReviewTrendData', 'performanceReviewTrendLabels', 'performanceReviewsByDepartment',
            'presentToday', 'absentToday', 'lateArrivalsToday', 'earlyDeparturesToday',
            'attendanceRate', 'punctualityRate', 'overtimeHoursToday',
            'weeklyAttendanceData', 'weeklyAttendanceLabels', 'monthlyAttendanceData', 'monthlyAttendanceLabels',
            'departmentAttendanceLabels', 'departmentAttendanceData', 'todayAttendance', 'recentAttendance',
            'todayLeave', 'pendingLeaveRequests', 'approvedLeaves', 'rejectedLeaves',
            'leaveTrendLabels', 'leaveTrendData',
            'openPositions', 'candidatesApplied', 'interviewsScheduled', 'interviewsToday',
            'offersReleased', 'candidatesJoined', 'offerAcceptanceRate',
            'payrollStatus', 'totalSalaryProcessed', 'upcomingPayrollDate',
            'statutoryDeductions', 'pendingSalaryApprovals', 'payrollCostByDepartment',
            'assetsAssigned', 'assetsDueForReturn', 'lostDamagedAssets',
            'attritionRate', 'employeeTurnoverRate', 'absenteeismRate', 'headcountGrowth',
            'costPerHire', 'goalCompletionRate', 'kpiAchievement',
            'birthdaysThisMonth', 'workAnniversariesThisMonth', 'policyUpdates', 'pendingApprovals',
            'pendingInvoices', 'completedTasks', 'pendingTasks',
            'latestInvoices', 'latestClients', 'latestProjects', 'recentGoalTracks',
            'attendanceFilter', 'deptAttendanceStats', 'daysDiff',
            'announcementItems'
        ));
    }
}
