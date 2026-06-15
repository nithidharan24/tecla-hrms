<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\PDF;

class PayslipReportController extends Controller
{
    public function index(Request $request)
    {
        $payslips = $this->getFilteredPayslips($request);
        return view('hrms.hr.Reports.payslip-report.index', compact('payslips'));
    }

    public function exportCSV(Request $request)
    {
        $payslips = $this->getFilteredPayslips($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payslip_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($payslips) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'S.No',
                'Employee Name',
                'Basic Salary',
                'HRA',
                'Conveyance',
                'TDS',
                'PF',
                'Net Salary',
                'Payment Month',
                'Payment Year'
            ]);

            // Add data rows
            foreach ($payslips as $index => $payslip) {
                fputcsv($file, [
                    $index + 1,
                    $payslip->full_name,
                    '$' . number_format($payslip->basic, 2),
                    '$' . number_format($payslip->hra, 2),
                    '$' . number_format($payslip->conveyance, 2),
                    '$' . number_format($payslip->tds, 2),
                    '$' . number_format($payslip->pf, 2),
                    '$' . number_format($payslip->net_salary, 2),
                    date('M', strtotime($payslip->created_at)),
                    date('Y', strtotime($payslip->created_at))
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $payslips = $this->getFilteredPayslips($request);

        $data = [
            'title' => 'Payslip Report - ' . date('Y-m-d'),
            'payslips' => $payslips,
            'filters' => [
                'employee_name' => $request->employee_name,
                'month' => $request->month ? date('F', mktime(0, 0, 0, $request->month, 1)) : null,
                'year' => $request->year
            ]
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.payslip-report.pdf', $data);
        
        return $pdf->download('payslip_report_' . date('Y-m-d') . '.pdf');
    }

   private function getFilteredPayslips(Request $request)
{
    $query = DB::table('employee_salaries')
        ->join('allemployees', 'employee_salaries.employee_id', '=', 'allemployees.id')
        ->select(
            'employee_salaries.id',
            'employee_salaries.employee_id',
            DB::raw("CAST(employee_salaries.basic AS DECIMAL(10,2)) as basic"),
            DB::raw("CAST(employee_salaries.hra AS DECIMAL(10,2)) as hra"),
            DB::raw("CAST(employee_salaries.conveyance AS DECIMAL(10,2)) as conveyance"),
            DB::raw("CAST(employee_salaries.tds AS DECIMAL(10,2)) as tds"),
            DB::raw("CAST(employee_salaries.pf AS DECIMAL(10,2)) as pf"),
            DB::raw("CAST(employee_salaries.net_salary AS DECIMAL(10,2)) as net_salary"),
            'employee_salaries.created_at',
            DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as full_name"),
            'allemployees.profile_image'
        );

    // Rest of your filtering logic remains the same
    if ($request->filled('employee_name')) {
        $query->where(DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname)"), 'like', '%' . $request->employee_name . '%');
    }

    if ($request->filled('month')) {
        $query->whereMonth('employee_salaries.created_at', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('employee_salaries.created_at', $request->year);
    }

    return $query->get();
}

    // Keep your existing downloadPdf method for individual payslips
   // PDF download method
    
}