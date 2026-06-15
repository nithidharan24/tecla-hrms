<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
    /**
     * Generate unique testing ticket ID in format TST-DDMMYY###
     */
    private function generateTestingTicketId()
    {
        $today = Carbon::now();
        $datePrefix = $today->format('dmy'); // DDMMYY format
        
        // Get the last testing ticket created today with this date prefix
        $lastTestingTicket = DB::table('testing_tickets')
            ->where('testing_ticket_id', 'like', "TST-{$datePrefix}%")
            ->orderBy('testing_ticket_id', 'desc')
            ->first();

        if ($lastTestingTicket) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastTestingTicket->testing_ticket_id, -3);
            $newSequence = $lastSequence + 1;
        } else {
            // First testing ticket of the day
            $newSequence = 1;
        }

        // Format: TST-DDMMYY### (3-digit sequence)
        return 'TST-' . $datePrefix . str_pad($newSequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of testing tickets with statistics
     */
    public function index(Request $request)
    {
        $role = session('role', 'employee');
        $employeeId = session('user_id');

        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();

        // 🧩 Base query - REMOVE PROJECT FILTERS
        $query = DB::table('testing_tickets')
            ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
            ->leftJoin('allemployees as assigned', 'testing_tickets.assigned_to', '=', 'assigned.id')
            ->leftJoin('allemployees as creator', 'testing_tickets.created_by', '=', 'creator.id')
            ->leftJoin('allemployees as tester', 'testing_tickets.tester_id', '=', 'tester.id')
            ->select(
                'testing_tickets.*',
                'projects.projectname',
                DB::raw("CONCAT(assigned.firstname, ' ', assigned.lastname) as assigned_name"),
                DB::raw("CONCAT(creator.firstname, ' ', creator.lastname) as creator_name"),
                DB::raw("CONCAT(tester.firstname, ' ', tester.lastname) as tester_name"),
                'assigned.profile_image as assigned_image',
                'creator.profile_image as creator_image',
                'tester.profile_image as tester_image'
            )
            ->where('testing_tickets.deleted_at', 0);

        // 🔹 Apply filters
        if ($branchFilter) {
            $query->where('assigned.branch_id', $branchFilter);
        }
        if ($departmentFilter) {
            $query->where('assigned.department', $departmentFilter);
        }
        if ($managerFilter) {
            $query->where('assigned.manager_id', $managerFilter);
        }

        // 🔹 Role-based logic
        if ($role === 'employee') {
            $query->where(function($q) use ($employeeId) {
                $q->where('testing_tickets.created_by', $employeeId)
                  ->orWhere('testing_tickets.assigned_to', $employeeId)
                  ->orWhere('testing_tickets.tester_id', $employeeId);
            });
        }
  // Get activity logs with filters
    $activityLogs = $this->getActivityLogs($request);
        // 🔹 Request filters
        if ($request->filled('project_id')) {
            $query->where('testing_tickets.project_id', $request->project_id);
        }
        if ($request->filled('ticket_subject')) {
            $query->where('testing_tickets.description', 'like', '%' . $request->ticket_subject . '%');
        }
        if ($request->filled('status')) {
            $query->where('testing_tickets.status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('testing_tickets.priority', $request->priority);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('testing_tickets.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('testing_tickets.created_at', '<=', $request->end_date);
        }

        // 🔹 Get all tickets for tabs
        $allTicketsQuery = clone $query;
        $allTickets = $allTicketsQuery->orderBy('testing_tickets.created_at', 'desc')->get();

        // 🔹 Paginate for table display
        $tickets = $query->orderBy('testing_tickets.created_at', 'desc')->paginate(10);

        // ==================== 📊 Stats Section ====================
        $statsQuery = DB::table('testing_tickets')
            ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
            ->leftJoin('allemployees as stats_assigned', 'testing_tickets.assigned_to', '=', 'stats_assigned.id')
            ->leftJoin('allemployees as stats_tester', 'testing_tickets.tester_id', '=', 'stats_tester.id')
            ->where('testing_tickets.deleted_at', 0);

        // Filters for stats
        if ($request->filled('project_id')) {
            $statsQuery->where('testing_tickets.project_id', $request->project_id);
        }
        if ($request->filled('status')) {
            $statsQuery->where('testing_tickets.status', $request->status);
        }
        if ($request->filled('priority')) {
            $statsQuery->where('testing_tickets.priority', $request->priority);
        }
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('testing_tickets.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('testing_tickets.created_at', '<=', $request->end_date);
        }

        // 🔹 Role filter for stats
        if ($role === 'employee') {
            $statsQuery->where(function($q) use ($employeeId) {
                $q->where('testing_tickets.created_by', $employeeId)
                  ->orWhere('testing_tickets.assigned_to', $employeeId)
                  ->orWhere('testing_tickets.tester_id', $employeeId);
            });
        }

        // 🔹 Branch, Dept, Manager filters for stats
        if ($branchFilter) {
            $statsQuery->where('stats_assigned.branch_id', $branchFilter);
        }
        if ($departmentFilter) {
            $statsQuery->where('stats_assigned.department', $departmentFilter);
        }
        if ($managerFilter) {
            $statsQuery->where('stats_assigned.manager_id', $managerFilter);
        }

        // 🔹 Counts
        $newTicketsCount     = (clone $statsQuery)->where('testing_tickets.status', 'Open')->count();
        $solvedTicketsCount  = (clone $statsQuery)->where('testing_tickets.status', 'Closed')->count();
        $openTicketsCount    = (clone $statsQuery)->where('testing_tickets.status', 'In Progress')->count();
        $pendingTicketsCount = (clone $statsQuery)->where('testing_tickets.status', 'Resolved')->count();
        $reopenTicketsCount  = (clone $statsQuery)->where('testing_tickets.status', 'Reopen')->count();

        // 🔹 Employees for assignment (admin only)
        $employees = [];
        if ($role === 'admin') {
            $employees = DB::table('allemployees')
                ->where('deleted_at', 0)
                ->select('id', 'firstname', 'lastname', 'profile_image')
                ->get();
        }

        // 🔹 Projects for dropdown - KEEP this filter for project dropdown
        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->where('status', '!=', 'Closed')
            ->select('id', 'projectname')
            ->get();

        // Get activity logs
        $activityLogs = $this->getActivityLogs();

        return view('hrms.Employee.testing.index', compact(
            'tickets', 
            'allTickets', 
            'role', 
            'newTicketsCount', 
            'solvedTicketsCount', 
            'openTicketsCount', 
            'pendingTicketsCount', 
            'reopenTicketsCount',
            'employees', 
            'projects',
            'activityLogs'
        ));
    }

    /**
     * Show the form for creating a new testing ticket
     */
    public function create()
    {
        // Fetch active, non-closed projects
        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->where('status', '!=', 'Closed')
            ->select('id', 'projectname', 'team')
            ->get();

        // Fetch all active employees for initial load
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'profile_image')
            ->get();

        return view('hrms.Employee.testing.create', compact('projects', 'employees'));
    }

    /**
     * Store a newly created testing ticket
     */
/**
 * Store a newly created testing ticket with multiple bugs
 */
public function store(Request $request)
{
    $request->validate([
        'project_id'        => 'required|exists:projects,id',
        'priority'          => 'required|in:High,Medium,Low',
        'assigned_to'       => 'nullable|exists:allemployees,id',
        // Validate that at least one bug is provided
        'bugs'              => 'required|array|min:1',
        'bugs.*.module_name' => 'required|string',
        'bugs.*.description' => 'required|string|min:10',
        'bugs.*.steps_to_reproduce' => 'nullable|string',
        'bugs.*.uploaded_files' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
    ]);

    // Generate unique testing ticket ID
    $testingTicketId = $this->generateTestingTicketId();
    $assignedTo      = $request->filled('assigned_to') ? $request->assigned_to : null;

    // Create main testing ticket
    $ticketData = [
        'testing_ticket_id' => $testingTicketId,
        'project_id'        => $request->project_id,
        'priority'          => $request->priority,
        'assigned_to'       => $assignedTo,
        'status'            => 'Open',
        'created_by'        => Auth::id() ?? session('user_id'),
        'deleted_at'        => 0,
        'created_at'        => now(),
        'updated_at'        => now(),
    ];

    // Insert main ticket
    $ticketId = DB::table('testing_tickets')->insertGetId($ticketData);

    // Process each bug
    $uploadedFiles = [];
    
    // Handle file uploads if any
    if ($request->hasFile('bugs')) {
        foreach ($request->file('bugs') as $index => $file) {
            if ($file && $file->isValid()) {
                $safeFileName = time() . '_' . $index . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/testing-tickets');
                
                if (!is_dir($destination)) {
                    @mkdir($destination, 0755, true);
                }
                
                $file->move($destination, $safeFileName);
                $uploadedFiles[$index] = 'uploads/testing-tickets/' . $safeFileName;
            }
        }
    }

    // Insert bugs
    foreach ($request->bugs as $index => $bugData) {
        $bug = [
            'testing_ticket_id' => $ticketId,
            'module_name'       => $bugData['module_name'],
            'description'       => $bugData['description'],
            'steps_to_reproduce'=> $bugData['steps_to_reproduce'] ?? null,
            'uploaded_files'    => $uploadedFiles[$index] ?? null,
            'status'            => 'Open',
            'created_by'        => Auth::id() ?? session('user_id'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
        
        DB::table('testing_bugs')->insert($bug);
    }

    // Send email notification if assigned
    if ($assignedTo) {
        $this->sendAssignmentEmail($assignedTo, $testingTicketId, $request->project_id, $request->priority, $request->bugs[0]['description'] ?? '');
    }

    // Log the activity
    $this->logActivity(
        $ticketId,
        'created',
        "Testing Ticket #{$testingTicketId} was created with " . count($request->bugs) . " bugs",
        ['bug_count' => count($request->bugs)]
    );

    return redirect()
        ->route('testing.index')
        ->with('success', 'Testing ticket created successfully! Ticket ID: ' . $testingTicketId . ' with ' . count($request->bugs) . ' bugs.');
}

/**
 * Send assignment email
 */
private function sendAssignmentEmail($assignedTo, $testingTicketId, $projectId, $priority, $description)
{
    $employee = DB::table('allemployees')
        ->where('id', $assignedTo)
        ->where('deleted_at', 0)
        ->first();

    if ($employee && $employee->email) {
        $projectName = DB::table('projects')
            ->where('id', $projectId)
            ->value('projectname');

        $assignedBy = Session::get('role') === 'admin'
            ? DB::table('admin_access')->where('id', Session::get('admin_id'))->value('name')
            : DB::table('allemployees')->where('id', Session::get('user_id'))->value(DB::raw("CONCAT(firstname,' ',lastname)"));

        Mail::raw(
            "Hello {$employee->firstname} {$employee->lastname},\n\n" .
            "You have been assigned a new Testing Ticket.\n\n" .
            "Ticket ID: {$testingTicketId}\n" .
            "Project: {$projectName}\n" .
            "Priority: {$priority}\n\n" .
            "Description:\n{$description}\n\n" .
            "Assigned By: {$assignedBy}\n\n" .
            "Please login to the system to view full details.\n\n" .
            "Regards,\nHRMS System",
            function ($message) use ($employee) {
                $message->to($employee->email)
                        ->subject('New Testing Ticket Assigned');
            }
        );
    }
}

    /**
     * Display the specified testing ticket
     */
    /**
 * Display the specified testing ticket with its bugs
 */
public function show($id)
{
    // Get the main ticket
    $ticket = DB::table('testing_tickets')
        ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
        ->leftJoin('allemployees as assigned', 'testing_tickets.assigned_to', '=', 'assigned.id')
        ->leftJoin('allemployees as creator', 'testing_tickets.created_by', '=', 'creator.id')
        ->leftJoin('allemployees as tester', 'testing_tickets.tester_id', '=', 'tester.id')
        ->select(
            'testing_tickets.*',
            'projects.projectname',
            DB::raw("CONCAT(assigned.firstname, ' ', assigned.lastname) as assigned_name"),
            DB::raw("CONCAT(creator.firstname, ' ', creator.lastname) as creator_name"),
            DB::raw("CONCAT(tester.firstname, ' ', tester.lastname) as tester_name"),
            'assigned.profile_image as assigned_image',
            'creator.profile_image as creator_image',
            'tester.profile_image as tester_image'
        )
        ->where('testing_tickets.id', $id)
        ->where('testing_tickets.deleted_at', 0)
        ->first();

    if (!$ticket) {
        return redirect()->route('testing.index')->with('error', 'Testing ticket not found.');
    }

    // Get bugs associated with this testing ticket
    $bugs = DB::table('testing_bugs')
        ->where('testing_ticket_id', $id)
        ->where('deleted_at', 0)
        ->get();

    // Get activity logs for this ticket
    $activityLogs = DB::table('testing_activity_logs')
        ->where('testing_ticket_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('hrms.Employee.testing.show', compact('ticket', 'bugs', 'activityLogs'));
}

/**
 * Update bug status
 */
public function updateBugStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Open,In Progress,Resolved,Closed,Reopen'
    ]);

    $bug = DB::table('testing_bugs')->where('id', $id)->first();
    
    if (!$bug) {
        return response()->json([
            'success' => false,
            'message' => 'Bug not found.'
        ], 404);
    }

    DB::table('testing_bugs')
        ->where('id', $id)
        ->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

    // Log the activity
    $this->logActivity(
        $bug->testing_ticket_id,
        'bug_status_changed',
        "Bug #{$id} status changed from {$bug->status} to {$request->status}",
        [
            'bug_id' => $id,
            'status' => [
                'old' => $bug->status,
                'new' => $request->status
            ]
        ]
    );

    return response()->json(['success' => true]);
}
    /**
     * Show the form for editing the specified testing ticket
     */
    public function edit($id)
    {
        $ticket = DB::table('testing_tickets')
            ->where('id', $id)
            ->where('deleted_at', 0)
            ->first();

        if (!$ticket) {
            return redirect()->route('testing.index')->with('error', 'Testing ticket not found.');
        }

        // Fetch active, non-closed projects
        $projects = DB::table('projects')
            ->where('deleted_at', 0)
            ->where('status', '!=', 'Closed')
            ->select('id', 'projectname', 'team')
            ->get();

        // Fetch all active employees
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'profile_image')
            ->get();

        return view('hrms.Employee.testing.edit', compact('ticket', 'projects', 'employees'));
    }

    /**
     * Update the specified testing ticket
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'project_id'        => 'required|exists:projects,id',
            'priority'          => 'required|in:High,Medium,Low',
            'assigned_to'       => 'nullable|exists:allemployees,id',
            'description'       => 'required|string|min:10',
            'steps_to_reproduce'=> 'nullable|string',
            'uploaded_files'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        // Get existing ticket data
        $existingTicket = DB::table('testing_tickets')->where('id', $id)->first();
        
        if (!$existingTicket) {
            return redirect()->route('testing.index')->with('error', 'Testing ticket not found.');
        }

        // ✅ Get tester ID from session
        $testerId = session('user_id');

        $updateData = [
            'project_id'        => $request->project_id,
            'priority'          => $request->priority,
            'assigned_to'       => $request->filled('assigned_to') ? $request->assigned_to : null,
            'description'       => $request->description,
            'steps_to_reproduce'=> $request->steps_to_reproduce,
            'tester_id'         => $testerId, // ✅ store tester ID here
            'updated_at'        => now(),
        ];

        // Track changes
        $changes = [];

        if ($existingTicket->project_id != $request->project_id) {
            $changes['project_id'] = [
                'old' => $this->getProjectName($existingTicket->project_id),
                'new' => $this->getProjectName($request->project_id)
            ];
        }

        if ($existingTicket->priority !== $request->priority) {
            $changes['priority'] = [
                'old' => $existingTicket->priority,
                'new' => $request->priority
            ];
        }

        if ($existingTicket->assigned_to != $request->assigned_to) {
            $changes['assigned_to'] = [
                'old' => $this->getEmployeeName($existingTicket->assigned_to),
                'new' => $this->getEmployeeName($request->assigned_to)
            ];
        }

        if ($existingTicket->description !== $request->description) {
            $changes['description'] = [
                'old' => substr($existingTicket->description, 0, 100) . (strlen($existingTicket->description) > 100 ? '...' : ''),
                'new' => substr($request->description, 0, 100) . (strlen($request->description) > 100 ? '...' : '')
            ];
        }

        if ($existingTicket->steps_to_reproduce !== $request->steps_to_reproduce) {
            $changes['steps_to_reproduce'] = [
                'old' => $existingTicket->steps_to_reproduce ? 'Provided' : 'Not provided',
                'new' => $request->steps_to_reproduce ? 'Provided' : 'Not provided'
            ];
        }

        // ✅ Handle file upload
        if ($request->hasFile('uploaded_files')) {
            $file = $request->file('uploaded_files');
            $safeFileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

            $destination = public_path('uploads/testing-tickets');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }

            $file->move($destination, $safeFileName);
            $updateData['uploaded_files'] = 'uploads/testing-tickets/' . $safeFileName;
            
            $changes['uploaded_files'] = [
                'old' => $existingTicket->uploaded_files ? 'File attached' : 'No file',
                'new' => 'New file attached'
            ];
        }

        DB::table('testing_tickets')->where('id', $id)->update($updateData);

        // Log the activity
        if (!empty($changes)) {
            $this->logActivity(
                $id,
                'updated',
                "Testing Ticket #{$existingTicket->testing_ticket_id} was updated",
                $changes
            );
        }

        return redirect()->route('testing.index')->with('success', 'Testing ticket updated successfully!');
    }

    /**
     * Get project team members via AJAX
     */
    public function getProjectTeamMembers($projectId)
    {
        $project = DB::table('projects')
            ->where('id', $projectId)
            ->where('deleted_at', 0)
            ->first();

        if (!$project || !$project->team) {
            return response()->json([]);
        }

        $teamIds = explode(',', $project->team);
        
        $teamMembers = DB::table('allemployees')
            ->whereIn('id', $teamIds)
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'profile_image')
            ->get();

        return response()->json($teamMembers);
    }

    /**
     * Update testing ticket status (Admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Closed,Reopen'
        ]);

        $ticket = DB::table('testing_tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Testing ticket not found.'
            ], 404);
        }

        DB::table('testing_tickets')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        // Log the activity
        $this->logActivity(
            $id,
            'status_changed',
            "Testing Ticket #{$ticket->testing_ticket_id} status changed from {$ticket->status} to {$request->status}",
            [
                'status' => [
                    'old' => $ticket->status,
                    'new' => $request->status
                ]
            ]
        );

        // Recalculate statistics with Closed-project exclusion
        $role = session('role', 'employee');
        $employeeId = session('user_id');
        
        $statsQuery = DB::table('testing_tickets')
            ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
            ->where('testing_tickets.deleted_at', 0);
        
        if ($role === 'employee') {
            $statsQuery->where(function($q) use ($employeeId) {
                $q->where('testing_tickets.created_by', $employeeId)
                  ->orWhere('testing_tickets.assigned_to', $employeeId)
                  ->orWhere('testing_tickets.tester_id', $employeeId);
            });
        }

        $newTicketsCount     = (clone $statsQuery)->where('testing_tickets.status', 'Open')->count();
        $solvedTicketsCount  = (clone $statsQuery)->where('testing_tickets.status', 'Closed')->count();
        $openTicketsCount    = (clone $statsQuery)->where('testing_tickets.status', 'In Progress')->count();
        $pendingTicketsCount = (clone $statsQuery)->where('testing_tickets.status', 'Resolved')->count();
        $reopenTicketsCount  = (clone $statsQuery)->where('testing_tickets.status', 'Reopen')->count();

        return response()->json([
            'success' => true,
            'newTicketsCount' => $newTicketsCount,
            'solvedTicketsCount' => $solvedTicketsCount,
            'openTicketsCount' => $openTicketsCount,
            'pendingTicketsCount' => $pendingTicketsCount,
            'reopenTicketsCount' => $reopenTicketsCount
        ]);
    }

    /**
     * Update testing ticket priority (Admin only)
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:High,Medium,Low'
        ]);

        $ticket = DB::table('testing_tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Testing ticket not found.'
            ], 404);
        }

        DB::table('testing_tickets')
            ->where('id', $id)
            ->update([
                'priority' => $request->priority,
                'updated_at' => now()
            ]);

        // Log the activity
        $this->logActivity(
            $id,
            'priority_changed',
            "Testing Ticket #{$ticket->testing_ticket_id} priority changed from {$ticket->priority} to {$request->priority}",
            [
                'priority' => [
                    'old' => $ticket->priority,
                    'new' => $request->priority
                ]
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Update testing ticket assignment (Admin only)
     */
    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:allemployees,id'
        ]);

        $ticket = DB::table('testing_tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Testing ticket not found.'
            ], 404);
        }

        $oldAssignedName = $this->getEmployeeName($ticket->assigned_to);
        $newAssignedName = $this->getEmployeeName($request->assigned_to);

        DB::table('testing_tickets')
            ->where('id', $id)
            ->update([
                'assigned_to' => $request->assigned_to,
                'updated_at' => now()
            ]);

        // Log the activity
        $this->logActivity(
            $id,
            'assigned',
            "Testing Ticket #{$ticket->testing_ticket_id} assigned from {$oldAssignedName} to {$newAssignedName}",
            [
                'assigned_to' => [
                    'old' => $oldAssignedName,
                    'new' => $newAssignedName
                ]
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Delete testing ticket (Admin only)
     */
    public function destroy($id)
    {
        $role = session('role', 'employee');
        
        if ($role !== 'admin') {
            return redirect()->route('testing.index')
                ->with('error', 'Access denied. Only administrators can delete testing tickets.');
        }

        $ticket = DB::table('testing_tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return redirect()->route('testing.index')->with('error', 'Testing ticket not found.');
        }

        // Log the activity before deletion
        $this->logActivity(
            $id,
            'deleted',
            "Testing Ticket #{$ticket->testing_ticket_id} was deleted",
            [
                'testing_ticket_id' => $ticket->testing_ticket_id,
                'project_id' => $this->getProjectName($ticket->project_id),
                'status' => $ticket->status,
                'priority' => $ticket->priority
            ]
        );

        DB::table('testing_tickets')
            ->where('id', $id)
            ->update(['deleted_at' => 1]);

        return redirect()->route('testing.index')->with('success', 'Testing ticket deleted successfully!');
    }

    /**
     * Log testing activity
     */
    private function logActivity($testingTicketId, $action, $description, $changes = [])
    {
        $role = Session::get('role');
        $performedById = null;
        $performedByName = '';
        
        // Get user info based on role
        if ($role === 'admin') {
            $performedById = Session::get('admin_id');
            $user = DB::table('admin_access')->where('id', $performedById)->first();
            if ($user) {
                $performedByName = $user->name;
            }
        } elseif ($role === 'employee') {
            $performedById = Session::get('user_id');
            $user = DB::table('allemployees')->where('id', $performedById)->first();
            if ($user) {
                $performedByName = $user->firstname . ' ' . $user->lastname;
            }
        }

        DB::table('testing_activity_logs')->insert([
            'testing_ticket_id' => $testingTicketId,
            'action' => $action,
            'description' => $description,
            'performed_by_role' => $role,
            'performed_by_id' => $performedById,
            'performed_by_name' => $performedByName,
            'changes' => !empty($changes) ? json_encode($changes) : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Get activity logs
     */
 private function getActivityLogs(Request $request = null)
{
    $role = Session::get('role');
    $employeeId = Session::get('user_id');

    $query = DB::table('testing_activity_logs')
        ->leftJoin('testing_tickets', 'testing_activity_logs.testing_ticket_id', '=', 'testing_tickets.id')
        ->leftJoin('projects', 'testing_tickets.project_id', '=', 'projects.id')
        ->select(
            'testing_activity_logs.*',
            'testing_tickets.testing_ticket_id',
            'testing_tickets.description as ticket_description',
            'projects.projectname'
        )
        ->where('testing_tickets.deleted_at', 0);

    // Apply filters from request
    if ($request) {
        // Action filter
        if ($request->filled('log_action')) {
            $query->where('testing_activity_logs.action', $request->log_action);
        }
        
        // Performed by role filter
        if ($request->filled('log_performed_by')) {
            $query->where('testing_activity_logs.performed_by_role', $request->log_performed_by);
        }
        
        // Testing Ticket ID filter
        if ($request->filled('log_ticket_id')) {
            $query->where('testing_tickets.testing_ticket_id', 'like', '%' . $request->log_ticket_id . '%');
        }
        
        // Project filter
        if ($request->filled('log_project_id')) {
            $query->where('testing_tickets.project_id', $request->log_project_id);
        }
        
        // Date range filters
        if ($request->filled('log_start_date')) {
            $query->whereDate('testing_activity_logs.created_at', '>=', $request->log_start_date);
        }
        
        if ($request->filled('log_end_date')) {
            $query->whereDate('testing_activity_logs.created_at', '<=', $request->log_end_date);
        }
        
        // Description search
        if ($request->filled('log_search')) {
            $query->where('testing_activity_logs.description', 'like', '%' . $request->log_search . '%');
        }
    }

    // If employee, only show logs for tickets they have access to
    if ($role === 'employee') {
        $query->whereIn('testing_activity_logs.testing_ticket_id', function($q) use ($employeeId) {
            $q->select('id')
              ->from('testing_tickets')
              ->where(function($sub) use ($employeeId) {
                  $sub->where('created_by', $employeeId)
                      ->orWhere('assigned_to', $employeeId)
                      ->orWhere('tester_id', $employeeId);
              });
        });
    }

    // Order by creation date descending
    $query->orderBy('testing_activity_logs.created_at', 'desc');
    
    // Limit results
    $query->limit(100);

    return $query->get();
}

    /**
     * Get employee name by ID
     */
    private function getEmployeeName($employeeId)
    {
        if (!$employeeId) {
            return 'Unassigned';
        }
        
        $employee = DB::table('allemployees')->where('id', $employeeId)->first();
        if ($employee) {
            return $employee->firstname . ' ' . $employee->lastname;
        }
        
        return 'Unknown Employee';
    }

    /**
     * Get project name by ID
     */
    private function getProjectName($projectId)
    {
        if (!$projectId) {
            return 'No Project';
        }
        
        $project = DB::table('projects')->where('id', $projectId)->first();
        if ($project) {
            return $project->projectname;
        }
        
        return 'Unknown Project';
    }
    /**
 * Show the form for editing a bug
 */
public function editBug($id)
{
    $bug = DB::table('testing_bugs')
        ->where('id', $id)
        ->where('deleted_at', 0)
        ->first();

    if (!$bug) {
        return redirect()->route('testing.index')->with('error', 'Bug not found.');
    }

    // Get the parent ticket for context
    $ticket = DB::table('testing_tickets')
        ->where('id', $bug->testing_ticket_id)
        ->first();

    return view('hrms.Employee.testing.bug_edit', compact('bug', 'ticket'));
}

/**
 * Update the specified bug
 */
public function updateBug(Request $request, $id)
{
    $request->validate([
        'module_name' => 'required|string',
        'description' => 'required|string|min:10',
        'steps_to_reproduce' => 'nullable|string',
        'uploaded_files' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
    ]);

    $bug = DB::table('testing_bugs')->where('id', $id)->first();
    
    if (!$bug) {
        return redirect()->route('testing.index')->with('error', 'Bug not found.');
    }

    $updateData = [
        'module_name' => $request->module_name,
        'description' => $request->description,
        'steps_to_reproduce' => $request->steps_to_reproduce,
        'updated_at' => now(),
    ];

    // Handle file upload
    if ($request->hasFile('uploaded_files')) {
        $file = $request->file('uploaded_files');
        $safeFileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $destination = public_path('uploads/testing-tickets');
        
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }
        
        $file->move($destination, $safeFileName);
        $updateData['uploaded_files'] = 'uploads/testing-tickets/' . $safeFileName;
    }

    DB::table('testing_bugs')->where('id', $id)->update($updateData);

    // Log the activity
    $this->logActivity(
        $bug->testing_ticket_id,
        'bug_updated',
        "Bug #{$id} was updated",
        ['bug_id' => $id]
    );

    return redirect()->route('testing.show', $bug->testing_ticket_id)
        ->with('success', 'Bug updated successfully!');
}
}