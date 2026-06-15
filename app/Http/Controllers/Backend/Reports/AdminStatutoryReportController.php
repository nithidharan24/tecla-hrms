<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminStatutoryReportController extends Controller
{
    public function index()
    {
        // Filters
        $month = request('month');

        // Fetch all months for dropdown
        $months = DB::table('monthly_payslips')
            ->select('payroll_month')
            ->groupBy('payroll_month')
            ->orderBy('payroll_month', 'DESC')
            ->get();

        // Fetch active statutory percentages
        $statutory = DB::table('statutory_rates')->where('is_active', 1)->first();

        if (!$statutory) {
            return back()->with('error', 'Active PF/ESI settings not found.');
        }

        // Base query
        $query = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->select(
                'monthly_payslips.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.department',
                'allemployees.branch_id'
            );

        if ($month) {
            $query->whereRaw("DATE(payroll_month) = DATE(?)", [$month]);
        }

        $records = $query->orderBy('payroll_month', 'DESC')->get();

        // Attach employer PF & ESI dynamically
        foreach ($records as $r) {
            $r->pf_employer = round(($r->basic_salary * $statutory->pf_employer_rate) / 100, 2);
            $r->esi_employer = round(($r->total_earnings * $statutory->esi_employer_rate) / 100, 2);
        }

        return view('hrms.hr.Reports.statutory-reports.index', compact('months', 'records'));
    }

    // ================== EXPORT TO EXCEL ===================
    public function export()
    {
        $month = request('month');

        $statutory = DB::table('statutory_rates')->where('is_active', 1)->first();

        $records = DB::table('monthly_payslips')
            ->join('allemployees', 'monthly_payslips.employee_id', '=', 'allemployees.id')
            ->select(
                'monthly_payslips.*',
                'allemployees.firstname',
                'allemployees.lastname'
            )
            ->when($month, fn($q) => $q->whereRaw("DATE(payroll_month) = DATE(?)", [$month]))
            ->orderBy('payroll_month', 'DESC')
            ->get();

        $filename = "PF_ESI_Report_" . ($month ?: 'all') . ".csv";

        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Employee Name',
            'Payroll Month',
            'Basic Salary',
            'Gross Salary',
            'PF Employee',
            'PF Employer',
            'ESI Employee',
            'ESI Employer',
            'Net Salary'
        ]);

        // Rows
        foreach ($records as $r) {
            $pf_employer = round(($r->basic_salary * $statutory->pf_employer_rate) / 100, 2);
            $esi_employer = round(($r->total_earnings * $statutory->esi_employer_rate) / 100, 2);

            fputcsv($handle, [
                $r->firstname . ' ' . $r->lastname,
                $r->payroll_month,
                $r->basic_salary,
                $r->total_earnings,
                $r->pf,
                $pf_employer,
                $r->esi,
                $esi_employer,
                $r->net_salary
            ]);
        }

        rewind($handle);

        return Response::make(stream_get_contents($handle), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename"
        ]);
    }
}
