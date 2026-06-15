@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Project Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Project Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Search Filter -->
        <form action="{{ route('project-reports.index') }}" method="GET">

    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">
            <div class="input-block mb-3 form-focus">
                <select name="project_name" class="form-control floating select">
                    <option value="">-- Select Project --</option>
                    @foreach($projectNames as $projectName)
                        <option value="{{ $projectName }}" {{ request('project_name') == $projectName ? 'selected' : '' }}>
                            {{ $projectName }}
                        </option>
                    @endforeach
                </select>
                <label class="focus-label">Project Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="input-block mb-3 form-focus">
                <select name="status" class="form-control floating select">
                    <option value="">-- Select Status --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <label class="focus-label">Status</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <button type="submit" class="btn btn-success w-100">Search</button>
        </div>
    </div>
</form>
<!-- /Search Filter -->

<!-- Export Buttons -->
<div class="row mb-3">
    <div class="col-md-12 text-end">
        <a href="{{ route('project-reports.export.csv', request()->query()) }}" class="btn btn-primary">
            <i class="fa fa-file-excel"></i> Export to CSV
        </a>
        <a href="{{ route('project-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
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
                                <th>Project Name</th>
                                <th>Client Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Team</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $key => $project)
                            <tr>
                                <td data-label="S.No">{{ $key + 1 }}</td>

                                <td data-label="Project Name">{{ $project->projectname }}</td>
                                
                                <td data-label="Client">{{ $project->client }}</td>
                                
                                <td data-label="Start Date">{{ date('d-m-Y', strtotime($project->startdate)) }}</td>
                                
                                <td data-label="End Date">{{ date('d-m-Y', strtotime($project->enddate)) }}</td>
                                
                                <td data-label="Status">
                                    <div class="dropdown action-label">
                                        <a href="#" class="btn btn-white btn-sm btn-rounded">
                                            <i class="fa-regular fa-circle-dot text-{{ $project->status == 'active' ? 'success' : 'danger' }}"></i>
                                            {{ ucfirst($project->status) }}
                                        </a>
                                    </div>
                                </td>
                                
                                <td data-label="Team">{!! $project->teamNames !!}</td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->
@endsection
