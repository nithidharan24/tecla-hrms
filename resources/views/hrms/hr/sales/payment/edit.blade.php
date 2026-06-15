@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Payment - {{ $payment->payment_id }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payment.update', $payment->payment_id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Invoice</label>
                                <input type="text" class="form-control" value="{{ $payment->invoice_id }} - {{ $payment->client_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Total</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ number_format($payment->invoice_total, 2) }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="text" class="form-control datepicker" id="payment_date" name="payment_date" 
                                    value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_amount" class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="payment_amount" 
                                        name="payment_amount" value="{{ number_format($payment->amount, 2, '.', '') }}" 
                                        min="0.01" max="{{ number_format($maxAmount, 2, '.', '') }}" required>
                                </div>
                                <small class="text-muted">Max: {{ number_format($maxAmount, 2) }}</small>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="Cash" {{ $payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank Transfer" {{ $payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Credit Card" {{ $payment->payment_method == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Check" {{ $payment->payment_method == 'Check' ? 'selected' : '' }}>Check</option>
                                    <option value="Other" {{ $payment->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ $payment->notes }}</textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payment.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    $('.datepicker').flatpickr({
        dateFormat: "d-m-Y",
        allowInput: true
    });
});
</script>
@endsection