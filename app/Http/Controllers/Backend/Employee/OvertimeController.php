<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        // Get per_page from the request, default to 100
        $perPage = $request->input('per_page', 100);
        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();

        
        
        // Fetch overtime records with pagination, excluding soft deleted ones (deleted_at != 1)
        $overtimeRecords = DB::table('overtime')
            ->join('allemployees', 'overtime.employee_name', '=', 'allemployees.id') // Join with allemployees
            ->where('overtime.deleted_at', '!=', 1)  // Exclude records where deleted_at is 1
            ->orWhereNull('overtime.deleted_at')      // Or where deleted_at is NULL
            ->select('overtime.*', DB::raw("CONCAT(allemployees.`firstname`, ' ', allemployees.`lastname`) AS employee_name")) // Select concatenated name
            ->orderBy('overtime.created_at', 'desc')
            ->paginate($perPage);
              // Apply filters
    
if ($branchFilter) {
    $overtimeRecords->where('allemployees.branch_id', $branchFilter);
}

if ($departmentFilter) {
    $overtimeRecords->where('allemployees.department', $departmentFilter);
}

    
// Manager filter — only show their team
if ($managerFilter) {
    $overtimeRecords->where('allemployees.manager_id', $managerFilter);
}
    
        // Calculate summary, excluding soft deleted records
        $overtimeSummary = [
            'employees' => DB::table('overtime')->where('deleted_at', '!=', 1)->orWhereNull('deleted_at')->distinct('employee_name')->count(),
            'hours' => DB::table('overtime')->where('deleted_at', '!=', 1)->orWhereNull('deleted_at')->sum('overtime_hours'),
            'pending_requests' => DB::table('overtime')->where('status', 'Pending')->where('deleted_at', '!=', 1)->orWhereNull('deleted_at')->count(),
            'rejected_requests' => DB::table('overtime')->where('status', 'Rejected')->where('deleted_at', '!=', 1)->orWhereNull('deleted_at')->count(),
        ];
        $employees = DB::table('allemployees')->get(); // Fetch employees from the database
        $firstEmployee = $employees->first(); // Get the first employee from the collection
    
        return view('hrms.Employee.overtime.index', compact('overtimeRecords', 'overtimeSummary', 'perPage','firstEmployee'));
    }
    


    
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'employee_name' => 'required|string',
            'overtime_date' => 'required|date',
            'overtime_hours' => 'required|numeric|min:0.1',
            'overtime_type' => 'required|string',
            'approved_by' => 'required|string',
            'description' => 'required|string|max:255',
            'status' => 'nullable|in:Approved,Pending,Rejected', // Allow null and ensure it's either 'Approved' or 'Pending' or 'Rejected'
        ]);
    
        // If no status is provided, set it to 'Pending'
        $status = $validatedData['status'] ?? 'Pending';
    
        // Insert data into the overtime table using DB facade
        DB::table('overtime')->insert([
            'employee_name' => $validatedData['employee_name'],
            'overtime_date' => $validatedData['overtime_date'],
            'overtime_hours' => $validatedData['overtime_hours'],
            'overtime_type' => $validatedData['overtime_type'],
            'approved_by' => $validatedData['approved_by'],
            'description' => $validatedData['description'],
            'status' => $status,  // Store the status here
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Redirect back to the overtime list page with a success message
        return redirect()->route('overtime.index')->with('success', 'Overtime entry added successfully!');
    }

    public function toggleStatus(Request $request, $id)
{
    // Validate the status value from the request
    $validated = $request->validate([
        'status' => 'required|in:Approved,Rejected,Pending',
    ]);

    // Find the overtime record by its ID
    $overtime = DB::table('overtime')->where('id', $id)->first();

    // Update the status in the database
    DB::table('overtime')
        ->where('id', $id)
        ->update(['status' => $validated['status'], 'updated_at' => now()]);

    // Log the status change for debugging
    \Log::info('Status changed to: ' . $validated['status']);

    // Return the new status as a JSON response
    return response()->json(['status' => $validated['status']]);
}



public function create()
{
    // Fetch employees from the allemployees table
    $employees = DB::table('allemployees')
        ->select(DB::raw("CONCAT(`firstname`, ' ', `lastname`) AS full_name"), 'id')
        ->get();

    // Fetch overtime types from the overtimes table
    $overtimeTypes = DB::table('overtimes')
        ->select('name')
        ->get();

    // Pass employees and overtime types to the view
    return view('hrms.Employee.overtime.create', compact('employees', 'overtimeTypes'));
}




public function edit($id)
{
    // Find the overtime record by its ID
    $overtime = DB::table('overtime')->where('id', $id)->first();

    // Fetch all employees with their full names
    $employees = DB::table('allemployees')
        ->select('id', DB::raw("CONCAT(`firstname`, ' ', `lastname`) AS full_name"))
        ->get();

    // Fetch overtime types from the overtimes table
    $overtimeTypes = DB::table('overtimes')
        ->select('name')
        ->get();

    // Pass the overtime record and employees list to the edit view
    return view('hrms.Employee.overtime.edit', compact('overtime', 'employees','overtimeTypes'));
}


public function update(Request $request, $id)
{
    // Validate the request
    $validatedData = $request->validate([
        'employee_name' => 'required|string',
        'overtime_date' => 'required|date',
        'overtime_hours' => 'required|numeric|min:0.1',
        'overtime_type' => 'required|string',
        'approved_by' => 'required|string',
        'description' => 'required|string|max:255',
    ]);

    // Update the overtime record in the database
    DB::table('overtime')->where('id', $id)->update([
        'employee_name' => $validatedData['employee_name'],
        'overtime_date' => $validatedData['overtime_date'],
        'overtime_hours' => $validatedData['overtime_hours'],
        'overtime_type' => $validatedData['overtime_type'],
        'approved_by' => $validatedData['approved_by'],
        'description' => $validatedData['description'],
        'updated_at' => now(),
    ]);

    // Redirect back to the overtime list page with a success message
    return redirect()->route('overtime.index')->with('success', 'Overtime entry updated successfully!');
}

    public function destroy($id)
{
    // Soft delete the overtime record by setting deleted_at to 1
    DB::table('overtime')
        ->where('id', $id)
        ->update(['deleted_at' => 1, 'updated_at' => now()]);

    // Redirect back to the overtime list page with a success message
    return redirect()->route('overtime.index')->with('success', 'Overtime entry moved to trash successfully!');
}

public function trash()
{
    // Get all soft-deleted records (deleted_at = 1) with employee names
    $trashedOvertimeRecords = DB::table('overtime')
        ->join('allemployees', 'overtime.employee_name', '=', 'allemployees.id') // Join with allemployees
        ->where('overtime.deleted_at', '=', 1) // Only soft deleted records
        ->select('overtime.*', DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) AS employee_name")) // Select concatenated name
        ->orderBy('created_at', 'desc')
        ->get();

    return view('hrms.Employee.overtime.trash', compact('trashedOvertimeRecords'));
}



public function restore($id)
{
    // Restore the soft-deleted overtime record by setting deleted_at to NULL
    DB::table('overtime')
        ->where('id', $id)
        ->update(['deleted_at' => 0, 'updated_at' => now()]);

    return redirect()->route('overtime.index')->with('success', 'Overtime entry restored successfully!');
}

public function deletePermanently($id)
{
    // Permanently delete the overtime record
    DB::table('overtime')->where('id', $id)->delete();

    return redirect()->route('overtime.trash')->with('success', 'Overtime entry permanently deleted!');
}


public function show($id)
{
    // Retrieve the specific overtime entry by ID
    $overtime = DB::table('overtime')->find($id);

    if (!$overtime) {
        return redirect()->route('overtime.index')->with('error', 'Overtime record not found.');
    }

    return view('hrms.Employee.overtime.show', compact('overtime'));
}

}
