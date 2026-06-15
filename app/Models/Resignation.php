<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    use HasFactory;

    protected $fillable = [
        // 1. Employee Details
        'employee_name',
        'employee_id',
        'department',
        'designation',
        'reporting_manager',
        'official_email',
        'contact_number',

        // 2. Resignation Details
        'date_of_resignation',
        'last_working_day',
        'notice_period_duration',
        'mode_of_resignation',

        // 3. Reason for Resignation
        'reason_for_resignation',
        'detailed_explanation',

        // 4. Exit Process Checklist
        'responsibilities_handed_over',
        'person_handover_to',
        'company_assets_returned',
        'list_of_returned_items',
        'serve_full_notice_period',
        'leave_planned_during_notice',

        // 5. Document Uploads
        'resignation_letter_path',
        'medical_certificate_path',

        // 6. Declaration
        'declaration_agreed',
        'declaration_place',
        'declaration_date',
        'employee_signature_path',

        // 7. HR/Manager Fields (internal)
        'approval_status',
        'resignation_acceptance_date',
        'exit_interview_scheduled_date',
        'clearance_status',
        'final_settlement_status',
        'feedback_notes',
    ];

    protected $casts = [
        'date_of_resignation' => 'date',
        'last_working_day' => 'date',
        'resignation_acceptance_date' => 'date',
        'exit_interview_scheduled_date' => 'date',
        'declaration_date' => 'date',
        'responsibilities_handed_over' => 'boolean',
        'company_assets_returned' => 'boolean',
        'serve_full_notice_period' => 'boolean',
        'declaration_agreed' => 'boolean',
    ];
}
