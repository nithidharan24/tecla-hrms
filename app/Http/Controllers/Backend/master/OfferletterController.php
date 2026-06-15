<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use App\Models\OfferLetter;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfferLetterMail;

class OfferletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $offerLetters = OfferLetter::orderBy('created_at', 'desc')->paginate(10);
        return view('hrms.master.offer-letter.index', compact('offerLetters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('hrms.master.offer-letter.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            // If this template is being set as active, deactivate others
            if ($request->has('is_active')) {
                OfferLetter::where('is_active', true)->update(['is_active' => false]);
            }
            OfferLetter::create([
                'title' => $request->title,
                'subject' => $request->subject,
                'content' => $request->content,
                'is_active' => $request->has('is_active')
            ]);
            return redirect()->route('offer-letter.index')->with('success', 'Offer letter template created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating offer letter: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create offer letter template. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $offerLetter = OfferLetter::findOrFail($id);
        return view('hrms.master.offer-letter.show', compact('offerLetter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $offerLetter = OfferLetter::findOrFail($id);
        return view('hrms.master.offer-letter.edit', compact('offerLetter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $offerLetter = OfferLetter::findOrFail($id);
            // If this template is being set as active, deactivate others
            if ($request->has('is_active') && !$offerLetter->is_active) {
                OfferLetter::where('is_active', true)->update(['is_active' => false]);
            }
            $offerLetter->update([
                'title' => $request->title,
                'subject' => $request->subject,
                'content' => $request->content,
                'is_active' => $request->has('is_active')
            ]);
            return redirect()->route('offer-letter.index')->with('success', 'Offer letter template updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating offer letter: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update offer letter template. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $offerLetter = OfferLetter::findOrFail($id);
            $offerLetter->delete();
            return redirect()->route('offer-letter.index')->with('success', 'Offer letter template deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting offer letter: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete offer letter template. Please try again.');
        }
    }

    /**
     * Return common company settings (logo path etc.)
     */
    private function getCompanySettings()
    {
        // Get general settings from GeneralSetting model
        $generalSettings = GeneralSetting::first();
        
        // Get logo settings from LogoSetting model
        $logoSetting = LogoSetting::first();
        
        // Determine logo path
        $logo = null;
        
        // First priority: logo_settings table
        if ($logoSetting && !empty($logoSetting->logo)) {
            $logo = $logoSetting->logo;
        }
        
        // Verify if logo file exists in public path
        if ($logo && !file_exists(public_path($logo))) {
            // Try to find the logo in common locations
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
            'companyName' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail' => $generalSettings->contact_email ?? '',
            'companyPhone' => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->contact_address ?? '',
            'gm_name' => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title' => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo' => $logo,
        ];
    }

    /**
     * Sets the specified offer letter template as active, deactivating all others.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setActive($id)
    {
        OfferLetter::where('is_active', true)->update(['is_active' => false]);
        OfferLetter::where('id', $id)->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Template activated.');
    }

    public function setInactive($id)
    {
        OfferLetter::where('id', $id)->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Template deactivated.');
    }

    // Convert admin-safe placeholders to Blade placeholders before rendering:
    protected function prepareTemplateForRender(string $rawContent): string
    {
        // Admin editor saves placeholders as @{{ ... }} to avoid evaluation while editing.
        // Convert them back to regular Blade placeholders before rendering.
        $content = str_replace('@{{', '{{', $rawContent);
        $content = str_replace('}}', '}}', $content);
        return $content;
    }

    // Helper: render a blade string using Blade::render with provided data
    protected function renderBladeString(string $content, array $data = []): string
    {
        $content = $this->prepareTemplateForRender($content);
        $html = Blade::render($content, $data);

        return $this->injectOfferSignature($html, $data['offerSignatureDataUri'] ?? null);
    }

    // Helper: generate PDF binary from HTML
    protected function pdfFromHtml(string $html)
    {
        $pdf = PDF::loadHTML($html);
        // set paper if needed:
        $pdf->setPaper('A4', 'portrait');
        return $pdf->output();
    }

    private function getOfferSignatureData(): array
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
            ->where('letter_type', 'offer_letter')
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

    // Preview route — will show HTML in browser using dummy data
    public function preview($id)
    {
        $template = OfferLetter::findOrFail($id);
        $settings = $this->getCompanySettings();

        // Dummy employee & appointment for preview (replace as needed)
        $employee = (object)[
            'firstname' => 'Nithish',
            'lastname' => 'G',
            'email' => 'nithish@example.com',
        ];
        
        $appointment = (object)[
            'designation' => 'Website Developer',
            'joining_date' => '10/11/2025',
            'work_location' => 'Chennai',
            'annual_ctc' => '1,44,000',
            'ctc_words' => 'One Lakh Forty Four Thousand Only',
            'basic_monthly' => '12000',
            'basic_annual' => '1,44,000',
            'hra_monthly' => '2630',
            'hra_annual' => '31,560',
            'cca_monthly' => '850',
            'cca_annual' => '10,200',
            'statutory_bonus_monthly' => '260',
            'statutory_bonus_annual' => '3120',
            'training_allowance_monthly' => '0',
            'training_allowance_annual' => '0',
            'special_allowance_monthly' => '0',
            'special_allowance_annual' => '0',
            'vpp_monthly' => '0',
            'vpp_annual' => '0',
            'gross_monthly' => '12000',
            'gross_annual' => '1,44,000',
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
            'net_income_monthly' => '12000',
            'net_income_annual' => '1,44,000',
            'ctc_monthly' => '12000',
            'ctc_annual' => '1,44,000',
        ];

        // Build data for Blade render
        $data = array_merge($settings, $this->getOfferSignatureData(), compact('employee','appointment'));

        // Make logo absolute path available as $logoPath in the template
        $logoPath = null;
        if (!empty($settings['logo'])) {
            $logoPath = public_path(ltrim($settings['logo'], '/'));
            $data['logoPath'] = $logoPath;
            $data['logo'] = $settings['logo'];
        }

        $html = $this->renderBladeString($template->content, $data);

        // return rendered HTML for debugging preview
        return response($html);
    }

    // Generate PDF and return as download
    public function downloadPdf($id)
    {
        $template = OfferLetter::findOrFail($id);
        $settings = $this->getCompanySettings();

        // NOTE: replace these with real employee data from your flow
        $employee = (object)[ 'firstname' => 'Nithish', 'lastname' => 'G' ];
        $appointment = (object)[ 'designation' => 'Website Developer', 'annual_ctc' => '1,44,000' ];

        $data = array_merge($settings, $this->getOfferSignatureData(), compact('employee','appointment'));
        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
            $data['logo'] = $settings['logo'];
        }

        $html = $this->renderBladeString($template->content, $data);
        $pdfBinary = $this->pdfFromHtml($html);

        $filename = 'offer-letter-' . $employee->firstname . '-' . $employee->lastname . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'. $filename .'"'
        ]);
    }

    // Send the PDF as email to the candidate
    public function sendOfferEmail(Request $request, $id)
    {
        $template = OfferLetter::findOrFail($id);
        $settings = $this->getCompanySettings();

        // Get candidate data from request (or fetch from DB)
        $employee = (object)[
            'firstname' => $request->input('firstname', 'Candidate'),
            'lastname' => $request->input('lastname', ''),
            'email' => $request->input('email'),
        ];

        if (!$employee->email) {
            return redirect()->back()->with('error', 'Recipient email is required.');
        }

        // appointment details could be provided in request or come from your employee module
        $appointment = (object)[
            'designation' => $request->input('designation', 'Role'),
            'annual_ctc' => $request->input('annual_ctc', '0'),
            // add other fields if passed
        ];

        $data = array_merge($settings, $this->getOfferSignatureData(), compact('employee','appointment'));
        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
            $data['logo'] = $settings['logo'];
        }

        $html = $this->renderBladeString($template->content, $data);
        $pdfBinary = $this->pdfFromHtml($html);

        // Send mail with pdf attached using OfferLetterMail mailable
        Mail::to($employee->email)->send(new OfferLetterMail($employee, $appointment, $pdfBinary, $settings));

        return redirect()->back()->with('success', 'Offer letter sent to ' . $employee->email);
    }
}
