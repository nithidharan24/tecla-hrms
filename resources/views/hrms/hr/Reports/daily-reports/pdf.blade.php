<!DOCTYPE html>
<html>
<head>
    <title>Daily Report - {{ $date }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-bottom: 30px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .present { color: green; }
        .absent { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Attendance Report</h1>
      <h3>{{ \Carbon\Carbon::parse($date)->format('d-m-y') }}</h3>

    </div>
    
    <div class="summary">
        <div class="summary-item"><strong>Total Employees:</strong> {{ $totalEmployees }}</div>
        <div class="summary-item"><strong>Present:</strong> {{ $todayPresent }}</div>
        <div class="summary-item"><strong>Absent:</strong> {{ $todayAbsent }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Department</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaves as $leave)
                <tr>
                    <td>#{{ str_pad($leave->employee_id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $leave->employee_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->date)->format('d-m-y') }}</td>
                    <td>{{ $leave->department }}</td>
                    <td class="{{ strtolower($leave->status) }}">{{ $leave->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>