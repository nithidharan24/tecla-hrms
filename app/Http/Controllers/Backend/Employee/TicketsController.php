<?php

namespace App\Http\Controllers\Backend\Tickets;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TicketsController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();

        // Count tickets based on different statuses - MAIN COUNT QUERY
        $queryCount = DB::table('tickets');
        
        // If employee, only count their assigned tickets OR tickets they created
        if ($role === 'employee') {
            $queryCount->where(function($q) use ($employeeId) {
                $q->where('assign_1', $employeeId)
                  ->orWhere('assign_2', $employeeId)
                  ->orWhere('assign_3', $employeeId)
                  ->orWhere('created_by', $employeeId);
            });
        }

        // Apply branch filter to counts
        if ($branchFilter) {
            $queryCount->where(function($q) use ($branchFilter) {
                $q->whereIn('id', function($sub) use ($branchFilter) {
                    $sub->select('tickets.id')
                        ->from('tickets')
                        ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
                        ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
                        ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
                        ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
                        ->where(function($q2) use ($branchFilter) {
                            $q2->where('assign1.branch_id', $branchFilter)
                               ->orWhere('assign2.branch_id', $branchFilter)
                               ->orWhere('assign3.branch_id', $branchFilter)
                               ->orWhere('creator.branch_id', $branchFilter);
                        });
                });
            });
        }

        $newTicketsCount = (clone $queryCount)->where('status', 'new')->count();
        $solvedTicketsCount = (clone $queryCount)->where('status', 'closed')->count();
        $openTicketsCount = (clone $queryCount)->where('status', 'open')->count();
        $pendingTicketsCount = (clone $queryCount)->where('status', 'in progress')->count();
        $allTicketsCount = $queryCount->count();

        // Main query for tickets
        $query = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
            ->select(
                'tickets.*',
                'assign1.firstname as assign1_firstname',
                'assign1.lastname as assign1_lastname',
                'assign1.profile_image as assign1_image',
                'assign1.branch_id as assign1_branch',
                'assign2.firstname as assign2_firstname',
                'assign2.lastname as assign2_lastname',
                'assign2.profile_image as assign2_image',
                'assign2.branch_id as assign2_branch',
                'assign3.firstname as assign3_firstname',
                'assign3.lastname as assign3_lastname',
                'assign3.profile_image as assign3_image',
                'assign3.branch_id as assign3_branch',
                'creator.firstname as creator_firstname',
                'creator.lastname as creator_lastname',
                'creator.branch_id as creator_branch'
            );

        // Branch filter for main query
        if ($branchFilter) {
            $query->where(function($q) use ($branchFilter) {
                $q->where('assign1.branch_id', $branchFilter)
                  ->orWhere('assign2.branch_id', $branchFilter)
                  ->orWhere('assign3.branch_id', $branchFilter)
                  ->orWhere('creator.branch_id', $branchFilter);
            });
        }
        
        // If employee, show their assigned tickets AND tickets they created
        if ($role === 'employee') {
            $query->where(function($q) use ($employeeId) {
                $q->where('tickets.assign_1', $employeeId)
                  ->orWhere('tickets.assign_2', $employeeId)
                  ->orWhere('tickets.assign_3', $employeeId)
                  ->orWhere('tickets.created_by', $employeeId);
            });
        }

        // ========== FILTER APPLICATIONS ==========
        // Ticket Subject filter
        if ($request->filled('ticket_subject')) {
            $query->where('tickets.ticket_subject', 'like', '%' . $request->ticket_subject . '%');
        }

        // Ticket ID filter
        if ($request->filled('ticket_id')) {
            $query->where('tickets.ticket_id', 'like', '%' . $request->ticket_id . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('tickets.status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('tickets.priority', $request->priority);
        }

        // Created By filter
        if ($request->filled('created_by')) {
            $query->where('tickets.created_by', $request->created_by);
        }

        // Assigned To filter
        if ($request->filled('assigned_to')) {
            $query->where(function($q) use ($request) {
                $q->where('tickets.assign_1', $request->assigned_to)
                  ->orWhere('tickets.assign_2', $request->assigned_to)
                  ->orWhere('tickets.assign_3', $request->assigned_to);
            });
        }

        // Date filtering
        if ($request->filled('start_date')) {
            $query->whereDate('tickets.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tickets.created_at', '<=', $request->end_date);
        }

        // Order by creation date descending
        $query->orderBy('tickets.created_at', 'desc');
        
        // Get filtered tickets
        $tickets = $query->get();

        // Get assignable employees from master configuration
        $assignableEmployees = $this->getAssignableEmployees();

        // Get creators list for filter dropdown
        $creatorsQuery = DB::table('allemployees')
            ->whereIn('id', function($sub) use ($branchFilter, $role, $employeeId) {
                $sub->select('created_by')->from('tickets');
                
                // Apply the same filters as main query
                if ($branchFilter) {
                    $sub->whereIn('id', function($sub2) use ($branchFilter) {
                        $sub2->select('tickets.id')
                            ->from('tickets')
                            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
                            ->where('creator.branch_id', $branchFilter);
                    });
                }
                
                if ($role === 'employee') {
                    $sub->where(function($q) use ($employeeId) {
                        $q->where('assign_1', $employeeId)
                          ->orWhere('assign_2', $employeeId)
                          ->orWhere('assign_3', $employeeId)
                          ->orWhere('created_by', $employeeId);
                    });
                }
            })
            ->select('id', 'firstname', 'lastname')
            ->distinct()
            ->orderBy('firstname')
            ->orderBy('lastname');

        $creators = $creatorsQuery->get();

        // Get activity logs for display
        $activityLogs = $this->getActivityLogs();

        // Check if we're using new design or old design
        $useNewDesign = $request->has('new_design') || Session::get('tickets_new_design', false);
        
        if ($useNewDesign) {
            // Store preference in session
            Session::put('tickets_new_design', true);
            
            return view('tickets.new-tickets', compact(
                'tickets',
                'allTicketsCount',
                'newTicketsCount',
                'solvedTicketsCount',
                'openTicketsCount',
                'pendingTicketsCount',
                'assignableEmployees',
                'creators',
                'role',
                'activityLogs'
            ));
        } else {
            // Remove new design preference if it exists
            Session::forget('tickets_new_design');
            
            return view('hrms.Employee.Tickets.index', compact(
                'tickets',
                'allTicketsCount',
                'newTicketsCount',
                'solvedTicketsCount',
                'openTicketsCount',
                'pendingTicketsCount',
                'assignableEmployees',
                'creators',
                'role',
                'activityLogs'
            ));
        }
    }

    /**
     * Get assignable employees from master configuration
     */

    /**
     * Show the form for creating a new ticket.
     */
    public function create() 
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        // Get assignable employees from master configuration
        $assignableEmployees = $this->getAssignableEmployees();

        // Get only assets assigned to the current employee AND with approved status
        $assets = DB::table('assets_company')
            ->where('asset_user', $employeeId)
            ->where('deleted_at', 0)
            ->where('status', 'approved')
            ->get();

        return view('hrms.Employee.Tickets.create', compact('assignableEmployees', 'assets', 'role'));
    }

    public function store(Request $request)
    {
        $ticketData = [
            'ticket_id' => $this->generateUniqueTicketId(),
            'ticket_subject' => $request->ticket_subject,
            'description' => $request->description,
            'status' => $request->status ?? 'new',
            'asset_id' => $request->asset_id,
            'priority' => $request->priority ?? 'Medium',
            'assign_1' => $request->assign_1,
            'assign_2' => $request->assign_2,
            'assign_3' => $request->assign_3,
            'created_by' => Session::get('user_id'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Handle file upload
        if ($request->hasFile('uploaded_files')) {
            $file = $request->file('uploaded_files');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tickets', $fileName, 'public');
            $ticketData['uploaded_files'] = $filePath;
        }

        // Insert ticket
        $ticketId = DB::table('tickets')->insertGetId($ticketData);
        
        // Log the activity
        $this->logActivity(
            $ticketId,
            'created',
            "Ticket #{$ticketData['ticket_id']} was created",
            $ticketData
        );

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');

        $query = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
            ->select(
                'tickets.*',
                'assign1.firstname as assign1_firstname',
                'assign1.lastname as assign1_lastname',
                'assign1.profile_image as assign1_image',
                'assign1.email as assign1_email',
                'assign2.firstname as assign2_firstname',
                'assign2.lastname as assign2_lastname',
                'assign2.profile_image as assign2_image',
                'assign2.email as assign2_email',
                'assign3.firstname as assign3_firstname',
                'assign3.lastname as assign3_lastname',
                'assign3.profile_image as assign3_image',
                'assign3.email as assign3_email',
                'creator.firstname as creator_firstname',
                'creator.lastname as creator_lastname',
                'creator.email as creator_email'
            )
            ->where('tickets.id', $id);

        // Employee can view tickets they created or are assigned to
        if ($role === 'employee') {
            $query->where(function($q) use ($employeeId) {
                $q->where('tickets.assign_1', $employeeId)
                  ->orWhere('tickets.assign_2', $employeeId)
                  ->orWhere('tickets.assign_3', $employeeId)
                  ->orWhere('tickets.created_by', $employeeId);
            });
        }

        $ticket = $query->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found or access denied.');
        }

        // Get activity logs for this ticket
        $activityLogs = DB::table('ticket_activity_logs')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hrms.Employee.Tickets.show', compact('ticket', 'role', 'activityLogs'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit($id)
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');
        
        $ticket = DB::table('tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permissions
        $canEdit = false;
        $editType = '';

        if ($role === 'admin') {
            $canEdit = true;
            $editType = 'admin';
        } elseif ($role === 'employee') {
            if ($ticket->created_by == $employeeId || 
                $ticket->assign_1 == $employeeId || 
                $ticket->assign_2 == $employeeId || 
                $ticket->assign_3 == $employeeId) {
                $canEdit = true;
                $editType = 'employee';
            }
        }

        if (!$canEdit) {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied. You can only edit tickets you created or are assigned to.');
        }

        $assignableEmployees = [];
        if ($role === 'admin') {
            $assignableEmployees = $this->getAssignableEmployees();
        }

        return view('hrms.Employee.Tickets.edit', compact('ticket', 'assignableEmployees', 'role', 'editType'));
    }

    /**
     * Get assignable employees from master configuration
     */
    private function getAssignableEmployees()
    {
        return DB::table('ticket_assignees as ta')
            ->join('allemployees as emp', 'ta.employee_id', '=', 'emp.id')
            ->where('ta.is_active', true)
            ->where('emp.deleted_at', 0)
            ->where('emp.status', 'active')
            ->select(
                'emp.id',
                'emp.firstname',
                'emp.lastname',
                'emp.profile_image',
                'emp.email',
                'ta.role as assignee_role',
                'ta.priority'
            )
            ->orderBy('ta.priority', 'asc')
            ->orderBy('emp.firstname', 'asc')
            ->get();
    }
    
    /**
     * Update the specified ticket.
     */
    public function update(Request $request, $id)
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');
        
        $ticket = DB::table('tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permissions
        $canEdit = false;
        if ($role === 'admin') {
            $canEdit = true;
        } elseif ($role === 'employee') {
            // Employee can edit if they created the ticket OR are assigned to it
            if ($ticket->created_by == $employeeId || 
                $ticket->assign_1 == $employeeId || 
                $ticket->assign_2 == $employeeId || 
                $ticket->assign_3 == $employeeId) {
                $canEdit = true;
            }
        }

        if (!$canEdit) {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied. You can only edit tickets you created or are assigned to.');
        }

        // Validation rules based on role
        $rules = [
            'ticket_subject' => 'required|string|max:255',
            'description' => 'required|string',
            'uploaded_files' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:10240',
        ];

        if ($role === 'admin') {
            // Admin can change everything
            $rules['priority'] = 'required|in:High,Medium,Low';
            $rules['status'] = 'required|in:new,open,in progress,on hold,closed,cancelled,reopened';
            $rules['assign_1'] = 'nullable|exists:allemployees,id';
            $rules['assign_2'] = 'nullable|exists:allemployees,id';
            $rules['assign_3'] = 'nullable|exists:allemployees,id';
        } elseif ($role === 'employee') {
            // Employee can only change status (limited options) and description
            $rules['status'] = 'required|in:open,in progress,on hold,closed,reopened';
        }

        $request->validate($rules);

        try {
            $updateData = [
                'ticket_subject' => $request->ticket_subject,
                'description' => $request->description,
                'updated_at' => now(),
            ];

            $changes = [];

            // Track changes
            if ($ticket->ticket_subject !== $request->ticket_subject) {
                $changes['ticket_subject'] = [
                    'old' => $ticket->ticket_subject,
                    'new' => $request->ticket_subject
                ];
            }

            if ($ticket->description !== $request->description) {
                $changes['description'] = [
                    'old' => $ticket->description,
                    'new' => $request->description
                ];
            }

            if ($role === 'admin') {
                // Admin can update everything
                if ($ticket->priority !== $request->priority) {
                    $updateData['priority'] = $request->priority;
                    $changes['priority'] = [
                        'old' => $ticket->priority,
                        'new' => $request->priority
                    ];
                }

                if ($ticket->status !== $request->status) {
                    $updateData['status'] = $request->status;
                    $changes['status'] = [
                        'old' => $ticket->status,
                        'new' => $request->status
                    ];
                }

                // Track assignment changes
                if ($ticket->assign_1 != $request->assign_1) {
                    $updateData['assign_1'] = $request->assign_1;
                    $changes['assign_1'] = [
                        'old' => $ticket->assign_1,
                        'new' => $request->assign_1
                    ];
                }

                if ($ticket->assign_2 != $request->assign_2) {
                    $updateData['assign_2'] = $request->assign_2;
                    $changes['assign_2'] = [
                        'old' => $ticket->assign_2,
                        'new' => $request->assign_2
                    ];
                }

                if ($ticket->assign_3 != $request->assign_3) {
                    $updateData['assign_3'] = $request->assign_3;
                    $changes['assign_3'] = [
                        'old' => $ticket->assign_3,
                        'new' => $request->assign_3
                    ];
                }
            } elseif ($role === 'employee') {
                // Employee can only update status (limited options)
                if ($ticket->status !== $request->status) {
                    $updateData['status'] = $request->status;
                    $changes['status'] = [
                        'old' => $ticket->status,
                        'new' => $request->status
                    ];
                }
            }

            // Handle file upload
            if ($request->hasFile('uploaded_files')) {
                // Delete old file if exists
                if ($ticket->uploaded_files) {
                    Storage::disk('public')->delete($ticket->uploaded_files);
                }

                $file = $request->file('uploaded_files');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('tickets', $fileName, 'public');
                $updateData['uploaded_files'] = $filePath;
                $changes['uploaded_files'] = [
                    'old' => $ticket->uploaded_files,
                    'new' => $filePath
                ];
            }

            DB::table('tickets')->where('id', $id)->update($updateData);

            // Log the activity
            if (!empty($changes)) {
                $this->logActivity(
                    $id,
                    'updated',
                    "Ticket #{$ticket->ticket_id} was updated",
                    $changes
                );
            }

            $message = $role === 'admin' 
                ? 'Ticket updated successfully.' 
                : 'Ticket updated successfully. Administrative changes require admin approval.';

            return redirect()->route('tickets.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update ticket.')
                ->withInput();
        }
    }

    /**
     * Remove the specified ticket.
     */
    public function destroy($id)
    {
        $role = Session::get('role');
        
        if ($role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'Access denied. Only administrators can delete tickets.');
        }

        try {
            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                return redirect()->route('tickets.index')
                    ->with('error', 'Ticket not found.');
            }

            // Log the activity before deletion
            $this->logActivity(
                $id,
                'deleted',
                "Ticket #{$ticket->ticket_id} was deleted",
                [
                    'ticket_id' => $ticket->ticket_id,
                    'ticket_subject' => $ticket->ticket_subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority
                ]
            );

            // Delete uploaded file if exists
            if ($ticket->uploaded_files) {
                Storage::disk('public')->delete($ticket->uploaded_files);
            }

            DB::table('tickets')->where('id', $id)->delete();

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting ticket: ' . $e->getMessage());
            return redirect()->route('tickets.index')
                ->with('error', 'Failed to delete ticket.');
        }
    }

    /**
     * Update ticket status via AJAX.
     */
    public function updateStatus(Request $request, $id)
    {
        $role = Session::get('role');
        
        if ($role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only administrators can update ticket status.'
            ], 403);
        }

        try {
            $request->validate([
                'status' => 'required|string|in:new,open,in progress,on hold,closed,cancelled,reopened'
            ]);

            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            // Track changes
            $changes = [
                'status' => [
                    'old' => $ticket->status,
                    'new' => $request->status
                ]
            ];

            DB::table('tickets')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            // Log the activity
            $this->logActivity(
                $id,
                'status_changed',
                "Ticket #{$ticket->ticket_id} status changed from {$ticket->status} to {$request->status}",
                $changes
            );

            // Get updated counts
            $newTicketsCount = DB::table('tickets')->where('status', 'new')->count();
            $solvedTicketsCount = DB::table('tickets')->where('status', 'closed')->count();
            $openTicketsCount = DB::table('tickets')->where('status', 'open')->count();
            $pendingTicketsCount = DB::table('tickets')->where('status', 'in progress')->count();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'newTicketsCount' => $newTicketsCount,
                'solvedTicketsCount' => $solvedTicketsCount,
                'openTicketsCount' => $openTicketsCount,
                'pendingTicketsCount' => $pendingTicketsCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating ticket status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket priority via AJAX.
     */
    public function updatePriority(Request $request, $id)
    {
        $role = Session::get('role');
        
        if ($role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only administrators can update ticket priority.'
            ], 403);
        }

        try {
            $request->validate([
                'priority' => 'required|string|in:High,Medium,Low'
            ]);

            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            // Track changes
            $changes = [
                'priority' => [
                    'old' => $ticket->priority,
                    'new' => $request->priority
                ]
            ];

            DB::table('tickets')->where('id', $id)->update([
                'priority' => $request->priority,
                'updated_at' => now()
            ]);

            // Log the activity
            $this->logActivity(
                $id,
                'priority_changed',
                "Ticket #{$ticket->ticket_id} priority changed from {$ticket->priority} to {$request->priority}",
                $changes
            );

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating ticket priority: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket assignment via AJAX.
     */
    public function updateAssignment(Request $request, $id)
    {
        $role = Session::get('role');
        
        if ($role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only administrators can update ticket assignments.'
            ], 403);
        }

        try {
            $request->validate([
                'assign_1' => 'nullable|exists:allemployees,id',
                'assign_2' => 'nullable|exists:allemployees,id',
                'assign_3' => 'nullable|exists:allemployees,id',
            ]);

            $ticket = DB::table('tickets')->where('id', $id)->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            $changes = [];

            // Check for assignment changes
            if ($ticket->assign_1 != $request->assign_1) {
                $changes['assign_1'] = [
                    'old' => $ticket->assign_1,
                    'new' => $request->assign_1
                ];
            }

            if ($ticket->assign_2 != $request->assign_2) {
                $changes['assign_2'] = [
                    'old' => $ticket->assign_2,
                    'new' => $request->assign_2
                ];
            }

            if ($ticket->assign_3 != $request->assign_3) {
                $changes['assign_3'] = [
                    'old' => $ticket->assign_3,
                    'new' => $request->assign_3
                ];
            }

            DB::table('tickets')->where('id', $id)->update([
                'assign_1' => $request->assign_1,
                'assign_2' => $request->assign_2,
                'assign_3' => $request->assign_3,
                'updated_at' => now()
            ]);

            // Log the activity if there were changes
            if (!empty($changes)) {
                $this->logActivity(
                    $id,
                    'assigned',
                    "Ticket #{$ticket->ticket_id} assignments were updated",
                    $changes
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating ticket assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity logs
     */
    private function getActivityLogs()
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');

        $query = DB::table('ticket_activity_logs')
            ->leftJoin('tickets', 'ticket_activity_logs.ticket_id', '=', 'tickets.id')
            ->select(
                'ticket_activity_logs.*',
                'tickets.ticket_id as ticket_number',
                'tickets.ticket_subject'
            )
            ->orderBy('ticket_activity_logs.created_at', 'desc')
            ->limit(50); // Show last 50 activities

        // If employee, only show logs for tickets they have access to
        if ($role === 'employee') {
            $query->whereIn('ticket_activity_logs.ticket_id', function($q) use ($employeeId) {
                $q->select('id')
                  ->from('tickets')
                  ->where(function($sub) use ($employeeId) {
                      $sub->where('assign_1', $employeeId)
                          ->orWhere('assign_2', $employeeId)
                          ->orWhere('assign_3', $employeeId)
                          ->orWhere('created_by', $employeeId);
                  });
            });
        }

        return $query->get();
    }

    /**
     * Log ticket activity
     */
    private function logActivity($ticketId, $action, $description, $changes = [])
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

        DB::table('ticket_activity_logs')->insert([
            'ticket_id' => $ticketId,
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
     * Generate a unique ticket ID
     */
    private function generateUniqueTicketId()
    {
        $datePart = now()->format('dmy'); // e.g., 260725
        $prefix = 'TKT-' . $datePart;

        // Find the latest ticket for today
        $latestTicket = DB::table('tickets')
            ->where('ticket_id', 'like', $prefix . '%')
            ->orderBy('ticket_id', 'desc')
            ->first();

        if ($latestTicket) {
            // Extract the last 4 digits and increment
            $lastNumber = (int)substr($latestTicket->ticket_id, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Download the ticket attachment.
     */
    public function download($id)
    {
        $employeeId = Session::get('user_id');
        $role = Session::get('role');
        
        $ticket = DB::table('tickets')->where('id', $id)->first();
        
        if (!$ticket) {
            return redirect()->route('tickets.index')
                ->with('error', 'Ticket not found.');
        }

        // Check permissions - employee can download if they created or are assigned to the ticket
        if ($role === 'employee') {
            if (!($ticket->created_by == $employeeId || 
                  $ticket->assign_1 == $employeeId || 
                  $ticket->assign_2 == $employeeId || 
                  $ticket->assign_3 == $employeeId)) {
                return redirect()->route('tickets.index')
                    ->with('error', 'Access denied.');
            }
        }

        if (!$ticket->uploaded_files) {
            return redirect()->back()
                ->with('error', 'No attachment found for this ticket.');
        }

        $filePath = storage_path('app/public/' . $ticket->uploaded_files);
        
        if (!file_exists($filePath)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        $fileName = pathinfo($ticket->uploaded_files, PATHINFO_BASENAME);
        
        return response()->download($filePath, $fileName);
    }
}