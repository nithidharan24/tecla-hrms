<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminLeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Total number of employees
        $totalEmployees = DB::table('allemployees')->count();

        // Today's date
        $today = now()->format('Y-m-d');

        // Present Employees (assuming present means those who are not on leave today)
        $presentEmployees = DB::table('employee_leaves')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->count();

        // Planned Leaves (future leaves)
        $plannedLeaves = DB::table('employee_leaves')
            ->where('from_date', '>', $today)
            ->count();

        // Unplanned Leaves (leaves taken today)
        $unplannedLeaves = DB::table('employee_leaves')
            ->where('from_date', $today)
            ->where('to_date', $today)
            ->count();

        $managerFilter = getManagerTeamFilter();
        
        // Pending Leave Requests
        $pendingRequests = DB::table('employee_leaves')
            ->where('status', 'pending')
            ->count();

        // Pending Permission Requests
        $pendingPermissionRequests = DB::table('employee_permissions')
            ->where('status', 'pending')
            ->count();

        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();

        // Leaves Query
        $leaveQuery = DB::table('employee_leaves')
            ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_leaves.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'designation.designation as designation_name',
                'allemployees.profile_image'
            )
            ->when($request->input('employee_name'), function($q) use ($request) {
                return $q->where(DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname)"), 'like', '%' . $request->input('employee_name') . '%');
            })
            ->when($request->input('leave_type'), function($q) use ($request) {
                return $q->where('employee_leaves.leave_type', $request->input('leave_type'));
            })
            ->when($request->input('leave_status'), function($q) use ($request) {
                return $q->where('employee_leaves.status', $request->input('leave_status'));
            })
            ->when($request->input('from_date'), function($q) use ($request) {
                return $q->whereDate('employee_leaves.from_date', '>=', $request->input('from_date'));
            })
            ->when($request->input('to_date'), function($q) use ($request) {
                return $q->whereDate('employee_leaves.to_date', '<=', $request->input('to_date'));
            });

        // Permissions Query
        $permissionQuery = DB::table('employee_permissions')
            ->join('allemployees', 'employee_permissions.employee_id', '=', 'allemployees.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_permissions.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'designation.designation as designation_name',
                'allemployees.profile_image'
            )
            ->when($request->input('employee_name'), function($q) use ($request) {
                return $q->where(DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname)"), 'like', '%' . $request->input('employee_name') . '%');
            })
            ->when($request->input('leave_status'), function($q) use ($request) {
                return $q->where('employee_permissions.status', $request->input('leave_status'));
            })
            ->when($request->input('from_date'), function($q) use ($request) {
                return $q->whereDate('employee_permissions.permission_date', '>=', $request->input('from_date'));
            })
            ->when($request->input('to_date'), function($q) use ($request) {
                return $q->whereDate('employee_permissions.permission_date', '<=', $request->input('to_date'));
            });

        // Apply department/branch filters
        if ($branchFilter) {
            $leaveQuery->where('allemployees.branch_id', $branchFilter);
            $permissionQuery->where('allemployees.branch_id', $branchFilter);
        }

        if ($departmentFilter) {
            $leaveQuery->where('allemployees.department', $departmentFilter);
            $permissionQuery->where('allemployees.department', $departmentFilter);
        }

        // Manager filter — only show their team
        if ($managerFilter) {
            $leaveQuery->where('allemployees.manager_id', $managerFilter);
            $permissionQuery->where('allemployees.manager_id', $managerFilter);
        }

        // Filter by request type
        if ($request->input('request_type') == 'permission') {
            $leaveQuery->whereRaw('1=0'); // Don't show leaves
        } elseif ($request->input('request_type') == 'leave') {
            $permissionQuery->whereRaw('1=0'); // Don't show permissions
        }

        // Execute queries
        $leaves = $leaveQuery->get();
        $employeePermissions = $permissionQuery->get();

        // Today's leave applications
        $todayApplications = DB::table('employee_leaves')
            ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_leaves.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'designation.designation as designation_name',
                'allemployees.profile_image'
            )
            ->whereDate('employee_leaves.created_at', now()->format('Y-m-d'))
            ->get();

        // Today's permission applications
        $todayPermissionApplications = DB::table('employee_permissions')
            ->join('allemployees', 'employee_permissions.employee_id', '=', 'allemployees.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_permissions.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'designation.designation as designation_name',
                'allemployees.profile_image'
            )
            ->whereDate('employee_permissions.created_at', now()->format('Y-m-d'))
            ->get();

        return view('hrms.Employee.AdminLeaves.index', compact(
            'totalEmployees',
            'presentEmployees',
            'plannedLeaves',
            'unplannedLeaves',
            'todayApplications',
            'todayPermissionApplications',
            'pendingRequests',
            'pendingPermissionRequests',
            'leaves',
            'employeePermissions'
        ));
    }

    public function updatePermissionStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,declined'
        ]);
    
        $permission = DB::table('employee_permissions')->where('id', $id)->first();
    
        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }
    
        // ✅ Update the permission status
        DB::table('employee_permissions')
            ->where('id', $id)
            ->update(['status' => $request->status]);
    
        // ✅ Trigger deduction logic only if approved
        if ($request->status === 'approved') {
            $hoursColumn = property_exists($permission, 'hours') ? $permission->hours : ($permission->permission_hours ?? 0);
            $this->deductPermissionHours($permission->employee_id, $hoursColumn);
        }
    
        return redirect()->route('team-leaves.index')->with('success', 'Leave updated successfully!');
    }
    

    // Add permission show method
    public function showPermission($id)
    {
        $permission = DB::table('employee_permissions')
            ->join('allemployees', 'employee_permissions.employee_id', '=', 'allemployees.id')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_permissions.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                'designation.designation as designation_name',
                'allemployees.profile_image',
                'allemployees.employeeid',
                'allemployees.email',
                'allemployees.phone'
            )
            ->where('employee_permissions.id', $id)
            ->first();

        if (!$permission) {
            return redirect()->route('admin-leaves.index')->with('error', 'Permission not found');
        }

        return view('hrms.Employee.AdminLeaves.show-permission', compact('permission'));
    }
    public function create()
    {
        // Fetch employee list to populate the select box
        return view('hrms.Employee.AdminLeaves.create');
    }

    // Handle form submission
    public function store(Request $request)
    {
        // Insert leave record into the database
        DB::table('admin_leaves')->insert([
            'employee_name' => $request->employee_name,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'no_of_days' => $request->num_days,
            'remaining_leaves' => $request->remaining_leaves,
            'leave_reason' => $request->leave_reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin-leaves.index')->with('success', 'Leave added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Retrieve the leave using DB facade instead of model
        $leave = DB::table('employee_leaves')->where('id', $id)->first();
    
        if (!$leave) {
            return redirect()->route('employee-leaves.index')->with('error', 'Leave not found!');
        }
    
        // Fetch the employee using the 'employee_id' from employee_leaves table
        $employee = DB::table('allemployees')->where('id', $leave->employee_id)->first();
    
        if (!$employee) {
            return redirect()->route('admin-leaves.index')->with('error', 'Employee not found!');
        }
    
        // Now you can fetch the employeeid from the employee record
        $employeeid = $employee->employeeid;
    
        return view('hrms.Employee.AdminLeaves.edit', compact('leave', 'employee', 'employeeid'));
    }
    
    
    public function update(Request $request, $id)
    {
       
    
        // Update the leave record directly using DB facade
        $affected = DB::table('employee_leaves')
            ->where('id', $id)
            ->update([
                'employee_name' => $request->input('employee_name'),
                'leave_type' => $request->input('leave_type'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
                'no_of_days' => $request->input('num_days'),
                'remaining_leaves' => $request->input('remaining_leaves'),
                'leave_reason' => $request->input('leave_reason'),
            ]);
    
        if ($affected) {
            return redirect()->route('admin-leaves.index')->with('success', 'Leave updated successfully!');
        } else {
            return redirect()->route('admin-leaves.index')->with('error', 'Failed to update leave.');
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Using DB method to delete the leave record
        DB::table('admin_leaves')->where('id', $id)->delete();
    
        // Redirect back to the leaves index with a success message
        return redirect()->route('admin-leaves.index')->with('success', 'Leave deleted successfully.');
    }


public function leaveStatistics()
{
    // Total number of employees
    $totalEmployees = DB::table('allemployees')->count();

    // Today's date
    $today = now()->format('Y-m-d');

    // Present Employees (assuming present means those who are not on leave today)
    $presentEmployees = DB::table('employee_leaves')
        ->where('from_date', '<=', $today)
        ->where('to_date', '>=', $today)
        ->count();

    // Planned Leaves (future leaves)
    $plannedLeaves = DB::table('employee_leaves')
        ->where('from_date', '>', $today)
        ->count();

    // Unplanned Leaves (leaves added today)
    $unplannedLeaves = DB::table('employee_leaves')
        ->whereDate('created_at', $today)
        ->count();

    // Pending Requests
    $pendingRequests = DB::table('employee_leaves')
        ->where('status', 'pending')
        ->count();

    return view('hrms.Employee.AdminLeaves.index', compact(
        'totalEmployees',
        'presentEmployees',
        'plannedLeaves',
        'unplannedLeaves',
        'pendingRequests'
    ));
}

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,approved,declined'
    ]);

    $leave = DB::table('employee_leaves')->where('id', $id)->first();

    if (!$leave) {
        return response()->json(['success' => false, 'message' => 'Leave not found'], 404);
    }

    $previousStatus = $leave->status;
    $newStatus      = $request->status;

    // Deduct balance on first approval
    if ($newStatus === 'approved' && $previousStatus !== 'approved') {
        $this->deductLeaves($leave->employee_id, $leave->leave_type, $leave->no_of_days, $leave->id);
    }

    // Restore balance if reverting from approved
    if ($previousStatus === 'approved' && $newStatus !== 'approved') {
        $paidWas = (int)($leave->paid_days ?? $leave->no_of_days);
        $this->addLeaves($leave->employee_id, $leave->leave_type, $paidWas);
        DB::table('employee_leaves')->where('id', $id)->update(['paid_days' => 0, 'lop_days' => 0]);
    }

    DB::table('employee_leaves')->where('id', $id)->update(['status' => $newStatus]);

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Leave updated successfully']);
    }

    return redirect()->route('team-leaves.index')->with('success', 'Leave updated successfully!');
}
private function deductLeaves($employeeId, $leaveType, $days, $leaveId = null)
{
    $days = (int) $days;

    // 1️⃣ LOP leave type — all days are unpaid
    if (strtolower($leaveType) === 'lop') {
        if ($leaveId) {
            DB::table('employee_leaves')->where('id', $leaveId)->update(['paid_days' => 0, 'lop_days' => $days]);
        }
        $this->recordLopDays($employeeId, $days);
        return;
    }

    // 2️⃣ Read max_allowed from annual_leaves (how many days are paid per leave application)
    $maxAllowed = (int)(DB::table('annual_leaves')->value('max_allowed') ?? 0);

    // paid_days = days up to max_allowed; lop_days = anything beyond
    if ($maxAllowed > 0) {
        $paidDays = min($days, $maxAllowed);
        $lopDays  = $days - $paidDays;
    } else {
        // max_allowed not configured — all days are paid
        $paidDays = $days;
        $lopDays  = 0;
    }

    // 3️⃣ Deduct paid_days from balance
    if ($paidDays > 0) {
        DB::table('employee_leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->update([
                'used_days'      => DB::raw("used_days + {$paidDays}"),
                'remaining_days' => DB::raw("GREATEST(0, remaining_days - {$paidDays})"),
                'updated_at'     => now(),
            ]);
    }

    // 4️⃣ Record LOP days if any
    if ($lopDays > 0) {
        $this->recordLopDays($employeeId, $lopDays);
    }

    // 5️⃣ Store paid/lop split on the leave record
    if ($leaveId) {
        DB::table('employee_leaves')->where('id', $leaveId)->update([
            'paid_days' => $paidDays,
            'lop_days'  => $lopDays,
        ]);
    }
}

