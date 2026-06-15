@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Testing Ticket Details</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('testing.index') }}">Testing Tickets</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->testing_ticket_id }}</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="{{ route('testing.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket Overview Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <strong>Ticket ID:</strong> {{ $ticket->testing_ticket_id }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small">Priority</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $ticket->priority == 'High' ? 'danger' : ($ticket->priority == 'Medium' ? 'warning' : 'success') }}">
                                        <i class="fa fa-circle"></i> {{ $ticket->priority }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $ticket->status == 'Closed' ? 'success' : ($ticket->status == 'Open' ? 'primary' : ($ticket->status == 'Reopen' ? 'danger' : 'warning')) }}">
                                        <i class="fa fa-circle"></i> {{ $ticket->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small">Created By</label>
                                <p class="mb-0">
                                    @if($ticket->creator_name && $ticket->creator_name !== 'Unknown')
                                        @if($ticket->creator_image)
                                            <img src="{{ asset($ticket->creator_image) }}" class="rounded-circle me-1" width="25" height="25" onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                                        @endif
                                        <small>{{ $ticket->creator_name }}</small>
                                    @else
                                        <small class="text-muted">Unknown</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small">Assigned To</label>
                                <p class="mb-0">
                                    @if($ticket->assigned_name && $ticket->assigned_name !== 'Unassigned')
                                        @if($ticket->assigned_image)
                                            <img src="{{ asset($ticket->assigned_image) }}" class="rounded-circle me-1" width="25" height="25" onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                                        @endif
                                        <small class="badge bg-info">{{ $ticket->assigned_name }}</small>
                                    @else
                                        <small class="text-muted">Unassigned</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <small class="text-muted">Created At: {{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y H:i') }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Updated At: {{ \Carbon\Carbon::parse($ticket->updated_at)->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bugs Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa fa-bug"></i> Bug Details
                        <span class="badge bg-primary ms-2">{{ $bugs->count() }} bugs</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($bugs && $bugs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">S.No</th>
                                    <th style="width: 15%">Module Name</th>
                                    <th style="width: 25%">Bug Description</th>
                                    <th style="width: 20%">Steps to Reproduce</th>
                                    <th style="width: 12%">Attachment</th>
                                    <th style="width: 12%">Status</th>
                                    <th style="width: 11%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bugs as $key => $bug)
                                    <tr class="align-middle">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $bug->module_name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($bug->description, 50) }}</small>
                                            @if(strlen($bug->description) > 50)
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#descModal{{ $bug->id }}" class="text-primary ms-1">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($bug->steps_to_reproduce)
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#stepsModal{{ $bug->id }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i> View Steps
                                                </a>
                                            @else
                                                <small class="text-muted">N/A</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($bug->uploaded_files)
                                                <a href="{{ asset($bug->uploaded_files) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i> View File
                                                </a>
                                            @else
                                                <small class="text-muted">No file</small>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Status dropdown for each bug -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle status-btn-{{ $bug->id }}" type="button" data-bs-toggle="dropdown">
                                                    <i class="fa fa-circle text-{{ 
                                                        $bug->status === 'Closed' ? 'success' : 
                                                        ($bug->status === 'Open' ? 'primary' : 
                                                        ($bug->status === 'In Progress' ? 'warning' : 
                                                        ($bug->status === 'Resolved' ? 'info' : 'danger'))) 
                                                    }}"></i>
                                                    {{ $bug->status ?? 'Open' }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" onclick="updateBugStatus({{ $bug->id }}, 'Open'); return false;">Open</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBugStatus({{ $bug->id }}, 'In Progress'); return false;">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBugStatus({{ $bug->id }}, 'Resolved'); return false;">Resolved</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBugStatus({{ $bug->id }}, 'Closed'); return false;">Closed</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateBugStatus({{ $bug->id }}, 'Reopen'); return false;">Reopen</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Fixed: This should point to bug edit, not testing ticket edit -->
                                            <a href="{{ route('testing.bug.edit', $bug->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Bug">
                                                <i class="fa fa-pencil"></i> Edit
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Description Modal -->
                                    <div class="modal fade" id="descModal{{ $bug->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Bug Description - {{ $bug->module_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="border p-3 bg-light">
                                                        {{ $bug->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Steps Modal -->
                                    @if($bug->steps_to_reproduce)
                                    <div class="modal fade" id="stepsModal{{ $bug->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Steps to Reproduce - {{ $bug->module_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="border p-3 bg-light">
                                                        {!! nl2br(e($bug->steps_to_reproduce)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bugs found for this ticket.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Logs -->
            @if(isset($activityLogs) && $activityLogs->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa fa-history"></i> Activity Log
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activityLogs as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->performed_by_name ?? 'System' }} ({{ $log->performed_by_role ?? 'N/A' }})</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function updateBugStatus(bugId, status) {
    Swal.fire({
        title: 'Update Status',
        text: `Are you sure you want to change the status to "${status}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("testing.updateBugStatus", ":id") }}'.replace(':id', bugId),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        const statusBtn = $('.status-btn-' + bugId);
                        const statusColor = 
                            status === 'Closed' ? 'success' : 
                            status === 'Open' ? 'primary' : 
                            status === 'In Progress' ? 'warning' : 
                            status === 'Resolved' ? 'info' : 'danger';
                        
                        statusBtn.html(`<i class="fa fa-circle text-${statusColor}"></i> ${status}`);
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Bug status updated successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update bug status. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>

<style>
.modal-dialog {
    max-width: 600px;
}
.modal-lg {
    max-width: 800px;
}
.border {
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem;
}
</style>
@endsection