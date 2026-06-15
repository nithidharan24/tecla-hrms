<!DOCTYPE html>
<html>
<head>
    <title>Employee Salary Report - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            color: white;
            font-size: 9px;
            text-transform: capitalize;
        }
        .status-sent { background-color: #28a745; } /* Green */
        .status-generated { background-color: #17a2b8; } /* Info blue */
        .status-failed { background-color: #dc3545; } /* Red */
    </style>
</head>
<body>
    <h1>Employee Salary Report</h1>
    <h2>For {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h2>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Emp ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Basic</th>
                <th>Earnings</th>
                <th>Deductions</th>
                <th>Net Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payslips as $key => $payslip)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $payslip->employeeid }}</td>
                <td>{{ $payslip->firstname }} {{ $payslip->lastname }}</td>
                <td>{{ $payslip->designation_name }}</td>
                <td>{{ number_format($payslip->basic_salary, 2) }}</td>
                <td>{{ number_format($payslip->total_earnings, 2) }}</td>
                <td>{{ number_format($payslip->total_deductions, 2) }}</td>
                <td>{{ number_format($payslip->net_salary, 2) }}</td>
                <td>
                    <span class="status-badge status-{{ $payslip->status }}">
                        {{ ucfirst($payslip->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
