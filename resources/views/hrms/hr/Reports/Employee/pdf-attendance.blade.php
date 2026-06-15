<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Attendance Report' }}</title>
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
    <h2>{{ $title ?? 'Attendance Report' }}</h2>
    <div class="meta">Generated at: {{ $generated_at }}</div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Branch</th>
                <th>Shift</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Work Hrs</th>
                <th>Break (min)</th>
                <th>OT (h)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $r)
            <tr>
                <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d/m/Y') : '' }}</td>
                <td>{{ $r->employeeid }}</td>
                <td>{{ trim(($r->firstname ?? '').' '.($r->lastname ?? '')) }}</td>
                <td>{{ $r->department_name }}</td>
                <td>{{ $r->branch_name }}</td>
                <td>{{ $r->shift_name }}</td>
                <td>{{ $r->punch_in ? \Carbon\Carbon::parse($r->punch_in)->format('d/m/Y H:i') : '' }}</td>
                <td>{{ $r->punch_out ? \Carbon\Carbon::parse($r->punch_out)->format('d/m/Y H:i') : '' }}</td>
                <td>{{ is_null($r->working_hours) ? '' : number_format((float)$r->working_hours, 2) }}</td>
                <td>{{ $r->total_break_taken }}</td>
                <td>{{ $r->overtime_hours }}</td>
                <td>{{ $r->status }}</td>
            </tr>
        @empty
            <tr><td colspan="12" class="small">No records found.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>