<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 10px; text-align: right; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 11px; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; color: white; }
        .bg-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Branch</th>
                <th class="text-right">Hours</th>
                <th class="text-right">Rate (₹/hr)</th>
                <th class="text-right">Amount (₹)</th>
                <th class="text-center">Status</th>
                <th>Approved By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row->overtime_date ? date('d/m/Y', strtotime($row->overtime_date)) : '' }}</td>
                <td>{{ $row->employeeid }}</td>
                <td>{{ $row->firstname }} {{ $row->lastname }}</td>
                <td>{{ $row->department_name }}</td>
                <td>{{ $row->branch_name }}</td>
                <td class="text-right">{{ number_format($row->overtime_hours, 2) }}</td>
                <td class="text-right">{{ number_format($row->overtime_rate, 2) }}</td>
                <td class="text-right">{{ number_format($row->overtime_amount, 2) }}</td>
                <td class="text-center">
                    @php
                        $badgeClass = 'badge bg-secondary';
                        if($row->status === 'approved') $badgeClass = 'badge bg-success';
                        elseif($row->status === 'pending') $badgeClass = 'badge bg-warning';
                        elseif($row->status === 'rejected') $badgeClass = 'badge bg-danger';
                    @endphp
                    <span class="{{ $badgeClass }}">{{ ucfirst($row->status) }}</span>
                </td>
                <td>{{ $row->approver_name ?: 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page {{ $pdf->getPageNumber() }} of {{ $pdf->getPageCount() }}
    </div>
</body>
</html>