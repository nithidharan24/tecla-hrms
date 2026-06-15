<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Illuminate\Support\Facades\Validator;

class HolidaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     return view('hrms.Employee.holidays.index');
    // }
    public function index()
{
    // Fetch all holidays from the 'holidays' table
    $holidays = DB::table('holidays')->get();

    // Pass the holidays to the view
    return view('hrms.Employee.holidays.index', compact('holidays'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  
     public function store(Request $request)
     {
         // Validate the form inputs
         $request->validate([
             'holiday_title' => 'required|string|max:255', // Ensure title is unique
             'holiday_date' => 'required|date|unique:holidays,holidaydate', // Ensure the holiday date is unique
         ]);
     
         // Determine the day of the week
         $day = \Carbon\Carbon::parse($request->holiday_date)->format('l'); // Get the day name (e.g. "Monday")
     
         // Insert the form data into the 'holidays' table
         DB::table('holidays')->insert([
             'title' => $request->holiday_title,
             'holidaydate' => $request->holiday_date,
             'day' => $day, // Store the calculated day
             'created_at' => now(),  // Current timestamp for created_at
             'updated_at' => now(),  // Current timestamp for updated_at
         ]);
     
         // Redirect back with a success message
         return redirect()->back()->with('success', 'Holiday added successfully!');
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
    // Fetch the specific holiday by its ID
    $holiday = DB::table('holidays')->where('id', $id)->first();

    // Return the holiday data as JSON (for the edit modal)
    return response()->json($holiday);
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
        
        // Update the holiday in the 'holidays' table
        DB::table('holidays')->where('id', $id)->update([
            'title' => $request->holiday_title,
            'holidaydate' => $request->holiday_date,
            'updated_at' => now(),  // Current timestamp for updated_at
        ]);
    
        // Redirect back with a success message
       return redirect()->route('employee-leaves.index')->with('success', 'Holiday added successfully.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete the holiday by its ID
        DB::table('holidays')->where('id', $id)->delete();
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Holiday deleted successfully!');
    }
 // In HolidayController.php
 public function checkTitle(Request $request) {
    // Logic to check if the title exists
    $exists = Holiday::where('title', $request->title)
        ->when($request->editMode, function ($query) use ($request) {
            return $query->where('id', '!=', $request->id); // Exclude the current holiday
        })
        ->exists();

    return response()->json(['exists' => $exists]);
}


 

}
