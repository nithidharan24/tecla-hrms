<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    // Fetch all departments from the database
    $departments = DB::table('department')->get();
    
    // Fetch activity logs for departments
    $activityLogs = DB::table('activity_logs')
        ->where('module', 'department')
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Pass the departments and logs to the view
    return view('hrms.Employee.department.index', compact('departments', 'activityLogs'));
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.Employee.department.create');
    
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate that the department name is unique
        $request->validate([
            'department' => 'required|string|max:255',
        ]);
    
        // Check if the department already exists
        $exists = DB::table('department')->where('department', $request->input('department'))->exists();
    
        if ($exists) {
            // Redirect back with a duplicate error message
            return redirect()->route('department.index')->with('duplicate_error', 'Department already exists.');
        }
    
        // Insert the new department and log activity
        $deptId = DB::table('department')->insertGetId([
            'department' => $request->input('department'),
        ]);

        DB::table('activity_logs')->insert([
            'module' => 'department',
            'action' => 'create',
            'record_id' => $deptId,
            'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
            'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
            'details' => json_encode(['department' => $request->input('department')]),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Redirect with success message
        return redirect()->route('department.index')->with('success', 'Department added successfully.');
    }
    
    
    


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch department data directly from the database
        $department = DB::table('department')->where('id', $id)->first();
        
        // Check if department exists
        if (!$department) {
            return redirect()->route('department.index')->with('error', 'Department not found.');
        }
    
        // Return the view with department data
        return view('hrms.Employee.department.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    // Validate the incoming request data
    $request->validate([
        'department' => 'required|string|max:255',
    ]);

    // Update the department data directly in the database
    $updated = DB::table('department')
        ->where('id', $id)
        ->update(['department' => $request->input('department')]);

    // Check if the update was successful
    if ($updated) {
        // Log update activity
        DB::table('activity_logs')->insert([
            'module' => 'department',
            'action' => 'update',
            'record_id' => $id,
            'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
            'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
            'details' => json_encode(['department' => $request->input('department')]),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('department.index')->with('success', 'Department updated successfully');
    } else {
        return redirect()->route('department.index')->with('error', 'Department not found or no changes made');
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
    $deleted = DB::table('department')->where('id', $id)->delete();

    if ($deleted) {
        // Log delete activity
        DB::table('activity_logs')->insert([
            'module' => 'department',
            'action' => 'delete',
            'record_id' => $id,
            'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
            'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
            'details' => json_encode(['deleted_id' => $id]),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('department.index')->with('success', 'Department deleted successfully.');
    } else {
        return redirect()->route('department.index')->with('error', 'Department not found.');
    }
}
public function check(Request $request)
{
    // Validate the department name input
    $request->validate([
        'department' => 'required|string',
    ]);

    // Check if the department name already exists in the database
    $exists = DB::table('department')
        ->where('department', $request->input('department'))
        ->exists();

    // Return a JSON response indicating whether the department exists
    return response()->json(['exists' => $exists]);
}

}
