@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Training');
@endphp
@extends('layouts.index')
@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Training Management</h2>
        </div>
    </div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Trainer List</h4>

    <a href="{{ route('trainers.index') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Trainer
    </a>
</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Trainer</th>
                    <th>Training Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration (Hours)</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                </tr>
            </thead>
            <tbody>
                @forelse($employeesNeedingTraining as $employee)
                <tr id="employee-row-{{ $employee->id }}">
                    <td data-label="Employee">
                        <a class="high">
                            <div class="d-flex align-items-center">
                                @if($employee->profile_image)
                                    <img src="{{ asset($employee->profile_image) }}" alt="Profile Image"
                                         style="width: 30px; height:30px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                @else
                                    <div style="width: 30px; height: 30px; background-color: #f0f0f0; border-radius: 50%; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-user" style="color: #999;"></i>
                                    </div>
                                @endif
                                {{ $employee->firstname }} {{ $employee->lastname }}
                            </div>
                        </a>
                    </td>
                    <td data-label="Employee ID">{{ $employee->employeeid }}</td>
                    <td data-label="Trainer">{{ $employee->trainer_name ?? 'Not Assigned' }}</td>
                    <td data-label="Training Type">{{ $employee->training_type ?? 'Not Set' }}</td>
                    <td data-label="Start Date">{{ $employee->start_date ? \Carbon\Carbon::parse($employee->start_date)->format('d-m-Y') : 'Not Set' }}</td>
                    <td data-label="End Date">{{ $employee->end_date ? \Carbon\Carbon::parse($employee->end_date)->format('d-m-Y') : 'Not Set' }}</td>
                    <td data-label="Duration">{{ $employee->duration_hours ?? 'Not Set' }}</td>

                    <td data-label="Status">
                        <div class="dropdown action-label">
                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-regular fa-circle-dot 
                                   {{ strtolower($employee->training_status) === 'completed' ? 'text-success' : 
                                      (strtolower($employee->training_status) === 'in_progress' ? 'text-warning' : 'text-danger') }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $employee->training_status)) }}
                            </a>
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item update-status" data-id="{{ $employee->id }}" data-status="pending">
                                    <i class="fa-regular fa-circle-dot text-danger"></i> Pending
                                </a>
                                <a href="#" class="dropdown-item update-status" data-id="{{ $employee->id }}" data-status="in_progress">
                                    <i class="fa-regular fa-circle-dot text-warning"></i> In Progress
                                </a>
                                <a href="#" class="dropdown-item update-status" data-id="{{ $employee->id }}" data-status="completed">
                                    <i class="fa-regular fa-circle-dot text-success"></i> Completed
                                </a>
                            </div>
                        </div>
                    </td>

                    <td data-label="Feedback">
                        <a href="#" class="od-icon-btn feedback-btn" title="Submit Feedback" 
                           data-employee-id="{{ $employee->id }}" 
                           data-training-id="{{ $employee->id }}"
                           data-bs-toggle="modal" 
                           data-bs-target="#feedbackModal">
                            <i class="fa-solid fa-comment"></i>
                        </a>
                    </td>

                    <td data-label="Actions" class="text-end">
                        <div class="od-inline-actions">
                            @if(isset($permissions) && $permissions->can_edit)
                            <a href="{{ route('trainings.editEmployee', $employee->id) }}" class="od-icon-btn" title="Edit Training Details">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">No employees requiring training found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- SIMPLIFIED Feedback Modal Form -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Training Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div id="feedbackErrors" class="alert alert-danger d-none" role="alert"></div>
            
            <form id="feedbackForm" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- SIMPLIFIED Rating - Simple dropdown -->
                    <div class="mb-3">
                        <label for="feedbackRating" class="form-label">Rating</label>
                        <select class="form-control" id="feedbackRating" name="feedback_rating">
                            <option value="">-- Select Rating --</option>
                            <option value="1">⭐  - Poor</option>
                            <option value="2">⭐⭐  - Fair</option>
                            <option value="3">⭐⭐⭐  - Good</option>
                            <option value="4">⭐⭐⭐⭐  - Very Good</option>
                            <option value="5">⭐⭐⭐⭐⭐  - Excellent</option>
                        </select>
                    </div>

                    <!-- Comments -->
                    <div class="mb-3">
                        <label for="feedbackComments" class="form-label">Your Comments</label>
                        <textarea class="form-control" id="feedbackComments" name="feedback_comments" rows="3" 
                                  placeholder="Share your feedback about the training"></textarea>
                    </div>

                    <!-- Trainer Feedback -->
                    <div class="mb-3">
                        <label for="trainerFeedback" class="form-label">Trainer's Notes</label>
                        <textarea class="form-control" id="trainerFeedback" name="trainer_feedback" rows="2" 
                                  placeholder="Trainer's comments"></textarea>
                    </div>

                    <!-- Assessment Score -->
                    <div class="mb-3">
                        <label for="assessmentScore" class="form-label">Score (%)</label>
                        <input type="number" class="form-control" id="assessmentScore" name="assessment_score" 
                               placeholder="Enter score 0-100">
                    </div>

                    <!-- Certificate Status -->
                    <div class="mb-3">
                        <label for="certificateStatus" class="form-label">Certificate</label>
                        <select class="form-control" id="certificateStatus" name="certificate_status">
                            <option value="Not Issued">Not Issued</option>
                            <option value="Issued">Issued</option>
                            <option value="Revoked">Revoked</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitFeedbackBtn">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const updateStatusUrl = "{{ route('trainings.updateEmployeeStatus', ':id') }}";
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // FEEDBACK MODAL HANDLING
    // =========================
    document.querySelectorAll('.feedback-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const employeeId = this.getAttribute('data-employee-id');
            const trainingId = this.getAttribute('data-training-id');

            const form = document.getElementById('feedbackForm');
            const errorDiv = document.getElementById('feedbackErrors');

            if (!form) return console.error('Feedback form not found');

            // Clear errors
            errorDiv.classList.add('d-none');
            errorDiv.innerHTML = '';

            // Set form action
            form.action = `/trainings/${employeeId}/feedback/${trainingId}`;

            // Reset form
            form.reset();

            console.log('Opened feedback modal:', { employeeId, trainingId });
        });
    });

    // =========================
    // FEEDBACK FORM SUBMIT
    // =========================
    const feedbackForm = document.getElementById('feedbackForm');

    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function () {
            const submitBtn = document.getElementById('submitFeedbackBtn');
            const errorDiv = document.getElementById('feedbackErrors');

            errorDiv.classList.add('d-none');

            console.log('Submitting to:', this.action);

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
        });
    }

    // =========================
    // STATUS UPDATE (FIXED)
    // =========================
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

    if (!csrfTokenMeta) {
        console.error('CSRF token not found. Add <meta name="csrf-token">');
        return;
    }

    const csrfToken = csrfTokenMeta.getAttribute('content');

    document.querySelectorAll('.update-status').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();

            const employeeId = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');

            if (!employeeId || !status) {
                console.error('Missing data-id or data-status');
                return;
            }

            const url = updateStatusUrl.replace(':id', employeeId);

            console.log('Sending request to:', url, 'with status:', status);

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => {
                console.log('Raw response:', response);

                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status);
                }

                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    location.reload();
                } else {
                    alert('Update failed (server returned false)');
                }
            })
            .catch(error => {
                console.error('Status update failed:', error);
                alert('Something went wrong. Check console.');
            });
        });
    });

});
</script>

<style>
.od-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background-color: #f0f0f0;
    color: #555;
    text-decoration: none;
    transition: all 0.2s;
}

.od-icon-btn:hover {
    background-color: #007bff;
    color: white;
}

.od-inline-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}
</style>

@endsection