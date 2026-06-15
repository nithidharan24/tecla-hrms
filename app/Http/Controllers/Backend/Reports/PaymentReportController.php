<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PDF;

class PaymentReportController extends Controller
{
    public function index(Request $request)
    {
        $payments = $this->getFilteredPayments($request);
        return view('hrms.hr.Reports.payments-reports.index', compact('payments'));
    }

    public function exportCSV(Request $request)
    {
        $payments = $this->getFilteredPayments($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Payment ID',
                'Invoice ID',
                'Client Name',
                'Payment Date',
                'Payment Amount',
                'Payment Method',
                'Invoice Total',
                'Total Paid',
                'Remaining Amount',
                'Status',
                'Notes'
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_id,
                    $payment->invoice_id,
                    $payment->client_name,
                    \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y'),
                    '$' . number_format($payment->amount, 2),
                    $payment->payment_method,
                    '$' . number_format($payment->invoice_total, 2),
                    '$' . number_format($payment->total_paid, 2),
                    '$' . number_format($payment->remaining_amount, 2),
                    ucfirst(str_replace('_', ' ', $payment->status)),
                    $payment->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $payments = $this->getFilteredPayments($request);

        $data = [
            'title' => 'Payments Report - ' . date('Y-m-d'),
            'payments' => $payments,
            'date_range' => $this->getDateRangeText($request)
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.payments-reports.pdf', $data);
        
        return $pdf->download('payments_report_' . date('Y-m-d') . '.pdf');
    }

    private function getFilteredPayments(Request $request)
    {
        $branchId = getAdminBranchFilter();
        
        $query = DB::table('invoice_payments as p')
            ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
            ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
            ->select(
                'p.payment_id',
                'p.invoice_id',
                'p.amount',
                'p.payment_date',
                'p.payment_method',
                'p.notes',
                'i.grant_amt as invoice_total',
                'i.status',
                DB::raw("(SELECT SUM(amount) FROM invoice_payments WHERE invoice_id = i.invoice_id) as total_paid"),
                DB::raw("(i.grant_amt - (SELECT SUM(amount) FROM invoice_payments WHERE invoice_id = i.invoice_id)) as remaining_amount"),
                DB::raw("CASE 
                    WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE i.client 
                END AS client_name")
            );

        if ($branchId) {
            $query->where('p.branch_id', $branchId);
        }

        if ($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)) {
            $fromDate = date('Y-m-d', strtotime($request->from));
            $toDate = date('Y-m-d', strtotime($request->to));

            $query->whereDate('p.payment_date', '>=', $fromDate)
                  ->whereDate('p.payment_date', '<=', $toDate);
        }

        return $query->orderBy('p.payment_date', 'desc')->get();
    }

    private function getDateRangeText(Request $request)
    {
        if ($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)) {
            return 'From ' . date('d-m-Y', strtotime($request->from)) . ' to ' . date('d-m-Y', strtotime($request->to));
        }
        return 'All Payments';
    }
}
