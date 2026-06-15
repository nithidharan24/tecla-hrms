@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Overtime');
@endphp@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container-fluid mt-4">

        <!-- Breadcrumb and Buttons in the Same Row -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h4 class="page-title">Overtime</h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Overtime</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('overtime.create') }}" class="btn" style="background-color: #ff9b44; border-radius: 50px; color: white;">
                     Add Overtime
                </a>
                @endif
                <a href="{{ route('overtime.trash') }}" class="btn btn-secondary" style="border-radius: 50px;">
                    Trash
                </a>
            </div>
        </div>

        <!-- Overtime Summary Cards -->
        <div class="row mb-4">
            @foreach (['employees' => 'Overtime Employee', 'hours' => 'Overtime Hours', 'pending_requests' => 'Pending Request', 'rejected_requests' => 'Rejected'] as $key => $label)
                <div class="col-md-3">
                    <div class="card bg-white summary-card">
                        <div class="card-body text-center">
                            <h6>{{ $label }}</h6>
                            <h3 class="font-weight-bold">{{ $overtimeSummary[$key] }}</h3>
                            <p class="text-muted">this month</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Overtime Table -->
        <div class="row">
            <div class="col-md-12 px-2">
                @if (count($overtimeRecords) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped custom-table datatable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>OT Date</th>
                                    <th>OT Hours</th>
                                    <th>OT Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Approved by</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($overtimeRecords as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $overtime->employee_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d-m-y') }}</td>
                                        <td>{{ $overtime->overtime_hours }}</td>
                                        <td>{{ $overtime->overtime_type }}</td>
                                        <td>{{ $overtime->description }}</td>
                                        <td class="text-center">
                                            <div class="dropdown action-label">
                                                <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    @if ($overtime->status === 'Approved')
                                                        <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                    @elseif ($overtime->status === 'Rejected')
                                                        <i class="fa-regular fa-circle-dot text-danger"></i> Rejected
                                                    @else
                                                        <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                    @endif
                                                </a>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $overtime->id }}, 'Approved')">
                                                        <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $overtime->id }}, 'Rejected')">
                                                        <i class="fa-regular fa-circle-dot text-danger"></i> Rejected
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $overtime->id }}, 'Pending')">
                                                        <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                    </a>
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ $overtime->approved_by }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-black"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                    <li><a class="dropdown-item" href="{{ route('overtime.edit', $overtime->id) }}">Edit</a></li>
                                                    @endif
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                    <li>
                                                        <form id="delete-form-{{ $overtime->id }}" action="{{ route('overtime.destroy', $overtime->id) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <a class="dropdown-item" href="#" onclick="confirmDelete({{ $overtime->id }}, '{{ $overtime->employee_name }}')">Delete</a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No overtime records found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function updateStatus(id, status) {
        $.ajax({
            url: '{{ route("overtime.toggleStatus", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                // Show success message with SweetAlert
                Swal.fire({
                    title: 'Success!',
                    text: 'Status updated successfully!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while updating status.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function confirmDelete(id, employeeName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the overtime record for ${employeeName}. This action will move the record to trash.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Wait until the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success message from Laravel session
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        // Check for error message from Laravel session
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif

        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 1s ease';
                successMessage.style.opacity = '0';
                
                setTimeout(function() {
                    successMessage.remove();
                }, 1000);
            }
        }, 3000);
    });
</script>

<style>
    .summary-card {
        background-color: #fff !important;
        height: 120px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .breadcrumb {
        padding-top: 10px;
        margin-bottom: 0;
    }

    /* Custom SweetAlert styling */
    .swal2-popup {
        border-radius: 15px;
    }
    
    .swal2-confirm {
        border-radius: 25px;
    }
    
    .swal2-cancel {
        border-radius: 25px;
    }
</style>

@endsection