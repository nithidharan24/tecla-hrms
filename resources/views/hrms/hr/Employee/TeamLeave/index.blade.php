@extends('layouts.index')

@section('content')
@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Team Leaves');
    $year = request('year', date('Y'));
    $today = now()->format('Y-m-d');
@endphp

<div class="content container-fluid mt-3">


    <!-- ==============================================
         MAIN CONTENT TABS
    ================================================= -->
    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#team-members">
                        Reportees
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#on-leave">
                        On Leave
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#leave-requests">
                        Leave Requests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#permission-requests">
                        Permission Requests
                    </button>
                </li>
                
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content">
                
                <!-- ================= TEAM MEMBERS TAB ================= -->
                <div class="tab-pane fade show active" id="team-members" role="tabpanel">
                    @if(count($teamMembers) > 0)
                        <div class="row g-3 p-3">
                            @foreach($teamMembers as $member)
                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                                    <div class="team-member-card-small">
                                        <div class="member-header">
                                            <div class="member-avatar-small">
                                                <div class="avatar-circle-small">
                                                    {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                                                </div>
                                            </div>
                                            
                                            <div class="member-info-small">
                                                <h6 class="member-name-small mb-1">{{ $member->firstname }} {{ $member->lastname }}</h6>
                                               
                                            </div>
                                        </div>
                                        
                                        <div class="member-body">
                                            
                                            
<div class="member-status-small">
    @php
        // Simple check: if they have attendance today and they're not on leave
        $hasAttendance = $member->attendanceToday ?? false;
        $isOnLeave = $member->onLeaveToday ?? false;
    @endphp
    
    @if($isOnLeave)
        <span class="badge bg-danger badge-sm">On Leave</span>
    @elseif($hasAttendance)
        <span class="badge bg-success badge-sm">Present</span>
    @else
        <!-- Check if today is a working day -->
        @php
            $isWorkingDay = $member->isWorkingDay ?? true;
            $workingDayReason = $member->workingDayReason ?? 'Working day';
        @endphp
        
        @if(!$isWorkingDay)
            @if(strpos(strtolower($workingDayReason), 'holiday') !== false)
                <span class="badge bg-info badge-sm">Holiday</span>
            @else
                <span class="badge bg-secondary badge-sm">Week Off</span>
            @endif
        @else
            <span class="badge bg-dark badge-sm">Absent</span>
        @endif
    @endif
</div>
                                        </div>
                                        
                                        <div class="member-footer mt-2">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('team-leaves.show', $member->id) }}" 
                                                   class="btn btn-sm btn-outline-primary btn-block flex-fill">
                                                    <i class="fa-solid fa-eye fa-xs"></i> View
                                                </a>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
                                <h5>No Team Members Found</h5>
                                <p class="text-muted">You are not assigned as a team lead or manager for any employees.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- ================= ON LEAVE TAB ================= -->
                <div class="tab-pane fade" id="on-leave" role="tabpanel">
                    @php
                        $membersOnLeave = collect($teamMembers)->filter(function($member) use ($today) {
                            return collect($member->leaves)->where('status', 'approved')
                                ->where('from_date', '<=', $today)
                                ->where('to_date', '>=', $today)
                                ->count() > 0;
                        });
                    @endphp
                    
                    @if($membersOnLeave->count() > 0)
                        <div class="row g-3 p-3">
                            @foreach($membersOnLeave as $member)
                                @php
                                    $currentLeave = collect($member->leaves)->where('status', 'approved')
                                        ->where('from_date', '<=', $today)
                                        ->where('to_date', '>=', $today)
                                        ->first();
                                @endphp
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                    <div class="on-leave-card-small">
                                        <div class="leave-header-small">
                                            <div class="leave-avatar-small">
                                                <div class="avatar-circle-small">
                                                    {{ substr($member->firstname, 0, 1) }}{{ substr($member->lastname, 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="leave-member-info-small">
                                                <h6 class="mb-1">{{ $member->firstname }} {{ $member->lastname }}</h6>
                                                <div class="text-muted small">
                                                    EMP {{ $member->employeeid }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="leave-details-small mt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-warning badge-sm">{{ $currentLeave->leave_type ?? 'N/A' }}</span>
                                                <span class="text-muted small">{{ $currentLeave->no_of_days }} days</span>
                                            </div>
                                            
                                            <div class="leave-dates-small small">
                                                <i class="fa-solid fa-calendar text-muted me-1"></i>
                                                {{ \Carbon\Carbon::parse($currentLeave->from_date)->format('d-M') }} 
                                                to 
                                                {{ \Carbon\Carbon::parse($currentLeave->to_date)->format('d-M') }}
                                            </div>
                                            
                                            <div class="leave-reason-small small mt-1">
                                                <i class="fa-solid fa-note-sticky text-muted me-1"></i>
                                                {{ Str::limit($currentLeave->leave_reason ?? 'No reason provided', 40) }}
                                            </div>
                                        </div>
                                        
                                        <div class="leave-footer-small mt-2">
                                            <a href="{{ route('team-leaves.show', $member->id) }}" 
                                               class="btn btn-sm btn-outline-primary btn-block">
                                                <i class="fa-solid fa-eye fa-xs"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fa-solid fa-calendar-check fa-3x text-success mb-3"></i>
                                <h5>No Team Members on Leave Today</h5>
                                <p class="text-muted">All your team members are present today.</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- ================= LEAVE REQUESTS TAB ================= -->
                <div class="tab-pane fade" id="leave-requests" role="tabpanel">
                    @php
                        $pendingLeaves = [];
                        foreach($teamMembers as $member) {
                            foreach($member->leaves as $leave) {
                                if($leave->status == 'pending') {
                                    $pendingLeaves[] = [
                                        'member' => $member,
                                        'leave' => $leave
                                    ];
                                }
                            }
                        }
                    @endphp
                       @if(isset($permissions) && $permissions->can_approve)
                    @if(count($pendingLeaves) > 0)
                    <div class="mb-2">
                        <select id="bulkLeaveAction" class="form-select form-select-sm w-auto d-inline-block">
                            <option value="">Bulk Action</option>
                            <option value="approved">Approve Selected</option>
                            <option value="declined">Decline Selected</option>
                        </select>
                    
                    </div>
                    @endif
                    
                        <div class="table-responsive">
                            <table class="table table-hover custom-table datatable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAllLeaves">
                                        </th>
                                        
                                        <th>Employee</th>
                                        <th>Employee ID</th>
                                        <th>Leave Type</th>
                                        <th>Leave Period</th>
                                        <th>Days</th>
                                        <th>Reason</th>
                                        <th>Applied On</th>
                                        <th>View</th>
                                        @if(isset($permissions) && $permissions->can_approve)
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingLeaves as $item)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="leave-checkbox" value="{{ $item['leave']->id }}">
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <div class="avatar-circle-xs">
                                                            {{ substr($item['member']->firstname, 0, 1) }}{{ substr($item['member']->lastname, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $item['member']->firstname }} {{ $item['member']->lastname }}</div>
                                                     
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item['member']->employeeid }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $item['leave']->leave_type }}</span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($item['leave']->from_date)->format('d-M-Y') }}
                                                <br><small class="text-muted">to</small><br>
                                                {{ \Carbon\Carbon::parse($item['leave']->to_date)->format('d-M-Y') }}
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $item['leave']->no_of_days }}</span> days
                                            </td>
                                            <td>
                                                <span data-bs-toggle="tooltip" title="{{ $item['leave']->leave_reason }}">
                                                    {{ Str::limit($item['leave']->leave_reason, 30) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($item['leave']->created_at)->format('d-M-Y') }}
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item['leave']->created_at)->format('h:i A') }}</small>
                                            </td>
                                            <td data-label="Actions" class="text-end">
                                                <a href="{{ route('admin-leaves.show', $leave->id) }}" class="od-icon-btn">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                            @if(isset($permissions) && $permissions->can_approve)
                                            <td data-label="Status" class="text-center" id="leave-status-{{ $item['leave']->id }}">
                                               <div class="dropdown action-label">
                                                    <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="fa-regular fa-circle-dot text-{{ 
                                                            $item['leave']->status == 'approved' ? 'success' : 
                                                            ($item['leave']->status == 'declined' ? 'danger' : 'warning') 
                                                        }}"></i> {{ ucfirst($item['leave']->status) }}
                                                    </button>
                                                
                                                    <div class="dropdown-menu dropdown-menu-right">

                                                        <!-- Pending -->
                                                        <form action="{{ url('admin-leaves/'.$item['leave']->id.'/update-status') }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                            </button>
                                                        </form>
                                                    
                                                        <!-- Approved -->
                                                        <form action="{{ url('admin-leaves/'.$item['leave']->id.'/update-status') }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                            </button>
                                                        </form>
                                                    
                                                        <!-- Declined -->
                                                        <form action="{{ url('admin-leaves/'.$item['leave']->id.'/update-status') }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="declined">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                                                            </button>
                                                        </form>
                                                    
                                                    </div>
                                               
                                                </div>
                                                
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>No Pending Leave Requests</h5>
                                <p class="text-muted">All leave requests have been processed.</p>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- ================= PERMISSION REQUESTS TAB ================= -->
