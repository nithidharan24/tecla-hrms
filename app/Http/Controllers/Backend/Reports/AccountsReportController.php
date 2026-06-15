<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AccountsReportController extends Controller
{
    public function index()
    {
        $paidByList = DB::table('expenses')->whereNotNull('paid_by')
            ->select('paid_by')->distinct()->orderBy('paid_by')->pluck('paid_by');

        $paymentMethods = DB::table('invoice_payments')->whereNotNull('payment_method')
            ->select('payment_method')->distinct()->orderBy('payment_method')->pluck('payment_method');

        return view('hrms.hr.Reports.Accounts.accounts-report', compact('paidByList', 'paymentMethods'));
    }

    // ========== ESTIMATES ==========
    public function getEstimateReport(Request $request)
    {
        try {
            $base = DB::table('estimates'); // for recordsTotal
            $recordsTotal = (clone $base)->count();

            $query = DB::table('estimates as e')
                ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
                ->select([
                    'e.estimate_id',
                    'e.estimate_date',
                    'e.expiry_date',
                    'e.grant_amt',
                    'e.status',
                    DB::raw("CASE 
                        WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE e.client 
                    END AS client_name")
                ]);

            // Filters
            if ($request->filled('from_date')) {
                try {
                    $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
                    $query->whereDate('e.estimate_date', '>=', $from);
                } catch (\Exception $e) {
                    $query->whereDate('e.estimate_date', '>=', $request->from_date);
                }
            }
            if ($request->filled('to_date')) {
                try {
                    $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
                    $query->whereDate('e.estimate_date', '<=', $to);
                } catch (\Exception $e) {
                    $query->whereDate('e.estimate_date', '<=', $request->to_date);
                }
            }
            if ($request->filled('status')) {
                $query->where('e.status', $request->status);
            }
            if ($request->filled('client')) {
                $client = $request->client;
                $query->where(function ($q) use ($client) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                      ->orWhere('e.client', 'like', "%{$client}%");
                });
            }
            if ($request->filled('estimate_id')) {
                $query->where('e.estimate_id', 'like', "%{$request->estimate_id}%");
            }

            $recordsFiltered = (clone $query)->count();

            // Ordering
            $orderColumn = 'e.estimate_date';
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
                $map = [
                    'estimate_date' => 'e.estimate_date',
                    'estimate_id'   => 'e.estimate_id',
                    'client_name'   => DB::raw("CASE WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) ELSE e.client END"),
                    'grant_amt'     => 'e.grant_amt',
                    'status'        => 'e.status',
                    'expiry_date'   => 'e.expiry_date',
                ];
                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
                }
            }
            $query->orderBy($orderColumn, $orderDir);

            // Paging
            $start = (int) ($request->start ?? 0);
            $length = (int) ($request->length ?? 10);
            if ($length !== -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getEstimateReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportEstimates(Request $request)
    {
        try {
            $query = DB::table('estimates as e')
                ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
                ->select([
                    'e.estimate_id',
                    'e.estimate_date',
                    'e.expiry_date',
                    'e.grant_amt',
                    'e.status',
                    DB::raw("CASE 
                        WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE e.client 
                    END AS client_name")
                ]);

            // same filters
            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('e.estimate_date', '>=', $from); }
                catch (\Exception $e) { $query->whereDate('e.estimate_date', '>=', $request->from_date); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('e.estimate_date', '<=', $to); }
                catch (\Exception $e) { $query->whereDate('e.estimate_date', '<=', $request->to_date); }
            }
            if ($request->filled('status')) { $query->where('e.status', $request->status); }
            if ($request->filled('client')) {
                $client = $request->client;
                $query->where(function ($q) use ($client) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                      ->orWhere('e.client', 'like', "%{$client}%");
                });
            }
            if ($request->filled('estimate_id')) { $query->where('e.estimate_id', 'like', "%{$request->estimate_id}%"); }

            $rows = $query->orderBy('e.estimate_date', 'desc')->get();

            $filename = 'estimate_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Estimate ID','Client','Estimate Date','Expiry Date','Grand Amount','Status']);
                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->estimate_id,
                        $r->client_name,
                        $r->estimate_date ? date('d/m/Y', strtotime($r->estimate_date)) : '',
                        $r->expiry_date ? date('d/m/Y', strtotime($r->expiry_date)) : '',
                        number_format((float)$r->grant_amt, 2),
                        ucfirst($r->status ?? '')
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export estimates: ' . $e->getMessage());
        }
    }

    public function exportEstimatesPdf(Request $request)
    {
        $query = DB::table('estimates as e')
            ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
            ->select([
                'e.estimate_id',
                'e.estimate_date',
                'e.expiry_date',
                'e.grant_amt',
                'e.status',
                DB::raw("CASE 
                    WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE e.client 
                END AS client_name")
            ]);

        // same filters
        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('e.estimate_date', '>=', $from); }
            catch (\Exception $e) { $query->whereDate('e.estimate_date', '>=', $request->from_date); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('e.estimate_date', '<=', $to); }
            catch (\Exception $e) { $query->whereDate('e.estimate_date', '<=', $request->to_date); }
        }
        if ($request->filled('status')) { $query->where('e.status', $request->status); }
        if ($request->filled('client')) {
            $client = $request->client;
            $query->where(function ($q) use ($client) {
                $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                  ->orWhere('e.client', 'like', "%{$client}%");
            });
        }
        if ($request->filled('estimate_id')) { $query->where('e.estimate_id', 'like', "%{$request->estimate_id}%"); }

        $rows = $query->orderBy('e.estimate_date', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-estimates', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Estimate Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('estimate_report_' . date('Y-m-d') . '.pdf');
    }

    // ========== EXPENSES ==========
    public function getExpenseReport(Request $request)
    {
        try {
            $recordsTotal = DB::table('expenses')->count();

            $query = DB::table('expenses')
                ->select([
                    'expense_id',
                    'item_name',
                    'purchase_from',
                    'purchase_date',
                    'purchased_by',
                    'amount',
                    'paid_by',
                    'status',
                    'created_at'
                ]);

            if ($request->filled('item_name')) {
                $query->where('item_name', 'like', "%{$request->item_name}%");
            }
            if ($request->filled('paid_by')) {
                $query->where('paid_by', $request->paid_by);
            }
            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay(); $query->where('created_at', '>=', $from); }
                catch (\Exception $e) { $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay()); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay(); $query->where('created_at', '<=', $to); }
                catch (\Exception $e) { $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay()); }
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $recordsFiltered = (clone $query)->count();

            $orderColumn = 'created_at';
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
                $map = [
                    'expense_id'    => 'expense_id',
                    'item_name'     => 'item_name',
                    'purchase_from' => 'purchase_from',
                    'purchase_date' => 'purchase_date',
                    'purchased_by'  => 'purchased_by',
                    'amount'        => 'amount',
                    'paid_by'       => 'paid_by',
                    'status'        => 'status',
                    'created_at'    => 'created_at'
                ];
                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
                }
            }
            $query->orderBy($orderColumn, $orderDir);

            $start = (int) ($request->start ?? 0);
            $length = (int) ($request->length ?? 10);
            if ($length !== -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getExpenseReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportExpenses(Request $request)
    {
        try {
            $query = DB::table('expenses')
                ->select([
                    'expense_id',
                    'item_name',
                    'purchase_from',
                    'purchase_date',
                    'purchased_by',
                    'amount',
                    'paid_by',
                    'status',
                    'created_at'
                ]);

            if ($request->filled('item_name')) { $query->where('item_name', 'like', "%{$request->item_name}%"); }
            if ($request->filled('paid_by')) { $query->where('paid_by', $request->paid_by); }
            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay(); $query->where('created_at', '>=', $from); }
                catch (\Exception $e) { $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay()); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay(); $query->where('created_at', '<=', $to); }
                catch (\Exception $e) { $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay()); }
            }
            if ($request->filled('status')) { $query->where('status', $request->status); }

            $rows = $query->orderBy('created_at', 'desc')->get();

            $filename = 'expense_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Expense ID','Item','Purchase From','Purchase Date','Purchased By','Amount','Paid By','Status','Created At']);
                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->expense_id,
                        $r->item_name,
                        $r->purchase_from,
                        $r->purchase_date ? date('d/m/Y', strtotime($r->purchase_date)) : '',
                        $r->purchased_by,
                        number_format((float)$r->amount, 2),
                        $r->paid_by,
                        ucfirst($r->status ?? ''),
                        $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : ''
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export expenses: ' . $e->getMessage());
        }
    }

    public function exportExpensesPdf(Request $request)
    {
        $query = DB::table('expenses')
            ->select([
                'expense_id',
                'item_name',
                'purchase_from',
                'purchase_date',
                'purchased_by',
                'amount',
                'paid_by',
                'status',
                'created_at'
            ]);

        if ($request->filled('item_name')) { $query->where('item_name', 'like', "%{$request->item_name}%"); }
        if ($request->filled('paid_by')) { $query->where('paid_by', $request->paid_by); }
        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay(); $query->where('created_at', '>=', $from); }
            catch (\Exception $e) { $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay()); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay(); $query->where('created_at', '<=', $to); }
            catch (\Exception $e) { $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay()); }
        }
        if ($request->filled('status')) { $query->where('status', $request->status); }

        $rows = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-expenses', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Expense Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('expense_report_' . date('Y-m-d') . '.pdf');
    }

    // ========== INVOICES ==========
    public function getInvoiceReport(Request $request)
    {
        try {
            $recordsTotal = DB::table('invoices')->count();

            $query = DB::table('invoices as i')
                ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
                ->select([
                    'i.invoice_id',
                    'i.invoice_date',
                    'i.due_date',
                    'i.total',
                    'i.tax_amt',
                    'i.discount',
                    'i.grant_amt',
                    'i.status',
                    DB::raw("CASE 
                        WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE i.client 
                    END AS client_name")
                ]);

            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('i.invoice_date', '>=', $from); }
                catch (\Exception $e) { $query->whereDate('i.invoice_date', '>=', $request->from_date); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('i.invoice_date', '<=', $to); }
                catch (\Exception $e) { $query->whereDate('i.invoice_date', '<=', $request->to_date); }
            }
            if ($request->filled('status')) { $query->where('i.status', $request->status); }
            if ($request->filled('client')) {
                $client = $request->client;
                $query->where(function ($q) use ($client) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                      ->orWhere('i.client', 'like', "%{$client}%");
                });
            }
            if ($request->filled('invoice_id')) { $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%"); }

            $recordsFiltered = (clone $query)->count();

            $orderColumn = 'i.invoice_date';
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
                $map = [
                    'invoice_date' => 'i.invoice_date',
                    'invoice_id'   => 'i.invoice_id',
                    'client_name'  => DB::raw("CASE WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) ELSE i.client END"),
                    'grant_amt'    => 'i.grant_amt',
                    'status'       => 'i.status',
                    'due_date'     => 'i.due_date',
                    'total'        => 'i.total',
                    'tax_amt'      => 'i.tax_amt',
                    'discount'     => 'i.discount',
                ];
                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
                }
            }
            $query->orderBy($orderColumn, $orderDir);

            $start = (int) ($request->start ?? 0);
            $length = (int) ($request->length ?? 10);
            if ($length !== -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getInvoiceReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportInvoices(Request $request)
    {
        try {
            $query = DB::table('invoices as i')
                ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
                ->select([
                    'i.invoice_id',
                    'i.invoice_date',
                    'i.due_date',
                    'i.total',
                    'i.tax_amt',
                    'i.discount',
                    'i.grant_amt',
                    'i.status',
                    DB::raw("CASE 
                        WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE i.client 
                    END AS client_name")
                ]);

            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('i.invoice_date', '>=', $from); }
                catch (\Exception $e) { $query->whereDate('i.invoice_date', '>=', $request->from_date); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('i.invoice_date', '<=', $to); }
                catch (\Exception $e) { $query->whereDate('i.invoice_date', '<=', $request->to_date); }
            }
            if ($request->filled('status')) { $query->where('i.status', $request->status); }
            if ($request->filled('client')) {
                $client = $request->client;
                $query->where(function ($q) use ($client) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                      ->orWhere('i.client', 'like', "%{$client}%");
                });
            }
            if ($request->filled('invoice_id')) { $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%"); }

            $rows = $query->orderBy('i.invoice_date', 'desc')->get();

            $filename = 'invoice_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Invoice ID','Client','Invoice Date','Due Date','Subtotal','Tax','Discount','Grand Amount','Status']);
                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->invoice_id,
                        $r->client_name,
                        $r->invoice_date ? date('d/m/Y', strtotime($r->invoice_date)) : '',
                        $r->due_date ? date('d/m/Y', strtotime($r->due_date)) : '',
                        number_format((float)$r->total, 2),
                        number_format((float)$r->tax_amt, 2),
                        number_format((float)$r->discount, 2),
                        number_format((float)$r->grant_amt, 2),
                        ucfirst($r->status ?? '')
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export invoices: ' . $e->getMessage());
        }
    }

    public function exportInvoicesPdf(Request $request)
    {
        $query = DB::table('invoices as i')
            ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
            ->select([
                'i.invoice_id',
                'i.invoice_date',
                'i.due_date',
                'i.total',
                'i.tax_amt',
                'i.discount',
                'i.grant_amt',
                'i.status',
                DB::raw("CASE 
                    WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE i.client 
                END AS client_name")
            ]);

        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('i.invoice_date', '>=', $from); }
            catch (\Exception $e) { $query->whereDate('i.invoice_date', '>=', $request->from_date); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('i.invoice_date', '<=', $to); }
            catch (\Exception $e) { $query->whereDate('i.invoice_date', '<=', $request->to_date); }
        }
        if ($request->filled('status')) { $query->where('i.status', $request->status); }
        if ($request->filled('client')) {
            $client = $request->client;
            $query->where(function ($q) use ($client) {
                $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$client}%")
                  ->orWhere('i.client', 'like', "%{$client}%");
            });
        }
        if ($request->filled('invoice_id')) { $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%"); }

        $rows = $query->orderBy('i.invoice_date', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-invoices', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Invoice Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('invoice_report_' . date('Y-m-d') . '.pdf');
    }

    // ========== PAYMENTS ==========
    public function getPaymentReport(Request $request)
    {
        try {
            $recordsTotal = DB::table('invoice_payments')->count();

            $query = DB::table('invoice_payments as p')
                ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
                ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
                ->select([
                    'p.payment_id',
                    'p.payment_date',
                    'p.amount',
                    'p.payment_method',
                    'i.invoice_id',
                    'i.grant_amt as invoice_total',
                    'i.status',
                    DB::raw("(SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id) as total_paid"),
                    DB::raw("(i.grant_amt - (SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id)) as remaining_amount"),
                    DB::raw("CASE 
                        WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE i.client 
                    END AS client_name")
                ]);

            if ($request->filled('invoice_id')) {
                $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%");
            }
            if ($request->filled('client_name')) {
                $name = $request->client_name;
                $query->where(function ($q) use ($name) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$name}%")
                      ->orWhere('i.client', 'like', "%{$name}%");
                });
            }
            if ($request->filled('payment_method')) {
                $query->where('p.payment_method', $request->payment_method);
            }
            if ($request->filled('status')) {
                $query->where('i.status', $request->status);
            }
            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('p.payment_date', '>=', $from); }
                catch (\Exception $e) { $query->whereDate('p.payment_date', '>=', $request->from_date); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('p.payment_date', '<=', $to); }
                catch (\Exception $e) { $query->whereDate('p.payment_date', '<=', $request->to_date); }
            }

            $recordsFiltered = (clone $query)->count();

            $orderColumn = 'p.payment_date';
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
                $map = [
                    'payment_date'     => 'p.payment_date',
                    'payment_id'       => 'p.payment_id',
                    'invoice_id'       => 'i.invoice_id',
                    'client_name'      => DB::raw("CASE WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) ELSE i.client END"),
                    'amount'           => 'p.amount',
                    'payment_method'   => 'p.payment_method',
                    'status'           => 'i.status',
                    'remaining_amount' => DB::raw("(i.grant_amt - (SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id))"),
                ];
                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
                }
            }
            $query->orderBy($orderColumn, $orderDir);

            $start = (int) ($request->start ?? 0);
            $length = (int) ($request->length ?? 10);
            if ($length !== -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->get();

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPaymentReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportPayments(Request $request)
    {
        try {
            $query = DB::table('invoice_payments as p')
                ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
                ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
                ->select([
                    'p.payment_id',
                    'p.payment_date',
                    'p.amount',
                    'p.payment_method',
                    'i.invoice_id',
                    'i.grant_amt as invoice_total',
                    'i.status',
                    DB::raw("(SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id) as total_paid"),
                    DB::raw("(i.grant_amt - (SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id)) as remaining_amount"),
                    DB::raw("CASE 
                        WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                        ELSE i.client 
                    END AS client_name")
                ]);

            if ($request->filled('invoice_id')) { $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%"); }
            if ($request->filled('client_name')) {
                $name = $request->client_name;
                $query->where(function ($q) use ($name) {
                    $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$name}%")
                      ->orWhere('i.client', 'like', "%{$name}%");
                });
            }
            if ($request->filled('payment_method')) { $query->where('p.payment_method', $request->payment_method); }
            if ($request->filled('status')) { $query->where('i.status', $request->status); }
            if ($request->filled('from_date')) {
                try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('p.payment_date', '>=', $from); }
                catch (\Exception $e) { $query->whereDate('p.payment_date', '>=', $request->from_date); }
            }
            if ($request->filled('to_date')) {
                try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('p.payment_date', '<=', $to); }
                catch (\Exception $e) { $query->whereDate('p.payment_date', '<=', $request->to_date); }
            }

            $rows = $query->orderBy('p.payment_date', 'desc')->get();

            $filename = 'payment_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Payment ID','Payment Date','Invoice ID','Client','Amount','Payment Method','Invoice Total','Total Paid','Remaining','Invoice Status']);
                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->payment_id,
                        $r->payment_date ? date('d/m/Y', strtotime($r->payment_date)) : '',
                        $r->invoice_id,
                        $r->client_name,
                        number_format((float)$r->amount, 2),
                        $r->payment_method,
                        number_format((float)$r->invoice_total, 2),
                        number_format((float)$r->total_paid, 2),
                        number_format((float)$r->remaining_amount, 2),
                        ucfirst($r->status ?? '')
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export payments: ' . $e->getMessage());
        }
    }

    public function exportPaymentsPdf(Request $request)
    {
        $query = DB::table('invoice_payments as p')
            ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
            ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
            ->select([
                'p.payment_id',
                'p.payment_date',
                'p.amount',
                'p.payment_method',
                'i.invoice_id',
                'i.grant_amt as invoice_total',
                'i.status',
                DB::raw("(SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id) as total_paid"),
                DB::raw("(i.grant_amt - (SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE invoice_id = i.invoice_id)) as remaining_amount"),
                DB::raw("CASE 
                    WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE i.client 
                END AS client_name")
            ]);

        if ($request->filled('invoice_id')) { $query->where('i.invoice_id', 'like', "%{$request->invoice_id}%"); }
        if ($request->filled('client_name')) {
            $name = $request->client_name;
            $query->where(function ($q) use ($name) {
                $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%{$name}%")
                  ->orWhere('i.client', 'like', "%{$name}%");
            });
        }
        if ($request->filled('payment_method')) { $query->where('p.payment_method', $request->payment_method); }
        if ($request->filled('status')) { $query->where('i.status', $request->status); }
        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('p.payment_date', '>=', $from); }
            catch (\Exception $e) { $query->whereDate('p.payment_date', '>=', $request->from_date); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('p.payment_date', '<=', $to); }
            catch (\Exception $e) { $query->whereDate('p.payment_date', '<=', $request->to_date); }
        }

        $rows = $query->orderBy('p.payment_date', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-payments', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Payment Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('payment_report_' . date('Y-m-d') . '.pdf');
    }
    // Add these methods to your existing AccountsReportController

