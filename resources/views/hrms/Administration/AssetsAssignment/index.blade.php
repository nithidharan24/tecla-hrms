@php
$permissions = App\Helpers\PermissionHelper::getPermissions('AssetsAssignment');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Assets Assignment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assets Assignment</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('assets-assignment.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-handshake"></i> Assign Asset
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('assets-assignment.index') }}" method="GET">
        <div class="row g-3 align-items-end">
            <!-- Employee Name Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Employee Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="employee_name" 
                        value="{{ request()->get('employee_name') }}" 
                        placeholder="Enter Employee Name"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">-- Select Status --</option>
                        <option value="assigned" {{ request()->get('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="returned" {{ request()->get('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
            </div>

            <!-- From Date -->
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        name="from_date" 
                        value="{{ old('from_date', request('from_date')) }}"
                    >
                </div>
            </div>

            <!-- To Date -->
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        name="to_date" 
                        value="{{ old('to_date', request('to_date')) }}"
                    >
                </div>
            </div>

            <!-- Search Button -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fa-solid fa-search"></i> Search
                </button>
            </div>
        </div>
    </form>

    <!-- Success or Error Alerts -->
    @if(Session::has('success'))
    <div class="alert alert-success" id="success-alert">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
    <div class="alert alert-danger" id="error-alert">{{ Session::get('error') }}</div>
    @elseif ($errors->any())
    <div class="alert alert-danger" id="error-alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div id="status-update-alert"></div>

    <!-- Assignments Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Asset Name</th>
                            <th>Asset ID</th>
                            <th>Model</th>
                            <th>Serial Number</th>
                            <th>Assigned Date</th>
                            <th>Return Date</th>
                            <th>Condition</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignments as $assignment)
                        <tr>
                            <td data-label="Employee Name">
                                <span class="high">{{ $assignment->firstname }} {{ $assignment->lastname }}</span>
                                @if($assignment->designation)
                                <br><small class="text-muted">{{ $assignment->designation }}</small>
                                @endif
                            </td>
                            <td data-label="Asset Name">
                                <span class="high">{{ $assignment->asset_name }}</span>
                            </td>
                            <td data-label="Asset ID">
                                <span class="digh">{{ $assignment->company_asset_id }}</span>
                            </td>
                            <td data-label="Model">
                                {{ $assignment->model ?? 'N/A' }}
                            </td>
                            <td data-label="Serial Number">
                                {{ $assignment->serial_number ?? 'N/A' }}
                            </td>
                            <td data-label="Assigned Date">
                                {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d/m/Y') }}
                            </td>
                            <td data-label="Return Date">
                                @if($assignment->return_date)
                                    {{ \Carbon\Carbon::parse($assignment->return_date)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Not Returned</span>
                                @endif
                            </td>
                            <td data-label="Condition">
                                <span class="badge 
                                    {{ $assignment->condition == 'New' ? 'bg-success' : 
                                       ($assignment->condition == 'Excellent' ? 'bg-info' : 
                                       ($assignment->condition == 'Good' ? 'bg-primary' : 
                                       ($assignment->condition == 'Fair' ? 'bg-warning' : 'bg-danger'))) }}">
                                    {{ $assignment->condition }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="status-badge 
                                    {{ strtolower($assignment->status) == 'assigned' ? 'approved' : 'returned' }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </td>
                            <td data-label="Actions" class="text-end">
                                <div class="od-inline-actions">
                                    
                                      
                                        <a href="{{ route('assets-assignment.edit', $assignment->id) }}" class="od-icon-btn" title="Edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                       
                                        <button type="button" class="od-icon-btn return-btn" data-id="{{ $assignment->id }}" title="Mark as Returned">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                   
                                    <button type="button" class="od-icon-btn delete-btn" data-id="{{ $assignment->id }}" title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                  
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($assignments->isEmpty())
                    <p class="text-center mt-3">No assignments found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Return Confirmation Modal -->
<div class="modal fade" id="return_asset" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-sm mb-3">
                        <div class="avatar-title bg-light text-success rounded-circle">
                            <i class="fa-solid fa-rotate-left fa-lg"></i>
                        </div>
                    </div>
                    <p class="mb-0">Are you sure you want to mark this asset as returned?</p>
                    <p class="text-info mt-2">This will make the asset available for assignment again.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-return" class="btn btn-success">
                    <i class="fa-solid fa-rotate-left me-1"></i> Mark as Returned
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete_assignment" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-sm mb-3">
                        <div class="avatar-title bg-light text-danger rounded-circle">
                            <i class="fa-solid fa-trash-can fa-lg"></i>
                        </div>
                    </div>
                    <p class="mb-0">Are you sure you want to delete this assignment?</p>
                    <p class="text-warning mt-2" id="delete-warning"></p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-delete" class="btn btn-danger">
                    <i class="fa-regular fa-trash-can me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Fade out success or error messages after a delay
    $(document).ready(function() {
        setTimeout(function() {
            $("#success-alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 3000);
        setTimeout(function() {
            $("#error-alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);
    });

    // Return asset functionality
    $(document).on("click", ".return-btn", function () {
        const assignmentId = $(this).data("id");
        $("#return_asset").modal("show");

        $("#confirm-return")
            .off("click")
            .on("click", () => {
                $.ajax({
                    url: "{{ url('assets-assignment') }}/" + assignmentId + "/return",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: (response) => {
                        $("#return_asset").modal("hide");
                        $("#status-update-alert")
                            .html('<div class="alert alert-success">Asset marked as returned successfully</div>')
                            .fadeIn()
                            .delay(2000)
                            .fadeOut();

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: (response) => {
                        $("#return_asset").modal("hide");
                        $("#status-update-alert")
                            .html('<div class="alert alert-danger">Failed to mark asset as returned</div>')
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                    },
                });
            });
    });

    // Delete assignment functionality
    $(document).ready(() => {
        $(document).on("click", ".delete-btn", function () {
            const assignmentId = $(this).data("id");
            $("#delete_assignment").modal("show");

            // Set up the confirm delete button handler
            $("#confirm-delete")
                .off("click")
                .on("click", () => {
                    $.ajax({
                        url: "{{ url('assets-assignment') }}/" + assignmentId,
                        method: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: (response) => {
                            $("#delete_assignment").modal("hide");
                            $("#status-update-alert")
                                .html('<div class="alert alert-success">Assignment deleted successfully</div>')
                                .fadeIn()
                                .delay(2000)
                                .fadeOut();

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                        error: (response) => {
                            $("#delete_assignment").modal("hide");
                            $("#status-update-alert")
                                .html('<div class="alert alert-danger">Failed to delete assignment</div>')
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        },
                    });
                });
        });
    });
</script>

@endsection