@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Application Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('resume.index') }}">Job Applications</a></li>
                    <li class="breadcrumb-item active">Application Details</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('resume.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Applications
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Candidate Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Candidate Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <p class="form-control-static">{{ $application->first_name }} {{ $application->last_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <p class="form-control-static">{{ $application->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <p class="form-control-static">
                                    <a href="tel:{{ $application->phone }}">{{ $application->phone }}</a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">LinkedIn Profile</label>
                                <p class="form-control-static">
                                    @if($application->linkedin)
                                        <a href="{{ $application->linkedin }}" target="_blank" class="text-primary">
                                            <i class="fab fa-linkedin"></i> View Profile
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Years of Experience</label>
                                <p class="form-control-static">{{ $application->years_experience ?? 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Expected Salary</label>
                                <p class="form-control-static">{{ $application->expected_salary ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase"></i> Job Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Job Title</label>
                                <p class="form-control-static">{{ $application->job_title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Department</label>
                                <p class="form-control-static">{{ $application->department }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Job Type</label>
                                <p class="form-control-static">{{ ucfirst($application->job_type) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <p class="form-control-static">{{ $application->job_location }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Salary Range</label>
                                <p class="form-control-static">${{ number_format($application->salary_from) }} - ${{ number_format($application->salary_to) }}</p>
                            </div>
                        </div>
                        @if($application->skills)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Required Skills</label>
                                <div class="mt-2">
                                    @foreach(explode(',', $application->skills) as $skill)
                                        <span class="badge bg-primary me-1">{{ trim($skill) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Employee Information (if converted) -->
            @if($employeeInfo)
            <div class="card mt-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-badge"></i> Employee Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Employee ID</label>
                                <p class="form-control-static"><strong>{{ $employeeInfo->employeeid }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <p class="form-control-static">{{ $employeeInfo->username }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Department</label>
                                <p class="form-control-static">{{ $employeeInfo->department_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Designation</label>
                                <p class="form-control-static">{{ $employeeInfo->designation_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Joining Date</label>
                                <p class="form-control-static">{{ \Carbon\Carbon::parse($employeeInfo->joiningdate)->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Employee Status</label>
                                <p class="form-control-static">
                                    <span class="badge bg-success">{{ ucfirst($employeeInfo->status) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('employee.show', $employeeInfo->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Employee Profile
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cover Letter -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Cover Letter
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <p class="form-control-static" style="white-space: pre-wrap;">{{ $application->cover_letter }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Application Status -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Application Status
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge badge-{{
                            $application->status == 'pending' ? 'warning' :
                            ($application->status == 'reviewed' ? 'info' :
                            ($application->status == 'shortlisted' ? 'secondary' :
                            ($application->status == 'interviewed' ? 'primary' :
                            ($application->status == 'hired' ? 'success' : 'danger'))))
                        }} p-3 fs-6">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    <p class="text-muted">Applied on {{ \Carbon\Carbon::parse($application->applied_at)->format('F d, Y \a\t H:i') }}</p>
                    
                    @if($application->converted_to_employee)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Converted to Employee</strong>
                        </div>
                    @endif

                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#update_status">
                        <i class="fas fa-edit"></i> Update Status
                    </button>
                </div>
            </div>

            <!-- Resume -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-pdf"></i> Resume
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($application->resume_path)
                        <div class="mb-3">
                            <i class="fas fa-file-pdf text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <a href="{{ route('resume.view-resume', $application->id) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> View Resume
                        </a>
                        <a href="{{ route('resume.view-resume', $application->id) }}?download=1" class="btn btn-outline-secondary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    @else
                        <p class="text-muted">No resume uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#send_email_modal">
                            <i class="fas fa-envelope"></i> Send Email
                        </button>
                        <a href="tel:{{ $application->phone }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone"></i> Call Candidate
                        </a>
                        @if($application->linkedin)
                        <a href="{{ $application->linkedin }}" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fab fa-linkedin"></i> View LinkedIn
                        </a>
                        @endif
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_application">
                            <i class="fas fa-trash"></i> Delete Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="update_status" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Application Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resume.update-status', $application->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Candidate: <strong>{{ $application->first_name }} {{ $application->last_name }}</strong></label>
                    </div>
                    <div class="form-group mb-3">
                        <label>Job Title: <strong>{{ $application->job_title }}</strong></label>
                    </div>
                    <div class="form-group mb-3">
                        <label>Current Status: 
                            <span class="badge badge-{{
                                $application->status == 'pending' ? 'warning' :
                                ($application->status == 'reviewed' ? 'info' :
                                ($application->status == 'shortlisted' ? 'secondary' :
                                ($application->status == 'interviewed' ? 'primary' :
                                ($application->status == 'hired' ? 'success' : 'danger'))))
                            }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="status">New Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="interviewed" {{ $application->status == 'interviewed' ? 'selected' : '' }}>Interviewed</option>
                            <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>
                                Hired (Will create employee record)
                            </option>
                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Note:</strong> Selecting "Hired" will automatically create an employee record and send login credentials.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="send_email_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope"></i> Send Email to {{ $application->first_name }} {{ $application->last_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="emailForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_type">Email Type</label>
                                <select name="email_type" id="email_type" class="form-control" required>
                                    <option value="">Select Email Type</option>
                                    <option value="general">General Communication</option>
                                    <option value="interview">Interview Invitation</option>
                                    <option value="offer">Job Offer</option>
                                    <option value="rejection">Application Rejection</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Recipient</label>
                                <input type="text" class="form-control" value="{{ $application->email }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="8" required></textarea>
                        <small class="text-muted">
                            You can use placeholders: {candidate_name}, {job_title}, {department}
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Preview:</strong> The email will be sent to <strong>{{ $application->email }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                        <i class="fas fa-paper-plane"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_application" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Delete Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5 text-muted">
                    Are you sure you want to delete this job application?
                </p>
                <p class="text-muted">
                    <strong>{{ $application->first_name }} {{ $application->last_name }}</strong> - {{ $application->job_title }}
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle"></i> Cancel
                </button>
                <form action="{{ route('resume.destroy', $application->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Set up CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Email type change handler
    $('#email_type').on('change', function() {
        const emailType = $(this).val();
        if (emailType) {
            // Load email template
            $.get(`{{ route('resume.email-template', '') }}/${emailType}`, function(template) {
                $('#subject').val(template.subject);
                $('#message').val(template.message);
            });
        }
    });

    // Email form submission
    $('#emailForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            email_type: $('#email_type').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };

        // Show loading state
        $('#sendEmailBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

        // Send email
        $.ajax({
            url: '{{ route("resume.send-email", $application->id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert('✅ ' + response.message);
                    $('#send_email_modal').modal('hide');
                    
                    // Reset form
                    $('#emailForm')[0].reset();
                } else {
                    alert('❌ ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send email. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('❌ ' + errorMessage);
            },
            complete: function() {
                // Reset button state
                $('#sendEmailBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Email');
            }
        });
    });

    // Reset form when modal is closed
    $('#send_email_modal').on('hidden.bs.modal', function() {
        $('#emailForm')[0].reset();
    });
});
</script>

@endsection
