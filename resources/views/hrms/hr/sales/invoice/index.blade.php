@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Invoices');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">All Invoices
  <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('invoice.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Create Invoice
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
                <i class="fa fa-chart-bar me-1"></i>Invoices Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $invoices->count() }}</span>
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
                        <!-- Total Invoices Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-file-invoice-dollar text-primary"></i> Total Invoices
                                    </span>
                                    <span class="text-success">{{ $invoices->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $invoices->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All invoices in system</small>
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
                                <small class="text-muted">Total invoice value</small>
                            </div>
                        </div>

                        <!-- Status Distribution Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-chart-pie text-warning"></i> Payment Status
                                    </span>
                                    <span class="text-warning">Overview</span>
                                </div>
                                <div class="mb-2">
                                    @php
                                        $statusCounts = [
                                            'sent' => 0,
                                            'paid' => 0,
                                            'partially paid' => 0
                                        ];
                                        foreach($invoices as $inv) {
                                            if(isset($statusCounts[$inv->status])) {
                                                $statusCounts[$inv->status]++;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Paid: {{ $statusCounts['paid'] }}</small>
                                        <small class="text-success">{{ round(($statusCounts['paid'] / max(1, $invoices->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Partially Paid: {{ $statusCounts['partially paid'] }}</small>
                                        <small class="text-warning">{{ round(($statusCounts['partially paid'] / max(1, $invoices->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small>Sent: {{ $statusCounts['sent'] }}</small>
                                        <small class="text-primary">{{ round(($statusCounts['sent'] / max(1, $invoices->count())) * 100) }}%</small>
                                    </div>
                                </div>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-warning" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Invoice payment status distribution</small>
                            </div>
                        </div>

                        <!-- Overdue Invoices Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-exclamation-triangle text-danger"></i> Overdue
                                    </span>
                                    <span class="text-danger">Attention</span>
                                </div>
                                @php
                                    $overdueCount = $invoices->filter(function($inv) {
                                        return \Carbon\Carbon::parse($inv->due_date)->isPast() && 
                                               in_array($inv->status, ['sent', 'partially paid']);
                                    })->count();
                                @endphp
                                <h3 class="mb-3 text-danger">{{ $overdueCount }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-danger" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Invoices past due date</small>
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
                                <h4 class="fw-bold mb-0">All Invoices</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openInvoiceFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="invoiceFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Invoices</h5>
                                    <button id="closeInvoiceFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('invoice.index') }}" id="filterForm">
                                    @if(request()->has('from_date') || request()->has('to_date') || request()->has('status') || request()->has('client_name'))
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
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partially paid" {{ request('status') == 'partially paid' ? 'selected' : '' }}>Partially Paid</option>
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
                                    <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Invoices Table -->
                            <div class="table-responsive">
                                @if($invoices->isEmpty())
                                    <div class="alert alert-light text-center py-4">
                                        <i class="fa fa-file-invoice fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Invoices Found</h5>
                                        <p class="text-muted">
                                            @if(request()->has('from_date') || request()->has('to_date') || request()->has('status') || request()->has('client_name'))
                                            No invoices found matching your filter criteria.
                                            @else
                                            No invoices have been created yet.
                                            @endif
                                        </p>
                                        @if(isset($permissions) && $permissions->can_create)
                                        <a href="{{ route('invoice.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus me-2"></i> Create Invoice
                                        </a>
                                        @endif
                                    </div>
                                @else
                                    <table class="table custom-table datatable mb-0">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Client</th>
                                                <th>Created Date</th>
                                                <th>Due Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoices as $inv)
                                                <tr id="invoice-row-{{ $inv->invoice_id }}">
                                                    <td data-label="Invoice ID">
                                                            {{ $inv->invoice_id }}
                                                    </td>
                                                    <td data-label="Client Name">
                                                        <span class="od-chip-highlight">{{ ucfirst($inv->client_name) }}</span>
                                                    </td>
                                                    <td data-label="Invoice Date">{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') }}</td>
                                                    <td data-label="Due Date">{{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</td>
                                                    <td data-label="Amount">₹{{ number_format($inv->grant_amt, 0) }}</td>
                                                    <td data-label="Status">
                                                        @php
                                                            $statusClass = [
                                                                'sent' => 'badge bg-primary',
                                                                'paid' => 'badge bg-success',
                                                                'partially paid' => 'badge bg-warning'
                                                            ][$inv->status] ?? 'badge bg-secondary';
                                                        @endphp
                                                        <span class="{{ $statusClass }}">
                                                            {{ ucfirst($inv->status) }}
                                                        </span>
                                                    </td>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <td data-label="Actions" class="text-end">
                                                        <div class="od-inline-actions">
                                                            @if($permissions->can_edit)
                                                            <a href="{{ route('invoice.edit', $inv->invoice_id) }}" class="od-icon-btn" title="Edit">
                                                                <i class="fa-solid fa-pencil"></i>
                                                            </a>
                                                            @endif
                                                    
                                                            <a href="javascript:void(0);" onclick="downloadPDF('{{ $inv->invoice_id }}')" class="od-icon-btn" title="Download PDF">
                                                                <i class="fa-solid fa-file-pdf"></i>
                                                            </a>
                                                    
                                                            <a href="javascript:void(0);" onclick="sendMail('{{ $inv->invoice_id }}')" class="od-icon-btn" title="Send Email">
                                                                <i class="fa-regular fa-envelope"></i>
                                                            </a>
                                                    
                                                            @if($permissions->can_delete)
                                                            <button type="button" class="od-icon-btn danger" onclick="deleteInvoice('{{ $inv->invoice_id }}')" title="Delete">
    <i class="fa-solid fa-trash"></i>
</button>

                                                            @endif
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $invoices->count() }} invoices
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 2 -->
<!-- =====================================================
        TAB 3 : INVOICE ACTIVITY LOG
====================================================== -->
<div class="tab-pane fade" id="activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-file-invoice me-2"></i> Invoice Activity Logs
                @if($logs && count($logs) > 0)
                    <small class="text-muted">({{ count($logs) }} records)</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
              
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="invoiceViewMode" id="invoiceListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="invoiceListView" onclick="switchInvoiceViewMode('list')">
                        <i class="fas fa-list"></i> 
                    <input type="radio" class="btn-check" name="invoiceViewMode" id="invoiceTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="invoiceTableView" onclick="switchInvoiceViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

    

        

        @if($logs && count($logs) > 0)
            <!-- List View -->
            <div id="invoiceListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @foreach ($logs as $index => $log)
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
                                            'email_sent' => 'fa-envelope text-secondary',
                                            'payment_received' => 'fa-credit-card text-success',
                                            'payment_failed' => 'fa-exclamation-circle text-danger'
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'fa-file-invoice text-info';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        <i class="fas fa-file-invoice me-1"></i>{{ $log->invoice_id }}
                                                    </a>
                                                    <span class="ms-2">- {{ $log->client_name ?? 'N/A' }}</span>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-rupee-sign me-1"></i>
                                                        ₹ {{ number_format($log->amount, 2) }}
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
                                                        'pdf_downloaded' => 'bg-info',
                                                        'csv_downloaded' => 'bg-info',
                                                        'email_sent'     => 'bg-secondary',
                                                        'payment_received' => 'bg-success',
                                                        'payment_failed' => 'bg-danger'
                                                    ][$log->action] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $cls }}">
                                                    {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                                </span>
                                                @if($log->status)
                                                    <br>
                                                    <span class="badge mt-1 bg-{{ 
                                                        $log->status == 'paid' ? 'success' : 
                                                        ($log->status == 'overdue' ? 'danger' : 
                                                        ($log->status == 'partially_paid' ? 'warning' : 'info')) 
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
                                                <div class="avatar-title" style="background-color: #6f42c1; color: white;">
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
                                                        @if($log->ip_address)
                                                            <small class="text-muted ms-3">
                                                                <i class="fas fa-globe me-1"></i> {{ $log->ip_address }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                       
                                                        @if($log->action == 'email_sent')
                                                            <button class="btn btn-sm btn-outline-success" onclick="resendInvoiceEmail('{{ $log->invoice_id }}')">
                                                                <i class="fas fa-paper-plane"></i> Resend
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyInvoiceDetails({{ json_encode($log) }})">
                                                <i class="fas fa-copy"></i> Copy Details
                                            </button>
                                            @if($log->action == 'pdf_downloaded')
                                                <button class="btn btn-sm btn-outline-danger" onclick="downloadInvoicePDF('{{ $log->invoice_id }}')">
                                                    <i class="fas fa-file-pdf"></i> Download PDF
                                                </button>
                                            @endif
                                            @if($log->action == 'csv_downloaded')
                                                <button class="btn btn-sm btn-outline-success" onclick="downloadInvoiceCSV('{{ $log->invoice_id }}')">
                                                    <i class="fas fa-file-csv"></i> Download CSV
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="invoiceTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="invoiceActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice ID</th>
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
                            @foreach ($logs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $log->invoice_id }}</strong></td>
                                <td>
                                    @php
                                        $cls = [
                                            'created'        => 'bg-success',
                                            'updated'        => 'bg-primary',
                                            'status_changed' => 'bg-warning',
                                            'deleted'        => 'bg-danger',
                                            'pdf_downloaded' => 'bg-info',
                                            'csv_downloaded' => 'bg-info',
                                            'email_sent'     => 'bg-secondary',
                                            'payment_received' => 'bg-success',
                                            'payment_failed' => 'bg-danger'
                                        ][$log->action] ?? 'bg-dark';
                                    @endphp
                                    <span class="badge {{ $cls }}">
                                        {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                    </span>
                                </td>
                                <td>{{ $log->client_name ?? 'N/A' }}</td>
                                <td>₹ {{ number_format($log->amount, 2) }}</td>
                                <td>
                                    @if($log->status)
                                        <span class="badge bg-{{ 
                                            $log->status == 'paid' ? 'success' : 
                                            ($log->status == 'overdue' ? 'danger' : 
                                            ($log->status == 'partially_paid' ? 'warning' : 'info')) 
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
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-eye me-2"></i> View Invoice
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="copyInvoiceDetails({{ json_encode($log) }})">
                                                    <i class="fas fa-copy me-2"></i> Copy Details
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-file-csv me-2"></i> Download CSV
                                                </a>
                                            </li>
                                            @if($log->action == 'email_sent')
                                            <li>
                                                <button class="dropdown-item" onclick="resendInvoiceEmail('{{ $log->invoice_id }}')">
                                                    <i class="fas fa-paper-plane me-2"></i> Resend Email
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteInvoiceLog({{ $log->id }})">
                                                    <i class="fas fa-trash me-2"></i> Delete Log
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Stats Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-3">Activity Summary</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $actionCounts = [];
                            $statusCounts = [];
                            $totalAmount = 0;
                            foreach ($logs as $log) {
                                $action = $log->action;
                                $actionCounts[$action] = ($actionCounts[$action] ?? 0) + 1;
                                
                                if ($log->status) {
                                    $statusCounts[$log->status] = ($statusCounts[$log->status] ?? 0) + 1;
                                }
                                
                                $totalAmount += $log->amount ?? 0;
                            }
                        @endphp
                        
                        <div class="summary-item">
                            <small class="text-muted d-block">Total Amount</small>
                            <strong class="text-success">₹ {{ number_format($totalAmount, 2) }}</strong>
                        </div>
                        
                        @foreach($actionCounts as $action => $count)
                            @php
                                $actionColors = [
                                    'created' => 'success',
                                    'updated' => 'primary',
                                    'status_changed' => 'warning',
                                    'deleted' => 'danger',
                                    'pdf_downloaded' => 'info',
                                    'csv_downloaded' => 'info',
                                    'email_sent' => 'secondary',
                                    'payment_received' => 'success',
                                    'payment_failed' => 'danger'
                                ];
                                $color = $actionColors[$action] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $color }} p-2">
                                {{ ucfirst(str_replace('_', ' ', $action)) }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="empty-state-title">No Invoice Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['invoice_action', 'invoice_client', 'invoice_status', 'invoice_date', 'invoice_search']))
                        No activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetInvoiceFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No invoice activity logs found yet.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<!-- END activity log tab -->

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
        border-left: 3px solid #6f42c1;
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

    .log-details {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        margin-right: 10px;
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
let invoiceActivityLogDataTable = null;

// View Mode Switching
function switchInvoiceViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('invoiceTableViewContent').classList.remove('d-none');
        document.getElementById('invoiceListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeInvoiceDataTable, 50);
    } else {
        document.getElementById('invoiceTableViewContent').classList.add('d-none');
        document.getElementById('invoiceListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (invoiceActivityLogDataTable !== null) {
            invoiceActivityLogDataTable.destroy();
            invoiceActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeInvoiceDataTable() {
    // Destroy existing DataTable if it exists
    if (invoiceActivityLogDataTable !== null) {
        invoiceActivityLogDataTable.destroy();
        invoiceActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('invoiceActivityLogTable');
    if (table && $('#invoiceActivityLogTable tbody tr').length > 0) {
        invoiceActivityLogDataTable = $('#invoiceActivityLogTable').DataTable({
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



// Remove individual filter
function removeInvoiceFilter(filterName) {
    const form = document.getElementById('invoiceLogFilterForm');
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

// Copy Invoice Details to Clipboard
function copyInvoiceDetails(log) {
    let textToCopy = `Invoice Activity Log Details:\n`;
    textToCopy += `Invoice ID: ${log.invoice_id}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Client: ${log.client_name || 'N/A'}\n`;
    textToCopy += `Amount: ₹ ${log.amount ? number_format(log.amount, 2) : '0.00'}\n`;
    textToCopy += `Status: ${log.status || 'N/A'}\n`;
    textToCopy += `Details: ${log.details}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Invoice details copied to clipboard',
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

// Helper function for number formatting
function number_format(number, decimals = 2) {
    number = parseFloat(number) || 0;
    return number.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Resend Invoice Email
function resendInvoiceEmail(invoiceId) {
    Swal.fire({
        title: 'Resend Invoice Email',
        text: 'Are you sure you want to resend this invoice email?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resend',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to resend invoice email
            Swal.fire({
                title: 'Email Sent!',
                text: 'Invoice email has been resent successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Download Invoice PDF
function downloadInvoicePDF(invoiceId) {
    // Add your download logic here
    window.location.href = `/invoices/${invoiceId}/download-pdf`;
}

// Download Invoice CSV
function downloadInvoiceCSV(invoiceId) {
    // Add your download logic here
    window.location.href = `/invoices/${invoiceId}/download-csv`;
}

// Delete Invoice Log
function deleteInvoiceLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this invoice activity log? This action cannot be undone.',
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
                text: 'Invoice activity log has been deleted.',
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
    switchInvoiceViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('invoice_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('invoiceLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('invoice_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('invoiceLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (invoiceActivityLogDataTable !== null) {
        invoiceActivityLogDataTable.columns.adjust();
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
        margin-top: 0;
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

    /* Status Badges */
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
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
        const openFilterBtn = document.getElementById('openInvoiceFilterBtn');
        const closeFilterBtn = document.getElementById('closeInvoiceFilterBtn');
        const filterPanel = document.getElementById('invoiceFilterPanel');

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
        const filterPanel = document.getElementById('invoiceFilterPanel');
        if (filterPanel) {
            filterPanel.classList.remove('active');
        }
        
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('invoice.index') }}";
    }

    function deleteInvoice(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This invoice will be deleted permanently!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('invoice.destroy', '') }}/" + id,
                type: "POST", // Laravel expects POST for method spoofing
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "DELETE"
                },
                success: function (res) {
                    Swal.fire({
                        icon: res.icon,
                        title: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    if (res.status === 1) {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to delete!',
                        text: xhr.responseJSON?.message || error || "Unknown error",
                    });
                }
            });
        }
    });
}


    function sendMail(id) {
    const url = "{{ route('invoice.send-email', ['id' => ':id']) }}".replace(':id', id);
    
    Swal.fire({
        title: 'Send Invoice Email',
        text: "Are you sure you want to send this invoice to the client?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, send it!',
        cancelButtonText: 'No, cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(url, {
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
                    text: "The invoice has been sent to the client.",
                    icon: "success",
                    timer: 2000
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: result.value?.message || "Failed to send email",
                    icon: "error"
                });
            }
        }
    });
}

    // Download PDF function
    function downloadPDF(invoiceId) {
        const url = `{{ route('invoice.pdf', ['id' => '__ID__']) }}`.replace('__ID__', invoiceId);

        const link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.download = `invoice_${invoiceId}.pdf`;
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();

        setTimeout(() => {
            document.body.removeChild(link);
        }, 100);
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