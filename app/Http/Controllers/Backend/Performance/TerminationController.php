<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\TerminationLetterMail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
class TerminationController extends Controller

{
    public function create(Request $request)
    {
        $activeEmployees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->orderBy('firstname')
            ->get();

        $selectedEmployeeId = $request->input('employee_id');
        $selectedEmployee = null;
        if ($selectedEmployeeId) {
            $selectedEmployee = DB::table('allemployees')
                ->where('employeeid', $selectedEmployeeId)
                ->where('deleted_at', 0)
                ->first();
        }

        $templates = DB::table('termination_letter_templates')->get();

        return view('hrms.performance.Termination.create', compact('activeEmployees', 'selectedEmployee', 'templates'));
    }

    public function index()
    {
        $terminations = DB::table('terminations')
            ->join('allemployees', 'allemployees.id', '=', 'terminations.employee_id')
            ->leftJoin('department', 'department.id', '=', 'allemployees.department')
            ->select(
                'terminations.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'allemployees.email', // Ensure employee email is selected
                'allemployees.joiningdate', // Ensure joiningdate is selected
                'department.department as department_name' // Use department_name for consistency
            )
            ->where('terminations.deleted_at', 0)
            ->orderBy('terminations.termination_date', 'desc')
            ->get();

        return view('hrms.performance.Termination.index', compact('terminations'));
    }

