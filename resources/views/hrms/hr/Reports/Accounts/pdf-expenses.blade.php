<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Expense Report' }}</title>
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
    <h2>{{ $title ?? 'Expense Report' }}</h2>
    <div class="meta">Generated at: {{ $generated_at ?? '' }}</div>
    <table>
        <thead>
            <tr>
                <th>Expense ID</th>
                <th>Item</th>
                <th>Purchase From</th>
                <th>Purchase Date</th>
                <th>Purchased By</th>
                <th>Amount</th>
                <th>Paid By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach(($rows ?? []) as $r)
            <tr>
                <td>{{ $r->expense_id }}</td>
                <td>{{ $r->item_name }}</td>
                <td>{{ $r->purchase_from }}</td>
                <td>{{ $r->purchase_date ? \Carbon\Carbon::parse($r->purchase_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $r->purchased_by }}</td>
                <td>{{ number_format((float)$r->amount, 2) }}</td>
                <td>{{ $r->paid_by }}</td>
                <td>{{ ucfirst($r->status ?? '') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>