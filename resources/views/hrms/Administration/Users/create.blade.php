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
                    <li class="breadcrumb-item active">Add User</li>
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
            <h5 class="card-title">Add User</h5>
        </div>
        <div class="card-body">
            <div id="resultMessage" style="font-size:18px;color:red;"></div>
            <form id="createUser" novalidate>
                @csrf
                <div class="row">

                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="fname" value="{{old('fname')}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Last Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="lname" value="{{old('lname')}}" required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" value="{{old('email')}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Phone <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" value="{{old('phone')}}" name="phone">
                        </div>
                    </div>
                    <input type="hidden" name="creator" value="{{session('role')}}">
                    <div class="col-sm-6">
                        <div class="input-block mb-3 select-focus">
                            <label class="col-form-label">Role <span class="text-danger">*</span></label>
                            <select  class="select form-control" name="role">
                                <option value="">Select Role</option>
                                @if (count($roles) > 0)
                                    @foreach ($roles as $item)
                                        <option value="{{ $item }}">{{ ucwords(str_replace('_', ' ', $item)) }}</option>
                                    @endforeach   
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Password <span class="text-danger">*</span></label>
                            <input class="form-control" type="password" name="password">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <label class="col-form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input class="form-control" type="password" name="confirm_password">
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
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][read]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][write]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][create]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][delete]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][import]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="custom_check">
                                        <input type="checkbox" class="permission-checkbox" name="permissions[{{ $permission }}][export]">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>

                <div id="otpSection" style="display: none;">
                    <label for="otp">OTP:</label>
                    <input type="text" name="otp" id="otp" required style="width: 210px; padding: 7px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                
                <span id="otpMessage" style="color: green;"></span>

                <div class="text-start mt-3">
                    <button type="submit" id="save" class="btn btn-primary">Save</button>
                    <button type="button" id="sbmit" class="btn btn-primary" style="display: none;">Submit</button>                    
                    <button type="reset" class="btn btn-secondary">Cancel</button>
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
            },
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                equalTo: "[name='password']"
            },
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
            password: {
                required: "Please provide a password.",
                minlength: "Password must be at least 6 characters."
            },
            confirm_password: {
                required: "Please confirm your password.",
                equalTo: "Passwords do not match."
            }
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
                generateOTP(form);
            } else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select at least one permission.',
                });
            }
        }
    });

    function generateOTP(form) {
        $.ajax({
            url: '{{ route('users.store') }}',
            method: 'POST',
            data: $(form).serialize(),
            success: function(response) {
                $('#otpMessage').text('OTP has been sent successfully and is valid for only 3 minutes.').css('color', 'green');
                $('#otpSection').slideDown();
                $('#save').hide();
                $('#sbmit').show();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'An error occurred while generating OTP.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage || 'Something went wrong.',
                });
                $('#otpMessage').text(errorMessage).css('color', 'red');
            },
            complete: function() {
                $("#overlay").hide();
                $("#loader").hide(); // Hide the loader after the request is complete
            }
        });
    }

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

    $('#sbmit').on('click', function() {
        const otpValue = $('input[name="otp"]').val();

        if (otpValue.length !== 6 || isNaN(otpValue)) {
            $('#otpMessage').text('Please enter a valid 6-digit OTP.').css('color', 'red');
            return;
        }
        $("#overlay").show();
        $("#loader").show();
        submitUserData();
    });

    function submitUserData() {
        const userData = $('#createUser').serialize();

        $.ajax({
            url: '{{ route('submit-new-userdata') }}',
            method: 'POST',
            data: userData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(()=>{
                    window.location = '/users';
                });

            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'An error occurred while submitting data.';
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                $("#overlay").hide();
                $("#loader").hide(); // Hide the loader after the request is complete
            }
        });
    }
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