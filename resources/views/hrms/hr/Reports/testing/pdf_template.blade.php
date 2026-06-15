<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Testing Ticket Report - {{ $reportType }}</title>
    <style type="text/css">
        /* PDF Report Styling */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 15px;
        }
        .header img {
            height: 60px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #2d3748;
            font-size: 24px;
            margin: 5px 0;
        }
        .header .subtitle {
            color: #718096;
            font-size: 14px;
        }
        .report-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 4px;
            border-left: 4px solid #3490dc;
        }
        .company-info {
            margin-top: 10px;
            font-size: 11px;
            color: #718096;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background-color: #2d3748;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        .badge-primary {
            background-color: #3490dc;
        }
        .badge-success {
            background-color: #38a169;
        }
        .badge-danger {
            background-color: #e53e3e;
        }
        .badge-warning {
            background-color: #dd6b20;
        }
        .badge-info {
            background-color: #3182ce;
        }
        .badge-secondary {
            background-color: #718096;
        }
        .summary-card {
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .summary-card-header {
            background-color: #2d3748;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
        }
        .summary-card-body {
            padding: 12px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #718096;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .chart-container {
            height: 300px;
            margin: 20px 0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-muted {
            color: #718096;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        @if(config('app.logo'))
            <img src="{{ config('app.logo') }}" alt="Company Logo">
        @endif
        <h1>Testing Ticket Report</h1>
        <div class="subtitle">{{ ucfirst($reportType) }} Report</div>
        <div class="company-info">
            {{ config('app.name') }} | Generated on: {{ now()->format('F j, Y \a\t h:i A') }}
        </div>
    </div>

    <!-- Report Info Section -->
    <div class="report-info">
        <strong>Report Parameters:</strong>
        <ul>
            @if($filters['start_date'] || $filters['end_date'])
                <li>
                    <strong>Date Range:</strong> 
                    {{ $filters['start_date'] ? \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') : 'Start' }} 
                    to 
                    {{ $filters['end_date'] ? \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') : 'End' }}
                </li>
            @endif
            @if($filters['status'])
                <li><strong>Status:</strong> {{ $filters['status'] }}</li>
            @endif
            @if($filters['priority'])
                <li><strong>Priority:</strong> {{ $filters['priority'] }}</li>
            @endif
            <li><strong>Total Records:</strong> {{ $totalRecords }}</li>
        </ul>
    </div>

    <!-- Report Content -->
    @if($reportType === 'summary')
        <!-- Summary Report Content -->
        <div class="summary-card">
            <div class="summary-card-header">Status Distribution</div>
            <div class="summary-card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['statusData'] as $status)
                        <tr>
                            <td>{{ $status->status }}</td>
                            <td>{{ $status->count }}</td>
                            <td>{{ $totalRecords > 0 ? number_format(($status->count / $totalRecords) * 100, 2) : 0 }}%</td>
                        </tr>
                        @endforeach
                        <tr style="background-color: #edf2f7; font-weight: bold;">
                            <td>Total</td>
                            <td>{{ $totalRecords }}</td>
                            <td>100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">Priority Distribution</div>
            <div class="summary-card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['priorityData'] as $priority)
                        <tr>
                            <td>{{ $priority->priority }}</td>
                            <td>{{ $priority->count }}</td>
                            <td>{{ $totalRecords > 0 ? number_format(($priority->count / $totalRecords) * 100, 2) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">Resolution Time Statistics</div>
            <div class="summary-card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Hours</th>
                            <th>Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Average Resolution Time</td>
                            <td>{{ number_format($data['resolutionStats']->avg_hours, 2) }}</td>
                            <td>{{ number_format($data['resolutionStats']->avg_hours / 24, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Minimum Resolution Time</td>
                            <td>{{ number_format($data['resolutionStats']->min_hours, 2) }}</td>
                            <td>{{ number_format($data['resolutionStats']->min_hours / 24, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Maximum Resolution Time</td>
                            <td>{{ number_format($data['resolutionStats']->max_hours, 2) }}</td>
                            <td>{{ number_format($data['resolutionStats']->max_hours / 24, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">Monthly Ticket Trend</div>
            <div class="summary-card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Ticket Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthlyTrend'] as $trend)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('F Y') }}</td>
                            <td>{{ $trend->count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($reportType === 'detailed')
        <!-- Detailed Report Content -->
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Project</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $ticket)
                <tr>
                    <td>{{ $ticket->testing_ticket_id }}</td>
                    <td>{{ $ticket->projectname }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($ticket->description, 50) }}</td>
                    <td>
                        <span class="badge 
                            @if($ticket->priority == 'High') badge-danger
                            @elseif($ticket->priority == 'Medium') badge-warning
                            @else badge-success
                            @endif">
                            {{ $ticket->priority }}
                        </span>
                    </td>
                    <td>
                        <span class="badge 
                            @if($ticket->status == 'Open') badge-primary
                            @elseif($ticket->status == 'In Progress') badge-info
                            @elseif($ticket->status == 'Resolved') badge-secondary
                            @else badge-success
                            @endif">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td>{{ $ticket->assigned_name }}</td>
                    <td>{{ $ticket->creator_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M j, Y h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('M j, Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <!-- Custom Report Content -->
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Project</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $ticket)
                <tr>
                    <td>{{ $ticket->testing_ticket_id }}</td>
                    <td>{{ $ticket->projectname }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($ticket->description, 50) }}</td>
                    <td>
                        <span class="badge 
                            @if($ticket->priority == 'High') badge-danger
                            @elseif($ticket->priority == 'Medium') badge-warning
                            @else badge-success
                            @endif">
                            {{ $ticket->priority }}
                        </span>
                    </td>
                    <td>
                        <span class="badge 
                            @if($ticket->status == 'Open') badge-primary
                            @elseif($ticket->status == 'In Progress') badge-info
                            @elseif($ticket->status == 'Resolved') badge-secondary
                            @else badge-success
                            @endif">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M j, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            <div class="summary-card">
                <div class="summary-card-header">Quick Statistics</div>
                <div class="summary-card-body">
                    <table>
                        <tbody>
                            <tr>
                                <td width="50%"><strong>Total Tickets</strong></td>
                                <td>{{ $totalRecords }}</td>
                            </tr>
                            <tr>
                                <td><strong>High Priority Tickets</strong></td>
                                <td>{{ $data->where('priority', 'High')->count() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Open Tickets</strong></td>
                                <td>{{ $data->where('status', 'Open')->count() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Closed Tickets</strong></td>
                                <td>{{ $data->where('status', 'Closed')->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer Section -->
    <div class="footer">
        <p>Confidential - For internal use only</p>
        <p>Page <span class="page-number"></span> of <span class="page-count"></span></p>
        <p>Generated by {{ Auth::user()->name ?? 'System' }} on {{ now()->format('F j, Y \a\t h:i A') }}</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>