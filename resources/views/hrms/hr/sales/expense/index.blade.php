@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Travel');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">

   <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{route('expense.create')}}" class="btn add-btn"> Initiate Travel</a>
                @endif
            </div>
    <!-- =====================================================
         ZOHO STYLE TABS
    ====================================================== -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#expenses-tab">Travel Claims</a>
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
             TAB 1 : EXPENSES LIST (Your original content)
        ====================================================== -->
        <div class="tab-pane fade show active" id="expenses-tab">
            <form action="{{ route('expense.index') }}" method="GET">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                            <input type="text" class="form-control floating" name="customer_name" value="{{ request()->get('customer_name') }}">
                            <label class="focus-label">Customer Name</label>
                        </div>
                    </div>
            
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" name="from_date" value="{{ request()->get('from_date') }}">
                            </div>
                            <label class="focus-label">From Departure Date</label>
                        </div>
                    </div>
            
                    <div class="col-sm-6 col-md-3">  
                        <div class="input-block mb-3 form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" name="to_date" value="{{ request()->get('to_date') }}">
                            </div>
                            <label class="focus-label">To Departure Date</label>
                        </div>
                    </div>
            
                    <div class="col-sm-6 col-md-3"> 
                        <div class="input-block mb-3 form-focus select-focus">
                            <select class="select floating" name="status"> 
                                <option value="">All Status</option>
                                <option value="approved" {{ request()->get('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request()->get('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <label class="focus-label">Status</label>
                        </div>
                    </div>
            
                    <div class="col-sm-6 col-md-3">  
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success w-100">Search</button>
                        </div>
                    </div>     
                </div>
            </form>

            <div class="row">
                @if (Session::has('messageType') && Session::has('message'))
                <div class="alert alert-{{ Session::get('messageType') }} alert-dismissible fade show" role="alert">
                     {{ Session::get('message') }}
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="table-responsive">
                        @if ($expenses->count())
                            <table id="expense-table" class="table custom-table datatable">
                                <thead>
                                    <tr>
                                        <th>Expense ID</th>
                                        <th>Employee ID</th>
                                        <th>Departure Date</th>
                                        <th>Purpose</th>
                                        <th>Amount</th>
                                        <th>Customer</th>
                                        <th>Billable</th>
                                        <th>Status</th>
                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                        <th class="text-end">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $item)
                                        <tr id="expense-row-{{ $item->expense_id }}">
                                            <td><strong>{{ $item->expense_id }}</strong></td>
                                            <td>{{ $item->employee_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->departure_date)->format('d M Y') }}</td>
                                            <td>{{ Str::limit($item->purpose_of_visit, 30) }}</td>
                                            <td>{{ $item->currency }} {{ number_format($item->expense_amount, 2) }}</td>
                                            <td>{{ $item->customer_name ?: 'N/A' }}</td>
                                            <td>
                                                @if($item->is_billable)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'approved' => 'od-chip-success',
                                                        'pending' => 'od-chip-warning',
                                                        'rejected' => 'od-chip-danger'
                                                    ][$item->status] ?? 'od-chip-secondary';
                                                @endphp
                                                <div class="dropdown">
                                                    <button class="od-chip {{ $statusClass }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-regular fa-circle-dot me-1"></i>{{ ucfirst($item->status) }}
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item {{ $item->status == 'approved' ? 'disabled' : '' }}" 
                                                                onclick="statusChange('{{ $item->expense_id }}', 'approved')">
                                                                <i class="fa-regular fa-circle-dot text-success me-1"></i> Approved
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item {{ $item->status == 'pending' ? 'disabled' : '' }}" 
                                                                onclick="statusChange('{{ $item->expense_id }}', 'pending')">
                                                                <i class="fa-regular fa-circle-dot text-warning me-1"></i> Pending
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item {{ $item->status == 'rejected' ? 'disabled' : '' }}" 
                                                                onclick="statusChange('{{ $item->expense_id }}', 'rejected')">
                                                                <i class="fa-regular fa-circle-dot text-danger me-1"></i> Rejected
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="od-inline-actions">
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                    <a href="{{ route('expense.edit', $item->expense_id) }}" class="od-icon-btn" title="Edit">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                    @endif
            
                                                    @if($item->upload_receipt)
                                                    <a href="{{ route('expense.download', $item->expense_id) }}" class="od-icon-btn" title="Download Receipt">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                    @endif
            
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                    <button type="button" class="od-icon-btn danger" onclick="deleteData('{{ $item->expense_id }}')" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-light text-center">No travel claims found</div>
                        @endif
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 1 -->

       <!-- =====================================================
        TAB 2 : ACTIVITY LOG (Dynamic Data from Database)
