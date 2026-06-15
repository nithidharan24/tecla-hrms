<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApiLeaveController extends Controller
{
    /**
     * Get employee leave details (balances, leaves list, permissions list).
     */
    public function index(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer', // Database auto-increment ID
        ]);

        $employeeId = $request->employee_id;

        // Check if employee exists
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        // Get leave balances
        $leaveBalances = $this->getLeaveBalances($employeeId);

        // Get leave requests history
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get permissions history
        $permissions = DB::table('employee_permissions')
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'balances' => $leaveBalances,
                'leaves' => $leaves,
                'permissions' => $permissions
            ]
        ], 200);
    }

    /**
     * Apply for Leave.
     */
    public function applyLeave(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:allemployees,employeeid', // Employee code/ID (e.g. EMP001)
            'leave_type' => 'required|in:Medical Leave,Hospitalisation,Maternity Leave,Casual Leave,LOP,Paternity Leave,Sick',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'num_days' => 'required|integer|min:1',
            'leave_reason' => 'required|string|max:255',
            'medical_certificate' => 'nullable', // supports file upload or base64 string
        ]);

        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id', 'team_lead_id', 'manager_id', 'firstname', 'lastname', 'employeeid', 'email']);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        // Handle File upload (multipart or base64)
        $medicalCertificatePath = null;
        if ($request->hasFile('medical_certificate')) {
            $file = $request->file('medical_certificate');
            $medicalCertificatePath = $file->storeAs(
                'medical_certificates',
                time() . '_' . $file->getClientOriginalName(),
                'public'
            );
        } elseif ($request->medical_certificate && is_string($request->medical_certificate)) {
            // Handle base64 string
            $fileData = $request->medical_certificate;
            if (strpos($fileData, 'data:') === 0) {
                $fileData = substr($fileData, strpos($fileData, ',') + 1);
            }
            $decodedData = base64_decode($fileData);
            $fileName = 'cert_' . time() . '_' . uniqid() . '.jpg';
            Storage::disk('public')->put('medical_certificates/' . $fileName, $decodedData);
            $medicalCertificatePath = 'medical_certificates/' . $fileName;
        }

        $remainingLeaves = $this->calculateRemainingLeaves(
            $employee->id,
            $request->leave_type,
            $request->num_days
        );

        if ($remainingLeaves < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient leave balance for this type.'
            ], 400);
        }

        $leaveId = DB::table('employee_leaves')->insertGetId([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'no_of_days' => $request->num_days,
            'remaining_leaves' => $remainingLeaves,
            'leave_reason' => $request->leave_reason,
            'medical_certificate' => $medicalCertificatePath,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notify TL, HR, and Manager
        $this->notifyApproversOnApplication('leave', $employee->id, [
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'num_days' => $request->num_days,
            'leave_reason' => $request->leave_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully!',
            'data' => [
                'leave_id' => $leaveId,
                'remaining_leaves' => $remainingLeaves
            ]
        ], 201);
    }

    /**
     * Apply for Permission.
     */
    public function applyPermission(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:allemployees,employeeid',
            'permission_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration' => 'required|numeric',
            'permission_reason' => 'required|string|max:255',
            'supporting_document' => 'nullable', // supports file upload or base64 string
        ]);

        $employee = DB::table('allemployees')
            ->where('employeeid', $request->employee_id)
            ->first(['id', 'team_lead_id', 'manager_id', 'firstname', 'lastname', 'employeeid', 'email']);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $supportingDocumentPath = null;
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $supportingDocumentPath = $file->storeAs(
                'supporting_documents',
                time() . '_permission_' . $file->getClientOriginalName(),
                'public'
            );
        } elseif ($request->supporting_document && is_string($request->supporting_document)) {
            // Handle base64 string
            $fileData = $request->supporting_document;
            if (strpos($fileData, 'data:') === 0) {
                $fileData = substr($fileData, strpos($fileData, ',') + 1);
            }
            $decodedData = base64_decode($fileData);
            $fileName = 'perm_' . time() . '_' . uniqid() . '.jpg';
            Storage::disk('public')->put('supporting_documents/' . $fileName, $decodedData);
            $supportingDocumentPath = 'supporting_documents/' . $fileName;
        }

        $permissionId = DB::table('employee_permissions')->insertGetId([
            'employee_id' => $employee->id,
            'permission_date' => $request->permission_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $request->duration,
            'permission_reason' => $request->permission_reason,
            'supporting_document' => $supportingDocumentPath,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notify TL, HR, and Manager
        $this->notifyApproversOnApplication('permission', $employee->id, [
            'permission_date' => $request->permission_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $request->duration,
            'permission_reason' => $request->permission_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission request submitted successfully!',
            'data' => [
                'permission_id' => $permissionId
            ]
        ], 201);
    }

    /* Helper Logic */

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

    private function notifyApproversOnApplication(string $type, int $employeeId, array $details): void
    {
        try {
            $employee = DB::table('allemployees')->where('id', $employeeId)->first();
            if (!$employee) {
                return;
            }

            $employeeName  = $employee->firstname . ' ' . $employee->lastname;
            $employeeEmpId = $employee->employeeid;
            $recipients    = $this->buildApproverRecipients($employee);

            if (empty($recipients)) {
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
            }

        } catch (\Exception $e) {
            Log::error("notifyApproversOnApplication failed for {$type}: " . $e->getMessage());
        }
    }

    private function buildApproverRecipients(object $employee): array
    {
        $recipients = [];

        if (!empty($employee->team_lead_id)) {
            $tl = DB::table('allemployees')->where('id', $employee->team_lead_id)->first();
            if ($tl && !empty($tl->email)) {
                $recipients[] = [
                    'email' => $tl->email,
                    'name'  => $tl->firstname . ' ' . $tl->lastname,
                    'role'  => 'Team Lead',
                ];
            }
        }

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
        }

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

        return $recipients;
    }
}
