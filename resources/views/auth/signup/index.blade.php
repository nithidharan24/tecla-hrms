<!DOCTYPE html>
<html lang="en">

@include('includes.head')

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">
            
                <!-- Account Logo -->
                <div class="account-logo">
                    <a><img src="{{asset('admin/assets/img/company.png')}}" alt="Tecla"></a>
                </div>
                <!-- /Account Logo -->
                
                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Register</h3>
                        <p class="account-subtitle">Access to our dashboard</p>
                        
                        <!-- Account Form -->
                        <form id="registrationForm" method="POST" action="{{ route('create-new-account') }}">
                            @csrf
                            <div class="input-block mb-4">
                                <label class="col-form-label">Email<span class="mandatory">*</span></label>
                                <input class="form-control" type="text" name="email" id="email">
                            </div>
                            <div class="input-block mb-4">
                                <label class="col-form-label">Password<span class="mandatory">*</span></label>
                                <input class="form-control" type="password" name="password" id="password">
                            </div>
                            <div class="input-block mb-4">
                                <label class="col-form-label">Repeat Password<span class="mandatory">*</span></label>
                                <input class="form-control" type="password" name="repeat_password" id="repeat_password">
                            </div>
                            <div class="input-block mb-4 text-center">
                                <button class="btn btn-primary account-btn" type="submit">Register</button>
                            </div>
                            <div class="account-footer">
                                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
                            </div>
                        </form>
                        <!-- /Account Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@include('includes.scripts')
<script>
    $(document).ready(function() {
        $("#registrationForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 8
                },
                repeat_password: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address.",
                    remote: "This email is already taken."
                },
                password: {
                    required: "Please provide a password.",
                    minlength: "Your password must be at least 8 characters long."
                },
                repeat_password: {
                    required: "Please repeat your password.",
                    equalTo: "Passwords do not match."
                }
            },
            errorClass: "error",
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<style>
    .mandatory{
        color: red;
    }
</style>
<script>
    @if ($errors->any())
        var errorMessage = '';
        @foreach ($errors->all() as $error)
            errorMessage += "{{ $error }}\n";
        @endforeach
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            text: errorMessage,
            confirmButtonText: 'OK'
        });
    @endif

    @if (session('messageType') && session('message'))
        Swal.fire({
            icon: '{{ session('messageType') }}',
            title: 'Notification',
            text: '{{ session('message') }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
</body>
</html>