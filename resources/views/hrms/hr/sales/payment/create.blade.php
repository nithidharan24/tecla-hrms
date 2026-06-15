@extends('layouts.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Record New Payment</h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" method="POST" action="{{ route('payment.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="invoice_id" class="form-label">Select Invoice</label>
                                <select class="form-select" id="invoice_id" name="invoice_id" required>
                                    <option value="">-- Select Invoice --</option>
                                    @foreach($invoices as $invoice)
                                        <option value="{{ $invoice->invoice_id }}">
                                            {{ $invoice->invoice_id }} - {{ $invoice->client_name }} (₹{{ number_format($invoice->grant_amt, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="loadInvoiceBtn" class="btn btn-primary mt-4">Load Invoice</button>
                            </div>
                        </div>
                        
                        <div id="invoiceDetails" class="mb-4 p-3 border rounded" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Client:</strong> <span id="clientName"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Invoice Date:</strong> <span id="invoiceDate"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Due Date:</strong> <span id="dueDate"></span></p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <p><strong>Total Amount:</strong> <span id="totalAmount"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Paid Amount:</strong> <span id="paidAmount"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Remaining Amount:</strong> <span id="remainingAmount" class="fw-bold"></span></p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <p><strong>Status:</strong> <span id="invoiceStatus" class="badge"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="text" class="form-control datepicker" id="payment_date" name="payment_date"
                                     value="{{ old('payment_date', \Carbon\Carbon::now()->format('d-m-Y')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_amount" class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount"
                                         name="payment_amount" value="{{ old('payment_amount') }}" required>
                                </div>
                                <small class="text-danger" id="amountError" style="display: none;"></small>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">-- Select Method --</option>
                                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                                    <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="submitText">Record Payment</span>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    #invoiceDetails {
        background-color: #f8f9fa;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    #amountError {
        font-size: 0.875em;
    }
    .btn-processing {
        position: relative;
        padding-right: 2.5rem;
    }
    .spinner-border {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // Initialize datepicker
    $('.datepicker').flatpickr({
        dateFormat: "d-m-Y",
        allowInput: true
    });

    // Format currency with Indian Rupee symbol
    function formatCurrency(amount) {
        return '₹' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Function to format date from YYYY-MM-DD to DD-MM-YYYY
    function formatDate(dateString) {
        if (!dateString) return '';
        
        // Handle different date formats
        let date;
        if (dateString.includes('-')) {
            // Handle YYYY-MM-DD format
            const parts = dateString.split('-');
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    // YYYY-MM-DD format
                    date = new Date(parts[0], parts[1] - 1, parts[2]);
                } else {
                    // DD-MM-YYYY format (already formatted)
                    return dateString;
                }
            }
        } else {
            date = new Date(dateString);
        }
        
        if (isNaN(date.getTime())) {
            return dateString; // Return original if can't parse
        }
        
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}-${month}-${year}`;
    }

    // Load invoice details
    $('#loadInvoiceBtn').click(function() {
        const invoiceId = $('#invoice_id').val();
        
        if (!invoiceId) {
            alert('Please select an invoice first');
            return;
        }
        
        $.ajax({
            url: "{{ route('payment.show.invoice') }}",
            type: "GET",
            data: { invoice_id: invoiceId },
            success: function(response) {
                $('#clientName').text(response.invoice.client_name);
                
                // Format dates to DD-MM-YYYY
                $('#invoiceDate').text(formatDate(response.invoice.invoice_date));
                $('#dueDate').text(formatDate(response.invoice.due_date));
                
                $('#totalAmount').text(formatCurrency(response.invoice.grant_amt));
                $('#paidAmount').text(formatCurrency(response.paid_amount));
                $('#remainingAmount').text(formatCurrency(response.remaining_amount));
                
                // Set status badge
                const status = response.invoice.status.toLowerCase();
                let badgeClass = 'bg-secondary';
                if (status === 'paid') badgeClass = 'bg-success';
                else if (status === 'partially_paid') badgeClass = 'bg-warning text-dark';
                else if (status === 'pending') badgeClass = 'bg-info';
                
                $('#invoiceStatus').text(response.invoice.status)
                    .removeClass().addClass('badge ' + badgeClass);
                
                // Show invoice details
                $('#invoiceDetails').show();
                
                // Reset validation
                $('#payment_amount').removeClass('is-invalid');
                $('#amountError').hide();
            },
            error: function(xhr) {
                alert('Error loading invoice details');
            }
        });
    });
    
    // Validate payment amount on input
    $('#payment_amount').on('input', function() {
        validatePaymentAmount();
    });
    
    // Form submission handler
    $('#paymentForm').submit(function(e) {
        if (!validatePaymentAmount()) {
            e.preventDefault();
            return;
        }

        // Disable button and show processing state
        const submitBtn = $('#submitBtn');
        const submitText = $('#submitText');
        const submitSpinner = $('#submitSpinner');
        
        submitBtn.prop('disabled', true);
        submitText.text('Processing...');
        submitSpinner.removeClass('d-none');
        submitBtn.addClass('btn-processing');
    });
    
    function validatePaymentAmount() {
        const paymentInput = $('#payment_amount');
        const paymentAmount = parseFloat(paymentInput.val());
        const remainingAmountText = $('#remainingAmount').text().replace('₹', '').replace(/,/g, '');
        const remainingAmount = parseFloat(remainingAmountText) || 0;
        const errorElement = $('#amountError');
        
        // Reset state
        paymentInput.removeClass('is-invalid');
        errorElement.hide();
        
        // Check if empty or not a number
        if (isNaN(paymentAmount)) {
            errorElement.text('Please enter a valid payment amount').show();
            paymentInput.addClass('is-invalid');
            resetSubmitButton();
            return false;
        }
        
        // Check if zero or negative
        if (paymentAmount <= 0) {
            errorElement.text('Payment amount must be greater than ₹0.00').show();
            paymentInput.addClass('is-invalid');
            resetSubmitButton();
            return false;
        }
        
        // Check if exceeds remaining amount
        if (paymentAmount > remainingAmount) {
            errorElement.text('Payment amount cannot exceed remaining amount of ' + formatCurrency(remainingAmount)).show();
            paymentInput.addClass('is-invalid');
            resetSubmitButton();
            return false;
        }
        
        return true;
    }
    
    function resetSubmitButton() {
        $('#submitBtn').prop('disabled', false);
        $('#submitText').text('Record Payment');
        $('#submitSpinner').addClass('d-none');
        $('#submitBtn').removeClass('btn-processing');
    }
});
</script>
@endsection