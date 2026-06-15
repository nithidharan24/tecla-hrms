<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Payment Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
        th { background: #eee; }
        h2 { margin: 0 0 10px 0; }
        .meta { margin-bottom: 10px; font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <h2>{{ $title ?? 'Payment Report' }}</h2>
    <div class="meta">Generated at: {{ $generated_at ?? '' }}</div>
    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Payment Date</th>
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Invoice Total</th>
                <th>Total Paid</th>
                <th>Remaining</th>
                <th>Invoice Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach(($rows ?? []) as $r)
            <tr>
                <td>{{ $r->payment_id }}</td>
                <td>{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $r->invoice_id }}</td>
                <td>{{ $r->client_name }}</td>
                <td>{{ number_format((float)$r->amount, 2) }}</td>
                <td>{{ $r->payment_method }}</td>
                <td>{{ number_format((float)$r->invoice_total, 2) }}</td>
                <td>{{ number_format((float)$r->total_paid, 2) }}</td>
                <td>{{ number_format((float)$r->remaining_amount, 2) }}</td>
                <td>{{ ucfirst($r->status ?? '') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>