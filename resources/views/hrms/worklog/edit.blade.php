@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container mt-5">
        <div class="page-header">
            <h2 class="pageheader-title">Edit Worklog</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('worklogs.index') }}" class="breadcrumb-link">Worklog List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form method="POST" action="{{ route('worklogs.update', $worklog->id) }}" class="shadow p-4 rounded" id="editworklog">
            @csrf
            @method('PUT')
            <h3 class="mb-4">Edit Worklog</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee Name <span class="text-danger">*</span></label>
                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $worklog->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $worklog->date }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="project" class="form-label">Project <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="project" name="project" value="{{ $worklog->project }}" required>
                </div>
                <div class="col-md-6">
                    <label for="task" class="form-label">Task <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="task" name="task" value="{{ $worklog->task }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="start_time" name="start_time" value="{{ $worklog->start_time }}" required>
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="end_time" name="end_time" value="{{ $worklog->end_time }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="break_minutes" class="form-label">Break (minutes) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="break_minutes" name="break_minutes" min="0" value="{{ $worklog->break_minutes }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required>{{ $worklog->description }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Initialize select2
    $('#employee_id').select2({
        placeholder: "Select an employee",
        allowClear: true
    });

    // Initialize CKEditor for the description textarea
    ClassicEditor.create(document.querySelector('#description'))
        .catch(error => console.error(error));

    // Form validation
    $("#editworklog").validate({
        rules: {
            employee_id: { required: true },
            date: { required: true },
            project: { required: true },
            task: { required: true },
            start_time: { required: true },
            end_time: { required: true },
            break_minutes: { required: true, min: 0 },
            description: { required: true }
        },
        messages: {
            employee_id: "Please select an employee",
            date: "Please enter a date",
            project: "Please enter a project name",
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
