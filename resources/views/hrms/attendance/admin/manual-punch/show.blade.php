@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Missed Punch Approvals Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.manual-punch.index') }}">Missed Punch Approvals</a></li>
                    <li class="breadcrumb-item active">Request #{{ $request->id }}</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                @if($request->status == 'pending')
                <div class="btn-group">
                    <form action="{{ route('admin.manual-punch.approve', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this request?')">
                            <i class="fa-solid fa-check"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fa-solid fa-times"></i> Reject
                    </button>
                </div>
                @endif
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Request Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Request ID</label>
                                <p class="form-control-static fw-bold">#{{ $request->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <p>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fa-solid fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-check me-1"></i>Approved
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fa-solid fa-times me-1"></i>Rejected
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Request Date</label>
                                <p class="form-control-static fw-bold">{{ date('d M Y', strtotime($request->request_date)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Request Type</label>
                                <p>
                                    <span class="badge {{ $request->request_type == 'punch_in' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucwords(str_replace('_', ' ', $request->request_type)) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Requested Time</label>
                                <p class="form-control-static fw-bold">{{ date('h:i A', strtotime($request->request_time)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Created At</label>
                                <p class="form-control-static">{{ date('d M Y, h:i A', strtotime($request->created_at)) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $request->reason }}
                            </div>
                        </div>
                    </div>

                    @if($request->admin_remarks)
                    <div class="mb-3">
                        <label class="form-label">Admin Remarks</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $request->admin_remarks }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Employee Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-primary text-white fs-3">
                                {{ substr($request->firstname, 0, 1) }}
                            </span>
                        </div>
                        <h5>{{ $request->firstname }} {{ $request->lastname }}</h5>
                        <p class="text-muted mb-0">ID: {{ $request->employeeid }}</p>
                    </div>

                    <div class="mb-2">
                        <i class="fa-solid fa-briefcase me-2 text-muted"></i>
                        <span class="text-muted">Department:</span>
                        <span class="float-end">{{ $request->department_name ?? 'N/A' }}</span>
                    </div>
        
                    <div class="mb-2">
                        <i class="fa-solid fa-user-tie me-2 text-muted"></i>
                        <span class="text-muted">Designation:</span>
                        <span class="float-end">{{ $request->designation_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            @if($attendance)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Attendance Record</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="fa-solid fa-clock me-2 text-muted"></i>
                        <span class="text-muted">Punch In:</span>
                        <span class="float-end">{{ $attendance->punch_in ? date('h:i A', strtotime($attendance->punch_in)) : 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-clock me-2 text-muted"></i>
                        <span class="text-muted">Punch Out:</span>
                        <span class="float-end">{{ $attendance->punch_out ? date('h:i A', strtotime($attendance->punch_out)) : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if($request->status != 'pending')
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Approval Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="fa-solid fa-user-check me-2 text-muted"></i>
                        <span class="text-muted">Approved By:</span>
                        <span class="float-end">{{ $request->approver_name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-calendar-check me-2 text-muted"></i>
                        <span class="text-muted">Approved At:</span>
                        <span class="float-end">{{ $request->approved_at ? date('d M Y, h:i A', strtotime($request->approved_at)) : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            @endif
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
            <form action="{{ route('admin.manual-punch.reject', $request->id) }}" method="POST">
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

<style>
    .avatar-lg .avatar-initial {
        width: 80px;
        height: 80px;
        font-size: 2rem;
        line-height: 80px;
    }
    .form-control-static {
        padding: 0.375rem 0;
        margin-bottom: 0;
        font-size: 1rem;
        line-height: 1.5;
        border-bottom: 1px solid #dee2e6;
    }
</style>

<script>
$(document).ready(function() {
    // Form validation for reject modal
    $('#rejectModal form').on('submit', function(e) {
        const remarks = $(this).find('textarea[name="remarks"]').val().trim();
        if (!remarks) {
            e.preventDefault();
            alert('Please provide a reason for rejection.');
            return false;
        }
    });
});
</script>
@endsection