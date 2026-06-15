@extends('layouts.index')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Interview-style filter card helpers */
    .filter-card { border: 1px solid #e3e6ef; border-radius: .5rem; }
    .filter-card .filter-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; justify-content: space-between; }
    .filter-card .filter-header h4 { margin: 0; font-weight: 600; }
    .filter-card .filter-body { padding: 1.25rem; }
    .export-buttons .btn { transition: all .2s ease; }
    .export-buttons .btn:hover { transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,.1); }

    .nav-tabs .nav-link.active { font-weight: 600; border-bottom: 3px solid #7367F0; }

    /* Table styles - improved for better alignment */
    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }
    
    #projectSummaryTable, 
    #taskSummaryTable, 
    #taskBoardTable {
        width: 100% !important;
        table-layout: auto !important;
    }
    
    .table.custom-table.mb-0.w-100, 
    .table.custom-table.w-100.dataTable, 
    .table.dataTable {
        width: 100% !important;
        table-layout: auto !important;
    }
    
    /* Allow text wrapping for better readability */
    .table.dataTable th, 
    .table.dataTable td {
        white-space: normal !important;
        word-wrap: break-word;
        vertical-align: middle;
    }
    
    /* DataTables wrapper adjustments */
    div.dataTables_wrapper {
        width: 100%;
        overflow: hidden;
    }
    
    div.dataTables_wrapper div.dataTables_scroll {
        overflow: visible;
    }
    
    div.dataTables_wrapper div.dataTables_scroll div.dataTables_scrollBody {
        overflow-x: visible !important;
        overflow-y: visible !important;
    }
    
    /* Badge styling */
    .badge {
        padding: 5px 10px;
        font-weight: 500;
    }
    
    /* Progress bar styling */
    .progress {
        min-width: 80px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table.dataTable th, 
        .table.dataTable td {
            font-size: 13px;
            padding: 8px 5px;
        }
    }
