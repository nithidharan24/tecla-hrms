@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Permission Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin-leaves.index') }}">Leaves & Permissions</a></li>
                    <li class="breadcrumb-item active">Permission Details</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('admin-leaves.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Permission Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Employee Name:</strong>
                                <span>{{ $permission->employee_name }}</span>
                            </div>
                            <div class="info-item">
                                <strong>Employee ID:</strong>
                                <span>{{ $permission->employeeid }}</span>
                            </div>
                            <div class="info-item">
                                <strong>Designation:</strong>
                                <span>{{ $permission->designation_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Permission Date:</strong>
                                <span>{{ \Carbon\Carbon::parse($permission->permission_date)->format('d M Y') }}</span>
                            </div>
                            <div class="info-item">
                                <strong>Time:</strong>
                                <span>{{ \Carbon\Carbon::parse($permission->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($permission->end_time)->format('h:i A') }}</span>
                            </div>
                            <div class="info-item">
                                <strong>Duration:</strong>
                                <span>{{ $permission->duration }} hours</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="info-item">
                                <strong>Permission Reason:</strong>
                                <p class="mt-2">{{ $permission->permission_reason }}</p>
                            </div>
                        </div>
                    </div>

                    @if($permission->supporting_document)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="info-item">
                                <strong>Supporting Document:</strong>
                                <div class="mt-2">
                                    <a href="{{ Storage::url($permission->supporting_document) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-download"></i> Download Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Status & Actions</h5>
                </div>
                <div class="card-body">
                    <div class="status-section text-center mb-4">
                        <strong>Current Status:</strong>
                        <div class="mt-2">
                            <span class="badge badge-{{ 
                                $permission->status == 'approved' ? 'success' : 
                                ($permission->status == 'declined' ? 'danger' : 'warning') 
                            }} p-2">
                                {{ ucfirst($permission->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="action-section">
                        <strong>Update Status:</strong>
                        <div class="btn-group-vertical w-100 mt-2">
                            <a href="#" class="btn btn-outline-warning btn-sm permission-status-change" 
                               data-id="{{ $permission->id }}" data-status="pending">
                                <i class="fa-regular fa-circle-dot text-warning"></i> Mark as Pending
                            </a>
                            <a href="#" class="btn btn-outline-success btn-sm permission-status-change mt-1" 
                               data-id="{{ $permission->id }}" data-status="approved">
                                <i class="fa-regular fa-circle-dot text-success"></i> Approve Permission
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-sm permission-status-change mt-1" 
                               data-id="{{ $permission->id }}" data-status="declined">
                                <i class="fa-regular fa-circle-dot text-danger"></i> Decline Permission
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Employee Contact</h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Email:</strong>
                        <span>{{ $permission->email }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Phone:</strong>
                        <span>{{ $permission->phone }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.permission-status-change').click(function(e) {
        e.preventDefault();
        
        const permissionId = $(this).data('id');
        const status = $(this).data('status');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to change the permission status to ${status}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                updatePermissionStatus(permissionId, status);
            }
        });
    });

    function updatePermissionStatus(permissionId, status) {
        $.ajax({
            url: "{{ url('admin-permissions') }}/" + permissionId + "/update-status",
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the status',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating the status: ' + xhr.responseText,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
});
</script>

<style>
.info-item {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}
.info-item strong {
    display: block;
    color: #666;
    font-size: 14px;
}
.info-item span {
    display: block;
    color: #333;
    font-weight: 500;
}
</style>
@endsection