<div class="tab-pane fade" id="permission-requests" role="tabpanel">
    @php
        $pendingPermissions = DB::table('employee_permissions')
            ->whereIn('employee_id', collect($teamMembers)->pluck('id'))
            ->where('status', 'pending')
            ->orderBy('permission_date', 'desc')
            ->get();
    @endphp

    @if($pendingPermissions->count() > 0)
    <div class="mb-2">
        <select id="bulkPermissionAction" class="form-select form-select-sm w-auto d-inline-block">
            <option value="">Bulk Action</option>
            <option value="approved">Approve Selected</option>
            <option value="declined">Decline Selected</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-hover custom-table datatable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAllPermissions">
                    </th>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>View</th>
                    @if(isset($permissions) && $permissions->can_approve)
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($pendingPermissions as $p)
                    @php
                        $emp = collect($teamMembers)->firstWhere('id', $p->employee_id);
                    @endphp

                    <tr>
                        <!-- Checkbox -->
                        <td>
                            <input type="checkbox" class="permission-checkbox" value="{{ $p->id }}">
                        </td>

                        <!-- Employee Name -->
                        <td>
                            @if($emp)
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <div class="avatar-circle-xs">
                                        {{ substr($emp->firstname,0,1) }}{{ substr($emp->lastname,0,1) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $emp->firstname }} {{ $emp->lastname }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">Employee Not Found</span>
                            @endif
                        </td>

                        <!-- Employee ID -->
                        <td>{{ $emp->employeeid ?? 'N/A' }}</td>

                        <!-- Date -->
                        <td>{{ $p->permission_date }}</td>

                        <!-- Time -->
                        <td>{{ $p->start_time }} - {{ $p->end_time }}</td>

                        <!-- Reason -->
                        <td>{{ Str::limit($p->permission_reason, 30) }}</td>

                        <!-- Duration -->
                        <td>{{ $p->duration }} hour(s)</td>

                        <!-- Status -->
                        <td>
                            <span class="badge bg-warning">Pending</span>
                        </td>

                        <!-- View -->
                        <td>
                            <a href="{{ url('permissions/'.$p->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                        
                        <!-- Actions -->
                        @if(isset($permissions) && $permissions->can_approve)
                        <td data-label="Status" class="text-center" id="permission-status-{{ $p->id }}">
                            <div class="dropdown action-label">
                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- Pending -->
                                    <form action="{{ route('admin-permissions.update-status', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                        </button>
                                    </form>

                                    <!-- Approved -->
                                    <form action="{{ route('admin-permissions.update-status', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                        </button>
                                    </form>

                                    <!-- Declined -->
                                    <form action="{{ route('admin-permissions.update-status', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                <h5>No Pending Permission Requests</h5>
                <p class="text-muted">All permission requests have been processed.</p>
            </div>
        </div>
    @endif
</div>
                
            </div>
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply Leave for Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="leaveFormContainer"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Quick Stats Cards - Orange Icons */
.stat-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border-radius: 10px;
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-3px);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.nav-tabs {
  
   margin-top: -60px;
}
.bg-orange { 
    background: linear-gradient(45deg, #ff5500, #ff5500) !important; 
}
.bg-orange i { color: white !important; }

/* Card Header Tabs */
.card-header-tabs .nav-link {
    border: none;
    color: #666;
    font-weight: 500;
    padding: 12px 20px;
    border-bottom: 3px solid transparent;
}
.card-header-tabs .nav-link.active {
    color: #f97316;
    border-bottom-color: #f97316;
    background: transparent;
}

/* Small Team Member Card (Reportees Tab) */
.team-member-card-small {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    height: 100%;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}
.team-member-card-small:hover {
    border-color: #ff7f2a;
    box-shadow: 0 4px 12px rgba(255, 127, 42, 0.1);
    transform: translateY(-2px);
}

.member-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.avatar-circle-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ff5500, #ff5500);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.member-info-small {
    margin-left: 12px;
    flex: 1;
}
.member-name-small {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    color: #333;
}
.member-details-small {
    font-size: 11px;
    color: #666;
}

