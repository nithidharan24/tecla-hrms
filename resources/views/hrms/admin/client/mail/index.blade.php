<!DOCTYPE html>
<html lang="en">

@include('includes.head')

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="mb-0">Welcome to Our Platform!</h4> --}}
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Dear {{ ucFirst($firstName ) }},<br><br>
                            Thank you for creating an account with us! We are thrilled to have you on board.
                        </p>
                        <p>
                            Your account has been successfully created. Here are your login details:
                        </p>
                        <div class="alert alert-info">
                            <strong>Username:</strong> {{ $username  }}<br>
                            <strong>Password:</strong> {{ $password  }}
                        </div>
                        <p>
                            With your account, you will have full transparency on your projects. You can monitor progress, manage tasks, and communicate with our team directly.
                        </p>
                        <p>If you have any questions or need assistance, feel free to contact our support team.</p>
                        <p>Best Regards,<br> Support Team</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
