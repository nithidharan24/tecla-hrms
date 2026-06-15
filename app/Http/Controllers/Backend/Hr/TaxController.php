<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
{
    $query = DB::table('taxes')->where('deleted_at', 0);
    
    // Apply search filter
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    
    // Apply status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Apply percentage range filter
    if ($request->filled('percentage_range')) {
        switch ($request->percentage_range) {
            case '0-5':
                $query->whereBetween('percentage', [0, 5]);
                break;
            case '6-10':
                $query->whereBetween('percentage', [6, 10]);
                break;
            case '11-15':
                $query->whereBetween('percentage', [11, 15]);
                break;
            case '16-20':
                $query->whereBetween('percentage', [16, 20]);
                break;
            case '21+':
                $query->where('percentage', '>=', 21);
                break;
        }
    }
    $taxLogs = DB::table('tax_activity_logs')
    ->orderBy('action_date', 'desc')
    ->limit(200)
    ->get();
    
    $taxes = $query->orderBy('created_at', 'desc')->get();
    
    return view('hrms.hr.sales.taxes.index', compact('taxes','taxLogs'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.hr.sales.payments.index');
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
        'tax' => 'required|string|max:255|unique:taxes,name',
        'percent' => 'required|numeric|min:0|max:100',
    ], [
        'tax.required' => 'The tax name is required.',
        'tax.unique' => 'This tax already exists.',
        'percent.required' => 'The percent field is required.',
        'percent.numeric' => 'The percentage must be a valid number.',
        'percent.min' => 'The percentage cannot be less than 0.',
        'percent.max' => 'The percentage cannot be more than 100.',
    ]);

    $taxUpperCase = strtoupper($validatedData['tax']);

    // Insert Tax
    $taxId = DB::table('taxes')->insertGetId([
        'name'       => $taxUpperCase,
        'percentage' => $validatedData['percent'],
        'status'     => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // LOG HERE
    $this->logTaxAction(
        $taxId,
        'created',
        'Tax created successfully',
        $taxUpperCase,
        $validatedData['percent'],
        'active'
    );

    Session::flash('messageType', 'success');
    Session::flash('message', 'Tax Created!');
    return redirect()->back();
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
    // Tax Edit
    $taxes = DB::table('taxes')->where('id',$id)->first();
    if (!$taxes) {
        // If it's an AJAX request, return JSON error
        if (request()->ajax()) {
            return response()->json(['error' => 'Tax not found.'], 404);
        }
        
        // Session message
        Session::flash('messageType', 'error');
        Session::flash('message', 'Tax not found.');
        return redirect()->route('show-viewtax');
    }
    
    // If it's an AJAX request, return JSON
    if (request()->ajax()) {
        return response()->json($taxes);
    }
    
    return response()->json($taxes);
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
        'tax' => 'required|string|max:255|unique:taxes,name,' . $id,
        'percent' => 'required|numeric|between:0,100',
    ]);

    $updatedName = strtoupper($validatedData['tax']);

    // Update tax
    DB::table('taxes')
        ->where('id', $id)
        ->update([
            'name'       => $updatedName,
            'percentage' => $validatedData['percent'],
            'updated_at' => now(),
        ]);

    // LOG HERE
    $this->logTaxAction(
        $id,
        'updated',
        'Tax updated successfully',
        $updatedName,
        $validatedData['percent'],
        'active'
    );

    Session::flash('messageType', 'success');
    Session::flash('message', 'Tax Updated!');
    return redirect()->back();
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function destroy($id)
{
    try {
        $tax = DB::table('taxes')->where('id', $id)->first();

        if (!$tax) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found!'
            ]);
        }

        DB::table('taxes')->where('id', $id)->delete();

        // Log Action
        $this->logTaxAction(
            $id,
            'deleted',
            "Tax {$tax->name} deleted",
            $tax->name,
            $tax->percentage,
            $tax->status
        );

        return response()->json([
            'success' => true,
            'message' => 'Tax deleted successfully!',
            'id' => $id
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Delete failed: ' . $e->getMessage()
        ]);
    }
}


private function logTaxAction($taxId, $action, $details = null, $taxName = null, $percent = null, $status = null)
{
    try {
        DB::table('tax_activity_logs')->insert([
            'tax_id'     => $taxId,
            'action'     => $action,
            'tax_name'   => $taxName,
            'percentage' => $percent,
            'status'     => $status,
            'details'    => $details,
            'user_id'    => auth()->id() ?? 1,
            'user_name'  => auth()->user()->name ?? 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action_date'=> now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {
        \Log::error("Tax Log Failed: " . $e->getMessage());
    }
}

/**
 * Change tax status
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function changeTaxStatus(Request $request, $id)
{
    try {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $tax = DB::table('taxes')->where('id', $id)->first();
        
        if (!$tax) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found!'
            ], 404);
        }

        // Update the status
        DB::table('taxes')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        // Log the status change
        $this->logTaxAction(
            $id,
            'status_changed',
            "Tax status changed from {$tax->status} to {$request->status}",
            $tax->name,
            $tax->percentage,
            $request->status
        );

        return response()->json([
            'success' => true,
            'message' => 'Tax status updated successfully!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update tax status: ' . $e->getMessage()
        ], 500);
    }
}
    
}
