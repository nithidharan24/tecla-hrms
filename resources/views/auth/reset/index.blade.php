<!DOCTYPE html>
<html lang="en">

@include('includes.head')

<body class="account-page">
    @php
        $resolvedId = $id
            ?? (request()->route('id') ?? request()->get('id'))
            ?? (Session::get('role') === 'admin' ? Session::get('admin_id') : Session::get('user_id'));

        $resolvedToken = $token
            ?? (request()->route('token') ?? request()->get('token'));
    @endphp

    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <div class="account-logo">
                    <a><img src="{{ asset('admin/assets/img/company.png') }}" alt="Tecla"></a>
                </div>

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Change Password</h3>

                        @if (empty($resolvedId))
                            <div class="alert alert-danger">
                                We couldn’t determine your account. Please open the reset link from your email again or log in and try from the profile menu.
                            </div>
                        @else
                            <form id="passwordForm" method="POST" action="{{ route('confirm-change-password', $resolvedId) }}">
                                @csrf
                                <div class="input-block mb-3">
                                    <label class="col-form-label">New password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control">
                                </div>

                                <input type="hidden" class="form-control" value="{{ $resolvedToken }}" name="pass" id="pass" />

                                <div class="input-block mb-3">
                                    <label class="col-form-label">Confirm password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                                </div>
                                <div class="submit-section mb-4">
                                    <button type="submit" class="btn btn-primary submit-btn">Update Password</button>
                                </div>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.scripts')

    <script>
        $(document).ready(function() {
            $("#passwordForm").validate({
                rules: {
                    new_password: {
                        required: true,
                        minlength: 8
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#new_password"
                    }
                },
                errorPlacement: function(error, element) {
                    error.css("color", "red");
                    error.insertAfter(element);
                },
                messages: {
                    new_password: "Password must be at least 8 characters.",
                    confirm_password: "Passwords do not match."
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