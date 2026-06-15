<?php

namespace App\Http\Controllers\Backend\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuscribecompanyController extends Controller
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
            ->leftJoin('modules', 'plan_modules.module_id', '=', 'modules.id')
            ->select(
                'subadmin.id',
                'subadmin.plan_name',
                'subadmin.plan_type',
                'subadmin.plan_amount',
                'subadmin.total_users',
                'subadmin.total_projects',
                'subadmin.total_storage',
                'subadmin.description',
                DB::raw('GROUP_CONCAT(DISTINCT modules.name SEPARATOR ", ") as modules')
            )
            ->groupBy(
                'subadmin.id',
                'subadmin.plan_name',
                'subadmin.plan_type',
                'subadmin.plan_amount',
                'subadmin.total_users',
                'subadmin.total_projects',
                'subadmin.total_storage',
                'subadmin.description'
            )
            ->get();
    
        // 🔥 Get active subscription
        $activeSubscription = DB::table('subcompany')
            ->where('status', 'active')
            ->first();
    
        return view('hrms.Subscription.subcompany.index', compact('plans','activeSubscription'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    
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
             'plan_id' => 'required'
         ]);
     
         $plan = DB::table('subadmin')->where('id', $request->plan_id)->first();
     
         if (!$plan) {
             return redirect()->back()->with('error', 'Plan not found.');
         }
     
         $startDate = Carbon::now();
     
         if ($plan->plan_type === 'monthly') {
             $endDate = $startDate->copy()->addMonth();
             $planDuration = "1 month";
         } else {
             $endDate = $startDate->copy()->addYear();
             $planDuration = "1 year";
         }
     
         // 🔥 JUST UPDATE FIRST ROW
         DB::table('subcompany')
             ->limit(1)
             ->update([
                 'plan_id'       => $plan->id,
                 'plan_name'     => $plan->plan_name,
                 'users'         => $plan->total_users,
                 'plan_duration' => $planDuration,
                 'start_date'    => $startDate,
                 'end_date'      => $endDate,
                 'amount'        => $plan->plan_amount,
                 'status'        => 'active',
                 'updated_at'    => now()
             ]);
     
         return redirect()->back()->with('success', 'Plan upgraded successfully!');
     }
     

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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


public function edit($id)
{
    $company = DB::table('subcompany')->where('id', $id)->first();
    if (!$company) {
        return redirect()->route('subscribecompany.index')->with('error', 'Company not found.');
    }

    // fetch all plans
    $plans = DB::table('subadmin')->get();

    // all modules
    $allModules = DB::table('modules')->get();

    // modules subscribed under this company's plan
    $subscribedModules = DB::table('plan_modules')
        ->where('plan_id', $company->plan_id)
        ->pluck('module_id')
        ->toArray();

    return view('hrms.Subscription.subcompany.edit', compact('company', 'plans', 'allModules', 'subscribedModules'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'company_name'   => 'required|string|max:255',
        'company_number' => 'required|string|max:50',
        'company_address'=> 'required|string|max:500',
        'plan_id'        => 'required|integer',
        'status'         => 'required|in:active,inactive,expired',
    ]);

    $plan = DB::table('subadmin')->where('id', $request->plan_id)->first();
    if (!$plan) {
        return redirect()->back()->with('error', 'Plan not found.');
    }

    // Update company details
    DB::table('subcompany')->where('id', $id)->update([
        'company_name'   => $request->company_name,
        'company_number' => $request->company_number,
        'company_address'=> $request->company_address,
        'users'          => $request->users,  // ✅ update users
        'plan_id'        => $plan->id,
        'plan_name'      => $plan->plan_name,
        'amount'         => $plan->plan_amount,
        'status'         => $request->status,
        'updated_at'     => now(),
    ]);

    // Update plan_modules (sync modules)
    DB::table('plan_modules')->where('plan_id', $plan->id)->delete();
    if ($request->has('modules')) {
        foreach ($request->modules as $moduleId) {
            DB::table('plan_modules')->insert([
                'plan_id'   => $plan->id,
                'module_id' => $moduleId,
            ]);
        }
    }

    // ✅ Store plan_id and users in session
    session([
        'plan_id' => $plan->id,
        'users'   => $request->users,
    ]);
    return redirect()->route('sub.index')->with('success', 'Subscribed company updated successfully.');
}


    public function destroy($id)
    {
        //
    }


}
