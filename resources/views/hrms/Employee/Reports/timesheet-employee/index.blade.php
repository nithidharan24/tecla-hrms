@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Timesheet Employee Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Reports</a></li>
                        <li class="breadcrumb-item active">Timesheet Employee Report</li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50">Total Hours</h6>
                                <h3 class="mb-0">{{ number_format($summary['total_hours'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fa fa-clock-o fa-3x text-white-50"></i>
                        </div>
                        <small class="text-white-50">{{ $summary['total_entries'] ?? 0 }} entries</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50">Approved Hours</h6>
                                <h3 class="mb-0">{{ number_format($summary['approved_hours'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fa fa-check-circle fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50">Pending Hours</h6>
                                <h3 class="mb-0">{{ number_format($summary['pending_hours'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fa fa-hourglass-half fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50">Rejected Hours</h6>
                                <h3 class="mb-0">{{ number_format($summary['rejected_hours'] ?? 0, 2) }}</h3>
                            </div>
                            <i class="fa fa-times-circle fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('timesheet-employee-reports.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects ?? [] as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->projectname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input name="from" value="{{ request('from') }}" class="form-control datepicker" type="text" placeholder="DD-MM-YYYY" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input name="to" value="{{ request('to') }}" class="form-control datepicker" type="text" placeholder="DD-MM-YYYY" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('timesheet-employee-reports.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('timesheet-employee-reports.export.csv', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Export to CSV
                </a>
                <a href="{{ route('timesheet-employee-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf-o"></i> Export to PDF
                </a>
            </div>
        </div>

        <!-- Timesheets Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0 datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Project</th>
                                <th>Week Period</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Comments</th>
                                <th>Approved Details</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timesheets ?? [] as $timesheet)
                            <tr>
                                <td>#{{ $timesheet->id }}</td>
                                <td>
                                    @php
                                        $empDetail = $employeeDetails[$timesheet->id] ?? null;
                                    @endphp
                                    @if($empDetail)
                                        <div>
                                            <strong>{{ $empDetail['full_name'] }}</strong>
                                            <br>
                                            <small class="text-muted">ID: {{ $empDetail['employee_code'] }}</small>
                                            @if(isset($empDetail['department']))
                                                <br>
                                                <small class="text-muted">{{ $empDetail['department'] }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Employee ID: {{ $timesheet->employee_id }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($timesheet->project)
                                        <strong>{{ $timesheet->project->projectname }}</strong>
                                        @if(isset($timesheet->project->id))
                                            <br>
                                            <small class="text-muted">ID: {{ $timesheet->project->id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $weekStart = \Carbon\Carbon::parse($timesheet->week_start);
                                        $weekEnd = $weekStart->copy()->addDays(6);
                                    @endphp
                                    <div>
                                        <strong>{{ $weekStart->format('d M Y') }}</strong>
                                        <br>
                                        <small class="text-muted">to {{ $weekEnd->format('d M Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6 p-2">
                                        <i class="fa fa-clock-o"></i> {{ number_format($timesheet->hours, 2) }} hrs
                                    </span>
                                </td>
                                <td>
                                    @if($timesheet->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="fa fa-check-circle"></i> Approved
                                        </span>
                                    @elseif($timesheet->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fa fa-hourglass-half"></i> Pending
                                        </span>
                                    @elseif($timesheet->status == 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="fa fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($timesheet->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($timesheet->comments)
                                        <span class="d-inline-block text-truncate" style="max-width: 150px;" 
                                              data-bs-toggle="tooltip" title="{{ $timesheet->comments }}">
                                            {{ $timesheet->comments }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($timesheet->approved_by && $timesheet->approved_at)
                                        <div>
                                            <small>
                                                <i class="fa fa-user"></i> 
                                                @php
                                                    $approver = DB::table('allemployees')->where('id', $timesheet->approved_by)->first();
                                                @endphp
                                                {{ $approver ? ($approver->firstname . ' ' . $approver->lastname) : 'Admin' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fa fa-calendar"></i> 
                                                {{ \Carbon\Carbon::parse($timesheet->approved_at)->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">Not approved yet</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary view-timesheet" 
                                            data-id="{{ $timesheet->id }}"
                                            data-bs-toggle="modal" data-bs-target="#viewTimesheetModal">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fa fa-clock-o fa-4x text-muted mb-3"></i>
                                        <h5>No Timesheets Found</h5>
                                        <p class="text-muted">Try adjusting your filters or create a new timesheet.</p>
                                        @if(Session::get('role') === 'employee')
                                            <a href="{{ route('timesheet.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Create Timesheet
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(isset($timesheets) && method_exists($timesheets, 'links'))
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $timesheets->firstItem() ?? 0 }} to {{ $timesheets->lastItem() ?? 0 }} 
                        of {{ $timesheets->total() }} entries
                    </div>
                    <div>
                        {{ $timesheets->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- View Timesheet Modal -->
<div class="modal fade" id="viewTimesheetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Timesheet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="timesheetDetails">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .empty-state {
        padding: 40px 20px;
    }
    .empty-state i {
        opacity: 0.5;
    }
    .card-title {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .datatable tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table-center td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize datepickers
    if ($('.datepicker').length > 0) {
        $('.datepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            useCurrent: false,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-arrow-up',
                down: 'fa fa-arrow-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        });
    }

    // Success message fade out
    setTimeout(function() {
        $('#successMessage').fadeOut('slow');
    }, 3000);

    // View Timesheet Details
    $('.view-timesheet').click(function() {
        var timesheetId = $(this).data('id');
        
        $('#timesheetDetails').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        $.ajax({
            url: '/timesheet-employee-reports/' + timesheetId + '/details',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var html = generateTimesheetDetails(data);
                    $('#timesheetDetails').html(html);
                } else {
                    $('#timesheetDetails').html('<div class="alert alert-danger">Failed to load details</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#timesheetDetails').html('<div class="alert alert-danger">Error loading details. Please try again.</div>');
            }
        });
    });

    function generateTimesheetDetails(data) {
        var timesheet = data.timesheet;
        var employee = data.employee;
        var weekStart = moment(timesheet.week_start);
        var weekEnd = moment(timesheet.week_start).add(6, 'days');
        
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Employee Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>${employee ? (employee.firstname + ' ' + employee.lastname) : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Employee ID:</th>
                                    <td>${employee ? (employee.employeeid || 'N/A') : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>${employee ? (employee.email || 'N/A') : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Department:</th>
                                    <td>${employee ? (employee.department || 'N/A') : 'N/A'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Timesheet Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Timesheet ID:</th>
                                    <td>#${timesheet.id}</td>
                                </tr>
                                <tr>
                                    <th>Project:</th>
                                    <td>${timesheet.project ? timesheet.project.projectname : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Week Period:</th>
                                    <td>${weekStart.format('DD MMM YYYY')} - ${weekEnd.format('DD MMM YYYY')}</td>
                                </tr>
                                <tr>
                                    <th>Total Hours:</th>
                                    <td><span class="badge bg-info">${parseFloat(timesheet.hours).toFixed(2)} hrs</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>${getStatusBadge(timesheet.status)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Comments & Additional Details</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Comments:</strong> ${timesheet.comments || 'No comments'}</p>
                            ${timesheet.rejection_reason ? 
                                `<div class="alert alert-danger mt-2">
                                    <strong>Rejection Reason:</strong><br>
                                    ${timesheet.rejection_reason}
                                </div>` : ''}
                            <hr>
                            <small class="text-muted">
                                Created: ${moment(timesheet.created_at).format('DD MMM YYYY HH:mm')}<br>
                                Last Updated: ${moment(timesheet.updated_at).format('DD MMM YYYY HH:mm')}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getStatusBadge(status) {
        switch(status) {
            case 'approved':
                return '<span class="badge bg-success">Approved</span>';
            case 'pending':
                return '<span class="badge bg-warning">Pending</span>';
            case 'rejected':
                return '<span class="badge bg-danger">Rejected</span>';
            default:
                return '<span class="badge bg-secondary">' + status + '</span>';
        }
    }
});
</script>
@endpush
@endsection