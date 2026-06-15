<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Employee Directory Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin: 0 0 8px 0; }
        .meta { font-size: 11px; margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
        th { background: #efefef; }
        .small { font-size: 11px; color: #444; }
    </style>
</head>
<body>
    <h2>{{ $title ?? 'Employee Directory Report' }}</h2>
    <div class="meta">Generated at: {{ $generated_at }}</div>

    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Joining Date</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Branch</th>
                <th>Manager</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $r)
            <tr>
                <td>{{ $r->employeeid }}</td>
                <td>{{ trim(($r->firstname ?? '').' '.($r->lastname ?? '')) }}</td>
                <td>{{ $r->email }}</td>
                <td>{{ $r->phone }}</td>
                <td>{{ $r->joiningdate ? \Carbon\Carbon::parse($r->joiningdate)->format('d/m/Y') : '' }}</td>
                <td>{{ $r->department_name }}</td>
                <td>{{ $r->designation_name }}</td>
                <td>{{ $r->branch_name }}</td>
                <td>{{ $r->manager_name }}</td>
            </tr>
        @empty
            <tr><td colspan="9" class="small">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>