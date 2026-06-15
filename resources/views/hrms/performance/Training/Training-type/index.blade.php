@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('TrainingType');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 15px; padding-top: 10px;"> 
			
            <!-- Page Content -->
            <div class="content container-fluid"  style="max-width: 100%; padding-top: 10px;">
            
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Training Type</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Training Type</li>
                            </ul>
                        </div>
                        <div class="col-auto float-end ms-auto">
@if(isset($permissions) && $permissions->can_create)
                            <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_type"> Add Type</a>
@endif
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                <div class="row">
                    <div class="col-md-12">
       <div class="table-responsive">
    <table id="training-type-table" class="table custom-table datatable">
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
            @foreach ($trainingTypes as $type)
            <tr>
                <td data-label="Type Name"><a class="high">{{ $type->name }}</a></td>
                <td data-label="Description">{{ $type->description }}</td>
                <td data-label="Status">
                    <div class="dropdown action-label">
                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-circle-dot {{ $type->status == 'Active' ? 'text-success' : 'text-danger' }}"></i> {{ $type->status }}
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('training-type.change-status', [$type->id, 'Active']) }}">
                                <i class="fa-regular fa-circle-dot text-success"></i> Active
                            </a>
                            <a class="dropdown-item" href="{{ route('training-type.change-status', [$type->id, 'Inactive']) }}">
                                <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                            </a>
                        </div>
                    </div>
                </td>
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if(isset($permissions) && $permissions->can_edit)
                        <a class="od-icon-btn edit-btn" href="javascript:void(0);" 
                            data-id="{{ $type->id }}" 
                            data-name="{{ $type->name }}" 
                            data-description="{{ $type->description }}" 
                            data-status="{{ $type->status }}" 
                            data-bs-toggle="modal" data-bs-target="#edit_type"
                            title="Edit Type">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        @endif
                        @if(isset($permissions) && $permissions->can_delete)
                        <a class="od-icon-btn danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_type" data-id="{{ $type->id }}" title="Delete Type">
                            <i class="fa-regular fa-trash-can"></i>
                        </a>
                        @endif
                    </div>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Odoo-style checkbox script -->
<script>
$(document).ready(function(){
    // Select/Deselect all rows
    $('#checkAll').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    // Row highlight on checkbox toggle
    $('.row-check').on('change', function() {
        if($(this).is(':checked')){
            $(this).closest('tr').addClass('od-selected');
        } else {
            $(this).closest('tr').removeClass('od-selected');
        }
    });

    // Click row to toggle checkbox
    $('#training-type-table tbody tr').on('click', function(e){
        if(!$(e.target).is('input[type="checkbox"], button, a')){
            var checkbox = $(this).find('.row-check');
            checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
        }
    });
});
</script>

                    </div>
                </div>
            </div>
            <!-- /Page Content -->

            <!-- Add Training Type Modal -->
            <div id="add_type" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <form action="{{ route('training-type.store') }}" method="POST">
    @csrf
    <div class="input-block mb-3">
        <label class="col-form-label">Type <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="name">
    </div>
    <div class="input-block mb-3">
        <label class="col-form-label">Description <span class="text-danger">*</span></label>
        <textarea class="form-control" name="description" rows="4"></textarea>
    </div>
    <div class="input-block mb-3">
        <label class="col-form-label">Status</label>
        <select class="form-control" name="status">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
    <div class="submit-section">
        <button class="btn btn-primary submit-btn">Submit</button>
    </div>
</form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Training Type Modal -->
            
            <!-- Edit Training Type Modal -->
<div id="edit_type" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-training-type-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Type <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="name" id="edit_name">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Status</label>
                        <select class="form-control" name="status" id="edit_status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

            
        <!-- Delete Training Type Modal -->
<div class="modal custom-modal fade" id="delete_type" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Training Type</h3>
                    <p>Are you sure you want to delete?</p>
                </div>
               <form id="delete-training-type-form" method="POST" action="">
    @csrf
    @method('DELETE')

    

    <div class="row justify-content-center g-3">
        <div class="col-6 col-md-5">
            <button type="button" class="btn btn-outline-secondary w-100 py-2" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </button>
        </div>
        <div class="col-6 col-md-5">
            <button type="submit" class="btn btn-danger w-100 py-2">
                <i class="bi bi-trash3-fill me-1"></i> Delete
            </button>
        </div>
    </div>
</form>

            </div>
        </div>
    </div>
</div>

<!-- /Delete Training Type Modal -->
        
        </div>
        <!-- /Page Wrapper -->

        <script>
   $(document).on('click', '.edit-btn', function () {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var description = $(this).data('description');
    var status = $(this).data('status');

    // Use Laravel route() with placeholder replacement
    var url = "{{ route('training-type.update', ':id') }}".replace(':id', id);
    $('#edit-training-type-form').attr('action', url);

    // Populate form fields
    $('#edit_name').val(name);
    $('#edit_description').val(description);
    $('#edit_status').val(status);

    // Open the modal
    $('#edit_type').modal('show');
});


    $(document).on('click', '.dropdown-item[data-bs-target="#delete_type"]', function () {
    var id = $(this).data('id');
    $('#delete-training-type-form').attr('action', '/training-type/' + id);
});



    $(document).ready(function() {
        // Edit button handler
  $(document).on('click', '.edit-btn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var description = $(this).data('description');
    var status = $(this).data('status');

    // Generate URL from named route
    var url = "{{ route('training-type.update', ':id') }}".replace(':id', id);
    $('#edit-training-type-form').attr('action', url);

    // Fill the form
    $('#edit_name').val(name);
    $('#edit_description').val(description);
    $('#edit_status').val(status);
});

        // Delete button handler
        $(document).on('click', '[data-bs-target="#delete_type"]', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = "{{ route('training-type.destroy', ':id') }}".replace(':id', id);
            $('#delete-training-type-form').attr('action', url);
        });

        // AJAX form submission for delete
        $('#delete-training-type-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            $.ajax({
                type: 'POST',
                url: url,
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#delete_type').modal('hide');
                        // Show success message
                        alert('Training type deleted successfully');
                        // Reload the page
                        location.reload();
                    } else {
                        alert(response.message || 'Error deleting training type');
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        });
    });
</script>
@endsection