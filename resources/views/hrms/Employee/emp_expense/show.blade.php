@extends('layouts.index')

@section('content')
<div class="container">
    <h2>Expense Details</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
            <p><strong>Amount:</strong> ₹{{ number_format($expense->expense_amount, 2) }}</p>
            <p><strong>Purpose:</strong> {{ $expense->expense_purpose }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ 
                    $expense->expense_status == 'approved' ? 'success' : 
                    ($expense->expense_status == 'rejected' ? 'danger' : 'warning') 
                }}">
                    {{ ucfirst($expense->expense_status) }}
                </span>
            </p>
            <p><strong>Receipt:</strong>
                @if($expense->receipt_attachment)
                    <a href="{{ asset('storage/'.$expense->receipt_attachment) }}" target="_blank">View Receipt</a>
                @else
                    No Attachment
                @endif
            </p>
        </div>
    </div>

    <a href="{{ route('employee_expenses.index') }}" class="btn btn-secondary">Back to Expenses</a>
</div>
@endsection
