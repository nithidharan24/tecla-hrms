<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\GoalAssigned; // Add this line+
use Illuminate\Support\Facades\Mail; // Make sure this is present

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branchId = getAdminBranchFilter();
        
        $query = DB::table('goals')
            ->leftJoin('allemployees as assigned_to', 'goals.assigned_to', '=', 'assigned_to.id')
            ->leftJoin('allemployees as assigned_by', 'goals.assigned_by', '=', 'assigned_by.id')
            ->leftJoin('department', 'goals.department_id', '=', 'department.id')
            ->select(
                'goals.*',
                DB::raw("CONCAT(assigned_to.firstname, ' ', assigned_to.lastname) as assigned_to_name"),
                DB::raw("CONCAT(assigned_by.firstname, ' ', assigned_by.lastname) as assigned_by_name"),
                'department.department as department_name'
            )
            ->where('goals.deleted_at', 0);

        // Apply filters
        if ($request->filled('goal_title')) {
            $query->where('goals.goal_title', 'like', '%' . $request->goal_title . '%');
        }

        if ($request->filled('goal_type')) {
            $query->where('goals.goal_type', $request->goal_type);
        }

        if ($request->filled('status')) {
            $query->where('goals.status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('goals.priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('goals.assigned_to', $request->assigned_to);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('goals.start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('goals.start_date', '<=', $request->to_date);
        }

        if ($branchId) {
            $query->where('goals.branch_id', $branchId);
        }

        $goals = $query->orderBy('goals.created_at', 'desc')->get();

        // Calculate counts for tabs
        $today = Carbon::now();
        
        $activeCount = $goals->whereIn('status', ['Not Started', 'In Progress'])->count();
        $completedCount = $goals->where('status', 'Completed')->count();
        
        $overdueCount = $goals->filter(function($goal) use ($today) {
            $endDate = Carbon::parse($goal->end_date);
            return $endDate->lt($today) && !in_array($goal->status, ['Completed', 'Cancelled']);
        })->count();
        
        $highPriorityCount = $goals->whereIn('priority', ['High', 'Critical'])->count();

        // Get employees for filter dropdown
        $employees = DB::table('allemployees')
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname')
            ->get();

        return view('hrms.performance.goals.index', compact(
            'goals', 
            'activeCount', 
            'completedCount', 
            'overdueCount', 
            'highPriorityCount',
            'employees'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.status', 'active')
            ->select('allemployees.*', 'department.department as department_name')
            ->get();

        $departments = DB::table('department')->get();

        return view('hrms.performance.goals.create', compact('employees', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{
    $request->validate([
        'goal_title' => 'required|string|max:255',
        'goal_description' => 'required|string',
        'goal_type' => 'required|in:Company,Department,Team,Individual',
        'category' => 'required|string|max:255',
        'assigned_to' => 'required|exists:allemployees,id',
        'start_date' => 'required|date_format:d-m-Y',
        'end_date' => 'required|date_format:d-m-Y|after:start_date',
        'target_value' => 'required|numeric|min:0',
        'unit' => 'required|string|max:50',
        'weightage' => 'required|integer|min:1|max:100',
        'priority' => 'required|in:Low,Medium,High,Critical',
        'review_cycle' => 'required|in:Monthly,Quarterly,Half-Yearly,Yearly',
        'remarks' => 'nullable|string'
    ]);

    $branchId = Session::get('branch_id');
    $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->format('Y-m-d');
    $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->format('Y-m-d');

    // Get assigned by employee details
    $assignedBy = DB::table('allemployees')->where('id', Auth::id())->first();
    $assignedByName = $assignedBy ? $assignedBy->firstname . ' ' . $assignedBy->lastname : 'System';

    // Insert goal and get the ID
    $goalId = DB::table('goals')->insertGetId([
        'goal_title' => $request->input('goal_title'),
        'goal_description' => $request->input('goal_description'),
        'goal_type' => $request->input('goal_type'),
        'category' => $request->input('category'),
        'assigned_to' => $request->input('assigned_to'),
        'assigned_by' => Auth::id(),
        'department_id' => $request->input('department_id'),
        'start_date' => $startDate,
        'end_date' => $endDate,
        'target_value' => $request->input('target_value'),
        'current_value' => 0,
        'unit' => $request->input('unit'),
        'weightage' => $request->input('weightage'),
        'progress_percentage' => 0,
        'status' => 'Not Started',
        'priority' => $request->input('priority'),
        'remarks' => $request->input('remarks'),
        'review_cycle' => $request->input('review_cycle'),
        'branch_id' => $branchId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Get the created goal for email
    $goal = DB::table('goals')->where('id', $goalId)->first();

    // Get assigned employee details for email
    $assignedEmployee = DB::table('allemployees')
        ->where('id', $request->input('assigned_to'))
        ->first();

    // Send email notification to the assigned employee
    if ($assignedEmployee && !empty($assignedEmployee->email)) {
        try {
            Mail::to($assignedEmployee->email)->send(new GoalAssigned(
                $goal,
                $assignedEmployee->firstname . ' ' . $assignedEmployee->lastname,
                $assignedByName
            ));
            
            \Log::info('Goal assignment email sent to: ' . $assignedEmployee->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send goal assignment email: ' . $e->getMessage());
        }
    }

    Session::flash('messageType', 'success');
    Session::flash('message', 'Goal created successfully and notification sent to employee!');

    return redirect()->route('goals.index');
}
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goal = DB::table('goals')
            ->leftJoin('allemployees as assigned_to', 'goals.assigned_to', '=', 'assigned_to.id')
            ->leftJoin('allemployees as assigned_by', 'goals.assigned_by', '=', 'assigned_by.id')
            ->leftJoin('department', 'goals.department_id', '=', 'department.id')
            ->select(
                'goals.*',
                DB::raw("CONCAT(assigned_to.firstname, ' ', assigned_to.lastname) as assigned_to_name"),
                DB::raw("CONCAT(assigned_by.firstname, ' ', assigned_by.lastname) as assigned_by_name"),
                'department.department as department_name'
            )
            ->where('goals.id', $id)
            ->where('goals.deleted_at', 0)
            ->first();

        if (!$goal) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Goal not found.');
            return redirect()->route('goals.index');
        }

        return view('hrms.performance.goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $goal = DB::table('goals')
            ->where('id', $id)
            ->where('deleted_at', 0)
            ->first();

        if (!$goal) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Goal not found.');
            return redirect()->route('goals.index');
        }

        $employees = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.department')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->select('allemployees.*', 'department.department as department_name')
            ->get();

        $departments = DB::table('department')->get();

        return view('hrms.performance.goals.edit', compact('goal', 'employees', 'departments'));
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
        $goal = DB::table('goals')->where('id', $id)->where('deleted_at', 0)->first();

        if (!$goal) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Goal not found.');
            return redirect()->route('goals.index');
        }

        $request->validate([
            'goal_title' => 'required|string|max:255',
            'goal_description' => 'required|string',
            'goal_type' => 'required|in:Company,Department,Team,Individual',
            'category' => 'required|string|max:255',
            'assigned_to' => 'required|exists:allemployees,id',
            'start_date' => 'required|date_format:d-m-Y',
            'end_date' => 'required|date_format:d-m-Y|after:start_date',
            'target_value' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'weightage' => 'required|integer|min:1|max:100',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'review_cycle' => 'required|in:Monthly,Quarterly,Half-Yearly,Yearly',
            'remarks' => 'nullable|string'
        ]);

        $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->format('Y-m-d');

        // Calculate progress percentage
        $currentValue = $goal->current_value;
        $targetValue = $request->input('target_value');
        $progressPercentage = $targetValue > 0 ? min(100, ($currentValue / $targetValue) * 100) : 0;

        DB::table('goals')->where('id', $id)->update([
            'goal_title' => $request->input('goal_title'),
            'goal_description' => $request->input('goal_description'),
            'goal_type' => $request->input('goal_type'),
            'category' => $request->input('category'),
            'assigned_to' => $request->input('assigned_to'),
            'department_id' => $request->input('department_id'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'target_value' => $targetValue,
            'unit' => $request->input('unit'),
            'weightage' => $request->input('weightage'),
            'progress_percentage' => $progressPercentage,
            'priority' => $request->input('priority'),
            'remarks' => $request->input('remarks'),
            'review_cycle' => $request->input('review_cycle'),
            'updated_at' => now(),
        ]);

        Session::flash('messageType', 'success');
        Session::flash('message', 'Goal updated successfully!');

        return redirect()->route('goals.index');
    }

    /**
     * Update goal progress
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProgress(Request $request, $id)
    {
        $goal = DB::table('goals')->where('id', $id)->where('deleted_at', 0)->first();

        if (!$goal) {
            return response()->json(['success' => false, 'message' => 'Goal not found.']);
        }

        $request->validate([
            'current_value' => 'required|numeric|min:0',
            'status' => 'required|in:Not Started,In Progress,On Hold,Completed,Cancelled'
        ]);

        $currentValue = $request->input('current_value');
        $targetValue = $goal->target_value;
        $progressPercentage = $targetValue > 0 ? min(100, ($currentValue / $targetValue) * 100) : 0;

        // Auto-complete if progress is 100%
        $status = $request->input('status');
        if ($progressPercentage >= 100 && $status !== 'Cancelled') {
            $status = 'Completed';
        }

        DB::table('goals')->where('id', $id)->update([
            'current_value' => $currentValue,
            'progress_percentage' => $progressPercentage,
            'status' => $status,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Progress updated successfully!',
            'progress_percentage' => $progressPercentage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $goal = DB::table('goals')->where('id', $id)->where('deleted_at', 0)->first();

        if (!$goal) {
            return response()->json(['status' => 'error', 'message' => 'Goal not found.']);
        }

        DB::table('goals')->where('id', $id)->update(['deleted_at' => 1]);

        return response()->json(['status' => 'success', 'message' => 'Goal deleted successfully!', 'id' => $id]);
    }

    // In GoalController.php - getEmployeesByDepartment method

/**
 * Get employees by department
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function getEmployeesByDepartment(Request $request)
{
    $departmentId = $request->input('department_id');
    
    $employees = DB::table('allemployees')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->where('allemployees.deleted_at', 0)
        ->where('allemployees.status', 'active');
        
    if ($departmentId) {
        $employees->where('allemployees.department', $departmentId);
    }
    
    $employees = $employees->select(
            'allemployees.id', 
            'allemployees.firstname', 
            'allemployees.lastname', 
            'department.department as department_name'
        )
        ->get()
        ->map(function($employee) {
            return [
                'id' => $employee->id,
                'text' => $employee->firstname . ' ' . $employee->lastname . 
                         ($employee->department_name ? ' - ' . $employee->department_name : '')
            ];
        });

    return response()->json($employees);
}
}