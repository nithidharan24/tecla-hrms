<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use App\Mail\Invoice\InvoiceMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Exports\InvoiceExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request)
{
    $query = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'i.invoice_id', 
            'i.invoice_date', 
            'i.due_date', 
            'i.grant_amt', 
            'i.status', 
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name"),
            'i.client as client_raw', // Add this for better filtering
            'c.first_name',
            'c.last_name'
        );

    // Apply branch filter
    $query = applyBranchFilter($query, 'i');

    // Apply client name filter - FIXED VERSION
    if ($request->filled('client_name')) {
        $searchTerm = '%' . $request->client_name . '%';
        
        $query->where(function($q) use ($searchTerm) {
            $q->where('c.first_name', 'like', $searchTerm)
              ->orWhere('c.last_name', 'like', $searchTerm)
              ->orWhere('c.first_name', 'like', str_replace(' ', '%', $searchTerm))
              ->orWhere('c.last_name', 'like', str_replace(' ', '%', $searchTerm))
              ->orWhereRaw("CONCAT(c.first_name, ' ', c.last_name) LIKE ?", [$searchTerm])
              ->orWhere('i.client', 'like', $searchTerm);
        });
    }

    // Apply date filters (using YYYY-MM-DD format from date inputs)
    if ($request->filled('from_date')) {
        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $query->whereDate('i.invoice_date', '>=', $fromDate);
        } catch (\Exception $e) {
            return back()->withErrors(['messages' => 'Invalid from date format. Please use YYYY-MM-DD format.']);
        }
    }

    if ($request->filled('to_date')) {
        try {
            $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereDate('i.invoice_date', '<=', $toDate);
        } catch (\Exception $e) {
            return back()->withErrors(['messages' => 'Invalid to date format. Please use YYYY-MM-DD format.']);
        }
    }

    // Apply status filter
    if ($request->filled('status')) {
        $query->where('i.status', $request->status);
    }

    // Calculate total amount for summary
    $totalAmount = (clone $query)->sum('i.grant_amt');

    // Get all results (NO pagination)
    $invoices = $query->orderBy('i.invoice_date', 'desc')->get();
    
    $logs = DB::table('invoice_activity_logs')
        ->orderBy('action_date', 'desc')
        ->limit(200)
        ->get();

    return view('hrms.hr.sales.invoice.index', compact('invoices', 'totalAmount','logs'))
        ->with('messages', $invoices->isEmpty() ? 'No records found' : '');
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = DB::table('clients')
        ->select('client_id', 'first_name', 'last_name')
        ->get();

        //projects
        $projects = DB::table('projects')
        ->where('status', 'active')
        ->where('deleted_at', 0)
        ->select('projectid', 'projectname')
        ->get();

        //tax
        $tax = DB::table('taxes')
        ->where('status', 'active')
        ->where('deleted_at', 0)
        ->select('id', 'name','percentage')
        ->get();

        return view('hrms.hr.sales.invoice.create',compact('clients','projects','tax'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
{

    $Id = $this->generateID('inv');
    $rules = [
        'email' => 'required|email',
        'mobile' => 'required|min:10',
        'ex_client' => 'required|string|max:255',
        'ex_project' => 'required|string|max:255',
        'client_address' => 'required|string',
        'billing_address' => 'required|string',
        'invdate' => 'required|date_format:d-m-Y',
        'discount' => 'nullable|numeric|min:0',
        'duedate' => 'nullable|date_format:d-m-Y', // ✅ allow duedate to be optional

        'other' => 'nullable|string'
    ];

    $taxes = $request->has('taxes') && is_array($request->input('taxes')) 
        ? array_filter($request->input('taxes'), function($value) {
            return !is_null($value) && $value !== '';  // Filter out null and empty values
        })
        : [];

    $tax = !empty($taxes) ? implode(',', $taxes) : null;

    $request->validate($rules);

    // Convert dates from d-m-Y to Y-m-d format
    $invoiceDate = Carbon::createFromFormat('d-m-Y', $request->input('invdate'))->format('Y-m-d');
  // Convert due date only if provided
  $dueDate = $request->filled('duedate')
  ? Carbon::createFromFormat('d-m-Y', $request->input('duedate'))->format('Y-m-d')
  : null;
    $totals = $this->calculateTotals([
        'taxes' => $taxes,
        'items' => $request->input('items',[])
    ]);

    $grant_total = $this->applyDiscount($totals['totalAmount'],$totals['totalTaxAmount'],$request->input('discount',0));
    $grant_total_rdf = number_format(round($grant_total), 2, '.', '');
    $branchId = Session::get('branch_id');
    $insertData = [
        'invoice_id' => $Id,
        'email' => $request->input('email'),
        'phone' => $request->input('mobile'),
        'client' => $request->input('ex_client'),
        'project' => $request->input('ex_project'),
        'client_address' => $request->input('client_address'),
        'billing_address' => $request->input('billing_address'),
        'invoice_date' => $invoiceDate,
        'due_date' => $dueDate,
        'branch_id' => $branchId, // store branch_id from session
        'total' => number_format($totals['totalAmount'], 2, '.', ''),
        'tax_amt' => number_format($totals['totalTaxAmount'], 2, '.', '') ?? 0,
        'discount' => $request->input('discount', 0),
        'grant_amt' => $grant_total_rdf,
        'other_information' => $request->input('other'),
        'tax' => $tax,
        'status' => 'sent' // Add status to track email sending
    ];

    DB::beginTransaction();

    try {
        // Insert invoice data
        $result = DB::table('invoices')->insert($insertData);
        
        if (!$result) {
            throw new \Exception('Failed to insert invoice');
        }

        $invoice_Id = $Id;

        // Insert invoice items
        if (empty($request->items)) {
            throw new \Exception('No items provided for the invoice');
        }

        foreach ($request->items as $item) {
            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice_Id,
                'item' => $item['name'],
                'description' => $item['description'],
                'unit_cost' => $item['unitCost'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
            ]);
        }

        // Send email
        $emailSent = $this->sendInvoiceEmail($invoice_Id);
        
        if ($emailSent['success']) {
            DB::commit();
            Session::flash('messageType', 'success');
            Session::flash('message', 'Invoice created and email sent successfully!');
        } else {
            // Email failed but invoice was created
            DB::commit();
            Session::flash('messageType', 'warning');
            Session::flash('message', 'Invoice created but email failed: ' . $emailSent['message']);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        Session::flash('messageType', 'error');
        Session::flash('message', 'Error creating invoice: ' . $e->getMessage());
        return redirect()->back();
    }
    $this->logInvoiceAction(
    $Id,
    'created',
    'Invoice created successfully',
    $request->ex_client,
    $grant_total_rdf,
    'sent'
);


    return redirect()->route('invoice.index');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = DB::table('invoices')->where('invoice_id',$id)->first();

        $clients = DB::table('clients')
        ->select('client_id', 'first_name', 'last_name')
        ->get();

        //projects
        $projects = DB::table('projects')
        ->where('status', 'active')
        ->where('deleted_at', 0)
        ->where('client', $invoices->client)
        ->select('projectid', 'projectname')
        ->get();

        //tax
        $tax = DB::table('taxes')
        ->where('status', 'active')
        ->where('deleted_at', 0)
        ->select('id', 'name','percentage')
        ->get();

        //invoice items
        $invoice_items = DB::table('invoice_items')->where('invoice_id',$id)->get();

        return view('hrms.hr.sales.invoice.edit',compact('invoices','clients','projects','tax','invoice_items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $rules = [
            'email' => 'required|email',
            'mobile' => 'required|min:10',
            'client_address' => 'required|string',
            'billing_address' => 'required|string',
            'invdate' => 'required|date_format:d-m-Y',
            'duedate' => 'required|date_format:d-m-Y',
            'total' => 'required|numeric',
            'totaltaxAmt' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'grantAmt' => 'required|numeric',
            'ex_project' => 'required',
            'ex_client' => 'required',
            'other' => 'nullable|string',
            'taxes' => 'nullable|array',
            'taxes.*' => 'nullable|numeric',
            'items' => 'required|array', // Ensure items are provided
            'items.*.id' => 'nullable|integer', // Allow ID for existing items
            'items.*.name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.unitCost' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.amount' => 'required|numeric',
        ];
    
        // Validate the request
        $request->validate($rules);
    
        // Filtering taxes
        $taxes = $request->has('taxes') && is_array($request->input('taxes')) 
            ? array_filter($request->input('taxes'), function($value) {
                return !is_null($value) && $value !== ''; // Filter out null and empty values
            })
            : [];
    
        $tax = !empty($taxes) ? implode(',', $taxes) : null;
    
        // Convert dates from d-m-Y to Y-m-d format
        $invoiceDate = Carbon::createFromFormat('d-m-Y', $request->input('invdate'))->format('Y-m-d');
        $dueDate = Carbon::createFromFormat('d-m-Y', $request->input('duedate'))->format('Y-m-d');

        $totals = $this->calculateTotals([
            'taxes' => $taxes,
            'items' => $request->input('items',[])
        ]);

        $grant_total = $this->applyDiscount($totals['totalAmount'],$totals['totalTaxAmount'],$request->input('discount',0));
        $grant_total_rdf = number_format(round($grant_total), 2, '.', '');
    
        // Prepare data for updating the invoice
        $updateData = [
            'email' => $request->input('email'),
            'phone' => $request->input('mobile'),
            'client' => $request->input('ex_client'),
            'project' => $request->input('ex_project'),
            'client_address' => $request->input('client_address'),
            'billing_address' => $request->input('billing_address'),
            'invoice_date' => $invoiceDate, // Use the formatted date
            'due_date' => $dueDate, // Use the formatted date
            'total' => number_format($totals['totalAmount'], 2, '.', ''),
            'tax_amt' => number_format($totals['totalTaxAmount'], 2, '.', '') ?? 0,
            'discount' => $request->input('discount', 0),
            'grant_amt' => $grant_total_rdf,
            'other_information' => $request->input('other'),
            'tax' => $tax
        ];
    
        // Update the invoice
        DB::table('invoices')->where('invoice_id', $id)->update($updateData);
        // Fetch existing invoice item IDs
        $existingIds = DB::table('invoice_items')
        ->where('invoice_id', $id)
        ->pluck('id')
        ->toArray(); // Convert to array for easier comparison

        // Process invoice items
        if (!empty($request->input('items'))) {
        // Create an array to store IDs of items from the request
        $requestIds = [];

        foreach ($request->input('items') as $item) {
            if (isset($item['id'])) {
                // If the item has an ID, update the existing item
                DB::table('invoice_items')->where('id', $item['id'])->update([
                    'item' => $item['name'],
                    'description' => $item['description'],
                    'unit_cost' => $item['unitCost'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                ]);
                // Store the ID for later comparison
                $requestIds[] = $item['id'];
            } else {
                // If the item doesn't have an ID, insert a new item
                DB::table('invoice_items')->insert([
                    'invoice_id' => $id,
                    'item' => $item['name'],
                    'description' => $item['description'],
                    'unit_cost' => $item['unitCost'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                ]);
            }
        }

        // Find IDs that exist in the database but not in the request
        $idsToDelete = array_diff($existingIds, $requestIds);

        // Delete any items that are no longer present in the request
        if (!empty($idsToDelete)) {
            DB::table('invoice_items')->whereIn('id', $idsToDelete)->delete();
        }

$invoice = DB::table('invoices')->where('invoice_id', $id)->first();

$this->logInvoiceAction(
    $id,
    'updated',
    'Invoice updated successfully',
    $request->ex_client,
    $grant_total_rdf,
    $invoice->status ?? null
);


        Session::flash('messageType', 'success');
        Session::flash('message', 'Invoice Updated Successfully!');
        } else {
        Session::flash('messageType', 'error');
        Session::flash('message', 'Failed to update invoice items. No items provided!');
        return redirect()->back();
        }

        return redirect()->route('invoice.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function destroy($id)
{
    try {

        // FETCH INVOICE BEFORE DELETE
        $invoice = DB::table('invoices')->where('invoice_id', $id)->first();

        if (!$invoice) {
            return response()->json([
                'status' => 0,
                'icon'   => 'error',
                'message' => 'Invoice not found!'
            ], 404);
        }

        DB::transaction(function () use ($id) {
            DB::table('invoice_items')->where('invoice_id', $id)->delete();
            DB::table('invoices')->where('invoice_id', $id)->delete();
        });

        // LOG DELETE
        $this->logInvoiceAction(
            $id,
            'deleted',
            "Invoice {$id} has been deleted",
            $invoice->client,
            $invoice->grant_amt,
            $invoice->status
        );

        return response()->json([
            'status' => 1,
            'icon'   => 'success',
            'message' => 'Invoice deleted successfully!',
            'id' => $id
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 0,
            'icon'   => 'error',
            'message' => 'Delete failed: ' . $e->getMessage()
        ], 500);
    }
}


    private function generateID($base)
    {
        // Get the last client ID or set the starting number
        $lastID = DB::table('invoices')
        ->whereNotNull('invoice_id')
        ->orderBy('id', 'desc')
        ->value('invoice_id');

        // Initialize base ID
        $baseID = Str::upper($base).'-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        // Generate new client ID
        do {
            $newID = $baseID . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('invoices')->where('invoice_id', $newID)->exists());

        return $newID;
    }

    public function getProjects(Request $request)
    {
        $clientId = $request->input('client_id');

        if ($clientId) {
            $projects = DB::table('projects as p')
                ->join('clients as c', 'p.client', '=', 'c.client_id')
                ->where('p.client', $clientId)
                ->where('p.deleted_at', 0)
                ->where('p.status', 'active')
                ->select('p.*')
                ->get();
        
            $client = DB::table('clients as c')
                ->where('c.client_id', $clientId)
                ->where('c.deleted_at', 0)
                ->where('c.status', 'active')
                ->first();
        }
        
        return response()->json([
            'projects' => $projects,
            'client' => $client,
        ]);
    }

    public function viewInvoice($id){

        $invoices = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'i.invoice_id', 
            'i.invoice_date', 
            'i.due_date', 
            'i.email',
            'i.phone',
            'i.grant_amt', 
            'i.other_information',
            'i.discount',
            'i.billing_address',
            'i.tax',
            'i.tax_amt',
            'i.total', 
            'i.status', 
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->where('i.invoice_id', $id)
        ->first();

        if (!$invoices) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Invoice not found.');
            return redirect()->back();
        }

        $tax = !is_null($invoices->tax) ? explode(',', $invoices->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        $invoice_items = DB::table('invoice_items')
        ->where('invoice_id', $id)
        ->where('deleted_at', 0)
        ->get();

        // dd($estimates);
        return view('hrms.hr.sales.invoice.invoiceView',compact('invoices','invoice_items','taxDetails'));
    }

  

    public function pdfPrint($id)
    {
        $invoices = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'i.invoice_id', 
            'i.invoice_date', 
            'i.due_date', 
            'i.email',
            'i.phone',
            'i.grant_amt', 
            'i.other_information',
            'i.discount',
            'i.billing_address',
            'i.tax',
            'i.tax_amt',
            'i.total', 
            'i.status', 
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->where('i.invoice_id', $id)
        ->first();

        if (!$invoices) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Estimate not found.');
            return redirect()->back();
        }

        $tax = !is_null($invoices->tax) ? explode(',', $invoices->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        $invoice_items = DB::table('invoice_items')
        ->where('invoice_id', $id)
        ->where('deleted_at', 0)
        ->get();
        $pdf = PDF::loadView('hrms.hr.sales.invoice.export', compact('invoices','invoice_items','taxDetails'));
        return $pdf->stream('Export-Invoice' . time() . rand(99, 9999) . '.pdf');
        // return view('hrms.hr.sales.estimate.export',compact('estimates','estimate_items','taxDetails'));
    }
public function sendInvoiceEmail($invoiceId)
{
    try {
        // Get invoice data with client name
        $invoice = DB::table('invoices as i')
            ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
            ->select(
                'i.*', // Select all invoice fields
                DB::raw("CASE 
                    WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE i.client 
                END AS client_name")
            )
            ->where('i.invoice_id', $invoiceId)
            ->first();

        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        // Get tax details
        $tax = !is_null($invoice->tax) ? explode(',', $invoice->tax) : [];
        $taxDetails = [];

        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        // Get invoice items
        $invoiceItems = DB::table('invoice_items')
            ->where('invoice_id', $invoiceId)
            ->where('deleted_at', 0)
            ->get();

        // Generate PDF - ensure variable names match your view
        $pdf = PDF::loadView('hrms.hr.sales.invoice.export', [
            'invoice' => $invoice,       // Singular to match your original code
            'invoice_items' => $invoiceItems,
            'taxDetails' => $taxDetails
        ]);

        // Send email
        Mail::to($invoice->email)->send(new InvoiceMailer(
            $invoice->client_name,
            $invoice->email,
            $pdf->output(),
            $invoice->invoice_id,
            $invoice->invoice_date,
            $invoice->grant_amt
        ));
$this->logInvoiceAction(
    $invoiceId,
    'email_sent',
    'Invoice email sent to client',
    $invoice->client_name,
    $invoice->grant_amt,
    $invoice->status
);

return [
    'success' => true,
    'message' => 'Email sent successfully'
];



    } catch (\Exception $e) {
        Log::error('Email sending failed for invoice ' . $invoiceId . ': ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function pdf($id)
{
    // Get invoice data with client name
    $invoice = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'i.*', // Select all invoice fields
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->where('i.invoice_id', $id)
        ->first();

    if (!$invoice) {
        Session::flash('messageType', 'error');
        Session::flash('message', 'Invoice not found.');
        return redirect()->back();
    }

    // Get tax details
    $tax = !is_null($invoice->tax) ? explode(',', $invoice->tax) : [];
    $taxDetails = [];

    if (!empty($tax)) {
        $taxDetails = DB::table('taxes')
            ->select('id', 'name', 'percentage')
            ->whereIn('id', $tax)
            ->get();
    }

    // Get invoice items
    $invoice_items = DB::table('invoice_items')
        ->where('invoice_id', $id)
        ->where('deleted_at', 0)
        ->get();

    // Generate PDF
    $pdf = PDF::loadView('hrms.hr.sales.invoice.export', [
        'invoice' => $invoice,
        'invoice_items' => $invoice_items,
        'taxDetails' => $taxDetails
    ]);
    $this->logInvoiceAction(
    $id,
    'pdf_downloaded',
    'Invoice PDF downloaded',
    $invoice->client_name,
    $invoice->grant_amt,
    $invoice->status
);


    return $pdf->download('Invoice-' . $invoice->invoice_id . '.pdf');
}
public function csv($id)
{
    // Get invoice data with client name
    $invoice = DB::table('invoices as i')
        ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
        ->select(
            'i.invoice_id',
            'i.invoice_date',
            'i.due_date',
            'i.email',
            'i.phone',
            'i.discount',
            'i.grant_amt',
            'i.other_information',
            'i.billing_address',
            'i.tax',
            'i.tax_amt',
            'i.total',
            'i.status',
            DB::raw("CASE 
                WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE i.client 
            END AS client_name")
        )
        ->where('i.invoice_id', $id)
        ->first();

    if (!$invoice) {
        Session::flash('messageType', 'error');
        Session::flash('message', 'Invoice not found.');
        return redirect()->back();
    }

    // Get tax details
    $taxIds = !is_null($invoice->tax) ? explode(',', $invoice->tax) : [];
    $taxDetails = [];

    if (!empty($taxIds)) {
        $taxDetails = DB::table('taxes')
            ->select('id', 'name', 'percentage')
            ->whereIn('id', $taxIds)
            ->get();
    }

    // Get invoice items
    $invoiceItems = DB::table('invoice_items')
        ->where('invoice_id', $id)
        ->where('deleted_at', 0)
        ->get();

    // Build CSV content
    $filename = 'Invoice-' . $invoice->invoice_id . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($invoice, $invoiceItems, $taxDetails) {
        $file = fopen('php://output', 'w');

        // Invoice main details
        fputcsv($file, ['Invoice ID', $invoice->invoice_id]);
        fputcsv($file, ['Client Name', $invoice->client_name]);
        fputcsv($file, ['Invoice Date', $invoice->invoice_date]);
        fputcsv($file, ['Due Date', $invoice->due_date]);
        fputcsv($file, ['Email', $invoice->email]);
        fputcsv($file, ['Phone', $invoice->phone]);
        fputcsv($file, ['Billing Address', $invoice->billing_address]);
        fputcsv($file, ['Discount', $invoice->discount]);
        fputcsv($file, ['Grant Amount', $invoice->grant_amt]);
        fputcsv($file, ['Tax Amount', $invoice->tax_amt]);
        fputcsv($file, ['Total', $invoice->total]);
        fputcsv($file, ['Status', $invoice->status]);
        fputcsv($file, ['Other Information', $invoice->other_information]);

        // Tax details (if any)
        if (!empty($taxDetails)) {
            fputcsv($file, []);
            fputcsv($file, ['Tax Details']);
            fputcsv($file, ['Tax ID', 'Name', 'Percentage']);
            foreach ($taxDetails as $tax) {
                fputcsv($file, [$tax->id, $tax->name, $tax->percentage]);
            }
        }

        // Invoice Items
        if (count($invoiceItems)) {
            fputcsv($file, []);
            fputcsv($file, ['Invoice Items']);
            fputcsv($file, ['Item ID', 'Description', 'Quantity', 'Rate', 'Amount']);
            foreach ($invoiceItems as $item) {
                fputcsv($file, [
                    $item->id ?? '',
                    $item->description ?? '',
                    $item->quantity ?? '',
                    $item->rate ?? '',
                    $item->amount ?? ''
                ]);
            }
        }

        fclose($file);
    };
    $this->logInvoiceAction(
    $id,
    'csv_downloaded',
    'Invoice CSV downloaded',
    $invoice->client_name,
    $invoice->grant_amt,
    $invoice->status
);


    return Response::stream($callback, 200, $headers);
}

    public function changeStatus(Request $request)
    {
        $invoices = DB::table('invoices')->where('invoice_id', $request->id)->first();

        if ($invoices) {
            DB::table('invoices')->where('invoice_id', $request->id)->update(['status' => $request->status]);
        }

        $this->logInvoiceAction(
    $request->id,
    'status_changed',
    "Status changed to {$request->status}",
    $invoice->client,
    $invoice->grant_amt,
    $request->status
);


        return response()->json([
            'status' => $invoices ? 1 : 0,
            'message' => $invoices ? 'Status has been updated!' : 'Failed to update status!'
        ]);
        
    }

    public function getData(Request $request)
    {
        $query = DB::table('invoices as i')
            ->leftJoin('clients as c', 'i.client', '=', 'c.client_id')
            ->select(
                'i.invoice_id', 
                'i.invoice_date', 
                'i.due_date', 
                'i.grant_amt', 
                'i.status', 
                DB::raw("CASE 
                    WHEN i.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE i.client 
                END AS client_name")
            );
    
        if ($request->fromDate) {
            $query->whereDate('i.invoice_date', '>=', $request->fromDate);
        }
    
        if ($request->toDate) {
            $query->whereDate('i.invoice_date', '<=', $request->toDate);
        }
    
        if ($request->status) {
            $query->where('i.status', $request->status);
        }
    
        $totalRecords = DB::table('invoices')->count(); // Count total records in the estimates table
        $filteredRecords = $query->count(); // Count filtered records based on search criteria
    
        $data = $query->get();
    
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

 

    function calculateTotals($data) {
        $taxPercentages = $this->getTaxPercentages($data['taxes']);
        $totalTaxAmount = $totalAmount = 0;
    
        foreach ($data['items'] as $item) {
            $itemAmount = $item['unitCost'] * $item['quantity'];
            $itemTaxAmount = 0;
    
            foreach ($taxPercentages as $taxPercentage) {
                $itemTaxAmount += $itemAmount * ($taxPercentage / 100);
            }
    
            $totalTaxAmount += $itemTaxAmount;
            $totalAmount += $itemAmount;
        }
    
        return [
            'totalTaxAmount' => $totalTaxAmount,
            'totalAmount' => $totalAmount
        ];
    }

    function getTaxPercentages($taxIds) {
        return DB::table('taxes')
            ->whereIn('id', $taxIds)
            ->pluck('percentage', 'id')
            ->toArray();
    }

    function applyDiscount($totalAmount, $totalTaxAmount, $discountPercentage) {
        if ($discountPercentage == 0) {
            return $totalAmount + $totalTaxAmount;
        }
    
        if ($discountPercentage > 0 && $discountPercentage <= 100) {
            $discountAmount = $totalAmount * ($discountPercentage / 100);
            $totalAmount -= $discountAmount;
        }
    
        return $totalAmount + $totalTaxAmount;
    }


    private function logInvoiceAction($invoiceId, $action, $details = null, $clientName = null, $amount = null, $status = null)
{
    try {
        DB::table('invoice_activity_logs')->insert([
            'invoice_id'  => $invoiceId,
            'action'      => $action,
            'client_name' => $clientName,
            'amount'      => $amount,
            'status'      => $status,
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
        Log::error("Invoice Log Failed: ".$e->getMessage());
        return false;
    }
}


}
