<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DesignationController extends Controller
{
    public function index()
    {
        // Retrieve all designations
        $designations = DB::table('designation')->select('id as designation_id', 'designation', 'department_id')->get();

        // Retrieve all departments
        $departments = DB::table('department')->select('id', 'department')->get();

        // Fetch activity logs for designations
        $activityLogs = DB::table('activity_logs')
            ->where('module', 'designation')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hrms.Employee.designation.index', compact('designations', 'departments', 'activityLogs'));
    }

    public function create()
    {
        // Fetch departments for the create form
        $departments = DB::table('department')->select('id', 'department')->get();
        return view('hrms.Employee.designation.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'department_id' => 'required|integer|exists:department,id',
            'designation' => 'required|string|max:255',
        ]);

        // Check if the designation already exists in the same department
        $existingDesignation = DB::table('designation')
            ->where('department_id', $request->department_id)
            ->where('designation', $request->designation)
            ->first();

        if ($existingDesignation) {
            // Redirect back with an error message if the designation already exists
            return redirect()->route('designation.index')->with('error', 'This designation already exists in the selected department.');
        }

        // Store the new designation in the database and log activity
        $desId = DB::table('designation')->insertGetId([
            'department_id' => $request->department_id,
            'designation' => $request->designation,
        ]);

        DB::table('activity_logs')->insert([
            'module' => 'designation',
            'action' => 'create',
            'record_id' => $desId,
            'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
            'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
            'details' => json_encode(['department_id' => $request->department_id, 'designation' => $request->designation]),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect back with a success message
        return redirect()->route('designation.index')->with('success', 'Designation added successfully.');
    }

    public function edit($id)
    {
        // Fetch designation data directly from the database
        $designation = DB::table('designation')->where('id', $id)->first();

        // Check if designation exists
        if (!$designation) {
            return redirect()->route('designation.index')->with('error', 'Designation not found.');
        }

        // Fetch departments for the edit form
        $departments = DB::table('department')->select('id', 'department')->get();

        return view('hrms.Employee.designation.edit', compact('designation', 'departments'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'department_id' => 'required|integer|exists:department,id',
            'designation' => 'required|string|max:255',
        ]);

        // Check if the designation already exists in the same department (excluding current record)
        $existingDesignation = DB::table('designation')
            ->where('department_id', $request->department_id)
            ->where('designation', $request->designation)
            ->where('id', '!=', $id) // Exclude current designation by ID
            ->first();

        if ($existingDesignation) {
            return redirect()->route('designation.index')->with('error', 'This designation already exists in the selected department.');
        }

        // Update the designation data directly in the database
        $updated = DB::table('designation')->where('id', $id)->update([
            'department_id' => $request->input('department_id'),
            'designation' => $request->input('designation')
        ]);

        // Check if the update was successful
        if ($updated) {
            DB::table('activity_logs')->insert([
                'module' => 'designation',
                'action' => 'update',
                'record_id' => $id,
                'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
                'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
                'details' => json_encode(['department_id' => $request->input('department_id'), 'designation' => $request->input('designation')]),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('designation.index')->with('success', 'Designation updated successfully');
        } else {
            return redirect()->route('designation.index')->with('error', 'Designation not found or no changes made');
        }
    }

    public function destroy($id)
    {
        // Hard delete the designation from the database
        $deleted = DB::table('designation')->where('id', $id)->delete();

        // Check if the delete was successful
        if ($deleted) {
            DB::table('activity_logs')->insert([
                'module' => 'designation',
                'action' => 'delete',
                'record_id' => $id,
                'user_id' => Session::get('user_id') ?? Session::get('admin_id'),
                'user_name' => Session::get('first_name') ? (Session::get('first_name') . ' ' . Session::get('last_name')) : Session::get('admin_name'),
                'details' => json_encode(['deleted_id' => $id]),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('designation.index')->with('success', 'Designation deleted successfully.');
        } else {
            return redirect()->route('designation.index')->with('error', 'Designation not found or already deleted.');
        }
    }
}
