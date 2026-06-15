@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">
                        @if($role === 'admin')
                            All Timesheets
                        @else
                            My Timesheets
                        @endif
                    </h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                                <li class="breadcrumb-item active">Timesheets</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Timesheet List</h4>
                        @if($role === 'employee')
                            <a href="{{ route('timesheet.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Timesheet
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        @if($role === 'admin')
                                            <th>Employee</th>
                                        @endif
                                        <th>Week</th>
                                        <th>Project</th>
                                        <th>Total Hours</th>
                                        <th>Comments</th>
                                        <th>Status</th>
                                        <th>Submitted On</th>
                                        @if($role === 'admin')
                                            <th>Actions</th>
                                        @else
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($timesheets as $timesheet)
                                    <tr>
                                    @if($role === 'admin')
    <td>
        {{ $timesheet->employee_name ?? 'N/A' }}
        <br>
        <small class="text-muted">{{ $timesheet->employee_id_display ?? '' }}</small>
    </td>
@endif
                                        <td>
                                            {{ Carbon\Carbon::parse($timesheet->week_start)->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ Carbon\Carbon::parse($timesheet->week_start)->format('D') }} - 
                                                {{ Carbon\Carbon::parse($timesheet->week_end ?? $timesheet->week_start)->addDays(6)->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <strong>{{ $timesheet->project->projectname ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $timesheet->project->projectid ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info" style="font-size: 14px;">{{ $timesheet->hours }} hrs</span>
                                        </td>
                                        <td>{{ Str::limit($timesheet->comments, 30) }}</td>
                                        <td>
                                            @if($timesheet->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($timesheet->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($timesheet->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                                @if($timesheet->rejection_reason)
                                                    <br>
                                                    <small class="text-danger" title="{{ $timesheet->rejection_reason }}">View reason</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $timesheet->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($role === 'admin')
                                                @if($timesheet->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="approveTimesheet({{ $timesheet->id }})"
                                                            title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="showRejectModal({{ $timesheet->id }})"
                                                            title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            @else
                                                @if($timesheet->status == 'pending')
                                                    <a href="{{ route('timesheet.edit', $timesheet->id) }}" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('timesheet.destroy', $timesheet->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Delete this entry?')"
                                                            title="Delete"
                                                            {{ $role === 'admin' && $timesheet->status != 'pending' ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $role === 'admin' ? '8' : '7' }}" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                No timesheets found.
                                                @if($role === 'employee')
                                                    <br>
                                                    <a href="{{ route('timesheet.create') }}" class="btn btn-primary btn-sm mt-2">
                                                        Create Your First Timesheet
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($role === 'admin' && $timesheets->where('status', 'pending')->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Bulk Actions</h5>
                                        <p class="card-text">
                                            <span class="badge badge-warning">{{ $timesheets->where('status', 'pending')->count() }}</span> pending timesheets
                                        </p>
                                        <button type="button" class="btn btn-success" onclick="bulkApprove()">
                                            <i class="fas fa-check-double"></i> Approve All Pending
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mt-3">
                            {{ $timesheets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Timesheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" 
                                  rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Timesheet</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function approveTimesheet(id) {
    if (confirm('Are you sure you want to approve this timesheet?')) {
        // Show loading state on button
        const button = event.target;
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/timesheet/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
                button.innerHTML = originalHtml;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing request: ' + (error.message || 'Unknown error'));
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
    }
}

function showRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `/timesheet/${id}/reject`;
    $('#rejectModal').modal('show');
}

function bulkApprove() {
    if (confirm('Approve all pending timesheets?')) {
        const button = event.target;
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        button.disabled = true;
        
        const pendingIds = [];
        @foreach($timesheets as $timesheet)
            @if($timesheet->status == 'pending')
                pendingIds.push({{ $timesheet->id }});
            @endif
        @endforeach
        
        if (pendingIds.length === 0) {
            alert('No pending timesheets to approve.');
            button.innerHTML = originalHtml;
            button.disabled = false;
            return;
        }
        
        fetch('{{ route("timesheet.bulk-approve") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ timesheet_ids: pendingIds })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.error || data.message || 'Unknown error'));
                button.innerHTML = originalHtml;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing request: ' + (error.message || 'Unknown error'));
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
    }
}

// Handle reject form submission
$(document).ready(function() {
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const reason = $('#rejection_reason').val();
        
        if (!reason) {
            alert('Please provide a rejection reason');
            return;
        }
        
        // Show loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                rejection_reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Error rejecting timesheet');
                    submitBtn.text(originalText);
                    submitBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to reject timesheet';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMsg);
                submitBtn.text(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>


@endsection