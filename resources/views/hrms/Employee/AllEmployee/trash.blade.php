@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Terminated Employees</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Terminated Employees</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('employee.index') }}" class="btn add-btn">
                        <i class="fa-solid fa-arrow-left"></i> Back to Employees
                    </a>
                </div>
            </div>
        </div>

        <!-- Bulk Action Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <button id="bulkRestoreBtn" class="btn btn-success btn-sm me-2" disabled>
                <i class="fa-solid fa-undo"></i> Restore Selected
            </button>
            <button id="bulkDeleteBtn" class="btn btn-danger btn-sm" disabled>
                <i class="fa-solid fa-trash"></i> Delete Selected
            </button>
        </div>

        <!-- Employee Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="trash-employee-table" class="table table-striped custom-table datatable">
                        <thead>
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                </th>
                                <th>Name</th>
                                <th>Employee ID</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th class="text-nowrap">Join Date</th>
                                <th class="text-end no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trashedEmployees as $employee)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="employee-checkbox form-check-input" value="{{ $employee->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($employee->profile_image) }}" alt="Profile Image" style="width: 50px; height: 50px; object-fit: cover; border-radius:50%">
                                            <div class="ms-2">
                                                <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->employeeid }}</td>
                                    <td><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></td>
                                    <td>{{ $employee->phone }}</td>
                                    <td>{{ \Carbon\Carbon::parse($employee->joiningdate)->format('d-m-y') }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a href="#" class="text-muted" data-bs-toggle="dropdown">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form action="{{ route('employee.restore', $employee->id) }}" method="POST" id="restore-form-{{ $employee->id }}">
                                                        @csrf
                                                        <button type="button" class="dropdown-item" onclick="confirmAction('restore', {{ $employee->id }});">
                                                            <i class="fa-solid fa-undo me-2"></i>Restore
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('employee.permanentlyDelete', $employee->id) }}" method="POST" id="delete-form-{{ $employee->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item" onclick="confirmAction('delete', {{ $employee->id }});">
                                                            <i class="fa-solid fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No terminated employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // ✅ Select All toggle
    checkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkButtons();
    });

    // ✅ Enable/disable bulk buttons
    checkboxes.forEach(cb => cb.addEventListener('change', toggleBulkButtons));

    function toggleBulkButtons() {
        const selected = Array.from(checkboxes).some(cb => cb.checked);
        bulkRestoreBtn.disabled = !selected;
        bulkDeleteBtn.disabled = !selected;
    }

    // ✅ Bulk Restore
    bulkRestoreBtn.addEventListener('click', function() {
        const selectedIds = getSelectedIds();
        if (!selectedIds.length) return;

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to restore ${selectedIds.length} employees.`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Restore",
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction("{{ route('employee.bulkRestore') }}", selectedIds, "Employees restored successfully!");
            }
        });
    });

    // ✅ Bulk Permanent Delete
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedIds = getSelectedIds();
        if (!selectedIds.length) return;

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to permanently delete ${selectedIds.length} employees.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete",
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction("{{ route('employee.bulkPermanentDelete') }}", selectedIds, "Employees permanently deleted!");
            }
        });
    });

    // ✅ Helper to get selected employee IDs
    function getSelectedIds() {
        return Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    }

    // ✅ Perform bulk action via AJAX
    function performBulkAction(url, ids, successMsg) {
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ employee_ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: successMsg,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire("Error", data.message || "Something went wrong", "error");
            }
        })
        .catch(() => Swal.fire("Error", "Server error occurred", "error"));
    }
});

// ✅ Single Restore/Delete confirmation (used in dropdown menu)
function confirmAction(action, id) {
    let title = '';
    let text = '';
    let icon = '';
    let confirmButtonText = '';

    if (action === 'restore') {
        title = 'Are you sure?';
        text = 'This will restore the employee.';
        icon = 'question';
        confirmButtonText = 'Yes, Restore';
    } else if (action === 'delete') {
        title = 'Are you sure?';
        text = 'This will permanently delete the employee record.';
        icon = 'warning';
        confirmButtonText = 'Yes, Delete';
    }

    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: action === 'delete' ? '#d33' : '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: confirmButtonText
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`${action}-form-${id}`).submit();
        }
    });
}
</script>


@endsection
