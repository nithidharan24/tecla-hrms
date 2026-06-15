<?php

namespace App\Http\Controllers\Backend\Performance\Performance_review;

use App\Http\Controllers\Controller;
use App\Exports\PerformanceReviewExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use PDF;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $performance_review_basic_infos = DB::table('performance_review_basic_infos')
        ->leftJoin('allemployees', 'performance_review_basic_infos.employee_name', '=', 'allemployees.id')
        ->leftJoin('designation', 'performance_review_basic_infos.designation_id', '=', 'designation.id')
        ->leftJoin('department', 'performance_review_basic_infos.department_id', '=', 'department.id') // Join with department table
        ->select(
            'performance_review_basic_infos.*', 
            'department.department as department_name', 
            'designation.designation',
            'allemployees.firstname', 
            'allemployees.lastname'
        )
        ->where('performance_review_basic_infos.deleted_at', 0)
        ->where('designation.deleted_at', 0)
        ->where('allemployees.deleted_at', 0)
        ->where('allemployees.status', 'active')
        ->get();
        return view('hrms.performance.performance.performance_review.index',compact('performance_review_basic_infos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->select('id', 'firstname', 'lastname','employeeid','joiningdate','department','designation')
        ->get();

        $designations = DB::table('designation')
        ->where('deleted_at', 0)
        ->select('id', 'designation', 'department_id')
        ->get();

        $departments = DB::table('department')
        ->select('id','department')
        ->get(); 

        return view('hrms.performance.performance.performance_review.create',compact('employees','designations','departments'));

    }

    public function getEmployeeDetails($id)
    {
        $employee = DB::table('allemployees')
            ->where('id', $id)
            ->select('employeeid', 'department', 'designation', 'joiningdate')
            ->first();

        if ($employee) {
            $employee->department = DB::table('department')
                ->where('id', $employee->department)
                ->value('department');

            $employee->designation = DB::table('designation')
                ->where('id', $employee->designation)
                ->value('designation');

            return response()->json($employee);
        }

        return response()->json(['error' => 'Employee not found'], 404);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate all inputs
        $this->validateInputs($request);

        // Start a database transaction
        DB::beginTransaction();

        try {
            $reviewId = $this->storeBasicInfo($request);
            $this->storeProfessionalExcellence($request, $reviewId);
            $this->storePersonalExcellence($request, $reviewId);
            $this->storeSpecialInitiatives($request, $reviewId);
            $this->storeRoleComments($request, $reviewId);
            $this->storeStrengthsAndImprovements($request, $reviewId);
            $this->storePersonalGoals($request, $reviewId);
            $this->storePersonalUpdates($request, $reviewId);
            $this->storeProfessionalGoals($request, $reviewId);
            $this->storeTrainingRequirements($request, $reviewId);
            $this->storeGeneralComments($request, $reviewId);
            $this->storeROAssessment($request, $reviewId);
            $this->storeHRDAssessment($request, $reviewId);
            $this->storeSignatures($request, $reviewId);

            // Commit the transaction
            DB::commit();

            return redirect()->route('performance-review.index')->with('success', 'Performance review submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    private function validateInputs(Request $request)
    {
        $request->validate([
            'employee' => 'required|exists:allemployees,id',
            'ro_name' => 'required|string|max:255',
            'ro_designation' => 'required|string|max:255',
            // Add other validation rules for all inputs
        ]);
    }

    private function storeBasicInfo(Request $request)
    {
        $designationId = DB::table('designation')->where('designation', $request->designation)->value('id');
        $departmentId = DB::table('department')->where('department', $request->department)->value('id');

        return DB::table('performance_review_basic_infos')->insertGetId([
            'employee_name' => $request->employee,
            'employee_id' => $request->emp_id,
            'designation_id' => $designationId,
            'department_id' =>  $departmentId,
            'date_of_join' => $request->doj,
            'ro_name' => $request->ro_name,
            'ro_designation' => $request->ro_designation,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function storeProfessionalExcellence(Request $request, $reviewId)
    {
        $professionalData = [
            [
                'key_result_area' => 'Production',
                'key_performance_indicator' => 'Quality',
                'weightage' => 30
            ],
            [
                'key_result_area' => 'Production',
                'key_performance_indicator' => 'TAT',
                'weightage' => 30
            ],
            [
                'key_result_area' => 'Process Improvement',
                'key_performance_indicator' => 'PMS, New Ideas',
                'weightage' => 10
            ],
            [
                'key_result_area' => 'Team Management',
                'key_performance_indicator' => 'Team Productivity, Dynamics, Attendance, Attrition',
                'weightage' => 5
            ],
            [
                'key_result_area' => 'Knowledge Sharing',
                'key_performance_indicator' => 'Sharing the Knowledge for Team Productivity',
                'weightage' => 5
            ],
            [
                'key_result_area' => 'Reporting and Communication',
                'key_performance_indicator' => 'Emails, Calls, Reports, and Other Communication',
                'weightage' => 5
            ]
        ];
    
        foreach ($professionalData as $index => $data) {
            DB::table('performance_review_professional_excellences')->insert([
                'review_id' => $reviewId,
                'key_result_area' => $data['key_result_area'],
                'key_performance_indicator' => $data['key_performance_indicator'],
                'weightage' => $data['weightage'],
                'percentage_self' => $request->input("percentage_self.{$index}", 0),
                'points_self' => $request->input("points_self.{$index}", 0),
                'percentage_ro' => $request->input("percentage_ro.{$index}", 0),
                'points_ro' => $request->input("points_ro.{$index}", 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Store the total percentages
        DB::table('performance_review_professional_excellences')
            ->where('review_id', $reviewId)
            ->update([
                'total_percentage_self' => $request->input('total_percentage_self', 0),
                'total_percentage_ro' => $request->input('total_percentage_ro', 0),
                'total_points_self' => $request->input('total_points_self', 0),
                'total_points_ro' => $request->input('total_points_ro', 0),
            ]);
    }

    private function storePersonalExcellence(Request $request, $reviewId)
    {
        $personalData = [
            [
                'personal_attribute' => 'Attendance',
                'key_indicator' => 'Planned or Unplanned Leaves',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Attendance',
                'key_indicator' => 'Time Consciousness',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Attitude & Behavior',
                'key_indicator' => 'Team Collaboration',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Attitude & Behavior',
                'key_indicator' => 'Professionalism & Responsiveness',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Policy & Procedures',
                'key_indicator' => 'Adherence to policies and procedures',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Initiatives',
                'key_indicator' => 'Special Efforts, Suggestions, Ideas, etc.',
                'weightage' => 2
            ],
            [
                'personal_attribute' => 'Continuous Skill Improvement',
                'key_indicator' => 'Preparedness to move to next level & Training utilization',
                'weightage' => 3
            ]
        ];
    
        foreach ($personalData as $index => $data) {
            DB::table('performance_review_personal_excellences')->insert([
                'review_id' => $reviewId,
                'personal_attribute' => $data['personal_attribute'],
                'key_indicator' => $data['key_indicator'],
                'weightage' => $data['weightage'],
                'percentage_self' => $request->input("personal_percentage_self.{$index}", 0),
                'points_self' => $request->input("personal_points_self.{$index}", 0),
                'percentage_ro' => $request->input("personal_percentage_ro.{$index}", 0),
                'points_ro' => $request->input("personal_points_ro.{$index}", 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Store the totals
        DB::table('performance_review_personal_excellences')->where('review_id', $reviewId)->update([
            'percentage_self_total' => $request->input('personal_total_percentage_self', 0),
            'points_self_total' => $request->input('personal_total_points_self', 0),
            'percentage_ro_total' => $request->input('personal_total_percentage_ro', 0),
            'points_ro_total' => $request->input('personal_total_points_ro', 0),
            'total_percentage' => $request->input('personal_total_percentage', 0),
        ]);
    }

    private function storeSpecialInitiatives(Request $request, $reviewId)
    {
        foreach ($request->input('achievement_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_special_initiatives')->insert([
                    'review_id' => $reviewId,
                    'achievement_self' => $value,
                    'achievement_ro' => $request->input('achievement_ro')[$key] ?? null,
                    'achievement_hod' => $request->input('achievement_hod')[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function storeRoleComments(Request $request, $reviewId)
    {
        foreach ($request->input('alteration_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_role_comments')->insert([
                    'review_id' => $reviewId,
                    'alteration_self' => $value,
                    'alteration_ro' => $request->input('alteration_ro')[$key] ?? null,
                    'alteration_hod' => $request->input('alteration_hod')[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function storeStrengthsAndImprovements(Request $request, $reviewId)
    {
        for ($i = 0; $i < 5; $i++) {
            DB::table('performance_review_strength_improvements')->insert([
                'review_id' => $reviewId,
                'strength' => $request->input('strength')[$i] ?? null,
                'improvement' => $request->input('improvement')[$i] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            DB::table('performance_review_hod_strength_improvements')->insert([
                'review_id' => $reviewId,
                'strength' => $request->input('hod_strength')[$i] ?? null,
                'improvement' => $request->input('hod_improvement')[$i] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function storePersonalGoals(Request $request, $reviewId)
    {
        for ($i = 0; $i < 3; $i++) {
            DB::table('performance_review_personal_goals')->insert([
                'review_id' => $reviewId,
                'last_year_goal' => $request->input('last_year_goal')[$i] ?? null,
                'current_year_goal' => $request->input('current_year_goal')[$i] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function storePersonalUpdates(Request $request, $reviewId)
    {
        DB::table('performance_review_personal_updates')->insert([
            'review_id' => $reviewId,
            'married_last_year' => $request->married_last_year,
            'married_last_year_details' => $request->married_last_year_details,
            'marriage_plans' => $request->marriage_plans,
            'marriage_plans_details' => $request->marriage_plans_details,
            'studies_last_year' => $request->studies_last_year,
            'studies_last_year_details' => $request->studies_last_year_details,
            'study_plans' => $request->study_plans,
            'study_plans_details' => $request->study_plans_details,
            'health_issues_last_year' => $request->health_issues_last_year,
            'health_issues_last_year_details' => $request->health_issues_last_year_details,
            'certification_plans' => $request->certification_plans,
            'certification_plans_details' => $request->certification_plans_details,
            'others_last_year' => $request->others_last_year,
            'others_last_year_details' => $request->others_last_year_details,
            'others_current_year' => $request->others_current_year,
            'others_current_year_details' => $request->others_current_year_details,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function storeProfessionalGoals(Request $request, $reviewId)
    {
        foreach ($request->input('goal_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_professional_goals')->insert([
                    'review_id' => $reviewId,
                    'goal_self' => $value,
                    'goal_ro' => $request->input('goal_ro')[$key] ?? null,
                    'goal_hod' => $request->input('goal_hod')[$key] ?? null,
                    'is_last_year' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($request->input('forthcoming_goal_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_professional_goals')->insert([
                    'review_id' => $reviewId,
                    'goal_self' => $value,
                    'goal_ro' => $request->input('forthcoming_goal_ro')[$key] ?? null,
                    'goal_hod' => $request->input('forthcoming_goal_hod')[$key] ?? null,
                    'is_last_year' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function storeTrainingRequirements(Request $request, $reviewId)
    {
        foreach ($request->input('training_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_training_requirements')->insert([
                    'review_id' => $reviewId,
                    'training_self' => $value,
                    'training_ro' => $request->input('training_ro')[$key] ?? null,
                    'training_hod' => $request->input('training_hod')[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function storeGeneralComments(Request $request, $reviewId)
    {
        foreach ($request->input('comment_self', []) as $key => $value) {
            if (!empty($value)) {
                DB::table('performance_review_general_comments')->insert([
                    'review_id' => $reviewId,
                    'comment_self' => $value,
                    'comment_ro' => $request->input('comment_ro')[$key] ?? null,
                    'comment_hod' => $request->input('comment_hod')[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function storeROAssessment(Request $request, $reviewId)
    {
        DB::table('performance_review_ro_assessments')->insert([
            'review_id' => $reviewId,
            'work_issues' => $request->work_issues,
            'work_issues_details' => $request->work_issues_details,
            'leave_issues' => $request->leave_issues,
            'leave_issues_details' => $request->leave_issues_details,
            'stability_issues' => $request->stability_issues,
            'stability_issues_details' => $request->stability_issues_details,
            'attitude_issues' => $request->attitude_issues,
            'attitude_issues_details' => $request->attitude_issues_details,
            'other_issues' => $request->other_issues,
            'other_issues_details' => $request->other_issues_details,
            'overall_performance' => $request->overall_performance,
            'overall_performance_details' => $request->overall_performance_details,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function storeHRDAssessment(Request $request, $reviewId)
    {
        DB::table('performance_review_hrd_assessments')->insert([
            'review_id' => $reviewId,
            'kra_points_available' => $request->kra_points_available,
            'kra_points_scored' => $request->kra_points_scored,
            'kra_comment' => $request->kra_comment,
            'professional_points_available' => $request->professional_points_available,
            'professional_points_scored' => $request->professional_points_scored,
            'professional_comment' => $request->professional_comment,
            'personal_points_available' => $request->personal_points_available,
            'personal_points_scored' => $request->personal_points_scored,
            'personal_comment' => $request->personal_comment,
            'achievement_points_available' => $request->achievement_points_available,
            'achievement_points_scored' => $request->achievement_points_scored,
            'achievement_comment' => $request->achievement_comment,
            'total_points_available' => $request->total_points_available,
            'total_points_scored' => $request->total_points_scored,
            'total_comment' => $request->total_comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function storeSignatures(Request $request, $reviewId)
    {
        DB::table('performance_review_signatures')->insert([
            'review_id' => $reviewId,
            'employee_name' => $request->employee_name,
            'employee_signature' => $request->employee_signature,
            'employee_date' => $request->employee_date,
            'ro_name' => $request->ro_name2,
            'ro_signature' => $request->ro_signature,
            'ro_date' => $request->ro_date,
            'hod_name' => $request->hod_name,
            
            'hod_signature' => $request->hod_signature,
            'hod_date' => $request->hod_date,
            'hrd_name' => $request->hrd_name,
            'hrd_signature' => $request->hrd_signature,
            'hrd_date' => $request->hrd_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
    $review = DB::table('performance_review_basic_infos')
        ->where('id', $id)
        ->first();

    if (!$review) {
        return redirect()->route('performance-review.index')->with('error', 'Review not found.');
    }

    $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->select('id', 'firstname', 'lastname', 'employeeid', 'joiningdate', 'department', 'designation')
        ->get();

    $designations = DB::table('designation')
        ->where('deleted_at', 0)
        ->select('id', 'designation', 'department_id')
        ->get();

    $departments = DB::table('department')
        ->select('id', 'department')
        ->get();

    // Fetch all related data
    $professionalExcellence = DB::table('performance_review_professional_excellences')
        ->where('review_id', $id)
        ->get();

    $personalExcellence = DB::table('performance_review_personal_excellences')
        ->where('review_id', $id)
        ->get();

    $specialInitiatives = DB::table('performance_review_special_initiatives')
        ->where('review_id', $id)
        ->get();

    $roleComments = DB::table('performance_review_role_comments')
        ->where('review_id', $id)
        ->get();

    $strengthsAndImprovements = DB::table('performance_review_strength_improvements')
        ->where('review_id', $id)
        ->get();

    $hodStrengthsAndImprovements = DB::table('performance_review_hod_strength_improvements')
        ->where('review_id', $id)
        ->get();

    $personalGoals = DB::table('performance_review_personal_goals')
        ->where('review_id', $id)
        ->get();

    $personalUpdates = DB::table('performance_review_personal_updates')
        ->where('review_id', $id)
        ->first();

    $professionalGoals = DB::table('performance_review_professional_goals')
        ->where('review_id', $id)
        ->get();

    $trainingRequirements = DB::table('performance_review_training_requirements')
        ->where('review_id', $id)
        ->get();

    $generalComments = DB::table('performance_review_general_comments')
        ->where('review_id', $id)
        ->get();

    $roAssessment = DB::table('performance_review_ro_assessments')
        ->where('review_id', $id)
        ->first();

    $hrdAssessment = DB::table('performance_review_hrd_assessments')
        ->where('review_id', $id)
        ->first();

    $signatures = DB::table('performance_review_signatures')
        ->where('review_id', $id)
        ->first();

    return view('hrms.performance.performance.performance_review.edit', compact(
        'review', 'employees', 'designations', 'departments',
        'professionalExcellence', 'personalExcellence', 'specialInitiatives',
        'roleComments', 'strengthsAndImprovements', 'hodStrengthsAndImprovements',
        'personalGoals', 'personalUpdates', 'professionalGoals',
        'trainingRequirements', 'generalComments', 'roAssessment',
        'hrdAssessment', 'signatures'
    ));
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
        // Validate all inputs
        $this->validateInputs($request);
    
        // Start a database transaction
        DB::beginTransaction();
    
        try {
            $this->updateBasicInfo($request, $id);
            $this->updateProfessionalExcellence($request, $id);
            $this->updatePersonalExcellence($request, $id);
            $this->updateSpecialInitiatives($request, $id);
            $this->updateRoleComments($request, $id);
            $this->updateStrengthsAndImprovements($request, $id);
            $this->updatePersonalGoals($request, $id);
            $this->updatePersonalUpdates($request, $id);
            $this->updateProfessionalGoals($request, $id);
            $this->updateTrainingRequirements($request, $id);
            $this->updateGeneralComments($request, $id);
            $this->updateROAssessment($request, $id);
            $this->updateHRDAssessment($request, $id);
            $this->updateSignatures($request, $id);
    
            // Commit the transaction
            DB::commit();
    
            return redirect()->route('performance-review.index')->with('success', 'Performance review updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    // Add update methods for each section, similar to the store methods
private function updateBasicInfo(Request $request, $id)
{
    $designationId = DB::table('designation')->where('designation', $request->designation)->value('id');
    $departmentId = DB::table('department')->where('department', $request->department)->value('id');

    DB::table('performance_review_basic_infos')
        ->where('id', $id)
        ->update([
            'employee_name' => $request->employee,
            'employee_id' => $request->emp_id,
            'designation_id' => $designationId,
            'department_id' =>  $departmentId,
            'date_of_join' => $request->doj,
            'ro_name' => $request->ro_name,
            'ro_designation' => $request->ro_designation,
            'updated_at' => now(),
        ]);
}

    private function updateProfessionalExcellence(Request $request, $id)
{
    DB::table('performance_review_professional_excellences')
        ->where('review_id', $id)
        ->delete();

    $professionalExcellence = [];
    foreach ($request->key_result_area as $index => $kra) {
        $professionalExcellence[] = [
            'review_id' => $id,
            'key_result_area' => $kra,
            'key_performance_indicator' => $request->key_performance_indicator[$index],
            'weightage' => $request->weightage[$index],
            'percentage_self' => $request->percentage_self[$index],
            'points_self' => $request->points_self[$index],
            'percentage_ro' => $request->percentage_ro[$index],
            'points_ro' => $request->points_ro[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_professional_excellences')->insert($professionalExcellence);
}

private function updatePersonalExcellence(Request $request, $id)
{
    DB::table('performance_review_personal_excellences')
        ->where('review_id', $id)
        ->delete();

    $personalExcellence = [];
    foreach ($request->personal_attribute as $index => $attribute) {
        $personalExcellence[] = [
            'review_id' => $id,
            'personal_attribute' => $attribute,
            'key_indicator' => $request->personal_key_indicator[$index],
            'weightage' => $request->personal_weightage[$index],
            'percentage_self' => $request->personal_percentage_self[$index],
            'points_self' => $request->personal_points_self[$index],
            'percentage_ro' => $request->personal_percentage_ro[$index],
            'points_ro' => $request->personal_points_ro[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_personal_excellences')->insert($personalExcellence);
}

private function updateSpecialInitiatives(Request $request, $id)
{
    DB::table('performance_review_special_initiatives')
        ->where('review_id', $id)
        ->delete();

    $specialInitiatives = [];
    foreach ($request->achievement_self as $index => $achievement) {
        $specialInitiatives[] = [
            'review_id' => $id,
            'achievement_self' => $achievement,
            'achievement_ro' => $request->achievement_ro[$index],
            'achievement_hod' => $request->achievement_hod[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_special_initiatives')->insert($specialInitiatives);
}

private function updateRoleComments(Request $request, $id)
{
    DB::table('performance_review_role_comments')
        ->where('review_id', $id)
        ->delete();

    $roleComments = [];
    foreach ($request->alteration_self as $index => $alteration) {
        $roleComments[] = [
            'review_id' => $id,
            'alteration_self' => $alteration,
            'alteration_ro' => $request->alteration_ro[$index],
            'alteration_hod' => $request->alteration_hod[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_role_comments')->insert($roleComments);
}

private function updateStrengthsAndImprovements(Request $request, $id)
{
    DB::table('performance_review_strength_improvements')
        ->where('review_id', $id)
        ->delete();

    $strengthsAndImprovements = [];
    foreach ($request->strength as $index => $strength) {
        $strengthsAndImprovements[] = [
            'review_id' => $id,
            'strength' => $strength,
            'improvement' => $request->improvement[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_strength_improvements')->insert($strengthsAndImprovements);
}

private function updatePersonalGoals(Request $request, $id)
{
    DB::table('performance_review_personal_goals')
        ->where('review_id', $id)
        ->delete();

    $personalGoals = [];
    foreach ($request->last_year_goal as $index => $lastYearGoal) {
        $personalGoals[] = [
            'review_id' => $id,
            'last_year_goal' => $lastYearGoal,
            'current_year_goal' => $request->current_year_goal[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_personal_goals')->insert($personalGoals);
}

private function updatePersonalUpdates(Request $request, $id)
{
    DB::table('performance_review_personal_updates')
        ->updateOrInsert(
            ['review_id' => $id],
            [
                'married_last_year' => $request->married_last_year,
                'married_last_year_details' => $request->married_last_year_details,
                'marriage_plans' => $request->marriage_plans,
                'marriage_plans_details' => $request->marriage_plans_details,
                'studies_last_year' => $request->studies_last_year,
                'studies_last_year_details' => $request->studies_last_year_details,
                'study_plans' => $request->study_plans,
                'study_plans_details' => $request->study_plans_details,
                'health_issues_last_year' => $request->health_issues_last_year,
                'health_issues_last_year_details' => $request->health_issues_last_year_details,
                'certification_plans' => $request->certification_plans,
                'certification_plans_details' => $request->certification_plans_details,
                'others_last_year' => $request->others_last_year,
                'others_last_year_details' => $request->others_last_year_details,
                'others_current_year' => $request->others_current_year,
                'others_current_year_details' => $request->others_current_year_details,
                'updated_at' => now(),
            ]
        );
}

private function updateProfessionalGoals(Request $request, $id)
{
    DB::table('performance_review_professional_goals')
        ->where('review_id', $id)
        ->delete();

    $professionalGoals = [];
    foreach ($request->goal_type as $index => $goalType) {
        $professionalGoals[] = [
            'review_id' => $id,
            'goal_type' => $goalType,
            'goal_description' => $request->goal_description[$index],
            'target_achievement_date' => $request->target_achievement_date[$index],
            'weightage' => $request->weightage[$index],
            'percentage_achieved_self' => $request->percentage_achieved_self[$index],
            'percentage_achieved_ro' => $request->percentage_achieved_ro[$index],
            'ro_comment' => $request->ro_comment[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_professional_goals')->insert($professionalGoals);
}

private function updateTrainingRequirements(Request $request, $id)
{
    DB::table('performance_review_training_requirements')
        ->where('review_id', $id)
        ->delete();

    $trainingRequirements = [];
    foreach ($request->training_requirement as $index => $requirement) {
        $trainingRequirements[] = [
            'review_id' => $id,
            'training_requirement' => $requirement,
            'comment' => $request->training_comment[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_training_requirements')->insert($trainingRequirements);
}

private function updateGeneralComments(Request $request, $id)
{
    DB::table('performance_review_general_comments')
        ->where('review_id', $id)
        ->delete();

    $generalComments = [];
    foreach ($request->self_comment as $index => $selfComment) {
        $generalComments[] = [
            'review_id' => $id,
            'self_comment' => $selfComment,
            'ro_comment' => $request->ro_comment[$index],
            'hod_comment' => $request->hod_comment[$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('performance_review_general_comments')->insert($generalComments);
}

private function updateROAssessment(Request $request, $id)
{
    DB::table('performance_review_ro_assessments')
        ->updateOrInsert(
            ['review_id' => $id],
            [
                'professional_skills_percentage' => $request->professional_skills_percentage,
                'personal_skills_percentage' => $request->personal_skills_percentage,
                'total_percentage' => $request->total_percentage,
                'updated_at' => now(),
            ]
        );
}

private function updateHRDAssessment(Request $request, $id)
{
    DB::table('performance_review_hrd_assessments')
        ->updateOrInsert(
            ['review_id' => $id],
            [
                'professional_skills_percentage' => $request->hrd_professional_skills_percentage,
                'personal_skills_percentage' => $request->hrd_personal_skills_percentage,
                'total_percentage' => $request->hrd_total_percentage,
                'updated_at' => now(),
            ]
        );
}

private function updateSignatures(Request $request, $id)
{
    DB::table('performance_review_signatures')
        ->updateOrInsert(
            ['review_id' => $id],
            [
                'employee_signature_date' => $request->employee_signature_date,
                'ro_signature_date' => $request->ro_signature_date,
                'hod_signature_date' => $request->hod_signature_date,
                'hrd_signature_date' => $request->hrd_signature_date,
                'updated_at' => now(),
            ]
        );
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
     public function destroy($id)
     {
           // Update the `deleted_at` field to `1` (soft delete)
           DB::table('performance_review_basic_infos')
           ->where('id', $id)
           ->update(['deleted_at' => 1]);
 
       // Redirect back with success message
       return redirect()->route('performance-review.index')->with('success', 'Performance review deleted successfully');
 
         // // Permanently delete the review by using the `delete()` method
         // DB::table('performance_review_basic_infos')->where('id', $id)->delete();
     
         // // Redirect back with success message
         // return redirect()->route('performance-review.index')->with('success', 'Performance review deleted permanently');
     }
 
     public function updateStatus(Request $request, $id)
     {
         // Validate the status to ensure only allowed values are passed
         $validated = $request->validate([
             'status' => 'required|in:active,inactive'
         ]);
 
         // Update the status using DB facade
         $updated = DB::table('performance_review_basic_infos')->where('id', $id)->update([
             'status' => $validated['status'],
             'updated_at' => now(), // Make sure to update the timestamp
         ]);
 
         if ($updated) {
             return response()->json(['success' => true, 'message' => 'Status updated successfully']);
         }
 
         return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
     }

    public function exportExcel($id)
    {
        return Excel::download(new PerformanceReviewExport($id), 'performance_review.xlsx');
    }

    public function exportPdf($id)
    {
        $review = $this->getPerformanceReviewData($id);
        $pdf = PDF::loadView('exports.performance_review_pdf', compact('review'));
        return $pdf->download('performance_review.pdf');
    }

    private function getPerformanceReviewData($id)
    {
        // Fetch all related data for the performance review
        $review = DB::table('performance_review_basic_infos')
            ->where('id', $id)
            ->first();

        if (!$review) {
            abort(404, 'Performance review not found');
        }

        $review->professional_excellence = DB::table('performance_review_professional_excellences')
            ->where('review_id', $id)
            ->get();

        $review->personal_excellence = DB::table('performance_review_personal_excellences')
            ->where('review_id', $id)
            ->get();

        $review->special_initiatives = DB::table('performance_review_special_initiatives')
            ->where('review_id', $id)
            ->get();

        $review->role_comments = DB::table('performance_review_role_comments')
            ->where('review_id', $id)
            ->get();

        $review->strengths_improvements = DB::table('performance_review_strength_improvements')
            ->where('review_id', $id)
            ->get();

        $review->hod_strengths_improvements = DB::table('performance_review_hod_strength_improvements')
            ->where('review_id', $id)
            ->get();

        $review->personal_goals = DB::table('performance_review_personal_goals')
            ->where('review_id', $id)
            ->get();

        $review->personal_updates = DB::table('performance_review_personal_updates')
            ->where('review_id', $id)
            ->first();

        $review->professional_goals = DB::table('performance_review_professional_goals')
            ->where('review_id', $id)
            ->get();

        $review->training_requirements = DB::table('performance_review_training_requirements')
            ->where('review_id', $id)
            ->get();

        $review->general_comments = DB::table('performance_review_general_comments')
            ->where('review_id', $id)
            ->get();

        $review->ro_assessment = DB::table('performance_review_ro_assessments')
            ->where('review_id', $id)
            ->first();

        $review->hrd_assessment = DB::table('performance_review_hrd_assessments')
            ->where('review_id', $id)
            ->first();

        $review->signatures = DB::table('performance_review_signatures')
            ->where('review_id', $id)
            ->first();

        return $review;
    }

}
