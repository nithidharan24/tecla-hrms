@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Taxes');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
     <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_tax">
                    <i class="fa fa-plus"></i> Add Tax
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
                <i class="fa fa-chart-bar me-1"></i>Taxes Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $taxes->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#tax-activity-tab">Activity Log</a>
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
                        <!-- Total Taxes Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-percent text-primary"></i> Total Taxes
                                    </span>
                                    <span class="text-success">{{ $taxes->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $taxes->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All tax configurations</small>
                            </div>
                        </div>

                        <!-- Active Taxes Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-check-circle text-success"></i> Active Taxes
                                    </span>
                                    <span class="text-success">{{ $taxes->where('status', 'active')->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-success">{{ $taxes->where('status', 'active')->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: {{ $taxes->count() > 0 ? round(($taxes->where('status', 'active')->count() / $taxes->count()) * 100) : 0 }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Currently active taxes</small>
                            </div>
                        </div>

                        <!-- Average Tax Rate Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-chart-line text-warning"></i> Average Rate
                                    </span>
                                    <span class="text-warning">Active Taxes</span>
                                </div>
                                @php
                                    $activeTaxes = $taxes->where('status', 'active');
                                    $averageRate = $activeTaxes->count() > 0 ? round($activeTaxes->avg('percentage'), 1) : 0;
                                @endphp
                                <h3 class="mb-3 text-warning">{{ $averageRate }}%</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-warning" style="width: {{ min($averageRate, 100) }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Average tax percentage</small>
                            </div>
                        </div>

                        <!-- Tax Range Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-sliders text-info"></i> Tax Range
                                    </span>
                                    <span class="text-info">Min-Max</span>
                                </div>
                                @php
                                    $minRate = $taxes->count() > 0 ? $taxes->min('percentage') : 0;
                                    $maxRate = $taxes->count() > 0 ? $taxes->max('percentage') : 0;
                                @endphp
                                <h4 class="mb-3 text-info">{{ $minRate }}% - {{ $maxRate }}%</h4>
                                <div class="text-muted small mb-2">{{ $taxes->where('status', 'active')->count() }} active rates</div>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Tax percentage range</small>
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
                                <h4 class="fw-bold mb-0">All Taxes</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openTaxFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="taxFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Taxes</h5>
                                    <button id="closeTaxFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('tax.index') }}" id="filterForm">
                                    @if(request()->has('search') || request()->has('status') || request()->has('percentage_range'))
                                    <div class="mb-3">
                                        <div class="alert alert-info py-2">
                                            <small><i class="fa fa-info-circle me-1"></i> Active filters applied</small>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tax Name</label>
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               class="form-control" placeholder="Search by tax name...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Percentage Range</label>
                                        <select name="percentage_range" class="form-select">
                                            <option value="">All Rates</option>
                                            <option value="0-5" {{ request('percentage_range') == '0-5' ? 'selected' : '' }}>0% - 5%</option>
                                            <option value="6-10" {{ request('percentage_range') == '6-10' ? 'selected' : '' }}>6% - 10%</option>
                                            <option value="11-15" {{ request('percentage_range') == '11-15' ? 'selected' : '' }}>11% - 15%</option>
                                            <option value="16-20" {{ request('percentage_range') == '16-20' ? 'selected' : '' }}>16% - 20%</option>
                                            <option value="21+" {{ request('percentage_range') == '21+' ? 'selected' : '' }}>21% and above</option>
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
                            @if(request()->has('search') || request()->has('status') || request()->has('percentage_range'))
                            <div class="alert alert-light border mb-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted me-2">Active Filters:</small>
                                        @if(request('search'))
                                        <span class="badge bg-info me-2">
                                            Search: "{{ request('search') }}"
                                            <a href="?{{ http_build_query(request()->except('search')) }}" class="text-white ms-1">
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
                                        @if(request('percentage_range'))
                                        <span class="badge bg-info me-2">
                                            Range: {{ request('percentage_range') }}%
                                            <a href="?{{ http_build_query(request()->except('percentage_range')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('tax.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Taxes Table -->
                            <div class="table-responsive">
                                @if($taxes->count())
                                    <table class="table custom-table datatable mb-0">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Tax Name</th>
                                                <th>Tax Percentage (%)</th>
                                                <th>Status</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($taxes as $item)
                                                <tr id="tax-row-{{ $item->id }}">
                                                    <td data-label="S.No">{{ $loop->iteration }}</td>
                                                    <td data-label="Name">
                                                        <span class="od-chip-highlight">{{ $item->name }}</span>
                                                    </td>
                                                    <td data-label="Percentage">
                                                        <span class="high">{{ $item->percentage }}%</span>
                                                    </td>
                                                    <td data-label="Status">
                                                        <div class="dropdown">
                                                            @php
                                                                $statusClass = $item->status == 'active' ? 'od-chip-success' : 'od-chip-danger';
                                                            @endphp
                                                            <button class="od-chip {{ $statusClass }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa-regular fa-circle-dot me-1"></i> {{ ucfirst($item->status) }}
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <button class="dropdown-item {{ $item->status == 'active' ? 'disabled' : '' }}" 
                                                                        onclick="statusChange('{{ $item->id }}', 'active')">
                                                                        <i class="fa-regular fa-circle-dot text-success me-1"></i> Active
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item {{ $item->status == 'inactive' ? 'disabled' : '' }}" 
                                                                        onclick="statusChange('{{ $item->id }}', 'inactive')">
                                                                        <i class="fa-regular fa-circle-dot text-danger me-1"></i> Inactive
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <td data-label="Actions" class="text-end">
                                                        <div class="od-inline-actions">
                                                            @if($permissions->can_edit)
                                                            <a class="od-icon-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#edit_tax" title="Edit">
                                                                <i class="fa-solid fa-pencil"></i>
                                                            </a>
                                                            @endif
                                                            @if($permissions->can_delete)
                                                           <button type="button" class="od-icon-btn danger" onclick="deleteData('{{ $item->id }}')" title="Delete">
    <i class="fa-regular fa-trash-can"></i>
</button>

                                                            @endif
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-light text-center py-4">
                                        <i class="fa fa-percent fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Taxes Found</h5>
                                        <p class="text-muted">
                                            @if(request()->has('search') || request()->has('status') || request()->has('percentage_range'))
                                            No taxes found matching your filter criteria.
                                            @else
                                            No taxes have been configured yet.
                                            @endif
                                        </p>
                                        @if(isset($permissions) && $permissions->can_create)
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_tax">
                                            <i class="fa fa-plus me-2"></i> Add Tax
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $taxes->count() }} taxes
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 2 -->

<!-- =====================================================
        TAB : TAX ACTIVITY LOG
====================================================== -->
<div class="tab-pane fade" id="tax-activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-percentage me-2"></i> Tax Activity Logs
                @if($taxLogs && count($taxLogs) > 0)
                    <small class="text-muted">({{ count($taxLogs) }} records)</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
               
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="taxViewMode" id="taxListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="taxListView" onclick="switchTaxViewMode('list')">
                        <i class="fas fa-list"></i> 
                    </label>
                    <input type="radio" class="btn-check" name="taxViewMode" id="taxTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="taxTableView" onclick="switchTaxViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

    

     

        @if($taxLogs && count($taxLogs) > 0)
            <!-- List View -->
            <div id="taxListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @foreach ($taxLogs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    @php
                                        $actionIcons = [
                                            'created' => 'fa-plus-circle text-success',
                                            'updated' => 'fa-edit text-primary',
                                            'status_changed' => 'fa-exchange-alt text-warning',
                                            'deleted' => 'fa-trash text-danger',
                                            'activated' => 'fa-toggle-on text-success',
                                            'deactivated' => 'fa-toggle-off text-danger',
                                            'rate_changed' => 'fa-percentage text-info'
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'fa-percentage text-dark';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        <i class="fas fa-percentage me-1"></i>
                                                        {{ $log->tax_id }}
                                                    </a>
                                                    <span class="ms-2">- {{ $log->tax_name }}</span>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-percent me-1"></i>
                                                        {{ $log->percentage }}%
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $cls = [
                                                        'created'        => 'bg-success',
                                                        'updated'        => 'bg-primary',
                                                        'status_changed' => 'bg-warning',
                                                        'deleted'        => 'bg-danger',
                                                        'activated'      => 'bg-success',
                                                        'deactivated'    => 'bg-danger',
                                                        'rate_changed'   => 'bg-info'
                                                    ][$log->action] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $cls }}">
                                                    {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                                </span>
                                                @if($log->status)
                                                    <br>
                                                    <span class="badge mt-1 bg-{{ 
                                                        $log->status == 'active' ? 'success' : 
                                                        ($log->status == 'inactive' ? 'danger' : 'warning') 
                                                    }}">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title" style="background-color: #dc3545; color: white;">
                                                    <i class="fas fa-percentage"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-2"><strong>Details:</strong> {{ $log->details }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i> {{ $log->performed_by ?? 'System' }}
                                                        </small>
                                                       
                                                        @if($log->ip_address)
                                                            <small class="text-muted ms-3">
                                                                <i class="fas fa-globe me-1"></i> {{ $log->ip_address }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                       
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyTaxDetails({{ json_encode($log) }})">
                                                <i class="fas fa-copy"></i> Copy Details
                                            </button>
                                            
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="taxTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="taxActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tax ID</th>
                                <th>Action</th>
                                <th>Tax Name</th>
                              
                                <th>Status</th>
                                <th>Details</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($taxLogs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $log->tax_id }}</strong></td>
                                <td>
                                    @php
                                        $cls = [
                                            'created'        => 'bg-success',
                                            'updated'        => 'bg-primary',
                                            'status_changed' => 'bg-warning',
                                            'deleted'        => 'bg-danger',
                                            'activated'      => 'bg-success',
                                            'deactivated'    => 'bg-danger',
                                            'rate_changed'   => 'bg-info'
                                        ][$log->action] ?? 'bg-dark';
                                    @endphp
                                    <span class="badge {{ $cls }}">
                                        {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                    </span>
                                </td>
                                <td>{{ $log->tax_name }}</td>
                               
                                <td>
                                    @if($log->status)
                                        <span class="badge bg-{{ 
                                            $log->status == 'active' ? 'success' : 
                                            ($log->status == 'inactive' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="log-details" title="{{ $log->details }}">
                                        {{ Str::limit($log->details, 50) }}
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, H:i') }}</td>
                              
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
           
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-percentage"></i></div>
                <div class="empty-state-title">No Tax Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['tax_action', 'tax_name', 'tax_status', 'tax_date', 'tax_search', 'tax_min_percentage', 'tax_max_percentage']))
                        No tax activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetTaxFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No tax activity logs found yet.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Tax Activity Log Styles */
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
        border-left: 3px solid #dc3545;
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
    }

    .log-details {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        margin-right: 10px;
        min-width: 120px;
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
        
        .summary-item {
            min-width: 100px;
            margin-bottom: 5px;
        }
    }
