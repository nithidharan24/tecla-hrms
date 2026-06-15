<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class sheduleController extends Controller
{
   public function scheduleInterview($candidateId)
{
    $candidate = DB::table('candidate')
        ->where('id', $candidateId)
        ->first();

    $interviewers = DB::table('allemployees')
        ->where('status', 'active')
        ->get();

    return view(
        'hrms.Jobs.shortlist.schedule-interview',
        compact(
            'candidate',
            'interviewers'
        )
    );
}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $schedules = DB::table('schedule')->get();
    $candidates = DB::table('candidate')->get();
  $interviewers = DB::table('allemployees')
        ->where('status', 'active')
        ->get();
    return view('hrms.Jobs.schedule.index', compact('schedules', 'candidates', 'interviewers'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  
 public function create()
{
    $candidates = DB::table('candidate')->get();

    $interviewers = DB::table('allemployees')
        ->where('status', 'active')
        ->get();

    return view(
        'hrms.Jobs.schedule.create',
        compact(
            'candidates',
            'interviewers'
        )
    );
}
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|exists:candidate,id',
            'selectdate1' => 'required|date',
            'selecttime1' => 'required|date_format:H:i',
            'selectdate2' => 'nullable|date',
            'selecttime2' => 'nullable|date_format:H:i',
            'selectdate3' => 'nullable|date',
            'selecttime3' => 'nullable|date_format:H:i',
        ]);

        // Insert data into the schedule table
        DB::table('schedule')->insert([
            'name' => $validatedData['name'],
            'selectdate1' => $validatedData['selectdate1'],
            'selecttime1' => $validatedData['selecttime1'],
            'selectdate2' => $validatedData['selectdate2'] ?? null,
            'selecttime2' => $validatedData['selecttime2'] ?? null,
            'selectdate3' => $validatedData['selectdate3'] ?? null,
            'selecttime3' => $validatedData['selecttime3'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('recruitment.index')->with('success', 'Schedule created successfully.');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
