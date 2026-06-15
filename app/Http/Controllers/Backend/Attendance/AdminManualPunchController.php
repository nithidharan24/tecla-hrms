<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminManualPunchController extends Controller
{
    /**
     * List all manual punch requests (Admin)
     */
    public function index(Request $request)
    {
        $status     = $request->filled('status') ? $request->status : null;
        $date       = $request->date;
        $employeeId = $request->employee_id;
        $type       = $request->type;
        $search     = $request->search;
        $perPage    = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [15, 25, 50, 100])) $perPage = 15;

        $query = DB::table('manual_punch_requests as mpr')
            ->join('allemployees as emp', 'emp.id', '=', 'mpr.employee_id')
            ->leftJoin('allemployees as appr', 'appr.id', '=', 'mpr.approved_by')
            ->whereNull('mpr.deleted_at')
            ->select(
                'mpr.*',
                'emp.firstname',
                'emp.lastname',
                'emp.employeeid',
                DB::raw("CONCAT(appr.firstname, ' ', appr.lastname) as approver_name")
            );

        if ($status && $status !== 'all') {
            $query->where('mpr.status', $status);
        }

        if ($date) {
            $query->whereDate('mpr.request_date', $date);
        }

        if ($employeeId) {
            $query->where('mpr.employee_id', $employeeId);
        }

        if ($type) {
            $query->where('mpr.request_type', $type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('emp.firstname', 'like', "%{$search}%")
                  ->orWhere('emp.lastname', 'like', "%{$search}%")
                  ->orWhere('emp.employeeid', 'like', "%{$search}%")
                  ->orWhere('mpr.reason', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('mpr.created_at', 'desc')->paginate($perPage);

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->orderBy('firstname')
            ->get();

        $stats = [
            'pending'  => DB::table('manual_punch_requests')->where('status', 'pending')->whereNull('deleted_at')->count(),
            'approved' => DB::table('manual_punch_requests')->where('status', 'approved')->whereNull('deleted_at')->count(),
            'rejected' => DB::table('manual_punch_requests')->where('status', 'rejected')->whereNull('deleted_at')->count(),
            'total'    => DB::table('manual_punch_requests')->whereNull('deleted_at')->count(),
        ];

        return view('hrms.attendance.admin.manual-punch.index', compact(
            'requests',
            'employees',
            'stats',
            'status',
            'date',
            'employeeId',
            'type',
            'search',
            'perPage'
        ));
    }

    /**
     * Show request details
     */
    public function show($id)
    {
        $request = DB::table('manual_punch_requests as mpr')
            ->join('allemployees as emp', 'emp.id', '=', 'mpr.employee_id')
            ->leftJoin('allemployees as appr', 'appr.id', '=', 'mpr.approved_by')
            ->where('mpr.id', $id)
            ->whereNull('mpr.deleted_at')
            ->select(
                'mpr.*',
                'emp.firstname',
                'emp.lastname',
                'emp.employeeid',
                'emp.department',
                'emp.designation',
                DB::raw("CONCAT(appr.firstname, ' ', appr.lastname) as approver_name")
            )
            ->first();

        if (!$request) {
            return redirect()->route('admin.manual-punch.index')
                ->with('error', 'Request not found or has been deleted.');
        }

        $attendance = DB::table('attendances')
            ->where('employee_id', $request->employee_id)
            ->whereDate('date', $request->request_date)
            ->first();

        return view('hrms.attendance.admin.manual-punch.show', compact('request', 'attendance'));
    }

    /**
     * Approve request
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $adminId = Session::get('user_id');

        $manual = DB::table('manual_punch_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->first();

        if (!$manual) {
            return redirect()->route('admin.manual-punch.index')
                ->with('error', 'Request already processed or not found.');
        }

        DB::beginTransaction();
        try {
            // Update request status
            DB::table('manual_punch_requests')
                ->where('id', $id)
                ->update([
                    'status'        => 'approved',
                    'approved_by'   => $adminId,
                    'approved_at'   => now(),
                    'admin_remarks' => $request->remarks,
                    'updated_at'    => now(),
                ]);

            // Update or create attendance record
            $attendance = DB::table('attendances')
                ->where('employee_id', $manual->employee_id)
                ->whereDate('date', $manual->request_date)
                ->first();

            if (!$attendance) {
                $attendanceId = DB::table('attendances')->insertGetId([
                    'employee_id' => $manual->employee_id,
                    'date'        => $manual->request_date,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $attendance = (object) ['id' => $attendanceId];
            }

            $time = Carbon::parse($manual->request_date . ' ' . $manual->request_time);
            $updateData = [];

            if ($manual->request_type === 'punch_in') {
                $updateData['punch_in'] = $time;
            } else {
                $updateData['punch_out'] = $time;
                
                // Calculate working hours for punch_out
                $currentAttendance = DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->first();
                
                if ($currentAttendance && $currentAttendance->punch_in) {
                    $punchIn = Carbon::parse($currentAttendance->punch_in);
                    $punchOut = $time;
                    $workingMinutes = $punchOut->diffInMinutes($punchIn);
                    
                    // Get allocated break time from shift
                    $shift = DB::table('schedule')
                        ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
                        ->where('schedule.employee_id', $manual->employee_id)
                        ->where('schedule.deleted_at', 0)
                        ->whereDate('schedule.schedule_start_date', '<=', $manual->request_date)
                        ->whereDate('schedule.schedule_end_date', '>=', $manual->request_date)
                        ->first();
                    
                    $breakTime = $shift->break_time ?? 0;
                    $workingMinutes = $workingMinutes - $breakTime;
                    $workingHours = round($workingMinutes / 60, 2);
                    $updateData['working_hours'] = $workingHours;
                }
            }

            // Update attendance time
            DB::table('attendances')
                ->where('id', $attendance->id)
                ->update($updateData);

            // Send approval email to employee
            $this->sendStatusEmail($manual->employee_id, $manual->request_type, $manual->request_date, $manual->request_time, $request->remarks, 'approved');

            DB::commit();

            return redirect()->route('admin.manual-punch.index')
                ->with('success', 'Request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval failed for request ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('admin.manual-punch.index')
                ->with('error', 'Approval failed. Please try again.');
        }
    }

    /**
     * Reject request
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $adminId = Session::get('user_id');

        $manual = DB::table('manual_punch_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->first();

        if (!$manual) {
            return redirect()->route('admin.manual-punch.index')
                ->with('error', 'Request already processed or not found.');
        }

        DB::beginTransaction();
        try {
            DB::table('manual_punch_requests')
                ->where('id', $id)
                ->update([
                    'status'        => 'rejected',
                    'approved_by'   => $adminId,
                    'approved_at'   => now(),
                    'admin_remarks' => $request->remarks,
                    'updated_at'    => now(),
                ]);

            DB::commit();

            // Send rejection email to employee
            $this->sendStatusEmail($manual->employee_id, $manual->request_type, $manual->request_date, $manual->request_time, $request->remarks, 'rejected');

            return redirect()->route('admin.manual-punch.index')
                ->with('success', 'Request rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection failed for request ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('admin.manual-punch.index')
                ->with('error', 'Rejection failed. Please try again.');
        }
    }

    /**
     * Bulk approve / reject
     */
    public function bulkProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action'   => 'required|in:approve,reject',
            'ids'      => 'required|string',
            'remarks'  => $request->action == 'reject' ? 'required|string|max:500' : 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.manual-punch.index')
                ->with('bulk_error', 'Validation failed: ' . $validator->errors()->first());
        }

        $ids = array_filter(explode(',', $request->ids));
        
        if (empty($ids)) {
            return redirect()->route('admin.manual-punch.index')
                ->with('bulk_error', 'No valid requests selected.');
        }

        $adminId = Session::get('user_id');
        $successCount = 0;
        $failCount = 0;
        $errorMessages = [];

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $manual = DB::table('manual_punch_requests')
                    ->where('id', $id)
                    ->where('status', 'pending')
                    ->whereNull('deleted_at')
                    ->first();

                if ($manual) {
                    // Update request status
                    DB::table('manual_punch_requests')
                        ->where('id', $id)
                        ->update([
                            'status'        => $request->action === 'approve' ? 'approved' : 'rejected',
                            'approved_by'   => $adminId,
                            'approved_at'   => now(),
                            'admin_remarks' => $request->remarks,
                            'updated_at'    => now(),
                        ]);

                    // If approving, update attendance record
                    if ($request->action === 'approve') {
                        $attendance = DB::table('attendances')
                            ->where('employee_id', $manual->employee_id)
                            ->whereDate('date', $manual->request_date)
                            ->first();

                        if (!$attendance) {
                            $attendanceId = DB::table('attendances')->insertGetId([
                                'employee_id' => $manual->employee_id,
                                'date'        => $manual->request_date,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);
                            $attendance = (object) ['id' => $attendanceId];
                        }

                        $time = Carbon::parse($manual->request_date . ' ' . $manual->request_time);
                        $updateData = [];

                        if ($manual->request_type === 'punch_in') {
                            $updateData['punch_in'] = $time;
                        } else {
                            $updateData['punch_out'] = $time;
                            
                            // Calculate working hours for punch_out
                            $currentAttendance = DB::table('attendances')
                                ->where('id', $attendance->id)
                                ->first();
                            
                            if ($currentAttendance && $currentAttendance->punch_in) {
                                $punchIn = Carbon::parse($currentAttendance->punch_in);
                                $punchOut = $time;
                                $workingMinutes = $punchOut->diffInMinutes($punchIn);
                                
                                // Get allocated break time from shift
                                $shift = DB::table('schedule')
                                    ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
                                    ->where('schedule.employee_id', $manual->employee_id)
                                    ->where('schedule.deleted_at', 0)
                                    ->whereDate('schedule.schedule_start_date', '<=', $manual->request_date)
                                    ->whereDate('schedule.schedule_end_date', '>=', $manual->request_date)
                                    ->first();
                                
                                $breakTime = $shift->break_time ?? 0;
                                $workingMinutes = $workingMinutes - $breakTime;
                                $workingHours = round($workingMinutes / 60, 2);
                                $updateData['working_hours'] = $workingHours;
                            }
                        }

                        DB::table('attendances')
                            ->where('id', $attendance->id)
                            ->update($updateData);
                        
                        // Send approval email to employee
                        $this->sendStatusEmail($manual->employee_id, $manual->request_type, $manual->request_date, $manual->request_time, $request->remarks, 'approved');
                    } else {
                        // Send rejection email to employee
                        $this->sendStatusEmail($manual->employee_id, $manual->request_type, $manual->request_date, $manual->request_time, $request->remarks, 'rejected');
                    }

                    $successCount++;
                } else {
                    $failCount++;
                    $errorMessages[] = "Request #{$id} was already processed or not found.";
                }
            }

            DB::commit();

            $message = "Successfully {$request->action}ed {$successCount} request(s).";
            
            if ($failCount > 0) {
                $message .= " {$failCount} request(s) failed.";
                if (!empty($errorMessages)) {
                    Log::warning('Bulk process partial failure: ' . implode(', ', $errorMessages));
                }
            }

            return redirect()->route('admin.manual-punch.index')
                ->with('bulk_success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk process failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            
            return redirect()->route('admin.manual-punch.index')
                ->with('bulk_error', 'Bulk process failed. Please try again.');
        }
    }

    /**
     * Export CSV
     */
    public function export(Request $request)
    {
        $query = DB::table('manual_punch_requests as mpr')
            ->join('allemployees as emp', 'emp.id', '=', 'mpr.employee_id')
            ->leftJoin('allemployees as appr', 'appr.id', '=', 'mpr.approved_by')
            ->whereNull('mpr.deleted_at')
            ->select(
                'mpr.id',
                'emp.employeeid',
                DB::raw("CONCAT(emp.firstname, ' ', emp.lastname) as employee_name"),
                'mpr.request_date',
                'mpr.request_type',
                'mpr.request_time',
                'mpr.reason',
                'mpr.status',
                DB::raw("CONCAT(appr.firstname, ' ', appr.lastname) as approver_name"),
                'mpr.approved_at',
                'mpr.admin_remarks',
                'mpr.created_at'
            );

        // Apply filters if present
        if ($request->status) {
            $query->where('mpr.status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('mpr.request_date', $request->date);
        }

        if ($request->employee_id) {
            $query->where('mpr.employee_id', $request->employee_id);
        }

        if ($request->type) {
            $query->where('mpr.request_type', $request->type);
        }

        $data = $query->orderBy('mpr.created_at', 'desc')->get();

        $filename = 'manual_punch_requests_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'ID', 'Employee ID', 'Employee Name', 'Request Date',
                'Request Type', 'Request Time', 'Reason', 'Status',
                'Approved By', 'Approved At', 'Admin Remarks', 'Created At'
            ]);

            // Data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->employeeid,
                    $row->employee_name,
                    $row->request_date,
                    ucwords(str_replace('_', ' ', $row->request_type)),
                    $row->request_time,
                    $row->reason,
                    ucfirst($row->status),
                    $row->approver_name ?? 'N/A',
                    $row->approved_at ? date('Y-m-d H:i:s', strtotime($row->approved_at)) : 'N/A',
                    $row->admin_remarks ?? 'N/A',
                    date('Y-m-d H:i:s', strtotime($row->created_at))
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send status email to employee (approved/rejected)
     */
    private function sendStatusEmail($employeeId, $requestType, $requestDate, $requestTime, $remarks, $status)
    {
        try {
            $employee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->first();

            if (!$employee || !$employee->email) {
                Log::warning('Employee email not found for employee ID: ' . $employeeId);
                return;
            }

            $data = [
                'employeeName' => $employee->firstname . ' ' . $employee->lastname,
                'requestType' => ucwords(str_replace('_', ' ', $requestType)),
                'requestDate' => Carbon::parse($requestDate)->format('d M Y'),
                'requestTime' => Carbon::parse($requestTime)->format('h:i A'),
                'remarks' => $remarks,
                'status' => $status
            ];

            $subject = $status === 'approved' ? 'Manual Punch Request Approved' : 'Manual Punch Request Rejected';

            Mail::send('emails.manual-punch-status', $data, function ($message) use ($employee, $subject) {
                $message->to($employee->email)
                    ->subject($subject);
            });

            Log::info(ucfirst($status) . ' email sent to employee: ' . $employee->email);
        } catch (\Exception $e) {
            Log::error('Failed to send status email: ' . $e->getMessage());
        }
    }
}