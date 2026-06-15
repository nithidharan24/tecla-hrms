<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .report-filters {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .filter-item {
            display: inline-block;
            margin-right: 15px;
        }
        .filter-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div class="report-title">Payslip Report</div>
        <div class="date-generated">Generated on: {{ date('Y-m-d H:i:s') }}</div>
    </div>

    @if($filters['employee_name'] || $filters['month'] || $filters['year'])
    <div class="report-filters">
        <span class="filter-label">Filters Applied:</span>
        @if($filters['employee_name'])
            <span class="filter-item">Employee: {{ $filters['employee_name'] }}</span>
        @endif
        @if($filters['month'])
            <span class="filter-item">Month: {{ $filters['month'] }}</span>
        @endif
        @if($filters['year'])
            <span class="filter-item">Year: {{ $filters['year'] }}</span>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Employee Name</th>
                <th class="text-right">Basic Salary</th>
                <th class="text-right">HRA</th>
                <th class="text-right">Conveyance</th>
                <th class="text-right">TDS</th>
                <th class="text-right">PF</th>
                <th class="text-right">Net Salary</th>
                <th>Month</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payslips as $index => $payslip)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payslip->full_name }}</td>
                <td class="text-right">${{ number_format($payslip->basic, 2) }}</td>
                <td class="text-right">${{ number_format($payslip->hra, 2) }}</td>
                <td class="text-right">${{ number_format($payslip->conveyance, 2) }}</td>
                <td class="text-right">${{ number_format($payslip->tds, 2) }}</td>
                <td class="text-right">${{ number_format($payslip->pf, 2) }}</td>
                <td class="text-right">${{ number_format($payslip->net_salary, 2) }}</td>
                <td>{{ date('M', strtotime($payslip->created_at)) }}</td>
                <td>{{ date('Y', strtotime($payslip->created_at)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

 
</body>
</html>