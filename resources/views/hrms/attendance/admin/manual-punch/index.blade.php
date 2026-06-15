@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Late Punch Approval');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Missed Punch Approvals</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Missed Punch Approvals</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a href="{{ route('admin.manual-punch.export') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <p class="mb-0">Total Requests</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-file-lines fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                            <p class="mb-0">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                            <p class="mb-0">Approved</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['rejected'] }}</h4>
                            <p class="mb-0">Rejected</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Requests</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('admin.manual-punch.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Employee</label>
                        <select class="form-select" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->firstname }} {{ $employee->lastname }} ({{ $employee->employeeid }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            <option value="punch_in" {{ request('type') == 'punch_in' ? 'selected' : '' }}>Punch In</option>
                            <option value="punch_out" {{ request('type') == 'punch_out' ? 'selected' : '' }}>Punch Out</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex align-items-end h-100">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('admin.manual-punch.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Manual Punch Requests</h5>
                @if(isset($permissions) && $permissions->can_approve)
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                        <label class="form-check-label" for="selectAllCheckbox" id="selectAllLabel">
                            Select All (<span id="selectedCountLabel">0</span>)
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="showBulkActionModal()" id="bulkActionBtn" disabled>
                        <i class="fa-solid fa-gears me-1"></i> Bulk Action
                    </button>
                </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            @if(isset($permissions) && $permissions->can_approve)
                            <th style="width: 30px;">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckboxHeader">
                            </th>
                            @endif
                            <th>#</th>
                            <th>Employee</th>
                            <th>Request Date</th>
                            <th>Type</th>
                            <th>Requested Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr class="{{ $request->status == 'pending' ? 'table-warning' : ($request->status == 'approved' ? 'table-success' : 'table-danger') }}"
                                id="row-{{ $request->id }}">
                                @if(isset($permissions) && $permissions->can_approve)
                                <td>
                                    @if($request->status == 'pending')
                                        <input type="checkbox" class="form-check-input request-checkbox" 
                                               value="{{ $request->id }}" id="checkbox-{{ $request->id }}">
                                    @endif
                                </td>
                                @endif
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial rounded-circle bg-primary text-white">
                                                {{ substr($request->firstname, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $request->firstname }} {{ $request->lastname }}</h6>
                                            <small class="text-muted">{{ $request->employeeid ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ date('d M Y', strtotime($request->request_date)) }}</span>
                                    <br><small class="text-muted">{{ date('l', strtotime($request->request_date)) }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $request->request_type == 'punch_in' ? 'bg-primary' : 'bg-info' }}">
                                        {{ ucwords(str_replace('_', ' ', $request->request_type)) }}
                                    </span>
                                </td>
                                <td>{{ date('h:i A', strtotime($request->request_time)) }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                          data-bs-toggle="tooltip" title="{{ $request->reason }}">
                                        {{ Str::limit($request->reason, 50) }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fa-solid fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-check me-1"></i>Approved
                                            @if($request->approved_by && $request->approver_name)
                                                <br><small>By: {{ $request->approver_name }}</small>
                                            @endif
                                        </span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="fa-solid fa-times me-1"></i>Rejected
                                            @if($request->approved_by && $request->approver_name)
                                                <br><small>By: {{ $request->approver_name }}</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fa-solid fa-question me-1"></i>{{ ucfirst($request->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ date('d M Y, h:i A', strtotime($request->created_at)) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.manual-punch.show', $request->id) }}" 
                                           class="btn btn-outline-info" data-bs-toggle="tooltip" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if($request->status == 'pending' && isset($permissions) && $permissions->can_approve)
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="approveRequest({{ $request->id }})" 
                                                    data-bs-toggle="tooltip" title="Approve">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="rejectRequest({{ $request->id }})" 
                                                    data-bs-toggle="tooltip" title="Reject">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (isset($permissions) && $permissions->can_approve) ? 10 : 9 }}" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-file-lines fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">No manual punch requests found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Improved Pagination Section with Filter Preservation -->
            @if($requests->hasPages())
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-3 mb-md-0">
                        <p class="text-muted mb-0">
                            Showing <span class="fw-semibold">{{ $requests->firstItem() }}</span>
                            to <span class="fw-semibold">{{ $requests->lastItem() }}</span>
                            of <span class="fw-semibold">{{ $requests->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-rounded mb-0">
                                    {{-- Previous Page Link --}}
                                    @if($requests->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $requests->appends(request()->query())->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $currentPage = $requests->currentPage();
                                        $lastPage = $requests->lastPage();
                                        $start = max(1, $currentPage - 2);
                                        $end = min($lastPage, $currentPage + 2);
                                    @endphp

                                    {{-- First page link --}}
                                    @if($start > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $requests->appends(request()->query())->url(1) }}">1</a>
                                        </li>
                                        @if($start > 2)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    {{-- Page links --}}
                                    @for($page = $start; $page <= $end; $page++)
                                        @if($page == $currentPage)
                                            <li class="page-item active" aria-current="page">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $requests->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    {{-- Last page link --}}
                                    @if($end < $lastPage)
                                        @if($end < $lastPage - 1)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $requests->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if($requests->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $requests->appends(request()->query())->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($requests->total() > 0)
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0">
                        Showing all <span class="fw-semibold">{{ $requests->total() }}</span> results
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this request?</p>
                    <div class="mb-3">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea class="form-control" name="remarks" rows="3" 
                                  placeholder="Add any remarks or notes..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        This will update the employee's attendance record automatically.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this request?</p>
                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remarks" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkActionForm" action="{{ route('admin.manual-punch.bulk-process') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action <span class="text-danger">*</span></label>
                        <select class="form-select" name="action" id="bulkActionSelect" required>
                            <option value="">Select Action</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="bulkRemarks" rows="3" 
                                  placeholder="Add remarks for all selected requests..."></textarea>
                        <small class="text-muted">Required when rejecting requests</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <span id="selectedCount">0</span> pending request(s) selected
                    </div>
                    <input type="hidden" name="ids" id="selectedIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="processBulkBtn">Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .avatar-initial { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        width: 32px; 
        height: 32px; 
        font-size: 0.875rem; 
    }
    .table-warning { background-color: rgba(255, 193, 7, 0.05); }
    .table-success { background-color: rgba(25, 135, 84, 0.05); }
    .table-danger { background-color: rgba(220, 53, 69, 0.05); }
    .empty-state { 
        padding: 3rem 1rem; 
        text-align: center; 
    }
    .empty-state i { 
        opacity: 0.5; 
    }
    .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    .selected-row {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    .bg-info {
        background-color: #0dcaf0 !important;
    }
    .text-dark {
        color: #212529 !important;
    }
    
    /* Pagination Styles */
    .pagination {
        margin: 0;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .pagination .page-item .page-link {
        color: #6c757d;
        border: 1px solid #dee2e6;
        border-radius: 8px !important;
        min-width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border-color: #4e73df;
        color: #fff;
        box-shadow: 0 2px 5px rgba(78, 115, 223, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #adb5bd;
        pointer-events: none;
    }
    
    .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border-color: #dee2e6;
        color: #4e73df;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .pagination .page-item .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        font-size: 0.875rem;
        border-radius: 8px !important;
    }
    
    /* Card footer pagination container */
    .card-footer .pagination {
        margin: 0;
        justify-content: flex-end;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-footer .d-flex {
            flex-direction: column;
            gap: 15px;
        }
        
        .pagination {
            justify-content: center;
        }
        
        .pagination .page-item .page-link {
            min-width: 34px;
            height: 34px;
            font-size: 0.8125rem;
        }
    }
    
    /* Pagination rounded style */
    .pagination-rounded .page-link {
        border-radius: 50px !important;
        margin: 0 2px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Update selected count label
    updateSelectedCountLabel();

    // Select all checkboxes in header
    $('#selectAllCheckboxHeader').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.request-checkbox').prop('checked', isChecked);
        $('.request-checkbox').trigger('change');
        updateSelectedCountLabel();
        updateBulkActionButton();
    });

    // Select all checkboxes in label
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.request-checkbox').prop('checked', isChecked);
        $('#selectAllCheckboxHeader').prop('checked', isChecked);
        $('.request-checkbox').trigger('change');
        updateSelectedCountLabel();
        updateBulkActionButton();
    });

    // Update row selection style and bulk action button
    $('.request-checkbox').on('change', function() {
        const rowId = $(this).val();
        const row = $('#row-' + rowId);
        
        if ($(this).prop('checked')) {
            row.addClass('selected-row');
        } else {
            row.removeClass('selected-row');
        }
        
        // Update header checkbox state
        const total = $('.request-checkbox').length;
        const checked = $('.request-checkbox:checked').length;
        $('#selectAllCheckboxHeader').prop('checked', total === checked && total > 0);
        $('#selectAllCheckbox').prop('checked', total === checked && total > 0);
        
        updateSelectedCountLabel();
        updateBulkActionButton();
    });

    // Update bulk action button based on selection
    $('#bulkActionSelect').on('change', function() {
        const action = $(this).val();
        const count = parseInt($('#selectedCount').text()) || 0;
        const btn = $('#processBulkBtn');
        
        if (action === 'approve') {
            btn.html(`<i class="fa-solid fa-check me-1"></i> Approve ${count} Request(s)`);
            btn.removeClass('btn-danger').addClass('btn-success');
        } else if (action === 'reject') {
            btn.html(`<i class="fa-solid fa-times me-1"></i> Reject ${count} Request(s)`);
            btn.removeClass('btn-success').addClass('btn-danger');
        } else {
            btn.text('Process');
            btn.removeClass('btn-success btn-danger').addClass('btn-primary');
        }
    });

    // Initial check for bulk action button
    updateBulkActionButton();
});

function updateSelectedCountLabel() {
    const count = $('.request-checkbox:checked').length;
    $('#selectedCountLabel').text(count);
    $('#selectedCount').text(count);
}

function updateBulkActionButton() {
    const count = $('.request-checkbox:checked').length;
    if (count > 0) {
        $('#bulkActionBtn').prop('disabled', false);
    } else {
        $('#bulkActionBtn').prop('disabled', true);
    }
}

function approveRequest(id) {
    $('#approveForm').attr('action', '{{ url("admin/manual-punch") }}/' + id + '/approve');
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectRequest(id) {
    $('#rejectForm').attr('action', '{{ url("admin/manual-punch") }}/' + id + '/reject');
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function showBulkActionModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            title: 'No Selection',
            text: 'Please select at least one pending request.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    $('#selectedCount').text(selectedIds.length);
    $('#selectedIds').val(selectedIds.join(','));
    
    // Reset modal state
    $('#bulkActionSelect').val('').trigger('change');
    $('#bulkRemarks').val('');
    
    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    modal.show();
}

function getSelectedIds() {
    const ids = [];
    $('.request-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    return ids;
}

// Form validation for bulk action
$('#bulkActionForm').on('submit', function(e) {
    e.preventDefault();
    
    const action = $('#bulkActionSelect').val();
    const ids = $('#selectedIds').val();
    const remarks = $('#bulkRemarks').val().trim();
    const selectedCount = ids.split(',').length;

    if (!action) {
        Swal.fire({
            title: 'Action Required',
            text: 'Please select an action (Approve or Reject).',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    if (!ids) {
        Swal.fire({
            title: 'No Selection',
            text: 'Please select at least one request.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    if (action === 'reject' && !remarks) {
        Swal.fire({
            title: 'Remarks Required',
            text: 'Please provide a reason for rejection.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Show confirmation dialog
    Swal.fire({
        title: `Confirm ${action === 'approve' ? 'Approval' : 'Rejection'}`,
        html: `<p>Are you sure you want to <strong>${action}</strong> ${selectedCount} request(s)?</p>
               ${remarks ? `<p><strong>Remarks:</strong> ${remarks}</p>` : ''}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, ${action} them`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: action === 'approve' ? '#198754' : '#dc3545',
        width: 500
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            $('#processBulkBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
            
            // Submit the form
            $('#bulkActionForm')[0].submit();
        }
    });
});

// Handle form validation for reject modal
$('#rejectForm').on('submit', function(e) {
    const remarks = $(this).find('textarea[name="remarks"]').val().trim();
    if (!remarks) {
        e.preventDefault();
        Swal.fire({
            title: 'Remarks Required',
            text: 'Please provide a reason for rejection.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }
});

// Handle response messages
@if(session('success') || session('error') || session('bulk_success') || session('bulk_error'))
    $(document).ready(function() {
        @if(session('bulk_success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session("bulk_success") }}',
                icon: 'success',
                confirmButtonText: 'OK',
                timer: 3000
            }).then(() => {
                window.location.reload();
            });
        @endif

        @if(session('bulk_error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session("bulk_error") }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonText: 'OK',
                timer: 3000
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
@endif

// Prevent closing modal on escape when form is submitting
$('#bulkActionModal').on('shown.bs.modal', function () {
    $(this).attr('data-keyboard', 'false');
});

$('#bulkActionModal').on('hidden.bs.modal', function () {
    $(this).attr('data-keyboard', 'true');
    $('#processBulkBtn').prop('disabled', false).text('Process');
});
</script>
@endsection