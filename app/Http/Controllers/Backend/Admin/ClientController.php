<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Client\WelcomeMailer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    // CLIENTS
    $company = DB::table('clients')
        ->where('deleted_at',0)
        ->pluck('company_name','client_id');
    
    // Get clients with filters
    $clientsQuery = DB::table('clients')->where('deleted_at',0);
    
    // Apply branch filter
    $clientsQuery = applyBranchFilter($clientsQuery, 'clients');
    
    // Apply status filter if provided
    if ($request->has('status') && $request->status != '') {
        $clientsQuery->where('status', $request->status);
    }
    
    // Apply other filters
    if ($request->has('client_id') && $request->client_id != '') {
        $clientsQuery->where('client_id', 'LIKE', '%' . $request->client_id . '%');
    }
    
    if ($request->has('client_name') && $request->client_name != '') {
        $clientsQuery->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', '%' . $request->client_name . '%');
    }
    
    if ($request->has('company') && $request->company != '') {
        $clientsQuery->where('company_name', $request->company);
    }
    
    $clients = $clientsQuery->get();
    
    // REST OF YOUR CODE...
    $projectController = new \App\Http\Controllers\Backend\Project\ProjectController();
    $projectResponse = $projectController->index($request)->getData();
    
    $projects = $projectResponse['projects'];
    $leaders  = $projectResponse['leaders'];
    $statuses = $projectResponse['statuses'];
    
    $taskController = new \App\Http\Controllers\Backend\Project\TaskController();
    $tasks = $taskController->index(true);
    $myTasks = $taskController->myTasks($request);
    
    return view('hrms.time-tracker.index', compact(
        'clients','company','projects','leaders','statuses','tasks','myTasks'
    ));
}
    

    // Download sample Excel template
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'First Name *',
            'Last Name *',
            'Email *',
            'Phone *',
            'Company Name *',
            'Website URL',
            'Live URL',
            'Address *',
            'Hosting Expiry Date',
            'Hosting AMC Renewal Date',
            'Domain Status *',
            'Domain Expiry Date'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Client_Import_Template.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    public function importClients(Request $request)
    {
        Log::info('importClients called', [
            'user_id' => optional(auth()->user())->id,
            'has_file' => $request->hasFile('file')
        ]);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:51200' // 50MB
        ]);

        if (!$request->hasFile('file')) {
            Log::warning('importClients: no file present');
            return redirect()->back()->with('error', 'No file uploaded.');
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->storeAs('temp', time() . '_' . preg_replace('/\s+/', '_', $originalName));
        $fullPath = storage_path('app/' . $storedPath);

        Log::info('importClients: uploaded file stored', ['stored_path' => $fullPath]);

        // Try loading spreadsheet
        try {
            $spreadsheet = IOFactory::load($fullPath);
            Log::info('Spreadsheet loaded successfully', ['file' => $fullPath]);
        } catch (\Throwable $e) {
            Log::error('Spreadsheet load failed', ['exception' => $e->getMessage()]);
            Storage::delete($storedPath);
            return redirect()->back()->with('error', 'Failed to read Excel file: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (!isset($rows[0])) {
            Log::warning('importClients: spreadsheet empty or missing header', ['file' => $fullPath]);
            Storage::delete($storedPath);
            return redirect()->back()->with('error', 'Excel file seems empty or malformed.');
        }

        // Optional: check header
        $header = array_map('trim', $rows[0]);
        $expected = [
            'First Name *',
            'Last Name *',
            'Email *',
            'Phone *',
            'Company Name *',
            'Website URL',
            'Live URL',
            'Address *',
            'Hosting Expiry Date',
            'Hosting AMC Renewal Date',
            'Domain Status *',
            'Domain Expiry Date'
        ];

        $dataRows = array_slice($rows, 1);
        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 2;

        foreach ($dataRows as $row) {
            $row = array_map(function ($v) {
                return is_string($v) ? trim($v) : $v;
            }, $row);

            $firstName = $row[0] ?? null;
            $lastName  = $row[1] ?? null;
            $email     = $row[2] ?? null;
            $phone     = $row[3] ?? null;
            $company   = $row[4] ?? null;
            $website   = $row[5] ?? null;
           
            $liveURL   = $row[7] ?? null;
            $address   = $row[8] ?? null;
            $hostingExpiry = $row[9] ?? null;
            $hostingAMC    = $row[10] ?? null;
            $domainStatus  = $row[11] ?? null;
            $domainExpiry  = $row[12] ?? null;

            // Skip blank rows
            if (empty($firstName) && empty($lastName) && empty($email)) {
                Log::info('Skipping blank row', ['row' => $rowNumber]);
                $rowNumber++;
                continue;
            }

            $rowErrors = [];

            // Validation
            if (empty($firstName)) $rowErrors[] = 'First Name missing';
            if (empty($lastName))  $rowErrors[] = 'Last Name missing';
            if (empty($email))     $rowErrors[] = 'Email missing';
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $rowErrors[] = 'Invalid Email';
            if (DB::table('clients')->where('email', $email)->exists()) $rowErrors[] = 'Email already exists';
            if (empty($phone))     $rowErrors[] = 'Phone missing';
            if (empty($company))   $rowErrors[] = 'Company Name missing';
          
            if (empty($address))   $rowErrors[] = 'Address missing';
            if (empty($domainStatus)) $rowErrors[] = 'Domain Status missing';

            // Convert date fields
            $hostingExpiryDate = null;
            $hostingAMCDate    = null;
            $domainExpiryDate  = null;

            if (!empty($hostingExpiry)) {
                if (is_numeric($hostingExpiry)) {
                    $hostingExpiryDate = Date::excelToDateTimeObject($hostingExpiry)->format('Y-m-d');
                } else {
                    $d = \DateTime::createFromFormat('m/d/Y', $hostingExpiry);
                    $hostingExpiryDate = $d ? $d->format('Y-m-d') : null;
                }
            }

            if (!empty($hostingAMC)) {
                if (is_numeric($hostingAMC)) {
                    $hostingAMCDate = Date::excelToDateTimeObject($hostingAMC)->format('Y-m-d');
                } else {
                    $d = \DateTime::createFromFormat('m/d/Y', $hostingAMC);
                    $hostingAMCDate = $d ? $d->format('Y-m-d') : null;
                }
            }

            if (!empty($domainExpiry)) {
                if (is_numeric($domainExpiry)) {
                    $domainExpiryDate = Date::excelToDateTimeObject($domainExpiry)->format('Y-m-d');
                } else {
                    $d = \DateTime::createFromFormat('m/d/Y', $domainExpiry);
                    $domainExpiryDate = $d ? $d->format('Y-m-d') : null;
                }
            }

            if (count($rowErrors) > 0) {
                $skipped++;
                $errors[] = "Row {$rowNumber} skipped: " . implode('; ', $rowErrors);
                Log::warning('Row validation skipped', ['row' => $rowNumber, 'errors' => $rowErrors, 'data' => $row]);
                $rowNumber++;
                continue;
            }

            // Insert client
            try {
                $clientId = $this->generateClientID();
                $branchId = Session::get('branch_id');
                $password = Str::random(10);
                $hashedPassword = bcrypt($password);

                DB::table('clients')->insert([
                    'client_id'    => $clientId,
                    'first_name'   => $firstName,
                    'last_name'    => $lastName,
                    'user_name'    => $email,
                    'email'        => $email,
                    'phone'        => $phone,
                    'company_name' => $company,
                    'website_url'      => $website,
                   
                    'live_url'     => $liveURL,
                    'address'      => $address,
                    'hosting_expiry_date' => $hostingExpiryDate,
                    'hosting_amc_renewal_date' => $hostingAMCDate,
                   
                    'domain_expiry_date' => $domainExpiryDate,
                    'branch_id'    => $branchId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

             
                $inserted++;
                Log::info('Inserted client from import', ['row' => $rowNumber, 'email' => $email]);
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "Row {$rowNumber} DB insert failed: " . $e->getMessage();
                Log::error('Database insert error on client import', ['row' => $rowNumber, 'exception' => $e->getMessage()]);
            }

            $rowNumber++;
        }

        Storage::delete($storedPath);

        Log::info('Client import completed', ['inserted' => $inserted, 'skipped' => $skipped, 'errors_count' => count($errors)]);

        $flash = "$inserted imported, $skipped skipped.";
        if (!empty($errors)) {
            return redirect()->route('time-tracker.index')->with('success', $flash)->with('import_errors', $errors);
        }

        return redirect()->route('time-tracker.index')->with('success', $flash);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = DB::table('services')->get();
        return view('hrms.admin.client.create', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:15',
            'company_name' => 'required|string|max:50',
            'client_address' => 'required|string|max:300',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            
            // New fields validation matching the blade file
            'website_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'hosting_type' => 'required|in:hosting_with_us,their_hosting,hosting_maintenance_with_us',
            'hosting_status' => 'required|in:active,inactive,expired',
            'hosting_expiry_date' => 'nullable|date',
            'domain_type' => 'required|in:domain_with_us,their_domain,domain_maintenance_with_us',
            'domain_status' => 'required|in:active,inactive,expired',
            'domain_expiry_date' => 'nullable|date',
            'digital_marketing_start_date' => 'nullable|date',
            'digital_marketing_end_date' => 'nullable|date|after_or_equal:digital_marketing_start_date',
            'hosting_amc_renewal_date' => 'nullable|date',
            'amc_reminder_days' => 'nullable|integer|min:1|max:365'
        ]);

        // Generate a unique client ID
        $clientId = $this->generateClientID();

        // Use email as username
        $username = $validatedData['email'];
        
        // Auto-generate a strong password
        $password = Str::random(10);
        
        // Get branch_id from session
        $branchId = Session::get('branch_id');
        
        // Format dates for new fields
        $hostingExpiry = $validatedData['hosting_expiry_date'] ? Carbon::parse($validatedData['hosting_expiry_date'])->format('Y-m-d') : null;
        $domainExpiry = $validatedData['domain_expiry_date'] ? Carbon::parse($validatedData['domain_expiry_date'])->format('Y-m-d') : null;
        $hostingAMCRenewal = $validatedData['hosting_amc_renewal_date'] ? Carbon::parse($validatedData['hosting_amc_renewal_date'])->format('Y-m-d') : null;
        $digitalMarketingStart = $validatedData['digital_marketing_start_date'] ? Carbon::parse($validatedData['digital_marketing_start_date'])->format('Y-m-d') : null;
        $digitalMarketingEnd = $validatedData['digital_marketing_end_date'] ? Carbon::parse($validatedData['digital_marketing_end_date'])->format('Y-m-d') : null;

        // Insert data into the 'clients' table with new fields
        DB::table('clients')->insert([
            'client_id' => $clientId,
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'user_name' => $username,
            'email' => $validatedData['email'],
            'password' => bcrypt($password),
            'phone' => $validatedData['phone'],
            'company_name' => $validatedData['company_name'],
            'address' => $validatedData['client_address'],
            'branch_id' => $branchId,
            'services' => json_encode($validatedData['services']),
            
            // New fields matching blade form
            'website_url' => $validatedData['website_url'],
            'live_url' => $validatedData['live_url'],
            'hosting_type' => $validatedData['hosting_type'],
            'hosting_status' => $validatedData['hosting_status'],
            'hosting_expiry_date' => $hostingExpiry,
            'domain_type' => $validatedData['domain_type'],
            'domain_status' => $validatedData['domain_status'],
            'domain_expiry_date' => $domainExpiry,
            'digital_marketing_start_date' => $digitalMarketingStart,
            'digital_marketing_end_date' => $digitalMarketingEnd,
            'hosting_amc_renewal_date' => $hostingAMCRenewal,
            'amc_reminder_days' => $validatedData['amc_reminder_days'] ?? 30,
            
            // Status field preserved
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->logActivity($clientId, 'created', 'Client created successfully');


        Session::flash('messageType', 'success');
        Session::flash('message', 'Client created successfully!');
      
        return redirect()->route('time-tracker.index');
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
        $client = DB::table('clients')->where('client_id', $id)->first();
        $services = DB::table('services')->get();
        
        if (!$client) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Client not found.');
            return redirect()->route('client.index');
        }
        
        // Decode the JSON services string to array
        $clientServices = json_decode($client->services ?? '[]', true);
        
        return view('hrms.admin.client.edit', compact('client', 'services', 'clientServices'));
    }

    public function update(Request $request, $id)
    {
        $client = DB::table('clients')->where('client_id', $id)->first();

        if (!$client) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Client not found.');
            return redirect()->route('client.index');
        }
        
        // Validation rules matching the blade form fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:clients,user_name,' . $client->id . ',id',
            'email' => 'required|email|unique:clients,email,' . $client->id . ',id',
            'password' => 'nullable|string|min:8',
            'phone' => 'required|string|max:15',
            'company_name' => 'required|string|max:50',
            'client_address' => 'required|string|max:300',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            
            // New fields validation matching blade form
            'website_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'hosting_type' => 'required|in:hosting_with_us,their_hosting,hosting_maintenance_with_us',
            'hosting_status' => 'required|in:active,inactive,expired',
            'hosting_expiry_date' => 'nullable|date',
            'domain_type' => 'required|in:domain_with_us,their_domain,domain_maintenance_with_us',
            'domain_status' => 'required|in:active,inactive,expired',
            'domain_expiry_date' => 'nullable|date',
            'digital_marketing_start_date' => 'nullable|date',
            'digital_marketing_end_date' => 'nullable|date|after_or_equal:digital_marketing_start_date',
            'hosting_amc_renewal_date' => 'nullable|date',
            'amc_reminder_days' => 'nullable|integer|min:1|max:365'
        ]);
        
        // Format dates for new fields
        $hostingExpiry = $request->hosting_expiry_date ? Carbon::parse($request->hosting_expiry_date)->format('Y-m-d') : null;
        $domainExpiry = $request->domain_expiry_date ? Carbon::parse($request->domain_expiry_date)->format('Y-m-d') : null;
        $hostingAMCRenewal = $request->hosting_amc_renewal_date ? Carbon::parse($request->hosting_amc_renewal_date)->format('Y-m-d') : null;
        $digitalMarketingStart = $request->digital_marketing_start_date ? Carbon::parse($request->digital_marketing_start_date)->format('Y-m-d') : null;
        $digitalMarketingEnd = $request->digital_marketing_end_date ? Carbon::parse($request->digital_marketing_end_date)->format('Y-m-d') : null;

        // Update the client details with new fields
        DB::table('clients')->where('client_id', $id)->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'user_name' => $request->input('username'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'company_name' => $request->input('company_name'),
            'address' => $request->input('client_address'),
            'services' => json_encode($request->input('services')),
            
            // New fields matching blade form
            'website_url' => $request->input('website_url'),
            'live_url' => $request->input('live_url'),
            'hosting_type' => $request->input('hosting_type'),
            'hosting_status' => $request->input('hosting_status'),
            'hosting_expiry_date' => $hostingExpiry,
            'domain_type' => $request->input('domain_type'),
            'domain_status' => $request->input('domain_status'),
            'domain_expiry_date' => $domainExpiry,
            'digital_marketing_start_date' => $digitalMarketingStart,
            'digital_marketing_end_date' => $digitalMarketingEnd,
            'hosting_amc_renewal_date' => $hostingAMCRenewal,
            'amc_reminder_days' => $request->input('amc_reminder_days') ?? 30,
            
            'password' => $request->filled('password') ? Hash::make($request->input('password')) : $client->password,
            'updated_at' => now()
        ]);
        $this->logActivity($id, 'updated', 'Client updated successfully');

        Session::flash('messageType', 'success');
        Session::flash('message', 'Client updated successfully!');
        return redirect()->route('time-tracker.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('clients')->where('client_id', $id)->delete();
        $this->logActivity($id, 'deleted', 'Client deleted successfully');
        return response(['status' => 'success', 'message' => 'Deleted Successfully!','id'=>$id]);
    }

    public function clientList(){
        $company = DB::table('clients')
        ->where('deleted_at', 0)
        ->pluck('company_name', 'client_id');
        $clients = DB::table('clients')->where('deleted_at',0)->get();
        return view('hrms.admin.client.clientView',compact('clients','company'));
    }

    public function showProfile($id){
        $client = DB::table('clients')->where('client_id', $id)->first();

        if (!$client) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Client not found.');
            return redirect()->route('client.index');
        }
    
        // Fetch projects associated with the client using a join query
        $projects = DB::table('projects')
            ->join('clients', 'projects.client', '=', 'clients.client_id')
            ->select('projects.*', 'clients.company_name')
            ->where('clients.client_id', $id)
            ->where('projects.deleted_at', 0)
            ->get();

        $tasks = DB::table('tasks')
            ->join('projects', 'projects.projectid', '=', 'tasks.projects')
            ->select('tasks.*')
            ->where('projects.client', $id)
            ->where('projects.deleted_at', 0)
            ->where('tasks.deleted_at', 0)
            ->get();
    
        return view('hrms.admin.client.profile', compact('client', 'projects','tasks'));
    }

    private function generateClientID()
    {
        // Get the last client ID or set the starting number
        $lastID = DB::table('clients')
        ->whereNotNull('client_id')
        ->orderBy('id', 'desc')
        ->value('client_id');

        // Initialize base ID
        $baseID = 'CLT-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        // Generate new client ID
        do {
            $newClientID = $baseID . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('clients')->where('client_id', $newClientID)->exists());

        return $newClientID;
    }

    public function changeStatus(Request $request)
{
    $validStatuses = [
        'active', 'inactive', 'completed', 'live', 'wip', 'staging', 
        'staging_waiting_approval', 'staging_client_modified', 
        'client_not_renewed', 'file_moved_to_client'
    ];

    if (!in_array($request->status, $validStatuses)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid status!']);
    }

    $client = DB::table('clients')->where('client_id', $request->id)->first();

    if ($client) {
        DB::table('clients')->where('client_id', $request->id)->update(['status' => $request->status]);
        
        // Return appropriate status code based on the new status
        $statusCode = in_array($request->status, ['active', 'completed', 'live']) ? 1 : 2;
        
        return response()->json(['status' => $statusCode, 'message' => 'Status has been updated!']);
    }

    return response()->json(['status' => 'error', 'message' => 'Client not found!']);
}
    /**
     * Send AMC renewal reminders manually
     */
    public function sendAMCReminders()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $clients = DB::table('clients')
            ->where('deleted_at', 0)
            ->whereNotNull('hosting_amc_renewal_date')
            ->whereNotNull('amc_reminder_days')
            ->get();

        $sentCount = 0;
        
        foreach ($clients as $client) {
            try {
                // Calculate the reminder date based on individual client setting
                $reminderDate = Carbon::parse($client->hosting_amc_renewal_date)
                    ->subDays($client->amc_reminder_days)
                    ->format('Y-m-d');
                
                // Check if today is the reminder date for this client
                if ($today === $reminderDate) {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($client->hosting_amc_renewal_date));
                    
                    Mail::to($client->email)
                        ->send(new AMCRenewalReminder($client, $daysLeft, $client->hosting_amc_renewal_date));
                    
                    $sentCount++;
                    
                    \Log::info("AMC reminder sent to {$client->email} ({$client->amc_reminder_days} days before expiry)");
                }
                
            } catch (\Exception $e) {
                \Log::error("Failed to send AMC reminder to {$client->email}: {$e->getMessage()}");
            }
        }

        Session::flash('messageType', 'success');
        Session::flash('message', "AMC renewal reminders sent to {$sentCount} clients.");
        
        return redirect()->route('time-tracker.index');
    }

    /**
     * Update AMC reminder days for a client
     */
    public function updateAmcReminder(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'amc_reminder_days' => 'required|integer|min:1|max:365'
        ]);

        try {
            DB::table('clients')
                ->where('client_id', $request->client_id)
                ->update([
                    'amc_reminder_days' => $request->amc_reminder_days,
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'AMC reminder days updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update AMC reminder days: ' . $e->getMessage()
            ], 500);
        }
    }
    private function logActivity($clientId, $action, $description = null)
{
    $role = Session::get('role');
    
    // Resolve user ID for employee or admin
    if ($role === 'employee') {
        $userId = Session::get('user_id');
        $performedBy = 'employee';
    } else {
        // Default for admin
        $userId = Session::get('admin_id'); 
        $performedBy = 'admin';
    }

    DB::table('client_activity_log')->insert([
        'client_id' => $clientId,
        'action' => $action,
        'performed_by' => $performedBy,
        'user_id' => $userId,
        'description' => $description,
        'created_at' => now()
    ]);
}
public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array'
    ]);

    DB::table('clients')
        ->whereIn('client_id', $request->ids)
        ->delete();

    foreach ($request->ids as $clientId) {
        $this->logActivity($clientId, 'deleted', 'Client deleted (bulk delete)');
    }

    Session::flash('messageType', 'success');
    Session::flash('message', count($request->ids) . " clients deleted successfully!");

    return redirect()->route('time-tracker.index');
}


}