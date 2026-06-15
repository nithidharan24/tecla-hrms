@extends('layouts.index')

@section('content')
<div >
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">
                        <i class="fas fa-calendar-day mr-2"></i>Daily Attendance Report
                    </h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#">Reports</a></li>
                        <li class="breadcrumb-item active">Daily Report</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download mr-1"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button type="submit" form="reportForm" name="export" value="pdf" class="dropdown-item">
                                <i class="fas fa-file-pdf text-danger mr-1"></i> PDF
                            </button>
                            <button type="submit" form="reportForm" name="export" value="csv" class="dropdown-item">
                                <i class="fas fa-file-excel text-success mr-1"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Employees</h5>
                                <h2 class="text-primary">{{ $totalEmployees }}</h2>
                            </div>
                            <div class="stat-icon bg-primary-light">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Present Today</h5>
                                <h2 class="text-success">{{ $todayPresent }}</h2>
                                <small class="text-muted">{{ number_format(($todayPresent/$totalEmployees)*100, 1) }}% of staff</small>
                            </div>
                            <div class="stat-icon bg-success-light">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Absent Today</h5>
                                <h2 class="text-danger">{{ $todayAbsent }}</h2>
                                <small class="text-muted">{{ number_format(($todayAbsent/$totalEmployees)*100, 1) }}% of staff</small>
                            </div>
                            <div class="stat-icon bg-danger-light">
                                <i class="fas fa-user-times text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('daily-report.index') }}" id="reportForm">
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group">
                                <label>Employee Name</label>
                                <input class="form-control" type="text" name="employee_name" value="{{ request('employee_name') }}" placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group">
                                <label>Department</label>
                                <select class="select form-control" name="department"> 
                                    <option value="">All Departments</option>
                                    <option value="Designing" {{ request('department') == 'Designing' ? 'selected' : '' }}>Designing</option>
                                    <option value="Development" {{ request('department') == 'Development' ? 'selected' : '' }}>Development</option>
                                    <option value="Finance" {{ request('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Hr & Finance" {{ request('department') == 'Hr & Finance' ? 'selected' : '' }}>Hr & Finance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group">
                                <label>From Date</label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="from_date" value="{{ request('from_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group">
                                <label>To Date</label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="to_date" value="{{ request('to_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="reset" class="btn btn-light mr-2">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Search Filter -->

        <!-- Attendance Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
    <table class="table table-hover table-center mb-0 datatable" id="leavesTable">
        <thead class="thead-light">
            <tr>
               
                <th>Employee</th>
                <th>Date</th>
                <th>Department</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaves as $index => $leave)
                <tr>
                   
                    <td data-label="Employee Name">
                        <h2 class="table-avatar">
                            <a class="high">{{ $leave->employee_name }}</a>
                        </h2>
                    </td>
                    <td data-label="Date">{{ \Carbon\Carbon::parse($leave->date)->format('d M, Y') }}</td>
                    <td data-label="Department">
                        <span class="badge badge-pill bg-info-light">{{ $leave->department }}</span>
                    </td>
                    <td data-label="Status" class="text-center high">
                        @if ($leave->status === 'Absent')
                            <span class="badge badge-pill bg-danger-light">
                                <i class="fas fa-user-times mr-1"></i> {{ ucfirst($leave->status) }}
                            </span>
                        @elseif ($leave->status === 'Present')
                            <span class="badge badge-pill bg-success-light">
                                <i class="fas fa-check-circle mr-1"></i> {{ ucfirst($leave->status) }}
                            </span>
                        @else
                            <span class="badge badge-pill bg-warning-light">
                                <i class="fas fa-question-circle mr-1"></i> {{ ucfirst($leave->status) }}
                            </span>
                        @endif
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all / row highlight
    $('#checkAllLeaves').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>

            </div>
        </div>
        <!-- /Attendance Table -->
    </div>
</div>

<style>
    .stat-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: none;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .filter-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .table-avatar .avatar {
        width: 40px;
        height: 40px;
    }
    .badge-pill {
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .bg-primary-light {
        background-color: rgba(70, 127, 207, 0.1);
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize date picker
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false,
            icons: {
                up: "fas fa-chevron-up",
                down: "fas fa-chevron-down",
                next: 'fas fa-chevron-right',
                previous: 'fas fa-chevron-left'
            }
        });
        
        // Initialize select2
        $('.select').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    });
</script>
@endsection