<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PayslipReleaseMail;
use App\Helpers\PayrollActivityLogger;
use Carbon\Carbon;
use PDF;
use App\Models\PayrollTemplate;
use Illuminate\Support\Facades\Blade;

class SalaryReleaseController extends Controller
{
    /**
     * Department-wise salary release listing (with filters).
     */
    public function index(Request $request)
    {
        $month        = (int) $request->input('month', date('m'));
        $year         = (int) $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');
        $status       = $request->input('status'); // sent | pending | null

        $base = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->whereYear('monthly_payslips.payroll_month', $year)
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->where('allemployees.deleted_at', 0);

        if ($departmentId === 'unassigned') {
            $base->whereNull('allemployees.department');
        } elseif (!empty($departmentId)) {
            $base->where('allemployees.department', $departmentId);
        }

        if ($status === 'sent') {
            $base->where('monthly_payslips.email_sent', 1);
        } elseif ($status === 'pending') {
            $base->where(function ($q) {
                $q->where('monthly_payslips.email_sent', 0)
                  ->orWhereNull('monthly_payslips.email_sent');
            });
        }

        $departments = (clone $base)
            ->select(
                'department.id as department_id',
                DB::raw("COALESCE(department.department, 'Unassigned') as department_name"),
                DB::raw('COUNT(monthly_payslips.id) as total_payslips'),
                DB::raw('SUM(monthly_payslips.net_salary) as total_net'),
                DB::raw('SUM(CASE WHEN monthly_payslips.email_sent = 1 THEN 1 ELSE 0 END) as sent_count'),
                DB::raw('SUM(CASE WHEN monthly_payslips.email_sent = 1 THEN 0 ELSE 1 END) as pending_count')
            )
            ->groupBy('department.id', 'department.department')
            ->orderBy('department.department')
            ->get();

        $departmentList = DB::table('department')
            ->where('deleted_at', 0)
            ->select('id', 'department')
            ->orderBy('department')
            ->get();

        $summary = [
            'total_departments' => $departments->count(),
            'total_payslips'    => $departments->sum('total_payslips'),
            'total_net'         => $departments->sum('total_net'),
            'sent_count'        => $departments->sum('sent_count'),
            'pending_count'     => $departments->sum('pending_count'),
        ];

        return view('hrms.hr.PayRoll.SalaryRelease.index', compact(
            'departments', 'departmentList', 'summary', 'month', 'year', 'departmentId', 'status'
        ));
    }

    /**
     * View detail of a single department (employee-wise) for individual release.
     */
    public function show(Request $request, $departmentId)
    {
        $month = (int) $request->input('month', date('m'));
        $year  = (int) $request->input('year', date('Y'));

        $department = $departmentId === 'unassigned'
            ? (object)['id' => null, 'department' => 'Unassigned']
            : DB::table('department')->where('id', $departmentId)->first();

        if (!$department) {
            return redirect()->route('salary-release.index')->with('error', 'Department not found.');
        }

        $query = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->whereYear('monthly_payslips.payroll_month', $year)
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->where('allemployees.deleted_at', 0);

        if ($departmentId === 'unassigned') {
            $query->whereNull('allemployees.department');
        } else {
            $query->where('allemployees.department', $departmentId);
        }

        $payslips = $query->select(
            'monthly_payslips.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.employeeid',
            'allemployees.email',
            'designation.designation as designation_name'
        )
        ->orderBy('allemployees.firstname')
        ->get();

        // Decode itemized additions/deductions for breakdown display
        foreach ($payslips as $p) {
            $add = json_decode($p->additions_data  ?? '[]', true);
            $ded = json_decode($p->deductions_data ?? '[]', true);
            $p->additions_items  = $add['items'] ?? [];
            $p->deductions_items = $ded['items'] ?? [];
        }

        return view('hrms.hr.PayRoll.SalaryRelease.show', compact(
            'department', 'payslips', 'month', 'year', 'departmentId'
        ));
    }