.member-body {
    margin-bottom: 12px;
}
.member-location-small {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
}
.member-status-small .badge-sm {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
}

.member-footer {
    margin-top: auto;
}
.member-footer .btn {
    font-size: 12px;
    padding: 5px 8px;
}

/* Small On Leave Card */
.on-leave-card-small {
    background: #fff;
    border: 1px solid #ffe5d6;
    border-radius: 10px;
    padding: 15px;
    height: 100%;
    transition: all 0.2s;
}
.on-leave-card-small:hover {
    border-color: #ff7f2a;
    box-shadow: 0 4px 12px rgba(255, 127, 42, 0.1);
}

.leave-header-small {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}
.leave-member-info-small h6 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
}

.leave-details-small {
    background: #fff8f3;
    border-radius: 6px;
    padding: 10px;
}
.leave-dates-small, .leave-reason-small {
    font-size: 12px;
}

.leave-footer-small .btn {
    font-size: 12px;
    padding: 5px 8px;
}

/* Table for Leave Requests */
.custom-table {
    font-size: 13px;
}
.custom-table thead {
    background: #f8f9fa;
}
.custom-table th {
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 15px;
}
.custom-table td {
    padding: 10px 15px;
    vertical-align: middle;
}

.avatar-circle-xs {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ff5500, #ff5500);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.btn-group-sm .btn {
    padding: 4px 8px;
    font-size: 12px;
}

