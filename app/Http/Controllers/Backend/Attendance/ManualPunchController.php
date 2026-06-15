<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManualPunchController extends Controller
{
    /**
     * Display manual punch requests (Employee)
     */
    public function index(Request $request)
    {
        $employeeId = Session::get('user_id');
        $today = now()->format('Y-m-d');

        // Employee details
        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('firstname', 'lastname', 'employeeid', 'designation')
            ->first();

        // Manual punch requests
        $requests = DB::table('manual_punch_requests as mpr')
            ->leftJoin('allemployees as appr', 'appr.id', '=', 'mpr.approved_by')
            ->where('mpr.employee_id', $employeeId)
            ->whereNull('mpr.deleted_at')
            ->select(
                'mpr.*',
                DB::raw("CONCAT(appr.firstname,' ',appr.lastname) as approver_name")
            )
            ->orderBy('mpr.created_at', 'desc')
            ->paginate(10);

        // Today attendance
        $todayAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        // Current schedule
        $currentSchedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->whereDate('schedule.schedule_start_date', '<=', $today)
            ->whereDate('schedule.schedule_end_date', '>=', $today)
            ->first();

        // Pending request check
        $hasPendingRequest = DB::table('manual_punch_requests')
            ->where('employee_id', $employeeId)
            ->whereDate('request_date', $today)
            ->where('status', 'pending')
            ->exists();

        return view('hrms.attendance.manual-punch.index', compact(
            'employee',
            'requests',
            'todayAttendance',
            'currentSchedule',
            'hasPendingRequest',
            'today'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $employeeId = Session::get('user_id');
        $today = now()->format('Y-m-d');

        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('firstname', 'lastname', 'employeeid')
            ->first();

        $todayAttendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        $existingRequest = DB::table('manual_punch_requests')
            ->where('employee_id', $employeeId)
            ->whereDate('request_date', $today)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('manual-punch.index')
                ->with('error', 'You already have a pending request for today.');
        }

        $currentSchedule = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->whereDate('schedule.schedule_start_date', '<=', $today)
            ->whereDate('schedule.schedule_end_date', '>=', $today)
            ->first();

        return view('hrms.attendance.manual-punch.create', compact(
            'employee',
            'todayAttendance',
            'currentSchedule',
            'today'
        ));
    }

    /**
     * Store manual punch request
     */
    public function store(Request $request)
    {
        $employeeId = Session::get('user_id');

        $request->validate([
            'request_type' => 'required|in:punch_in,punch_out',
            'request_date' => 'required|date',
            'request_time' => 'required|date_format:H:i',
            'reason' => 'required|min:10|max:500',
        ]);

        if (Carbon::parse($request->request_date)->isFuture()) {
            return back()->withInput()->with('error', 'Future dates not allowed.');
        }

        // Check for pending requests
        $pendingRequest = DB::table('manual_punch_requests')
            ->where('employee_id', $employeeId)
            ->whereDate('request_date', $request->request_date)
            ->where('request_type', $request->request_type)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return back()->withInput()->with('error', 'You already have a pending ' . str_replace('_', ' ', $request->request_type) . ' request for this date.');
        }

        // Check attendance records
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $request->request_date)
            ->first();

        if ($request->request_type === 'punch_in') {
            if ($attendance && $attendance->punch_in) {
                return back()->withInput()->with('error', 'Punch in already exists for this date.');
            }
        }

        if ($request->request_type === 'punch_out') {
            if (!$attendance || !$attendance->punch_in) {
                return back()->withInput()->with('error', 'Punch in is required before submitting a punch out request.');
            }
            if ($attendance->punch_out) {
                return back()->withInput()->with('error', 'Punch out already exists for this date.');
            }
        }

        DB::table('manual_punch_requests')->insert([
            'employee_id' => $employeeId,
            'request_date' => $request->request_date,
            'request_type' => $request->request_type,
            'request_time' => $request->request_time,
            'reason' => $request->reason,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email to manager
        $this->sendManagerNotification($employeeId, $request->request_type, $request->request_date, $request->request_time, $request->reason);

        return redirect()->route('manual-punch.index')
            ->with('success', 'Manual punch request submitted.');
    }

    /**
     * Edit request
     */
    public function edit($id)
    {
        $employeeId = Session::get('user_id');

        $requestData = DB::table('manual_punch_requests')
            ->where('id', $id)
            ->where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->first();

        abort_if(!$requestData, 404);

        if (Carbon::parse($requestData->created_at)->diffInHours(now()) > 1) {
            return redirect()->route('manual-punch.index')
                ->with('error', 'Edit time expired.');
        }

        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('firstname', 'lastname', 'employeeid')
            ->first();

        return view('hrms.attendance.manual-punch.edit', compact('employee', 'requestData'));
    }

    /**
     * Update request
     */
    public function update(Request $request, $id)
    {
        $employeeId = Session::get('user_id');

        $request->validate([
            'request_time' => 'required|date_format:H:i',
            'reason' => 'required|min:10|max:500',
        ]);

        DB::table('manual_punch_requests')
            ->where('id', $id)
            ->where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->update([
                'request_time' => $request->request_time,
                'reason' => $request->reason,
                'updated_at' => now(),
            ]);

        return redirect()->route('manual-punch.index')
            ->with('success', 'Request updated.');
    }

    /**
     * Delete request (soft delete)
     */
    public function destroy($id)
    {
        $employeeId = Session::get('user_id');

        DB::table('manual_punch_requests')
            ->where('id', $id)
            ->where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->update(['deleted_at' => now()]);

        return redirect()->route('manual-punch.index')
            ->with('success', 'Request deleted.');
    }

    /**
     * AJAX availability check
     */
    public function checkAvailability(Request $request)
    {
        $employeeId = Session::get('user_id');
        $date = $request->date;
        $type = $request->type;
        
        if (Carbon::parse($date)->isFuture()) {
            return response()->json([
                'can_request' => false, 
                'message' => 'Future date not allowed',
                'existing_record' => null
            ]);
        }
        
        // Check for existing pending request
        $pendingRequest = DB::table('manual_punch_requests')
            ->where('employee_id', $employeeId)
            ->whereDate('request_date', $date)
            ->where('request_type', $type)
            ->where('status', 'pending')
            ->first();
        
        if ($pendingRequest) {
            return response()->json([
                'can_request' => false,
                'message' => 'You already have a pending ' . str_replace('_', ' ', $type) . ' request for this date.',
                'existing_record' => [
                    'type' => 'pending_request',
                    'date' => $date,
                    'time' => Carbon::parse($pendingRequest->request_time)->format('h:i A'),
                    'request_id' => $pendingRequest->id
                ]
            ]);
        }
        
        // Check attendance table for existing punch in/out
        $attendance = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();
        
        $existing_record = null;
        $can_request = true;
        $message = '';
        
        if ($type === 'punch_in') {
            if ($attendance && $attendance->punch_in) {
                $can_request = false;
                $message = 'Punch in already exists for this date in attendance records.';
                $existing_record = [
                    'type' => 'existing_punch_in',
                    'date' => Carbon::parse($date)->format('M d, Y'),
                    'time' => Carbon::parse($attendance->punch_in)->format('h:i A'),
                    'raw_time' => $attendance->punch_in
                ];
            }
        } else if ($type === 'punch_out') {
            if (!$attendance || !$attendance->punch_in) {
                $can_request = false;
                $message = 'Punch in is required before creating a punch out request.';
                $existing_record = [
                    'type' => 'missing_punch_in',
                    'date' => Carbon::parse($date)->format('M d, Y'),
                    'time' => null
                ];
            } else if ($attendance && $attendance->punch_out) {
                $can_request = false;
                $message = 'Punch out already exists for this date in attendance records.';
                $existing_record = [
                    'type' => 'existing_punch_out',
                    'date' => Carbon::parse($date)->format('M d, Y'),
                    'time' => Carbon::parse($attendance->punch_out)->format('h:i A'),
                    'raw_time' => $attendance->punch_out
                ];
            }
        }
        
        if ($can_request) {
            $message = 'No conflicts found. You can submit your request.';
        }
        
        return response()->json([
            'can_request' => $can_request,
            'message' => $message,
            'existing_record' => $existing_record
        ]);
    }

    /**
     * Send email notification to manager
     */
    private function sendManagerNotification($employeeId, $requestType, $requestDate, $requestTime, $reason)
    {
        try {
            $employee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->first();

            if (!$employee || !$employee->manager_id) {
                Log::info('No manager assigned for employee ID: ' . $employeeId);
                return;
            }

            $manager = DB::table('allemployees')
                ->where('id', $employee->manager_id)
                ->first();

            if (!$manager || !$manager->email) {
                Log::warning('Manager email not found for employee ID: ' . $employeeId);
                return;
            }

            $data = [
                'managerName' => $manager->firstname,
                'employeeName' => $employee->firstname . ' ' . $employee->lastname,
                'employeeId' => $employee->employeeid,
                'requestType' => ucwords(str_replace('_', ' ', $requestType)),
                'requestDate' => Carbon::parse($requestDate)->format('d M Y'),
                'requestTime' => Carbon::parse($requestTime)->format('h:i A'),
                'reason' => $reason
            ];

            Mail::send('emails.manual-punch-request', $data, function ($message) use ($manager) {
                $message->to($manager->email)
                    ->subject('New Manual Punch Request - Pending Approval');
            });

            Log::info('Manager notification email sent to: ' . $manager->email);
        } catch (\Exception $e) {
            Log::error('Failed to send manager notification email: ' . $e->getMessage());
        }
    }
}