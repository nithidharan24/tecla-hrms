@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Resignation');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header bg-light">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title d-flex align-items-center">Resignation</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    
                    <li class="breadcrumb-item active"><a href="{{ route('resignation.index') }}">Resignation</li>
                </ul>
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
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th class="text-center">S.NO</th>
                            <th class="text-center">Resignation Employee</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Notice Date</th>
                            <th class="text-center">Resignation Date</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resignations as $resignation)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>                      
                            <td data-label="S.No" class="text-center">{{ $loop->iteration }}</td>                      

                            <td data-label="Employee">
                                <div class="table-avatar">
                                    <img src="{{ asset($resignation->profile_image ?? 'path/to/default/image.png') }}" 
                                         alt="{{ $resignation->firstname }}" class="avatar" />
                                    <a href="#">{{ $resignation->firstname }} {{ $resignation->lastname }}</a> 
                                </div>
                            </td>
                            
                            <td data-label="Department" class="text-center">{{ $resignation->department ?? 'Department' }}</td>
                            
                            <td data-label="Reason" class="text-center">{{ $resignation->reason }}</td>
                            
                            <td data-label="Notice Date" class="text-center">{{ \Carbon\Carbon::parse($resignation->notice_date)->format('d-m-Y') }}</td>
                            
                            <td data-label="Resignation Date" class="text-center">{{ \Carbon\Carbon::parse($resignation->resignation_date)->format('d-m-Y') }}</td>
                            
                            <td data-label="Actions" class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if($permissions->can_edit)
                                        <a class="dropdown-item" href="{{ route('resignation.edit', $resignation->id) }}">
                                            <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                        </a>
                                        @endif
                                        @if($permissions->can_delete)
                                        <button type="button" class="dropdown-item delete-btn" data-id="{{ $resignation->id }}">
                                            <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                        </button>      
                                        @endif                               
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

<!-- Delete Today Work Modal -->
<div class="modal custom-modal fade" id="delete_workdetail" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Resignation Details</h3>
                    <p>Are you sure you want to delete?</p>
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
        deleteForm = $(`<form action="{{ url('resignation') }}/${id}" method="POST">
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
@endsection
