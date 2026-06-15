@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">Employee Reports</h2>
            
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('employee.reports') }}" class="form-inline">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <input type="text" name="employee_name" class="form-control" placeholder="Employee Name" value="{{ request('employee_name') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <select name="department" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $id => $department)
                                        <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $key => $status)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('employee.reports') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            @if(request()->hasAny(['employee_name', 'department', 'status', 'date_from', 'date_to']))
                                <span class="badge badge-info">Filtered Results</span>
                            @else
                                <span class="badge badge-light">All Employees</span>
                            @endif
                        </div>
                        <div>
                            <form method="GET" action="{{ route('employee.reports') }}" class="form-inline">
                                @foreach(request()->all() as $key => $value)
                                    @if($key != 'export' && $key != 'pdf')
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <button type="submit" name="export" value="csv" class="btn btn-success btn-sm mr-2">
                                    <i class="fas fa-file-csv"></i> Export CSV
                                </button>
                                <button type="submit" name="pdf" value="1" class="btn btn-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Joining Date</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $employee->employee_id }}</td>
                                    <td>
                                        <strong>{{ $employee->employee_name }}</strong>
                                        <div class="text-muted small">{{ $employee->email }}</div>
                                    </td>
                                    <td>{{ $employee->department_name ?? 'N/A' }}</td>
                                    <td>{{ $employee->designation_name ?? 'N/A' }}</td>
                                    <td>{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $employee->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $employee->status == 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No employees found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($employees->hasPages() && !request()->has('export') && !request()->has('pdf'))
                    <div class="card-footer">
                        {{ $employees->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
        }
        .card-header, .card-footer {
            display: none;
        }
    }
</style>
@endsection