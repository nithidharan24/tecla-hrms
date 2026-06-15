<?php

namespace App\Http\Controllers\Backend\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch active employees (not deleted)
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'designation')
            ->get();
            
            
        // Fetch projects
        $projects = DB::table('projects')
            ->select('id', 'projectname')
            ->get();

        return view('hrms.team-management.create', compact('employees', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
    
        $team = DB::table('teams')->insertGetId([
            'team_name' => $request->team_name,
            'team_description' => $request->team_description,
            'manager_id' => $request->manager_id,
            'team_leader_id' => $request->team_leader_id,
            'project_id' => $request->project_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        foreach ($request->employees as $employee_id) {
            DB::table('team_members')->insert([
                'team_id' => $team,
                'employee_id' => $employee_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        return redirect()->route('team.index')->with('success', 'Team created successfully.');
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
