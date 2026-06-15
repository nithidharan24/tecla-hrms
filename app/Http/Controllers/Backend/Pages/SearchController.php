<?php

namespace App\Http\Controllers\Backend\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Assuming you are searching in the 'projects' table or similar
        $keyword = $request->input('keyword');
        $clients = DB::table('clients')->get();
        
        // Query the database for non-deleted projects
        $projects = DB::table('projects')
                        ->where('deleted_at',0) // Only get projects that are not soft deleted
                        ->where(function($query) use ($keyword) {
                            $query->where('projectname', 'LIKE', "%{$keyword}%")
                                  ->orWhere('description', 'LIKE', "%{$keyword}%");
                        })
                        ->get();
    
        // Return the view with results
        return view('hrms.Pages.search.index', compact('projects', 'keyword','clients'));
    }
    


    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
