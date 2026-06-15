@extends('layouts.admin')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Manual Attendance Requests</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Manual Requests</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="exportRequests()">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
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

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.attendance.manual-requests') }}" method="GET" id="requestsFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-warning w-100 text-white">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Requested Times</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial rounded-circle bg-primary text-white">
                                                {{ substr($request->firstname, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $request->firstname }} {{ $request->lastname }}</h6>
                                            <small class="text-muted">{{ $request->employeeid }}</small>
                                            <br>
                                            <small class="badge bg-secondary">{{ $request->department }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($request->date)->format('l') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->type == 'late_entry' ? 'danger' : ($request->type == 'correction' ? 'warning' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                    </span>
                                    @if($request->shift_name)
                                        <br><small class="text-muted">{{ $request->shift_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($request->requested_punch_in)
                                        <div class="mb-1">
                                            <span class="badge bg-primary">In: {{ \Carbon\Carbon::parse($request->requested_punch_in)->format('h:i A') }}</span>
                                            @if($request->scheduled_start)
                                                <br><small class="text-muted">Scheduled: {{ \Carbon\Carbon::parse($request->scheduled_start)->format('h:i A') }}</small>
                                            @endif
                                        </div>
                                    @endif
                                    @if($request->requested_punch_out)
                                        <div>
                                            <span class="badge bg-danger">Out: {{ \Carbon\Carbon::parse($request->requested_punch_out)->format('h:i A') }}</span>
                                            @if($request->scheduled_end)
                                                <br><small class="text-muted">Scheduled: {{ \Carbon\Carbon::parse($request->scheduled_end)->format('h:i A') }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" 
                                         data-bs-toggle="tooltip" title="{{ $request->reason }}">
                                        {{ $request->reason }}
                                    </div>
                                    @if($request->proof_file)
                                        <br>
                                        <a href="{{ Storage::url($request->proof_file) }}" target="_blank" class="small">
                                            <i class="fa-solid fa-paperclip"></i> View Document
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}
                                        </small>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-check me-1"></i> Approved
                                        </span>
                                        @if($request->approved_at)
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($request->approved_at)->format('M d, h:i A') }}
                                            </small>
                                        @endif
                                    @elseif($request->status == 'processed')
                                        <span class="badge bg-info">
                                            <i class="fa-solid fa-sync me-1"></i> Processed
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fa-solid fa-times me-1"></i> Rejected
                                        </span>
                                        @if($request->rejection_reason)
                                            <br>
                                            <small class="text-muted">{{ $request->rejection_reason }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewRequestDetails({{ $request->id }})">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        
                                        @if($request->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="approveRequest({{ $request->id }})">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="rejectRequest({{ $request->id }})">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        @elseif($request->status == 'approved')
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="processRequest({{ $request->id }})">
                                                <i class="fa-solid fa-cogs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-inbox fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">No manual attendance requests found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Pagination -->
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
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
            <div class="modal-body">
                <form id="approveForm">
                    <input type="hidden" name="request_id" id="approve_request_id">
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes for the employee"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmApprove()">Approve</button>
            </div>
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
            <div class="modal-body">
                <form id="rejectForm">
                    <input type="hidden" name="request_id" id="reject_request_id">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Explain why this request is being rejected"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="processModalContent">
                <p>Are you sure you want to process this attendance request? This will create/update the attendance record.</p>
                <div class="alert alert-warning">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    This action cannot be undone. Please verify the details before processing.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmProcess()">Process Attendance</button>
            </div>
        </div>
    </div>
</div>

<style>
    .empty-state { opacity: 0.6; }
    .avatar-initial { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; font-size: 0.875rem; }
</style>

<script>
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    let currentRequestId = null;

    function viewRequestDetails(requestId) {
        $.get(`/admin/attendance/manual-request/${requestId}/details`, function(response) {
            if (response.success) {
                const request = response.data;
                const existingIn = request.existing_punch_in ? 
                    new Date(request.existing_punch_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A';
                const existingOut = request.existing_punch_out ? 
                    new Date(request.existing_punch_out).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A';
                
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Employee Information</h6>
                            <p class="mb-1"><strong>Name:</strong> ${request.firstname} ${request.lastname}</p>
                            <p class="mb-1"><strong>Employee ID:</strong> ${request.employeeid || 'N/A'}</p>
                            <p class="mb-1"><strong>Department:</strong> ${request.department || 'N/A'}</p>
                            <p class="mb-1"><strong>Designation:</strong> ${request.designation || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Schedule Information</h6>
                            <p class="mb-1"><strong>Shift:</strong> ${request.shift_name || 'N/A'}</p>
                            <p class="mb-1"><strong>Scheduled In:</strong> ${request.scheduled_start ? 
                                new Date('2000-01-01T' + request.scheduled_start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}</p>
                            <p class="mb-1"><strong>Scheduled Out:</strong> ${request.scheduled_end ? 
                                new Date('2000-01-01T' + request.scheduled_end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Request Details</h6>
                            <p class="mb-1"><strong>Date:</strong> ${new Date(request.date).toLocaleDateString()}</p>
                            <p class="mb-1"><strong>Type:</strong> ${request.type.replace(/_/g, ' ').toUpperCase()}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-${request.status === 'pending' ? 'warning' : 
                                    request.status === 'approved' ? 'success' : 
                                    request.status === 'processed' ? 'info' : 'danger'}">
                                    ${request.status.toUpperCase()}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Requested Times</h6>
                            <p class="mb-1"><strong>Punch In:</strong> ${request.requested_punch_in || 'N/A'}</p>
                            <p class="mb-1"><strong>Punch Out:</strong> ${request.requested_punch_out || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Existing Attendance</h6>
                            <p class="mb-1"><strong>Current Punch In:</strong> ${existingIn}</p>
                            <p class="mb-1"><strong>Current Punch Out:</strong> ${existingOut}</p>
                            <p class="mb-1"><strong>Working Hours:</strong> ${request.existing_working_hours || '0.00'} hrs</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Request Information</h6>
                            <p><strong>Reason:</strong></p>
                            <p class="text-muted">${request.reason || 'No reason provided'}</p>
                        </div>
                    </div>
                    
                    ${request.proof_file ? `
                        <div class="alert alert-info">
                            <i class="fa-solid fa-paperclip me-2"></i>
                            <strong>Supporting Document:</strong> 
                            <a href="/storage/${request.proof_file}" target="_blank" class="ms-2">View Document</a>
                        </div>
                    ` : ''}
                    
                    ${request.admin_notes ? `
                        <div class="alert alert-secondary">
                            <h6>Admin Notes:</h6>
                            <p class="mb-0">${request.admin_notes}</p>
                        </div>
                    ` : ''}
                    
                    ${request.rejection_reason ? `
                        <div class="alert alert-danger">
                            <h6>Rejection Reason:</h6>
                            <p class="mb-0">${request.rejection_reason}</p>
                        </div>
                    ` : ''}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted"><small><strong>Submitted:</strong> ${new Date(request.created_at).toLocaleString()}</small></p>
                        </div>
                        ${request.approved_at ? `
                            <div class="col-md-6">
                                <p class="mb-0 text-muted"><small><strong>Approved:</strong> ${new Date(request.approved_at).toLocaleString()}</small></p>
                            </div>
                        ` : ''}
                    </div>
                `;
                
                $('#requestDetailsContent').html(html);
                $('#requestDetailsModal').modal('show');
            }
        });
    }

    function approveRequest(requestId) {
        currentRequestId = requestId;
        $('#approve_request_id').val(requestId);
        $('#approveModal').modal('show');
    }

    function confirmApprove() {
        const formData = new FormData(document.getElementById('approveForm'));
        
        $.post(`/admin/attendance/manual-request/${currentRequestId}/approve`, {
            notes: formData.get('notes'),
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                $('#approveModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Request approved successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }).fail(function(xhr) {
            let errorMessage = 'Failed to approve request';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            Swal.fire('Error!', errorMessage, 'error');
        });
    }

    function rejectRequest(requestId) {
        currentRequestId = requestId;
        $('#reject_request_id').val(requestId);
        $('#rejectModal').modal('show');
    }

    function confirmReject() {
        const formData = new FormData(document.getElementById('rejectForm'));
        
        if (!formData.get('reason')) {
            Swal.fire('Validation Error', 'Please provide a reason for rejection', 'error');
            return;
        }
        
        $.post(`/admin/attendance/manual-request/${currentRequestId}/reject`, {
            reason: formData.get('reason'),
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                $('#rejectModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Request rejected successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }).fail(function(xhr) {
            let errorMessage = 'Failed to reject request';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            Swal.fire('Error!', errorMessage, 'error');
        });
    }

    function processRequest(requestId) {
        currentRequestId = requestId;
        $('#processModal').modal('show');
    }

    function confirmProcess() {
        $.post(`/admin/attendance/manual-request/${currentRequestId}/process`, {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                $('#processModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Attendance processed successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }).fail(function(xhr) {
            let errorMessage = 'Failed to process request';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            Swal.fire('Error!', errorMessage, 'error');
        });
    }

    function exportRequests() {
        const status = $('#status').val();
        const date = $('#date').val();
        const departmentId = $('#department_id').val();
        
        let url = `/admin/attendance/manual-requests/export?`;
        const params = new URLSearchParams();
        
        if (status) params.append('status', status);
        if (date) params.append('date', date);
        if (departmentId) params.append('department_id', departmentId);
        
        window.open(url + params.toString(), '_blank');
    }
</script>
@endsection