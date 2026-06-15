<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\PayrollActivityLogger;

class PayrollItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all additions and employees
        $additions = DB::table('additions')->get();
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();
        
        // Fetch all overtime entries and deductions
        $overtimes = DB::table('overtimes')->get();
        $deductions = DB::table('deductions')->get();
    
        // Pass data to the view
        return view('hrms.hr.PayRoll.PayrollItems.Additions.index', compact('additions', 'employees', 'overtimes', 'deductions'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_amount' => 'nullable|numeric',
            'addition_assignee' => 'required|string',
        ]);

        // Prepare data for insertion
        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'unit_amount' => $request->unit_amount,
        ];

        // Handle employee assignment
        $data['employee_id'] = ($request->addition_assignee === 'all_employees')
            ? DB::table('allemployees')->pluck('id')->implode(',')
            : $request->employee_id;

        // Insert into additions
        DB::table('additions')->insert($data);

        // Log activity
        PayrollActivityLogger::logCreate(
            'Additions',
            $request->name,
            $request->employee_id ?: null,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Addition created successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all additions and employees
        $additions = DB::table('additions')->get();
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();

        return view('hrms.hr.PayRoll.PayrollItems.Additions.create', compact('additions', 'employees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the addition and employees for editing
        $addition = DB::table('additions')->find($id);
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();

        if (!$addition) {
            return redirect()->route('payroll.index')->with('error', 'Addition not found.');
        }

        return view('hrms.hr.PayRoll.PayrollItems.Additions.edit', compact('addition', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Get old data before update
        $oldAddition = DB::table('additions')->find($id);
        $oldData = $oldAddition ? (array)$oldAddition : null;

        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_amount' => 'nullable|numeric',
            'employee_assignment' => 'required|string',
        ]);

        // Prepare data for update
        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'unit_amount' => $request->unit_amount,
        ];

        // Handle employee assignment
        if ($request->employee_assignment === 'all_employees') {
            $data['employee_id'] = DB::table('allemployees')->pluck('id')->implode(',');
        } elseif ($request->employee_assignment === 'single_employee') {
            $data['employee_id'] = $request->employee_id;
        } else {
            $data['employee_id'] = null;
        }

        // Update addition
        DB::table('additions')->where('id', $id)->update($data);

        // Log activity
        PayrollActivityLogger::logUpdate(
            'Additions',
            $request->name,
            $request->employee_id ?: null,
            $oldData,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Addition updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Get data before deletion
        $addition = DB::table('additions')->find($id);
        if ($addition) {
            $oldData = (array)$addition;
            
            // Log activity
            PayrollActivityLogger::logDelete(
                'Additions',
                $addition->name,
                $addition->employee_id ?: null,
                $oldData
            );
        }

        // Delete the addition
        DB::table('additions')->where('id', $id)->delete();

        return redirect()->route('payroll.combined')->with('success', 'Addition deleted successfully.');
    }

    /**
     * Show form to create overtime
     */
    public function createOvertime()
    {
        $hierarchies = DB::table('hierarchies')->get();
        return view('hrms.hr.PayRoll.PayrollItems.Overtime.create', compact('hierarchies'));
    }

    /**
     * Store overtime entry
     */
    public function storeOvertime(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'hierarchy_id' => 'required|exists:hierarchies,id',
        ]);

        $data = [
            'name' => $request->name,
            'rate_type' => 'Per Hour',
            'rate' => $request->rate,
            'hierarchy_id' => $request->hierarchy_id,
        ];

        DB::table('overtimes')->insert($data);
        
        // Log activity
        PayrollActivityLogger::logCreate(
            'Overtime',
            $request->name,
            null,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Overtime entry created successfully.');
    }

    /**
     * Edit overtime entry
     */
    public function overtimeedit($id)
    {
        $overtime = DB::table('overtimes')->find($id);
        $hierarchies = DB::table('hierarchies')->get();
        
        if (!$overtime) {
            return redirect()->route('payroll.index')->with('error', 'Overtime not found.');
        }
        
        return view('hrms.hr.PayRoll.PayrollItems.Overtime.edit', compact('overtime', 'hierarchies'));
    }

    /**
     * Update overtime entry
     */
    public function overtimeupdate(Request $request, $id)
    {
        // Get old data before update
        $oldOvertime = DB::table('overtimes')->find($id);
        $oldData = $oldOvertime ? (array)$oldOvertime : null;

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'hierarchy_id' => 'required|exists:hierarchies,id',
        ]);

        $data = [
            'name' => $request->name,
            'rate_type' => 'Per Hour',
            'rate' => $request->rate,
            'hierarchy_id' => $request->hierarchy_id,
        ];

        DB::table('overtimes')->where('id', $id)->update($data);

        // Log activity
        PayrollActivityLogger::logUpdate(
            'Overtime',
            $request->name,
            null,
            $oldData,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Overtime entry updated successfully.');
    }

    /**
     * Store deduction
     */
    public function deductionstore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_amount' => 'nullable|numeric',
           
        ]);

        // Prepare data for insertion
        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'unit_amount' => $request->unit_amount,
        ];

        // Handle employee assignment
        $data['employee_id'] = ($request->addition_assignee === 'all_employees')
            ? DB::table('allemployees')->pluck('id')->implode(',')
            : $request->employee_id;

        
        DB::table('deductions')->insert($data);

        // Log activity
        PayrollActivityLogger::logCreate(
            'Deductions',
            $request->name,
            $request->employee_id ?: null,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Deduction created successfully.');
    }

    /**
     * Show form to create deduction
     */
    public function deductioncreate()
    {
        // Fetch all deductions and employees
        $deductions = DB::table('deductions')->get();
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();

        return view('hrms.hr.PayRoll.PayrollItems.Deductions.create', compact('deductions', 'employees'));
    }

    /**
     * Edit deduction
     */
    public function deductionedit($id)
    {
        $deductions = DB::table('deductions')->where('id',$id)->first();

        if(!$deductions){
            abort(404);
        }
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();

        if (!$deductions) {
            return redirect()->route('deductions.index')->with('error', 'Deduction not found.');
        }

        return view('hrms.hr.PayRoll.PayrollItems.Deductions.edit', compact('employees', 'deductions'));
    }

    /**
     * Update deduction
     */
    public function deductionupdate(Request $request, $id)
    {
        // Get old data before update
        $oldDeduction = DB::table('deductions')->find($id);
        $oldData = $oldDeduction ? (array)$oldDeduction : null;

        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_amount' => 'nullable|numeric',
            'employee_assignment' => 'required|string',
        ]);

        // Prepare data for update
        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'unit_amount' => $request->unit_amount,
        ];

        // Handle employee assignment
        if ($request->employee_assignment === 'all_employees') {
            $data['employee_id'] = DB::table('allemployees')->pluck('id')->implode(',');
        } elseif ($request->employee_assignment === 'single_employee') {
            $data['employee_id'] = $request->employee_id;
        } else {
            $data['employee_id'] = null;
        }

        // Update deduction
        DB::table('deductions')->where('id', $id)->update($data);

        // Log activity
        PayrollActivityLogger::logUpdate(
            'Deductions',
            $request->name,
            $request->employee_id ?: null,
            $oldData,
            $data
        );

        return redirect()->route('payroll.combined')->with('success', 'Deduction updated successfully.');
    }

    /**
     * Delete deduction
     */
    public function deductiondestroy($id)
    {
        // Get data before deletion
        $deduction = DB::table('deductions')->find($id);
        if ($deduction) {
            $oldData = (array)$deduction;
            
            // Log activity
            PayrollActivityLogger::logDelete(
                'Deductions',
                $deduction->name,
                $deduction->employee_id ?: null,
                $oldData
            );
        }

        // Delete the deduction
        DB::table('deductions')->where('id', $id)->delete();

        return redirect()->route('payroll.combined')->with('success', 'Deduction deleted successfully.');
    }

    /**
     * Delete overtime entry
     */
    public function overtimedestroy($id)
    {
        // Get data before deletion
        $overtime = DB::table('overtimes')->find($id);
        
        if ($overtime) {
            $oldData = (array)$overtime;
            
            // Log activity
            PayrollActivityLogger::logDelete(
                'Overtime',
                $overtime->name,
                null,
                $oldData
            );
        }
        
        // Delete the overtime entry
        DB::table('overtimes')->where('id', $id)->delete();

        return redirect()->route('payroll.combined')->with('success', 'Overtime entry deleted successfully.');
    }
}