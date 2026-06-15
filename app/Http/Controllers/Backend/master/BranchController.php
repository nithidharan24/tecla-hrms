<?php

namespace App\Http\Controllers\Backend\master;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index(Request $request)
{
    try {

        /*
        |--------------------------------------------------------------------------
        | Statistics (GLOBAL - Not affected by filter)
        |--------------------------------------------------------------------------
        */
        $totalBranches     = Branch::count();
        $activeBranches    = Branch::where('status', 1)->count();
        $inactiveBranches  = Branch::where('status', 0)->count();
        $uniqueLocations   = Branch::distinct()->count('address');

        $activePercentage   = $totalBranches > 0 ? round(($activeBranches / $totalBranches) * 100) : 0;
        $inactivePercentage = $totalBranches > 0 ? round(($inactiveBranches / $totalBranches) * 100) : 0;

        // Dummy growth values (replace with real logic later)
        $growthPercentage           = 10;
        $activeGrowthPercentage     = 5;
        $inactiveGrowthPercentage   = 2;
        $locationsGrowthPercentage  = 15;


        /*
        |--------------------------------------------------------------------------
        | Filter Logic (Case-Insensitive Proper Way)
        |--------------------------------------------------------------------------
        */

        $branches = Branch::query();

        // Branch Name (case-insensitive)
        if ($request->filled('name')) {
            $branches->whereRaw(
                'LOWER(name) LIKE ?',
                ['%' . strtolower($request->name) . '%']
            );
        }

        // Address (case-insensitive)
        if ($request->filled('address')) {
            $branches->whereRaw(
                'LOWER(address) LIKE ?',
                ['%' . strtolower($request->address) . '%']
            );
        }

        // Status
        if ($request->status !== null && $request->status !== '') {
            $branches->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $branches = $branches->latest()->paginate(10)->appends($request->query());


        return view('hrms.master.branch.index', compact(
            'totalBranches',
            'activeBranches',
            'inactiveBranches',
            'uniqueLocations',
            'activePercentage',
            'inactivePercentage',
            'growthPercentage',
            'activeGrowthPercentage',
            'inactiveGrowthPercentage',
            'locationsGrowthPercentage',
            'branches'
        ));

    } catch (\Exception $e) {

        Log::error('Error in branch index: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Error loading branches data.');
    }
}


    public function create()
    {
        return view('hrms.master.branch.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:branches,name',
                'address' => 'required|string|max:500',
                'phone' => 'required|string|max:20',
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time',
                'status' => 'boolean'
            ], [
                'name.unique' => 'A branch with this name already exists.',
                'closing_time.after' => 'Closing time must be after opening time.',
            ]);

            // Set default status if not provided
            $validatedData['status'] = $request->has('status') ? 1 : 0;

            Branch::create($validatedData);

            return redirect()->route('branches.index')
                ->with('success', 'Branch created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating branch: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating branch. Please try again.')
                ->withInput();
        }
    }

    public function show(Branch $branch)
    {
        try {
            return view('hrms.master.branch.show', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Error showing branch: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'Branch not found or error loading branch details.');
        }
    }

    public function edit(Branch $branch)
    {
        try {
            return view('hrms.master.branch.edit', compact('branch'));
        } catch (\Exception $e) {
            Log::error('Error loading branch for edit: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'Error loading branch for editing.');
        }
    }

  public function update(Request $request, Branch $branch)
{
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
        ], [
            'name.unique' => 'A branch with this name already exists.',
            'closing_time.after' => 'Closing time must be after opening time.',
        ]);

        $branch->update($validatedData);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        Log::error('Error updating branch: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error updating branch. Please try again.')
            ->withInput();
    }
}


public function updateStatus(Branch $branch)
{
    try {
        $validated = request()->validate([
            'status' => 'required|boolean'
        ]);

        $branch->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $branch->status,
            'status_text' => $branch->status ? 'Active' : 'Inactive'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update status: ' . $e->getMessage()
        ], 500);
    }
}

    public function destroy(Branch $branch)
    {
        try {
            $branchName = $branch->name;
            $branch->delete();

            return redirect()->route('branches.index')
                ->with('success', "Branch  deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Error deleting branch: ' . $e->getMessage());
            return redirect()->route('branches.index')
                ->with('error', 'Error deleting branch. Please try again.');
        }
    }
}
