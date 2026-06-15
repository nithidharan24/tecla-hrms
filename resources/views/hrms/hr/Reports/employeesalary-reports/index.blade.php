@extends('layouts.index')

@section('content')

<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">

        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Employee Salary Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employee Salary Reports</li>
                    </ul>
                </div>
            </div>
        </div>


 
        <form action="{{ route('employee-salary-reports.index') }}" method="GET">
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <select name="month" class="form-control floating select">
                            <option value="">-- Select Month --</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endfor
                        </select>
                        <label class="focus-label">Month</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <select name="year" class="form-control floating select">
                            <option value="">-- Select Year --</option>
                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <label class="focus-label">Year</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <select name="employee_id" class="form-control floating select">
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Employee Name</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('employee-salary-reports.export.csv', request()->query()) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export to CSV
                </a>
                <a href="{{ route('employee-salary-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Payroll Month</th>
                                <th>Basic Salary</th>
                                <th>Total Earnings</th>
                                <th>Total Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payslips as $key => $payslip)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $payslip->employeeid }}</td>
                                <td>{{ $payslip->firstname }} {{ $payslip->lastname }}</td>
                                <td>{{ $payslip->designation_name }}</td>
                                <td>{{ $payslip->payroll_month_formatted }}</td>
                                <td>{{ number_format($payslip->basic_salary, 2) }}</td>
                                <td>{{ number_format($payslip->total_earnings, 2) }}</td>
                                <td>{{ number_format($payslip->total_deductions, 2) }}</td>
                                <td>{{ number_format($payslip->net_salary, 2) }}</td>
                                <td>
                                    <div class="dropdown action-label">
                                        <a href="#" class="btn btn-white btn-sm btn-rounded">
                                            <i class="fa-regular fa-circle-dot text-{{ $payslip->status == 'sent' ? 'success' : ($payslip->status == 'generated' ? 'info' : 'danger') }}"></i>
                                            {{ ucfirst($payslip->status) }}
                                        </a>
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
 
</div>

@endsection
