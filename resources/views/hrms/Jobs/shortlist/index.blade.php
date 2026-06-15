@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Shortlist Candidates');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Shortlisted Candidates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Shortlisted Candidates</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $shortlistedCandidates->total() }}</h3>
                    <p>Total Shortlisted</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $shortlistedCandidates->where('status', 'interview_scheduled')->count() }}</h3>
                    <p>Interview Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $shortlistedCandidates->where('status', 'telephonic_scheduled')->count() }}</h3>
                    <p>Telephonic Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $shortlistedCandidates->where('status', 'interview_completed')->count() }}</h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Filters Card -->
    <div class="card filter-card">
        <div class="card-body">
            <form id="filter-form" method="GET" action="{{ route('shortlist.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search name / ID...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <select class="form-select" name="position">
                                <option value="">All Positions</option>
                                @isset($positions)
                                    @foreach($positions as $position)
                                        <option value="{{ $position->position_applied }}" {{ request('position') == $position->position_applied ? 'selected' : '' }}>
                                            {{ $position->position_applied }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <select class="form-select" name="status">
                                <option value="">Pending</option>
                                <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                <option value="telephonic_scheduled" {{ request('status') == 'telephonic_scheduled' ? 'selected' : '' }}>Telephonic Scheduled</option>
                                <option value="telephonic_completed" {{ request('status') == 'telephonic_completed' ? 'selected' : '' }}>Telephonic Completed</option>
                                <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                <option value="interview_completed" {{ request('status') == 'interview_completed' ? 'selected' : '' }}>Interview Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-reset w-100" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Filters Card -->

    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table id="shortlist-table" class="table table-striped custom-table datatable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Candidate Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Status</th>
                <th>Interview Status</th>
                <th>Resume</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($shortlistedCandidates as $candidate)
            <tr>
                <td data-label="ID">{{ $candidate->id }}</td>
                <td data-label="Full Name">
                    <strong>{{ $candidate->first_name }} {{ $candidate->last_name }}</strong>
                </td>
                <td data-label="Email">{{ $candidate->email }}</td>
                <td data-label="Position Applied">{{ $candidate->position_applied }}</td>
                <td data-label="Status">
                    <span class="badge
                        @if($candidate->status == 'shortlisted') bg-info
                        @elseif($candidate->status == 'telephonic_scheduled') bg-warning
                        @elseif($candidate->status == 'telephonic_completed') bg-primary
                        @elseif($candidate->status == 'interview_scheduled') bg-secondary
                        @elseif($candidate->status == 'interview_completed') bg-success
                        @endif">
                        {{ ucwords(str_replace('_', ' ', $candidate->status)) }}
                    </span>
                </td>
                <td data-label="Latest Interview">
                    @if($candidate->interviews->count() > 0)
                        @php $latestInterview = $candidate->interviews->last(); @endphp
                        <span class="badge
                            @if($latestInterview->status == 'scheduled') bg-warning
                            @elseif($latestInterview->status == 'completed') bg-success
                            @elseif($latestInterview->status == 'cancelled') bg-danger
                            @elseif($latestInterview->status == 'rescheduled') bg-info
                            @endif">
                            {{ ucfirst($latestInterview->status) }}
                        </span><br>
                        <small>{{ $latestInterview->interview_datetime->format('M d, Y H:i') }}</small>
                    @else
                        <span class="text-muted">No Interview</span>
                    @endif
                </td>
                <td data-label="Resume">
                    @if($candidate->resume_path)
                        <div class="od-inline-actions">
                            <a href="{{ route('shortlist.view-resume', $candidate->id) }}" target="_blank" class="od-icon-btn" title="View Resume">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('shortlist.view-resume', ['id' => $candidate->id, 'download' => 1]) }}" class="od-icon-btn" title="Download Resume">
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
                            <a class="dropdown-item" href="{{ route('shortlist.schedule-interview', $candidate->id) }}">
                                <i class="fa-solid fa-calendar-days m-r-5"></i> Schedule Interview
                            </a>
                            @if($candidate->interviews->count() > 0)
                                <a class="dropdown-item" href="javascript:void(0)" 
                                   onclick="updateInterviewStatus({{ $candidate->interviews->last()->id }})">
                                    <i class="fa-solid fa-pen m-r-5"></i> Update Interview
                                </a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

            <!-- Pagination -->
            @if($shortlistedCandidates->count() > 0)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $shortlistedCandidates->firstItem() }} to {{ $shortlistedCandidates->lastItem() }} of {{ $shortlistedCandidates->total() }} entries
                        </div>
                        <div>
                            {{ $shortlistedCandidates->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Interview Status Modal -->
<div class="modal fade" id="interviewStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Interview Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="interviewStatusForm">
                    <input type="hidden" id="interviewId">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="interviewStatus" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveInterviewStatus()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Filter functionality
    $(document).ready(function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: @json(session('success')),
                timer: 1800,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('error'))
            });
        @endif

        // Auto-submit form on filter change
        $('select[name="position"], select[name="status"], select[name="interview_status"]').on('change', function() {
            $('#filter-form').submit();
        });

        // Enter key support for search
        $('input[name="search"]').on('keypress', function(e) {
            if (e.which === 13) {
                $('#filter-form').submit();
            }
        });
    });

    function resetFilters() {
        window.location.href = '{{ route("shortlist.index") }}';
    }

    function updateInterviewStatus(interviewId) {
        $('#interviewId').val(interviewId);
        $('#interviewStatusModal').modal('show');
    }

    function saveInterviewStatus() {
        const interviewId = $('#interviewId').val();
        const status = $('#interviewStatus').val();

        $.ajax({
            url: "{{ route('shortlist.update-interview-status', ':id') }}".replace(':id', interviewId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                rating: $('#rating').val(),
                feedback: $('#feedback').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#interviewStatusModal').modal('hide');
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        location.reload();
                    });
                }
            },
            error: function() {
                Swal.fire('Error!', 'Something went wrong!', 'error');
            }
        });
    }

