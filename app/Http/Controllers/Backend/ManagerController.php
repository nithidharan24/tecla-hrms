<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function index()
    {
        $managerId = Session::get('user_id'); // Manager logged-in ID
        $today = Carbon::today();

        // Get the department ID for this manager
        $departmentId = DB::table('allemployees')
            ->where('id', $managerId)
            ->value('department');

        // Get department name
        $departmentName = DB::table('department')
            ->where('id', $departmentId)
            ->value('department');

        // Total Employees under this Manager
        $totalEmployees = DB::table('allemployees')
            ->where('manager_id', $managerId)
            ->where('deleted_at', 0)
            ->count();

        // Today's Attendance Stats
        $presentToday = DB::table('attendances as a')
            ->join('allemployees as e', 'a.employee_id', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->whereDate('a.date', $today)
            ->whereNotNull('a.punch_in')
            ->distinct('a.employee_id')
            ->count();

        $absentToday = $totalEmployees - $presentToday;

        $lateArrivals = DB::table('attendances as a')
            ->join('allemployees as e', 'a.employee_id', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->whereDate('a.date', $today)
            ->whereTime('a.punch_in', '>', '09:15:00')
            ->count();

        // Leave Summary
        $pendingLeaves = DB::table('employee_leaves as l')
            ->join('allemployees as e', 'l.employee_id', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->where('l.status', 'pending')
            ->count();

        $approvedLeaves = DB::table('employee_leaves as l')
            ->join('allemployees as e', 'l.employee_id', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->where('l.status', 'approved')
            ->count();

        // Weekly Attendance Trend
        $attendanceTrendData = [];
        $attendanceTrendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = DB::table('attendances as a')
                ->join('allemployees as e', 'a.employee_id', '=', 'e.id')
                ->where('e.manager_id', $managerId)
                ->whereDate('a.date', $date)
                ->whereNotNull('a.punch_in')
                ->distinct('a.employee_id')
                ->count();
            $attendanceTrendData[] = $count;
            $attendanceTrendLabels[] = $date->format('M d');
        }

        // Recent Attendance Records
        $recentAttendance = DB::table('attendances as a')
            ->join('allemployees as e', 'a.employee_id', '=', 'e.id')
            ->leftJoin('designation as des', 'e.designation', '=', 'des.id')
            ->select('e.firstname', 'e.lastname', 'des.designation', 'a.punch_in', 'a.punch_out', 'a.date')
            ->where('e.manager_id', $managerId)
            ->orderBy('a.date', 'desc')
            ->limit(5)
            ->get();

        // Holiday Data
        $upcomingHolidays = DB::table('holidays')
            ->where('holidaydate', '>=', $today)
            ->orderBy('holidaydate', 'asc')
            ->limit(5)
            ->get();

        // Task Summary for Manager's Department
        $completedTasks = DB::table('tasks as t')
            ->join('allemployees as e', 't.assigned_to', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->where('t.status', 'completed')
            ->count();

        $pendingTasks = DB::table('tasks as t')
            ->join('allemployees as e', 't.assigned_to', '=', 'e.id')
            ->where('e.manager_id', $managerId)
            ->where('t.status', 'pending')
            ->count();
// ===============================
// PROJECT GRAPH DATA
// ===============================
$projectGraph = DB::table('projects')
    ->select('projectname', 'totalhours')
    ->where('deleted_at', 0)
    ->orderBy('id', 'desc')
    ->limit(6)
    ->get();

$projectNames = $projectGraph->pluck('projectname');
$projectHours = $projectGraph->pluck('totalhours');

// ===============================
// TASK STATUS GRAPH DATA
// ===============================
$taskStats = DB::table('tasks as t')
    ->join('allemployees as e', 't.assigned_to', '=', 'e.id')
    ->where('e.manager_id', $managerId)
    ->selectRaw("SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed,
                 SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending,
                 SUM(CASE WHEN t.status NOT IN ('completed','pending') THEN 1 ELSE 0 END) as other")
    ->first();

$taskLabels = ['Completed', 'Pending', 'Other'];
$taskData = [$taskStats->completed, $taskStats->pending, $taskStats->other];

// ===============================
// TIMESHEET GRAPH DATA
// ===============================
$timesheetGraph = DB::table('timesheet as ts')
    ->join('allemployees as e', 'ts.employee_id', '=', 'e.id')
    ->select('e.firstname', DB::raw('SUM(ts.total_hours) as total_hours'))
    ->where('e.manager_id', $managerId)
    ->groupBy('e.firstname')
    ->orderByDesc('total_hours')
    ->limit(6)
    ->get();

$employeeNames = $timesheetGraph->pluck('firstname');
$employeeHours = $timesheetGraph->pluck('total_hours');


        return view('hrms.admin.managerdashboard.index', compact(
            'departmentName',
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateArrivals',
            'pendingLeaves',
            'approvedLeaves',
            'attendanceTrendData',
            'attendanceTrendLabels',
            'recentAttendance',
            'upcomingHolidays',
            'completedTasks',
            'pendingTasks',
            'projectNames',
            'projectHours',
            'taskLabels',
            'taskData',
            'employeeNames',
            'employeeHours'
        ));
    }
}
