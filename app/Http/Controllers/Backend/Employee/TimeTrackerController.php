<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TimeTrackerController extends Controller
{
    
    public function index(Request $request)
    {
        // CLIENTS
        $company = DB::table('clients')
            ->where('deleted_at',0)
            ->pluck('company_name','client_id');
    
        $clients = applyBranchFilter(
            DB::table('clients')->where('deleted_at',0),
            'clients'
        )->get();
    
    
        // PROJECTS
        $projectController = new \App\Http\Controllers\Backend\Project\ProjectController();
        $projectResponse = $projectController->index($request)->getData();
    
        $projects = $projectResponse['projects'];
        $leaders  = $projectResponse['leaders'];
        $statuses = $projectResponse['statuses'];
    
    
        $taskController = new \App\Http\Controllers\Backend\Project\TaskController();
        $tasks = $taskController->index(true);  // NOW returns array/collection
        $myTasks = $taskController->myTasks($request);

        return view('hrms.time-tracker.index', compact(
            'clients','company','projects','leaders','statuses','tasks','myTasks'
        ));
        
    }
    
    

  
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
