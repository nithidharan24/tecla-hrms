<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDF;

class InvoiceTemplateController extends Controller
{
    private function getCompanySettings()
    {
        $generalSettings = DB::table('general_settings')->first();
        $logoSetting = DB::table('logo_settings')->first();
        $logo = $logoSetting->logo ?? 'uploads/media_6896d63471f63.png';
        $logoPath = $this->resolvePublicPath($logo);
        $logoDataUri = null;

        if ($logoPath) {
            $mimeType = mime_content_type($logoPath) ?: 'image/png';
            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        return [
            'companyName' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail' => $generalSettings->contact_email ?? 'info@tecla.in',
            'companyPhone' => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->address ?? 'Chennai – 600073.',
            'gst' => $generalSettings->gst ?? '33BMDPG3443D1ZO',
            'pan' => $generalSettings->pan ?? '',
            'gstin' => $generalSettings->gstin ?? '33AOLPG3921M1ZV',
            'bankName' => $generalSettings->bank_name ?? 'FEDERAL BANK LTD',
            'accountName' => $generalSettings->account_name ?? 'TECLA MEDIA',
            'accountNumber' => $generalSettings->account_number ?? '17420200005636',
            'ifscCode' => $generalSettings->ifsc_code ?? 'FDRL0001742',
            'logo' => $logo,
            'logoPath' => $logoPath,
            'logoDataUri' => $logoDataUri,
        ];
    }

    private function resolvePublicPath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $cleanPath = ltrim($path, '/');
        $possiblePaths = [
            $cleanPath,
            'uploads/' . $cleanPath,
            'storage/' . $cleanPath,
            'images/' . $cleanPath,
            'assets/images/' . $cleanPath,
            'logo/' . $cleanPath,
        ];

        foreach (array_unique($possiblePaths) as $possiblePath) {
            $publicPath = public_path($possiblePath);

            if (file_exists($publicPath)) {
                return $publicPath;
            }
        }

        return null;
    }

    private function renderInvoiceTemplate(string $content, array $data): string
    {
        $html = Blade::render($content, $data);
        $html = $this->injectInvoiceSignature($html, $data['offerSignatureDataUri'] ?? null);

        return $this->injectInvoiceLogo($html, $data['logoDataUri'] ?? null, $data['companyName'] ?? 'Company');
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

    private function injectInvoiceSignature(string $html, ?string $signatureDataUri): string
    {
        if (
            empty($signatureDataUri) ||
            strpos($html, 'invoice-signature-img') !== false ||
            strpos($html, 'authorized-signature') !== false
        ) {
            return $html;
        }

        $signatureHtml = '<div class="authorized-signature" style="height:46px;margin:8px 0 4px;display:flex;justify-content:flex-end;align-items:flex-end;">'
            . '<img class="invoice-signature-img" src="' . $signatureDataUri . '" alt="Authorized Signature" style="max-height:44px;max-width:170px;width:auto;height:auto;object-fit:contain;display:block;">'
            . '</div>';

        $updatedHtml = preg_replace('/(<div\s+class=(["\'])signature-line\2[^>]*>)/i', $signatureHtml . '$1', $html, 1);

        return $updatedHtml ?: $html;
    }

    private function injectInvoiceLogo(string $html, ?string $logoDataUri, string $companyName): string
    {
        if (empty($logoDataUri)) {
            return $html;
        }

        $logoHtml = '<div class="logo-area" style="text-align:center;margin-bottom:14px;">'
            . '<img class="invoice-logo-img" src="' . $logoDataUri . '" alt="' . e($companyName) . ' Logo" '
            . 'style="max-height:58px;max-width:180px;width:auto;height:auto;object-fit:contain;display:inline-block;">'
            . '</div>';

        $updatedHtml = preg_replace(
            '/<div\s+class=(["\'])logo-area\1[^>]*>\s*<div\s+class=(["\'])logo-name\2[^>]*>.*?<\/div>\s*<div\s+class=(["\'])logo-sub\3[^>]*>.*?<\/div>\s*<\/div>/is',
            $logoHtml,
            $html,
            1
        );

        if (!$updatedHtml || $updatedHtml === $html) {
            $updatedHtml = preg_replace(
                '/<div\s+class=(["\'])logo-area\1[^>]*>\s*<img\b[^>]*>\s*<\/div>/is',
                $logoHtml,
                $html,
                1
            );
        }

        return $updatedHtml ?: $html;
    }

    public function index()
    {
        $templates = InvoiceTemplate::orderBy('created_at', 'desc')->get();
        return view('invoice_templates.index', compact('templates'));
    }

    public function create()
    {
        $settings = $this->getCompanySettings();
        return view('invoice_templates.create', $settings);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:invoice_templates,name',
            'content' => 'required|string',
        ]);

