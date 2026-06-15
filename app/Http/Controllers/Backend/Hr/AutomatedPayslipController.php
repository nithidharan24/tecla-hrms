<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\MonthlyPayslipMail;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

class AutomatedPayslipController extends Controller
{
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
     * Display all generated payslips
     */
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');

        $query = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'monthly_payslips.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.email',
                'designation.designation as designation_name',
                DB::raw("DATE_FORMAT(monthly_payslips.payroll_month, '%M %Y') as payroll_month_formatted")
            )
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->whereYear('monthly_payslips.payroll_month', $year);

        if ($employeeId) {
            $query->where('monthly_payslips.employee_id', $employeeId);
        }

        $payslips = $query->orderBy('monthly_payslips.created_at', 'desc')->get();

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('firstname')
            ->get();

        // Calculate summary statistics
        $summaryStats = $this->calculateSummaryStats($month, $year);

        return view('hrms.payroll.automated-payslip.index', compact(
            'payslips', 'employees', 'month', 'year', 'summaryStats'
        ));
    }

    /**
     * Generate monthly payslips for all employees
     */
    public function generateMonthlyPayslips($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        $payrollMonth = Carbon::create($year, $month, 1);
        
        Log::info("Starting payslip generation for {$payrollMonth->format('F Y')}");

        if (!Schema::hasColumn('employee_salaries', 'approval_status') ||
            !Schema::hasColumn('employee_salaries', 'release_status')) {
            return [
                'success' => false,
                'generated_count' => 0,
                'errors' => ['Salary approval/release status columns are missing. Please run migrations.'],
                'message' => 'Salary approval/release status columns are missing. Please run migrations.'
            ];
        }
        
        // Get all employees who have salary records
        $employeesWithSalary = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->where('employee_salaries.approval_status', 'approved')
            ->where('employee_salaries.release_status', 'released')
            ->select(
                'employee_salaries.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'allemployees.joiningdate',
                'allemployees.profile_image',
                'designation.designation as designation_name'
            )
            ->get();

        if ($employeesWithSalary->isEmpty()) {
            return [
                'success' => true,
                'generated_count' => 0,
                'errors' => [],
                'message' => "No released salary records found for {$payrollMonth->format('F Y')}"
            ];
        }

        $generatedCount = 0;
        $errors = [];

        foreach ($employeesWithSalary as $employeeSalary) {
            try {
                // Check if payslip already exists for this month
                $existingPayslip = DB::table('monthly_payslips')
                    ->where('employee_id', $employeeSalary->employee_id)
                    ->whereMonth('payroll_month', $month)
                    ->whereYear('payroll_month', $year)
                    ->first();

                if ($existingPayslip) {
                    Log::info("Payslip already exists for employee {$employeeSalary->employeeid}");
                    continue; // Skip if already generated
                }

                // Calculate comprehensive payslip data
                $payslipData = $this->calculateComprehensivePayslip(
                    $employeeSalary, 
                    $month, 
                    $year
                );
                
                if ($payslipData) {
                    // Store payslip record
                    $payslipId = $this->storeMonthlyPayslip($payslipData, $payrollMonth);
                    
                    if ($payslipId) {
                        // Generate PDF
                        $pdfPath = $this->generatePayslipPDF($payslipId);
                        
                        // Send email with attachment
                        if ($employeeSalary->email) {
                            $this->sendPayslipEmail($payslipId, $pdfPath);
                        }
                        
                        $generatedCount++;
                        Log::info("Generated payslip for employee {$employeeSalary->employeeid}");
                    }
                }
            } catch (Exception $e) {
                $errorMsg = "Error generating payslip for {$employeeSalary->firstname} {$employeeSalary->lastname}: " . $e->getMessage();
                $errors[] = $errorMsg;
                Log::error($errorMsg);
                
                // Mark as failed in database
                DB::table('monthly_payslips')->updateOrInsert(
                    [
                        'employee_id' => $employeeSalary->employee_id,
                        'payroll_month' => $payrollMonth
                    ],
                    [
                        'status' => 'failed',
                        'updated_at' => now()
                    ]
                );
            }
        }

        Log::info("Payslip generation completed. Generated: {$generatedCount}, Errors: " . count($errors));

        return [
            'success' => true,
            'generated_count' => $generatedCount,
            'errors' => $errors,
            'message' => "Generated {$generatedCount} payslips for {$payrollMonth->format('F Y')}"
        ];
    }

    /**
     * Calculate comprehensive payslip using existing salary structure + dynamic data
     */
    private function calculateComprehensivePayslip($employeeSalary, $month, $year)
    {
        Log::debug('Employee Salary Object:', [
            'object' => (array)$employeeSalary,
            'hra_value' => $employeeSalary->hra ?? 'NULL',
            'da_value' => $employeeSalary->da ?? 'NULL'
        ]);

        $basic = (float) $employeeSalary->basic;
        
        // Clean and convert percentage values to numeric
        $hraPercent = $this->cleanPercentageValue($employeeSalary->hra);
        $daPercent = $this->cleanPercentageValue($employeeSalary->da);
        
        $hraAmount = round($basic * $hraPercent / 100, 2);
        $daAmount = round($basic * $daPercent / 100, 2);

        // Base salary components from employee_salaries table
        $baseSalaryData = [
            'basic' => $basic,
            'hra' => $hraAmount,
            'da' => $daAmount,
            'conveyance' => (float) ($employeeSalary->conveyance ?? 0),
            'allowance' => (float) ($employeeSalary->allowance ?? 0),
            'medical' => (float) ($employeeSalary->medical ?? 0),
            'welfare' => (float) ($employeeSalary->welfare ?? 0),
            'tds' => (float) ($employeeSalary->tds ?? 0),
            'pf' => (float) ($employeeSalary->pf ?? 0),
            'esi' => (float) ($employeeSalary->esi ?? 0),
            'tax' => (float) ($employeeSalary->tax ?? 0),
        ];

        // Get dynamic data for the month
        $attendanceData = $this->getAttendanceData($employeeSalary->employee_id, $month, $year);
        $leaveData = $this->getLeaveData($employeeSalary->employee_id, $month, $year);
        $overtimeData = $this->getOvertimeData($employeeSalary->employee_id, $month, $year);
        $dynamicAdditions = $this->getDynamicAdditions($employeeSalary->employee_id, $month, $year);
        $dynamicDeductions = $this->getDynamicDeductions($employeeSalary->employee_id, $month, $year);
        
        // Get LOP data for the month
        $lopData = $this->getLopData($employeeSalary->employee_id, $month, $year);

        // Calculate working days adjustments
        $totalWorkingDays = $this->getWorkingDaysInMonth($month, $year);
        $actualWorkingDays = $attendanceData['working_days'];
        
        // Calculate pro-rated salary if employee didn't work full month
        $workingDaysRatio = $totalWorkingDays > 0 ? $actualWorkingDays / $totalWorkingDays : 1;
        
        // Adjust basic components based on working days (if less than full month)
        $adjustedBasic = $baseSalaryData['basic'];
        $adjustedHra = $baseSalaryData['hra'];
        $adjustedDa = $baseSalaryData['da'];
        
        // Calculate total earnings
        $totalEarnings = $adjustedBasic + $adjustedHra + $adjustedDa +
                         $baseSalaryData['conveyance'] + $baseSalaryData['allowance'] +
                         $baseSalaryData['medical'] + $baseSalaryData['welfare'] +
                        $overtimeData['total_amount'] + $dynamicAdditions['total'];

        // Calculate total deductions (including LOP deduction)
        $totalDeductions = $baseSalaryData['tds'] + $baseSalaryData['pf'] +
                           $baseSalaryData['esi'] + $baseSalaryData['tax'] +
                           $lopData['lop_deduction_amount'] + $dynamicDeductions['total'];

        // Calculate net salary
        $netSalary = $totalEarnings - $totalDeductions;

        return [
            'employee_id' => $employeeSalary->employee_id,
            'employee_data' => $employeeSalary,
            'base_salary_data' => $baseSalaryData,
            'adjusted_basic' => $adjustedBasic,
            'adjusted_hra' => $adjustedHra,
            'adjusted_da' => $adjustedDa,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'total_working_days' => $totalWorkingDays,
            'actual_working_days' => $actualWorkingDays,
            'working_days_ratio' => $workingDaysRatio,
            'attendance_data' => $attendanceData,
            'leave_data' => $leaveData,
            'overtime_data' => $overtimeData,
            'dynamic_additions' => $dynamicAdditions,
            'dynamic_deductions' => $dynamicDeductions,
            'lop_data' => $lopData,
            'salary_month' => Carbon::create($year, $month, 1)->format('F, Y')
        ];
    }

    /**
     * Get LOP (Loss of Pay) data for the month
     */
    private function getLopData($employeeId, $month, $year)
    {
        $payrollMonth = sprintf('%04d-%02d', $year, $month);
        
        // Get LOP record from employee_lop_records table
        $lopRecord = DB::table('employee_lop_records')
            ->where('employee_id', $employeeId)
            ->where('month', $payrollMonth)
            ->first();

        $lopDays = $lopRecord ? $lopRecord->lop_days : 0;
        
        // Calculate LOP deduction amount
        $lopDeductionAmount = 0;
        if ($lopDays > 0) {
            // Get employee's basic salary for calculation
            $employeeSalary = DB::table('employee_salaries')
                ->where('employee_id', $employeeId)
                ->first();
            
            if ($employeeSalary) {
                $basicSalary = (float) $employeeSalary->basic;
                $totalWorkingDays = $this->getWorkingDaysInMonth($month, $year);
                $perDaySalary = $totalWorkingDays > 0 ? ($basicSalary / $totalWorkingDays) : 0;
                $lopDeductionAmount = $lopDays * $perDaySalary;
            }
        }

        return [
            'lop_days' => $lopDays,
            'lop_deduction_amount' => $lopDeductionAmount,
            'lop_record' => $lopRecord
        ];
    }

    /**
     * Clean percentage values and convert to numeric
     */
    private function cleanPercentageValue($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        // Remove % symbol and any whitespace
        $cleaned = str_replace(['%', ' '], '', $value);
        
        // Convert to float
        return (float) $cleaned;
    }

    /**
     * Get attendance data for the month
     */
    private function getAttendanceData($employeeId, $month, $year)
    {
        $attendanceRecords = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('punch_out')
            ->get();

        $totalHours = 0;
        $workingDays = 0;
        $lateArrivals = 0;
        $earlyDepartures = 0;

        foreach ($attendanceRecords as $record) {
            $totalHours += $record->working_hours ?? 0;
            $workingDays++;
            
            if (isset($record->status)) {
                if ($record->status === 'late') {
                    $lateArrivals++;
                }
                if ($record->status === 'early_departure') {
                    $earlyDepartures++;
                }
            }
        }

        return [
            'total_hours' => $totalHours,
            'working_days' => $workingDays,
            'late_arrivals' => $lateArrivals,
            'early_departures' => $earlyDepartures,
            'records' => $attendanceRecords
        ];
    }

    /**
     * Get leave data for the month
     */
    private function getLeaveData($employeeId, $month, $year)
    {
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function($query) use ($month, $year) {
                $query->whereMonth('from_date', $month)
                      ->whereYear('from_date', $year)
                      ->orWhereMonth('to_date', $month)
                      ->whereYear('to_date', $year);
            })
            ->get();

        $totalLeaveDays = 0;
        $unpaidLeaveDays = 0;
        $leaveTypes = [];

        foreach ($leaves as $leave) {
            $leaveDays = $leave->no_of_days ?? 0;
            $totalLeaveDays += $leaveDays;
            
            // Determine if leave is paid or unpaid
            $isPaidLeave = in_array($leave->leave_type, [
                'Annual Leave', 'Sick Leave', 'Casual Leave', 'Medical Leave'
            ]);
            
            if (!$isPaidLeave) {
                $unpaidLeaveDays += $leaveDays;
            }
            
            $leaveTypes[] = [
                'type' => $leave->leave_type,
                'days' => $leaveDays,
                'from_date' => $leave->from_date,
                'to_date' => $leave->to_date,
                'is_paid' => $isPaidLeave
            ];
        }

        return [
            'total_leave_days' => $totalLeaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'leave_types' => $leaveTypes
        ];
    }

    /**
     * Get overtime data for the month
     */
    private function getOvertimeData($employeeId, $month, $year)
    {
        $overtimeRecords = DB::table('employee_overtime')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereMonth('overtime_date', $month)
            ->whereYear('overtime_date', $year)
            ->get();

        $totalHours = 0;
        $totalAmount = 0;

        foreach ($overtimeRecords as $record) {
            $totalHours += $record->overtime_hours ?? 0;
            $totalAmount += $record->overtime_amount ?? 0;
        }

        return [
            'total_hours' => $totalHours,
            'total_amount' => $totalAmount,
            'records' => $overtimeRecords
        ];
    }

    /**
     * Get dynamic additions for the month
     */
    private function getDynamicAdditions($employeeId, $month, $year)
    {
        // Get from additions table (your existing system)
        $additions = DB::table('additions')
            ->where(function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                      ->orWhere('employee_id', 'LIKE', "%{$employeeId}%")
                      ->orWhereRaw("FIND_IN_SET(?, employee_id)", [$employeeId]);
            })
            ->get();

        // Also get from employee_additions table (individual assignments)
        $employeeAdditions = DB::table('employee_additions')
            ->leftJoin('additions', 'employee_additions.addition_id', '=', 'additions.id')
            ->where('employee_additions.employee_id', $employeeId)
            ->where('employee_additions.is_active', 1)
            ->where('employee_additions.effective_from', '<=', "{$year}-{$month}-01")
            ->where(function($query) use ($year, $month) {
                $query->whereNull('employee_additions.effective_to')
                      ->orWhere('employee_additions.effective_to', '>=', "{$year}-{$month}-01");
            })
            ->select(
                'employee_additions.*',
                'additions.name',
                'additions.category'
            )
            ->get();

        $items = [];
        $total = 0;

        // Process general additions
        foreach ($additions as $addition) {
            $amount = (float) ($addition->unit_amount ?? 0);
            $items[] = [
                'name' => $addition->name,
                'category' => $addition->category,
                'amount' => $amount,
                'type' => 'general'
            ];
            $total += $amount;
        }

        // Process individual additions
        foreach ($employeeAdditions as $addition) {
            if ($this->shouldApplyThisMonth($addition, $month, $year)) {
                $amount = (float) ($addition->amount ?? 0);
                $items[] = [
                    'name' => $addition->name,
                    'category' => $addition->category,
                    'amount' => $amount,
                    'type' => 'individual'
                ];
                $total += $amount;
            }
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Get dynamic deductions for the month
     */
    private function getDynamicDeductions($employeeId, $month, $year)
    {
        // Get from deductions table (your existing system)
        $deductions = DB::table('deductions')
            ->where(function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                      ->orWhere('employee_id', 'LIKE', "%{$employeeId}%")
                      ->orWhereRaw("FIND_IN_SET(?, employee_id)", [$employeeId]);
            })
            ->get();

        // Also get from employee_deductions table (individual assignments)
        $employeeDeductions = DB::table('employee_deductions')
            ->leftJoin('deductions', 'employee_deductions.deduction_id', '=', 'deductions.id')
            ->where('employee_deductions.employee_id', $employeeId)
            ->where('employee_deductions.is_active', 1)
            ->where('employee_deductions.effective_from', '<=', "{$year}-{$month}-01")
            ->where(function($query) use ($year, $month) {
                $query->whereNull('employee_deductions.effective_to')
                      ->orWhere('employee_deductions.effective_to', '>=', "{$year}-{$month}-01");
            })
            ->select(
                'employee_deductions.*',
                'deductions.name',
                'deductions.category'
            )
            ->get();

        $items = [];
        $total = 0;

        // Process general deductions
        foreach ($deductions as $deduction) {
            $amount = (float) ($deduction->unit_amount ?? 0);
            $items[] = [
                'name' => $deduction->name,
                'category' => $deduction->category,
                'amount' => $amount,
                'type' => 'general'
            ];
            $total += $amount;
        }

        // Process individual deductions
        foreach ($employeeDeductions as $deduction) {
            if ($this->shouldApplyThisMonth($deduction, $month, $year)) {
                // Check installment limits
                if ($deduction->max_installments &&
                     $deduction->installments_completed >= $deduction->max_installments) {
                    // Mark as completed and skip
                    DB::table('employee_deductions')
                        ->where('id', $deduction->id)
                        ->update(['is_active' => 0, 'deactivated_at' => now()]);
                    continue;
                }

                $amount = (float) ($deduction->amount ?? 0);
                $items[] = [
                    'name' => $deduction->name,
                    'category' => $deduction->category,
                    'amount' => $amount,
                    'type' => 'individual',
                    'installment' => ($deduction->installments_completed ?? 0) + 1,
                    'max_installments' => $deduction->max_installments
                ];
                $total += $amount;

                // Update installment count
                if ($deduction->max_installments) {
                    DB::table('employee_deductions')
                        ->where('id', $deduction->id)
                        ->increment('installments_completed');
                }
            }
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Check if addition/deduction should be applied this month
     */
    private function shouldApplyThisMonth($item, $month, $year)
    {
        $currentDate = Carbon::create($year, $month, 1);
        $effectiveFrom = Carbon::parse($item->effective_from);
        
        // Check if it's within the effective period
        if ($currentDate->lt($effectiveFrom)) {
            return false;
        }
        
        if (isset($item->effective_to) && $item->effective_to && $currentDate->gt(Carbon::parse($item->effective_to))) {
            return false;
        }

        // Check recurrence
        if (!$item->is_recurring) {
            // One-time payment - only apply in the effective month
            return $currentDate->isSameMonth($effectiveFrom);
        }

        switch ($item->recurrence_type) {
            case 'monthly':
                return true; // Apply every month
                
            case 'quarterly':
                // Apply every 3 months from effective date
                $monthsDiff = $effectiveFrom->diffInMonths($currentDate);
                return $monthsDiff % 3 === 0;
                
            case 'yearly':
                // Apply once a year on the same month as effective date
                return $currentDate->month === $effectiveFrom->month;
                
            default:
                return true;
        }
    }

    /**
     * Get working days in month (excluding weekends and holidays)
     */
    private function getWorkingDaysInMonth($month, $year)
{
    $startDate = Carbon::create($year, $month, 1);
    $endDate = $startDate->copy()->endOfMonth();

    $workingDays = 0;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $workingDays++;
    }

    return $workingDays;
}



    /**
     * Store monthly payslip record
     */
    private function storeMonthlyPayslip($payslipData, $payrollMonth)
    {
        return DB::table('monthly_payslips')->insertGetId([
            'employee_id' => $payslipData['employee_id'],
            'payroll_month' => $payrollMonth,
            'basic_salary' => $payslipData['adjusted_basic'],
            'hra' => $payslipData['adjusted_hra'],
            'da' => $payslipData['adjusted_da'],
            'conveyance' => $payslipData['base_salary_data']['conveyance'],
            'allowance' => $payslipData['base_salary_data']['allowance'],
            'medical' => $payslipData['base_salary_data']['medical'],
            'welfare' => $payslipData['base_salary_data']['welfare'],
            'overtime_amount' => $payslipData['overtime_data']['total_amount'],
            'dynamic_additions' => $payslipData['dynamic_additions']['total'],
            'total_earnings' => $payslipData['total_earnings'],
            'tds' => $payslipData['base_salary_data']['tds'],
            'pf' => $payslipData['base_salary_data']['pf'],
            'esi' => $payslipData['base_salary_data']['esi'],
            'tax' => $payslipData['base_salary_data']['tax'],
            'lop_deduction' => $payslipData['lop_data']['lop_deduction_amount'],
            'lop_days' => $payslipData['lop_data']['lop_days'],
            'dynamic_deductions' => $payslipData['dynamic_deductions']['total'],
            'total_deductions' => $payslipData['total_deductions'],
            'net_salary' => $payslipData['net_salary'],
            'total_working_days' => $payslipData['total_working_days'],
            'actual_working_days' => $payslipData['actual_working_days'],
            'total_hours_worked' => $payslipData['attendance_data']['total_hours'],
            'overtime_hours' => $payslipData['overtime_data']['total_hours'],
            'leave_days_taken' => $payslipData['leave_data']['total_leave_days'],
            'unpaid_leave_days' => $payslipData['leave_data']['unpaid_leave_days'],
            'late_arrivals' => $payslipData['attendance_data']['late_arrivals'],
            'early_departures' => $payslipData['attendance_data']['early_departures'],
            'attendance_data' => json_encode($payslipData['attendance_data']),
            'leave_data' => json_encode($payslipData['leave_data']),
            'overtime_data' => json_encode($payslipData['overtime_data']),
            'additions_data' => json_encode($payslipData['dynamic_additions']),
            'deductions_data' => json_encode($payslipData['dynamic_deductions']),
            'lop_data' => json_encode($payslipData['lop_data']),
            'status' => 'generated',
            'generated_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Generate PDF payslip
     */
    private function generatePayslipPDF($payslipId)
    {
        $payslip = $this->getPayslipWithEmployeeDetails($payslipId);
        
        if (!$payslip) {
            throw new Exception("Payslip not found");
        }

        // Get company settings
        $companySettings = $this->getCompanySettings();
        
        // Get employee bank information
        $bankInfo = $this->getEmployeeBankInfo($payslip->employee_id);
        $netInWords = ucwords($this->convertNumberToWords((int) $payslip->net_salary));
        $pdf = PDF::loadView('hrms.master.letters.templates.payroll-template', compact('payslip', 'companySettings', 'bankInfo', 'netInWords'));
        
        $fileName = "payslip_{$payslip->employeeid}_{$payslip->payroll_month_formatted}.pdf";
        $fileName = str_replace([' ', ','], ['_', ''], $fileName);
        
        $filePath = storage_path("app/public/monthly_payslips/{$fileName}");
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        $pdf->save($filePath);
        
        // Update payslip with PDF path
        DB::table('monthly_payslips')
            ->where('id', $payslipId)
            ->update([
                'pdf_path' => "monthly_payslips/{$fileName}",
                'updated_at' => now()
            ]);

        return $filePath;
    }

    /**
     * Send payslip email
     */
    private function sendPayslipEmail($payslipId, $pdfPath)
    {
        $payslip = $this->getPayslipWithEmployeeDetails($payslipId);
        
        if (!$payslip || !$payslip->email) {
            Log::warning("Cannot send email for payslip {$payslipId}: No email address");
            return false;
        }

        if (!file_exists($pdfPath)) {
            throw new Exception("Payslip PDF not found at {$pdfPath}");
        }

        try {
            Mail::to($payslip->email)->send(new MonthlyPayslipMail($payslip, $pdfPath));
            
            // Update email sent status
            DB::table('monthly_payslips')
                ->where('id', $payslipId)
                ->update([
                    'email_sent' => true,
                    'email_sent_at' => now(),
                    'status' => 'sent',
                    'updated_at' => now()
                ]);

            Log::info("Payslip email sent successfully to {$payslip->email}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send payslip email to {$payslip->email}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payslip with employee details
     */
    private function getPayslipWithEmployeeDetails($payslipId)
    {
        return DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('monthly_payslips.id', $payslipId)
            ->select(
                'monthly_payslips.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.email',
                'allemployees.phone',
                'allemployees.joiningdate',
                'designation.designation as designation_name',
                DB::raw("DATE_FORMAT(monthly_payslips.payroll_month, '%M %Y') as payroll_month_formatted")
            )
            ->first();
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummaryStats($month, $year)
    {
        $baseQuery = DB::table('monthly_payslips')
            ->whereMonth('payroll_month', $month)
            ->whereYear('payroll_month', $year);

        return [
            'total_payslips' => $baseQuery->count(),
            'emails_sent' => $baseQuery->where('email_sent', true)->count(),
            'total_payout' => $baseQuery->sum('net_salary'),
            'average_salary' => $baseQuery->avg('net_salary'),
            'total_overtime' => $baseQuery->sum('overtime_amount'),
            'total_lop_deduction' => $baseQuery->sum('lop_deduction'),
            'failed_count' => $baseQuery->where('status', 'failed')->count()
        ];
    }

    /**
     * Manual trigger for generating payslips
     */
    public function generatePayslips(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        try {
            $result = $this->generateMonthlyPayslips($month, $year);
            
            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            } else {
                return redirect()->back()->with('error', $result['message'] ?? 'Failed to generate payslips');
            }
        } catch (Exception $e) {
            Log::error("Manual payslip generation failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Download payslip PDF
     */
    public function downloadPayslip($id)
    {
        $payslip = DB::table('monthly_payslips')->where('id', $id)->first();
        
        if (!$payslip || !$payslip->pdf_path) {
            return redirect()->back()->with('error', 'Payslip not found');
        }

        $filePath = storage_path("app/public/{$payslip->pdf_path}");
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Payslip file not found');
        }

        return response()->download($filePath);
    }

    /**
     * Resend payslip email
     */
    public function resendEmail($id)
    {
        try {
            $payslip = DB::table('monthly_payslips')->where('id', $id)->first();
            
            if (!$payslip) {
                return redirect()->back()->with('error', 'Payslip not found');
            }
            
            $pdfPath = storage_path("app/public/{$payslip->pdf_path}");
            
            $this->sendPayslipEmail($id, $pdfPath);
            return redirect()->back()->with('success', 'Payslip email sent successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * View individual payslip details
     */
    public function show($id)
    {
        $payslip = $this->getPayslipWithEmployeeDetails($id);
        
        if (!$payslip) {
            return redirect()->back()->with('error', 'Payslip not found');
        }

        // Decode JSON data
        $payslip->attendance_data = json_decode($payslip->attendance_data, true);
        $payslip->leave_data = json_decode($payslip->leave_data, true);
        $payslip->overtime_data = json_decode($payslip->overtime_data, true);
        $payslip->additions_data = json_decode($payslip->additions_data, true);
        $payslip->deductions_data = json_decode($payslip->deductions_data, true);
        $payslip->lop_data = json_decode($payslip->lop_data, true);

        return view('hrms.payroll.automated-payslip.show', compact('payslip'));
    }

    /**
     * Convert number to words (for payslip)
     */
    public function convertNumberToWords($number) 
    {
        if ($number < 0) {
            return 'negative ' . $this->convertNumberToWords(-$number);
        }

        $numberWords = [
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
            5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
            14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
            18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
            80 => 'eighty', 90 => 'ninety'
        ];

        $scales = [
            1000 => 'thousand', 1000000 => 'million', 1000000000 => 'billion',
            1000000000000 => 'trillion'
        ];

        if ($number <= 20) {
            return $numberWords[$number];
        } elseif ($number < 100) {
            return $numberWords[floor($number / 10) * 10] .
                    ($number % 10 ? ' ' . $numberWords[$number % 10] : '');
        } elseif ($number < 1000) {
            return $numberWords[floor($number / 100)] . ' hundred' .
                    ($number % 100 ? ' ' . $this->convertNumberToWords($number % 100) : '');
        }

        // Handle thousands and above
        foreach ($scales as $scale => $scaleWord) {
            if ($number < $scale * 1000) {
                return $this->convertNumberToWords(floor($number / $scale)) . ' ' .
                        $scaleWord .
                        ($number % $scale ? ' ' . $this->convertNumberToWords($number % $scale) : '');
            }
        }
    }
}
