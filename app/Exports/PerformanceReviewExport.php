<?php
// Moved here from app/Export/PerformanceReviewExport.php so PSR-4 autoload matches namespace App\Exports.

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Controllers\PerformanceReviewExportController;

class PerformanceReviewExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $controller = new PerformanceReviewExportController();
        $review = $controller->getPerformanceReviewData($this->id);

        $professionalExcellence = $review->professional_excellence->first();
        $personalExcellence = $review->personal_excellence->first();

        return collect([
            [
                'Employee Name' => $review->employee_name,
                'Employee ID' => $review->employee_id,
                'Designation' => $review->designation_id,
                'Department' => $review->department_id,
                'Date of Join' => $review->date_of_join,
                'RO Name' => $review->ro_name,
                'RO Designation' => $review->ro_designation,

                // Professional Excellence
                'Production Quality Weightage' => $professionalExcellence->weightage ?? '',
                'Production Quality Percentage Self' => $professionalExcellence->percentage_self ?? '',
                'Production Quality Points Self' => $professionalExcellence->points_self ?? '',
                'Production Quality Percentage RO' => $professionalExcellence->percentage_ro ?? '',
                'Production Quality Points RO' => $professionalExcellence->points_ro ?? '',

                // Personal Excellence
                'Attendance Weightage' => $personalExcellence->weightage ?? '',
                'Attendance Percentage Self' => $personalExcellence->percentage_self ?? '',
                'Attendance Points Self' => $personalExcellence->points_self ?? '',
                'Attendance Percentage RO' => $personalExcellence->percentage_ro ?? '',
                'Attendance Points RO' => $personalExcellence->points_ro ?? '',

                // Special Initiatives
                'Special Initiative Self' => $review->special_initiatives->first()->achievement_self ?? '',
                'Special Initiative RO' => $review->special_initiatives->first()->achievement_ro ?? '',
                'Special Initiative HOD' => $review->special_initiatives->first()->achievement_hod ?? '',

                // Role Comments
                'Role Comment Self' => $review->role_comments->first()->alteration_self ?? '',
                'Role Comment RO' => $review->role_comments->first()->alteration_ro ?? '',
                'Role Comment HOD' => $review->role_comments->first()->alteration_hod ?? '',

                // Strengths and Improvements
                'Strength' => $review->strengths_improvements->first()->strength ?? '',
                'Improvement' => $review->strengths_improvements->first()->improvement ?? '',

                // Personal Goals
                'Last Year Goal' => $review->personal_goals->first()->last_year_goal ?? '',
                'Current Year Goal' => $review->personal_goals->first()->current_year_goal ?? '',

                // Personal Updates
                'Married Last Year' => $review->personal_updates->married_last_year ?? '',
                'Marriage Plans' => $review->personal_updates->marriage_plans ?? '',

                // Professional Goals
                'Professional Goal Self' => $review->professional_goals->first()->goal_self ?? '',
                'Professional Goal RO' => $review->professional_goals->first()->goal_ro ?? '',
                'Professional Goal HOD' => $review->professional_goals->first()->goal_hod ?? '',

                // Training Requirements
                'Training Requirement Self' => $review->training_requirements->first()->training_self ?? '',
                'Training Requirement RO' => $review->training_requirements->first()->training_ro ?? '',
                'Training Requirement HOD' => $review->training_requirements->first()->training_hod ?? '',

                // General Comments
                'General Comment Self' => $review->general_comments->first()->comment_self ?? '',
                'General Comment RO' => $review->general_comments->first()->comment_ro ?? '',
                'General Comment HOD' => $review->general_comments->first()->comment_hod ?? '',

                // RO Assessment
                'Work Issues' => $review->ro_assessment->work_issues ?? '',
                'Leave Issues' => $review->ro_assessment->leave_issues ?? '',
                'Overall Performance' => $review->ro_assessment->overall_performance ?? '',

                // HRD Assessment
                'KRA Points Available' => $review->hrd_assessment->kra_points_available ?? '',
                'KRA Points Scored' => $review->hrd_assessment->kra_points_scored ?? '',
                'Total Points Available' => $review->hrd_assessment->total_points_available ?? '',
                'Total Points Scored' => $review->hrd_assessment->total_points_scored ?? '',

                // Signatures
                'Employee Signature Date' => $review->signatures->employee_date ?? '',
                'RO Signature Date' => $review->signatures->ro_date ?? '',
                'HOD Signature Date' => $review->signatures->hod_date ?? '',
                'HRD Signature Date' => $review->signatures->hrd_date ?? '',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee ID',
            'Designation',
            'Department',
            'Date of Join',
            'RO Name',
            'RO Designation',

            'Production Quality Weightage',
            'Production Quality Percentage Self',
            'Production Quality Points Self',
            'Production Quality Percentage RO',
            'Production Quality Points RO',

            'Attendance Weightage',
            'Attendance Percentage Self',
            'Attendance Points Self',
            'Attendance Percentage RO',
            'Attendance Points RO',

            'Special Initiative Self',
            'Special Initiative RO',
            'Special Initiative HOD',

            'Role Comment Self',
            'Role Comment RO',
            'Role Comment HOD',

            'Strength',
            'Improvement',

            'Last Year Goal',
            'Current Year Goal',

            'Married Last Year',
            'Marriage Plans',

            'Professional Goal Self',
            'Professional Goal RO',
            'Professional Goal HOD',

            'Training Requirement Self',
            'Training Requirement RO',
            'Training Requirement HOD',

            'General Comment Self',
            'General Comment RO',
            'General Comment HOD',

            'Work Issues',
            'Leave Issues',
            'Overall Performance',

            'KRA Points Available',
            'KRA Points Scored',
            'Total Points Available',
            'Total Points Scored',

            'Employee Signature Date',
            'RO Signature Date',
            'HOD Signature Date',
            'HRD Signature Date',
        ];
    }
}
