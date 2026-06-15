@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Clearance Details - {{ $termination->firstname }} {{ $termination->lastname }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('clearance.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Employee Details</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Employee ID:</th>
                                        <td>{{ $termination->employeeid }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $termination->department_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Designation:</th>
                                        <td>{{ $termination->designation_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Joining Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($termination->joiningdate)->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termination Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($termination->termination_date)->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termination Reason:</th>
                                        <td>{{ $termination->reason }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Clearance Progress</h5>
                            @php
                                $clearances = [
                                    'hr' => $termination->hr_status,
                                    'manager' => $termination->manager_status,
                                    'team_lead' => $termination->team_lead_status,
                                    'support_manager' => $termination->support_manager_status
                                ];
                                $approvedCount = collect($clearances)->where('approved')->count();
                                $totalCount = count($clearances);
                                $progress = ($approvedCount / $totalCount) * 100;
                            @endphp
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%">
                                    <strong>{{ $approvedCount }}/{{ $totalCount }} Approved</strong>
                                </div>
                            </div>
                            <div class="text-center">
                                <h3>{{ number_format($progress, 0) }}% Complete</h3>
                                @if($approvedCount == $totalCount)
                                    <span class="badge badge-success badge-lg">Clearance Completed</span>
                                @elseif(collect($clearances)->contains('rejected'))
                                    <span class="badge badge-danger badge-lg">Clearance Rejected</span>
                                @else
                                    <span class="badge badge-warning badge-lg">Clearance In Progress</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Clearance Sections -->
                    <div class="row">
                        @foreach(['hr' => 'HR Department', 'manager' => 'Manager', 'team_lead' => 'Team Lead', 'support_manager' => 'Support Manager'] as $type => $label)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ $label }} Clearance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge badge-{{ $termination->{$type.'_status'} == 'approved' ? 'success' : ($termination->{$type.'_status'} == 'rejected' ? 'danger' : 'warning') }} badge-lg">
                                            {{ ucfirst($termination->{$type.'_status'}) }}
                                        </span>
                                        @if($termination->{$type.'_approved_at'})
                                            <small class="text-muted">
                                                <strong>Approved on:</strong> {{ \Carbon\Carbon::parse($termination->{$type.'_approved_at'})->format('M d, Y H:i') }}<br>
                                                @if(isset($approvers[$type]))
                                                    <strong>Approved by:</strong> {{ $approvers[$type]['name'] }}
                                                    <span class="badge badge-{{ $approvers[$type]['type'] == 'admin' ? 'primary' : 'info' }} ml-1">
                                                        {{ ucfirst($approvers[$type]['type']) }}
                                                    </span>
                                                @endif
                                            </small>
                                        @endif
                                    </div>

                                    @if($termination->{$type.'_remarks'})
                                        <div class="alert alert-info">
                                            <strong>Remarks:</strong><br>
                                            {{ $termination->{$type.'_remarks'} }}
                                        </div>
                                    @endif

                                    <!-- Approval Form (show only if pending and user has appropriate role) -->
                                    <form action="{{ route('clearance.update-status', $termination->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="clearance_type" value="{{ $type }}">
                                        <div class="form-group">
                                            <label><strong>Remarks:</strong></label>
                                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks (optional)"></textarea>
                                        </div>
                                        <div class="btn-group w-100">
                                            <button type="submit" name="status" value="approved" class="btn btn-success btn-lg">
                                                <i class="fas fa-check"></i> Approve Clearance
                                            </button>
                                            <button type="submit" name="status" value="rejected" class="btn btn-danger btn-lg">
                                                <i class="fas fa-times"></i> Reject Clearance
                                            </button>
                                        </div>
                                    </form>
                                   
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection