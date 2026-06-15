<?php

namespace App\Http\Controllers\Backend\Jobs;
 
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\OfferLetter;
use App\Models\AppointmentLetterTemplate;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use App\Mail\CandidateSelectedMail;
use App\Mail\CandidateRejectedMail;
use App\Mail\CandidateShortlistedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;

class AddresumeController extends Controller
{
    public function index(Request $request)
{
    $branchId = getAdminBranchFilter();
    
    $query = Candidate::query();
    
    // Apply branch filter
    if ($branchId) {
        $query->where('branch_id', $branchId);
    }
    
    // Apply search filter
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('position_applied', 'LIKE', "%{$search}%");
        });
    }
    
    // Apply position filter
    if ($request->has('position') && $request->position != '') {
        $query->where('position_applied', $request->position);
    }
    
    // Apply status filter
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }
    
    // Apply experience filter
    if ($request->has('experience') && $request->experience != '') {
        $query->where('experience_years', '>=', $request->experience);
    }
    
    // Apply date range filter
    if ($request->has('start_date') && $request->start_date != '') {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    
    if ($request->has('end_date') && $request->end_date != '') {
        $query->whereDate('created_at', '<=', $request->end_date);
    }
    
    // Get unique positions for filter dropdown - ALWAYS get this
    $positions = Candidate::select('position_applied')
        ->distinct()
        ->whereNotNull('position_applied')
        ->where('position_applied', '!=', '')
        ->orderBy('position_applied')
        ->get();
    
    $candidates = $query->orderBy('created_at', 'desc')->paginate(10);
    
    return view('hrms.Jobs.add-resume.index', compact('candidates', 'positions'));
}

