@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('All Employees');
@endphp
@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">All Employees </h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employee</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('employee.create') }}" class="btn add-btn">
                         Add Employee
                    </a>

                    <div class="view-icons">
                        <a href="{{ route('employee.grid') }}" class="grid-view btn btn-link active"><i class="fa fa-th"></i></a>
                        <a href="{{ route('employee.index') }}" class="list-view btn btn-link"><i class="fa-solid fa-bars"></i></a>
                    </div>
                </div>
            </div>
        </div>

        
      <!-- Filter Row -->
<div class="row filter-row mb-4 p-3 rounded bg-light">
    <form action="{{ route('employee.grid') }}" method="GET" class="w-100 d-flex flex-wrap">
        <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
            <input type="text" name="employee_id" class="form-control" placeholder="Employee ID">
        </div>
        <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
            <input type="text" name="employee_name" class="form-control" placeholder="Employee Name">
        </div>
        <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
    <select name="designation" class="form-select" style="height: calc(1.5em + 0.75rem + 9px);">
        <option selected>Select Designation</option>
        @foreach($designations as $designation)
            <option value="{{ $designation->id }}">{{ $designation-> designation}}</option>
        @endforeach
    </select>
</div>

                
                <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </form>
        </div>

        <!-- Employee Grid -->
        <div class="row">
            @forelse($employees as $employee)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center">
                    <div class="card-body position-relative">
                        <!-- Dropdown positioned to the top-right corner -->
                        <div class="dropdown position-absolute top-0 end-0 m-2">
    <a href="#" class="btn btn-sm btn-link p-0 border-0 text-muted" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('employee.edit', $employee->id) }}">
                <i class="fa-solid fa-pencil me-2"></i>Edit
            </a>
        </li>
        <li>
            @if($permissions->can_delete)
                <form action="{{ route('employee.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to move this employee to trash?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </form>
            @endif
        </li>
        
    </ul>
</div>


                        <!-- Employee image and details -->
                        <img src="{{ asset($employee->profile_image) }}" alt="{{ $employee->firstname }}" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="card-title">{{ $employee->firstname }} {{ $employee->lastname }}</h5>
                        <td>
    <div class="text-muted" id="designation-profile-{{ $employee->id }}">
        {{ $employee->designation_name }} <!-- Access the designation name here -->
    </div>
</td>

                    </div>
                </div>
            </div>
            @empty
                <p class="text-center">No employees found</p>
            @endforelse
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
                <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;" id="cancelDeleteBtn">Cancel</button>

                <form id="confirmForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg" id="confirmDeleteBtn" style="border-radius: 50px; width: 150px;">Confirm</button>
                </form>
                
            </div>
        </div>
    </div>
</div>

<script>
    let currentEmployeeId = null;
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

    function confirmDelete(employeeId) {
        currentEmployeeId = employeeId;
        // Update modal form action dynamically
        const form = document.getElementById('confirmForm');
        form.action = `/employee/${employeeId}`;
        confirmationModal.show();
    }

    // Confirm button submits form
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (currentEmployeeId) {
            document.getElementById('confirmForm').submit();
        }
    });

    // Cancel resets state
    document.getElementById('cancelDeleteBtn').addEventListener('click', function () {
        currentEmployeeId = null;
    });
</script>

    
@endsection
