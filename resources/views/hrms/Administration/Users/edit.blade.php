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
                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

</div>
<!-- /Page Content -->

<!-- Add Form -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit User</h5>
        </div>
        <div class="card-body">
            <div id="resultMessage" style="font-size:18px;color:red;"></div>
            <form id="createUser" method="POST" action="{{route('users.update',$userDetails->user_id)}}"  novalidate>
                @csrf
                @method('PUT')
                <div class="row">

                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="fname" value="{{old('fname',$userDetails->first_name)}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Last Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="lname" value="{{old('lname',$userDetails->last_name)}}" required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" value="{{old('email',$userDetails->email)}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Phone <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" value="{{old('phone',$userDetails->phone)}}" name="phone">
                        </div>
                    </div>
                    <input type="hidden" name="who" value="{{session('role')}}">
                    <div class="col-sm-6">
                        <div class="input-block mb-3 select-focus">
                            <label class="col-form-label">Role <span class="text-danger">*</span></label>
                            <select  class="select form-control" name="role">
                                <option value="">Select Role</option>
                                @if (count($roles) > 0)
                                    @foreach ($roles as $item)
                                        <option value="{{ $item }}" {{$userDetails->role==$item ? 'selected':''}}>{{ ucwords(str_replace('_', ' ', $item)) }}</option>
                                    @endforeach  
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive m-t-15">
                    <span id="customPermissionError" style="color: red;"></span>
                    <table class="table table-striped custom-table datatable">
                        <thead>
                            <tr>
                                <th>Module Permission <span class="text-danger">*</span></th>
                                <th class="text-center">All</th>
                                <th class="text-center">Read</th>
                                <th class="text-center">Write</th>
                                <th class="text-center">Create</th>
                                <th class="text-center">Delete</th>
                                <th class="text-center">Import</th>
                                <th class="text-center">Export</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr>
                                <td>{{ ucfirst($permission) }}</td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="module-all" data-permission="{{ $permission }}">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][read]" {{ isset($userPermissions[$permission]['read']) && $userPermissions[$permission]['read'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][write]" {{ isset($userPermissions[$permission]['write']) && $userPermissions[$permission]['write'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][create]" {{ isset($userPermissions[$permission]['create']) && $userPermissions[$permission]['create'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][delete]" {{ isset($userPermissions[$permission]['delete']) && $userPermissions[$permission]['delete'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][import]" {{ isset($userPermissions[$permission]['import']) && $userPermissions[$permission]['import'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][export]" {{ isset($userPermissions[$permission]['export']) && $userPermissions[$permission]['export'] === 'on' ? 'checked' : '' }}>
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                         
                        </tbody>
                    </table>
                </div>

                <div class="text-start mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>                 
                </div>
            </form>
        </div>
    </div>
</div>
<div id="overlay" style="display: none;"></div>
<div id="loader" style="display: none; text-align: center;">
    <svg class="pl" width="240" height="240" viewBox="0 0 240 240">
        <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 660" stroke-dashoffset="-330" stroke-linecap="round"></circle>
        <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 220" stroke-dashoffset="-110" stroke-linecap="round"></circle>
        <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
        <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
    </svg>
</div>

<script>

    
$(document).ready(function() {

    function countCheckedPermissions() {
        return document.querySelectorAll('.permission-checkbox:checked').length;
    }

    $("#createUser").validate({
        rules: {
            fname:{
                required:true,
            },
            lname:{
                required:true,
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            role: {
                required: true
            }
        },
        messages: {
            fname:{
                required:"First name required.",
            },
            lname:{
                required:"Last name required.",
            },
            email: {
                required: "Please enter an email address.",
                email: "Please enter a valid email address."
            },
            phone: {
                required: "Please enter a phone number.",
                digits: "Please enter only digits.",
                minlength: "Phone number must be at least 10 digits.",
                maxlength: "Phone number must not exceed 15 digits."
            },
            role: "Please select a role.",
        },
        errorElement: "span",
        errorClass: "text-danger",
        errorPlacement: function(error, element) {
            if (element.attr("name") === "role") {
                error.insertAfter(element.closest('.select-focus'));
            }else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            // Call function to generate OTP
            if(countCheckedPermissions()!==0){
                $("#overlay").show();
                $("#loader").show();
                form.submit();
            } else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select at least one permission.',
                });
            }
        }
    });

    $('.permission-checkbox').change(function() {
        const $row = $(this).closest('tr');
        const allCheckbox = $row.find('.module-all');

        if ($(this).is(':checked')) {
            if ($row.find('.permission-checkbox:checked').length === $row.find('.permission-checkbox').length) {
                allCheckbox.prop('checked', true);
            }
        } else {
            allCheckbox.prop('checked', false);
        }
    });

    $('.module-all').change(function() {
        const $row = $(this).closest('tr');
        const isChecked = $(this).is(':checked');
        
        $row.find('.permission-checkbox').prop('checked', isChecked);
    });

});
</script>

<script>

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