public function create()
{
    $branchId = getAdminBranchFilter();
    $jobs = DB::table('managejobs')
        ->where('status', 'open')
        ->select('id', 'job_title')
        ->get();

    $employees = DB::table('allemployees')
        ->select('id', 'firstname', 'lastname', 'employeeid', 'email')
        ->where('deleted_at', 0)
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->orderBy('firstname')
        ->get();

    return view('hrms.Jobs.add-resume.create', compact('jobs', 'employees'));
}


    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'position_applied' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'experience_years' => 'required|string|max:10',
            'expected_salary' => 'nullable|numeric',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'hiring_manager_id' => 'nullable|integer',
            'team_lead_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }
        $branchId = Session::get('branch_id');
        Candidate::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'position_applied' => $request->position_applied,
            'source' => $request->source,
            'experience_years' => $request->experience_years,
            'expected_salary' => $request->expected_salary,
            'resume_path' => $resumePath,
            'notes' => $request->notes,
            'hiring_manager_id' => $request->hiring_manager_id,
            'team_lead_id' => $request->team_lead_id,
            'assigned_at' => ($request->hiring_manager_id || $request->team_lead_id) ? now() : null,
            'branch_id' => $branchId, // store branch_id from session
            'status' => 'applied'
        ]);

        $candidate = Candidate::where('email', $request->email)->first();
        if ($candidate) {
            $this->notifyAssignedInterviewers($candidate);
        }

        return redirect()->route('recruitment.index')->with('success', 'Candidate added successfully!');
    }

    public function show($id)
    {
        $candidate = Candidate::with('interviews')->findOrFail($id);
        return view('hrms.Jobs.add-resume.show', compact('candidate'));
    }

    public function edit($id)
    {
        $branchId = getAdminBranchFilter();
        $candidate = Candidate::findOrFail($id);
        $jobs = DB::table('managejobs')->where('status', 'open')->get();
        $employees = DB::table('allemployees')
            ->select('id', 'firstname', 'lastname', 'employeeid', 'email')
            ->where('deleted_at', 0)
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('firstname')
            ->get();
        return view('hrms.Jobs.add-resume.edit', compact('candidate', 'jobs', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'position_applied' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'experience_years' => 'required|string|max:10',
            'expected_salary' => 'nullable|numeric',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'hiring_manager_id' => 'nullable|integer',
            'team_lead_id' => 'nullable|integer',
            'status' => 'required|in:applied,shortlisted,telephonic_scheduled,telephonic_completed,interview_scheduled,interview_completed,selected,rejected,offer_sent',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $candidate->status;
        $newStatus = $request->status;

        $resumePath = $candidate->resume_path;
        if ($request->hasFile('resume')) {
            // Delete old resume
            if ($resumePath) {
                Storage::disk('public')->delete($resumePath);
            }
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $candidate->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'position_applied' => $request->position_applied,
            'source' => $request->source,
            'experience_years' => $request->experience_years,
            'expected_salary' => $request->expected_salary,
            'resume_path' => $resumePath,
            'status' => $newStatus,
            'hiring_manager_id' => $request->hiring_manager_id,
            'team_lead_id' => $request->team_lead_id,
            'assigned_at' => ($request->hiring_manager_id || $request->team_lead_id) ? ($candidate->assigned_at ?? now()) : null,
            'notes' => $request->notes
        ]);

        if ($request->hiring_manager_id || $request->team_lead_id) {
            $candidate->refresh();
            $this->notifyAssignedInterviewers($candidate);
        }

        // Send email if status changed to selected, rejected, or shortlisted
        if ($oldStatus !== $newStatus) {
            $this->sendStatusChangeEmail($candidate, $newStatus);
        }

        return redirect()->route('recruitment.index')->with('success', 'Candidate updated successfully!');
    }

    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);

        // Delete resume file
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }

        $candidate->delete();

        return redirect()->route('recruitment.index')->with('success', 'Candidate deleted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $oldStatus = $candidate->status;
            $newStatus = $request->status;

            // Update the status first
            $candidate->update(['status' => $newStatus]);

            // Log the status change
            Log::info('Status updated successfully', [
                'candidate_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Send email if status changed to selected, rejected, or shortlisted
            if ($oldStatus !== $newStatus) {
                try {
                    $this->sendStatusChangeEmail($candidate, $newStatus);
                } catch (\Exception $emailError) {
                    // Log email error but don't fail the status update
                    Log::error('Email sending failed but status updated', [
                        'candidate_id' => $id,
                        'status' => $newStatus,
                        'email_error' => $emailError->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Status update failed', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRoundStatus(Request $request, $id)
    {
        $request->validate([
            'round' => 'required|in:hr_interview_status,technical_interview_status,manager_round_status,final_round_status',
            'status' => 'required|in:pending,scheduled,completed,selected,rejected'
        ]);

        try {
            $candidate = Candidate::findOrFail($id);
            $candidate->update([
                $request->round => $request->status
            ]);

            if ($request->status === 'rejected') {
                $candidate->update(['status' => 'rejected']);
                $this->sendStatusChangeEmail($candidate, 'rejected');
            } elseif ($request->round === 'final_round_status' && $request->status === 'selected') {
                $candidate->update(['status' => 'selected']);
                $this->sendStatusChangeEmail($candidate, 'selected');
            } elseif ($request->status === 'scheduled') {
                $this->sendInterviewStatusEmail($candidate, $request->round, $request->status);
            }

            return response()->json(['success' => true, 'message' => 'Interview round status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Round status update failed', [
                'candidate_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to update round status.'], 500);
        }
    }

    /**
     * Send email notification based on status change
     */


    public function viewResume($id)
    {
        try {
            // Get the candidate record
            $candidate = Candidate::findOrFail($id);

            if (!$candidate->resume_path) {
                Log::error('Resume path is empty', ['candidate_id' => $id]);
                return redirect()->route('add-resume.index')->with('error', 'Resume file not found.');
            }

            // Try multiple possible file paths
            $possiblePaths = [
                storage_path('app/public/' . $candidate->resume_path),
                storage_path('app/' . $candidate->resume_path),
                public_path('storage/' . $candidate->resume_path),
                public_path($candidate->resume_path)
            ];
            $filePath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }

            // If file not found in any of the paths, log the issue
            if (!$filePath) {
                Log::error('Resume file not found in any path', [
                    'candidate_id' => $id,
                    'resume_path' => $candidate->resume_path,
                    'checked_paths' => $possiblePaths
                ]);
                return redirect()->route('add-resume.index')->with('error', 'Resume file not found on server.');
            }

            // Get file info
            $fileName = basename($filePath);
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);

            // Check if download is requested
            $isDownload = request()->has('download') && request()->get('download') == '1';

            // Generate a proper filename for download
            $candidateName = $candidate->first_name . '_' . $candidate->last_name;
            $downloadFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidateName) . '_Resume.' . $fileExtension;

            Log::info('Serving resume file', [
                'candidate_id' => $id,
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'mime_type' => $mimeType,
                'is_download' => $isDownload
            ]);

            // Return file response with proper headers
            if ($isDownload) {
                // Force download
                return Response::download($filePath, $downloadFileName, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'attachment; filename="' . $downloadFileName . '"'
                ]);
            } else {
                // Display in browser (for PDFs and images)
                return Response::file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $downloadFileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error serving resume file', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('add-resume.index')->with('error', 'Error accessing resume file: ' . $e->getMessage());
        }
    }

    /**
     * Get MIME type based on file extension
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    private function notifyAssignedInterviewers(Candidate $candidate)
    {
        $employeeIds = array_filter([$candidate->hiring_manager_id ?? null, $candidate->team_lead_id ?? null]);

        if (empty($employeeIds)) {
            return;
        }

        $employees = DB::table('allemployees')
            ->whereIn('id', $employeeIds)
            ->whereNotNull('email')
            ->get();

        foreach ($employees as $employee) {
            try {
                Mail::raw(
                    "Dear {$employee->firstname},\n\nYou have been assigned to the hiring process for {$candidate->first_name} {$candidate->last_name} ({$candidate->position_applied}).\n\nPlease share your available interview date and time slot with HR so the interview can be scheduled.\n\nBest Regards,\nHR Team",
                    function ($message) use ($employee, $candidate) {
                        $message->to($employee->email)
                            ->subject('Hiring Assignment: ' . $candidate->first_name . ' ' . $candidate->last_name);
                    }
                );
            } catch (\Exception $e) {
                Log::error('Failed to notify assigned interviewer', [
                    'candidate_id' => $candidate->id,
                    'employee_id' => $employee->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function sendInterviewStatusEmail(Candidate $candidate, $round, $status)
    {
        try {
            $roundLabel = ucwords(str_replace(['_', 'status'], [' ', ''], $round));

            Mail::raw(
                "Dear {$candidate->first_name},\n\nYour {$roundLabel} interview status has been updated to " . ucfirst($status) . ".\n\nPlease watch your email for schedule details from HR.\n\nBest Regards,\nHR Team",
                function ($message) use ($candidate, $roundLabel) {
                    $message->to($candidate->email)
                        ->subject($roundLabel . ' Interview Status Update');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send interview status email', [
                'candidate_id' => $candidate->id,
                'round' => $round,
                'error' => $e->getMessage()
            ]);
        }
    }

    
    private function generateOfferLetterPDF(OfferLetter $template, Candidate $candidate)
    {
        $employee = (object)[
            'firstname' => $candidate->first_name,
            'lastname' => $candidate->last_name,
            'employeeid' => 'CAND' . str_pad($candidate->id, 4, '0', STR_PAD_LEFT),
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'address' => $candidate->address ?? 'Address to be provided',
        ];

        $appointment = $this->generateAppointmentData($candidate);
        $settings = $this->getOfferCompanySettings();

        $data = array_merge(
            $settings,
            $this->getOfferSignatureData(),
            compact('employee', 'appointment')
        );

        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
        }

        $html = Blade::render($this->prepareOfferTemplateForRender($template->content), $data);
        $html = $this->injectOfferSignature($html, $data['offerSignatureDataUri'] ?? null);

        return PDF::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ])
            ->output();
    }

    private function getOfferCompanySettings(): array
    {
        $generalSettings = GeneralSetting::first();
        $logoSetting = LogoSetting::first();
        $logo = $logoSetting->logo ?? null;

        if ($logo && !file_exists(public_path($logo))) {
            $possiblePaths = [
                $logo,
                'uploads/' . $logo,
                'storage/' . $logo,
                'images/' . $logo,
                'assets/images/' . $logo,
                'logo/' . $logo,
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists(public_path($path))) {
                    $logo = $path;
                    break;
                }
            }

            if (!file_exists(public_path($logo))) {
                $logo = null;
            }
        }

        return [
            'companyName' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail' => $generalSettings->contact_email ?? '',
            'companyPhone' => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->contact_address ?? '',
            'gm_name' => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title' => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo' => $logo,
            'logoPath' => null,
        ];
    }

    private function getOfferSignatureData(string $letterType = 'offer_letter'): array
    {
        $signatureData = [
            'offerSignaturePath' => null,
            'offerSignaturePublicPath' => null,
            'offerSignatureDataUri' => null,
        ];

        if (!Schema::hasTable('letter_signatures')) {
            return $signatureData;
        }

        $signature = DB::table('letter_signatures')
            ->where('letter_type', $letterType)
            ->first();

        if (!$signature || empty($signature->signature_path)) {
            return $signatureData;
        }

        $publicPath = public_path($signature->signature_path);

        if (!File::exists($publicPath)) {
            return $signatureData;
        }

        $mimeType = $signature->mime_type ?: File::mimeType($publicPath);

        return [
            'offerSignaturePath' => $signature->signature_path,
            'offerSignaturePublicPath' => $publicPath,
            'offerSignatureDataUri' => 'data:' . $mimeType . ';base64,' . base64_encode(File::get($publicPath)),
        ];
    }

    private function prepareOfferTemplateForRender(string $rawContent): string
    {
        return str_replace('@{{', '{{', $rawContent);
    }

    private function injectOfferSignature(string $html, ?string $signatureDataUri): string
    {
        if (
            empty($signatureDataUri) ||
            strpos($html, 'offer-signature-img') !== false ||
            strpos($html, 'authorized-signature') !== false
        ) {
            return $html;
        }

        $signatureHtml = '<style>.dynamic-signature + .sig-name{margin-top:4px !important;}</style>'
            . '<div class="dynamic-signature" style="height:46px;margin:8px 0 0;display:flex;align-items:flex-end;">'
            . '<img class="offer-signature-img" src="' . $signatureDataUri . '" alt="Authorized Signature" style="max-height:44px;max-width:170px;object-fit:contain;display:block;">'
            . '</div>';

        $updatedHtml = preg_replace('/(<div\s+class=(["\'])sig-name\2[^>]*>)/i', $signatureHtml . '$1', $html, 1);

        return $updatedHtml ?: $html;
    }


  private function sendStatusChangeEmail(Candidate $candidate, $status)
    {
        try {
            switch ($status) {
                case 'selected':
                    $offerTemplate = OfferLetter::where('is_active', true)->first()
                        ?: OfferLetter::orderBy('created_at', 'desc')->first();
                    
                    if ($offerTemplate) {
                        try {
                            $pdfContent = $this->generateOfferLetterPDF($offerTemplate, $candidate);
                            
                            Mail::to($candidate->email)->send(new CandidateSelectedMail($candidate, $pdfContent));
                            
                            Log::info('Selection email with offer letter sent to candidate', [
                                'candidate_id' => $candidate->id,
                                'email' => $candidate->email,
                                'offer_template_id' => $offerTemplate->id,
                                'status' => $status
                            ]);
                        } catch (\Exception $templateError) {
                            Log::warning('Offer letter generation failed, sending regular selection email', [
                                'candidate_id' => $candidate->id,
                                'error' => $templateError->getMessage()
                            ]);
                            
                            Mail::to($candidate->email)->send(new CandidateSelectedMail($candidate));
                        }
                    } else {
                        // No template found, send regular selection email
                        Mail::to($candidate->email)->send(new CandidateSelectedMail($candidate));
                        Log::info('Regular selection email sent (no offer letter template)', [
                            'candidate_id' => $candidate->id,
                            'email' => $candidate->email,
                            'status' => $status
                        ]);
                    }
                    break;

                case 'rejected':
                    Mail::to($candidate->email)->send(new CandidateRejectedMail($candidate));
                    Log::info('Rejection email sent to candidate', [
                        'candidate_id' => $candidate->id,
                        'email' => $candidate->email,
                        'status' => $status
                    ]);
                    break;

                case 'shortlisted':
                    Mail::to($candidate->email)->send(new CandidateShortlistedMail($candidate));
                    Log::info('Shortlisted email sent to candidate', [
                        'candidate_id' => $candidate->id,
                        'email' => $candidate->email,
                        'status' => $status
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status change email', [
                'candidate_id' => $candidate->id,
                'email' => $candidate->email,
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw the error to prevent breaking the status update
        }
    }

    
public function debugTemplate($id)
{
    try {
        $candidate = Candidate::findOrFail($id);
        $template = AppointmentLetterTemplate::first();
        
        if (!$template) {
            return response()->json(['error' => 'No template found']);
        }

        // Get the raw template content
        $templateContent = $template->content;
        
        // Extract all variables used in the template
        preg_match_all('/\$(\w+)/', $templateContent, $matches);
        $variables = array_unique($matches[1]);
        
        // Filter out PHP and Laravel specific variables
        $templateVariables = array_filter($variables, function($var) {
            return !in_array($var, ['__env', 'app', 'errors', 'message', 'employee', 'appointment']);
        });

        return response()->json([
            'template_variables' => $templateVariables,
            'template_content_preview' => substr($templateContent, 0, 500) . '...',
            'available_data' => [
                'employee_fields' => ['firstname', 'lastname', 'employeeid', 'email', 'phone', 'address'],
                'appointment_fields' => array_keys((array)$this->generateAppointmentData($candidate))
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}

    /**
     * Generate appointment letter PDF
     */
    private function generateAppointmentPDF($templateId, Candidate $candidate, $appointmentData)
    {
        try {
            $template = AppointmentLetterTemplate::findOrFail($templateId);
            
            // Create employee object for the template
            $employee = (object)[
                'firstname' => $candidate->first_name,
                'lastname' => $candidate->last_name,
                'employeeid' => 'EMP' . str_pad($candidate->id, 4, '0', STR_PAD_LEFT),
                'email' => $candidate->email,
                'phone' => $candidate->phone,
                'address' => $candidate->address ?? 'Address to be provided',
            ];

            $settings = $this->getCompanySettings();
            if (!empty($settings['logo'])) {
                $settings['logoPath'] = public_path(ltrim($settings['logo'], '/'));
            }

            // Render the template with actual candidate and appointment data
            $html = Blade::render($template->content, [
                'employee' => $employee,
                'appointment' => $appointmentData
            ] + $settings + $this->getOfferSignatureData('appointment_letter'));
            
            $pdf = PDF::loadHTML($html)
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true,
                          'defaultFont' => 'sans-serif'
                      ]);
            
            return $pdf->output();
            
        } catch (\Exception $e) {
            Log::error('Failed to generate appointment letter PDF', [
                'template_id' => $templateId,
                'candidate_id' => $candidate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Helper method to get department from position
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

    /**
     * Convert number to words
     */
    private function convertNumberToWords($number) 
    {
        if ($number <= 0) {
            return 'Zero';
        }
        
        $number = (int)$number;
        $words = [];
        
        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $thousands = ['', 'Thousand', 'Lakh', 'Crore'];
        
        // Handle lakhs and crores (Indian numbering system)
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

    /**
     * Manual appointment letter sending endpoint
     */
    public function sendAppointmentLetter($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            
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
            
            // Generate appointment letter PDF
            $pdfContent = $this->generateAppointmentPDF($appointmentTemplate->id, $candidate, $appointmentData);
            
            // Send selection email with appointment letter PDF attached
            Mail::to($candidate->email)->send(new CandidateSelectedMail($candidate, $pdfContent));
            
            Log::info('Manual appointment letter sent to candidate', [
                'candidate_id' => $candidate->id,
                'email' => $candidate->email,
                'appointment_template_id' => $appointmentTemplate->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment letter sent successfully to ' . $candidate->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment letter', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send appointment letter: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Generate appointment data for candidate that matches template expectations
 */
private function generateAppointmentData(Candidate $candidate)
{
    // Calculate joining date (2 weeks from now)
    $joiningDate = now()->addDays(14);
    
    // Calculate CTC based on expected salary or default
    $expectedSalary = $candidate->expected_salary ?: 300000; // Default 3 LPA if not specified
    $monthlySalary = $expectedSalary / 12;

    // Calculate salary components (typical salary structure)
    $basicMonthly = $monthlySalary * 0.5;    // 50% basic
    $hraMonthly = $monthlySalary * 0.2;      // 20% HRA  
    $ccaMonthly = $monthlySalary * 0.1;      // 10% conveyance
    $specialAllowanceMonthly = $monthlySalary * 0.15; // 15% special allowance
    $statutoryBonusMonthly = $monthlySalary * 0.05;   // 5% bonus

    // Deductions
    $pfEmployeeMonthly = $basicMonthly * 0.12; // 12% of basic for PF
    $esiEmployeeMonthly = $monthlySalary * 0.0075; // 0.75% of gross for ESI
    $profTaxMonthly = 200; // Professional tax

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
        
        // Salary components - Monthly (all the fields your template expects)
        'basic_monthly' => number_format($basicMonthly, 2),
        'hra_monthly' => number_format($hraMonthly, 2),
        'cca_monthly' => number_format($ccaMonthly, 2),
        'statutory_bonus_monthly' => number_format($statutoryBonusMonthly, 2),
        'training_allowance_monthly' => number_format(0, 2), // Set to 0 if not applicable
        'special_allowance_monthly' => number_format($specialAllowanceMonthly, 2),
        'vpp_monthly' => number_format(0, 2), // Set to 0 if not applicable
        'gross_monthly' => number_format($monthlySalary, 2),
        'pf_employer_monthly' => number_format(0, 2), // Employer PF contribution
        'esi_employer_monthly' => number_format(0, 2), // Employer ESI contribution
        'pf_employee_monthly' => number_format($pfEmployeeMonthly, 2),
        'esi_employee_monthly' => number_format($esiEmployeeMonthly, 2),
        'staff_welfare_monthly' => number_format(0, 2), // Set to 0 if not applicable
        'prof_tax_monthly' => number_format($profTaxMonthly, 2),
        'net_income_monthly' => number_format($netIncomeMonthly, 2),
        'ctc_monthly' => number_format($monthlySalary, 2),
        
        // Salary components - Annual
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
}