        InvoiceTemplate::create($validatedData);
        return redirect()->route('invoice-template.index')->with('success', 'Invoice template created successfully.');
    }

    public function edit($id)
    {
        $invoiceTemplate = InvoiceTemplate::findOrFail($id);
        $settings = $this->getCompanySettings();
        return view('invoice_templates.edit', compact('invoiceTemplate') + $settings);
    }

    public function update(Request $request, $id)
    {
        $invoiceTemplate = InvoiceTemplate::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:invoice_templates,name,' . $invoiceTemplate->id,
            'content' => 'required|string',
        ]);
        $invoiceTemplate->update($validatedData);
        return redirect()->route('invoice-template.index')->with('success', 'Invoice template updated successfully.');
    }

    public function destroy($id)
    {
        InvoiceTemplate::findOrFail($id)->delete();
        return redirect()->route('invoice-template.index')->with('success', 'Invoice template deleted successfully.');
    }

     public function preview($id)
    {
        $template = InvoiceTemplate::findOrFail($id);
        $settings = $this->getCompanySettings();
        extract($settings);

        // Dummy invoice data for preview
        $invoice = (object)[
            'invoice_number' => '2425/20',
            'invoice_date' => '22/02/2025',
            'due_date' => '24/02/2025',
            'period' => 'February 2025',
        ];

        $customer = (object)[
            'name' => 'Sakuram',
            'address' => 'Kovilambakkam, Chennai-96',
            'email' => 'customer@example.com',
            'phone' => '+91 98765 43210',
            'gst' => 'GST Registration Number',
        ];

        $items = [
            (object)[
                'description' => 'Hosting and Maintenance service Renewal',
                'quantity' => 1,
                'rate' => 8400.00,
                'amount' => 8400.00,
            ],
            (object)[
                'description' => 'Domain Renewal',
                'quantity' => 1,
                'rate' => 750.00,
                'amount' => 750.00,
            ],
        ];

        $subtotal = 9150.00;
        $taxRate = 18; // IGST percentage
        $taxAmount = ($subtotal * $taxRate) / 100;
        $grandTotal = $subtotal + $taxAmount;

        $numberToWords = function($num) use (&$numberToWords) {
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            
            $num = intval($num);
            if ($num == 0) return 'Zero';
            
            if ($num < 10) return $ones[$num];
            if ($num < 20) return $teens[$num - 10];
            if ($num < 100) return $tens[intval($num / 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
            if ($num < 1000) return $ones[intval($num / 100)] . ' Hundred' . ($num % 100 ? ' ' . $numberToWords($num % 100) : '');
            if ($num < 100000) return $numberToWords(intval($num / 1000)) . ' Thousand' . ($num % 1000 ? ' ' . $numberToWords($num % 1000) : '');
            
            return 'Amount';
        };

        $grandTotalWords = $numberToWords($grandTotal) . ' Only';

        $data = array_merge($this->getLetterSignatureData('invoice_template'), compact(
            'invoice', 'customer', 'items', 'subtotal', 'taxRate', 'taxAmount', 'grandTotal', 'grandTotalWords',
            'companyName', 'companyEmail', 'companyPhone', 'companyAddress', 'gst', 'pan', 'gstin',
            'bankName', 'accountName', 'accountNumber', 'ifscCode', 'logo', 'logoPath', 'logoDataUri'
        ));

        $html = $this->renderInvoiceTemplate($template->content, $data);

        return response($html);
    }

    public function generatePDF($templateId)
    {
        // Similar to preview but returns PDF
        $template = InvoiceTemplate::findOrFail($templateId);
        $settings = $this->getCompanySettings();
        extract($settings);

        $invoice = (object)[
            'invoice_number' => '2425/20',
            'invoice_date' => '22/02/2025',
            'due_date' => '24/02/2025',
            'period' => 'February 2025',
        ];

        $customer = (object)[
            'name' => 'Sakuram',
            'address' => 'Kovilambakkam, Chennai-96',
            'email' => 'customer@example.com',
            'phone' => '+91 98765 43210',
            'gst' => 'GST Registration Number',
        ];

        $items = [
            (object)['description' => 'Hosting and Maintenance service Renewal', 'quantity' => 1, 'rate' => 8400.00, 'amount' => 8400.00],
            (object)['description' => 'Domain Renewal', 'quantity' => 1, 'rate' => 750.00, 'amount' => 750.00],
        ];

        $subtotal = 9150.00;
        $taxRate = 18;
        $taxAmount = ($subtotal * $taxRate) / 100;
        $grandTotal = $subtotal + $taxAmount;
        $grandTotalWords = 'Ten Thousand Seven Hundred and Ninety Seven Only';

        $data = array_merge($this->getLetterSignatureData('invoice_template'), compact(
            'invoice', 'customer', 'items', 'subtotal', 'taxRate', 'taxAmount', 'grandTotal', 'grandTotalWords',
            'companyName', 'companyEmail', 'companyPhone', 'companyAddress', 'gst', 'pan', 'gstin',
            'bankName', 'accountName', 'accountNumber', 'ifscCode', 'logo', 'logoPath', 'logoDataUri'
        ));

        $html = $this->renderInvoiceTemplate($template->content, $data);

        $pdf = PDF::loadHTML($html);
        return $pdf->download('Invoice.pdf');
    }
}
