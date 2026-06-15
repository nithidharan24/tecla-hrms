@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Expense Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Expense Report</li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div id="successMessage" class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search Filter -->
        <div class="row filter-row">
            <form method="GET" action="{{ route('expenses-reports.index') }}" class="row g-3">
                <div class="col-sm-6 col-md-3"> 
                    <div class="input-block mb-3 form-focus select-focus">
                        <select name="employee_id" class="select floating"> 
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ (request('employee_id') == $employee->id && request()->has('employee_id')) ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->id }})
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Employee</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3"> 
                    <div class="input-block mb-3 form-focus select-focus">
                        <select name="purchased_by" class="select floating"> 
                            <option value="">Select Buyer</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer }}" {{ request('purchased_by') == $buyer ? 'selected' : '' }}>
                                    {{ $buyer }}
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Purchased By</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2">  
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <input name="from" value="{{ request('from') }}" class="form-control floating datetimepicker" type="text" placeholder="DD-MM-YYYY">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2">  
                    <div class="input-block mb-3 form-focus">
                        <div class="cal-icon">
                            <input name="to" value="{{ request('to') }}" class="form-control floating datetimepicker" type="text" placeholder="DD-MM-YYYY">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2">  
                    <button type="submit" class="btn btn-success w-100"> Search </button>
                    @if(request()->hasAny(['employee_id', 'purchased_by', 'from', 'to']))
                        <a href="{{ route('expenses-reports.index') }}" class="btn btn-secondary w-100 mt-2">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Export Buttons -->
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('expenses-reports.export.csv', request()->query()) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export to CSV
                </a>
                <a href="{{ route('expenses-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Expense ID</th>
                                <th>Item / Purpose</th>
                                <th>Purchase From / Place</th>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Purchased By</th>
                                <th>Amount</th>
                                <th>Paid By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                            <tr>
                                <td>
                                    {{ $expense->expense_id ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($expense->item_name && $expense->item_name != 'Travel Expense')
                                        {{ $expense->item_name }}
                                    @elseif($expense->purpose_of_visit)
                                        {{ $expense->purpose_of_visit }}
                                        @if($expense->place_of_visit)
                                            <br><small class="text-muted">📍 {{ $expense->place_of_visit }}</small>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($expense->purchase_from)
                                        <span class="od-chip-highlight">{{ $expense->purchase_from }}</span>
                                    @elseif($expense->place_of_visit)
                                        <span class="od-chip-highlight">{{ $expense->place_of_visit }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($expense->purchase_date)
                                        {{ \Carbon\Carbon::parse($expense->purchase_date)->format('d-m-Y') }}
                                    @elseif($expense->departure_date)
                                        {{ \Carbon\Carbon::parse($expense->departure_date)->format('d-m-Y') }}
                                        @if($expense->arrival_date && $expense->arrival_date != $expense->departure_date)
                                            <br><small>to {{ \Carbon\Carbon::parse($expense->arrival_date)->format('d-m-Y') }}</small>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $employeeInfo = isset($employeeDetails[$expense->id]) ? $employeeDetails[$expense->id] : null;
                                    @endphp
                                    
                                    @if($employeeInfo)
                                        <div>
                                            <strong>{{ $employeeInfo['full_name'] ?? 'N/A' }}</strong>
                                            @if($employeeInfo['employee_code'])
                                                <br><small>ID: {{ $employeeInfo['employee_code'] }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <div>
                                            @if($expense->employee_full_name)
                                                <strong>{{ $expense->employee_full_name }}</strong>
                                                @if($expense->employee_code)
                                                    <br><small>ID: {{ $expense->employee_code }}</small>
                                                @endif
                                            @elseif($expense->purchased_by)
                                                <strong>{{ $expense->purchased_by }}</strong>
                                            @elseif($expense->employee_id)
                                                <strong>ID: {{ $expense->employee_id }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ $expense->purchased_by ?? 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $amount = $expense->amount ?? $expense->expense_amount ?? 0;
                                        $currency = $expense->currency ?? 'INR';
                                        $symbol = $currency == 'INR' ? '₹' : '$';
                                    @endphp
                                    
                                    @if($amount > 0)
                                        <span class="text-success">{{ $symbol }}{{ number_format($amount, 2) }}</span>
                                        <br><small class="text-muted">{{ $currency }}</small>
                                    @else
                                        {{ $symbol }}0.00
                                    @endif
                                </td>
                                <td>
                                    {{ $expense->paid_by ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($expense->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($expense->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($expense->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($expense->status ?? 'N/A') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No expenses found.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Display total count -->
                    @if($expenses->count() > 0)
                        <div class="mt-3">
                            <p>Showing {{ $expenses->count() }} entries</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Success message fade out
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 1s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 1000);
            }
        }, 2000);
        
        // Initialize date pickers
        $('.datetimepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            useCurrent: false
        });
    });
</script>
@endsection