<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class EmployeeLeavesController extends Controller
{
    public function index()
    {
        $employeeId = session('user_id');
        $year = request('year', date('Y'));

        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->whereYear('from_date', $year)
            ->get();

        $employeePermissions = DB::table('employee_permissions')
            ->where('employee_id', $employeeId)
            ->get();

        $annualLeaves          = $leaves->filter(fn($l) => $l->leave_type == 'Casual Leave');
        $sickLeaves            = $leaves->filter(fn($l) => $l->leave_type == 'Sick');
        $hospitalisationLeaves = $leaves->filter(fn($l) => $l->leave_type == 'Hospitalisation');
        $maternityLeaves       = $leaves->filter(fn($l) => $l->leave_type == 'Maternity Leave');
        $paternityLeaves       = $leaves->filter(fn($l) => $l->leave_type == 'Paternity Leave');

        $medicalLeaves = $leaves->filter(fn($l) =>
            in_array($l->leave_type, ['Hospitalisation', 'Paternity Leave', 'Maternity Leave', 'Sick'])
        );

        $otherLeaves = $leaves->filter(fn($l) =>
            !in_array($l->leave_type, ['Casual Leave', 'Hospitalisation', 'Paternity Leave', 'Maternity Leave', 'Sick', 'LOP'])
        );

        $annualSettings  = DB::table('annual_leaves')->first();
        $medicalSettings = DB::table('medical_leaves')->first();

        $totalSickLeaves                = $medicalSettings->sick ?? 0;
        $takenSickLeaves                = $sickLeaves->sum('no_of_days');
        $remainingSickLeaves            = $totalSickLeaves - $takenSickLeaves;

        $totalHospitalisationLeaves     = $medicalSettings->hospitalisation ?? 0;
        $takenHospitalisationLeaves     = $hospitalisationLeaves->sum('no_of_days');
        $remainingHospitalisationLeaves = $totalHospitalisationLeaves - $takenHospitalisationLeaves;

        $totalMaternityLeaves           = $medicalSettings->maternity ?? 0;
        $takenMaternityLeaves           = $maternityLeaves->sum('no_of_days');
        $remainingMaternityLeaves       = $totalMaternityLeaves - $takenMaternityLeaves;

        $totalPaternityLeaves           = $medicalSettings->paternity ?? 0;
        $takenPaternityLeaves           = $paternityLeaves->sum('no_of_days');
        $remainingPaternityLeaves       = $totalPaternityLeaves - $takenPaternityLeaves;

        $recentLeaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $allocated = DB::table('employee_leave_information')
            ->where('employee_id', $employeeId)
            ->first();

        $balanceFromTable = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->get()
            ->keyBy('leave_type');

        // Allocation source — employee_leave_information is authoritative
        $empLeaveInfo = DB::table('employee_leave_information')
            ->where('employee_id', $employeeId)
            ->first();

        $defaultAllocation = [
            'Casual Leave'    => (int)($empLeaveInfo->casual_leaves          ?? $annualSettings->days              ?? 0),
            'Sick'            => (int)($empLeaveInfo->sick_leaves             ?? $medicalSettings->sick             ?? 0),
            'Hospitalisation' => (int)($empLeaveInfo->hospitalization_leaves ?? $medicalSettings->hospitalisation  ?? 0),
            'Maternity Leave' => (int)($empLeaveInfo->maternity_leaves       ?? $medicalSettings->maternity        ?? 0),
            'Paternity Leave' => (int)($empLeaveInfo->paternity_leaves       ?? $medicalSettings->paternity        ?? 0),
        ];

        // Auto-create missing balance rows so allocated always shows correctly
        foreach ($defaultAllocation as $type => $allocDays) {
            if (!isset($balanceFromTable[$type]) && $allocDays > 0) {
                DB::table('employee_leave_balances')->insert([
                    'employee_id'    => $employeeId,
                    'leave_type'     => $type,
                    'allocated_days' => $allocDays,
                    'used_days'      => 0,
                    'remaining_days' => $allocDays,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } elseif (isset($balanceFromTable[$type]) && $balanceFromTable[$type]->allocated_days != $allocDays && $allocDays > 0) {
                // Sync allocated_days if admin changed the allocation
                DB::table('employee_leave_balances')
                    ->where('employee_id', $employeeId)
                    ->where('leave_type', $type)
                    ->update(['allocated_days' => $allocDays, 'updated_at' => now()]);
            }
        }

        // Re-fetch after auto-create
        $balanceFromTable = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->get()
            ->keyBy('leave_type');

        // Read paid_days and lop_days directly from employee_leaves (source of truth set at approval time)
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
            $bal       = $balanceFromTable[$dbKey] ?? $balanceFromTable[$display] ?? null;
            $allocated = $bal ? (int)$bal->allocated_days : ($defaultAllocation[$dbKey] ?? $defaultAllocation[$display] ?? 0);
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

        $holidays          = DB::table('holidays')->orderBy('holidaydate', 'asc')->get();
        $upcoming_leaves   = DB::table('employee_leaves')->where('employee_id', $employeeId)->where('from_date', '>=', today())->orderBy('from_date', 'asc')->get();
        $upcoming_holidays = DB::table('holidays')->where('holidaydate', '>=', today())->orderBy('holidaydate', 'asc')->get();
        $past_leaves       = DB::table('employee_leaves')->where('employee_id', $employeeId)->where('from_date', '<', today())->orderBy('from_date', 'desc')->get();
        $past_holidays     = DB::table('holidays')->where('holidaydate', '<', today())->orderBy('holidaydate', 'desc')->get();

        $employeeShifts = DB::table('schedule as ss')
            ->join('shifts as s', 's.id', '=', 'ss.shift_id')
            ->where('ss.employee_id', $employeeId)
            ->select('ss.*', 's.shift_name', 's.start_time', 's.end_time', 's.break_time', 's.days_of_week')
            ->get();

        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $approvedLeaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->get();

        $leaveDays = [];
        foreach ($approvedLeaves as $leave) {
            $from = \Carbon\Carbon::parse($leave->from_date);
            $to   = \Carbon\Carbon::parse($leave->to_date);
            // Only mark working days as leave (skip employee's week-off days)
            $workingDaysList = [];
            foreach ($employeeShifts as $shift) {
                if (!empty($shift->days_of_week)) {
                    $workingDaysList = array_map('trim', explode(',', $shift->days_of_week));
                    break;
                }
            }
            if (empty($workingDaysList)) {
                $workingDaysList = ['Mon','Tue','Wed','Thu','Fri'];
            }
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                if (in_array($d->format('D'), $workingDaysList)) {
                    $leaveDays[$d->format('Y-m-d')] = $leave->leave_type;
                }
            }
        }

        $calendarShifts = [];
        foreach ($employeeShifts as $shift) {
            $allowedDays   = array_map('trim', explode(',', $shift->days_of_week));
            $scheduleStart = \Carbon\Carbon::parse($shift->schedule_start_date);
            $scheduleEnd   = $shift->repeat_every_week == 1
                ? now()->addYears(1)
                : \Carbon\Carbon::parse($shift->schedule_end_date);

            for ($date = $weekStart->copy(); $date->lte($weekEnd); $date->addDay()) {
                $dayShort = $date->format('D');
                if ($date->lt($scheduleStart) || $date->gt($scheduleEnd)) continue;
                if (!in_array($dayShort, $allowedDays)) continue;
                $calendarShifts[] = [
                    'date'       => $date->format('Y-m-d'),
                    'shift_name' => $shift->shift_name,
                    'start_time' => $shift->start_time,
                    'end_time'   => $shift->end_time,
                ];
            }
        }

        $holidaysList = DB::table('holidays')->get();
        $holidayDays  = [];
        foreach ($holidaysList as $h) {
            $holidayDays[\Carbon\Carbon::parse($h->holidaydate)->format('Y-m-d')] = $h->title;
        }

        $leavesQuery = DB::table('employee_leaves')->where('employee_id', $employeeId);

        if (request('period') == 'this_year')  $leavesQuery->whereYear('from_date', date('Y'));
        if (request('period') == 'last_year')  $leavesQuery->whereYear('from_date', date('Y') - 1);
        if (request('period') == 'custom') {
            if (request('from_date')) $leavesQuery->where('from_date', '>=', request('from_date'));
            if (request('to_date'))   $leavesQuery->where('to_date',   '<=', request('to_date'));
        }
        if (request('paid_type') == 'paid')   $leavesQuery->where('type', 'Paid');
        if (request('paid_type') == 'unpaid') $leavesQuery->where('type', 'Unpaid');
        if (request('leave_type') && request('leave_type') != 'all') {
            $leavesQuery->where('leave_type', request('leave_type'));
        }

        $leaves = $leavesQuery->get();

        return view('hrms.Employee.EmployeeLeaves.index', [
            'leaveBalance'                   => $leaveBalance,
            'leaves'                         => $leaves,
            'employeePermissions'            => $employeePermissions,
            'annualLeaves'                   => $annualLeaves,
            'medicalLeaves'                  => $medicalLeaves,
            'otherLeaves'                    => $otherLeaves,
            'sickLeaves'                     => $sickLeaves,
            'hospitalisationLeaves'          => $hospitalisationLeaves,
            'maternityLeaves'                => $maternityLeaves,
            'paternityLeaves'                => $paternityLeaves,
            'totalSickLeaves'                => $totalSickLeaves,
            'totalHospitalisationLeaves'     => $totalHospitalisationLeaves,
            'totalMaternityLeaves'           => $totalMaternityLeaves,
            'totalPaternityLeaves'           => $totalPaternityLeaves,
            'remainingSickLeaves'            => $remainingSickLeaves,
            'remainingHospitalisationLeaves' => $remainingHospitalisationLeaves,
            'remainingMaternityLeaves'       => $remainingMaternityLeaves,
            'remainingPaternityLeaves'       => $remainingPaternityLeaves,
            'holidays'                       => $holidays,
            'upcoming_leaves'                => $upcoming_leaves,
            'upcoming_holidays'              => $upcoming_holidays,
            'past_leaves'                    => $past_leaves,
            'past_holidays'                  => $past_holidays,
            'calendarShifts'                 => $calendarShifts,
            'weekStart'                      => $weekStart,
            'weekEnd'                        => $weekEnd,
            'leaveDays'                      => $leaveDays,
            'holidayDays'                    => $holidayDays,
            'allocated'                      => $allocated,
            'recentLeaves'                   => $recentLeaves,
        ]);
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    public function create(Request $request)
    {
        $employeeId = session('employee_id') ?? $request->employee_id;

        $employee = DB::table('allemployees')
            ->select('firstname', 'lastname', 'employeeid', 'id')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        $leaveBalances = $this->getLeaveBalances($employee->id);

        // Get employee's current shift information
        $employeeShift = DB::table('schedule as s')
            ->join('shifts as sh', 'sh.id', '=', 's.shift_id')
            ->where('s.employee_id', $employee->id)
            ->where('s.schedule_start_date', '<=', date('Y-m-d'))
            ->where(function($query) {
                $query->where('s.schedule_end_date', '>=', date('Y-m-d'))
                      ->orWhere('s.repeat_every_week', 1);
            })
            ->select('sh.shift_name', 'sh.start_time', 'sh.end_time', 'sh.days_of_week', 's.schedule_start_date', 's.schedule_end_date', 's.repeat_every_week')
            ->first();

        // Get max_allowed from annual_leaves settings
        $maxAllowed = (int)(DB::table('annual_leaves')->value('max_allowed') ?? 0);

        // Get employee's week-off days from shift schedule
        $weekOffDays = ['Sat', 'Sun']; // default
        if ($employeeShift && !empty($employeeShift->days_of_week)) {
            $workingDays = array_map('trim', explode(',', $employeeShift->days_of_week));
            $allDays     = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $weekOffDays = array_values(array_diff($allDays, $workingDays));
        }

        return view('hrms.Employee.EmployeeLeaves.create', [
            'employee'        => $employee,
            'leaveBalances'   => $leaveBalances,
            'remainingLeaves' => $leaveBalances['Casual Leave'] ?? 0,
            'employeeShift'   => $employeeShift,
            'maxAllowed'      => $maxAllowed,
            'weekOffDays'     => $weekOffDays,
        ]);
    }

    // =========================================================================
    // STORE LEAVE
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'         => 'required|exists:allemployees,employeeid',
            'leave_type'          => 'required|in:Medical Leave,Hospitalisation,Maternity Leave,Casual Leave,LOP,Paternity Leave,Sick',
            'from_date'           => 'required|date|after_or_equal:today',
            'to_date'             => 'required|date|after_or_equal:from_date',
            'num_days'            => 'required|integer|min:1',
            'leave_reason'        => 'required|string|max:255',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id', 'team_lead_id', 'manager_id']);

        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        // ── Overlap check ──
        $overlap = DB::table('employee_leaves')
            ->where('employee_id', $employee->id)
            ->whereNotIn('status', ['declined'])
            ->where('deleted_at', 0)
            ->where('from_date', '<=', $request->to_date)
            ->where('to_date',   '>=', $request->from_date)
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'You already have a leave request overlapping these dates.');
        }

        // Recalculate working days server-side using employee's actual shift (ignores their week-off days)
        $numDays    = $this->countWorkingDays($employee->id, $request->from_date, $request->to_date);
        $maxAllowed = (int)(DB::table('annual_leaves')->value('max_allowed') ?? 0);

        if (strtolower($request->leave_type) === 'lop') {
            $paidDays = 0;
            $lopDays  = $numDays;
        } else {
            $paidDays = ($maxAllowed > 0) ? min($numDays, $maxAllowed) : $numDays;
            $lopDays  = $numDays - $paidDays;
        }

        $remainingLeaves = $this->calculateRemainingLeaves(
            $employee->id,
            $request->leave_type,
            $paidDays
        );

        if ($remainingLeaves < 0) {
            return back()->withInput()->with('error', 'Insufficient leave balance for this type.');
        }

        $medicalCertificatePath = null;
        if ($request->hasFile('medical_certificate')) {
            $file = $request->file('medical_certificate');
            $medicalCertificatePath = $file->storeAs(
                'medical_certificates',
                time() . '_' . $file->getClientOriginalName(),
                'public'
            );
        }

        DB::table('employee_leaves')->insert([
            'employee_id'         => $employee->id,
            'leave_type'          => $request->leave_type,
            'from_date'           => $request->from_date,
            'to_date'             => $request->to_date,
            'no_of_days'          => $numDays,
            'paid_days'           => $paidDays,
            'lop_days'            => $lopDays,
            'remaining_leaves'    => $remainingLeaves,
            'leave_reason'        => $request->leave_reason,
            'medical_certificate' => $medicalCertificatePath,
            'status'              => 'pending',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        $this->notifyApproversOnApplication('leave', $employee->id, [
            'leave_type'   => $request->leave_type,
            'from_date'    => $request->from_date,
            'to_date'      => $request->to_date,
            'num_days'     => $request->num_days,
            'leave_reason' => $request->leave_reason,
        ]);

        return redirect()->route('employee-leaves.index')
            ->with('success', 'Leave request submitted successfully!');
    }

    // =========================================================================
    // STORE PERMISSION
    // =========================================================================

    public function storePermission(Request $request)
    {
        $request->validate([
            'employee_id'         => 'required|exists:allemployees,employeeid',
            'permission_date'     => 'required|date',
            'start_time'          => 'required',
            'end_time'            => 'required',
            'duration'            => 'required|numeric',
            'permission_reason'   => 'required|string|max:255',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        // ── Fetch all needed fields so buildApproverRecipients() has TL and Manager ──
        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id', 'team_lead_id', 'manager_id']);

        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        $supportingDocumentPath = null;
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $supportingDocumentPath = $file->storeAs(
                'supporting_documents',
                time() . '_permission_' . $file->getClientOriginalName(),
                'public'
            );
        }

        DB::table('employee_permissions')->insert([
            'employee_id'         => $employee->id,
            'permission_date'     => $request->permission_date,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'duration'            => $request->duration,
            'permission_reason'   => $request->permission_reason,
            'supporting_document' => $supportingDocumentPath,
            'status'              => 'pending',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // ── Notify TL, HR, and Manager ──
        $this->notifyApproversOnApplication('permission', $employee->id, [
            'permission_date'   => $request->permission_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'duration'          => $request->duration,
            'permission_reason' => $request->permission_reason,
        ]);

        return redirect()->route('employee-leaves.index')
            ->with('success', 'Permission request submitted successfully!');
    }

    // =========================================================================
    // NOTIFICATION — APPROVERS (on application submission)
    // =========================================================================

    /**
     * Notify TL + HR + Manager when employee submits a leave or permission.
     */
    private function notifyApproversOnApplication(string $type, int $employeeId, array $details): void
    {
        try {
            $employee = DB::table('allemployees')->where('id', $employeeId)->first();
            if (!$employee) {
                Log::warning("notifyApproversOnApplication: employee ID {$employeeId} not found");
                return;
            }

            $employeeName  = $employee->firstname . ' ' . $employee->lastname;
            $employeeEmpId = $employee->employeeid;
            $recipients    = $this->buildApproverRecipients($employee);

            if (empty($recipients)) {
                Log::warning("notifyApproversOnApplication: no recipients found for employee ID {$employeeId}");
                return;
            }

            $view    = $type === 'leave' ? 'emails.leave-applied' : 'emails.permission-applied';
            $subject = $type === 'leave'
                ? "Leave Request from {$employeeName} (ID: {$employeeEmpId})"
                : "Permission Request from {$employeeName} (ID: {$employeeEmpId})";

            foreach ($recipients as $recipient) {
                Mail::send($view, [
                    'recipientName' => $recipient['name'],
                    'employeeName'  => $employeeName,
                    'employeeId'    => $employeeEmpId,
                    'details'       => $details,
                ], function ($message) use ($recipient, $subject) {
                    $message->to($recipient['email'], $recipient['name'])->subject($subject);
                });

                Log::info("Application notification sent to {$recipient['role']}: {$recipient['email']}");
            }

        } catch (\Exception $e) {
            Log::error("notifyApproversOnApplication failed for {$type}: " . $e->getMessage());
        }
    }

    // =========================================================================
    // NOTIFICATION — EMPLOYEE (on consensus decision)
    // =========================================================================

    /**
     * Notify the employee when their leave/permission is finally approved or declined.
     * Called from TeamLeaveController after consensus is reached.
     * $forcedStatus is passed directly to avoid DB read timing issues.
     */
    public function notifyEmployeeOnDecision(string $type, int $recordId, string $forcedStatus = null): void
    {
        try {
            if ($type === 'leave') {

                $record = DB::table('employee_leaves')->where('id', $recordId)->first();
                if (!$record) {
                    Log::warning("notifyEmployeeOnDecision: leave ID {$recordId} not found");
                    return;
                }

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

    // =========================================================================
    // BUILD APPROVER RECIPIENTS
    // =========================================================================

    /**
     * Build TL + Manager + HR recipient list for a given employee object.
     * Employee object must include team_lead_id and manager_id fields.
     */
    private function buildApproverRecipients(object $employee): array
    {
        $recipients = [];

        // ── Team Lead ──
        if (!empty($employee->team_lead_id)) {
            $tl = DB::table('allemployees')->where('id', $employee->team_lead_id)->first();
            if ($tl && !empty($tl->email)) {
                $recipients[] = [
                    'email' => $tl->email,
                    'name'  => $tl->firstname . ' ' . $tl->lastname,
                    'role'  => 'Team Lead',
                ];
            }
        } else {
            Log::warning("buildApproverRecipients: no team_lead_id for employee ID {$employee->id}");
        }

        // ── Manager ──
        if (!empty($employee->manager_id)) {
            $manager = DB::table('allemployees')->where('id', $employee->manager_id)->first();
            if ($manager && !empty($manager->email)) {
                $alreadyAdded = collect($recipients)->pluck('email')->contains($manager->email);
                if (!$alreadyAdded) {
                    $recipients[] = [
                        'email' => $manager->email,
                        'name'  => $manager->firstname . ' ' . $manager->lastname,
                        'role'  => 'Manager',
                    ];
                }
            }
        } else {
            Log::warning("buildApproverRecipients: no manager_id for employee ID {$employee->id}");
        }

        // ── HR — all active employees with HR hierarchy level ──
        $hrEmployees = DB::table('allemployees as a')
            ->join('hierarchies as h', 'a.hierarchy_id', '=', 'h.id')
            ->whereIn(DB::raw('LOWER(TRIM(h.hierarchy_level))'), ['hr', 'hr manager'])
            ->where('a.status', 'active')
            ->select('a.email', 'a.firstname', 'a.lastname')
            ->get();

        foreach ($hrEmployees as $hr) {
            if (empty($hr->email)) continue;
            $alreadyAdded = collect($recipients)->pluck('email')->contains($hr->email);
            if (!$alreadyAdded) {
                $recipients[] = [
                    'email' => $hr->email,
                    'name'  => $hr->firstname . ' ' . $hr->lastname,
                    'role'  => 'HR',
                ];
            }
        }

        Log::info("buildApproverRecipients for employee ID {$employee->id}: " . json_encode(
            collect($recipients)->map(fn($r) => ['role' => $r['role'], 'email' => $r['email']])->toArray()
        ));

        return $recipients;
    }

    // =========================================================================
    // LEAVE BALANCES HELPER
    // =========================================================================

    private function getLeaveBalances($employeeId): array
    {
        $annualSettings  = DB::table('annual_leaves')->first();
        $medicalSettings = DB::table('medical_leaves')->first();
        $lopSettings     = DB::table('lop_leaves')->first();

        $existingBalances = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->pluck('remaining_days', 'leave_type')
            ->toArray();

        return [
            'Casual Leave'    => $existingBalances['Casual Leave']    ?? ($annualSettings->days ?? 0),
            'Sick'            => $existingBalances['Sick']            ?? ($medicalSettings->sick ?? 0),
            'Hospitalisation' => $existingBalances['Hospitalisation'] ?? ($medicalSettings->hospitalisation ?? 0),
            'Maternity Leave' => $existingBalances['Maternity Leave'] ?? ($medicalSettings->maternity ?? 0),
            'Paternity Leave' => $existingBalances['Paternity Leave'] ?? ($medicalSettings->paternity ?? 0),
            'LOP'             => $existingBalances['LOP']             ?? (
                ($lopSettings && $lopSettings->earned_leaves == 'yes')
                    ? ($lopSettings->days ?? 0)
                    : 'Unlimited'
            ),
        ];
    }

    // =========================================================================
    // REMAINING LEAVES CALCULATOR
    // =========================================================================

    private function calculateRemainingLeaves($employeeId, $leaveType, $requestedDays)
    {
        $balance = DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->first();

        if (!$balance) {
            $allocated = $this->getDefaultAllocation($leaveType);
            DB::table('employee_leave_balances')->insert([
                'employee_id'    => $employeeId,
                'leave_type'     => $leaveType,
                'allocated_days' => $allocated,
                'used_days'      => 0,
                'remaining_days' => $allocated,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            return $allocated - $requestedDays;
        }

        return $balance->remaining_days - $requestedDays;
    }

    private function getDefaultAllocation($leaveType)
    {
        switch ($leaveType) {
            case 'Casual Leave':    return DB::table('annual_leaves')->value('days') ?? 0;
            case 'Sick':            return DB::table('medical_leaves')->value('sick') ?? 0;
            case 'Hospitalisation': return DB::table('medical_leaves')->value('hospitalisation') ?? 0;
            case 'Maternity Leave': return DB::table('medical_leaves')->value('maternity') ?? 0;
            case 'Paternity Leave': return DB::table('medical_leaves')->value('paternity') ?? 0;
            default:                return 0;
        }
    }

    /**
     * Count only working days (per employee's shift schedule) between two dates inclusive.
     * Falls back to Mon-Fri if no schedule is found.
     */
    private function countWorkingDays(int $employeeId, string $from, string $to): int
    {
        $schedule = DB::table('schedule as s')
            ->join('shifts as sh', 'sh.id', '=', 's.shift_id')
            ->where('s.employee_id', $employeeId)
            ->where('s.schedule_start_date', '<=', $from)
            ->where(function ($q) use ($to) {
                $q->where('s.repeat_every_week', '>', 0)
                  ->orWhere(function ($q2) use ($to) {
                      $q2->where('s.repeat_every_week', 0)
                         ->where('s.schedule_end_date', '>=', $to);
                  });
            })
            ->select('sh.days_of_week')
            ->first();

        $workingDays = ($schedule && !empty($schedule->days_of_week))
            ? array_map('trim', explode(',', $schedule->days_of_week))
            : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

        $count   = 0;
        $current = \Carbon\Carbon::parse($from);
        $end     = \Carbon\Carbon::parse($to);

        while ($current->lte($end)) {
            if (in_array($current->format('D'), $workingDays)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    // =========================================================================
    // EDIT / UPDATE / DESTROY — LEAVE
    // =========================================================================

    public function show($id) {}

    public function edit($id)
    {
        $leave = DB::table('employee_leaves')->where('id', $id)->first();
        if (!$leave) {
            return redirect()->route('employee-leaves.index')->with('error', 'Leave not found!');
        }

        $employee = DB::table('allemployees')->where('id', $leave->employee_id)->first();
        if (!$employee) {
            return redirect()->route('employee-leaves.index')->with('error', 'Employee not found!');
        }

        $leaveBalance    = DB::table('employee_leave_balances')
            ->where('employee_id', $leave->employee_id)
            ->where('leave_type', $leave->leave_type)
            ->first();
        $remainingLeaves = $leaveBalance->remaining_days ?? 0;

        return view('hrms.Employee.EmployeeLeaves.edit', [
            'leave'           => $leave,
            'employee'        => $employee,
            'employeeid'      => $employee->employeeid,
            'remainingLeaves' => $remainingLeaves,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id'                => 'required|exists:allemployees,employeeid',
            'leave_type'                 => 'required|in:Medical Leave,Hospitalisation,Maternity Leave,Casual Leave,LOP,Paternity Leave,Sick',
            'from_date'                  => 'required|date',
            'to_date'                    => 'required|date|after_or_equal:from_date',
            'num_days'                   => 'required|integer|min:1',
            'leave_reason'               => 'required|string|max:255',
            'medical_certificate'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'remove_medical_certificate' => 'nullable|boolean',
        ]);

        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id']);

        if (!$employee) return back()->with('error', 'Employee not found.');

        $leave = DB::table('employee_leaves')->where('id', $id)->first();
        if (!$leave) return back()->with('error', 'Leave record not found.');

        // Block editing approved/declined leaves
        if (in_array($leave->status, ['approved', 'declined'])) {
            return back()->with('error', 'Cannot edit a leave that has already been ' . $leave->status . '.');
        }

        // Overlap check (exclude self)
        $overlap = DB::table('employee_leaves')
            ->where('employee_id', $employee->id)
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['declined'])
            ->where('deleted_at', 0)
            ->where('from_date', '<=', $request->to_date)
            ->where('to_date',   '>=', $request->from_date)
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'These dates overlap with another leave request.');
        }

        $medicalCertificatePath = $leave->medical_certificate;

        if ($request->boolean('remove_medical_certificate')) {
            if ($medicalCertificatePath && Storage::disk('public')->exists($medicalCertificatePath)) {
                Storage::disk('public')->delete($medicalCertificatePath);
            }
            $medicalCertificatePath = null;
        }

        if ($request->hasFile('medical_certificate')) {
            if ($medicalCertificatePath && Storage::disk('public')->exists($medicalCertificatePath)) {
                Storage::disk('public')->delete($medicalCertificatePath);
            }
            $file = $request->file('medical_certificate');
            $medicalCertificatePath = $file->storeAs(
                'medical_certificates',
                time() . '_' . $file->getClientOriginalName(),
                'public'
            );
        }

        $affected = DB::table('employee_leaves')->where('id', $id)->update([
            'employee_id'         => $employee->id,
            'leave_type'          => $request->leave_type,
            'from_date'           => $request->from_date,
            'to_date'             => $request->to_date,
            'no_of_days'          => $request->num_days,
            'leave_reason'        => $request->leave_reason,
            'medical_certificate' => $medicalCertificatePath,
            'updated_at'          => now(),
        ]);

        return $affected
            ? redirect()->route('employee-leaves.index')->with('success', 'Leave updated successfully!')
            : redirect()->route('employee-leaves.index')->with('error', 'Failed to update leave.');
    }

    public function destroy($id)
    {
        $leave = DB::table('employee_leaves')->where('id', $id)->first();
        if ($leave && $leave->status === 'approved') {
            // Restore balance on delete of approved leave
            DB::table('employee_leave_balances')
                ->where('employee_id', $leave->employee_id)
                ->where('leave_type', $leave->leave_type)
                ->update([
                    'used_days'      => DB::raw('GREATEST(0, used_days - ' . (int)$leave->no_of_days . ')'),
                    'remaining_days' => DB::raw('remaining_days + ' . (int)$leave->no_of_days),
                    'updated_at'     => now(),
                ]);
        }
        DB::table('employee_leaves')->where('id', $id)->delete();
        return redirect()->route('employee-leaves.index')->with('success', 'Leave deleted successfully.');
    }

    // =========================================================================
    // EDIT / UPDATE / DESTROY — PERMISSION
    // =========================================================================

    public function createPermission(Request $request)
    {
        $employeeId = session('employee_id') ?? $request->employee_id;
        $employee   = DB::table('allemployees')
            ->select('firstname', 'lastname', 'employeeid', 'id')
            ->where('employeeid', $employeeId)
            ->first();

        if (!$employee) return redirect()->back()->with('error', 'Employee not found');

        return view('hrms.Employee.EmployeeLeaves.create', [
            'employee'        => $employee,
            'leaveBalances'   => $this->getLeaveBalances($employee->id),
            'remainingLeaves' => $this->getLeaveBalances($employee->id)['Casual Leave'] ?? 0,
        ]);
    }

    public function editPermission($id)
    {
        $permission = DB::table('employee_permissions')->where('id', $id)->first();
        if (!$permission) {
            return redirect()->route('employee-permissions.index')->with('error', 'Permission not found!');
        }

        $employee = DB::table('allemployees')->where('id', $permission->employee_id)->first();
        if (!$employee) {
            return redirect()->route('employee-permissions.index')->with('error', 'Employee not found!');
        }

        return view('hrms.Employee.EmployeeLeaves.permissionedit', [
            'permission' => $permission,
            'employee'   => $employee,
            'employeeid' => $employee->employeeid,
        ]);
    }

    public function updatePermission(Request $request, $id)
    {
        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id']);

        if (!$employee) return back()->with('error', 'Employee not found.');

        $permission = DB::table('employee_permissions')->where('id', $id)->first();
        if (!$permission) return back()->with('error', 'Permission record not found.');

        $supportingDocumentPath = $permission->supporting_document;

        if ($request->boolean('remove_supporting_document')) {
            if ($supportingDocumentPath && Storage::disk('public')->exists($supportingDocumentPath)) {
                Storage::disk('public')->delete($supportingDocumentPath);
            }
            $supportingDocumentPath = null;
        }

        if ($request->hasFile('supporting_document')) {
            if ($supportingDocumentPath && Storage::disk('public')->exists($supportingDocumentPath)) {
                Storage::disk('public')->delete($supportingDocumentPath);
            }
            $file = $request->file('supporting_document');
            $supportingDocumentPath = $file->storeAs(
                'supporting_documents',
                time() . '_permission_' . $file->getClientOriginalName(),
                'public'
            );
        }

        $affected = DB::table('employee_permissions')->where('id', $id)->update([
            'employee_id'         => $employee->id,
            'permission_date'     => $request->permission_date,
            'start_time'          => $request->start_time,
            'end_time'            => $request->end_time,
            'duration'            => $request->duration,
            'permission_reason'   => $request->permission_reason,
            'supporting_document' => $supportingDocumentPath,
            'updated_at'          => now(),
        ]);

        return $affected
            ? redirect()->route('employee-leaves.index')->with('success', 'Permission updated successfully!')
            : redirect()->route('employee-leaves.index')->with('error', 'Failed to update permission.');
    }

    public function destroyPermission($id)
    {
        $permission = DB::table('employee_permissions')->where('id', $id)->first();
        if (!$permission) {
            return redirect()->route('employee-leaves.index')->with('error', 'Permission not found.');
        }

        if ($permission->supporting_document && Storage::disk('public')->exists($permission->supporting_document)) {
            Storage::disk('public')->delete($permission->supporting_document);
        }

        DB::table('employee_permissions')->where('id', $id)->delete();
        return redirect()->route('employee-leaves.index')->with('success', 'Permission deleted successfully.');
    }

    // =========================================================================
    // UTILITY
    // =========================================================================

    public function getEmployeeByEmployeeId($employee_id)
    {
        $employee = DB::table('allemployees')->where('employeeid', $employee_id)->first();
        return $employee
            ? response()->json([
                'id'        => $employee->id,
                'firstname' => $employee->firstname,
                'lastname'  => $employee->lastname,
            ])
            : response()->json(['message' => 'Employee not found'], 404);
    }
}