</style>
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Project Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Project Reports</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item">
                        <a href="#project_summary_report" data-bs-toggle="tab" class="nav-link active">
                            Project Summary Report
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#task_summary_report" data-bs-toggle="tab" class="nav-link">
                            Task Summary Report
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#task_board_report" data-bs-toggle="tab" class="nav-link">
                            Task Board Report
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content">

        <div id="project_summary_report" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-12">

                    <div class="card shadow-sm border-0 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
        <h5 class="mb-0 fw-semibold text-primary">
            <i class="fas fa-filter me-2"></i> Project Filters
        </h5>
        <button id="exportProjectCSV" type="button" class="btn btn-outline-warning btn-sm">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </button>
    </div>

    <div class="card-body">
        <form id="projectSummaryFilterForm">
            <div class="row g-4">
                <!-- Project Name -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Project Name</label>
                    <input type="text" class="form-control shadow-sm" name="project_name" id="project_name" placeholder="Enter project name">
                </div>

                <!-- Client -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Client</label>
                    <select class="form-select select2 shadow-sm" name="client" id="client">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client }}">{{ $client->client }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Manager -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Project Manager</label>
                    <select class="form-select select2 shadow-sm" name="project_manager" id="project_manager">
                        <option value="">All Managers</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ ucfirst(strtolower($employee->firstname)) }} {{ ucfirst(strtolower($employee->lastname)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Status</label>
                    <select class="form-select select2 shadow-sm" name="status" id="status">
                        <option value="">All Statuses</option>
                        <option value="Initiated">Initiated</option>
                        <option value="Planned">Planned</option>
                        <option value="Active">Active</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="row g-4 mt-0">
                <!-- Start Date -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Start Date (From)</label>
                    <input type="date" class="form-control shadow-sm" name="start_date" id="start_date">
                </div>

                <!-- End Date -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">End Date (To)</label>
                    <input type="date" class="form-control shadow-sm" name="end_date" id="end_date">
                </div>

                <!-- Priority -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Priority</label>
                    <select class="form-select select2 shadow-sm" name="priority" id="priority">
                        <option value="">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-end justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> Apply
                    </button>
                    <button type="button" id="resetProjectFilters" class="btn btn-outline-secondary px-4 shadow-sm">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


                    <div class="card">
                        <div class="card-body">
                            
                                <table class="table table-striped custom-table mb-0 w-100" id="projectSummaryTable">
                                    <thead>
                                        <tr>
                                            <th>Project ID</th>
                                            <th>Project Name</th>
                                            <th>Client</th>
                                            <th>Project Manager</th>
                                            <th>Team</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Total Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="task_summary_report" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
                            <h5 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-filter me-2"></i> Task Filters
                            </h5>
                            <button id="exportTaskCSV" type="button" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                        </div>
                    
                        <div class="card-body">
                            <form id="taskSummaryFilterForm">
                                <div class="row g-4">
                                    <!-- Project -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Project</label>
                                        <select class="form-select select2 shadow-sm" name="project" id="task_project">
                                            <option value="">All Projects</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->projectid }}">{{ $project->projectname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                    
                                    <!-- Task Name -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Task Name</label>
                                        <input type="text" class="form-control shadow-sm" name="task_name" id="task_name" placeholder="Enter task name">
                                    </div>
                    
                                    <!-- Assigned To -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Assigned To</label>
                                        <select class="form-select select2 shadow-sm" name="assigned_to" id="task_assigned_to">
                                            <option value="">All Assignees</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ ucfirst(strtolower($employee->firstname)) }} {{ ucfirst(strtolower($employee->lastname)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                    
                                    <!-- Status -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Status</label>
                                        <select class="form-select select2 shadow-sm" name="status" id="task_status">
                                            <option value="">All Statuses</option>
                                            <option value="pending">Pending</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                            <option value="blocked">Blocked</option>
                                        </select>
                                    </div>
                                </div>
                    
                                <div class="row g-4 mt-0">
                                    <!-- Due Date -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Due Date</label>
                                        <input type="date" class="form-control shadow-sm" name="due_date" id="task_due_date">
                                    </div>
                    
                                    <!-- Priority -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-muted">Priority</label>
                                        <select class="form-select select2 shadow-sm" name="priority" id="task_priority">
                                            <option value="">All Priorities</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Urgent">Urgent</option>
                                        </select>
                                    </div>
                    
                                    <!-- Buttons -->
                                    <div class="col-md-6 d-flex align-items-end justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                            <i class="fas fa-check me-1"></i> Apply
                                        </button>
                                        <button type="button" id="resetTaskFilters" class="btn btn-outline-secondary px-4 shadow-sm">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0 w-100" id="taskSummaryTable">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Task Name</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="task_board_report" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">


                    <div class="filter-card mb-3">
                        <div class="card shadow-sm border-0 mb-4">
                            <!-- Header -->
                            <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
                                <h5 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-filter me-2"></i> Task Board Filters
                                </h5>
                                <button id="exportTaskBoardCSV" type="button" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-file-csv me-1"></i> Export CSV
                                </button>
                            </div>
                        
                            <!-- Body -->
                            <div class="card-body">
                                <form id="taskBoardFilterForm">
                                    <div class="row g-4">
                                        <!-- Project Name -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Project Name</label>
                                            <input type="text" class="form-control shadow-sm" name="project_name" id="tb_project_name" placeholder="Enter project name">
                                        </div>
                        
                                        <!-- Client -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Client</label>
                                            <select class="form-select select2 shadow-sm" name="client" id="tb_client">
                                                <option value="">All Clients</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->client }}">{{ $client->client }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <!-- Project Manager -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Project Manager</label>
                                            <select class="form-select select2 shadow-sm" name="project_manager" id="tb_project_manager">
                                                <option value="">All Managers</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ ucfirst(strtolower($employee->firstname)) }} {{ ucfirst(strtolower($employee->lastname)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <!-- Assigned To -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Assigned To</label>
                                            <select class="form-select select2 shadow-sm" name="assigned_to" id="tb_assigned_to">
                                                <option value="">All Assignees</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ ucfirst(strtolower($employee->firstname)) }} {{ ucfirst(strtolower($employee->lastname)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                        
                                    <div class="row g-4 mt-2">
                                        <!-- Task Status -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Task Status</label>
                                            <select class="form-select select2 shadow-sm" name="task_status" id="tb_task_status">
                                                <option value="">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                                <option value="blocked">Blocked</option>
                                            </select>
                                        </div>
                        
                                        <!-- Task Priority -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-muted">Task Priority</label>
                                            <select class="form-select select2 shadow-sm" name="priority" id="tb_priority">
                                                <option value="">All</option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Urgent">Urgent</option>
                                            </select>
                                        </div>
                        
                                        <!-- Buttons -->
                                        <div class="col-md-6 d-flex align-items-end justify-content-start gap-2">
                                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                                <i class="fas fa-check me-1"></i> Apply
                                            </button>
                                            <button type="button" id="resetTaskBoardFilters" class="btn btn-outline-secondary px-4 shadow-sm">
                                                <i class="fas fa-undo me-1"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                    </div>

                    <div class="card">
                        <div class="card-body">
                            
                                <table class="table table-striped custom-table mb-0 w-100" id="taskBoardTable">
                                    <thead>
                                        <tr>
                                            <th>Project ID</th>
                                            <th>Project Name</th>
                                            <th>Client</th>
                                            <th>Project Manager</th>
                                            <th>Team</th>
                                            <th>Total</th>
                                            <th>Pending</th>
                                            <th>In Progress</th>
                                            <th>Completed</th>
                                            <th>Progress</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<script>
$(document).ready(function() {
    // CSRF for all AJAX requests
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Select2
    $('.select2').select2();

    // Function to adjust column widths
    function adjustTableColumns() {
        if ($.fn.dataTable.isDataTable('#projectSummaryTable')) {
            $('#projectSummaryTable').DataTable().columns.adjust().draw();
        }
        if ($.fn.dataTable.isDataTable('#taskSummaryTable')) {
            $('#taskSummaryTable').DataTable().columns.adjust().draw();
        }
        if ($.fn.dataTable.isDataTable('#taskBoardTable')) {
            $('#taskBoardTable').DataTable().columns.adjust().draw();
        }
    }

    // Adjust on tab change
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        setTimeout(adjustTableColumns, 100);
    });

    // Adjust on window resize with debounce
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(adjustTableColumns, 250);
    });

    // Project Summary - with error handling
    var projectTable = $('#projectSummaryTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        autoWidth: true,
        responsive: false,
        ajax: {
            url: "{{ route('newproject.reports.project-summary') }}",
            type: "POST",
            data: function(d) {
                d.project_name   = $('#project_name').val();
                d.client         = $('#client').val();
                d.project_manager= $('#project_manager').val();
                d.status         = $('#status').val();
                d.start_date     = $('#start_date').val();
                d.end_date       = $('#end_date').val();
                d.priority       = $('#priority').val();
            },
            error: function(xhr, error, thrown) {
                console.log('Project Summary AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                alert('Error loading project data. Please check the console for details.');
            }
        },
        columns: [
            { data: 'projectId' },
            { data: 'projectname' },
            { data: 'client' },
            { data: 'leader_name' },
            { data: 'team_names' },
            { data: 'startdate', render: function(d) { 
                return d ? moment(d).format('DD-MM-YYYY') : ''; 
            }},
            { data: 'enddate', render: function(d) { 
                return d ? moment(d).format('DD-MM-YYYY') : ''; 
            }},
            { data: 'status', render: function(data) {
                var cls = 'bg-secondary';
                switch(data) {
                    case 'Initiated': cls='bg-secondary';break;
                    case 'Planned': cls='bg-info';break;
                    case 'Active': cls='bg-success';break;
                    case 'On Hold': cls='bg-warning';break;
                    case 'Completed': cls='bg-primary';break;
                }
                return '<span class="badge '+cls+'">'+data+'</span>';
            }},
            { data: 'priority' },
            { data: 'totalhours' }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        initComplete: function() {
            setTimeout(adjustTableColumns, 100);
        },
        drawCallback: function() {
            adjustTableColumns();
        },
        language: {
            processing: "Loading project data..."
        }
    });

    // Task Summary - with error handling
    var taskTable = $('#taskSummaryTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        autoWidth: true,
        responsive: false,
        ajax: {
            url: "{{ route('newproject.reports.task-summary') }}",
            type: "POST",
            data: function(d) {
                d.project     = $('#task_project').val();
                d.task_name   = $('#task_name').val();
                d.assigned_to = $('#task_assigned_to').val();
                d.status      = $('#task_status').val();
                d.due_date    = $('#task_due_date').val();
                d.priority    = $('#task_priority').val();
            },
            error: function(xhr, error, thrown) {
                console.log('Task Summary AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                alert('Error loading task data. Please check the console for details.');
            }
        },
        columns: [
            { data: 'project_name' },
            { data: 'task_name' },
            { data: 'assigned_to_name' },
            { data: 'status', render: function(data){
                var cls = 'bg-secondary';
                switch(data) {
                    case 'pending': cls='bg-warning'; break;
                    case 'in_progress': cls='bg-primary'; break;
                    case 'completed': cls='bg-success'; break;
                    case 'blocked': cls='bg-danger'; break;
                }
                return '<span class="badge '+cls+'">'+ String(data).replace('_',' ').toUpperCase() +'</span>';
            }},
            { data: 'due_date', render: function(d) { 
                return d ? moment(d).format('DD-MM-YYYY') : ''; 
            }},
            { data: 'priority' }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        initComplete: function() {
            setTimeout(adjustTableColumns, 100);
        },
        drawCallback: function() {
            adjustTableColumns();
        },
        language: {
            processing: "Loading task data..."
        }
    });

    // Task Board - with error handling
    var taskBoardTable = $('#taskBoardTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        autoWidth: true,
        responsive: false,
        ajax: {
            url: "{{ route('newproject.reports.taskboard-report') }}",
            type: "POST",
            data: function(d) {
                d.project_name    = $('#tb_project_name').val();
                d.client          = $('#tb_client').val();
                d.project_manager = $('#tb_project_manager').val();
                d.assigned_to     = $('#tb_assigned_to').val();
                d.task_status     = $('#tb_task_status').val();
                d.priority        = $('#tb_priority').val();
            },
            error: function(xhr, error, thrown) {
                console.log('Task Board AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                alert('Error loading task board data. Please check the console for details.');
            }
        },
        columns: [
            { data: 'projectId' },
            { data: 'projectname' },
            { data: 'client' },
            { data: 'leader_name' },
            { data: 'team_names' },
            { data: 'total_tasks' },
            { data: 'pending_tasks' },
            { data: 'in_progress_tasks' },
            { data: 'completed_tasks' },
            { data: 'progress_percentage', render: function(val){
                var pct = Number(val || 0).toFixed(1);
                return '<div class="progress" style="height:20px;">' +
                       '<div class="progress-bar bg-success" role="progressbar" style="width:'+pct+'%">'+pct+'%</div>' +
                       '</div>';
            }},
            { data: 'enddate', render: function(d) { 
                return d ? moment(d).format('DD-MM-YYYY') : ''; 
            }}
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        initComplete: function() {
            setTimeout(adjustTableColumns, 100);
        },
        drawCallback: function() {
            adjustTableColumns();
        },
        language: {
            processing: "Loading board data..."
        }
    });

    // Apply/Reset + Export handlers
    $('#projectSummaryFilterForm').on('submit', function(e){ 
        e.preventDefault(); 
        projectTable.ajax.reload(adjustTableColumns, false); 
    });
    
    $('#resetProjectFilters').on('click', function(){
        $('#projectSummaryFilterForm')[0].reset();
        $('#project_name').val('');
        $('#client').val('').trigger('change');
        $('#project_manager').val('').trigger('change');
        $('#status').val('').trigger('change');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#priority').val('').trigger('change');
        projectTable.ajax.reload(adjustTableColumns, false);
    });
    
    $('#exportProjectCSV').on('click', function () {
        const qs = $('#projectSummaryFilterForm').serialize();
        window.location = "{{ route('newproject.reports.export-project-summary') }}?"+qs;
    });

    $('#taskSummaryFilterForm').on('submit', function(e){ 
        e.preventDefault(); 
        taskTable.ajax.reload(adjustTableColumns, false); 
    });
    
    $('#resetTaskFilters').on('click', function(){
        $('#taskSummaryFilterForm')[0].reset();
        $('#task_project').val('').trigger('change');
        $('#task_name').val('');
        $('#task_assigned_to').val('').trigger('change');
        $('#task_status').val('').trigger('change');
        $('#task_due_date').val('');
        $('#task_priority').val('').trigger('change');
        taskTable.ajax.reload(adjustTableColumns, false);
    });
    
    $('#exportTaskCSV').on('click', function () {
        const qs = $('#taskSummaryFilterForm').serialize();
        window.location = "{{ route('newproject.reports.export-task-summary') }}?"+qs;
    });

    $('#taskBoardFilterForm').on('submit', function(e){ 
        e.preventDefault(); 
        taskBoardTable.ajax.reload(adjustTableColumns, false); 
    });
    
    $('#resetTaskBoardFilters').on('click', function(){
        $('#taskBoardFilterForm')[0].reset();
        $('#tb_project_name').val('');
        $('#tb_client').val('').trigger('change');
        $('#tb_project_manager').val('').trigger('change');
        $('#tb_assigned_to').val('').trigger('change');
        $('#tb_task_status').val('').trigger('change');
        $('#tb_priority').val('').trigger('change');
        taskBoardTable.ajax.reload(adjustTableColumns, false);
    });
    
    $('#exportTaskBoardCSV').on('click', function () {
        const qs = $('#taskBoardFilterForm').serialize();
        window.location = "{{ route('newproject.reports.export-taskboard-report') }}?"+qs;
    });

    // Trigger initial load
    setTimeout(function() {
        projectTable.ajax.reload(null, false);
        taskTable.ajax.reload(null, false);
        taskBoardTable.ajax.reload(null, false);
    }, 500);
});
</script>
@endsection