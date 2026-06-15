<?php

namespace App\Http\Controllers\Backend\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = DB::table('subadmin')
            ->leftJoin('plan_modules', 'subadmin.id', '=', 'plan_modules.plan_id')
            ->leftJoin('modules', 'plan_modules.module_id', '=', 'modules.id') // join modules table
            ->select(
                'subadmin.id',
                'subadmin.plan_name',
                'subadmin.plan_type',
                'subadmin.plan_amount',
                'subadmin.total_users',
                'subadmin.total_projects',
                'subadmin.total_storage',
                'subadmin.description',
                'subadmin.status',
                'subadmin.created_at',
                'subadmin.updated_at',
                DB::raw('GROUP_CONCAT(modules.name SEPARATOR ", ") as modules') // concat names instead of IDs
            )
            ->groupBy(
                'subadmin.id',
                'subadmin.plan_name',
                'subadmin.plan_type',
                'subadmin.plan_amount',
                'subadmin.total_users',
                'subadmin.total_projects',
                'subadmin.total_storage',
                'subadmin.description',
                'subadmin.status',
                'subadmin.created_at',
                'subadmin.updated_at'
            )
            ->get();
    
        return view('hrms.Subscription.subadmin.index', compact('plans'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.Subscription.subadmin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_amount' => 'required|numeric',
            'plan_type' => 'required|string|max:255',
            'num_users' => 'required|string|max:255',
          
            'plan_description' => 'required|string|max:500',
          
            'modules' => 'required|array'
        ]);
    
        $planId = DB::table('subadmin')->insertGetId([
            'plan_name' => $request->plan_name,
            'plan_amount' => $request->plan_amount,
            'plan_type' => $request->plan_type,
            'total_users' => $request->num_users,
            'total_projects' => $request->num_projects,
            'total_storage' => $request->storage_space,
            'description' => $request->plan_description,
          
           
        ]);
    
        foreach ($request->modules as $moduleId) {
            DB::table('plan_modules')->insert([
                'plan_id' => $planId,
                'module_id' => $moduleId,
              
            ]);
        }
    
        return redirect()->route('subscribe.index')->with('success', 'Plan with modules added successfully!');
    }
    
    


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
{
    // Fetch the subscription details for the given ID from the subcompany table
    $subscription = DB::table('subcompany')->where('id', $id)->first();

    // Check if subscription exists
    if (!$subscription) {
        return redirect()->route('subscribecompany.index')->with('error', 'Subscription not found.');
    }

    // Pass the subscription data to the view
    return view('hrms.Subscription.subcompany.show', compact('subscription'));
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = DB::table('subadmin')->where('id', $id)->first();
    
        if (!$plan) {
            return redirect()->route('subscribe.index')->with('error', 'Plan not found');
        }
    
        // All available modules
        $allModules = DB::table('modules')->get();
    
        // Modules already assigned to this plan
        $planModules = DB::table('plan_modules')
            ->where('plan_id', $id)
            ->pluck('module_id')
            ->toArray();
    
        return view('hrms.Subscription.subadmin.edit', compact('plan', 'allModules', 'planModules'));
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
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_amount' => 'required|numeric',
            'plan_type' => 'required|string|in:monthly,annual',
            'total_users' => 'required|integer',
           
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'modules' => 'required|array'
        ]);
    
        // Update plan details
        DB::table('subadmin')->where('id', $id)->update([
            'plan_name' => $request->input('plan_name'),
            'plan_amount' => $request->input('plan_amount'),
            'plan_type' => $request->input('plan_type'),
            'total_users' => $request->input('total_users'),
            'total_projects' => $request->input('total_projects'),
            'total_storage' => $request->input('total_storage'),
            'description' => $request->input('description'),
            'status' => $request->input('status') ?? 'inactive',
           
        ]);
    
        // Update modules (delete old and insert new)
        DB::table('plan_modules')->where('plan_id', $id)->delete();
        foreach ($request->modules as $moduleId) {
            DB::table('plan_modules')->insert([
                'plan_id' => $id,
                'module_id' => $moduleId,
                
            ]);
        }
    
        return redirect()->route('subscribe.index')->with('success', 'Plan updated successfully with modules');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function subscribedCompanies()
    {
        // First, mark expired subscriptions automatically
        DB::table('subcompany')
            ->where('end_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update([
                'status' => 'expired',
                'updated_at' => now(),
            ]);
    
        // Now fetch all companies with plan details
        $companies = DB::table('subcompany')
            ->join('subadmin', 'subcompany.plan_id', '=', 'subadmin.id')
            ->select(
                'subcompany.*',
                'subadmin.plan_name',
                'subadmin.plan_type',
                'subadmin.plan_amount'
            )
            ->orderBy('subcompany.created_at', 'desc')
            ->get();
    
        return view('hrms.Subscription.subcompany.show', compact('companies'));
    }
    
public function updateCompanyStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:active,inactive,expired',
    ]);

    DB::table('subcompany')->where('id', $id)->update([
        'status' => $request->status,
        'updated_at' => now()
    ]);

    return redirect()->back()->with('success', 'Company subscription status updated successfully.');
}
public function destroy($id)
{
    // Check if plan exists
    $plan = DB::table('subadmin')->where('id', $id)->first();
    if (!$plan) {
        return redirect()->route('subscribe.index')->with('error', 'Plan not found.');
    }

    // First delete related modules from plan_modules
    DB::table('plan_modules')->where('plan_id', $id)->delete();

    // Then delete the plan
    DB::table('subadmin')->where('id', $id)->delete();

    return redirect()->route('subscribe.index')->with('success', 'Plan deleted successfully.');
}

}
