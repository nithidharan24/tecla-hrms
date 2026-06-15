<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF; // barryvdh/laravel-dompdf
use App\Mail\MemoMail;
use Illuminate\Support\Facades\Blade;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MemoController extends Controller
{
    // ─────────────────────────────────────────────
    // PRIVATE HELPERS (from Controller 1)
    // ─────────────────────────────────────────────

    private function getCompanySettings(): array
    {
        $generalSettings = GeneralSetting::first();
        $logoSetting     = LogoSetting::first();

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
            'offerSignaturePath'       => null,
            'offerSignaturePublicPath' => null,
            'offerSignatureDataUri'    => null,
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
            'offerSignaturePath'       => $signature->signature_path,
            'offerSignaturePublicPath' => $publicPath,
            'offerSignatureDataUri'    => 'data:' . $mimeType . ';base64,' . base64_encode(File::get($publicPath)),
        ];
    }

    private function renderMemoContent(string $rawContent, array $data): string
    {
        $decodedContent = html_entity_decode($rawContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content        = str_replace('@{{', '{{', $decodedContent);
        $html           = Blade::render($content, $data);

        return $this->injectMemoSignature($html, $data['offerSignatureDataUri'] ?? null);
    }

    private function injectMemoSignature(string $html, ?string $signatureDataUri): string
    {
        if (
            empty($signatureDataUri) ||
            strpos($html, 'memo-signature-img') !== false ||
            strpos($html, 'authorized-signature') !== false
        ) {
            return $html;
        }

        $signatureHtml = '<style>.dynamic-signature + .sig-name{margin-top:4px !important;}</style>'
            . '<div class="dynamic-signature" style="height:46px;margin:8px 0 0;display:flex;align-items:flex-end;">'
            . '<img class="memo-signature-img" src="' . $signatureDataUri . '" alt="Authorized Signature" style="max-height:44px;max-width:170px;object-fit:contain;display:block;">'
            . '</div>';

        $updatedHtml = preg_replace('/(<div\s+class=(["\'])sig-name\2[^>]*>)/i', $signatureHtml . '$1', $html, 1);

        return $updatedHtml ?: $html;
    }

    private function memoHasSubjectColumn(): bool
    {
        return Schema::hasColumn('memo', 'subject');
    }

    private function withMemoSubjectFallback($memo)
    {
        if ($memo instanceof \Illuminate\Support\Collection) {
            return $memo->map(fn ($item) => $this->withMemoSubjectFallback($item));
        }

        if ($memo && !property_exists($memo, 'subject')) {
            $memo->subject = $memo->name ?? 'Memo';
        }

        return $memo;
    }

    /**
     * Build a dummy employee object used for previewing templates.
     */
    private function dummyEmployee(string $companyName = 'TECLA MEDIA'): object
    {
        return (object) [
            'firstname'        => 'Jane',
            'lastname'         => 'Doe',
            'employeeid'       => 'EMP002',
            'email'            => 'jane.doe@example.com',
            'phone'            => '+1234567890',
            'joiningdate'      => '01-01-2022',
            'department_name'  => 'Sales',
            'designation_name' => 'Sales Executive',
            'profile_image'    => 'default.png',
            'branch_name'      => 'Head Office',
            'company'          => $companyName,
        ];
    }

    // ─────────────────────────────────────────────
    // SAVE SEND HISTORY  (from Controller 2)
    // ─────────────────────────────────────────────

    /**
     * Persist a record of every memo send attempt (success or failure)
     * to the memo_send_history table.
     */
    private function saveSendHistory(
        object $employee,
        object $memo,
        ?string $pdfPath,
        string $status,
        ?string $errorMessage = null
    ): void {
        try {
            DB::table('memo_send_history')->insert([
                'employee_id'           => $employee->id,
                'employee_name'         => ($employee->firstname ?? '') . ' ' . ($employee->lastname ?? ''),
                'employee_email'        => $employee->email,
                'employee_employeeid'   => $employee->employeeid    ?? null,
                'designation_name'      => $employee->designation_name ?? null,
                'department_name'       => $employee->department_name  ?? null,
                'branch_name'           => $employee->branch_name      ?? null,
                'memo_id'               => $memo->id,
                'memo_name'             => $memo->name,
                'memo_content'          => $memo->content,
                'status'                => $status,
                'error_message'         => $errorMessage,
                'pdf_path'              => $pdfPath,
                'sent_at'               => ($status === 'sent') ? now() : null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            Log::info('Memo send history saved', [
                'employee_id' => $employee->id,
                'memo_id'     => $memo->id,
                'status'      => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save memo send history: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // TEMPLATE CRUD
    // ─────────────────────────────────────────────

    public function index()
    {
        $memos = DB::table('memo')->orderBy('created_at', 'desc')->get();
        $memos = $this->withMemoSubjectFallback($memos);

        // Send-history dashboard statistics
        $sendStats = DB::table('memo_send_history')
            ->select(
                DB::raw('COUNT(*) as total_sent'),
                DB::raw('SUM(CASE WHEN status = "sent"   THEN 1 ELSE 0 END) as successful_sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_sent'),
                DB::raw('COUNT(DISTINCT employee_id) as unique_employees')
            )
            ->first();

        return view('hrms.master.memo.index', compact('memos', 'sendStats'));
    }

    public function create()
    {
        return view('hrms.master.memo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $memoData = [
            'name'       => $request->input('name'),
            'content'    => $request->input('content'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($this->memoHasSubjectColumn()) {
            $memoData['subject'] = $request->input('subject');
        }

        DB::table('memo')->insert($memoData);

        return redirect()->route('memo.index')->with('success', 'Memo template created.');
    }

    public function edit($id)
    {
        $memo = DB::table('memo')->find($id);

        if (!$memo) {
            return redirect()->route('memo.index')->with('error', 'Memo template not found.');
        }

        $memo = $this->withMemoSubjectFallback($memo);

        return view('hrms.master.memo.edit', compact('memo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $memoData = [
            'name'       => $request->name,
            'content'    => $request->content,
            'updated_at' => now(),
        ];

        if ($this->memoHasSubjectColumn()) {
            $memoData['subject'] = $request->subject;
        }

        DB::table('memo')->where('id', $id)->update($memoData);

        return redirect()->route('memo.index')->with('success', 'Memo updated successfully!');
    }

    public function destroy($id)
    {
        DB::table('memo')->where('id', $id)->delete();

        return redirect()->route('memo.index')->with('success', 'Memo deleted successfully!');
    }

    // ─────────────────────────────────────────────
    // SEND MEMO TO EMPLOYEE
    // ─────────────────────────────────────────────

    public function sendMemo($id)
    {
        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('department',  'allemployees.department',  '=', 'department.id')
            ->leftJoin('branches',    'allemployees.branch_id',   '=', 'branches.id')
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'department.department   as department_name',
                'branches.name           as branch_name'
            )
            ->where('allemployees.id', $id)
            ->first();

        if (!$employee) {
            return back()->with('error', 'Employee not found!');
        }

        $memoTemplate = DB::table('memo')->orderBy('id', 'desc')->first();

        if (!$memoTemplate) {
            return back()->with('error', 'No memo template found!');
        }

        $memoTemplate = $this->withMemoSubjectFallback($memoTemplate);

        // Guard against raw PHP in templates
        $rawContent = html_entity_decode($memoTemplate->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('/(<\?php|@php)/i', $rawContent)) {
            $this->saveSendHistory($employee, $memoTemplate, null, 'failed', 'Template contains forbidden PHP directives.');
            return back()->with('error', 'Template contains forbidden PHP directives.');
        }

        $settings = $this->getCompanySettings();

        $data = array_merge(
            $settings,
            $this->getLetterSignatureData('memo_letter'),
            [
                'employee' => $employee,
                'memo'     => $memoTemplate,
            ]
        );

        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
        }

        try {
            $renderedContent = $this->renderMemoContent($memoTemplate->content, $data);

            // Save PDF to storage
            $pdfDirectory = storage_path('app/public/memos');
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            $pdfFileName = 'memo_' . ($employee->employeeid ?? $employee->id) . '_' . time() . '.pdf';
            $storagePath = 'public/memos/' . $pdfFileName;
            $fullPdfPath = storage_path('app/' . $storagePath);

            PDF::loadHTML($renderedContent)->save($fullPdfPath);

            Mail::to($employee->email)->send(new MemoMail($employee, $memoTemplate, $fullPdfPath));

            $this->saveSendHistory($employee, $memoTemplate, $storagePath, 'sent');

            return back()->with('success', 'Memo sent to ' . ($employee->firstname ?? 'employee') . '.');

        } catch (\Exception $e) {
            $this->saveSendHistory($employee, $memoTemplate, null, 'failed', $e->getMessage());

            return back()->with('error', 'Failed to send memo: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // PREVIEW (saved template)
    // ─────────────────────────────────────────────

    public function preview($id)
    {
        try {
            $memo = DB::table('memo')->find($id);

            if (!$memo) {
                return redirect()->route('memo.index')->with('error', 'Template not found.');
            }

            $memo     = $this->withMemoSubjectFallback($memo);
            $settings = $this->getCompanySettings();
            $employee = $this->dummyEmployee($settings['companyName'] ?? 'TECLA MEDIA');

            $data = array_merge(
                $settings,
                $this->getLetterSignatureData('memo_letter'),
                compact('employee', 'memo')
            );

            if (!empty($settings['logo'])) {
                $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
            }

            $renderedContent = $this->renderMemoContent($memo->content, $data);

            return response($renderedContent)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Error previewing memo template: ' . $e->getMessage());

            return redirect()->route('memo.index')
                ->with('error', 'Failed to preview template: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // PREVIEW DRAFT (unsaved content → inline PDF)
    // ─────────────────────────────────────────────

    public function previewDraft(Request $request)
    {
        $request->validate([
            'name'    => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $settings = $this->getCompanySettings();
        $employee = $this->dummyEmployee($settings['companyName'] ?? 'TECLA MEDIA');

        $memo = (object) [
            'name'    => $request->input('name', 'Memo Template'),
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
        ];

        $data = array_merge(
            $settings,
            $this->getLetterSignatureData('memo_letter'),
            compact('employee', 'memo')
        );

        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
        }

        $renderedContent = $this->renderMemoContent($memo->content, $data);

        $pdf = PDF::loadHTML($renderedContent);

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="memo-preview.pdf"',
        ]);
    }

    // ─────────────────────────────────────────────
    // SEND HISTORY  (from Controller 2)
    // ─────────────────────────────────────────────

    public function sendHistory(Request $request)
    {
        $query = DB::table('memo_send_history')->orderBy('created_at', 'desc');

        if ($request->filled('employee_name')) {
            $query->where('employee_name', 'LIKE', '%' . $request->employee_name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sendHistory = $query->paginate(50);

        $stats = DB::table('memo_send_history')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sent"   THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->first();

        return view('hrms.master.memo.history', compact('sendHistory', 'stats'));
    }

    public function viewSendDetails($id)
    {
        $sendRecord = DB::table('memo_send_history')->where('id', $id)->first();

        if (!$sendRecord) {
            return redirect()->route('memo.history')->with('error', 'Record not found.');
        }

        return view('hrms.master.memo.send-details', compact('sendRecord'));
    }

    // ─────────────────────────────────────────────
    // RESEND FAILED MEMO  (from Controller 2)
    // ─────────────────────────────────────────────

    public function resendMemo($historyId)
    {
        $sendRecord = DB::table('memo_send_history')->where('id', $historyId)->first();

        if (!$sendRecord) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $employee = DB::table('allemployees')->where('id', $sendRecord->employee_id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.']);
        }

        $memo = DB::table('memo')->where('id', $sendRecord->memo_id)->first();

        if (!$memo) {
            return response()->json(['success' => false, 'message' => 'Memo template not found.']);
        }

        $memo     = $this->withMemoSubjectFallback($memo);
        $settings = $this->getCompanySettings();

        $data = array_merge(
            $settings,
            $this->getLetterSignatureData('memo_letter'),
            compact('employee', 'memo')
        );

        if (!empty($settings['logo'])) {
            $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
        }

        try {
            $renderedContent = $this->renderMemoContent($memo->content, $data);

            $pdfDirectory = storage_path('app/public/memos');
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            $pdfFileName = 'memo_' . ($employee->employeeid ?? $employee->id) . '_' . time() . '.pdf';
            $storagePath = 'public/memos/' . $pdfFileName;
            $fullPdfPath = storage_path('app/' . $storagePath);

            PDF::loadHTML($renderedContent)->save($fullPdfPath);

            Mail::to($employee->email)->send(new MemoMail($employee, $memo, $fullPdfPath));

            DB::table('memo_send_history')
                ->where('id', $historyId)
                ->update([
                    'status'        => 'sent',
                    'error_message' => null,
                    'pdf_path'      => $storagePath,
                    'sent_at'       => now(),
                    'updated_at'    => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Memo resent successfully.']);

        } catch (\Exception $e) {
            DB::table('memo_send_history')
                ->where('id', $historyId)
                ->update([
                    'error_message' => $e->getMessage(),
                    'updated_at'    => now(),
                ]);

            return response()->json(['success' => false, 'message' => 'Failed to resend: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────
    // DELETE SEND HISTORY RECORD  (from Controller 2)
    // ─────────────────────────────────────────────

    public function deleteSendHistory($id)
    {
        try {
            $record = DB::table('memo_send_history')->where('id', $id)->first();

            // Remove the stored PDF if it still exists
            if ($record && $record->pdf_path) {
                $fullPath = storage_path('app/' . $record->pdf_path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            DB::table('memo_send_history')->where('id', $id)->delete();

            return redirect()->route('memo.history')->with('success', 'Record deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('memo.history')
                ->with('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // STATISTICS  (from Controller 2)
    // ─────────────────────────────────────────────

    /**
     * Returns last-30-days daily send statistics as JSON
     * (for dashboard charts).
     */
    public function getStatistics()
    {
        $stats = DB::table('memo_send_history')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sent"   THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'DESC')
            ->get();

        return response()->json($stats);
    }
}