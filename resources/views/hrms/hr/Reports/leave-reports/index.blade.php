@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Leave Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leave Report</li>
                    </ul>
                </div>
                <div class="col-auto">
    <div class="btn-group">
        <a href="{{ route('leave-reports.pdf', ['employee' => request('employee'), 'department' => request('department'), 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
           class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="{{ route('leave-reports.csv', ['employee' => request('employee'), 'department' => request('department'), 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
           class="btn btn-success">
            <i class="fas fa-file-csv"></i> CSV
        </a>
    </div>
</div>
            </div>
        </div>

        <form method="GET" action="{{ route('leave-reports.index') }}">
            <div class="row filter-row mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <input type="text" name="employee" class="form-control floating" value="{{ request('employee') }}">
                        <label class="focus-label">Employee</label>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
    <div class="input-block mb-3 form-focus select-focus">
        <select name="department" class="select floating">
            <option value="">Select Department</option>
            @foreach($departments as $id => $name)
                <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <label class="focus-label">Department</label>
    </div>
</div>


                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <input type="text" name="from_date" class="form-control floating datetimepicker" 
                                   value="{{ request('from_date') }}">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <input type="text" name="to_date" class="form-control floating datetimepicker" 
                                   value="{{ request('to_date') }}">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </div>
        </form>

       <!-- Combined Table -->
       <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Department</th>
                                <th>Leave Type</th>
                                <th>No. of Days</th>
                                <th>Remaining Leave</th>
                                <th>Total Leaves</th>
                                <th>Total Leave Taken</th>
                                <th>Leave Carry Forward</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adminLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->employee_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                                    <td>{{ $leave->department_name }}</td>
                                    <td>
                                        @if ($leave->leave_type === 'Hospitalisation')
                                            <button class="btn btn-outline-info btn-sm">Hospitalisation</button>
                                        @elseif ($leave->leave_type === 'Parenting Leave')
                                            <button class="btn btn-outline-warning btn-sm">Parenting Leave</button>
                                        @elseif ($leave->leave_type === 'Emergency Leave')
                                            <button class="btn btn-outline-danger btn-sm">Emergency Leave</button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-danger btn-sm">{{ $leave->no_of_days }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-warning btn-sm"><b>{{ $annualLeaves->first()->total_leaves - $leave->no_of_days }}</b></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-success btn-sm"><b>{{ $annualLeaves->first()->total_leaves }}</b></span>
                                    </td>
                                    <td>{{ $leave->no_of_days }}</td>
                                    <td>{{ $annualLeaves->first()->carry_forward }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
