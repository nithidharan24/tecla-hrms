@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Budgets');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
   <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('budgets.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Budgets
                </a>
                @endif
            </div>
    <!-- /Page Header -->

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ZOHO STYLE TABS -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#summary-tab">
                <i class="fa fa-chart-bar me-1"></i> Budgets Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $budgets->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#budget-activity-tab">
        <i class="fa fa-history me-1"></i> Activity Log
    </a>
</li>

    </ul>

    <!-- TAB CONTENT AREA -->
    <div class="tab-content pt-4">

        <!-- TAB 1 : SUMMARY -->
        <div class="tab-pane fade show active" id="summary-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-group m-b-30">
                        <!-- Total Budgets Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-wallet text-primary"></i> Total Budgets
                                    </span>
                                    <span class="text-primary">{{ $budgets->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $budgets->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All budget configurations</small>
                            </div>
                        </div>

                        <!-- Total Budget Amount -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-money-bill-wave text-success"></i> Total Amount
                                    </span>
                                    <span class="text-success">All Budgets</span>
                                </div>
                                @php
                                    $totalAmount = $budgets->sum('budget_amount');
                                @endphp
                                <h3 class="mb-3 text-success">₹{{ number_format($totalAmount, 2) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Combined budget amount</small>
                            </div>
                        </div>

                        <!-- Project Budgets -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-project-diagram text-warning"></i> Project Budgets
                                    </span>
                                    <span class="text-warning">{{ $budgets->where('budget_type', 'project')->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-warning">{{ $budgets->where('budget_type', 'project')->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    @php
                                        $projectPercent = $budgets->count() > 0 ? round(($budgets->where('budget_type', 'project')->count() / $budgets->count()) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-warning" style="width: {{ $projectPercent }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Project-based budgets</small>
                            </div>
                        </div>

                        <!-- Category Budgets -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-tags text-info"></i> Category Budgets
                                    </span>
                                    <span class="text-info">{{ $budgets->where('budget_type', 'category')->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-info">{{ $budgets->where('budget_type', 'category')->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    @php
                                        $categoryPercent = $budgets->count() > 0 ? round(($budgets->where('budget_type', 'category')->count() / $budgets->count()) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-info" style="width: {{ $categoryPercent }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Category-based budgets</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TAB 1 -->

        <!-- TAB 2 : LIST WITH FILTER -->
        <div class="tab-pane fade" id="list-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Top Bar with Filter Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top:-10px;">
                                <h4 class="fw-bold mb-0">All Budgets</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openBudgetFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="budgetFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Budgets</h5>
                                    <button id="closeBudgetFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('budgets.index') }}" id="filterForm">
                                    @if(request()->has('search') || request()->has('budget_type') || request()->has('date_range'))
                                    <div class="mb-3">
                                        <div class="alert alert-info py-2">
                                            <small><i class="fa fa-info-circle me-1"></i> Active filters applied</small>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Budget Title</label>
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               class="form-control" placeholder="Search by budget title...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Budget Type</label>
                                        <select name="budget_type" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="project" {{ request('budget_type') == 'project' ? 'selected' : '' }}>Project</option>
                                            <option value="category" {{ request('budget_type') == 'category' ? 'selected' : '' }}>Category</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date Range</label>
                                        <select name="date_range" class="form-select" id="dateRangeSelect">
                                            <option value="">All Time</option>
                                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>

                                    <div id="customDateRange" class="mb-3" style="display: none;">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">Start Date</label>
                                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">End Date</label>
                                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                                            </div>
                                        </div>
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
                            @if(request()->has('search') || request()->has('budget_type') || request()->has('date_range'))
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
                                        @if(request('budget_type'))
                                        <span class="badge bg-info me-2">
                                            Type: {{ ucfirst(request('budget_type')) }}
                                            <a href="?{{ http_build_query(request()->except('budget_type')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('date_range'))
                                        <span class="badge bg-info me-2">
                                            Range: {{ ucfirst(str_replace('_', ' ', request('date_range'))) }}
                                            <a href="?{{ http_build_query(request()->except('date_range', 'start_date', 'end_date')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Budgets Table -->
                            <div class="table-responsive">
                                @if($budgets->count())
                                    <table id="budget-table" class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th style="width:36px;">
                                                    <input type="checkbox" class="od-check" id="checkAllBudget" aria-label="Select all">
                                                </th>
                                                <th>S.No</th>
                                                <th>Budget Title</th>
                                                <th>Budget Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Total Revenue</th>
                                                <th>Total Expenses</th>
                                                <th>Tax Amount</th>
                                                <th>Budget Amount</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($budgets as $budget)
                                            <tr id="budget-row-{{ $budget->id }}">
                                                <td>
                                                    <input type="checkbox" class="od-check row-check-budget" aria-label="Select row">
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><span class="od-chip-highlight">{{ $budget->budget_title }}</span></td>
                                                <td>{{ $budget->budget_type }}</td>
                                                <td>{{ \Carbon\Carbon::parse($budget->start_date)->format('d/m/y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($budget->end_date)->format('d/m/y') }}</td>
                                                <td>{{ $budget->total_revenue }}</td>
                                                <td>{{ $budget->total_expenses }}</td>
                                                <td>{{ $budget->tax_amount }}</td>
                                                <td>{{ $budget->budget_amount }}</td>
                                                <td class="text-end">
                                                    <div class="od-inline-actions">
                                                        @if(isset($permissions) && $permissions->can_edit)
                                                        <a class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#edit_budget_{{ $budget->id }}" title="Edit">
                                                            <i class="fa-solid fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                        @if(isset($permissions) && $permissions->can_delete)
                                                        <a class="od-icon-btn danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $budget->id }}" title="Delete">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-light text-center py-4">
                                        <i class="fa fa-wallet fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Budgets Found</h5>
                                        <p class="text-muted">
                                            @if(request()->has('search') || request()->has('budget_type') || request()->has('date_range'))
                                            No budgets found matching your filter criteria.
                                            @else
                                            No budgets have been created yet.
                                            @endif
                                        </p>
                                        @if(isset($permissions) && $permissions->can_create)
                                        <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus me-2"></i> Create Budget
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $budgets->count() }} budgets
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TAB 2 -->

    <!-- =====================================================
        TAB : BUDGET ACTIVITY LOG
====================================================== -->
<div class="tab-pane fade" id="budget-activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-money-bill-wave me-2"></i> Budget Activity Logs
                @if($budgetLogs && count($budgetLogs) > 0)
                    <small class="text-muted">({{ count($budgetLogs) }} records)</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
              
                
                <!-- View Toggle -->
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="budgetViewMode" id="budgetListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="budgetListView" onclick="switchBudgetViewMode('list')">
                        <i class="fas fa-list"></i> 
                    </label>
                    <input type="radio" class="btn-check" name="budgetViewMode" id="budgetTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="budgetTableView" onclick="switchBudgetViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

      
  
        <!-- Quick Summary -->
        @if($budgetLogs && count($budgetLogs) > 0)
            <div class="d-flex flex-wrap gap-3 mb-3">
                <div class="summary-item">
                    <small class="text-muted d-block">Total Budgets</small>
                    <span class="fw-bold">{{ $budgetLogs->count() }}</span>
                </div>
                <div class="summary-item">
                    <small class="text-muted d-block">Total Amount</small>
                    <span class="fw-bold">₹ {{ number_format($budgetLogs->sum('budget_amount'), 2) }}</span>
                </div>
                <div class="summary-item">
                    <small class="text-muted d-block">Avg. Amount</small>
                    <span class="fw-bold">₹ {{ number_format($budgetLogs->avg('budget_amount'), 2) }}</span>
                </div>
                <div class="summary-item">
                    <small class="text-muted d-block">Total Revenue</small>
                    <span class="fw-bold text-success">₹ {{ number_format($budgetLogs->sum('total_revenue'), 2) }}</span>
                </div>
                <div class="summary-item">
                    <small class="text-muted d-block">Total Profit</small>
                    <span class="fw-bold text-success">₹ {{ number_format($budgetLogs->sum('expected_profit'), 2) }}</span>
                </div>
            </div>
        @endif

        @if($budgetLogs && count($budgetLogs) > 0)
            <!-- List View -->
            <div id="budgetListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @foreach ($budgetLogs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    @php
                                        $actionIcons = [
                                            'created' => 'fa-plus-circle text-success',
                                            'updated' => 'fa-edit text-primary',
                                            'deleted' => 'fa-trash text-danger',
                                            'approved' => 'fa-check-circle text-success',
                                            'rejected' => 'fa-times-circle text-danger'
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'fa-money-bill-wave text-dark';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        <i class="fas fa-money-bill-wave me-1"></i>
                                                        {{ $log->budget_id }}
                                                    </a>
                                                    <span class="ms-2">- {{ $log->title }}</span>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-rupee-sign me-1"></i>
                                                        ₹ {{ number_format($log->budget_amount, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $cls = [
                                                        'created'  => 'bg-success',
                                                        'updated'  => 'bg-primary',
                                                        'deleted'  => 'bg-danger',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger'
                                                    ][$log->action] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $cls }}">
                                                    {{ ucfirst($log->action) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title" style="background-color: #17a2b8; color: white;">
                                                    <i class="fas fa-chart-pie"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-2"><strong>Details:</strong> {{ $log->details }}</p>
                                                
                                                <!-- Financial Summary -->
                                                <div class="row g-2 mt-2">
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-between border-bottom pb-1">
                                                            <span>Total Revenue:</span>
                                                            <span class="fw-bold text-success">₹ {{ number_format($log->total_revenue, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between border-bottom pb-1 mt-1">
                                                            <span>Total Expenses:</span>
                                                            <span class="fw-bold text-danger">₹ {{ number_format($log->total_expenses, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-between border-bottom pb-1">
                                                            <span>Expected Profit:</span>
                                                            <span class="fw-bold {{ $log->expected_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                                ₹ {{ number_format($log->expected_profit, 2) }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between border-bottom pb-1 mt-1">
                                                            <span>Budget Amount:</span>
                                                            <span class="fw-bold text-primary">₹ {{ number_format($log->budget_amount, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-3">
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
                                                        <!-- Add action buttons here if needed -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyBudgetDetails({{ json_encode($log) }})">
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
            <div id="budgetTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="budgetActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Budget ID</th>
                                <th>Action</th>
                                <th>Title</th>
                                <th>Total Revenue</th>
                                <th>Total Expenses</th>
                                <th>Profit</th>
                                <th>Budget Amount</th>
                                <th>Details</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($budgetLogs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $log->budget_id }}</strong></td>
                                <td>
                                    @php
                                        $cls = [
                                            'created'  => 'bg-success',
                                            'updated'  => 'bg-primary',
                                            'deleted'  => 'bg-danger',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger'
                                        ][$log->action] ?? 'bg-dark';
                                    @endphp
                                    <span class="badge {{ $cls }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>{{ $log->title }}</td>
                                <td>₹ {{ number_format($log->total_revenue, 2) }}</td>
                                <td>₹ {{ number_format($log->total_expenses, 2) }}</td>
                                <td>
                                    <span class="{{ $log->expected_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₹ {{ number_format($log->expected_profit, 2) }}
                                    </span>
                                </td>
                                <td>₹ {{ number_format($log->budget_amount, 2) }}</td>
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
            
            <!-- Pagination (if using pagination) -->
            @if($budgetLogs instanceof \Illuminate\Pagination\LengthAwarePaginator && $budgetLogs->hasPages())
                <div class="mt-3">
                    {{ $budgetLogs->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="empty-state-title">No Budget Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['budget_action', 'budget_title', 'budget_date', 'budget_min_amount', 'budget_max_amount']))
                        No budget activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetBudgetFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No budget activity logs found yet.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Budget Activity Log Styles */
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
        border-left: 3px solid #17a2b8;
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
let budgetActivityLogDataTable = null;

// View Mode Switching
function switchBudgetViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('budgetTableViewContent').classList.remove('d-none');
        document.getElementById('budgetListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeBudgetDataTable, 50);
    } else {
        document.getElementById('budgetTableViewContent').classList.add('d-none');
        document.getElementById('budgetListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (budgetActivityLogDataTable !== null) {
            budgetActivityLogDataTable.destroy();
            budgetActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeBudgetDataTable() {
    // Destroy existing DataTable if it exists
    if (budgetActivityLogDataTable !== null) {
        budgetActivityLogDataTable.destroy();
        budgetActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('budgetActivityLogTable');
    if (table && $('#budgetActivityLogTable tbody tr').length > 0) {
        budgetActivityLogDataTable = $('#budgetActivityLogTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            order: [[9, 'desc']], // Sort by Date column descending
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
function resetBudgetFilters() {
    const form = document.getElementById('budgetLogFilterForm');
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
function removeBudgetFilter(filterName) {
    const form = document.getElementById('budgetLogFilterForm');
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

// Copy Budget Details to Clipboard
function copyBudgetDetails(log) {
    let textToCopy = `Budget Activity Log Details:\n`;
    textToCopy += `Budget ID: ${log.budget_id}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Title: ${log.title}\n`;
    textToCopy += `Total Revenue: ₹ ${number_format(log.total_revenue, 2)}\n`;
    textToCopy += `Total Expenses: ₹ ${number_format(log.total_expenses, 2)}\n`;
    textToCopy += `Expected Profit: ₹ ${number_format(log.expected_profit, 2)}\n`;
    textToCopy += `Budget Amount: ₹ ${number_format(log.budget_amount, 2)}\n`;
    textToCopy += `Details: ${log.details}\n`;
    textToCopy += `Performed By: ${log.performed_by || 'System'}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Budget details copied to clipboard',
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

// View Budget Details
function viewBudgetDetails(budgetId) {
    // Add your logic to view budget details
    window.location.href = `/budgets/${budgetId}`;
}

// Download Budget Report
function downloadBudgetReport(budgetId) {
    Swal.fire({
        title: 'Export Budget Report',
        text: 'Preparing budget report for download...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    
    // Add your AJAX call here to generate and download report
    // Example:
    // window.location.href = `/budgets/${budgetId}/export`;
}

// Delete Budget Log
function deleteBudgetLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this budget activity log? This action cannot be undone.',
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
                text: 'Budget activity log has been deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Helper function for number formatting
function number_format(number, decimals) {
    number = parseFloat(number);
    return number.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Set default view mode to List View
    switchBudgetViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('budget_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('budgetLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('budget_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('budgetLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (budgetActivityLogDataTable !== null) {
        budgetActivityLogDataTable.columns.adjust();
    }
});
</script>

    </div>
    <!-- END tab-content -->
</div>

<!-- ALL MODALS ARE PLACED HERE AT THE BOTTOM -->

@foreach($budgets as $budget)
<!-- Delete Modal for {{ $budget->id }} -->
<div class="modal fade" id="deleteModal_{{ $budget->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Budget Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete "{{ $budget->budget_title }}" budget item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Budget Modal for {{ $budget->id }} -->
<div class="modal custom-modal fade" id="edit_budget_{{ $budget->id }}" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('budgets.update', $budget->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="input-block mb-3">
                        <label class="col-form-label">Budget Title</label>
                        <input class="form-control" type="text" name="budget_title" value="{{ $budget->budget_title }}" required>
                    </div>
                    <label class="col-form-label">Choose Budget Type</label>
                    <div class="input-block mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="budget_type" value="project" {{ $budget->budget_type == 'project' ? 'checked' : '' }} required>
                            <label class="form-check-label">Project</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="budget_type" value="category" {{ $budget->budget_type == 'category' ? 'checked' : '' }} required>
                            <label class="form-check-label">Category</label>
                        </div>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Start Date</label>
                        <input class="form-control" type="date" name="start_date" value="{{ \Carbon\Carbon::parse($budget->start_date)->format('Y-m-d') }}" required>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">End Date</label>
                        <input class="form-control" type="date" name="end_date" value="{{ \Carbon\Carbon::parse($budget->end_date)->format('Y-m-d') }}" required>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Total Revenue</label>
                        <input class="form-control" type="text" name="total_revenue" value="{{ $budget->total_revenue }}" required>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Total Expenses</label>
                        <input class="form-control" type="text" name="total_expenses" value="{{ $budget->total_expenses }}" required>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Expected Profit (C = A - B)</label>
                        <input class="form-control" type="text" name="expected_profit" value="{{ $budget->budget_amount }}" readonly>
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Tax Amount</label>
                        <input class="form-control" type="text" name="tax_amount" value="{{ $budget->tax_amount }}">
                    </div>
                    <div class="input-block mb-3">
                        <label class="col-form-label">Budget Amount</label>
                        <input class="form-control" type="text" name="budget_amount" value="{{ $budget->budget_amount }}" readonly>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

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

    /* Progress Bar */
    .height-five {
        height: 5px;
    }

    /* Alert Styling */
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
        const openFilterBtn = document.getElementById('openBudgetFilterBtn');
        const closeFilterBtn = document.getElementById('closeBudgetFilterBtn');
        const filterPanel = document.getElementById('budgetFilterPanel');
        const dateRangeSelect = document.getElementById('dateRangeSelect');
        const customDateRange = document.getElementById('customDateRange');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
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

        // Show/hide custom date range
        if (dateRangeSelect && customDateRange) {
            // Check on page load
            if (dateRangeSelect.value === 'custom') {
                customDateRange.style.display = 'block';
            }

            dateRangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.style.display = 'block';
                } else {
                    customDateRange.style.display = 'none';
                }
            });
        }

        // Handle individual filter badge removal
        document.querySelectorAll('.badge.bg-info a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                window.location.href = url;
            });
        });

        // Check All functionality
        const checkAllBudget = document.getElementById('checkAllBudget');
        const rowsBudget = document.querySelectorAll('.row-check-budget');

        if (checkAllBudget) {
            checkAllBudget.addEventListener('change', function() {
                rowsBudget.forEach(r => {
                    r.checked = this.checked;
                    r.closest('tr').classList.toggle('od-selected', this.checked);
                });
            });
        }

        rowsBudget.forEach(r => {
            r.addEventListener('change', function() {
                this.closest('tr').classList.toggle('od-selected', this.checked);
            });
        });
    });

    // Reset filters function
    function resetFilters() {
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('budgets.index') }}";
    }
</script>
@endsection