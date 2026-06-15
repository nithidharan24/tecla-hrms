@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Employee Attendance');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Missed Punch Request</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('manual-punch.index') }}">Attendance</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Missed Punch Request</li>
                    </ol>
                </nav>
            </div>
            @if(isset($permissions) && $permissions->can_create)
            <div class="col-auto">
                @if(!$hasPendingRequest)
                    <a href="{{ route('manual-punch.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> New Request
                    </a>
                @endif
            </div>
            
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Today's Status Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle text-white">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">{{ $employee->firstname }} {{ $employee->lastname }}</h5>
                    <small class="text-muted">{{ $employee->employeeid }}</small>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-1">Today's Status</h6>
                                @if($todayAttendance)
                                    @if($todayAttendance->punch_out)
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($todayAttendance->punch_in)
                                        <span class="badge bg-info">In Progress</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Not Started</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-1">Punch In</h6>
                                @if($todayAttendance && $todayAttendance->punch_in)
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($todayAttendance->punch_in)->format('h:i A') }}</span>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-1">Punch Out</h6>
                                @if($todayAttendance && $todayAttendance->punch_out)
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($todayAttendance->punch_out)->format('h:i A') }}</span>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($hasPendingRequest)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You have a pending manual punch request for today. Please wait for admin approval.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Request History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="card-title mb-0">Request History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Request Date</th>
                            <th>Type</th>
                            <th>Requested Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $request)
                            <tr>
                                <td class="ps-4">{{ ($requests->currentPage() - 1) * $requests->perPage() + $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}</span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($request->request_date)->format('l') }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $request->request_type === 'punch_in' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucwords(str_replace('_', ' ', $request->request_type)) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($request->request_time)->format('h:i A') }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                          data-bs-toggle="tooltip" title="{{ $request->reason }}">
                                        {{ Str::limit($request->reason, 50) }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Approved
                                        </span>
                                        {{-- @if($request->approved_by && $request->approver)
                                            <br><small class="text-muted">By: {{ $request->approver->firstname }}</small>
                                        @endif --}}
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Rejected
                                        </span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <div class="btn-group btn-group-sm">
                                            @if(isset($permissions) && $permissions->can_edit)
                                            <a href="{{ route('manual-punch.edit', $request->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                    @endif
                    @if(isset($permissions) && $permissions->can_delete)
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteRequest({{ $request->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">No manual punch requests found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($requests->hasPages())
                <div class="card-footer bg-white">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this request?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
    .avatar-lg { width: 80px; height: 80px; }
    .avatar-title { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
</style>

<script>
    var manualPunchRoutes = {
        destroy: function(id) {
            return '{{ route("manual-punch.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id);
        }
    };

    function deleteRequest(id) {
        const form = document.getElementById('deleteForm');
        form.action = manualPunchRoutes.destroy(id);
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection