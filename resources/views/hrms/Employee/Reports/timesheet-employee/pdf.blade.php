<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timesheet Employee Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4e73df;
            margin: 0 0 10px;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .summary-card {
            flex: 1;
            padding: 15px;
            margin: 0 5px;
            border-radius: 5px;
            color: white;
            text-align: center;
        }
        .card-primary { background-color: #4e73df; }
        .card-success { background-color: #1cc88a; }
        .card-warning { background-color: #f6c23e; }
        .card-danger { background-color: #e74a3b; }
        .summary-card h3 {
            margin: 5px 0;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fc;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-approved {
            color: #1cc88a;
            font-weight: bold;
        }
        .status-pending {
            color: #f6c23e;
            font-weight: bold;
        }
        .status-rejected {
            color: #e74a3b;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            opacity: 0.1;
            font-size: 60px;
            color: #4e73df;
            transform: rotate(-45deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">HRMS</div>
    
    <div class="header">
        <h1>Timesheet Employee Report</h1>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
        <p>Period: 
            @if(request('from'))
                From {{ request('from') }} 
            @endif
            @if(request('to'))
                To {{ request('to') }}
            @endif
            @if(!request('from') && !request('to'))
                All Time
            @endif
        </p>
    </div>

    <div class="summary-cards">
        <div class="summary-card card-primary">
            <h3>{{ number_format($totalHours ?? 0, 2) }}</h3>
            <div>Total Hours</div>
        </div>
        <div class="summary-card card-success">
            <h3>{{ number_format($totalApproved ?? 0, 2) }}</h3>
            <div>Approved Hours</div>
        </div>
        <div class="summary-card card-warning">
            <h3>{{ number_format($totalPending ?? 0, 2) }}</h3>
            <div>Pending Hours</div>
        </div>
        <div class="summary-card card-danger">
            <h3>{{ number_format($totalRejected ?? 0, 2) }}</h3>
            <div>Rejected Hours</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Project</th>
                <th>Week Period</th>
                <th>Hours</th>
                <th>Status</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timesheets ?? [] as $timesheet)
            @php
                $weekStart = \Carbon\Carbon::parse($timesheet->week_start);
                $weekEnd = $weekStart->copy()->addDays(6);
                $employeeName = isset($employeeDetails[$timesheet->employee_id]) 
                    ? $employeeDetails[$timesheet->employee_id]['name'] 
                    : 'N/A';
            @endphp
            <tr>
                <td>#{{ $timesheet->id }}</td>
                <td>{{ $employeeName }}</td>
                <td>{{ $timesheet->project->projectname ?? 'N/A' }}</td>
                <td>{{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}</td>
                <td style="text-align: center;">{{ number_format($timesheet->hours, 2) }}</td>
                <td style="text-align: center;">
                    <span class="status-{{ $timesheet->status }}">
                        {{ ucfirst($timesheet->status) }}
                    </span>
                </td>
                <td>{{ $timesheet->comments ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No timesheets found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated report. For any queries, please contact HR department.</p>
        <p>Page {PAGE_NUM} of {PAGE_COUNT}</p>
    </div>
</body>
</html>