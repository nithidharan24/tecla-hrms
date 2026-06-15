@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Shift Interchange Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('scheduling.index') }}">Scheduling</a></li>
                    <li class="breadcrumb-item active">Shift Interchange</li>
                </ul>
            </div>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        @if($userRole === 'admin')
                            All Shift Interchange Requests
                        @else
                            My Shift Interchange Requests
                        @endif
                    </h4>
                    @if($userRole === 'employee')
                        <a href="{{ route('scheduling.shift-interchange.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Request Interchange
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($interchangeRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Requester</th>
                                        <th>Target Employee</th>
                                        <th>Interchange Date</th>
                                        <th>Shifts</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Requested On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($interchangeRequests as $index => $request)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->requester_firstname }} {{ $request->requester_lastname }}</strong>
                                                    <br><small class="text-muted">{{ $request->requester_department }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->target_firstname }} {{ $request->target_lastname }}</strong>
                                                    <br><small class="text-muted">{{ $request->target_department }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($request->interchange_date)->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($request->interchange_date)->format('l') }}</small>
                                            </td>
                                            <td>
                                                <div class="mb-2">
                                                    <strong>From:</strong> {{ $request->requester_shift_name }}
                                                    <br><small>{{ \Carbon\Carbon::parse($request->requester_start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($request->requester_end_time)->format('h:i A') }}</small>
                                                </div>
                                                <div>
                                                    <strong>To:</strong> {{ $request->target_shift_name }}
                                                    <br><small>{{ \Carbon\Carbon::parse($request->target_start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($request->target_end_time)->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $request->reason }}">
                                                    {{ $request->reason }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($request->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">Cancelled</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                                            <td>
                                                @if($userRole === 'admin' && $request->status === 'pending')
                                                    <div class="btn-group" role="group">
                                                        {{-- <CHANGE> add named route URLs as data attributes for JS --}}
                                                        <button
                                                            class="btn btn-sm btn-success"
                                                            id="approveBtn{{ $request->id }}"
                                                            data-approve-url="{{ route('scheduling.shift-interchange.approve', ['id' => $request->id]) }}"
                                                            onclick="approveRequest({{ $request->id }})"
                                                        >
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button
                                                            class="btn btn-sm btn-danger"
                                                            id="rejectBtn{{ $request->id }}"
                                                            data-reject-url="{{ route('scheduling.shift-interchange.reject', ['id' => $request->id]) }}"
                                                            onclick="rejectRequest({{ $request->id }})"
                                                        >
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                @elseif($userRole === 'employee' && $request->status === 'pending' && $request->requester_id == Session::get('user_id'))
                                                    <button
                                                        class="btn btn-sm btn-outline-danger"
                                                        id="cancelBtn{{ $request->id }}"
                                                        data-cancel-url="{{ route('scheduling.shift-interchange.cancel', ['id' => $request->id]) }}"
                                                        onclick="cancelRequest({{ $request->id }})"
                                                    >
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                @else
                                                    <span class="text-muted">No actions available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5>No Interchange Requests Found</h5>
                            <p class="text-muted">
                                @if($userRole === 'employee')
                                    You haven't made any shift interchange requests yet.
                                @else
                                    No shift interchange requests have been submitted.
                                @endif
                            </p>
                            @if($userRole === 'employee')
                                <a href="{{ route('scheduling.shift-interchange.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create Your First Request
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Process Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adminActionForm">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Add any notes about this decision..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelActionBtn">Cancel</button>
                <button type="button" class="btn" id="confirmActionBtn">
                    <span id="actionBtnText">Confirm</span>
                    <span id="actionBtnSpinner" class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Safe CSRF fallback: use meta if present, otherwise inject from Blade
const CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '{{ csrf_token() }}';

let currentRequestId = null;
let currentAction = null;
let currentActionUrl = null;

// Set button to loading state or reset it
function setButtonProcessing(buttonId, isProcessing) {
    const btn = document.getElementById(buttonId);
    if (!btn) return;
    if (isProcessing) {
        btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
        btn.disabled = true;
    } else {
        if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
        btn.disabled = false;
    }
}

// Set modal button to loading state
function setModalButtonProcessing(isProcessing) {
    const confirmBtn = document.getElementById('confirmActionBtn');
    const cancelBtn = document.getElementById('cancelActionBtn');
    const spinner = document.getElementById('actionBtnSpinner');
    if (isProcessing) {
        spinner.classList.remove('d-none');
        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
    } else {
        spinner.classList.add('d-none');
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
    }
}

// Approve: read per-row named route URL
function approveRequest(requestId) {
    currentRequestId = requestId;
    currentAction = 'approve';
    const btn = document.getElementById(`approveBtn${requestId}`);
    currentActionUrl = btn ? btn.getAttribute('data-approve-url') : null;

    document.getElementById('modalTitle').textContent = 'Approve Interchange Request';
    document.getElementById('actionBtnText').textContent = 'Approve';
    document.getElementById('confirmActionBtn').className = 'btn btn-success';

    document.getElementById('admin_notes').value = '';
    setModalButtonProcessing(false);
    $('#adminActionModal').modal('show');
}

// Reject: read per-row named route URL
function rejectRequest(requestId) {
    currentRequestId = requestId;
    currentAction = 'reject';
    const btn = document.getElementById(`rejectBtn${requestId}`);
    currentActionUrl = btn ? btn.getAttribute('data-reject-url') : null;

    document.getElementById('modalTitle').textContent = 'Reject Interchange Request';
    document.getElementById('actionBtnText').textContent = 'Reject';
    document.getElementById('confirmActionBtn').className = 'btn btn-danger';

    document.getElementById('admin_notes').value = '';
    setModalButtonProcessing(false);
    $('#adminActionModal').modal('show');
}

// Cancel: uses per-row named route URL
function cancelRequest(requestId) {
    if (!confirm('Are you sure you want to cancel this interchange request?')) return;

    setButtonProcessing(`cancelBtn${requestId}`, true);

    const btn = document.getElementById(`cancelBtn${requestId}`);
    const url = btn ? btn.getAttribute('data-cancel-url') : null;

    if (!url) {
        console.error('Missing cancel URL for request', requestId);
        setButtonProcessing(`cancelBtn${requestId}`, false);
        showToast('error', 'Missing route URL');
        return;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            showToast('success', 'Request cancelled successfully');
            setTimeout(() => location.reload(), 1200);
        } else {
            setButtonProcessing(`cancelBtn${requestId}`, false);
            showToast('error', (data && (data.message || data.error)) || 'Failed to cancel request');
        }
    })
    .catch(err => {
        console.error(err);
        setButtonProcessing(`cancelBtn${requestId}`, false);
        showToast('error', 'An error occurred while cancelling the request');
    });
}

// Confirm action in modal (approve/reject)
document.getElementById('confirmActionBtn').addEventListener('click', function() {
    const adminNotes = document.getElementById('admin_notes').value;

    if (!currentActionUrl) {
        console.error('Missing approve/reject URL for request', currentRequestId);
        showToast('error', 'Missing route URL');
        return;
    }

    setModalButtonProcessing(true);

    fetch(currentActionUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ admin_notes: adminNotes })
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            setButtonProcessing(`${currentAction === 'approve' ? 'approveBtn' : 'rejectBtn'}${currentRequestId}`, true);
            showToast('success', `Request ${currentAction}d successfully`);
            $('#adminActionModal').modal('hide');
            setTimeout(() => location.reload(), 1200);
        } else {
            setModalButtonProcessing(false);
            showToast('error', (data && (data.message || data.error)) || `Failed to ${currentAction} request`);
        }
    })
    .catch(err => {
        console.error(err);
        setModalButtonProcessing(false);
        showToast('error', `An error occurred while ${currentAction}ing the request`);
    });
});

// Toast helper
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Reset modal when closed
$('#adminActionModal').on('hidden.bs.modal', function () {
    document.getElementById('admin_notes').value = '';
    setModalButtonProcessing(false);
});
</script>
@endsection