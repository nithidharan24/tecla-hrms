@extends('layouts.index') {{-- Changed from layouts.index to layouts.app --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Payments for Invoice: {{ $invoice->invoice_id }}</h4>
                    <a href="{{ route('invoice.payment.create', $invoice->invoice_id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Payment
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Invoice Total</h6>
                                    <p class="card-text h4">{{ number_format($invoice->grant_amt, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Paid Amount</h6>
                                    <p class="card-text h4">{{ number_format($invoice->paid_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Balance</h6>
                                    <p class="card-text h4">{{ number_format($invoice->grant_amt - $invoice->paid_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payments->isEmpty())
                        <div class="alert alert-info">No payments recorded for this invoice.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->notes }}</td>
                                        <td>
                                            <a href="{{ route('invoice.payment.edit', $payment->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-payment" data-id="{{ $payment->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-payment').click(function() {
        if(confirm('Are you sure you want to delete this payment?')) {
            const paymentId = $(this).data('id');
            
            $.ajax({
                url: "{{ url('invoice/payment') }}/" + paymentId,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.status === 'success') {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error deleting payment: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endpush