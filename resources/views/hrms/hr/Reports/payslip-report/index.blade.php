@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
                
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Payslip Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payslip Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Search Filter -->
<form method="GET" action="{{ route('payslip-reports.index') }}">
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <input type="text" name="employee_name" class="form-control floating" value="{{ request('employee_name') }}">
                <label class="focus-label">Employee Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <select name="month" class="form-control floating select">
                    <option value="">Select Month</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date("M", mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
                <label class="focus-label">Month</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <select name="year" class="form-control floating select">
                    <option value="">Select Year</option>
                    @foreach(range(date('Y') - 5, date('Y')) as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <label class="focus-label">Year</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">  
            <button type="submit" class="btn btn-success w-100"> Search </button>  
        </div>     
    </div>
</form>
<!-- /Search Filter -->

<!-- Export Buttons -->
<div class="row mb-3">
    <div class="col-md-12 text-end">
        <a href="{{ route('payslip-reports.export.csv', request()->query()) }}" class="btn btn-primary">
            <i class="fa fa-file-excel"></i> Export to CSV
        </a>
        <a href="{{ route('payslip-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fa fa-file-pdf"></i> Export to PDF
        </a>
    </div>
</div>
<!-- /Export Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Employee Name</th>
                                <th>Paid Amount</th>
                                <th>Payment Month</th>
                                <th>Payment Year</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($payslips as $key => $payslip)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>
            <h2 class="table-avatar">
            <a  class="avatar"><img src="{{ asset($payslip->profile_image) }}" alt="User Image"></a>  
                <a >{{ $payslip->full_name }}</a>
            </h2>
        </td>
        <td>${{ number_format($payslip->net_salary, 2) }}</td>
        <td>{{ date('M', strtotime($payslip->created_at)) }}</td>
        <td>{{ date('Y', strtotime($payslip->created_at)) }}</td>
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
