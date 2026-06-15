@extends('layouts.index')

@section('content')
<style>
    #employeeDirectoryTable, #attendanceReportTable { width: 100% !important; }
    #employeeDirectoryTable thead th, #attendanceReportTable thead th { white-space: nowrap; }
    .table-responsive { overflow-x: auto; }
    .btn-export { margin-top: 10px; }
</style>

<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Employee Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#employee_directory_report" data-bs-toggle="tab" class="nav-link active">Employee Directory</a></li>
                    <li class="nav-item"><a href="#attendance_report" data-bs-toggle="tab" class="nav-link">Attendance Report</a></li>
                     <li class="nav-item"><a href="#overtime_report" data-bs-toggle="tab" class="nav-link">Overtime Report</a></li>
                     <li class="nav-item"><a href="#schedule_report" data-bs-toggle="tab" class="nav-link">Schedule Report</a></li>
                     <li class="nav-item"><a href="#leave_report" data-bs-toggle="tab" class="nav-link">Leave Report</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div id="employee_directory_report" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-12">

                   <div class="card">
    <div class="card-body">
      <div class="filter-section bg-light p-3 rounded-3 shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-semibold text-primary mb-0">
                <i class="fas fa-filter me-2"></i>Filters
            </h4>
        </div>
        <div class="export-buttons">
            <button id="exportEmpDirCSV" type="button" class="btn btn-outline-success btn-sm me-2">
                <i class="fas fa-file-csv me-1"></i>  CSV
            </button>
            <button id="exportEmpDirPDF" type="button" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i>  PDF
            </button>
        </div>
    </div>
