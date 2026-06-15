<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
class PaymentController extends Controller
{
    /**
     * Display payment entry form
     */
   public function create()
    {
        $invoices = DB::table('invoices')
            ->leftJoin('clients', 'invoices.client', '=', 'clients.client_id')
            ->select(
                'invoices.invoice_id',
                'invoices.grant_amt',
                DB::raw("CASE 
                    WHEN invoices.client LIKE 'CLT-%' THEN CONCAT(clients.first_name, ' ', clients.last_name) 
                    ELSE invoices.client 
                END AS client_name")
            )
            ->where('invoices.status', '!=', 'paid')
            ->get();

        return view('hrms.hr.sales.payment.create', compact('invoices'));
    }

    /**
     * Show invoice details for payment
     */
  public function showInvoice(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id'
        ]);

        $invoice = DB::table('invoices')
            ->leftJoin('clients', 'invoices.client', '=', 'clients.client_id')
            ->select(
                'invoices.invoice_id',
                'invoices.invoice_date',
                'invoices.due_date',
                'invoices.grant_amt',
                'invoices.status',
                DB::raw("CASE 
                    WHEN invoices.client LIKE 'CLT-%' THEN CONCAT(clients.first_name, ' ', clients.last_name) 
                    ELSE invoices.client 
                END AS client_name")
            )
            ->where('invoices.invoice_id', $request->invoice_id)
            ->first();

        $paidAmount = DB::table('invoice_payments')
            ->where('invoice_id', $request->invoice_id)
            ->sum('amount');

        $remainingAmount = $invoice->grant_amt - $paidAmount;

        return response()->json([
            'invoice' => $invoice,
            'paid_amount' => (float)$paidAmount,
            'remaining_amount' => (float)$remainingAmount,
            'status' => $invoice->status
        ]);
    }

