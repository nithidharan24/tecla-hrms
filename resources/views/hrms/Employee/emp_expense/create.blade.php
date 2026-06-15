@extends('layouts.index')

@section('title', 'Create New Expense')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Submit New Expense</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('employee_expenses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <label for="expense_date" class="col-md-3 col-form-label">Expense Date</label>
                <div class="col-md-9">
                    <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                           id="expense_date" name="expense_date" 
                           value="{{ old('expense_date', date('Y-m-d')) }}" 
                           max="{{ date('Y-m-d') }}" required>
                    @error('expense_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="expense_amount" class="col-md-3 col-form-label">Amount</label>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control @error('expense_amount') is-invalid @enderror" 
                               id="expense_amount" name="expense_amount" 
                               value="{{ old('expense_amount') }}" required>
                        @error('expense_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="expense_purpose" class="col-md-3 col-form-label">Purpose</label>
                <div class="col-md-9">
                    <textarea class="form-control @error('expense_purpose') is-invalid @enderror" 
                              id="expense_purpose" name="expense_purpose" rows="3" required>{{ old('expense_purpose') }}</textarea>
                    @error('expense_purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="receipt_attachment" class="col-md-3 col-form-label">Receipt</label>
                <div class="col-md-9">
                    <input type="file" class="form-control @error('receipt_attachment') is-invalid @enderror" 
                           id="receipt_attachment" name="receipt_attachment">
                    <div class="form-text">Upload JPG, PNG, PDF (Max 5MB)</div>
                    @error('receipt_attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 offset-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Submit Expense
                    </button>
                    <a href="{{ route('employee_expenses.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush