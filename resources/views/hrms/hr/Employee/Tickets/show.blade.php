@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Ticket Details</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}" class="breadcrumb-link">Ticket List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_id }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <!-- Ticket Header Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-0 text-white">
                                    <i class="fa fa-ticket"></i> {{ $ticket->ticket_id }}
                                </h4>
                                <p class="mb-0 text-white-50">{{ $ticket->ticket_subject }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-{{ $ticket->status === 'closed' ? 'success' : ($ticket->status === 'new' ? 'light' : 'warning') }} fs-6 me-2">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                                <span class="badge bg-{{ $ticket->priority === 'High' ? 'danger' : ($ticket->priority === 'Medium' ? 'warning' : 'success') }} fs-6">
                                    {{ $ticket->priority }} Priority
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                            
                            @php
                                $employeeId = Session::get('user_id');
                                $canEdit = false;
                                if ($role === 'admin') {
                                    $canEdit = true;
                                } elseif ($role === 'employee') {
                                    $canEdit = ($ticket->created_by == $employeeId || 
                                               $ticket->assign_1 == $employeeId || 
                                               $ticket->assign_2 == $employeeId || 
                                               $ticket->assign_3 == $employeeId);
                                }
                            @endphp
                            
                            @if($canEdit)
                            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary">
                                <i class="fa fa-edit"></i> Edit Ticket
                            </a>
                            @endif
                            
                            @if($role === 'admin')
                            <button class="btn btn-info" onclick="showAssignModal()">
                                <i class="fa fa-user-plus"></i> Manage Assignment
                            </button>
                            <button class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fa fa-trash"></i> Delete Ticket
                            </button>
                            @endif
                            
                          @if($ticket->uploaded_files)
<a href="{{ route('tickets.download', $ticket->id) }}" class="btn btn-success">
    <i class="fa fa-download"></i> Download Attachment
</a>
@endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Main Ticket Details -->
                    <div class="col-lg-8">
                        <!-- Ticket Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-info-circle text-primary"></i> Ticket Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Ticket ID:</label>
                                        <p class="text-muted">{{ $ticket->ticket_id }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Subject:</label>
                                        <p class="text-muted">{{ $ticket->ticket_subject }}</p>
                                    </div>
                                    @if($ticket->asset_id)
    @php
        $asset = DB::table('assets_company')
            ->where('id', $ticket->asset_id)
            ->first();
    @endphp
    
    @if($asset)
        <div class="col-md-12 mb-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-laptop"></i> Related Asset
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ $asset->asset_name }}</h5>
                            <p><strong>Asset ID:</strong> {{ $asset->asset_id }}</p>
                            <p><strong>Model:</strong> {{ $asset->model }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Serial Number:</strong> {{ $asset->serial_number }}</p>
                            <p><strong>Condition:</strong> {{ $asset->condition }}</p>
                        </div>
                    </div>
                    
                    @if($asset->asset_user == Session::get('user_id'))
                        <a href="{{ route('assets.edit', $asset->id) }}" 
                           class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fa fa-eye"></i> View Your Asset Details
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Priority:</label>
                                        <p>
                                            <span class="badge bg-{{ $ticket->priority === 'High' ? 'danger' : ($ticket->priority === 'Medium' ? 'warning' : 'success') }} fs-6">
                                                {{ $ticket->priority }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <p>
                                            @if($role === 'admin')
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-{{ $ticket->status === 'closed' ? 'success' : ($ticket->status === 'new' ? 'primary' : 'warning') }} dropdown-toggle" 
                                                        type="button" id="statusDropdown" data-bs-toggle="dropdown">
                                                    {{ ucfirst($ticket->status) }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('new')">New</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('open')">Open</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('in progress')">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('on hold')">On Hold</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('closed')">Closed</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('cancelled')">Cancelled</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('reopened')">Reopened</a></li>
                                                </ul>
                                            </div>
                                            @else
                                            <span class="badge bg-{{ $ticket->status === 'closed' ? 'success' : ($ticket->status === 'new' ? 'primary' : 'warning') }} fs-6">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Created Date:</label>
                                        <p class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Last Updated:</label>
                                        <p class="text-muted">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-file-text text-primary"></i> Description
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="description-content">
                                    {!! nl2br(e($ticket->description)) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Attachment -->
                        @if($ticket->uploaded_files)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-paperclip text-primary"></i> Attachment
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="attachment-item d-flex align-items-center p-3 border rounded">
                                    @php
                                        $fileExtension = pathinfo($ticket->uploaded_files, PATHINFO_EXTENSION);
                                        $fileName = basename($ticket->uploaded_files);
                                        $fileSize = file_exists(storage_path('app/public/' . $ticket->uploaded_files)) 
                                                   ? number_format(filesize(storage_path('app/public/' . $ticket->uploaded_files)) / 1024, 2) 
                                                   : 'Unknown';
                                    @endphp
                                    
                                    <div class="file-icon me-3">
                                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                            <i class="fa fa-image fa-2x text-success"></i>
                                        @elseif(strtolower($fileExtension) === 'pdf')
                                            <i class="fa fa-file-pdf fa-2x text-danger"></i>
                                        @elseif(in_array(strtolower($fileExtension), ['doc', 'docx']))
                                            <i class="fa fa-file-word fa-2x text-primary"></i>
                                        @else
                                            <i class="fa fa-file fa-2x text-muted"></i>
                                        @endif
                                    </div>
                                    
                                    <div class="file-info flex-grow-1">
                                        <h6 class="mb-1">{{ $fileName }}</h6>
                                        <small class="text-muted">Size: {{ $fileSize }} KB</small>
                                    </div>
                                    
                                    <div class="file-actions">
                                        <a href="{{ route('tickets.download', $ticket->id)}}" 
                                           target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="{{route('tickets.download', $ticket->id) }}" 
                                           download class="btn btn-sm btn-outline-success">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Created By -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-user text-primary"></i> Created By
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if($ticket->creator_firstname)
                                <div class="creator-info">
                                    <div class="avatar mb-3">
                                        <img src="{{ asset('assets/img/default-avatar.png') }}" 
                                             alt="Creator" class="rounded-circle" width="80" height="80">
                                    </div>
                                    <h6 class="mb-1">{{ $ticket->creator_firstname }} {{ $ticket->creator_lastname }}</h6>
                                    @if($ticket->creator_email)
                                    <p class="text-muted small mb-0">{{ $ticket->creator_email }}</p>
                                    @endif
                                </div>
                                @else
                                <div class="text-muted">
                                    <i class="fa fa-user-times fa-2x mb-2"></i>
                                    <p>Creator information not available</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Assigned Staff -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fa fa-users text-primary"></i> Assigned Staff
                                </h5>
                                @if($role === 'admin')
                                <button class="btn btn-sm btn-outline-primary" onclick="showAssignModal()">
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endif
                            </div>
                            <div class="card-body">
                                @php
                                    $hasAssignments = false;
                                    $assignments = [
                                        ['id' => $ticket->assign_1, 'name' => $ticket->assign1_firstname . ' ' . $ticket->assign1_lastname, 'email' => $ticket->assign1_email, 'image' => $ticket->assign1_image],
                                        ['id' => $ticket->assign_2, 'name' => $ticket->assign2_firstname . ' ' . $ticket->assign2_lastname, 'email' => $ticket->assign2_email, 'image' => $ticket->assign2_image],
                                        ['id' => $ticket->assign_3, 'name' => $ticket->assign3_firstname . ' ' . $ticket->assign3_lastname, 'email' => $ticket->assign3_email, 'image' => $ticket->assign3_image]
                                    ];
                                @endphp

                                @foreach($assignments as $index => $assignment)
                                    @if($assignment['id'])
                                        @php $hasAssignments = true; @endphp
                                        <div class="assignee-item d-flex align-items-center mb-3 p-2 border rounded">
                                            <div class="avatar me-3">
                                                <img src="{{ $assignment['image'] ? asset($assignment['image']) : asset('assets/img/default-avatar.png') }}" 
                                                     alt="Assignee" class="rounded-circle" width="40" height="40"
                                                     onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                                            </div>
                                            <div class="assignee-info">
                                                <h6 class="mb-0">{{ trim($assignment['name']) }}</h6>
                                                @if($assignment['email'])
                                                <small class="text-muted">{{ $assignment['email'] }}</small>
                                                @endif
                                                <div>
                                                    <span class="badge bg-info">Assignee {{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if(!$hasAssignments)
                                <div class="text-center text-muted">
                                    <i class="fa fa-user-times fa-2x mb-2"></i>
                                    <p class="mb-0">No staff assigned yet</p>
                                    @if($role === 'admin')
                                    <button class="btn btn-sm btn-primary mt-2" onclick="showAssignModal()">
                                        <i class="fa fa-user-plus"></i> Assign Staff
                                    </button>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ticket Timeline -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-clock text-primary"></i> Timeline
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Ticket Created</h6>
                                            <p class="timeline-text">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($ticket->created_at != $ticket->updated_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Last Updated</h6>
                                            <p class="timeline-text">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($ticket->status === 'closed')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Ticket Closed</h6>
                                            <p class="timeline-text">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal (Admin Only) -->
@if($role === 'admin')
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">
                    <i class="fa fa-user-plus"></i> Manage Assignment - {{ $ticket->ticket_id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="modal_assign_1" class="form-label">Assign 1</label>
                            <select id="modal_assign_1" name="assign_1" class="form-control">
                                <option value="">Select Employee</option>
                                @php
                                    $employees = DB::table('allemployees')->select('id', 'firstname', 'lastname', 'email', 'profile_image')->get();
                                @endphp
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $ticket->assign_1 == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="modal_assign_2" class="form-label">Assign 2</label>
                            <select id="modal_assign_2" name="assign_2" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $ticket->assign_2 == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="modal_assign_3" class="form-label">Assign 3</label>
                            <select id="modal_assign_3" name="assign_3" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $ticket->assign_3 == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAssignment()">
                    <i class="fa fa-save"></i> Save Assignment
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Custom CSS -->
<style>
.description-content {
    line-height: 1.6;
    font-size: 14px;
    color: #333;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.attachment-item {
    transition: all 0.3s ease;
}

.attachment-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.timeline-text {
    margin-bottom: 0;
    font-size: 12px;
    color: #666;
}

.assignee-item {
    transition: all 0.3s ease;
}

.assignee-item:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px);
}

.creator-info .avatar img,
.assignee-item .avatar img {
    border: 2px solid #007bff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-marker {
        left: -18px;
        width: 12px;
        height: 12px;
    }
    
    .assignee-item {
        flex-direction: column;
        text-align: center;
    }
    
    .assignee-item .avatar {
        margin-bottom: 10px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if($role === 'admin')
function updateStatus(status) {
    Swal.fire({
        title: 'Update Status',
        text: `Are you sure you want to change the status to "${status.charAt(0).toUpperCase() + status.slice(1)}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("tickets.updateStatus", $ticket->id) }}',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket status updated successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update status.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function showAssignModal() {
    $('#assignModal').modal('show');
}

function saveAssignment() {
    const assign1 = $('#modal_assign_1').val();
    const assign2 = $('#modal_assign_2').val();
    const assign3 = $('#modal_assign_3').val();

    $.ajax({
        url: '{{ route("tickets.updateAssignment", $ticket->id) }}',
        type: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            assign_1: assign1,
            assign_2: assign2,
            assign_3: assign3
        },
        success: function(response) {
            if (response.success) {
                $('#assignModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Assignment updated successfully.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update assignment.',
                icon: 'error'
            });
        }
    });
}

function confirmDelete() {
    Swal.fire({
        title: 'Delete Ticket',
        html: `Are you sure you want to delete this ticket?<br><strong>{{ $ticket->ticket_subject }}</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tickets.destroy", $ticket->id) }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
@endif

// Auto-refresh page every 30 seconds for real-time updates
setInterval(function() {
    // Only refresh if the ticket is not closed and user is not interacting
    if ('{{ $ticket->status }}' !== 'closed' && !document.querySelector('.modal.show')) {
        location.reload();
    }
}, 30000);
</script>
@endsection