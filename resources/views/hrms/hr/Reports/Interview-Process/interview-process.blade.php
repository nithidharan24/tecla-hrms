@extends('layouts.index')

@section('content')
<style>
    #interviewSchedulingTable {
        width: 100% !important;
    }
    #interviewSchedulingTable thead th {
        white-space: nowrap;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Interview Process Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#job_posting_report" data-bs-toggle="tab" class="nav-link active">Job Posting Report</a></li>
                    <li class="nav-item"><a href="#resume_management_report" data-bs-toggle="tab" class="nav-link">Candidate List Report</a></li>
                    <li class="nav-item"><a href="#candidate_shortlisting_report" data-bs-toggle="tab" class="nav-link">Candidate Shortlisting Report</a></li>
                    <!-- <li class="nav-item"><a href="#candidate_list_report" data-bs-toggle="tab" class="nav-link">Candidate List Report</a></li> -->
                    <li class="nav-item"><a href="#interview_scheduling_report" data-bs-toggle="tab" class="nav-link">Interview Scheduling Report</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Job Posting Report Tab -->
        <div id="job_posting_report" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Filters</h4>
                            <form id="jobPostingFilterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Job Title</label>
                                            <input type="text" class="form-control" name="job_title" id="job_title">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select class="form-control select2" name="department" id="department">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->department }}">{{ $department->department }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <input type="text" class="form-control" name="location" id="location">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date Posted</label>
                                            <input type="date" class="form-control" name="date_posted" id="date_posted">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control select2" name="status" id="status">
                                                <option value="">All Statuses</option>
                                                <option value="Open">Open</option>
                                                <option value="Closed">Closed</option>
                                                <option value="On Hold">On Hold</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                                            <button type="button" id="resetFilters" class="btn btn-secondary">Reset</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button id="exportCSV" class="btn btn-primary">
                                                <i class="fa fa-file-excel"></i> Export to CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                
                                <table class="table table-striped custom-table mb-0" id="jobPostingTable">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Department</th>
                                            <th>Location</th>
                                            <th>Vacancies</th>
                                            <th>Posted Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Job Posting Report Tab -->

        <!-- Resume Management Report Tab -->
<div id="resume_management_report" class="tab-pane fade">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Filters</h4>
                    <form id="resumeManagementFilterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" class="form-control" name="position_applied" id="position_applied">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Candidate Name</label>
                                    <input type="text" class="form-control" name="candidate_name" id="candidate_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Received From</label>
                                    <input type="date" class="form-control" name="date_from" id="date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Received To</label>
                                    <input type="date" class="form-control" name="date_to" id="date_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Resume Status</label>
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="">All Statuses</option>
                                        <option value="applied">Applied</option>
                                        <option value="shortlisted">Shortlisted</option>
                                        <option value="telephonic_scheduled">Telephonic Scheduled</option>
                                        <option value="telephonic_completed">Telephonic Completed</option>
                                        <option value="interview_scheduled">Interview Scheduled</option>
                                        <option value="interview_completed">Interview Completed</option>
                                        <option value="selected">Selected</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <button type="button" id="resetResumeFilters" class="btn btn-secondary">Reset</button>
                                    <button id="exportResumeCSV" style="margin-top: 10px;" class="btn btn-success">
                                        <i class="fa fa-file-excel"></i> Export to CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0" id="resumeManagementTable">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Candidate Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Date Received</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Resume Management Report Tab -->