</div>



        <form id="employeeDirectoryFilterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" name="employee_id" id="emp_employee_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <input type="text" class="form-control" name="employee_name" id="emp_employee_name" placeholder="First or Last name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Department</label>
                        <select class="form-control select2" name="department_id" id="emp_department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @php $showBranchFilter = $branches->count() > 1; @endphp
                @if($showBranchFilter)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control select2" name="branch_id" id="emp_branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Designation</label>
                        <select class="form-control select2" name="designation_id" id="emp_designation_id">
                            <option value="">All Designations</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->designation }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Joining From</label>
                        <input type="date" class="form-control" name="joining_from" id="emp_joining_from">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Joining To</label>
                        <input type="date" class="form-control" name="joining_to" id="emp_joining_to">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group ms-auto">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <button type="button" id="resetEmpDirFilters" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0" id="employeeDirectoryTable">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Branch</th>
                                            <th>Joining Date</th>
                                            <th>Hierarchy</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="attendance_report" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
                <button id="exportAttendanceCSV" type="button" class="btn btn-success btn-export">
                    <i class="fa fa-file-excel"></i> CSV
                </button>
                <button id="exportAttendancePDF" type="button" class="btn btn-danger btn-export">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
            </div>
        </div>

        <form id="attendanceFilterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" class="form-control" name="date_from" id="att_date_from">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" name="date_to" id="att_date_to">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" name="employee_id" id="att_employee_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <input type="text" class="form-control" name="employee_name" id="att_employee_name">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Department</label>
                        <select class="form-control select2" name="department_id" id="att_department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($branches->count() > 1)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control select2" name="branch_id" id="att_branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control select2" name="status" id="att_status">
                            <option value="">All</option>
                            <option value="on_time">On Time</option>
                            <option value="early">Early Arrival</option>
                            <option value="late">Late</option>
                            <option value="early_departure">Early Departure</option>
                            <option value="completed">Completed</option>
                            <option value="overtime">Overtime</option>
                            <option value="no_schedule">No Schedule</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Min Work Hours</label>
                        <input type="number" step="0.25" class="form-control" name="min_work_hours" id="att_min_work_hours" placeholder="e.g., 8">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check" style="margin-top: 8px;">
                        <input class="form-check-input" type="checkbox" value="1" id="att_overtime_only">
                        <label class="form-check-label" for="att_overtime_only">Overtime only</label>
                    </div>
                </div>
                <div class="col-md-9 d-flex align-items-end">
                    <div class="form-group ms-auto">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <button type="button" id="resetAttFilters" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0" id="attendanceReportTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Employee ID</th>
                                            <th>Full Name</th>
                                            <th>Department</th>
                                            <th>Branch</th>
                                            <th>Shift</th>
                                            <th>Punch In</th>
                                            <th>Punch Out</th>
                                            <th>Working Hours</th>
                                            <th>Break (min)</th>
                                            <th>Overtime (h)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody> AJAX </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
          <!-- Overtime Report -->
        <div id="overtime_report" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">
                  <div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
                <button id="exportOvertimeCSV" type="button" class="btn btn-success btn-export">
                    <i class="fa fa-file-excel"></i> CSV
                </button>
                <button id="exportOvertimePDF" type="button" class="btn btn-danger btn-export">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
            </div>
        </div>

        <form id="overtimeFilterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Month</label>
                        <select class="form-control select2" name="month" id="ov_month">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Year</label>
                        <select class="form-control select2" name="year" id="ov_year">
                            @for($i=date('Y')-5; $i<=date('Y')+1; $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control select2" name="status" id="ov_status">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" name="employee_id" id="ov_employee_id">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <input type="text" class="form-control" name="employee_name" id="ov_employee_name">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Department</label>
                        <select class="form-control select2" name="department_id" id="ov_department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($branches->count() > 1)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control select2" name="branch_id" id="ov_branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group ms-auto">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <button type="button" id="resetOvertimeFilters" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped custom-table mb-0" id="overtimeReportTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Employee ID</th>
                                            <th>Full Name</th>
                                            <th>Department</th>
                                            <th>Branch</th>
                                            <th>Overtime Hours</th>
                                            <th>Rate (₹/hr)</th>
                                            <th>Amount (₹)</th>
                                            <th>Status</th>
                                            <th>Approved By</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Overtime Report -->
         
<div id="schedule_report" class="tab-pane fade">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Filters</h4>
                        <div>
                            <button id="exportScheduleCSV" type="button" class="btn btn-success btn-export">
                                <i class="fa fa-file-excel"></i> CSV
                            </button>
                            <button id="exportSchedulePDF" type="button" class="btn btn-danger btn-export">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </button>
                        </div>
                    </div>

                    <form id="scheduleFilterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" name="date_from" id="sch_date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" name="date_to" id="sch_date_to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input type="text" class="form-control" name="employee_id" id="sch_employee_id">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employee Name</label>
                                    <input type="text" class="form-control" name="employee_name" id="sch_employee_name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control select2" name="department_id" id="sch_department_id">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($branches->count() > 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select class="form-control select2" name="branch_id" id="sch_branch_id">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" id="sch_status">
                                        <option value="">All</option>
                                        <option value="active">Active</option>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Publish Status</label>
                                    <select class="form-control select2" name="publish_status" id="sch_publish_status">
                                        <option value="all">All</option>
                                        <option value="1">Published</option>
                                        <option value="0">Draft</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9 d-flex align-items-end">
                                <div class="form-group ms-auto">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <button type="button" id="resetSchFilters" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0" id="scheduleReportTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Branch</th>
                                    <th>Shift</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Break (min)</th>
                                    <th>Schedule Start</th>
                                    <th>Schedule End</th>
                                    <th>Repeat (weeks)</th>
                                    <th>Publish Status</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Leave Report -->
<div id="leave_report" class="tab-pane fade">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Filters</h4>
                        <div>
                            <button id="exportLeaveCSV" type="button" class="btn btn-success btn-export">
                                <i class="fa fa-file-excel"></i> CSV
                            </button>
                            <button id="exportLeavePDF" type="button" class="btn btn-danger btn-export">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </button>
                        </div>
                    </div>

                    <form id="leaveFilterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" name="date_from" id="leave_date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" name="date_to" id="leave_date_to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input type="text" class="form-control" name="employee_id" id="leave_employee_id">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employee Name</label>
                                    <input type="text" class="form-control" name="employee_name" id="leave_employee_name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control select2" name="department_id" id="leave_department_id">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($branches->count() > 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select class="form-control select2" name="branch_id" id="leave_branch_id">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Leave Type</label>
                                    <select class="form-control select2" name="leave_type" id="leave_type">
                                        <option value="all">All Types</option>
                                        <option value="Casual Leave">Casual Leave</option>
                                        <option value="Sick">Sick Leave</option>
                                        <option value="Hospitalisation">Hospitalisation</option>
                                        <option value="Maternity Leave">Maternity Leave</option>
                                        <option value="Paternity Leave">Paternity Leave</option>
                                        <option value="LOP">Loss of Pay (LOP)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" id="leave_status">
                                        <option value="all">All Statuses</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 d-flex align-items-end">
                                <div class="form-group ms-auto">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <button type="button" id="resetLeaveFilters" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped custom-table mb-0" id="leaveReportTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Branch</th>
                                    <th>Leave Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script>
$(function() {
    // Employee Directory
    var empDirTable = $('#employeeDirectoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-reports.employee-directory-report') }}",
            type: "POST",
            data: function(d) {
                d.employee_id   = $('#emp_employee_id').val();
                d.employee_name = $('#emp_employee_name').val();
                d.department_id = $('#emp_department_id').val();
                d.designation_id= $('#emp_designation_id').val();
                d.branch_id     = $('#emp_branch_id').length ? $('#emp_branch_id').val() : '';
                d.joining_from  = $('#emp_joining_from').val();
                d.joining_to    = $('#emp_joining_to').val();
                d._token        = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'employeeid', name: 'employeeid' },
            { data: 'full_name', name: 'full_name', render: (d, t, r) => (r.firstname || '') + ' ' + (r.lastname || '') },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'department_name', name: 'department_name' },
            { data: 'designation_name', name: 'designation_name' },
            { data: 'branch_name', name: 'branch_name' },
            { data: 'joiningdate', name: 'joiningdate', render: function(data){ if(!data) return ''; var date=new Date(data); return ('0'+date.getDate()).slice(-2)+'/'+('0'+(date.getMonth()+1)).slice(-2)+'/'+date.getFullYear(); } },
            { data: 'hierarchy_level', name: 'hierarchy_level' },
        ],
        order: [[7, 'desc']],
        language: { emptyTable: "No employees found", zeroRecords: "No matching employees found" }
    });

    $('#employeeDirectoryFilterForm').on('submit', function(e){ e.preventDefault(); empDirTable.ajax.reload(); });
    $('#resetEmpDirFilters').on('click', function(){
        $('#employeeDirectoryFilterForm')[0].reset();
        $('.select2').val('').trigger('change');
        empDirTable.ajax.reload();
    });

    // CSV export
    $('#exportEmpDirCSV').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-employee-directory') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#employeeDirectoryFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        $('body').append(form); form.submit(); form.remove();
    });

    // PDF export
    $('#exportEmpDirPDF').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-employee-directory-pdf') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#employeeDirectoryFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        $('body').append(form); form.submit(); form.remove();
    });

    // Attendance
    var attTable = $('#attendanceReportTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-reports.attendance-report') }}",
            type: "POST",
            data: function(d) {
                d.date_from       = $('#att_date_from').val();
                d.date_to         = $('#att_date_to').val();
                d.employee_id     = $('#att_employee_id').val();
                d.employee_name   = $('#att_employee_name').val();
                d.department_id   = $('#att_department_id').val();
                d.branch_id       = $('#att_branch_id').length ? $('#att_branch_id').val() : '';
                d.status          = $('#att_status').val();
                d.min_work_hours  = $('#att_min_work_hours').val();
                d.overtime_only   = $('#att_overtime_only').is(':checked') ? 1 : 0;
                d._token          = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'date', name: 'date', render: d => d ? new Date(d).toLocaleDateString('en-GB') : '' },
            { data: 'employeeid', name: 'employeeid' },
            { data: 'full_name', name: 'full_name', render: (d,t,r) => (r.firstname || '') + ' ' + (r.lastname || '') },
            { data: 'department_name', name: 'department_name' },
            { data: 'branch_name', name: 'branch_name' },
            { data: 'shift_name', name: 'shift_name' },
            { data: 'punch_in', name: 'punch_in', render: d => d ? new Date(d).toLocaleString() : '' },
            { data: 'punch_out', name: 'punch_out', render: d => d ? new Date(d).toLocaleString() : '' },
            { data: 'working_hours', name: 'working_hours', render: d => (d==null ? '' : Number(d).toFixed(2)) },
            { data: 'total_break_taken', name: 'total_break_taken' },
            { data: 'overtime_hours', name: 'overtime_hours' },
            { data: 'status', name: 'status', render: function(data){
                if(!data) return '';
                var cls='bg-secondary';
                switch(data){case 'on_time':cls='bg-success';break;case 'early':cls='bg-info';break;case 'late':cls='bg-warning';break;case 'early_departure':cls='bg-danger';break;case 'completed':cls='bg-primary';break;case 'overtime':cls='bg-dark';break;}
                return '<span class="badge '+cls+'">'+ data.replace(/_/g,' ').replace(/\b\w/g, s=>s.toUpperCase()) +'</span>';
            }},
        ],
        order: [[0, 'desc']],
        language: { emptyTable: "No attendance records found", zeroRecords: "No matching attendance records found" }
    });

    $('#attendanceFilterForm').on('submit', function(e){ e.preventDefault(); attTable.ajax.reload(); });
    $('#resetAttFilters').on('click', function(){
        $('#attendanceFilterForm')[0].reset();
        $('.select2').val('').trigger('change');
        $('#att_overtime_only').prop('checked', false);
        attTable.ajax.reload();
    });

    // CSV export
    $('#exportAttendanceCSV').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-attendance') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#attendanceFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        form.append($('<input>', { type:'hidden', name:'overtime_only', value: $('#att_overtime_only').is(':checked') ? 1 : 0 }));
        $('body').append(form); form.submit(); form.remove();
    });

    // PDF export
    $('#exportAttendancePDF').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-attendance-pdf') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#attendanceFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        form.append($('<input>', { type:'hidden', name:'overtime_only', value: $('#att_overtime_only').is(':checked') ? 1 : 0 }));
        $('body').append(form); form.submit(); form.remove();
    });
     var overtimeTable = $('#overtimeReportTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('employee-reports.overtime-report') }}",
            type: "POST",
            data: function(d) {
                d.month = $('#ov_month').val();
                d.year = $('#ov_year').val();
                d.status = $('#ov_status').val();
                d.employee_id = $('#ov_employee_id').val();
                d.employee_name = $('#ov_employee_name').val();
                d.department_id = $('#ov_department_id').val();
                d.branch_id = $('#ov_branch_id').length ? $('#ov_branch_id').val() : '';
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'overtime_date', name: 'overtime_date', render: d => d ? new Date(d).toLocaleDateString('en-GB') : '' },
            { data: 'employeeid', name: 'employeeid' },
            { data: 'full_name', name: 'full_name', render: (d,t,r) => (r.firstname || '') + ' ' + (r.lastname || '') },
            { data: 'department_name', name: 'department_name' },
            { data: 'branch_name', name: 'branch_name' },
            { data: 'overtime_hours', name: 'overtime_hours' },
            { data: 'overtime_rate', name: 'overtime_rate' },
            { data: 'overtime_amount', name: 'overtime_amount' },
            { data: 'status', name: 'status', render: function(data){
                if(!data) return '';
                var cls = 'bg-secondary';
                switch(data) {
                    case 'approved': cls = 'bg-success'; break;
                    case 'pending': cls = 'bg-warning'; break;
                    case 'rejected': cls = 'bg-danger'; break;
                }
                return '<span class="badge '+cls+'">'+ data.charAt(0).toUpperCase() + data.slice(1) +'</span>';
            }},
            { data: 'approver_name', name: 'approver_name', render: d => d || 'N/A' },
        ],
        order: [[0, 'desc']],
        language: { emptyTable: "No overtime records found", zeroRecords: "No matching overtime records found" }
    });

    $('#overtimeFilterForm').on('submit', function(e){ e.preventDefault(); overtimeTable.ajax.reload(); });
    $('#resetOvertimeFilters').on('click', function(){
        $('#overtimeFilterForm')[0].reset();
        $('.select2').val('').trigger('change');
        $('#ov_month').val(new Date().getMonth() + 1).trigger('change');
        $('#ov_year').val(new Date().getFullYear()).trigger('change');
        overtimeTable.ajax.reload();
    });

    // CSV export
    $('#exportOvertimeCSV').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-overtime') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#overtimeFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        $('body').append(form); form.submit(); form.remove();
    });

    // PDF export
    $('#exportOvertimePDF').on('click', function(e){
        e.preventDefault();
        var url = "{{ route('employee-reports.export-overtime-pdf') }}";
        var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
            .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
        $('#overtimeFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
        $('body').append(form); form.submit(); form.remove();
    });

    // Schedule Report Table
var schTable = $('#scheduleReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('employee-reports.schedule-report') }}",
        type: "POST",
        data: function(d) {
            d.date_from = $('#sch_date_from').val();
            d.date_to = $('#sch_date_to').val();
            d.employee_id = $('#sch_employee_id').val();
            d.employee_name = $('#sch_employee_name').val();
            d.department_id = $('#sch_department_id').val();
            d.branch_id = $('#sch_branch_id').length ? $('#sch_branch_id').val() : '';
            d.shift_id = $('#sch_shift_id').val();
            d.status = $('#sch_status').val();
            d.publish_status = $('#sch_publish_status').val();
            d._token = "{{ csrf_token() }}";
        }
    },
    columns: [
        { data: 'employeeid', name: 'employeeid' },
        { data: 'full_name', name: 'full_name', render: (d,t,r) => (r.firstname || '') + ' ' + (r.lastname || '') },
        { data: 'department_name', name: 'department_name' },
        { data: 'branch_name', name: 'branch_name' },
        { data: 'shift_name', name: 'shift_name' },
        { data: 'formatted_start_time', name: 'formatted_start_time' },
        { data: 'formatted_end_time', name: 'formatted_end_time' },
        { data: 'break_time', name: 'break_time' },
        { data: 'formatted_start_date', name: 'formatted_start_date' },
        { data: 'formatted_end_date', name: 'formatted_end_date' },
        { data: 'repeat_every_week', name: 'repeat_every_week' },
        { data: 'publish_status', name: 'publish_status', 
          render: function(data, type, row) {
              return '<span class="badge ' + row.publish_class + '">' + data + '</span>';
          }
        },
        { data: 'status', name: 'status', 
          render: function(data, type, row) {
              return '<span class="badge ' + row.status_class + '">' + data + '</span>';
          }
        },
        { data: 'formatted_created_at', name: 'formatted_created_at' }
    ],
    order: [[8, 'desc']], // Default order by schedule start date
    language: { emptyTable: "No schedules found", zeroRecords: "No matching schedules found" }
});

