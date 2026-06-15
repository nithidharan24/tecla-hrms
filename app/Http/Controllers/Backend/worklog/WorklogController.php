<?php

namespace App\Http\Controllers\Backend\worklog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorklogController extends Controller
{
public function index(Request $request)
{
    // Get user info from session
    $employeeId = session('user_id');
    $role = session('role');
    
    // Base query
    $query = DB::table('worklogs')
        ->join('allemployees', 'worklogs.employee_id', '=', 'allemployees.id')
        ->leftJoin('projects', 'worklogs.projectid', '=', 'projects.projectid')
        ->select(
            'worklogs.*',
            DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
            'projects.projectname as project',
            'projects.projectid as project_id'
        );
        
    // Filter for employees
    if ($role === 'employee') {
        $query->where('worklogs.employee_id', $employeeId);
    }
    
    // Apply filters
    if ($request->filled('employee_id')) {
        $query->where('worklogs.employee_id', $request->employee_id);
    }

    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereBetween('worklogs.date', [
            $request->date_from, 
            $request->date_to
        ]);
    }

    if ($request->filled('project_id')) {
        $query->where('projects.projectid', $request->project_id);
    }

    $worklogs = $query->orderBy('worklogs.date', 'desc')
        ->paginate(10)
        ->appends($request->except('page'));

    // Always get projects for filter dropdown
    $projects = DB::table('projects')
        ->select('projectid', 'projectname')
        ->get();

    // Only get employees for admin users
    $employees = [];
    if ($role !== 'employee') {
        $employees = DB::table('allemployees')
            ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
            ->get();
    }

