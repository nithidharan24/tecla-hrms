<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDF; 
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Session;
use App\Models\HikeLetterTemplate;
use App\Mail\HikeLetterPdfMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Helpers\PayrollActivityLogger;
use Carbon\Carbon;

class EmployeeSalaryController extends Controller
{
    /**
     * Display combined index with all payroll sections
     */
    public function combinedIndex(Request $request)
    {
        // Payroll Items data
        $additions = DB::table('additions')->get();
        $employees = DB::table('allemployees')->select('employeeid', 'firstname', 'lastname', 'id')->get();
        $overtimes = DB::table('overtimes')->get();
        $deductions = DB::table('deductions')->get();
        
        // Employee Salary data - Include all employees (left join with salaries)
    $salaries = DB::table('allemployees')
        ->leftJoin('employee_salaries', 'allemployees.id', '=', 'employee_salaries.employee_id')
        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
        ->where('allemployees.deleted_at', 0)
        ->select(
            'allemployees.id as employee_id',
            'allemployees.employeeid',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.email',
            'allemployees.joiningdate',
            'allemployees.profile_image', 
            'designation.designation as designation_name',
            'employee_salaries.id as salary_id',
            'employee_salaries.net_salary',
            'employee_salaries.basic',
            'employee_salaries.da',
            'employee_salaries.hra',
            'employee_salaries.pf',
            'employee_salaries.esi',
            'employee_salaries.tds',
            'employee_salaries.conveyance',
            'employee_salaries.allowance',
            'employee_salaries.medical',
            'employee_salaries.tax',
            'employee_salaries.welfare',
            'employee_salaries.approval_status',
            'employee_salaries.release_status'
        )
        ->orderBy('allemployees.firstname')
        ->get();
        // Employee Salary data
        $salaries = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
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

        $approvedSalaries = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
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
            ->where('employee_salaries.approval_status', 'approved')
            ->get();
        
        // Automated Payslips data
$month = $request->input('month', date('m'));
$year = $request->input('year', date('Y'));
$employeeId = $request->input('employee_id');

$payslipQuery = DB::table('monthly_payslips')
    ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
    ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
    ->select(
        'monthly_payslips.*',
        'allemployees.firstname',
        'allemployees.lastname',
        'allemployees.employeeid',
        'allemployees.email',
        'designation.designation as designation_name',
        DB::raw("DATE_FORMAT(monthly_payslips.payroll_month, '%M %Y') as payroll_month_formatted"),
        DB::raw('monthly_payslips.total_earnings as total_earnings'),
        DB::raw('monthly_payslips.net_salary as net_salary')
    )
    ->whereYear('monthly_payslips.payroll_month', $year)
    ->whereMonth('monthly_payslips.payroll_month', $month);
if ($employeeId) {
    $payslipQuery->where('monthly_payslips.employee_id', $employeeId);
}

$payslips = $payslipQuery->orderBy('monthly_payslips.created_at', 'desc')->get();

// Decode JSON fields for each payslip
foreach ($payslips as $payslip) {
    $payslip->attendance_data = json_decode($payslip->attendance_data, true) ?? [];
    $payslip->leave_data = json_decode($payslip->leave_data, true) ?? [];
    $payslip->overtime_data = json_decode($payslip->overtime_data, true) ?? [];
    $payslip->additions_data = json_decode($payslip->additions_data, true) ?? ['items' => [], 'total' => 0];
    $payslip->deductions_data = json_decode($payslip->deductions_data, true) ?? ['items' => [], 'total' => 0];
    $payslip->lop_data = json_decode($payslip->lop_data, true) ?? [];
}

        $payslipEmployees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('firstname')
            ->get();
$departments = DB::table('department')->where('deleted_at', 0)->select('id', 'department')->get();
        
        // Calculate summary statistics for payslips
        $summaryStats = $this->calculateSummaryStats($month, $year);

        // Activity Log data
        $activityLogQuery = DB::table('payroll_activity_log')
            ->leftJoin('allemployees', 'payroll_activity_log.employee_id', '=', 'allemployees.id')
            ->select(
                'payroll_activity_log.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email as employee_email'
            )
            ->orderBy('payroll_activity_log.created_at', 'desc');
        
        // Apply filters
        if ($request->filled('action_type')) {
            $activityLogQuery->where('action', $request->action_type);
        }
        
        if ($request->filled('module')) {
            $activityLogQuery->where('module', $request->module);
        }
        
        if ($request->filled('date')) {
            $activityLogQuery->whereDate('payroll_activity_log.created_at', $request->date);
        }
        
        $activityLogs = $activityLogQuery->paginate(20);

        return view('hrms.hr.PayRoll.EmployeeSalary.combined-index', compact(
            'additions', 
            'employees', 
            'overtimes', 
            'deductions',
            'salaries',
            'approvedSalaries',
            'payslips',
            'payslipEmployees',
            'month',
            'year',
            'summaryStats',
            'activityLogs',
            'departments' 
        ));
    }

    /**
     * Export employee salary table to CSV.
     */
    public function exportSalariesCsv(Request $request)
    {
        $query = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_salaries.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'allemployees.joiningdate',
                'designation.designation as designation_name'
            );

        $salaries = $query->orderBy('allemployees.firstname', 'asc')->get();

        $filename = 'employee_salaries_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($salaries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'S.No', 'Employee ID', 'Employee Name', 'Email', 'Join Date', 'Designation', 'Salary', 'Status'
            ]);

            foreach ($salaries as $index => $salary) {
                fputcsv($file, [
                    $index + 1,
                    $salary->employeeid,
                    $salary->firstname . ' ' . $salary->lastname,
                    $salary->email,
                    $salary->joiningdate ? Carbon::parse($salary->joiningdate)->format('d-m-Y') : '',
                    $salary->designation_name,
                    $salary->net_salary,
                    ucfirst($salary->approval_status ?? 'pending')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export employee salary table to PDF.
     */
    public function exportSalariesPdf(Request $request)
    {
        $salaries = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select(
                'employee_salaries.*',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'allemployees.joiningdate',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.firstname', 'asc')
            ->get();

        $pdf = PDF::loadView('hrms.hr.PayRoll.EmployeeSalary.salary_pdf', compact('salaries'));
        return $pdf->download('employee_salaries_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Calculate summary statistics for payslips
     */
    private function calculateSummaryStats($month, $year)
    {
        $baseQuery = DB::table('monthly_payslips')
            ->whereMonth('payroll_month', $month)
            ->whereYear('payroll_month', $year);

        return [
            'total_payslips'     => $baseQuery->count(),
            'emails_sent'        => $baseQuery->where('email_sent', true)->count(),
            'total_payout'       => $baseQuery->sum('net_salary'),
            'average_salary'     => $baseQuery->avg('net_salary'),
            'total_overtime'     => $baseQuery->sum('overtime_amount'),
            'total_lop_deduction'=> $baseQuery->sum('lop_deduction'),
            'failed_count'       => $baseQuery->where('status', 'failed')->count()
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salaries = DB::table('employee_salaries')
            ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
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
    
        return view('hrms.hr.PayRoll.EmployeeSalary.index', compact('salaries'));
    }
    
   /**
 * Show the form for creating a new resource.
 * DISABLED - Salary is created from Employee module
 */
public function create()
{
    return redirect()->route('employee.create')->with('info', 'Salary is configured when adding or editing employees.');
}

/**
 * Store a newly created resource in storage.
 * DISABLED - Salary is created from Employee module
 */
public function store(Request $request)
{
    return redirect()->route('employee.create')->with('info', 'Salary is configured when adding or editing employees.');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $salary = DB::table('employee_salaries')->where('id', $id)->first();

    if (!$salary) {
        abort(404, 'Salary record not found.');
    }

    $employees = DB::table('allemployees')
        ->select('employeeid', 'firstname', 'lastname', 'id')
        ->where('deleted_at', 0)
        ->get();

    // Get Salary Master Configuration
    $salaryConfig = DB::table('salary_master_config')->first();
    
    if (!$salaryConfig) {
        $salaryConfig = (object)[
            'gross_to_basic_percentage' => 50,
            'da_percentage' => 0,
            'hra_percentage' => 0,
            'conveyance' => 0,
            'special_allowance' => 0,
            'medical_allowance' => 0,
            'pf_percentage' => 0,
            'esi_percentage' => 0,
            'professional_tax' => 0,
            'welfare_fund' => 0,
            'tds' => 0,
        ];
    }

    return view('hrms.hr.PayRoll.EmployeeSalary.edit', compact('salary', 'employees', 'salaryConfig'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $oldSalary = DB::table('employee_salaries')->where('id', $id)->first();
    $oldData   = $oldSalary ? (array)$oldSalary : null;
    
    $request->validate([
        'employee_id' => 'required|exists:allemployees,id',
        'basic'       => 'required|numeric|min:0',
        'gross_salary' => 'nullable|numeric|min:0',
    ]);

    $basicSalary = floatval($request->basic) ?? 0;
    
    // Get percentages from form
    $daPercentage = floatval($request->da) ?? 0;
    $hraPercentage = floatval($request->hra) ?? 0;
    $pfPercentage = floatval($request->pf) ?? 0;
    $esiPercentage = floatval($request->esi) ?? 0;
    
    // Calculate amounts from percentages
    $daAmount = ($daPercentage / 100) * $basicSalary;
    $hraAmount = ($hraPercentage / 100) * $basicSalary;
    $pfAmount = ($pfPercentage / 100) * $basicSalary;
    $esiAmount = ($esiPercentage / 100) * $basicSalary;
    
    // Get fixed amount fields
    $conveyance = floatval($request->conveyance) ?? 0;
    $allowance = floatval($request->allowance) ?? 0;
    $medical = floatval($request->medical) ?? 0;
    $tds = floatval($request->tds) ?? 0;
    $tax = floatval($request->tax) ?? 0;
    $welfare = floatval($request->welfare) ?? 0;
    $employeeLeave = floatval($request->employee_leave) ?? 0;
    
    // Calculate totals
    $totalEarnings = $basicSalary + $daAmount + $hraAmount + $conveyance + $allowance + $medical;
    
    // Add dynamic additions
    if ($request->has('additions') && is_array($request->additions)) {
        foreach ($request->additions as $addition) {
            $totalEarnings += floatval($addition['amount'] ?? 0);
        }
    }
    
    $totalDeductions = $pfAmount + $esiAmount + $tds + $tax + $welfare + $employeeLeave;
    
    // Add dynamic deductions
    if ($request->has('deductions') && is_array($request->deductions)) {
        foreach ($request->deductions as $deduction) {
            $totalDeductions += floatval($deduction['amount'] ?? 0);
        }
    }
    
    $netSalary = $totalEarnings - $totalDeductions;
    $grossSalary = floatval($request->gross_salary) ?? $totalEarnings;
    
    \Log::info('Updating salary:', [
        'employee_id' => $request->employee_id,
        'basic' => $basicSalary,
        'da_percentage' => $daPercentage,
        'da_amount' => $daAmount,
        'hra_percentage' => $hraPercentage,
        'hra_amount' => $hraAmount,
        'pf_percentage' => $pfPercentage,
        'pf_amount' => $pfAmount,
        'esi_percentage' => $esiPercentage,
        'esi_amount' => $esiAmount,
        'total_earnings' => $totalEarnings,
        'total_deductions' => $totalDeductions,
        'net_salary' => $netSalary,
    ]);

    $updated = DB::table('employee_salaries')
        ->where('id', $id)
        ->update([
            'employee_id' => $request->employee_id,
            'basic' => $basicSalary,
            'da' => $daAmount,
            'hra' => $hraAmount,
            'pf' => $pfAmount,
            'esi' => $esiAmount,
            'conveyance' => $conveyance,
            'allowance' => $allowance,
            'medical' => $medical,
            'tds' => $tds,
            'tax' => $tax,
            'welfare' => $welfare,
            'employee_leave' => $employeeLeave,
            'net_salary' => $netSalary,
            'gross_salary' => $grossSalary,
            'updated_at' => now()
        ]);
    
    if ($updated) {
        $newSalary = DB::table('employee_salaries')->where('id', $id)->first();
        $newData   = $newSalary ? (array)$newSalary : null;
        
        $employee = DB::table('allemployees')->find($request->employee_id);
        if ($employee) {
            PayrollActivityLogger::logUpdate(
                'Employee Salary',
                "Salary for {$employee->firstname} {$employee->lastname}",
                $request->employee_id,
                $oldData,
                $newData
            );
        }
        
        return redirect()->route('salary.index')->with('success', 'Salary updated successfully.');
    } else {
        return redirect()->back()->with('error', 'Failed to update salary. Please try again.');
    }
}

    public function updateApprovalStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'approval_status' => 'required|in:pending,approved,hold',
        ]);

        if (!Schema::hasColumn('employee_salaries', 'approval_status')) {
            return redirect()->back()->withErrors([
                'approval_status' => 'Salary approval status column is missing. Please run migrations.',
            ]);
        }

        $salary = DB::table('employee_salaries')->where('id', $id)->first();

        if (!$salary) {
            abort(404, 'Salary record not found.');
        }

        $updateData = [
            'approval_status' => $validated['approval_status'],
        ];

        if ($validated['approval_status'] !== 'approved' && Schema::hasColumn('employee_salaries', 'release_status')) {
            $updateData['release_status'] = 'hold';
        }

        if (Schema::hasColumn('employee_salaries', 'updated_at')) {
            $updateData['updated_at'] = now();
        }

        DB::table('employee_salaries')
            ->where('id', $id)
            ->update($updateData);

        return redirect()->route('payroll.combined', ['tab' => 'employee_salary'])
            ->with('success', 'Salary status updated successfully.');
    }

    public function bulkUpdateApprovalStatus(Request $request)
    {
        $validated = $request->validate([
            'salary_ids' => 'required|array|min:1',
            'salary_ids.*' => 'integer|exists:employee_salaries,id',
            'approval_status' => 'required|in:approved,hold',
        ]);

        if (!Schema::hasColumn('employee_salaries', 'approval_status')) {
            return redirect()->back()->withErrors([
                'approval_status' => 'Salary approval status column is missing. Please run migrations.',
            ]);
        }

        $updateData = [
            'approval_status' => $validated['approval_status'],
        ];

        if ($validated['approval_status'] !== 'approved' && Schema::hasColumn('employee_salaries', 'release_status')) {
            $updateData['release_status'] = 'hold';
        }

        if (Schema::hasColumn('employee_salaries', 'updated_at')) {
            $updateData['updated_at'] = now();
        }

        $updated = DB::table('employee_salaries')
            ->whereIn('id', $validated['salary_ids'])
            ->update($updateData);

        $messageStatus = $validated['approval_status'] === 'approved' ? 'approved' : 'held';

        return redirect()->route('payroll.combined', ['tab' => 'employee_salary'])
            ->with('success', "{$updated} salary record(s) {$messageStatus} successfully.");
    }

    public function bulkUpdateReleaseStatus(Request $request)
    {
        $validated = $request->validate([
            'salary_ids' => 'required|array|min:1',
            'salary_ids.*' => 'integer|exists:employee_salaries,id',
            'release_status' => 'required|in:released,hold',
        ]);

        if (!Schema::hasColumn('employee_salaries', 'release_status')) {
            return redirect()->back()->withErrors([
                'release_status' => 'Salary release status column is missing. Please run migrations.',
            ]);
        }

        $updateData = [
            'release_status' => $validated['release_status'],
        ];

        if (Schema::hasColumn('employee_salaries', 'updated_at')) {
            $updateData['updated_at'] = now();
        }

        $updated = DB::table('employee_salaries')
            ->whereIn('id', $validated['salary_ids'])
            ->where('approval_status', 'approved')
            ->update($updateData);

        $messageStatus = $validated['release_status'] === 'released' ? 'released' : 'held';

        return redirect()->route('payroll.combined', ['tab' => 'salary_release'])
            ->with('success', "{$updated} approved salary record(s) {$messageStatus} successfully.");
    }
    
    /**
     * Bulk update a specific field for all salary records.
     */
    public function bulkUpdateField(Request $request)
    {
        $request->validate([
            'field' => 'required|string|in:basic,da,hra,conveyance,allowance,medical,pf,esi,tds,tax,welfare',
            'value' => 'required|numeric|min:0'
        ]);

        $field = $request->field;
        $value = floatval($request->value);

        if (in_array($field, ['da', 'hra', 'pf', 'esi'])) {
            $formatMessage = "{$value}%";
        } else {
            $formatMessage = "₹{$value}";
        }

        $salaries = DB::table('employee_salaries')->get();
        $updatedCount = 0;

        foreach ($salaries as $salary) {
            $salaryData = (array)$salary;
            
            $actualValue = $value;

            $salaryData[$field] = $actualValue;
            
            $totalEarnings = floatval($salaryData['basic'] ?? 0) + floatval($salaryData['da'] ?? 0) + floatval($salaryData['hra'] ?? 0) + floatval($salaryData['conveyance'] ?? 0) + floatval($salaryData['allowance'] ?? 0) + floatval($salaryData['medical'] ?? 0);
            $totalDeductions = floatval($salaryData['pf'] ?? 0) + floatval($salaryData['esi'] ?? 0) + floatval($salaryData['tds'] ?? 0) + floatval($salaryData['tax'] ?? 0) + floatval($salaryData['welfare'] ?? 0) + floatval($salaryData['employee_leave'] ?? 0);
            
            $netSalary = max(0, $totalEarnings - $totalDeductions);

            $updateData = [
                $field => $actualValue,
                'net_salary' => $netSalary
            ];

            if (Schema::hasColumn('employee_salaries', 'updated_at')) {
                $updateData['updated_at'] = now();
            }

            DB::table('employee_salaries')->where('id', $salary->id)->update($updateData);
            $updatedCount++;
        }

        return redirect()->route('payroll.combined', ['tab' => 'employee_salary'])
            ->with('success', "Successfully updated " . strtoupper($field) . " to {$formatMessage} for {$updatedCount} records.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $salary = DB::table('employee_salaries')->where('id', $id)->first();
        
        if ($salary) {
            $oldData  = (array)$salary;
            $employee = DB::table('allemployees')->find($salary->employee_id);
            
            if ($employee) {
                PayrollActivityLogger::logDelete(
                    'Employee Salary',
                    "Salary for {$employee->firstname} {$employee->lastname}",
                    $salary->employee_id,
                    $oldData
                );
            }
        }
        
        DB::table('employee_salaries')->where('id', $id)->delete();

        return redirect()->route('salary.index')->with('success', 'Salary deleted successfully.');
    }

    /**
     * Get additions and deductions for an employee
     */
    public function getAdditionsAndDeductions(Request $request)
    {
        $employeeId = $request->input('employee_id');
        
        $additions = DB::table('additions')
            ->where('employee_id', $employeeId)
            ->orWhere('employee_id', 'all')
            ->select('name', 'unit_amount')
            ->get();
    
        $deductions = DB::table('deductions')
            ->where('employee_id', $employeeId)
            ->orWhere('employee_id', 'all')
            ->select('name', 'unit_amount')
            ->get();
    
        $statutory = DB::table('employee_bank_statutory')
            ->where('employee_id', $employeeId)
            ->select('total_rate', 'total_esi_rate')
            ->first();
    
        return response()->json([
            'additions'     => $additions,
            'deductions'    => $deductions,
            'total_rate'    => $statutory->total_rate ?? 0,
            'total_esi_rate'=> $statutory->total_esi_rate ?? 0
        ]);
    }
    
    /**
     * Show payslip for an employee
     */
    public function showPayslip($id)
    {
        $salary = DB::table('employee_salaries')->where('id', $id)->first();
    
        if (!$salary) {
            abort(404);
        }
    
        $employee = DB::table('allemployees')
            ->where('id', $salary->employee_id)
            ->where('deleted_at', 0)
            ->first();

        if (!$employee) {
            abort(404);
        }
    
        $designation = DB::table('designation')->where('id', $employee->designation)->first();
    
        if (!$designation) {
            abort(404);
        }
    
        $earnings = [
            ['label' => 'Basic Salary', 'amount' => $salary->basic],
            ['label' => 'HRA',          'amount' => $salary->hra],
            ['label' => 'Conveyance',   'amount' => $salary->conveyance],
        ];
    
        $deductions = [
            ['label' => 'TDS', 'amount' => $salary->tds],
            ['label' => 'PF',  'amount' => $salary->pf],
        ];
    
        $totalEarnings   = array_sum(array_column($earnings, 'amount'));
        $totalDeductions = array_sum(array_column($deductions, 'amount'));
        $netSalary       = $salary->net_salary;
        $netSalaryInWords= $this->convertNumberToWords($netSalary);
        $salaryMonth     = date('F, Y', strtotime($salary->created_at));
    
        return view('hrms.hr.PayRoll.Payslip.index', compact(
            'employee',
            'designation',
            'earnings',
            'deductions',
            'totalEarnings',
            'totalDeductions',
            'netSalary',
            'netSalaryInWords',
            'salaryMonth',
            'salary',
            'id'
        ));
    }
    
    /**
     * Convert number to words
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
            1000        => 'thousand',
            1000000     => 'million',
            1000000000  => 'billion',
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

        foreach ($scales as $scale => $scaleWord) {
            if ($number < $scale * 1000) {
                return $this->convertNumberToWords(floor($number / $scale)) . ' ' .
                       $scaleWord .
                       ($number % $scale ? ' ' . $this->convertNumberToWords($number % $scale) : '');
            }
        }
    }

    /**
     * Download CSV payslip
     */
    public function downloadCSV($id)
    {
        $salary = DB::table('employee_salaries')->where('id', $id)->first();
        
        $employee = DB::table('allemployees')
            ->where('id', $salary->employee_id)
            ->where('deleted_at', 0)
            ->first();

        $earnings = [
            ['label' => 'Basic Salary', 'amount' => $salary->basic],
            ['label' => 'HRA',          'amount' => $salary->hra],
            ['label' => 'Conveyance',   'amount' => $salary->conveyance],
        ];
    
        $deductions = [
            ['label' => 'TDS', 'amount' => $salary->tds],
            ['label' => 'PF',  'amount' => $salary->pf],
        ];
    
        $csvData   = [];
        $csvData[] = ['Label', 'Amount'];
    
        foreach ($earnings as $earning) {
            $csvData[] = [$earning['label'], $earning['amount']];
        }
    
        $csvData[] = ['Total Earnings', array_sum(array_column($earnings, 'amount'))];
        $csvData[] = [''];
    
        foreach ($deductions as $deduction) {
            $csvData[] = [$deduction['label'], $deduction['amount']];
        }
    
        $csvData[] = ['Total Deductions', array_sum(array_column($deductions, 'amount'))];
        $csvData[] = ['Net Salary', $salary->net_salary];
    
        $filename = 'payslip_' . $employee->employeeid . '_' . date('Y-m-d') . '.csv';
        $handle   = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        exit;
    }
    
    /**
     * Download PDF payslip
     */
    public function downloadPdf($id)
    {
        $salary = DB::table('employee_salaries')->where('id', $id)->first();
        if (!$salary) {
            abort(404);
        }

        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->select('allemployees.*', 'designation.designation as designation_name')
            ->where('allemployees.id', $salary->employee_id)
            ->where('allemployees.deleted_at', 0)
            ->first();

        if (!$employee) {
            abort(404);
        }

        $earnings = [
            ['label' => 'Basic Salary', 'amount' => (float)$salary->basic],
            ['label' => 'HRA',          'amount' => (float)$salary->hra],
            ['label' => 'Conveyance',   'amount' => (float)$salary->conveyance],
        ];

        $deductions = [
            ['label' => 'TDS', 'amount' => (float)$salary->tds],
            ['label' => 'PF',  'amount' => (float)$salary->pf],
        ];

        $totalEarnings   = array_sum(array_column($earnings, 'amount'));
        $totalDeductions = array_sum(array_column($deductions, 'amount'));
        $netSalary       = (float)$salary->net_salary;
        $netSalaryInWords= $this->convertNumberToWords($netSalary);
        $salaryMonth     = date('F, Y', strtotime($salary->created_at));

        $html  = "<h1>Salary Slip for {$salaryMonth}</h1>";
        $html .= "<p>Employee: {$employee->firstname} {$employee->lastname}</p>";
        $html .= "<p>Designation: {$employee->designation_name}</p>";
        $html .= "<h3>Earnings</h3><ul>";
        foreach ($earnings as $earning) {
            $html .= "<li>{$earning['label']}: ₹" . number_format($earning['amount'], 2) . "</li>";
        }
        $html .= "</ul><h3>Deductions</h3><ul>";
        foreach ($deductions as $deduction) {
            $html .= "<li>{$deduction['label']}: ₹" . number_format($deduction['amount'], 2) . "</li>";
        }
        $html .= "</ul>";
        $html .= "<p><strong>Total Earnings: ₹" . number_format($totalEarnings, 2) . "</strong></p>";
        $html .= "<p><strong>Total Deductions: ₹" . number_format($totalDeductions, 2) . "</strong></p>";
        $html .= "<p><strong>Net Salary: ₹" . number_format($netSalary, 2) . "</strong></p>";
        $html .= "<p>Amount in Words: {$netSalaryInWords}</p>";

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream("salary_slip_{$salaryMonth}.pdf", ["Attachment" => true]);
    }

    /**
     * Get TDS percentage based on basic salary
     */
    public function getTdsPercentage(Request $request)
    {
        $basicSalary    = $request->input('basic_salary');
        $salarySettings = DB::table('salary_settings')->first();

        if ($salarySettings) {
            $tdsEntries = json_decode($salarySettings->tds_entries, true);
            foreach ($tdsEntries as $entry) {
                if ($basicSalary >= $entry['tds_salary_from'] && $basicSalary <= $entry['tds_salary_to']) {
                    return response()->json(['tds_percentage' => $entry['tds_percentage']]);
                }
            }
        }

        return response()->json(['tds_percentage' => 0]);
    }

    /**
     * Send hike letter to employee and save history
     */
    public function sendHikeLetter($id)
{
    // Salary record
    $salary = DB::table('employee_salaries')->where('id', $id)->first();
    if (!$salary) {
        return redirect()->back()->with('error', 'Salary record not found.');
    }

    // Check if hike letter was already sent today
    if (Schema::hasTable('hike_letter_history')) {
        $existingSent = DB::table('hike_letter_history')
            ->where('salary_id', $id)
            ->where('status', 'sent')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingSent) {
            $oldDetails = json_decode($existingSent->new_salary_details, true);
            $currentDetails = $this->getCurrentSalaryDetails($salary);
            
            $oldDetailsJson = json_encode($oldDetails);
            $currentDetailsJson = json_encode($currentDetails);
            
            if ($oldDetailsJson === $currentDetailsJson) {
                return redirect()->back()->with('warning', 'Hike letter already sent today with the same salary details. Please wait 24 hours before sending again or update the salary first.');
            } else {
                \Log::info('Salary details changed, allowing resend', [
                    'salary_id' => $id,
                    'old_details' => $oldDetailsJson,
                    'new_details' => $currentDetailsJson
                ]);
            }
        }
    }

    // Employee with designation and department
    $employee = DB::table('allemployees')
        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->select(
            'allemployees.*',
            'designation.designation as designation_name',
            'department.department as department_name',
            'branches.name as branch_name'
        )
        ->where('allemployees.id', $salary->employee_id)
        ->first();

    if (!$employee || empty($employee->email)) {
        return redirect()->back()->with('error', 'Employee or email not found.');
    }

    // Get latest hike letter template
    $template = HikeLetterTemplate::orderByDesc('created_at')->first();
    if (!$template) {
        return redirect()->back()->with('error', 'Hike letter template not found.');
    }

    // Get previous salary for old CTC calculation
    $previousSalary = DB::table('employee_salaries')
        ->where('employee_id', $salary->employee_id)
        ->where('id', '<', $salary->id)
        ->orderByDesc('id')
        ->first();

    $annualCtcFromSalary = function ($salaryRow): float {
        if (!$salaryRow) return 0;
        $grossMonthly = (float)($salaryRow->basic ?? 0)
            + (float)($salaryRow->hra ?? 0)
            + (float)($salaryRow->da ?? 0)
            + (float)($salaryRow->conveyance ?? 0)
            + (float)($salaryRow->allowance ?? 0)
            + (float)($salaryRow->medical ?? 0);
        return $grossMonthly * 12;
    };

    // Map payroll fields
    $basic      = (float)($salary->basic ?? 0);
    $hra        = (float)($salary->hra ?? 0);
    $da         = (float)($salary->da ?? 0);
    $conveyance = (float)($salary->conveyance ?? 0);
    $allowance  = (float)($salary->allowance ?? 0);
    $medical    = (float)($salary->medical ?? 0);
    $welfare    = (float)($salary->welfare ?? 0);
    $pfEmployee = (float)($salary->pf ?? 0);
    $esiEmployee= (float)($salary->esi ?? 0);
    $tds        = (float)($salary->tds ?? 0);
    $profTax    = (float)($salary->tax ?? 0);

    $grossMonthly     = $basic + $hra + $da + $conveyance + $allowance + $medical;
    $deductionsMonthly= $pfEmployee + $esiEmployee + $tds + $profTax + $welfare;
    $netMonthly       = (float)($salary->net_salary ?? max(0, $grossMonthly - $deductionsMonthly));
    $ctcMonthly       = $grossMonthly;
    $ctcAnnual        = $ctcMonthly * 12;

    // Build hike object expected by the template
    $hike = (object)[
        'effective_date'             => now()->format('jS F Y'),
        'old_ctc'                    => number_format($annualCtcFromSalary($previousSalary), 2),
        'new_ctc'                    => number_format($ctcAnnual, 2),
        'new_ctc_words'              => $this->convertNumberToWords((int)round($ctcAnnual)) . ' only',
        'designation'                => $employee->designation_name ?? '',
        'basic_monthly'              => number_format($basic, 2),
        'basic_annual'               => number_format($basic * 12, 2),
        'hra_monthly'                => number_format($hra, 2),
        'hra_annual'                 => number_format($hra * 12, 2),
        'cca_monthly'                => number_format($conveyance, 2),
        'cca_annual'                 => number_format($conveyance * 12, 2),
        'statutory_bonus_monthly'    => number_format(0, 2),
        'statutory_bonus_annual'     => number_format(0, 2),
        'training_allowance_monthly' => number_format(0, 2),
        'training_allowance_annual'  => number_format(0, 2),
        'special_allowance_monthly'  => number_format($allowance, 2),
        'special_allowance_annual'   => number_format($allowance * 12, 2),
        'vpp_monthly'                => number_format(0, 2),
        'vpp_annual'                 => number_format(0, 2),
        'gross_monthly'              => number_format($grossMonthly, 2),
        'gross_annual'               => number_format($grossMonthly * 12, 2),
        'pf_employer_monthly'        => number_format(0, 2),
        'pf_employer_annual'         => number_format(0, 2),
        'esi_employer_monthly'       => number_format(0, 2),
        'esi_employer_annual'        => number_format(0, 2),
        'pf_employee_monthly'        => number_format($pfEmployee, 2),
        'pf_employee_annual'         => number_format($pfEmployee * 12, 2),
        'esi_employee_monthly'       => number_format($esiEmployee, 2),
        'esi_employee_annual'        => number_format($esiEmployee * 12, 2),
        'staff_welfare_monthly'      => number_format($welfare, 2),
        'staff_welfare_annual'       => number_format($welfare * 12, 2),
        'prof_tax_monthly'           => number_format($profTax, 2),
        'prof_tax_annual'            => number_format($profTax * 12, 2),
        'net_income_monthly'         => number_format($netMonthly, 2),
        'net_income_annual'          => number_format($netMonthly * 12, 2),
        'ctc_monthly'                => number_format($ctcMonthly, 2),
        'ctc_annual'                 => number_format($ctcAnnual, 2),
    ];

    $employeeView = (object)[
        'firstname'        => $employee->firstname ?? '',
        'lastname'         => $employee->lastname ?? '',
        'employeeid'       => $employee->employeeid ?? '',
        'designation_name' => $employee->designation_name ?? '',
        'department_name'  => $employee->department_name ?? '',
        'branch_name'      => $employee->branch_name ?? '',
        'joining_date'     => $employee->joiningdate ?? '',
        'email'            => $employee->email ?? '',
    ];

    // Build old salary details for history
    $oldSalaryDetails = null;
    if ($previousSalary) {
        $oldBasic        = (float)($previousSalary->basic ?? 0);
        $oldGross        = $oldBasic + (float)($previousSalary->hra ?? 0) + (float)($previousSalary->da ?? 0);
        $oldCtcAnnual    = $oldGross * 12;
        $oldSalaryDetails= [
            'basic'       => number_format($oldBasic, 2),
            'hra'         => number_format($previousSalary->hra ?? 0, 2),
            'da'          => number_format($previousSalary->da ?? 0, 2),
            'gross_monthly'=> number_format($oldGross, 2),
            'ctc_annual'  => number_format($oldCtcAnnual, 2),
            'net_salary'  => number_format($previousSalary->net_salary ?? 0, 2),
        ];
    }

    // Build new salary details for history
    $newSalaryDetails = [
        'basic'        => number_format($basic, 2),
        'hra'          => number_format($hra, 2),
        'da'           => number_format($da, 2),
        'conveyance'   => number_format($conveyance, 2),
        'allowance'    => number_format($allowance, 2),
        'medical'      => number_format($medical, 2),
        'pf'           => number_format($pfEmployee, 2),
        'esi'          => number_format($esiEmployee, 2),
        'tds'          => number_format($tds, 2),
        'prof_tax'     => number_format($profTax, 2),
        'welfare'      => number_format($welfare, 2),
        'gross_monthly'=> number_format($grossMonthly, 2),
        'gross_annual' => number_format($grossMonthly * 12, 2),
        'net_monthly'  => number_format($netMonthly, 2),
        'net_annual'   => number_format($netMonthly * 12, 2),
        'ctc_monthly'  => number_format($ctcMonthly, 2),
        'ctc_annual'   => number_format($ctcAnnual, 2),
        'effective_date'=> now()->format('Y-m-d'),
    ];

    try {
        // Render template and generate PDF
        $html = Blade::render($template->content, [
            'employee'   => $employeeView,
            'hike'       => $hike,
        ] + $this->getCompanySettings() + $this->getLetterSignatureData('hike_letter'));

        // Save PDF to storage
        $pdfDirectory = storage_path('app/public/hike-letters');
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }

        $pdfFileName  = 'hike-letter-' . ($employee->employeeid ?? 'employee') . '-' . time() . '.pdf';
        $pdfStoragePath = 'public/hike-letters/' . $pdfFileName;
        $fullPdfPath  = storage_path('app/' . $pdfStoragePath);

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
        $pdf->save($fullPdfPath);

        $pdfContent = file_get_contents($fullPdfPath);

        $subject = 'Hike Letter - ' . ($employee->firstname ?? '') . ' ' . ($employee->lastname ?? '');
$body = "Dear " . ($employee->firstname ?? '') . " " . ($employee->lastname ?? '') . ",\n\n"
      . "We are pleased to inform you that your salary hike letter is attached to this email.\n\n"
      . "Effective Date: " . now()->format('d F Y') . "\n"
      . "Please find attached the official hike letter for your reference.\n\n"
      . "You can also download the PDF attachment for your records.\n\n"
      . "For any questions, please contact the HR department.\n\n"
      . "Best regards,\n"
      . "HR Department\n"
      . config('app.name', 'HRMS');
      
$filename = 'hike-letter-' . ($employee->employeeid ?? 'employee') . '.pdf';

Mail::to($employee->email)->send(
    new HikeLetterPdfMail($subject, $body, $pdfContent, $filename)
);

        // Save successful history record
        $this->saveHikeLetterHistory(
            $employee,
            $salary,
            $oldSalaryDetails,
            $newSalaryDetails,
            $pdfStoragePath,
            'sent',
            null
        );

        // Log email activity
        PayrollActivityLogger::logEmail(
            'Employee Salary',
            "Sent hike letter to {$employee->firstname} {$employee->lastname} ({$employee->employeeid}) - New CTC: ₹" . number_format($ctcAnnual, 2),
            $employee->id
        );

        return redirect()->back()->with('success', 'Hike letter emailed successfully to ' . $employee->email);

    } catch (\Exception $e) {
        \Log::error('Hike letter sending failed: ' . $e->getMessage(), [
            'salary_id'   => $id,
            'employee_id' => $employee->id ?? null,
            'trace'       => $e->getTraceAsString(),
        ]);

        // Save failed history record
        $this->saveHikeLetterHistory(
            $employee,
            $salary,
            $oldSalaryDetails ?? null,
            array_merge($newSalaryDetails, ['error' => $e->getMessage()]),
            null,
            'failed',
            $e->getMessage()
        );

        return redirect()->back()->with('error', 'Failed to send hike letter: ' . $e->getMessage());
    }
}

    /**
     * Save hike letter send history to database
     */
    private function saveHikeLetterHistory($employee, $salary, $oldSalaryDetails, $newSalaryDetails, $pdfPath, $status, $errorMessage = null)
    {
        try {
            if (!Schema::hasTable('hike_letter_history')) {
                \Log::warning('hike_letter_history table does not exist');
                return null;
            }

            $historyId = DB::table('hike_letter_history')->insertGetId([
                'employee_id'          => $employee->id,
                'employee_name'        => $employee->firstname . ' ' . $employee->lastname,
                'employee_email'       => $employee->email,
                'employee_employeeid'  => $employee->employeeid ?? null,
                'designation_name'     => $employee->designation_name ?? null,
                'department_name'      => $employee->department_name ?? null,
                'branch_name'          => $employee->branch_name ?? null,
                'salary_id'            => $salary->id,
                'old_salary_details'   => $oldSalaryDetails ? json_encode($oldSalaryDetails) : null,
                'new_salary_details'   => json_encode($newSalaryDetails),
                'status'               => $status,
                'error_message'        => $errorMessage,
                'pdf_path'             => $pdfPath,
                'email_sent_at'        => $status === 'sent' ? now() : null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            \Log::info('Hike letter history saved', [
                'history_id'  => $historyId,
                'employee_id' => $employee->id,
                'salary_id'   => $salary->id,
                'status'      => $status,
            ]);

            return $historyId;

        } catch (\Exception $e) {
            \Log::error('Failed to save hike letter history: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * View hike letter send history
     */
    public function hikeLetterHistory(Request $request)
    {
        $query = DB::table('hike_letter_history')->orderBy('created_at', 'desc');

        if ($request->filled('employee_name')) {
            $query->where('employee_name', 'LIKE', '%' . $request->employee_name . '%');
        }

        if ($request->filled('employee_employeeid')) {
            $query->where('employee_employeeid', 'LIKE', '%' . $request->employee_employeeid . '%');
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

        $history = $query->paginate(50);

        $stats = DB::table('hike_letter_history')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed'),
                DB::raw('COUNT(DISTINCT employee_id) as unique_employees')
            )
            ->first();

        return view('hrms.hr.PayRoll.EmployeeSalary.hike-letter-history', compact('history', 'stats'));
    }

    /**
     * View single hike letter send details
     */
    public function viewHikeLetterDetails($id)
    {
        $record = DB::table('hike_letter_history')->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('salary.hike-history')->with('error', 'Record not found.');
        }

        $record->old_salary_details = json_decode($record->old_salary_details, true);
        $record->new_salary_details = json_decode($record->new_salary_details, true);

        return view('hrms.hr.PayRoll.EmployeeSalary.hike-letter-details', compact('record'));
    }

    /**
     * Resend failed hike letter
     */
    public function resendHikeLetter($historyId)
    {
        $record = DB::table('hike_letter_history')->where('id', $historyId)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'department.department as department_name',
                'branches.name as branch_name'
            )
            ->where('allemployees.id', $record->employee_id)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.']);
        }

        $salary = DB::table('employee_salaries')->where('id', $record->salary_id)->first();

        if (!$salary) {
            return response()->json(['success' => false, 'message' => 'Salary record not found.']);
        }

        $template = HikeLetterTemplate::orderByDesc('created_at')->first();
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found.']);
        }

        try {
            $newDetails = json_decode($record->new_salary_details, true);

            $hike = (object)[
                'effective_date'             => $newDetails['effective_date'] ?? now()->format('jS F Y'),
                'new_ctc'                    => $newDetails['ctc_annual'] ?? '0',
                'new_ctc_words'              => $this->convertNumberToWords((int)round(floatval(str_replace(',', '', $newDetails['ctc_annual'] ?? '0')))) . ' only',
                'designation'                => $employee->designation_name ?? '',
                'basic_monthly'              => $newDetails['basic'] ?? '0',
                'basic_annual'               => $newDetails['basic_annual'] ?? '0',
                'hra_monthly'                => $newDetails['hra'] ?? '0',
                'hra_annual'                 => $newDetails['hra_annual'] ?? '0',
                'cca_monthly'                => $newDetails['conveyance'] ?? '0',
                'cca_annual'                 => number_format((float)str_replace(',', '', $newDetails['conveyance'] ?? '0') * 12, 2),
                'special_allowance_monthly'  => $newDetails['allowance'] ?? '0',
                'special_allowance_annual'   => number_format((float)str_replace(',', '', $newDetails['allowance'] ?? '0') * 12, 2),
                'gross_monthly'              => $newDetails['gross_monthly'] ?? '0',
                'gross_annual'               => $newDetails['gross_annual'] ?? '0',
                'net_income_monthly'         => $newDetails['net_monthly'] ?? '0',
                'net_income_annual'          => $newDetails['net_annual'] ?? '0',
                'ctc_monthly'                => $newDetails['ctc_monthly'] ?? '0',
                'ctc_annual'                 => $newDetails['ctc_annual'] ?? '0',
                'statutory_bonus_monthly'    => '0.00',
                'statutory_bonus_annual'     => '0.00',
                'training_allowance_monthly' => '0.00',
                'training_allowance_annual'  => '0.00',
                'vpp_monthly'                => '0.00',
                'vpp_annual'                 => '0.00',
                'pf_employer_monthly'        => '0.00',
                'pf_employer_annual'         => '0.00',
                'esi_employer_monthly'       => '0.00',
                'esi_employer_annual'        => '0.00',
                'pf_employee_monthly'        => $newDetails['pf'] ?? '0',
                'pf_employee_annual'         => number_format((float)str_replace(',', '', $newDetails['pf'] ?? '0') * 12, 2),
                'esi_employee_monthly'       => $newDetails['esi'] ?? '0',
                'esi_employee_annual'        => number_format((float)str_replace(',', '', $newDetails['esi'] ?? '0') * 12, 2),
                'staff_welfare_monthly'      => $newDetails['welfare'] ?? '0',
                'staff_welfare_annual'       => number_format((float)str_replace(',', '', $newDetails['welfare'] ?? '0') * 12, 2),
                'prof_tax_monthly'           => $newDetails['prof_tax'] ?? '0',
                'prof_tax_annual'            => number_format((float)str_replace(',', '', $newDetails['prof_tax'] ?? '0') * 12, 2),
                'old_ctc'                    => '0.00',
            ];

            $employeeView = (object)[
                'firstname'        => $employee->firstname ?? '',
                'lastname'         => $employee->lastname ?? '',
                'employeeid'       => $employee->employeeid ?? '',
                'designation_name' => $employee->designation_name ?? '',
                'department_name'  => $employee->department_name ?? '',
                'branch_name'      => $employee->branch_name ?? '',
                'joining_date'     => $employee->joiningdate ?? '',
                'email'            => $employee->email ?? '',
            ];

            $html = Blade::render($template->content, [
                'employee' => $employeeView,
                'hike'     => $hike,
            ] + $this->getCompanySettings() + $this->getLetterSignatureData('hike_letter'));

            // Save PDF to storage
            $pdfDirectory = storage_path('app/public/hike-letters');
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            $pdfFileName    = 'hike-letter-' . ($employee->employeeid ?? 'employee') . '-' . time() . '.pdf';
            $pdfStoragePath = 'public/hike-letters/' . $pdfFileName;
            $fullPdfPath    = storage_path('app/' . $pdfStoragePath);

            $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
            $pdf->save($fullPdfPath);

            $pdfContent = file_get_contents($fullPdfPath);

            Mail::to($employee->email)->send(
                new HikeLetterPdfMail('Your Hike Letter', 'Please find attached your hike letter.', $pdfContent, $pdfFileName)
            );

            // Update history record to sent
            DB::table('hike_letter_history')
                ->where('id', $historyId)
                ->update([
                    'status'        => 'sent',
                    'error_message' => null,
                    'pdf_path'      => $pdfStoragePath,
                    'email_sent_at' => now(),
                    'updated_at'    => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Hike letter resent successfully.']);

        } catch (\Exception $e) {
            DB::table('hike_letter_history')
                ->where('id', $historyId)
                ->update([
                    'error_message' => $e->getMessage(),
                    'updated_at'    => now(),
                ]);

            return response()->json(['success' => false, 'message' => 'Failed to resend: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete hike letter history record
     */
    public function deleteHikeLetterHistory($id)
    {
        try {
            $record = DB::table('hike_letter_history')->where('id', $id)->first();

            if ($record && $record->pdf_path && file_exists(storage_path('app/' . $record->pdf_path))) {
                unlink(storage_path('app/' . $record->pdf_path));
            }

            DB::table('hike_letter_history')->where('id', $id)->delete();

            return redirect()->route('salary.hike-history')->with('success', 'Record deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('salary.hike-history')->with('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF from history
     */
    public function downloadHikeLetterPdf($id)
    {
        $record = DB::table('hike_letter_history')->where('id', $id)->first();

        if (!$record || !$record->pdf_path) {
            return redirect()->back()->with('error', 'PDF file not found.');
        }

        $filePath = storage_path('app/' . $record->pdf_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'PDF file not found on server.');
        }

        return response()->download($filePath, basename($filePath));
    }

    /**
     * Get company settings for PDF templates
     */
    private function getCompanySettings(): array
    {
        $generalSettings = DB::table('general_settings')->first();
        $logoSetting     = DB::table('logo_settings')->first();
        $logo            = $logoSetting->logo ?? null;

        $settings = [
            'companyName'    => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail'   => $generalSettings->contact_email ?? '',
            'companyPhone'   => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->contact_address ?? ($generalSettings->address ?? ''),
            'gm_name'        => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title'       => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo'           => $logo,
            'logoPath'       => null,
        ];

        if (!empty($logo)) {
            $fullLogoPath = public_path(ltrim($logo, '/'));
            if (file_exists($fullLogoPath)) {
                $settings['logoPath'] = $fullLogoPath;
            }
        }

        return $settings;
    }

    /**
     * Get letter signature data for PDF templates
     */
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

    /**
     * Get current salary details as an array for comparison
     */
    private function getCurrentSalaryDetails($salary)
    {
        return [
            'basic' => (float)($salary->basic ?? 0),
            'hra' => (float)($salary->hra ?? 0),
            'da' => (float)($salary->da ?? 0),
            'conveyance' => (float)($salary->conveyance ?? 0),
            'allowance' => (float)($salary->allowance ?? 0),
            'medical' => (float)($salary->medical ?? 0),
            'pf' => (float)($salary->pf ?? 0),
            'esi' => (float)($salary->esi ?? 0),
            'tds' => (float)($salary->tds ?? 0),
            'tax' => (float)($salary->tax ?? 0),
            'welfare' => (float)($salary->welfare ?? 0),
            'net_salary' => (float)($salary->net_salary ?? 0),
        ];
    }

    public function getEmployeesByDepartment(Request $request)
    {
        $departmentId = $request->input('department_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        // Get all active employees that HAVE a basic salary configured
        $employees = DB::table('allemployees')
            ->join('employee_salaries', 'allemployees.id', '=', 'employee_salaries.employee_id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->where('employee_salaries.basic', '>', 0)
            ->when($departmentId, function($query) use ($departmentId) {
                return $query->where('allemployees.department', $departmentId);
            })
            ->select(
                'allemployees.id',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.department',
                'department.department as department_name',
                'employee_salaries.basic as existing_basic',
                'employee_salaries.id as salary_id'
            )
            ->orderBy('allemployees.firstname')
            ->get();
        
        // Check which employees already have payslip for this month
        foreach ($employees as $employee) {
            $existingPayslip = DB::table('monthly_payslips')
                ->where('employee_id', $employee->id)
                ->whereYear('payroll_month', $year)
                ->whereMonth('payroll_month', $month)
                ->first();
            
            $employee->has_salary_for_month = !is_null($existingPayslip);
        }
        
        $departments = DB::table('department')
            ->where('deleted_at', 0)
            ->select('id', 'department')
            ->orderBy('department')
            ->get();
        
        return response()->json([
            'employees' => $employees,
            'departments' => $departments
        ]);
    }

    /**
     * Process salary for selected employees
     *
     * Pulls basic + components from employee_salaries, overtime, expense
     * reimbursement AND payroll "Add Addition" items, then writes a payslip.
     */
    public function processSalary(Request $request)
    {
        try {
            \Log::info('Process salary request received', $request->all());
     
            $month       = (int) $request->month;
            $year        = (int) $request->year;
            $employeeIds = $request->input('employee_ids');
     
            $employeeIds = is_array($employeeIds) ? array_filter($employeeIds) : [];
     
            if (empty($employeeIds)) {
                return response()->json([
                    'processed_count'         => 0,
                    'failed_count'            => 0,
                    'already_processed_count' => 0,
                    'message'                 => 'No employees selected',
                ]);
            }
     
            $results = ['processed' => [], 'failed' => [], 'already_processed' => []];
     
            foreach ($employeeIds as $employeeId) {
                try {
                    $payrollDate = sprintf('%04d-%02d-01', $year, $month);
     
                    // ── Already processed? ──
                    $existingPayslip = DB::table('monthly_payslips')
                        ->where('employee_id', $employeeId)
                        ->where('payroll_month', $payrollDate)
                        ->first();
     
                    if ($existingPayslip) {
                        $results['already_processed'][] = $employeeId;
                        continue;
                    }
     
                    // ── Employee (need employeeid string for expense lookup) ──
                    $employee = DB::table('allemployees')
                        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                        ->where('allemployees.id', $employeeId)
                        ->select('allemployees.*', 'designation.designation as designation_name')
                        ->first();
     
                    if (!$employee) {
                        $results['failed'][] = $employeeId;
                        continue;
                    }
     
                    // ── Salary record ──
                    $salary = DB::table('employee_salaries')
                        ->where('employee_id', $employeeId)
                        ->orderBy('id', 'desc')
                        ->first();
     
                    if (!$salary) {
                        \Log::warning("No salary record for employee {$employeeId}");
                        $results['failed'][] = $employeeId;
                        continue;
                    }
     
                    $basicSalary = floatval($salary->basic ?? 0);
                    if ($basicSalary <= 0) {
                        \Log::warning("No basic salary for employee {$employeeId}");
                        $results['failed'][] = $employeeId;
                        continue;
                    }
     
                    // ── Earnings components ──
                    $daAmount   = floatval($salary->da         ?? 0);
                    $hraAmount  = floatval($salary->hra        ?? 0);
                    $conveyance = floatval($salary->conveyance ?? 0);
                    $allowance  = floatval($salary->allowance  ?? 0);
                    $medical    = floatval($salary->medical    ?? 0);
                    $welfare    = floatval($salary->welfare    ?? 0);
     
                    // ── Deduction components ──
                    $pfAmount  = floatval($salary->pf  ?? 0);
                    $esiAmount = floatval($salary->esi ?? 0);
                    $tds       = floatval($salary->tds ?? 0);
                    $tax       = floatval($salary->tax ?? 0);
                    $empLeave  = floatval($salary->employee_leave ?? 0);
     
                    // ── Overtime ──
                    $overtimeData = $this->fetchOvertimeData($employeeId, $month, $year);
     
                    // ── Gross for LOP calculation ──
                    $grossForLop = $basicSalary + $daAmount + $hraAmount
                                 + $conveyance + $allowance + $medical + $welfare;
     
                    // ── Attendance / leave / LOP ──
                    $calc = $this->buildAttendanceLeaveLop($employeeId, $month, $year, $grossForLop);
     
                    // ── EXPENSE REIMBURSEMENT ──
                    // expenses.employee_id stores the employeeid string (e.g. 'EMP001')
                    $expenseReimbursement = 0.0;
                    if (!empty($employee->employeeid)) {
                        $expenseReimbursement = (float) DB::table('expenses')
                            ->where('employee_id', $employee->employeeid)
                            ->where('status', 'approved')
                            ->whereMonth('departure_date', $month)
                            ->whereYear('departure_date', $year)
                            ->sum('expense_amount');
                    }
     
                    \Log::info("Expense reimbursement for {$employee->employeeid}: {$expenseReimbursement}");
     
                    // ── PAYROLL ADDITIONS (assigned via "Add Addition") ──
                    // additions.employee_id may hold the numeric id, the employeeid
                    // string, or 'all' — match any of those so nothing is missed.
                    $additionRecords = DB::table('additions')
                        ->where(function ($q) use ($employeeId, $employee) {
                            $q->where('employee_id', $employeeId)
                              ->orWhere('employee_id', $employee->employeeid)
                              ->orWhere('employee_id', 'all');
                        })
                        ->get();
     
                    $additionsTotal       = 0.0;
                    $payrollAdditionItems = [];
                    foreach ($additionRecords as $add) {
                        $amt = (float) ($add->unit_amount ?? 0);
                        if ($amt == 0) continue;
                        $additionsTotal += $amt;
                        $payrollAdditionItems[] = [
                            'name'     => $add->name,
                            'category' => $add->category ?? 'Addition',
                            'amount'   => $amt,
                        ];
                    }
     
                    \Log::info("Payroll additions for {$employee->employeeid}: {$additionsTotal}");
     
                    // ── Totals ──
                    $totalEarnings = $grossForLop
                                   + $overtimeData['total_amount']
                                   + $expenseReimbursement
                                   + $additionsTotal;
     
                    $totalDeductions = $pfAmount + $esiAmount + $tds + $tax + $empLeave
                                     + $calc['lop_deduction_amount'];
     
                    $netSalary = max(0, $totalEarnings - $totalDeductions);
     
                    // ── additions_data items: expense reimbursement + payroll additions ──
                    // show.blade / payslip pages read $addition['name'], ['category'], ['amount']
                    $additionsItems = [];
                    if ($expenseReimbursement > 0) {
                        $additionsItems[] = [
                            'name'     => 'Expense Reimbursement',
                            'category' => 'Reimbursement',
                            'amount'   => $expenseReimbursement,
                        ];
                    }
                    $additionsItems = array_merge($additionsItems, $payrollAdditionItems);
     
                    // ── Insert payslip ──
                    $payslipId = DB::table('monthly_payslips')->insertGetId([
                        'employee_id'               => intval($employeeId),
                        'payroll_month'             => $payrollDate,
     
                        // Earnings
                        'basic_salary'              => $basicSalary,
                        'hra'                       => (string) $hraAmount,
                        'da'                        => (string) $daAmount,
                        'conveyance'                => $conveyance,
                        'allowance'                 => $allowance,
                        'medical'                   => $medical,
                        'welfare'                   => $welfare,
                        'overtime_amount'           => $overtimeData['total_amount'],
                        'dynamic_additions'         => $expenseReimbursement + $additionsTotal,
                        'total_earnings'            => $totalEarnings,
     
                        // Deductions
                        'tds'                       => $tds,
                        'pf'                        => $pfAmount,
                        'esi'                       => $esiAmount,
                        'tax'                       => $tax,
                        'lop_deduction'             => $calc['lop_deduction_amount'],
                        'lop_days'                  => $calc['lop_days'],
                        'employee_leave_deduction'  => $empLeave,
                        'unpaid_leave_deduction'    => 0,
                        'dynamic_deductions'        => 0,
                        'total_deductions'          => $totalDeductions,
     
                        // Net
                        'net_salary'                => $netSalary,
     
                        // Attendance
                        'total_working_days'        => $calc['expected_working_days'],
                        'actual_working_days'       => $calc['actual_working_days'],
                        'total_hours_worked'        => $calc['total_hours'],
                        'overtime_hours'            => $overtimeData['total_hours'],
                        'late_arrivals'             => $calc['late_arrivals'],
                        'early_departures'          => $calc['early_departures'],
     
                        // Leave
                        'leave_days_taken'          => $calc['total_leave_days'],
                        'unpaid_leave_days'         => $calc['unpaid_leave_days'],
     
                        // JSON blobs
                        'attendance_data'           => json_encode([
                            'total_hours'      => $calc['total_hours'],
                            'working_days'     => $calc['actual_working_days'],
                            'present_days'     => $calc['actual_working_days'],
                            'late_arrivals'    => $calc['late_arrivals'],
                            'early_departures' => $calc['early_departures'],
                        ]),
                        'leave_data'                => json_encode([
                            'total_leave_days'  => $calc['total_leave_days'],
                            'paid_leave_days'   => $calc['paid_leave_days'],
                            'unpaid_leave_days' => $calc['unpaid_leave_days'],
                            'leave_types'       => $calc['leave_types'],
                        ]),
                        'overtime_data'             => json_encode($overtimeData),
     
                        // additions_data: expense reimbursement + "Add Addition" items
                        'additions_data'            => json_encode([
                            'items' => $additionsItems,
                            'total' => $expenseReimbursement + $additionsTotal,
                        ]),
     
                        'deductions_data'           => json_encode(['items' => [], 'total' => 0]),
                        'lop_data'                  => json_encode([
                            'lop_days'              => $calc['lop_days'],
                            'lop_deduction_amount'  => $calc['lop_deduction_amount'],
                            'per_day_salary'        => $calc['per_day_salary'],
                            'expected_working_days' => $calc['expected_working_days'],
                            'present_days'          => $calc['actual_working_days'],
                            'paid_leave_days'       => $calc['paid_leave_days'],
                            'unpaid_leave_days'     => $calc['unpaid_leave_days'],
                            'absent_days'           => $calc['absent_days'],
                            'holiday_days'          => $calc['holiday_days'],
                            'off_days'              => $calc['off_days'],
                            'total_calendar_days'   => $calc['total_calendar_days'],
                            'holidays'              => $calc['holidays'],
                        ]),
     
                        // Status
                        'status'                    => 'generated',
                        'email_sent'                => 0,
                        'generated_at'              => now()->format('Y-m-d H:i:s'),
                        'created_at'                => now(),
                        'updated_at'                => now(),
                    ]);
     
                    $results['processed'][] = $employeeId;
                    \Log::info("Payslip generated", [
                        'employee_id'          => $employeeId,
                        'payslip_id'           => $payslipId,
                        'expense_reimbursement'=> $expenseReimbursement,
                        'additions_total'      => $additionsTotal,
                    ]);
     
                    PayrollActivityLogger::logGenerate(
                        'Automated Payslips',
                        "Payslip generated for {$employee->firstname} {$employee->lastname} — "
                            . date('F Y', mktime(0, 0, 0, $month, 1, $year))
                            . (($expenseReimbursement + $additionsTotal) > 0
                                ? " (incl. additions: ₹" . number_format($expenseReimbursement + $additionsTotal, 2) . ")"
                                : ""),
                        $employeeId,
                        [
                            'month'                => $month,
                            'year'                 => $year,
                            'net_salary'           => $netSalary,
                            'payslip_id'           => $payslipId,
                            'expense_reimbursement'=> $expenseReimbursement,
                            'additions_total'      => $additionsTotal,
                        ]
                    );
     
                } catch (\Exception $e) {
                    $results['failed'][] = $employeeId;
                    \Log::error("Payslip generation failed for employee {$employeeId}", [
                        'error' => $e->getMessage(),
                        'line'  => $e->getLine(),
                        'file'  => $e->getFile(),
                    ]);
                }
            }
     
            return response()->json([
                'processed_count'         => count($results['processed']),
                'failed_count'            => count($results['failed']),
                'already_processed_count' => count($results['already_processed']),
                'processed'               => $results['processed'],
                'failed'                  => $results['failed'],
                'already_processed'       => $results['already_processed'],
            ]);
     
        } catch (\Exception $e) {
            \Log::error("Process salary fatal error", ['error' => $e->getMessage()]);
            return response()->json([
                'error'                   => true,
                'message'                 => $e->getMessage(),
                'processed_count'         => 0,
                'failed_count'            => 0,
                'already_processed_count' => 0,
            ]);
        }
    }

    /**
     * Re-process salary for held employees
     */
    public function reprocessHeldSalary(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);
        
        $month = $request->month;
        $year = $request->year;
        
        $heldSalaries = DB::table('employee_salaries')
            ->where('approval_status', 'hold')
            ->orWhere('release_status', 'hold')
            ->get();
        
        $results = [
            'processed' => [],
            'failed' => []
        ];
        
        foreach ($heldSalaries as $salary) {
            try {
                $salaryData = $this->calculateEmployeeSalary($salary->employee_id, $month, $year);
                
                if ($salaryData) {
                    DB::table('employee_salaries')
                        ->where('id', $salary->id)
                        ->update(array_merge($salaryData, [
                            'approval_status' => 'approved',
                            'updated_at' => now()
                        ]));
                    
                    $results['processed'][] = $salary->employee_id;
                } else {
                    $results['failed'][] = $salary->employee_id;
                }
            } catch (\Exception $e) {
                $results['failed'][] = $salary->employee_id;
                \Log::error("Salary reprocessing failed: " . $e->getMessage());
            }
        }
        
        $message = sprintf("Reprocessed: %d, Failed: %d", count($results['processed']), count($results['failed']));
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Calculate salary for a specific employee
     */
    private function calculateEmployeeSalary($employeeId, $month, $year)
    {
        try {
            $employee = DB::table('allemployees')->where('id', $employeeId)->first();
            if (!$employee) {
                \Log::error("Employee not found: {$employeeId}");
                return null;
            }
            
            $salaryConfig = DB::table('salary_master_config')->first();
            if (!$salaryConfig) {
                \Log::error("Salary master config not found");
                return null;
            }
            
            $existingSalary = DB::table('employee_salaries')
                ->where('employee_id', $employeeId)
                ->orderBy('id', 'desc')
                ->first();
            
            $basicSalary = $existingSalary ? $existingSalary->basic : 0;
            
            if ($basicSalary <= 0) {
                \Log::warning("No basic salary found for employee: {$employeeId}");
                return null;
            }
            
            $daPercentage = $salaryConfig->da_percentage ?? 0;
            $hraPercentage = $salaryConfig->hra_percentage ?? 0;
            $pfPercentage = $salaryConfig->pf_percentage ?? 0;
            $esiPercentage = $salaryConfig->esi_percentage ?? 0;
            
            $daAmount = ($daPercentage / 100) * $basicSalary;
            $hraAmount = ($hraPercentage / 100) * $basicSalary;
            $pfAmount = ($pfPercentage / 100) * $basicSalary;
            $esiAmount = ($esiPercentage / 100) * $basicSalary;
            
            $conveyance = $salaryConfig->conveyance ?? 0;
            $allowance = $salaryConfig->special_allowance ?? 0;
            $medical = $salaryConfig->medical_allowance ?? 0;
            $tds = $salaryConfig->tds ?? 0;
            $tax = $salaryConfig->professional_tax ?? 0;
            $welfare = $salaryConfig->welfare_fund ?? 0;
            
            $totalEarnings = $basicSalary + $daAmount + $hraAmount + $conveyance + $allowance + $medical;
            $totalDeductions = $pfAmount + $esiAmount + $tds + $tax + $welfare;
            $netSalary = max(0, $totalEarnings - $totalDeductions);
            $grossSalary = $totalEarnings;
            
            return [
                'basic' => $basicSalary,
                'da' => $daAmount,
                'hra' => $hraAmount,
                'pf' => $pfAmount,
                'esi' => $esiAmount,
                'conveyance' => $conveyance,
                'allowance' => $allowance,
                'medical' => $medical,
                'tds' => $tds,
                'tax' => $tax,
                'welfare' => $welfare,
                'net_salary' => $netSalary,
                'gross_salary' => $grossSalary,
                'employee_leave' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error calculating salary for employee {$employeeId}: " . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers: real data fetchers used by processSalary()
    // ─────────────────────────────────────────────────────────────

    private function fetchAttendanceData($employeeId, $month, $year): array
    {
        $records = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('punch_out')
            ->get();

        $totalHours     = 0;
        $workingDays    = 0;
        $lateArrivals   = 0;
        $earlyDepartures = 0;

        foreach ($records as $r) {
            $totalHours += floatval($r->working_hours ?? 0);
            $workingDays++;
            if (($r->status ?? '') === 'late')            $lateArrivals++;
            if (($r->status ?? '') === 'early_departure') $earlyDepartures++;
        }

        return [
            'total_hours'     => $totalHours,
            'working_days'    => $workingDays,
            'late_arrivals'   => $lateArrivals,
            'early_departures'=> $earlyDepartures,
        ];
    }

    private function fetchLeaveData($employeeId, $month, $year): array
    {
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($q) use ($month, $year) {
                $q->where(function ($q2) use ($month, $year) {
                    $q2->whereMonth('from_date', $month)->whereYear('from_date', $year);
                })->orWhere(function ($q2) use ($month, $year) {
                    $q2->whereMonth('to_date', $month)->whereYear('to_date', $year);
                });
            })
            ->get();

        $totalLeaveDays  = 0;
        $unpaidLeaveDays = 0;
        $leaveTypes      = [];
        $paidTypes       = ['Annual Leave', 'Sick Leave', 'Casual Leave', 'Medical Leave'];

        foreach ($leaves as $leave) {
            $days = intval($leave->no_of_days ?? 0);
            $totalLeaveDays += $days;
            $isPaid = in_array($leave->leave_type, $paidTypes);
            if (!$isPaid) $unpaidLeaveDays += $days;

            $leaveTypes[] = [
                'type'      => $leave->leave_type,
                'days'      => $days,
                'from_date' => $leave->from_date,
                'to_date'   => $leave->to_date,
                'is_paid'   => $isPaid,
            ];
        }

        return [
            'total_leave_days'  => $totalLeaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'leave_types'       => $leaveTypes,
        ];
    }

    private function fetchOvertimeData($employeeId, $month, $year): array
    {
        $records = DB::table('employee_overtime')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereMonth('overtime_date', $month)
            ->whereYear('overtime_date', $year)
            ->get();

        $totalHours  = 0;
        $totalAmount = 0;

        foreach ($records as $r) {
            $totalHours  += floatval($r->overtime_hours  ?? 0);
            $totalAmount += floatval($r->overtime_amount ?? 0);
        }

        return [
            'total_hours'  => $totalHours,
            'total_amount' => $totalAmount,
            'records'      => $records,
        ];
    }

    private function fetchLopData($employeeId, $month, $year, float $basicSalary): array
    {
        $payrollMonth = sprintf('%04d-%02d', $year, $month);

        $lopRecord = DB::table('employee_lop_records')
            ->where('employee_id', $employeeId)
            ->where('month', $payrollMonth)
            ->first();

        $lopDays            = $lopRecord ? intval($lopRecord->lop_days) : 0;
        $lopDeductionAmount = 0;

        if ($lopDays > 0 && $basicSalary > 0) {
            $totalDays          = $this->getCalendarDaysInMonth($month, $year);
            $perDay             = $totalDays > 0 ? ($basicSalary / $totalDays) : 0;
            $lopDeductionAmount = round($lopDays * $perDay, 2);
        }

        return [
            'lop_days'             => $lopDays,
            'lop_deduction_amount' => $lopDeductionAmount,
            'lop_record'           => $lopRecord,
        ];
    }

    private function getCalendarDaysInMonth($month, $year): int
    {
        return (int) \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
    }

    private function computePayrollDays($employeeId, $month, $year): array
    {
        $start = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
     
        // ── Working weekdays from the employee's shift(s) ──
        $shiftDays = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.employee_id', $employeeId)
            ->where('schedule.deleted_at', 0)
            ->pluck('shifts.days_of_week');
     
        $allowedDays = [];
        foreach ($shiftDays as $dow) {
            foreach (array_map('trim', explode(',', (string) $dow)) as $d) {
                if ($d !== '') {
                    $allowedDays[strtolower($d)] = true;
                }
            }
        }
        // Fallback: Mon–Sat if the employee has no schedule configured
        if (empty($allowedDays)) {
            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $d) {
                $allowedDays[$d] = true;
            }
        }
     
        // ── Company holidays in this month ──
        $holidayRows = DB::table('holidays')
            ->whereBetween('holidaydate', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();
     
        $holidayMap = [];
        foreach ($holidayRows as $h) {
            $holidayMap[\Carbon\Carbon::parse($h->holidaydate)->format('Y-m-d')] = $h->title;
        }
     
        // ── Count expected working days ──
        $expectedWorkingDays = 0;
        $offDays             = 0;
        $holidayDays         = 0;
     
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $short = strtolower($d->format('D'));
            $full  = strtolower($d->format('l'));
     
            $isWorkingWeekday = isset($allowedDays[$short]) || isset($allowedDays[$full]);
     
            if (!$isWorkingWeekday) {
                $offDays++;
                continue;
            }
            if (isset($holidayMap[$d->format('Y-m-d')])) {
                $holidayDays++;
                continue;
            }
            $expectedWorkingDays++;
        }
     
        return [
            'allowed_days'          => $allowedDays,
            'holiday_map'           => $holidayMap,
            'expected_working_days' => $expectedWorkingDays,
            'off_days'              => $offDays,
            'holiday_days'          => $holidayDays,
            'total_calendar_days'   => $start->daysInMonth,
        ];
    }
     
    /**
     * Count how many WORKING days of a leave fall inside the given month
     */
    private function countLeaveWorkingDays($fromDate, $toDate, $month, $year, array $allowedDays, array $holidayMap): int
    {
        $monthStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();
     
        $from = \Carbon\Carbon::parse($fromDate);
        $to   = \Carbon\Carbon::parse($toDate);
     
        $start = $from->greaterThan($monthStart) ? $from->copy() : $monthStart->copy();
        $endD  = $to->lessThan($monthEnd)        ? $to->copy()   : $monthEnd->copy();
     
        if ($start->gt($endD)) {
            return 0;
        }
     
        $count = 0;
        for ($d = $start->copy(); $d->lte($endD); $d->addDay()) {
            $short = strtolower($d->format('D'));
            $full  = strtolower($d->format('l'));
            if (!(isset($allowedDays[$short]) || isset($allowedDays[$full]))) continue;
            if (isset($holidayMap[$d->format('Y-m-d')]))                       continue;
            $count++;
        }
        return $count;
    }
     
    /**
     * Master calculator: working days + present days + paid/unpaid leaves + LOP + deduction.
     */
    private function buildAttendanceLeaveLop($employeeId, $month, $year, float $grossMonthly): array
    {
        $cal         = $this->computePayrollDays($employeeId, $month, $year);
        $allowedDays = $cal['allowed_days'];
        $holidayMap  = $cal['holiday_map'];
        $expected    = $cal['expected_working_days'];
     
        // ── Present working days ──
        $presentDates = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('punch_out')
            ->selectRaw('DATE(date) as d')
            ->distinct()
            ->pluck('d');
     
        $presentDays = 0;
        foreach ($presentDates as $dateVal) {
            $d     = \Carbon\Carbon::parse($dateVal);
            $short = strtolower($d->format('D'));
            $full  = strtolower($d->format('l'));
            if (!(isset($allowedDays[$short]) || isset($allowedDays[$full]))) continue;
            if (isset($holidayMap[$d->format('Y-m-d')]))                       continue;
            $presentDays++;
        }
     
        // ── Hours / late / early ──
        $records = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('punch_out')
            ->get();
     
        $totalHours = 0; $late = 0; $early = 0;
        foreach ($records as $r) {
            $totalHours += floatval($r->working_hours ?? 0);
            if (($r->status ?? '') === 'late')            $late++;
            if (($r->status ?? '') === 'early_departure') $early++;
        }
     
        // ── Approved leaves overlapping the month ──
        $leaves = DB::table('employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($q) use ($month, $year) {
                $q->where(function ($q2) use ($month, $year) {
                    $q2->whereMonth('from_date', $month)->whereYear('from_date', $year);
                })->orWhere(function ($q2) use ($month, $year) {
                    $q2->whereMonth('to_date', $month)->whereYear('to_date', $year);
                });
            })
            ->get();
     
        $paidLeaveDays   = 0;
        $unpaidLeaveDays = 0;
        $leaveTypes      = [];
     
        foreach ($leaves as $lv) {
            $daysInMonth = $this->countLeaveWorkingDays(
                $lv->from_date, $lv->to_date, $month, $year, $allowedDays, $holidayMap
            );
            if ($daysInMonth <= 0) continue;
     
            $isUnpaid = ($lv->leave_type === 'LOP')
                        || (isset($lv->type) && strtolower($lv->type) === 'unpaid');
     
            if ($isUnpaid) $unpaidLeaveDays += $daysInMonth;
            else           $paidLeaveDays   += $daysInMonth;
     
            $leaveTypes[] = [
                'type'      => $lv->leave_type,
                'days'      => $daysInMonth,
                'from_date' => $lv->from_date,
                'to_date'   => $lv->to_date,
                'is_paid'   => !$isUnpaid,
            ];
        }
     
        // ── LOP ──
        $accounted  = $presentDays + $paidLeaveDays + $unpaidLeaveDays;
        $absentDays = max(0, $expected - $accounted);
        $lopDays    = $unpaidLeaveDays + $absentDays;
     
        $perDay        = $expected > 0 ? round($grossMonthly / $expected, 2) : 0;
        $lopDeduction  = round($lopDays * $perDay, 2);
     
        return [
            'expected_working_days' => $expected,
            'actual_working_days'   => $presentDays,
            'paid_leave_days'       => $paidLeaveDays,
            'unpaid_leave_days'     => $unpaidLeaveDays,
            'total_leave_days'      => $paidLeaveDays + $unpaidLeaveDays,
            'absent_days'           => $absentDays,
            'lop_days'              => $lopDays,
            'per_day_salary'        => $perDay,
            'lop_deduction_amount'  => $lopDeduction,
            'holiday_days'          => $cal['holiday_days'],
            'off_days'              => $cal['off_days'],
            'total_calendar_days'   => $cal['total_calendar_days'],
            'leave_types'           => $leaveTypes,
            'total_hours'           => $totalHours,
            'late_arrivals'         => $late,
            'early_departures'      => $early,
            'holidays'              => $holidayMap,
        ];
    }
}