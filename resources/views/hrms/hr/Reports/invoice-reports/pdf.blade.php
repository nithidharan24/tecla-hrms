<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Invoice Number</th>
                <th>Client</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $invoice->invoice_id }}</td>
                <td>{{ $invoice->client_name }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</td>
                <td>Rs.{{ number_format($invoice->grant_amt, 2) }}</td>
                <td>
                    @php
                        $statusClasses = [
                            'sent' => 'info',
                            'paid' => 'success',
                            'partially paid' => 'warning',
                            'cancelled' => 'danger'
                        ];
                        $status = strtolower($invoice->status);
                        $badgeClass = $statusClasses[$status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $badgeClass }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>