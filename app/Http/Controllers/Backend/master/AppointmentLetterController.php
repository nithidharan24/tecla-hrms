<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppointmentLetterTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;

class AppointmentLetterController extends Controller
{
    /* ─────────────────────────────────────────────
     | COMPANY SETTINGS
     ───────────────────────────────────────────── */

    private function getCompanySettings(): array
    {
        $generalSettings = DB::table('general_settings')->first();

        $logoSetting = DB::table('logo_settings')->first();

        $logo = null;

        if ($logoSetting && !empty($logoSetting->logo)) {
            $logo = $logoSetting->logo;
        }

        // Resolve correct logo path
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

            'companyName' =>
                $generalSettings->site_name
                ?? 'TECLA MEDIA',

            'companyEmail' =>
                $generalSettings->contact_email
                ?? '',

            'companyPhone' =>
                $generalSettings->contact_phone
                ?? '',

            'companyAddress' =>
                $generalSettings->address
                ?? '',

            'gm_name' =>
                $generalSettings->gm_name
                ?? 'A.SURENDER',

            'gm_title' =>
                $generalSettings->gm_title
                ?? 'GENERAL MANAGER',

            'logo' => $logo,
        ];
    }

    private function getLetterSignatureData(string $letterType): array
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

    /* ─────────────────────────────────────────────
     | INDEX
     ───────────────────────────────────────────── */

    public function index()
    {
        $templates = AppointmentLetterTemplate::orderBy(
            'created_at',
            'desc'
        )->get();

        return view(
            'hrms.master.appointment-letter.index',
            compact('templates')
        );
    }

    /* ─────────────────────────────────────────────
     | CREATE
     ───────────────────────────────────────────── */

    public function create()
    {
        $settings = $this->getCompanySettings();

        return view(
            'hrms.master.appointment-letter.create',
            $settings
        );
    }

    /* ─────────────────────────────────────────────
     | STORE
     ───────────────────────────────────────────── */

    public function store(Request $request)
    {
        $validatedData = $request->validate([

            'name' =>
                'required|string|max:255|unique:appointment_letter_templates,name',

            'content' =>
                'required|string',
        ]);

        AppointmentLetterTemplate::create($validatedData);

        return redirect()
            ->route('appointment-letter.index')
            ->with(
                'success',
                'Appointment letter template created successfully.'
            );
    }

    /* ─────────────────────────────────────────────
     | SHOW
     ───────────────────────────────────────────── */

    public function show($id)
    {
        $appointmentLetter =
            AppointmentLetterTemplate::findOrFail($id);

        return view(
            'hrms.master.appointment-letter.show',
            compact('appointmentLetter')
        );
    }

    /* ─────────────────────────────────────────────
     | EDIT
     ───────────────────────────────────────────── */

    public function edit($id)
    {
        $appointmentLetter =
            AppointmentLetterTemplate::findOrFail($id);

        $settings = $this->getCompanySettings();

        return view(
            'hrms.master.appointment-letter.edit',
            compact('appointmentLetter') + $settings
        );
    }

    /* ─────────────────────────────────────────────
     | UPDATE
     ───────────────────────────────────────────── */

    public function update(Request $request, $id)
    {
        $appointmentLetter =
            AppointmentLetterTemplate::findOrFail($id);

        $validatedData = $request->validate([

            'name' =>
                'required|string|max:255|unique:appointment_letter_templates,name,' .
                $appointmentLetter->id,

            'content' =>
                'required|string',
        ]);

        $appointmentLetter->update($validatedData);

        return redirect()
            ->route('appointment-letter.index')
            ->with(
                'success',
                'Appointment letter template updated successfully.'
            );
    }

    /* ─────────────────────────────────────────────
     | DELETE
     ───────────────────────────────────────────── */

    public function destroy($id)
    {
        AppointmentLetterTemplate::findOrFail($id)->delete();

        return redirect()
            ->route('appointment-letter.index')
            ->with(
                'success',
                'Appointment letter template deleted successfully.'
            );
    }

    /* ─────────────────────────────────────────────
     | PREVIEW
     ───────────────────────────────────────────── */

    public function preview($id)
    {
        $template =
            AppointmentLetterTemplate::findOrFail($id);

        /* ─────────────────────────────
         | COMPANY SETTINGS
         ───────────────────────────── */

        $settings = $this->getCompanySettings();

        /* ─────────────────────────────
         | DUMMY EMPLOYEE
         ───────────────────────────── */

        $employee = (object)[

            'firstname' =>
                'Remya',

            'lastname' =>
                'AR',

            'employeeid' =>
                'EMP001',

            'email' =>
                'remya.ar@example.com',

            'phone' =>
                '+91 9876543210',

            'address' =>
                'Anna Nagar, Chennai',

        ];

        /* ─────────────────────────────
         | DUMMY APPOINTMENT DATA
         ───────────────────────────── */

        $appointment = (object)[

            'joining_date' =>
                '2026-06-01',

            'designation' =>
                'Digital Marketing Executive',

            'department' =>
                'Marketing',

            'reporting_to' =>
                'Marketing Manager',

            'work_location' =>
                'Chennai',

            'employment_type' =>
                'Full Time',

            'probation_period' =>
                '6 Months',

            'notice_period' =>
                '60 Days',

            'annual_ctc' =>
                '4,80,000',

            'ctc_words' =>
                'Four Lakhs Eighty Thousand Only',

            /* MONTHLY */

            'basic_monthly' => '18,000',
            'hra_monthly' => '9,000',
            'cca_monthly' => '3,000',
            'statutory_bonus_monthly' => '1,500',
            'training_allowance_monthly' => '1,000',
            'special_allowance_monthly' => '5,500',
            'vpp_monthly' => '0',

            'gross_monthly' => '38,000',

            'pf_employer_monthly' => '2,160',
            'esi_employer_monthly' => '0',

            'pf_employee_monthly' => '2,160',
            'esi_employee_monthly' => '0',

            'staff_welfare_monthly' => '100',
            'prof_tax_monthly' => '208',

            'net_income_monthly' => '35,532',

            'ctc_monthly' => '40,000',

            /* ANNUAL */

            'basic_annual' => '2,16,000',
            'hra_annual' => '1,08,000',
            'cca_annual' => '36,000',
            'statutory_bonus_annual' => '18,000',
            'training_allowance_annual' => '12,000',
            'special_allowance_annual' => '66,000',
            'vpp_annual' => '0',

            'gross_annual' => '4,56,000',

            'pf_employer_annual' => '25,920',
            'esi_employer_annual' => '0',

            'pf_employee_annual' => '25,920',
            'esi_employee_annual' => '0',

            'staff_welfare_annual' => '1,200',
            'prof_tax_annual' => '2,496',

            'net_income_annual' => '4,26,384',

            'ctc_annual' => '4,80,000',
        ];

        /* ─────────────────────────────
         | MERGE DATA
         ───────────────────────────── */

        $data = array_merge(
            $settings,
            $this->getLetterSignatureData('appointment_letter'),
            compact('employee', 'appointment')
        );

        /* ─────────────────────────────
         | LOGO PATH
         ───────────────────────────── */

        if (!empty($settings['logo'])) {

            $logoPath = public_path(
                ltrim($settings['logo'], '/')
            );

            $data['logoPath'] = $logoPath;

            $data['logo'] = $settings['logo'];
        }

        /* ─────────────────────────────
         | RENDER
         ───────────────────────────── */

        $html = Blade::render(
            $template->content,
            $data
        );

        return response($html)
            ->header('Content-Type', 'text/html');
    }

    /* ─────────────────────────────────────────────
     | GENERATE PDF
     ───────────────────────────────────────────── */

    public function generatePDF(
        $templateId,
        $employee,
        $appointment
    ) {
        $template =
            AppointmentLetterTemplate::findOrFail($templateId);

        $settings = $this->getCompanySettings();

        $data = array_merge(
            $settings,
            $this->getLetterSignatureData('appointment_letter'),
            compact('employee', 'appointment')
        );

        if (!empty($settings['logo'])) {

            $logoPath = public_path(
                ltrim($settings['logo'], '/')
            );

            $data['logoPath'] = $logoPath;

            $data['logo'] = $settings['logo'];
        }

        $html = Blade::render(
            $template->content,
            $data
        );

        $pdf = PDF::loadHTML($html);

        return $pdf->download(
            'appointment-letter-' .
            $employee->firstname .
            '.pdf'
        );
    }
}
