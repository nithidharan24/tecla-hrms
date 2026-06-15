@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container mt-5">
        <div class="page-header">
            <h2 class="pageheader-title">Submit Worklog</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('worklogs.index') }}" class="breadcrumb-link">Worklog List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Submit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form method="POST" action="{{ route('worklogs.store') }}" class="shadow p-4 rounded" id="addworklog">
            @csrf
            <h3 class="mb-4">Add Worklog</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee Name <span class="text-danger">*</span></label>
                    @if(session('role') === 'employee')
                        <input type="hidden" name="employee_id" value="{{ session('user_id') }}">
                        <input type="text" class="form-control" value="{{ session('first_name') }} {{ session('last_name') }}" readonly>
                    @else
                        <select name="employee_id" id="employee_id" class="form-control select2" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @if(old('employee_id') == $employee->id) selected @endif>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="project" class="form-label">Project <span class="text-danger">*</span></label>
                    <select name="projectid" class="form-control" required>
                        <option value="">-- Select Project --</option>
                        @foreach($recentProjects as $project)
                            <option value="{{ $project->projectid }}" @if(old('projectid') == $project->projectid) selected @endif>
                                {{ $project->projectname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="task" class="form-label">Task <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="task" name="task" value="{{ old('task') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="break_minutes" class="form-label">Break (minutes) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="break_minutes" name="break_minutes" min="0" value="{{ old('break_minutes', 0) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Only initialize select2 if it exists (for admin)
    if ($('#employee_id').length) {
        $('#employee_id').select2({
            placeholder: "Select an employee",
            allowClear: true
        });
    }

    // Initialize CKEditor for the description textarea
    ClassicEditor.create(document.querySelector('#description'))
        .catch(error => console.error(error));

    // Form validation
    $("#addworklog").validate({
        rules: {
            employee_id: { required: true },
            date: { required: true },
            projectid: { required: true },
            task: { required: true },
            start_time: { required: true },
            end_time: { required: true },
            break_minutes: { required: true, min: 0 },
            description: { required: true }
        },
        messages: {
            employee_id: "Please select an employee",
            date: "Please enter a date",
            projectid: "Please select a project",
            task: "Please enter a task",
            start_time: "Please enter a start time",
            end_time: "Please enter an end time",
            break_minutes: "Please enter break time",
            description: "Please enter a description"
        },
        errorClass: "error",
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        highlight: function(element) {
            $(element).addClass("error");
        },
        unhighlight: function(element) {
            $(element).removeClass("error");
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});
</script>
@endsection