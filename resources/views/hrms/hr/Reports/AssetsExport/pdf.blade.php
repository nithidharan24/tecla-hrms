<!DOCTYPE html>
<html>
<head>
    <title>Assets Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin-bottom: 5px; }
        .header p { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 11px; color: #666; }
        .filters { margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border-radius: 4px; }
        .filters p { margin: 5px 0; }
        .summary { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Assets Report</h1>
        <p>Generated on: {{ date('d/m/Y H:i') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Filters Applied:</h3>
        <p><strong>Employee Name:</strong> {{ $filters['employee_name'] ?? 'All' }}</p>
        <p><strong>Status:</strong> {{ $filters['status'] ?? 'All' }}</p>
        <p><strong>Branch:</strong> {{ $filters['branch'] ?? 'All' }}</p>
        <p><strong>Date Range:</strong> {{ $filters['from_date'] ?? 'N/A' }} to {{ $filters['to_date'] ?? 'N/A' }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Assigned To</th>
                <th>Branch</th>
                <th>Purchase Date</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->asset_id }}</td>
                <td>{{ $asset->asset_name }}</td>
                <td>
                    @if($asset->firstname)
                        {{ $asset->firstname }} {{ $asset->lastname }}
                    @else
                        Unassigned
                    @endif
                </td>
                <td>{{ $asset->branch_name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d/m/Y') }}</td>
                <td>{{ number_format($asset->value, 2) }}</td>
                <td>{{ ucfirst($asset->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>Total Assets: {{ count($assets) }}</p>
        <p>Total Value: {{ number_format($assets->sum('value'), 2) }}</p>
    </div>

   
</body>
</html>