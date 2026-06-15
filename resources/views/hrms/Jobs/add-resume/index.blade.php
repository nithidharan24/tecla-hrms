@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Add Resumes');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Candidate Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Candidates</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('add-resume.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Candidate
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filters</h5>
            <form id="filter-form" method="GET" action="{{ route('add-resume.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Name, Email, Position">
                        </div>
                    </div>
                    <div class="col-md-2">
    <div class="form-group">
        <label>Position</label>
        <select class="form-control" name="position">
            <option value="">All Positions</option>
            @isset($positions)
                @foreach($positions as $position)
                    <option value="{{ $position->position_applied }}" {{ request('position') == $position->position_applied ? 'selected' : '' }}>
                        {{ $position->position_applied }}
                    </option>
                @endforeach
            @else
                <!-- Fallback positions if $positions is not available -->
                <option value="Software Developer">Software Developer</option>
                <option value="Web Developer">Web Developer</option>
                <option value="Frontend Developer">Frontend Developer</option>
                <option value="Backend Developer">Backend Developer</option>
            @endisset
        </select>
    </div>
</div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="">All Status</option>
                                <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                                <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                <option value="telephonic_scheduled" {{ request('status') == 'telephonic_scheduled' ? 'selected' : '' }}>Telephonic Scheduled</option>
                                <option value="telephonic_completed" {{ request('status') == 'telephonic_completed' ? 'selected' : '' }}>Telephonic Completed</option>
                                <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                <option value="interview_completed" {{ request('status') == 'interview_completed' ? 'selected' : '' }}>Interview Completed</option>
                                <option value="selected" {{ request('status') == 'selected' ? 'selected' : '' }}>Selected</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Min Experience</label>
                            <select class="form-control" name="experience">
                                <option value="">Any Experience</option>
                                <option value="0" {{ request('experience') == '0' ? 'selected' : '' }}>Fresher</option>
                                <option value="1" {{ request('experience') == '1' ? 'selected' : '' }}>1+ Years</option>
                                <option value="2" {{ request('experience') == '2' ? 'selected' : '' }}>2+ Years</option>
                                <option value="3" {{ request('experience') == '3' ? 'selected' : '' }}>3+ Years</option>
                                <option value="5" {{ request('experience') == '5' ? 'selected' : '' }}>5+ Years</option>
                                <option value="8" {{ request('experience') == '8' ? 'selected' : '' }}>8+ Years</option>
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
    <table id="candidate-table" class="table table-striped custom-table datatable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Experience</th>
                <th>Status</th>
                <th>Resume</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($candidates as $candidate)
            <tr>
                <td data-label="ID">{{ $candidate->id }}</td>
                <td data-label="Full Name">
                    <strong>{{ $candidate->first_name }} {{ $candidate->last_name }}</strong>
                </td>
                <td data-label="Email">{{ $candidate->email }}</td>
                <td data-label="Phone">{{ $candidate->phone }}</td>
                <td data-label="Position Applied">{{ $candidate->position_applied }}</td>
                <td data-label="Experience (Years)">{{ $candidate->experience_years }} years</td>
                <td data-label="Status">
                    <select class="form-select status-select" data-id="{{ $candidate->id }}">
                        <option value="applied" {{ $candidate->status == 'applied' ? 'selected' : '' }}>Applied</option>
                        <option value="shortlisted" {{ $candidate->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                        <option value="telephonic_scheduled" {{ $candidate->status == 'telephonic_scheduled' ? 'selected' : '' }}>Telephonic Scheduled</option>
                        <option value="telephonic_completed" {{ $candidate->status == 'telephonic_completed' ? 'selected' : '' }}>Telephonic Completed</option>
                        <option value="interview_scheduled" {{ $candidate->status == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                        <option value="interview_completed" {{ $candidate->status == 'interview_completed' ? 'selected' : '' }}>Interview Completed</option>
                        <option value="selected" {{ $candidate->status == 'selected' ? 'selected' : '' }}>Selected</option>
                        <option value="rejected" {{ $candidate->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </td>
                <td data-label="Resume">
                    @if($candidate->resume_path)
                        <div class="od-inline-actions">
                            <a href="{{ route('add-resume.view-resume', $candidate->id) }}" target="_blank" class="od-icon-btn" title="View Resume">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('add-resume.view-resume', ['id' => $candidate->id, 'download' => 1]) }}" class="od-icon-btn" title="Download Resume">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    @else
                        <span class="text-muted">No Resume</span>
                    @endif
                </td>
                <td data-label="Actions" class="text-end">
                    <div class="dropdown dropdown-action">
                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(isset($permissions) && $permissions->can_edit)
                            <a class="dropdown-item" href="{{ route('add-resume.edit', $candidate->id) }}">
                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                            </a>
                            @endif
                            @if(isset($permissions) && $permissions->can_delete)
                            <a class="dropdown-item delete-candidate-btn" href="#" data-id="{{ $candidate->id }}">
                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <form id="delete-form-{{ $candidate->id }}" action="{{ route('add-resume.destroy', $candidate->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

            
            <!-- Pagination -->
            @if($candidates->count() > 0)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $candidates->firstItem() }} to {{ $candidates->lastItem() }} of {{ $candidates->total() }} entries
                        </div>
                        <div>
                            {{ $candidates->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this candidate?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .status-select {
        min-width: 180px;
        white-space: nowrap;
        font-size: 12px;
    }
    
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Filter functionality
        $('#filter-btn').on('click', function() {
            applyFilters();
        });

        // Reset filters
        $('#reset-btn').on('click', function() {
            resetFilters();
        });

        // Enter key support for search
        $('#search').on('keypress', function(e) {
            if (e.which === 13) {
                applyFilters();
            }
        });

        function applyFilters() {
            $('#filter-form').submit();
        }

        // Auto-submit form on filter change (optional)
        $('select[name="position"], select[name="status"], select[name="experience"]').on('change', function() {
            $('#filter-form').submit();
        });

        // Status change handler
        $('.status-select').change(function() {
            const candidateId = $(this).data('id');
            const newStatus = $(this).val();

            const updateStatusUrl = "{{ route('add-resume.update-status', ['id' => ':candidateId']) }}".replace(':candidateId', candidateId);

            $.ajax({
                url: updateStatusUrl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            });
        });

        // Delete candidate functionality
        $('.delete-candidate-btn').on('click', function(e) {
            e.preventDefault();
            var candidateId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this candidate?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + candidateId).submit();
                }
            });
        });
    });

    function resetFilters() {
        window.location.href = '{{ route("add-resume.index") }}';
    }

    // Auto hide messages
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000);
</script>

<script>
const checkAll = document.getElementById('checkAll');
const rows = document.querySelectorAll('.row-check');

checkAll?.addEventListener('change', function() {
    rows.forEach(r => {
        r.checked = this.checked;
        r.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rows.forEach(r => {
    r.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

@endsection