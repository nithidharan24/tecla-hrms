<?php

namespace App\Http\Controllers\Backend\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\ScheduleNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Console\Scheduling\Schedule;

class SchedulingController extends Controller
{
    public function index()
{
    // Clean up expired schedules and update current status first
    $this->cleanupExpiredSchedules();
    $this->updateCurrentScheduleStatus();
    $this->autoPublishSchedules();
    
    $today = Carbon::today();
    
    $departmentFilter = getEmployeeDepartmentFilter();
    $branchFilter = getAdminBranchFilter();
    $managerFilter = getManagerTeamFilter();

    $query = DB::table('schedule')
    ->leftJoin('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
    ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
    ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
    ->select(
        'schedule.*',
        'allemployees.firstname',
        'allemployees.lastname',
        'allemployees.designation',
        'allemployees.email',
        'allemployees.profile_image',
        'shifts.start_time',
        'shifts.end_time',
        'shifts.shift_name',
        'shifts.break_time',
        'shifts.days_of_week',
        'schedule.repeat_every_week',
        'schedule.schedule_start_date',
        'schedule.schedule_end_date',
        'schedule.needs_update_resolved_at',
        'schedule.needs_update_acknowledged',
        'department.department'
    )
    ->where('schedule.deleted_at', 0)
    ->where(function($query) use ($today) {
        // Include schedules that are either:
        // 1. Not ended yet (normal schedules)
        // 2. OR are repeating weekly (ignore end date for these)
        $query->where('schedule.schedule_end_date', '>=', $today)
              ->orWhere('schedule.repeat_every_week', '>', 0);
    })
    ->where('allemployees.deleted_at', 0)
    ->where('allemployees.status', 'active');

    // ✅ Apply branch/department filters BEFORE get()
    if ($branchFilter) {
        $query->where('allemployees.branch_id', $branchFilter);
    }
    
    if ($departmentFilter) {
        $query->where('allemployees.department', $departmentFilter);
    }
    
    // Manager filter — only show their team
    if ($managerFilter) {
        $query->where('allemployees.manager_id', $managerFilter);
    }
    
    // Now execute
    $schedules = $query->orderBy('schedule.schedule_end_date', 'asc')->get();
    
    $daysOfWeek = [];
    for ($i = 0; $i < 7; $i++) {
        $day = $today->copy()->addDays($i);
        $daysOfWeek[] = [
            'display' => $day->format('D') . $day->format('d'),
            'date' => $day->format('Y-m-d'),
            'day_name' => $day->format('D'),
            'carbon' => $day
        ];
    }

    $schedules->map(function ($schedule) use ($today) {
        if (!empty($schedule->start_time)) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $duration = $end->diff($start);
            $totalMinutes = ($duration->h * 60) + $duration->i - $schedule->break_time;
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $schedule->duration = $hours . ' hrs ' . $minutes . ' mins';
        } else {
            $schedule->duration = 'No Shift Assigned';
        }
        
        $schedule->week_format = $schedule->repeat_every_week . ' week' . ($schedule->repeat_every_week > 1 ? 's' : '');
        
        if ($schedule->schedule_start_date && $schedule->schedule_end_date) {
            $startDate = Carbon::parse($schedule->schedule_start_date);
            $endDate = Carbon::parse($schedule->schedule_end_date);
            
            $scheduleDays = $startDate->diffInDays($endDate) + 1;
            $schedule->schedule_duration = $scheduleDays . ' day' . ($scheduleDays > 1 ? 's' : '');
            
            $schedule->formatted_start_date = $startDate->format('M d, Y');
            $schedule->formatted_end_date = $endDate->format('M d, Y');
            
            // For weekly repeating schedules, they never end soon
            if ($schedule->repeat_every_week) {
                $schedule->ending_soon = false;
                $schedule->days_remaining = null;
            } else {
                $oneWeekBefore = $today->copy()->addWeek();
                $schedule->ending_soon = $endDate->lte($oneWeekBefore) && $endDate->gte($today) && $startDate->lte($today);
                $schedule->days_remaining = $today->diffInDays($endDate, false);
            }
            
            $isUpcoming = $startDate->gt($today);
            
            if ($isUpcoming) {
                $daysUntilStart = $today->diffInDays($startDate, false);
                $shouldNeedUpdate = ($daysUntilStart <= 7 && $daysUntilStart >= 0);
                
                $schedule->needs_update = $shouldNeedUpdate && 
                                        !$schedule->needs_update_acknowledged && 
                                        is_null($schedule->needs_update_resolved_at);
            } else {
                $schedule->needs_update = false;
            }
            
            $schedule->is_upcoming = $isUpcoming;
            
            // For weekly repeating schedules, they're always current once started
            if ($schedule->repeat_every_week) {
                $schedule->is_current = $startDate->lte($today);
            } else {
                $schedule->is_current = $startDate->lte($today) && $endDate->gte($today);
            }
            
            $schedule->was_updated_after_flag = !is_null($schedule->needs_update_resolved_at);
        }
    });

    // Filter current schedules for the weekly view
    $currentSchedules = $schedules->filter(function($schedule) {
        return $schedule->is_current;
    });

    $endingSoonSchedules = $schedules->where('ending_soon', true)->sortBy('days_remaining');
    $schedulesNeedingUpdate = $schedules->where('needs_update', true);
    $upcomingSchedules = $schedules->where('is_upcoming', true);

    // Get shift interchange requests for dashboard
    $userRole = Session::get('role');
    $userId = Session::get('user_id');
    $adminId = Session::get('admin_id');
    
    $pendingInterchangeRequests = 0;
    if ($userRole === 'admin') {
        $pendingInterchangeRequests = DB::table('shift_interchanges')
            ->where('status', 'pending')
            ->where('deleted_at', 0)
            ->count();
    } elseif ($userRole === 'employee') {
        $pendingInterchangeRequests = DB::table('shift_interchanges')
            ->where(function($query) use ($userId) {
                $query->where('requester_id', $userId)
                      ->orWhere('target_employee_id', $userId);
            })
            ->where('status', 'pending')
            ->where('deleted_at', 0)
            ->count();
    }

    return view('hrms.Employee.Schedule.index', [
        'schedules' => $schedules,
        'currentSchedules' => $currentSchedules, // Add this line
        'daysOfWeek' => $daysOfWeek,
        'endingSoonSchedules' => $endingSoonSchedules,
        'schedulesNeedingUpdate' => $schedulesNeedingUpdate,
        'upcomingSchedules' => $upcomingSchedules,
        'today' => $today,
        'pendingInterchangeRequests' => $pendingInterchangeRequests,
        'userRole' => $userRole
    ]);
}