<div id="candidate_shortlisting_report" class="tab-pane fade">
    <div class="row">
        <div class="col-md-12">
             Filters 
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Filters</h4>
                    <form id="candidateShortlistingFilterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" class="form-control" name="position_applied" id="shortlisted_position_applied" placeholder="e.g., Software Engineer">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Candidate Name</label>
                                    <input type="text" class="form-control" name="candidate_name" id="shortlisted_candidate_name" placeholder="First or Last name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" name="date_from" id="shortlisted_date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" name="date_to" id="shortlisted_date_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Resume Available</label>
                                    <select class="form-control" name="has_resume" id="shortlisted_has_resume">
                                        <option value="">All</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <button type="button" id="resetShortlistingFilters" class="btn btn-secondary">Reset</button>
                                    <button id="exportShortlistingCSV" style="margin-left: 10px;" class="btn btn-success">
                                        <i class="fa fa-file-excel"></i> Export to CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Note: This report lists candidates whose status is strictly “Shortlisted”.</p>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0" id="candidateShortlistingTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Candidate Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Date Received</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Interview Scheduling Report Tab -->
        <div id="interview_scheduling_report" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Filters</h4>
                            <form id="interviewSchedulingFilterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Job Title</label>
                                            <input type="text" class="form-control" name="job_title" id="interview_job_title">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Candidate Name</label>
                                            <input type="text" class="form-control" name="candidate_name" id="candidate_name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Interviewer Name</label>
                                            <input type="text" class="form-control" name="interviewer_name" id="interviewer_name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Interview Mode</label>
                                            <select class="form-control select2" name="interview_mode" id="interview_mode">
                                                <option value="">All Modes</option>
                                                <option value="telephonic">Telephonic</option>
                                                <option value="face_to_face">Face to Face</option>
                                                <option value="video_call">Video Call</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Interview Date From</label>
                                            <input type="date" class="form-control" name="interview_date_from" id="interview_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Interview Date To</label>
                                            <input type="date" class="form-control" name="interview_date_to" id="interview_date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Interview Status</label>
                                            <select class="form-control select2" name="interview_status" id="interview_status">
                                                <option value="">All Statuses</option>
                                                <option value="scheduled">Scheduled</option>
                                                <option value="completed">Completed</option>
                                                <option value="cancelled">Cancelled</option>
                                                <option value="rescheduled">Rescheduled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 30px;">
                                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                                            <button type="button" id="resetInterviewFilters" class="btn btn-secondary">Reset</button>
                                            <button id="exportInterviewCSV" style="margin-top: 10px;" class="btn btn-success">
                                                <i class="fa fa-file-excel"></i> Export to CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0" id="interviewSchedulingTable">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Candidate Name</th>
                                            <th>Interviewer</th>
                                            <th>Interview Mode</th>
                                            <th>Interview Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Interview Scheduling Report Tab -->
    </div>
</div>
<!-- /Page Content -->

