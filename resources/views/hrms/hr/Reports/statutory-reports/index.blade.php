@extends('layouts.index')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">PF & ESI Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">HRMS</li>
                    <li class="breadcrumb-item active">Statutory Reports</li>
                </ul>
            </div>
        </div>
    </div>

    <form method="GET">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Filter Month</label>
                <select name="month" class="form-select">
                    <option value="">-- All Months --</option>
                    @foreach($months as $m)
                    <option value="{{ $m->payroll_month }}" 
                        {{ request('month') == $m->payroll_month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($m->payroll_month)->format('F Y') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filter</button>
            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end">
                <a href="{{ route('admin.statutory.reports.export', ['month' => request('month')]) }}" 
                   class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">PF & ESI Detailed Report</h5>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Basic</th>
                        <th>Gross</th>
                        <th>PF Emp</th>
                        <th>PF Employer</th>
                        <th>ESI Emp</th>
                        <th>ESI Employer</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td>{{ $r->firstname }} {{ $r->lastname }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->payroll_month)->format('F Y') }}</td>
                        <td>₹{{ number_format($r->basic_salary, 2) }}</td>
                        <td>₹{{ number_format($r->total_earnings, 2) }}</td>
                        <td>₹{{ number_format($r->pf, 2) }}</td>
                        <td>₹{{ number_format($r->pf_employer, 2) }}</td>
                        <td>₹{{ number_format($r->esi, 2) }}</td>
                        <td>₹{{ number_format($r->esi_employer, 2) }}</td>
                        <td>₹{{ number_format($r->net_salary, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-danger">No Records Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