    /**
     * Store a new payment
     */
public function store(Request $request)
{
    $request->validate([
        'invoice_id' => 'required|exists:invoices,invoice_id',
        'payment_date' => 'required|date',
        'payment_amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string|max:50',
        'notes' => 'nullable|string'
    ]);

    // Get invoice details
    $invoice = DB::table('invoices')
        ->where('invoice_id', $request->invoice_id)
        ->first();

    // Calculate remaining amount
    $paidAmount = DB::table('invoice_payments')
        ->where('invoice_id', $request->invoice_id)
        ->sum('amount');

    $remainingAmount = $invoice->grant_amt - $paidAmount;

    // Validate payment amount
    if ($request->payment_amount > $remainingAmount) {
        return back()->withErrors([
            'payment_amount' => 'Payment amount cannot exceed remaining amount of ' . number_format($remainingAmount, 2)
        ])->withInput();
    }

    // Generate payment ID
    $paymentId = $this->generatePaymentId();
    $branchId = Session::get('branch_id');

    // Insert payment
    DB::table('invoice_payments')->insert([
        'payment_id' => $paymentId,
        'invoice_id' => $request->invoice_id,
        'amount' => $request->payment_amount,
        'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date)->format('Y-m-d'),
        'payment_method' => $request->payment_method,
        'notes' => $request->notes,
        'branch_id' => $branchId,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Determine new status
    $newPaidAmount = $paidAmount + $request->payment_amount;
    $status = abs($newPaidAmount - $invoice->grant_amt) < 0.01 ? 'paid' : 'partially_paid';

    // Get client name
    $clientName = DB::table('clients')
        ->where('client_id', $invoice->client)
        ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"))
        ->value('name') ?? $invoice->client;

    // LOG HERE (correct position)
    $this->logPaymentAction(
        $paymentId,
        $request->invoice_id,
        'created',
        "Payment of ₹{$request->payment_amount} created",
        $clientName,
        $request->payment_amount,
        $status
    );

    // Update invoice status
    DB::table('invoices')
        ->where('invoice_id', $request->invoice_id)
        ->update(['status' => $status]);

    return redirect()->route('payment.index')
        ->with('success', 'Payment recorded successfully!');
}


    /**
     * Display all payments
     */
   public function index(Request $request) 
{
    $query = DB::table('invoice_payments as p')
        ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'p.payment_id',
            'p.payment_date',
            'p.amount',
            'p.payment_method',
            'i.invoice_id',
            'i.grant_amt as invoice_total',
            'i.status',
            DB::raw("(SELECT SUM(amount) FROM invoice_payments WHERE invoice_id = i.invoice_id) as total_paid"),
            DB::raw("(i.grant_amt - (SELECT SUM(amount) FROM invoice_payments WHERE invoice_id = i.invoice_id)) as remaining_amount"),
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        );

    $branchId = Session::get('branch_id');

    // 🔍 Apply filters
    if ($request->filled('invoice_id')) {
        $query->where('i.invoice_id', 'like', '%' . $request->invoice_id . '%');
    }

    if ($request->filled('client_name')) {
        $query->where(function($q) use ($request) {
            $q->where(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', '%' . $request->client_name . '%')
              ->orWhere('i.client', 'like', '%' . $request->client_name . '%');
        });
    }

    if ($request->filled('payment_method')) {
        $query->where('p.payment_method', $request->payment_method);
    }

    if ($request->filled('status')) {
        $query->where('i.status', $request->status);
    }

    // 📅 Payment date filter
    if ($request->filled('payment_date_from')) {
        $query->whereDate('p.payment_date', '>=', $request->payment_date_from);
    }

    if ($request->filled('payment_date_to')) {
        $query->whereDate('p.payment_date', '<=', $request->payment_date_to);
    }

    // Calculate total amount for summary
    $totalAmount = (clone $query)->sum('p.amount');

    // Add pagination
    $payments = $query->orderBy('p.payment_date', 'desc')->paginate(20)->appends($request->query());
    $paymentLogs = DB::table('payment_activity_logs')
    ->orderBy('action_date', 'desc')
    ->limit(200)
    ->get();


    return view('hrms.hr.sales.payment.index', compact('payments', 'totalAmount','paymentLogs'));
}
    
    
// Add these methods to your PaymentController

/**
 * Show the form for editing a payment
 */
public function edit($paymentId)
{
    $payment = DB::table('invoice_payments as p')
        ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'p.*',
            'i.invoice_id',
            'i.grant_amt as invoice_total',
            DB::raw("(SELECT SUM(amount) FROM invoice_payments WHERE invoice_id = i.invoice_id AND payment_id != p.payment_id) as other_payments"),
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->where('p.payment_id', $paymentId)
        ->first();

    if (!$payment) {
        return redirect()->route('payment.index')
            ->with('error', 'Payment not found');
    }

    // Calculate max allowed payment amount
    $maxAmount = $payment->invoice_total - ($payment->other_payments ?? 0);

    return view('hrms.hr.sales.payment.edit', compact('payment', 'maxAmount'));
}
/**
 * Update the specified payment
 */
public function update(Request $request, $paymentId)
{
    $request->validate([
        'payment_date' => 'required|date',
        'payment_amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string|max:50',
        'notes' => 'nullable|string'
    ]);

    // Fetch payment
    $payment = DB::table('invoice_payments as p')
        ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
        ->select('p.*', 'i.invoice_id', 'i.grant_amt')
        ->where('p.payment_id', $paymentId)
        ->first();

    if (!$payment) {
        return redirect()->route('payment.index')
            ->with('error', 'Payment not found');
    }

    // Calculate max allowed
    $otherPayments = DB::table('invoice_payments')
        ->where('invoice_id', $payment->invoice_id)
        ->where('payment_id', '!=', $paymentId)
        ->sum('amount');

    $maxAmount = $payment->grant_amt - $otherPayments;

    if ($request->payment_amount > $maxAmount) {
        return back()->withErrors([
            'payment_amount' => 'Payment amount cannot exceed remaining amount of ' . number_format($maxAmount, 2)
        ])->withInput();
    }

    // Update payment
    DB::table('invoice_payments')
        ->where('payment_id', $paymentId)
        ->update([
            'amount' => $request->payment_amount,
            'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date)->format('Y-m-d'),
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

    // Recalculate status
    $totalPaid = DB::table('invoice_payments')
        ->where('invoice_id', $payment->invoice_id)
        ->sum('amount');

    $status = abs($totalPaid - $payment->grant_amt) < 0.01 ? 'paid' : 'partially_paid';

    // Get client name
    $clientName = DB::table('clients')
        ->where('client_id', $payment->invoice_id)
        ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"))
        ->value('name') ?? $payment->invoice_id;

    // LOG HERE
    $this->logPaymentAction(
        $paymentId,
        $payment->invoice_id,
        'updated',
        "Payment updated to ₹{$request->payment_amount}",
        $clientName,
        $request->payment_amount,
        $status
    );

    // Update invoice
    DB::table('invoices')
        ->where('invoice_id', $payment->invoice_id)
        ->update(['status' => $status]);

    return redirect()->route('payment.index')
        ->with('success', 'Payment updated successfully!');
}
public function changeStatus(Request $request)
{
    $invoice = DB::table('invoices')->where('invoice_id', $request->id)->first();

    if ($invoice) {
        DB::table('invoices')->where('invoice_id', $request->id)->update([
            'status' => $request->status
        ]);
    }

    // Get client name
    $clientName = DB::table('clients')
        ->where('client_id', $invoice->client)
        ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"))
        ->value('name') ?? $invoice->client;

    // LOG HERE
    $this->logPaymentAction(
        null,
        $request->id,
        'status_changed',
        "Invoice payment status changed to {$request->status}",
        $clientName,
        $invoice->grant_amt,
        $request->status
    );

    return response()->json([
        'status' => $invoice ? 1 : 0,
        'message' => $invoice ? 'Status has been updated!' : 'Failed to update status!'
    ]);
}

    /**
     * Generate unique payment ID
     */
    private function generatePaymentId()
    {
        $lastID = DB::table('invoice_payments')
            ->orderBy('id', 'desc')
            ->value('payment_id');

        $baseID = 'PAY-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        do {
            $newID = $baseID . str_pad($newIDNumber, 6, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('invoice_payments')->where('payment_id', $newID)->exists());

        return $newID;
    }
    /**
 * Remove the specified payment from storage.
 */
public function destroy($paymentId)
{
    DB::beginTransaction();

    try {
        // Fetch payment
        $payment = DB::table('invoice_payments as p')
            ->join('invoices as i', 'p.invoice_id', '=', 'i.invoice_id')
            ->select('p.*', 'i.invoice_id', 'i.grant_amt', 'i.client')  // Added i.client
            ->where('p.payment_id', $paymentId)
            ->first();

        if (!$payment) {
            return response()->json([
                'status' => 0,
                'message' => 'Payment not found'
            ]);
        }

        // Delete the payment
        DB::table('invoice_payments')->where('payment_id', $paymentId)->delete();

        // Recalculate invoice status
        $totalPaid = DB::table('invoice_payments')
            ->where('invoice_id', $payment->invoice_id)
            ->sum('amount');

        // FIXED: Explicitly cast status to string
        if ($totalPaid == 0) {
            $status = (string) 'pending';  // Force string
        } else {
            $status = (string) (abs($totalPaid - $payment->grant_amt) < 0.01 ? 'paid' : 'partially_paid');
        }

        // FIXED: Update invoice with explicit string casting
        DB::table('invoices')
            ->where('invoice_id', $payment->invoice_id)
            ->update(['status' => $status]);  // $status is now definitely a string

        DB::commit();

        // Get client name
        $clientName = DB::table('clients')
            ->where('client_id', $payment->client)  // Fixed: use $payment->client, not $invoice->client
            ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"))
            ->value('name') ?? $payment->invoice_id;

        // LOG HERE
        $this->logPaymentAction(
            $paymentId,
            $payment->invoice_id,
            'deleted',
            "Payment {$paymentId} has been deleted",
            $clientName,
            $payment->amount,
            $status
        );

        return response()->json([
            'status' => 1,
            'message' => 'Payment deleted successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 0,
            'message' => 'Failed to delete payment: ' . $e->getMessage()
        ]);
    }
}


private function logPaymentAction($paymentId, $invoiceId, $action, $details = null, $clientName = null, $amount = null, $status = null)
{
    try {
        // FORCE STRING CONVERSION for status
        $status = (string) $status;
        
        DB::table('payment_activity_logs')->insert([
            'payment_id'  => $paymentId,
            'invoice_id'  => $invoiceId,
            'action'      => $action,
            'client_name' => $clientName,
            'amount'      => $amount,
            'status'      => $status,  // Now forced to string
            'details'     => $details,
            'user_id'     => auth()->id() ?? 1,
            'user_name'   => auth()->user()->name ?? 'System',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'action_date' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return true;
    } catch (\Exception $e) {
        \Log::error("Payment Log Failed: " . $e->getMessage());
        return false;
    }
}
}