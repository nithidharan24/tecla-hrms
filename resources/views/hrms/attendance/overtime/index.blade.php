@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Overtime Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Overtime</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-success" onclick="bulkApprove()">
                        <i class="fa-solid fa-check"></i> Bulk Approve
                    </button>
                    <button class="btn btn-primary" onclick="exportOvertime()">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #2196F3;"> <!-- Blue -->
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summaryStats['total_records'] }}</h4>
                <p class="mb-0 fw-semibold">Total Records</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #FF9800;"> <!-- Orange -->
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summaryStats['pending_records'] }}</h4>
                <p class="mb-0 fw-semibold">Pending</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #4CAF50;"> <!-- Green -->
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summaryStats['approved_records'] }}</h4>
                <p class="mb-0 fw-semibold">Approved</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #F44336;"> <!-- Red -->
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summaryStats['rejected_records'] }}</h4>
                <p class="mb-0 fw-semibold">Rejected</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #00BCD4;"> <!-- Cyan -->
            <div class="card-body text-center">
                <h4 class="mb-0">{{ number_format($summaryStats['total_overtime_hours'], 1) }}</h4>
                <p class="mb-0 fw-semibold">Total Hours</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card" style="background-color: #fff; color: #000; border-left: 5px solid #9C27B0;"> <!-- Purple -->
            <div class="card-body text-center">
                <h4 class="mb-0">₹{{ number_format($summaryStats['total_overtime_amount'], 0) }}</h4>
                <p class="mb-0 fw-semibold">Total Amount</p>
            </div>
        </div>
    </div>