====================================================== -->
<div class="tab-pane fade" id="activity-tab">
    <div class="card p-3">
        <!-- Header with Filter and View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-money-bill-wave me-2"></i> Expense Activity Logs
                @if($logs && count($logs) > 0)
                    <small class="text-muted">({{ count($logs) }} records)</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
                
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="expenseViewMode" id="expenseListView" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="expenseListView" onclick="switchExpenseViewMode('list')">
                        <i class="fas fa-list"></i> 
                    </label>
                    <input type="radio" class="btn-check" name="expenseViewMode" id="expenseTableView" autocomplete="off">
                    <label class="btn btn-outline-primary" for="expenseTableView" onclick="switchExpenseViewMode('table')">
                        <i class="fas fa-table"></i> 
                    </label>
                </div>
            </div>
        </div>

       

       
        @if($logs && count($logs) > 0)
            <!-- List View -->
            <div id="expenseListViewContent" class="view-content">
                <div class="activity-log-container">
                    <div class="timeline">
                        @foreach ($logs as $index => $log)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    @php
                                        $actionIcons = [
                                            'created' => 'fa-plus-circle text-success',
                                            'updated' => 'fa-edit text-primary',
                                            'edited' => 'fa-edit text-primary',
                                            'status_changed' => 'fa-exchange-alt text-warning',
                                            'deleted' => 'fa-trash text-danger',
                                            'rejected' => 'fa-times-circle text-danger',
                                            'approved' => 'fa-check-circle text-success',
                                            'downloaded' => 'fa-download text-info',
                                            'submitted' => 'fa-paper-plane text-info'
                                        ];
                                        $icon = $actionIcons[strtolower($log->action)] ?? 'fa-money-bill-wave text-warning';
                                    @endphp
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="#" class="text-primary fw-bold">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i>
                                                        {{ $log->expense_id ?? 'N/A' }}
                                                    </a>
                                                    <span class="ms-2">- Employee: {{ $log->employee_id ?? 'N/A' }}</span>
                                                </h6>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                                    </span>
                                                    @if($log->amount)
                                                        <span class="badge bg-light text-dark border">
                                                            
                                                            {{ number_format($log->amount, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $actionBadgeClass = [
                                                        'created' => 'bg-success',
                                                        'updated' => 'bg-primary',
                                                        'edited' => 'bg-primary',
                                                        'status_changed' => 'bg-warning',
                                                        'deleted' => 'bg-danger',
                                                        'rejected' => 'bg-danger',
                                                        'approved' => 'bg-success',
                                                        'downloaded' => 'bg-info',
                                                        'submitted' => 'bg-info'
                                                    ][strtolower($log->action)] ?? 'bg-secondary';
                                                    
                                                    $actionLabel = ucfirst(str_replace('_', ' ', $log->action));
                                                @endphp
                                                <span class="badge {{ $actionBadgeClass }}">
                                                    {{ $actionLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title" style="background-color: #ffc107; color: #212529;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-2"><strong>Details:</strong> {{ $log->details ?? 'No details provided' }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-tag me-1"></i> {{ $log->performed_by ?? 'System' }}
                                                        </small>
                                                      
                                                      
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        
                                                        @if(strtolower($log->action) == 'downloaded')
                                                            <button class="btn btn-sm btn-outline-success" onclick="downloadExpenseAgain('{{ $log->expense_id }}')">
                                                                <i class="fas fa-download"></i> Download
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="copyExpenseDetails({{ json_encode($log) }})">
                                                <i class="fas fa-copy"></i> Copy Details
                                            </button>
                                            @if(in_array(strtolower($log->action), ['approved', 'submitted']))
                                                <button class="btn btn-sm btn-outline-warning" onclick="viewExpenseReport('{{ $log->expense_id }}')">
                                                    <i class="fas fa-chart-bar"></i> View Report
                                                </button>
                                            @endif
                                            @if(strtolower($log->action) == 'rejected')
                                                <button class="btn btn-sm btn-outline-danger" onclick="resubmitExpense('{{ $log->expense_id }}')">
                                                    <i class="fas fa-redo"></i> Resubmit
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
            <div id="expenseTableViewContent" class="view-content d-none">
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="expenseActivityLogTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Expense ID</th>
                                <th>Action</th>
                                <th>Employee ID</th>
                                <th>Amount</th>
                               
                                <th>Details</th>
                                <th>Date & Time</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $log->expense_id ?? 'N/A' }}</strong></td>
                                <td>
                                    @php
                                        $actionBadgeClass = [
                                            'created' => 'bg-success',
                                            'updated' => 'bg-primary',
                                            'edited' => 'bg-primary',
                                            'status_changed' => 'bg-warning',
                                            'deleted' => 'bg-danger',
                                            'rejected' => 'bg-danger',
                                            'approved' => 'bg-success',
                                            'downloaded' => 'bg-info'
                                        ][strtolower($log->action)] ?? 'bg-secondary';
                                        
                                        $actionLabel = ucfirst(str_replace('_', ' ', $log->action));
                                    @endphp
                                    <span class="badge {{ $actionBadgeClass }}">{{ $actionLabel }}</span>
                                </td>
                                <td>{{ $log->employee_id ?? 'N/A' }}</td>
                                <td>
                                    @if($log->amount)
                                        <strong>{{ number_format($log->amount, 2) }}</strong>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="log-details" title="{{ $log->details ?? 'No details provided' }}">
                                        {{ Str::limit($log->details ?? 'No details provided', 50) }}
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->action_date)->format('d M Y H:i') }}</td>
                              
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
                            <h6 class="mb-3">Expense Activity Summary</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $actionCounts = [];
                                    $totalAmount = 0;
                                    $approvedAmount = 0;
                                    $pendingAmount = 0;
                                    $employeeCounts = [];
                                    
                                    foreach ($logs as $log) {
                                        $action = strtolower($log->action);
                                        $actionCounts[$action] = ($actionCounts[$action] ?? 0) + 1;
                                        
                                        if ($log->amount) {
                                            $totalAmount += $log->amount;
                                            
                                            if ($action == 'approved') {
                                                $approvedAmount += $log->amount;
                                            } elseif (in_array($action, ['created', 'submitted'])) {
                                                $pendingAmount += $log->amount;
                                            }
                                        }
                                        
                                        if ($log->employee_id) {
                                            $employeeCounts[$log->employee_id] = ($employeeCounts[$log->employee_id] ?? 0) + 1;
                                        }
                                    }
                                    
                                    $uniqueEmployees = count($employeeCounts);
                                @endphp
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Total Amount</small>
                                    <strong class="text-success">{{ number_format($totalAmount, 2) }}</strong>
                                </div>
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Approved Amount</small>
                                    <strong class="text-success">{{ number_format($approvedAmount, 2) }}</strong>
                                </div>
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Pending Amount</small>
                                    <strong class="text-warning">{{ number_format($pendingAmount, 2) }}</strong>
                                </div>
                                
                                <div class="summary-item">
                                    <small class="text-muted d-block">Employees</small>
                                    <strong class="text-info">{{ $uniqueEmployees }}</strong>
                                </div>
                                
                                @foreach($actionCounts as $action => $count)
                                    @php
                                        $actionColors = [
                                            'created' => 'info',
                                            'updated' => 'primary',
                                            'edited' => 'primary',
                                            'status_changed' => 'warning',
                                            'deleted' => 'danger',
                                            'rejected' => 'danger',
                                            'approved' => 'success',
                                            'downloaded' => 'info',
                                            'submitted' => 'info'
                                        ];
                                        $color = $actionColors[$action] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }} p-2">
                                        {{ ucfirst(str_replace('_', ' ', $action)) }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-3">Top Employees</h6>
                            @php
                                arsort($employeeCounts);
                                $topEmployees = array_slice($employeeCounts, 0, 5, true);
                            @endphp
                            @foreach($topEmployees as $employeeId => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-truncate" style="max-width: 100px;">{{ $employeeId }}</span>
                                    <strong>{{ $count }} activities</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="empty-state-title">No Expense Activity Logs</div>
                <div class="empty-state-text">
                    @if(request()->hasAny(['expense_action', 'expense_employee', 'expense_date', 'expense_search', 'expense_min_amount', 'expense_max_amount']))
                        No expense activity logs found with the current filters.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="resetExpenseFilters()">
                                Clear Filters
                            </button>
                        </div>
                    @else
                        No expense activity logs found yet.
                    @endif
                </div>
            </div>
        @endif

        <!-- Debug info (remove in production) -->
        @if(app()->environment('local') || app()->environment('development'))
        <div class="mt-3 p-2 border rounded bg-light">
            <small class="text-muted">Debug: {{ $logs->count() }} logs found</small>
        </div>
        @endif
    </div>
</div> <!-- END TAB 2 -->

<style>
    /* Expense Activity Log Styles */
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
        border-left: 3px solid #ffc107;
    }

    .timeline-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
       
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
let expenseActivityLogDataTable = null;

// View Mode Switching
function switchExpenseViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('expenseTableViewContent').classList.remove('d-none');
        document.getElementById('expenseListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializeExpenseDataTable, 50);
    } else {
        document.getElementById('expenseTableViewContent').classList.add('d-none');
        document.getElementById('expenseListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (expenseActivityLogDataTable !== null) {
            expenseActivityLogDataTable.destroy();
            expenseActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializeExpenseDataTable() {
    // Destroy existing DataTable if it exists
    if (expenseActivityLogDataTable !== null) {
        expenseActivityLogDataTable.destroy();
        expenseActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('expenseActivityLogTable');
    if (table && $('#expenseActivityLogTable tbody tr').length > 0) {
        expenseActivityLogDataTable = $('#expenseActivityLogTable').DataTable({
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
function resetExpenseFilters() {
    const form = document.getElementById('expenseLogFilterForm');
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
function removeExpenseFilter(filterName) {
    const form = document.getElementById('expenseLogFilterForm');
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

// Copy Expense Details to Clipboard
function copyExpenseDetails(log) {
    let textToCopy = `Expense Activity Log Details:\n`;
    textToCopy += `Expense ID: ${log.expense_id || 'N/A'}\n`;
    textToCopy += `Action: ${log.action}\n`;
    textToCopy += `Employee ID: ${log.employee_id || 'N/A'}\n`;
    textToCopy += `Amount: ${log.amount ? number_format(log.amount, 2) : 'N/A'}\n`;
    textToCopy += `Department: ${log.department || 'N/A'}\n`;
    textToCopy += `Details: ${log.details || 'No details provided'}\n`;
    textToCopy += `Performed By: ${log.performed_by || 'System'}\n`;
    textToCopy += `Date & Time: ${log.action_date}\n`;
    
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Expense details copied to clipboard',
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

// Download Expense Again
function downloadExpenseAgain(expenseId) {
    // Add your download logic here
    window.location.href = `/expenses/${expenseId}/download`;
}

// View Expense Report
function viewExpenseReport(expenseId) {
    // Add your report viewing logic here
    window.open(`/expenses/${expenseId}/report`, '_blank');
}

// Resubmit Expense
function resubmitExpense(expenseId) {
    Swal.fire({
        title: 'Resubmit Expense',
        text: 'Are you sure you want to resubmit this rejected expense?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resubmit',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add your AJAX call here to resubmit expense
            Swal.fire({
                title: 'Resubmitted!',
                text: 'Expense has been resubmitted successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Delete Expense Log
function deleteExpenseLog(logId) {
    Swal.fire({
        title: 'Delete Activity Log',
        text: 'Are you sure you want to delete this expense activity log? This action cannot be undone.',
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
                text: 'Expense activity log has been deleted.',
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
    switchExpenseViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('expense_')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('expenseLogFilters'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('expense_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('expenseLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (expenseActivityLogDataTable !== null) {
        expenseActivityLogDataTable.columns.adjust();
    }
});
</script>

    </div> <!-- END tab-content -->
</div>

<script>
    // Auto-close alerts
    setTimeout(function() {
        $(".alert-dismissible").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); });
    }, 3000);

    function deleteData(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('expense') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#expense-row-' + id).remove();
                            Swal.fire({ title: 'Deleted!', text: response.message, icon: 'success', confirmButtonText: 'OK' });
                        } else {
                            Swal.fire({ title: 'Error!', text: response.message, icon: 'error', confirmButtonText: 'OK' });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({ title: 'Error!', text: 'Something went wrong!', icon: 'error', confirmButtonText: 'OK' });
                    }
                });
            }
        });
    }

function statusChange(id, status) {
    Swal.fire({
        title: 'Change Status?',
        text: `Are you sure you want to mark this expense as ${status}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('expense-changeStatus', ':id') }}".replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('AJAX Error:', xhr.responseText);
                    
                    let errorMessage = 'Failed to update status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Route not found. Please check your route configuration.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please check your logs.';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
<style>
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

.tabs-underline {
    width: 100%;
    height: 2px;
    background: #e5eaf2;
    margin-top: -4px;
    margin-bottom: 12px;
}
</style>
@endsection