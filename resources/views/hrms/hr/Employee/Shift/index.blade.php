@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Shift & Schedule');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header bg-light">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title d-flex align-items-center">Shift List</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="">Employees</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('shift.index')}}">Shift List</a></li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{route('shift.create')}}" class="btn add-btn m-r-5">Add Shifts</a>
                @endif
                <!-- <a href="{{route('scheduling.create')}}" class="btn add-btn m-r-5">Assign Schedule</a> -->
            </div>
        </div>
    </div>

    <!-- Success or Error Alerts -->
    @if(Session::has('success'))
    <div class="alert alert-success" id="success-alert">{{ Session::get('success') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" id="error-alert">{{ Session::get('error') }}</div>
    @endif

    <div id="status-update-alert"></div>

    <!-- Shift List Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th class="text-center">S.NO</th>
                            <th class="text-center">Shift Name</th>
                            <th class="text-center">Start Time</th>
                            <th class="text-center">End Time</th>
                            <th class="text-center">Break Time</th>
                            <th class="text-center">Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                            <tr>
                                <td data-label="S.I.">{{ $loop->iteration }}</td>
<td data-label="Shift Name">{{ $shift->shift_name }}</td>
<td data-label="Start Time">{{ $shift->start_time }}</td>
<td data-label="End Time">{{ $shift->end_time }}</td>
<td data-label="Break Time">{{ $shift->break_time }}</td>
<td data-label="Status">
    <div class="dropdown action-label">
        <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fa-regular fa-circle-dot {{ $shift->status == 'active' ? 'text-success' : 'text-danger' }}"></i>
            {{ ucfirst($shift->status) }}
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item update-status" href="#" data-id="{{ $shift->id }}" data-status="active">
                <i class="fa-regular fa-circle-dot text-success"></i> Active
            </a>
            <a class="dropdown-item update-status" href="#" data-id="{{ $shift->id }}" data-status="inactive">
                <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
            </a>
        </div>
    </div>
</td>
<td data-label="Actions" class="text-end">
    <div class="dropdown dropdown-action">
        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="material-icons">more_vert</i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('shift.edit', $shift->id) }}">
                <i class="fa-solid fa-pencil m-r-5"></i> Edit
            </a>
            <button type="button" class="dropdown-item delete-btn" data-id="{{ $shift->id }}">
                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
            </button>                                     
        </div>
    </div>
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Shift Modal -->
<div class="modal custom-modal fade" id="delete_shift" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Shift</h3>
                    <p>Are you sure you want to delete this shift?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" id="confirm-delete" class="btn btn-primary continue-btn">Delete</a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Delete Modal -->

<script>
    // Automatically close success and error messages
    setTimeout(function() {
        $("#success-alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 1000);

    setTimeout(function() {
        $("#error-alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);

    // Delete confirmation modal logic
    let deleteForm;
    $('body').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        deleteForm = $(`<form action="{{ url('shift') }}/${id}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>`);
        $('body').append(deleteForm);
        $('#delete_shift').modal('show');
    });

    // Confirm deletion
    $('#confirm-delete').on('click', function() {
        deleteForm.submit();
    });

    // Handle status updates
  // Handle status updates
$(document).on('click', '.update-status', function (e) {
    e.preventDefault();

    let shiftId = $(this).data('id');
    let newStatus = $(this).data('status');
    let $statusDropdown = $(this).closest('.dropdown');

    $.ajax({
       url: "{{ route('shift.status', ['id' => ':id']) }}".replace(':id', shiftId),
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: newStatus
        },
        success: function (response) {
            if (response.success) {
                // Update the dropdown text and icon color
                let iconClass = newStatus == 'active' ? 'text-success' : 'text-danger';
                $statusDropdown.find('.dropdown-toggle').html(`
                    <i class="fa-regular fa-circle-dot ${iconClass}"></i> ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
                `);
                // Show success alert
                $("#status-update-alert").html(`
                    <div class="alert alert-success">
                        Status updated successfully!
                    </div>
                `).fadeIn().delay(3000).fadeOut();
            } else {
                $("#status-update-alert").html(`
                    <div class="alert alert-danger">
                        ${response.message}
                    </div>
                `).fadeIn().delay(3000).fadeOut();
            }
        },
        error: function () {
            $("#status-update-alert").html(`
                <div class="alert alert-danger">
                    Error updating status. Please try again later.
                </div>
            `).fadeIn().delay(3000).fadeOut();
        }
    });
});
</script>

@endsection