// ========== BUDGET EXPENSES ==========
public function getBudgetExpenseReport(Request $request)
{
    try {
        $recordsTotal = DB::table('budgets-expenses')->count();

        $query = DB::table('budgets-expenses as be')
            ->leftJoin('categories as c', 'be.categories', '=', 'c.id')
            ->leftJoin('subcategories as sc', 'be.sub-categories', '=', 'sc.id')
            ->select([
                'be.id',
                'be.Notes',
                'c.name as category_name',
                'sc.name as subcategory_name',
                'be.amount',
                'be.currency_symbol',
                'be.expense-date as expense_date',
                'be.created_at',
                'be.img'
            ]);

        // Filters
        if ($request->filled('category')) {
            $query->where('c.name', 'like', "%{$request->category}%");
        }
        if ($request->filled('sub_category')) {
            $query->where('sc.name', 'like', "%{$request->sub_category}%");
        }
        if ($request->filled('from_date')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
                $query->whereDate('be.expense-date', '>=', $from);
            } catch (\Exception $e) {
                $query->whereDate('be.expense-date', '>=', $request->from_date);
            }
        }
        if ($request->filled('to_date')) {
            try {
                $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
                $query->whereDate('be.expense-date', '<=', $to);
            } catch (\Exception $e) {
                $query->whereDate('be.expense-date', '<=', $request->to_date);
            }
        }
        if ($request->filled('notes')) {
            $query->where('be.Notes', 'like', "%{$request->notes}%");
        }

        $recordsFiltered = (clone $query)->count();

        // Ordering
        $orderColumn = 'be.expense-date';
        $orderDir = 'desc';
        if ($request->has('order')) {
            $dir = $request->order[0]['dir'] ?? 'desc';
            $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
            $map = [
                'expense_date' => 'be.expense-date',
                'category_name' => 'c.name',
                'subcategory_name' => 'sc.name',
                'amount' => 'be.amount',
                'created_at' => 'be.created_at',
                'Notes' => 'be.Notes'
            ];
            if (array_key_exists($colKey, $map)) {
                $orderColumn = $map[$colKey];
                $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
            }
        }
        $query->orderBy($orderColumn, $orderDir);

        // Paging
        $start = (int) ($request->start ?? 0);
        $length = (int) ($request->length ?? 10);
        if ($length !== -1) {
            $query->offset($start)->limit($length);
        }

        $rows = $query->get();

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows
        ]);
    } catch (\Exception $e) {
        Log::error('Error in getBudgetExpenseReport: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

public function exportBudgetExpenses(Request $request)
{
    try {
        $query = DB::table('budgets-expenses as be')
            ->leftJoin('categories as c', 'be.categories', '=', 'c.id')
            ->leftJoin('subcategories as sc', 'be.sub-categories', '=', 'sc.id')
            ->select([
                'be.id',
                'be.Notes',
                'c.name as category_name',
                'sc.name as subcategory_name',
                'be.amount',
                'be.currency_symbol',
                'be.expense-date as expense_date',
                'be.created_at'
            ]);

        // same filters
        if ($request->filled('category')) { $query->where('c.name', 'like', "%{$request->category}%"); }
        if ($request->filled('sub_category')) { $query->where('sc.name', 'like', "%{$request->sub_category}%"); }
        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('be.expense-date', '>=', $from); }
            catch (\Exception $e) { $query->whereDate('be.expense-date', '>=', $request->from_date); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('be.expense-date', '<=', $to); }
            catch (\Exception $e) { $query->whereDate('be.expense-date', '<=', $request->to_date); }
        }
        if ($request->filled('notes')) { $query->where('be.Notes', 'like', "%{$request->notes}%"); }

        $rows = $query->orderBy('be.expense-date', 'desc')->get();

        $filename = 'budget_expense_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID','Category','Sub Category','Amount','Currency','Notes','Expense Date','Created At']);
            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->category_name,
                    $r->subcategory_name,
                    number_format((float)$r->amount, 2),
                    $r->currency_symbol,
                    $r->Notes,
                    $r->expense_date ? date('d/m/Y', strtotime($r->expense_date)) : '',
                    $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to export budget expenses: ' . $e->getMessage());
    }
}

