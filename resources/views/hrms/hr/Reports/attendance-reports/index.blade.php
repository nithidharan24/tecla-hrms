@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Attendance Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/admin-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Attendance Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Search Filter -->
        <form method="GET" action="{{ route('attendance-reports.index') }}">
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <input type="text" name="employee_name" class="form-control floating" placeholder="Employee Name" value="{{ request('employee_name') }}">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <select name="month" class="form-control floating select">
                                <option value="">Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <label class="focus-label">Month</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <select name="year" class="form-control floating select">
                                <option value="">Select Year</option>
                                @for ($year = 2020; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <label class="focus-label">Year</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('attendance-reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
        <!-- /Search Filter -->

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        @if(request()->hasAny(['employee_name', 'month', 'year']))
                            <span class="badge badge-info">Filtered Results</span>
                        @else
                            <span class="badge badge-light">All Records</span>
                        @endif
                    </div>
                    <div>
                        <form method="GET" action="{{ route('attendance-reports.index') }}" class="form-inline">
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
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Work Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendanceData as $attendance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->employee_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                    <td>{{ $attendance->punch_in ?? '-' }}</td>
                                    <td>{{ $attendance->punch_out ?? '-' }}</td>
                                    <td>
                                        @if (is_null($attendance->punch_in) && is_null($attendance->punch_out))
                                            <span class="badge badge-danger">Week Off</span>
                                        @else
                                            <span class="badge badge-success">Present</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Attendance Data Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attendanceData->hasPages() && !request()->has('export') && !request()->has('pdf'))
                <div class="card-footer">
                    {{ $attendanceData->appends(request()->query())->links() }}
                </div>
                @endif
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