<!-- View Job Modal -->
<div class="modal custom-modal fade" id="view_job_modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="jobDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
<!-- /View Job Modal -->

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#jobPostingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('interview-process.job-posting-report') }}",
            type: "POST",
            data: function (d) {
                d.job_title = $('#job_title').val();
                d.department = $('#department').val();
                d.location = $('#location').val();
                d.date_posted = $('#date_posted').val();
                d.status = $('#status').val();
                d._token = "{{ csrf_token() }}";
            },
            error: function(xhr, error, thrown) {
                console.log('AJAX Error:', xhr.responseText);
            }
        },
        columns: [
            { data: 'job_title', name: 'job_title' },
            { data: 'department', name: 'department' },
            { data: 'job_location', name: 'job_location' },
            { data: 'vacancies', name: 'vacancies' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    if (!data) return '';
                    var date = new Date(data);
                    return ('0' + date.getDate()).slice(-2) + '/' + 
                           ('0' + (date.getMonth()+1)).slice(-2) + '/' + 
                           date.getFullYear();
                }
            },
            { 
                data: 'end_date', 
                name: 'end_date',
                render: function(data) {
                    if (!data) return '';
                    var date = new Date(data);
                    return ('0' + date.getDate()).slice(-2) + '/' + 
                           ('0' + (date.getMonth()+1)).slice(-2) + '/' + 
                           date.getFullYear();
                }
            },
            { data: 'status', name: 'status' }
        ],
        language: {
            emptyTable: "No job postings found",
            zeroRecords: "No matching job postings found"
        }
    });

    // Apply filters
    $('#jobPostingFilterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#jobPostingFilterForm')[0].reset();
        $('.select2').val('').trigger('change');
        table.ajax.reload();
    });

    // Export to CSV
    $('#exportCSV').on('click', function() {
        var form = $('#jobPostingFilterForm');
        var url = "{{ route('interview-process.export-job-posting') }}";
        
        // Create a temporary form to submit with current filters
        var tempForm = $('<form>', {
            'action': url,
            'method': 'POST',
            'target': '_blank'
        }).append($('<input>', {
            'name': '_token',
            'value': "{{ csrf_token() }}",
            'type': 'hidden'
        }));
        
        // Add all filter values to the form
        form.find('input, select').each(function() {
            if (this.name) {
                tempForm.append($('<input>', {
                    'name': this.name,
                    'value': $(this).val(),
                    'type': 'hidden'
                }));
            }
        });
        
        $('body').append(tempForm);
        tempForm.submit();
        tempForm.remove();
    });
    // Initialize Interview Scheduling DataTable
    var interviewTable = $('#interviewSchedulingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('interview-process.interview-scheduling-report') }}",
            type: "POST",
            data: function (d) {
                d.job_title = $('#interview_job_title').val();
                d.candidate_name = $('#candidate_name').val();
                d.interviewer_name = $('#interviewer_name').val();
                d.interview_mode = $('#interview_mode').val();
                d.interview_date_from = $('#interview_date_from').val();
                d.interview_date_to = $('#interview_date_to').val();
                d.interview_status = $('#interview_status').val();
                d._token = "{{ csrf_token() }}";
            },
            error: function(xhr, error, thrown) {
                console.log('AJAX Error:', xhr.responseText);
            }
        },
        columns: [
            { data: 'job_title', name: 'job_title' },
            { data: 'candidate_name', name: 'candidate_name' },
            { data: 'interviewer_name', name: 'interviewer_name' },
            { 
                data: 'interview_type', 
                name: 'interview_type',
                render: function(data) {
                    if (!data) return '';
                    return data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
            },
            { 
                data: 'interview_datetime', 
                name: 'interview_datetime',
                render: function(data) {
                    if (!data) return '';
                    var date = new Date(data);
                    return date.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    if (!data) return '';
                    var badgeClass = '';
                    switch(data) {
                        case 'scheduled': badgeClass = 'bg-warning'; break;
                        case 'completed': badgeClass = 'bg-success'; break;
                        case 'cancelled': badgeClass = 'bg-danger'; break;
                        case 'rescheduled': badgeClass = 'bg-info'; break;
                        default: badgeClass = 'bg-secondary';
                    }
                    return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            }
        ],
        language: {
            emptyTable: "No interview records found",
            zeroRecords: "No matching interview records found"
        },
        responsive: true,
        autoWidth: true
    });

    // Apply filters for interview scheduling
    $('#interviewSchedulingFilterForm').on('submit', function(e) {
        e.preventDefault();
        interviewTable.ajax.reload();
    });

    // Reset filters for interview scheduling
    $('#resetInterviewFilters').on('click', function() {
        $('#interviewSchedulingFilterForm')[0].reset();
        $('#interview_mode, #interview_status').val('').trigger('change');
        interviewTable.ajax.reload();
    });

    // Export to CSV for interview scheduling
    $('#exportInterviewCSV').on('click', function(e) {
        e.preventDefault();
        
        // Get all current filter values
        var filters = {
            job_title: $('#interview_job_title').val(),
            candidate_name: $('#candidate_name').val(),
            interviewer_name: $('#interviewer_name').val(),
            interview_mode: $('#interview_mode').val(),
            interview_date_from: $('#interview_date_from').val(),
            interview_date_to: $('#interview_date_to').val(),
            interview_status: $('#interview_status').val(),
            _token: "{{ csrf_token() }}"
        };

        // Create a hidden iframe for the download
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Create a form inside the iframe
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('interview-process.export-interview-scheduling') }}";
        form.target = '_blank';

        // Add all filter values as hidden inputs
        Object.keys(filters).forEach(function(key) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = filters[key];
            form.appendChild(input);
        });

        // Append form to iframe and submit
        iframe.contentDocument.body.appendChild(form);
        form.submit();

        // Clean up after a short delay
        setTimeout(function() {
            document.body.removeChild(iframe);
        }, 1000);
    });
    // Initialize Resume Management DataTable
// Initialize Resume Management DataTable
var resumeTable = $('#resumeManagementTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('interview-process.resume-management-report') }}",
        type: "POST",
        data: function (d) {
            d.position_applied = $('#position_applied').val();
            d.candidate_name = $('#candidate_name').val();
            d.date_from = $('#date_from').val();
            d.date_to = $('#date_to').val();
            d.status = $('#status').val();
            d._token = "{{ csrf_token() }}";
        },
        error: function(xhr, error, thrown) {
            console.log('AJAX Error:', xhr.responseText);
            // Show error message to user
            alert('Failed to load data. Please check console for details.');
        }
    },
    columns: [
        { data: 'position_applied', name: 'position_applied' },
        { 
            data: 'full_name', 
            name: 'full_name',
            render: function(data, type, row) {
                return row.first_name + ' ' + row.last_name;
            }
        },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        { 
            data: 'created_at', 
            name: 'created_at',
            render: function(data) {
                if (!data) return '';
                var date = new Date(data);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        },
        { 
            data: 'status', 
            name: 'status',
            render: function(data) {
                if (!data) return '';
                var badgeClass = '';
                switch(data) {
                    case 'applied': badgeClass = 'bg-secondary'; break;
                    case 'shortlisted': badgeClass = 'bg-info'; break;
                    case 'telephonic_scheduled': badgeClass = 'bg-warning'; break;
                    case 'telephonic_completed': badgeClass = 'bg-primary'; break;
                    case 'interview_scheduled': badgeClass = 'bg-secondary'; break;
                    case 'interview_completed': badgeClass = 'bg-primary'; break;
                    case 'selected': badgeClass = 'bg-success'; break;
                    case 'rejected': badgeClass = 'bg-danger'; break;
                    default: badgeClass = 'bg-secondary';
                }
                return '<span class="badge ' + badgeClass + '">' + 
                       data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + 
                       '</span>';
            }
        }
    ],
    language: {
        emptyTable: "No resume records found",
        zeroRecords: "No matching resume records found",
        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
    },
    responsive: true,
    autoWidth: true
});