</style>

<script>
// Global variable for DataTable
let taxActivityLogDataTable = null;

// View Mode Switching
function switchTaxViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('taxTableViewContent').classList.remove('d-none');
        document.getElementById('taxListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeTaxDataTable, 50);
    } else {
        document.getElementById('taxTableViewContent').classList.add('d-none');
        document.getElementById('taxListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (taxActivityLogDataTable !== null) {
            taxActivityLogDataTable.destroy();
            taxActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeTaxDataTable() {
    // Destroy existing DataTable if it exists
    if (taxActivityLogDataTable !== null) {
        taxActivityLogDataTable.destroy();
        taxActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('taxActivityLogTable');
    if (table && $('#taxActivityLogTable tbody tr').length > 0) {
        taxActivityLogDataTable = $('#taxActivityLogTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            order: [[8, 'desc']], // Sort by Date column descending
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
function resetTaxFilters() {
    const form = document.getElementById('taxLogFilterForm');
    const inputs = form.querySelectorAll('select, input[type="text"], input[type="date"], input[type="number"]');
    
    inputs.forEach(input => {
        if (input.type === 'text' || input.type === 'date' || input.type === 'number') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    
    form.submit();
}

// Remove individual filter
function removeTaxFilter(filterName) {
    const form = document.getElementById('taxLogFilterForm');
    const input = form.querySelector(`[name="${filterName}"]`);
    
    if (input) {
        if (input.type === 'text' || input.type === 'date' || input.type === 'number') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    }
    
    form.submit();
}

// Copy Tax Details to Clipboard
function copyTaxDetails(log) {
    let textToCopy = `Tax Activity Log Details:\n`;
    textToCopy += `Tax ID: ${log.tax_id}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Tax Name: ${log.tax_name}\n`;
    textToCopy += `Percentage: ${log.percentage}%\n`;
    textToCopy += `Tax Type: ${log.tax_type || 'N/A'}\n`;
    textToCopy += `Status: ${log.status || 'N/A'}\n`;
    textToCopy += `Details: ${log.details}\n`;
    textToCopy += `Performed By: ${log.performed_by || 'System'}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Tax details copied to clipboard',
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

// Apply Tax to Items
function applyTaxToItems(taxId) {
    Swal.fire({
        title: 'Apply Tax to Items',
        text: 'Are you sure you want to apply this tax to all items?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, apply',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to apply tax to items
            Swal.fire({
                title: 'Applied!',
                text: 'Tax has been applied to items successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Edit Tax
function editTax(taxId) {
    // Add your edit logic here
    window.location.href = `/taxes/${taxId}/edit`;
}

// Deactivate Tax
function deactivateTax(taxId) {
    Swal.fire({
        title: 'Deactivate Tax',
        text: 'Are you sure you want to deactivate this tax?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, deactivate',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to deactivate tax
            Swal.fire({
                title: 'Deactivated!',
                text: 'Tax has been deactivated successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Delete Tax Log
function deleteTaxLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this tax activity log? This action cannot be undone.',
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
                text: 'Tax activity log has been deleted.',
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
    switchTaxViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('tax_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('taxLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('tax_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('taxLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (taxActivityLogDataTable !== null) {
        taxActivityLogDataTable.columns.adjust();
    }
});
</script>


    </div> <!-- END tab-content -->
</div>

<!-- Add Tax Modal -->
<div id="add_tax" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addtax" method="POST" action="{{ route('tax.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-block mb-3">
                        <label class="col-form-label">Tax Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="tax" id="taxname" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Tax Percentage (%) <span class="text-danger">*</span></label>
                        <input class="form-control" name="percent" id="percentage" type="text">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tax Modal -->
<div id="edit_tax" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tax</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edittax" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Tax Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="tax" id="etaxname" type="text">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Tax Percentage (%) <span class="text-danger">*</span></label>
                        <input class="form-control" name="percent" id="epercentage" type="text">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

    /* Status Chips */
    .od-chip-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    
    .od-chip-danger {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .od-chip {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
        cursor: pointer;
    }
    
    .od-chip.dropdown-toggle::after {
        margin-left: 5px;
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
        const openFilterBtn = document.getElementById('openTaxFilterBtn');
        const closeFilterBtn = document.getElementById('closeTaxFilterBtn');
        const filterPanel = document.getElementById('taxFilterPanel');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
                // Pre-fill filter values from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const searchInput = document.querySelector('input[name="search"]');
                const statusSelect = document.querySelector('select[name="status"]');
                const rangeSelect = document.querySelector('select[name="percentage_range"]');
                
                if (searchInput && urlParams.has('search')) {
                    searchInput.value = urlParams.get('search');
                }
                if (statusSelect && urlParams.has('status')) {
                    statusSelect.value = urlParams.get('status');
                }
                if (rangeSelect && urlParams.has('percentage_range')) {
                    rangeSelect.value = urlParams.get('percentage_range');
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

        // Edit modal setup - BETTER APPROACH WITH DATA ATTRIBUTES
document.querySelectorAll('[data-bs-target="#edit_tax"]').forEach(button => {
    button.addEventListener('click', function(e) {
        const id = this.getAttribute('data-id');
        
        // Get URLs from data attributes or generate them
        const editUrl = `{{ url('tax') }}/${id}/edit`; // Using url() helper
        const updateUrl = `{{ url('tax') }}/${id}`;    // Using url() helper
        
        // Show loading state
        const taxNameInput = document.getElementById('etaxname');
        const taxPercentInput = document.getElementById('epercentage');
        const form = document.getElementById('edittax');
        
        taxNameInput.value = 'Loading...';
        taxPercentInput.value = 'Loading...';
        
        // Fetch tax data
        fetch(editUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Populate the form fields
            taxNameInput.value = data.name || '';
            taxPercentInput.value = data.percentage || '';
            
            // Set the form action URL for update
            form.action = updateUrl;
            
            // Make sure the form has the correct method spoofing
            // Add this if not already present in your form
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PUT';
            }
        })
        .catch(error => {
            console.error('Error fetching tax data:', error);
            taxNameInput.value = '';
            taxPercentInput.value = '';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load tax data. Please try again.'
            });
        });
    });
});

        // Form validation initialization (you can keep your existing jQuery validation)
        if (typeof $.fn.validate !== 'undefined') {
            // Initialize form validation
            $("#addtax").validate({
                rules: {
                    tax: {
                        required: true,
                        maxlength: 10
                    },
                    percent: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 100 
                    },
                },
                messages: {
                    tax: {
                        required: "Tax name is required.",
                        maxlength: "Tax name should not exceed 10 characters."
                    },
                    percent: {
                        required: "Percentage is required.",
                        number: "Percentage should be a number.",
                        min: "Percentage cannot be less than 0.",
                        max: "Percentage cannot exceed 100."
                    }
                },
                errorClass: "error",
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-block').append(error);
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

            $("#edittax").validate({
                rules: {
                    tax: {
                        required: true,
                        maxlength: 10
                    },
                    percent: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 100 
                    },
                },
                messages: {
                    tax: {
                        required: "Tax name is required.",
                        maxlength: "Tax name should not exceed 10 characters."
                    },
                    percent: {
                        required: "Percentage is required.",
                        number: "Percentage should be a number.",
                        min: "Percentage cannot be less than 0.",
                        max: "Percentage cannot exceed 100."
                    }
                },
                errorClass: "error",
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-block').append(error);
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

            // Reset validation when modals are shown
            document.getElementById('add_tax').addEventListener('show.bs.modal', function() {
                $("#addtax").validate().resetForm();
                document.getElementById('addtax').reset();
            });

            document.getElementById('edit_tax').addEventListener('show.bs.modal', function() {
                $("#edittax").validate().resetForm();
            });
        }
    });

    // Reset filters function
    function resetFilters() {
        // Clear all filter inputs in the form
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.reset();
        }
        
        // Close filter panel
        const filterPanel = document.getElementById('taxFilterPanel');
        if (filterPanel) {
            filterPanel.classList.remove('active');
        }
        
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('tax.index') }}";
    }

    // Status change function
// Status change function - UPDATED VERSION
function statusChange(id, status) {
    Swal.fire({
        title: "Are you sure to change the status?",
        text: `Do you want to change the status to ${status}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        allowOutsideClick: false,
    }).then(function(result) {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Using the named route
            fetch(`{{ route('tax.changeStatus', '') }}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Success!",
                        text: data.message || "Tax status changed successfully.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message || "Failed to update tax status.",
                        icon: "error"
                    });
                }
            })
            .catch(error => {
                console.error("Error updating tax status:", error);
                Swal.fire({
                    title: "Error",
                    text: error.message || "Failed to update tax status. Please try again.",
                    icon: "error"
                });
            });
        }
    });
}

  function deleteData(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This tax will be permanently deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "/tax/" + id,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function (res) {

                    if (res.success) {

                        Swal.fire({
                            title: "Deleted!",
                            text: res.message,
                            icon: "success",
                            timer: 1400,
                            showConfirmButton: false
                        }).then(() => {

                            // Remove row
                            $("#tax-row-" + id).fadeOut(300, function() {
                                $(this).remove();

                                // If table empty → reload
                                if ($("tbody tr").length === 0) {
                                    location.reload();
                                }
                            });
                        });

                    } else {
                        Swal.fire("Error!", res.message, "error");
                    }
                },

                error: function () {
                    Swal.fire("Error!", "Something went wrong.", "error");
                }

            });

        }
    });
}


    // Show validation errors using SweetAlert
    @if ($errors->any())
        let errorMessage = '';
        @foreach ($errors->all() as $error)
            errorMessage += "{{ $error }}\n";
        @endforeach
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            text: errorMessage,
        });
    @endif
</script>
@endsection