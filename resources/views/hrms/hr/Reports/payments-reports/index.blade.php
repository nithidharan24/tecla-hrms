@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Payments Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payments Report</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="row filter-row">
            <form method="GET" action="{{ route('payments-report.index') }}" class="w-100 d-flex">
                <div class="col-md-4">
                    <div class="input-block mb-0 form-focus">
                        <div class="cal-icon">
                            <input name="from" class="form-control floating datetimepicker" type="text" value="{{ request('from') }}">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-block mb-0 form-focus">
                        <div class="cal-icon">
                            <input name="to" class="form-control floating datetimepicker" type="text" value="{{ request('to') }}">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </form>
        </div>
        <!-- /Search Filter -->

        <!-- Export Buttons -->
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('payments-report.export.csv', request()->query()) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export to CSV
                </a>
                <a href="{{ route('payments-report.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>
        <!-- /Export Buttons -->

        <!-- Added summary cards to show payment statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary">{{ $payments->count() }}</h4>
                        <p class="mb-0">Total Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success">{{ number_format($payments->sum('amount'), 2) }}</h4>
                        <p class="mb-0">Total Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info">{{ number_format($payments->sum('total_paid'), 2) }}</h4>
                        <p class="mb-0">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning">{{ number_format($payments->sum('remaining_amount'), 2) }}</h4>
                        <p class="mb-0">Total Remaining</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-3">
            <div class="col-md-12">
                <div class="table-responsive">
    <table class="table custom-table datatable mb-0" id="paymentsTable">
        <thead>
            <tr>
               
                <th>Payment ID</th>
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Invoice Total</th>
                <th>Total Paid</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
            <tr class="row-check" id="payment-row-{{ $payment->payment_id }}">
        
                <td data-label="Payment ID">
                    <span class="high">{{ $payment->payment_id }}</span>
                </td>
                
                <td data-label="Invoice ID">
                    <a href="#">{{ $payment->invoice_id }}</a>
                </td>
                
                <td data-label="Client Name">
                    <span class="od-chip-highlight">{{ $payment->client_name }}</span>
                </td>
                
                <td data-label="Payment Date">
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                </td>
                
                <td data-label="Amount">
                    <span class="text-success">{{ number_format($payment->amount, 2) }}</span>
                </td>
                
                <td data-label="Payment Method">
                    <span class="badge badge-info">{{ $payment->payment_method }}</span>
                </td>
                
                <td data-label="Invoice Total">
                    {{ number_format($payment->invoice_total, 2) }}
                </td>
                
                <td data-label="Total Paid">
                    {{ number_format($payment->total_paid, 2) }}
                </td>
                
                <td data-label="Remaining Amount">
                    @if($payment->remaining_amount > 0)
                        <span class="text-warning">{{ number_format($payment->remaining_amount, 2) }}</span>
                    @else
                        <span class="text-success">0.00</span>
                    @endif
                </td>
                
                <td data-label="Status">
                    @if($payment->status == 'paid')
                        <span class="badge badge-success">Paid</span>
                    @elseif($payment->status == 'partially_paid')
                        <span class="badge badge-warning">Partially Paid</span>
                    @else
                        <span class="badge badge-danger">Pending</span>
                    @endif
                </td>
                
                <td data-label="Notes">
                    {{ $payment->notes ?? '-' }}
                </td>
                
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center">No payments found for the selected criteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>



            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('checkAllPayments');
    const checkboxes = document.querySelectorAll('#paymentsTable .row-checkbox');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
            toggleRowHighlight(cb.closest('.row-check'), cb.checked);
        });
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            toggleRowHighlight(cb.closest('.row-check'), cb.checked);
            selectAll.checked = Array.from(checkboxes).every(c => c.checked);
        });
    });

    function toggleRowHighlight(row, isChecked) {
        if(isChecked) row.classList.add('od-selected');
        else row.classList.remove('od-selected');
    }
});
</script>
@endsection