/* Badge Sizes */
.badge-sm {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
}

/* Empty State */
.empty-state {
    padding: 40px 20px;
    color: #666;
}
.empty-state i {
    opacity: 0.5;
}
.empty-state h5 {
    margin-bottom: 10px;
    color: #444;
}

/* Year Navigation */
.zs-date-box {
    padding: 5px 12px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #dce1e8;
    font-size: 13px;
    min-width: 200px;
    text-align: center;
}

/* Responsive Grid */
@media (max-width: 768px) {
    .col-sm-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
@media (max-width: 576px) {
    .col-sm-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<script>
    
    function updateLeaveStatus(leaveId, status) {
        $.ajax({
            url: "{{ url('admin-leaves') }}/" + leaveId + "/update-status",
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the status',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating the status: ' + xhr.responseText,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

document.addEventListener("DOMContentLoaded", function () {
    // Year navigation
    const yearInput = document.getElementById("summaryYear");
    const yearRangeBox = document.getElementById("yearRangeBox");

    if (yearInput && yearRangeBox) {
        function updateDisplay() {
            const y = yearInput.value;
            yearRangeBox.innerHTML = `01-Jan-${y} - 31-Dec-${y}`;
        }

        // Prev Year
        const prevYearBtn = document.getElementById("prevYearBtn");
        if (prevYearBtn) {
            prevYearBtn.addEventListener("click", function () {
                yearInput.value = parseInt(yearInput.value) - 1;
                updateDisplay();
                reloadSummary();
            });
        }

        // Next Year
        const nextYearBtn = document.getElementById("nextYearBtn");
        if (nextYearBtn) {
            nextYearBtn.addEventListener("click", function () {
                yearInput.value = parseInt(yearInput.value) + 1;
                updateDisplay();
                reloadSummary();
            });
        }

        // Calendar Picker
        const yearPickerBtn = document.getElementById("yearPickerBtn");
        if (yearPickerBtn) {
            yearPickerBtn.addEventListener("click", function () {
                let y = prompt("Enter year (YYYY):", yearInput.value);
                if (y && !isNaN(y)) {
                    yearInput.value = y;
                    updateDisplay();
                    reloadSummary();
                }
            });
        }

        // Reload summary UI with selected year
        function reloadSummary() {
            window.location.href = `?year=${yearInput.value}`;
        }
    }

    // Initialize Bootstrap tabs
    const triggerTabList = document.querySelectorAll('[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Apply leave function
function applyLeaveForMember(employeeId) {
    fetch(`/team-leaves/${employeeId}/apply-leave-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('leaveFormContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('applyLeaveModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load leave form');
        });
}

// Approve leave function
function approveLeave(leaveId) {
    if (confirm('Are you sure you want to approve this leave request?')) {
        fetch(`/team-leaves/${leaveId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Leave approved successfully!');
                location.reload();
            } else {
                alert('Failed to approve leave: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to approve leave');
        });
    }
}

// Reject leave function
function rejectLeave(leaveId) {
    const reason = prompt('Please enter reason for rejection:');
    if (reason !== null) {
        fetch(`/team-leaves/${leaveId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Leave rejected successfully!');
                location.reload();
            } else {
                alert('Failed to reject leave: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to reject leave');
        });
    }
}

// ===========================
// SELECT ALL CHECKBOXES
// ===========================
document.addEventListener('DOMContentLoaded', function () {

// Leaves
document.getElementById('selectAllLeaves')?.addEventListener('click', function () {
    document.querySelectorAll('.leave-checkbox').forEach(cb => cb.checked = this.checked);
});

// Permissions
document.getElementById('selectAllPermissions')?.addEventListener('click', function () {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = this.checked);
});
});

// ===========================
// BULK LEAVE APPROVAL / DECLINE
// ===========================
function applyBulkLeaveAction() {
let action = document.getElementById("bulkLeaveAction").value;
if (!action) return alert("Select an action");

let selected = [...document.querySelectorAll(".leave-checkbox:checked")].map(cb => cb.value);
if (selected.length === 0) return alert("Select at least one leave.");

fetch("{{ url('/admin-leaves/bulk-update') }}", {
    method: "POST",
    headers: { 
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}" 
    },
    body: JSON.stringify({ ids: selected, status: action })
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        window.location.reload();
    } else {
        alert("Error performing bulk action.");
    }
});
}

// ===========================
// BULK PERMISSION APPROVAL / DECLINE
// ===========================
function applyBulkPermissionAction() {
let action = document.getElementById("bulkPermissionAction").value;
if (!action) return alert("Select an action");

let selected = [...document.querySelectorAll(".permission-checkbox:checked")].map(cb => cb.value);
if (selected.length === 0) return alert("Select at least one permission.");

fetch("{{ url('/admin-permissions/bulk-update') }}", {
    method: "POST",
    headers: { 
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}" 
    },
    body: JSON.stringify({ ids: selected, status: action })
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        window.location.reload();
    } else {
        alert("Error performing bulk action.");
    }
});
}

</script>
@endsection