// Apply filters for resume management
$('#resumeManagementFilterForm').on('submit', function(e) {
    e.preventDefault();
    resumeTable.ajax.reload();
});

// Reset filters for resume management
$('#resetResumeFilters').on('click', function() {
    $('#resumeManagementFilterForm')[0].reset();
    $('#status').val('').trigger('change');
    resumeTable.ajax.reload();
});

// Export to CSV for resume management
$('#exportResumeCSV').on('click', function(e) {
    e.preventDefault();
    
    // Get all current filter values
    var filters = {
        position_applied: $('#position_applied').val(),
        candidate_name: $('#candidate_name').val(),
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val(),
        status: $('#status').val(),
        _token: "{{ csrf_token() }}"
    };

    // Create a hidden iframe for the download
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);

    // Create a form inside the iframe
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('interview-process.export-resume-management') }}";
    form.target = '_blank';

    // Add all filter values as hidden inputs
    Object.keys(filters).forEach(function(key) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = filters[key];
        form.appendChild(input);
    });

    // Append form to iframe and submit
    iframe.contentDocument.body.appendChild(form);
    form.submit();

    // Clean up after a short delay
    setTimeout(function() {
        document.body.removeChild(iframe);
    }, 1000);
});
// Candidate Shortlisting DataTable (ONLY 'shortlisted' status)
    var shortlistedTable = $('#candidateShortlistingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('interview-process.candidate-shortlisting-report') }}",
            type: "POST",
            data: function (d) {
                d.position_applied = $('#shortlisted_position_applied').val();
                d.candidate_name   = $('#shortlisted_candidate_name').val();
                d.date_from        = $('#shortlisted_date_from').val();
                d.date_to          = $('#shortlisted_date_to').val();
                d.has_resume       = $('#shortlisted_has_resume').val();
                d._token           = "{{ csrf_token() }}";
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr.responseText);
                alert('Failed to load shortlisted candidates.');
            }
        },
        columns: [
            { data: 'position_applied', name: 'position_applied' },
            { 
                data: 'full_name', 
                name: 'full_name',
                render: function(data, type, row) {
                    var first = row.first_name ? row.first_name : '';
                    var last  = row.last_name ? row.last_name : '';
                    return (first + ' ' + last).trim();
                }
            },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    if (!data) return '';
                    var date = new Date(data);
                    return date.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function() {
                    return '<span class="badge bg-info">Shortlisted</span>';
                }
            }
        ],
        language: {
            emptyTable: "No shortlisted candidates found",
            zeroRecords: "No matching shortlisted candidates found",
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        responsive: true,
        autoWidth: true
    });

    // Apply filters
    $('#candidateShortlistingFilterForm').on('submit', function(e) {
        e.preventDefault();
        shortlistedTable.ajax.reload();
    });

    // Reset filters
    $('#resetShortlistingFilters').on('click', function() {
        $('#candidateShortlistingFilterForm')[0].reset();
        shortlistedTable.ajax.reload();
    });

    // Export CSV
    $('#exportShortlistingCSV').on('click', function(e) {
        e.preventDefault();

        var filters = {
            position_applied: $('#shortlisted_position_applied').val(),
            candidate_name:   $('#shortlisted_candidate_name').val(),
            date_from:        $('#shortlisted_date_from').val(),
            date_to:          $('#shortlisted_date_to').val(),
            has_resume:       $('#shortlisted_has_resume').val(),
            _token:           "{{ csrf_token() }}"
        };

        // Hidden iframe form submission to start download
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('interview-process.export-candidate-shortlisting') }}";
        form.target = '_blank';

        Object.keys(filters).forEach(function(key) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = filters[key];
            form.appendChild(input);
        });

        iframe.contentDocument.body.appendChild(form);
        form.submit();

        setTimeout(function() {
            document.body.removeChild(iframe);
        }, 1000);
    });
});
</script>
@endsection
