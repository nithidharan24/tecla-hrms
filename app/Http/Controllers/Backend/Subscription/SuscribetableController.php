<?php

namespace App\Http\Controllers\Backend\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuscribetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetching all subscribed companies from the subcompany table
        $subscribedCompanies = DB::table('subcompany')->get();
    
        // Returning the index view with subscribed companies data
        return view('hrms.Subscription.subtable.index', compact('subscribedCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return create view
        return view('hrms.Subscription.subtable.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Store logic here
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
        // Get the subscription
        $subscription = DB::table('subcompany')->where('id', $id)->first();
        
        if (!$subscription) {
            return redirect()->route('subscribetable.index')
                ->with('error', 'Subscription not found!');
        }
        
        return view('hrms.Subscription.subtable.edit', compact('subscription'));
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
        // Update logic here
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
            // Delete the subscribed company
            $deleted = DB::table('subcompany')->where('id', $id)->delete();
            
            if ($deleted) {
                return redirect()->route('subscribetable.index')
                    ->with('success', 'Subscription deleted successfully!');
            } else {
                return redirect()->route('subscribetable.index')
                    ->with('error', 'Subscription not found or already deleted!');
            }
        } catch (\Exception $e) {
            return redirect()->route('subscribetable.index')
                ->with('error', 'Error deleting subscription: ' . $e->getMessage());
        }
    }
}