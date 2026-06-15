@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Policies Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Policies Report</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="row filter-row">
            <form method="GET" action="{{ route('policies-report.index') }}" class="w-100 d-flex">
                <div class="col-md-3">
                    <div class="input-block mb-0 form-focus">
                        <div class="cal-icon">
                            <input name="from" class="form-control floating datetimepicker" type="text" value="{{ request('from') }}">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-block mb-0 form-focus">
                        <div class="cal-icon">
                            <input name="to" class="form-control floating datetimepicker" type="text" value="{{ request('to') }}">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-block mb-0 form-focus">
                        <select class="form-control floating" name="department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </form>
        </div>
        <!-- /Search Filter -->

        <!-- Export Buttons -->
        <div class="row mb-3 p-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('policies-report.export.csv', array_merge(request()->query(), ['department_id' => request('department_id')])) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> CSV
                </a>
                <a href="{{ route('policies-report.export.pdf', array_merge(request()->query(), ['department_id' => request('department_id')])) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>
        <!-- /Export Buttons -->

        <div class="row pt-3">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>Policy ID</th>
                                <th>Policy Name</th>
                                <th>Description</th>
                                <th>Department</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($policies as $policy)
                                <tr>
                                    <td>{{ $policy->id }}</td>
                                    <td>{{ $policy->policy_name }}</td>
                                    <td>{{ Str::limit($policy->description, 50) }}</td>
                                    <td>
                                        @if($policy->department_id)
                                           {{ $policy->department_name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($policy->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script>
    $(document).ready(function() {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                up: "fa fa-angle-up",
                down: "fa fa-angle-down",
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            }
        });
    });
</script>
@endsection