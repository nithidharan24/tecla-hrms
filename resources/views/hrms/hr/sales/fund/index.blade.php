@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Provident Fund');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Provident Fund</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Provident Fund</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{route('providentfund.create')}}" class="btn add-btn"> Add Provident Fund</a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        @if (Session::has('messageType') && Session::has('message'))
        @if (Session::get('messageType') === 'success')
            <div class="alert alert-success" id="success-alert">
                {{ Session::get('message') }}
            </div>
        @elseif (Session::get('messageType') === 'error')
            <div class="alert alert-danger" id="error-alert">
                {{ Session::get('message') }}
            </div>
        @endif
    @endif
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable mb-0">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Provident Fund Type</th>
                            <th>Employee Share</th>
                            <th>Organization Share</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($pf_funds) > 0)
                            @foreach ($pf_funds as $item)
                                <tr id="p-fund-row-{{$item->pf_id}}">
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#">{{ucFirst($item->employee_name)}} <span> {{ucFirst('  '.$item->designation_name)}}</span></a>
                                        </h2>
                                    </td>
                                    <td>{{$item->pf_type}}</td>
                                    <td>
                                        @if($item->pf_type == 'Fixed Amount')
                                            {{ $item->employee_share_amount }}
                                        @elseif($item->pf_type == 'Percentage Based')
                                            {{ $item->employee_share_percent }}%
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->pf_type == 'Fixed Amount')
                                            {{ $item->organization_share_amount}}
                                        @elseif($item->pf_type == 'Percentage Based')
                                            {{ $item->organization_share_percent }}%
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown action-label">
                                            <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" id="bt" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                                <i id="cls" class="fa-regular fa-circle-dot {{$item->status=='approved' ? 'text-success':'text-danger'}}"></i>
                                                {{ ucfirst($item->status) }}
                                            </button>
    
                                            <div class="dropdown-menu">
                                                <!-- Active Button -->
                                                <button class="dropdown-item {{ $item->status == 'approved' ? 'disabled' : '' }}" 
                                                    onclick="statusChange('{{$item->pf_id}}', 'approved')">
                                                    <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                </button>
                                            
                                                <!-- Inactive Button -->
                                                <button class="dropdown-item {{ $item->status == 'pending' ? 'disabled' : '' }}" 
                                                    onclick="statusChange('{{$item->pf_id}}', 'pending')">
                                                    <i class="fa-regular fa-circle-dot text-danger"></i> Pending
                                                </button>
                                            </div>                                        
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(isset($permissions) && $permissions->can_edit)
                                                <a class="dropdown-item" href="{{ route('providentfund.edit', $item->pf_id) }}">
                                                    <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                </a>
                                                @endif
                                                @if(isset($permissions) && $permissions->can_delete)
                                                <button class="dropdown-item" onclick="deleteData('{{$item->pf_id}}')">
                                                    <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>No Record Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->

<script>

    // Automatically close the success message after 1 second
    setTimeout(function() {
        $("#success-alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 1000);

    // Automatically close the error message after 1 second
    setTimeout(function() {
        $("#error-alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 3000);

    function statusChange(id, status) {
        Swal.fire({
            title: "Are you sure to change the status?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('provifund.changeStatus', ':id') }}".replace(':id', id),
                    data: { status: status },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        Swal.fire({
                            title: "Success",
                            text: "Status changed successfully.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function (error) {
                        console.error("Error updating status:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to update status.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }


    function deleteData(id) {
        
        Swal.fire({
            title: "Are you sure you want to delete this Provident Fund?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = "{{ route('providentfund.destroy', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Successfully deleted.",
                            icon: "success"
                        }).then(() => {
                            // Remove the table row
                            $('#p-fund-row-'+id).remove();
                        });
                    },
                    error: function (error) {
                        console.error("Error deleting:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to delete.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }
    // Show validation errors using SweetAlert
    @if ($errors->any())
    var errorMessage = '';
    @foreach ($errors->all() as $error)
        errorMessage += "{{ $error }}\n";
    @endforeach
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        text: errorMessage,
    });
    @endif

</script>

@endsection
