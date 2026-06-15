<?php

namespace App\Http\Controllers\Backend\Jobs;


use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\BackgroundVerification;
use App\Models\AppointmentLetterTemplate;
use App\Mail\BgAppointmentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use Blade;

class BackgroundVerificationController extends Controller
{
  public function index(Request $request)
{
    $branchId = getAdminBranchFilter();
    
    $query = BackgroundVerification::with(['candidate', 'verifiedBy'])
        ->whereHas('candidate'); // ✅ prevents null candidates

    if ($branchId) {
        $query->whereHas('candidate', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }

    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->whereHas('candidate', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    if ($request->has('start_date') && $request->start_date != '') {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->has('end_date') && $request->end_date != '') {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $bgvRecords = $query->orderBy('created_at', 'desc')->paginate(10);

    $statuses = [
        'not_started' => 'BGV Not Yet Done',
        'in_progress' => 'BGV In Progress',
        'completed' => 'BGV Completed',
        'failed' => 'BGV Failed'
    ];

    return view('hrms.Jobs.background-verification.index', compact('bgvRecords', 'statuses'));
}


public function create()
{
    $branchId = getAdminBranchFilter();

    // Get candidate IDs that already have BGV records
    $alreadyVerifiedIds = BackgroundVerification::pluck('candidate_id')->toArray();

    // Fetch only selected candidates who don't have a BGV record yet
    $query = Candidate::where('status', 'selected')
        ->whereNotIn('id', $alreadyVerifiedIds);

    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    $candidates = $query->orderBy('first_name')->get();

    return view('hrms.Jobs.background-verification.create', compact('candidates'));
}


  public function store(Request $request)
{
    $request->validate([
        'candidate_id' => 'required|exists:candidates,id',
        'status' => 'required|in:not_started,in_progress,completed,failed',
        'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        'remarks' => 'nullable|string',
        'verification_date' => 'nullable|date'
    ]);

    try {
        DB::beginTransaction();

        $filePath = null;
        
        // Handle single file upload
        if ($request->hasFile('document_file') && $request->file('document_file')) {
            $file = $request->file('document_file');
            $fileName = 'bgv_docs/' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public', $fileName);
            $filePath = str_replace('public/', '', $filePath);
        }

        $documentData = [
            'file_path' => $filePath,
            'original_name' => $request->hasFile('document_file') ? $request->file('document_file')->getClientOriginalName() : null,
            'uploaded_at' => now()->toDateTimeString()
        ];

        $bgvRecord = BackgroundVerification::create([
            'candidate_id' => $request->candidate_id,
            'status' => $request->status,
            'documents' => $filePath ? [$documentData] : [], // Store document data only if file exists
            'remarks' => $request->remarks,
            'verification_date' => $request->verification_date,
            'verified_by' => auth()->id()
        ]);

        DB::commit();

        return redirect()->route('background-verification.index')->with('success', 'Background verification record created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('BGV creation failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to create background verification record: ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    $bgvRecord = BackgroundVerification::findOrFail($id);

    $request->validate([
        'candidate_id' => 'required|exists:candidates,id',
        'status' => 'required|in:not_started,in_progress,completed,failed',
        'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        'remarks' => 'nullable|string',
        'verification_date' => 'nullable|date'
    ]);

    try {
        DB::beginTransaction();

        $existingDocuments = $bgvRecord->documents ?? [];
        $existingDocument = !empty($existingDocuments) ? $existingDocuments[0] : null;
        
        $filePath = $existingDocument['file_path'] ?? null;
        $originalName = $existingDocument['original_name'] ?? null;

        // Handle file upload for single document
        if ($request->hasFile('document_file') && $request->file('document_file')) {
            $file = $request->file('document_file');
            $fileName = 'bgv_docs/' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public', $fileName);
            $filePath = str_replace('public/', '', $filePath);
            $originalName = $file->getClientOriginalName();
            
            // Delete old file if exists
            if ($existingDocument && $existingDocument['file_path']) {
                Storage::delete('public/' . $existingDocument['file_path']);
            }
        }

        $documentData = [
            'file_path' => $filePath,
            'original_name' => $originalName,
            'uploaded_at' => $existingDocument['uploaded_at'] ?? now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ];

        $bgvRecord->update([
            'candidate_id' => $request->candidate_id,
            'status' => $request->status,
            'documents' => $filePath ? [$documentData] : [], // Store document data only if file exists
            'remarks' => $request->remarks,
            'verification_date' => $request->verification_date,
            'verified_by' => auth()->id()
        ]);

        DB::commit();

        return redirect()->route('background-verification.index')->with('success', 'Background verification record updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('BGV update failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update background verification record: ' . $e->getMessage());
    }
}
   // Show a single verification record
    public function show($id)
    {
        $verification = BackgroundVerification::with('candidate')->findOrFail($id);
        return view('hrms.Jobs.background-verification.show', compact('verification'));
    }


    public function edit($id)
    {
        $bgvRecord = BackgroundVerification::with('candidate')->findOrFail($id);
        
        $branchId = getAdminBranchFilter();
        $query = Candidate::where('status', 'selected');
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $candidates = $query->orderBy('first_name')->get();
        
        return view('hrms.Jobs.background-verification.edit', compact('bgvRecord', 'candidates'));
    }



    public function destroy($id)
    {
        $bgvRecord = BackgroundVerification::findOrFail($id);

        // Delete associated files
        if ($bgvRecord->documents) {
            foreach ($bgvRecord->documents as $document) {
                if ($document['file_path']) {
                    Storage::delete('public/' . $document['file_path']);
                }
            }
        }

        $bgvRecord->delete();

        return redirect()->route('background-verification.index')->with('success', 'Background verification record deleted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,failed'
        ]);

        $bgvRecord = BackgroundVerification::findOrFail($id);
        $bgvRecord->update([
            'status' => $request->status,
            'verified_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
    }

   
    /**
     * Generate appointment data for candidate
     */
    private function generateAppointmentData(Candidate $candidate)
    {
        $joiningDate = now()->addDays(14);
        $expectedSalary = $candidate->expected_salary ?: 300000;
        $monthlySalary = $expectedSalary / 12;

        // Calculate salary components
        $basicMonthly = $monthlySalary * 0.5;
        $hraMonthly = $monthlySalary * 0.2;
        $ccaMonthly = $monthlySalary * 0.1;
        $specialAllowanceMonthly = $monthlySalary * 0.15;
        $statutoryBonusMonthly = $monthlySalary * 0.05;

        // Deductions
        $pfEmployeeMonthly = $basicMonthly * 0.12;
        $esiEmployeeMonthly = $monthlySalary * 0.0075;
        $profTaxMonthly = 200;

        $netIncomeMonthly = $monthlySalary - $pfEmployeeMonthly - $esiEmployeeMonthly - $profTaxMonthly;

        return (object)[
            'joining_date' => $joiningDate->format('jS F Y'),
            'designation' => $candidate->position_applied,
            'department' => $this->getDepartmentFromPosition($candidate->position_applied),
            'reporting_to' => 'Department Manager',
            'work_location' => 'Corporate Office',
            'employment_type' => 'Full Time',
            'probation_period' => '6 months',
            'notice_period' => '60 days',
            'annual_ctc' => number_format($expectedSalary, 2),
            'ctc_words' => $this->convertNumberToWords($expectedSalary) . ' Only',
            
            // Monthly components
            'basic_monthly' => number_format($basicMonthly, 2),
            'hra_monthly' => number_format($hraMonthly, 2),
            'cca_monthly' => number_format($ccaMonthly, 2),
            'statutory_bonus_monthly' => number_format($statutoryBonusMonthly, 2),
            'training_allowance_monthly' => number_format(0, 2),
            'special_allowance_monthly' => number_format($specialAllowanceMonthly, 2),
            'vpp_monthly' => number_format(0, 2),
            'gross_monthly' => number_format($monthlySalary, 2),
            'pf_employer_monthly' => number_format(0, 2),
            'esi_employer_monthly' => number_format(0, 2),
            'pf_employee_monthly' => number_format($pfEmployeeMonthly, 2),
            'esi_employee_monthly' => number_format($esiEmployeeMonthly, 2),
            'staff_welfare_monthly' => number_format(0, 2),
            'prof_tax_monthly' => number_format($profTaxMonthly, 2),
            'net_income_monthly' => number_format($netIncomeMonthly, 2),
            'ctc_monthly' => number_format($monthlySalary, 2),
            
            // Annual components
            'basic_annual' => number_format($basicMonthly * 12, 2),
            'hra_annual' => number_format($hraMonthly * 12, 2),
            'cca_annual' => number_format($ccaMonthly * 12, 2),
            'statutory_bonus_annual' => number_format($statutoryBonusMonthly * 12, 2),
            'training_allowance_annual' => number_format(0, 2),
            'special_allowance_annual' => number_format($specialAllowanceMonthly * 12, 2),
            'vpp_annual' => number_format(0, 2),
            'gross_annual' => number_format($expectedSalary, 2),
            'pf_employer_annual' => number_format(0, 2),
            'esi_employer_annual' => number_format(0, 2),
            'pf_employee_annual' => number_format($pfEmployeeMonthly * 12, 2),
            'esi_employee_annual' => number_format($esiEmployeeMonthly * 12, 2),
            'staff_welfare_annual' => number_format(0, 2),
            'prof_tax_annual' => number_format($profTaxMonthly * 12, 2),
            'net_income_annual' => number_format($netIncomeMonthly * 12, 2),
            'ctc_annual' => number_format($expectedSalary, 2),
        ];
    }

    /**
     * Generate appointment letter PDF
     */
  

    /**
     * Helper methods (reuse from your existing controller)
     */
    private function getDepartmentFromPosition($position)
    {
        $position = strtolower($position);
        
        $departmentMap = [
            'developer' => 'IT',
            'software' => 'IT',
            'engineer' => 'Engineering',
            'designer' => 'Creative',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'manager' => 'Management',
            'hr' => 'Human Resources',
            'account' => 'Finance',
            'finance' => 'Finance',
        ];
        
        foreach ($departmentMap as $key => $department) {
            if (str_contains($position, $key)) {
                return $department;
            }
        }
        
        return 'Operations';
    }

    private function convertNumberToWords($number) 
    {
        if ($number <= 0) return 'Zero';
        
        $number = (int)$number;
        $words = [];
        
        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        if ($number >= 10000000) {
            $crores = (int)($number / 10000000);
            $words[] = $this->convertNumberToWords($crores) . ' Crore';
            $number %= 10000000;
        }
        
        if ($number >= 100000) {
            $lakhs = (int)($number / 100000);
            $words[] = $this->convertNumberToWords($lakhs) . ' Lakh';
            $number %= 100000;
        }
        
        if ($number >= 1000) {
            $thousandsVal = (int)($number / 1000);
            $words[] = $this->convertNumberToWords($thousandsVal) . ' Thousand';
            $number %= 1000;
        }
        
        if ($number >= 100) {
            $hundreds = (int)($number / 100);
            $words[] = $units[$hundreds] . ' Hundred';
            $number %= 100;
        }
        
        if ($number >= 20) {
            $tensVal = (int)($number / 10);
            $words[] = $tens[$tensVal];
            $number %= 10;
        }
        
        if ($number >= 10) {
            $words[] = $teens[$number - 10];
            $number = 0;
        }
        
        if ($number > 0) {
            $words[] = $units[$number];
        }
        
        return implode(' ', $words);
    }


    

    public function sendAppointmentLetter($id)
    {
        try {
            $bgvRecord = BackgroundVerification::with('candidate')->findOrFail($id);
            $candidate = $bgvRecord->candidate;

            // Check if BGV is completed
            if ($bgvRecord->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot send appointment letter. Background verification is not completed.'
                ], 400);
            }

            // Check if there's an appointment letter template
            $appointmentTemplate = AppointmentLetterTemplate::first();

            if (!$appointmentTemplate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No appointment letter template found. Please create one first.'
                ], 400);
            }

            // Generate appointment letter data
            $appointmentData = $this->generateAppointmentData($candidate);
            
            $pdfContent = $this->generateAppointmentPDF($appointmentTemplate->id, $candidate, $appointmentData);
            
            // Send appointment letter email with PDF attached
            Mail::to($candidate->email)->send(new BgAppointmentMail($candidate, $pdfContent));
            
            Log::info('Appointment letter sent via BGV module', [
                'candidate_id' => $candidate->id,
                'bgv_record_id' => $id,
                'email' => $candidate->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment letter sent successfully to ' . $candidate->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment letter from BGV', [
                'bgv_record_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send appointment letter: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCompanySettings()
    {
        $generalSettings = DB::table('general_settings')->first();
        $logoSetting = DB::table('logo_settings')->first();

        return [
            'companyName' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail' => $generalSettings->contact_email ?? '',
            'companyPhone' => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->address ?? '',
            'gm_name' => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title' => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo' => $logoSetting->logo ?? 'uploads/media_6896d63471f63.png',
        ];
    }

    private function generateAppointmentPDF($templateId, Candidate $candidate, $appointmentData)
    {
        try {
            $template = AppointmentLetterTemplate::findOrFail($templateId);
            $settings = $this->getCompanySettings();
            
            // Extract settings for use in blade render
            extract($settings);

            $employee = (object)[
                'firstname' => $candidate->first_name,
                'lastname' => $candidate->last_name,
                'employeeid' => 'EMP' . str_pad($candidate->id, 4, '0', STR_PAD_LEFT),
                'email' => $candidate->email,
                'phone' => $candidate->phone,
                'address' => $candidate->address ?? 'Address to be provided',
            ];

            $logoPath = null;
            if (!empty($logo)) {
                $cleanLogo = ltrim($logo, '/');
                $logoPath = public_path($cleanLogo);
            }

            $html = Blade::render($template->content, [
                'employee' => $employee,
                'appointment' => $appointmentData,
                'companyName' => $companyName,
                'companyEmail' => $companyEmail,
                'companyPhone' => $companyPhone,
                'companyAddress' => $companyAddress,
                'gm_name' => $gm_name,
                'gm_title' => $gm_title,
                'logo' => $logo,
                'logoPath' => $logoPath
            ]);

            $pdf = PDF::loadHTML($html)
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true,
                          'defaultFont' => 'sans-serif'
                      ]);

            return $pdf->output();

        } catch (\Exception $e) {
            Log::error('Failed to generate appointment letter PDF from BGV', [
                'template_id' => $templateId,
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}