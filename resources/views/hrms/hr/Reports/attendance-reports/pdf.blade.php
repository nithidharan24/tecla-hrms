<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .filters { margin-bottom: 20px; font-size: 12px; }
        .filters span { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; font-size: 12px; text-align: right; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Generated on: {{ date('d M Y H:i:s') }}</p>
    </div>

    @if(count(array_filter($filters)))
    <div class="filters">
        <h3>Filters Applied:</h3>
        <ul>
            @if(!empty($filters['employee_name']))
                <li><span>Employee Name:</span> {{ $filters['employee_name'] }}</li>
            @endif
            @if(!empty($filters['month']))
                <li><span>Month:</span> {{ date('F', mktime(0, 0, 0, $filters['month'], 1)) }}</li>
            @endif
            @if(!empty($filters['year']))
                <li><span>Year:</span> {{ $filters['year'] }}</li>
            @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Work Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->employee_name }}</td>
                <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                <td>{{ $record->punch_in ?? '-' }}</td>
                <td>{{ $record->punch_out ?? '-' }}</td>
                <td>
                    @if (is_null($record->punch_in) && is_null($record->punch_out))
                        <span class="text-danger">Week Off</span>
                    @else
                        Present
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Records: {{ count($attendanceData) }}
    </div>
</body>
</html>