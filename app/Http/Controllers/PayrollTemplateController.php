<?php

namespace App\Http\Controllers;

use App\Models\PayrollTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use PDF;

class PayrollTemplateController extends Controller
{
    private function getCompanySettings()
    {
        $generalSettings = DB::table('general_settings')->first();
        $logoSetting = DB::table('logo_settings')->first();

        return [
            'company_name' => $generalSettings->site_name ?? 'TECLA MEDIA',
            'company_email' => $generalSettings->contact_email ?? 'info@tecla.in',
            'company_phone' => $generalSettings->contact_phone ?? '',
            'company_address' => $generalSettings->address ?? 'Chennai – 600073.',
            'company_logo' => $logoSetting->logo ?? 'uploads/media_6896d63471f63.png',
        ];
    }

    /**
     * Dummy itemized additions/deductions used for preview + sample PDF
     * so templates that loop over them render correctly.
     */
    private function sampleAdditionItems(): array
    {
        return [
            ['name' => 'Expense Reimbursement', 'category' => 'Reimbursement', 'amount' => 3000],
            ['name' => 'Testing', 'category' => 'Monthly remuneration', 'amount' => 5000],
        ];
    }

    public function index()
    {
        $templates = PayrollTemplate::orderBy('created_at', 'desc')->get();
        return view('payroll_templates.index', compact('templates'));
    }

    public function create()
    {
        $companySettings = $this->getCompanySettings();
        return view('payroll_templates.create', compact('companySettings'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:payroll_templates,name',
            'content' => 'required|string',
        ]);

        PayrollTemplate::create($validatedData);
        return redirect()->route('payroll-template.index')->with('success', 'Payroll template created successfully.');
    }

    public function edit($id)
    {
        $payrollTemplate = PayrollTemplate::findOrFail($id);
        $companySettings = $this->getCompanySettings();
        return view('payroll_templates.edit', compact('payrollTemplate', 'companySettings'));
    }

    public function update(Request $request, $id)
    {
        $payrollTemplate = PayrollTemplate::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:payroll_templates,name,' . $payrollTemplate->id,
            'content' => 'required|string',
        ]);
        $payrollTemplate->update($validatedData);
        return redirect()->route('payroll-template.index')->with('success', 'Payroll template updated successfully.');
    }

    public function destroy($id)
    {
        PayrollTemplate::findOrFail($id)->delete();
        return redirect()->route('payroll-template.index')->with('success', 'Payroll template deleted successfully.');
    }

    public function preview($id)
    {
        $template = PayrollTemplate::findOrFail($id);
        $companySettings = $this->getCompanySettings();
        extract($companySettings);

        // Dummy payslip data for preview
        $payslip = (object)[
            'employeeid' => 'EMP001',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'designation_name' => 'Senior Developer',
            'joiningdate' => '2022-01-15',
            'actual_working_days' => 28,
            'total_working_days' => 30,
            'payroll_month' => '2025-02',
            'payroll_month_formatted' => 'February 2025',
            'basic_salary' => 50000,
            'hra' => 10000,
            'conveyance' => 2000,
            'da' => 5000,
            'medical' => 1500,
            'allowance' => 3000,
            'overtime_amount' => 0,
            'dynamic_additions' => 8000,
            'dynamic_deductions' => 0,
            'pf' => 6000,
            'esi' => 800,
            'tax' => 0,
            'welfare' => 500,
            'tds' => 0,
            'total_earnings' => 79500,
            'total_deductions' => 7300,
            'net_salary' => 72200,
            'additions_data' => json_encode([
                'items' => $this->sampleAdditionItems(),
                'total' => 8000,
            ]),
            'deductions_data' => json_encode(['items' => [], 'total' => 0]),
        ];

        $bankInfo = [
            'pan_no' => 'ABCDE1234F',
            'bank_name' => 'FEDERAL BANK LTD',
            'bank_account_no' => '17420200005636',
            'ifsc_code' => 'FDRL0001742',
        ];

        // LOP calculation
        $employeeDbId = $payslip->employee_id ?? null;
        $payrollMonthForLop = '2025-02';
        
        $lopRecord = DB::table('employee_lop_records')
            ->where('employee_id', $employeeDbId)
            ->where('month', $payrollMonthForLop)
            ->first();

        $lopDays = $lopRecord ? $lopRecord->lop_days : 0;
        $perDaySalary = $payslip->total_working_days > 0 ? ($payslip->basic_salary / $payslip->total_working_days) : 0;
        $lopDeductionAmount = $lopDays * $perDaySalary;

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

        // Fetch company logo
        $logoPath = null;
        if (!empty($company_logo)) {
            $cleanLogo = ltrim($company_logo, '/');
            $logoPath = public_path($cleanLogo);
        }

        // Itemized additions/deductions for templates that loop over them
        $dynamicAdditionItems  = $this->sampleAdditionItems();
        $dynamicDeductionItems = [];

        $html = Blade::render($template->content, compact(
            'payslip', 'bankInfo', 'lopDays', 'lopDeductionAmount', 'numberToWords',
            'company_name', 'company_email', 'company_phone', 'company_address', 'company_logo', 'logoPath',
            'dynamicAdditionItems', 'dynamicDeductionItems'
        ));

        return response($html);
    }

    public function generatePDF($templateId)
    {
        $template = PayrollTemplate::findOrFail($templateId);
        $companySettings = $this->getCompanySettings();
        extract($companySettings);

        $payslip = (object)[
            'employeeid' => 'EMP001',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'designation_name' => 'Senior Developer',
            'joiningdate' => '2022-01-15',
            'actual_working_days' => 28,
            'total_working_days' => 30,
            'payroll_month' => '2025-02',
            'payroll_month_formatted' => 'February 2025',
            'basic_salary' => 50000,
            'hra' => 10000,
            'conveyance' => 2000,
            'da' => 5000,
            'medical' => 1500,
            'allowance' => 3000,
            'overtime_amount' => 0,
            'dynamic_additions' => 8000,
            'dynamic_deductions' => 0,
            'pf' => 6000,
            'esi' => 800,
            'tax' => 0,
            'welfare' => 500,
            'tds' => 0,
            'total_earnings' => 79500,
            'total_deductions' => 7300,
            'net_salary' => 72200,
            'additions_data' => json_encode([
                'items' => $this->sampleAdditionItems(),
                'total' => 8000,
            ]),
            'deductions_data' => json_encode(['items' => [], 'total' => 0]),
        ];

        $bankInfo = [
            'pan_no' => 'ABCDE1234F',
            'bank_name' => 'FEDERAL BANK LTD',
            'bank_account_no' => '17420200005636',
            'ifsc_code' => 'FDRL0001742',
        ];

        $lopDays = 0;
        $lopDeductionAmount = 0;

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

        $logoPath = null;
        if (!empty($company_logo)) {
            $cleanLogo = ltrim($company_logo, '/');
            $logoPath = public_path($cleanLogo);
        }

        // Itemized additions/deductions for templates that loop over them
        $dynamicAdditionItems  = $this->sampleAdditionItems();
        $dynamicDeductionItems = [];

        $html = Blade::render($template->content, compact(
            'payslip', 'bankInfo', 'lopDays', 'lopDeductionAmount', 'numberToWords',
            'company_name', 'company_email', 'company_phone', 'company_address', 'company_logo', 'logoPath',
            'dynamicAdditionItems', 'dynamicDeductionItems'
        ));

        $pdf = PDF::loadHTML($html);
        return $pdf->download('Payslip.pdf');
    }
}