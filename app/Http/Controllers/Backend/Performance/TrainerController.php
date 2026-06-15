<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class TrainerController extends Controller
{
    public function index()
    {$branchId = getAdminBranchFilter();
        // Retrieve all trainers from the database
        $trainers = DB::table('trainers')->get();
        $employees = DB::table('allemployees')->get(); // Fetch employees from the database
        $firstEmployee = $employees->first(); // Get the first employee from the collection
        if ($branchId) {
            $firstEmployee->where('e.branch_id', $branchId);
        }
        return view('hrms.performance.Training.Trainers.index', compact('trainers','employees','firstEmployee'));
    }

    public function create()
{
    // Fetch designations from the designation table
    $designations = DB::table('designation')->select('id', 'designation')->get();

    // Display the form for creating a new trainer
    return view('hrms.performance.Training.Trainers.create', compact('designations'));
}


    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name'     => 'required|string|max:255',
        'last_name'      => 'required|string|max:255',
        'designation_id' => 'required|integer',
        'email'          => 'required|email|unique:trainers,email',
        'phone'          => 'required|string|max:15',
        'description'    => 'nullable|string',
    ]);

    // Add branch_id manually from session
    $validated['branch_id'] = Session::get('branch_id');

    // Insert into DB
    $inserted = DB::table('trainers')->insert($validated);

    if ($inserted) {
        return redirect()->route('trainers.index')->with('success', 'Trainer created successfully.');
    } else {
        return back()->with('error', 'Something went wrong. Trainer not created.');
    }
}

    

    public function show($id)
    {
        // Display the specified trainer's details (optional)
        $trainer = DB::table('trainers')->where('id', $id)->first();
        return view('hrms.performance.Training.Trainers.show', compact('trainer'));
    }

    public function edit($id)
{
    // Fetch the specified trainer
    $trainer = DB::table('trainers')->where('id', $id)->first();

    if (!$trainer) {
        return redirect()->route('trainers.index')->with('error', 'Trainer not found!');
    }

    // Fetch designations for dropdown
    $designations = DB::table('designation')->select('id', 'designation')->get();

    return view('hrms.performance.Training.Trainers.edit', compact('trainer', 'designations'));
}

    
   public function updateStatus(Request $request, $id)
{
    DB::table('trainers')
        ->where('id', $id)
        ->update(['status' => $request->status]);

    return redirect()->back()->with('success', 'Status updated successfully.');
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'first_name'     => 'required|string|max:255',
        'last_name'      => 'required|string|max:255',
        'designation_id' => 'required|integer',
        'email'          => 'required|email|unique:trainers,email,' . $id,
        'phone'          => 'required|string|max:15',
        'description'    => 'nullable|string',
    ]);

    $validated['branch_id'] = Session::get('branch_id');

    $updated = DB::table('trainers')->where('id', $id)->update($validated);

    if ($updated) {
        return redirect()->route('trainers.index')->with('success', 'Trainer updated successfully.');
    } else {
        return back()->with('error', 'No changes were made or something went wrong.');
    }
}


   



    public function changeStatus($id)
    {
        $trainer = DB::table('trainers')->find($id);
    
        if (!$trainer) {
            return redirect()->route('trainers.index')->with('error', 'Trainer not found!');
        }
    
        $newStatus = $trainer->status === 'Active' ? 'Inactive' : 'Active';
        DB::table('trainers')->where('id', $id)->update(['status' => $newStatus]);
    
        return redirect()->route('trainers.index')->with('success', 'Trainer status updated successfully!');
    }

    
    public function destroy($id)
{
    try {
        // Attempt to delete the trainer record
        $deleted = DB::table('trainers')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($deleted) {
            return redirect()->route('trainers.index')->with('success', 'Trainer deleted successfully!');
        } else {
            return redirect()->back()->withErrors(['error' => 'Trainer not found or already deleted.']);
        }
    } catch (\Exception $e) {
        // Log any exception that occurs
        \Log::error('Error deleting trainer: ' . $e->getMessage());

        // Redirect with an error message
        return redirect()->back()->withErrors(['error' => 'Failed to delete trainer. Please try again.']);
    }
}


}


