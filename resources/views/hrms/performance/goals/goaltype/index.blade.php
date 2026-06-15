@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Goal Type');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Goal Type</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Goal Type</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_type"> Add New</a>
                @endif
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
    <table id="goaltype-table" class="table custom-table datatable">
        <thead>
            <tr>
             
                <th>Type</th>
                <th>Description</th>
                <th>Status</th>
               @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($goalTypes as $item)
                <tr>
                  
                    <td data-label="Goal">
                        <a class="high">{{ $item->goal }}</a>
                    </td>
                    <td data-label="Description">{{ $item->description }}</td>
                    <td data-label="Status">
                        <span class="od-chip-highlight">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td data-label="Actions" class="text-end">
                        <div class="od-inline-actions">
                            @if($permissions->can_edit)
                                <a href="#" class="od-icon-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#edit_type" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            @endif
                            @if($permissions->can_delete)
                                <button class="od-icon-btn danger" title="Delete" onclick="deletegoalType('{{ $item->id }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all rows
    $('#checkAll').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    // Row highlight on individual checkbox
    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>

        </div>
    </div>

</div>
<!-- /Page Content -->

{{-- Add Goal Type Modal --}}
<div id="add_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Goal Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addgoal" method="POST" action="{{route('goal.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label">Goal Type <span class="text-danger">*</span></label>
                        <input class="form-control" name="goal" id="goal" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="description" style="resize: none;" rows="4"></textarea>
                    </div>
                    <div class="submit-section d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
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
                <form id="editgoal" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Goal Type <span class="text-danger">*</span></label>
                        <input class="form-control" name="goal" id="goal_edit" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="description_edit" style="resize: none;" rows="4"></textarea>
                    </div>
                    <div class="submit-section d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Goal Modal -->

<script>
    // Submit button handling for both forms
    document.getElementById('submitBtn')?.addEventListener('click', function () {
        this.disabled = true;
        this.textContent = 'Processing...';
        this.closest('form').submit();
    });
    
    document.getElementById('editSubmitBtn')?.addEventListener('click', function () {
        this.disabled = true;
        this.textContent = 'Processing...';
        this.closest('form').submit();
    });

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
        $("#addgoal").validate({
            rules: {
                goal: {
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
                goal: {
                    required: "Goal Type is required."
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
            $("#editgoal").validate({
                rules: {
                    goal: {
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
                    goal: {
                        required: "Goal Type is required."
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

        // This ensures that when the modal is shown, validation is re-triggered if needed
            $('#add_type').on('shown.bs.modal', function () {
                $('#addgoal').validate().resetForm();
                $('#addgoal')[0].reset();
                $('.form-control').removeClass('is-invalid');
            });

            // Reset validation and form when the modal is opened
            $('#edit_type').on('shown.bs.modal', function () {
                $('#editgoal').validate().resetForm();
            });

            $('#edit_type').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');

            // Fetch goal data and populate the modal
            $.get('goal/type/edit/' + id, function(data) {
                $('#goal_edit').val(data.goal);
                $('#description_edit').val(data.description);
                var updateUrl = "{{ route('goal.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                $('#editgoal').attr('action', updateUrl);
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
                    url: "{{ route('goal-statuschange') }}",  // Updated route name
                    data: { id: id, status: status },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                    if (data.status === 1) {
                        Swal.fire({
                            title: "Success",
                            text: "Goal-type status changed successfully.",
                            icon: "success"
                        }).then(()=>{
                            window.location.reload();
                        });
                    }else if (data.status === 2) {
                        Swal.fire({
                            title: "Success",
                            text: "Goal-type status changed successfully.",
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
                        console.error("Error updating Goal-type status:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to update Goal-type status.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

    function deletegoalType(id) {
        Swal.fire({
            title: "Are you sure you want to delete this Goal Type?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = "{{ route('goal.destroy', ':id') }}";
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
                            text: "Goal-type has been deleted.",
                            icon: "success"
                        }).then(() => {
                            // Remove the table row
                            $('#goaltype-row-' + id).remove();
                        });
                    },
                    error: function (error) {
                        console.error("Error deleting goal type:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to delete goal type.",
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