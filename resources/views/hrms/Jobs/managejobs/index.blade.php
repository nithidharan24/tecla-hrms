@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Manage Jobs');
@endphp

@extends('layouts.index')

@section('content')

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Jobs</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jobs</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('managejobs.create') }}" class="btn add-btn">
                    <i class="fa-solid fa-plus"></i> Add Job
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Filters Card -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Filters</h5>
        <form id="filter-form" method="GET" action="{{ route('managejobs.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Job Title, Department, Location">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Department</label>
                        <select class="form-control" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department }}" {{ request('department') == $dept->department ? 'selected' : '' }}>
                                    {{ $dept->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Job Type</label>
                        <select class="form-control" name="job_type">
                            <option value="">All Types</option>
                            <option value="Full Time" {{ request('job_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                            <option value="Part Time" {{ request('job_type') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                            <option value="Contract" {{ request('job_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Remote" {{ request('job_type') == 'Remote' ? 'selected' : '' }}>Remote</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">All Status</option>
                            <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" class="form-control" name="end_date" 
                                       value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Filters Card -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="job-table" class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Job Type</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($manageJobs as $job)
                        <tr>
                            <td>
                                <strong>{{ $job->job_title }}</strong>
                            </td>
                            <td>{{ $job->department }}</td>
                            <td>{{ $job->job_location }}</td>
                            <td>{{ \Carbon\Carbon::parse($job->start_date)->format('d-m-Y') }}</td>
                            <td>
                                {{ $job->end_date ? \Carbon\Carbon::parse($job->end_date)->format('d-m-Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge badge-pill bg-{{ $job->job_type == 'Full Time' ? 'success' : ($job->job_type == 'Part Time' ? 'warning' : 'info') }}-light">
                                    {{ $job->job_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-pill bg-{{ $job->status == 'Active' ? 'success' : ($job->status == 'Closed' ? 'danger' : 'secondary') }}-light">
                                    {{ $job->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if(isset($permissions) && $permissions->can_edit)
                                        <a class="dropdown-item" href="{{ route('managejobs.edit', $job->id) }}">
                                            <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                        </a>
                                        @endif
                                        @if(isset($permissions) && $permissions->can_delete)
                                        <a class="dropdown-item delete-job-btn" href="#" data-id="{{ $job->id }}">
                                            <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <form id="delete-form-{{ $job->id }}" action="{{ route('managejobs.destroy', $job->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa-solid fa-briefcase fa-2x mb-3"></i><br>
                                No jobs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($manageJobs->count() > 0)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $manageJobs->firstItem() }} to {{ $manageJobs->lastItem() }} of {{ $manageJobs->total() }} entries
                        </div>
                        <div>
                            {{ $manageJobs->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Delete job functionality
        $('.delete-job-btn').on('click', function(e) {
            e.preventDefault();
            var jobId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This job will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + jobId).submit();
                }
            });
        });

        // Auto-submit form on filter change (optional)
        $('select[name="department"], select[name="job_type"], select[name="status"]').on('change', function() {
            $('#filter-form').submit();
        });
    });

    function resetFilters() {
        window.location.href = '{{ route("managejobs.index") }}';
    }
</script>

<script>
    // Automatically hide success and error messages after 1.5 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
        let errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 1500);
</script>

<style>
.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
    font-size: 16px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn {
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
}

.badge {
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 12px;
}

.bg-success-light {
    background-color: #d4edda;
    color: #155724;
}

.bg-warning-light {
    background-color: #fff3cd;
    color: #856404;
}

.bg-info-light {
    background-color: #d1ecf1;
    color: #0c5460;
}

.bg-danger-light {
    background-color: #f8d7da;
    color: #721c24;
}

.bg-secondary-light {
    background-color: #e2e3e5;
    color: #383d41;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.dropdown-menu {
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dropdown-item {
    padding: 8px 16px;
    font-size: 14px;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

.text-muted {
    color: #6c757d !important;
}

.pagination {
    margin-bottom: 0;
}
</style>

@endsection