    public function store(Request $request)
    {
        $employee = DB::table('allemployees')
            ->where('allemployees.employeeid', $request->input('employee_id'))
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select('allemployees.*', 'department.department as department_name', 'designation.designation as designation_name')
            ->first();

        if (!$employee) {
            Log::error('TerminationController@store: Employee not found for employeeid: ' . $request->input('employee_id'));
            return redirect()->route('terminations.create')->with('error', 'Employee not found!');
        }

        $request->validate([
            'employee_id' => 'required|string|exists:allemployees,employeeid',
            'termination_type' => 'required|string|max:255',
            'termination_date' => 'required|date',
            'notice_date' => 'required|date',
            'termination_reason' => 'required|string|max:255',
        ]);

        $terminationId = DB::table('terminations')->insertGetId([
            'employee_id' => $employee->id,
            'termination_type' => $request->input('termination_type'),
            'termination_date' => $request->input('termination_date'),
            'reason' => $request->input('termination_reason'),
            'notice_date' => $request->input('notice_date'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('allemployees')->where('id', $employee->id)->update(['deleted_at' => 1]);

        $termination = DB::table('terminations')
            ->where('id', $terminationId)
            ->first();

        if ($termination) {
            // Call the sendEmail method to handle the email sending logic
            // Note: Request object is passed for consistency, but not strictly used in sendEmail beyond this point
            return $this->sendEmail($request, $termination->id);
        } else {
            Log::warning('TerminationController@store: Termination record not found after insert for employee ID: ' . $employee->id);
            return redirect()->route('employee.index')->with('success', 'Termination added, but letter could not be generated as termination record was not found.');
        }
    }

    /**
     * Sends the termination letter email with PDF attachment for an existing termination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $terminationId
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, $terminationId)
    {
        Log::debug('sendEmail method called for termination ID: ' . $terminationId);

        $termination = DB::table('terminations')->where('id', $terminationId)->first();

        if (!$termination) {
            Log::error('TerminationController@sendEmail: Termination record not found for ID: ' . $terminationId);
            return redirect()->back()->with('error', 'Termination record not found to send email.');
        }

        $employee = DB::table('allemployees')
            ->where('allemployees.id', $termination->employee_id)
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select('allemployees.*', 'department.department as department_name', 'designation.designation as designation_name')
            ->first();

        if (!$employee) {
            Log::error('TerminationController@sendEmail: Employee not found for termination ID: ' . $terminationId);
            return redirect()->back()->with('error', 'Employee associated with this termination not found.');
        }

        try {
            Log::info('Proceeding to generate letter for employee: ' . $employee->email);

            $template = DB::table('termination_letter_templates')->first(); // Fetch the first template

            if (!$template) {
                Log::warning('TerminationController@sendEmail: No termination letter template found in DB. Email will not be sent.');
                return redirect()->back()->with('error', 'No termination letter template found. Please create one first.');
            }

            Log::debug('Fetched template: ' . $template->name . ' (ID: ' . $template->id . ')');

            // Data to pass to the Blade template for rendering
            $data = [
                'employee' => $employee,
                'termination' => $termination,
                'emailSubject' => $template->subject, // Passed for use within the template if needed
                // Add any other dynamic data that your template expects here.
                // For example, if your template has [Your Company Name], [Your Company Address], etc.
                // you should either replace them directly in the template content in DB
                // or pass them as variables here, like:
                'companyName' => 'Your Company Name',
                'companyAddress' => '123 Main St, Anytown, USA',
                'companyPhone' => '+1 (555) 123-4567',
                'companyEmail' => 'info@yourcompany.com',
            ];

            Log::debug('Data prepared for Blade rendering. Employee email: ' . $employee->email);
            Log::debug('Template content type: ' . gettype($template->content) . '. First 100 chars: ' . substr($template->content, 0, 100));

            // Render the Blade template content (which is expected to be a full HTML string from DB)
            try {
                $renderedHtmlForBoth = Blade::render($template->content, $data);
                Log::info('Blade content rendered successfully. Length: ' . strlen($renderedHtmlForBoth) . ' chars. First 100 chars: ' . substr($renderedHtmlForBoth, 0, 100));
            } catch (\Throwable $e) {
                Log::error('Error rendering Blade template for termination ID: ' . $terminationId . ': ' . $e->getMessage());
                Log::error('Blade Render Exception: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to render letter template: ' . $e->getMessage() . '. Check logs for details.');
            }

            // The rendered content is already a full HTML document (assuming the DB template is structured that way)
            $htmlEmailContent = $renderedHtmlForBoth;
            $pdfHtmlContent = $renderedHtmlForBoth; // Use the same full HTML for PDF directly

            Log::debug('HTML for email prepared. Length: ' . strlen($htmlEmailContent) . '. First 100 chars: ' . substr($htmlEmailContent, 0, 100));
            Log::debug('HTML for PDF prepared. Length: ' . strlen($pdfHtmlContent) . '. First 100 chars: ' . substr($pdfHtmlContent, 0, 100));

            // Generate PDF content
            try {
                $pdf = Pdf::loadHtml($pdfHtmlContent); // Explicitly load HTML string
                $pdfContent = $pdf->output();
                Log::info('PDF content generated successfully. Size: ' . strlen($pdfContent) . ' bytes.');

                // --- Temporary: Save PDF to storage for debugging ---
                $pdfFileName = 'termination_letter_' . $employee->employeeid . '_' . now()->format('Ymd_His') . '.pdf';
                Storage::put('public/termination_letters/' . $pdfFileName, $pdfContent);
                Log::info('Temporary PDF saved to: storage/app/public/termination_letters/' . $pdfFileName);

            } catch (\Throwable $e) {
                Log::error('Error generating PDF for termination ID: ' . $terminationId . ': ' . $e->getMessage());
                Log::error('PDF Generation Exception: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage() . '. Check logs for details.');
            }

            // Send email with PDF attachment
            try {
                Mail::to($employee->email)->send(new TerminationLetterMail($employee, $termination, $pdfContent, $template->subject, $htmlEmailContent));
                Log::info('Termination letter email dispatched successfully to: ' . $employee->email . ' (Termination ID: ' . $terminationId . ')');
                return redirect()->back()->with('success', 'Termination letter sent successfully!');
            } catch (\Throwable $e) {
                Log::error('Error sending termination letter email for ID: ' . $terminationId . ': ' . $e->getMessage());
                Log::error('Mail Send Exception: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to send letter: ' . $e->getMessage() . '. Check logs for details.');
            }

        } catch (\Exception $e) {
            // This general catch block will only catch exceptions not caught by inner specific blocks
            Log::error('An unexpected general error occurred during termination letter process for ID: ' . $terminationId . ': ' . $e->getMessage());
            Log::error('General Catch Exception: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage() . '. Check logs for details.');
        }
    }

    public function edit($id)
    {
        $termination = DB::table('terminations')->where('id', $id)->first();

        if (!$termination) {
            return redirect()->route('terminations.index')->with('error', 'Termination record not found!');
        }

        $employee = DB::table('allemployees')->where('id', $termination->employee_id)->first();

        if (!$employee) {
            return redirect()->route('terminations.index')->with('error', 'Employee not found!');
        }

        return view('hrms.performance.Termination.edit', compact('termination', 'employee'));
    }

    public function update(Request $request, $id)
    {
        $termination = DB::table('terminations')->where('id', $id)->first();
        if (!$termination) {
            return redirect()->route('terminations.index')->with('error', 'Termination record not found!');
        }

        $newEmployee = DB::table('allemployees')
            ->where('employeeid', $request->input('employee_id'))
            ->first();

        if (!$newEmployee) {
            return redirect()->route('terminations.edit', $id)->with('error', 'New employee not found!')
                ->with(compact('termination'));
        }

        $request->validate([
            'employee_id' => 'required|string|exists:allemployees,employeeid',
            'termination_type' => 'required|string|max:255',
            'termination_date' => 'required|date',
            'notice_date' => 'required|date',
            'termination_reason' => 'required|string|max:255',
        ]);

        $oldEmployee = DB::table('allemployees')->where('id', $termination->employee_id)->first();

        DB::table('terminations')->where('id', $id)->update([
            'employee_id' => $newEmployee->id,
            'termination_type' => $request->input('termination_type'),
            'termination_date' => $request->input('termination_date'),
            'reason' => $request->input('termination_reason'),
            'notice_date' => $request->input('notice_date'),
            'updated_at' => now(),
        ]);

        DB::table('allemployees')->where('id', $newEmployee->id)->update(['deleted_at' => 1]);

        if ($oldEmployee && $oldEmployee->id !== $newEmployee->id) {
            DB::table('allemployees')->where('id', $oldEmployee->id)->update(['deleted_at' => 0]);
        }

        return redirect()->route('terminations.index')->with('success', 'Termination updated successfully!');
    }

    public function destroy($id)
    {
        $termination = DB::table('terminations')->where('id', $id)->first();
        if (!$termination) {
            return redirect()->route('terminations.index')->with('error', 'Termination record not found!');
        }

        DB::table('terminations')->where('id', $id)->update(['deleted_at' => 1]);
        DB::table('allemployees')->where('id', $termination->employee_id)->update(['deleted_at' => 0]);

        return redirect()->route('terminations.index')->with('success', 'Termination deleted and employee restored successfully!');
    }

    public function restore($id)
    {
        DB::table('terminations')->where('id', $id)->update(['deleted_at' => 0]);
        return redirect()->route('terminations.index')->with('success', 'Termination restored successfully!');
    }



        public function show($id)
    {
        $termination = DB::table('terminations')
            ->join('allemployees', 'allemployees.id', '=', 'terminations.employee_id')
            ->leftJoin('department', 'department.id', '=', 'allemployees.department')
            ->leftJoin('designation', 'designation.id', '=', 'allemployees.designation')
            ->select(
                'terminations.*',
                'allemployees.id as employee_id',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'allemployees.email',
                'allemployees.joiningdate',
                'allemployees.phone',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->where('terminations.id', $id)
            ->where('terminations.deleted_at', 0)
            ->first();

        if (!$termination) {
            return redirect()->route('terminations.index')->with('error', 'Terminated employee record not found!');
        }

        // Fetch employee profile details
        $employeeProfile = DB::table('employee_profile_main')
            ->where('employee_id', $termination->employee_id)
            ->first();

        if (!$employeeProfile) {
            $employeeProfile = (object) [
                'birthday' => '',
                'gender' => '',
                'address' => '',
                'state' => '',
                'country' => '',
                'pin_code' => '',
            ];
        }

        // Fetch emergency contact
        $emergencyContact = DB::table('employee_emergency_contact')
            ->where('employee_id', $termination->employee_id)
            ->first();

        // Fetch bank information
        $bankInfo = DB::table('employee_bank_informations')
            ->where('employee_id', $termination->employee_id)
            ->first();

        // Fetch bank statutory (salary information)
        $bankStatutory = DB::table('employee_bank_statutory')
            ->where('employee_id', $termination->employee_id)
            ->first();

        $pastSalaryRecords = DB::table('employee_bank_statutory')
            ->where('employee_id', $termination->employee_id)
            ->get();

        // Fetch family members
        $familyMembers = DB::table('employee_family_informations')
            ->where('employee_id', $termination->employee_id)
            ->get();

        // Fetch education information
        $educationInfos = DB::table('employee_education_informations')
            ->where('employee_id', $termination->employee_id)
            ->get();

        // Fetch experience information
        $experienceInfos = DB::table('employee_experience_informations')
            ->where('employee_id', $termination->employee_id)
            ->get();

        // Fetch personal information
        $personalInfo = DB::table('employee_personal_informations')
            ->where('employee_id', $termination->employee_id)
            ->first();

        return view('hrms.performance.Termination.show', compact(
            'termination',
            'employeeProfile',
            'emergencyContact',
            'bankInfo',
            'bankStatutory',
            'pastSalaryRecords',
            'familyMembers',
            'educationInfos',
            'experienceInfos',
            'personalInfo'
        ));
    }
}
