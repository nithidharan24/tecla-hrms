<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PFTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pftypes = DB::table('pf_types')->where('deleted_at',0)->get();
        return view('hrms.hr.sales.fundtype.index',compact('pftypes'));
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
        $request->validate([
            'pftypename' => 'required|string',
            'description' => 'required|string|max:500',
        ]);

        DB::table('pf_types')->insert([
            'type' => $request->input('pftypename'),
            'description' => $request->input('description'),
        ]);

        // Session message
        Session::flash('messageType', 'success');
        Session::flash('message', 'PF type Created!');
            
        // Redirect with a success message
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

        $pftype = DB::table('pf_types')->where('id', $id)->first();

        if (!$pftype) {
            // Session message
            Session::flash('messageType', 'error');
            Session::flash('message', 'Pf-type not found.');
            return redirect()->route('pftype.index');
        }
    
        // Validation rules
        $request->validate([
            'edit_pftypename' => 'required|string',
            'edit_description' => 'required|string|max:500',
        ]);
    
        // Update the client details
        DB::table('pf_types')->where('id', $id)->update([
            'type' => $request->input('edit_pftypename'),
            'description' => $request->input('edit_description'),
        ]);

        // Session message
        Session::flash('messageType', 'success');
        Session::flash('message', 'Pf-type updated successfully!');
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
        DB::table('pf_types')->where('id', $id)->delete();
        return response(['status' => 'success', 'message' => 'Deleted Successfully!','id'=>$id]);
    }

    public function getData($id){
        $pftype = DB::table('pf_types')->where('id',$id)->first();
        if (!$pftype) {
            // Session message
            Session::flash('messageType', 'error');
            Session::flash('message', 'PF-type not found.');
            return redirect()->route('pftype.index');
        }
        return response()->json($pftype);
    }

    public function changeStatus(Request $request)
    {
        $pftype = DB::table('pf_types')->where('id', $request->id)->first();
        if ($pftype) {
            DB::table('pf_types')->where('id', $request->id)->update(['status' => $request->status]);
        }
        return response(['status' => $pftype->status == 'active' ? 1 : 2, 'message' => 'Status has been updated!']);
    }
}