</script>

<style>
.content.container-fluid {
    background: #f5f6fb;
    min-height: calc(100vh - 60px);
    padding: 28px 24px;
}

.page-header {
    margin-bottom: 24px;
}

.page-title {
    color: #020b36;
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 4px;
}

.breadcrumb {
    color: #7b88aa;
    font-size: 14px;
}

.stats-card {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-bottom: 3px solid #ff6b13;
    border-radius: 14px;
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 15px;
    min-height: 108px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
}

.stats-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.stats-icon.bg-primary {
    background: #eef0ff !important;
    color: #5d5fef;
}

.stats-icon.bg-success {
    background: #eafaf1 !important;
    color: #20bf6b;
}

.stats-icon.bg-warning {
    background: #fff6df !important;
    color: #f59f00;
}

.stats-icon.bg-info {
    background: #e8fbff !important;
    color: #12b6cb;
}

.stats-content h3 {
    color: #020b36;
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    margin: 0 0 6px;
}

.stats-content p {
    color: #29344d;
    font-size: 14px;
    font-weight: 700;
    margin: 0;
}

.filter-card,
.table-responsive {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
}

.filter-card {
    margin-bottom: 24px;
}

.filter-card .card-body {
    padding: 18px;
}

.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    min-height: 44px;
    padding: 10px 14px;
}

.filter-card .form-control:focus,
.filter-card .form-select:focus,
.modal .form-control:focus,
.modal .form-select:focus {
    border-color: #ff6b13;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 19, 0.12);
}

.btn-reset {
    background: #ff6b13;
    border: 1px solid #ff6b13;
    border-radius: 10px;
    color: #fff;
    font-weight: 700;
    min-height: 44px;
}

.btn-reset:hover {
    background: #e85f0f;
    border-color: #e85f0f;
    color: #fff;
}

#shortlist-table {
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
}

#shortlist-table thead th {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    color: #59647d;
    font-size: 12px;
    font-weight: 800;
    padding: 16px 18px;
    text-transform: uppercase;
}

#shortlist-table tbody td {
    border-top: 1px solid #edf0f5;
    color: #29344d;
    padding: 16px 18px;
    vertical-align: middle;
}

#shortlist-table tbody tr:hover {
    background: #fff8f3;
}

#shortlist-table strong {
    color: #020b36;
}

.badge {
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    padding: 7px 12px;
}

.badge.bg-info {
    background: #e8fbff !important;
    color: #078ca0;
}

.badge.bg-warning {
    background: #fff4df !important;
    color: #cf7600;
}

.badge.bg-primary {
    background: #eef0ff !important;
    color: #5455d9;
}

.badge.bg-secondary {
    background: #eef1f6 !important;
    color: #526071;
}

.badge.bg-success {
    background: #eafaf1 !important;
    color: #078c4a;
}

.badge.bg-danger {
    background: #fff0f0 !important;
    color: #d43d3d;
}

.od-inline-actions {
    display: inline-flex;
    gap: 8px;
}

.od-icon-btn {
    align-items: center;
    background: #fff;
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    display: inline-flex;
    height: 36px;
    justify-content: center;
    width: 40px;
}

.od-icon-btn:hover,
.action-icon:hover {
    color: #ff6b13;
}

.dropdown-menu {
    border: 1px solid #e1e5ef;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(8, 24, 74, 0.12);
}

.dropdown-item {
    color: #29344d;
    font-weight: 600;
    padding: 10px 16px;
}

.dropdown-item i {
    color: #ff6b13;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    border: 1px solid #dfe4ee;
    border-radius: 8px;
    color: #020b36;
    margin: 0 2px;
}

.page-link:hover,
.page-item.active .page-link {
    background: #ff6b13;
    border-color: #ff6b13;
    color: #fff;
}
</style>

@endsection
