@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Background Verification');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Background Verification</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">BGV Records</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('background-verification.create') }}" class="btn add-btn">
                        <i class="fa fa-plus"></i> Add BGV Record
                    </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filters</h5>
            <form method="GET" action="{{ route('background-verification.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Candidate Name or Email"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Filters Card -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Candidate</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Verification Date</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bgvRecords as $index => $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $record->candidate->first_name }} {{ $record->candidate->last_name }}</strong><br>
                                    <small>{{ $record->candidate->email }}</small>
                                </td>
                                <td>{{ $record->candidate->position_applied }}</td>
                                <td>
                                    <select class="form-select status-select" data-id="{{ $record->id }}">
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ $record->status == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    @if($record->documents && count($record->documents) > 0)
                                        {{ count($record->documents) }} Document(s)
                                    @else
                                        <span class="text-muted">No Documents</span>
                                    @endif
                                </td>
                                <td>{{ $record->verification_date ? $record->verification_date->format('d M Y') : 'N/A' }}</td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('background-verification.show', $record->id) }}">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            @if(isset($permissions) && $permissions->can_edit)
                                                <a class="dropdown-item" href="{{ route('background-verification.edit', $record->id) }}">
                                                    <i class="fa-solid fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            <!-- Send Appointment Letter action - Only visible when status is 'completed' -->
                                            @if($record->status == 'completed')
                                                <a class="dropdown-item send-letter" href="#" data-id="{{ $record->id }}">
                                                    <i class="fa-solid fa-envelope"></i> Send Appointment Letter
                                                </a>
                                            @endif
                                            @if(isset($permissions) && $permissions->can_delete)
                                                <a class="dropdown-item text-danger delete-record" href="#" data-id="{{ $record->id }}">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <form id="delete-form-{{ $record->id }}" action="{{ route('background-verification.destroy', $record->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bgvRecords->count() > 0)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $bgvRecords->firstItem() }} to {{ $bgvRecords->lastItem() }} of {{ $bgvRecords->total() }} entries
                            </div>
                            <div>
                                {{ $bgvRecords->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .status-select {
        min-width: 160px;
        font-size: 12px;
    }
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-title { 
        font-weight: 600; 
        color: #333; 
        font-size: 16px; 
        margin-bottom: 15px; 
    }
    .form-group label { 
        font-weight: 500; 
        color: #495057; 
        font-size: 14px; 
        margin-bottom: 5px; 
    }
    .form-control { 
        border-radius: 4px; 
        font-size: 14px; 
    }
    .dropdown-menu { 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Status change - ONLY updates status, does NOT send email
    $('.status-select').change(function() {
        const recordId = $(this).data('id');
        const newStatus = $(this).val();
        const updateUrl = "{{ route('background-verification.update-status', ':id') }}".replace(':id', recordId);
        
        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
            },
            error: function() {
                Swal.fire('Error!', 'Unable to update status.', 'error');
            }
        });
    });

    // Delete record
    $('.delete-record').click(function(e) {
        e.preventDefault();
        const recordId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + recordId).submit();
            }
        });
    });

    // Send appointment letter - ONLY sends when button is clicked
    $('.send-letter').click(function(e) {
        e.preventDefault();
        const recordId = $(this).data('id');
        const sendUrl = "{{ route('background-verification.send-appointment-letter', ':id') }}".replace(':id', recordId);

        Swal.fire({
            title: 'Send Appointment Letter?',
            text: "Are you sure you want to send the appointment letter?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, send it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: sendUrl,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Sent!', response.message || 'Appointment letter sent successfully.', 'success');
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to send appointment letter.';
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});

function resetFilters() {
    window.location.href = "{{ route('background-verification.index') }}";
}

// Auto hide success message after 3 seconds
setTimeout(() => {
    const successMsg = document.getElementById('success-message');
    if (successMsg) {
        successMsg.style.transition = 'opacity 0.5s';
        successMsg.style.opacity = '0';
        setTimeout(() => { successMsg.remove(); }, 500);
    }
}, 3000);

</script>
@endsection
