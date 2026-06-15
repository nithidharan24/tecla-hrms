<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class Performance_IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $performance_indicators = DB::table('performance_indicator')
        ->leftJoin('designation', 'performance_indicator.designation_id', '=', 'designation.id') // Change 'id' to 'designationid'
        ->leftJoin('department', 'performance_indicator.designation_id', '=', 'department.id') // Change 'id' to 'departmentid'
        ->select(
            'performance_indicator.*',
            DB::raw("CONCAT(performance_indicator.designation_id, ' = ', designation.id) as designation_match"), // Concatenation for designation_id
            DB::raw("CONCAT(designation.department_id, ' = ', department.id) as department_match"), // Concatenation for department_id
            'department.department as department_name', // Select department_name
            'designation.designation',
            'designation.department_id'
        )
        ->where('performance_indicator.deleted_at', 0)
        ->where('designation.deleted_at', 0)
        ->get();
        return view('hrms.performance.performance.performance_indicator.index',compact('performance_indicators'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $designations = DB::table('designation')
            ->where('deleted_at', 0)
            ->select('id', 'designation')
            ->get();

        return view('hrms.performance.performance.performance_indicator.create', compact('designations'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation' => 'required|exists:designation,id',
            'technical.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            'organizational.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            'status' => 'required|in:active,inactive',
        ]);
    
        // Now, store the data in the database after passing validation
        // Example:
        DB::table('performance_indicator')->insert([
            'designation_id' => $request->input('designation'),
            'technical' => json_encode($request->input('technical')),
            'organizational' => json_encode($request->input('organizational')),
            'status' => $request->input('status'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return redirect()->route('performance-indicator.index')->with('success', 'Performance Indicator created successfully!');
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
        // Fetch the shift by ID
        $performance_indicators = DB::table('performance_indicator')
        ->where('id', $id)
        ->first();

        $designations = DB::table('designation')
        ->where('deleted_at', 0)
        ->select('id', 'designation')
        ->get();

        // Check if the shift exists
        if (!$performance_indicators) {
            return redirect()->route('performance-indicator.index')->with('error', 'Performance indicator not found');
        }

        // Pass the shift data to the edit view
        return view('hrms.performance.performance.performance_indicator.edit', compact('performance_indicators','designations'));
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
            'designation' => 'required|exists:designation,id',
            'technical.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            'organizational.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            'status' => 'required|in:active,inactive',
        ]);

        // Update the performance indicator in the database
        $updated = DB::table('performance_indicator')->where('id', $id)->update([
            'designation_id' => $request->input('designation'),
            'technical' => json_encode($request->input('technical')),
            'organizational' => json_encode($request->input('organizational')),
            'status' => $request->input('status'),
            'updated_at' => now(),
        ]);

        // Check if the update was successful and return an appropriate response
        if ($updated) {
            return redirect()->route('performance-indicator.index')->with('success', 'Performance Indicator updated successfully!');
        } else {
            return redirect()->route('performance-indicator.index')->with('error', 'Failed to update Performance Indicator.');
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
          // Update the `deleted_at` field to `1` (soft delete)
          DB::table('performance_indicator')
          ->where('id', $id)
          ->update(['deleted_at' => 1]);

      // Redirect back with success message
      return redirect()->route('performance-indicator.index')->with('success', 'Performance indicator deleted successfully');

        // // Permanently delete the timesheet by using the `delete()` method
        // DB::table('performance_indicator')->where('id', $id)->delete();
    
        // // Redirect back with success message
        // return redirect()->route('performance-indicator.index')->with('success', 'Performance indicator deleted permanently');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the status to ensure only allowed values are passed
        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        // Update the status using DB facade
        $updated = DB::table('performance_indicator')->where('id', $id)->update([
            'status' => $validated['status'],
            'updated_at' => now(), // Make sure to update the timestamp
        ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
    }

}
