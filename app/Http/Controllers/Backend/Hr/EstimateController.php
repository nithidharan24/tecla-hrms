<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use App\Mail\Estimate\EstimationMailer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
// use App\Exports\BrandExport;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Str;
 use Symfony\Component\HttpFoundation\StreamedResponse;

class EstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request)
{
    $branchId = getAdminBranchFilter();
    
    // Start with a subquery or modify the main query to handle name filtering better
    $query = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
        ->select(
            'e.estimate_id', 
            'e.estimate_date', 
            'e.expiry_date', 
            'e.grant_amt', 
            'e.status', 
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name"),
            'e.client as client_raw', // Add this for better filtering
            'c.first_name',
            'c.last_name'
        );
    
    // Apply branch filter
    if ($branchId) {
        $query->where('e.branch_id', $branchId);
    }
    
    // Apply client name filter - FIXED VERSION
    if ($request->filled('client_name')) {
        $searchTerm = '%' . $request->client_name . '%';
        
        $query->where(function($q) use ($searchTerm) {
            $q->where('c.first_name', 'like', $searchTerm)
              ->orWhere('c.last_name', 'like', $searchTerm)
              ->orWhere('c.first_name', 'like', str_replace(' ', '%', $searchTerm))
              ->orWhere('c.last_name', 'like', str_replace(' ', '%', $searchTerm))
              ->orWhereRaw("CONCAT(c.first_name, ' ', c.last_name) LIKE ?", [$searchTerm])
              ->orWhere('e.client', 'like', $searchTerm);
        });
    }
    
    // Apply date filters
    if ($request->filled('from_date')) {
        try {
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $query->whereDate('e.estimate_date', '>=', $fromDate);
        } catch (\Exception $e) {
            return back()->withErrors(['messages' => 'Invalid from date format. Please use YYYY-MM-DD format.']);
        }
    }

    if ($request->filled('to_date')) {
        try {
            $toDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereDate('e.estimate_date', '<=', $toDate);
        } catch (\Exception $e) {
            return back()->withErrors(['messages' => 'Invalid to date format. Please use YYYY-MM-DD format.']);
        }
    }

    // Apply status filter
    if ($request->filled('status')) {
        $query->where('e.status', $request->status);
    }

    // Calculate total amount for summary
    $totalAmount = (clone $query)->sum('e.grant_amt');

    // Add pagination - 10 records per page
    $estimates = $query->orderBy('estimate_date', 'desc')
                      ->paginate(10)
                      ->appends($request->query());

    $logs = DB::table('estimate_activity_logs')
        ->orderBy('action_date', 'desc')
        ->limit(100)
        ->get();

    return view('hrms.hr.sales.estimate.index', compact('estimates', 'totalAmount','logs'))
           ->with('messages', $estimates->isEmpty() ? 'No records found' : '');
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //clients
        $clients = DB::table('clients')->where('status','active')->where('deleted_at',0)
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
 


