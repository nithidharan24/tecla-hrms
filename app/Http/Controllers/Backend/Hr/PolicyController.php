<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PolicyController extends Controller
{
    // Display all policies
    public function index()
    {
        $branchId = getAdminBranchFilter();
        $departments = DB::table('department')->select('id', 'department')->get();
        $employees = DB::table('allemployees')->select('id', 'firstname', 'lastname', 'email', 'department')->get();

        $policies = DB::table('policies')
            ->select('id', 'policy_name', 'description', 'file_path', 'created_at', 'notify_type', 'acknowledge_required', 'deadline_date', 'file_access', 'employee_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // <CHANGE> Apply branch filter only if branchId exists
        if ($branchId) {
            $policies = $policies->filter(function ($policy) use ($branchId) {
                return DB::table('policies')->where('id', $policy->id)->where('branch_id', $branchId)->exists();
            });
        }

        // Get logs for the logs tab
        $logs = DB::table('policy_logs')
            ->leftJoin('policies', 'policy_logs.policy_id', '=', 'policies.id')
            ->leftJoin('allemployees as user_employees', 'policy_logs.user_id', '=', 'user_employees.id')
            ->leftJoin('allemployees as action_employees', 'policy_logs.employee_id', '=', 'action_employees.id')
            ->select(
                'policy_logs.*',
                'policies.policy_name',
                'user_employees.firstname as user_firstname',
                'user_employees.lastname as user_lastname',
                'action_employees.firstname as employee_firstname',
                'action_employees.lastname as employee_lastname'
            )
            ->orderBy('policy_logs.action_date', 'desc')
            ->get();

        return view('hrms.Employee.Policy.index', compact('policies', 'departments', 'employees', 'logs'));
    }

    // Add a new policy
    public function store(Request $request)
    {
        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->storeAs('policies', $originalName);
            $branchId = Session::get('branch_id');

            $notifyThrough = implode(',', $request->input('notify_through', []));
            $deadlineType = $request->input('deadline_type', 'no_deadline');
            $deadlineDate = ($deadlineType === 'enforce_deadline') ? $request->input('deadline_date') : null;

            // <CHANGE> Get the current user's employee ID first
            $authUser = Auth::user();
            $currentUserEmployeeId = null;
            if ($authUser && $authUser->email) {
                $userEmployee = DB::table('allemployees')
                    ->where('email', $authUser->email)
                    ->select('id')
                    ->first();
                $currentUserEmployeeId = $userEmployee ? $userEmployee->id : null;
            }

            $policyId = DB::table('policies')->insertGetId([
                'policy_name' => $request->input('policy_name'),
                'description' => $request->input('description'),
                'file_access' => $request->input('file_access'),
                'employee_id' => $request->input('employee_id'),
                'branch_id' => $branchId,
                'file_path' => $filePath,
                'notify_type' => $notifyThrough,
                'acknowledge_required' => $request->has('acknowledge_required') ? 1 : 0,
                'deadline_date' => $deadlineDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // <CHANGE> Create acknowledgement record if acknowledgement is required
            if ($request->has('acknowledge_required')) {
                $selectedEmployeeId = $request->input('employee_id');
                DB::table('policy_acknowledgements')->insert([
                    'policy_id' => $policyId,
                    'employee_id' => $selectedEmployeeId,
                    'acknowledged_at' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Log the policy creation
            $this->logPolicyAction($policyId, 'created', 'Policy created successfully', $currentUserEmployeeId);

            if ($request->has('notify_through') && in_array('email', $request->input('notify_through'))) {
                $emailResult = $this->sendPolicyEmails($request, $policyId);
                if ($emailResult) {
                    $this->logPolicyAction($policyId, 'email_sent', 'Policy notification email sent to employees', $currentUserEmployeeId);
                }
            }

            return redirect()->back()->with('success', 'Policy added successfully!');
        } catch (\Exception $e) {
            Log::error("Error storing policy: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add policy: ' . $e->getMessage());
        }
    }

    private function sendPolicyEmails($request, $policyId)
    {
        try {
            $employeeId = $request->input('employee_id');

            $employee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->select('id', 'email', 'firstname', 'lastname')
                ->first();
            if (!$employee) {
                Log::warning("Employee not found for ID: {$employeeId}");
                return false;
            }

            // Get policy details
            $policy = DB::table('policies')->where('id', $policyId)->first();
            if (!$policy) {
                Log::warning("Policy not found for ID: {$policyId}");
                return false;
            }

            Mail::send('emails.policy-acknowledgement', [
                'employee' => $employee,
                'policy' => $policy,
                'acknowledgeRequired' => $policy->acknowledge_required,
                'deadlineDate' => $policy->deadline_date,
                'policyId' => $policyId
            ], function ($message) use ($employee, $policy) {
                $message->to($employee->email)
                    ->subject('Policy Acknowledgement Required: ' . $policy->policy_name);

                if ($policy->file_path && Storage::exists($policy->file_path)) {
                    $message->attach(Storage::path($policy->file_path), [
                        'as' => basename($policy->file_path),
                        'mime' => Storage::mimeType($policy->file_path)
                    ]);
                }
            });

            Log::info("Policy email sent to: {$employee->email} for policy: {$policy->policy_name}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send policy email: " . $e->getMessage());
            return false;
        }
    }

    // Update the specified policy
    public function update(Request $request, $id)
    {
        $request->validate([
            'policy_name' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx',
            'file_access' => 'required|in:employee,role',
            'employee_id' => 'required|integer',
            'notify_through' => 'nullable|array',
            'acknowledge_required' => 'nullable|boolean',
            'deadline_type' => 'nullable|in:no_deadline,enforce_deadline',
            'deadline_date' => 'nullable|date_format:Y-m-d',
        ]);

        try {
            // <CHANGE> Get current user employee ID
            $authUser = Auth::user();
            $currentUserEmployeeId = null;
            if ($authUser && $authUser->email) {
                $userEmployee = DB::table('allemployees')
                    ->where('email', $authUser->email)
                    ->select('id')
                    ->first();
                $currentUserEmployeeId = $userEmployee ? $userEmployee->id : null;
            }

            $notifyThrough = implode(',', $request->input('notify_through', []));
            $deadlineType = $request->input('deadline_type', 'no_deadline');
            $deadlineDate = ($deadlineType === 'enforce_deadline') ? $request->input('deadline_date') : null;

            $updateData = [
                'policy_name' => $request->input('policy_name'),
                'description' => $request->input('description'),
                'file_access' => $request->input('file_access'),
                'employee_id' => $request->input('employee_id'),
                'notify_type' => $notifyThrough,
                'acknowledge_required' => $request->has('acknowledge_required') ? 1 : 0,
                'deadline_date' => $deadlineDate,
                'updated_at' => now()
            ];

            if ($request->hasFile('file')) {
                $oldFile = DB::table('policies')->where('id', $id)->value('file_path');
                if ($oldFile && Storage::exists($oldFile)) {
                    Storage::delete($oldFile);
                }

                $updateData['file_path'] = $request->file('file')->storeAs('policies', $request->file('file')->getClientOriginalName());
            }

            DB::table('policies')->where('id', $id)->update($updateData);

            // Log the policy update
            $this->logPolicyAction($id, 'updated', 'Policy updated successfully', $currentUserEmployeeId);

            return redirect()->route('policies.index')->with('success', 'Policy updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating policy: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update policy: ' . $e->getMessage());
        }
    }

    // Delete a specific policy
    public function destroy($id)
    {
        try {
            // <CHANGE> Get current user employee ID
            $authUser = Auth::user();
            $currentUserEmployeeId = null;
            if ($authUser && $authUser->email) {
                $userEmployee = DB::table('allemployees')
                    ->where('email', $authUser->email)
                    ->select('id')
                    ->first();
                $currentUserEmployeeId = $userEmployee ? $userEmployee->id : null;
            }

            $policy = DB::table('policies')->where('id', $id)->first();
            $filePath = $policy->file_path;

            DB::table('policies')->where('id', $id)->delete();
            // <CHANGE> Also delete related acknowledgements
            DB::table('policy_acknowledgements')->where('policy_id', $id)->delete();

            // Log the policy deletion
            $this->logPolicyAction($id, 'deleted', "Policy '{$policy->policy_name}' deleted", $currentUserEmployeeId);

            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            return redirect()->back()->with('success', 'Policy deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting policy: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete policy.');
        }
    }

    // <CHANGE> Public acknowledge method - accessible without authentication
    public function acknowledgePolicy($id)
    {
        try {
            $policy = DB::table('policies')->where('id', $id)->first();

            if (!$policy) {
                return view('hrms.Employee.Policy.acknowledge', [
                    'success' => false,
                    'message' => 'Policy not found.'
                ]);
            }

            // Get all acknowledgements for this policy
            $acknowledgements = DB::table('policy_acknowledgements')
                ->leftJoin('allemployees', 'policy_acknowledgements.employee_id', '=', 'allemployees.id')
                ->where('policy_acknowledgements.policy_id', $id)
                ->select(
                    'policy_acknowledgements.*',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.email'
                )
                ->get();

            return view('hrms.Employee.Policy.acknowledge', [
                'policy' => $policy,
                'acknowledgements' => $acknowledgements,
                'policyId' => $id
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading acknowledge page: " . $e->getMessage());
            return view('hrms.Employee.Policy.acknowledge', [
                'success' => false,
                'message' => 'Error loading policy.'
            ]);
        }
    }

    // <CHANGE> Mark acknowledgement - new method to handle from the page
    public function markAcknowledged(Request $request, $policyId)
    {
        try {
            $email = $request->input('email');
            $policy = DB::table('policies')->where('id', $policyId)->first();

            if (!$policy) {
                return response()->json(['success' => false, 'message' => 'Policy not found']);
            }

            $employee = DB::table('allemployees')->where('email', $email)->first();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Employee not found']);
            }

            // Update acknowledgement record
            DB::table('policy_acknowledgements')
                ->where('policy_id', $policyId)
                ->where('employee_id', $employee->id)
                ->update([
                    'acknowledged_at' => now(),
                    'updated_at' => now()
                ]);

            // Log the acknowledgement
            $this->logPolicyAction($policyId, 'acknowledged', 'Policy acknowledged by employee', $employee->id);

            return response()->json(['success' => true, 'message' => 'Policy acknowledged successfully']);
        } catch (\Exception $e) {
            Log::error("Error acknowledging policy: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to acknowledge policy']);
        }
    }

    // <CHANGE> Helper method - fixed to not rely on Auth
    private function logPolicyAction($policyId, $action, $details = null, $userId = null)
    {
        try {
            // <CHANGE> Use provided $userId or try to get from auth
            if (!$userId && Auth::check()) {
                $authUser = Auth::user();
                if ($authUser && $authUser->email) {
                    $userEmployee = DB::table('allemployees')
                        ->where('email', $authUser->email)
                        ->select('id')
                        ->first();
                    $userId = $userEmployee ? $userEmployee->id : null;
                }
            }

            DB::table('policy_logs')->insert([
                'policy_id' => $policyId,
                'action' => $action,
                'user_id' => $userId,
                'employee_id' => null,
                'details' => $details,
                'action_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("Policy action logged: Policy ID: $policyId, Action: $action");
        } catch (\Exception $e) {
            Log::error("Error logging policy action: " . $e->getMessage());
        }
    }

    // Download the specified policy file
    public function download($id)
    {
        $policy = DB::table('policies')->where('id', $id)->first();
        if (!$policy || empty($policy->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if (Storage::exists($policy->file_path)) {
            // Log the download action
            $authUser = Auth::user();
            $userId = null;
            if ($authUser && $authUser->email) {
                $userEmployee = DB::table('allemployees')
                    ->where('email', $authUser->email)
                    ->select('id')
                    ->first();
                $userId = $userEmployee ? $userEmployee->id : null;
            }
            $this->logPolicyAction($id, 'downloaded', 'Policy file downloaded', $userId);

            $originalFileName = basename($policy->file_path);
            return Storage::download($policy->file_path, $originalFileName);
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}