    // Shift Interchange Methods
    public function shiftInterchangeIndex()
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        $adminId = Session::get('admin_id');
        
        if ($userRole === 'admin') {
            // Admin can see all interchange requests
            $interchangeRequests = DB::table('shift_interchanges as si')
                ->leftJoin('allemployees as requester', 'si.requester_id', '=', 'requester.id')
                ->leftJoin('allemployees as target', 'si.target_employee_id', '=', 'target.id')
                ->leftJoin('schedule as req_schedule', 'si.requester_schedule_id', '=', 'req_schedule.id')
                ->leftJoin('schedule as target_schedule', 'si.target_schedule_id', '=', 'target_schedule.id')
                ->leftJoin('shifts as req_shift', 'req_schedule.shift_id', '=', 'req_shift.id')
                ->leftJoin('shifts as target_shift', 'target_schedule.shift_id', '=', 'target_shift.id')
                ->leftJoin('department as req_dept', 'req_schedule.department_id', '=', 'req_dept.id')
                ->leftJoin('department as target_dept', 'target_schedule.department_id', '=', 'target_dept.id')
                ->select(
                    'si.*',
                    'requester.firstname as requester_firstname',
                    'requester.lastname as requester_lastname',
                    'requester.email as requester_email',
                    'target.firstname as target_firstname',
                    'target.lastname as target_lastname',
                    'target.email as target_email',
                    'req_shift.shift_name as requester_shift_name',
                    'req_shift.start_time as requester_start_time',
                    'req_shift.end_time as requester_end_time',
                    'target_shift.shift_name as target_shift_name',
                    'target_shift.start_time as target_start_time',
                    'target_shift.end_time as target_end_time',
                    'req_dept.department as requester_department',
                    'target_dept.department as target_department'
                )
                ->where('si.deleted_at', 0)
                ->orderBy('si.created_at', 'desc')
                ->get();
        } else {
            // Employee can only see their own requests (sent and received)
            $interchangeRequests = DB::table('shift_interchanges as si')
                ->leftJoin('allemployees as requester', 'si.requester_id', '=', 'requester.id')
                ->leftJoin('allemployees as target', 'si.target_employee_id', '=', 'target.id')
                ->leftJoin('schedule as req_schedule', 'si.requester_schedule_id', '=', 'req_schedule.id')
                ->leftJoin('schedule as target_schedule', 'si.target_schedule_id', '=', 'target_schedule.id')
                ->leftJoin('shifts as req_shift', 'req_schedule.shift_id', '=', 'req_shift.id')
                ->leftJoin('shifts as target_shift', 'target_schedule.shift_id', '=', 'target_shift.id')
                ->leftJoin('department as req_dept', 'req_schedule.department_id', '=', 'req_dept.id')
                ->leftJoin('department as target_dept', 'target_schedule.department_id', '=', 'target_dept.id')
                ->select(
                    'si.*',
                    'requester.firstname as requester_firstname',
                    'requester.lastname as requester_lastname',
                    'requester.email as requester_email',
                    'target.firstname as target_firstname',
                    'target.lastname as target_lastname',
                    'target.email as target_email',
                    'req_shift.shift_name as requester_shift_name',
                    'req_shift.start_time as requester_start_time',
                    'req_shift.end_time as requester_end_time',
                    'target_shift.shift_name as target_shift_name',
                    'target_shift.start_time as target_start_time',
                    'target_shift.end_time as target_end_time',
                    'req_dept.department as requester_department',
                    'target_dept.department as target_department'
                )
                ->where('si.deleted_at', 0)
                ->where(function($query) use ($userId) {
                    $query->where('si.requester_id', $userId)
                          ->orWhere('si.target_employee_id', $userId);
                })
                ->orderBy('si.created_at', 'desc')
                ->get();
        }

