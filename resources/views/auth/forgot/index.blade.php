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
                        <h3 class="account-title">Forgot Password?</h3>
                        <p class="account-subtitle">Enter your email to get a password reset link</p>
                        
                        <!-- Account Form -->
                        <form id="resetPasswordForm" method="post" action="{{route('forgot-password.submit')}}">
                            @csrf
                            <div class="input-block mb-4">
                                <label class="col-form-label">Email Address</label>
                                <input class="form-control" id="email" name="email" type="text">
                            </div>
                            <div class="input-block mb-4 text-center">
                                <button class="btn btn-primary account-btn" type="submit">Reset Password</button>
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
    $("#resetPasswordForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: {
                required: "Email is required.",
                email: "Please enter a valid email address."
            }
        },
        errorClass: "text-danger",
        errorElement: "span",
        submitHandler: function(form) {
            form.submit(); 
        }
    });
});

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

        @if (Session::get('error'))
        Swal.fire({
            title: "{{ Session::get('error') }}",
            text: "",
            icon: "error"
        });
        @endif

</script>
</body>
</html>