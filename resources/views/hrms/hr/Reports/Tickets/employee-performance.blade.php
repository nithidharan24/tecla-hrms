@extends('layouts.index')

@section('title', 'Employee Performance Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Employee Performance Report</h2>
                </div>
                <div class="btn-group">
                    <a href="{{ route('ticket.reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('ticket.reports.employee.performance', $employee->id) }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h3 class="card-title">{{ $assignedStats->total_assigned ?? 0 }}</h3>
                    <p class="card-text">Assigned Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h3 class="card-title">{{ $assignedStats->closed_tickets ?? 0 }}</h3>
                    <p class="card-text">Closed Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <h3 class="card-title">{{ $createdStats->total_created ?? 0 }}</h3>
                    <p class="card-text">Created Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body">
                    <h3 class="card-title">{{ round($assignedStats->avg_resolution_hours ?? 0, 1) }}h</h3>
                    <p class="card-text">Avg Resolution Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assignment Performance</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalAssigned = $assignedStats->total_assigned ?? 0;
                        $closedTickets = $assignedStats->closed_tickets ?? 0;
                        $openTickets = $assignedStats->open_tickets ?? 0;
                        $inProgressTickets = $assignedStats->in_progress_tickets ?? 0;
                        $successRate = $totalAssigned > 0 ? round(($closedTickets / $totalAssigned) * 100, 1) : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Success Rate</span>
                            <span class="fw-bold text-{{ $successRate >= 80 ? 'success' : ($successRate >= 60 ? 'warning' : 'danger') }}">
                                {{ $successRate }}%
                            </span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $successRate >= 80 ? 'success' : ($successRate >= 60 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $successRate }}%"></div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="text-success">{{ $closedTickets }}</h6>
                                <small class="text-muted">Closed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="text-info">{{ $inProgressTickets }}</h6>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6 class="text-warning">{{ $openTickets }}</h6>
                            <small class="text-muted">Open</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Creation Activity</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalCreated = $createdStats->total_created ?? 0;
                        $resolvedCreated = $createdStats->resolved_created ?? 0;
                        $highPriorityCreated = $createdStats->high_priority_created ?? 0;
                        $creationResolutionRate = $totalCreated > 0 ? round(($resolvedCreated / $totalCreated) * 100, 1) : 0;
                    @endphp

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h5 class="text-primary">{{ $totalCreated }}</h5>
                            <small class="text-muted">Total Created</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-success">{{ $resolvedCreated }}</h5>
                            <small class="text-muted">Resolved</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-danger">{{ $highPriorityCreated }}</h5>
                            <small class="text-muted">High Priority</small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Resolution Rate</span>
                            <span class="fw-bold">{{ $creationResolutionRate }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $creationResolutionRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Creator</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $ticket->ticket_id }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $ticket->ticket_subject }}">
                                            {{ $ticket->ticket_subject }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $ticket->status == 'closed' ? 'success' : 
                                            ($ticket->status == 'open' ? 'warning' : 
                                            ($ticket->status == 'in progress' ? 'info' : 'primary')) 
                                        }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $ticket->priority == 'High' ? 'danger' : 
                                            ($ticket->priority == 'Medium' ? 'warning' : 'success') 
                                        }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->creator_firstname }} {{ $ticket->creator_lastname }}</td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket->id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent activity found</h6>
                        <p class="text-muted">No tickets found for the selected date range.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