        return view('hrms.hr.sales.estimate.create',compact('clients','projects','tax'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Generate a unique client ID
        // dd($request->all());
        $Id = $this->generateID('est');
        $rules = [
            'email' => 'required|email',
            'project_type' => 'required|string',
            'client_type' => 'required|string',
            'mobile' => 'required|min:10',
            'client_address' => 'required|string',
            'billing_address' => 'required|string',
            'estdate' => 'required|date_format:d-m-Y',
            'expdate' => 'required|date_format:d-m-Y',
            'total' => 'required|numeric',
            'totaltaxAmt' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'grantAmt' => 'required|numeric',
            'ex_project' => [
                'required_if:client_type,existing|required_if:project_type,existing|string',
            ],
            'ex_nw_project' => [
                'required_if:client_type,existing|required_if:project_type,new|string',
            ],
            'nw_project' => [
                'required_if:client_type,new|string',
            ],
            'other' => 'nullable|string',
            'taxes' => 'nullable|array',
            'taxes.*' => 'nullable|numeric'
        ];

        $taxes = $request->has('taxes') && is_array($request->input('taxes')) 
            ? array_filter($request->input('taxes'), function($value) {
                return !is_null($value) && $value !== '';  // Filter out null and empty values
            })
            : [];

        $tax = !empty($taxes) ? implode(',', $taxes) : null;

        $projectType = $request->input('project_type');
        $clientType = $request->input('client_type');
        $project = null;

        if ($clientType === 'new') {
            $project = $request->input('nw_project');
        } elseif ($clientType === 'existing') {
            $project = $projectType === 'new' ? $request->input('ex_nw_project') : $request->input('ex_project');
        }


        $request->validate($rules);

        // Convert dates from d-m-Y to Y-m-d format
        $estimateDate = Carbon::createFromFormat('d-m-Y', $request->input('estdate'))->format('Y-m-d');
        $expiryDate = Carbon::createFromFormat('d-m-Y', $request->input('expdate'))->format('Y-m-d');
        
        $totals = $this->calculateTotals([
            'taxes' => $taxes,
            'items' => $request->input('items',[])
        ]);

        $grant_total = $this->applyDiscount($totals['totalAmount'],$totals['totalTaxAmount'],$request->input('discount',0));
        $grant_total_rdf = number_format(round($grant_total), 2, '.', '');
        $branchId = Session::get('branch_id');

        $insertData = [
            'estimate_id' => $Id,
            'email' => $request->input('email'),
            'phone' => $request->input('mobile'),
            'client' => $request->input($request->input('client_type') === 'new' ? 'nw_client' : 'ex_client'),
            'project' => $project,
            'client_address' => $request->input('client_address'),
            'billing_address' => $request->input('billing_address'),
            'estimate_date' => $estimateDate, // Use the formatted date
            'expiry_date' => $expiryDate, // Use the formatted date
            'total' => number_format($totals['totalAmount'], 2, '.', ''),
            'tax_amt' => number_format($totals['totalTaxAmount'], 2, '.', '') ?? 0,
            'discount' => $request->input('discount', 0),
            'grant_amt' => $grant_total_rdf,
            'branch_id' => $branchId, // store branch_id from session
            'other_information' => $request->input('other'),
            'tax' => $tax
        ];

        // Insert data
        $result = DB::table('estimates')->insert($insertData);
        $estimate_Id='';
        if ($result) {
            $estimate_Id = $Id;
        } else {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Failed to insert estimate or no items provided!');
            return redirect()->back();
        }

        if ($estimate_Id && !empty($request->items)) {
            foreach ($request->items as $item) {
                DB::table('estimate_items')->insert([
                    'estimate_id' => $estimate_Id,
                    'item' => $item['name'],
                    'description' => $item['description'],
                    'unit_cost' => $item['unitCost'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                ]);
            }
            Session::flash('messageType', 'success');
            Session::flash('message', 'Estimate Created!');

            $this->sendEstimateEmail($estimate_Id);
        } else {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Failed to insert estimate or no items provided!');
            return redirect()->back();
        }
        $this->logEstimateAction(
    $Id,
    'created',
    'Estimate created',
    $insertData['client'],
    $insertData['grant_amt'],
    'pending'
);


        return redirect()->route('estimate.index');
        
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
  $estimates = DB::table('estimates')->where('estimate_id', $id)->first();
    
    // Format the dates from Y-m-d to d-m-Y for display in the form
    if ($estimates) {
        $estimates->estimate_date = Carbon::parse($estimates->estimate_date)->format('d-m-Y');
        $estimates->expiry_date = Carbon::parse($estimates->expiry_date)->format('d-m-Y');
    }
    
    $estimate_items = DB::table('estimate_items')->where('estimate_id', $id)->get();
        $clients = DB::table('clients')->where('status', 'active')->where('deleted_at', 0)
            ->select('client_id', 'first_name', 'last_name')
            ->get();
                
        
        // Fetch tax details
        $tax = DB::table('taxes')
            ->where('status', 'active')
            ->where('deleted_at', 0)
            ->select('id', 'name', 'percentage')
            ->get();
        
        // Prepare client and project variables based on estimates data
        $client_id = $estimates->client; // assuming this contains the client ID or client name
        $project_id = $estimates->project; // assuming this contains the project ID or project name
        
        // Determine the client type and values to fill
        $client_type = 'new'; // Default to new
        $new_client = '';
        $existing_client_id = '';
        $existing_project_id = '';
        $new_project = '';
        
        if (str_starts_with($client_id, 'CLT-')) {
            // Existing Client
            $client_type = 'existing';
            $existing_client_id = $client_id; // Existing Client ID
        } else {
            // New Client
            $new_client = $client_id; // Normal client name
        }
        
        if (str_starts_with($project_id, 'PRO-')) {
            // Existing Project
            $existing_project_id = $project_id; // Existing Project ID
        } else {
            // New Project
            $new_project = $project_id; // Normal project name
        }

        // Fetch projects as needed
        $projects = DB::table('projects')
        ->where('status', 'active')
        ->where('deleted_at', 0)
        ->where('client',$client_id)
        ->select('projectid', 'projectname')
        ->get();
        
        return view('hrms.hr.sales.estimate.edit', compact('estimates', 'clients', 'projects', 'tax', 'estimate_items', 'client_type', 'existing_client_id', 'new_client', 'existing_project_id', 'new_project'));
        
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
        $rules = [
            'email' => 'required|email',
            'project_type' => 'required|string',
            'client_type' => 'required|string',
            'mobile' => 'required|min:10',
            'client_address' => 'required|string',
            'billing_address' => 'required|string',
            'estdate' => 'required|date_format:d-m-Y',
            'expdate' => 'required|date_format:d-m-Y',
            'total' => 'required|numeric',
            'totaltaxAmt' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'grantAmt' => 'required|numeric',
            'ex_project' => [
                'required_if:client_type,existing|required_if:project_type,existing|string',
            ],
            'ex_nw_project' => [
                'required_if:client_type,existing|required_if:project_type,new|string',
            ],
            'nw_project' => [
                'required_if:client_type,new|string',
            ],
            'other' => 'nullable|string',
            'taxes' => 'nullable|array',
            'taxes.*' => 'nullable|numeric'
        ];
    
        // Validate the request data
        $request->validate($rules);
    
        // Filter taxes
        $taxes = $request->has('taxes') && is_array($request->input('taxes'))
            ? array_filter($request->input('taxes'), function($value) {
                return !is_null($value) && $value !== ''; // Filter out null and empty values
            })
            : [];
    
        $tax = !empty($taxes) ? implode(',', $taxes) : null;
    
        // Convert dates from d-m-Y to Y-m-d format
        $estimateDate = Carbon::createFromFormat('d-m-Y', $request->input('estdate'))->format('Y-m-d');
        $expiryDate = Carbon::createFromFormat('d-m-Y', $request->input('expdate'))->format('Y-m-d');
    
        // Determine project based on client and project type
        $clientType = $request->input('client_type');
        $projectType = $request->input('project_type');
        $project = null;
    
        if ($clientType === 'new') {
            $project = $request->input('nw_project');
        } elseif ($clientType === 'existing') {
            $project = $projectType === 'new' ? $request->input('ex_nw_project') : $request->input('ex_project');
        }

        $totals = $this->calculateTotals([
            'taxes' => $taxes,
            'items' => $request->input('items',[])
        ]);

        $grant_total = $this->applyDiscount($totals['totalAmount'],$totals['totalTaxAmount'],$request->input('discount',0));
        $grant_total_rdf = number_format(round($grant_total), 2, '.', '');
       
        // Prepare data for updating the estimate
        $updateData = [
            'email' => $request->input('email'),
            'phone' => $request->input('mobile'),
            'client' => $request->input($clientType === 'new' ? 'nw_client' : 'ex_client'),
            'project' => $project,
            'client_address' => $request->input('client_address'),
            'billing_address' => $request->input('billing_address'),
            'estimate_date' => $estimateDate,
            'expiry_date' => $expiryDate,
            'total' => number_format($totals['totalAmount'], 2, '.', ''),
            'tax_amt' => number_format($totals['totalTaxAmount'], 2, '.', '') ?? 0,
            'discount' => $request->input('discount', 0),
            'grant_amt' => $grant_total_rdf,
            'other_information' => $request->input('other'),
            'tax' => $tax
        ];
    
        // Update the estimate
        DB::table('estimates')->where('estimate_id', $id)->update($updateData);
    
        // Fetch existing estimate item IDs
        $existingIds = DB::table('estimate_items')
            ->where('estimate_id', $id)
            ->pluck('id')
            ->toArray(); // Convert to array for easier comparison
    
        // Process estimate items
        if ($request->has('items')) {
            // Create an array to store IDs of items from the request
            $requestIds = [];
    
            foreach ($request->input('items') as $item) {
                if (isset($item['id'])) {
                    // If the item has an ID, update the existing item
                    DB::table('estimate_items')->where('id', $item['id'])->update([
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
                    DB::table('estimate_items')->insert([
                        'estimate_id' => $id,
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
                DB::table('estimate_items')->whereIn('id', $idsToDelete)->delete();
            }
    
            Session::flash('messageType', 'success');
            Session::flash('message', 'Estimate Updated Successfully!');
        } else {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Failed to update estimate items. No items provided!');
            return redirect()->back();
        }
        $this->logEstimateAction(
    $id,
    'updated',
    'Estimate updated',
    $request->input('client_type') === 'new' ? $request->nw_client : $request->ex_client,
    $grant_total_rdf,
    'updated'
);

    
        return redirect()->route('estimate.index');
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
            DB::transaction(function () use ($id) {
                DB::table('estimate_items')->where('estimate_id', $id)->delete();
                DB::table('estimates')->where('estimate_id', $id)->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted Successfully!',
                'id' => $id
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
        $this->logEstimateAction(
    $id,
    'deleted',
    "Estimate $id deleted",
    null,
    null,
    null
);

    }

    private function generateID($base)
    {
        // Get the last client ID or set the starting number
        $lastID = DB::table('estimates')
        ->whereNotNull('estimate_id')
        ->orderBy('id', 'desc')
        ->value('estimate_id');

        // Initialize base ID
        $baseID = Str::upper($base).'-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        // Generate new client ID
        do {
            $newID = $baseID . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('estimates')->where('estimate_id', $newID)->exists());

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

    public function viewEstimate($id){

        $estimates = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'e.estimate_id', 
            'e.estimate_date', 
            'e.expiry_date', 
            'e.email',
            'e.phone',
            'e.grant_amt', 
            'e.other_information',
            'e.discount',
            'e.billing_address',
            'e.tax',
            'e.tax_amt',
            'e.total', 
            'e.status', 
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name")
        )
        ->where('e.estimate_id', $id)
        ->first();

        if (!$estimates) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Estimate not found.');
            return redirect()->back();
        }

        $tax = !is_null($estimates->tax) ? explode(',', $estimates->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        $estimate_items = DB::table('estimate_items')
        ->where('estimate_id', $id)
        ->where('deleted_at', 0)
        ->get();

        // dd($estimates);
        return view('hrms.hr.sales.estimate.estimateView',compact('estimates','estimate_items','taxDetails'));
    }

    public function pdf($id)
    {
        $estimates = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'e.estimate_id', 
            'e.estimate_date', 
            'e.expiry_date', 
            'e.email',
            'e.phone',
            'e.grant_amt', 
            'e.other_information',
            'e.discount',
            'e.billing_address',
            'e.tax',
            'e.tax_amt',
            'e.total', 
            'e.status', 
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name")
        )
        ->where('e.estimate_id', $id)
        ->first();

        if (!$estimates) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Estimate not found.');
            return redirect()->back();
        }

        $tax = !is_null($estimates->tax) ? explode(',', $estimates->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }
        $this->logEstimateAction(
    $id,
    'pdf_downloaded',
    'Estimate PDF downloaded',
    $estimates->client_name,
    $estimates->grant_amt,
    $estimates->status
);


        $estimate_items = DB::table('estimate_items')
        ->where('estimate_id', $id)
        ->where('deleted_at', 0)
        ->get();
        $pdf = PDF::loadView('hrms.hr.sales.estimate.export', compact('estimates','estimate_items','taxDetails'));
        return $pdf->download('Export-Estimate' . time() . rand(99, 9999) . '.pdf');
        // return view('hrms.hr.sales.estimate.export',compact('estimates','estimate_items','taxDetails'));
    }

    public function pdfPrint($id)
    {
        $estimates = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'e.estimate_id', 
            'e.estimate_date', 
            'e.expiry_date', 
            'e.email',
            'e.phone',
            'e.grant_amt', 
            'e.other_information',
            'e.discount',
            'e.billing_address',
            'e.tax',
            'e.tax_amt',
            'e.total', 
            'e.status', 
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name")
        )
        ->where('e.estimate_id', $id)
        ->first();

        if (!$estimates) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Estimate not found.');
            return redirect()->back();
        }

        $tax = !is_null($estimates->tax) ? explode(',', $estimates->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        $estimate_items = DB::table('estimate_items')
        ->where('estimate_id', $id)
        ->where('deleted_at', 0)
        ->get();
        $pdf = PDF::loadView('hrms.hr.sales.estimate.export', compact('estimates','estimate_items','taxDetails'));
        return $pdf->stream('Export-Estimate' . time() . rand(99, 9999) . '.pdf');
        // return view('hrms.hr.sales.estimate.export',compact('estimates','estimate_items','taxDetails'));
    }


public function downloadEstimateCsv($id)
{
    $estimate = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
        ->select(
            'e.estimate_id',
            'e.estimate_date',
            'e.expiry_date',
            'e.email',
            'e.phone',
            'e.discount',
            'e.grant_amt',
            'e.other_information',
            'e.billing_address',
            'e.tax',
            'e.tax_amt',
            'e.total',
            'e.status',
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name")
        )
        ->where('e.estimate_id', $id)
        ->first();

    if (!$estimate) {
        Session::flash('messageType', 'error');
        Session::flash('message', 'Estimate not found.');
        return redirect()->back();
    }

    $taxIds = !is_null($estimate->tax) ? explode(',', $estimate->tax) : [];
    $taxDetails = [];
    if (!empty($taxIds)) {
        $taxDetails = DB::table('taxes')
            ->select('id', 'name', 'percentage')
            ->whereIn('id', $taxIds)
            ->get();
    }

    $estimateItems = DB::table('estimate_items')
        ->where('estimate_id', $id)
        ->where('deleted_at', 0)
        ->get();

    $filename = 'Estimate-' . $estimate->estimate_id . '.csv';

    $response = new StreamedResponse(function () use ($estimate, $taxDetails, $estimateItems) {
        $handle = fopen('php://output', 'w');

        // Write estimate main data
        fputcsv($handle, ['Estimate ID', $estimate->estimate_id]);
        fputcsv($handle, ['Client Name', $estimate->client_name]);
        fputcsv($handle, ['Estimate Date', $estimate->estimate_date]);
        fputcsv($handle, ['Expiry Date', $estimate->expiry_date]);
        fputcsv($handle, ['Email', $estimate->email]);
        fputcsv($handle, ['Phone', $estimate->phone]);
        fputcsv($handle, ['Billing Address', $estimate->billing_address]);
        fputcsv($handle, ['Discount', $estimate->discount]);
        fputcsv($handle, ['Grant Amount', $estimate->grant_amt]);
        fputcsv($handle, ['Tax Amount', $estimate->tax_amt]);
        fputcsv($handle, ['Total', $estimate->total]);
        fputcsv($handle, ['Status', $estimate->status]);
        fputcsv($handle, ['Other Information', $estimate->other_information]);

        // Tax details
        if ($taxDetails->count()) {
            fputcsv($handle, []);
            fputcsv($handle, ['Tax Details']);
            fputcsv($handle, ['ID', 'Name', 'Percentage']);
            foreach ($taxDetails as $tax) {
                fputcsv($handle, [$tax->id, $tax->name, $tax->percentage]);
            }
        }

        // Estimate items
        if ($estimateItems->count()) {
            fputcsv($handle, []);
            fputcsv($handle, ['Estimate Items']);
            fputcsv($handle, ['Item ID', 'Description', 'Quantity', 'Rate', 'Amount']);
            foreach ($estimateItems as $item) {
                fputcsv($handle, [
                    $item->id ?? '',
                    $item->description ?? '',
                    $item->quantity ?? '',
                    $item->rate ?? '',
                    $item->amount ?? ''
                ]);
            }
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');

    return $response;
}

    public function sendEstimateEmail($id){
        $estimates = DB::table('estimates as e')
        ->leftJoin('clients as c', 'e.client', '=', 'c.client_id') // Join on client ID
        ->select(
            'e.estimate_id', 
            'e.estimate_date', 
            'e.expiry_date', 
            'e.email',
            'e.phone',
            'e.grant_amt', 
            'e.other_information',
            'e.discount',
            'e.billing_address',
            'e.tax',
            'e.tax_amt',
            'e.total', 
            'e.status', 
            DB::raw("CASE 
                WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                ELSE e.client 
            END AS client_name")
        )
        ->where('e.estimate_id', $id)
        ->first();

        if (!$estimates) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Estimate not found.');
            return redirect()->back();
        }

        $tax = !is_null($estimates->tax) ? explode(',', $estimates->tax) : [];

        $taxDetails = [];
        if (!empty($tax)) {
            $taxDetails = DB::table('taxes')
                ->select('id', 'name', 'percentage')
                ->whereIn('id', $tax)
                ->get();
        }

        $estimate_items = DB::table('estimate_items')
        ->where('estimate_id', $id)
        ->where('deleted_at', 0)
        ->get();
        
$this->logEstimateAction(
    $id,
    'email_sent',
    'Estimate email sent to client',
    $estimates->client_name,
    $estimates->grant_amt,
    $estimates->status
);

        $pdf = PDF::loadView('hrms.hr.sales.estimate.export', compact('estimates', 'estimate_items', 'taxDetails'));
        $pdfContent = $pdf->output();
    
        Mail::to($estimates->email)->send(new EstimationMailer($estimates->client_name, $estimates->email, $pdfContent));

        return response()->json(['success' => 'Email sent successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $estimate = DB::table('estimates')->where('estimate_id', $request->id)->first();
        $this->logEstimateAction(
    $request->id,
    'status_changed',
    "Status changed to {$request->status}",
    null,
    null,
    $request->status
);


        if ($estimate) {
            DB::table('estimates')->where('estimate_id', $request->id)->update(['status' => $request->status]);
        }

        return response()->json([
            'status' => $estimate ? 1 : 0,
            'message' => $estimate ? 'Status has been updated!' : 'Failed to update status!'
        ]);
        
    }

    public function getData(Request $request)
    {
        $query = DB::table('estimates as e')
            ->leftJoin('clients as c', 'e.client', '=', 'c.client_id')
            ->select(
                'e.estimate_id', 
                'e.estimate_date', 
                'e.expiry_date', 
                'e.grant_amt', 
                'e.status', 
                DB::raw("CASE 
                    WHEN e.client LIKE 'CLT-%' THEN CONCAT(c.first_name, ' ', c.last_name) 
                    ELSE e.client 
                END AS client_name")
            );
    
        if ($request->fromDate) {
            $query->whereDate('e.estimate_date', '>=', $request->fromDate);
        }
    
        if ($request->toDate) {
            $query->whereDate('e.estimate_date', '<=', $request->toDate);
        }
    
        if ($request->status) {
            $query->where('e.status', $request->status);
        }
    
        $totalRecords = DB::table('estimates')->count(); // Count total records in the estimates table
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
    
    private function logEstimateAction($estimateId, $action, $details = null, $clientName = null, $amount = null, $status = null)
{
    try {
        $logData = [
            'estimate_id'   => $estimateId,
            'action'        => $action,
            'client_name'   => $clientName,
            'amount'        => $amount,
            'status'        => $status,
            'details'       => $details,
            'user_id'       => auth()->id() ?? 1,
            'user_name'     => auth()->user()->name ?? 'System',
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'action_date'   => now(),
            'created_at'    => now(),
            'updated_at'    => now()
        ];

        DB::table('estimate_activity_logs')->insert($logData);
        return true;

    } catch (\Exception $e) {
        // fallback minimal logging
        DB::table('estimate_activity_logs')->insert([
            'estimate_id' => $estimateId,
            'action' => $action,
            'details' => $details,
            'action_date' => now()
        ]);
        return false;
    }
}

    
    


}
