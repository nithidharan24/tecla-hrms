<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MonthlyPayslipMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payslip;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct($payslip, $pdfPath)
    {
        $this->payslip = $payslip;
        $this->pdfPath = $pdfPath;
        
        // Log the payslip data for debugging
        Log::info('MonthlyPayslipMail created', [
            'employee_id' => $this->payslip->employeeid ?? 'N/A',
            'email' => $this->payslip->email ?? 'N/A',
            'month' => $this->payslip->payroll_month_formatted ?? 'N/A',
            'pdf_exists' => file_exists($this->pdfPath)
        ]);
    }

    /**
     * Get company settings from database
     */
    private function getCompanySettings()
    {
        $generalSettings = DB::table('general_settings')->first();
        $logoSettings = DB::table('logo_settings')->first();
        
        return [
            'company_name' => $generalSettings->site_name ?? 'Your Company Name',
            'company_address' => $generalSettings->contact_address ?? 'Your Company Address',
            'company_phone' => $generalSettings->contact_phone ?? 'N/A',
            'company_email' => $generalSettings->contact_email ?? 'info@yourcompany.com',
            'company_logo' => $logoSettings->logo ?? null,
        ];
    }

    /**
     * Get employee bank information
     */
    private function getEmployeeBankInfo($employeeId)
    {
        $bankInfo = DB::table('employee_bank_informations')
            ->where('employee_id', $employeeId)
            ->first();

        return [
            'bank_name' => $bankInfo->bank_name ?? 'Not Applicable',
            'bank_account_no' => $bankInfo->bank_account_no ?? 'Not Applicable',
            'ifsc_code' => $bankInfo->ifsc_code ?? 'Not Applicable',
            'pan_no' => $bankInfo->pan_no ?? 'Not Applicable',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companySettings = $this->getCompanySettings();
        
        return new Envelope(
            subject: 'Monthly Payslip - ' . ($this->payslip->payroll_month_formatted ?? 'N/A') . ' - ' . ($this->payslip->employeeid ?? 'N/A') . ' - ' . $companySettings['company_name'],
        );
    }

    /**
     * Calculate LOP deduction amount
     */
    private function calculateLopDeduction($employeeId, $payrollMonth, $basicSalary, $totalWorkingDays)
    {
        // Get LOP days from employee_lop_records table
        $lopRecord = DB::table('employee_lop_records')
            ->where('employee_id', $employeeId)
            ->where('month', $payrollMonth)
            ->first();

        $lopDays = $lopRecord ? $lopRecord->lop_days : 0;
        
        // Calculate per day salary based on basic salary
        $perDaySalary = $totalWorkingDays > 0 ? ($basicSalary / $totalWorkingDays) : 0;
        
        // Calculate LOP deduction amount
        $lopDeductionAmount = $lopDays * $perDaySalary;

        return [
            'lop_days' => $lopDays,
            'lop_deduction_amount' => $lopDeductionAmount
        ];
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get company settings
        $companySettings = $this->getCompanySettings();
        
        // Get employee ID for bank info and LOP calculation
        $employeeDbId = $this->payslip->employee_id ?? $this->payslip->id ?? null;
        
        // Get employee bank information
        $bankInfo = $this->getEmployeeBankInfo($employeeDbId);
        
        // Safely extract and format all data with null checks
        $employeeName = trim(($this->payslip->firstname ?? '') . ' ' . ($this->payslip->lastname ?? ''));
        $employeeId = $this->payslip->employeeid ?? 'N/A';
        $month = $this->payslip->payroll_month_formatted ?? 'N/A';
        
        // Extract payroll month in YYYY-MM format for LOP lookup
        $payrollMonthForLop = date('Y-m', strtotime($this->payslip->payroll_month ?? now()));
        
        // Calculate LOP deduction
        $lopData = $this->calculateLopDeduction(
            $employeeDbId, 
            $payrollMonthForLop, 
            $this->payslip->basic_salary ?? 0,
            $this->payslip->total_working_days ?? 0
        );
        
        // Safely convert numeric values with proper null handling
        $netSalary = $this->safeNumberFormat($this->payslip->net_salary ?? 0);
        $totalEarnings = $this->safeNumberFormat($this->payslip->total_earnings ?? 0);
        $totalDeductions = $this->safeNumberFormat($this->payslip->total_deductions ?? 0);
        $basicSalary = $this->safeNumberFormat($this->payslip->basic_salary ?? 0);
        $hra = $this->safeNumberFormat($this->payslip->hra ?? 0);
        $da = $this->safeNumberFormat($this->payslip->da ?? 0);
        $conveyance = $this->safeNumberFormat($this->payslip->conveyance ?? 0);
        $allowance = $this->safeNumberFormat($this->payslip->allowance ?? 0);
        $medical = $this->safeNumberFormat($this->payslip->medical ?? 0);
        $welfare = $this->safeNumberFormat($this->payslip->welfare ?? 0);
        $overtimeAmount = $this->safeNumberFormat($this->payslip->overtime_amount ?? 0);
        $dynamicAdditions = $this->safeNumberFormat($this->payslip->dynamic_additions ?? 0);
        
        // Deductions
        $pf = $this->safeNumberFormat($this->payslip->pf ?? 0);
        $esi = $this->safeNumberFormat($this->payslip->esi ?? 0);
        $tds = $this->safeNumberFormat($this->payslip->tds ?? 0);
        $tax = $this->safeNumberFormat($this->payslip->tax ?? 0);
        $dynamicDeductions = $this->safeNumberFormat($this->payslip->dynamic_deductions ?? 0);
        
        // LOP deduction
        $lopDeductionAmount = $this->safeNumberFormat($lopData['lop_deduction_amount']);
        $lopDays = $lopData['lop_days'];
        
        // Working days and attendance
        $actualWorkingDays = (int) ($this->payslip->actual_working_days ?? 0);
        $totalWorkingDays = (int) ($this->payslip->total_working_days ?? 0);
        $workingDays = $actualWorkingDays . '/' . $totalWorkingDays;
        
        // Hours and other numeric data
        $totalHoursWorked = $this->safeNumberFormat($this->payslip->total_hours_worked ?? 0, 1);
        $overtimeHours = $this->safeNumberFormat($this->payslip->overtime_hours ?? 0, 1);
        $leaveDays = (int) ($this->payslip->leave_days_taken ?? 0);
        $lateArrivals = (int) ($this->payslip->late_arrivals ?? 0);
        $earlyDepartures = (int) ($this->payslip->early_departures ?? 0);

        return new Content(
            view: 'emails.monthly-payslip',
            with: [
                'payslip' => $this->payslip,
                'employeeName' => $employeeName,
                'employeeId' => $employeeId,
                'month' => $month,
                'netSalary' => $netSalary,
                'totalEarnings' => $totalEarnings,
                'totalDeductions' => $totalDeductions,
                'workingDays' => $workingDays,
                'totalHoursWorked' => $totalHoursWorked,
                'overtimeHours' => $overtimeHours,
                'leaveDays' => $leaveDays,
                'lateArrivals' => $lateArrivals,
                'earlyDepartures' => $earlyDepartures,
                
                // Earnings breakdown
                'basicSalary' => $basicSalary,
                'hra' => $hra,
                'da' => $da,
                'conveyance' => $conveyance,
                'allowance' => $allowance,
                'medical' => $medical,
                'welfare' => $welfare,
                'overtimeAmount' => $overtimeAmount,
                'dynamicAdditions' => $dynamicAdditions,
                
                // Deductions breakdown
                'pf' => $pf,
                'esi' => $esi,
                'tds' => $tds,
                'tax' => $tax,
                'dynamicDeductions' => $dynamicDeductions,
                
                // LOP deduction data
                'lopDeductionAmount' => $lopDeductionAmount,
                'lopDays' => $lopDays,
                
                // Company settings
                'companySettings' => $companySettings,
                
                // Bank information
                'bankInfo' => $bankInfo,
                
                // Additional info
                'designationName' => $this->payslip->designation_name ?? 'N/A',
                'actualWorkingDays' => $actualWorkingDays,
                'totalWorkingDays' => $totalWorkingDays,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $fileName = "payslip_{$this->payslip->employeeid}_{$this->payslip->payroll_month_formatted}.pdf";
        $fileName = str_replace([' ', ',', '/', '\\'], ['_', '', '_', '_'], $fileName);
        
        // Check if PDF file exists
        if (!file_exists($this->pdfPath)) {
            Log::error('PDF file not found for email attachment', [
                'pdf_path' => $this->pdfPath,
                'employee_id' => $this->payslip->employeeid ?? 'N/A'
            ]);
            return [];
        }
        
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($fileName)
                ->withMime('application/pdf'),
        ];
    }

    /**
     * Safely format numbers with null checking
     */
    private function safeNumberFormat($value, $decimals = 2)
    {
        // Handle null, empty string, or non-numeric values
        if (is_null($value) || $value === '' || !is_numeric($value)) {
            return number_format(0, $decimals);
        }
        
        // Convert to float and format
        return number_format((float) $value, $decimals);
    }

    /**
     * Handle failed jobs
     */
    public function failed(\Throwable $exception)
    {
        Log::error('MonthlyPayslipMail job failed', [
            'employee_id' => $this->payslip->employeeid ?? 'N/A',
            'email' => $this->payslip->email ?? 'N/A',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}