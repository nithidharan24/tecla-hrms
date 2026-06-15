<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionLetterTemplate;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;

class PromotionLetterController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  HELPER — exact copy of OfferletterController::getCompanySettings()
    // ─────────────────────────────────────────────────────────────
    private function getCompanySettings(): array
    {
        // Get general settings
        $generalSettings = GeneralSetting::first();

        // Get logo settings
        $logoSetting = LogoSetting::first();

        // Determine logo path
        $logo = null;

        // First priority: logo_settings table
        if ($logoSetting && !empty($logoSetting->logo)) {
            $logo = $logoSetting->logo;
        }

        // Verify if logo file exists in public path
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

            // If still not found, set to null
            if (!file_exists(public_path($logo))) {
                $logo = null;
            }
        }

        return [
            'companyName'    => $generalSettings->site_name      ?? 'TECLA MEDIA',
            'companyEmail'   => $generalSettings->contact_email  ?? '',
            'companyPhone'   => $generalSettings->contact_phone  ?? '',
            'companyAddress' => $generalSettings->contact_address ?? '',
            'gm_name'        => $generalSettings->gm_name        ?? 'A.SURENDER',
            'gm_title'       => $generalSettings->gm_title       ?? 'GENERAL MANAGER',
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

    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $templates = PromotionLetterTemplate::orderBy('created_at', 'desc')->get();
        return view('hrms.master.promotion-letter.index', compact('templates'));
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        return view('hrms.master.promotion-letter.create');
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:promotion_letter_templates,name',
            'content' => 'required|string',
        ]);

        PromotionLetterTemplate::create($validated);

        return redirect()
            ->route('promotion-letter.index')
            ->with('success', 'Promotion letter template created successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────────────────────────
    public function edit(PromotionLetterTemplate $promotionLetter)
    {
        return view('hrms.master.promotion-letter.edit', compact('promotionLetter'));
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, PromotionLetterTemplate $promotionLetter)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:promotion_letter_templates,name,' . $promotionLetter->id,
            'content' => 'required|string',
        ]);

        $promotionLetter->update($validated);

        return redirect()
            ->route('promotion-letter.index')
            ->with('success', 'Promotion letter template updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(PromotionLetterTemplate $promotionLetter)
    {
        $promotionLetter->delete();

        return redirect()
            ->route('promotion-letter.index')
            ->with('success', 'Promotion letter template deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    //  PREVIEW — mirrors OfferletterController::preview() exactly
    // ─────────────────────────────────────────────────────────────
    public function preview($id)
    {
        $template = PromotionLetterTemplate::findOrFail($id);
        $settings = $this->getCompanySettings();

        // Dummy employee for preview
        $employee = (object)[
            'firstname'       => 'John',
            'lastname'        => 'Doe',
            'employeeid'      => 'EMP001',
            'email'           => 'john.doe@example.com',
            'department_name' => 'Engineering',
        ];

        // Dummy promotion for preview
        $promotion = (object)[
            'promotion_date' => '2025-08-01',
        ];

        $oldDesignationName = 'Junior Developer';
        $newDesignationName = 'Mid-Level Developer';

        // Build data for Blade render
        $data = array_merge($settings, $this->getLetterSignatureData('promotion_letter'), compact(
            'employee',
            'promotion',
            'oldDesignationName',
            'newDesignationName'
        ));

        // Make logo absolute path available as $logoPath in the template
        // — identical to how OfferletterController does it
        $logoPath = null;
        if (!empty($settings['logo'])) {
            $logoPath = public_path(ltrim($settings['logo'], '/'));
            $data['logoPath'] = $logoPath;
            $data['logo']     = $settings['logo'];
        }

        $html = Blade::render($template->content, $data);

        return response($html)->header('Content-Type', 'text/html');
    }

    // ─────────────────────────────────────────────────────────────
    //  GENERATE PDF
    // ─────────────────────────────────────────────────────────────
    public function generatePdf($templateId, $employeeId, $promotionId)
    {
        $template = PromotionLetterTemplate::findOrFail($templateId);
        $settings = $this->getCompanySettings();

        // Replace with your real models:
        // $employee           = \App\Models\Employee::findOrFail($employeeId);
        // $promotion          = \App\Models\Promotion::findOrFail($promotionId);
        // $oldDesignationName = \App\Models\Designation::find($promotion->old_designation_id)?->name ?? '';
        // $newDesignationName = \App\Models\Designation::find($promotion->new_designation_id)?->name ?? '';

        $data = array_merge($settings, $this->getLetterSignatureData('promotion_letter'), compact(
            'employee',
            'promotion',
            'oldDesignationName',
            'newDesignationName'
        ));

        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
            $data['logo']     = $settings['logo'];
        }

        $html     = Blade::render($template->content, $data);
        $pdf      = PDF::loadHTML($html)->setPaper('A4', 'portrait');
        $filename = 'promotion_letter_' . ($employee->employeeid ?? $employeeId) . '.pdf';

        return $pdf->download($filename);
    }
}
