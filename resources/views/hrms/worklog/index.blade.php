@extends('layouts.index')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@if (session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" id="error-message">
        {{ session('error') }}
    </div>
@endif

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Worklogs</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Worklogs</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('worklogs.create') }}" class="btn add-btn">
                    <i class="fa-solid fa-plus"></i> Add Worklog
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <!-- Filter Section -->
    <div class="row filter-row mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="employee_filter">Employee</label>
                <select class="form-control select" id="employee_filter">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="project_filter">Project</label>
                <select class="form-control select" id="project_filter">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->projectid }}" {{ request('project') == $project->projectid ? 'selected' : '' }}>
                            {{ $project->projectname }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="date_from">Date From</label>
                <input type="date" class="form-control" id="date_from" value="{{ request('date_from') }}">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="date_to">Date To</label>
                <input type="date" class="form-control" id="date_to" value="{{ request('date_to') }}">
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mt-4">
            <button type="button" id="filter_btn" class="btn btn-success btn-block"> Filter </button>
        </div>
        <div class="col-sm-6 col-md-3 mt-4">
            <button type="button" id="reset_filter" class="btn btn-secondary btn-block"> Reset </button>
        </div>
    </div>
    <!-- /Filter Section -->
   
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped datatable mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Task</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Break (min)</th>
                            <th>Description</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($worklogs as $worklog)
                            <tr>
                                <td>{{ $worklog->employee_name }}</td>
                                <td>{{ $worklog->date }}</td>
                                <td>{{ $worklog->project }}</td>
                                <td>{{ $worklog->task }}</td>
                                <td>{{ $worklog->start_time }}</td>
                                <td>{{ $worklog->end_time }}</td>
                                <td>{{ $worklog->break_minutes }}</td>
                                <td>{{ $worklog->description }}</td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('worklogs.edit', $worklog->id) }}">
                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                            </a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_worklog_{{ $worklog->id }}">
                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="delete_worklog_{{ $worklog->id }}" tabindex="-1" role="dialog" aria-labelledby="delete_worklog_label_{{ $worklog->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="delete_worklog_label_{{ $worklog->id }}">
                                                        <i class="fas fa-exclamation-triangle"></i> Delete Worklog
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center py-5">
                                                    <p class="fs-5 text-muted">
                                                        Are you sure you want to delete this worklog? This action cannot be undone.
                                                    </p>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <i class="fas fa-trash-alt fa-3x text-danger"></i>
                                                    </div>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                        <i class="fas fa-times-circle"></i> Cancel
                                                    </button>
                                                    <form action="{{ route('worklogs.destroy', $worklog->id) }}" method="POST" class="d-inline">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select').select2();

    // Filter button click handler
    $('#filter_btn').click(function() {
        let employeeId = $('#employee_filter').val();
        let projectId = $('#project_filter').val();
        let dateFrom = $('#date_from').val();
        let dateTo = $('#date_to').val();

        let queryParams = [];
        
        if (employeeId) queryParams.push(`employee_id=${employeeId}`);
        if (projectId) queryParams.push(`project=${projectId}`);
        if (dateFrom) queryParams.push(`date_from=${dateFrom}`);
        if (dateTo) queryParams.push(`date_to=${dateTo}`);

        let queryString = queryParams.join('&');
        window.location.href = "{{ route('worklogs.index') }}" + (queryString ? '?' + queryString : '');
    });

    // Reset filter button
    $('#reset_filter').click(function() {
        window.location.href = "{{ route('worklogs.index') }}";
    });

    // Auto-submit when date range changes (optional)
    $('#date_from, #date_to').change(function() {
        if ($('#date_from').val() && $('#date_to').val()) {
            $('#filter_btn').click();
        }
    });

    // Hide success/error messages after 5 seconds
    setTimeout(function() {
        $('#success-message, #error-message').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@endsection