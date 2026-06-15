@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Trainers');        
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px;padding-top: 0;">
    <!-- Page Content -->
    <div class="content container-fluid" style="max-width: 100%;padding-top: 25px;">
        <!-- Page Header -->
        <div class="page-header" style="margin-bottom: 10px;">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Trainers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Trainers</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('trainers.create') }}" class="btn btn-primary">
                        Add New 
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-12">
             <div class="table-responsive">
    <table id="trainers-table" class="table custom-table datatable">
        <thead>
            <tr>
               
                <th>Trainer Name</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Description</th>
                <th>Status</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach($trainers as $trainer)
            <tr>
                
                <td data-label="Name">{{ $trainer->first_name }} {{ $trainer->last_name }}</td>
                <td data-label="Phone">{{ $trainer->phone }}</td>
                <td data-label="Email"><a href="mailto:{{ $trainer->email }}">{{ $trainer->email }}</a></td>
                <td data-label="Description">{{ $trainer->description }}</td>
                <td data-label="Status">
                    <div class="dropdown action-label">
                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-circle-dot {{ $trainer->status == 'Active' ? 'text-success' : 'text-danger' }}"></i> {{ $trainer->status }}
                        </a>
                        <div class="dropdown-menu">
                            <form action="{{ route('trainers.updateStatus', $trainer->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Active">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa-regular fa-circle-dot text-success"></i> Active
                                </button>
                            </form>
                            <form action="{{ route('trainers.updateStatus', $trainer->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Inactive">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if(isset($permissions) && $permissions->can_edit)
                        <a href="{{ route('trainers.edit', $trainer->id) }}" class="od-icon-btn" title="Edit">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        @endif
                        @if(isset($permissions) && $permissions->can_delete)
                        <form action="{{ route('trainers.destroy', $trainer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="od-icon-btn danger" title="Delete" onclick="return confirm('Are you sure you want to delete this trainer?');">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Odoo-style checkbox row highlight script -->
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
    $('#trainers-table tbody tr').on('click', function(e){
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
</div>
<!-- /Page Wrapper -->

<script>
    $(document).ready(function () {
        $('#edit_type').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var trainerId = button.data('trainer-id');

            $.get('/trainers/' + trainerId, function (data) {
                $('#edit_type [name="first_name"]').val(data.first_name);
                $('#edit_type [name="last_name"]').val(data.last_name);
                $('#edit_type [name="role"]').val(data.role);
                $('#edit_type [name="email"]').val(data.email);
                $('#edit_type [name="phone"]').val(data.phone);
                $('#edit_type [name="status"]').val(data.status);
                $('#edit_type [name="description"]').val(data.description);

                $('#edit_type form').attr('action', '/trainers/' + trainerId);
            });
        });

        $('#delete_type').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var trainerId = button.data('trainer-id'); 

            var actionUrl = '/trainers/' + trainerId;
            $('#deleteTrainerForm').attr('action', actionUrl);
        });
    });
</script>
@endsection
