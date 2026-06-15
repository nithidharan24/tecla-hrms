@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Payment for Invoice: {{ $invoice->invoice_id }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoice.payment.update', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Client Name</label>
                                <input type="text" class="form-control" value="{{ $invoice->client_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Balance</label>
                                <input type="text" class="form-control" 
                                    value="{{ number_format($invoice->grant_amt - $invoice->paid_amount + $payment->amount, 2) }}" 
                                    readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" name="payment_date" 
                                    value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" step="0.01" min="0.01" 
                                    max="{{ $invoice->grant_amt - $invoice->paid_amount + $payment->amount }}" 
                                    value="{{ $payment->amount }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="Cash" {{ $payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Credit Card" {{ $payment->payment_method == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Bank Transfer" {{ $payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Check" {{ $payment->payment_method == 'Check' ? 'selected' : '' }}>Check</option>
                                    <option value="Other" {{ $payment->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="1">{{ $payment->notes }}</textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('invoice.payment', $payment->invoice_id) }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
@endsection