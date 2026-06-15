@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
            
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Ticket Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Ticket Report</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        
        @if(session('success'))
            <div id="successMessage" class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Tickets</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('ticket.reports.index') }}" id="filterForm">
                    <div class="row filter-row">
                        <!-- Status Filter -->
                        <div class="col-sm-6 col-md-3"> 
                            <div class="input-block mb-3 form-focus select-focus">
                                <select name="status" class="form-select floating">
                                    <option value="">All Status</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>

                        <!-- Priority Filter -->
                        <div class="col-sm-6 col-md-3"> 
                            <div class="input-block mb-3 form-focus select-focus">
                                <select name="priority" class="form-select floating">
                                    <option value="">All Priority</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                        </div>

                        <!-- From Date -->
                        <div class="col-sm-6 col-md-3">  
                            <div class="input-block mb-3 form-focus">
                                <div class="cal-icon">
                                    <input name="start_date" class="form-control floating datetimepicker" type="text" 
                                           placeholder="dd-mm-yyyy" value="{{ request('start_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <!-- To Date -->
                        <div class="col-sm-6 col-md-3">  
                            <div class="input-block mb-3 form-focus">
                                <div class="cal-icon">
                                    <input name="end_date" class="form-control floating datetimepicker" type="text" 
                                           placeholder="dd-mm-yyyy" value="{{ request('end_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Search and Reset Buttons -->
                        <div class="col-sm-6 col-md-6">  
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <a href="{{ route('ticket.reports.index') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>     
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h3 class="card-title">{{ $summaryStats['total'] ?? 0 }}</h3>
                        <p class="card-text">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h3 class="card-title">{{ $summaryStats['new'] ?? 0 }}</h3>
                        <p class="card-text">New Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h3 class="card-title">{{ $summaryStats['open'] ?? 0 }}</h3>
                        <p class="card-text">Open Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h3 class="card-title">{{ $summaryStats['in_progress'] ?? 0 }}</h3>
                        <p class="card-text">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h3 class="card-title">{{ $summaryStats['closed'] ?? 0 }}</h3>
                        <p class="card-text">Closed Tickets</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        @if(isset($tickets) && count($tickets) > 0)
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <div class="btn-group">
                    <a href="{{ route('ticket.reports.export.csv', request()->query()) }}" class="btn btn-primary">
                        <i class="fa fa-file-excel"></i> Export to CSV
                    </a>
                    <a href="{{ route('ticket.reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="fa fa-file-pdf"></i> Export to PDF
                    </a>
                    <a href="{{ route('ticket.reports.detailed') }}" class="btn btn-info">
                        <i class="fa fa-list"></i> Detailed Report
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Results Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if(isset($tickets) && count($tickets) > 0)
                        <div class="table-responsive">
                            <table class="table custom-table mb-0" id="ticketTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Ticket ID</th>
                                        <th>Subject</th>
                                        <th>Client</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td data-label="S.No">{{ $loop->iteration }}</td>
                                        <td data-label="Ticket ID">
                                            <span class="high">{{ $ticket->ticket_id ?? 'N/A' }}</span>
                                        </td>
                                        <td data-label="Subject">{{ $ticket->subject ?? 'N/A' }}</td>
                                        <td data-label="Client">
                                            <span class="od-chip-highlight">{{ $ticket->client->full_name ?? 'N/A' }}</span>
                                        </td>
                                        <td data-label="Created Date">
                                            {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') : 'N/A' }}
                                        </td>
                                        <td data-label="Status">
                                            @php
                                                $statusClasses = [
                                                    'new' => 'info',
                                                    'open' => 'warning',
                                                    'in_progress' => 'secondary',
                                                    'closed' => 'success'
                                                ];
                                                $status = strtolower($ticket->status ?? '');
                                                $badgeClass = $statusClasses[$status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'Unknown')) }}</span>
                                        </td>
                                        <td data-label="Priority">
                                            @php
                                                $priorityClasses = [
                                                    'low' => 'success',
                                                    'medium' => 'warning',
                                                    'high' => 'danger'
                                                ];
                                                $priority = strtolower($ticket->priority ?? '');
                                                $priorityBadgeClass = $priorityClasses[$priority] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $priorityBadgeClass }}">{{ ucfirst($ticket->priority ?? 'Unknown') }}</span>
                                        </td>
                                        <td data-label="Assigned To">
                                            {{ $ticket->assignedTo->firstname ?? 'Unassigned' }} {{ $ticket->assignedTo->lastname ?? '' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-ticket fa-3x text-muted"></i>
                            </div>
                            <h5>No tickets found</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Performance Section (Admin Only) -->
        @if($role === 'admin' && isset($employeeStats['assigned']) && $employeeStats['assigned']->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Employee Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Assigned Tickets</th>
                                        <th>Closed Tickets</th>
                                        <th>Success Rate</th>
                                        <th>Avg Resolution (Hours)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeStats['assigned'] as $employee)
                                    @php
                                        $assigned = $employee->assigned_tickets ?? 0;
                                        $closed = $employee->closed_tickets ?? 0;
                                        $successRate = $assigned > 0 
                                            ? round(($closed / $assigned) * 100, 1) 
                                            : 0;
                                        $employeeId = $employee->id ?? null;
                                    @endphp
                                    @if($employeeId)
                                    <tr>
                                        <td>{{ $employee->firstname }} {{ $employee->lastname }}</td>
                                        <td>{{ $assigned }}</td>
                                        <td>{{ $closed }}</td>
                                        <td>
                                            <span class="badge bg-{{ $successRate >= 80 ? 'success' : ($successRate >= 60 ? 'warning' : 'danger') }}">
                                                {{ $successRate }}%
                                            </span>
                                        </td>
                                        <td>{{ round($employee->avg_resolution_hours ?? 0, 2) }}</td>
                                        <td>
                                            <a href="{{ route('ticket.reports.employee.performance', ['employeeId' => $employeeId]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-chart-line"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.transition = 'opacity 1s ease';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.remove();
            }, 1000);
        }
    }, 5000);

    // Initialize date pickers
    if (typeof $.fn.datetimepicker !== 'undefined') {
        $('.datetimepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            icons: {
                up: 'fa fa-angle-up',
                down: 'fa fa-angle-down',
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            }
        });
    }

    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('ticketTable')) {
        $('#ticketTable').DataTable({
            "pageLength": 25,
            "ordering": true,
            "searching": true,
            "lengthChange": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    }

    // Initialize DataTable for employee performance table
    if (typeof $.fn.DataTable !== 'undefined' && document.querySelector('.custom-table')) {
        $('.custom-table').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true,
            "lengthChange": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    }
});
</script>

<style>
.filter-row .input-block {
    margin-bottom: 1rem;
}

.badge {
    font-size: 0.75em;
    padding: 0.375rem 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group .btn {
    margin-right: 0.5rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.dropdown-toggle::after {
    margin-left: 0.5rem;
}

.stat-card {
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection