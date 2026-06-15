<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EmployeeSalaryreportController extends Controller
{
    /**
     * Display a listing of the employee salary reports.
     *
     * @return \Illuminate\Http\Response
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
            ) // Removed the comma here
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->whereYear('monthly_payslips.payroll_month', $year);

        if ($employeeId) {
            $query->where('monthly_payslips.employee_id', $employeeId);
        }

        $payslips = $query->orderBy('monthly_payslips.payroll_month', 'desc')
                         ->orderBy('allemployees.firstname', 'asc')
                         ->get();

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('firstname')
            ->get();

        return view('hrms.hr.Reports.employeesalary-reports.index', compact(
            'payslips', 'employees', 'month', 'year'
        ));
    }

    /**
     * Export employee salary report to CSV.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportCsv(Request $request)
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
                'designation.designation as designation_name',
                DB::raw("DATE_FORMAT(monthly_payslips.payroll_month, '%M %Y') as payroll_month_formatted")
            )
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->whereYear('monthly_payslips.payroll_month', $year);

        if ($employeeId) {
            $query->where('monthly_payslips.employee_id', $employeeId);
        }

        $payslips = $query->orderBy('monthly_payslips.payroll_month', 'desc')
                         ->orderBy('allemployees.firstname', 'asc')
                         ->get();

        $filename = 'employee_salary_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payslips) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'S.No', 'Employee ID', 'Employee Name', 'Designation', 'Payroll Month',
                'Basic Salary', 'HRA', 'DA', 'Conveyance', 'Allowance', 'Medical', 'Welfare',
                'Overtime Amount', 'Dynamic Additions', 'Total Earnings', 'TDS', 'PF', 'ESI', 'Tax',
                'LOP Deduction', 'Dynamic Deductions', 'Total Deductions', 'Net Salary', 'Status'
            ]); // CSV Headers

            foreach ($payslips as $key => $payslip) {
                fputcsv($file, [
                    $key + 1,
                    $payslip->employeeid,
                    $payslip->firstname . ' ' . $payslip->lastname,
                    $payslip->designation_name,
                    $payslip->payroll_month_formatted,
                    $payslip->basic_salary,
                    $payslip->hra,
                    $payslip->da,
                    $payslip->conveyance,
                    $payslip->allowance,
                    $payslip->medical,
                    $payslip->welfare,
                    $payslip->overtime_amount,
                    $payslip->dynamic_additions,
                    $payslip->total_earnings,
                    $payslip->tds,
                    $payslip->pf,
                    $payslip->esi,
                    $payslip->tax,
                    $payslip->lop_deduction,
                    $payslip->dynamic_deductions,
                    $payslip->total_deductions,
                    $payslip->net_salary,
                    ucfirst($payslip->status)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export employee salary report to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
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
                'designation.designation as designation_name',
                DB::raw("DATE_FORMAT(monthly_payslips.payroll_month, '%M %Y') as payroll_month_formatted")
            )
            ->whereMonth('monthly_payslips.payroll_month', $month)
            ->whereYear('monthly_payslips.payroll_month', $year);

        if ($employeeId) {
            $query->where('monthly_payslips.employee_id', $employeeId);
        }

        $payslips = $query->orderBy('monthly_payslips.payroll_month', 'desc')
                         ->orderBy('allemployees.firstname', 'asc')
                         ->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.employeesalary-reports.pdf_report', compact('payslips', 'month', 'year'));
        return $pdf->download('employee_salary_report_' . date('Ymd_His') . '.pdf');
    }
}