</div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('overtime.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" id="month" name="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $selectedStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $selectedStatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $selectedEmployee == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->firstname }} {{ $employee->lastname }} ({{ $employee->employeeid }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overtime Records Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $monthName }} {{ $year }} - Overtime Records</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                           
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Overtime Hours</th>
                            <th>Rate (₹/hr)</th>
                            <th>Amount (₹)</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            
                           @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimeRecords as $record)
                            <tr data-id="{{ $record->id }}">
                               
                                <td data-label="Employee">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $record->firstname }} {{ $record->lastname }}</h6>
                                            <small class="text-muted">{{ $record->employeeid }}</small>
                                            @if($record->designation)
                                                <br><small class="badge bg-secondary">{{ $record->designation }}</small>
                                            @endif
                                            @if($record->department)
                                                <br><small class="badge bg-info">{{ $record->department }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td data-label="Overtime Date">
                                    <span class="fw-bold">{{ date('d M Y', strtotime($record->overtime_date)) }}</span>
                                    <br><small class="text-muted">{{ date('l', strtotime($record->overtime_date)) }}</small>
                                </td>
                                
                                <td data-label="Hours">
                                    <span class="badge bg-primary-light text-primary">
                                        {{ number_format($record->overtime_hours, 2) }} hrs
                                    </span>
                                </td>
                                
                                <td data-label="Rate">
                                    ₹{{ number_format($record->overtime_rate, 2) }}
                                </td>
                                
                                <td data-label="Amount" class="fw-bold">
                                    ₹{{ number_format($record->overtime_amount, 2) }}
                                </td>
                                
                                <td data-label="Status">
                                    @if($record->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($record->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                
                                <td data-label="Approved By">
                                    @if(!empty($record->approver_firstname) || !empty($record->approver_lastname))
                                        <div>
                                            <small class="fw-bold">{{ $record->approver_firstname }} {{ $record->approver_lastname }}</small>
                                            <br><small class="text-muted">{{ date('d M Y', strtotime($record->approved_at)) }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                
                                <td data-label="Actions">
                                    <div class="btn-group">
                                        @if($record->status == 'pending')
                                            <!-- Approve -->
                                            <form action="{{ url('overtime/'.$record->id.'/approve') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                
                                            <!-- Reject -->
                                            <form action="{{ url('overtime/'.$record->id.'/reject') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="remarks" value="Rejected by admin">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">No overtime records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    {{-- Custom Details Drawer --}}
    <div id="customDetailsDrawer" class="custom-modal-overlay" style="display:none;">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <h5>Overtime Details</h5>
                <button onclick="closeDrawer('customDetailsDrawer')">×</button>
            </div>
            <div id="overtimeDetailsContent" class="custom-modal-body">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>

    {{-- Custom Approval Modal --}}
    <div id="customApprovalDialog" class="custom-modal-overlay" style="display:none;">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <h5 id="approvalModalTitle">Approve Overtime</h5>
                <button onclick="closeDrawer('customApprovalDialog')">×</button>
            </div>
            <div class="custom-modal-body">
                <form id="approvalForm">
                    <input type="hidden" id="overtimeId" name="overtime_id">
                    <input type="hidden" id="approvalAction" name="action">
                    <div class="mb-3">
                        <label for="approvalRemarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="approvalRemarks" name="remarks" rows="3"
                            placeholder="Enter remarks (optional for approval, required for rejection)"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-2" onclick="confirmApproval()">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Custom Modal Styles --}}
<style>
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1050;
    display: flex;
    justify-content: center;
    align-items: center;
}

.custom-modal {
    background: #fff;
    width: 500px;
    border-radius: 8px;
    overflow: hidden;
    animation: fadeIn 0.3s ease;
}

.custom-modal-header {
    padding: 15px;
    background-color: #343a40;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-modal-header button {
    background: transparent;
    border: none;
    color: white;
    font-size: 24px;
    line-height: 1;
}

.custom-modal-body {
    padding: 20px;
}

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}
</style>

{{-- JS Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    
$(document).ready(function() {
    toastr.options = {
        "positionClass": "toast-top-right",
        "timeOut": 3000
    };

    $('#selectAll').change(function() {
        $('.overtime-checkbox:not(:disabled)').prop('checked', this.checked);
    });

    $('.overtime-checkbox').change(function() {
        const totalCheckboxes = $('.overtime-checkbox:not(:disabled)').length;
        const checkedCheckboxes = $('.overtime-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
});

function openDrawer(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeDrawer(id) {
    document.getElementById(id).style.display = 'none';
}

function showOvertimeDetails(overtimeId) {
    $.get(`/overtime/details/${overtimeId}`, function(data) {
        $('#overtimeDetailsContent').html(data);
        openDrawer('customDetailsDrawer');
    }).fail(function() {
        toastr.error('Failed to load overtime details');
    });
}

function approveOvertime(overtimeId) {
    $.ajax({
        url: `/overtime/${overtimeId}/approve`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                updateRowStatus(overtimeId, 'approve');
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Failed to approve overtime record');
        }
    });
}


function confirmApproval() {
    const overtimeId = $('#overtimeId').val();
    const action = $('#approvalAction').val();
    const remarks = $('#approvalRemarks').val();

    if (action === 'reject' && !remarks.trim()) {
        toastr.error('Remarks are required for rejection');
        return;
    }

    $.ajax({
        url: `/overtime/${overtimeId}/${action}`,
        type: 'POST',
        data: {
            remarks: remarks,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                updateRowStatus(overtimeId, action, remarks);
                $('#approvalRemarks').val('');
                closeDrawer('customApprovalDialog');
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Failed to process request');
        }
    });
}

function updateRowStatus(overtimeId, action, remarks) {
    const row = $(`tr[data-id="${overtimeId}"]`);
    row.find('td:nth-child(7) span.badge')
        .removeClass('bg-warning bg-success bg-danger')
        .addClass(action === 'approve' ? 'bg-success' : 'bg-danger')
        .text(action === 'approve' ? 'Approved' : 'Rejected');

    row.find('.overtime-checkbox').prop('disabled', true);
    row.find('.btn-outline-success, .btn-outline-danger').prop('disabled', true);

    const currentDate = new Date().toLocaleDateString('en-US', {
        day: 'numeric', month: 'short', year: 'numeric'
    });

    row.find('td:nth-child(8)').html(`
        <div>
            <br><small class="text-muted">${currentDate}</small>
            ${remarks ? '<br><small class="text-muted">' + remarks + '</small>' : ''}
        </div>
    `);
}

function bulkApprove() {
    const selectedIds = $('.overtime-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        toastr.warning('Please select overtime records to approve');
        return;
    }

    if (confirm(`Are you sure you want to approve ${selectedIds.length} overtime records?`)) {
        $.post('/overtime/bulk-approve', {
            overtime_ids: selectedIds,
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        }).fail(function() {
            toastr.error('Failed to bulk approve overtime records');
        });
    }
}

function exportOvertime() {
    const month = $('#month').val();
    const year = $('#year').val();
    const status = $('#status').val();
    const employeeId = $('#employee_id').val();

    const params = new URLSearchParams({
        month: month,
        year: year,
        status: status,
        employee_id: employeeId,
        format: 'excel'
    });

    window.open(`/overtime/export?${params.toString()}`, '_blank');
}
</script>
@endsection