private function deductPermissionHours($employeeId, $hoursTaken)
{
    // 1️⃣ Fetch allowed permission hours for the month
    $maxAllowedHours = DB::table('annual_leaves')->value('permission_hours') ?? 0;

    // 2️⃣ Get already approved permission hours for this month
    $month = now()->format('Y-m');
    $usedHours = DB::table('employee_permissions')
        ->where('employee_id', $employeeId)
        ->where('status', 'approved')
        ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
        ->sum('duration');

    // 3️⃣ Calculate total taken hours including current approval
    $totalHours = $usedHours + $hoursTaken;

    // 4️⃣ Find exceeded hours
    $exceededHours = max(0, $totalHours - $maxAllowedHours);

    // 5️⃣ Convert exceeded hours to LOP days (8 hrs = 1 day)
    $lopToAdd = $exceededHours > 0 ? ($exceededHours / 8) : 0;

    if ($lopToAdd > 0) {
        // 6️⃣ Get existing LOP for this employee and month (if any)
        $existingLop = DB::table('employee_lop_records')
            ->where('employee_id', $employeeId)
            ->where('month', $month)
            ->value('lop_days') ?? 0;

        // 7️⃣ Update the total LOP days
        $newLop = $existingLop + $lopToAdd;

        DB::table('employee_lop_records')
            ->updateOrInsert(
                ['employee_id' => $employeeId, 'month' => $month],
                ['lop_days' => $newLop, 'updated_at' => now(), 'created_at' => now()]
            );
    }
}

