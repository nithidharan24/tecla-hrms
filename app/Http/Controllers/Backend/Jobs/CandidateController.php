<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchId = getAdminBranchFilter();
        // Retrieve all candidates from the 'candidates' table
        $candidates = DB::table('candidate')->get();
        if ($branchId) {
            $candidates->where('e.branch_id', $branchId);
        }
        // Return the index view with the candidates data
        return view('hrms.Jobs.candidatelist.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.Jobs.candidatelist.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'created_at' => 'required|date',
    ]);
    $branchId = Session::get('branch_id');
    // Store in the database
    DB::table('candidate')->insert([
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'],
        'branch_id' => $branchId, // store branch_id from session
        'created_at' => $validatedData['created_at'],
    ]);

    return redirect()->route('recruitment.index')->with('success', 'Candidate added successfully!');
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
        $candidate = DB::table('candidate')->where('id', $id)->first();
        return view('hrms.Jobs.candidatelist.edit', compact('candidate'));
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
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'createdate' => 'required|date',
        ]);

        DB::table('candidate')->where('id', $id)->update([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'createdate' => $validatedData['createdate'],
            'updated_at' => now(), // Update the timestamp
        ]);

        return redirect()->route('recruitment.index')->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('candidate')->where('id', $id)->delete();
        return redirect()->route('recruitment.index')->with('success', 'Candidate deleted successfully.');
    }
}
