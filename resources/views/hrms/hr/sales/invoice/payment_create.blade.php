@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Payment for Invoice: {{ $invoice->invoice_id }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoice.payment.store', $invoice->invoice_id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Client Name</label>
                                <input type="text" class="form-control" value="{{ $invoice->client_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Balance</label>
                                <input type="text" class="form-control" value="{{ number_format($balance, 2) }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" name="payment_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" step="0.01" min="0.01" max="{{ $balance }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Check">Check</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="1"></textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('invoice.payment', $invoice->invoice_id) }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Record Payment</button>
                        </div>
                    </form>
                </div>
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