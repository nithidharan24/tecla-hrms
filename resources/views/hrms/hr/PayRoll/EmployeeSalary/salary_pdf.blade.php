<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Salary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Employee Salary Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Employee ID</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Designation</th>
                <th>Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaries as $index => $salary)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $salary->firstname }} {{ $salary->lastname }}</td>
                    <td>{{ $salary->employeeid }}</td>
                    <td>{{ $salary->email }}</td>
                    <td>{{ $salary->joiningdate ? \Carbon\Carbon::parse($salary->joiningdate)->format('d-m-Y') : '' }}</td>
                    <td>{{ $salary->designation_name }}</td>
                    <td class="text-right">{{ number_format($salary->net_salary, 2) }}</td>
                    <td>{{ ucfirst($salary->approval_status ?? 'pending') }}</td>
                </tr>
            @endforeach
            @if($salaries->isEmpty())
                <tr>
                    <td colspan="8" style="text-align:center;">No salary records available.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