private function recordLopDays($employeeId, $lopDays)
{
    $month = now()->format('Y-m');

    DB::table('employee_lop_records')->updateOrInsert(
        [
            'employee_id' => $employeeId,
            'month' => $month,
        ],
        [
            'lop_days' => DB::raw("lop_days + $lopDays"),
            'updated_at' => now(),
            'created_at' => now(),
        ]
    );
}


private function addLeaves($employeeId, $leaveType, $days)
{
    DB::table('employee_leave_balances')
        ->where('employee_id', $employeeId)
        ->where('leave_type', $leaveType)
        ->update([
            'used_days'      => DB::raw('GREATEST(0, used_days - ' . (int)$days . ')'),
            'remaining_days' => DB::raw('remaining_days + ' . (int)$days),
            'updated_at'     => now(),
        ]);
}

/**
 * Display the specified leave resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function show($id)
{
    // Retrieve the leave with employee details using joins
    $leave = DB::table('employee_leaves')
        ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
        ->join('designation', 'allemployees.designation', '=', 'designation.id')
        ->select(
            'employee_leaves.*',
            DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
            'designation.designation as designation_name',
            'allemployees.profile_image',
            'allemployees.employeeid',
            'allemployees.email',
            'allemployees.phone'
        )
        ->where('employee_leaves.id', $id)
        ->first();

    if (!$leave) {
        return redirect()->route('admin-leaves.index')->with('error', 'Leave not found');
    }

    // Calculate working days between dates (excluding weekends)
    $fromDate = \Carbon\Carbon::parse($leave->from_date);
    $toDate = \Carbon\Carbon::parse($leave->to_date);
    $noOfDays = $fromDate->diffInDaysFiltered(function($date) {
        return !$date->isWeekend();
    }, $toDate) + 1; // Add 1 to include the start date

    // Get file info for the tooltip
    $fileInfo = $this->getFileInfo($leave->medical_certificate);

    return view('hrms.Employee.AdminLeaves.show', compact('leave', 'noOfDays', 'fileInfo'));
}

public function downloadMedicalCertificate($id)
{
    $leave = DB::table('employee_leaves')->where('id', $id)->first();
    
    if (!$leave || !$leave->medical_certificate) {
        return redirect()->back()->with('error', 'Medical certificate not found.');
    }

    $filePath = storage_path('app/public/' . $leave->medical_certificate);
    
    if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File not found.');
    }

    $fileName = 'medical_certificate_' . $leave->employee_name . '_' . $leave->from_date . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    
    return response()->download($filePath, $fileName);
}

public function viewMedicalCertificate($id)
{
    $leave = DB::table('employee_leaves')->where('id', $id)->first();
    
    if (!$leave || !$leave->medical_certificate) {
        return response()->json(['error' => 'Medical certificate not found.'], 404);
    }

    $filePath = storage_path('app/public/' . $leave->medical_certificate);
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'File not found.'], 404);
    }

    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeType = $this->getMimeType($fileExtension);

    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
    ]);
}

private function getMimeType($extension)
{
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
}

// Helper method to get file info (for tooltip)
private function getFileInfo($filePath)
{
    if (!$filePath) {
        return 'No file available';
    }

    $fullPath = storage_path('app/public/' . $filePath);
    
    if (!file_exists($fullPath)) {
        return 'File not found';
    }

    $fileSize = filesize($fullPath);
    $fileExtension = pathinfo($fullPath, PATHINFO_EXTENSION);
    $fileModified = date('d M Y H:i', filemtime($fullPath));

    $sizeFormatted = $this->formatFileSize($fileSize);

    return "Type: " . strtoupper($fileExtension) . " | Size: $sizeFormatted | Modified: $fileModified";
}

private function formatFileSize($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
public function bulkLeaveUpdate(Request $request)
{
    $ids = $request->ids;
    $status = $request->status;

    foreach ($ids as $id) {
        $req = new Request(['status' => $status]);
        $this->updateStatus($req, $id); // Using your existing leave function
    }

    return response()->json(['success' => true]);
}
public function bulkPermissionUpdate(Request $request)
{
    $ids = $request->ids;
    $status = $request->status;

    foreach ($ids as $id) {
        $req = new Request(['status' => $status]);
        $this->updatePermissionStatus($req, $id); // Using your existing permission function
    }

    return response()->json(['success' => true]);
}

}
