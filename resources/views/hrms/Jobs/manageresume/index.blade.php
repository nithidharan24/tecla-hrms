@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Manage Resumes');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Job Applications</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Job Applications</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshApplications()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="showStats()">
                        <i class="fas fa-chart-bar"></i> Statistics
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Application Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->count() }}</h4>
                    <p class="mb-0">Total Applications</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->where('status', 'pending')->count() }}</h4>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->where('status', 'reviewed')->count() }}</h4>
                    <p class="mb-0">Reviewed</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->where('status', 'shortlisted')->count() }}</h4>
                    <p class="mb-0">Shortlisted</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->where('status', 'hired')->count() }}</h4>
                    <p class="mb-0">Hired</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ $applications->where('status', 'rejected')->count() }}</h4>
                    <p class="mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table custom-table datatable" id="projects-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Job Title</th>
                       
                            <th>Experience</th>
                            <th>Expected Salary</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th>Resume</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                        <tr>
                            <td data-label="ID">{{ $application->id }}</td>

                            <td data-label="Applicant Name">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            {{ strtoupper(substr($application->first_name, 0, 1)) }}{{ strtoupper(substr($application->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>{{ $application->first_name }} {{ $application->last_name }}</strong>
                                        @if($application->linkedin)
                                            <br>
                                            <a href="{{ $application->linkedin }}" target="_blank" class="text-primary small">
                                                <i class="fab fa-linkedin"></i> LinkedIn
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td data-label="Email">{{ $application->email }}</td>
                            <td data-label="Phone">{{ $application->phone }}</td>
                            <td data-label="Job Title">{{ $application->job_title }}</td>
                            <td data-label="Experience">{{ $application->years_experience ?? 'Not specified' }}</td>
                            <td data-label="Expected Salary">{{ $application->expected_salary ?? 'Not specified' }}</td>
                            
                            <td data-label="Status">
                                <span class="badge badge-{{ 
                                    $application->status == 'pending' ? 'warning' : 
                                    ($application->status == 'reviewed' ? 'info' : 
                                    ($application->status == 'shortlisted' ? 'primary' :  
                                    ($application->status == 'hired' ? 'success' : 'danger'))) 
                                }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            
                            <td data-label="Applied At">{{ \Carbon\Carbon::parse($application->applied_at)->format('d-m-Y H:i') }}</td>
                            
                            <td data-label="Resume">
                                @if (!empty($application->resume_path))
                                    <a href="{{ route('resume.view-resume', $application->id) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>
                            
                            <td  data-label="Actions" class="text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('resume.show', $application->id) }}">
                                                <i class="fa-solid fa-eye m-r-5"></i> View Details
                                            </a>
                                            @if(isset($permissions) && $permissions->can_delete)
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_application_{{ $application->id }}">
                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Update Status Modal -->
                                <div class="modal fade" id="update_status_{{ $application->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Application Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('resume.update-status', $application->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Current Status: <strong>{{ ucfirst($application->status) }}</strong></label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="status">New Status</label>
                                                        <select name="status" class="form-control" required>
                                                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                                            <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                                            <option value="interviewed" {{ $application->status == 'interviewed' ? 'selected' : '' }}>Interviewed</option>
                                                            <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>Hired</option>
                                                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="delete_application_{{ $application->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-exclamation-triangle"></i> Delete Application
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-5">
                                                <p class="fs-5 text-muted">
                                                    Are you sure you want to delete this job application?
                                                </p>
                                                <p class="text-muted">
                                                    <strong>{{ $application->first_name }} {{ $application->last_name }}</strong> - {{ $application->job_title }}
                                                </p>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                    <i class="fas fa-times-circle"></i> Cancel
                                                </button>
                                                <form action="{{ route('resume.destroy', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger px-4">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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

<script>
function refreshApplications() {
    location.reload();
}

function showStats() {
    // You can implement a detailed statistics modal here
    alert('Statistics feature can be implemented with charts and detailed analytics');
}
</script>
@endsection
