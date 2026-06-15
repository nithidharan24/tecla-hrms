<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // Add this import

class ClearanceController extends Controller
{
    /**
     * Display a listing of the clearance requests.
     */
 /**
 * Display a listing of the clearance requests.
 */
public function index(Request $request)
{
    $query = DB::table('terminations')
        ->join('allemployees', 'allemployees.id', '=', 'terminations.employee_id')
        ->leftJoin('department', 'department.id', '=', 'allemployees.department')
        ->leftJoin('designation', 'designation.id', '=', 'allemployees.designation')
        ->select(
            'terminations.*',
            'allemployees.employeeid',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.profile_image',
            'allemployees.email',
            'allemployees.joiningdate',
            'department.department as department_name',
            'designation.designation as designation_name'
        )
        ->where('terminations.deleted_at', 0);

    // Filter by clearance status
    if ($request->has('status_filter') && $request->status_filter != 'all') {
        $status = $request->status_filter;
        
        if ($status == 'pending') {
            $query->where(function($q) {
                $q->where('terminations.hr_status', 'pending')
                  ->orWhere('terminations.manager_status', 'pending')
                  ->orWhere('terminations.team_lead_status', 'pending')
                  ->orWhere('terminations.support_manager_status', 'pending');
            });
        } elseif ($status == 'completed') {
            $query->where('terminations.hr_status', 'approved')
                  ->where('terminations.manager_status', 'approved')
                  ->where('terminations.team_lead_status', 'approved')
                  ->where('terminations.support_manager_status', 'approved');
        } elseif ($status == 'rejected') {
            $query->where(function($q) {
                $q->where('terminations.hr_status', 'rejected')
                  ->orWhere('terminations.manager_status', 'rejected')
                  ->orWhere('terminations.team_lead_status', 'rejected')
                  ->orWhere('terminations.support_manager_status', 'rejected');
            });
        }
    }

    // Filter by specific role pending approvals (for role-based view)
    if ($request->has('role_filter') && $request->role_filter != 'all') {
        $role = $request->role_filter;
        $query->where("terminations.{$role}_status", 'pending');
    }

    $terminations = $query->orderBy('terminations.termination_date', 'desc')
                        ->get();

    // Get approver information for all terminations and add it to each termination
    $terminations = $terminations->map(function($termination) {
        $termination->approvers = $this->getApproverNames($termination);
        return $termination;
    });

    $clearanceStats = $this->getClearanceStats();

    return view('hrms.performance.Clearance.index', compact('terminations', 'clearanceStats'));
}
/**
 * Bulk update all pending clearances for a single employee
 */
public function bulkUpdateSingle(Request $request, $id)
{
    $request->validate([
        'remarks' => 'nullable|string|max:500'
    ]);

    $termination = DB::table('terminations')->where('id', $id)->first();
    
    if (!$termination) {
        return redirect()->back()->with('error', 'Termination record not found!');
    }

    $currentUser = $this->getCurrentUserInfo();
    $remarks = $request->input('remarks');
    $updatedRoles = [];

    // Update all pending clearances
    $roles = ['hr', 'manager', 'team_lead', 'support_manager'];
    
    foreach ($roles as $role) {
        if ($termination->{$role . '_status'} === 'pending') {
            DB::table('terminations')->where('id', $id)->update([
                $role . '_status' => 'approved',
                $role . '_remarks' => $remarks,
                $role . '_approved_at' => now(),
                $role . '_approved_by' => $currentUser['id'],
                $role . '_approved_by_type' => $currentUser['type'],
                'updated_at' => now()
            ]);
            $updatedRoles[] = $role;
        }
    }

    // Check if all clearances are approved
    $this->checkAllClearancesApproved($id);

    if (count($updatedRoles) > 0) {
        return redirect()->route('clearance.index')->with('success', 'Approved ' . count($updatedRoles) . ' pending clearances for employee!');
    } else {
        return redirect()->route('clearance.index')->with('info', 'No pending clearances to approve for this employee.');
    }
}
    /**
     * Show clearance details and approval interface
     */
    public function show($id)
    {
        $termination = DB::table('terminations')
            ->join('allemployees', 'allemployees.id', '=', 'terminations.employee_id')
            ->leftJoin('department', 'department.id', '=', 'allemployees.department')
            ->leftJoin('designation', 'designation.id', '=', 'allemployees.designation')
            ->select(
                'terminations.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'allemployees.email',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->where('terminations.id', $id)
            ->where('terminations.deleted_at', 0)
            ->first();

        if (!$termination) {
            return redirect()->route('clearance.index')->with('error', 'Termination record not found!');
        }

        // Get approver names with role information
        $approvers = $this->getApproverNames($termination);

        return view('hrms.performance.Clearance.show', compact('termination', 'approvers'));
    }

    /**
     * Update clearance status for a specific role
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'clearance_type' => 'required|in:hr,manager,team_lead,support_manager',
            'status' => 'required|in:approved,rejected',
            'remarks' => 'nullable|string|max:500'
        ]);

        $termination = DB::table('terminations')->where('id', $id)->first();
        
        if (!$termination) {
            return redirect()->back()->with('error', 'Termination record not found!');
        }

        $clearanceType = $request->input('clearance_type');
        $status = $request->input('status');
        $remarks = $request->input('remarks');

        // Get current user information
        $currentUser = $this->getCurrentUserInfo();

        $updateData = [
            $clearanceType . '_status' => $status,
            $clearanceType . '_remarks' => $remarks,
            $clearanceType . '_approved_at' => now(),
            $clearanceType . '_approved_by' => $currentUser['id'],
            $clearanceType . '_approved_by_type' => $currentUser['type'], // 'admin' or 'employee'
            'updated_at' => now()
        ];

        DB::table('terminations')->where('id', $id)->update($updateData);

        // Check if all clearances are approved
        $this->checkAllClearancesApproved($id);

        // Log the action
        Log::info("Clearance {$clearanceType} {$status} for termination ID: {$id} by {$currentUser['type']} ID: " . $currentUser['id']);

        $roleNames = [
            'hr' => 'HR',
            'manager' => 'Manager',
            'team_lead' => 'Team Lead',
            'support_manager' => 'Support Manager'
        ];

        return redirect()->route('clearance.show', $id)->with('success', $roleNames[$clearanceType] . ' clearance ' . $status . ' successfully!');
    }

    /**
     * Get current user information (admin or employee)
     */
    private function getCurrentUserInfo()
    {
        $role = Session::get('role');
        
        if ($role === 'admin') {
            return [
                'id' => Session::get('admin_id'),
                'type' => 'admin',
                'name' => Session::get('admin_name')
            ];
        } elseif ($role === 'employee') {
            return [
                'id' => Session::get('user_id'),
                'type' => 'employee',
                'name' => Session::get('first_name') . ' ' . Session::get('last_name')
            ];
        } else {
            return [
                'id' => 0,
                'type' => 'unknown',
                'name' => 'Unknown User'
            ];
        }
    }

    /**
     * Get approver names for display with proper user type handling
     */
    private function getApproverNames($termination)
    {
        $approvers = [];
        $roles = ['hr', 'manager', 'team_lead', 'support_manager'];
        
        foreach ($roles as $role) {
            $approvedBy = $termination->{$role . '_approved_by'};
            $approvedByType = $termination->{$role . '_approved_by_type'} ?? 'admin'; // Default to admin for backward compatibility
            
            if ($approvedBy) {
                if ($approvedByType === 'admin') {
                    $user = DB::table('admin_access')->where('id', $approvedBy)->first();
                    $approvers[$role] = [
                        'name' => $user ? $user->name : 'Unknown Admin',
                        'type' => 'admin',
                        'email' => $user ? $user->email : 'N/A'
                    ];
                } elseif ($approvedByType === 'employee') {
                    $user = DB::table('allemployees')->where('id', $approvedBy)->first();
                    $approvers[$role] = [
                        'name' => $user ? $user->firstname . ' ' . $user->lastname : 'Unknown Employee',
                        'type' => 'employee',
                        'email' => $user ? $user->email : 'N/A'
                    ];
                } else {
                    $approvers[$role] = [
                        'name' => 'Unknown User',
                        'type' => 'unknown',
                        'email' => 'N/A'
                    ];
                }
            } else {
                $approvers[$role] = null;
            }
        }
        
        return $approvers;
    }

    /**
     * Get clearance statistics
     */
    public function getClearanceStats()
    {
        $stats = DB::table('terminations')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN hr_status = "pending" OR manager_status = "pending" OR team_lead_status = "pending" OR support_manager_status = "pending" THEN 1 ELSE 0 END) as pending_total'),
                DB::raw('SUM(CASE WHEN hr_status = "pending" THEN 1 ELSE 0 END) as pending_hr'),
                DB::raw('SUM(CASE WHEN manager_status = "pending" THEN 1 ELSE 0 END) as pending_manager'),
                DB::raw('SUM(CASE WHEN team_lead_status = "pending" THEN 1 ELSE 0 END) as pending_team_lead'),
                DB::raw('SUM(CASE WHEN support_manager_status = "pending" THEN 1 ELSE 0 END) as pending_support_manager'),
                DB::raw('SUM(CASE WHEN hr_status = "approved" AND manager_status = "approved" AND team_lead_status = "approved" AND support_manager_status = "approved" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN hr_status = "rejected" OR manager_status = "rejected" OR team_lead_status = "rejected" OR support_manager_status = "rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->where('deleted_at', 0)
            ->first();

        return $stats;
    }

    /**
     * Check if all clearances are approved and update overall status
     */
    private function checkAllClearancesApproved($terminationId)
    {
        $termination = DB::table('terminations')->where('id', $terminationId)->first();
        
        if ($termination->hr_status === 'approved' &&
            $termination->manager_status === 'approved' &&
            $termination->team_lead_status === 'approved' &&
            $termination->support_manager_status === 'approved') {
            
            DB::table('terminations')->where('id', $terminationId)->update([
                'clearance_completed_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('All clearances completed for termination ID: ' . $terminationId);
        }
    }

    /**
     * Bulk update clearance statuses
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'clearance_ids' => 'required|array',
            'clearance_ids.*' => 'exists:terminations,id',
            'clearance_type' => 'required|in:hr,manager,team_lead,support_manager',
            'status' => 'required|in:approved,rejected',
            'remarks' => 'nullable|string|max:500'
        ]);

        $clearanceIds = $request->input('clearance_ids');
        $clearanceType = $request->input('clearance_type');
        $status = $request->input('status');
        $remarks = $request->input('remarks');

        // Get current user information
        $currentUser = $this->getCurrentUserInfo();

        foreach ($clearanceIds as $id) {
            $updateData = [
                $clearanceType . '_status' => $status,
                $clearanceType . '_remarks' => $remarks,
                $clearanceType . '_approved_at' => now(),
                $clearanceType . '_approved_by' => $currentUser['id'],
                $clearanceType . '_approved_by_type' => $currentUser['type'],
                'updated_at' => now()
            ];

            DB::table('terminations')->where('id', $id)->update($updateData);
            $this->checkAllClearancesApproved($id);
        }

        $roleNames = [
            'hr' => 'HR',
            'manager' => 'Manager',
            'team_lead' => 'Team Lead',
            'support_manager' => 'Support Manager'
        ];

        return redirect()->route('clearance.index')->with('success', count($clearanceIds) . ' ' . $roleNames[$clearanceType] . ' clearances ' . $status . ' successfully!');
    }

    /**
     * Export clearance report
     */
    public function exportReport(Request $request)
    {
        $query = DB::table('terminations')
            ->join('allemployees', 'allemployees.id', '=', 'terminations.employee_id')
            ->leftJoin('department', 'department.id', '=', 'allemployees.department')
            ->select(
                'terminations.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'department.department as department_name'
            )
            ->where('terminations.deleted_at', 0);

        if ($request->has('export_type')) {
            switch ($request->export_type) {
                case 'pending':
                    $query->where(function($q) {
                        $q->where('terminations.hr_status', 'pending')
                          ->orWhere('terminations.manager_status', 'pending')
                          ->orWhere('terminations.team_lead_status', 'pending')
                          ->orWhere('terminations.support_manager_status', 'pending');
                    });
                    break;
                case 'completed':
                    $query->where('terminations.hr_status', 'approved')
                          ->where('terminations.manager_status', 'approved')
                          ->where('terminations.team_lead_status', 'approved')
                          ->where('terminations.support_manager_status', 'approved');
                    break;
            }
        }

        $clearances = $query->orderBy('terminations.termination_date', 'desc')
                          ->get();

        // You can implement CSV or Excel export here
        // For now, return JSON (you can implement proper export later)
        return response()->json($clearances);
    }
}