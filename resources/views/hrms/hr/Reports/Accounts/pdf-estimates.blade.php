<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Estimate Report' }}</title>
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
    <h2>{{ $title ?? 'Estimate Report' }}</h2>
    <div class="meta">Generated at: {{ $generated_at ?? '' }}</div>
    <table>
        <thead>
            <tr>
                <th>Estimate ID</th>
                <th>Client</th>
                <th>Estimate Date</th>
                <th>Expiry Date</th>
                <th>Grand Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach(($rows ?? []) as $r)
            <tr>
                <td>{{ $r->estimate_id }}</td>
                <td>{{ $r->client_name }}</td>
                <td>{{ $r->estimate_date ? \Carbon\Carbon::parse($r->estimate_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $r->expiry_date ? \Carbon\Carbon::parse($r->expiry_date)->format('d/m/Y') : '' }}</td>
                <td>{{ number_format((float)$r->grant_amt, 2) }}</td>
                <td>{{ ucfirst($r->status ?? '') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>