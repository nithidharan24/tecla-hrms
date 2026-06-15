<?php

namespace App\Http\Controllers\Backend\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AssetsAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branchId = getAdminBranchFilter();
        
        $query = DB::table('assets_assignment')
                    ->leftJoin('assets_company', 'assets_assignment.asset_id', '=', 'assets_company.id')
                    ->leftJoin('allemployees', 'assets_assignment.employee_id', '=', 'allemployees.id')
                    ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                    ->select(
                        'assets_assignment.*',
                        'assets_company.asset_name',
                        'assets_company.asset_id as company_asset_id',
                        'assets_company.model',
                        'assets_company.serial_number',
                        'allemployees.firstname',
                        'allemployees.lastname',
                        'designation.designation'
                    )
                    ->where('assets_assignment.deleted_at', 0)
                    ->where('assets_company.deleted_at', 0)
                    ->where('allemployees.deleted_at', 0);
                    
        if ($branchId) {
            $query->where('assets_assignment.branch_id', $branchId);
        }
                     
        // Check if the user has entered an employee name
        if ($request->has('employee_name') && !empty($request->employee_name)) {
            $employeeName = $request->employee_name;
            $query->where(function($q) use ($employeeName) {
                $q->where('allemployees.firstname', 'like', '%' . $employeeName . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $employeeName . '%');
            });
        }

        // Check if the user has selected a status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('assets_assignment.status', $request->status);
        }

        // Check if the user has entered an assigned date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = trim($request->from_date);
            $toDate = trim($request->to_date);
            if (!empty($fromDate) && !empty($toDate)) {
                try {
                    $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                    $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                    $query->whereBetween('assets_assignment.assigned_date', [$fromDate, $toDate]);
                } catch (\Exception $e) {
                    return back()->withErrors(['message' => 'Invalid date format. Please enter valid dates.']);
                }
            }
        }

        $assignments = $query->paginate(10);

        return view('hrms.Administration.AssetsAssignment.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname')
            ->get();

        $assets = DB::table('assets_company')
            ->where('deleted_at', 0)
            ->where('available_quantity', '>', 0)
            ->select('id', 'asset_name', 'asset_id', 'available_quantity', 'model', 'serial_number')
            ->get();

        return view('hrms.Administration.AssetsAssignment.create', compact('employees', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $branchId = Session::get('branch_id');

        // Validation rules
        $request->validate([
            'asset_id' => 'required|integer|exists:assets_company,id',
            'employee_id' => 'required|integer|exists:allemployees,id',
            'assigned_date' => 'required|date',
            'return_date' => 'nullable|date|after:assigned_date',
            'condition' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Check if asset is available
        $asset = DB::table('assets_company')
            ->where('id', $request->asset_id)
            ->where('available_quantity', '>', 0)
            ->first();

        if (!$asset) {
            return back()->withErrors(['asset_id' => 'Selected asset is not available for assignment.']);
        }

        DB::transaction(function () use ($request, $branchId, $asset) {
            // Create assignment record
            $assignmentId = DB::table('assets_assignment')->insertGetId([
                'asset_id' => $request->asset_id,
                'employee_id' => $request->employee_id,
                'assigned_date' => $request->assigned_date,
                'return_date' => $request->return_date,
                'condition' => $request->condition,
                'remarks' => $request->remarks,
                'branch_id' => $branchId,
                'status' => 'assigned',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update available quantity in assets_company
            DB::table('assets_company')
                ->where('id', $request->asset_id)
                ->decrement('available_quantity');
        });

        return redirect()->route('assets-assignment.index')->with('success', 'Asset assigned successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $assignment = DB::table('assets_assignment')
            ->where('id', $id)
            ->first();

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname')
            ->get();

        $assets = DB::table('assets_company')
            ->where('deleted_at', 0)
            ->select('id', 'asset_name', 'asset_id', 'model', 'serial_number')
            ->get();

        if ($assignment) {
            return view('hrms.Administration.AssetsAssignment.edit', compact('assignment', 'employees', 'assets'));
        } else {
            return redirect()->route('assets-assignment.index')->with('error', 'Assignment not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $assignment = DB::table('assets_assignment')->where('id', $id)->first();
        
        if (!$assignment) {
            return redirect()->route('assets-assignment.index')->with('error', 'Assignment not found.');
        }

        // Validation rules
        $request->validate([
            'employee_id' => 'required|integer|exists:allemployees,id',
            'assigned_date' => 'required|date',
            'return_date' => 'nullable|date|after:assigned_date',
            'condition' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::table('assets_assignment')->where('id', $id)->update([
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'return_date' => $request->return_date,
            'condition' => $request->condition,
            'remarks' => $request->remarks,
            'updated_at' => now()
        ]);

        return redirect()->route('assets-assignment.index')->with('success', 'Assignment updated successfully.');
    }

    /**
     * Mark asset as returned
     */
    public function markReturned($id)
    {
        $assignment = DB::table('assets_assignment')->where('id', $id)->first();

        if (!$assignment) {
            return redirect()->route('assets-assignment.index')->with('error', 'Assignment not found.');
        }

        if ($assignment->status === 'returned') {
            return redirect()->route('assets-assignment.index')->with('error', 'Asset is already returned.');
        }

        DB::transaction(function () use ($assignment) {
            // Update assignment status
            DB::table('assets_assignment')
                ->where('id', $assignment->id)
                ->update([
                    'status' => 'returned',
                    'return_date' => now(),
                    'updated_at' => now()
                ]);

            // Increment available quantity in assets_company
            DB::table('assets_company')
                ->where('id', $assignment->asset_id)
                ->increment('available_quantity');
        });

        return redirect()->route('assets-assignment.index')->with('success', 'Asset marked as returned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $assignment = DB::table('assets_assignment')->where('id', $id)->first();

        if ($assignment && $assignment->status === 'assigned') {
            // If assignment is active, increment available quantity
            DB::table('assets_company')
                ->where('id', $assignment->asset_id)
                ->increment('available_quantity');
        }

        DB::table('assets_assignment')
            ->where('id', $id)
            ->update(['deleted_at' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Assignment deleted successfully']);
        }
        
        return redirect()->route('assets-assignment.index')->with('success', 'Assignment deleted successfully');
    }
}