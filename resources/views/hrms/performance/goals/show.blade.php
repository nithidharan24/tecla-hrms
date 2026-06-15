@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Goal Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('goals.index') }}">Goals</a></li>
                    <li class="breadcrumb-item active">Goal Details</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('goals.edit', $goal->id) }}" class="btn btn-primary">
                    <i class="fa fa-pencil"></i> Edit Goal
                </a>
                <a href="{{ route('goals.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Goals
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $goal->goal_title }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Goal Type:</strong>
                                <span class="badge bg-info-light">{{ $goal->goal_type }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Category:</strong>
                                <span>{{ $goal->category }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Assigned To:</strong>
                                <span>{{ $goal->assigned_to_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Assigned By:</strong>
                                <span>{{ $goal->assigned_by_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Department:</strong>
                                <span>{{ $goal->department_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Priority:</strong>
                                <span class="badge 
                                    @if($goal->priority == 'High') bg-danger
                                    @elseif($goal->priority == 'Medium') bg-warning
                                    @elseif($goal->priority == 'Critical') bg-dark
                                    @else bg-info
                                    @endif">
                                    {{ $goal->priority }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Start Date:</strong>
                                <span>{{ \Carbon\Carbon::parse($goal->start_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>End Date:</strong>
                                <span>{{ \Carbon\Carbon::parse($goal->end_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Target Value:</strong>
                                <span>{{ number_format($goal->target_value, 2) }} {{ $goal->unit }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Current Value:</strong>
                                <span>{{ number_format($goal->current_value, 2) }} {{ $goal->unit }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Weightage:</strong>
                                <span>{{ $goal->weightage }}%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Review Cycle:</strong>
                                <span>{{ $goal->review_cycle }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="info-item mb-3">
                        <strong>Progress:</strong>
                        <div class="progress mt-2" style="height: 12px;">
                            <div class="progress-bar 
                                @if($goal->progress_percentage >= 80) bg-success
                                @elseif($goal->progress_percentage >= 50) bg-warning
                                @else bg-danger
                                @endif" 
                                role="progressbar" 
                                style="width: {{ $goal->progress_percentage }}%"
                                aria-valuenow="{{ $goal->progress_percentage }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ number_format($goal->progress_percentage, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <strong>Status:</strong>
                        <span class="badge 
                            @if($goal->status == 'Completed') bg-success
                            @elseif($goal->status == 'In Progress') bg-primary
                            @elseif($goal->status == 'On Hold') bg-warning
                            @elseif($goal->status == 'Cancelled') bg-danger
                            @else bg-secondary
                            @endif">
                            {{ $goal->status }}
                        </span>
                    </div>

                    <div class="info-item mb-3">
                        <strong>Goal Description:</strong>
                        <p class="mt-2">{{ $goal->goal_description }}</p>
                    </div>

                    @if($goal->remarks)
                    <div class="info-item">
                        <strong>Remarks:</strong>
                        <p class="mt-2">{{ $goal->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ratings Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Ratings</h4>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <strong>Self Rating:</strong>
                        <div class="mt-2">
                            @if($goal->self_rating)
                                <span class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ $i <= $goal->self_rating ? ' text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                </span>
                                <span class="ms-2">({{ number_format($goal->self_rating, 1) }}/5)</span>
                            @else
                                <span class="text-muted">Not rated yet</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <strong>Manager Rating:</strong>
                        <div class="mt-2">
                            @if($goal->manager_rating)
                                <span class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ $i <= $goal->manager_rating ? ' text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                </span>
                                <span class="ms-2">({{ number_format($goal->manager_rating, 1) }}/5)</span>
                            @else
                                <span class="text-muted">Not rated yet</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <strong>Overall Rating:</strong>
                        <div class="mt-2">
                            @if($goal->overall_rating)
                                <span class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ $i <= $goal->overall_rating ? ' text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                </span>
                                <span class="ms-2">({{ number_format($goal->overall_rating, 1) }}/5)</span>
                            @else
                                <span class="text-muted">Not calculated yet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary update-progress-btn" data-id="{{ $goal->id }}">
                            <i class="fa fa-refresh"></i> Update Progress
                        </button>
                        <button type="button" class="btn btn-warning update-ratings-btn" data-id="{{ $goal->id }}">
                            <i class="fa fa-star"></i> Update Ratings
                        </button>
                        <button type="button" class="btn btn-info change-status-btn" data-id="{{ $goal->id }}" data-status="{{ $goal->status }}">
                            <i class="fa fa-edit"></i> Change Status
                        </button>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Timeline</h4>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Goal Created</h6>
                                <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($goal->created_at)->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($goal->updated_at)->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal custom-modal fade" id="updateProgressModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Goal Progress</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateProgressForm">
                    @csrf
                    <input type="hidden" id="goal_id" name="goal_id">
                    <div class="input-block mb-3">
                        <label class="col-form-label">Current Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="current_value" name="current_value" value="{{ $goal->current_value }}" required>
                        <small class="form-text text-muted">Target Value: {{ $goal->target_value }} {{ $goal->unit }}</small>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control select" id="status" name="status" required>
                            <option value="Not Started" {{ $goal->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                            <option value="In Progress" {{ $goal->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="On Hold" {{ $goal->status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="Completed" {{ $goal->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ $goal->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update Progress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Ratings Modal -->
<div class="modal custom-modal fade" id="updateRatingsModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Goal Ratings</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateRatingsForm">
                    @csrf
                    <input type="hidden" id="rating_goal_id" name="goal_id">
                    <div class="input-block mb-3">
                        <label class="col-form-label">Self Rating (0-5)</label>
                        <input type="number" step="0.1" min="0" max="5" class="form-control" id="self_rating" name="self_rating" value="{{ $goal->self_rating }}" placeholder="Enter self rating">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Manager Rating (0-5)</label>
                        <input type="number" step="0.1" min="0" max="5" class="form-control" id="manager_rating" name="manager_rating" value="{{ $goal->manager_rating }}" placeholder="Enter manager rating">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update Ratings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Update Progress
        $('.update-progress-btn').on('click', function() {
            var goalId = $(this).data('id');
            $('#goal_id').val(goalId);
            $('#updateProgressModal').modal('show');
        });

        $('#updateProgressForm').on('submit', function(e) {
            e.preventDefault();
            var goalId = $('#goal_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ url("performance/goals") }}/' + goalId + '/update-progress',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#updateProgressModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update progress'
                    });
                }
            });
        });

        // Update Ratings
        $('.update-ratings-btn').on('click', function() {
            var goalId = $(this).data('id');
            $('#rating_goal_id').val(goalId);
            $('#updateRatingsModal').modal('show');
        });

        $('#updateRatingsForm').on('submit', function(e) {
            e.preventDefault();
            var goalId = $('#rating_goal_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ url("performance/goals") }}/' + goalId + '/update-ratings',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#updateRatingsModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update ratings'
                    });
                }
            });
        });

        // Change Status
        $('.change-status-btn').on('click', function() {
            var goalId = $(this).data('id');
            var currentStatus = $(this).data('status');
            
            Swal.fire({
                title: 'Change Status',
                input: 'select',
                inputOptions: {
                    'Not Started': 'Not Started',
                    'In Progress': 'In Progress',
                    'On Hold': 'On Hold',
                    'Completed': 'Completed',
                    'Cancelled': 'Cancelled'
                },
                inputValue: currentStatus,
                showCancelButton: true,
                confirmButtonText: 'Update',
                showLoaderOnConfirm: true,
                preConfirm: (status) => {
                    return $.ajax({
                        url: '{{ route("goals.change-status") }}',
                        type: 'POST',
                        data: {
                            id: goalId,
                            status: status
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Status has been updated',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        });
    });
</script>

<style>
    .info-item {
        margin-bottom: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-item strong {
        display: block;
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .rating-stars {
        font-size: 1.2rem;
    }
    .timeline {
        position: relative;
        padding-left: 1rem;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-point {
        position: absolute;
        left: -1rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        background: #007bff;
        border-radius: 50%;
    }
    .timeline-content {
        margin-left: 1rem;
    }
</style>
@endsection