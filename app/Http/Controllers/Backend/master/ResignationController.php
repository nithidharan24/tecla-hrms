<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resignation;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf; // << add this

class ResignationController extends Controller
{
    public function index()
    {
        $resignationRecords = Resignation::all();
        return view('hrms.master.resignation-form.index', compact('resignationRecords'));
    }

    public function create()
    {
        return view('hrms.master.resignation-form.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'reporting_manager' => 'nullable|string|max:255',
            'official_email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'date_of_resignation' => 'required|date',
            'last_working_day' => 'required|date|after_or_equal:date_of_resignation',
            'notice_period_duration' => 'nullable|string|max:100',
            'mode_of_resignation' => 'required|string|max:50',
            'reason_for_resignation' => 'required|string|max:255',
            'detailed_explanation' => 'nullable|string',
            'other_reason' => 'nullable|string|max:255',
            'responsibilities_handed_over' => 'boolean',
            'person_handover_to' => 'nullable|string|max:255',
            'company_assets_returned' => 'boolean',
            'list_of_returned_items' => 'nullable|string',
            'serve_full_notice_period' => 'boolean',
            'leave_planned_during_notice' => 'nullable|string',
            'resignation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'medical_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'declaration_agreed' => 'required|boolean',
            'declaration_place' => 'nullable|string|max:255',
            'declaration_date' => 'nullable|date',
            'employee_signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'approval_status' => 'nullable|string|max:50',
            'resignation_acceptance_date' => 'nullable|date',
            'exit_interview_scheduled_date' => 'nullable|date',
            'clearance_status' => 'nullable|string|max:255',
            'final_settlement_status' => 'nullable|string|max:255',
            'feedback_notes' => 'nullable|string',
        ]);

        foreach (['responsibilities_handed_over','company_assets_returned','serve_full_notice_period','declaration_agreed'] as $bf) {
            $validatedData[$bf] = $request->boolean($bf);
        }
        if (($validatedData['reason_for_resignation'] ?? null) === 'Others' && $request->filled('other_reason')) {
            $validatedData['reason_for_resignation'] = 'Others: ' . $request->string('other_reason');
        }
        unset($validatedData['other_reason']);

        $targetDirectory = 'resignation_documents';
        $publicPath = public_path($targetDirectory);
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }
        $fileFields = ['resignation_letter', 'medical_certificate', 'employee_signature'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $validatedData[$field . '_path'] = $targetDirectory . '/' . $fileName;
            }
        }
        foreach ($fileFields as $field) { unset($validatedData[$field]); }

        Resignation::create($validatedData);

        return redirect()->route('resignation.index')->with('success', 'Resignation record created successfully!');
    }

    public function show(Resignation $resignation)
    {
        return view('hrms.master.resignation-form.show', compact('resignation'));
    }

    public function edit(Resignation $resignation)
    {
        return view('hrms.master.resignation-form.edit', compact('resignation'));
    }

    public function update(Request $request, Resignation $resignation)
    {
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'reporting_manager' => 'nullable|string|max:255',
            'official_email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'date_of_resignation' => 'required|date',
            'last_working_day' => 'required|date|after_or_equal:date_of_resignation',
            'notice_period_duration' => 'nullable|string|max:100',
            'mode_of_resignation' => 'required|string|max:50',
            'reason_for_resignation' => 'required|string|max:255',
            'detailed_explanation' => 'nullable|string',
            'other_reason' => 'nullable|string|max:255',
            'responsibilities_handed_over' => 'boolean',
            'person_handover_to' => 'nullable|string|max:255',
            'company_assets_returned' => 'boolean',
            'list_of_returned_items' => 'nullable|string',
            'serve_full_notice_period' => 'boolean',
            'leave_planned_during_notice' => 'nullable|string',
            'resignation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'medical_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'declaration_agreed' => 'required|boolean',
            'declaration_place' => 'nullable|string|max:255',
            'declaration_date' => 'nullable|date',
            'employee_signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'approval_status' => 'nullable|string|max:50',
            'resignation_acceptance_date' => 'nullable|date',
            'exit_interview_scheduled_date' => 'nullable|date',
            'clearance_status' => 'nullable|string|max:255',
            'final_settlement_status' => 'nullable|string|max:255',
            'feedback_notes' => 'nullable|string',
        ]);

        foreach (['responsibilities_handed_over','company_assets_returned','serve_full_notice_period','declaration_agreed'] as $bf) {
            $validatedData[$bf] = $request->boolean($bf);
        }
        if (($validatedData['reason_for_resignation'] ?? null) === 'Others' && $request->filled('other_reason')) {
            $validatedData['reason_for_resignation'] = 'Others: ' . $request->string('other_reason');
        }
        unset($validatedData['other_reason']);

        $targetDirectory = 'resignation_documents';
        $publicPath = public_path($targetDirectory);
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0777, true, true);
        }
        $fileFields = ['resignation_letter', 'medical_certificate', 'employee_signature'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($resignation->{$field . '_path'} && File::exists(public_path($resignation->{$field . '_path'}))) {
                    File::delete(public_path($resignation->{$field . '_path'}));
                }
                $file = $request->file($field);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $validatedData[$field . '_path'] = $targetDirectory . '/' . $fileName;
            } else {
                $validatedData[$field . '_path'] = $resignation->{$field . '_path'};
            }
            unset($validatedData[$field]);
        }

        $resignation->update($validatedData);

        return redirect()->route('resignation.index')->with('success', 'Resignation record updated successfully!');
    }

    public function destroy(Resignation $resignation)
    {
        $fileFields = ['resignation_letter_path', 'medical_certificate_path', 'employee_signature_path'];
        foreach ($fileFields as $field) {
            if ($resignation->{$field} && File::exists(public_path($resignation->{$field}))) {
                File::delete(public_path($resignation->{$field}));
            }
        }
        $resignation->delete();

        return redirect()->route('resignation.index')->with('success', 'Resignation record deleted successfully!');
    }

    /**
     * Generate and download PDF for the specified resignation.
     */
    public function downloadPdf(Resignation $resignation)
    {
        $signatureBase64 = null;
        if ($resignation->employee_signature_path && File::exists(public_path($resignation->employee_signature_path))) {
            $type = pathinfo(public_path($resignation->employee_signature_path), PATHINFO_EXTENSION);
            $data = File::get(public_path($resignation->employee_signature_path));
            $signatureBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('hrms.master.resignation-form.resignation-pdf', compact('resignation', 'signatureBase64'));
        return $pdf->download('resignation_' . preg_replace('/\s+/', '_', $resignation->employee_name) . '.pdf');
    }
}