public function exportBudgetExpensesPdf(Request $request)
{
    $query = DB::table('budgets-expenses as be')
        ->leftJoin('categories as c', 'be.categories', '=', 'c.id')
        ->leftJoin('subcategories as sc', 'be.sub-categories', '=', 'sc.id')
        ->select([
            'be.id',
            'be.Notes',
            'c.name as category_name',
            'sc.name as subcategory_name',
            'be.amount',
            'be.currency_symbol',
            'be.expense-date as expense_date',
            'be.created_at'
        ]);

    // same filters
    if ($request->filled('category')) { $query->where('c.name', 'like', "%{$request->category}%"); }
    if ($request->filled('sub_category')) { $query->where('sc.name', 'like', "%{$request->sub_category}%"); }
    if ($request->filled('from_date')) {
        try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('be.expense-date', '>=', $from); }
        catch (\Exception $e) { $query->whereDate('be.expense-date', '>=', $request->from_date); }
    }
    if ($request->filled('to_date')) {
        try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('be.expense-date', '<=', $to); }
        catch (\Exception $e) { $query->whereDate('be.expense-date', '<=', $request->to_date); }
    }
    if ($request->filled('notes')) { $query->where('be.Notes', 'like', "%{$request->notes}%"); }

    $rows = $query->orderBy('be.expense-date', 'desc')->get();

    $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-budget-expenses', [
        'rows' => $rows,
        'generated_at' => now()->format('d/m/Y H:i'),
        'title' => 'Budget Expense Report'
    ])->setPaper('a4', 'landscape');

    return $pdf->download('budget_expense_report_' . date('Y-m-d') . '.pdf');
}
// ========== BUDGET REVENUE ==========
// ========== BUDGET REVENUE ==========
public function getBudgetRevenueReport(Request $request)
{
    try {
        $recordsTotal = DB::table('budget-revenue')->count();

        $query = DB::table('budget-revenue as br')
            ->leftJoin('categories as c', 'br.categories', '=', 'c.id')
            ->leftJoin('subcategories as sc', 'br.sub-categories', '=', 'sc.id')
            ->select([
                'br.id',
                'br.Notes',
                'c.name as category_name',
                'sc.name as subcategory_name',
                'br.amount',
                'br.revenue-date as revenue_date',
                'br.created_at',
                'br.img'
            ]);

        // Filters
        if ($request->filled('category')) {
            $query->where('c.name', 'like', "%{$request->category}%");
        }
        if ($request->filled('sub_category')) {
            $query->where('sc.name', 'like', "%{$request->sub_category}%");
        }
        if ($request->filled('from_date')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
                $query->whereDate('br.revenue-date', '>=', $from);
            } catch (\Exception $e) {
                $query->whereDate('br.revenue-date', '>=', $request->from_date);
            }
        }
        if ($request->filled('to_date')) {
            try {
                $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
                $query->whereDate('br.revenue-date', '<=', $to);
            } catch (\Exception $e) {
                $query->whereDate('br.revenue-date', '<=', $request->to_date);
            }
        }
        if ($request->filled('notes')) {
            $query->where('br.Notes', 'like', "%{$request->notes}%");
        }

        $recordsFiltered = (clone $query)->count();

        // Ordering
        $orderColumn = 'br.revenue-date';
        $orderDir = 'desc';
        if ($request->has('order')) {
            $dir = $request->order[0]['dir'] ?? 'desc';
            $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
            $map = [
                'revenue_date' => 'br.revenue-date',
                'category_name' => 'c.name',
                'subcategory_name' => 'sc.name',
                'amount' => 'br.amount',
                'created_at' => 'br.created_at',
                'Notes' => 'br.Notes'
            ];
            if (array_key_exists($colKey, $map)) {
                $orderColumn = $map[$colKey];
                $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
            }
        }
        $query->orderBy($orderColumn, $orderDir);

        // Paging
        $start = (int) ($request->start ?? 0);
        $length = (int) ($request->length ?? 10);
        if ($length !== -1) {
            $query->offset($start)->limit($length);
        }

        $rows = $query->get();

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows
        ]);
    } catch (\Exception $e) {
        Log::error('Error in getBudgetRevenueReport: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}
public function exportBudgetRevenue(Request $request)
{
    try {
        $query = DB::table('budget-revenue as br')
            ->leftJoin('categories as c', 'br.categories', '=', 'c.id')
            ->leftJoin('subcategories as sc', 'br.sub-categories', '=', 'sc.id')
            ->select([
                'br.id',
                'br.Notes',
                'c.name as category_name',
                'sc.name as subcategory_name',
                'br.amount',
                'br.currency_symbol',
                'br.revenue-date as revenue_date',
                'br.created_at'
            ]);

        // same filters
        if ($request->filled('category')) { $query->where('c.name', 'like', "%{$request->category}%"); }
        if ($request->filled('sub_category')) { $query->where('sc.name', 'like', "%{$request->sub_category}%"); }
        if ($request->filled('from_date')) {
            try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('br.revenue-date', '>=', $from); }
            catch (\Exception $e) { $query->whereDate('br.revenue-date', '>=', $request->from_date); }
        }
        if ($request->filled('to_date')) {
            try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('br.revenue-date', '<=', $to); }
            catch (\Exception $e) { $query->whereDate('br.revenue-date', '<=', $request->to_date); }
        }
        if ($request->filled('notes')) { $query->where('br.Notes', 'like', "%{$request->notes}%"); }

        $rows = $query->orderBy('br.revenue-date', 'desc')->get();

        $filename = 'budget_revenue_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID','Category','Sub Category','Amount','Currency','Notes','Revenue Date','Created At']);
            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->category_name,
                    $r->subcategory_name,
                    number_format((float)$r->amount, 2),
                    $r->currency_symbol,
                    $r->Notes,
                    $r->revenue_date ? date('d/m/Y', strtotime($r->revenue_date)) : '',
                    $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to export budget revenue: ' . $e->getMessage());
    }
}

