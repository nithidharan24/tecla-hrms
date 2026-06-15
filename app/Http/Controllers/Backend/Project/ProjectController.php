<?php

namespace App\Http\Controllers\Backend\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Get all available project statuses
     */
    private function getProjectStatuses()
    {
        return [
            'Initiated' => 'Initiated',
            'Planned' => 'Planned',
            'Active' => 'Active',
            'On Hold' => 'On Hold',
            'Pending' => 'Pending',
            'Review' => 'Review',
            'Completed' => 'Completed',
            'Closed' => 'Closed',
            'Cancelled' => 'Cancelled'
        ];
    }

    /**
     * Get status button class based on status
     */
    private function getStatusButtonClass($status)
    {
        $classes = [
            'Initiated' => 'btn-secondary',
            'Planned' => 'btn-info',
            'Active' => 'btn-success',
            'On Hold' => 'btn-warning',
            'Pending' => 'btn-primary',
            'Review' => 'btn-dark',
            'Completed' => 'btn-success',
            'Closed' => 'btn-secondary',
            'Cancelled' => 'btn-danger'
        ];
        return $classes[$status] ?? 'btn-secondary';
    }

    /**
     * Display a listing of the resource.
     */

public function download($id)
{
    $project = DB::table('projects')->where('id', $id)->first();

    if (!$project || !$project->projectfile) {
        return redirect()->back()->with('error', 'File not found.');
    }

    $filePath = storage_path('app/public/' . $project->projectfile);

    if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File not found.');
    }

    return response()->download($filePath);
}
public function index(Request $request)
{
    $role = session('role');
    $employeeId = session('user_id'); // this is correct normal ID

    // Base query
    $projectsQuery = DB::table('projects as p')
        ->leftJoin('allemployees as pe', 'p.projectleader', '=', 'pe.id')
        ->select(
            'p.*',
            DB::raw("CONCAT(pe.firstname, ' ', pe.lastname) as leaderName")
        );

    // ---------------------------------
    // ROLE BASED PROJECT VISIBILITY
    // ---------------------------------
    if ($role === 'employee') {

        $projectsQuery->where(function ($q) use ($employeeId) {
            $q->where('p.projectleader', $employeeId)
              ->orWhereRaw("FIND_IN_SET($employeeId, p.team)");
        });
    }
    // Admin sees everything (no filter)


    // ---------------------------------
    // APPLY FILTERS FROM FORM
    // ---------------------------------
    if ($request->filled('status')) {
        $projectsQuery->where('p.status', $request->status);
    }

    if ($request->filled('priority')) {
        $projectsQuery->where('p.priority', $request->priority);
    }

    if ($request->filled('leader')) {
        $projectsQuery->where('p.projectleader', $request->leader);
    }

    if ($request->filled('projectname')) {
        $projectsQuery->where('p.projectname', 'like', '%' . $request->projectname . '%');
    }

    if ($request->filled('startdate') && $request->filled('enddate')) {
        $projectsQuery->whereBetween('p.startdate', [$request->startdate, $request->enddate]);
    }


    // ---------------------------------
    // GET PROJECTS
    // ---------------------------------
    $projects = $projectsQuery->get();


    // ---------------------------------
    // BUILD TEAM NAMES
    // ---------------------------------
    foreach ($projects as $project) {
        $teamIds = array_filter(explode(',', $project->team));

        $teamMembers = DB::table('allemployees')
            ->whereIn('id', $teamIds)
            ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
            ->pluck('name')
            ->toArray();

        $project->teamNames = !empty($teamMembers)
            ? collect($teamMembers)->map(fn($n, $i) => ($i + 1) . '. ' . $n)->implode('<br>')
            : 'N/A';
    }


    // Dropdown data
    $statuses = $this->getProjectStatuses();
    $leaders = DB::table('allemployees')
        ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as name"))
        ->get();

    return view('hrms.time-tracker.index', compact('projects', 'statuses', 'leaders'));
}



    public function create()
    {
        $client = DB::table('clients')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->get();

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'designation')
            ->get();

        $statuses = $this->getProjectStatuses();
        return view('hrms.Employee.Project.create', compact('client', 'employees', 'statuses'));
    }


    public function store(Request $request)
    {
        // Generate unique project ID
        $latestProject = DB::table('projects')->orderBy('id', 'desc')->first();
        $newProjectId = 'PRO-' . str_pad($latestProject ? $latestProject->id + 1 : 1, 4, '0', STR_PAD_LEFT);
    
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('projectfile')) {
            $fileName = time() . '_' . $request->file('projectfile')->getClientOriginalName();
            $filePath = $request->file('projectfile')->storeAs('uploads/projects', $fileName, 'public');
        }
    
        // Calculate total hours
        $startDate = Carbon::parse($request->startdate);
        $endDate   = Carbon::parse($request->enddate);
        $totalHours = $endDate->diffInHours($startDate);
    
        // Convert team array to comma-separated string
        $team = is_array($request->team) ? implode(',', $request->team) : $request->team;
    
        // Validate and set status
        $requestedStatus = $request->status;
        $validStatuses   = array_keys($this->getProjectStatuses());
        $statusToStore   = 'Initiated'; // Default
    
        if (!empty($requestedStatus) && in_array($requestedStatus, $validStatuses)) {
            $statusToStore = $requestedStatus;
        }
    
        // =========================
        // INSERT PROJECT
        // =========================
        DB::table('projects')->insert([
            'projectid'      => $newProjectId,
            'projectname'    => $request->projectname,
            'client'         => $request->client,
            'startdate'      => $request->startdate,
            'enddate'        => $request->enddate,
            'rate'           => $request->rate,
            'worktype'       => $request->worktype,
            'projectleader'  => $request->projectleader,
            'team'           => $team,
            'priority'       => $request->priority,
            'description'    => $request->description,
            'projectfile'    => $filePath,
            'totalhours'     => $totalHours,
            'status'         => $statusToStore,
            'deleted_at'     => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    
        // Log Activity
        $this->logProjectActivity($newProjectId, 'created', 'Project created successfully');
    
        // =========================
        // SEND EMAILS TO TEAM
        // =========================
    
        // 1. Convert comma-separated IDs to array
        $teamIds = [];
        if (!empty($team)) {
            $teamIds = array_filter(array_map('trim', explode(',', $team)));
        }
    
        if (!empty($teamIds)) {
    
            // 2. Fetch team members by ID from allemployees table
            $teamMembers = DB::table('allemployees')
                ->whereIn('id', $teamIds)
                ->whereNotNull('email')
                ->select(
                    'email',
                    DB::raw("CONCAT(COALESCE(firstname,''), ' ', COALESCE(lastname,'')) as name")
                )
                ->get();
    
            // 3. Loop and send email
            foreach ($teamMembers as $member) {
    
                $to      = $member->email;
                $subject = "You have been added to Project: " . $request->projectname;
    
                $message = "
                    Hello {$member->name},<br><br>
                    You have been added to the project <strong>{$request->projectname}</strong>.<br><br>
                    <strong>Project ID:</strong> {$newProjectId}<br>
                    <strong>Start Date:</strong> {$request->startdate}<br>
                    <strong>End Date:</strong> {$request->enddate}<br>
                    <strong>Priority:</strong> {$request->priority}<br><br>
                    Please check your dashboard for more details.<br><br>
                    Regards,<br>
                    Admin Team
                ";
    
                \Mail::send([], [], function ($mail) use ($to, $subject, $message) {
                    $mail->to($to)
                         ->subject($subject)
                         ->html($message);
                });
            }
        }
    
        return redirect()->route('time-tracker.index')->with('success', 'Project added successfully!');
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = DB::table('projects')->where('id', $id)->first();

        if (!$project) {
            return redirect()->route('time-tracker.index')->with('error', 'Project not found.');
        }

        // Get project leader name
        $leader = DB::table('allemployees')
            ->where('id', $project->projectleader)
            ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
            ->first();

        $project->leaderName = $leader ? $leader->name : 'N/A';

        // Get team members
        $teamIds = explode(',', $project->team);
        $teamMembers = DB::table('allemployees')
            ->whereIn('id', $teamIds)
            ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
            ->pluck('name')
            ->toArray();

        // Format as numbered list
        if (!empty($teamMembers)) {
            $formattedNames = '';
            foreach ($teamMembers as $index => $name) {
                $formattedNames .= ($index + 1) . '. ' . $name . '<br>';
            }
            $project->teamNames = $formattedNames;
        } else {
            $project->teamNames = 'N/A';
        }

        return view('hrms.Employee.Project.view', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $project = DB::table('projects')->where('id', $id)->first();

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found.');
        }

        $client = DB::table('clients')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->get();

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'designation')
            ->get();

        $selectedTeam = explode(',', $project->team);
        $currentLeaderId = $project->projectleader;
        $statuses = $this->getProjectStatuses();

        return view('hrms.Employee.Project.edit', compact(
            'project',
            'client',
            'employees',
            'selectedTeam',
            'statuses',
            'currentLeaderId'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $project = DB::table('projects')->where('id', $id)->first();

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found.');
        }

        // Handle file upload if provided
        $filePath = $project->projectfile;
        if ($request->hasFile('projectfile')) {
            // Delete old file if exists
            if ($filePath) {
                \Storage::disk('public')->delete($filePath);
            }
            
            $fileName = time() . '_' . $request->file('projectfile')->getClientOriginalName();
            $filePath = $request->file('projectfile')->storeAs('uploads/projects', $fileName, 'public');
        }

        // Calculate total hours
        $startDate = Carbon::parse($request->startdate);
        $endDate = Carbon::parse($request->enddate);
        $totalHours = $endDate->diffInHours($startDate);

        // Convert team array to comma-separated string
        $team = is_array($request->team) ? implode(',', $request->team) : $request->team;

        // Validate and set status
        $requestedStatus = $request->status;
        $validStatuses = array_keys($this->getProjectStatuses());
        $statusToUpdate = in_array($requestedStatus, $validStatuses) ? $requestedStatus : $project->status;

        // Update project data
        $updateData = [
            'projectname' => $request->projectname,
            'client' => $request->client,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'rate' => $request->rate,
            'worktype' => $request->worktype,
            'projectleader' => $request->projectleader, // Store ID
            'team' => $team,
            'priority' => $request->priority,
            'status' => $statusToUpdate,
            'description' => $request->description,
            'totalhours' => $totalHours,
            'updated_at' => now(),
        ];

        if ($filePath) {
            $updateData['projectfile'] = $filePath;
        }

        DB::table('projects')->where('id', $id)->update($updateData);
        $this->logProjectActivity($project->projectid, 'updated', 'Project updated successfully');

        return redirect()->route('time-tracker.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $project = DB::table('projects')->where('id', $id)->first();

    if (!$project) {
        return redirect()->route('projects.index')->with('error', 'Project not found.');
    }

    // Delete the project file if it exists
    if ($project->projectfile) {
        \Storage::disk('public')->delete($project->projectfile);
    }

    // Permanent delete the project row
    DB::table('projects')->where('id', $id)->delete();
    $this->logProjectActivity($project->projectid, 'deleted', 'Project deleted successfully');

    return redirect()->route('time-tracker.index')->with('success', 'Project deleted successfully!');
}


    /**
     * Update project status via AJAX
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:projects,id',
            'newstatus' => 'required|string|in:Initiated,Planned,Active,On Hold,Pending,Review,Completed,Closed,Cancelled',
        ]);

        try {
            $updated = DB::table('projects')
                ->where('id', $request->id)
                ->update(['status' => $request->newstatus, 'updated_at' => now()]);

            if ($updated) {
                $this->logProjectActivity(
                    DB::table('projects')->where('id', $request->id)->value('projectid'),
                    'status_changed',
                    'Status changed to: ' . $request->newstatus
                );
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'new_status' => $request->newstatus
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No changes made'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    private function logProjectActivity($projectId, $action, $description = null)
{
    $role = session('role');

    if ($role === 'employee') {
        $userId = session('user_id');
        $performedBy = 'employee';
    } else {
        $userId = session('admin_id'); // admin id stored here
        $performedBy = 'admin';
    }

    DB::table('project_activity_log')->insert([
        'project_id' => $projectId,
        'action' => $action,
        'performed_by' => $performedBy,
        'user_id' => $userId,
        'description' => $description,
        'created_at' => now()
    ]);
}
public function multiDelete(Request $request)
{
    $ids = $request->ids;

    if (!$ids) {
        return redirect()->back()->with('error', 'No projects selected.');
    }

    $projects = DB::table('projects')->whereIn('id', $ids)->get();

    foreach ($projects as $project) {
        if ($project->projectfile) {
            \Storage::disk('public')->delete($project->projectfile);
        }

        DB::table('projects')->where('id', $project->id)->delete();

        $this->logProjectActivity($project->projectid, 'deleted', 'Project deleted (multi-delete)');
    }

    return redirect()->route('time-tracker.index')->with('success', 'Selected projects deleted successfully!');
}
public function projectdetails($projectid)
{
    // Fetch project basic info using projectid (e.g. PRO-0001)
    $project = DB::table('projects')
        ->leftJoin('allemployees', 'projects.projectleader', '=', 'allemployees.id')
        ->leftJoin('clients', 'projects.client', '=', 'clients.id')
        ->where('projects.projectid', $projectid)
        ->select(
            'projects.*',
            DB::raw("CONCAT(allemployees.firstname,' ',allemployees.lastname) as leader_name"),
            DB::raw("CONCAT(clients.first_name,' ',clients.last_name) as client_name")
        )
        ->first();

    if (!$project) {
        abort(404, "Project not found");
    }

    // 👍 Count team members from comma-separated values
    $teamList = $project->team ? explode(',', $project->team) : [];
    $project->project_users_count = count(array_filter($teamList));

    // Ensure description exists
    $project->description = $project->description ?? '';

    return view('hrms.Employee.Project.project_details', compact('project'));
}



}