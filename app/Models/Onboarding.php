<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'date_of_birth', 'gender', 'blood_group', 'nationality', 'marital_status',
        'mobile_number', 'alternate_contact_number', 'personal_email_id', 'aadhaar_number',
        'pan_number', 'passport_number', 'father_mother_name', 'emergency_contact_name',
        'emergency_contact_number', 'emergency_contact_relationship',
        'current_address', 'permanent_address', 'is_current_permanent',
        'highest_qualification', 'stream_specialization', 'institution_name', 'year_of_passing',
        'cgpa_percentage', 'tenth_twelfth_marks_boards_year', 'certifications',
        'previous_company_name', 'last_drawn_salary', 'reason_for_leaving',
        'experience_letter_path', 'relieving_letter_path',
        'date_of_joining', 'employee_code', 'department', 'designation', 'reporting_manager',
        'work_location', 'shift_timing', 'employment_type', 'probation_period_months',
        'bank_name', 'branch', 'account_holder_name', 'account_number', 'ifsc_code',
        'cancelled_cheque_path',
        'has_medical_conditions', 'medical_history', 'is_covered_by_insurance', 'nominee_name',
        'nominee_relationship',
        'resume_path', 'passport_photo_path', 'aadhaar_card_front_path', 'aadhaar_card_back_path',
        'pan_card_path', 'educational_certificates_path', 'experience_certificates_path',
        'address_proof_path', 'signed_offer_letter_path', 'covid_vaccination_certificate_path',
        'declaration_agreed', 'declaration_place', 'declaration_date', 'signature_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'declaration_date' => 'date',
        'is_current_permanent' => 'boolean',
        'has_medical_conditions' => 'boolean',
        'is_covered_by_insurance' => 'boolean',
        'declaration_agreed' => 'boolean',
    ];
}