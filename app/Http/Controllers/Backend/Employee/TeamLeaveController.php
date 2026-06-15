<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeamLeaveController extends Controller
{
    
public function index()
{
    $role = session('role');
    $year = request('year', date('Y'));

    // ============================
    // 1. ADMIN LOGIN
    // ============================
    if ($role === 'admin') {
        $teamMembers = DB::table('allemployees as a')
            ->leftJoin('department as d',    'a.department', '=', 'd.id')
            ->leftJoin('designation as des', 'a.designation','=', 'des.id')
            ->select('a.*', 'd.department as department_name', 'des.designation as designation_name')
            ->where('a.status', 'active')
            ->orderBy('a.firstname')
            ->get();

        $teamMembersWithLeaves = [];
        foreach ($teamMembers as $member) {
            $teamMembersWithLeaves[] = $this->getMemberLeaveSummary($member, $year);
        }

        // Build allLeaveRequests
        $allLeaveRequests = [];
        foreach ($teamMembersWithLeaves as $member) {
            foreach ($member->leaves as $leave) {
                $allLeaveRequests[] = [
                    'member' => $member,
                    'leave'  => $leave,
                    'overall_status' => $this->getOverallStatus(
                        $leave->tl_approved ?? null,
                        $leave->hr_approved ?? null,
                        $leave->manager_approved ?? null
                    ),
                    'tl_name'      => $this->getApproverName($leave->tl_approved_by ?? null),
                    'hr_name'      => $this->getApproverName($leave->hr_approved_by ?? null),
                    'manager_name' => $this->getApproverName($leave->manager_approved_by ?? null),
                ];
            }
        }

        $pendingLeaves = collect($allLeaveRequests)
            ->filter(fn($item) => $item['overall_status'] === 'pending')
            ->values();

        // Build allPermissionsWithStatus
        $allPermissions = DB::table('employee_permissions')
            ->whereIn('employee_id', collect($teamMembersWithLeaves)->pluck('id'))
            ->orderBy('permission_date', 'desc')
            ->get();

        $allPermissionsWithStatus = $allPermissions->map(function ($p) {
            if (!isset($p->tl_approved))      $p->tl_approved      = null;
            if (!isset($p->hr_approved))      $p->hr_approved      = null;
            if (!isset($p->manager_approved)) $p->manager_approved = null;
            $p->overall_status = $this->getOverallStatus(
                $p->tl_approved,
                $p->hr_approved,
                $p->manager_approved
            );
            return $p;
        });

        $pendingPermissions = $allPermissionsWithStatus
            ->filter(fn($p) => $p->overall_status === 'pending')
            ->values();

        return view('hrms.Employee.TeamLeave.index', [
            'employee'                => null,
            'teamMembers'             => $teamMembersWithLeaves,
            'allLeaveRequests'        => $allLeaveRequests,
            'pendingLeaves'           => $pendingLeaves,
            'allPermissionsWithStatus'=> $allPermissionsWithStatus,
            'pendingPermissions'      => $pendingPermissions,
            'year'                    => $year,
            'isTeamLead'              => true,   // admin can see all columns
            'isManager'               => true,
            'isHR'                    => true,
            'totalTeamMembers'        => count($teamMembers),
        ]);
    }

    // ============================
    // 2. EMPLOYEE LOGIN (Manager/TL/HR)
    // ============================
    $employeeId = session('user_id');

    if (!$employeeId) {
        return redirect()->route('login')->withErrors('Please login first.');
    }

    $employee = DB::table('allemployees')
        ->select('id', 'employeeid', 'firstname', 'lastname', 'team_lead_id', 'manager_id', 'designation', 'hierarchy_id')
        ->where('id', $employeeId)
        ->first();

    if (!$employee) {
        return redirect()->back()->with('error', 'Employee not found.');
    }

    $isTeamLead = DB::table('allemployees')->where('team_lead_id', $employeeId)->exists();
    $isManager  = DB::table('allemployees')->where('manager_id',  $employeeId)->exists();

    $hierarchy      = DB::table('hierarchies')->where('id', $employee->hierarchy_id)->first();
    $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';
    $isHR           = in_array($hierarchyLevel, ['hr', 'hr manager']);

    // Also detect via hierarchy level so badge-based roles work even if no employee rows exist yet
    if (!$isManager && in_array($hierarchyLevel, ['manager'])) {
        $isManager = true;
    }
    if (!$isTeamLead && in_array($hierarchyLevel, ['team lead', 'teamlead', 'tl'])) {
        $isTeamLead = true;
    }

    $teamMembers = $this->getTeamMembers($employeeId, $isTeamLead, $isManager, $isHR);

    $teamMembersWithLeaves = [];
    foreach ($teamMembers as $member) {
        $teamMembersWithLeaves[] = $this->getMemberLeaveSummary($member, $year);
    }

    // Build allLeaveRequests for employee role too
    $allLeaveRequests = [];
    foreach ($teamMembersWithLeaves as $member) {
        foreach ($member->leaves as $leave) {
            $allLeaveRequests[] = [
                'member' => $member,
                'leave'  => $leave,
                'overall_status' => $this->getOverallStatus(
                    $leave->tl_approved ?? null,
                    $leave->hr_approved ?? null,
                    $leave->manager_approved ?? null
                ),
                'tl_name'      => $this->getApproverName($leave->tl_approved_by ?? null),
                'hr_name'      => $this->getApproverName($leave->hr_approved_by ?? null),
                'manager_name' => $this->getApproverName($leave->manager_approved_by ?? null),
            ];
        }
    }

    $pendingLeaves = collect($allLeaveRequests)
        ->filter(fn($item) => $item['overall_status'] === 'pending')
        ->values();

    $allPermissions = DB::table('employee_permissions')
        ->whereIn('employee_id', collect($teamMembersWithLeaves)->pluck('id'))
        ->orderBy('permission_date', 'desc')
        ->get();

    $allPermissionsWithStatus = $allPermissions->map(function ($p) {
        if (!isset($p->tl_approved))      $p->tl_approved      = null;
        if (!isset($p->hr_approved))      $p->hr_approved      = null;
        if (!isset($p->manager_approved)) $p->manager_approved = null;
        $p->overall_status = $this->getOverallStatus(
            $p->tl_approved,
            $p->hr_approved,
            $p->manager_approved
        );
        return $p;
    });

    $pendingPermissions = $allPermissionsWithStatus
        ->filter(fn($p) => $p->overall_status === 'pending')
        ->values();

    return view('hrms.Employee.TeamLeave.index', [
        'employee'                => $employee,
        'teamMembers'             => $teamMembersWithLeaves,
        'allLeaveRequests'        => $allLeaveRequests,
        'pendingLeaves'           => $pendingLeaves,
        'allPermissionsWithStatus'=> $allPermissionsWithStatus,
        'pendingPermissions'      => $pendingPermissions,
        'year'                    => $year,
        'isTeamLead'              => $isTeamLead,
        'isManager'               => $isManager,
        'isHR'                    => $isHR,
        'totalTeamMembers'        => count($teamMembers),
    ]);
}

    /**
     * Get team members based on the employee's role with correct department join
     */
    private function getTeamMembers($employeeId, $isTeamLead, $isManager, $isHR = false)
{
    $query = DB::table('allemployees as a')
        ->select(
            'a.id', 'a.employeeid', 'a.firstname', 'a.lastname', 'a.email',
            'des.designation as designation', 'd.department as department_name',
            'a.team_lead_id', 'a.manager_id', 'a.joiningdate', 'a.status',
            't.firstname as teamlead_firstname', 't.lastname as teamlead_lastname',
            'm.firstname as manager_firstname', 'm.lastname as manager_lastname'
        )
        ->leftJoin('designation as des', 'a.designation', '=', 'des.id')
        ->leftJoin('allemployees as t', 'a.team_lead_id', '=', 't.id')
        ->leftJoin('allemployees as m', 'a.manager_id',   '=', 'm.id')
        ->leftJoin('department as d',   'a.department',   '=', 'd.id')
        ->where('a.status', 'active');

    if ($isHR) {
        // HR sees all active employees
    } elseif ($isManager) {
        // Manager: filter by manager_id FK if data exists, else fall back to same department
        $hasManagerIdData = DB::table('allemployees')->where('manager_id', $employeeId)->exists();
        if ($hasManagerIdData) {
            $query->where('a.manager_id', $employeeId);
        } else {
            // Fall back to department-based filtering
            $managerDept = DB::table('allemployees')->where('id', $employeeId)->value('department');
            if ($managerDept) {
                $query->where('a.department', $managerDept);
            }
        }
    } elseif ($isTeamLead) {
        // Team lead: strictly see members where team_lead_id = this person
        // If they have no members assigned, they should see an empty list, NOT everyone.
        $query->where('a.team_lead_id', $employeeId);
    }

    return $query->orderBy('a.firstname')->get();
}

    /**
     * Get leave summary for a team member with attendance checking
     */
    private function getMemberLeaveSummary($member, $year)
    {
        // -----------------------------------------
        // 1. Fetch allocation from employee_leave_information
        // -----------------------------------------
        $info = DB::table('employee_leave_information')
            ->where('employee_id', $member->id)
            ->first();

        $allocated = [
            "Casual Leave"    => $info->casual_leaves ?? 0,
            "Sick Leave"      => $info->sick_leaves ?? 0,
            "Hospitalisation" => $info->hospitalization_leaves ?? 0,
            "Maternity Leave" => $info->maternity_leaves ?? 0,
            "Paternity Leave" => $info->paternity_leaves ?? 0,
        ];

        // -----------------------------------------
        // 2. Check TODAY'S / FILTERED DATE attendance
        // -----------------------------------------
        $today = request('date', date('Y-m-d')); // Grab filtered date from request
        
        // Get attendance record for today
        $attendanceToday = DB::table('attendances')
            ->where('employee_id', $member->id)
            ->whereDate('date', $today)
            ->first();
        
        // Determine attendance status
        $attendanceStatus = 'Absent'; // Default
        if ($attendanceToday) {
            // If they have punched in at all, they are present.
            // (Previously this falsely marked them as Half Day if they hadn't punched out yet).
            if ($attendanceToday->punch_in || $attendanceToday->punch_out) {
                $attendanceStatus = 'Present';
            }
        }

        // -----------------------------------------
        // 3. Add Working Day Check
        // -----------------------------------------
        $workingDayCheck = $this->isWorkingDay($member->id, $today);
        $member->isWorkingDay = $workingDayCheck['is_working'];
        $member->workingDayReason = $workingDayCheck['reason'];

        // -----------------------------------------
        // 4. Fetch paid/lop directly from employee_leaves (source of truth)
        // -----------------------------------------
        $balanceRows = DB::table('employee_leave_balances')
            ->where('employee_id', $member->id)
            ->get()
            ->keyBy('leave_type');

        $paidRows = DB::table('employee_leaves')
            ->where('employee_id', $member->id)
            ->whereYear('from_date', $year)
            ->where('status', 'approved')
            ->select('leave_type', DB::raw('SUM(paid_days) as total_paid'), DB::raw('SUM(lop_days) as total_lop'))
            ->groupBy('leave_type')
            ->get()
            ->keyBy('leave_type');

        // -----------------------------------------
        // 5. Build leave balances from balances table
        // -----------------------------------------
        $leaveTypeMap = [
            'Casual Leave'    => 'Casual Leave',
            'Sick Leave'      => 'Sick',
            'Hospitalisation' => 'Hospitalisation',
            'Maternity Leave' => 'Maternity Leave',
            'Paternity Leave' => 'Paternity Leave',
        ];

        $leaveBalance = [];
        foreach ($leaveTypeMap as $displayType => $dbKey) {
            $bal            = $balanceRows[$dbKey] ?? $balanceRows[$displayType] ?? null;
            $allocated_days = $bal ? $bal->allocated_days : ($allocated[$displayType] ?? 0);
            $remaining      = $bal ? $bal->remaining_days : $allocated_days;
            $row            = $paidRows[$dbKey] ?? $paidRows[$displayType] ?? null;
            $paid           = $row ? (int)$row->total_paid : 0;
            $lop            = $row ? (int)$row->total_lop  : 0;

            $leaveBalance[$displayType] = [
                'allocated' => $allocated_days,
                'paid'      => $paid,
                'lop'       => $lop,
                'remaining' => $remaining,
            ];
        }

        // -----------------------------------------
        // 6. SUM totals
        // -----------------------------------------
        $totalPaid      = array_sum(array_column($leaveBalance, 'paid'));
        $totalLOP       = array_sum(array_column($leaveBalance, 'lop'));
        $totalRemaining = array_sum(array_column($leaveBalance, 'remaining'));

        // -----------------------------------------
        // 7. Get all leaves for this selected year
        // -----------------------------------------
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $member->id)
            ->whereYear('from_date', $year)
            ->orderBy('from_date', 'desc')
            ->get();

        // Upcoming leaves (only for this year)
        $upcomingLeaves = DB::table('employee_leaves')
            ->where('employee_id', $member->id)
            ->where('status', 'approved')
            ->whereYear('from_date', $year)
            ->where('from_date', '>=', now())
            ->orderBy('from_date', 'asc')
            ->get();

        // Past leaves (only for this year)
        $pastLeaves = DB::table('employee_leaves')
            ->where('employee_id', $member->id)
            ->where('status', 'approved')
            ->whereYear('from_date', $year)
            ->where('from_date', '<', now())
            ->orderBy('from_date', 'desc')
            ->get();

        // -----------------------------------------
        // 8. Check if employee is on leave today
        // -----------------------------------------
        $onLeaveToday = DB::table('employee_leaves')
            ->where('employee_id', $member->id)
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->first();

        // -----------------------------------------
        // 9. Attach all data to the member object
        // -----------------------------------------
        $member->attendanceToday = $attendanceToday;
        $member->attendanceStatus = $attendanceStatus;
        $member->onLeaveToday = $onLeaveToday;
        $member->onLeaveType = $onLeaveToday ? $onLeaveToday->leave_type : null;
        
        $member->leaveBalance = $leaveBalance;
        $member->totalPaid = $totalPaid;
        $member->totalLOP = $totalLOP;
        $member->totalRemaining = $totalRemaining;
        $member->totalLeavesTaken = $totalPaid + $totalLOP;

        $member->leaves = $leaves;
        $member->upcomingLeaves = $upcomingLeaves;
        $member->pastLeaves = $pastLeaves;

        return $member;
    }
    
    /**
     * View individual team member's leave details
     */
    public function show($employeeId)
    {
        $loggedInEmployeeId = session('user_id');
        
        // Check if the logged-in employee can view this team member's details
        $canView = $this->canViewTeamMember($loggedInEmployeeId, $employeeId);

        if (!$canView) {
            return redirect()->route('team-leaves.index')->with('error', 'You are not authorized to view this team member\'s details.');
        }

        // Get the team member details
        $member = DB::table('allemployees as a')
            ->select(
                'a.id', 'a.employeeid', 'a.firstname', 'a.lastname', 'a.email',
                'a.joiningdate', 'a.department', 'a.designation',
                'des.designation as designation_name',
                'd.department as department_name',
                't.firstname as teamlead_firstname', 't.lastname as teamlead_lastname',
                'm.firstname as manager_firstname',  'm.lastname as manager_lastname'
            )
            ->leftJoin('designation as des', 'a.designation', '=', 'des.id')
            ->leftJoin('department as d',    'a.department',  '=', 'd.id')
            ->leftJoin('allemployees as t',  'a.team_lead_id','=', 't.id')
            ->leftJoin('allemployees as m',  'a.manager_id',  '=', 'm.id')
            ->where('a.id', $employeeId)
            ->first();

        if (!$member) {
            return redirect()->route('team-leaves.index')->with('error', 'Team member not found.');
        }

        $year = request('year', date('Y'));

        // Get leave summary for this member
        $memberWithLeaves = $this->getMemberLeaveSummary($member, $year);

        return view('hrms.Employee.TeamLeave.show', [
            'member'       => $memberWithLeaves,
            'year'         => $year,
            'leaveBalance' => $memberWithLeaves->leaveBalance,
            'leaves'       => $memberWithLeaves->leaves,
            'upcomingLeaves' => $memberWithLeaves->upcomingLeaves,
            'pastLeaves'   => $memberWithLeaves->pastLeaves,
            'isTeamLead'   => DB::table('allemployees')->where('team_lead_id', $loggedInEmployeeId)->exists() || in_array(strtolower(trim(DB::table('hierarchies')->where('id', DB::table('allemployees')->where('id',$loggedInEmployeeId)->value('hierarchy_id'))->value('hierarchy_level') ?? '')), ['team lead','teamlead','tl']),
            'isManager'    => DB::table('allemployees')->where('manager_id',  $loggedInEmployeeId)->exists() || in_array(strtolower(trim(DB::table('hierarchies')->where('id', DB::table('allemployees')->where('id',$loggedInEmployeeId)->value('hierarchy_id'))->value('hierarchy_level') ?? '')), ['manager']),
            'isHR'         => in_array(strtolower(trim(DB::table('hierarchies')->where('id', DB::table('allemployees')->where('id',$loggedInEmployeeId)->value('hierarchy_id'))->value('hierarchy_level') ?? '')), ['hr','hr manager']),
        ]);
    }

    /**
     * Check if logged-in employee can view team member's details
     */
    private function canViewTeamMember($loggedInEmployeeId, $teamMemberId)
    {
        if (!$loggedInEmployeeId || !$teamMemberId) return false;

        // Admin always can view
        if (session('role') === 'admin') return true;

        $teamMember = DB::table('allemployees')->where('id', $teamMemberId)->first();
        if (!$teamMember) return false;

        // Direct FK match
        if ($teamMember->team_lead_id == $loggedInEmployeeId || $teamMember->manager_id == $loggedInEmployeeId) {
            return true;
        }

        $loggedInEmp = DB::table('allemployees')->where('id', $loggedInEmployeeId)->first();
        if (!$loggedInEmp) return false;

        $hierarchy      = DB::table('hierarchies')->where('id', $loggedInEmp->hierarchy_id)->first();
        $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';

        // HR sees everyone
        if (in_array($hierarchyLevel, ['hr', 'hr manager'])) return true;

        // Manager sees same department
        if (in_array($hierarchyLevel, ['manager']) && $loggedInEmp->department == $teamMember->department) {
            return true;
        }

        // Team Lead sees members where team_lead_id matches (already covered above, but also by hierarchy)
        if (in_array($hierarchyLevel, ['team lead', 'teamlead', 'tl'])) {
            return DB::table('allemployees')
                ->where('id', $teamMemberId)
                ->where('team_lead_id', $loggedInEmployeeId)
                ->exists();
        }

        return false;
    }

    /**
     * Apply leave on behalf of team member
     */
    public function applyLeaveForMember(Request $request, $employeeId)
    {
        // Validate the request
        $validated = $request->validate([
            'leave_type' => 'required|in:Casual Leave,Sick,Hospitalisation,Maternity Leave,Paternity Leave,LOP',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'num_days' => 'required|integer|min:1',
            'leave_reason' => 'required|string|max:255',
            'type' => 'required|in:Paid,Unpaid'
        ]);

        // Get logged-in employee (who is applying the leave)
        $loggedInEmployeeId = session('user_id');

        // Check if the logged-in employee can apply leave for this team member
        $canView = $this->canViewTeamMember($loggedInEmployeeId, $employeeId);

        if (!$canView) {
            return redirect()->back()->with('error', 'You are not authorized to apply leave for this team member.');
        }

        // Get team member
        $teamMember = DB::table('allemployees')
            ->where('id', $employeeId)
            ->first();

        if (!$teamMember) {
            return redirect()->back()->with('error', 'Team member not found.');
        }

        // Calculate remaining leaves
        $remainingLeaves = $this->calculateRemainingLeaves($employeeId, $request->leave_type, $request->num_days, $request->type);

        if ($remainingLeaves < 0 && $request->type == 'Paid') {
            return redirect()->back()->with('error', 'Insufficient paid leave balance for this type.');
        }

        // Insert leave record
        DB::table('employee_leaves')->insert([
            'employee_id' => $employeeId,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'no_of_days' => $request->num_days,
            'remaining_leaves' => $remainingLeaves,
            'leave_reason' => $request->leave_reason,
            'type' => $request->type,
            'applied_by' => $loggedInEmployeeId,
            'status' => 'approved', // Auto-approve since team lead/manager is applying
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update leave balance if it's a paid leave
        if ($request->type == 'Paid') {
            $this->updateLeaveBalance($employeeId, $request->leave_type, $request->num_days);
        }

        return redirect()->route('team-leaves.show', $employeeId)
            ->with('success', 'Leave applied successfully for ' . $teamMember->firstname . ' ' . $teamMember->lastname);
    }

    /**
     * Calculate remaining leaves
     */
    private function calculateRemainingLeaves($employeeId, $leaveType, $requestedDays, $type)
    {
        if ($type == 'Unpaid') {
            return 0;
        }

        // Get current balance for this leave type
        $balance = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->first();

        if (!$balance) {
            // Initialize if not exists
            $allocated = $this->getDefaultAllocation($leaveType);
            
            DB::table('employee_leave_balances')->insert([
                'employee_id' => $employeeId,
                'leave_type' => $leaveType,
                'allocated_days' => $allocated,
                'used_days' => 0,
                'remaining_days' => $allocated,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return $allocated - $requestedDays;
        }

        return $balance->remaining_days - $requestedDays;
    }

    /**
     * Update leave balance
     */
    private function updateLeaveBalance($employeeId, $leaveType, $usedDays)
    {
        DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->update([
                'used_days' => DB::raw('used_days + ' . $usedDays),
                'remaining_days' => DB::raw('remaining_days - ' . $usedDays),
                'updated_at' => now()
            ]);
    }

    /**
     * Get default allocation
     */
    private function getDefaultAllocation($leaveType)
    {
        switch ($leaveType) {
            case 'Casual Leave':
                return DB::table('annual_leaves')->value('days') ?? 12;
            case 'Sick':
                return DB::table('medical_leaves')->value('sick') ?? 6;
            case 'Hospitalisation':
                return DB::table('medical_leaves')->value('hospitalisation') ?? 30;
            case 'Maternity Leave':
                return DB::table('medical_leaves')->value('maternity') ?? 180;
            case 'Paternity Leave':
                return DB::table('medical_leaves')->value('paternity') ?? 7;
            default:
                return 0;
        }
    }

    /**
     * Check if today is a working day for the employee
     */
    private function isWorkingDay($employeeId, $date)
    {
        $today = date('Y-m-d');
        \Log::info("Checking working day for employee: {$employeeId}, date: {$date}, today: {$today}");
        
        // Check if it's a holiday
        $holiday = DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->first();
        
        if ($holiday) {
            \Log::info("Holiday found: {$holiday->title}");
            return [
                'is_working' => false,
                'reason' => 'Holiday: ' . $holiday->title,
                'type' => 'holiday'
            ];
        }

        // Get employee's schedule
        $schedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.schedule_start_date', '<=', $date)
            ->where(function($query) use ($date) {
                $query->where('schedule.repeat_every_week', '>', 0)
                      ->orWhere(function($q) use ($date) {
                          $q->where('schedule.repeat_every_week', 0)
                            ->where('schedule.schedule_end_date', '>=', $date);
                      });
            })
            ->select('shifts.days_of_week', 'shifts.shift_name', 'schedule.schedule_start_date', 'schedule.schedule_end_date')
            ->first();

        \Log::info("Schedule for employee {$employeeId}: " . json_encode($schedule));

        if (!$schedule) {
            \Log::info("No schedule found for employee {$employeeId}");
            return [
                'is_working' => true, // Assume working day if no schedule
                'reason' => 'No schedule found',
                'type' => 'no_schedule'
            ];
        }

        if (empty($schedule->days_of_week)) {
            \Log::info("No working days defined for employee {$employeeId}");
            return [
                'is_working' => false,
                'reason' => 'No working days defined',
                'type' => 'no_working_days'
            ];
        }

        $carbonDate = \Carbon\Carbon::parse($date);
        $dayName = $carbonDate->format('l'); // Monday, Tuesday, etc.
        $dayNumber = $carbonDate->dayOfWeekIso; // 1=Monday, 7=Sunday
        
        \Log::info("Date: {$date}, Day Name: {$dayName}, Day Number: {$dayNumber}");
        \Log::info("Working days string: {$schedule->days_of_week}");

        // Parse working days
        $workingDays = array_map('trim', explode(',', $schedule->days_of_week));
        \Log::info("Parsed working days: " . json_encode($workingDays));
        
        $isWorkingDay = false;
        $matchedDay = null;
        
        foreach ($workingDays as $day) {
            // Check numeric days (1-7)
            if (is_numeric($day) && intval($day) == $dayNumber) {
                $isWorkingDay = true;
                $matchedDay = $day;
                break;
            }
            
            // Check day names (full or short)
            $dayLower = strtolower($day);
            $dayNameLower = strtolower($dayName);
            $dayNameShort = strtolower(substr($dayName, 0, 3));
            
            if ($dayLower == $dayNameLower || $dayLower == $dayNameShort) {
                $isWorkingDay = true;
                $matchedDay = $day;
                break;
            }
        }

        \Log::info("Is working day: " . ($isWorkingDay ? 'Yes' : 'No') . ", Matched day: " . ($matchedDay ?? 'None'));
        
        return [
            'is_working' => $isWorkingDay,
            'reason' => $isWorkingDay ? 'Working day' : 'Non-working day',
            'type' => $isWorkingDay ? 'working_day' : 'non_working_day',
            'schedule_info' => [
                'shift_name' => $schedule->shift_name,
                'days_of_week' => $schedule->days_of_week,
                'start_date' => $schedule->schedule_start_date,
                'end_date' => $schedule->schedule_end_date,
                'matched_day' => $matchedDay
            ]
        ];
    }
        /**
     * Update leave status with multi-stage approval consensus logic
     */
   public function updateLeaveStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,declined',
        'stage'  => 'nullable|in:tl,hr,manager'
    ]);

    $newStatus  = $request->input('status');
    $stage      = $request->input('stage');
    $role       = session('role');

    // ── Support both employee login (user_id) and admin login (admin_id) ──
    $employeeId = session('user_id') ?? session('admin_id');

    $isTL      = false;
    $isManager = false;
    $isHR      = false;
    $isAdmin   = ($role === 'admin');

    Log::info("updateLeaveStatus | role:{$role} | employeeId:{$employeeId} | stage:{$stage} | newStatus:{$newStatus} | leaveId:{$id}");

    if ($employeeId && !$isAdmin) {
        $emp = DB::table('allemployees')->where('id', $employeeId)->first();

        if ($emp) {
            $hierarchy      = DB::table('hierarchies')->where('id', $emp->hierarchy_id)->first();
            $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';
            $isHR           = in_array($hierarchyLevel, ['hr', 'hr manager']);

            Log::info("Role detection | emp found | hierarchy_level:{$hierarchyLevel} | isHR:" . ($isHR?'true':'false'));
        } else {
            Log::warning("updateLeaveStatus | employee not found in allemployees for id:{$employeeId}");
        }

        $isTL      = DB::table('allemployees')->where('team_lead_id', $employeeId)->exists();
        $isManager = DB::table('allemployees')->where('manager_id',   $employeeId)->exists();

        // Fallback: detect via hierarchy level
        if (!$isManager && isset($hierarchyLevel) && in_array($hierarchyLevel, ['manager'])) {
            $isManager = true;
        }
        if (!$isTL && isset($hierarchyLevel) && in_array($hierarchyLevel, ['team lead', 'teamlead', 'tl'])) {
            $isTL = true;
        }

        Log::info("isTL:" . ($isTL?'true':'false') . " | isManager:" . ($isManager?'true':'false') . " | isHR:" . ($isHR?'true':'false'));
    }

    $updateFields = [];

    if ($isAdmin) {
        if ($stage === 'tl')          { $updateFields['tl_approved'] = $newStatus; $updateFields['tl_approved_by'] = $employeeId; }
        elseif ($stage === 'hr')      { $updateFields['hr_approved'] = $newStatus; $updateFields['hr_approved_by'] = $employeeId; }
        elseif ($stage === 'manager') { $updateFields['manager_approved'] = $newStatus; $updateFields['manager_approved_by'] = $employeeId; }
        else                          $updateFields['status'] = $newStatus;

    } elseif ($isHR) {
        $updateFields['hr_approved']    = $newStatus;
        $updateFields['hr_approved_by'] = $employeeId;

    } elseif ($isTL && $isManager) {
        if ($stage === 'manager') { $updateFields['manager_approved'] = $newStatus; $updateFields['manager_approved_by'] = $employeeId; }
        else                      { $updateFields['tl_approved']      = $newStatus; $updateFields['tl_approved_by']      = $employeeId; }

    } elseif ($isTL) {
        $updateFields['tl_approved']    = $newStatus;
        $updateFields['tl_approved_by'] = $employeeId;

    } elseif ($isManager) {
        $updateFields['manager_approved']    = $newStatus;
        $updateFields['manager_approved_by'] = $employeeId;

    } else {
        Log::warning("updateLeaveStatus | NOT AUTHORIZED | role:{$role} | employeeId:{$employeeId} | isHR:{$isHR} | isTL:{$isTL} | isManager:{$isManager}");
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }
        return redirect()->back()->with('error', 'You are not authorized to perform this action.');
    }

    Log::info("updateFields: " . json_encode($updateFields));

    // ── Capture previous status BEFORE update ──
    $previousStatus = DB::table('employee_leaves')->where('id', $id)->value('status');

    // ── Apply stage update ──
    DB::table('employee_leaves')->where('id', $id)->update($updateFields);

    // ── Re-fetch and recalculate consensus ──
    $leave         = DB::table('employee_leaves')->where('id', $id)->first();
    $overallStatus = 'pending';

    if ($leave) {
        $approvedCount = 0;
        $declinedCount = 0;

        foreach (['tl_approved', 'hr_approved', 'manager_approved'] as $col) {
            if ($leave->$col === 'approved') $approvedCount++;
            elseif ($leave->$col === 'declined') $declinedCount++;
        }

        if ($approvedCount >= 2)     $overallStatus = 'approved';
        elseif ($declinedCount >= 2) $overallStatus = 'declined';

        DB::table('employee_leaves')->where('id', $id)->update(['status' => $overallStatus]);

        Log::info("Leave {$id} | tl:{$leave->tl_approved} hr:{$leave->hr_approved} manager:{$leave->manager_approved} | approved:{$approvedCount} declined:{$declinedCount} | overall:{$overallStatus} | previous:{$previousStatus}");

        // ── Balance deduction / restoration on first consensus ──
        if ($overallStatus === 'approved' && $previousStatus !== 'approved') {
            $days       = (int) $leave->no_of_days;
            $leaveType  = $leave->leave_type;

            if (strtolower($leaveType) === 'lop') {
                // All days are LOP/unpaid
                $paidDays = 0;
                $lopDays  = $days;
            } else {
                $maxAllowed = (int)(DB::table('annual_leaves')->value('max_allowed') ?? 0);
                $paidDays   = $maxAllowed > 0 ? min($days, $maxAllowed) : $days;
                $lopDays    = $days - $paidDays;
            }

            if ($paidDays > 0) {
                DB::table('employee_leave_balances')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type', $leaveType)
                    ->update([
                        'used_days'      => DB::raw("used_days + {$paidDays}"),
                        'remaining_days' => DB::raw("GREATEST(0, remaining_days - {$paidDays})"),
                        'updated_at'     => now(),
                    ]);
            }

            if ($lopDays > 0) {
                $month = now()->format('Y-m');
                DB::table('employee_lop_records')->updateOrInsert(
                    ['employee_id' => $leave->employee_id, 'month' => $month],
                    ['lop_days' => DB::raw("lop_days + {$lopDays}"), 'updated_at' => now(), 'created_at' => now()]
                );
            }

            DB::table('employee_leaves')->where('id', $id)->update([
                'paid_days' => $paidDays,
                'lop_days'  => $lopDays,
            ]);

        } elseif ($overallStatus !== 'approved' && $previousStatus === 'approved') {
            // Restore only paid_days (not lop_days) back to balance
            $paidWas = (int)($leave->paid_days ?? $leave->no_of_days);
            if ($paidWas > 0) {
                DB::table('employee_leave_balances')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type', $leave->leave_type)
                    ->update([
                        'used_days'      => DB::raw('GREATEST(0, used_days - ' . $paidWas . ')'),
                        'remaining_days' => DB::raw('remaining_days + ' . $paidWas),
                        'updated_at'     => now(),
                    ]);
            }
            DB::table('employee_leaves')->where('id', $id)->update(['paid_days' => 0, 'lop_days' => 0]);
        }

        // ── Notify employee on first consensus ──
        if (in_array($overallStatus, ['approved', 'declined']) && $previousStatus === 'pending') {
            Log::info("Triggering leave decision mail for leave ID: {$id}");
            app(\App\Http\Controllers\Backend\Employee\EmployeeLeavesController::class)
                ->notifyEmployeeOnDecision('leave', $id, $overallStatus);
        }
    }

    if ($request->ajax()) {
        return response()->json([
            'success'        => true,
            'message'        => 'Leave status updated successfully',
            'overall_status' => $overallStatus,
        ]);
    }

    return redirect()->back()->with('success', 'Leave status updated successfully');
}
    /**
     * Update permission status with multi-stage approval consensus logic
     */
   public function updatePermissionStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,declined',
        'stage'  => 'nullable|in:tl,hr,manager'
    ]);

    $newStatus  = $request->input('status');
    $stage      = $request->input('stage');
    $role       = session('role');
    $employeeId = session('user_id') ?? session('admin_id');

    $isTL      = false;
    $isManager = false;
    $isHR      = false;
    $isAdmin   = ($role === 'admin');

    Log::info("updatePermissionStatus | role:{$role} | employeeId:{$employeeId} | stage:{$stage} | newStatus:{$newStatus} | permId:{$id}");

    if ($employeeId && !$isAdmin) {
        $emp = DB::table('allemployees')->where('id', $employeeId)->first();

        if ($emp) {
            $hierarchy      = DB::table('hierarchies')->where('id', $emp->hierarchy_id)->first();
            $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';
            $isHR           = in_array($hierarchyLevel, ['hr', 'hr manager']);

            Log::info("Role detection | emp found | hierarchy_level:{$hierarchyLevel} | isHR:" . ($isHR?'true':'false'));
        } else {
            Log::warning("updatePermissionStatus | employee not found for id:{$employeeId}");
        }

        $isTL      = DB::table('allemployees')->where('team_lead_id', $employeeId)->exists();
        $isManager = DB::table('allemployees')->where('manager_id',   $employeeId)->exists();

        // Fallback: detect via hierarchy level
        if (!$isManager && isset($hierarchyLevel) && in_array($hierarchyLevel, ['manager'])) {
            $isManager = true;
        }
        if (!$isTL && isset($hierarchyLevel) && in_array($hierarchyLevel, ['team lead', 'teamlead', 'tl'])) {
            $isTL = true;
        }

        Log::info("isTL:" . ($isTL?'true':'false') . " | isManager:" . ($isManager?'true':'false') . " | isHR:" . ($isHR?'true':'false'));
    }

    $updateFields = [];

    if ($isAdmin) {
        if ($stage === 'tl')          $updateFields['tl_approved']      = $newStatus;
        elseif ($stage === 'hr')      $updateFields['hr_approved']      = $newStatus;
        elseif ($stage === 'manager') $updateFields['manager_approved'] = $newStatus;
        else                          $updateFields['status']           = $newStatus;

    } elseif ($isHR) {
        $updateFields['hr_approved'] = $newStatus;

    } elseif ($isTL && $isManager) {
        if ($stage === 'manager')     $updateFields['manager_approved'] = $newStatus;
        else                          $updateFields['tl_approved']      = $newStatus;

    } elseif ($isTL) {
        $updateFields['tl_approved'] = $newStatus;

    } elseif ($isManager) {
        $updateFields['manager_approved'] = $newStatus;

    } else {
        Log::warning("updatePermissionStatus | NOT AUTHORIZED | role:{$role} | employeeId:{$employeeId}");
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }
        return redirect()->back()->with('error', 'You are not authorized to perform this action.');
    }

    Log::info("updateFields: " . json_encode($updateFields));

    $previousStatus = DB::table('employee_permissions')->where('id', $id)->value('status');

    DB::table('employee_permissions')->where('id', $id)->update($updateFields);

    $permission    = DB::table('employee_permissions')->where('id', $id)->first();
    $overallStatus = 'pending';

    if ($permission) {
        $approvedCount = 0;
        $declinedCount = 0;

        foreach (['tl_approved', 'hr_approved', 'manager_approved'] as $col) {
            if ($permission->$col === 'approved') $approvedCount++;
            elseif ($permission->$col === 'declined') $declinedCount++;
        }

        if ($approvedCount >= 2)     $overallStatus = 'approved';
        elseif ($declinedCount >= 2) $overallStatus = 'declined';

        DB::table('employee_permissions')->where('id', $id)->update(['status' => $overallStatus]);

        Log::info("Permission {$id} | tl:{$permission->tl_approved} hr:{$permission->hr_approved} manager:{$permission->manager_approved} | approved:{$approvedCount} declined:{$declinedCount} | overall:{$overallStatus} | previous:{$previousStatus}");

        if (in_array($overallStatus, ['approved', 'declined']) && $previousStatus === 'pending') {
            Log::info("Triggering permission decision mail for permission ID: {$id}");
            app(\App\Http\Controllers\Backend\Employee\EmployeeLeavesController::class)
                ->notifyEmployeeOnDecision('permission', $id, $overallStatus);
        }
    }

    if ($request->ajax()) {
        return response()->json([
            'success'        => true,
            'message'        => 'Permission status updated successfully',
            'overall_status' => $overallStatus,
        ]);
    }

    return redirect()->back()->with('success', 'Permission status updated successfully');
}
// Change the signature to accept status directly
public function notifyEmployeeOnDecision(string $type, int $recordId, string $forcedStatus = null): void
{
    try {
        if ($type === 'leave') {
            $record = DB::table('employee_leaves')->where('id', $recordId)->first();
            if (!$record) {
                Log::warning("notifyEmployeeOnDecision: leave ID {$recordId} not found");
                return;
            }

            // Use forcedStatus if passed, otherwise use record's status
            $finalStatus = $forcedStatus ?? $record->status;

            if (!in_array($finalStatus, ['approved', 'declined'])) {
                Log::warning("notifyEmployeeOnDecision: leave {$recordId} status is '{$finalStatus}', skipping mail");
                return;
            }

            $employee = DB::table('allemployees')->where('id', $record->employee_id)->first();
            if (!$employee) {
                Log::warning("notifyEmployeeOnDecision: employee not found for leave {$recordId}");
                return;
            }
            if (empty($employee->email)) {
                Log::warning("notifyEmployeeOnDecision: employee ID {$employee->id} has no email");
                return;
            }

            Mail::send('emails.leave-status', [
                'employeeName' => $employee->firstname . ' ' . $employee->lastname,
                'status'       => $finalStatus,
                'details'      => [
                    'leave_type' => $record->leave_type,
                    'from_date'  => $record->from_date,
                    'to_date'    => $record->to_date,
                    'no_of_days' => $record->no_of_days,
                ],
            ], function ($message) use ($employee, $finalStatus) {
                $message->to($employee->email, $employee->firstname . ' ' . $employee->lastname)
                    ->subject('Your Leave Request has been ' . ucfirst($finalStatus));
            });

            Log::info("Leave decision mail sent to {$employee->email} | status: {$finalStatus}");

        } elseif ($type === 'permission') {
            $record = DB::table('employee_permissions')->where('id', $recordId)->first();
            if (!$record) {
                Log::warning("notifyEmployeeOnDecision: permission ID {$recordId} not found");
                return;
            }

            $finalStatus = $forcedStatus ?? $record->status;

            if (!in_array($finalStatus, ['approved', 'declined'])) {
                Log::warning("notifyEmployeeOnDecision: permission {$recordId} status is '{$finalStatus}', skipping mail");
                return;
            }

            $employee = DB::table('allemployees')->where('id', $record->employee_id)->first();
            if (!$employee) {
                Log::warning("notifyEmployeeOnDecision: employee not found for permission {$recordId}");
                return;
            }
            if (empty($employee->email)) {
                Log::warning("notifyEmployeeOnDecision: employee ID {$employee->id} has no email");
                return;
            }

            Mail::send('emails.permission-status', [
                'employeeName' => $employee->firstname . ' ' . $employee->lastname,
                'status'       => $finalStatus,
                'details'      => [
                    'permission_date'   => $record->permission_date,
                    'start_time'        => $record->start_time,
                    'end_time'          => $record->end_time,
                    'duration'          => $record->duration,
                    'permission_reason' => $record->permission_reason,
                ],
            ], function ($message) use ($employee, $finalStatus) {
                $message->to($employee->email, $employee->firstname . ' ' . $employee->lastname)
                    ->subject('Your Permission Request has been ' . ucfirst($finalStatus));
            });

            Log::info("Permission decision mail sent to {$employee->email} | status: {$finalStatus}");
        }

    } catch (\Exception $e) {
        Log::error("notifyEmployeeOnDecision failed for {$type} ID {$recordId}: " . $e->getMessage());
        Log::error($e->getTraceAsString());
    }
}

private function getApproverName(?int $employeeId): ?string
{
    if (!$employeeId) return null;
    $emp = DB::table('allemployees')->where('id', $employeeId)->first();
    return $emp ? trim($emp->firstname . ' ' . $emp->lastname) : null;
}

private function getOverallStatus(?string $tlApproved, ?string $hrApproved, ?string $managerApproved): string
{
    $approvedCount = 0;
    $declinedCount = 0;

    foreach ([$tlApproved, $hrApproved, $managerApproved] as $status) {
        if ($status === 'approved') {
            $approvedCount++;
        } elseif ($status === 'declined') {
            $declinedCount++;
        }
    }

    if ($approvedCount >= 2) {
        return 'approved';
    }

    if ($declinedCount >= 2) {
        return 'declined';
    }

    return 'pending';
}
}