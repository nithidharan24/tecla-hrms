<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
            line-height: 1.4;
        }
        h1 { 
            color: #333;
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px 8px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #f8f9fa; 
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .text-muted {
            color: #6c757d;
        }
        .travel-note {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .currency-symbol {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    
    @if(count($expenses) > 0)
        <table>
            <thead>
                <tr>
                    <th>Expense ID</th>
                    <th>Item / Purpose</th>
                    <th>Purchase From / Place</th>
                    <th>Date</th>
                    <th>Purchased By / Employee</th>
                    <th>Amount</th>
                    <th>Paid By</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                @php
                    $amount = $expense->amount ?? $expense->expense_amount ?? 0;
                    $currency = $expense->currency ?? 'INR';
                    $symbol = $currency == 'INR' ? '₹' : '$';
                @endphp
                <tr>
                    <td>
                        {{ $expense->expense_id ?? 'N/A' }}
                    </td>
                    <td>
                        @if(!empty($expense->item_name))
                            {{ $expense->item_name }}
                        @elseif(!empty($expense->purpose_of_visit))
                            {{ $expense->purpose_of_visit }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                        
                        @if(!empty($expense->place_of_visit) && empty($expense->item_name))
                            <div class="travel-note">{{ $expense->place_of_visit }}</div>
                        @endif
                        
                        @if(!empty($expense->customer_name))
                            <div class="travel-note">Customer: {{ $expense->customer_name }}</div>
                        @endif
                    </td>
                    
                    <td>
                        @if(!empty($expense->purchase_from))
                            {{ $expense->purchase_from }}
                        @elseif(!empty($expense->place_of_visit))
                            {{ $expense->place_of_visit }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    
                    <td>
                        @if(!empty($expense->purchase_date))
                            {{ \Carbon\Carbon::parse($expense->purchase_date)->format('d-m-Y') }}
                        @elseif(!empty($expense->departure_date))
                            {{ \Carbon\Carbon::parse($expense->departure_date)->format('d-m-Y') }}
                            
                            @if(!empty($expense->arrival_date) && $expense->arrival_date != $expense->departure_date)
                                <div class="travel-note">
                                    to {{ \Carbon\Carbon::parse($expense->arrival_date)->format('d-m-Y') }}
                                </div>
                            @endif
                            
                            @if(!empty($expense->duration_days))
                                <div class="travel-note">
                                    Duration: {{ $expense->duration_days }} day(s)
                                </div>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    
                    <td>
                        @php
                            $purchasedByName = '';
                            if(!empty($expense->purchased_by)) {
                                $purchasedByName = $expense->purchased_by;
                            } elseif(!empty($expense->employee_full_name)) {
                                $purchasedByName = $expense->employee_full_name;
                                if(!empty($expense->employee_code)) {
                                    $purchasedByName .= ' (' . $expense->employee_code . ')';
                                }
                            } elseif(!empty($expense->employee_id)) {
                                $purchasedByName = 'ID: ' . $expense->employee_id;
                            }
                        @endphp
                        
                        @if(!empty($purchasedByName))
                            {{ $purchasedByName }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    
                    <td>
                        <strong>INR {{ number_format($amount, 2) }}</strong>
                        
                        @if(!empty($expense->currency))
                            <div class="travel-note">{{ $expense->currency }}</div>
                        @endif
                        
                        @if(isset($expense->is_billable) && $expense->is_billable)
                            <div class="travel-note">Billable</div>
                        @endif
                    </td>
                    
                    <td>
                        {{ $expense->paid_by ?? 'N/A' }}
                    </td>
                    
                    <td>
                        @php
                            $statusClass = 'badge-secondary';
                            if($expense->status == 'approved') {
                                $statusClass = 'badge-success';
                            } elseif($expense->status == 'pending') {
                                $statusClass = 'badge-warning';
                            } elseif($expense->status == 'rejected') {
                                $statusClass = 'badge-danger';
                            }
                        @endphp
                        
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($expense->status ?? 'N/A') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary">
            <h3>Summary</h3>
            
            @php
                $totalAmount = 0;
                $regularExpenses = 0;
                $travelExpenses = 0;
                $approvedCount = 0;
                $pendingCount = 0;
                $rejectedCount = 0;
                $currencyTotals = [];
                
                foreach($expenses as $expense) {
                    $amount = $expense->amount ?? $expense->expense_amount ?? 0;
                    $currency = $expense->currency ?? 'INR';
                    $totalAmount += $amount;
                    
                    if(!isset($currencyTotals[$currency])) {
                        $currencyTotals[$currency] = 0;
                    }
                    $currencyTotals[$currency] += $amount;
                    
                    if(!empty($expense->item_name)) {
                        $regularExpenses += $amount;
                    } else {
                        $travelExpenses += $amount;
                    }
                    
                    if($expense->status == 'approved') $approvedCount++;
                    if($expense->status == 'pending') $pendingCount++;
                    if($expense->status == 'rejected') $rejectedCount++;
                }
            @endphp
            
            <div class="summary-row">
                <strong>Total Expenses:</strong>
                <strong>INR{{ number_format($totalAmount, 2) }}</strong>
            </div>
            
            @if($regularExpenses > 0)
            <div class="summary-row">
                <span>Regular Expenses:</span>
                <span>INR{{ number_format($regularExpenses, 2) }}</span>
            </div>
            @endif
            
            @if($travelExpenses > 0)
            <div class="summary-row">
                <span>Travel Expenses:</span>
                <span>INR{{ number_format($travelExpenses, 2) }}</span>
            </div>
            @endif
            
            @if(count($currencyTotals) > 1)
                <div style="margin-top: 10px;">
                    <strong>By Currency:</strong>
                    @foreach($currencyTotals as $curr => $currAmount)
                        <div class="summary-row">
                            <span>{{ $curr }}:</span>
                            <span>{{ $curr == 'INR' ? '₹' : '$' }}{{ number_format($currAmount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <div style="margin-top: 15px;">
                <span class="badge badge-success">{{ $approvedCount }} Approved</span>
                <span class="badge badge-warning">{{ $pendingCount }} Pending</span>
                <span class="badge badge-danger">{{ $rejectedCount }} Rejected</span>
            </div>
            
            <div style="margin-top: 15px; color: #666; font-size: 11px;">
                Generated on: {{ date('d-m-Y H:i:s') }}
            </div>
        </div>
    @else
        <p style="text-align: center; color: #666; padding: 50px;">No expenses found matching the criteria.</p>
    @endif
</body>
</html>