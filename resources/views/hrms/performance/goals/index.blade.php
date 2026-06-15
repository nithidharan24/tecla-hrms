@php
    use Carbon\Carbon;
    $employeeId = Session::get('user_id');
    $role = Session::get('role');
    $permissions = App\Helpers\PermissionHelper::getPermissions('Goals');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    
    <!-- Success/Error Messages -->
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
            <i class="fa fa-exclamation-circle"></i> 
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                    <button class="nav-link active" id="all-goals-tab" data-bs-toggle="tab"
                            data-bs-target="#all-goals" type="button" role="tab">
                        All Goals ({{ $goals->count() ?? 0 }})
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-goals-tab" data-bs-toggle="tab"
                            data-bs-target="#active-goals" type="button" role="tab">
                        Active ({{ $activeCount ?? 0 }})
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-goals-tab" data-bs-toggle="tab"
                            data-bs-target="#completed-goals" type="button" role="tab">
                        Completed ({{ $completedCount ?? 0 }})
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="overdue-goals-tab" data-bs-toggle="tab"
                            data-bs-target="#overdue-goals" type="button" role="tab">
                        Overdue ({{ $overdueCount ?? 0 }})
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="high-priority-tab" data-bs-toggle="tab"
                            data-bs-target="#high-priority" type="button" role="tab">
                        High Priority ({{ $highPriorityCount ?? 0 }})
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="page-heade2r goals-header">
        <div class="row align-items-center">
            <div class="col">
                <!-- Optional header content -->
            </div>
            <div class="col-auto float-end ms-auto d-flex">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('goals.create') }}" class="btn add-btn me-2">
                    <i class="fa fa-plus"></i> Add Goal
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

                {{-- All Goals Tab --}}
                <div class="tab-pane fade show active" id="all-goals" role="tabpanel">
                    @if($goals->count() > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Goal Title</th>
                                                <th>Type</th>
                                                <th>Assigned To</th>
                                                <th>Department</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($goals as $index => $goal)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('goals.show', $goal->id) }}" class="text-primary fw-bold">
                                                            {{ $goal->goal_title ?? 'N/A' }}
                                                        </a>
                                                        @if($goal->goal_description)
                                                        <small class="d-block text-muted">{{ Str::limit($goal->goal_description, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $goal->goal_type ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $goal->assigned_to_name ?? 'N/A' }}</strong>
                                                        @if($goal->assigned_by_name)
                                                        <small class="d-block text-muted">Assigned by: {{ $goal->assigned_by_name }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $goal->department_name ?? 'N/A' }}</td>
                                                    <td>{{ Carbon::parse($goal->start_date ?? now())->format('d-m-Y') }}</td>
                                                    <td>
                                                        {{ Carbon::parse($goal->end_date ?? now())->format('d-m-Y') }}
                                                        @php
                                                            $today = Carbon::now();
                                                            $endDate = Carbon::parse($goal->end_date);
                                                            $daysRemaining = $today->diffInDays($endDate, false);
                                                        @endphp
                                                        <br>
                                                        <small class="{{ $daysRemaining < 0 ? 'text-danger' : ($daysRemaining < 7 ? 'text-warning' : 'text-success') }}">
                                                            {{ $daysRemaining >= 0 ? $daysRemaining . ' days left' : abs($daysRemaining) . ' days overdue' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $progressPercent = $goal->progress_percentage ?? 0;
                                                            $progressClass = $progressPercent >= 80 ? 'bg-success' : ($progressPercent >= 50 ? 'bg-warning' : 'bg-danger');
                                                        @endphp
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                                 style="width: {{ $progressPercent }}%;" 
                                                                 aria-valuenow="{{ $progressPercent }}" 
                                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $progressPercent }}%</small>
                                                        <div class="small text-muted">
                                                            {{ $goal->current_value ?? 0 }} / {{ $goal->target_value ?? 0 }} {{ $goal->unit ?? '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'Not Started' => 'secondary',
                                                                'In Progress' => 'primary',
                                                                'On Hold' => 'warning',
                                                                'Completed' => 'success',
                                                                'Cancelled' => 'danger'
                                                            ];
                                                            $statusColor = $statusColors[$goal->status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColor }}">
                                                            {{ $goal->status ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $priorityColors = [
                                                                'Low' => 'info',
                                                                'Medium' => 'warning',
                                                                'High' => 'danger',
                                                                'Critical' => 'dark'
                                                            ];
                                                            $priorityColor = $priorityColors[$goal->priority] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $priorityColor }}">
                                                            {{ $goal->priority ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" href="{{ route('goals.show', $goal->id) }}">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                @if(isset($permissions) && $permissions->can_edit)
                                                                <a class="dropdown-item" href="{{ route('goals.edit', $goal->id) }}">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </a>
                                                                @endif
                                                                <a class="dropdown-item update-progress-btn" href="#" 
                                                                   data-id="{{ $goal->id }}" 
                                                                   data-current="{{ $goal->current_value ?? 0 }}"
                                                                   data-target="{{ $goal->target_value ?? 0 }}"
                                                                   onclick="showUpdateProgressModal(this)">
                                                                    <i class="fa fa-refresh"></i> Update Progress
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                @if(isset($permissions) && $permissions->can_delete)
                                                                <a class="dropdown-item text-danger delete-btn" href="#" 
                                                                   data-id="{{ $goal->id }}" 
                                                                   data-title="{{ $goal->goal_title ?? 'N/A' }}"
                                                                   onclick="setDeleteGoal(this)">
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
                                <div class="empty-state-icon"><i class="fas fa-bullseye"></i></div>
                                <div class="empty-state-title">No Goals Found</div>
                                <div class="empty-state-text">No goals found with current filters.</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Active Goals Tab --}}
                <div class="tab-pane fade" id="active-goals" role="tabpanel">
                    @php
                        $activeGoals = $goals->whereIn('status', ['Not Started', 'In Progress'])->values();
                    @endphp

                    @if($activeGoals->count() > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Goal Title</th>
                                                <th>Assigned To</th>
                                                <th>End Date</th>
                                                <th>Progress</th>
                                                <th>Priority</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeGoals as $index => $goal)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('goals.show', $goal->id) }}" class="text-primary fw-bold">
                                                            {{ $goal->goal_title ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $goal->assigned_to_name ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ Carbon::parse($goal->end_date)->format('d-m-Y') }}
                                                        @php
                                                            $today = Carbon::now();
                                                            $endDate = Carbon::parse($goal->end_date);
                                                            $daysRemaining = $today->diffInDays($endDate, false);
                                                        @endphp
                                                        <br>
                                                        <small class="{{ $daysRemaining < 0 ? 'text-danger' : ($daysRemaining < 7 ? 'text-warning' : 'text-success') }}">
                                                            {{ $daysRemaining >= 0 ? $daysRemaining . ' days left' : abs($daysRemaining) . ' days overdue' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $progressPercent = $goal->progress_percentage ?? 0;
                                                            $progressClass = $progressPercent >= 80 ? 'bg-success' : ($progressPercent >= 50 ? 'bg-warning' : 'bg-danger');
                                                        @endphp
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                                 style="width: {{ $progressPercent }}%;"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $progressPercent }}%</small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $priorityColors = [
                                                                'Low' => 'info',
                                                                'Medium' => 'warning',
                                                                'High' => 'danger',
                                                                'Critical' => 'dark'
                                                            ];
                                                            $priorityColor = $priorityColors[$goal->priority] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $priorityColor }}">
                                                            {{ $goal->priority ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item update-progress-btn" href="#" 
                                                                   data-id="{{ $goal->id }}"
                                                                   onclick="showUpdateProgressModal(this)">
                                                                    <i class="fa fa-refresh"></i> Update Progress
                                                                </a>
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
                                <div class="empty-state-icon"><i class="fas fa-bullseye"></i></div>
                                <div class="empty-state-title">No Active Goals</div>
                                <div class="empty-state-text">All goals are either completed or cancelled.</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Completed Goals Tab --}}
                <div class="tab-pane fade" id="completed-goals" role="tabpanel">
                    @php
                        $completedGoals = $goals->where('status', 'Completed')->values();
                    @endphp

                    @if($completedGoals->count() > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Goal Title</th>
                                                <th>Assigned To</th>
                                                <th>Completion Date</th>
                                                <th>Progress</th>
                                                <th>Rating</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($completedGoals as $index => $goal)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('goals.show', $goal->id) }}" class="text-primary fw-bold">
                                                            {{ $goal->goal_title ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $goal->assigned_to_name ?? 'N/A' }}</td>
                                                    <td>{{ Carbon::parse($goal->updated_at)->format('d-m-Y') }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" role="progressbar" 
                                                                 style="width: {{ $goal->progress_percentage ?? 100 }}%;"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $goal->progress_percentage ?? 100 }}%</small>
                                                    </td>
                                                    <td>
                                                        @if($goal->overall_rating)
                                                            <div class="rating">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $goal->overall_rating)
                                                                        <i class="fa fa-star text-warning"></i>
                                                                    @else
                                                                        <i class="fa fa-star text-muted"></i>
                                                                    @endif
                                                                @endfor
                                                                <small class="text-muted">({{ number_format($goal->overall_rating, 1) }}/5)</small>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-secondary">Not Rated</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" href="{{ route('goals.show', $goal->id) }}">
                                                                    <i class="fa fa-eye"></i> View Details
                                                                </a>
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
                                <div class="empty-state-icon"><i class="fas fa-bullseye"></i></div>
                                <div class="empty-state-title">No Completed Goals</div>
                                <div class="empty-state-text">No goals have been completed yet.</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Overdue Goals Tab --}}
                <div class="tab-pane fade" id="overdue-goals" role="tabpanel">
                    @php
                        $overdueGoals = $goals->filter(function($goal) {
                            $endDate = Carbon::parse($goal->end_date);
                            $today = Carbon::now();
                            return $endDate->lt($today) && !in_array($goal->status, ['Completed', 'Cancelled']);
                        })->values();
                    @endphp

                    @if($overdueGoals->count() > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Goal Title</th>
                                                <th>Assigned To</th>
                                                <th>Due Date</th>
                                                <th>Days Overdue</th>
                                                <th>Progress</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($overdueGoals as $index => $goal)
                                                @php
                                                    $endDate = Carbon::parse($goal->end_date);
                                                    $today = Carbon::now();
                                                    $daysOverdue = $today->diffInDays($endDate);
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('goals.show', $goal->id) }}" class="text-primary fw-bold">
                                                            {{ $goal->goal_title ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $goal->assigned_to_name ?? 'N/A' }}</td>
                                                    <td>{{ Carbon::parse($goal->end_date)->format('d-m-Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-danger">{{ $daysOverdue }} days</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $progressPercent = $goal->progress_percentage ?? 0;
                                                            $progressClass = $progressPercent >= 80 ? 'bg-success' : ($progressPercent >= 50 ? 'bg-warning' : 'bg-danger');
                                                        @endphp
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                                 style="width: {{ $progressPercent }}%;"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $progressPercent }}%</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" href="{{ route('goals.edit', $goal->id) }}">
                                                                    <i class="fa fa-edit"></i> Extend Deadline
                                                                </a>
                                                                <a class="dropdown-item update-progress-btn" href="#" 
                                                                   data-id="{{ $goal->id }}"
                                                                   onclick="showUpdateProgressModal(this)">
                                                                    <i class="fa fa-refresh"></i> Update Progress
                                                                </a>
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
                                <div class="empty-state-icon"><i class="fas fa-bullseye"></i></div>
                                <div class="empty-state-title">No Overdue Goals</div>
                                <div class="empty-state-text">Great! All goals are on schedule.</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- High Priority Tab --}}
                <div class="tab-pane fade" id="high-priority" role="tabpanel">
                    @php
                        $highPriorityGoals = $goals->whereIn('priority', ['High', 'Critical'])->values();
                    @endphp

                    @if($highPriorityGoals->count() > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Goal Title</th>
                                                <th>Assigned To</th>
                                                <th>End Date</th>
                                                <th>Priority</th>
                                                <th>Progress</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($highPriorityGoals as $index => $goal)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('goals.show', $goal->id) }}" class="text-primary fw-bold">
                                                            {{ $goal->goal_title ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $goal->assigned_to_name ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ Carbon::parse($goal->end_date)->format('d-m-Y') }}
                                                        @php
                                                            $today = Carbon::now();
                                                            $endDate = Carbon::parse($goal->end_date);
                                                            $daysRemaining = $today->diffInDays($endDate, false);
                                                        @endphp
                                                        <br>
                                                        <small class="{{ $daysRemaining < 0 ? 'text-danger' : ($daysRemaining < 7 ? 'text-warning' : 'text-success') }}">
                                                            {{ $daysRemaining >= 0 ? $daysRemaining . ' days left' : abs($daysRemaining) . ' days overdue' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $priorityColors = [
                                                                'High' => 'danger',
                                                                'Critical' => 'dark'
                                                            ];
                                                            $priorityColor = $priorityColors[$goal->priority] ?? 'danger';
                                                        @endphp
                                                        <span class="badge bg-{{ $priorityColor }}">
                                                            <i class="fa fa-exclamation-circle"></i> {{ $goal->priority }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $progressPercent = $goal->progress_percentage ?? 0;
                                                            $progressClass = $progressPercent >= 80 ? 'bg-success' : ($progressPercent >= 50 ? 'bg-warning' : 'bg-danger');
                                                        @endphp
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                                                 style="width: {{ $progressPercent }}%;"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $progressPercent }}%</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item update-progress-btn" href="#" 
                                                                   data-id="{{ $goal->id }}"
                                                                   onclick="showUpdateProgressModal(this)">
                                                                    <i class="fa fa-refresh"></i> Update Progress
                                                                </a>
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
                                <div class="empty-state-icon"><i class="fas fa-bullseye"></i></div>
                                <div class="empty-state-title">No High Priority Goals</div>
                                <div class="empty-state-text">No goals are marked as high priority.</div>
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
            <i class="fa fa-filter"></i> Filter Goals
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('goals.index') }}" id="goalFilterForm">
            <div class="mb-3">
                <label for="goal_title" class="form-label">Search Goal Title</label>
                <input type="text" class="form-control" id="goal_title" name="goal_title" 
                       value="{{ request('goal_title') }}" placeholder="Search by goal title...">
            </div>

            <div class="mb-3">
                <label for="goal_type" class="form-label">Goal Type</label>
                <select class="form-select" id="goal_type" name="goal_type">
                    <option value="">All Types</option>
                    <option value="Company" {{ request('goal_type') == 'Company' ? 'selected' : '' }}>Company</option>
                    <option value="Department" {{ request('goal_type') == 'Department' ? 'selected' : '' }}>Department</option>
                    <option value="Team" {{ request('goal_type') == 'Team' ? 'selected' : '' }}>Team</option>
                    <option value="Individual" {{ request('goal_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="Not Started" {{ request('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                <label for="assigned_to" class="form-label">Assigned To</label>
                <select class="form-select" id="assigned_to" name="assigned_to">
                    <option value="">All Employees</option>
                    @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->firstname }} {{ $employee->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="from_date" class="form-label">Start From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" 
                       value="{{ request('from_date') }}">
            </div>

            <div class="mb-3">
                <label for="to_date" class="form-label">Start To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" 
                       value="{{ request('to_date') }}">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" onclick="resetGoalFilters()">
                    <i class="fa fa-refresh"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-labelledby="updateProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProgressModalLabel">
                    <i class="fa fa-refresh"></i> Update Goal Progress
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProgressForm">
                    @csrf
                    <input type="hidden" id="goal_id" name="goal_id">
                    <div class="mb-3">
                        <label for="current_value" class="form-label">Current Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="current_value" name="current_value" required>
                        <small class="form-text text-muted">Target Value: <span id="target_value_display">0</span></small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3" id="progress_preview" style="display: none;">
                        <label class="form-label">Progress Preview</label>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" id="progress_bar_preview" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <small class="text-muted" id="progress_percentage_preview">0%</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-update" class="btn btn-primary">
                    <i class="fa fa-refresh me-1"></i> Update Progress
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-report-tabs {
        display: flex;
        gap: 20px;
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
        font-size: 14px;
        color: #555;
        padding: 8px 0;
        position: relative;
        border-radius: 0 !important;
        font-weight: 500;
        white-space: nowrap;
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

    .progress {
        width: 100px;
        margin-bottom: 5px;
    }

    .rating i {
        font-size: 12px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        if ($.fn.dataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable({
                "pageLength": 25,
                "order": [[0, 'desc']]
            });
        }

        // Calculate counts for tabs
        calculateTabCounts();
    });

    function calculateTabCounts() {
        // These would ideally come from the controller
        // For now, we'll calculate from the DOM
        const allGoals = {{ $goals->count() ?? 0 }};
        const activeGoals = document.querySelectorAll('#all-goals tbody tr').length;
        const completedGoals = document.querySelectorAll('#all-goals tbody tr .badge.bg-success').length;
        
        // Update tab counts
        document.querySelector('#all-goals-tab').textContent = `All Goals (${allGoals})`;
        document.querySelector('#active-goals-tab').textContent = `Active (${activeGoals})`;
        document.querySelector('#completed-goals-tab').textContent = `Completed (${completedGoals})`;
    }

    function resetGoalFilters() {
        window.location.href = "{{ route('goals.index') }}";
    }

    let currentGoalId = null;
    let targetValue = 0;

    function showUpdateProgressModal(element) {
        currentGoalId = element.getAttribute('data-id');
        const currentValue = element.getAttribute('data-current') || 0;
        targetValue = element.getAttribute('data-target') || 0;

        $('#goal_id').val(currentGoalId);
        $('#current_value').val(currentValue);
        $('#target_value_display').text(targetValue);
        
        // Calculate initial progress
        calculateProgress(currentValue);
        
        $('#updateProgressModal').modal('show');
    }

    // Calculate progress when current value changes
    $('#current_value').on('input', function() {
        calculateProgress($(this).val());
    });

    function calculateProgress(currentValue) {
        if (targetValue > 0) {
            const progressPercent = Math.min(100, (currentValue / targetValue) * 100);
            $('#progress_bar_preview').css('width', progressPercent + '%');
            $('#progress_percentage_preview').text(progressPercent.toFixed(1) + '%');
            
            // Change color based on progress
            if (progressPercent >= 80) {
                $('#progress_bar_preview').removeClass('bg-warning bg-danger').addClass('bg-success');
            } else if (progressPercent >= 50) {
                $('#progress_bar_preview').removeClass('bg-success bg-danger').addClass('bg-warning');
            } else {
                $('#progress_bar_preview').removeClass('bg-success bg-warning').addClass('bg-danger');
            }
            
            $('#progress_preview').show();
        }
    }

    $('#confirm-update').click(function() {
        if (!currentGoalId) return;
        
        const formData = $('#updateProgressForm').serialize();
        
        $.ajax({
            url: "{{ url('performance/goals') }}/" + currentGoalId + "/update-progress",
            method: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
            },
            success: function(response) {
                $('#updateProgressModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Progress updated successfully!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(response) {
                $('#updateProgressModal').modal('hide');
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update progress.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    function setDeleteGoal(element) {
        const goalId = element.getAttribute('data-id');
        const goalTitle = element.getAttribute('data-title');

        Swal.fire({
            title: 'Delete Goal',
            html: `Are you sure you want to delete the goal:<br><strong>${goalTitle}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteGoal(goalId);
            }
        });
    }

    function deleteGoal(goalId) {
        $.ajax({
            url: "{{ url('performance/goals') }}/" + goalId,
            method: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Goal has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(response) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete goal.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
@endsection