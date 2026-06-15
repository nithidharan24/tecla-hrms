@extends('layouts.index')

@section('title', 'Edit Expense')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Expense</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('employee_expenses.update', $expense->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label for="expense_date" class="col-md-3 col-form-label">Expense Date</label>
                <div class="col-md-9">
                    <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                           id="expense_date" name="expense_date" 
                           value="{{ old('expense_date', $expense->expense_date) }}" 
                           max="{{ date('Y-m-d') }}" required
                           @if($expense->expense_status !== 'pending') disabled @endif>
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
                               value="{{ old('expense_amount', $expense->expense_amount) }}" required
                               @if($expense->expense_status !== 'pending') disabled @endif>
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
                              id="expense_purpose" name="expense_purpose" rows="3" required
                              @if($expense->expense_status !== 'pending') disabled @endif>{{ old('expense_purpose', $expense->expense_purpose) }}</textarea>
                    @error('expense_purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="receipt_attachment" class="col-md-3 col-form-label">Receipt</label>
                <div class="col-md-9">
                    @if($expense->receipt_attachment)
                        <div class="mb-2">
                            <a href="{{ asset($expense->receipt_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View Current Receipt
                            </a>
                        </div>
                    @endif
                    
                    <input type="file" class="form-control @error('receipt_attachment') is-invalid @enderror" 
                           id="receipt_attachment" name="receipt_attachment"
                           @if($expense->expense_status !== 'pending') disabled @endif>
                    <div class="form-text">Upload JPG, PNG, PDF (Max 5MB). Only if you need to change the receipt.</div>
                    @error('receipt_attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 offset-md-3">
                    @if($expense->expense_status === 'pending')
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Expense
                        </button>
                    @endif
                    <a href="{{ route('employee_expenses.index') }}" class="btn btn-outline-secondary">
                        Back to List
                    </a>
                    
                    @if($expense->expense_status === 'pending')
                        <button type="button" class="btn btn-danger float-end" 
                                onclick="confirmDelete({{ $expense->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
        </form>
        
        @if($expense->expense_status === 'pending')
            <form id="delete-form-{{ $expense->id }}" 
                  action="{{ route('employee_expenses.destroy', $expense->id) }}" 
                  method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this expense?')) {
            event.preventDefault();
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection