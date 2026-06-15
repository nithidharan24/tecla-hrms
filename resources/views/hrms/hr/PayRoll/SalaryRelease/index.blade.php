@extends('layouts.index')

@section('content')
<div class="content container-fluid">

    <div class="page-header mb-3">
        <h4 class="mb-0"><i class="fas fa-paper-plane text-primary me-2"></i> Salary Release</h4>
        <small class="text-muted">Release payslips department-wise or per employee. (No hold — send only)</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="d-flex align-items-center justify-content-between border-start border-4 border-primary bg-white shadow-sm p-3 rounded">
                <div><h3 class="fw-bold mb-1">{{ $summary['total_departments'] }}</h3><p class="mb-0 text-muted">Departments</p></div>
                <i class="fas fa-building fs-2 text-primary"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="d-flex align-items-center justify-content-between border-start border-4 border-info bg-white shadow-sm p-3 rounded">
                <div><h3 class="fw-bold mb-1">{{ $summary['total_payslips'] }}</h3><p class="mb-0 text-muted">Total Payslips</p></div>
                <i class="fas fa-file-alt fs-2 text-info"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="d-flex align-items-center justify-content-between border-start border-4 border-success bg-white shadow-sm p-3 rounded">
                <div><h3 class="fw-bold mb-1">{{ $summary['sent_count'] }}</h3><p class="mb-0 text-muted">Already Sent</p></div>
                <i class="fas fa-check-circle fs-2 text-success"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="d-flex align-items-center justify-content-between border-start border-4 border-warning bg-white shadow-sm p-3 rounded">
                <div><h3 class="fw-bold mb-1">₹{{ number_format($summary['total_net'], 0) }}</h3><p class="mb-0 text-muted">Total Payout</p></div>
                <i class="fas fa-money-bill-wave fs-2 text-warning"></i>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('salary-release.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-control">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <select name="year" class="form-control">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departmentList as $d)
                        <option value="{{ $d->id }}" {{ (string)$departmentId === (string)$d->id ? 'selected' : '' }}>{{ $d->department }}</option>
                    @endforeach
                    <option value="unassigned" {{ $departmentId === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    {{-- Department table --}}
    <div class="table-responsive">
        <table class="table custom-table datatable">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Employees</th>
                    <th>Total Net</th>
                    <th>Sent / Pending</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                @php $deptKey = $dept->department_id ?? 'unassigned'; @endphp
                <tr>
                    <td><strong>{{ $dept->department_name }}</strong></td>
                    <td><span class="badge bg-info">{{ $dept->total_payslips }}</span></td>
                    <td><strong class="text-success">₹{{ number_format($dept->total_net, 2) }}</strong></td>
                    <td>
                        <span class="badge bg-success">{{ $dept->sent_count }} sent</span>
                        <span class="badge bg-warning">{{ $dept->pending_count }} pending</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('salary-release.show', ['departmentId' => $deptKey, 'month' => $month, 'year' => $year]) }}"
                           class="btn btn-sm btn-outline-primary">
                           <i class="fas fa-eye"></i> View Detail
                        </a>
                        <form method="POST" action="{{ route('salary-release.release-department') }}" class="d-inline"
                              onsubmit="return confirm('Release payslips to ALL {{ $dept->total_payslips }} employee(s) in {{ $dept->department_name }}?');">
                            @csrf
                            <input type="hidden" name="department_id" value="{{ $deptKey }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-paper-plane"></i> Release Salary
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <h6 class="text-muted">No payslips found for this period</h6>
                        <small class="text-muted">Generate payslips in "Process Payroll" first.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection