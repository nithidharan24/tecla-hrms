<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timesheet Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f0f0f0; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Timesheet Report</h2>
    <div class="muted">Exported at: {{ $exportedAt }}</div>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Designation</th>
                <th>Project</th>
                <th>Assigned Date</th>
                <th>Assigned Hours</th>
                <th>Total Hours</th>
                <th>Remaining Hours</th>
                <th>Deadline</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $t)
                <tr>
                    <td>{{ ($t->firstname ?? '') . ' ' . ($t->lastname ?? '') }}</td>
                    <td>{{ $t->designation ?? 'N/A' }}</td>
                    <td>{{ $t->projectname ?? 'N/A' }}</td>
                    <td>{{ $t->assigned_date ? \Carbon\Carbon::parse($t->assigned_date)->format('d-m-Y') : '' }}</td>
                    <td>{{ $t->assigned_hours }}</td>
                    <td>{{ $t->total_hours }}</td>
                    <td>{{ $t->remaining_hours }}</td>
                    <td>{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d-m-Y') : '' }}</td>
                    <td>{{ $t->description }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;">No data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>