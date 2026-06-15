<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class FeedbackformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        // Base query
        $query = DB::table('employee_feedback')
            ->leftJoin('department', 'employee_feedback.department', '=', 'department.id')
            ->select('employee_feedback.*', 'department.department as department_name');
        
        // If user is employee, show only their feedback
        if ($userRole === 'employee') {
            $query->where('employee_feedback.employee_id', $userId);
        }
        
        // If user is admin, show all feedback
        // No additional filter needed for admin
        
        $feedbacks = $query->orderBy('employee_feedback.created_at', 'desc')
            ->paginate(10);
    
        return view('hrms.master.feedback-form.index', compact('feedbacks', 'userRole'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = DB::table('department')->get();
        
        // Get logged-in employee data
        $employee = null;
        $employeeName = '';
        $employeeDepartment = '';
        
        if (Session::get('role') === 'employee') {
            $employeeId = Session::get('user_id');
            $employee = DB::table('allemployees')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->select('allemployees.*', 'department.department as department_name')
                ->where('allemployees.id', $employeeId)
                ->first();
            
            if ($employee) {
                $employeeName = $employee->firstname . ' ' . $employee->lastname;
                $employeeDepartment = $employee->department; // department ID
            }
        }
        
        return view('hrms.master.feedback-form.create', compact('departments', 'employeeName', 'employeeDepartment', 'employee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'job_satisfaction' => 'required|integer|between:1,5',
            'work_environment' => 'required|integer|between:1,5',
            'manager_support' => 'required|integer|between:1,5',
            'growth_opportunities' => 'required|integer|between:1,5',
            'recommend_company' => 'required|in:yes,no',
            'suggestions' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        try {
            DB::table('employee_feedback')->insert([
                'employee_name' => $request->employee_name,
                'department' => $request->department,
                'job_satisfaction' => $request->job_satisfaction,
                'work_environment' => $request->work_environment,
                'manager_support' => $request->manager_support,
                'growth_opportunities' => $request->growth_opportunities,
                'recommend_company' => $request->recommend_company,
                'suggestions' => $request->suggestions,
                'additional_comments' => $request->additional_comments,
                'employee_id' => Session::get('user_id'), // Store employee ID for reference
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('feedback.success')
                ->with('success', 'Thank you! Your feedback has been submitted successfully.')
                ->with('feedback_data', $request->all());

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your feedback. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        $feedback = DB::table('employee_feedback')
            ->leftJoin('department', 'employee_feedback.department', '=', 'department.id')
            ->select('employee_feedback.*', 'department.department as department_name')
            ->where('employee_feedback.id', $id)
            ->first();

        if (!$feedback) {
            return redirect()->route('feedback.index')
                ->with('error', 'Feedback not found.');
        }

        // If user is employee, check if they own this feedback
        if ($userRole === 'employee' && $feedback->employee_id != $userId) {
            return redirect()->route('feedback.index')
                ->with('error', 'You are not authorized to view this feedback.');
        }

        return view('hrms.master.feedback-form.show', compact('feedback', 'userRole'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        $feedback = DB::table('employee_feedback')
            ->leftJoin('department', 'employee_feedback.department', '=', 'department.id')
            ->select('employee_feedback.*', 'department.department as department_name')
            ->where('employee_feedback.id', $id)
            ->first();
            
        $departments = DB::table('department')->get();
        
        if (!$feedback) {
            return redirect()->route('feedback.index')
                ->with('error', 'Feedback not found.');
        }

        // If user is employee, check if they own this feedback
        if ($userRole === 'employee' && $feedback->employee_id != $userId) {
            return redirect()->route('feedback.index')
                ->with('error', 'You are not authorized to edit this feedback.');
        }

        return view('hrms.master.feedback-form.edit', compact('feedback','departments', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        // First check authorization
        if ($userRole === 'employee') {
            $feedback = DB::table('employee_feedback')->find($id);
            if (!$feedback || $feedback->employee_id != $userId) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You are not authorized to update this feedback.');
            }
        }

        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'job_satisfaction' => 'required|integer|between:1,5',
            'work_environment' => 'required|integer|between:1,5',
            'manager_support' => 'required|integer|between:1,5',
            'growth_opportunities' => 'required|integer|between:1,5',
            'recommend_company' => 'required|in:yes,no',
            'suggestions' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        try {
            DB::table('employee_feedback')
                ->where('id', $id)
                ->update([
                    'employee_name' => $request->employee_name,
                    'department' => $request->department,
                    'job_satisfaction' => $request->job_satisfaction,
                    'work_environment' => $request->work_environment,
                    'manager_support' => $request->manager_support,
                    'growth_opportunities' => $request->growth_opportunities,
                    'recommend_company' => $request->recommend_company,
                    'suggestions' => $request->suggestions,
                    'additional_comments' => $request->additional_comments,
                    'updated_at' => now(),
                ]);

            return redirect()->route('feedback.index')
                ->with('success', 'Feedback updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the feedback. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        // First check authorization
        if ($userRole === 'employee') {
            $feedback = DB::table('employee_feedback')->find($id);
            if (!$feedback || $feedback->employee_id != $userId) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You are not authorized to delete this feedback.');
            }
        }

        try {
            DB::table('employee_feedback')->where('id', $id)->delete();

            return redirect()->route('feedback.index')
                ->with('success', 'Feedback deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the feedback.');
        }
    }

    /**
     * Show the success page after form submission.
     */
    public function success()
    {
        return view('hrms.master.feedback-form.success');
    }

    /**
     * Generate feedback analytics
     */
    public function analytics()
    {
        $userRole = Session::get('role');
        $userId = Session::get('user_id');
        
        // Base query for analytics
        $baseQuery = DB::table('employee_feedback');
        
        // If user is employee, show only their analytics
        if ($userRole === 'employee') {
            $baseQuery->where('employee_id', $userId);
        }
        
        $analytics = [
            'total_responses' => $baseQuery->count(),
            'avg_job_satisfaction' => $baseQuery->avg('job_satisfaction'),
            'avg_work_environment' => $baseQuery->avg('work_environment'),
            'avg_manager_support' => $baseQuery->avg('manager_support'),
            'avg_growth_opportunities' => $baseQuery->avg('growth_opportunities'),
            'recommendation_rate' => $baseQuery->clone()->where('recommend_company', 'yes')->count() / max($baseQuery->clone()->count(), 1) * 100,
            'department_breakdown' => $baseQuery->clone()
                ->select('department', DB::raw('count(*) as count'))
                ->groupBy('department')
                ->get(),
        ];
        
        return view('hrms.master.feedback-form.analytics', compact('analytics', 'userRole'));
    }
}