<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $experiences = DB::table('experience')->get();
        return view('hrms.Jobs.experience.index', compact('experiences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // This method is typically for showing a creation form,
        // but your current setup uses a modal, so it might not be directly used.
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'experience' => 'required|string|max:255', // Already set to string validation
        ]);

        // Insert the validated data into the 'experience' table
        // Default status for new entries will be 'active'
        DB::table('experience')->insert([
            'experience' => trim($validatedData['experience']),
            'status' => 'active', // Default status for new experience
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Experience added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // This method is typically for displaying a single resource.
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // This method is for a separate edit page, not directly used by the modal.
        // The modal population is handled by JavaScript in the index view.
        $experience = DB::table('experience')->where('id', $id)->first();
        return view('experience.edit', compact('experience'));
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
        $validatedData = $request->validate([
            'experience' => 'required|string|max:255', // Already set to string validation
        ]);

        // Update the experience in the database
        DB::table('experience')->where('id', $id)->update([
            'experience' => trim($validatedData['experience']),
            'updated_at' => now(),
        ]);

        // Redirect back with success message
        return redirect()->route('recruitment.index')->with('success', 'Experience updated successfully!');
    }

    /**
     * Toggle the status of the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $id)
    {
        $experience = DB::table('experience')->where('id', $id)->first();

        if (!$experience) {
            return response()->json(['message' => 'Experience not found.'], 404);
        }

        // Determine the new status
        $newStatus = ($experience->status == 'active') ? 'inactive' : 'active';

        DB::table('experience')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully!', 'new_status' => $newStatus]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete the experience from the database
        DB::table('experience')->where('id', $id)->delete();
        // Redirect back with success message
        return redirect()->back()->with('success', 'Experience deleted successfully!');
    }
}