public function exportBudgetRevenuePdf(Request $request)
{
    $query = DB::table('budget-revenue as br')
        ->leftJoin('categories as c', 'br.categories', '=', 'c.id')
        ->leftJoin('subcategories as sc', 'br.sub-categories', '=', 'sc.id')
        ->select([
            'br.id',
            'br.Notes',
            'c.name as category_name',
            'sc.name as subcategory_name',
            'br.amount',
            'br.currency_symbol',
            'br.revenue-date as revenue_date',
            'br.created_at'
        ]);

    // same filters
    if ($request->filled('category')) { $query->where('c.name', 'like', "%{$request->category}%"); }
    if ($request->filled('sub_category')) { $query->where('sc.name', 'like', "%{$request->sub_category}%"); }
    if ($request->filled('from_date')) {
        try { $from = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay(); $query->whereDate('br.revenue-date', '>=', $from); }
        catch (\Exception $e) { $query->whereDate('br.revenue-date', '>=', $request->from_date); }
    }
    if ($request->filled('to_date')) {
        try { $to = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay(); $query->whereDate('br.revenue-date', '<=', $to); }
        catch (\Exception $e) { $query->whereDate('br.revenue-date', '<=', $request->to_date); }
    }
    if ($request->filled('notes')) { $query->where('br.Notes', 'like', "%{$request->notes}%"); }

    $rows = $query->orderBy('br.revenue-date', 'desc')->get();

    $pdf = Pdf::loadView('hrms.hr.Reports.Accounts.pdf-budget-revenue', [
        'rows' => $rows,
        'generated_at' => now()->format('d/m/Y H:i'),
        'title' => 'Budget Revenue Report'
    ])->setPaper('a4', 'landscape');

    return $pdf->download('budget_revenue_report_' . date('Y-m-d') . '.pdf');
}
}