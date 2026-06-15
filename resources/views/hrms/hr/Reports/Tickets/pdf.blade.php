<!DOCTYPE html>
<html>
<head>
    <title>Ticket Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; }
        .summary-cards { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 10px; width: 15%; text-align: center; }
        .card h3 { margin: 0; font-size: 18px; }
        .card p { margin: 5px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section-title { background-color: #f2f2f2; padding: 5px; margin: 15px 0 10px 0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ticket Report</h1>
        <p>Generated on: {{ $now }}</p>
        @if($startDate || $endDate)
        <p>Date range: {{ $startDate ? $startDate : 'Start' }} to {{ $endDate ? $endDate : 'End' }}</p>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="section-title">Summary Statistics</div>
    <!-- <div class="summary-cards">
        <div class="card">
            <h3>{{ $summaryStats['total'] }}</h3>
            <p>Total Tickets</p>
        </div>
        <div class="card">
            <h3>{{ $summaryStats['new'] }}</h3>
            <p>New Tickets</p>
        </div>
        <div class="card">
            <h3>{{ $summaryStats['open'] }}</h3>
            <p>Open Tickets</p>
        </div>
        <div class="card">
            <h3>{{ $summaryStats['in_progress'] }}</h3>
            <p>In Progress</p>
        </div>
        <div class="card">
            <h3>{{ $summaryStats['closed'] }}</h3>
            <p>Closed Tickets</p>
        </div>
        <div class="card">
            <h3>{{ $summaryStats['avg_resolution_hours'] }}h</h3>
            <p>Avg Resolution</p>
        </div>
    </div> -->

    <!-- Priority Breakdown -->
    <div class="section-title">Priority Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Priority</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php $total = $summaryStats['total']; @endphp
            @foreach($priorityStats as $priority => $stat)
            <tr>
                <td>{{ ucfirst($priority) }}</td>
                <td>{{ $stat->count }}</td>
                <td>{{ $total > 0 ? round(($stat->count / $total) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Status Breakdown -->
    <div class="section-title">Status Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statusStats as $status => $stat)
            <tr>
                <td>{{ ucfirst($status) }}</td>
                <td>{{ $stat->count }}</td>
                <td>{{ $total > 0 ? round(($stat->count / $total) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Employee Performance (Admin Only) -->
    @if(!empty($employeeStats['assigned']))
    <div class="section-title">Employee Performance</div>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Assigned Tickets</th>
                <th>Closed Tickets</th>
                <th>Success Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeStats['assigned'] as $employee)
            <tr>
                <td>{{ $employee->firstname }} {{ $employee->lastname }}</td>
                <td>{{ $employee->assigned_tickets }}</td>
                <td>{{ $employee->closed_tickets }}</td>
                <td>
                    @php
                        $successRate = $employee->assigned_tickets > 0 
                            ? round(($employee->closed_tickets / $employee->assigned_tickets) * 100, 1) 
                            : 0;
                    @endphp
                    {{ $successRate }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Detailed Tickets -->
    <div class="page-break"></div>
    <div class="section-title">Detailed Tickets</div>
    <table>
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Created By</th>
                <th>Assignee</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->ticket_id }}</td>
                <td>{{ Str::limit($ticket->ticket_subject, 30) }}</td>
                <td>{{ ucfirst($ticket->status) }}</td>
                <td>{{ ucfirst($ticket->priority) }}</td>
                <td>{{ $ticket->creator_firstname }} {{ $ticket->creator_lastname }}</td>
                <td>
                    @if($ticket->assign1_firstname)
                        {{ $ticket->assign1_firstname }} {{ $ticket->assign1_lastname }}
                    @else
                        Unassigned
                    @endif
                </td>
                <td>{{ $ticket->created_at }}</td>
                <td>{{ $ticket->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>