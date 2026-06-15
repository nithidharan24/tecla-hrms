<!DOCTYPE html>
<html>
<head>
    <title>Employee Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee Report</h1>
        <p>Generated on: {{ date('d M Y H:i:s') }}</p>
    </div>

    @if(count(array_filter($filters)))
    <div class="filters">
        <h3>Filters Applied:</h3>
        <ul>
            @if(!empty($filters['employee_name']))
                <li><span>Employee Name:</span> {{ $filters['employee_name'] }}</li>
            @endif
            @if(!empty($filters['department']))
                <li><span>Department:</span> {{ DB::table('department')->where('id', $filters['department'])->value('department') }}</li>
            @endif
            @if(!empty($filters['status']))
                <li><span>Status:</span> {{ ucfirst($filters['status']) }}</li>
            @endif
            @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                <li><span>Date Range:</span> 
                    {{ !empty($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') : 'Start' }} 
                    to 
                    {{ !empty($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d M Y') : 'End' }}
                </li>
            @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Joining Date</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->employee_id }}</td>
                <td>{{ $employee->employee_name }}</td>
                <td>{{ $employee->department_name ?? 'N/A' }}</td>
                <td>{{ $employee->designation_name ?? 'N/A' }}</td>
                <td>{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A' }}</td>
                <td>{{ ucfirst($employee->gender) ?? 'N/A' }}</td>
                <td>{{ $employee->phone ?? 'N/A' }}</td>
                <td>{{ ucfirst($employee->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Employees: {{ count($employees) }}
    </div>
</body>
</html>