    /**
     * Release (email payslip) to ALL employees of a department for the month.
     */
    public function releaseDepartment(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2000|max:2100',
        ]);

        $month        = (int) $request->month;
        $year         = (int) $request->year;
        $departmentId = $request->department_id;

        $query = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->whereYear('monthly_payslips.payroll_month', $year)
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->where('allemployees.deleted_at', 0)
            ->select('monthly_payslips.id');

        if ($departmentId === 'unassigned') {
            $query->whereNull('allemployees.department');
        } elseif (!empty($departmentId)) {
            $query->where('allemployees.department', $departmentId);
        }

        $payslipIds = $query->pluck('id');

        $sent = 0; $failed = 0; $skipped = 0;

        foreach ($payslipIds as $pid) {
            $result = $this->sendSinglePayslip($pid);
            if (!empty($result['success'])) {
                $sent++;
            } elseif (($result['reason'] ?? '') === 'no_email') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        $msg = "Payslips released — Sent: {$sent}, Failed: {$failed}, Skipped (no email): {$skipped}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sent'    => $sent,
                'failed'  => $failed,
                'skipped' => $skipped,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Release (email payslip) to a SINGLE employee.
     */
    public function releaseEmployee(Request $request)
    {
        $request->validate([
            'payslip_id' => 'required|integer|exists:monthly_payslips,id',
        ]);

        $result = $this->sendSinglePayslip($request->payslip_id);

        if (!empty($result['success'])) {
            $msg = 'Payslip emailed successfully.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        }

        $reasonText = ($result['reason'] ?? '') === 'no_email'
            ? 'Employee has no email address.'
            : ($result['reason'] ?? 'Unknown error');

        $msg = 'Failed to send payslip: ' . $reasonText;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $msg], 422);
        }
        return redirect()->back()->with('error', $msg);
    }

    // =========================================================================
    //  Core send logic
    // =========================================================================

    private function getCompanySettings(): array
    {
        $general     = DB::table('general_settings')->first();
        $logoSetting = DB::table('logo_settings')->first();
        $logo        = $logoSetting->logo ?? null;

        $logoPath = null;
        if (!empty($logo)) {
            $full = public_path(ltrim($logo, '/'));
            if (file_exists($full)) {
                $logoPath = $full;
            }
        }

        return [
            'name'     => $general->site_name        ?? 'TECLA MEDIA',
            'email'    => $general->contact_email     ?? '',
            'phone'    => $general->contact_phone     ?? '',
            'address'  => $general->contact_address   ?? ($general->address ?? ''),
            'logoPath' => $logoPath,
        ];
    }

    /** Indian-system number to words (Crore / Lakh / Thousand). */
    private function numberToWords($number): string
    {
        $number = (int) $number;
        if ($number === 0) return 'Zero Rupees Only';

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $two = function ($n) use ($ones, $tens) {
            if ($n < 20) return $ones[$n];
            return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
        };
        $three = function ($n) use ($ones, $two) {
            $str = '';
            if ($n >= 100) { $str .= $ones[intdiv($n, 100)] . ' Hundred '; $n %= 100; }
            if ($n > 0)    $str .= $two($n);
            return trim($str);
        };

        $crore    = intdiv($number, 10000000); $number %= 10000000;
        $lakh     = intdiv($number, 100000);   $number %= 100000;
        $thousand = intdiv($number, 1000);     $number %= 1000;
        $hundred  = $number;

        $words = '';
        if ($crore)    $words .= $three($crore) . ' Crore ';
        if ($lakh)     $words .= $two($lakh) . ' Lakh ';
        if ($thousand) $words .= $two($thousand) . ' Thousand ';
        if ($hundred)  $words .= $three($hundred);

        return trim($words) . ' Rupees Only';
    }

    private function sendSinglePayslip($payslipId): array
    {
        $payslip = DB::table('monthly_payslips')->where('id', $payslipId)->first();
        if (!$payslip) {
            return ['success' => false, 'reason' => 'payslip_not_found'];
        }
     
        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('department',  'allemployees.department',  '=', 'department.id')
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'department.department as department_name'
            )
            ->where('allemployees.id', $payslip->employee_id)
            ->where('allemployees.deleted_at', 0)
            ->first();
     
        if (!$employee || empty($employee->email)) {
            return ['success' => false, 'reason' => 'no_email'];
        }
     
        try {
            $data = $this->buildPayslipData($payslip, $employee);
     
            // ── Render HTML: PayrollTemplate first, fallback to Blade view ──
            $template = \App\Models\PayrollTemplate::orderBy('created_at', 'desc')->first();
     
            if ($template) {
                $html = $this->renderPayslipFromTemplate($template, $payslip, $employee, $data);
            } else {
                $html = view('hrms.hr.PayRoll.SalaryRelease.payslip_pdf', $data)->render();
            }
     
            // ── Save PDF ──
            $pdfDir = storage_path('app/public/payslips');
            if (!file_exists($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
     
            $monthLabel     = Carbon::parse($payslip->payroll_month)->format('F_Y');
            $pdfFileName    = 'payslip-' . ($employee->employeeid ?? $employee->id) . '-' . $monthLabel . '-' . time() . '.pdf';
            $pdfStoragePath = 'public/payslips/' . $pdfFileName;
            $fullPath       = storage_path('app/' . $pdfStoragePath);
     
            $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
            $pdf->save($fullPath);
            $pdfContent = file_get_contents($fullPath);
     
            // ── Send email ──
            $monthName = Carbon::parse($payslip->payroll_month)->format('F Y');
            $subject   = 'Payslip for ' . $monthName . ' - ' . $employee->firstname . ' ' . $employee->lastname;
            $body      = "Dear " . $employee->firstname . " " . $employee->lastname . ",\n\n"
                       . "Please find attached your payslip for " . $monthName . ".\n\n"
                       . "Net Pay: Rs. " . number_format($payslip->net_salary, 2) . "\n\n"
                       . "For any queries, please contact the HR department.\n\n"
                       . "Best regards,\nHR Department\n"
                       . config('app.name', 'HRMS');
     
            Mail::to($employee->email)->send(
                new PayslipReleaseMail($subject, $body, $pdfContent, $pdfFileName, $employee, $payslip, $data['company'], $monthName)
            );
     
            DB::table('monthly_payslips')->where('id', $payslip->id)->update([
                'status'        => 'sent',
                'email_sent'    => 1,
                'email_sent_at' => now(),
                'pdf_path'      => $pdfStoragePath,
                'updated_at'    => now(),
            ]);
     
            PayrollActivityLogger::logEmail(
                'Salary Release',
                "Payslip released to {$employee->firstname} {$employee->lastname} ({$employee->employeeid}) for {$monthName} - Net: Rs." . number_format($payslip->net_salary, 2),
                $employee->id
            );
     
            return ['success' => true];
     
        } catch (\Exception $e) {
            Log::error('Payslip release failed: ' . $e->getMessage(), [
                'payslip_id' => $payslipId,
                'trace'      => $e->getTraceAsString(),
            ]);
     
            DB::table('monthly_payslips')->where('id', $payslipId)->update([
                'status'     => 'failed',
                'updated_at' => now(),
            ]);
     
            return ['success' => false, 'reason' => $e->getMessage()];
        }
    }
     
    // ─────────────────────────────────────────────────────────────
    // renderPayslipFromTemplate()
    // Converts stored DB data into the variables PayrollTemplate expects.
    // ─────────────────────────────────────────────────────────────
    private function renderPayslipFromTemplate($template, $payslip, $employee, array $data): string
    {
        $company = $data['company'];
     
        $payslipView = (object) [
            'employeeid'             => $employee->employeeid,
            'firstname'              => $employee->firstname,
            'lastname'               => $employee->lastname,
            'designation_name'       => $employee->designation_name ?? '',
            'joiningdate'            => $employee->joiningdate ?? '',
            'actual_working_days'    => $payslip->actual_working_days ?? 0,
            'total_working_days'     => $payslip->total_working_days  ?? 0,
            'payroll_month'          => $payslip->payroll_month,
            'payroll_month_formatted'=> $data['salaryMonth'],
            'basic_salary'           => $payslip->basic_salary   ?? 0,
            'hra'                    => $payslip->hra             ?? 0,
            'conveyance'             => $payslip->conveyance      ?? 0,
            'da'                     => $payslip->da              ?? 0,
            'medical'                => $payslip->medical         ?? 0,
            'allowance'              => $payslip->allowance       ?? 0,
            'welfare'                => $payslip->welfare         ?? 0,
            'overtime_amount'        => $payslip->overtime_amount ?? 0,
            'dynamic_additions'      => $payslip->dynamic_additions  ?? 0,
            'dynamic_deductions'     => $payslip->dynamic_deductions ?? 0,
            'pf'                     => $payslip->pf              ?? 0,
            'esi'                    => $payslip->esi             ?? 0,
            'tax'                    => $payslip->tax             ?? 0,
            'tds'                    => $payslip->tds             ?? 0,
            'lop_deduction'          => $payslip->lop_deduction   ?? 0,
            'lop_days'               => $payslip->lop_days        ?? 0,
            'total_earnings'         => $data['totalEarnings'],
            'total_deductions'       => $data['totalDeductions'],
            'net_salary'             => $data['netSalary'],
        ];
     
        // Itemized dynamic additions/deductions (expense reimbursement + "Add Addition")
        $addData = json_decode($payslip->additions_data  ?? '[]', true);
        $dedData = json_decode($payslip->deductions_data ?? '[]', true);
        $dynamicAdditionItems  = $addData['items'] ?? [];
        $dynamicDeductionItems = $dedData['items'] ?? [];

        // Bank info (correct table: employee_bank_informations)
        $bankInfoRow = DB::table('employee_bank_informations')
            ->where('employee_id', $employee->id)
            ->select('pan_no', 'bank_name', 'bank_account_no', 'ifsc_code')
            ->first();
     
        // Fallback: try matching on employeeid string
        if (!$bankInfoRow) {
            $bankInfoRow = DB::table('employee_bank_informations')
                ->where('employee_id', $employee->employeeid)
                ->select('pan_no', 'bank_name', 'bank_account_no', 'ifsc_code')
                ->first();
        }
     
        $bankInfo = [
            'pan_no'          => $bankInfoRow->pan_no           ?? '',
            'bank_name'       => $bankInfoRow->bank_name        ?? '',
            'bank_account_no' => $bankInfoRow->bank_account_no  ?? '',
            'ifsc_code'       => $bankInfoRow->ifsc_code        ?? '',
        ];
     
        $lopDays            = $payslip->lop_days       ?? 0;
        $lopDeductionAmount = $payslip->lop_deduction  ?? 0;
        $perDaySalary       = ($payslip->total_working_days ?? 0) > 0
                                ? (($payslip->basic_salary ?? 0) / $payslip->total_working_days)
                                : 0;
     
        $numberToWords = function ($num) use (&$numberToWords) {
            $ones  = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                      'Seventeen', 'Eighteen', 'Nineteen'];
            $tens  = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            $num   = intval($num);
            if ($num == 0)     return 'Zero';
            if ($num < 10)     return $ones[$num];
            if ($num < 20)     return $teens[$num - 10];
            if ($num < 100)    return $tens[intdiv($num, 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
            if ($num < 1000)   return $ones[intdiv($num, 100)] . ' Hundred'
                                      . ($num % 100 ? ' ' . $numberToWords($num % 100) : '');
            if ($num < 100000) return $numberToWords(intdiv($num, 1000)) . ' Thousand'
                                      . ($num % 1000 ? ' ' . $numberToWords($num % 1000) : '');
            return 'Amount';
        };
     
        $company_name    = $company['name'];
        $company_email   = $company['email'];
        $company_phone   = $company['phone'];
        $company_address = $company['address'];
        $logoPath        = $company['logoPath'];
        $company_logo    = null;

        return \Illuminate\Support\Facades\Blade::render(
            $template->content,
            [
                'payslip'               => $payslipView,
                'bankInfo'              => $bankInfo,
                'lopDays'               => $lopDays,
                'lopDeductionAmount'    => $lopDeductionAmount,
                'perDaySalary'          => $perDaySalary,
                'numberToWords'         => $numberToWords,
                'company_name'          => $company_name,
                'company_email'         => $company_email,
                'company_phone'         => $company_phone,
                'company_address'       => $company_address,
                'company_logo'          => $company_logo,
                'logoPath'              => $logoPath,
                'employee'              => $employee,
                'dynamicAdditionItems'  => $dynamicAdditionItems,
                'dynamicDeductionItems' => $dynamicDeductionItems,
            ]
        );
    }
     
    // ─────────────────────────────────────────────────────────────
    // buildPayslipData()
    // ─────────────────────────────────────────────────────────────
    private function buildPayslipData($payslip, $employee): array
    {
        // Expense reimbursement: read from dynamic_additions (set by processSalary),
        // else fall back to querying expenses directly (legacy payslips).
        $expenseReimbursement = (float) ($payslip->dynamic_additions ?? 0);
     
        if ($expenseReimbursement == 0) {
            $payrollCarbon = Carbon::parse($payslip->payroll_month);
            $expenseReimbursement = (float) DB::table('expenses')
                ->where('employee_id', $employee->employeeid)
                ->where('status', 'approved')
                ->whereMonth('departure_date', $payrollCarbon->month)
                ->whereYear('departure_date', $payrollCarbon->year)
                ->sum('expense_amount');
        }
     
        $earnings = [
            ['label' => 'Basic Salary',    'amount' => (float) ($payslip->basic_salary ?? 0)],
            ['label' => 'HRA',             'amount' => (float) ($payslip->hra          ?? 0)],
            ['label' => 'DA',              'amount' => (float) ($payslip->da           ?? 0)],
            ['label' => 'Conveyance',      'amount' => (float) ($payslip->conveyance   ?? 0)],
            ['label' => 'Other Allowance', 'amount' => (float) ($payslip->allowance    ?? 0)],
            ['label' => 'Medical',         'amount' => (float) ($payslip->medical      ?? 0)],
            ['label' => 'Staff Welfare',   'amount' => (float) ($payslip->welfare      ?? 0)],
            ['label' => 'Overtime',        'amount' => (float) ($payslip->overtime_amount ?? 0)],
        ];
     
        // Dynamic additions (expense reimbursement + "Add Addition" items).
        // Prefer the itemized list stored on the payslip; fall back to the single sum.
        $addData       = json_decode($payslip->additions_data ?? '[]', true);
        $additionItems = $addData['items'] ?? [];

        if (!empty($additionItems)) {
            foreach ($additionItems as $item) {
                $amt = (float) ($item['amount'] ?? 0);
                if ($amt == 0) continue;
                $earnings[] = ['label' => $item['name'] ?? 'Addition', 'amount' => $amt];
            }
        } elseif ($expenseReimbursement > 0) {
            $earnings[] = ['label' => 'Expense Reimbursement', 'amount' => $expenseReimbursement];
        }
     
        $deductions = [
            ['label' => 'PF',               'amount' => (float) ($payslip->pf          ?? 0)],
            ['label' => 'ESI',              'amount' => (float) ($payslip->esi         ?? 0)],
            ['label' => 'TDS',              'amount' => (float) ($payslip->tds         ?? 0)],
            ['label' => 'Professional Tax', 'amount' => (float) ($payslip->tax         ?? 0)],
            ['label' => 'LOP Deduction',    'amount' => (float) ($payslip->lop_deduction ?? 0)],
            ['label' => 'Employee Leave',   'amount' => (float) ($payslip->employee_leave_deduction ?? 0)],
        ];
     
        // Strip zero rows
        $earnings   = array_values(array_filter($earnings,   fn ($e) => $e['amount'] != 0));
        $deductions = array_values(array_filter($deductions, fn ($d) => $d['amount'] != 0));
     
        $totalEarnings   = (float) ($payslip->total_earnings   ?? array_sum(array_column($earnings,   'amount')));
        $totalDeductions = (float) ($payslip->total_deductions ?? array_sum(array_column($deductions, 'amount')));
        $netSalary       = (float) ($payslip->net_salary       ?? ($totalEarnings - $totalDeductions));
     
        return [
            'employee'             => $employee,
            'payslip'              => $payslip,
            'earnings'             => $earnings,
            'deductions'           => $deductions,
            'totalEarnings'        => $totalEarnings,
            'totalDeductions'      => $totalDeductions,
            'netSalary'            => $netSalary,
            'netInWords'           => $this->numberToWords((int) round($netSalary)),
            'salaryMonth'          => Carbon::parse($payslip->payroll_month)->format('F Y'),
            'expenseReimbursement' => $expenseReimbursement,
            'company'              => $this->getCompanySettings(),
        ];
    }

    /**
     * Release (email payslip) to MULTIPLE selected employees in one attempt.
     */
    public function releaseSelected(Request $request)
    {
        $request->validate([
            'payslip_ids'   => 'required|array|min:1',
            'payslip_ids.*' => 'integer|exists:monthly_payslips,id',
        ]);

        $sent = 0; $failed = 0; $skipped = 0;

        foreach ($request->payslip_ids as $pid) {
            $result = $this->sendSinglePayslip($pid);
            if (!empty($result['success'])) {
                $sent++;
            } elseif (($result['reason'] ?? '') === 'no_email') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        $msg = "Selected payslips released — Sent: {$sent}, Failed: {$failed}, Skipped (no email): {$skipped}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(compact('sent', 'failed', 'skipped') + ['success' => true, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }
}