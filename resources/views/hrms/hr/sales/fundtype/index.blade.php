@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Provident Type</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Provident Type</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_type">Add New</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
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
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="width-thirty">S.No</th>
                            <th>Type Name</th>
                            <th>Description </th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pftypes as $item)
                            <tr id="pf-type-row-{{$item->id}}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->type}}</td>
                                <td>
                                @if(strlen($item->description) < 60)
                                    <p>{{ $light }}</p>
                                @else
                                    <p>{{ substr($item->description, 0, 60) }}...</p>
                                @endif
                                </td>
                                <td>
                                    <div class="dropdown action-label">
                                        <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" id="bt" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                            <i id="cls" class="fa-regular fa-circle-dot {{$item->status=='active' ? 'text-success':'text-danger'}}"></i>
                                            {{ ucfirst($item->status) }}
                                        </button>

                                        <div class="dropdown-menu">
                                            <!-- Active Button -->
                                            <button class="dropdown-item {{ $item->status == 'active' ? 'disabled' : '' }}" 
                                                onclick="statusChange('{{$item->id}}', 'active')">
                                                <i class="fa-regular fa-circle-dot text-success"></i> Active
                                            </button>
                                        
                                            <!-- Inactive Button -->
                                            <button class="dropdown-item {{ $item->status == 'inactive' ? 'disabled' : '' }}" 
                                                onclick="statusChange('{{$item->id}}', 'inactive')">
                                                <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                                            </button>
                                        </div>                                        
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#edit_type"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                            <button class="dropdown-item" onclick="deleteData('{{$item->id}}')"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</button>
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
<!-- /Page Content -->

{{-- Add Type Modal --}}
<div id="add_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New PF Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_pftype" method="POST" action="{{route('pftype.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label">PF Type <span class="text-danger">*</span></label>
                        <input class="form-control" name="pftypename" id="pftypename" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="description" style="resize: none;" rows="4"></textarea>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Goal Modal -->
<div id="edit_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Goal Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_pftype" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Goal Type <span class="text-danger">*</span></label>
                        <input class="form-control" name="edit_pftypename" id="pftypename_edit" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="edit_description" id="description_edit" style="resize: none;" rows="4"></textarea>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Goal Modal -->

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

    // Jquery Validation For Add Goal Type
    $(document).ready(function () {
        // Custom validation method for "First Name"
        $.validator.addMethod("customName", function (value, element) {
            return this.optional(element) || /^[^\s].{0,29}$/.test(value);
        }, "Invalid format. No leading spaces allowed, and must not exceed 30 characters.");

        // Initialize form validation
        $("#add_pftype").validate({
            rules: {
                pftypename: {
                    required: true,
                    customName: true
                },
                description: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                },
            },
            messages: {
                pftypename: {
                    required: "PF Type is required."
                },
                description: {
                    required: "Description is required.",
                    minlength: "Description must be at least 10 characters.",
                    maxlength: "Description must not exceed 500 characters."
                }
            },
            errorClass: "error",
            errorElement: "div",
            errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.input-block').append(error);
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

        // Jquery Validation For Edit Goal Type
            // Initialize validation for edit goal form
            $("#edit_pftype").validate({
                rules: {
                    edit_pftypename: {
                        required: true,
                        customName: true
                    },
                    edit_description: {
                        required: true,
                        minlength: 10,
                        maxlength: 500
                    },
                },
                messages: {
                    edit_pftypename: {
                        required: "PF Type is required."
                    },
                    edit_description: {
                        required: "Description is required.",
                        minlength: "Description must be at least 10 characters.",
                        maxlength: "Description must not exceed 500 characters."
                    }
                },
                errorClass: "error",
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-block').append(error);
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

        // This ensures that when the modal is shown, validation is re-triggered if needed
            $('#add_type').on('shown.bs.modal', function () {
                $('#add_pftype').validate().resetForm();
                $('#add_pftype')[0].reset();
                $('.form-control').removeClass('is-invalid');
            });

            // Reset validation and form when the modal is opened
            $('#edit_type').on('shown.bs.modal', function () {
                $('#edit_pftype').validate().resetForm();
            });

            $('#edit_type').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');           

            // Fetch goal data and populate the modal
            $.get('pfund/type/edit/' + id, function(data) {
                $('#pftypename_edit').val(data.type);
                $('#description_edit').val(data.description);
                var updateUrl = "{{ route('pftype.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                $('#edit_pftype').attr('action', updateUrl);
            });

            });


    });

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
                    type: "PUT",
                    url: "{{ route('pfund-type-statuschange') }}",  // Updated route name
                    data: { id: id, status: status },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                    if (data.status === 1) {
                        Swal.fire({
                            title: "Success",
                            text: "Pf-type status changed successfully.",
                            icon: "success"
                        }).then(()=>{
                            window.location.reload();
                        });
                    }else if (data.status === 2) {
                        Swal.fire({
                            title: "Success",
                            text: "Pf-type status changed successfully.",
                            icon: "success"
                        }).then(()=>{
                            window.location.reload();
                        });
                    }else {
                        Swal.fire({
                            title: "Error",
                            text: data.message,
                            icon: "error"
                        });
                    }
                    },
                    error: function (error) {
                        console.error("Error updating Pf-type status:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to update Pf-type status.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: "Are you sure you want to delete this item?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = "{{ route('pftype.destroy', ':id') }}";
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
                            text: "PF-type has been deleted.",
                            icon: "success"
                        }).then(() => {
                            // Remove the table row
                            $('#pf-type-row-' + id).remove();
                        });
                    },
                    error: function (error) {
                        console.error("Error deleting pf type:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to delete pf type.",
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
