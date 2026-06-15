@extends('layouts.index')

@section('title', 'Detailed Ticket Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Detailed Ticket Report</h2>
                <div class="btn-group">
                    <a href="{{ route('ticket.reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    @if($role === 'admin')
                    <button type="button" class="btn btn-success" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ticket.reports.detailed') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="ticket_subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="ticket_subject" name="ticket_subject" 
                                       value="{{ request('ticket_subject') }}" placeholder="Search by subject...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in progress" {{ request('status') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="on hold" {{ request('status') == 'on hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All Priorities</option>
                                    <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            @if($role === 'admin')
                            <div class="col-md-2">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="created_by" class="form-label">Created By</label>
                                <select class="form-select" id="created_by" name="created_by">
                                    <option value="">All Creators</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('created_by') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('ticket.reports.detailed') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tickets ({{ $tickets->count() }} results)</h5>
                    <small class="text-muted">Total tickets found: {{ $tickets->count() }}</small>
                </div>
                <div class="card-body">
                    @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Creator</th>
                                    @if($role === 'admin')
                                    <th>Assignee</th>
                                    @endif
                                    <th>Asset</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
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
                                            ($ticket->status == 'in progress' ? 'info' : 
                                            ($ticket->status == 'new' ? 'primary' : 'secondary'))) 
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
                                    @if($role === 'admin')
                                    <td>
                                        @if($ticket->assign1_firstname)
                                            {{ $ticket->assign1_firstname }} {{ $ticket->assign1_lastname }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>{{ $ticket->asset_name ?? 'N/A' }}</td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y H:i') }}</small>
                                    </td>
                                    
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tickets.show', $ticket->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($role === 'admin' || $ticket->created_by == session('user_id') || 
                                                $ticket->assign_1 == session('user_id') || 
                                                $ticket->assign_2 == session('user_id') || 
                                                $ticket->assign_3 == session('user_id'))
                                            <a href="{{ route('tickets.edit', $ticket->id) }}" 
                                               class="btn btn-outline-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tickets found</h5>
                        <p class="text-muted">Try adjusting your filters to see more results.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    // Get current filter parameters
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Redirect to export URL with current filters
    window.location.href = '{{ route("ticket.reports.export.csv") }}?' + params.toString();
}
</script>
@endsection
