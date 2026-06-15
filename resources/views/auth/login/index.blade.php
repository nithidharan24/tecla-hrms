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
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to our dashboard</p>
                        
                        <!-- Account Form -->
                        <form action="{{ route('login.submit') }}" method="POST" novalidate>
                            @csrf
                            <div class="input-block mb-4">
                                <label class="col-form-label">Email Address</label>
                                <input class="form-control" type="text" value="{{old('email')}}" name="email" >
                            </div>
                            <div class="input-block mb-4">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <label class="col-form-label">Password</label>
                                    </div>
                                    <div class="col-auto">
                                        <a class="text-muted" href="{{route('forgot-password')}}">
                                            Forgot password?
                                        </a>
                                    </div>
                                </div>
                                <div class="position-relative">
                                    <input class="form-control" name="password" type="password" id="password">
                                    <span class="fa-solid fa-eye-slash" id="toggle-password"></span>
                                </div>
                            </div>
                            <div class="input-block mb-4 text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
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
        $("form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                }
            },
            messages: {
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please provide a password",
                }
            },
            errorElement: "label",
            errorPlacement: function(error, element) {
                error.css('color', 'red'); 
                error.insertAfter(element);
            }
        });
    });

    @if (session('messageType') && session('message'))
        Swal.fire({
            icon: '{{ session('messageType') }}',
            title: 'Notification',
            text: '{{ session('message') }}',
            confirmButtonText: 'OK'
        });
    @endif

    @if (Session::get('success'))
        Swal.fire({
            position: "center",
            icon: "success",
            title: "{{ Session::get('success') }}",
            showConfirmButton: false,
            timer: 2500
        });
    @endif

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

</script>
</body>
</html>