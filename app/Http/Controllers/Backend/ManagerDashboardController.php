<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $managerId   = Session::get('user_id');
        $today       = now()->format('Y-m-d');

        // Manager's own department
        $manager = DB::table('allemployees')->where('id', $managerId)->first();
        $departmentId = $manager->department ?? null;

        $departmentName = DB::table('department')->where('id', $departmentId)->value('department') ?? 'N/A';

        // ── TEAM STRENGTH ────────────────────────────────────────────────────
        $teamStrength = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('department', $departmentId)
            ->count();

        $activeTeamCount = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('department', $departmentId)
            ->where('status', 'active')
            ->count();

        // ── ATTENDANCE ───────────────────────────────────────────────────────
        $firstPunchins = DB::table('attendances')
            ->select('employee_id', DB::raw('MIN(punch_in) as first_punch_in'), DB::raw('MAX(punch_out) as last_punch_out'))
            ->whereDate('date', $today)
            ->whereNotNull('punch_in')
            ->groupBy('employee_id');

        $todayAttendance = DB::table('allemployees as ae')
            ->leftJoinSub($firstPunchins, 'fp', 'ae.id', '=', 'fp.employee_id')
            ->leftJoin('schedule as s', function ($join) use ($today) {
                $join->on('ae.id', '=', 's.employee_id')
                    ->where('s.schedule_start_date', '<=', $today)
                    ->where('s.schedule_end_date', '>=', $today)
                    ->where('s.publish', 1)
                    ->where('s.deleted_at', 0)
                    ->where('s.is_current', 1);
            })
            ->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->select(
                'ae.id',
                'ae.firstname',
                'ae.lastname',
                'ae.profile_image',
                'des.designation',
                'fp.first_punch_in as punch_in',
                'fp.last_punch_out as punch_out',
                'sh.start_time as scheduled_start'
            )
            ->where('ae.deleted_at', 0)
            ->where('ae.status', 'active')
            ->where('ae.department', $departmentId)
            ->get();

        $todayAttendance->each(function ($a) {
            if (!$a->punch_in) {
                $a->status = 'Absent';
                $a->is_late = false;
                $a->late_minutes = 0;
                return;
            }
            $checkIn = Carbon::parse($a->punch_in);
            $start = $a->scheduled_start
                ? Carbon::parse($a->scheduled_start)
                : Carbon::parse($a->punch_in)->startOfDay()->setTime(9, 30);
            $grace = $start->copy()->addMinutes(5);
            if ($checkIn->lte($grace)) {
                $a->status = 'On Time';
                $a->is_late = false;
                $a->late_minutes = 0;
            } else {
                $a->status = 'Late';
                $a->is_late = true;
                $a->late_minutes = $checkIn->diffInMinutes($start);
            }
        });

        $presentToday   = $todayAttendance->whereNotNull('punch_in')->count();
        $absentToday    = $todayAttendance->whereNull('punch_in')->count();
        $lateArrivals   = $todayAttendance->where('is_late', true)->whereNotNull('punch_in')->count();
        $presentList    = $todayAttendance->filter(fn($a) => !is_null($a->punch_in))->values();
        $absentList     = $todayAttendance->filter(fn($a) => is_null($a->punch_in))->values();
        $lateList       = $todayAttendance->filter(fn($a) => $a->is_late && !is_null($a->punch_in))->values();

        // Pending attendance requests (employees of this dept with attendance corrections)
        $pendingAttendanceRequests = Schema::hasTable('attendance_requests')
            ? DB::table('attendance_requests as ar')
                ->join('allemployees as ae', 'ar.employee_id', '=', 'ae.id')
                ->where('ae.department', $departmentId)
                ->where('ar.status', 'pending')
                ->select('ar.*', 'ae.firstname', 'ae.lastname')
                ->orderBy('ar.created_at', 'desc')
                ->get()
            : collect();

        $pendingAttendanceCount = $pendingAttendanceRequests->count();

        // Weekly attendance trend
        $attendanceTrendData   = [];
        $attendanceTrendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = DB::table('attendances as a')
                ->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')
                ->where('ae.department', $departmentId)
                ->whereDate('a.date', $date->format('Y-m-d'))
                ->whereNotNull('a.punch_in')
                ->distinct('a.employee_id')
                ->count();
            $attendanceTrendData[]   = $count;
            $attendanceTrendLabels[] = $date->format('M d');
        }

        $recentAttendance = DB::table('attendances as a')
            ->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->select('ae.firstname', 'ae.lastname', 'des.designation', 'a.punch_in', 'a.punch_out', 'a.date')
            ->where('ae.department', $departmentId)
            ->orderBy('a.date', 'desc')
            ->limit(10)
            ->get();

        // ── LEAVES ───────────────────────────────────────────────────────────
        $pendingLeavesList = DB::table('employee_leaves as el')
            ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
            ->where('ae.department', $departmentId)
            ->where('el.status', 'pending')
            ->where(function($q) { $q->whereNull('el.manager_approved')->orWhere('el.manager_approved', 'pending'); })
            ->select(
                'el.id', 'ae.firstname', 'ae.lastname', 'ae.profile_image',
                'el.leave_type', 'el.from_date', 'el.to_date', 'el.no_of_days', 'el.leave_reason'
            )
            ->orderBy('el.created_at', 'desc')
            ->get();

        $pendingPermissionsList = Schema::hasTable('employee_permissions')
            ? DB::table('employee_permissions as ep')
                ->join('allemployees as ae', 'ep.employee_id', '=', 'ae.id')
                ->where('ae.department', $departmentId)
                ->where('ep.status', 'pending')
                ->where(function($q) { $q->whereNull('ep.manager_approved')->orWhere('ep.manager_approved', 'pending'); })
                ->select(
                    'ep.id', 'ae.firstname', 'ae.lastname', 'ae.profile_image',
                    'ep.permission_date', 'ep.start_time', 'ep.end_time', 'ep.duration', 'ep.permission_reason'
                )
                ->orderBy('ep.created_at', 'desc')
                ->get()
            : collect();

        $pendingLeavesCount      = $pendingLeavesList->count();
        $pendingPermissionsCount = $pendingPermissionsList->count();

        $approvedLeaves = DB::table('employee_leaves as el')
            ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
            ->where('ae.department', $departmentId)
            ->where('el.manager_approved', 'approved')
            ->count();

        $rejectedLeaves = DB::table('employee_leaves as el')
            ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
            ->where('ae.department', $departmentId)
            ->where('el.manager_approved', 'declined')
            ->count();

        // ── EMPLOYEE STATUS ───────────────────────────────────────────────────
        $newJoinersThisMonth = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('department', $departmentId)
            ->whereYear('joiningdate', now()->year)
            ->whereMonth('joiningdate', now()->month)
            ->count();

        $onNoticePeriod = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('department', $departmentId)
            ->whereIn('status', ['notice', 'notice period', 'on_notice', 'resigned'])
            ->count();

        $recentEmployees = DB::table('allemployees as ae')
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->where('ae.deleted_at', 0)
            ->where('ae.department', $departmentId)
            ->select('ae.id', 'ae.firstname', 'ae.lastname', 'ae.profile_image', 'ae.status', 'ae.joiningdate', 'des.designation')
            ->orderBy('ae.created_at', 'desc')
            ->limit(8)
            ->get();

        // ── INTERVIEW / RECRUITMENT ───────────────────────────────────────────
        $openPositions = DB::table('managejobs')
            ->where('status', 'open')
            ->where('department', $departmentId)
            ->sum('vacancies');

        $interviewsScheduled = DB::table('interviews as i')
            ->join('managejobs as j', 'i.job_id', '=', 'j.id')
            ->where('j.department', $departmentId)
            ->where('i.interview_datetime', '>=', now())
            ->count();

        $interviewsToday = DB::table('interviews as i')
            ->join('managejobs as j', 'i.job_id', '=', 'j.id')
            ->where('j.department', $departmentId)
            ->whereDate('i.interview_datetime', $today)
            ->count();

        $recentInterviews = DB::table('interviews as i')
            ->join('candidate as c', 'i.candidate_id', '=', 'c.id')
            ->join('managejobs as j', 'i.job_id', '=', 'j.id')
            ->where('j.department', $departmentId)
            ->select('i.*', DB::raw("CONCAT(c.first_name, ' ', c.last_name) as candidate_name"), 'c.email as candidate_email', 'j.job_title')
            ->orderBy('i.interview_datetime', 'asc')
            ->limit(5)
            ->get();

        // ── TASKS ─────────────────────────────────────────────────────────────
        $deptEmployeeIds = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('department', $departmentId)
            ->pluck('id');

        $taskBase = DB::table('tasks')
            ->whereIn('assigned_to', $deptEmployeeIds)
            ->where('deleted_at', 0);

        $assignedTasks  = (clone $taskBase)->count();
        $completedTasks = (clone $taskBase)->where('status', 'completed')->count();
        $pendingTasks   = (clone $taskBase)->whereNotIn('status', ['completed'])->count();
        $overdueTasks   = (clone $taskBase)->whereNotIn('status', ['completed'])->where('due_date', '<', $today)->count();

        // Project-wise task status
        $projectTaskStatus = DB::table('tasks as t')
            ->join('projects as p', 't.projects', '=', 'p.projectid')
            ->whereIn('t.assigned_to', $deptEmployeeIds)
            ->where('t.deleted_at', 0)
            ->select('p.projectname', DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN t.status="completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN t.status!="completed" AND t.due_date < "'.$today.'" THEN 1 ELSE 0 END) as overdue')
            )
            ->groupBy('p.projectname')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Team productivity (completed tasks per employee this month)
        $teamProductivity = DB::table('tasks as t')
            ->join('allemployees as ae', 't.assigned_to', '=', 'ae.id')
            ->whereIn('t.assigned_to', $deptEmployeeIds)
            ->where('t.deleted_at', 0)
            ->where('t.status', 'completed')
            ->whereYear('t.updated_at', now()->year)
            ->whereMonth('t.updated_at', now()->month)
            ->select('ae.firstname', 'ae.lastname',
                DB::raw('COUNT(*) as completed_count'),
                DB::raw('SUM(CASE WHEN t.due_date >= t.updated_at THEN 1 ELSE 0 END) as on_time')
            )
            ->groupBy('ae.id', 'ae.firstname', 'ae.lastname')
            ->orderByDesc('completed_count')
            ->limit(5)
            ->get();

        // ── ATTENDANCE EXCEPTIONS ─────────────────────────────────────────────
        // Upcoming planned leaves (approved, future)
        $upcomingPlannedLeaves = DB::table('employee_leaves as el')
            ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->where('ae.department', $departmentId)
            ->where('el.status', 'approved')
            ->where('el.from_date', '>', $today)
            ->select('ae.firstname', 'ae.lastname', 'des.designation', 'el.leave_type', 'el.from_date', 'el.to_date', 'el.no_of_days')
            ->orderBy('el.from_date')
            ->limit(10)
            ->get();

        // Missing check-ins (punched in but no punch-out, older than today)
        $missingCheckIns = DB::table('allemployees as ae')
            ->leftJoin('attendances as a', function($join) use ($today) {
                $join->on('ae.id', '=', 'a.employee_id')->whereDate('a.date', $today);
            })
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->where('ae.deleted_at', 0)
            ->where('ae.status', 'active')
            ->where('ae.department', $departmentId)
            ->whereNull('a.punch_in')
            ->select('ae.firstname', 'ae.lastname', 'des.designation')
            ->limit(10)
            ->get();

        // Early check-outs (punch_out significantly before shift end)
        $earlyCheckOuts = DB::table('attendances as a')
            ->join('allemployees as ae', 'a.employee_id', '=', 'ae.id')
            ->leftJoin('designation as des', 'ae.designation', '=', 'des.id')
            ->leftJoin('schedule as s', function($join) use ($today) {
                $join->on('ae.id', '=', 's.employee_id')
                    ->where('s.schedule_start_date', '<=', $today)
                    ->where('s.schedule_end_date', '>=', $today)
                    ->where('s.publish', 1)->where('s.deleted_at', 0)->where('s.is_current', 1);
            })
            ->leftJoin('shifts as sh', 's.shift_id', '=', 'sh.id')
            ->where('ae.department', $departmentId)
            ->whereDate('a.date', $today)
            ->whereNotNull('a.punch_out')
            ->whereNotNull('sh.end_time')
            ->whereRaw('TIME(a.punch_out) < SUBTIME(sh.end_time, "00:30:00")')
            ->select('ae.firstname', 'ae.lastname', 'des.designation', 'a.punch_out', 'sh.end_time')
            ->limit(10)
            ->get();

        // ── UPCOMING HOLIDAYS ─────────────────────────────────────────────────
        $upcomingHolidays = DB::table('holidays')
            ->where('holidaydate', '>=', $today)
            ->where('holidaydate', '<=', now()->addDays(30)->format('Y-m-d'))
            ->orderBy('holidaydate', 'asc')
            ->get();

        return view('hrms.admin.managerdashboard.index', compact(
            'departmentName',
            'departmentId',
            // Attendance
            'teamStrength',
            'activeTeamCount',
            'presentToday',
            'absentToday',
            'lateArrivals',
            'presentList',
            'absentList',
            'lateList',
            'pendingAttendanceCount',
            'pendingAttendanceRequests',
            'attendanceTrendData',
            'attendanceTrendLabels',
            'recentAttendance',
            // Leaves
            'pendingLeavesCount',
            'pendingPermissionsCount',
            'pendingLeavesList',
            'pendingPermissionsList',
            'approvedLeaves',
            'rejectedLeaves',
            // Employee Status
            'newJoinersThisMonth',
            'onNoticePeriod',
            'recentEmployees',
            // Interview
            'openPositions',
            'interviewsScheduled',
            'interviewsToday',
            'recentInterviews',
            // Tasks
            'assignedTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'projectTaskStatus',
            'teamProductivity',
            // Attendance Exceptions
            'upcomingPlannedLeaves',
            'missingCheckIns',
            'earlyCheckOuts',
            // Misc
            'upcomingHolidays'
        ));
    }

    public function processApproval(Request $request)
    {
        $managerId    = Session::get('user_id');
        $type         = $request->input('type');
        $id           = (int) $request->input('id');
        $action       = $request->input('action');

        try {
            // Verify the record belongs to manager's department before acting
            if ($type === 'leave') {
                $leave = DB::table('employee_leaves as el')
                    ->join('allemployees as ae', 'el.employee_id', '=', 'ae.id')
                    ->where('el.id', $id)
                    ->where('ae.department', function ($q) use ($managerId) {
                        $q->select('department')->from('allemployees')->where('id', $managerId);
                    })
                    ->select('el.*')
                    ->first();

                if (!$leave) {
                    return response()->json(['success' => false, 'message' => 'Not found or access denied'], 404);
                }
                if (!in_array($action, ['approved', 'declined', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                // Capture BEFORE update
                $previousStatus = $leave->status ?? 'pending';

                DB::table('employee_leaves')->where('id', $id)->update([
                    'manager_approved'    => $action,
                    'manager_approved_by' => $managerId,
                ]);

                // Recalculate consensus (2/3 rule)
                $leave = DB::table('employee_leaves')->where('id', $id)->first();
                $approved = 0; $declined = 0;
                foreach (['tl_approved','hr_approved','manager_approved'] as $col) {
                    if ($leave->$col === 'approved') $approved++;
                    elseif ($leave->$col === 'declined') $declined++;
                }
                $overall = $approved >= 2 ? 'approved' : ($declined >= 2 ? 'declined' : 'pending');
                DB::table('employee_leaves')->where('id', $id)->update(['status' => $overall]);
                if ($overall === 'approved' && $previousStatus !== 'approved') {
                    $maxAllowed = (int)(DB::table('annual_leaves')->value('max_allowed') ?? 0);
                    $paidDays   = $maxAllowed > 0 ? min((int)$leave->no_of_days, $maxAllowed) : (int)$leave->no_of_days;
                    $lopDays    = (int)$leave->no_of_days - $paidDays;
                    if ($paidDays > 0) {
                        DB::table('employee_leave_balances')
                            ->where('employee_id', $leave->employee_id)
                            ->where('leave_type', $leave->leave_type)
                            ->update([
                                'used_days'      => DB::raw("used_days + {$paidDays}"),
                                'remaining_days' => DB::raw("GREATEST(0, remaining_days - {$paidDays})"),
                                'updated_at'     => now(),
                            ]);
                    }
                    DB::table('employee_leaves')->where('id', $id)->update(['paid_days' => $paidDays, 'lop_days' => $lopDays]);
                } elseif ($overall !== 'approved' && $previousStatus === 'approved') {
                    $paidWas = (int)($leave->paid_days ?? $leave->no_of_days);
                    if ($paidWas > 0) {
                        DB::table('employee_leave_balances')
                            ->where('employee_id', $leave->employee_id)
                            ->where('leave_type', $leave->leave_type)
                            ->update([
                                'used_days'      => DB::raw('GREATEST(0, used_days - '.$paidWas.')'),
                                'remaining_days' => DB::raw('remaining_days + '.$paidWas),
                                'updated_at'     => now(),
                            ]);
                    }
                    DB::table('employee_leaves')->where('id', $id)->update(['paid_days' => 0, 'lop_days' => 0]);
                }

            } elseif ($type === 'permission') {
                $perm = DB::table('employee_permissions as ep')
                    ->join('allemployees as ae', 'ep.employee_id', '=', 'ae.id')
                    ->where('ep.id', $id)
                    ->where('ae.department', function ($q) use ($managerId) {
                        $q->select('department')->from('allemployees')->where('id', $managerId);
                    })
                    ->first();

                if (!$perm) {
                    return response()->json(['success' => false, 'message' => 'Not found or access denied'], 404);
                }
                if (!in_array($action, ['approved', 'declined', 'pending'])) {
                    return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
                }
                // Capture BEFORE update
                $previousPermStatus = $perm->status ?? 'pending';

                DB::table('employee_permissions')->where('id', $id)->update([
                    'manager_approved'    => $action,
                    'manager_approved_by' => $managerId,
                ]);

                // Recalculate consensus
                $perm = DB::table('employee_permissions')->where('id', $id)->first();
                $approved = 0; $declined = 0;
                foreach (['tl_approved','hr_approved','manager_approved'] as $col) {
                    if ($perm->$col === 'approved') $approved++;
                    elseif ($perm->$col === 'declined') $declined++;
                }
                $overall = $approved >= 2 ? 'approved' : ($declined >= 2 ? 'declined' : 'pending');
                DB::table('employee_permissions')->where('id', $id)->update(['status' => $overall]);

            } else {
                return response()->json(['success' => false, 'message' => 'Unknown type'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
