@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp



@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mt-5">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Department</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Department</li>
                </ul>
            </div>
           
              
@if(isset($permissions) && $permissions->can_create)

                <div class="col-auto float-end ms-auto">
                    <button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_department">
                        Add Department
                    </button>
                </div>
                @endif
            </div>
        
    </div>
  
    
    <div class="row">
        <div class="col-md-12">
            <!-- Success and Error messages -->
            <div class="row mb-3">
                <div class="col-md-12">
                    @if (session('success'))
                        <div class="alert alert-success" id="success-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" id="error-message">
                            {{ session('error') }}
                        </div>
                    @endif
                    <!-- Display error message for duplicate department -->
                    @if (session('duplicate_error'))
                        <div class="alert alert-danger" id="duplicate-error-message">
                            {{ session('duplicate_error') }}
                        </div>
                    @endif
                </div>
            </div>

<div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead>
            <tr>
               
                <th>S.No</th>
                <th>Department Name</th>
               @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($departments as $department)
            <tr>
                
                <td data-label="S. No.">{{ $loop->iteration }}</td>

                <td data-label="Department">
                    <span class="od-chip-highlight">{{ $department->department }}</span>
                </td>
                
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if($permissions->can_edit)
                        <button class="od-icon-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#edit_department_{{ $department->id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                        @endif
                
                        @if($permissions->can_delete)
                        <button class="od-icon-btn danger" title="Delete" data-bs-toggle="modal" data-bs-target="#delete_department_{{ $department->id }}">
                            <i class="fa fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </td>
                
            </tr>

            <!-- Edit Department Modal -->
            <div class="modal fade" id="edit_department_{{ $department->id }}" tabindex="-1" aria-labelledby="editDepartmentLabel_{{ $department->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDepartmentLabel_{{ $department->id }}">Edit Department</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('department.update', $department->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="col-form-label" for="departmentName_{{ $department->id }}">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="department" id="departmentName_{{ $department->id }}" value="{{ $department->department }}" required>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Department Modal -->
            <div class="modal fade" id="delete_department_{{ $department->id }}" tabindex="-1" aria-labelledby="deleteDepartmentLabel_{{ $department->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteDepartmentLabel_{{ $department->id }}">Delete Department</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p>Are you sure you want to delete the department "<strong>{{ $department->department }}</strong>"?</p>
                            <form action="{{ route('department.destroy', $department->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="d-flex justify-content-center gap-3 mt-3">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @endforeach
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllDepartments = document.getElementById('checkAllDepartments');
const rowChecksDepartment = document.querySelectorAll('.row-check-department');

checkAllDepartments?.addEventListener('change', function() {
    rowChecksDepartment.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksDepartment.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div id="add_department" class="modal fade" tabindex="-1" aria-labelledby="addDepartmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentLabel">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDepartmentForm" method="POST" action="{{ route('department.store') }}">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label" for="departmentName">Department Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="departmentName" name="department" placeholder="Enter Department Name" required>
                        <span class="text-danger" id="error-message"></span>
                    </div>
                    <div class="submit-section">
                        <button type="submit" id="submitBtn" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentInput = document.getElementById('departmentName');
        const errorMessage = document.getElementById('error-message');
        const submitButton = document.getElementById('submitBtn');

        departmentInput.addEventListener('input', function() {
            const departmentName = departmentInput.value.trim();

            if (departmentName.length > 0) {
                // Send AJAX request to check if the department name exists
                fetch("{{ route('department.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ department: departmentName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        errorMessage.textContent = 'Department already exists';
                        submitButton.disabled = true; // Disable the submit button
                    } else {
                        errorMessage.textContent = ''; // Clear the error message
                        submitButton.disabled = false; // Enable the submit button
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });

        // Hide success and error messages after 5 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const duplicateErrorMessage = document.getElementById('duplicate-error-message');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
            if (duplicateErrorMessage) duplicateErrorMessage.style.display = 'none';
        }, 5000);
    });
</script>
@endsection
