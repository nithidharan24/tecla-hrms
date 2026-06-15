<?php
namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeavesSettingsController extends Controller
{// Method to show the leave settings page
    public function index()
    {
        // Fetch leave settings from the database
        $settings = DB::table('annual_leaves')->first();
        
        // Fetch sick leave settings
        $sickLeave = DB::table('medical_leaves')->select('sick')->first();
        
        // Fetch hospitalisation leave settings
        $hospitalisationLeave = DB::table('medical_leaves')->select('hospitalisation')->first();
        
        // Fetch maternity leave settings
        $maternityLeave = DB::table('medical_leaves')->select('maternity')->first();
        
        // Fetch paternity leave settings
        $paternityLeave = DB::table('medical_leaves')->select('paternity')->first();
        
        // Fetch permission hours settings
        $permissionHours = DB::table('annual_leaves')->select('permission_hours')->first();
        
        $lopLeaves = DB::table('lop_leaves')->select('id', 'days', 'carry_forward', 'carry_forward_max', 'earned_leaves')->first();
        $employees = DB::table('allemployees')
            ->select('employeeid', 'firstname', 'lastname')
            ->get();
        $customPolicies = DB::table('annual_leaves_custom_policies')->get();
        
        return view('hrms.Employee.LeaveSettings.create', compact(
            'settings',
            'customPolicies', 
            'sickLeave', 
            'hospitalisationLeave', 
            'maternityLeave', 
            'paternityLeave', 
            'permissionHours',
            'lopLeaves',
            'employees'
        ));
    }

    public function updateAnnualDays(Request $request)
    {
       
        DB::table('annual_leaves')
            ->where('id', $request->id)
            ->update(['days' => $request->annual_days]);

        return redirect()->back()->with('success', 'Annual days updated successfully!');
    }

    public function updateCarryForward(Request $request)
    {
       

        DB::table('annual_leaves')
            ->where('id', $request->id)
            ->update([
                'carry_forward' => $request->carry_forward,
                'carry_forward_max' => $request->carry_forward_max,
            ]);

        return redirect()->back()->with('success', 'Carry forward settings updated successfully!');
    }
    public function updatePermissionHours(Request $request)
    {
        $request->validate([
            'permission_hours' => 'required|integer|min:0|max:100'
        ]);

        // Update the permission hours setting in the database
        DB::table('annual_leaves')
            ->update(['permission_hours' => $request->input('permission_hours')]);

        return redirect()->back()->with('success', 'Permission hours setting updated successfully!');
    }

    public function updateEarnedLeaves(Request $request)
    {
       
        DB::table('annual_leaves')
            ->where('id', $request->id)
            ->update(['earned_leaves' => $request->earned_leaves]);

        return redirect()->back()->with('success', 'Earned leaves updated successfully!');
    }
    public function update(Request $request)
    {
        // Check if settings exist in the database
        $settings = DB::table('annual_leaves')->first(); // Change 'leave_settings' to your actual table name

        if ($settings) {
            // Update the existing settings
            DB::table('annual_leaves')->where('id', $settings->id)->update([
                'days' => $request->input('annual_days'),
                'carry_forward' => $request->input('carry_forward'),
                'carry_forward_max' => $request->input('carry_forward_max'),
                'earned_leaves' => $request->input('earned_leaves'),
            ]);
        } else {
            // Optionally, you can insert if no settings exist
            DB::table('annual_leaves')->insert([
                'days' => $request->input('annual_days'),
                'carry_forward' => $request->input('carry_forward'),
                'carry_forward_max' => $request->input('carry_forward_max'),
                'earned_leaves' => $request->input('earned_leaves'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('leave-settings.index')->with('success', 'Leave settings updated successfully');
    }
    
    
    public function updateSick(Request $request)
    {
        DB::table('medical_leaves')
            ->update(['sick' => $request->input('sick')]);
    
        return redirect()->back()->with('success', 'Sick leave setting updated successfully!');
    }
    
public function updateHospitalisation(Request $request)
{
    
    // Update the hospitalisation leave setting in the database
    DB::table('medical_leaves')
        ->update(['hospitalisation' => $request->input('hospitalisation')]); // Remove the where clause if you want to update all records

    // Optionally, return a success response
    return redirect()->back()->with('success', 'Hospitalisation leave setting updated successfully!');
}

public function updateMaternity(Request $request)
{
    

    // Update the maternity leave setting in the database
    DB::table('medical_leaves')
        ->update(['maternity' => $request->input('maternity')]); // Remove the where clause if you want to update all records

    // Optionally, return a success response
    return redirect()->back()->with('success', 'Maternity leave setting updated successfully!');
}

public function updatePaternity(Request $request)
{
    

    // Update the paternity leave setting in the database
    DB::table('medical_leaves')
        ->update(['paternity' => $request->input('paternity')]); // Remove the where clause if you want to update all records

    // Optionally, return a success response
    return redirect()->back()->with('success', 'Paternity leave setting updated successfully!');
}
public function updateLOPDays(Request $request)
    {
        

        DB::table('lop_leaves')
            ->where('id', $request->id)
            ->update(['days' => $request->lop_days]);

        return redirect()->back()->with('success', 'LOP days updated successfully!');
    }

    public function updateLOPCarryForward(Request $request)
    {
        

        DB::table('lop_leaves')
            ->where('id', $request->id)
            ->update([
                'carry_forward' => $request->lop_carry_forward,
                'carry_forward_max' => $request->lop_carry_forward_max,
            ]);

        return redirect()->back()->with('success', 'Carry forward settings updated successfully!');
    }
    
   public function updateLOPEarnedLeaves(Request $request)
{
   

    DB::table('lop_leaves')
        ->where('id', $request->id)
        ->update(['earned_leaves' => $request->lop_earned_leaves]);

    return redirect()->back()->with('success', 'LOP earned leaves setting updated successfully!');
} 
public function updateMaxAllowed(Request $request)
{
    // Validate input
    $request->validate([
        'max_allowed' => 'required|integer|min:0'
    ]);

    // Update the max_allowed column in annual_leaves table
    DB::table('annual_leaves')
        ->update(['max_allowed' => $request->input('max_allowed')]);

    return redirect()->back()->with('success', 'Maximum allowed leaves per month updated successfully!');
}

}
