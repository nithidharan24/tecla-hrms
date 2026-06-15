@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-message">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <script>
            setTimeout(function() {
                let alert = document.getElementById('alert-message');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        </script>

        {{-- Tabs --}}
        <div class="row">
            <div class="col-md-12">
                <ul class="custom-report-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab"
                                data-bs-target="#dashboard" type="button" role="tab">
                            Dashboard & Analytics ({{ $allTicketsCount }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#all-tickets" type="button" role="tab">
                            All Tickets
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <!-- <h3 class="page-title">Tickets Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tickets</li>
                    </ul> -->
                </div>
                <div class="col-auto float-end ms-auto d-flex">
                    <a href="{{ route('tickets.create') }}" class="btn add-btn me-2">
                        <i class="fa fa-plus"></i> Create Ticket
                    </a>

                    <button class="btn btn-outline-secondary" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"
                            aria-controls="filterOffcanvas">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content">
                    {{-- Dashboard & Analytics Tab --}}
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        
                        {{-- Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <div class="card summary-card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Total Tickets</h6>
                                                <h2 class="mb-0">{{ $allTicketsCount }}</h2>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <div class="card summary-card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Open Tickets</h6>
                                                <h2 class="mb-0">{{ $openTicketsCount }}</h2>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-folder-open fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <div class="card summary-card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">In Progress</h6>
                                                <h2 class="mb-0">{{ $inProgressTicketsCount }}</h2>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-spinner fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                                <div class="card summary-card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Resolved/Closed</h6>
                                                <h2 class="mb-0">{{ $resolvedTicketsCount + $closedTicketsCount }}</h2>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-check-circle fa-3x opacity-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Department-wise Chart Row --}}
                        <div class="row mb-4">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Department-wise Tickets</h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $deptStats = $tickets->groupBy('department_name')->map(function($deptTickets) {
                                                return $deptTickets->count();
                                            })->sortDesc()->take(5);
                                        @endphp
                                        
                                        @if($deptStats->count() > 0)
                                            <div class="chart-container" style="height: 250px;">
                                                <canvas id="deptChart"></canvas>
                                            </div>
                                            <div class="mt-3">
                                                @foreach($deptStats as $dept => $count)
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span>{{ $dept ?: 'N/A' }}</span>
                                                        <span class="badge bg-primary">{{ $count }} tickets</span>
                                                    </div>
                                                    <div class="progress mb-3" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" 
                                                             role="progressbar" 
                                                             style="width: {{ ($count / $allTicketsCount) * 100 }}%" 
                                                             aria-valuenow="{{ $count }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="{{ $allTicketsCount }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No department data available</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Employee-wise Tickets</h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $employeeStats = $tickets->groupBy(function($ticket) {
                                                return $ticket->raiser_firstname . ' ' . $ticket->raiser_lastname;
                                            })->map(function($empTickets) {
                                                return $empTickets->count();
                                            })->sortDesc()->take(5);
                                        @endphp
                                        
                                        @if($employeeStats->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th class="text-end">Ticket Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($employeeStats as $employee => $count)
                                                            <tr>
                                                                <td>{{ $employee }}</td>
                                                                <td class="text-end">
                                                                    <span class="badge bg-info">{{ $count }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No employee data available</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Ticket Status Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="p-3 border rounded bg-light">
                                                    <h3 class="text-primary">{{ $openTicketsCount }}</h3>
                                                    <span class="text-muted">Open</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="p-3 border rounded bg-light">
                                                    <h3 class="text-warning">{{ $inProgressTicketsCount }}</h3>
                                                    <span class="text-muted">In Progress</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="p-3 border rounded bg-light">
                                                    <h3 class="text-secondary">{{ $tickets->where('states', 'Waiting')->count() }}</h3>
                                                    <span class="text-muted">Waiting</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="p-3 border rounded bg-light">
                                                    <h3 class="text-info">{{ $resolvedTicketsCount }}</h3>
                                                    <span class="text-muted">Resolved</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="p-3 border rounded bg-light">
                                                    <h3 class="text-success">{{ $closedTicketsCount }}</h3>
                                                    <span class="text-muted">Closed</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- All Tickets Tab (Only Table) --}}
                    <div class="tab-pane fade" id="all-tickets" role="tabpanel">
                        {{-- Tickets Table --}}
                        @if($tickets && count($tickets) > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Raised By</th>
                                                    <th>Assigned To</th>
                                                    <th>Department</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    <th>Attachment</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tickets as $index => $ticket)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                           
                                                                {{ $ticket->ticket_id ?? 'N/A' }}
                                                        
                                                        </td>
                                                        <td>{{ Str::limit($ticket->title ?? '', 30) }}</td>
                                                        <td>
                                                            @switch($ticket->category)
                                                                @case('Hardware')
                                                                    <span class="badge bg-info">Hardware</span>
                                                                    @break
                                                                @case('Software')
                                                                    <span class="badge bg-primary">Software</span>
                                                                    @break
                                                                @case('Network')
                                                                    <span class="badge bg-warning">Network</span>
                                                                    @break
                                                                @case('HR Access')
                                                                    <span class="badge bg-danger">HR Access</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ $ticket->category }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($ticket->raiser_image)
                                                                    <img src="{{ asset($ticket->raiser_image) }}" 
                                                                         class="rounded-circle me-2" 
                                                                         width="30" height="30"
                                                                         onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                                                                @else
                                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                                         style="width: 30px; height: 30px;">
                                                                        {{ strtoupper(substr($ticket->raiser_firstname ?? 'U', 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <span>{{ $ticket->raiser_firstname }} {{ $ticket->raiser_lastname }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($ticket->assigned_to)
                                                                <div class="d-flex align-items-center">
                                                                    @if($ticket->assignee_image)
                                                                        <img src="{{ asset($ticket->assignee_image) }}" 
                                                                             class="rounded-circle me-2" 
                                                                             width="30" height="30"
                                                                             onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                                                                    @else
                                                                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2" 
                                                                             style="width: 30px; height: 30px;">
                                                                            {{ strtoupper(substr($ticket->assignee_firstname ?? 'S', 0, 1)) }}
                                                                        </div>
                                                                    @endif
                                                                    <span>{{ $ticket->assignee_firstname }} {{ $ticket->assignee_lastname }}</span>
                                                                    @if($ticket->assignee_designation_name)
                                                                        <small class="text-muted ms-1">({{ $ticket->assignee_designation_name }})</small>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic">Not Assigned</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $ticket->department_name ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if(in_array($role, ['admin', 'support']))
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    @php
                                                                        $priorityColors = [
                                                                            'Low' => 'success',
                                                                            'Medium' => 'warning',
                                                                            'High' => 'danger',
                                                                            'Critical' => 'dark'
                                                                        ];
                                                                        $priorityColor = $priorityColors[$ticket->priority] ?? 'secondary';
                                                                    @endphp
                                                                    <i class="fa fa-circle text-{{ $priorityColor }}"></i> 
                                                                    {{ $ticket->priority }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Critical')">
                                                                        <i class="fa fa-circle text-dark"></i> Critical
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                                @php
                                                                    $priorityColors = [
                                                                        'Low' => 'success',
                                                                        'Medium' => 'warning',
                                                                        'High' => 'danger',
                                                                        'Critical' => 'dark'
                                                                    ];
                                                                    $priorityColor = $priorityColors[$ticket->priority] ?? 'secondary';
                                                                @endphp
                                                                <span class="badge bg-{{ $priorityColor }}">
                                                                    {{ $ticket->priority }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(in_array($role, ['admin', 'support']))
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="stateDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    @php
                                                                        $stateColors = [
                                                                            'Open' => 'primary',
                                                                            'In progress' => 'warning',
                                                                            'Waiting' => 'secondary',
                                                                            'Resolved' => 'info',
                                                                            'Closed' => 'success'
                                                                        ];
                                                                        $stateColor = $stateColors[$ticket->states] ?? 'secondary';
                                                                    @endphp
                                                                    <i class="fa fa-circle text-{{ $stateColor }}"></i> 
                                                                    {{ $ticket->states }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updateState({{ $ticket->id }}, 'Open')">
                                                                        <i class="fa fa-circle text-primary"></i> Open
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updateState({{ $ticket->id }}, 'In progress')">
                                                                        <i class="fa fa-circle text-warning"></i> In Progress
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updateState({{ $ticket->id }}, 'Waiting')">
                                                                        <i class="fa fa-circle text-secondary"></i> Waiting
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updateState({{ $ticket->id }}, 'Resolved')">
                                                                        <i class="fa fa-circle text-info"></i> Resolved
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updateState({{ $ticket->id }}, 'Closed')">
                                                                        <i class="fa fa-circle text-success"></i> Closed
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                                @php
                                                                    $stateColors = [
                                                                        'Open' => 'primary',
                                                                        'In progress' => 'warning',
                                                                        'Waiting' => 'secondary',
                                                                        'Resolved' => 'info',
                                                                        'Closed' => 'success'
                                                                    ];
                                                                    $stateColor = $stateColors[$ticket->states] ?? 'secondary';
                                                                @endphp
                                                                <span class="badge bg-{{ $stateColor }}">
                                                                    {{ $ticket->states }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($ticket->uploaded_files)
                                                                <a href="{{ route('tickets.download', $ticket->id) }}" 
                                                                   class="btn btn-sm btn-success" 
                                                                   title="Download Attachment">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                   
                                                                    @php
                                                                        $canEdit = false;
                                                                        if ($role === 'admin' || $role === 'support') {
                                                                            $canEdit = true;
                                                                        } elseif ($role === 'employee' && $ticket->raised_by == session('user_id') && !in_array($ticket->states, ['Resolved', 'Closed'])) {
                                                                            $canEdit = true;
                                                                        }
                                                                    @endphp
                                                                    
                                                                    @if($canEdit)
                                                                        <a class="dropdown-item" href="{{ route('tickets.edit', $ticket->id) }}">
                                                                            <i class="fa fa-edit"></i> Edit
                                                                        </a>
                                                                    @endif
                                                                    
                                                                    @if($role === 'admin')
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item text-danger" href="#" 
                                                                           data-ticket-id="{{ $ticket->id }}" 
                                                                           data-ticket-title="{{ $ticket->title }}"
                                                                           onclick="setDeleteTicket(this)">
                                                                            <i class="fa fa-trash"></i> Delete
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
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-ticket-alt"></i></div>
                                    <div class="empty-state-title">No Tickets Found</div>
                                    <div class="empty-state-text">No tickets found with current filters.</div>
                                    <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-3">
                                        <i class="fa fa-plus"></i> Create Your First Ticket
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Offcanvas (Right Side) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="fa fa-filter"></i> Filter Tickets
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('tickets.index') }}" id="filterForm">
                <div class="mb-3">
                    <label for="ticket_id" class="form-label">Ticket ID</label>
                    <input type="text" class="form-control" id="ticket_id" name="ticket_id" 
                           value="{{ request('ticket_id') }}" placeholder="Search by Ticket ID...">
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="{{ request('title') }}" placeholder="Search by title...">
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="Hardware" {{ request('category') == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="Software" {{ request('category') == 'Software' ? 'selected' : '' }}>Software</option>
                        <option value="Network" {{ request('category') == 'Network' ? 'selected' : '' }}>Network</option>
                        <option value="HR Access" {{ request('category') == 'HR Access' ? 'selected' : '' }}>HR Access</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="states" class="form-label">State</label>
                    <select class="form-select" id="states" name="states">
                        <option value="">All States</option>
                        <option value="Open" {{ request('states') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In progress" {{ request('states') == 'In progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Waiting" {{ request('states') == 'Waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="Resolved" {{ request('states') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Closed" {{ request('states') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                @if(in_array($role, ['admin', 'support']))
                <div class="mb-3">
                    <label for="assigned_to" class="form-label">Assigned To</label>
                    <select class="form-select" id="assigned_to" name="assigned_to">
                        <option value="">All Assignees</option>
                        @foreach($supportTeam as $support)
                            <option value="{{ $support->id }}" {{ request('assigned_to') == $support->id ? 'selected' : '' }}>
                                {{ $support->firstname }} {{ $support->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date') }}">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date') }}">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .custom-report-tabs {
            display: flex;
            gap: 35px;
            border-bottom: 1px solid #e5e7eb;
            padding-left: 0;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .custom-report-tabs .nav-item {
            list-style: none;
        }

        .custom-report-tabs .nav-link {
            background: none !important;
            border: none !important;
            font-size: 16px;
            color: #555;
            padding: 10px 0;
            position: relative;
            border-radius: 0 !important;
            font-weight: 500;
            cursor: pointer;
        }

        .custom-report-tabs .nav-link:hover {
            color: #222;
        }

        .custom-report-tabs .nav-link.active {
            color: #ff6b00 !important;
            font-weight: 600;
        }

        .custom-report-tabs .nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 30px;
            height: 3px;
            background: #ff6b00;
            border-radius: 10px;
        }

        .summary-card {
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .summary-icon {
            opacity: 0.7;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        .ticket-table .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .ticket-table .empty-state-icon {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .ticket-table .empty-state-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .ticket-table .empty-state-text {
            color: #999;
            font-size: 14px;
        }

        .add-btn {
            background: #ff6b00;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
        }

        .add-btn:hover {
            background: #e65c00;
            color: white;
        }

        .action-label .btn-white {
            background: white;
            border: 1px solid #ddd;
            padding: 4px 12px;
            font-size: 12px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 11px;
            font-weight: 500;
            padding: 5px 8px;
        }

        .rounded-circle {
            object-fit: cover;
        }

        .bg-primary {
            background-color: #0d6efd !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .bg-success {
            background-color: #198754 !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Department Chart
            const deptStats = @json($deptStats);
            if (deptStats && Object.keys(deptStats).length > 0) {
                const ctx = document.getElementById('deptChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(deptStats).map(dept => dept ? dept : 'N/A'),
                        datasets: [{
                            label: 'Number of Tickets',
                            data: Object.values(deptStats),
                            backgroundColor: [
                                '#0d6efd',
                                '#ffc107',
                                '#0dcaf0',
                                '#198754',
                                '#dc3545'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });

        function resetFilters() {
            window.location.href = "{{ route('tickets.index') }}";
        }

        @if(in_array($role, ['admin', 'support']))
        function updateState(ticketId, state) {

let url = "{{ route('tickets.updateState', ':id') }}";
url = url.replace(':id', ticketId);

Swal.fire({
    title: 'Update State',
    text: `Are you sure you want to change the state to "${state}"?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, update it!'
}).then((result) => {

    if (result.isConfirmed) {

        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                states: state
            },
            success: function(response) {

                if (response.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Ticket state updated'
                    }).then(() => location.reload());

                } else {

                    Swal.fire('Error', response.message, 'error');

                }

            }
        });

    }

});

}

function updatePriority(ticketId, priority) {

let url = "{{ route('tickets.updatePriority', ':id') }}";
url = url.replace(':id', ticketId);

Swal.fire({
    title: 'Update Priority',
    text: `Change priority to "${priority}"?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes'
}).then((result) => {

    if (result.isConfirmed) {

        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                priority: priority
            },
            success: function(response) {

                if (response.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Priority updated'
                    }).then(() => location.reload());

                } else {

                    Swal.fire('Error', response.message, 'error');

                }

            }
        });

    }

});

}
        @endif

        @if($role === 'admin')
        function setDeleteTicket(element) {
            const ticketId = element.getAttribute('data-ticket-id');
            const ticketTitle = element.getAttribute('data-ticket-title');

            Swal.fire({
                title: 'Delete Ticket',
                html: `Are you sure you want to delete the ticket:<br><strong>${ticketTitle}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url("/tickets") }}/' + ticketId;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        @endif

        // Auto-submit form when filter changes (optional)
        $(document).ready(function() {
            $('#filterForm select').on('change', function() {
                // Uncomment to auto-submit on select change
                // $('#filterForm').submit();
            });
        });
    </script>
@endsection