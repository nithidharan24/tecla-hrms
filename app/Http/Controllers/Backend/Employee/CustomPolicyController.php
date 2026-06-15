<?php
namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class CustomPolicyController extends Controller
{
    public function create()
{
    $employees = DB::table('allemployees')->get(); // Fetch employees from the database
    return view('hrms.Employee.LeaveSettings.index', compact('employees')); // Adjust the view name accordingly
}
    public function storeCustomPolicy(Request $request)
{
   

    // Insert policies for each selected employee
    foreach ($request->customleave_to as $employeeId) {
        DB::table('annual_leaves_custom_policies')->insert([
            'policy_name' => $request->policy_name,
            'policy_days' => $request->days,
            'employee_id' => $employeeId,
            
        ]);
    }

    return redirect()->back()->with('success', 'Custom Policy added successfully!');
}
    public function updateCustomPolicy(Request $request, $id)
    {
       

        // Update the custom policy in the database
        DB::table('annual_leaves_custom_policies')
            ->where('id', $id)
            ->update([
                'policy_name' => $request->input('policy_name'),
                'policy_days' => $request->input('days'),
            ]);

        return redirect()->back()->with('success', 'Custom Policy updated successfully!');
    }

    public function destroy($id)
    {
        // Delete the custom policy
        DB::table('annual_leaves_custom_policies')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Custom Policy deleted successfully!');
    }

    
    
}
