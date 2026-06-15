@extends('layouts.index')
@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('timesheet.index') }}">Timesheets</a></li>
                    <li class="breadcrumb-item active">Edit Timesheet</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Edit Timesheet</div>
        <div class="card-body">
            <form id="editWorkDetails" method="POST" action="{{ route('timesheet.update', $timesheet->id) }}">
                @csrf
                @method('PUT')
                
                <!-- Employee and Project Select Fields -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="employee">Employee</label>
                        <select class="form-control select2" name="employee" id="employee">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $timesheet->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->firstname }} {{ $employee->lastname }}
                                </option>
                            @endforeach
                        </select>
                        <span id="employeeError" class="text-danger"></span>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="project">Project</label>
                        <select class="form-control select2" name="project" id="project">
                            @foreach ($projects as $project)
                                <option value="{{ $project->projectid }}" {{ $timesheet->project_id == $project->projectid ? 'selected' : '' }}>
                                    {{ $project->projectname }}
                                </option>
                            @endforeach
                        </select>
                        <span id="projectError" class="text-danger"></span>
                    </div>
                </div>

             
                <!-- Assigned Date and Assigned Hours Fields -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="assigned_date">Date</label>
                        <input type="date" class="form-control" name="assigned_date" id="assigned_date" value="{{ $timesheet->assigned_date }}">
                        <span id="assignedDateError" class="text-danger"></span>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="assigned_hours">Worked Hours</label>
                        <input type="number" class="form-control" name="assigned_hours" id="assigned_hours" value="{{ $timesheet->assigned_hours }}">
                        <span id="assignedHoursError" class="text-danger"></span>
                    </div>
                </div>

                <!-- Description -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4" placeholder="Enter Description" style="resize: none">{{ $timesheet->description }}</textarea>
                        <span id="descriptionError" class="text-danger"></span>
                    </div>
                </div>

                <div class="row mt-4 text-center">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2
    $('#employee').select2({
        placeholder: "Select an Employee",
        allowClear: true
    });
    $('#project').select2({
        placeholder: "Select a Project",
        allowClear: true
    });

    function loadProjectsForEmployee(employeeId, selectedProjectId = null) {
        if (employeeId) {
            $('#project').prop('disabled', true);

            $.ajax({
                url: '{{ url("/api/getEmployeeProjects") }}/' + employeeId,
                type: 'GET',
                success: function(response) {
                    $('#project').empty();
                    $('#project').append('<option value="" disabled>Select a Project</option>');

                    if (response.length > 0) {
                        $.each(response, function(index, project) {
                            $('#project').append(
                                '<option value="' + project.projectid + '">' + project.projectname + '</option>'
                            );
                        });

                        // Set selected project
                        if (selectedProjectId) {
                            $('#project').val(selectedProjectId).trigger('change');
                        }
                    } else {
                        $('#project').append('<option value="" disabled>No projects found for this employee</option>');
                    }

                    $('#project').prop('disabled', false).trigger('change');
                },
                error: function(error) {
                    console.log('Error fetching projects:', error);
                    $('#project').prop('disabled', false);
                }
            });
        }
    }

    // 🔹 Run once on page load for the already selected employee/project
    var initialEmployeeId = "{{ $timesheet->employee_id }}";
    var initialProjectId = "{{ $timesheet->project_id }}";
    loadProjectsForEmployee(initialEmployeeId, initialProjectId);

    // 🔹 Run again whenever employee changes
    $('#employee').on('change', function() {
        loadProjectsForEmployee($(this).val());
    });

    // Fetch project details when a project is selected
    $('#project').on('change', function() {
        var projectId = $(this).val();
        if (projectId) {
            $.ajax({
                url: '/api/getProjectDetails/' + projectId,
                type: 'GET',
                success: function(response) {
                    $('#total_hours').val(response.totalhours);
                    $('#deadline').val(response.enddate);
                },
                error: function(error) {
                    console.log('Error fetching project details:', error);
                }
            });
        } else {
            $('#total_hours').val('');
            $('#deadline').val('');
        }
    });

    // Recalculate remaining hours
    $('#assigned_hours').on('input', function() {
        var totalHours = parseFloat($('#total_hours').val()) || 0;
        var assignedHours = parseFloat($(this).val()) || 0;
        var remainingHours = totalHours - assignedHours;
        $('#remaining_hours').val(remainingHours >= 0 ? remainingHours : 0);
    });
});

</script>
@endsection
