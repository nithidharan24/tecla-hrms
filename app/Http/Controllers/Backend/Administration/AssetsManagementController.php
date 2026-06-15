<?php

namespace App\Http\Controllers\Backend\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AssetsManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $branchId = getAdminBranchFilter();
        
        // Get Assets
        $assetQuery = DB::table('assets_company')
                    ->select('assets_company.*')
                    ->where('assets_company.deleted_at', 0);
                    
        if ($branchId) {
            $assetQuery->where('assets_company.branch_id', $branchId);
        }
                     
        // Apply asset filters
        if ($request->has('asset_name') && !empty($request->asset_name)) {
            $assetName = $request->asset_name;
            $assetQuery->where('assets_company.asset_name', 'like', '%' . $assetName . '%');
        }

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 'available') {
                $assetQuery->where('assets_company.available_quantity', '>', 0);
            } elseif ($request->status == 'out_of_stock') {
                $assetQuery->where('assets_company.available_quantity', '<=', 0);
            }
        }

        if ($request->has('condition') && !empty($request->condition)) {
            $assetQuery->where('assets_company.condition', $request->condition);
        }

        if ($request->has('location') && !empty($request->location)) {
            $assetQuery->where('assets_company.location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = trim($request->from_date);
            $toDate = trim($request->to_date);
            if (!empty($fromDate) && !empty($toDate)) {
                try {
                    $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                    $toDate = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                    $assetQuery->whereBetween('assets_company.purchase_date', [$fromDate, $toDate]);
                } catch (\Exception $e) {
                    // Handle error
                }
            }
        }

        $assets = $assetQuery->paginate(10);

        // Calculate counts for asset tabs
        $totalCount = DB::table('assets_company')
            ->where('deleted_at', 0)
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->count();

        $availableCount = DB::table('assets_company')
            ->where('deleted_at', 0)
            ->where('available_quantity', '>', 0)
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->count();

        $outOfStockCount = DB::table('assets_company')
            ->where('deleted_at', 0)
            ->where('available_quantity', '<=', 0)
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->count();

        // Get Assignments
        $assignmentQuery = DB::table('assets_assignment')
            ->join('allemployees', 'allemployees.id', '=', 'assets_assignment.employee_id')
            ->join('assets_company', 'assets_company.id', '=', 'assets_assignment.asset_id')
            ->select(
                'assets_assignment.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.designation',
                'assets_company.asset_name',
                'assets_company.model',
                'assets_company.serial_number',
                'assets_company.asset_id as company_asset_id'
            )
            ->where('assets_assignment.deleted_at', 0);

        // Apply assignment filters
        if ($request->filled('employee_name')) {
            $assignmentQuery->where(function($q) use ($request) {
                $q->where('allemployees.firstname', 'like', '%' . $request->employee_name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $request->employee_name . '%');
            });
        }

        if ($request->filled('asset_name')) {
            $assignmentQuery->where('assets_company.asset_name', 'like', '%' . $request->asset_name . '%');
        }

        if ($request->filled('status')) {
            $assignmentQuery->where('assets_assignment.status', $request->status);
        }

        if ($request->filled('from_date')) {
            $assignmentQuery->whereDate('assets_assignment.assigned_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $assignmentQuery->whereDate('assets_assignment.assigned_date', '<=', $request->to_date);
        }

        $assignments = $assignmentQuery->get();

        // Calculate counts for assignment tabs
        $totalAssignmentsCount = count($assignments);
        $assignedCount = $assignments->where('status', 'assigned')->count();
        $returnedCount = $assignments->where('status', 'returned')->count();

        return view('hrms.Administration.AssetsManagement.index', compact(
            'assets', 
            'totalCount',
            'availableCount',
            'outOfStockCount',
            'assignments',
            'totalAssignmentsCount',
            'assignedCount',
            'returnedCount'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrms.Administration.AssetsManagement.create');
    }
/**
 * Check if asset has assignments
 */
public function checkAssignments($id)
{
    $assignedCount = DB::table('assets_assignment')
        ->where('asset_id', $id)
        ->where('status', 'assigned')
        ->count();

    return response()->json(['has_assignments' => $assignedCount > 0]);
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $branchId = Session::get('branch_id');

        // Validation rules
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'purchase_from' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'condition' => 'required|string|max:100',
            'warranty' => 'required|integer|min:0',
            'location' => 'required|string|max:150',
            'description' => 'required|string|min:10|max:255',
            'asset_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle asset image upload
        $assetImagePath = null;
        if ($request->hasFile('asset_image')) {
            $assetImage = $request->file('asset_image');
            $imageName = time() . '_' . $assetImage->getClientOriginalName();
            $assetImage->move(public_path('admin/uploads/assets'), $imageName);
            $assetImagePath = 'admin/uploads/assets/' . $imageName;
        }

        // Generate asset ID
        $assetId = 'AST-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        DB::table('assets_company')->insert([
            'asset_name' => $request->asset_name,
            'quantity' => $request->quantity,
            'available_quantity' => $request->quantity, // Initially all are available
            'asset_id' => $assetId,
            'purchase_date' => $request->purchase_date,
            'purchase_from' => $request->purchase_from,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'supplier' => $request->supplier,
            'condition' => $request->condition,
            'warranty' => $request->warranty,
            'branch_id' => $branchId,
            'location' => $request->location,
            'asset_image' => $assetImagePath,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $asset = DB::table('assets_company')->where('id', $id)->first();
    
        if ($asset) {
            return view('hrms.Administration.AssetsManagement.edit', compact('asset'));
        } else {
            return redirect()->route('assets-management.index')->with('error', 'Asset not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $asset = DB::table('assets_company')->where('id', $id)->first();
        
        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Asset not found.');
        }

        // Validation rules
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'purchase_from' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'condition' => 'required|string|max:100',
            'warranty' => 'required|integer|min:0',
            'location' => 'required|string|max:150',
            'description' => 'required|string|min:10|max:255',
            'asset_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Calculate new available quantity based on quantity change
        $quantityDifference = $request->quantity - $asset->quantity;
        $newAvailableQuantity = $asset->available_quantity + $quantityDifference;

        // Ensure available quantity doesn't go below 0
        if ($newAvailableQuantity < 0) {
            return back()->withErrors(['quantity' => 'Cannot reduce quantity below currently assigned assets.']);
        }

        // Handle asset image upload
        $assetImagePath = $asset->asset_image;
        if ($request->hasFile('asset_image')) {
            // Delete old image if exists
            if ($asset->asset_image && file_exists(public_path($asset->asset_image))) {
                unlink(public_path($asset->asset_image));
            }
            
            $assetImage = $request->file('asset_image');
            $imageName = time() . '_' . $assetImage->getClientOriginalName();
            $assetImage->move(public_path('admin/uploads/assets'), $imageName);
            $assetImagePath = 'admin/uploads/assets/' . $imageName;
        }

        // Handle remove asset image checkbox
        if ($request->has('remove_asset_image')) {
            if ($asset->asset_image && file_exists(public_path($asset->asset_image))) {
                unlink(public_path($asset->asset_image));
            }
            $assetImagePath = null;
        }

        DB::table('assets_company')->where('id', $id)->update([
            'asset_name' => $request->asset_name,
            'quantity' => $request->quantity,
            'available_quantity' => $newAvailableQuantity,
            'purchase_date' => $request->purchase_date,
            'purchase_from' => $request->purchase_from,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'supplier' => $request->supplier,
            'condition' => $request->condition,
            'warranty' => $request->warranty,
            'location' => $request->location,
            'asset_image' => $assetImagePath,
            'description' => $request->description,
            'updated_at' => now()
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Check if there are any assigned assets
        $assignedCount = DB::table('assets_assignment')
            ->where('asset_id', $id)
            ->where('status', 'assigned')
            ->count();

        if ($assignedCount > 0) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete asset. There are assigned items.']);
            }
            return redirect()->route('assets.index')->with('error', 'Cannot delete asset. There are assigned items.');
        }

        DB::table('assets_company')
            ->where('id', $id)
            ->update(['deleted_at' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Asset deleted successfully']);
        }
        
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully');
    }
}