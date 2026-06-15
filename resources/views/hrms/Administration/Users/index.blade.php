@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Users</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{route('users.create')}}" class="btn add-btn"> Add Users</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form method="GET" action="{{ route('users.index') }}">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-4">  
                <div class="input-block mb-3 form-focus">
                    <input type="text" name="name" class="form-control floating" value="{{ request('name') }}">
                    <label class="focus-label">Name</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-4"> 
                <div class="input-block mb-3 form-focus select-focus">
                    <select name="role" class="select floating"> 
                        <option value="">Select Role</option>
                        @if (count($roles) > 0)
                            @foreach ($roles as $item)
                                <option value="{{ $item }}" {{ request('role') == $item ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $item)) }}
                                </option>
                            @endforeach  
                        @endif
                    </select>
                    <label class="focus-label">Role</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">  
                <button type="submit" class="btn btn-success w-100">Search</button>  
            </div>     
        </div>
    </form>
    
    
    <!-- /Search Filter -->

</div>
<!-- /Page Content -->

<div class="container-fluid">
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
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created Date</th>
                                    <th>Role</th>
                                    <th>Reset Credentials</th>
                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($users) > 0)
                                    @foreach ($users as $item)
                                        <tr id="items-row-{{$item->user_id}}">
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a >{{ucFirst($item->user_name)}} <span>{{ ucwords(str_replace('_', ' ', $item->role)) }}</span></a>
                                                </h2>
                                            </td>
                                            <td><a href="mailto:{{$item->email}}" class="__cf_email__" data-cfemail="b4d0d5daddd1d8c4dbc6c0d1c6f4d1ccd5d9c4d8d19ad7dbd9">[email&#160;protected]</a></td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('d:m:y', $item->created_date)->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge bg-inverse-success"> {{ $item->role === 'master_admin' ? 'Admin' : ucfirst($item->role) }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary" onclick="sendTemporaryPassword('{{$item->user_id}}')">Reset</button>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{route('users.edit',$item->user_id)}}" ><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                                        <button class="dropdown-item"  onclick="deleteData('{{$item->user_id}}')"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

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
            var url = "{{ route('users.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Deleted Successfully!",
                        icon: "success"
                    }).then(() => {
                        $('#items-row-' + id).remove();
                    });
                },
                error: function(error) {
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

function sendTemporaryPassword(userId) {
    $.ajax({
        url: "{{ route('set-user-send.email', ':id') }}".replace(':id', userId),
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to send temporary password.',
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