        return view('hrms.Employee.Schedule.shift_interchange', compact('interchangeRequests', 'userRole'));
    }

    public function shiftInterchangeCreate()
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('role');
        
        if ($userRole !== 'employee') {
            return redirect()->route('scheduling.shift-interchange')->with('error', 'Only employees can create interchange requests.');
        }

        // Get current employee's active schedules
        $today = Carbon::today();
        $mySchedules = DB::table('schedule')
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->select(
                'schedule.*',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'department.department'
            )
            ->where('schedule.employee_id', $userId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.publish', 1)
            ->where('schedule.schedule_end_date', '>=', $today)
            ->get();

        // Get other employees' schedules that can be interchanged
        $availableSchedules = DB::table('schedule')
            ->leftJoin('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->select(
                'schedule.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'department.department'
            )
            ->where('schedule.employee_id', '!=', $userId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.publish', 1)
            ->where('schedule.schedule_end_date', '>=', $today)
            ->where('allemployees.status', 'active')
            ->where('allemployees.deleted_at', 0)
            ->get();

        return view('hrms.Employee.Schedule.shift_interchange_create', compact('mySchedules', 'availableSchedules'));
    }

    public function shiftInterchangeStore(Request $request)
    {
        $request->validate([
            'requester_schedule_id' => 'required|exists:schedule,id',
            'target_schedule_id' => 'required|exists:schedule,id',
            'interchange_dates' => 'required|array|min:1',
            'interchange_dates.*' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:500'
        ]);

        $userId = Session::get('user_id');
        $userRole = Session::get('role');
        
        if ($userRole !== 'employee') {
            return redirect()->route('scheduling.shift-interchange')->with('error', 'Only employees can create interchange requests.');
        }

        try {
            // Verify the requester schedule belongs to the current user
            $requesterSchedule = DB::table('schedule')->where('id', $request->requester_schedule_id)->first();
            if (!$requesterSchedule || $requesterSchedule->employee_id != $userId) {
                return redirect()->back()->with('error', 'Invalid schedule selection.');
            }

            // Get target employee ID
            $targetSchedule = DB::table('schedule')->where('id', $request->target_schedule_id)->first();
            if (!$targetSchedule) {
                return redirect()->back()->with('error', 'Target schedule not found.');
            }

            // Check for existing pending requests for the same dates
            foreach ($request->interchange_dates as $date) {
                $existingRequest = DB::table('shift_interchanges')
                    ->where('requester_id', $userId)
                    ->where('target_employee_id', $targetSchedule->employee_id)
                    ->where('interchange_date', $date)
                    ->where('status', 'pending')
                    ->where('deleted_at', 0)
                    ->exists();

                if ($existingRequest) {
                    return redirect()->back()->with('error', "You already have a pending request for {$date}.");
                }
            }

            // Create interchange requests for each selected date
            foreach ($request->interchange_dates as $date) {
                DB::table('shift_interchanges')->insert([
                    'requester_id' => $userId,
                    'target_employee_id' => $targetSchedule->employee_id,
                    'requester_schedule_id' => $request->requester_schedule_id,
                    'target_schedule_id' => $request->target_schedule_id,
                    'interchange_date' => $date,
                    'reason' => $request->reason,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Send notification email to target employee
            $this->sendInterchangeRequestEmail($userId, $targetSchedule->employee_id, $request->interchange_dates, $request->reason);

            return redirect()->route('scheduling.shift-interchange')->with('success', 'Shift interchange request submitted successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to create shift interchange request: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit interchange request.');
        }
    }

    public function shiftInterchangeApprove(Request $request, $id)
    {
        $userRole = Session::get('role');
        
        if ($userRole !== 'admin') {
            return response()->json(['error' => 'Only administrators can approve interchange requests.'], 403);
        }

        try {
            $interchange = DB::table('shift_interchanges')->where('id', $id)->first();
            
            if (!$interchange) {
                return response()->json(['error' => 'Interchange request not found.'], 404);
            }

            if ($interchange->status !== 'pending') {
                return response()->json(['error' => 'This request has already been processed.'], 400);
            }

            // Update the interchange status
            DB::table('shift_interchanges')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'approved_by' => Session::get('admin_id'),
                    'approved_at' => now(),
                    'admin_notes' => $request->admin_notes,
                    'updated_at' => now()
                ]);

            // Create temporary schedule adjustments for the interchange date
            $this->createScheduleAdjustments($interchange);

            // Send notification emails
            $this->sendApprovalNotificationEmails($interchange, 'approved');

            return response()->json(['success' => 'Interchange request approved successfully.']);
        } catch (\Exception $e) {
            Log::error("Failed to approve interchange request {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to approve interchange request.'], 500);
        }
    }

    public function shiftInterchangeReject(Request $request, $id)
    {
        $userRole = Session::get('role');
        
        if ($userRole !== 'admin') {
            return response()->json(['error' => 'Only administrators can reject interchange requests.'], 403);
        }

        try {
            $interchange = DB::table('shift_interchanges')->where('id', $id)->first();
            
            if (!$interchange) {
                return response()->json(['error' => 'Interchange request not found.'], 404);
            }

            if ($interchange->status !== 'pending') {
                return response()->json(['error' => 'This request has already been processed.'], 400);
            }

            // Update the interchange status
            DB::table('shift_interchanges')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'approved_by' => Session::get('admin_id'),
                    'approved_at' => now(),
                    'admin_notes' => $request->admin_notes,
                    'updated_at' => now()
                ]);

            // Send notification emails
            $this->sendApprovalNotificationEmails($interchange, 'rejected');

            return response()->json(['success' => 'Interchange request rejected.']);
        } catch (\Exception $e) {
            Log::error("Failed to reject interchange request {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to reject interchange request.'], 500);
        }
    }

    public function shiftInterchangeCancel($id)
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('role');
        
        if ($userRole !== 'employee') {
            return response()->json(['error' => 'Only employees can cancel their requests.'], 403);
        }

        try {
            $interchange = DB::table('shift_interchanges')
                ->where('id', $id)
                ->where('requester_id', $userId)
                ->first();
            
            if (!$interchange) {
                return response()->json(['error' => 'Interchange request not found or you do not have permission to cancel it.'], 404);
            }

            if ($interchange->status !== 'pending') {
                return response()->json(['error' => 'Only pending requests can be cancelled.'], 400);
            }

            // Update the interchange status
            DB::table('shift_interchanges')
                ->where('id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);

            return response()->json(['success' => 'Interchange request cancelled successfully.']);
        } catch (\Exception $e) {
            Log::error("Failed to cancel interchange request {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to cancel interchange request.'], 500);
        }
    }

    public function getAvailableEmployeesForInterchange(Request $request)
    {
        $scheduleId = $request->schedule_id;
        $userId = Session::get('user_id');
        
        if (!$scheduleId) {
            return response()->json([]);
        }

        // Get the selected schedule details
        $selectedSchedule = DB::table('schedule')
            ->where('id', $scheduleId)
            ->where('employee_id', $userId)
            ->first();

        if (!$selectedSchedule) {
            return response()->json([]);
        }

        // Get other employees with schedules in the same time period
        $availableEmployees = DB::table('schedule')
            ->leftJoin('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->select(
                'schedule.id as schedule_id',
                'allemployees.id as employee_id',
                'allemployees.firstname',
                'allemployees.lastname',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'department.department'
            )
            ->where('schedule.employee_id', '!=', $userId)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.publish', 1)
            ->where('schedule.schedule_end_date', '>=', Carbon::today())
            ->where('allemployees.status', 'active')
            ->where('allemployees.deleted_at', 0)
            ->where(function($query) use ($selectedSchedule) {
                $query->whereBetween('schedule.schedule_start_date', [$selectedSchedule->schedule_start_date, $selectedSchedule->schedule_end_date])
                      ->orWhereBetween('schedule.schedule_end_date', [$selectedSchedule->schedule_start_date, $selectedSchedule->schedule_end_date])
                      ->orWhere(function($q) use ($selectedSchedule) {
                          $q->where('schedule.schedule_start_date', '<=', $selectedSchedule->schedule_start_date)
                            ->where('schedule.schedule_end_date', '>=', $selectedSchedule->schedule_end_date);
                      });
            })
            ->get();

        return response()->json($availableEmployees);
    }

    private function createScheduleAdjustments($interchange)
    {
        // Create schedule adjustments table entry for tracking the interchange
        DB::table('schedule_adjustments')->insert([
            'interchange_id' => $interchange->id,
            'original_employee_id' => $interchange->requester_id,
            'replacement_employee_id' => $interchange->target_employee_id,
            'original_schedule_id' => $interchange->requester_schedule_id,
            'replacement_schedule_id' => $interchange->target_schedule_id,
            'adjustment_date' => $interchange->interchange_date,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function sendInterchangeRequestEmail($requesterId, $targetEmployeeId, $dates, $reason)
    {
        try {
            $requester = DB::table('allemployees')->where('id', $requesterId)->first();
            $target = DB::table('allemployees')->where('id', $targetEmployeeId)->first();
            
            if (!$requester || !$target || !$target->email) {
                return false;
            }

            $datesString = implode(', ', array_map(function($date) {
                return Carbon::parse($date)->format('M d, Y');
            }, $dates));

            $mailData = [
                'type' => 'interchange_request',
                'requester_name' => $requester->firstname . ' ' . $requester->lastname,
                'target_name' => $target->firstname . ' ' . $target->lastname,
                'dates' => $datesString,
                'reason' => $reason,
                'request_date' => now()->format('M d, Y \\a\\t g:i A')
            ];

            // You would need to create this mail class or use existing one
            // Mail::to($target->email)->send(new ScheduleNotification($mailData));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send interchange request email: " . $e->getMessage());
            return false;
        }
    }

  

    // Original scheduling methods continue below...

    private function autoPublishSchedules()
    {
        $today = Carbon::today();

        try {
            $schedulesToPublish = DB::table('schedule')
                ->where('schedule_start_date', '<=', $today)
                ->where('schedule_end_date', '>=', $today)
                ->where('publish', 0)
                ->where('deleted_at', 0)
                ->get();

            foreach ($schedulesToPublish as $schedule) {
                DB::table('schedule')
                    ->where('id', $schedule->id)
                    ->update([
                        'publish' => 1,
                        'updated_at' => now()
                    ]);

                $this->sendScheduleCreatedEmail($schedule->id);
            }
        } catch (\Exception $e) {
            Log::error("Failed to auto-publish schedules: " . $e->getMessage());
        }
    }

    public function create()
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'email')
            ->get();

        $departments = DB::table('department')
            ->select('id', 'department')
            ->get();

        $shifts = DB::table('shifts')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'shift_name', 'start_time', 'end_time', 'break_time', 'days_of_week')
            ->get();

        return view('hrms.Employee.Schedule.create', compact('employees', 'departments', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|exists:department,id',
            'employees' => 'required|array|min:1',
            'employees.*' => 'exists:allemployees,id',
            'shift' => 'required|exists:shifts,id',
            'repeat_every_week' => 'required|in:0,1',
            'schedule_start_date' => 'required|date|after_or_equal:today',
            'schedule_end_date' => 'required|date|after:schedule_start_date',
        ]);

        $successCount = 0;
        $emailFailures = [];

        $repeatWeekly = (int) $request->repeat_every_week === 1;
        $publishStatus = $request->publish ? 1 : 0;

        foreach ($request->employees as $employeeId) {
            try {
                $overlapping = DB::table('schedule')
                    ->where('employee_id', $employeeId)
                    ->where('deleted_at', 0)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('schedule_start_date', [$request->schedule_start_date, $request->schedule_end_date])
                            ->orWhereBetween('schedule_end_date', [$request->schedule_start_date, $request->schedule_end_date])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('schedule_start_date', '<=', $request->schedule_start_date)
                                    ->where('schedule_end_date', '>=', $request->schedule_end_date);
                            });
                    })
                    ->exists();

                if ($overlapping) {
                    continue;
                }

                $scheduleId = DB::table('schedule')->insertGetId([
                    'department_id' => $request->department,
                    'employee_id' => $employeeId,
                    'shift_id' => $request->shift,
                    'repeat_every_week' => $repeatWeekly ? 1 : 0,
                    'schedule_start_date' => $request->schedule_start_date,
                    'schedule_end_date' => $request->schedule_end_date,
                    'accept_extra_hours' => $request->accept_extra_hours ? 1 : 0,
                    'publish' => $publishStatus,
                    'is_current' => 1,
                    'needs_update_acknowledged' => false,
                    'needs_update_resolved_at' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $successCount++;

                if ($publishStatus) {
                    $emailSent = $this->sendScheduleCreatedEmail($scheduleId);
                    if (!$emailSent) {
                        $employee = DB::table('allemployees')->find($employeeId);
                        $emailFailures[] = $employee->firstname . ' ' . $employee->lastname;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to create schedule for employee {$employeeId}: " . $e->getMessage());
            }
        }

        $message = "Schedule added successfully for {$successCount} employee(s)!";
        if (!empty($emailFailures)) {
            $message .= " Email notifications failed for: " . implode(', ', $emailFailures);
        }

        return redirect()->route('scheduling.index')->with('success', $message);
    }

    public function edit($id)
    {
        $schedule = DB::table('schedule')->where('id', $id)->first();

        if (!$schedule) {
            return redirect()->route('scheduling.index')->with('error', 'Schedule not found.');
        }

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'email')
            ->get();

        $departments = DB::table('department')
            ->select('id', 'department')
            ->get();

        $shifts = DB::table('shifts')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'shift_name', 'start_time', 'end_time', 'break_time', 'days_of_week')
            ->get();

        return view('hrms.Employee.Schedule.edit', compact('schedule', 'employees', 'departments', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'department' => 'required|exists:department,id',
            'employee' => 'required|exists:allemployees,id',
            'schedule_start_date' => 'required|date',
            'schedule_end_date' => 'required|date|after:schedule_start_date',
            'shift' => 'required|exists:shifts,id',
            'repeat_every_week' => 'required|in:0,1',
        ]);

        try {
            $currentSchedule = DB::table('schedule')->find($id);
            if (!$currentSchedule) {
                return redirect()->route('scheduling.index')->with('error', 'Schedule not found.');
            }

            $today = Carbon::today();
            $endDate = Carbon::parse($currentSchedule->schedule_end_date);
            $daysUntilEnd = $today->diffInDays($endDate, false);

            $repeatWeekly = (int) $request->repeat_every_week === 1;

            if ($request->has('create_new_schedule') && $daysUntilEnd <= 3 && $daysUntilEnd >= 0) {
                $newScheduleId = $this->createNewScheduleVersion($request, $id, $currentSchedule, $repeatWeekly);
                $message = 'New schedule created successfully! Previous schedule remains active until ' .
                    $endDate->format('M d, Y') . '.';
                $scheduleIdForEmail = $newScheduleId;
                $isNewSchedule = true;
            } else {
                DB::table('schedule')
                    ->where('id', $id)
                    ->update([
                        'department_id' => $request->department,
                        'employee_id' => $request->employee,
                        'shift_id' => $request->shift,
                        'repeat_every_week' => $repeatWeekly ? 1 : 0,
                        'schedule_start_date' => $request->schedule_start_date,
                        'schedule_end_date' => $request->schedule_end_date,
                        'accept_extra_hours' => $request->has('accept_extra_hours') ? 1 : 0,
                        'publish' => $request->has('publish') ? 1 : 0,
                        'updated_at' => now(),
                        'needs_update_resolved_at' => now(),
                        'needs_update_acknowledged' => true
                    ]);

                $message = 'Schedule updated successfully!';
                $scheduleIdForEmail = $id;
                $isNewSchedule = false;
            }

            if ($request->has('publish')) {
                $emailSent = $this->sendScheduleUpdatedEmail($scheduleIdForEmail, $isNewSchedule);
                if (!$emailSent) {
                    $message .= ' (Email notification failed to send)';
                }
            }

            return redirect()->route('scheduling.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Failed to update schedule {$id}: " . $e->getMessage());
            return redirect()->route('scheduling.index')->with('error', 'Failed to update schedule.');
        }
    }
    private function createNewScheduleVersion($request, $currentId, $currentSchedule, bool $repeatWeekly)
    {
        $newStart = Carbon::parse($request->schedule_start_date);
        $newEnd = Carbon::parse($request->schedule_end_date);

        $overlapping = DB::table('schedule')
            ->where('employee_id', $request->employee)
            ->where('deleted_at', 0)
            ->where('id', '!=', $currentId)
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->whereBetween('schedule_start_date', [$newStart, $newEnd])
                    ->orWhereBetween('schedule_end_date', [$newStart, $newEnd])
                    ->orWhere(function ($q) use ($newStart, $newEnd) {
                        $q->where('schedule_start_date', '<=', $newStart)
                            ->where('schedule_end_date', '>=', $newEnd);
                    });
            })
            ->exists();

        if ($overlapping) {
            throw new \Exception('The new schedule overlaps with an existing schedule.');
        }

        $newScheduleId = DB::table('schedule')->insertGetId([
            'department_id' => $request->department,
            'employee_id' => $request->employee,
            'shift_id' => $request->shift,
            'repeat_every_week' => $repeatWeekly ? 1 : 0,
            'schedule_start_date' => $newStart,
            'schedule_end_date' => $newEnd,
            'accept_extra_hours' => $request->has('accept_extra_hours') ? 1 : 0,
            'publish' => $request->has('publish') ? 1 : 0,
            'is_current' => 0,
            'previous_schedule_id' => $currentId,
            'needs_update_acknowledged' => true,
            'needs_update_resolved_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $newScheduleId;
    }
     public function destroy($id)
    {
        try {
            $schedule = DB::table('schedule')
                ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
                ->where('schedule.id', $id)
                ->select('schedule.*', 'allemployees.email', 'allemployees.firstname', 'allemployees.lastname')
                ->first();

            if (!$schedule) {
                return redirect()->route('scheduling.index')->with('error', 'Schedule not found.');
            }

            DB::table('schedule')
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            $this->sendScheduleCancelledEmail($schedule);

            return redirect()->route('scheduling.index')->with('success', 'Schedule deleted successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to delete schedule {$id}: " . $e->getMessage());
            return redirect()->route('scheduling.index')->with('error', 'Failed to delete schedule.');
        }
    }

    public function getEmployeesByDepartment($departmentId)
    {
        $employees = DB::table('allemployees')
            ->where('department', $departmentId)
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'designation', 'email')
            ->get();
            
        return response()->json($employees);
    }

    private function sendScheduleCreatedEmail($scheduleId)
    {
        try {
            $schedule = $this->getScheduleForEmail($scheduleId);
            if (!$schedule || empty($schedule->email)) {
                return false;
            }

            $mailData = [
                'type' => 'created',
                'employee_name' => $schedule->firstname . ' ' . $schedule->lastname,
                'department' => $schedule->department_name,
                'shift_name' => $schedule->shift_name,
                'start_time' => Carbon::parse($schedule->start_time)->format('g:i A'),
                'end_time' => Carbon::parse($schedule->end_time)->format('g:i A'),
                'schedule_start' => Carbon::parse($schedule->schedule_start_date)->format('M d, Y'),
                'schedule_end' => Carbon::parse($schedule->schedule_end_date)->format('M d, Y'),
                'created_by' => Auth::user()->name ?? 'System',
                'created_date' => now()->format('M d, Y \\a\\t g:i A'),
            ];

            Mail::to($schedule->email)->send(new ScheduleNotification($mailData));
            
            if (config('mail.hr_email')) {
                Mail::to(config('mail.hr_email'))->send(new ScheduleNotification(array_merge($mailData, ['is_hr_copy' => true])));
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send schedule created email for schedule {$scheduleId}: " . $e->getMessage());
            return false;
        }
    }

    private function sendScheduleUpdatedEmail($scheduleId, $isNewSchedule = false)
    {
        try {
            $schedule = $this->getScheduleForEmail($scheduleId);
            if (!$schedule || empty($schedule->email)) {
                return false;
            }

            $mailData = [
                'type' => $isNewSchedule ? 'new_version' : 'updated',
                'employee_name' => $schedule->firstname . ' ' . $schedule->lastname,
                'department' => $schedule->department_name,
                'shift_name' => $schedule->shift_name,
                'start_time' => Carbon::parse($schedule->start_time)->format('g:i A'),
                'end_time' => Carbon::parse($schedule->end_time)->format('g:i A'),
                'schedule_start' => Carbon::parse($schedule->schedule_start_date)->format('M d, Y'),
                'schedule_end' => Carbon::parse($schedule->schedule_end_date)->format('M d, Y'),
                'updated_by' => Auth::user()->name ?? 'System',
                'updated_date' => now()->format('M d, Y \\a\\t g:i A'),
            ];

            Mail::to($schedule->email)->send(new ScheduleNotification($mailData));
            
            if (config('mail.hr_email')) {
                Mail::to(config('mail.hr_email'))->send(new ScheduleNotification(array_merge($mailData, ['is_hr_copy' => true])));
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send schedule updated email for schedule {$scheduleId}: " . $e->getMessage());
            return false;
        }
    }

    private function sendScheduleCancelledEmail($schedule)
    {
        try {
            if (empty($schedule->email)) {
                return false;
            }

            $mailData = [
                'type' => 'cancelled',
                'employee_name' => $schedule->firstname . ' ' . $schedule->lastname,
                'schedule_start' => Carbon::parse($schedule->schedule_start_date)->format('M d, Y'),
                'schedule_end' => Carbon::parse($schedule->schedule_end_date)->format('M d, Y'),
                'cancelled_by' => Auth::user()->name ?? 'System',
                'cancelled_date' => now()->format('M d, Y \\a\\t g:i A'),
            ];

            Mail::to($schedule->email)->send(new ScheduleNotification($mailData));
            
            if (config('mail.hr_email')) {
                Mail::to(config('mail.hr_email'))->send(new ScheduleNotification(array_merge($mailData, ['is_hr_copy' => true])));
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send schedule cancelled email: " . $e->getMessage());
            return false;
        }
    }

    private function getScheduleForEmail($scheduleId)
    {
        return DB::table('schedule')
            ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->join('department', 'schedule.department_id', '=', 'department.id')
            ->where('schedule.id', $scheduleId)
            ->select(
                'schedule.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'department.department as department_name'
            )
            ->first();
    }

  
    private function updateCurrentScheduleStatus()
    {
        $today = Carbon::today();

        try {
            DB::table('schedule')
                ->where('schedule_end_date', '<', $today)
                ->where('is_current', 1)
                ->where('deleted_at', 0)
                ->update(['is_current' => 0, 'updated_at' => now()]);

            $schedulesToActivate = DB::table('schedule')
                ->where('schedule_start_date', '<=', $today)
                ->where('schedule_end_date', '>=', $today)
                ->where('is_current', 0)
                ->where('deleted_at', 0)
                ->get();

            foreach ($schedulesToActivate as $schedule) {
                $hasCurrentSchedule = DB::table('schedule')
                    ->where('employee_id', $schedule->employee_id)
                    ->where('is_current', 1)
                    ->where('deleted_at', 0)
                    ->where('id', '!=', $schedule->id)
                    ->exists();

                if (!$hasCurrentSchedule) {
                    DB::table('schedule')
                        ->where('id', $schedule->id)
                        ->update(['is_current' => 1, 'updated_at' => now()]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to update current schedule status: " . $e->getMessage());
        }
    }

    private function cleanupExpiredSchedules()
    {
        $cutoffDate = Carbon::now()->subDays(30);
        
        try {
            DB::table('schedule')
                ->where('schedule_end_date', '<', $cutoffDate)
                ->where('deleted_at', 0)
                ->update(['deleted_at' => now()]);

            $hardDeleteDate = Carbon::now()->subYear();
            DB::table('schedule')
                ->where('schedule_end_date', '<', $hardDeleteDate)
                ->where('deleted_at', '!=', 0)
                ->delete();
        } catch (\Exception $e) {
            Log::error("Failed to cleanup expired schedules: " . $e->getMessage());
        }
    }
private function sendApprovalNotificationEmails($interchange, $status)
{
    try {
        $requester = DB::table('allemployees')->where('id', $interchange->requester_id)->first();
        $target = DB::table('allemployees')->where('id', $interchange->target_employee_id)->first();
        
        if (!$requester || !$target) {
            return false;
        }

        // Get schedule details for both employees
        $requesterSchedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.id', $interchange->requester_schedule_id)
            ->select('shifts.shift_name', 'shifts.start_time', 'shifts.end_time')
            ->first();

        $targetSchedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.id', $interchange->target_schedule_id)
            ->select('shifts.shift_name', 'shifts.start_time', 'shifts.end_time')
            ->first();

        $mailData = [
            'type' => 'interchange_' . $status,
            'interchange_date' => Carbon::parse($interchange->interchange_date)->format('M d, Y'),
            'status' => $status,
            'processed_date' => now()->format('M d, Y \\a\\t g:i A'),
            'admin_notes' => $interchange->admin_notes,
            'requester_name' => $requester->firstname . ' ' . $requester->lastname,
            'target_name' => $target->firstname . ' ' . $target->lastname,
            'requester_original_shift' => $requesterSchedule ? [
                'name' => $requesterSchedule->shift_name,
                'start' => Carbon::parse($requesterSchedule->start_time)->format('g:i A'),
                'end' => Carbon::parse($requesterSchedule->end_time)->format('g:i A')
            ] : null,
            'target_original_shift' => $targetSchedule ? [
                'name' => $targetSchedule->shift_name,
                'start' => Carbon::parse($targetSchedule->start_time)->format('g:i A'),
                'end' => Carbon::parse($targetSchedule->end_time)->format('g:i A')
            ] : null,
            // For approved requests, show what the new shifts will be
            'requester_new_shift' => ($status === 'approved' && $targetSchedule) ? [
                'name' => $targetSchedule->shift_name,
                'start' => Carbon::parse($targetSchedule->start_time)->format('g:i A'),
                'end' => Carbon::parse($targetSchedule->end_time)->format('g:i A')
            ] : null,
            'target_new_shift' => ($status === 'approved' && $requesterSchedule) ? [
                'name' => $requesterSchedule->shift_name,
                'start' => Carbon::parse($requesterSchedule->start_time)->format('g:i A'),
                'end' => Carbon::parse($requesterSchedule->end_time)->format('g:i A')
            ] : null
        ];

        // Send to requester
        if ($requester->email) {
            $requesterMailData = array_merge($mailData, [
                'is_requester' => true,
                'other_employee_name' => $target->firstname . ' ' . $target->lastname
            ]);
            Mail::to($requester->email)->send(new ScheduleNotification($requesterMailData));
        }
        
        // Send to target employee
        if ($target->email) {
            $targetMailData = array_merge($mailData, [
                'is_requester' => false,
                'other_employee_name' => $requester->firstname . ' ' . $requester->lastname
            ]);
            Mail::to($target->email)->send(new ScheduleNotification($targetMailData));
        }

        return true;
    } catch (\Exception $e) {
        Log::error("Failed to send interchange {$status} notification emails: " . $e->getMessage());
        return false;
    }
}
}