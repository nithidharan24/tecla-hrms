@extends('layouts.index')
@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Roles</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_type">Add Role</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">

            @if (Session::has('messageType') && Session::has('message'))
                <div class="alert alert-{{ Session::get('messageType') == 'success' ? 'success' : 'danger' }}" id="{{ Session::get('messageType') }}-alert">
                    {{ Session::get('message') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Role Name</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $item)
                            <tr id="item-row-{{$item->id}}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $item->role_name)) }}</td>
                                <td>
                                    <div class="dropdown action-label">
                                        <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fa-regular fa-circle-dot {{ $item->status == 'active' ? 'text-success' : 'text-danger' }}"></i> 
                                            {{ ucfirst($item->status) }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item {{ $item->status == 'active' ? 'disabled' : '' }}" onclick="statusChange('{{ $item->id }}', 'active')">
                                                <i class="fa-regular fa-circle-dot text-success"></i> Active
                                            </button>
                                            <button class="dropdown-item {{ $item->status == 'inactive' ? 'disabled' : '' }}" onclick="statusChange('{{ $item->id }}', 'inactive')">
                                                <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#edit_type"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                            <button class="dropdown-item" onclick="deleteData('{{ $item->id }}')"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</button>
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

<!-- Add Role Modal -->
<div id="add_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-type" method="POST" action="{{ route('add-user-role') }}">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label">Role Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="roleName" id="role-name" type="text">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="edit_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-type" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Role Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="edit_roleName" id="roleName_edit" type="text">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    setTimeout(() => $("#success-alert, #error-alert").fadeTo(500, 0).slideUp(500, function() { $(this).remove(); }), 3000);

    $(document).ready(function () {
        $.validator.addMethod("customName", function (value) {
            return /^[^\s].{0,29}$/.test(value);
        }, "No leading spaces, max 30 chars.");

        $("#add-type").validate({
            rules: {
                roleName: { required: true, customName: true }
            },
            messages: {
                roleName: { required: "Role name is required." }
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

        $("#edit-type").validate({
            rules: {
                edit_roleName: { required: true, customName: true }
            },
            messages: {
                edit_roleName: { required: "Role name is required." }
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

        $('#add_type').on('shown.bs.modal', function () {
            $('#add-type').validate().resetForm();
            $('#add-type')[0].reset();
            $('.form-control').removeClass('is-invalid');
        });

        $('#edit_type').on('show.bs.modal', function (e) {
            let id = $(e.relatedTarget).data('id');
          $.get('{{ url("/fetch/user-role") }}/' + id + '/data', function(data) {
                $('#roleName_edit').val(data.role);
                let updateUrl = "{{ route('update-role-data', ':id') }}".replace(':id', id);
                $('#edit-type').attr('action', updateUrl);
            });
        });
    });

    function statusChange(id, status) {
        Swal.fire({
            title: "Are you sure to change the status?", 
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "PUT",
                    url: "{{ route('update-role-statuschange') }}",
                    data: { id, status },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: data => {
                        Swal.fire({
                            title: data.status === 1 ? "Success" : "Error",
                            text: data.status === 1 ? "Status changed successfully." : data.message,
                            icon: data.status === 1 ? "success" : "error"
                        }).then(() => {
                            if (data.status === 1) window.location.reload();
                        });
                    },
                    error: () => {
                        Swal.fire("Error", "Failed to update status.", "error");
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
            cancelButtonText: "No"
        }).then(result => {
            if (result.isConfirmed) {
                let url = "{{ route('delete-role-date', ':id') }}".replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: () => {
                        Swal.fire("Deleted!", "Deleted Successfully.", "success").then(() => {
                            $('#item-row-' + id).remove();
                        });
                    },
                    error: () => {
                        Swal.fire("Error", "Failed to delete.", "error");
                    }
                });
            }
        });
    }

    @if ($errors->any())
        let errorMessage = `{{ implode('\n', $errors->all()) }}`;
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            text: errorMessage,
        });
    @endif
</script>

@endsection
