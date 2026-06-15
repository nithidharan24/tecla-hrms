<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class Performance_AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $performance_appraisals = DB::table('performance_appraisal')
        ->leftJoin('allemployees', 'performance_appraisal.employee_id', '=', 'allemployees.id')
        ->leftJoin('designation', 'performance_appraisal.designation_id', '=', 'designation.id')
        ->leftJoin('department', 'designation.department_id', '=', 'department.id') // Join with department table
        ->select(
            'performance_appraisal.*', 
            DB::raw("CONCAT(performance_appraisal.designation_id, ' = ', designation.id) as designation_match"), // Concatenation for designation_id
            DB::raw("CONCAT(designation.department_id, ' = ', department.id) as department_match"), // Concatenation for department_id
            'department.department as department_name', // Select department_name
            'designation.designation',
            'designation.department_id',
            'allemployees.firstname', 
            'allemployees.lastname'
        )
        ->where('performance_appraisal.deleted_at', 0)
        ->where('designation.deleted_at', 0)
        ->where('allemployees.deleted_at', 0)
        ->where('allemployees.status', 'active')
        ->get();
    
    return view('hrms.performance.performance.performance_appraisal.index', compact('performance_appraisals'));
    

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname')
            ->get();
    
        $designations = DB::table('designation')
            ->where('deleted_at', 0)
            ->select('id', 'designation')
            ->get();
    
        // Initialize empty arrays for competencies
        $technicalCompetencies = [];
        $organizationalCompetencies = [];
    
        // Optionally, you can fetch all performance indicators to avoid making another query later
        $performance_indicators = DB::table('performance_indicator')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'designation_id', 'technical', 'organizational')
            ->get();
    
        return view('hrms.performance.performance.performance_appraisal.create', compact('employees', 'designations', 'performance_indicators', 'technicalCompetencies', 'organizationalCompetencies'));
    }
    

    public function getPerformanceIndicators(Request $request)
    {
        $designationId = $request->input('designation_id');

        $performanceIndicator = DB::table('performance_indicator')
            ->where('designation_id', $designationId)
            ->where('status', 'active')
            ->where('deleted_at', 0)
            ->first();

        if ($performanceIndicator) {
            $response = [
                'success' => true,
                'data' => [
                    'technical' => json_decode($performanceIndicator->technical, true),
                    'organizational' => json_decode($performanceIndicator->organizational, true),
                ],
            ];
        } else {
            $response = ['success' => false, 'message' => 'No data found'];
        }

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            // $request->validate([
            //     'employee_id' => 'required|exists:allemployees,id',
            //     'designation_id' => 'required|exists:designation,id',
            //     'appraisal_date' => 'required|date',
            //     'technical.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            //     'organizational.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            //     'status' => 'required|in:active,inactive',
            // ]);
    
            // Insert performance appraisal data
            DB::table('performance_appraisal')->insert([
                'employee_id' => $request->employee,
                'designation_id' => $request->designation,
                'appraisal_date' => $request->appraisal_date,
                'technical' => json_encode($request->input('technical')),
                'organizational' => json_encode($request->input('organizational')),
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Redirect with success message
            return redirect()->route('performance-appraisal.index')->with('success', 'Performance appraisal saved successfully!');
        } catch (\Exception $e) {
            // Log error message and return back with error message
            \Log::error('Error saving performance appraisal: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while saving the data. Please try again.']);
        }
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
    $performanceAppraisal = DB::table('performance_appraisal')
        ->where('id', $id)
        ->where('deleted_at', 0)
        ->first();

    if (!$performanceAppraisal) {
        return redirect()->route('performance-appraisal.index')->with('error', 'Performance appraisal not found.');
    }

    $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->select('id', 'firstname', 'lastname')
        ->get();

    $designations = DB::table('designation')
        ->where('deleted_at', 0)
        ->select('id', 'designation')
        ->get();

    $performanceAppraisal->technical = json_decode($performanceAppraisal->technical, true);
    $performanceAppraisal->organizational = json_decode($performanceAppraisal->organizational, true);

    return view('hrms.performance.performance.performance_appraisal.edit', compact('performanceAppraisal', 'employees', 'designations'));
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
        try {
            // $request->validate([
            //     'employee' => 'required|exists:allemployees,id',
            //     'designation' => 'required|exists:designation,id',
            //     'appraisal_date' => 'required|date',
            //     'technical.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            //     'organizational.*' => 'required|in:none,beginner,intermediate,advanced,expert',
            //     'status' => 'required|in:active,inactive',
            // ]);

            DB::table('performance_appraisal')
                ->where('id', $id)
                ->update([
                    'employee_id' => $request->employee,
                    'designation_id' => $request->designation,
                    'appraisal_date' => $request->appraisal_date,
                    'technical' => json_encode($request->input('technical')),
                    'organizational' => json_encode($request->input('organizational')),
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);

            return redirect()->route('performance-appraisal.index')->with('success', 'Performance appraisal updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating performance appraisal: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating the data. Please try again.']);
        }
    }

    public function destroy($id)
    {
          // Update the `deleted_at` field to `1` (soft delete)
          DB::table('performance_appraisal')
          ->where('id', $id)
          ->update(['deleted_at' => 1]);

      // Redirect back with success message
      return redirect()->route('performance-appraisal.index')->with('success', 'Performance appraisal deleted successfully');

        // // Permanently delete the timesheet by using the `delete()` method
        // DB::table('performance_appraisal')->where('id', $id)->delete();
    
        // // Redirect back with success message
        // return redirect()->route('performance-appraisal.index')->with('success', 'Performance appraisal deleted permanently');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the status to ensure only allowed values are passed
        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        // Update the status using DB facade
        $updated = DB::table('performance_appraisal')->where('id', $id)->update([
            'status' => $validated['status'],
            'updated_at' => now(), // Make sure to update the timestamp
        ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
    }
}
