<?php

namespace App\Http\Controllers\Backend\Tickets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TicketAssigneesController extends Controller
{
    public function index()
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->route('tickets.index')->with('error','Access denied.');

        $assignableEmployees = DB::table('ticket_assignees as ta')
            ->join('allemployees as emp', 'ta.employee_id', '=', 'emp.id')
            ->leftJoin('designation', 'emp.designation', '=', 'designation.id')
            ->leftJoin('department', 'emp.department', '=', 'department.id')
            ->leftJoin('branches', 'emp.branch_id', '=', 'branches.id')
            ->where('emp.deleted_at', 0)
            ->select(
                'ta.*',
                'emp.firstname',
                'emp.lastname',
                'department.department as department_name',
                'designation.designation as designation_name',
                'branches.name as branch_name',
                'ta.role as assignee_role'
            )
            ->orderBy('ta.priority', 'asc')
            ->get();

        return view('hrms.Employee.Tickets.assignees', compact('assignableEmployees'));
    }

    public function create()
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->route('ticket-assignees.index')->with('error','Access denied.');

        // Get all departments
        $departments = DB::table('department')
            ->orderBy('department', 'asc')
            ->get();

        // Get employees not already assigned (initially empty, will be populated via AJAX)
        $allEmployees = collect([]);

        return view('hrms.Employee.Tickets.edit-assignee', [
            'assignee' => null,
            'departments' => $departments,
            'allEmployees' => $allEmployees
        ]);
    }

    // New method to get employees by department via AJAX
    public function getEmployeesByDepartment($departmentId)
    {
        $employees = DB::table('allemployees as emp')
            ->leftJoin('department', 'emp.department', '=', 'department.id')
            ->leftJoin('designation', 'emp.designation', '=', 'designation.id')
            ->where('emp.deleted_at', 0)
            ->where('emp.status', 'active')
            ->where('emp.department', $departmentId)
            ->whereNotIn('emp.id', function($q){ 
                $q->select('employee_id')->from('ticket_assignees'); 
            })
            ->select(
                'emp.id',
                'emp.firstname',
                'emp.lastname',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->orderBy('emp.firstname', 'asc')
            ->get();

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->back()->with('error','Access denied.');

        $request->validate([
            'employee_id'=>'required|exists:allemployees,id',
            'role'=>'nullable|string|max:255',
            'priority'=>'required|integer|min:1',
            'is_active'=>'required|boolean'
        ]);

        DB::table('ticket_assignees')->insert([
            'employee_id'=>$request->employee_id,
            'role'=>$request->role,
            'priority'=>$request->priority,
            'is_active'=>$request->is_active,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        return redirect()->route('ticket-assignees.index')->with('success','Employee assigned successfully.');
    }

    public function edit($id)
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->route('ticket-assignees.index')->with('error','Access denied.');

        $assignee = DB::table('ticket_assignees as ta')
            ->join('allemployees as emp', 'ta.employee_id','=','emp.id')
            ->leftJoin('department', 'emp.department', '=', 'department.id')
            ->leftJoin('designation', 'emp.designation', '=', 'designation.id')
            ->select(
                'ta.*',
                'emp.firstname',
                'emp.lastname',
                'emp.department',
                'emp.designation',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->where('ta.id',$id)->first();

        if(!$assignee) return redirect()->route('ticket-assignees.index')->with('error','Assignee not found.');

        // Get all departments for the dropdown
        $departments = DB::table('department')
            ->where('status', 1)
            ->orderBy('department', 'asc')
            ->get();

        return view('hrms.Employee.Tickets.edit-assignee', compact('assignee', 'departments'));
    }

    public function update(Request $request,$id)
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->back()->with('error','Access denied.');

        $request->validate([
            'role'=>'nullable|string|max:255',
            'priority'=>'required|integer|min:1',
            'is_active'=>'required|boolean'
        ]);

        DB::table('ticket_assignees')->where('id',$id)->update([
            'role'=>$request->role,
            'priority'=>$request->priority,
            'is_active'=>$request->is_active,
            'updated_at'=>now()
        ]);

        return redirect()->route('ticket-assignees.index')->with('success','Assignee updated successfully.');
    }

    public function destroy($id)
    {
        $role = Session::get('role');
        if($role !== 'admin') return redirect()->back()->with('error','Access denied.');

        DB::table('ticket_assignees')->where('id',$id)->delete();
        return redirect()->route('ticket-assignees.index')->with('success','Assignee removed successfully.');
    }
}