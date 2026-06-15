<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .date-range { text-align: center; margin-bottom: 15px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-bottom: 20px; }
        .summary-item { display: inline-block; margin-right: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <div class="date-range">{{ $date_range }}</div>
    </div>

    <!-- Added summary section to PDF -->
    <div class="summary">
        <div class="summary-item"><strong>Total Payments:</strong> {{ $payments->count() }}</div>
        <div class="summary-item"><strong>Total Amount:</strong> ${{ number_format($payments->sum('amount'), 2) }}</div>
        <div class="summary-item"><strong>Total Paid:</strong> ${{ number_format($payments->sum('total_paid'), 2) }}</div>
        <div class="summary-item"><strong>Total Remaining:</strong> ${{ number_format($payments->sum('remaining_amount'), 2) }}</div>
    </div>

    <!-- Updated PDF table to show comprehensive payment data -->
    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_id }}</td>
                    <td>{{ $payment->invoice_id }}</td>
                    <td>{{ $payment->client_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                    <td class="text-right">Rs.{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $payment->status)) }}</td>
                    <td class="text-right">Rs.{{ number_format($payment->remaining_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
