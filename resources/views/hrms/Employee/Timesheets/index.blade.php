
@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Timesheet');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header bg-light">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title d-flex align-items-center">Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('timesheet.index') }}">Timesheet</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('timesheet.create') }}" class="btn add-btn">
                     Add Today Work
                </a>
                @endif
            </div>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success" id="success-alert">{{ Session::get('success') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" id="error-alert">{{ Session::get('error') }}</div>
    @endif

    <div class="row">
       <div class="col-md-12">
    <div class="table-responsive">
        <table class="table  custom-table datatable" id="timesheet-table">
            <thead>
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="checkAll" class="od-check">
                    </th>
                    <th class="text-center">S.No</th>
                    <th class="text-center">Employee</th>
                    <th class="text-center">Project</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Hours</th>
                    <th class="text-center">Description</th>
                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                </tr>
            </thead>
            <tbody>
                @foreach($timesheets as $timesheet)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="od-check row-check">
                    </td>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <div class="table-avatar">
                            <img src="{{ asset($timesheet->profile_image ?? 'path/to/default/image.png') }}" 
                                 alt="{{ $timesheet->firstname }}" class="avatar" />
                            <a href="#">{{ $timesheet->firstname }} {{ $timesheet->lastname }}</a>
                            <span class="text-muted">Role: {{ $timesheet->designation ?? 'Role' }}</span>
                        </div>
                    </td>
                    <td class="od-text-highlight">{{ $timesheet->projectname }}</td>
                    <td>{{ \Carbon\Carbon::parse($timesheet->assigned_date)->format('d M Y') }}</td>
                    <td><span class="od-chip-highlight">{{ $timesheet->assigned_hours }} Hours</span></td>
                    <td>{{ \Str::limit($timesheet->description, 50, '...') }}</td>
                 <td class="text-end">
    <div class="od-inline-actions">
        @if(isset($permissions) && $permissions->can_edit)
        <a href="{{ route('timesheet.edit', $timesheet->id) }}" class="od-icon-btn" title="Edit">
            <i class="fa-solid fa-pencil"></i>
        </a>
        @endif
        @if(isset($permissions) && $permissions->can_delete)
        <button type="button" class="od-icon-btn danger" onclick="confirmDelete({{ $timesheet->id }});" title="Delete">
            <i class="fa-solid fa-trash"></i>
        </button>
                                        <form id="delete-form-{{ $timesheet->id }}" action="{{ route('timesheet.destroy', $timesheet->id) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
        @endif
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


<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="confirmationModalLabel" style="font-weight: bold;">Move to Trash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to move this employee to trash?
            </div>
            <div class="modal-footer d-flex justify-content-around border-0">
                <button type="button" class="btn btn-outline-warning btn-lg" id="cancelDeleteBtn" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteBtn" style="border-radius: 50px; width: 150px;">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal (if you have one) -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="notificationMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentEmployeeId = null;
    const confirmationModalEl = document.getElementById('confirmationModal');
    const confirmationModal = new bootstrap.Modal(confirmationModalEl);

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    // Function to show delete confirmation modal
    window.confirmDelete = function(employeeId) {
        currentEmployeeId = employeeId;
        confirmationModal.show();
    };

    // Confirm delete button click
    confirmBtn.addEventListener('click', function() {
        if (!currentEmployeeId) return;

        const deleteForm = document.getElementById('delete-form-' + currentEmployeeId);
        if (deleteForm) {
            // Submit the form
            deleteForm.submit();
        }
    });

    // Cancel button click
    cancelBtn.addEventListener('click', function() {
        currentEmployeeId = null;
        confirmationModal.hide();
    });

    // Optional: Reset currentEmployeeId when modal closes
    confirmationModalEl.addEventListener('hidden.bs.modal', function () {
        currentEmployeeId = null;
    });

    // Checkbox row selection (for your table selection)
    const checkAll = document.getElementById('checkAll');
    const rows = document.querySelectorAll('.row-check');

    checkAll?.addEventListener('change', function() {
        rows.forEach(r => {
            r.checked = this.checked;
            r.closest('tr').classList.toggle('od-selected', this.checked);
        });
    });

    rows.forEach(r => {
        r.addEventListener('change', function() {
            this.closest('tr').classList.toggle('od-selected', this.checked);
        });
    });

    // Function to show notifications
    window.showNotification = function(title, message) {
        const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
        document.getElementById('notificationModalLabel').textContent = title;
        document.getElementById('notificationMessage').textContent = message;
        notificationModal.show();
    };

    // Example: changeDesignation function (if needed)
    window.changeDesignation = function(employeeId, designationId) {
        $.ajax({
            url: '{{ route("employee.updateDesignation") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: employeeId,
                designation_id: designationId
            },
            success: function(response) {
                if(response.success) {
                    $('#designation-display-' + employeeId).text(response.new_designation);
                    $('#designation-profile-' + employeeId).text(response.new_designation);
                    showNotification('Updated!', 'Designation updated successfully!');
                } else {
                    showNotification('Error!', 'Error updating designation!');
                }
            },
            error: function() {
                showNotification('Error!', 'Something went wrong!');
            }
        });
    };
});
</script>

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
        deleteForm = $(`<form action="{{ url('timesheet') }}/${id}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>`);
        $('body').append(deleteForm);
        $('#delete_workdetail').modal('show');
    });

    // Confirm deletion
    $('#confirm-delete').on('click', function() {
        deleteForm.submit();
    });
</script>

</script>

<!-- JS for checkbox row selection -->
<script>
const checkAll = document.getElementById('checkAll');
const rows = document.querySelectorAll('.row-check');

checkAll?.addEventListener('change', function() {
    rows.forEach(r => {
        r.checked = this.checked;
        r.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rows.forEach(r => {
    r.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

</script>
@endsection
