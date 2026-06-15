@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Today Work details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('timesheet.index')}}">Timesheet</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('timesheet.create')}}">Add</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add Work</h4>
                <div class="card-body">
                    <form id="addWorkDetails" method="POST" action="{{route('timesheet.store')}}">
                        @csrf
                        
                        <!-- Employee and Project Fields with select2 and default option -->
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="employee">Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employee" id="employee">
                                    <option value="" disabled selected>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                    @endforeach
                                </select>
                                <span id="employeeError" class="text-danger"></span>
                            </div>
    
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="project">Project <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="project" id="project" disabled>
                                    <option value="" disabled selected>Select a Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->projectid }}">{{ $project->projectname }}</option>
                                    @endforeach
                                </select>
                                <span id="projectError" class="text-danger"></span>
                            </div>
                        </div>

                        <!-- Assigned Date and Assigned Hours Fields -->
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_date"> Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" id="assigned_date">
                                <span id="assignedDateError" class="text-danger"></span>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_hours">Worked Hours <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="assigned_hours" id="assigned_hours" placeholder="Assigned hours">
                                <span id="assignedHoursError" class="text-danger"></span>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Enter Description" style="resize: none"></textarea>
                                <span id="descriptionError" class="text-danger"></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitBtn">
                                    <span id="submitText">Submit</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Form End -->
    <script>
        $(document).ready(function() {
            // Initialize select2 with placeholder
            $('#employee').select2({
                placeholder: "Select an Employee",
                allowClear: true
            });
            
            $('#project').select2({
                placeholder: "Select a Project",
                allowClear: true
            });

            // When employee is selected, fetch their projects
            $('#employee').on('change', function() {
                var employeeId = $(this).val();
                
                // Clear project field
                $('#project').val(null).trigger('change');
                
                if (employeeId) {
                    // Show loading state
                    $('#project').prop('disabled', true);
                    
                    $.ajax({
                        url: '{{ url("/api/getEmployeeProjects") }}/' + employeeId,
                        type: 'GET',
                        success: function(response) {
                            // Clear existing options
                            $('#project').empty();
                            $('#project').append('<option value="" disabled selected>Select a Project</option>');
                            
                            // Add new options
                            if (response.length > 0) {
                                $.each(response, function(index, project) {
                                    $('#project').append('<option value="' + project.projectid + '">' + project.projectname + '</option>');
                                });
                            } else {
                                $('#project').append('<option value="" disabled>No projects found for this employee</option>');
                            }
                            
                            // Enable and refresh select2
                            $('#project').prop('disabled', false).trigger('change');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading projects:', error);
                            $('#project').prop('disabled', false);
                            alert('Error loading projects: ' + error);
                        }
                    });
                }
            });

            // Function to validate all fields
            function validateFields() {
                var isValid = true;

                // Validate employee selection
                if ($('#employee').val() === null) {
                    $('#employeeError').text('Please select an employee.');
                    isValid = false;
                } else {
                    $('#employeeError').text('');
                }

                // Validate project selection
                if ($('#project').val() === null) {
                    $('#projectError').text('Please select a project.');
                    isValid = false;
                } else {
                    $('#projectError').text('');
                }

                // Validate assigned date
                if ($('#assigned_date').val() === '') {
                    $('#assignedDateError').text('Assigned date is required.');
                    isValid = false;
                } else {
                    $('#assignedDateError').text('');
                }

                // Validate assigned hours
                if ($('#assigned_hours').val() === '') {
                    $('#assignedHoursError').text('Assigned hours are required.');
                    isValid = false;
                } else {
                    $('#assignedHoursError').text('');
                }

                // Validate description
                var description = $('#description').val();
                if (description.length < 10 || description.length > 255 || /^\s/.test(description)) {
                    if (description.length < 10) {
                        $('#descriptionError').text('Description must be at least 10 characters.');
                    } else if (description.length > 255) {
                        $('#descriptionError').text('Description must not exceed 255 characters.');
                    } else if (/^\s/.test(description)) {
                        $('#descriptionError').text('Description should not start with a space.');
                    }
                    isValid = false;
                } else {
                    $('#descriptionError').text('');
                }

                return isValid;
            }

            // Validate fields on change or input events        
            $('#employee, #project, #assigned_date, #assigned_hours, #description').on('change input', function() {
                validateFields();
            });

            // Form validation on submit
            $('#addWorkDetails').on('submit', function(e) {
                if (validateFields()) {
                    // Disable the submit button and show processing state
                    $('#submitBtn').prop('disabled', true);
                    $('#submitText').text('Processing...');
                    $('#submitSpinner').removeClass('d-none');
                } else {
                    e.preventDefault(); // Prevent form submission if validation fails
                }
            });
        });
    </script>   
</div>
<!-- /Page Content -->
@endsection