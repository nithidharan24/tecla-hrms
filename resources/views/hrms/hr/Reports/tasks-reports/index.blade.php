@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
                
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Task Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Task Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Content Starts -->
        <!-- Search Filter -->
<form method="GET" action="{{ route('task-reports.index') }}">
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <select name="project" class="form-control floating select">
                    <option value="">All</option>
                    @foreach($projects as $project) <!-- Use $projects to get the project names -->
                        <option value="{{ $project->projectid }}" {{ request('project') == $project->projectid ? 'selected' : '' }}>
                            {{ $project->projectname }}
                        </option>
                    @endforeach
                </select>
                <label class="focus-label">Project Name</label>
            </div>
        </div>
        <!-- <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <select name="status" class="form-control floating select">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <label class="focus-label">Status</label>
            </div>
        </div> -->
        <div class="col-sm-6 col-md-3">  
            <button type="submit" class="btn btn-success w-100">Search</button>  
        </div>     
    </div>
</form>
<!--/Search Filter -->

        <!-- Export Buttons -->
<div class="row mb-3">
    <div class="col-md-12 text-end">
        <a href="{{ route('task-reports.export.csv', request()->query()) }}" class="btn btn-primary">
            <i class="fa fa-file-excel"></i> Export to CSV
        </a>
        <a href="{{ route('task-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fa fa-file-pdf"></i> Export to PDF
        </a>
    </div>
</div>
<!-- /Export Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
    <table class="table  custom-table mb-0 datatable" id="taskTable">
        <thead>
            <tr>
               
                <th>Task Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <!-- <th class="text-center">Status</th> -->
                <th>Assigned By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                   
                    <td data-label="Task Name">
                        <span class="high">{{ $task->task_name }}</span>
                    </td>
                    
                    <td data-label="Start Date">
                        {{ date('d M Y', strtotime($task->start_date)) }}
                    </td>
                    
                    <td data-label="End Date">
                        {{ date('d M Y', strtotime($task->end_date)) }}
                    </td>
                    
                    <!-- <td class="text-center" data-label="Status">
                        {{ ucfirst($task->task_status) }}
                    </td> -->
                    
                    <td data-label="Assigned To">
                        <span class="od-chip-highlight">{{ $task->assigned_to_name }}</span>
                    </td>
                    

                </tr>
            @endforeach
        </tbody>
    </table>
</div>



            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Select all / row highlight
    $('#checkAll').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>
@endsection
