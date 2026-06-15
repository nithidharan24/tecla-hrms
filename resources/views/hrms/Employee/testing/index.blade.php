@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Testing');
    use Carbon\Carbon; 
    $employeeId = Session::get('user_id');
    // $allTickets is already available from controller
@endphp
@extends('layouts.index')

@section('content')
    <div class="content container-fluid">
      
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
                        <button class="nav-link active" id="all-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#all-tickets" type="button" role="tab">
                            All Testing  ({{ count($allTickets) }})
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="open-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#open-tickets" type="button" role="tab">
                            Open Testing 
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="in-progress-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#in-progress-tickets" type="button" role="tab">
                            In Progress 
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="resolved-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#resolved-tickets" type="button" role="tab">
                            Resolved
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="closed-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#closed-tickets" type="button" role="tab">
                            Closed
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reopened-tickets-tab" data-bs-toggle="tab"
                                data-bs-target="#reopened-tickets" type="button" role="tab">
                            Reopened
                        </button>
                    </li>
                    {{-- Activity Log Tab --}}
<li class="nav-item" role="presentation">
    <button class="nav-link" id="activity-log-tab" data-bs-toggle="tab"
            data-bs-target="#activity-log" type="button" role="tab">
        <i class="fa fa-history"></i> Activity Log
    </button>