$('#scheduleFilterForm').on('submit', function(e){ e.preventDefault(); schTable.ajax.reload(); });
$('#resetSchFilters').on('click', function(){
    $('#scheduleFilterForm')[0].reset();
    $('.select2').val('').trigger('change');
    schTable.ajax.reload();
});

// CSV export
$('#exportScheduleCSV').on('click', function(e){
    e.preventDefault();
    var url = "{{ route('employee-reports.export-schedule-report') }}";
    var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
        .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
    $('#scheduleFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
    $('body').append(form); form.submit(); form.remove();
});

// PDF export
$('#exportSchedulePDF').on('click', function(e){
    e.preventDefault();
    var url = "{{ route('employee-reports.export-schedule-report-pdf') }}";
    var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
        .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
    $('#scheduleFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
    $('body').append(form); form.submit(); form.remove();
});
// Leave Report Table
var leaveTable = $('#leaveReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('employee-reports.leave-report') }}",
        type: "POST",
        data: function(d) {
            d.date_from = $('#leave_date_from').val();
            d.date_to = $('#leave_date_to').val();
            d.employee_id = $('#leave_employee_id').val();
            d.employee_name = $('#leave_employee_name').val();
            d.department_id = $('#leave_department_id').val();
            d.branch_id = $('#leave_branch_id').length ? $('#leave_branch_id').val() : '';
            d.leave_type = $('#leave_type').val();
            d.status = $('#leave_status').val();
            d._token = "{{ csrf_token() }}";
        }
    },
    columns: [
        { data: 'employeeid', name: 'employeeid' },
        { data: 'full_name', name: 'full_name' },
        { data: 'department_name', name: 'department_name' },
        { data: 'branch_name', name: 'branch_name' },
        { data: 'leave_type', name: 'leave_type' },
        { data: 'from_date', name: 'from_date', render: d => d ? new Date(d).toLocaleDateString('en-GB') : '' },
        { data: 'to_date', name: 'to_date', render: d => d ? new Date(d).toLocaleDateString('en-GB') : '' },
        { data: 'no_of_days', name: 'no_of_days' },
        { data: 'leave_reason', name: 'leave_reason' },
        { data: 'status', name: 'status', render: function(data){
            if(!data) return '';
            var cls = 'bg-secondary';
            switch(data) {
                case 'approved': cls = 'bg-success'; break;
                case 'pending': cls = 'bg-warning'; break;
                case 'declined': cls = 'bg-danger'; break;
                case 'new': cls = 'bg-info'; break;
            }
            return '<span class="badge '+cls+'">'+ data.charAt(0).toUpperCase() + data.slice(1) +'</span>';
        }},
        { data: 'created_at', name: 'created_at', render: d => d ? new Date(d).toLocaleString() : '' }
    ],
    order: [[5, 'desc']], // Default order by from_date
    language: { emptyTable: "No leave records found", zeroRecords: "No matching leave records found" }
});

$('#leaveFilterForm').on('submit', function(e){ e.preventDefault(); leaveTable.ajax.reload(); });
$('#resetLeaveFilters').on('click', function(){
    $('#leaveFilterForm')[0].reset();
    $('.select2').val('').trigger('change');
    $('#leave_status').val('all').trigger('change');
    $('#leave_type').val('all').trigger('change');
    leaveTable.ajax.reload();
});

// CSV export
$('#exportLeaveCSV').on('click', function(e){
    e.preventDefault();
    var url = "{{ route('employee-reports.export-leave-report') }}";
    var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
        .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
    $('#leaveFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
    $('body').append(form); form.submit(); form.remove();
});

// PDF export
$('#exportLeavePDF').on('click', function(e){
    e.preventDefault();
    var url = "{{ route('employee-reports.export-leave-report-pdf') }}";
    var form = $('<form>', { action: url, method: 'POST', target: '_blank' })
        .append($('<input>', { type: 'hidden', name: '_token', value: "{{ csrf_token() }}" }));
    $('#leaveFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>', { type:'hidden', name:this.name, value:$(this).val() })); });
    $('body').append(form); form.submit(); form.remove();
});
});

</script>
<style>
.filter-section {
    border-left: 4px solid #0d6efd;
}
.export-buttons .btn {
    transition: all 0.2s ease;
}
.export-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
@endsection