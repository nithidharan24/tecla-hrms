<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        // Validate request
        $request->validate([
            'client' => 'nullable|exists:clients,client_id',
            'from_date' => 'nullable|date_format:d-m-Y',
            'to_date' => 'nullable|date_format:d-m-Y',
            'status' => 'nullable|in:sent,paid,partially paid'
        ]);

        // Fetch clients for the select dropdown
        $clients = DB::table('clients')
            ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"), 'client_id as id')
            ->orderBy('first_name')
            ->get();
    
        // Get filtered invoices
        $invoices = $this->getFilteredInvoices($request);
    
        return view('hrms.hr.Reports.invoice-reports.index', compact('invoices', 'clients'));
    }
    
    public function destroy($id)
    {
        try {
            $invoice = DB::table('invoices')->where('id', $id)->first();

            if ($invoice) {
                DB::table('invoices')->where('id', $id)->delete();
                return redirect()->route('invoice-reports.index')->with('success', 'Invoice deleted successfully.');
            } else {
                return redirect()->route('invoice-reports.index')->with('error', 'Invoice not found.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting invoice: ' . $e->getMessage());
            return redirect()->route('invoice-reports.index')->with('error', 'Error deleting invoice.');
        }
    }

    public function exportCSV(Request $request)
    {
        try {
            $invoices = $this->getFilteredInvoices($request);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="invoice_report_' . date('d-m-Y') . '.csv"',
            ];

            $callback = function() use ($invoices) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'S.No',
                    'Invoice Number',
                    'Client',
                    'Invoice Date',
                    'Due Date',
                    'Amount',
                    'Status'
                ]);

                // Add data rows
                foreach ($invoices as $index => $invoice) {
                    fputcsv($file, [
                        $index + 1,
                        $invoice->invoice_id ?? 'N/A',
                        $invoice->client_name ?? 'N/A',
                        $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('d-m-Y') : 'N/A',
                        $invoice->due_date ? Carbon::parse($invoice->due_date)->format('d-m-Y') : 'N/A',
                        '$' . number_format((float)$invoice->grant_amt, 2),
                        ucfirst($invoice->status ?? 'N/A')
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting CSV: ' . $e->getMessage());
            return redirect()->route('invoice-reports.index')->with('error', 'Error exporting CSV file.');
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $invoices = $this->getFilteredInvoices($request);

            $data = [
                'title' => 'Invoice Report - ' . date('d-m-Y'),
                'invoices' => $invoices,
                'filters' => [
                    'client' => $request->client,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'status' => $request->status
                ]
            ];

            $pdf = PDF::loadView('hrms.hr.Reports.invoice-reports.pdf', $data);
            
            return $pdf->download('invoice_report_' . date('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->route('invoice-reports.index')->with('error', 'Error exporting PDF file.');
        }
    }

    private function getFilteredInvoices(Request $request)
    {
        $query = DB::table('invoices')
            ->join('clients', 'invoices.client', '=', 'clients.client_id')
            ->select(
                'invoices.*', 
                DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) AS client_name")
            )
            ->orderBy('invoices.invoice_date', 'desc');

        // Client filter
        if ($request->filled('client') && $request->client != '') {
            $query->where('clients.client_id', $request->client);
        }
    
        // Date filters
        if ($request->filled('from_date') && $request->from_date != '') {
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
                $query->whereDate('invoices.invoice_date', '>=', $fromDate);
            } catch (\Exception $e) {
                Log::error('Invalid from_date format: ' . $request->from_date);
            }
        }
    
        if ($request->filled('to_date') && $request->to_date != '') {
            try {
                $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
                $query->whereDate('invoices.invoice_date', '<=', $toDate);
            } catch (\Exception $e) {
                Log::error('Invalid to_date format: ' . $request->to_date);
            }
        }

        // Status filter
        if ($request->filled('status') && $request->status != '') {
            $query->where('invoices.status', $request->status);
        }

        return $query->get();
    }
}