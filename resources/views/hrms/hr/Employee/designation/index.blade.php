@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mt-5">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Designations</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Designations</li>
                </ul>
            </div>
                @if(isset($permissions) && $permissions->can_create)

            <div class="col-auto float-end ms-auto">
                <button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_designation">
                     Add Designation
                </button>
            </div>
                @endif
        </div>
    </div>

    <!-- Success and Error Messages -->
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

    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead>
            <tr>
               
                <th>S.No</th>
                <th>Designation</th>
                <th>Department</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($designations as $index => $designation)
            <tr>
                <td data-label="S. No.">{{ $index + 1 }}</td>

<td data-label="Designation">
    <span class="od-chip-highlight">{{ $designation->designation }}</span>
</td>

<td data-label="Department">
    <span class="high">
        @php
            $department = $departments->firstWhere('id', $designation->department_id);
        @endphp
        {{ $department ? $department->department : 'N/A' }}
    </span>
</td>

<td data-label="Actions" class="text-end">
    <div class="od-inline-actions">
        @if($permissions->can_edit)
        <button class="od-icon-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#edit_designation_{{ $designation->designation_id }}">
            <i class="fa fa-edit"></i>
        </button>
        @endif

        @if($permissions->can_delete)
        <button class="od-icon-btn danger" title="Delete" data-bs-toggle="modal" data-bs-target="#delete_designation_{{ $designation->designation_id }}">
            <i class="fa fa-trash"></i>
        </button>
        @endif
    </div>
</td>

            </tr>

            <!-- Edit Designation Modal -->
            <div class="modal fade" id="edit_designation_{{ $designation->designation_id }}" tabindex="-1" aria-labelledby="editDesignationLabel_{{ $designation->designation_id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDesignationLabel_{{ $designation->designation_id }}">Edit Designation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('designation.update', $designation->designation_id) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="designationName_{{ $designation->designation_id }}">Designation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" name="designation" id="designationName_{{ $designation->designation_id }}" value="{{ old('designation', $designation->designation) }}" required>
                                    @error('designation')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="departmentName_{{ $designation->designation_id }}">Department <span class="text-danger">*</span></label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" name="department_id" id="departmentName_{{ $designation->designation_id }}" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ $designation->department_id == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Designation Modal -->
            <div class="modal fade" id="delete_designation_{{ $designation->designation_id }}" tabindex="-1" aria-labelledby="deleteDesignationLabel_{{ $designation->designation_id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteDesignationLabel_{{ $designation->designation_id }}">Delete Designation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p>Are you sure you want to delete the designation: <strong>{{ $designation->designation }}</strong>?</p>
                            <form method="POST" action="{{ route('designation.destroy', $designation->designation_id) }}">
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
const checkAllDesignations = document.getElementById('checkAllDesignations');
const rowChecksDesignation = document.querySelectorAll('.row-check-designation');

checkAllDesignations?.addEventListener('change', function() {
    rowChecksDesignation.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksDesignation.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

        </div>
    </div>
</div>

<!-- Add Designation Modal -->
<div class="modal fade" id="add_designation" tabindex="-1" aria-labelledby="addDesignationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDesignationLabel">Add Designation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('designation.store') }}">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label" for="designationName">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('designation') is-invalid @enderror" name="designation" id="designationName" value="{{ old('designation') }}" required>
                        @error('designation')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label" for="departmentName">Department <span class="text-danger">*</span></label>
                        <select class="form-control @error('department_id') is-invalid @enderror" name="department_id" id="departmentName" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->department }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile-specific styles */
    @media (max-width: 767.98px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        .modal-content {
            border-radius: 0.5rem;
        }
        .modal-header, .modal-footer {
            padding: 1rem;
        }
        .modal-body {
            padding: 1rem;
            max-height: calc(100vh - 140px);
            overflow-y: auto;
        }
        .btn-close {
            font-size: 1.2rem;
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }
    }
    
    /* Ensure modal backdrop covers entire screen */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }
    
    /* Make sure modal is above backdrop */
    .modal {
        z-index: 1050;
    }
</style>

<!-- Success/Error Message Auto-Dismiss Script -->
<script>
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (successMessage) {
            successMessage.classList.add('d-none');
        }

        if (errorMessage) {
            errorMessage.classList.add('d-none');
        }
    }, 2000); // 2000ms = 2 seconds
</script>
@endsection