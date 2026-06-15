@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Clearance Management</h4>
                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-clipboard-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Clearances</span>
                                    <span class="info-box-number">{{ $clearanceStats->total }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending</span>
                                    <span class="info-box-number">{{ $clearanceStats->pending_total }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ $clearanceStats->completed }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Rejected</span>
                                    <span class="info-box-number">{{ $clearanceStats->rejected }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="info-box bg-gradient-secondary">
                                <span class="info-box-icon"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completion Rate</span>
                                    <span class="info-box-number">
                                        {{ $clearanceStats->total > 0 ? round(($clearanceStats->completed / $clearanceStats->total) * 100, 1) : 0 }}%
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $clearanceStats->total > 0 ? ($clearanceStats->completed / $clearanceStats->total) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Clearance Requests</h3>
                    <div class="card-tools">
                        <form action="{{ route('clearance.index') }}" method="GET" class="form-inline">
                            <select name="status_filter" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status_filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ request('status_filter') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <a href="{{ route('clearance.index') }}" class="btn btn-sm btn-secondary ml-2">Reset</a>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Termination Date</th>
                                    <th>Clearance Progress</th>
                                    <th>HR</th>
                                    <th>Manager</th>
                                    <th>Team Lead</th>
                                    <th>Support Manager</th>
                                    <th>Overall Status</th>
                                   @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($terminations as $termination)
                                @php
    $approvers = $termination->approvers;
    
    // Simple and reliable counting method
    $approvedCount = 0;
    if ($termination->hr_status === 'approved') $approvedCount++;
    if ($termination->manager_status === 'approved') $approvedCount++;
    if ($termination->team_lead_status === 'approved') $approvedCount++;
    if ($termination->support_manager_status === 'approved') $approvedCount++;
    
    $totalCount = 4; // Fixed number of clearance types
    $progress = ($approvedCount / $totalCount) * 100;
@endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                           
                                            <div class="ml-2">
                                                {{ $termination->firstname }} {{ $termination->lastname }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $termination->employeeid }}</td>
                                    <td>{{ $termination->department_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($termination->termination_date)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%">
                                                {{ $approvedCount }}/{{ $totalCount }}
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($progress, 0) }}% Complete</small>
                                    </td>
                                    
                                    <!-- HR Status with Quick Actions -->
                                    <td>
                                        <div class="clearance-status">
                                            <span class="badge badge-{{ $termination->hr_status == 'approved' ? 'success' : ($termination->hr_status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($termination->hr_status) }}
                                            </span>
                                            
                                            @if($termination->hr_status == 'pending')
                                                <div class="btn-group btn-group-sm mt-1" role="group">
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="hr">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Approve HR clearance?')" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="hr">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Reject HR clearance?')" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                @if(isset($approvers['hr']))
                                                    <br>
                                                    <small class="text-muted" data-toggle="tooltip" title="Approved by {{ $approvers['hr']['name'] }} ({{ ucfirst($approvers['hr']['type']) }}) on {{ \Carbon\Carbon::parse($termination->hr_approved_at)->format('M d, Y') }}">
                                                        <i class="fas fa-user"></i> {{ \Illuminate\Support\Str::limit($approvers['hr']['name'], 10) }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Manager Status with Quick Actions -->
                                    <td>
                                        <div class="clearance-status">
                                            <span class="badge badge-{{ $termination->manager_status == 'approved' ? 'success' : ($termination->manager_status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($termination->manager_status) }}
                                            </span>
                                            
                                            @if($termination->manager_status == 'pending')
                                                <div class="btn-group btn-group-sm mt-1" role="group">
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="manager">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Approve Manager clearance?')" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="manager">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Reject Manager clearance?')" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                @if(isset($approvers['manager']))
                                                    <br>
                                                    <small class="text-muted" data-toggle="tooltip" title="Approved by {{ $approvers['manager']['name'] }} ({{ ucfirst($approvers['manager']['type']) }}) on {{ \Carbon\Carbon::parse($termination->manager_approved_at)->format('M d, Y') }}">
                                                        <i class="fas fa-user"></i> {{ \Illuminate\Support\Str::limit($approvers['manager']['name'], 10) }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Team Lead Status with Quick Actions -->
                                    <td>
                                        <div class="clearance-status">
                                            <span class="badge badge-{{ $termination->team_lead_status == 'approved' ? 'success' : ($termination->team_lead_status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($termination->team_lead_status) }}
                                            </span>
                                            
                                            @if($termination->team_lead_status == 'pending')
                                                <div class="btn-group btn-group-sm mt-1" role="group">
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="team_lead">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Approve Team Lead clearance?')" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="team_lead">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Reject Team Lead clearance?')" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                @if(isset($approvers['team_lead']))
                                                    <br>
                                                    <small class="text-muted" data-toggle="tooltip" title="Approved by {{ $approvers['team_lead']['name'] }} ({{ ucfirst($approvers['team_lead']['type']) }}) on {{ \Carbon\Carbon::parse($termination->team_lead_approved_at)->format('M d, Y') }}">
                                                        <i class="fas fa-user"></i> {{ \Illuminate\Support\Str::limit($approvers['team_lead']['name'], 10) }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Support Manager Status with Quick Actions -->
                                    <td>
                                        <div class="clearance-status">
                                            <span class="badge badge-{{ $termination->support_manager_status == 'approved' ? 'success' : ($termination->support_manager_status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($termination->support_manager_status) }}
                                            </span>
                                            
                                            @if($termination->support_manager_status == 'pending')
                                                <div class="btn-group btn-group-sm mt-1" role="group">
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="support_manager">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Approve Support Manager clearance?')" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="clearance_type" value="support_manager">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Reject Support Manager clearance?')" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                @if(isset($approvers['support_manager']))
                                                    <br>
                                                    <small class="text-muted" data-toggle="tooltip" title="Approved by {{ $approvers['support_manager']['name'] }} ({{ ucfirst($approvers['support_manager']['type']) }}) on {{ \Carbon\Carbon::parse($termination->support_manager_approved_at)->format('M d, Y') }}">
                                                        <i class="fas fa-user"></i> {{ \Illuminate\Support\Str::limit($approvers['support_manager']['name'], 10) }}
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($approvedCount == $totalCount)
                                            <span class="badge badge-success">Completed</span>
                                        @elseif(collect($clearances)->contains('rejected'))
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('clearance.show', $termination->id) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($termination->hr_status == 'pending' || $termination->manager_status == 'pending' || $termination->team_lead_status == 'pending' || $termination->support_manager_status == 'pending')
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#bulkApproveModal{{ $termination->id }}" title="Bulk Approve">
                                                    <i class="fas fa-bolt"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Bulk Approve Modal -->
                                        <div class="modal fade" id="bulkApproveModal{{ $termination->id }}" tabindex="-1" role="dialog" aria-labelledby="bulkApproveModalLabel{{ $termination->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="bulkApproveModalLabel{{ $termination->id }}">Bulk Approve Clearances</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('clearance.bulk-update-single', $termination->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Approve all pending clearances for <strong>{{ $termination->firstname }} {{ $termination->lastname }}</strong>?</p>
                                                            
                                                            <div class="form-group">
                                                                <label for="remarks{{ $termination->id }}">Remarks (Optional)</label>
                                                                <textarea class="form-control" id="remarks{{ $termination->id }}" name="remarks" rows="3" placeholder="Add remarks..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Approve All Pending</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">No clearance requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.clearance-status {
    text-align: center;
    min-width: 100px;
}
.clearance-status .badge {
    display: block;
    margin: 0 auto 2px auto;
}
.clearance-status small {
    font-size: 0.7rem;
    line-height: 1;
}
.clearance-status .btn-group {
    display: flex;
    justify-content: center;
}
.clearance-status .btn-group .btn {
    padding: 0.15rem 0.3rem;
    font-size: 0.7rem;
}
</style>

<script>
// Initialize tooltips
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection