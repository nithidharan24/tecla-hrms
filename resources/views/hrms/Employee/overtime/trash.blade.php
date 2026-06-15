@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container-fluid mt-4">

        @if (session('success'))
        <div id="successMessage" class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col">
                <h4 class="page-title">Overtime Trash</h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">Overtime</a></li>
                    <li class="breadcrumb-item active">Trash</li>
                </ul>
            </div>
        </div>

        <!-- Overtime Table in Trash -->
        <div class="row">
            <div class="col-md-12 px-2">
                @if (count($trashedOvertimeRecords) > 0)
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
                                @foreach ($trashedOvertimeRecords as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $overtime->employee_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d M Y') }}</td>
                                        <td>{{ $overtime->overtime_hours }}</td>
                                        <td>{{ $overtime->overtime_type }}</td>
                                        <td>{{ $overtime->description }}</td>
                                        <td>{{ ucfirst($overtime->status) }}</td>
                                        <td>{{ $overtime->approved_by }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <!-- Restore Button -->
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="confirmRestore({{ $overtime->id }}, '{{ $overtime->employee_name }}')">
                                                            <i class="fas fa-undo text-success me-2"></i>Restore
                                                        </a>
                                                    </li>
                                                    <!-- Delete Permanently Button -->
                                                    <li>
                                                        <form id="delete-permanently-form-{{ $overtime->id }}" action="{{ route('overtime.deletePermanently', $overtime->id) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <a class="dropdown-item" href="#" onclick="confirmPermanentDelete({{ $overtime->id }}, '{{ $overtime->employee_name }}')">
                                                            <i class="fas fa-trash text-danger me-2"></i>Delete Permanently
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No records in trash</h5>
                        <p class="text-muted">All overtime records are currently active.</p>
                        <a href="{{ route('overtime.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Overtime
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmRestore(id, employeeName) {
        Swal.fire({
            title: 'Restore Record?',
            text: `Are you sure you want to restore the overtime record for ${employeeName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-2"></i>Yes, restore it!',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Restoring...',
                    text: 'Please wait while we restore the record.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Redirect to restore route
                window.location.href = `{{ route('overtime.restore', ':id') }}`.replace(':id', id);
            }
        });
    }

    function confirmPermanentDelete(id, employeeName) {
        Swal.fire({
            title: 'Permanently Delete?',
            html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p>You are about to <strong>permanently delete</strong> the overtime record for:</p>
                    <p class="fw-bold text-danger">${employeeName}</p>
                    <p class="text-muted small">This action cannot be undone!</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, delete permanently!',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we permanently delete the record.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form
                document.getElementById('delete-permanently-form-' + id).submit();
            }
        });
    }

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
                position: 'top-end',
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        @endif

        // Check for error message from Laravel session
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        @endif

        // Hide success message after 3 seconds (fallback)
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
    /* Custom SweetAlert styling */
    .swal2-popup {
        border-radius: 15px;
        font-family: inherit;
    }
    
    .swal2-confirm, .swal2-cancel {
        border-radius: 25px;
        font-weight: 500;
        padding: 10px 20px;
    }
    
    .swal2-icon.swal2-warning {
        border-color: #f0ad4e;
        color: #f0ad4e;
    }
    
    .swal2-icon.swal2-question {
        border-color: #5bc0de;
        color: #5bc0de;
    }

    /* Empty state styling */
    .text-center.py-5 {
        padding: 3rem 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin: 2rem 0;
    }

    /* Dropdown menu icons */
    .dropdown-item i {
        width: 16px;
        text-align: center;
    }
</style>
@endsection