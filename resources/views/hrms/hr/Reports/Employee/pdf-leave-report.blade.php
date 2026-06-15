<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leave Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 14px; margin-bottom: 10px; }
        .filters { margin-bottom: 15px; }
        .filter-item { margin-right: 15px; display: inline-block; }
        .filter-label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Leave Report</div>
        <div class="subtitle">{{ config('app.name') }}</div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Filters Applied:</strong><br>
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            <span class="filter-item">
                <span class="filter-label">Date Range:</span>
                {{ $filters['date_from'] ? date('d/m/Y', strtotime($filters['date_from'])) : '' }} 
                to 
                {{ $filters['date_to'] ? date('d/m/Y', strtotime($filters['date_to'])) : '' }}
            </span>
        @endif
        @if(!empty($filters['employee_id']))
            <span class="filter-item">
                <span class="filter-label">Employee ID:</span> {{ $filters['employee_id'] }}
            </span>
        @endif
        @if(!empty($filters['employee_name']))
            <span class="filter-item">
                <span class="filter-label">Employee Name:</span> {{ $filters['employee_name'] }}
            </span>
        @endif
        @if(!empty($filters['department_id']))
            <span class="filter-item">
                <span class="filter-label">Department:</span> {{ $departments->firstWhere('id', $filters['department_id'])->department ?? '' }}
            </span>
        @endif
        @if(!empty($filters['branch_id']))
            <span class="filter-item">
                <span class="filter-label">Branch:</span> {{ $branches->firstWhere('id', $filters['branch_id'])->name ?? '' }}
            </span>
        @endif
        @if(!empty($filters['leave_type']) && $filters['leave_type'] != 'all')
            <span class="filter-item">
                <span class="filter-label">Leave Type:</span> {{ $filters['leave_type'] }}
            </span>
        @endif
        @if(!empty($filters['status']) && $filters['status'] != 'all')
            <span class="filter-item">
                <span class="filter-label">Status:</span> {{ ucfirst($filters['status']) }}
            </span>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Branch</th>
                <th>Leave Type</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row['employee_id'] }}</td>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['branch'] }}</td>
                <td>{{ $row['leave_type'] }}</td>
                <td>{{ $row['from_date'] }}</td>
                <td>{{ $row['to_date'] }}</td>
                <td>{{ $row['days'] }}</td>
                <td>{{ $row['reason'] }}</td>
                <td>{{ $row['status'] }}</td>
                <td>{{ $row['created_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ $generated_at }}
    </div>
</body>
</html>