    return view('hrms.worklog.index', [
        'worklogs' => $worklogs,
        'projects' => $projects,
        'employees' => $employees
    ]);
}
   public function create()
{
    $employeeId = session('user_id');
    $role = session('role');
    
    // For employees, only show their own information
    if ($role === 'employee') {
        $employees = DB::table('allemployees')
            ->where('id', $employeeId)
            ->select('id', 'firstname', 'lastname', 'email')
            ->get()
            ->map(function ($employee) {
                $employee->full_name = $employee->firstname . ' ' . $employee->lastname;
                return $employee;
            });
    } else {
        // For admins, show all employees
        $employees = DB::table('allemployees')
            ->select('id', 'firstname', 'lastname', 'email')
            ->get()
            ->map(function ($employee) {
                $employee->full_name = $employee->firstname . ' ' . $employee->lastname;
                return $employee;
            });
    }

    $recentProjects = DB::table('projects')
        ->select('projectid', 'projectname')
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('hrms.worklog.create', compact('recentProjects', 'employees', 'employeeId', 'role'));
}

 public function store(Request $request)
{
    $employeeId = session('user_id');
    $role = session('role');
    
    $validated = $request->validate([
        'employee_id' => 'required|exists:allemployees,id',
        'date' => 'required|date',
        'projectid' => 'required|exists:projects,projectid',
        'task' => 'required|string|max:255',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'break_minutes' => 'required|integer|min:0',
        'description' => 'required|string',
    ]);
    
    // For employees, ensure they can only submit for themselves
    if ($role === 'employee' && $validated['employee_id'] != $employeeId) {
        return back()->withErrors('You can only submit worklogs for yourself.');
    }

    // Calculate total minutes worked
    $start = Carbon::createFromFormat('H:i', $validated['start_time']);
    $end = Carbon::createFromFormat('H:i', $validated['end_time']);
    $totalMinutes = $end->diffInMinutes($start) - $validated['break_minutes'];

    // Add calculated fields to the validated data
    $validated['total_hours'] = $totalMinutes / 60;
    $validated['created_at'] = now();
    $validated['updated_at'] = now();

    DB::table('worklogs')->insert($validated);

    return redirect()->route('worklogs.index')
        ->with('success', 'Worklog submitted successfully.');
}
    public function show($id)
    {
        $employeeId = session('user_id');
        $role = session('role');
        
        $query = DB::table('worklogs')
            ->join('allemployees', 'worklogs.employee_id', '=', 'allemployees.id')
            ->select(
                'worklogs.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name")
            )
            ->where('worklogs.id', $id);
            
        // For employees, only allow viewing their own logs
        if ($role === 'employee') {
            $query->where('worklogs.employee_id', $employeeId);
        }

        $worklog = $query->first();

        if (!$worklog) {
            abort(404);
        }

        return view('hrms.worklog.show', compact('worklog'));
    }

    public function edit($id)
    {
        $employeeId = session('user_id');
        $role = session('role');
        
        $query = DB::table('worklogs')
            ->join('allemployees', 'worklogs.employee_id', '=', 'allemployees.id')
            ->select(
                'worklogs.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name")
            )
            ->where('worklogs.id', $id);
            
        // For employees, only allow editing their own logs
        if ($role === 'employee') {
            $query->where('worklogs.employee_id', $employeeId);
        }

        $worklog = $query->first();

        if (!$worklog) {
            abort(404);
        }

        // For employees, only show their own information
        if ($role === 'employee') {
            $employees = DB::table('allemployees')
                ->where('id', $employeeId)
                ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
                ->get();
        } else {
            // For admins, show all employees
            $employees = DB::table('allemployees')
                ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
                ->get();
        }

        return view('hrms.worklog.edit', compact('worklog', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $employeeId = session('user_id');
        $role = session('role');
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:allemployees,id',
            'date' => 'required|date',
            'project' => 'required|string|max:255',
            'task' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'break_minutes' => 'nullable|integer|min:0',
        ]);
        
        // Check if the worklog exists and belongs to the employee (if employee)
        $worklog = DB::table('worklogs')->where('id', $id);
        
        if ($role === 'employee') {
            $worklog->where('employee_id', $employeeId);
        }
        
        $worklog = $worklog->first();
        
        if (!$worklog) {
            abort(404);
        }
        
        // For employees, ensure they can only update for themselves
        if ($role === 'employee' && $validated['employee_id'] != $employeeId) {
            return back()->withErrors('You can only update worklogs for yourself.');
        }

        DB::table('worklogs')
            ->where('id', $id)
            ->update($validated);

        return redirect()->route('worklogs.index')
            ->with('success', 'Worklog updated successfully.');
    }

    public function destroy($id)
    {
        $employeeId = session('user_id');
        $role = session('role');
        
        $query = DB::table('worklogs')->where('id', $id);
        
        // For employees, only allow deleting their own logs
        if ($role === 'employee') {
            $query->where('employee_id', $employeeId);
        }
        
        $worklog = $query->first();
        
        if (!$worklog) {
            abort(404);
        }

        DB::table('worklogs')
            ->where('id', $id)
            ->delete();

        return redirect()->route('worklogs.index')
            ->with('success', 'Worklog deleted successfully.');
    }

    public function report()
    {
        $employeeId = session('user_id');
        $role = session('role');
        
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $query = DB::table('worklogs')
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);
            
        // For employees, only show their own data
        if ($role === 'employee') {
            $query->where('employee_id', $employeeId);
        }

        $totalMinutes = $query->clone()
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time) - SUM(COALESCE(break_minutes, 0)) as total_minutes')
            ->value('total_minutes');

        $totalHours = floor($totalMinutes / 60) . 'h ' . ($totalMinutes % 60) . 'm';

        $projectSummary = $query->clone()
            ->join('allemployees', 'worklogs.employee_id', '=', 'allemployees.id')
            ->select(
                'worklogs.project',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_name"),
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time) - COALESCE(break_minutes, 0)) as total_minutes')
            )
            ->groupBy('worklogs.project', 'allemployees.firstname', 'allemployees.lastname')
            ->orderByDesc('total_minutes')
            ->get()
            ->map(function ($item) {
                $hours = floor($item->total_minutes / 60);
                $minutes = $item->total_minutes % 60;
                $item->formatted_time = "{$hours}h {$minutes}m";
                return $item;
            });

        return view('hrms.worklog.report', compact('totalHours', 'projectSummary'));
    }
}