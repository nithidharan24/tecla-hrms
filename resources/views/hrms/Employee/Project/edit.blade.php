@extends('layouts.index')
@section('content')
<div class="content container-fluid">
    <div class="container mt-5">
        <div class="page-header">
            <h2 class="pageheader-title">Edit Project</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}" class="breadcrumb-link">Project List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form action="{{ route('projects.update', $project->id) }}" method="POST" class="shadow p-4 rounded" enctype="multipart/form-data" id="editproject">
            @csrf
            @method('PUT')
            <h3 class="mb-4">Edit Project</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="projectname" class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="projectname" name="projectname" value="{{ $project->projectname }}" required>
                </div>
                <div class="col-md-6">
                    <label for="client">Client <span class="text-danger">*</span></label>
                    <select class="form-control select2" name="client" id="client">
                        <option value="" disabled>Select a client</option>
                        @foreach ($client as $cl)
                            <option value="{{ $cl->client_id }}" {{ $project->client == $cl->client_id ? 'selected' : '' }}>
                                {{ $cl->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="startdate" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="startdate" name="startdate" value="{{ $project->startdate }}" required>
                </div>
                <div class="col-md-6">
                    <label for="enddate" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="enddate" name="enddate" value="{{ $project->enddate }}" required>
                    <div class="invalid-feedback" id="date-error">End date must be after the start date.</div>
                </div>
            </div>

            <!-- <div class="row mb-3">
                <div class="col-md-6">
                    <label for="rate" class="form-label">Rate <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="rate" name="rate" value="{{ $project->rate }}" required>
                </div>
                <div class="col-md-6">
                    <label for="worktype" class="form-label">Work Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="worktype" name="worktype" required>
                        <option value="">Select work type...</option>
                        <option value="Hourly" {{ $project->worktype == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                        <option value="Fixed" {{ $project->worktype == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                    </select>
                </div>
            </div> -->

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="projectleader" class="form-label">Project Leader <span class="text-danger">*</span></label>
                    <select class="form-control select2-employee" id="projectleader" name="projectleader" required>
                        <option value="" disabled>Select Project Leader</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ isset($currentLeaderId) && $currentLeaderId == $employee->id ? 'selected' : '' }}>
                                {{ $employee->firstname }} {{ $employee->lastname }}
                            </option>
                        @endforeach
                    </select>
                   
                </div>
                <div class="col-md-6">
                    <label for="team" class="form-label">Team <span class="text-danger">*</span></label>
                    <select class="form-control select2-employee" id="team" name="team[]" multiple>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, $selectedTeam) ? 'selected' : '' }}>
                                {{ $employee->firstname }} {{ $employee->lastname }}
                            </option>
                        @endforeach
                    </select>
                     Display Team Member Images 
                    <div id="teamImages" class="mt-2"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="">Select priority...</option>
                        <option value="high" {{ $project->priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ $project->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ $project->priority == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="" disabled>Select status...</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ $project->status == $key ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required>{{ $project->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="projectfile" class="form-label">Upload Project File</label>
                <input type="file" class="form-control" id="projectfile" name="projectfile">
                @if($project->projectfile)
                    <small class="text-muted">Current file: {{ basename($project->projectfile) }}</small>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 for team
        $('#team').select2({
            placeholder: "Select Team Members"
        });

        // Initialize select2 for project leader
        $('#projectleader').select2({
            placeholder: "Select Project Leader"
        });

        // Initialize select2 for client
        $('#client').select2({
            placeholder: "Select a client",
            allowClear: true
        });

        // Date validation
        const startDate = document.getElementById('startdate');
        const endDate = document.getElementById('enddate');
        const dateError = document.getElementById('date-error');

        function validateDates() {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);

            if (end <= start) {
                endDate.setCustomValidity("End date must be after start date");
                dateError.style.display = 'block';
            } else {
                endDate.setCustomValidity("");
                dateError.style.display = 'none';
            }
        }

        startDate.addEventListener('change', validateDates);
        endDate.addEventListener('change', validateDates);
    });
</script>
@endsection
