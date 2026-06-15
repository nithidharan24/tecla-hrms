@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Goal</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('goals.index') }}">Goals</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('goals.show', $goal->id) }}">{{ Str::limit($goal->goal_title, 20) }}</a></li>
                    <li class="breadcrumb-item active">Edit Goal</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('goals.show', $goal->id) }}" class="btn btn-secondary">
                    <i class="fa fa-eye"></i> View Goal
                </a>
                <a href="{{ route('goals.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Goals
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Goal Information</h4>
                </div>
                <div class="card-body">
                    <form id="editGoal" method="POST" action="{{ route('goals.update', $goal->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="goal_title" class="col-form-label">Goal Title <span class="text-danger">*</span></label>
                                    <input id="goal_title" name="goal_title" class="form-control" type="text" value="{{ old('goal_title', $goal->goal_title) }}" placeholder="Enter goal title" />
                                    @error('goal_title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="category" class="col-form-label">Category <span class="text-danger">*</span></label>
                                    <input id="category" name="category" class="form-control" type="text" value="{{ old('category', $goal->category) }}" placeholder="e.g., Sales, Productivity, Learning" />
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Goal Type <span class="text-danger">*</span></label>
                                    <select id="goal_type" name="goal_type" class="form-control select"> 
                                        <option value="">Select Goal Type</option>
                                        <option value="Company" {{ old('goal_type', $goal->goal_type) == 'Company' ? 'selected' : '' }}>Company</option>
                                        <option value="Department" {{ old('goal_type', $goal->goal_type) == 'Department' ? 'selected' : '' }}>Department</option>
                                        <option value="Team" {{ old('goal_type', $goal->goal_type) == 'Team' ? 'selected' : '' }}>Team</option>
                                        <option value="Individual" {{ old('goal_type', $goal->goal_type) == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    </select>
                                    @error('goal_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Department</label>
                                    <select id="department_id" name="department_id" class="form-control select"> 
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $goal->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->department }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        <div class="col-sm-6">
    <div class="input-block mb-3">
        <label class="col-form-label">Assigned To <span class="text-danger">*</span></label>
        <select id="assigned_to" name="assigned_to" class="form-control select"> 
            <option value="">Select Employee</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" 
                    {{ old('assigned_to', $goal->assigned_to) == $employee->id ? 'selected' : '' }}>
                    {{ $employee->firstname }} {{ $employee->lastname }}
                    @if($employee->department_name)
                        - {{ $employee->department_name }}
                    @endif
                </option>
            @endforeach
        </select>
        @error('assigned_to')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>


                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Priority <span class="text-danger">*</span></label>
                                    <select id="priority" name="priority" class="form-control select"> 
                                        <option value="">Select Priority</option>
                                        <option value="Low" {{ old('priority', $goal->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('priority', $goal->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('priority', $goal->priority) == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Critical" {{ old('priority', $goal->priority) == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="start_date" class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input id="start_date" name="start_date" class="form-control datetimepicker" type="text" value="{{ old('start_date', \Carbon\Carbon::parse($goal->start_date)->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" />
                                    </div>
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="end_date" class="col-form-label">End Date <span class="text-danger">*</span></label>
                                    <div class="cal-icon">
                                        <input id="end_date" name="end_date" class="form-control datetimepicker" type="text" value="{{ old('end_date', \Carbon\Carbon::parse($goal->end_date)->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" />
                                    </div>
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="target_value" class="col-form-label">Target Value <span class="text-danger">*</span></label>
                                    <input id="target_value" name="target_value" class="form-control" type="number" step="0.01" min="0" value="{{ old('target_value', $goal->target_value) }}" placeholder="e.g., 100, 75.5" />
                                    @error('target_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="unit" class="col-form-label">Unit <span class="text-danger">*</span></label>
                                    <input id="unit" name="unit" class="form-control" type="text" value="{{ old('unit', $goal->unit) }}" placeholder="e.g., %, leads, sales, clients, hours" />
                                    @error('unit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label for="weightage" class="col-form-label">Weightage (%) <span class="text-danger">*</span></label>
                                    <input id="weightage" name="weightage" class="form-control" type="number" min="1" max="100" value="{{ old('weightage', $goal->weightage) }}" placeholder="Enter weightage percentage" />
                                    <small class="form-text text-muted">Weightage should be between 1% and 100%</small>
                                    @error('weightage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Review Cycle <span class="text-danger">*</span></label>
                                    <select id="review_cycle" name="review_cycle" class="form-control select"> 
                                        <option value="">Select Review Cycle</option>
                                        <option value="Monthly" {{ old('review_cycle', $goal->review_cycle) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="Quarterly" {{ old('review_cycle', $goal->review_cycle) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="Half-Yearly" {{ old('review_cycle', $goal->review_cycle) == 'Half-Yearly' ? 'selected' : '' }}>Half-Yearly</option>
                                        <option value="Yearly" {{ old('review_cycle', $goal->review_cycle) == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                    @error('review_cycle')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="input-block mb-3">
                                    <label for="goal_description" class="col-form-label">Goal Description <span class="text-danger">*</span></label>
                                    <textarea id="goal_description" name="goal_description" class="form-control" rows="4" placeholder="Describe the goal in detail, including objectives and success criteria">{{ old('goal_description', $goal->goal_description) }}</textarea>
                                    @error('goal_description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="input-block mb-3">
                                    <label for="remarks" class="col-form-label">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Any additional comments or notes">{{ old('remarks', $goal->remarks) }}</textarea>
                                    @error('remarks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Progress Information (Read-only) -->
                            <div class="col-sm-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Progress Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="info-item">
                                                    <strong>Current Value:</strong>
                                                    <span>{{ number_format($goal->current_value, 2) }} {{ $goal->unit }}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="info-item">
                                                    <strong>Progress:</strong>
                                                    <span>{{ number_format($goal->progress_percentage, 1) }}%</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="info-item">
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
                                            </div>
                                        </div>
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
                                        <small class="text-muted mt-2 d-block">
                                            Note: Progress and current value are automatically calculated. To update progress, use the "Update Progress" feature from the goal details page.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">
                                <i class="fa fa-save"></i> Update Goal
                            </button>
                            <a href="{{ route('goals.show', $goal->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize date pickers
        $('.datetimepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            useCurrent: false,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        });

        // Initialize select2
        $('.select').select2({
            minimumResultsForSearch: -1
        });

        // Department change event - Filter employees by department
        $('#department_id').on('change', function() {
            var departmentId = $(this).val();
            var assignedToSelect = $('#assigned_to');
            
            if (departmentId) {
                // Show loading
                assignedToSelect.prop('disabled', true);
                assignedToSelect.html('<option value="">Loading employees...</option>');
                
                // AJAX call to get employees by department
                $.ajax({
                    url: '{{ route("goals.get-employees-by-department") }}',
                    type: 'GET',
                    data: {
                        department_id: departmentId
                    },
                    success: function(response) {
                        assignedToSelect.prop('disabled', false);
                        assignedToSelect.html('<option value="">Select Employee</option>');
                        
                        if (response.length > 0) {
                            $.each(response, function(index, employee) {
                                var isSelected = employee.id == '{{ $goal->assigned_to }}' ? 'selected' : '';
                                assignedToSelect.append(
                                    $('<option ' + isSelected + '></option>').val(employee.id).text(employee.text)
                                );
                            });
                        } else {
                            assignedToSelect.append(
                                $('<option></option>').val('').text('No employees found in this department')
                            );
                        }
                    },
                    error: function(xhr) {
                        assignedToSelect.prop('disabled', false);
                        assignedToSelect.html('<option value="">Error loading employees</option>');
                        console.error('Error:', xhr.responseText);
                    }
                });
            } else {
                // If no department selected, show all employees
                loadAllEmployees();
            }
        });

        // Function to load all employees
        function loadAllEmployees() {
            var assignedToSelect = $('#assigned_to');
            assignedToSelect.prop('disabled', true);
            assignedToSelect.html('<option value="">Loading all employees...</option>');
            
            $.ajax({
                url: '{{ route("goals.get-employees-by-department") }}',
                type: 'GET',
                data: {
                    department_id: ''
                },
                success: function(response) {
                    assignedToSelect.prop('disabled', false);
                    assignedToSelect.html('<option value="">Select Employee</option>');
                    
                    if (response.length > 0) {
                        $.each(response, function(index, employee) {
                            var isSelected = employee.id == '{{ $goal->assigned_to }}' ? 'selected' : '';
                            assignedToSelect.append(
                                $('<option ' + isSelected + '></option>').val(employee.id).text(employee.text)
                            );
                        });
                    }
                },
                error: function(xhr) {
                    assignedToSelect.prop('disabled', false);
                    assignedToSelect.html('<option value="">Error loading employees</option>');
                    console.error('Error:', xhr.responseText);
                }
            });
        }

        // Custom validation method for end date
        $.validator.addMethod("endDateAfterStart", function(value, element) {
            var startDate = $('#start_date').val();
            if (!startDate || !value) return true;
            
            var startParts = startDate.split("-");
            var endParts = value.split("-");
            
            var start = new Date(startParts[2], startParts[1] - 1, startParts[0]);
            var end = new Date(endParts[2], endParts[1] - 1, endParts[0]);
            
            return end > start;
        }, "End date must be after start date");

        // Form validation
        $('#editGoal').validate({
            rules: {
                goal_title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                goal_description: {
                    required: true,
                    minlength: 10
                },
                goal_type: {
                    required: true
                },
                category: {
                    required: true,
                    minlength: 2
                },
                assigned_to: {
                    required: true
                },
                start_date: {
                    required: true
                },
                end_date: {
                    required: true,
                    endDateAfterStart: true
                },
                target_value: {
                    required: true,
                    number: true,
                    min: 0
                },
                unit: {
                    required: true
                },
                weightage: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 100
                },
                priority: {
                    required: true
                },
                review_cycle: {
                    required: true
                }
            },
            messages: {
                goal_title: {
                    required: "Please enter goal title.",
                    minlength: "Goal title must be at least 3 characters long."
                },
                goal_description: {
                    required: "Please enter goal description.",
                    minlength: "Description must be at least 10 characters long."
                },
                goal_type: {
                    required: "Please select goal type."
                },
                category: {
                    required: "Please enter category."
                },
                assigned_to: {
                    required: "Please select assigned employee."
                },
                start_date: {
                    required: "Please enter start date."
                },
                end_date: {
                    required: "Please enter end date."
                },
                target_value: {
                    required: "Please enter target value.",
                    number: "Please enter a valid number."
                },
                unit: {
                    required: "Please enter unit."
                },
                weightage: {
                    required: "Please enter weightage.",
                    number: "Please enter a valid number.",
                    min: "Weightage must be at least 1%.",
                    max: "Weightage cannot exceed 100%."
                },
                priority: {
                    required: "Please select priority."
                },
                review_cycle: {
                    required: "Please select review cycle."
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.hasClass("datetimepicker")) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                $('.submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                form.submit();
            }
        });

        // Restrict end date to be after start date
        $('#start_date').on('dp.change', function(e) {
            if ($('#start_date').val()) {
                var startParts = $('#start_date').val().split("-");
                var startDate = new Date(startParts[2], startParts[1] - 1, startParts[0]);
                startDate.setDate(startDate.getDate() + 1);
                
                $('#end_date').data("DateTimePicker").minDate(startDate);
                if ($('#end_date').val()) {
                    var endParts = $('#end_date').val().split("-");
                    var endDate = new Date(endParts[2], endParts[1] - 1, endParts[0]);
                    if (endDate <= startDate) {
                        $('#end_date').val('');
                    }
                }
            }
        });

        // Trigger department change on page load if department is pre-selected
        @if(old('department_id', $goal->department_id))
            $('#department_id').trigger('change');
        @endif
    });

    // Show validation errors using SweetAlert
    @if ($errors->any())
        @php
            $errorMessages = '';
            foreach ($errors->all() as $error) {
                $errorMessages .= $error . "\\n";
            }
        @endphp
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `{!! addslashes($errorMessages) !!}`,
            confirmButtonText: 'OK'
        });
    @endif

    // Show success message
    @if(Session::has('messageType') && Session::has('message'))
        Swal.fire({
            icon: "{{ Session::get('messageType') }}",
            title: "{{ Session::get('message') }}",
            showConfirmButton: true,
            timer: 3000
        });
    @endif
</script>

<style>
    .is-invalid {
        border-color: #f46a6a !important;
    }
    .error {
        color: #f46a6a;
        font-size: 13px;
        margin-top: 5px;
    }
    .select2-container--default .select2-selection--single .is-invalid {
        border-color: #f46a6a !important;
    }
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1rem;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }
    .submit-section {
        border-top: 1px solid #dee2e6;
        padding-top: 1.5rem;
        margin-top: 1rem;
        text-align: right;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
    .info-item {
        margin-bottom: 0.5rem;
    }
    .info-item strong {
        display: block;
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 0.375rem;
    }
</style>
@endsection