</li>
                </ul>
            </div>
        </div>

        <div class="page-hea52der">
            <div class="row align-items-center">
                <div class="col">
                    <!-- <h3 class="page-title">Testing Tickets Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Testing Tickets</li>
                    </ul> -->
                </div>
                <div class="col-auto float-end ms-auto d-flex">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('testing.create') }}" class="btn add-btn me-2">
                        <i class="fa fa-plus"></i> Add Testing
                    </a>
                    @endif

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

                    {{-- All Testing Tickets Tab --}}
                    <div class="tab-pane fade show active" id="all-tickets" role="tabpanel">
                        @if($allTickets && count($allTickets) > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Testing ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allTickets as $index => $ticket)
                                                    @php
                                                        $ticket = (object) $ticket; // Ensure it's an object
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_approve))
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="statusDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    @php
                                                                        $status = $ticket->status ?? 'Open';
                                                                        $statusIcons = [
                                                                            'Open' => 'primary',
                                                                            'In Progress' => 'warning',
                                                                            'Resolved' => 'info',
                                                                            'Closed' => 'success',
                                                                            'Reopen' => 'danger'
                                                                        ];
                                                                        $statusColor = $statusIcons[$status] ?? 'secondary';
                                                                    @endphp
                                                                    <i class="fa fa-circle text-{{ $statusColor }}"></i> 
                                                                    {{ $status }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updateStatus({{ $ticket->id }}, 'Open')">Open</button>
                                                                    <button class="dropdown-item" onclick="updateStatus({{ $ticket->id }}, 'In Progress')">In Progress</button>
                                                                    <button class="dropdown-item" onclick="updateStatus({{ $ticket->id }}, 'Resolved')">Resolved</button>
                                                                    <button class="dropdown-item" onclick="updateStatus({{ $ticket->id }}, 'Closed')">Closed</button>
                                                                    <button class="dropdown-item" onclick="updateStatus({{ $ticket->id }}, 'Reopen')">Reopen</button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            @php
                                                                $status = $ticket->status ?? 'Open';
                                                                $statusColors = [
                                                                    'Open' => 'primary',
                                                                    'In Progress' => 'warning',
                                                                    'Resolved' => 'info',
                                                                    'Closed' => 'success',
                                                                    'Reopen' => 'danger'
                                                                ];
                                                                $statusColor = $statusColors[$status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $statusColor }}">
                                                                {{ $status }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        @endif
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No Testing Tickets</div>
                                    <div class="empty-state-text">No testing tickets found with current filters.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Open Testing Tab --}}
                    <div class="tab-pane fade" id="open-tickets" role="tabpanel">
                        @php
                            $openTickets = collect($allTickets)->where('status', 'Open')->values();
                        @endphp

                        @if($openTickets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($openTickets as $ticket)
                                                    @php
                                                        $ticket = (object) $ticket;
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                {{ $ticket->status ?? 'Open' }}
                                                            </span>
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No Open Testing Tickets</div>
                                    <div class="empty-state-text">All caught up! There are no open testing tickets.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- In Progress Tab --}}
                    <div class="tab-pane fade" id="in-progress-tickets" role="tabpanel">
                        @php
                            $inProgressTickets = collect($allTickets)->where('status', 'In Progress')->values();
                        @endphp

                        @if($inProgressTickets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($inProgressTickets as $ticket)
                                                    @php
                                                        $ticket = (object) $ticket;
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning">
                                                                {{ $ticket->status ?? 'In Progress' }}
                                                            </span>
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No In Progress Testing Tickets</div>
                                    <div class="empty-state-text">No testing tickets are currently in progress.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Resolved Tab --}}
                    <div class="tab-pane fade" id="resolved-tickets" role="tabpanel">
                        @php
                            $resolvedTickets = collect($allTickets)->where('status', 'Resolved')->values();
                        @endphp

                        @if($resolvedTickets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resolvedTickets as $ticket)
                                                    @php
                                                        $ticket = (object) $ticket;
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                {{ $ticket->status ?? 'Resolved' }}
                                                            </span>
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No Resolved Testing Tickets</div>
                                    <div class="empty-state-text">No testing tickets have been resolved yet.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Closed Tab --}}
                    <div class="tab-pane fade" id="closed-tickets" role="tabpanel">
                        @php
                            $closedTickets = collect($allTickets)->where('status', 'Closed')->values();
                        @endphp

                        @if($closedTickets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($closedTickets as $ticket)
                                                    @php
                                                        $ticket = (object) $ticket;
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                {{ $ticket->status ?? 'Closed' }}
                                                            </span>
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No Closed Testing Tickets</div>
                                    <div class="empty-state-text">No testing tickets have been closed yet.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Reopened Tab --}}
                    <div class="tab-pane fade" id="reopened-tickets" role="tabpanel">
                        @php
                            $reopenedTickets = collect($allTickets)->where('status', 'Reopen')->values();
                        @endphp

                        @if($reopenedTickets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Ticket ID</th>
                                                    <th>Project</th>
                                                    <th>Description</th>
                                                    <th>Created By</th>
                                                    <th>Assigned To</th>
                                                    <th>Tester</th>
                                                    <th>Created Date</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($reopenedTickets as $ticket)
                                                    @php
                                                        $ticket = (object) $ticket;
                                                        $canEdit = false;
                                                        if ($role === 'admin') {
                                                            $canEdit = true;
                                                        } elseif ($role === 'employee') {
                                                            $canEdit = ($ticket->created_by == $employeeId || 
                                                                       $ticket->assigned_to == $employeeId ||
                                                                       $ticket->tester_id == $employeeId);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('testing.show', $ticket->id) }}" class="text-primary fw-bold">
                                                                {{ $ticket->testing_ticket_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ $ticket->projectname ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($ticket->description ?? '', 50) }}</td>
                                                        <td>
                                                            @if(!empty($ticket->creator_name))
                                                                <span class="small">{{ $ticket->creator_name }}</span>
                                                            @else
                                                                <span class="text-muted">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->assigned_name))
                                                                <span class="badge bg-info me-1 mb-1">
                                                                    {{ $ticket->assigned_name }}
                                                                </span>
                                                            @else
                                                                @if($role === 'admin')
                                                                    <button class="btn btn-sm btn-outline-warning" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Unassigned</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($ticket->tester_name))
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $ticket->tester_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">No Tester</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ Carbon::parse($ticket->created_at ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @if($role === 'admin')
                                                            <div class="dropdown action-label">
                                                                <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button" 
                                                                        id="priorityDropdown{{ $ticket->id }}" data-bs-toggle="dropdown">
                                                                    <i class="fa fa-circle text-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}"></i> 
                                                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'High')">
                                                                        <i class="fa fa-circle text-danger"></i> High
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Medium')">
                                                                        <i class="fa fa-circle text-warning"></i> Medium
                                                                    </button>
                                                                    <button class="dropdown-item" onclick="updatePriority({{ $ticket->id }}, 'Low')">
                                                                        <i class="fa fa-circle text-success"></i> Low
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <span class="badge bg-{{ ($ticket->priority ?? 'Medium') === 'High' ? 'danger' : (($ticket->priority ?? 'Medium') === 'Medium' ? 'warning' : 'success') }}">
                                                                {{ $ticket->priority ?? 'Medium' }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-danger">
                                                                {{ $ticket->status ?? 'Reopen' }}
                                                            </span>
                                                        </td>
                                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('testing.show', $ticket->id) }}">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                    @if($canEdit && ($ticket->created_by ?? 0) != $employeeId)
                                                                    <a class="dropdown-item" href="{{ route('testing.edit', $ticket->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @endif
                                                                    @if($role === 'admin')
                                                                    <a class="dropdown-item" href="#" onclick="showAssignModal({{ $ticket->id }})">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-ticket-id="{{ $ticket->id }}" 
                                                                       data-ticket-subject="{{ $ticket->testing_ticket_id ?? 'N/A' }}"
                                                                       onclick="setDeleteTicket(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @endif
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
                                    <div class="empty-state-icon"><i class="fas fa-bug"></i></div>
                                    <div class="empty-state-title">No Reopened Testing Tickets</div>
                                    <div class="empty-state-text">No testing tickets have been reopened.</div>
                                </div>
                            </div>
                        @endif
                    </div>
{{-- Activity Log Tab Content --}}
<div class="tab-pane fade" id="activity-log" role="tabpanel">
    {{-- Filter Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fa fa-history me-2"></i>Activity Logs 
            @if($activityLogs && count($activityLogs) > 0)
                <small class="text-muted">({{ count($activityLogs) }} records)</small>
            @endif
        </h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" type="button" 
                    data-bs-toggle="collapse" data-bs-target="#activityLogFilters" 
                    aria-expanded="false" aria-controls="activityLogFilters">
                <i class="fa fa-filter"></i> Filter
            </button>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="listView" onclick="switchViewMode('list')">
                    <i class="fas fa-list"></i> 
                </label>
                <input type="radio" class="btn-check" name="viewMode" id="tableView" autocomplete="off">
                <label class="btn btn-outline-primary" for="tableView" onclick="switchViewMode('table')">
                    <i class="fas fa-table"></i>
                </label>
            </div>
            @if($activityLogs && count($activityLogs) > 0)
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportActivityLogs()">
                    <i class="fa fa-download"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- Collapsible Filters --}}
    <div class="collapse mb-3" id="activityLogFilters">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('testing.index') }}" id="activityLogFilterForm">
                    <input type="hidden" name="tab" value="activity-log">
                    
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="log_action" class="form-label">Action Type</label>
                            <select class="form-select" id="log_action" name="log_action">
                                <option value="">All Actions</option>
                                <option value="created" {{ request('log_action') == 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('log_action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('log_action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="assigned" {{ request('log_action') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="status_changed" {{ request('log_action') == 'status_changed' ? 'selected' : '' }}>Status Changed</option>
                                <option value="priority_changed" {{ request('log_action') == 'priority_changed' ? 'selected' : '' }}>Priority Changed</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_performed_by" class="form-label">Performed By</label>
                            <select class="form-select" id="log_performed_by" name="log_performed_by">
                                <option value="">All Users</option>
                                <option value="admin" {{ request('log_performed_by') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="employee" {{ request('log_performed_by') == 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_ticket_id" class="form-label">Testing Ticket ID</label>
                            <input type="text" class="form-control" id="log_ticket_id" name="log_ticket_id" 
                                   value="{{ request('log_ticket_id') }}" placeholder="Enter Testing ID">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_project_id" class="form-label">Project</label>
                            <select class="form-select" id="log_project_id" name="log_project_id">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('log_project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->projectname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="log_start_date" name="log_start_date" 
                                   value="{{ request('log_start_date') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="log_end_date" name="log_end_date" 
                                   value="{{ request('log_end_date') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="log_search" class="form-label">Search Description</label>
                            <input type="text" class="form-control" id="log_search" name="log_search" 
                                   value="{{ request('log_search') }}" placeholder="Search in description...">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="button" class="btn btn-secondary w-50" onclick="resetActivityLogFilters()">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary w-50">
                                    <i class="fa fa-search"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Quick Filter Pills (Optional) --}}
    @if(request()->hasAny(['log_action', 'log_performed_by', 'log_ticket_id', 'log_project_id', 'log_start_date', 'log_end_date', 'log_search']))
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark border">Active Filters:</span>
            @if(request('log_action'))
                <span class="badge bg-info">
                    Action: {{ ucfirst(request('log_action')) }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeFilter('log_action')"></button>
                </span>
            @endif
            @if(request('log_performed_by'))
                <span class="badge bg-info">
                    Role: {{ ucfirst(request('log_performed_by')) }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeFilter('log_performed_by')"></button>
                </span>
            @endif
            @if(request('log_ticket_id'))
                <span class="badge bg-info">
                    Testing ID: {{ request('log_ticket_id') }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeFilter('log_ticket_id')"></button>
                </span>
            @endif
            @if(request('log_project_id'))
                @php
                    $projectName = collect($projects)->firstWhere('id', request('log_project_id'))->projectname ?? 'Unknown';
                @endphp
                <span class="badge bg-info">
                    Project: {{ $projectName }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeFilter('log_project_id')"></button>
                </span>
            @endif
            @if(request('log_start_date') || request('log_end_date'))
                <span class="badge bg-info">
                    Date: {{ request('log_start_date') ?: 'Any' }} to {{ request('log_end_date') ?: 'Any' }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeDateFilters()"></button>
                </span>
            @endif
            @if(request('log_search'))
                <span class="badge bg-info">
                    Search: "{{ request('log_search') }}"
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" onclick="removeFilter('log_search')"></button>
                </span>
            @endif
        </div>
    </div>
    @endif

    @if($activityLogs && count($activityLogs) > 0)
        {{-- List View --}}
        <div id="listViewContent" class="view-content">
            <div class="activity-log-container">
                <div class="timeline">
                    @foreach($activityLogs as $log)
                        @php
                            $log = (object) $log;
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                @php
                                    $actionIcons = [
                                        'created' => 'fa-plus-circle text-success',
                                        'updated' => 'fa-edit text-primary',
                                        'deleted' => 'fa-trash text-danger',
                                        'assigned' => 'fa-user-plus text-info',
                                        'status_changed' => 'fa-exchange-alt text-warning',
                                        'priority_changed' => 'fa-flag text-secondary'
                                    ];
                                    $icon = $actionIcons[$log->action] ?? 'fa-history text-secondary';
                                @endphp
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                @if($log->testing_ticket_id)
                                                    <a href="{{ route('testing.show', $log->testing_ticket_id) }}" class="text-primary fw-bold">
                                                        {{ $log->testing_ticket_id }}
                                                    </a>
                                                    <span class="ms-2">- {{ $log->projectname ?? 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ Carbon::parse($log->created_at ?? now())->format('d M Y, h:i A') }}
                                                </span>
                                                <span class="badge bg-{{ ($log->performed_by_role ?? 'system') === 'admin' ? 'danger' : 'primary' }}">
                                                    {{ ucfirst($log->performed_by_role ?? 'system') }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="timeline-body">
                                    <div class="d-flex align-items-start gap-3 mb-2">
                                        <div class="avatar-sm">
                                            @php
                                                $initials = '';
                                                if ($log->performed_by_name) {
                                                    $nameParts = explode(' ', $log->performed_by_name);
                                                    $initials = '';
                                                    foreach ($nameParts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                    }
                                                    $initials = substr($initials, 0, 2);
                                                }
                                            @endphp
                                            <div class="avatar-title" style="background-color: {{ ($log->performed_by_role ?? 'system') === 'admin' ? '#dc3545' : '#0d6efd' }}; color: white;">
                                                {{ $initials ?: 'S' }}
                                            </div>
                                        </div>
                                        <div>
                                            <p class="mb-1"><strong>Performed by:</strong> {{ $log->performed_by_name ?? 'System' }}</p>
                                            <p class="mb-0 text-muted small">{{ $log->description ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($log->changes)
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-info" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#changes-list-{{ $log->id }}">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </button>
                                            <div class="collapse mt-2" id="changes-list-{{ $log->id }}">
                                                <div class="card card-body bg-light">
                                                    <h6 class="mb-3">Changes Made:</h6>
                                                    @php
                                                        $changes = json_decode($log->changes, true);
                                                    @endphp
                                                    @if(is_array($changes))
                                                        <div class="row">
                                                            @foreach($changes as $field => $change)
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="change-item p-2 border rounded">
                                                                        <small class="text-muted d-block mb-1">
                                                                            {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                                        </small>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="text-danger me-2">
                                                                                <small>Old:</small>
                                                                                <div class="fw-semibold">
                                                                                    {{ $change['old'] ?? 'N/A' }}
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-arrow-right text-muted mx-2"></i>
                                                                            <div class="text-success">
                                                                                <small>New:</small>
                                                                                <div class="fw-semibold">
                                                                                    {{ $change['new'] ?? 'N/A' }}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-muted mb-0">No detailed changes available</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Table View --}}
        <div id="tableViewContent" class="view-content d-none">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table" id="activityLogTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Testing ID</th>
                                    <th>Project</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Performed By</th>
                                    <th>Role</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activityLogs as $log)
                                    @php
                                        $log = (object) $log;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="small">{{ Carbon::parse($log->created_at ?? now())->format('d-m-Y') }}</div>
                                            <div class="text-muted smaller">{{ Carbon::parse($log->created_at ?? now())->format('h:i A') }}</div>
                                        </td>
                                        <td>
                                            @if($log->testing_ticket_id)
                                                <a href="{{ route('testing.show', $log->testing_ticket_id) }}" class="text-primary fw-bold">
                                                    {{ $log->testing_ticket_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->projectname ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $actionColors = [
                                                    'created' => 'success',
                                                    'updated' => 'primary',
                                                    'deleted' => 'danger',
                                                    'assigned' => 'info',
                                                    'status_changed' => 'warning',
                                                    'priority_changed' => 'secondary'
                                                ];
                                                $color = $actionColors[$log->action] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="log-description" style="max-width: 200px;">
                                                {{ $log->description ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @php
                                                        $initials = '';
                                                        if ($log->performed_by_name) {
                                                            $nameParts = explode(' ', $log->performed_by_name);
                                                            $initials = '';
                                                            foreach ($nameParts as $part) {
                                                                $initials .= strtoupper(substr($part, 0, 1));
                                                            }
                                                            $initials = substr($initials, 0, 2);
                                                        }
                                                    @endphp
                                                    <div class="avatar-sm" style="width: 30px; height: 30px; background-color: {{ ($log->performed_by_role ?? 'system') === 'admin' ? '#dc3545' : '#0d6efd' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                        {{ $initials ?: 'S' }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small">{{ $log->performed_by_name ?? 'System' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ ($log->performed_by_role ?? 'system') === 'admin' ? 'danger' : 'primary' }}">
                                                {{ ucfirst($log->performed_by_role ?? 'system') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->changes)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item" type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#changes-table-{{ $log->id }}">
                                                                <i class="fa fa-eye me-2"></i> View Changes
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item" type="button" 
                                                                    onclick="copyLogDetails({{ json_encode($log) }})">
                                                                <i class="fa fa-copy me-2"></i> Copy Details
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="collapse mt-2" id="changes-table-{{ $log->id }}">
                                                    <div class="card card-body">
                                                        @php
                                                            $changes = json_decode($log->changes, true);
                                                        @endphp
                                                        @if(is_array($changes))
                                                            <h6 class="mb-2">Changes:</h6>
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Field</th>
                                                                        <th>Old Value</th>
                                                                        <th>New Value</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($changes as $field => $change)
                                                                        <tr>
                                                                            <td class="fw-bold">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                                                            <td class="text-danger">
                                                                                @if(is_array($change['old'] ?? null))
                                                                                    {{ json_encode($change['old']) }}
                                                                                @else
                                                                                    {{ $change['old'] ?? 'N/A' }}
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-success">
                                                                                @if(is_array($change['new'] ?? null))
                                                                                    {{ json_encode($change['new']) }}
                                                                                @else
                                                                                    {{ $change['new'] ?? 'N/A' }}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <p class="text-muted mb-0">No detailed changes available</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">No details</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Activity Log Stats --}}
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="mb-3">Activity Summary</h6>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $actionCounts = [];
                        foreach ($activityLogs as $log) {
                            $log = (object) $log;
                            $action = $log->action ?? 'unknown';
                            $actionCounts[$action] = ($actionCounts[$action] ?? 0) + 1;
                        }
                    @endphp
                    @foreach($actionCounts as $action => $count)
                        @php
                            $actionColors = [
                                'created' => 'success',
                                'updated' => 'primary',
                                'deleted' => 'danger',
                                'assigned' => 'info',
                                'status_changed' => 'warning',
                                'priority_changed' => 'secondary'
                            ];
                            $color = $actionColors[$action] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} p-2">
                            {{ ucfirst(str_replace('_', ' ', $action)) }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="ticket-table">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                <div class="empty-state-title">No Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['log_action', 'log_performed_by', 'log_ticket_id', 'log_project_id', 'log_start_date', 'log_end_date', 'log_search']))
                        No activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetActivityLogFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No activity logs found yet.
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Activity Log Specific Styles */
    .log-description {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .smaller {
        font-size: 0.8rem;
    }
    
    .avatar-sm {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
    
    .avatar-title {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }
    
    .filter-pill {
        cursor: pointer;
    }
    
    .filter-pill .btn-close {
        padding: 0.3rem;
        font-size: 0.7rem;
    }
    
    /* View Toggle Styles */
    .btn-group .btn {
        padding: 0.375rem 0.75rem;
    }
    
    .btn-check:checked + .btn-outline-primary {
        background-color: #0f5c7e;
        border-color: #0f5c7e;
        color: white;
    }
    
    /* View Content */
    .view-content {
        transition: opacity 0.3s ease;
    }
    
    /* Timeline Styles for List View */
    .activity-log-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        padding-left: 50px;
    }

    .timeline-marker {
        position: absolute;
        left: 10px;
        top: 0;
        width: 24px;
        height: 24px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .timeline-marker i {
        font-size: 12px;
    }

    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 3px solid #0f5c7e;
    }

    .timeline-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .timeline-body {
        font-size: 0.9rem;
    }

    .change-item {
        transition: all 0.3s ease;
    }

    .change-item:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    /* Table Styles */
    .custom-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    .custom-table td {
        vertical-align: middle;
    }

    /* DataTables Customization */
    .dataTables_wrapper {
        margin-top: 10px;
    }
    
    .dataTables_length,
    .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .dataTables_filter input {
        margin-left: 5px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
    
    .dataTables_length select {
        margin: 0 5px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 15px;
        }

        .timeline-item {
            padding-left: 40px;
        }

        .timeline-marker {
            left: 5px;
            width: 20px;
            height: 20px;
        }

        .timeline-marker i {
            font-size: 10px;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            float: none;
            text-align: center;
        }
    }
</style>

<script>
// Global variable to store DataTable instance
let activityLogDataTable = null;

// Reset Activity Log Filters
function resetActivityLogFilters() {
    const form = document.getElementById('activityLogFilterForm');
    const inputs = form.querySelectorAll('select, input[type="text"], input[type="date"]');
    
    inputs.forEach(input => {
        if (input.type === 'text' || input.type === 'date') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    
    form.submit();
}

// Remove individual filter
function removeFilter(filterName) {
    const form = document.getElementById('activityLogFilterForm');
    const input = form.querySelector(`[name="${filterName}"]`);
    
    if (input) {
        if (input.type === 'text' || input.type === 'date') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    }
    
    form.submit();
}

// Remove date filters
function removeDateFilters() {
    const form = document.getElementById('activityLogFilterForm');
    const startDate = form.querySelector('#log_start_date');
    const endDate = form.querySelector('#log_end_date');
    
    if (startDate) startDate.value = '';
    if (endDate) endDate.value = '';
    
    form.submit();
}

// Copy Log Details to Clipboard
function copyLogDetails(log) {
    let textToCopy = `Activity Log Details:\n`;
    textToCopy += `Date: ${log.created_at}\n`;
    textToCopy += `Testing ID: ${log.testing_ticket_id || 'N/A'}\n`;
    textToCopy += `Project: ${log.projectname || 'N/A'}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Description: ${log.description}\n`;
    textToCopy += `Performed By: ${log.performed_by_name} (${log.performed_by_role})\n`;
    
    if (log.changes) {
        textToCopy += `\nChanges:\n`;
        const changes = JSON.parse(log.changes);
        Object.entries(changes).forEach(([field, change]) => {
            textToCopy += `${field}: ${change.old || 'N/A'} → ${change.new || 'N/A'}\n`;
        });
    }
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Log details copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        })
        .catch(err => {
            console.error('Failed to copy: ', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to copy details'
            });
        });
}

// Export Activity Logs
function exportActivityLogs() {
    Swal.fire({
        title: 'Export Activity Logs',
        text: 'Select export format:',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'CSV',
        cancelButtonText: 'Cancel',
        showDenyButton: true,
        
    }).then((result) => {
        if (result.isConfirmed) {
            exportToCSV();
        } else if (result.isDenied) {
            exportToPDF();
        }
    });
}

function exportToCSV() {
    const activeView = document.getElementById('tableViewContent').classList.contains('d-none') ? 'list' : 'table';
    
    if (activeView === 'table') {
        exportTableToCSV();
    } else {
        exportListViewToCSV();
    }
}

function exportTableToCSV() {
    const table = document.getElementById('activityLogTable');
    const rows = table.querySelectorAll('tr');
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        if (!th.querySelector('button')) { // Skip action column headers
            headers.push(`"${th.textContent.trim()}"`);
        }
    });
    csvContent += headers.join(',') + "\r\n";
    
    // Add data rows
    rows.forEach((row, index) => {
        if (index > 0) { // Skip header row
            const rowData = [];
            row.querySelectorAll('td').forEach((td, tdIndex) => {
                // Skip the action column (last td)
                if (tdIndex < row.cells.length - 1) {
                    let cellText = td.textContent.trim();
                    // Remove line breaks and extra spaces
                    cellText = cellText.replace(/\n/g, ' ').replace(/\s+/g, ' ');
                    rowData.push(`"${cellText}"`);
                }
            });
            csvContent += rowData.join(',') + "\r\n";
        }
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `testing_activity_logs_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportListViewToCSV() {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Date & Time,Testing ID,Project,Action,Description,Performed By,Role,Changes\r\n";
    
    const logs = @json($activityLogs);
    
    logs.forEach(log => {
        const row = [
            `"${log.created_at}"`,
            `"${log.testing_ticket_id || 'N/A'}"`,
            `"${log.projectname || 'N/A'}"`,
            `"${log.action}"`,
            `"${log.description}"`,
            `"${log.performed_by_name || 'System'}"`,
            `"${log.performed_by_role || 'system'}"`,
            `"${log.changes ? 'Yes' : 'No'}"`
        ];
        csvContent += row.join(',') + "\r\n";
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `testing_activity_logs_list_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    Swal.fire({
        title: 'Export PDF',
        text: 'PDF export feature is under development',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

// View Mode Switching
function switchViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('tableViewContent').classList.remove('d-none');
        document.getElementById('listViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeDataTable, 50);
    } else {
        document.getElementById('tableViewContent').classList.add('d-none');
        document.getElementById('listViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (activityLogDataTable !== null) {
            activityLogDataTable.destroy();
            activityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeDataTable() {
    // Destroy existing DataTable if it exists
    if (activityLogDataTable !== null) {
        activityLogDataTable.destroy();
        activityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('activityLogTable');
    if (table && $('#activityLogTable tbody tr').length > 0) {
        activityLogDataTable = $('#activityLogTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            order: [[0, 'desc']], // Sort by Date column descending
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            initComplete: function() {
                // Adjust DataTable after initialization
                this.api().columns.adjust();
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Set default view mode to List View
    switchViewMode('list');

    // Show filter section if there are active filters
    // Check if any filter parameters are present in the URL
    const hasActiveFilters = window.location.search.includes('log_');
    if (hasActiveFilters) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('activityLogFilters'), {
            toggle: true
        });
    }

    // Reinitialize DataTable when Bootstrap tab is shown (only for table view)
    const activityLogTab = document.getElementById('activity-log-tab');
    if (activityLogTab) {
        activityLogTab.addEventListener('shown.bs.tab', function() {
            // Only reinitialize if table view is currently active
            if (!document.getElementById('tableViewContent').classList.contains('d-none')) {
                setTimeout(initializeDataTable, 100);
            }
        });
    }

    // Auto-submit form when date inputs change
    const dateInputs = document.querySelectorAll('#log_start_date, #log_end_date');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('activityLogFilterForm').submit();
            }
        });
    });
});
// Handle window resize
window.addEventListener('resize', function() {
    if (activityLogDataTable !== null) {
        activityLogDataTable.columns.adjust();
    }
});
</script>
                </div>
            </div>
        </div>
    </div>

    {{-- Assignment Modal --}}
    @if($role === 'admin')
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">
                        <i class="fa fa-user-plus"></i> Assign Testing Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm">
                        <input type="hidden" id="assignTicketId" name="ticket_id">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            Only active employees are available for assignment.
                        </div>
                        <div class="mb-3">
                            <label for="modal_assigned_to" class="form-label">Assign To</label>
                            <select id="modal_assigned_to" name="assigned_to" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAssignment()">
                        <i class="fa fa-save"></i> Save Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Offcanvas (Right Side) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="fa fa-filter"></i> Filter Testing Tickets
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('testing.index') }}" id="filterForm">
                <div class="mb-3">
                    <label for="ticket_subject" class="form-label">Search Description</label>
                    <input type="text" class="form-control" id="ticket_subject" name="ticket_subject" 
                           value="{{ request('ticket_subject') }}" placeholder="Search by description...">
                </div>

                <div class="mb-3">
                    <label for="project_id" class="form-label">Project</label>
                    <select class="form-select" id="project_id" name="project_id">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->projectname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                        <option value="Reopen" {{ request('status') == 'Reopen' ? 'selected' : '' }}>Reopen</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priority</option>
                        <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

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

        .ticket-table .empty-state {
            text-align: center;
            padding: 60px 20px;
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
    </style>

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
        });

        function resetFilters() {
            window.location.href = "{{ route('testing.index') }}";
        }

        @if($role === 'admin')
        function updateStatus(ticketId, status) {
            Swal.fire({
                title: 'Update Status',
                text: `Are you sure you want to change the status to "${status}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("testing.updateStatus", ":id") }}'.replace(':id', ticketId),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                const statusButton = $(`#statusDropdown${ticketId}`);
                                let statusIcon = '';
                                let statusText = status.charAt(0).toUpperCase() + status.slice(1);
                                
                                if(status === 'Open') {
                                    statusIcon = '<i class="fa fa-circle text-primary"></i>';
                                } else if(status === 'In Progress') {
                                    statusIcon = '<i class="fa fa-circle text-warning"></i>';
                                } else if(status === 'Resolved') {
                                    statusIcon = '<i class="fa fa-circle text-info"></i>';
                                } else if(status === 'Closed') {
                                    statusIcon = '<i class="fa fa-circle text-success"></i>';
                                } else if(status === 'Reopen') {
                                    statusIcon = '<i class="fa fa-circle text-danger"></i>';
                                }
                                
                                statusButton.html(statusIcon + ' ' + statusText);

                                // Update tab counts
                                document.querySelector('#open-tickets-tab').textContent = `Open Testing (${response.newTicketsCount})`;
                                document.querySelector('#in-progress-tickets-tab').textContent = `In Progress (${response.openTicketsCount})`;
                                document.querySelector('#resolved-tickets-tab').textContent = `Resolved (${response.pendingTicketsCount})`;
                                document.querySelector('#closed-tickets-tab').textContent = `Closed (${response.solvedTicketsCount})`;
                                document.querySelector('#reopened-tickets-tab').textContent = `Reopened (${response.reopenTicketsCount})`;

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Testing ticket status updated successfully.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        }

        function updatePriority(ticketId, priority) {
            Swal.fire({
                title: 'Update Priority',
                text: `Are you sure you want to change the priority to "${priority}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("testing.updatePriority", ":id") }}'.replace(':id', ticketId),
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            priority: priority
                        },
                        success: function(response) {
                            if (response.success) {
                                const priorityButton = $(`#priorityDropdown${ticketId}`);
                                const priorityColor = priority === 'High' ? 'danger' : (priority === 'Medium' ? 'warning' : 'success');
                                priorityButton.html(`<i class="fa fa-circle text-${priorityColor}"></i> ${priority}`);
                                
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Testing ticket priority updated successfully.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        }

        function showAssignModal(ticketId) {
            $('#assignTicketId').val(ticketId);
            $('#assignModal').modal('show');
        }

        function saveAssignment() {
            const ticketId = $('#assignTicketId').val();
            const assignedTo = $('#modal_assigned_to').val();

            $.ajax({
                url: '{{ route("testing.updateAssignment", ":id") }}'.replace(':id', ticketId),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_to: assignedTo
                },
                success: function(response) {
                    if (response.success) {
                        $('#assignModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Assignment updated successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                }
            });
        }

        function setDeleteTicket(element) {
            const ticketId = element.getAttribute('data-ticket-id');
            const ticketSubject = element.getAttribute('data-ticket-subject');

            Swal.fire({
                title: 'Delete Testing Ticket',
                html: `Are you sure you want to delete the testing ticket:<br><strong>${ticketSubject}</strong>?`,
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
                    form.action = '{{ route("testing.destroy", ":id") }}'.replace(':id', ticketId);
                    
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
    </script>
@endsection