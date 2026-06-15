@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Estimates');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
  <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('estimate.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Create Estimate
                </a>
                @endif
            </div>

    <!-- Success/Error Messages -->
    @if(Session::has('messageType') && Session::has('message'))
        @if(Session::get('messageType') === 'success')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ Session::get('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @elseif(Session::get('messageType') === 'error')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ Session::get('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    @endif

    <!-- =====================================================
         ZOHO STYLE TABS
    ====================================================== -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#summary-tab">
                <i class="fa fa-chart-bar me-1"></i>Estimates Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $estimates->count() }}</span>
            </a>
        </li>
            <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#activity-tab">Activity Log</a>
    </li>
    </ul>

    <!-- =====================================================
         TAB CONTENT AREA
    ====================================================== -->
    <div class="tab-content pt-4">

        <!-- =====================================================
             TAB 1 : SUMMARY
        ====================================================== -->
        <div class="tab-pane fade show active" id="summary-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-group m-b-30">
                        <!-- Total Estimates Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-file-invoice text-primary"></i> Total Estimates
                                    </span>
                                    <span class="text-success">{{ $estimates->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $estimates->total() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All estimates in system</small>
                            </div>
                        </div>

                        <!-- Total Amount Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-rupee-sign text-success"></i> Total Amount
                                    </span>
                                    <span class="text-success">₹{{ number_format($totalAmount ?? 0, 0) }}</span>
                                </div>
                                <h3 class="mb-3 text-success">₹{{ number_format($totalAmount ?? 0, 0) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Total estimate value</small>
                            </div>
                        </div>

                        <!-- Status Distribution Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-chart-pie text-warning"></i> Status Overview
                                    </span>
                                    <span class="text-warning">Distribution</span>
                                </div>
                                <div class="mb-2">
                                    @php
                                        $statusCounts = [
                                            'sent' => 0,
                                            'accepted' => 0,
                                            'declined' => 0,
                                            'expired' => 0
                                        ];
                                        foreach($estimates as $est) {
                                            if(isset($statusCounts[$est->status])) {
                                                $statusCounts[$est->status]++;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Sent: {{ $statusCounts['sent'] }}</small>
                                        <small class="text-primary">{{ round(($statusCounts['sent'] / max(1, $estimates->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Accepted: {{ $statusCounts['accepted'] }}</small>
                                        <small class="text-success">{{ round(($statusCounts['accepted'] / max(1, $estimates->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small>Others: {{ $statusCounts['declined'] + $statusCounts['expired'] }}</small>
                                        <small class="text-danger">{{ round((($statusCounts['declined'] + $statusCounts['expired']) / max(1, $estimates->count())) * 100) }}%</small>
                                    </div>
                                </div>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-warning" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Estimate status distribution</small>
                            </div>
                        </div>

                        <!-- Recent Estimates Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-clock text-info"></i> Recent Estimates
                                    </span>
                                    <span class="text-info">This Month</span>
                                </div>
                                @php
                                    $thisMonthCount = $estimates->filter(function($est) {
                                        return \Carbon\Carbon::parse($est->estimate_date)->isCurrentMonth();
                                    })->count();
                                @endphp
                                <h3 class="mb-3 text-info">{{ $thisMonthCount }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Estimates created this month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 1 -->

        <!-- =====================================================
             TAB 2 : LIST WITH FILTER
        ====================================================== -->
        <div class="tab-pane fade" id="list-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Top Bar with Filter Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top:-10px;">
                                <h4 class="fw-bold mb-0">All Estimates</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openEstimateFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="estimateFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Estimates</h5>
                                    <button id="closeEstimateFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('estimate.index') }}" id="filterForm">
                                    @if(request()->has('from_date') || request()->has('to_date') || request()->has('status'))
                                    <div class="mb-3">
                                        <div class="alert alert-info py-2">
                                            <small><i class="fa fa-info-circle me-1"></i> Active filters applied</small>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Client Name</label>
                                        <input type="text" name="client_name" value="{{ request('client_name') }}" 
                                               class="form-control" placeholder="Search by client name...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">From Date</label>
                                        <input type="date" name="from_date" value="{{ request('from_date') }}" 
                                               class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">To Date</label>
                                        <input type="date" name="to_date" value="{{ request('to_date') }}" 
                                               class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search me-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-light border" onclick="resetFilters()">
                                            <i class="fa fa-refresh me-1"></i> Reset Filters
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Active Filters Display -->
                            @if(request()->has('from_date') || request()->has('to_date') || request()->has('status') || request()->has('client_name'))
                            <div class="alert alert-light border mb-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted me-2">Active Filters:</small>
                                        @if(request('client_name'))
                                        <span class="badge bg-info me-2">
                                            Client: "{{ request('client_name') }}"
                                            <a href="?{{ http_build_query(request()->except('client_name')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('from_date'))
                                        <span class="badge bg-info me-2">
                                            From: {{ request('from_date') }}
                                            <a href="?{{ http_build_query(request()->except('from_date')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('to_date'))
                                        <span class="badge bg-info me-2">
                                            To: {{ request('to_date') }}
                                            <a href="?{{ http_build_query(request()->except('to_date')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('status'))
                                        <span class="badge bg-info me-2">
                                            Status: {{ ucfirst(request('status')) }}
                                            <a href="?{{ http_build_query(request()->except('status')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('estimate.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Estimates Table -->
                            <div class="table-responsive">
                                <table class="table custom-table datatable mb-0">
                                    <thead>
                                        <tr>
                                            <th>Estimate ID</th>
                                            <th>Client</th>
                                            <th>Estimate Date</th>
                                            <th>Expiry Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                <th class="text-end">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($estimates as $est)
                                            <tr>
                                                <td data-label="Estimate ID">
                                                        {{ $est->estimate_id }}
                                                </td>
                                                <td data-label="Client Name">
                                                    <span class="od-chip-highlight">{{ ucfirst($est->client_name) }}</span>
                                                </td>
                                                <td data-label="Estimate Date">{{ \Carbon\Carbon::parse($est->estimate_date)->format('d M Y') }}</td>
                                                <td data-label="Expiry Date">{{ \Carbon\Carbon::parse($est->expiry_date)->format('d M Y') }}</td>
                                                <td data-label="Amount">₹{{ number_format($est->grant_amt, 0) }}</td>
                                                <td data-label="Status">
                                                    <div class="dropdown action-label">
                                                        <button class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-regular fa-circle-dot
                                                                {{ $est->status == 'sent' ? 'text-primary' :
                                                                    ($est->status == 'expired' ? 'text-warning' :
                                                                    ($est->status == 'accepted' ? 'text-success' : 'text-danger')) }}">
                                                            </i>
                                                            {{ ucfirst($est->status) }}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <button class="dropdown-item {{ $est->status == 'sent' ? 'disabled' : '' }}"
                                                                onclick="statusChange('{{ $est->estimate_id }}', 'sent')">
                                                                <i class="fa-regular fa-circle-dot text-primary"></i> Sent
                                                            </button>
                                                            <button class="dropdown-item {{ $est->status == 'expired' ? 'disabled' : '' }}"
                                                                onclick="statusChange('{{ $est->estimate_id }}', 'expired')">
                                                                <i class="fa-regular fa-circle-dot text-warning"></i> Expired
                                                            </button>
                                                            <button class="dropdown-item {{ $est->status == 'accepted' ? 'disabled' : '' }}"
                                                                onclick="statusChange('{{ $est->estimate_id }}', 'accepted')">
                                                                <i class="fa-regular fa-circle-dot text-success"></i> Accepted
                                                            </button>
                                                            <button class="dropdown-item {{ $est->status == 'declined' ? 'disabled' : '' }}"
                                                                onclick="statusChange('{{ $est->estimate_id }}', 'declined')">
                                                                <i class="fa-regular fa-circle-dot text-danger"></i> Declined
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                <td data-label="Actions" class="text-end">
                                                    <div class="od-inline-actions">
                                                        @if($permissions->can_edit)
                                                        <a href="{{ route('estimate.edit', $est->estimate_id) }}" class="od-icon-btn" title="Edit">
                                                            <i class="fa-solid fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                
                                                        <a href="{{ route('estimate.pdf', $est->estimate_id) }}" target="_blank" class="od-icon-btn" title="Download PDF">
                                                            <i class="fa-solid fa-file-pdf"></i>
                                                        </a>
                                                
                                                        @if($permissions->can_delete)
                                                        <button type="button" class="od-icon-btn danger" title="Delete"
                                                            onclick="deleteData('{{ $est->estimate_id }}')">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                        @endif
                                                
                                                        <button type="button" class="od-icon-btn" title="Send Email"
                                                            onclick="sendMail('{{ $est->estimate_id }}')">
                                                            <i class="fa-regular fa-envelope"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ isset($permissions) && ($permissions->can_edit || $permissions->can_delete) ? '7' : '6' }}" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="fa fa-file-invoice fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No Estimates Found</h5>
                                                        <p class="text-muted">
                                                            @if(request()->has('from_date') || request()->has('to_date') || request()->has('status') || request()->has('client_name'))
                                                            No estimates found matching your filter criteria.
                                                            @else
                                                            No estimates have been created yet.
                                                            @endif
                                                        </p>
                                                        @if(isset($permissions) && $permissions->can_create)
                                                        <a href="{{ route('estimate.create') }}" class="btn btn-primary">
                                                            <i class="fa fa-plus me-2"></i> Create Estimate
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($estimates->hasPages())
                            <div class="mt-3">
                                {{ $estimates->appends(request()->query())->links() }}
                            </div>
                            @endif

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $estimates->firstItem() ?? 0 }}-{{ $estimates->lastItem() ?? 0 }} of {{ $estimates->total() }} estimates
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 2 -->

      <!-- =====================================================
             TAB 3 : activity log
====================================================== -->
<div class="tab-pane fade" id="activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Estimate Activity Logs</h5>
            <div class="d-flex gap-2">
               
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="estimateViewMode" id="estimateListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="estimateListView" onclick="switchEstimateViewMode('list')">
                        <i class="fas fa-list"></i> 
                    </label>
                    <input type="radio" class="btn-check" name="estimateViewMode" id="estimateTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="estimateTableView" onclick="switchEstimateViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['estimate_action', 'estimate_client', 'estimate_status', 'estimate_date']))
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-light text-dark border">Active Filters:</span>
                @if(request('estimate_action'))
                    <span class="badge bg-info">
                        Action: {{ ucfirst(request('estimate_action')) }}
                        <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                                onclick="removeEstimateFilter('estimate_action')"></button>
                    </span>
                @endif
                @if(request('estimate_client'))
                    <span class="badge bg-info">
                        Client: {{ request('estimate_client') }}
                        <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                                onclick="removeEstimateFilter('estimate_client')"></button>
                    </span>
                @endif
                @if(request('estimate_status'))
                    <span class="badge bg-info">
                        Status: {{ ucfirst(request('estimate_status')) }}
                        <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                                onclick="removeEstimateFilter('estimate_status')"></button>
                    </span>
                @endif
                @if(request('estimate_date'))
                    <span class="badge bg-info">
                        Date: {{ request('estimate_date') }}
                        <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                                onclick="removeEstimateFilter('estimate_date')"></button>
                    </span>
                @endif
            </div>
        </div>
        @endif

      

        @if($logs && count($logs) > 0)
            <!-- List View -->
            <div id="estimateListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @forelse ($logs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    @php
                                        $actionIcons = [
                                            'created' => 'fa-plus-circle text-success',
                                            'updated' => 'fa-edit text-primary',
                                            'status_changed' => 'fa-exchange-alt text-warning',
                                            'deleted' => 'fa-trash text-danger',
                                            'pdf_downloaded' => 'fa-file-pdf text-danger',
                                            'csv_downloaded' => 'fa-file-csv text-success',
                                            'email_sent' => 'fa-envelope text-secondary'
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'fa-history text-dark';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        {{ $log->estimate_id }}
                                                    </a>
                                                    <span class="ms-2">- {{ $log->client_name ?? 'N/A' }}</span>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-money-bill me-1"></i>
                                                        {{ $log->amount ?? '0.00' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                @php
                                                    $cls = [
                                                        'created' => 'bg-success',
                                                        'updated' => 'bg-primary',
                                                        'status_changed' => 'bg-warning',
                                                        'deleted' => 'bg-danger',
                                                        'pdf_downloaded' => 'bg-info',
                                                        'csv_downloaded' => 'bg-info',
                                                        'email_sent' => 'bg-secondary'
                                                    ][$log->action] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $cls }}">
                                                    {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                                </span>
                                                @if($log->status)
                                                    <span class="badge bg-{{ $log->status == 'accepted' ? 'success' : ($log->status == 'declined' ? 'danger' : 'warning') }} mt-1">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title" style="background-color: #6c757d; color: white;">
                                                    {{ substr($log->client_name ?? 'C', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-2"><strong>Details:</strong> {{ $log->details }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i> {{ $log->performed_by ?? 'System' }}
                                                        </small>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyEstimateDetails({{ json_encode($log) }})">
                                                <i class="fas fa-copy"></i> Copy Details
                                            </button>
                                            @if($log->action == 'pdf_downloaded' || $log->action == 'email_sent')
                                                <button class="btn btn-sm btn-outline-success" onclick="resendEstimate({{ $log->id }})">
                                                    <i class="fas fa-paper-plane"></i> Resend
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No activity logs found</h5>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="estimateTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="estimateActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Estimate ID</th>
                                <th>Action</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Details</th>
                                <th>Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $log->estimate_id }}</strong></td>
                                <td>
                                    @php
                                        $cls = [
                                            'created' => 'bg-success',
                                            'updated' => 'bg-primary',
                                            'status_changed' => 'bg-warning',
                                            'deleted' => 'bg-danger',
                                            'pdf_downloaded' => 'bg-info',
                                            'csv_downloaded' => 'bg-info',
                                            'email_sent' => 'bg-secondary'
                                        ][$log->action] ?? 'bg-dark';
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_',' ', $log->action)) }}</span>
                                </td>
                                <td>{{ $log->client_name ?? 'N/A' }}</td>
                                <td>{{ $log->amount ?? '0.00' }}</td>
                                <td>
                                    @if($log->status)
                                        <span class="badge bg-{{ $log->status == 'accepted' ? 'success' : ($log->status == 'declined' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $log->details }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->action_date)->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button class="dropdown-item" onclick="copyEstimateDetails({{ json_encode($log) }})">
                                                    <i class="fas fa-copy me-2"></i> Copy Details
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-eye me-2"></i> View Estimate
                                                </a>
                                            </li>
                                            @if($log->action == 'pdf_downloaded' || $log->action == 'email_sent')
                                            <li>
                                                <button class="dropdown-item" onclick="resendEstimate({{ $log->id }})">
                                                    <i class="fas fa-paper-plane me-2"></i> Resend
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteEstimateLog({{ $log->id }})">
                                                    <i class="fas fa-trash me-2"></i> Delete Log
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No activity logs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                <div class="empty-state-title">No Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['estimate_action', 'estimate_client', 'estimate_status', 'estimate_date']))
                        No activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetEstimateFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No estimate activity logs found yet.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div><!-- END tab-3 -->

<style>
    /* Activity Log Styles */
    .view-content {
        transition: opacity 0.3s ease;
    }
    
    .filter-pill {
        cursor: pointer;
    }
    
    .filter-pill .btn-close {
        padding: 0.3rem;
        font-size: 0.7rem;
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
        border-left: 3px solid #0d6efd;
    }

    .timeline-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .timeline-body {
        font-size: 0.9rem;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-size: 18px;
        color: #666;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .empty-state-text {
        color: #999;
        font-size: 14px;
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
    }
</style>

<script>
// Global variable for DataTable
let estimateActivityLogDataTable = null;

// View Mode Switching
function switchEstimateViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('estimateTableViewContent').classList.remove('d-none');
        document.getElementById('estimateListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeEstimateDataTable, 50);
    } else {
        document.getElementById('estimateTableViewContent').classList.add('d-none');
        document.getElementById('estimateListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (estimateActivityLogDataTable !== null) {
            estimateActivityLogDataTable.destroy();
            estimateActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeEstimateDataTable() {
    // Destroy existing DataTable if it exists
    if (estimateActivityLogDataTable !== null) {
        estimateActivityLogDataTable.destroy();
        estimateActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('estimateActivityLogTable');
    if (table && $('#estimateActivityLogTable tbody tr').length > 0) {
        estimateActivityLogDataTable = $('#estimateActivityLogTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            order: [[7, 'desc']], // Sort by Date column descending
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
            }
        });
    }
}

// Reset Filters
function resetEstimateFilters() {
    const form = document.getElementById('estimateLogFilterForm');
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
function removeEstimateFilter(filterName) {
    const form = document.getElementById('estimateLogFilterForm');
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

// Copy Estimate Details to Clipboard
function copyEstimateDetails(log) {
    let textToCopy = `Estimate Activity Log Details:\n`;
    textToCopy += `Estimate ID: ${log.estimate_id}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Client: ${log.client_name || 'N/A'}\n`;
    textToCopy += `Amount: ${log.amount || '0.00'}\n`;
    textToCopy += `Status: ${log.status || 'N/A'}\n`;
    textToCopy += `Details: ${log.details}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Estimate details copied to clipboard',
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

// Resend Estimate
function resendEstimate(logId) {
    Swal.fire({
        title: 'Resend Estimate',
        text: 'Are you sure you want to resend this estimate?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resend',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to resend the estimate
            Swal.fire({
                title: 'Resent!',
                text: 'Estimate has been resent successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Delete Estimate Log
function deleteEstimateLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this activity log? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to delete the log
            Swal.fire({
                title: 'Deleted!',
                text: 'Activity log has been deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Set default view mode to List View
    switchEstimateViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('estimate_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('estimateLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('estimate_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('estimateLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (estimateActivityLogDataTable !== null) {
        estimateActivityLogDataTable.columns.adjust();
    }
});
</script>


    </div> <!-- END tab-content -->
</div>

<!-- Include jQuery and SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Zoho Style Tabs */
    .leave-tabs .nav-link {
        font-size: 15px;
        font-weight: 500;
        color: #333;
      
        border-bottom: 3px solid transparent;
        padding: 10px 18px;
    }

    .leave-tabs .nav-link.active {
        color: #f97316;
        border-bottom: 3px solid #f97316;
    }

    /* Filter Slide Panel Styles */
    .filter-slide-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 380px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .filter-slide-panel.active {
        right: 0;
    }

    .filter-square-btn {
        width: 42px;
        height: 42px;
        background: #fff;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #595959;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .filter-square-btn:hover {
        background: #f5f5f5;
    }

    /* Active Filters Badges */
    .badge.bg-info a {
        text-decoration: none;
        opacity: 0.8;
    }
    
    .badge.bg-info a:hover {
        opacity: 1;
    }

    /* Tab Content Area */
    .tab-content {
        margin-top: 20px;
    }

    /* Card Group Styling */
    .card-group.m-b-30 .card {
        border: 1px solid #e5eaf2;
        border-radius: 8px;
        margin-right: 15px;
    }

    .card-group.m-b-30 .card:last-child {
        margin-right: 0;
    }

    /* Alert Styling */
    .alert {
        position: relative;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1.25rem 1rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 0;
    }

    .empty-state i {
        opacity: 0.5;
    }

    .empty-state h5 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }

    /* Dropdown Styling */
    .dropdown-menu {
        min-width: 180px;
    }
    .dropdown-item {
        padding: 8px 15px;
        font-size: 14px;
    }
    .dropdown-item i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            });
        }, 5000);

        // Filter Panel Functionality
        const openFilterBtn = document.getElementById('openEstimateFilterBtn');
        const closeFilterBtn = document.getElementById('closeEstimateFilterBtn');
        const filterPanel = document.getElementById('estimateFilterPanel');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
                // Pre-fill filter values from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const clientInput = document.querySelector('input[name="client_name"]');
                const fromDateInput = document.querySelector('input[name="from_date"]');
                const toDateInput = document.querySelector('input[name="to_date"]');
                const statusSelect = document.querySelector('select[name="status"]');
                
                if (clientInput && urlParams.has('client_name')) {
                    clientInput.value = urlParams.get('client_name');
                }
                if (fromDateInput && urlParams.has('from_date')) {
                    fromDateInput.value = urlParams.get('from_date');
                }
                if (toDateInput && urlParams.has('to_date')) {
                    toDateInput.value = urlParams.get('to_date');
                }
                if (statusSelect && urlParams.has('status')) {
                    statusSelect.value = urlParams.get('status');
                }
            };
        }

        if (closeFilterBtn) {
            closeFilterBtn.onclick = () => {
                filterPanel.classList.remove('active');
            };
        }

        // Close filter when clicking outside
        document.addEventListener('click', (e) => {
            if (filterPanel.classList.contains('active') && 
                !filterPanel.contains(e.target) && 
                e.target !== openFilterBtn) {
                filterPanel.classList.remove('active');
            }
        });

        // Handle individual filter badge removal
        document.querySelectorAll('.badge.bg-info a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                window.location.href = url;
            });
        });
    });

    // Reset filters function
    function resetFilters() {
        // Clear all filter inputs in the form
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.reset();
        }
        
        // Close filter panel
        const filterPanel = document.getElementById('estimateFilterPanel');
        if (filterPanel) {
            filterPanel.classList.remove('active');
        }
        
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('estimate.index') }}";
    }

    // Status change function
    function statusChange(id, status) {
        Swal.fire({
            title: "Are you sure to change the status?",
            text: `You are about to change the status to ${status.toUpperCase()}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Change it!",
            cancelButtonText: "No, Cancel",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch("{{ route('estimate-changeStatus') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        id: id, 
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 1) {
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("AJAX Error:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to update status. Please try again.",
                        icon: "error"
                    });
                });
            }
        });
    }

    // Send mail function
    function sendMail(id) {
        Swal.fire({
            title: "Send Estimate Email",
            text: "Are you sure you want to send this estimate to the client?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Send it!",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/estimate/${id}/send-email`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value && result.value.success) {
                    Swal.fire({
                        title: "Sent!",
                        text: "The estimate has been sent to the client.",
                        icon: "success",
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: result.value.message || "Failed to send email",
                        icon: "error"
                    });
                }
            }
        });
    }

    // Delete estimate function
    function deleteData(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This estimate will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(`{{ route('estimate.destroy', '') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: "Deleted!",
                            text: data.message,
                            icon: "success",
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Delete Error:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to delete estimate. Please try again.",
                        icon: "error"
                    });
                });
            }
        });
    }
</script>
@endsection