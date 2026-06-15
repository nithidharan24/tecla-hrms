<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Str;

class ProvidentFundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pf_funds = DB::table('provident_fund')
        ->join('allemployees', 'provident_fund.employee_name', '=', 'allemployees.employeeid') // Adjusted employee ID field
        ->join('designation', 'allemployees.designation', '=', 'designation.id')
        ->select('provident_fund.*', DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"), 'designation.designation as designation_name')
        ->get();
    
        return view('hrms.hr.sales.fund.index',compact('pf_funds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = DB::table('allemployees')->where('deleted_at',0)->get();
        $pftype  = DB::table('pf_types')->where('deleted_at',0)->get();
    
        return view('hrms.hr.sales.fund.create',compact('employees','pftype'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Id = $this->generateID('epfid');
        $request->validate([
            'employee_name' => 'required',
            'pf_type' => 'required',
            'employee_share_amount' => 'nullable|required_if:pf_type,Fixed Amount|numeric',
            'organization_share_amount' => 'nullable|required_if:pf_type,Fixed Amount|numeric',
            'employee_share_percent' => 'nullable|required_if:pf_type,Percentage Based|numeric|between:0,100',
            'organization_share_percent' => 'nullable|required_if:pf_type,Percentage Based|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
        ], [
            'employee_name.required' => 'Please select an employee.',
            'pf_type.required' => 'Please select a provident fund type.',
        ]);

        $data = [
            'pf_id' => $Id,
            'employee_name' => $request->employee_name,
            'pf_type' => $request->pf_type,
            'description' => $request->description,
        ];
    
        if ($request->pf_type === 'Fixed Amount') {
            $data['employee_share_amount'] = $request->employee_share_amount;
            $data['organization_share_amount'] = $request->organization_share_amount;
            $data['employee_share_percent'] = null;
            $data['organization_share_percent'] = null;
        } elseif ($request->pf_type === 'Percentage Based') {
            $data['employee_share_amount'] = null;
            $data['organization_share_amount'] = null;
            $data['employee_share_percent'] = $request->employee_share_percent;
            $data['organization_share_percent'] = $request->organization_share_percent;
        }

        DB::table('provident_fund')->insert($data);
        Session::flash('messageType', 'success');
        Session::flash('message', 'Successfully Created!');
        return redirect()->route('providentfund.index');
    
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
        $employees = DB::table('allemployees')->where('deleted_at',0)->get();
        $pf_fund = DB::table('provident_fund')->where('pf_id',$id)->first();
        return view('hrms.hr.sales.fund.edit',compact('employees','pf_fund'));
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
            'employee_name' => 'required',
            'pf_type' => 'required',
            'employee_share_amount' => 'nullable|required_if:pf_type,Fixed Amount|numeric',
            'organization_share_amount' => 'nullable|required_if:pf_type,Fixed Amount|numeric',
            'employee_share_percent' => 'nullable|required_if:pf_type,Percentage Based|numeric|between:0,100',
            'organization_share_percent' => 'nullable|required_if:pf_type,Percentage Based|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
        ], [
            'employee_name.required' => 'Please select an employee.',
            'pf_type.required' => 'Please select a provident fund type.',
        ]);
        
        $data = [
            'employee_name' => $request->employee_name,
            'pf_type' => $request->pf_type,
            'description' => $request->description,
        ];
        
        if ($request->pf_type === 'Fixed Amount') {
            $data['employee_share_amount'] = $request->employee_share_amount;
            $data['organization_share_amount'] = $request->organization_share_amount;
            $data['employee_share_percent'] = null;
            $data['organization_share_percent'] = null;
        } elseif ($request->pf_type === 'Percentage Based') {
            $data['employee_share_amount'] = null;
            $data['organization_share_amount'] = null;
            $data['employee_share_percent'] = $request->employee_share_percent;
            $data['organization_share_percent'] = $request->organization_share_percent;
        }
        
        // Update the existing record based on the 'pf_id'
        DB::table('provident_fund')
            ->where('pf_id', $id)
            ->update($data);
        
        // Flash success message and redirect
        Session::flash('messageType', 'success');
        Session::flash('message', 'Successfully Updated!');
        return redirect()->route('providentfund.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('provident_fund')->where('pf_id', $id)->delete();
        return response(['status' => 'success', 'message' => 'Deleted Successfully!','id'=>$id]);
    }

    private function generateID($base)
    {
        // Get the last client ID or set the starting number
        $lastID = DB::table('provident_fund')
        ->whereNotNull('pf_id')
        ->orderBy('id', 'desc')
        ->value('pf_id');

        // Initialize base ID
        $baseID = Str::upper($base).'-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        // Generate new client ID
        do {
            $newID = $baseID . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('provident_fund')->where('pf_id', $newID)->exists());

        return $newID;
    }

    public function changeStatus(Request $request, $id){
        $pfund = DB::table('provident_fund')->where('pf_id', $id)->first();
        if ($pfund) {
            DB::table('provident_fund')->where('pf_id', $id)->update(['status' => $request->status]);
        }
        return response(['status' => $pfund->status == 'approved' ? 1 : 2, 'message' => 'Status has been updated!']);
    }

}
