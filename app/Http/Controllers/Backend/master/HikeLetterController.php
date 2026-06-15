<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HikeLetterTemplate;
use Illuminate\Support\Facades\Blade;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;

class HikeLetterController extends Controller
{
    public function index()
    {
        $templates = HikeLetterTemplate::orderBy('created_at', 'desc')->get();
        return view('hrms.master.hike-letter.index', compact('templates'));
    }

    public function create()
    {
        return view('hrms.master.hike-letter.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:hike_letter_templates,name',
            'content' => 'required|string',
        ]);

        HikeLetterTemplate::create($validatedData);

        return redirect()->route('hike-letter.index')->with('success', 'Hike letter template created successfully.');
    }

    public function show($id)
    {
        $hikeLetter = HikeLetterTemplate::findOrFail($id);
        return view('hrms.master.hike-letter.show', compact('hikeLetter'));
    }

    public function edit($id)
    {
        $hikeLetter = HikeLetterTemplate::findOrFail($id);
        return view('hrms.master.hike-letter.edit', compact('hikeLetter'));
    }

    public function update(Request $request, $id)
    {
        $hikeLetter = HikeLetterTemplate::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:hike_letter_templates,name,' . $hikeLetter->id,
            'content' => 'required|string',
        ]);

        $hikeLetter->update($validatedData);

        return redirect()->route('hike-letter.index')->with('success', 'Hike letter template updated successfully.');
    }

    public function destroy($id)
    {
        $hikeLetter = HikeLetterTemplate::findOrFail($id);
        $hikeLetter->delete();
        
        return redirect()->route('hike-letter.index')->with('success', 'Hike letter template deleted successfully.');
    }

    public function preview($id)
    {
        $template = HikeLetterTemplate::findOrFail($id);

        // Dummy data for preview only
        $employee = (object)[
            'firstname' => 'Nitish',
            'lastname' => 'K',
            'employeeid' => 'EMP001',
        ];
        
        $hike = (object)[
            'effective_date' => '01st February 2024',
            'new_ctc' => '3,60,000',
            'new_ctc_words' => 'Three Lakhs Sixty Thousand Only',
            'designation' => 'Senior Web Developer',
            'basic_monthly' => '13,260',
            'basic_annual' => '5,59,120',
            'hra_monthly' => '7,630',
            'hra_annual' => '91,560',
            'cca_monthly' => '4,350',
            'cca_annual' => '52,200',
            'statutory_bonus_monthly' => '1,890',
            'statutory_bonus_annual' => '22,680',
            'training_allowance_monthly' => '0',
            'training_allowance_annual' => '0',
            'special_allowance_monthly' => '2,870',
            'special_allowance_annual' => '34,440',
            'vpp_monthly' => '0',
            'vpp_annual' => '0',
            'gross_monthly' => '30,000',
            'old_ctc' => '5,40,000',
            'gross_annual' => '7,60,000',
            'pf_employer_monthly' => '0',
            'pf_employer_annual' => '0',
            'esi_employer_monthly' => '0',
            'esi_employer_annual' => '0',
            'pf_employee_monthly' => '0',
            'pf_employee_annual' => '0',
            'esi_employee_monthly' => '0',
            'esi_employee_annual' => '0',
            'staff_welfare_monthly' => '0',
            'staff_welfare_annual' => '0',
            'prof_tax_monthly' => '0',
            'prof_tax_annual' => '0',
            'net_income_monthly' => '30,000',
            'net_income_annual' => '7,60,000',
            'ctc_monthly' => '30,000',
            'ctc_annual' => '7,60,000',
        ];

       $settings = $this->getCompanySettings();

$data = array_merge(
    $settings,
    $this->getLetterSignatureData('hike_letter'),
    compact('employee', 'hike')
);

if (!empty($settings['logo'])) {

    $logoPath = public_path(ltrim($settings['logo'], '/'));

    $data['logoPath'] = $logoPath;
    $data['logo'] = $settings['logo'];
}

$html = Blade::render($template->content, $data);
        return response($html);
    }
private function getCompanySettings(): array
{
    $generalSettings = GeneralSetting::first();

    $logoSetting = LogoSetting::first();

    $logo = null;

    if ($logoSetting && !empty($logoSetting->logo)) {
        $logo = $logoSetting->logo;
    }

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
        'companyName'    => $generalSettings->site_name ?? 'TECLA MEDIA',
        'companyEmail'   => $generalSettings->contact_email ?? '',
        'companyPhone'   => $generalSettings->contact_phone ?? '',
        'companyAddress' => $generalSettings->contact_address ?? '',
        'gm_name'        => $generalSettings->gm_name ?? 'A.SURENDER',
        'gm_title'       => $generalSettings->gm_title ?? 'GENERAL MANAGER',
        'logo'           => $logo,
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
    /**
     * Generate PDF for hike letter with actual employee and hike data
     * This method should be called from your hike management module with real data
     * 
     * @param int $templateId - The template ID to use
     * @param object $employee - Employee object with firstname, lastname, etc.
     * @param object $hike - Hike object with all salary details
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($templateId, $employee, $hike)
    {
        $template = HikeLetterTemplate::findOrFail($templateId);

        // Render the template with actual employee and hike data
       $settings = $this->getCompanySettings();

$data = array_merge(
    $settings,
    $this->getLetterSignatureData('hike_letter'),
    compact('employee', 'hike')
);

if (!empty($settings['logo'])) {

    $logoPath = public_path(ltrim($settings['logo'], '/'));

    $data['logoPath'] = $logoPath;
    $data['logo'] = $settings['logo'];
}

$html = Blade::render($template->content, $data);
        $pdf = PDF::loadHTML($html);
        
        return $pdf->download('hike-letter-' . $employee->firstname . '-' . $employee->lastname . '.pdf');
    }
}
