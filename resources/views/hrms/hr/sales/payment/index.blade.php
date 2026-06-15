@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Payments');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
  <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('payment.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Payment
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
                <i class="fa fa-chart-bar me-1"></i>Payments Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $payments->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#payment-activity-tab">
        Payment Activity Log
    </a>
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
                        <!-- Total Payments Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-credit-card text-primary"></i> Total Payments
                                    </span>
                                    <span class="text-success">{{ $payments->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $payments->total() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All payment records</small>
                            </div>
                        </div>

                        <!-- Total Amount Received Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-rupee-sign text-success"></i> Total Received
                                    </span>
                                    <span class="text-success">₹{{ number_format($totalAmount ?? 0, 0) }}</span>
                                </div>
                                <h3 class="mb-3 text-success">₹{{ number_format($totalAmount ?? 0, 0) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Total payment amount received</small>
                            </div>
                        </div>

                        <!-- Payment Methods Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-wallet text-warning"></i> Payment Methods
                                    </span>
                                    <span class="text-warning">Distribution</span>
                                </div>
                                <div class="mb-2">
                                    @php
                                        $methodCounts = [
                                            'cash' => 0,
                                            'cheque' => 0,
                                            'bank transfer' => 0,
                                            'credit card' => 0
                                        ];
                                        foreach($payments as $payment) {
                                            if(isset($methodCounts[$payment->payment_method])) {
                                                $methodCounts[$payment->payment_method]++;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Cash: {{ $methodCounts['cash'] }}</small>
                                        <small class="text-primary">{{ round(($methodCounts['cash'] / max(1, $payments->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Bank Transfer: {{ $methodCounts['bank transfer'] }}</small>
                                        <small class="text-success">{{ round(($methodCounts['bank transfer'] / max(1, $payments->count())) * 100) }}%</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small>Cheque/Card: {{ $methodCounts['cheque'] + $methodCounts['credit card'] }}</small>
                                        <small class="text-warning">{{ round((($methodCounts['cheque'] + $methodCounts['credit card']) / max(1, $payments->count())) * 100) }}%</small>
                                    </div>
                                </div>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-warning" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Payment method distribution</small>
                            </div>
                        </div>

                        <!-- Recent Payments Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-clock text-info"></i> Recent Payments
                                    </span>
                                    <span class="text-info">This Month</span>
                                </div>
                                @php
                                    $thisMonthCount = $payments->filter(function($payment) {
                                        return \Carbon\Carbon::parse($payment->payment_date)->isCurrentMonth();
                                    })->count();
                                @endphp
                                <h3 class="mb-3 text-info">{{ $thisMonthCount }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Payments received this month</small>
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
                                <h4 class="fw-bold mb-0">All Payments</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openPaymentFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="paymentFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Payments</h5>
                                    <button id="closePaymentFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('payment.index') }}" id="filterForm">
                                    @if(request()->has('invoice_id') || request()->has('client_name') || request()->has('payment_method') || request()->has('status') || request()->has('payment_date_from') || request()->has('payment_date_to'))
                                    <div class="mb-3">
                                        <div class="alert alert-info py-2">
                                            <small><i class="fa fa-info-circle me-1"></i> Active filters applied</small>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Invoice ID</label>
                                        <input type="text" name="invoice_id" value="{{ request('invoice_id') }}" 
                                               class="form-control" placeholder="Search by invoice ID...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Client Name</label>
                                        <input type="text" name="client_name" value="{{ request('client_name') }}" 
                                               class="form-control" placeholder="Search by client name...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Payment Method</label>
                                        <select name="payment_method" class="form-select">
                                            <option value="">All Methods</option>
                                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="bank transfer" {{ request('payment_method') == 'bank transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="credit card" {{ request('payment_method') == 'credit card' ? 'selected' : '' }}>Credit Card</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">From Date</label>
                                        <input type="date" name="payment_date_from" value="{{ request('payment_date_from') }}" 
                                               class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">To Date</label>
                                        <input type="date" name="payment_date_to" value="{{ request('payment_date_to') }}" 
                                               class="form-control">
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
                            @if(request()->has('invoice_id') || request()->has('client_name') || request()->has('payment_method') || request()->has('status') || request()->has('payment_date_from') || request()->has('payment_date_to'))
                            <div class="alert alert-light border mb-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted me-2">Active Filters:</small>
                                        @if(request('invoice_id'))
                                        <span class="badge bg-info me-2">
                                            Invoice: "{{ request('invoice_id') }}"
                                            <a href="?{{ http_build_query(request()->except('invoice_id')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('client_name'))
                                        <span class="badge bg-info me-2">
                                            Client: "{{ request('client_name') }}"
                                            <a href="?{{ http_build_query(request()->except('client_name')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('payment_method'))
                                        <span class="badge bg-info me-2">
                                            Method: {{ ucfirst(request('payment_method')) }}
                                            <a href="?{{ http_build_query(request()->except('payment_method')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('status'))
                                        <span class="badge bg-info me-2">
                                            Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                                            <a href="?{{ http_build_query(request()->except('status')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('payment_date_from'))
                                        <span class="badge bg-info me-2">
                                            From: {{ request('payment_date_from') }}
                                            <a href="?{{ http_build_query(request()->except('payment_date_from')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('payment_date_to'))
                                        <span class="badge bg-info me-2">
                                            To: {{ request('payment_date_to') }}
                                            <a href="?{{ http_build_query(request()->except('payment_date_to')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('payment.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Payments Table -->
                            <div class="table-responsive">
                                @if($payments->count())
                                    <table class="table custom-table datatable mb-0">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th>Client</th>
                                                <th>Amount</th>
                                                <th>Paid / Total</th>
                                                <th>Remaining</th>
                                                <th>Status</th>
                                                <th>Method</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr id="payment-row-{{ $payment->payment_id }}">
                                                    <td data-label="Payment ID">
                                                        <span class="high">{{ $payment->payment_id }}</span>
                                                    </td>
                                                    <td data-label="Invoice ID">{{ $payment->invoice_id }}</td>
                                                    <td data-label="Payment Date">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                                    <td data-label="Client Name">
                                                        <span class="od-chip-highlight">{{ ucfirst($payment->client_name) }}</span>
                                                    </td>
                                                    <td data-label="Amount">₹{{ number_format($payment->amount, 2) }}</td>
                                                    <td data-label="Paid / Total">{{ number_format($payment->total_paid, 2) }} / {{ number_format($payment->invoice_total, 2) }}</td>
                                                    <td data-label="Remaining Amount">₹{{ number_format($payment->remaining_amount, 2) }}</td>
                                                    <td data-label="Status">
                                                        @php
                                                            $statusClass = [
                                                                'paid' => 'od-chip-success',
                                                                'partially_paid' => 'od-chip-warning',
                                                                'pending' => 'od-chip-danger'
                                                            ][$payment->status] ?? 'od-chip-secondary';
                                                        @endphp
                                                        <span class="od-chip {{ $statusClass }}">
                                                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                                        </span>
                                                    </td>
                                                    <td data-label="Payment Method">{{ ucfirst($payment->payment_method) }}</td>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <td data-label="Actions" class="text-end">
                                                        <div class="od-inline-actions">
                                                            @if($permissions->can_edit)
                                                            <a href="{{ route('payment.edit', $payment->payment_id) }}" class="od-icon-btn" title="Edit">
                                                                <i class="fa-solid fa-pencil"></i>
                                                            </a>
                                                            @endif
                                                    
                                                            @if($permissions->can_delete)
                                                            <button type="button" class="od-icon-btn danger" onclick="deleteData('{{ $payment->payment_id }}')" title="Delete">
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
                                @else
                                    <div class="alert alert-light text-center py-4">
                                        <i class="fa fa-credit-card fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Payments Found</h5>
                                        <p class="text-muted">
                                            @if(request()->has('invoice_id') || request()->has('client_name') || request()->has('payment_method') || request()->has('status') || request()->has('payment_date_from') || request()->has('payment_date_to'))
                                            No payments found matching your filter criteria.
                                            @else
                                            No payments have been recorded yet.
                                            @endif
                                        </p>
                                        @if(isset($permissions) && $permissions->can_create)
                                        <a href="{{ route('payment.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus me-2"></i> Add Payment
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Pagination -->
                            @if($payments->hasPages())
                            <div class="mt-3">
                                {{ $payments->appends(request()->query())->links() }}
                            </div>
                            @endif

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $payments->firstItem() ?? 0 }}-{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} payments
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 2 -->

   <!-- =====================================================
        TAB : PAYMENT ACTIVITY LOG
====================================================== -->
<div class="tab-pane fade" id="payment-activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-credit-card me-2"></i> Payment Activity Logs
                @if($paymentLogs && count($paymentLogs) > 0)
                    <small class="text-muted">({{ count($paymentLogs) }} records)</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
              
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="paymentViewMode" id="paymentListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="paymentListView" onclick="switchPaymentViewMode('list')">
                        <i class="fas fa-list"></i> 
                    </label>
                    <input type="radio" class="btn-check" name="paymentViewMode" id="paymentTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="paymentTableView" onclick="switchPaymentViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

     
        @if($paymentLogs && count($paymentLogs) > 0)
            <!-- List View -->
            <div id="paymentListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @foreach ($paymentLogs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    @php
                                        $actionIcons = [
                                            'created' => 'fa-plus-circle text-success',
                                            'updated' => 'fa-edit text-primary',
                                            'status_changed' => 'fa-exchange-alt text-warning',
                                            'deleted' => 'fa-trash text-danger',
                                            'refunded' => 'fa-undo text-danger',
                                            'failed' => 'fa-times-circle text-danger',
                                            'cancelled' => 'fa-ban text-secondary'
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'fa-credit-card text-success';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        <i class="fas fa-credit-card me-1"></i>
                                                        @if($log->payment_id && $log->payment_id != 'N/A')
                                                            {{ $log->payment_id }}
                                                        @else
                                                            Payment #{{ $index + 1 }}
                                                        @endif
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
                                                    @if($log->invoice_id)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-file-invoice me-1"></i> {{ $log->invoice_id }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $cls = [
                                                        'created'        => 'bg-success',
                                                        'updated'        => 'bg-primary',
                                                        'status_changed' => 'bg-warning',
                                                        'deleted'        => 'bg-danger',
                                                        'refunded'       => 'bg-danger',
                                                        'failed'         => 'bg-danger',
                                                        'cancelled'      => 'bg-secondary'
                                                    ][$log->action] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $cls }}">
                                                    {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                                </span>
                                                @if($log->status)
                                                    <br>
                                                    <span class="badge mt-1 bg-{{ 
                                                        $log->status == 'completed' ? 'success' : 
                                                        ($log->status == 'failed' ? 'danger' : 
                                                        ($log->status == 'pending' ? 'warning' : 'info')) 
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
                                                <div class="avatar-title" style="background-color: #28a745; color: white;">
                                                    <i class="fas fa-money-bill-wave"></i>
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
                                                      
                                                        @if($log->status == 'completed')
                                                            <button class="btn btn-sm btn-outline-success" onclick="generateReceipt('{{ $log->payment_id }}')">
                                                                <i class="fas fa-receipt"></i> Receipt
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyPaymentDetails({{ json_encode($log) }})">
                                                <i class="fas fa-copy"></i> Copy Details
                                            </button>
                                            @if($log->status == 'pending')
                                                <button class="btn btn-sm btn-outline-warning" onclick="markAsCompleted('{{ $log->payment_id }}')">
                                                    <i class="fas fa-check"></i> Mark Complete
                                                </button>
                                            @endif
                                            @if($log->status == 'completed')
                                                <button class="btn btn-sm btn-outline-danger" onclick="initiateRefund('{{ $log->payment_id }}')">
                                                    <i class="fas fa-undo"></i> Refund
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
            <div id="paymentTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="paymentActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Payment ID</th>
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
                            @foreach ($paymentLogs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($log->payment_id && $log->payment_id != 'N/A')
                                        <strong>{{ $log->payment_id }}</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td><strong>{{ $log->invoice_id }}</strong></td>
                                <td>
                                    @php
                                        $cls = [
                                            'created'        => 'bg-success',
                                            'updated'        => 'bg-primary',
                                            'status_changed' => 'bg-warning',
                                            'deleted'        => 'bg-danger',
                                            'refunded'       => 'bg-danger',
                                            'failed'         => 'bg-danger',
                                            'cancelled'      => 'bg-secondary'
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
                                            $log->status == 'completed' ? 'success' : 
                                            ($log->status == 'failed' ? 'danger' : 
                                            ($log->status == 'pending' ? 'warning' : 'info')) 
                                        }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                               
                                <td>
                                    <div class="log-details" title="{{ $log->details }}">
                                        {{ Str::limit($log->details, 40) }}
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
                                                    <i class="fas fa-eye me-2"></i> View Payment
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-file-invoice me-2"></i> View Invoice
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="copyPaymentDetails({{ json_encode($log) }})">
                                                    <i class="fas fa-copy me-2"></i> Copy Details
                                                </button>
                                            </li>
                                            @if($log->status == 'completed')
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-receipt me-2"></i> Generate Receipt
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="initiateRefund('{{ $log->payment_id }}')">
                                                    <i class="fas fa-undo me-2"></i> Refund Payment
                                                </button>
                                            </li>
                                            @endif
                                            @if($log->status == 'pending')
                                            <li>
                                                <button class="dropdown-item" onclick="markAsCompleted('{{ $log->payment_id }}')">
                                                    <i class="fas fa-check me-2"></i> Mark as Completed
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deletePaymentLog({{ $log->id }})">
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
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-3">Payment Summary</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $actionCounts = [];
                                    $statusCounts = [];
                                    $totalAmount = 0;
                                    $completedAmount = 0;
                                    $pendingAmount = 0;
                                    
                                    foreach ($paymentLogs as $log) {
                                        $action = $log->action;
                                        $actionCounts[$action] = ($actionCounts[$action] ?? 0) + 1;
                                        
                                        if ($log->status) {
                                            $statusCounts[$log->status] = ($statusCounts[$log->status] ?? 0) + 1;
                                        }
                                        
                                        $totalAmount += $log->amount ?? 0;
                                        
                                        if ($log->status == 'completed') {
                                            $completedAmount += $log->amount ?? 0;
                                        } elseif ($log->status == 'pending') {
                                            $pendingAmount += $log->amount ?? 0;
                                        }
                                    }
                                @endphp
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Total Amount</small>
                                    <strong class="text-success">₹ {{ number_format($totalAmount, 2) }}</strong>
                                </div>
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Completed</small>
                                    <strong class="text-success">₹ {{ number_format($completedAmount, 2) }}</strong>
                                </div>
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Pending</small>
                                    <strong class="text-warning">₹ {{ number_format($pendingAmount, 2) }}</strong>
                                </div>
                                
                                @foreach($actionCounts as $action => $count)
                                    @php
                                        $actionColors = [
                                            'created' => 'success',
                                            'updated' => 'primary',
                                            'status_changed' => 'warning',
                                            'deleted' => 'danger',
                                            'refunded' => 'danger',
                                            'failed' => 'danger',
                                            'cancelled' => 'secondary'
                                        ];
                                        $color = $actionColors[$action] ?? 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $color }} p-2">
                                        {{ ucfirst(str_replace('_', ' ', $action)) }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-3">Status Distribution</h6>
                            @foreach($statusCounts as $status => $count)
                                @php
                                    $statusColors = [
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $color = $statusColors[$status] ?? 'info';
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    <strong>{{ $count }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-credit-card"></i></div>
                <div class="empty-state-title">No Payment Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['payment_action', 'payment_client', 'payment_status', 'payment_date', 'payment_invoice', 'payment_method']))
                        No payment activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetPaymentFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No payment activity logs found yet.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<!-- END payment activity log tab -->

<style>
    /* Payment Activity Log Styles */
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
        border-left: 3px solid #28a745;
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
let paymentActivityLogDataTable = null;

// View Mode Switching
function switchPaymentViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('paymentTableViewContent').classList.remove('d-none');
        document.getElementById('paymentListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializePaymentDataTable, 50);
    } else {
        document.getElementById('paymentTableViewContent').classList.add('d-none');
        document.getElementById('paymentListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (paymentActivityLogDataTable !== null) {
            paymentActivityLogDataTable.destroy();
            paymentActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializePaymentDataTable() {
    // Destroy existing DataTable if it exists
    if (paymentActivityLogDataTable !== null) {
        paymentActivityLogDataTable.destroy();
        paymentActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('paymentActivityLogTable');
    if (table && $('#paymentActivityLogTable tbody tr').length > 0) {
        paymentActivityLogDataTable = $('#paymentActivityLogTable').DataTable({
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
function resetPaymentFilters() {
    const form = document.getElementById('paymentLogFilterForm');
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
function removePaymentFilter(filterName) {
    const form = document.getElementById('paymentLogFilterForm');
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

// Copy Payment Details to Clipboard
function copyPaymentDetails(log) {
    let textToCopy = `Payment Activity Log Details:\n`;
    textToCopy += `Payment ID: ${log.payment_id || 'N/A'}\n`;
    textToCopy += `Invoice ID: ${log.invoice_id}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Client: ${log.client_name || 'N/A'}\n`;
    textToCopy += `Amount: ₹ ${log.amount ? number_format(log.amount, 2) : '0.00'}\n`;
    textToCopy += `Status: ${log.status || 'N/A'}\n`;
    textToCopy += `Payment Method: ${log.payment_method || 'N/A'}\n`;
    textToCopy += `Details: ${log.details}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Payment details copied to clipboard',
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

// Generate Receipt
function generateReceipt(paymentId) {
    // Add your receipt generation logic here
    window.open(`/payments/${paymentId}/receipt`, '_blank');
}

// Mark Payment as Completed
function markAsCompleted(paymentId) {
    Swal.fire({
        title: 'Mark as Completed',
        text: 'Are you sure you want to mark this payment as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark complete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to mark payment as completed
            Swal.fire({
                title: 'Completed!',
                text: 'Payment has been marked as completed.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Initiate Refund
function initiateRefund(paymentId) {
    Swal.fire({
        title: 'Initiate Refund',
        text: 'Are you sure you want to initiate a refund for this payment?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, refund',
        cancelButtonText: 'Cancel',
        input: 'number',
        inputLabel: 'Refund Amount',
        inputPlaceholder: 'Enter refund amount',
        inputAttributes: {
            step: '0.01',
            min: '0'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to process refund
            Swal.fire({
                title: 'Refund Initiated!',
                text: 'Refund has been initiated successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Delete Payment Log
function deletePaymentLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this payment activity log? This action cannot be undone.',
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
                text: 'Payment activity log has been deleted.',
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
    switchPaymentViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('payment_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('paymentLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('payment_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('paymentLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (paymentActivityLogDataTable !== null) {
        paymentActivityLogDataTable.columns.adjust();
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

    /* Status Chips */
    .od-chip-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .od-chip-warning {
        background-color: #fff3cd;
        color: #664d03;
    }
    
    .od-chip-danger {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .od-chip {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
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
        const openFilterBtn = document.getElementById('openPaymentFilterBtn');
        const closeFilterBtn = document.getElementById('closePaymentFilterBtn');
        const filterPanel = document.getElementById('paymentFilterPanel');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
                // Pre-fill filter values from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const invoiceInput = document.querySelector('input[name="invoice_id"]');
                const clientInput = document.querySelector('input[name="client_name"]');
                const methodSelect = document.querySelector('select[name="payment_method"]');
                const statusSelect = document.querySelector('select[name="status"]');
                const fromDateInput = document.querySelector('input[name="payment_date_from"]');
                const toDateInput = document.querySelector('input[name="payment_date_to"]');
                
                if (invoiceInput && urlParams.has('invoice_id')) {
                    invoiceInput.value = urlParams.get('invoice_id');
                }
                if (clientInput && urlParams.has('client_name')) {
                    clientInput.value = urlParams.get('client_name');
                }
                if (methodSelect && urlParams.has('payment_method')) {
                    methodSelect.value = urlParams.get('payment_method');
                }
                if (statusSelect && urlParams.has('status')) {
                    statusSelect.value = urlParams.get('status');
                }
                if (fromDateInput && urlParams.has('payment_date_from')) {
                    fromDateInput.value = urlParams.get('payment_date_from');
                }
                if (toDateInput && urlParams.has('payment_date_to')) {
                    toDateInput.value = urlParams.get('payment_date_to');
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
        const filterPanel = document.getElementById('paymentFilterPanel');
        if (filterPanel) {
            filterPanel.classList.remove('active');
        }
        
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('payment.index') }}";
    }

    // Delete payment function
    function deleteData(id) {
        Swal.fire({
            title: "Are you sure you want to delete this Payment?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(`{{ route('payment.destroy', '') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Payment deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            // Remove the table row
                            document.getElementById(`payment-row-${id}`).remove();
                            // Show updated count
                            const rowCount = document.querySelectorAll('tbody tr').length;
                            if (rowCount === 0) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.message || "Failed to delete.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error",
                        text: "Failed to delete payment.",
                        icon: "error"
                    });
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