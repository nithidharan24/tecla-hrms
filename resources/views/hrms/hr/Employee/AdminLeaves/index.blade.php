@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Leaves (Admin)');
    $permissionPermissions = App\Helpers\PermissionHelper::getPermissions('Permissions (Admin)');
    
    // Determine active tab based on filter
    $activeTab = 'leaves'; // Default to leaves tab
    if (request('request_type') == 'permission') {
        $activeTab = 'permissions';
    } elseif (request('request_type') == 'leave') {
        $activeTab = 'leaves';
    }
@endphp
@extends('layouts.index')

@section('content')             

    <!-- Page Content -->
    <div class="content container-fluid mt-5">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Leaves & Permissions Admin</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leaves & Permissions</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Leave Statistics -->
        <div class="row">
            <div class="col-md-3 d-flex">
                <div class="stats-info w-100">
                    <h6>Today Leaves</h6>
                    <h4>{{ $presentEmployees }} / {{ $totalEmployees }}</h4>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stats-info w-100">
                    <h6>Planned Leaves</h6>
                    <h4>{{ $plannedLeaves }} <span>Future</span></h4>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stats-info w-100">
                    <h6>Unplanned Leaves</h6>
                    <h4>{{ $unplannedLeaves }} <span>Today</span></h4>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="stats-info w-100">
                    <h6>Pending Requests</h6>
                    <h4>{{ $pendingRequests + $pendingPermissionRequests }}</h4>
                </div>
            </div>
        </div>
        <!-- /Leave Statistics -->

        <!-- Compact Search Filter -->
        <form method="GET" action="{{ route('admin-leaves.index') }}" class="mb-4" id="filterForm">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-center">
                        <!-- Employee Name -->
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group mb-0">
                                <input type="text" name="employee_name" class="form-control form-control-sm" 
                                       value="{{ request('employee_name') }}" placeholder="Employee Name">
                            </div>
                        </div>

                        <!-- Request Type -->
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group mb-0">
                                <select name="request_type" id="request_type" class="form-select form-select-sm">
                                    <option value="">All Requests</option>
                                    <option value="leave" {{ request('request_type') == 'leave' ? 'selected' : '' }}>Leaves</option>
                                    <option value="permission" {{ request('request_type') == 'permission' ? 'selected' : '' }}>Permissions</option>
                                </select>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group mb-0">
                                <select name="leave_status" id="leave_status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('leave_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('leave_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="declined" {{ request('leave_status') == 'declined' ? 'selected' : '' }}>Declined</option>
                                </select>
                            </div>
                        </div>

                        <!-- From Date -->
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group mb-0">
                                <input type="text" name="from_date" class="form-control form-control-sm datetimepicker"
                                       value="{{ request('from_date') }}" placeholder="From Date">
                            </div>
                        </div>

                        <!-- To Date -->
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group mb-0">
                                <input type="text" name="to_date" class="form-control form-control-sm datetimepicker"
                                       value="{{ request('to_date') }}" placeholder="To Date">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-2 col-sm-12">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-success btn-sm w-50">
                                    <i class="fa fa-search me-1"></i> Search
                                </button>
                                <a href="{{ route('admin-leaves.index') }}" class="btn btn-outline-secondary btn-sm w-50">
                                    <i class="fa fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /Compact Search Filter -->

        <!-- Main Requests Table with Tabs -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="requestsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab == 'leaves' ? 'active' : '' }}" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#leaves" type="button" role="tab" aria-controls="leaves" aria-selected="{{ $activeTab == 'leaves' ? 'true' : 'false' }}">
                                    <i class="fa-solid fa-calendar-day me-2"></i>Leave Requests ({{ $leaves->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab == 'permissions' ? 'active' : '' }}" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="{{ $activeTab == 'permissions' ? 'true' : 'false' }}">
                                    <i class="fa-solid fa-clock me-2"></i>Permission Requests ({{ $employeePermissions->count() }})
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="requestsTabContent">
                            <!-- Leaves Tab -->
                            <div class="tab-pane fade {{ $activeTab == 'leaves' ? 'show active' : '' }}" id="leaves" role="tabpanel" aria-labelledby="leaves-tab">
                                @if($leaves->count() > 0)
                                <div class="leaves-table-container">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Leave Type</th>
                                                    <th>From</th>
                                                    <th>To</th>
                                                    <th>No of Days</th>
                                                    <th>Reason</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($leaves as $leave)
                                                <tr>
                                                    <td data-label="Employee">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-2">
                                                                <strong>{{ $leave->employee_name }}</strong>
                                                                <div class="text-muted">{{ $leave->designation_name }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    <td data-label="Leave Type">
                                                        <span class="od-chip-highlight">{{ $leave->leave_type }}</span>
                                                    </td>
                                                    
                                                    <td data-label="From Date">
                                                        <span class="od-text-highlight">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</span>
                                                    </td>
                                                    
                                                    <td data-label="To Date">
                                                        <span class="od-text-highlight">{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</span>
                                                    </td>
                                                    
                                                    <td data-label="No. of Days">
                                                        {{ $leave->no_of_days }} days
                                                    </td>
                                                    
                                                    <td data-label="Reason">
                                                        {{ $leave->leave_reason }}
                                                    </td>
                                                    
                                                    <td data-label="Status" class="text-center" id="leave-status-{{ $leave->id }}">
                                                        <div class="dropdown action-label">
                                                            <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="fa-regular fa-circle-dot text-{{ 
                                                                    $leave->status == 'approved' ? 'success' : 
                                                                    ($leave->status == 'declined' ? 'danger' : 'warning') 
                                                                }}"></i> {{ ucfirst($leave->status) }}
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item status-change" href="#" data-id="{{ $leave->id }}" data-status="pending" data-type="leave">
                                                                    <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                                </a>
                                                                <a class="dropdown-item status-change" href="#" data-id="{{ $leave->id }}" data-status="approved" data-type="leave">
                                                                    <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                                </a>
                                                                <a class="dropdown-item status-change" href="#" data-id="{{ $leave->id }}" data-status="declined" data-type="leave">
                                                                    <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    <td data-label="Actions" class="text-end">
                                                        <a href="{{ route('admin-leaves.show', $leave->id) }}" class="od-icon-btn">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Leave Requests Found</h5>
                                    <p class="text-muted">No leave requests match your current filters.</p>
                                    @if(request()->hasAny(['employee_name', 'request_type', 'leave_status', 'from_date', 'to_date']))
                                    <a href="{{ route('admin-leaves.index') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa fa-undo me-1"></i> Clear Filters
                                    </a>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Permissions Tab -->
                            <div class="tab-pane fade {{ $activeTab == 'permissions' ? 'show active' : '' }}" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                                @if($employeePermissions->count() > 0)
                                <div class="leaves-table-container">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Date</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Duration (Hours)</th>
                                                    <th>Reason</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employeePermissions as $permission)
                                                <tr>
                                                    <td data-label="Employee">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-2">
                                                                <strong>{{ $permission->employee_name }}</strong>
                                                                <div class="text-muted">{{ $permission->designation_name }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td data-label="Date">{{ \Carbon\Carbon::parse($permission->permission_date)->format('d M Y') }}</td>
                                                    <td data-label="Start Time">{{ \Carbon\Carbon::parse($permission->start_time)->format('h:i A') }}</td>
                                                    <td data-label="End Time">{{ \Carbon\Carbon::parse($permission->end_time)->format('h:i A') }}</td>
                                                    <td data-label="Duration">{{ $permission->duration }} hours</td>
                                                    <td data-label="Reason">{{ $permission->permission_reason }}</td>
                                                    <td data-label="Status" class="text-center" id="permission-status-{{ $permission->id }}">
                                                        <div class="dropdown action-label">
                                                            <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="fa-regular fa-circle-dot text-{{ 
                                                                    $permission->status == 'approved' ? 'success' : 
                                                                    ($permission->status == 'declined' ? 'danger' : 'warning') 
                                                                }}"></i> {{ ucfirst($permission->status) }}
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item permission-status-change" href="#" data-id="{{ $permission->id }}" data-status="pending">
                                                                    <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                                </a>
                                                                <a class="dropdown-item permission-status-change" href="#" data-id="{{ $permission->id }}" data-status="approved">
                                                                    <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                                </a>
                                                                <a class="dropdown-item permission-status-change" href="#" data-id="{{ $permission->id }}" data-status="declined">
                                                                    <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td data-label="Actions" class="text-end">
                                                        <a href="{{ route('admin-permissions.show', $permission->id) }}" class="od-icon-btn">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-clock fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Permission Requests Found</h5>
                                    <p class="text-muted">No permission requests match your current filters.</p>
                                    @if(request()->hasAny(['employee_name', 'request_type', 'leave_status', 'from_date', 'to_date']))
                                    <a href="{{ route('admin-leaves.index') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa fa-undo me-1"></i> Clear Filters
                                    </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Auto-switch tabs based on request_type filter
        function switchTabBasedOnFilter() {
            const requestType = $('#request_type').val();
            if (requestType === 'leave') {
                // Switch to leaves tab
                $('#leaves-tab').tab('show');
            } else if (requestType === 'permission') {
                // Switch to permissions tab
                $('#permissions-tab').tab('show');
            }
            // If empty or "All Requests", keep current tab
        }

        // Switch tab when request_type changes
        $('#request_type').change(function() {
            switchTabBasedOnFilter();
        });

        // Switch tab when form is submitted
        $('#filterForm').on('submit', function() {
            const requestType = $('#request_type').val();
            if (requestType === 'leave') {
                // Ensure leaves tab is active after form submission
                setTimeout(() => {
                    $('#leaves-tab').tab('show');
                }, 100);
            } else if (requestType === 'permission') {
                // Ensure permissions tab is active after form submission
                setTimeout(() => {
                    $('#permissions-tab').tab('show');
                }, 100);
            }
        });

        // Leave status change handler
        $('.status-change').click(function(e) {
            e.preventDefault();
            
            const leaveId = $(this).data('id');
            const status = $(this).data('status');
            const type = $(this).data('type');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to change the status to ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateLeaveStatus(leaveId, status);
                }
            });
        });

        // Permission status change handler
        $('.permission-status-change').click(function(e) {
            e.preventDefault();
            
            const permissionId = $(this).data('id');
            const status = $(this).data('status');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to change the permission status to ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updatePermissionStatus(permissionId, status);
                }
            });
        });

        // Initialize tab based on current filter on page load
        switchTabBasedOnFilter();
    });

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

    function updatePermissionStatus(permissionId, status) {
        $.ajax({
            url: "{{ url('admin-permissions') }}/" + permissionId + "/update-status",
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
    </script>

    <style>
    /* Compact Filter Styling */
    .card-body.py-3 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
    
    .form-control-sm, .form-select-sm {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        height: calc(1.5em + 0.5rem + 2px);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-group.mb-0 {
        margin-bottom: 0 !important;
    }
    
    .row.g-2 {
        --bs-gutter-x: 0.5rem;
        --bs-gutter-y: 0.5rem;
    }

    /* Leaves Table Container */
    .leaves-table-container {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Table Base */
    .leaves-table-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .leaves-table-container thead {
        background: orange !important;
        color: #fff;
        text-transform: uppercase;
        font-weight: 600;
    }

    .leaves-table-container thead th {
        padding: 12px 15px;
        border-bottom: 2px solid #f0f2f5;
        text-align: left;
    }

    /* Row Hover */
    .leaves-table-container tbody tr {
        transition: all 0.25s ease;
    }
    .leaves-table-container td:nth-child(2){
        color: rgb(4, 95, 231);
    }

    .leaves-table-container td:nth-child(5){
        color: rgb(21, 206, 40);
    }
    .leaves-table-container td:nth-child(6){
        color: rgb(236, 130, 17);
    }

    /* Table Cells */
    .leaves-table-container tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f2f5;
        vertical-align: middle;
    }

    /* Avatar + Name */
    .leaves-table-container .table-avatar {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .leaves-table-container .table-avatar .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .leaves-table-container .table-avatar a {
        text-decoration: none;
        color: #111827;
        font-weight: 600;
    }

    .leaves-table-container .table-avatar span {
        display: block;
        font-size: 12px;
        color: #6b7280;
    }

    /* Status Capsule */
    .leaves-table-container .btn.btn-white {
        border-radius: 999px;
        padding: 5px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    /* Status Colors */
    .text-success {
        color: #028174 !important;
    }

    .text-warning {
        color: #f59e0b !important;
    }

    .text-danger {
        color: #e11d48 !important;
    }

    /* Dropdown Items */
    .leaves-table-container .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.1);
    }

    .leaves-table-container .dropdown-item {
        font-size: 14px;
        padding: 8px 14px;
        color: #374151;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .leaves-table-container .dropdown-item:hover {
        background: #ebebeb;
        color: #0e0e0e;
    }

    /* View Button */
    .leaves-table-container .btn-primary {
        background: orange;
        border-color: orange;
        font-size: 13px;
        padding: 4px 10px;
    }

    .leaves-table-container .btn-primary:hover {
        background: #ff7a00cc;
    }

    /* Empty State */
    .leaves-table-container .table td[colspan] {
        text-align: center;
        color: #9ca3af;
        font-size: 15px;
        padding: 25px 0;
    }

    /* Tab Styling */
    .nav-tabs.card-header-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    .nav-tabs.card-header-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .nav-tabs.card-header-tabs .nav-link:hover {
        border-color: transparent;
        color: #495057;
        background-color: rgba(0,0,0,0.03);
    }
    .nav-tabs.card-header-tabs .nav-link.active {
        color: #ff6f00;
        background-color: transparent;
        border-color: #ff6f00;
        font-weight: 600;
    }
    .tab-content {
        padding-top: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .leaves-table-container table td, 
        .leaves-table-container table th {
            padding: 8px 10px;
            font-size: 13px;
        }
        
        /* Stack filter on mobile */
        .row.g-2 .col-md-2 {
            margin-bottom: 0.5rem;
        }
    }
    </style>
@endsection