<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Onboarding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // Import File facade

class OnboardingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $onboardings = Onboarding::all();
        return view('hrms.master.onboarding-process.index', compact('onboardings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrms.master.onboarding-process.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // 1. Personal Details
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'mobile_number' => 'required|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
            'personal_email_id' => 'required|email|max:255',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'father_mother_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // 2. Address Details
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'is_current_permanent' => 'boolean',
            // 3. Educational Details
            'highest_qualification' => 'nullable|string|max:255',
            'stream_specialization' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|integer|digits:4',
            'cgpa_percentage' => 'nullable|string|max:50',
            'tenth_twelfth_marks_boards_year' => 'nullable|string|max:255',
            'certifications' => 'nullable|string',
            // 4. Previous Employment Details
            'previous_company_name' => 'nullable|string|max:255',
            'last_drawn_salary' => 'nullable|numeric',
            'reason_for_leaving' => 'nullable|string',
            'experience_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'relieving_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            // 5. Job/Employment Details (For Current Role)
            'date_of_joining' => 'required|date',
            'employee_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'reporting_manager' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'shift_timing' => 'nullable|string|max:100',
            'employment_type' => 'nullable|string|max:50',
            'probation_period_months' => 'nullable|integer',
            // 6. Bank Details
            'bank_name' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'cancelled_cheque' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 7. Health & Insurance Details
            'has_medical_conditions' => 'boolean',
            'medical_history' => 'nullable|string',
            'is_covered_by_insurance' => 'boolean',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_relationship' => 'nullable|string|max:100',
            // 8. Document Uploads
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'aadhaar_card_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'aadhaar_card_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'educational_certificates' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'experience_certificates' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'address_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signed_offer_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'covid_vaccination_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 9. Declaration & Signature
            'declaration_agreed' => 'required|boolean',
            'declaration_place' => 'nullable|string|max:255',
            'declaration_date' => 'nullable|date',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // For uploaded signature
        ]);

        // Define the target directory within public
        $targetDirectory = 'onboarding_documents';
        $publicPath = public_path($targetDirectory);

        // Ensure the directory exists
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }

        // Handle file uploads
        $fileFields = [
            'experience_letter', 'relieving_letter', 'cancelled_cheque', 'resume',
            'passport_photo', 'aadhaar_card_front', 'aadhaar_card_back', 'pan_card',
            'educational_certificates', 'experience_certificates', 'address_proof',
            'signed_offer_letter', 'covid_vaccination_certificate', 'signature'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $validatedData[$field . '_path'] = $targetDirectory . '/' . $fileName; // Store relative path
            }
        }

        // Remove file objects from validatedData before creating the record
        foreach ($fileFields as $field) {
            unset($validatedData[$field]);
        }

        Onboarding::create($validatedData);

        return redirect()->route('onboarding.index')->with('success', 'Onboarding data saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Onboarding $onboarding)
    {
        return view('hrms.master.onboarding-process.show', compact('onboarding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Onboarding $onboarding)
    {
        return view('hrms.master.onboarding-process.edit', compact('onboarding'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Onboarding $onboarding)
    {
        $validatedData = $request->validate([
            // Validation rules similar to store method
            // For files, make them nullable and handle updates
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'mobile_number' => 'required|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
            'personal_email_id' => 'required|email|max:255',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'father_mother_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'is_current_permanent' => 'boolean',
            'highest_qualification' => 'nullable|string|max:255',
            'stream_specialization' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|integer|digits:4',
            'cgpa_percentage' => 'nullable|string|max:50',
            'tenth_twelfth_marks_boards_year' => 'nullable|string|max:255',
            'certifications' => 'nullable|string',
            'previous_company_name' => 'nullable|string|max:255',
            'last_drawn_salary' => 'nullable|numeric',
            'reason_for_leaving' => 'nullable|string',
            'experience_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'relieving_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'date_of_joining' => 'required|date',
            'employee_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'reporting_manager' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'shift_timing' => 'nullable|string|max:100',
            'employment_type' => 'nullable|string|max:50',
            'probation_period_months' => 'nullable|integer',
            'bank_name' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'cancelled_cheque' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'has_medical_conditions' => 'boolean',
            'medical_history' => 'nullable|string',
            'is_covered_by_insurance' => 'boolean',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_relationship' => 'nullable|string|max:100',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'aadhaar_card_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'aadhaar_card_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'educational_certificates' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'experience_certificates' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'address_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signed_offer_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'covid_vaccination_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'declaration_agreed' => 'required|boolean',
            'declaration_place' => 'nullable|string|max:255',
            'declaration_date' => 'nullable|date',
            'signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Define the target directory within public
        $targetDirectory = 'onboarding_documents';
        $publicPath = public_path($targetDirectory);

        // Ensure the directory exists
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }

        $fileFields = [
            'experience_letter', 'relieving_letter', 'cancelled_cheque', 'resume',
            'passport_photo', 'aadhaar_card_front', 'aadhaar_card_back', 'pan_card',
            'educational_certificates', 'experience_certificates', 'address_proof',
            'signed_offer_letter', 'covid_vaccination_certificate', 'signature'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($onboarding->{$field . '_path'} && File::exists(public_path($onboarding->{$field . '_path'}))) {
                    File::delete(public_path($onboarding->{$field . '_path'}));
                }
                $file = $request->file($field);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $validatedData[$field . '_path'] = $targetDirectory . '/' . $fileName; // Store relative path
            } else {
                // If file is not re-uploaded, keep the old path
                $validatedData[$field . '_path'] = $onboarding->{$field . '_path'};
            }
            unset($validatedData[$field]); // Remove file object
        }

        $onboarding->update($validatedData);

        return redirect()->route('onboarding.index')->with('success', 'Onboarding data updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Onboarding $onboarding)
    {
        // Delete associated files
        $fileFields = [
            'experience_letter_path', 'relieving_letter_path', 'cancelled_cheque_path', 'resume_path',
            'passport_photo_path', 'aadhaar_card_front_path', 'aadhaar_card_back_path', 'pan_card_path',
            'educational_certificates_path', 'experience_certificates_path', 'address_proof_path',
            'signed_offer_letter_path', 'covid_vaccination_certificate_path', 'signature_path'
        ];

        foreach ($fileFields as $field) {
            if ($onboarding->{$field} && File::exists(public_path($onboarding->{$field}))) {
                File::delete(public_path($onboarding->{$field}));
            }
        }

        $onboarding->delete();

        return redirect()->route('onboarding.index')->with('success', 'Onboarding record deleted successfully!');
    }

    /**
     * Generate and download PDF for the specified resource.
     */
    public function downloadPdf(Onboarding $onboarding)
    {
        $passportPhotoBase64 = null;
        if ($onboarding->passport_photo_path && File::exists(public_path($onboarding->passport_photo_path))) {
            $type = pathinfo(public_path($onboarding->passport_photo_path), PATHINFO_EXTENSION);
            $data = File::get(public_path($onboarding->passport_photo_path));
            $passportPhotoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $signatureBase64 = null;
        if ($onboarding->signature_path && File::exists(public_path($onboarding->signature_path))) {
            $type = pathinfo(public_path($onboarding->signature_path), PATHINFO_EXTENSION);
            $data = File::get(public_path($onboarding->signature_path));
            $signatureBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('hrms.master.onboarding-process.onboarding-pdf', compact('onboarding', 'passportPhotoBase64', 'signatureBase64'));
        return $pdf->download('onboarding_' . $onboarding->full_name . '.pdf');
    }
}
