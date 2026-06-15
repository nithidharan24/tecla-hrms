<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Leave Reports</h1>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>No. of Days</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adminLeaves as $leave)
                <tr>
                    <td>{{ $leave->employee_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                    <td>{{ $leave->department_name }}</td>
                    <td>{{ $leave->leave_type }}</td>
                    <td>{{ $leave->no_of_days }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
