<?php

namespace App\Http\Controllers\Backend\Offboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OffboardingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'inprogress');

        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->get();

        $departments = DB::table('department')->get();

        $query = DB::table('offboarding_requests as o')
            ->join('allemployees as e', 'o.employee_id', '=', 'e.id')
            ->leftJoin('department as d', 'e.department', '=', 'd.id')
            ->select(
                'o.*',
                'e.firstname',
                'e.lastname',
                'e.employeeid',
                'e.department',
                'e.joiningdate',
                'd.department as department_name'
            );

        // Apply status filter
        if ($status !== 'all') {
            $query->where('o.status', $status);
        }

        // Apply employee filter
        if ($request->has('employee_filter')) {
            if ($request->employee_filter === 'specific' && $request->filled('employee_id')) {
                $query->where('o.employee_id', $request->employee_id);
            }
        }

        // Apply exit type filter
        if ($request->filled('exit_type') && $request->exit_type !== 'all') {
            $query->where('o.offboarding_type', $request->exit_type);
        }

        // Apply department filter
        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('e.department', $request->department);
        }

        // Apply date period filter
        if ($request->filled('date_period')) {
            $now = Carbon::now();

            switch ($request->date_period) {
                case 'current_year':
                    $query->whereYear('o.last_working_date', $now->year);
                    break;

                case 'last_year':
                    $query->whereYear('o.last_working_date', $now->year - 1);
                    break;

                case 'custom':
                    if ($request->filled('from_date')) {
                        $query->whereDate('o.last_working_date', '>=', $request->from_date);
                    }
                    if ($request->filled('to_date')) {
                        $query->whereDate('o.last_working_date', '<=', $request->to_date);
                    }
                    break;
            }
        }

        $offboardings = $query->orderBy('o.created_at', 'desc')->get();

        return view('hrms.offboarding.index', compact(
            'offboardings',
            'status',
            'employees',
            'departments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'employeeid', 'designation', 'department', 'joiningdate')
            ->get();

        return view('hrms.offboarding.create', compact('employees'));
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
{
    Log::info('========== OFFBOARDING STORE START ==========');
    Log::info('REQUEST DATA', $request->all());

    $request->validate([
        'employee_id' => 'required|exists:allemployees,id',
        'offboarding_type' => 'required|in:resignation,termination,deceased',
        'last_working_date' => 'required|date',
        'login_disable_date' => 'required|date',
        'reason' => 'required|string|max:500',
        'replacement_required' => 'required|in:Yes,No',
        'explanation' => 'nullable|string',
        'employee_status' => 'required|string',
        'exit_type' => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        'deceased_date' => 'nullable|date',
    ]);

    DB::beginTransaction();

    try {

        $employee = DB::table('allemployees')
            ->where('id', $request->employee_id)
            ->first();

        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        $offboardingData = [
            'employee_id' => $request->employee_id,
            'offboarding_type' => $request->offboarding_type,
            'reason' => $request->reason,
            'explanation' => $request->explanation,
            'last_working_date' => $request->last_working_date,
            'login_disable_date' => $request->login_disable_date,
            'replacement_required' => $request->replacement_required,
            'employee_status' => $request->employee_status,
            'exit_type' => $request->exit_type,
            'status' => 'inprogress',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (
            $request->offboarding_type === 'deceased'
            && !empty($request->deceased_date)
        ) {
            $offboardingData['deceased_date'] = $request->deceased_date;
        }

        Log::info('INSERT DATA', $offboardingData);

        $offboardingId = DB::table('offboarding_requests')
            ->insertGetId($offboardingData);

        Log::info('OFFBOARDING INSERTED ID : ' . $offboardingId);

        // TEMPORARILY DISABLE THIS
        /*
        $this->createClearanceTasks(
            $offboardingId,
            $request->employee_id,
            $employee->employeeid
        );
        */

        DB::commit();

        return redirect()
            ->route('offboarding.index')
            ->with('success', 'Offboarding created successfully');

    } catch (\Throwable $e) {

        DB::rollback();

        Log::error('OFFBOARDING FAILED');
        Log::error($e->getMessage());
        Log::error($e->getFile());
        Log::error('LINE : ' . $e->getLine());

        dd($e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch Offboarding Request
        $offboarding = DB::table('offboarding_requests as o')
            ->join('allemployees as e', 'o.employee_id', '=', 'e.id')
            ->leftJoin('department as d', 'e.department', '=', 'd.id')
            ->leftJoin('designation as des', 'e.designation', '=', 'des.id')
            ->select(
                'o.*',
                'e.firstname',
                'e.lastname',
                'e.employeeid',
                'e.joiningdate',
                'e.email',
                'e.phone',
                'e.department',
                'e.manager_id',
                'e.profile_image',
                'd.department as department_name',
                'des.designation as designation_name'
            )
            ->where('o.id', $id)
            ->first();

        if (!$offboarding) {
            return redirect()->route('offboarding.index')
                ->with('error', 'Offboarding request not found.');
        }

        // Calculate experience
        $joiningDate = Carbon::parse($offboarding->joiningdate);
        $now = Carbon::now();
        $experience = $joiningDate->diff($now)->format('%y year(s) %m month(s)');

        // Fetch clearance tasks + details
        $clearances = DB::table('offboarding_clearances')
            ->where('offboarding_id', $id)
            ->get()
            ->map(function ($clearance) {
                $details = null;
                $table = '';

                switch ($clearance->clearance_type) {
                    case 'it':  $table = 'clearance_it_details'; break;
                    case 'hr':  $table = 'clearance_hr_details'; break;
                    case 'admin': $table = 'clearance_admin_details'; break;
                }

                if ($table) {
                    $details = DB::table($table)
                        ->where('clearance_id', $clearance->id)
                        ->first();
                }

                $clearance->details = $details;
                return $clearance;
            });

        // Reporting Manager
        $reportingManager = null;
        if ($offboarding->manager_id) {
            $reportingManager = DB::table('allemployees')
                ->where('id', $offboarding->manager_id)
                ->select('firstname', 'lastname', 'employeeid', 'designation')
                ->first();
        }

        // Created By
        $createdBy = null;
        if ($offboarding->created_by) {
            $createdBy = DB::table('allemployees')
                ->where('id', $offboarding->created_by)
                ->select('firstname', 'lastname', 'employeeid')
                ->first();
        }

        // Clearance Progress
        $totalClearances = $clearances->count();
        $completedClearances = $clearances->where('status', 'completed')->count();
        $progressPercentage = $totalClearances > 0
            ? round(($completedClearances / $totalClearances) * 100)
            : 0;

        // Employees for dropdowns
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->get();

        /* ======================================================
           CAREER HISTORY (MERGED HERE)
        ====================================================== */

        // EMPLOYEE MASTER PROFILE FOR CAREER TAB
        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'department.department as department_name',
                'branches.name as branch_name'
            )
            ->where('allemployees.id', $offboarding->employee_id)
            ->first();

        $events = [];

        // JOINING EVENT
        if (!empty($employee->joiningdate)) {
            $events[] = [
                'date' => $employee->joiningdate,
                'title' => 'Joined the Company',
                'description' =>
                    "Joined as <b>{$employee->designation_name}</b> in <b>{$employee->department_name}</b> department.",
                'type' => 'join'
            ];
        }

        // PROMOTIONS HISTORY
        $promotions = DB::table('promotions')
            ->join('designation as old', 'promotions.promotion_from', '=', 'old.id')
            ->join('designation as new', 'promotions.promotion_to', '=', 'new.id')
            ->leftJoin('department', 'promotions.department_id', '=', 'department.id')
            ->where('promotions.employee_id', $employee->employeeid)
            ->orderBy('promotions.promotion_date', 'DESC')
            ->select(
                'promotions.promotion_date',
                'old.designation as old_role',
                'new.designation as new_role',
                'department.department as dept_name'
            )
            ->get();

        foreach ($promotions as $p) {
            $events[] = [
                'date' => $p->promotion_date,
                'title' => "Promoted to {$p->new_role}",
                'description' =>
                    "Role changed from <b>{$p->old_role}</b> to <b>{$p->new_role}</b> in <b>{$p->dept_name}</b> department.",
                'type' => 'promotion'
            ];
        }

        // Sort career events (latest first)
        usort($events, function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        /* ======================================================
           RETURN VIEW WITH NEW VARIABLES
        ====================================================== */

        return view('hrms.offboarding.show', compact(
            'offboarding',
            'clearances',
            'experience',
            'reportingManager',
            'createdBy',
            'totalClearances',
            'completedClearances',
            'progressPercentage',
            'employees',

            // ***** NEW FOR CAREER HISTORY TAB *****
            'employee',
            'events'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $offboarding = DB::table('offboarding_requests')->find($id);

        if (!$offboarding) {
            return redirect()->route('offboarding.index')
                ->with('error', 'Offboarding request not found.');
        }

        $employee = DB::table('allemployees')
            ->where('id', $offboarding->employee_id)
            ->select('id', 'firstname', 'lastname', 'employeeid', 'designation', 'department', 'joiningdate')
            ->first();

        // Calculate experience
        $joiningDate = Carbon::parse($employee->joiningdate);
        $now = Carbon::now();
        $employee->experience = $joiningDate->diff($now)->format('%y years %m months');

        return view('hrms.offboarding.edit', compact('offboarding', 'employee'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'last_working_date' => 'required|date',
            'login_disable_date' => 'required|date',
            'replacement_required' => 'required|in:Yes,No',
            'explanation' => 'nullable|string',
            'employee_status' => 'required|string',
            'deceased_date' => 'required_if:offboarding_type,deceased|date|nullable',
            'exit_type' => 'nullable|string|max:255',
        ]);

        try {
            $updateData = [
                'reason' => $request->reason,
                'explanation' => $request->explanation,
                'last_working_date' => $request->last_working_date,
                'login_disable_date' => $request->login_disable_date,
                'replacement_required' => $request->replacement_required,
                'employee_status' => $request->employee_status,
                'exit_type' => $request->exit_type,
                'updated_at' => now(),
            ];

            // Add deceased date if type is deceased
            if ($request->offboarding_type === 'deceased') {
                $updateData['deceased_date'] = $request->deceased_date;
            }

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('offboarding_attachments', $fileName, 'public');
                $updateData['attachment_path'] = $filePath;
            }

            DB::table('offboarding_requests')
                ->where('id', $id)
                ->update($updateData);

            return redirect()->route('offboarding.index')
                ->with('success', 'Offboarding request updated successfully!');

        } catch (\Exception $e) {
            Log::error('Offboarding update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update offboarding request: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Get all clearance IDs for this offboarding
            $clearances = DB::table('offboarding_clearances')
                ->where('offboarding_id', $id)
                ->select('id', 'clearance_type')
                ->get();

            // Delete clearance details based on type
            foreach ($clearances as $clearance) {
                switch ($clearance->clearance_type) {
                    case 'it':
                        DB::table('clearance_it_details')->where('clearance_id', $clearance->id)->delete();
                        break;
                    case 'hr':
                        DB::table('clearance_hr_details')->where('clearance_id', $clearance->id)->delete();
                        break;
                    case 'admin':
                        DB::table('clearance_admin_details')->where('clearance_id', $clearance->id)->delete();
                        break;
                }
            }

            // Delete clearance tasks
            DB::table('offboarding_clearances')->where('offboarding_id', $id)->delete();

            // Delete offboarding request
            DB::table('offboarding_requests')->where('id', $id)->delete();

            DB::commit();

            return redirect()->route('offboarding.index')
                ->with('success', 'Offboarding request deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Offboarding deletion failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete offboarding request: ' . $e->getMessage());
        }
    }

    /**
     * Store clearance task
     * Route expected: offboarding.clearance.store (POST) with $id = offboarding id
     */
    public function storeClearance(Request $request, $id)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'clearance_type' => 'required|in:it,hr,admin,other',
            'department' => 'required|string|max:100',
            'assigned_to' => 'nullable|exists:allemployees,id',
        ]);

        try {
            $clearanceId = DB::table('offboarding_clearances')->insertGetId([
                'offboarding_id' => $id,
                'task_name' => $request->task_name,
                'clearance_type' => $request->clearance_type,
                'department' => $request->department,
                'assigned_to' => $request->assigned_to,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create details record for specific clearance types (if required)
            if (in_array($request->clearance_type, ['it', 'hr', 'admin'])) {
                $offboarding = DB::table('offboarding_requests')->where('id', $id)->first();
                $employee = DB::table('allemployees')->where('id', $offboarding->employee_id)->first();

                $detailsData = [
                    'clearance_id' => $clearanceId,
                    'employee_id' => $employee->employeeid,
                    'offboarding_id' => 'Request-' . $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                switch ($request->clearance_type) {
                    case 'it':
                        DB::table('clearance_it_details')->insert($detailsData);
                        break;
                    case 'hr':
                        DB::table('clearance_hr_details')->insert($detailsData);
                        break;
                    case 'admin':
                        DB::table('clearance_admin_details')->insert($detailsData);
                        break;
                }
            }

            return redirect()->back()->with('success', 'Clearance task added successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to add clearance task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add clearance task. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update clearance task status (simple status + comments)
     * Route expected: offboarding.clearance.update (PUT) with $id = clearance id
     */
    public function updateClearance(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,rejected',
            'comments' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'status' => $request->status,
                'comments' => $request->comments,
                'updated_at' => now(),
            ];

            if ($request->status === 'completed') {
                $updateData['completed_at'] = now();
            }

            DB::table('offboarding_clearances')
                ->where('id', $id)
                ->update($updateData);

            return redirect()->back()->with('success', 'Clearance task updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update clearance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update clearance task. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update IT Clearance Details
     * Route expected: offboarding.it.clearance.update (PUT) with $id = clearance id
     */
    public function updateItClearance(Request $request, $id)
    {
        $request->validate([
            'laptop_returned' => 'required|in:Yes,No,NA',
            'mobile_phone_returned' => 'required|in:Yes,No,NA',
            'keys_returned' => 'required|in:Yes,No,NA',
            'other_equipment_returned' => 'nullable|in:Yes,No,NA',
            'email_deactivated' => 'required|in:Yes,No,NA',
            'system_access_revoked' => 'required|in:Yes,No,NA',
            'vpn_access_revoked' => 'required|in:Yes,No,NA',
            'software_licenses_deactivated' => 'required|in:Yes,No,NA',
            'comments' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'laptop_returned' => $request->laptop_returned,
                'mobile_phone_returned' => $request->mobile_phone_returned,
                'keys_returned' => $request->keys_returned,
                'other_equipment_returned' => $request->other_equipment_returned,
                'other_equipment_specify' => $request->other_equipment_specify ?? null,
                'email_deactivated' => $request->email_deactivated,
                'system_access_revoked' => $request->system_access_revoked,
                'vpn_access_revoked' => $request->vpn_access_revoked,
                'software_licenses_deactivated' => $request->software_licenses_deactivated,
                'comments' => $request->comments,
                'updated_at' => now(),
            ];

            DB::table('clearance_it_details')
                ->where('clearance_id', $id)
                ->update($updateData);

            // Update clearance status to completed if all items are Yes
            $allCompleted = $this->checkItClearanceCompletion($id);
            if ($allCompleted) {
                DB::table('offboarding_clearances')
                    ->where('id', $id)
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            return redirect()->back()->with('success', 'IT clearance updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update IT clearance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update IT clearance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update HR Clearance Details
     * Route expected: offboarding.hr.clearance.update (PUT) with $id = clearance id
     */
    public function updateHrClearance(Request $request, $id)
    {
        $request->validate([
            'exit_interview_completed' => 'required|in:Yes,No,NA',
            'exit_interview_date' => 'nullable|date',
            'exit_interview_by' => 'nullable|string|max:255',
            'final_paycheck_processed' => 'required|in:Yes,No,NA',
            'unused_vacation_compensated' => 'required|in:Yes,No,NA',
            'benefits_terminated' => 'required|in:Yes,No,NA',
            'forms_signed' => 'required|in:Yes,No,NA',
            'nda_signed' => 'required|in:Yes,No,NA',
            'confidentiality_signed' => 'nullable|in:Yes,No,NA',
            'comments' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'exit_interview_completed' => $request->exit_interview_completed,
                'exit_interview_date' => $request->exit_interview_date,
                'exit_interview_by' => $request->exit_interview_by,
                'final_paycheck_processed' => $request->final_paycheck_processed,
                'unused_vacation_compensated' => $request->unused_vacation_compensated,
                'benefits_terminated' => $request->benefits_terminated,
                'forms_signed' => $request->forms_signed,
                'nda_signed' => $request->nda_signed,
                'confidentiality_signed' => $request->confidentiality_signed,
                'comments' => $request->comments,
                'updated_at' => now(),
            ];

            DB::table('clearance_hr_details')
                ->where('clearance_id', $id)
                ->update($updateData);

            // Update clearance status to completed if all items are Yes
            $allCompleted = $this->checkHrClearanceCompletion($id);
            if ($allCompleted) {
                DB::table('offboarding_clearances')
                    ->where('id', $id)
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            return redirect()->back()->with('success', 'HR clearance updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update HR clearance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update HR clearance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update Admin Clearance Details
     * Route expected: offboarding.admin.clearance.update (PUT) with $id = clearance id
     */
    public function updateAdminClearance(Request $request, $id)
    {
        $request->validate([
            'id_badge_returned' => 'required|in:Yes,No,NA',
            'building_access_revoked' => 'required|in:Yes,No,NA',
            'access_cards_returned' => 'required|in:Yes,No,NA',
            'parking_permit_returned' => 'required|in:Yes,No,NA',
            'other_property_returned' => 'required|in:Yes,No,NA',
            'comments' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'id_badge_returned' => $request->id_badge_returned,
                'building_access_revoked' => $request->building_access_revoked,
                'access_cards_returned' => $request->access_cards_returned,
                'parking_permit_returned' => $request->parking_permit_returned,
                'other_property_returned' => $request->other_property_returned,
                'other_property_specify' => $request->other_property_specify ?? null,
                'comments' => $request->comments,
                'updated_at' => now(),
            ];

            DB::table('clearance_admin_details')
                ->where('clearance_id', $id)
                ->update($updateData);

            // Update clearance status to completed if all items are Yes
            $allCompleted = $this->checkAdminClearanceCompletion($id);
            if ($allCompleted) {
                DB::table('offboarding_clearances')
                    ->where('id', $id)
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            return redirect()->back()->with('success', 'Admin clearance updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update admin clearance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update admin clearance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete clearance task
     * Route expected: offboarding.clearance.destroy (DELETE) with $id = clearance id
     */
    public function destroyClearance($id)
    {
        DB::beginTransaction();
        try {
            // Get clearance type first
            $clearance = DB::table('offboarding_clearances')->where('id', $id)->first();

            if ($clearance) {
                // Delete details based on type
                switch ($clearance->clearance_type) {
                    case 'it':
                        DB::table('clearance_it_details')->where('clearance_id', $id)->delete();
                        break;
                    case 'hr':
                        DB::table('clearance_hr_details')->where('clearance_id', $id)->delete();
                        break;
                    case 'admin':
                        DB::table('clearance_admin_details')->where('clearance_id', $id)->delete();
                        break;
                }

                // Delete clearance task
                DB::table('offboarding_clearances')->where('id', $id)->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete clearance: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Assign clearance to employee
     */
    public function assignClearance(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:allemployees,id',
        ]);

        try {
            DB::table('offboarding_clearances')
                ->where('id', $id)
                ->update([
                    'assigned_to' => $request->assigned_to,
                    'updated_at' => now(),
                ]);

            return redirect()->back()->with('success', 'Clearance assigned successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to assign clearance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign clearance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document_type' => 'required|string',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('offboarding_documents', $fileName, 'public');

                // Optionally insert record into a documents table here
                // DB::table('offboarding_documents')->insert([...]);
            }

            return redirect()->back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to upload document: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload document. Error: ' . $e->getMessage());
        }
    }

    /**
     * Get employees for dropdown
     */
    public function getEmployees()
    {
        $employees = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->get();

        return response()->json($employees);
    }

    /**
     * Create default clearance tasks for offboarding
     */
    private function createClearanceTasks($offboardingId, $employeeId, $employeeCode)
    {
        $defaultTasks = [
            [
                'task_name' => 'IT Clearance',
                'clearance_type' => 'it',
                'department' => 'IT'
            ],
            [
                'task_name' => 'HR Clearance',
                'clearance_type' => 'hr',
                'department' => 'HR'
            ],
            [
                'task_name' => 'Admin Clearance',
                'clearance_type' => 'admin',
                'department' => 'Admin'
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($defaultTasks as $task) {
                // Insert clearance task
                $clearanceId = DB::table('offboarding_clearances')->insertGetId([
                    'offboarding_id' => $offboardingId,
                    'task_name' => $task['task_name'],
                    'clearance_type' => $task['clearance_type'],
                    'department' => $task['department'],
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create details record based on clearance type
                switch ($task['clearance_type']) {
                    case 'it':
                        DB::table('clearance_it_details')->insert([
                            'clearance_id' => $clearanceId,
                            'employee_id' => $employeeCode,
                            'offboarding_id' => 'Request-' . $offboardingId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        break;

                    case 'hr':
                        DB::table('clearance_hr_details')->insert([
                            'clearance_id' => $clearanceId,
                            'employee_id' => $employeeCode,
                            'offboarding_id' => 'Request-' . $offboardingId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        break;

                    case 'admin':
                        DB::table('clearance_admin_details')->insert([
                            'clearance_id' => $clearanceId,
                            'employee_id' => $employeeCode,
                            'offboarding_id' => 'Request-' . $offboardingId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        break;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create default clearance tasks: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if IT clearance is completed
     */
    private function checkItClearanceCompletion($clearanceId)
    {
        $details = DB::table('clearance_it_details')
            ->where('clearance_id', $clearanceId)
            ->first();

        if (!$details) return false;

        $fields = [
            'laptop_returned',
            'mobile_phone_returned',
            'keys_returned',
            'email_deactivated',
            'system_access_revoked',
            'vpn_access_revoked',
            'software_licenses_deactivated'
        ];

        foreach ($fields as $field) {
            if (!isset($details->$field) || $details->$field !== 'Yes') {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if HR clearance is completed
     */
    private function checkHrClearanceCompletion($clearanceId)
    {
        $details = DB::table('clearance_hr_details')
            ->where('clearance_id', $clearanceId)
            ->first();

        if (!$details) return false;

        $fields = [
            'exit_interview_completed',
            'final_paycheck_processed',
            'unused_vacation_compensated',
            'benefits_terminated',
            'forms_signed',
            'nda_signed'
        ];

        foreach ($fields as $field) {
            if (!isset($details->$field) || $details->$field !== 'Yes') {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if Admin clearance is completed
     */
    private function checkAdminClearanceCompletion($clearanceId)
    {
        $details = DB::table('clearance_admin_details')
            ->where('clearance_id', $clearanceId)
            ->first();

        if (!$details) return false;

        $fields = [
            'id_badge_returned',
            'building_access_revoked',
            'access_cards_returned'
        ];

        foreach ($fields as $field) {
            if (!isset($details->$field) || $details->$field !== 'Yes') {
                return false;
            }
        }

        return true;
    }
}
