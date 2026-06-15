@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="page-title">Testing Reports Dashboard</h2>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Report Filters</h4>
        </div>
        <div class="card-body">
            <form id="reportFiltersForm">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="Open">Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    @if(session('role') === 'admin')
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="branch_id">Branch</label>
                            <select class="form-control" id="branch_id" name="branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="applyFilters" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

  

  

 

    <!-- Tickets Table -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Testing Tickets</h6>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-sm btn-primary" id="refreshTickets">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ticketsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created At</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->testing_ticket_id }}</td>
                                <td>{{ Str::limit($ticket->description, 50) }}</td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $ticket->priority === 'High' ? 'danger' : 
                                        ($ticket->priority === 'Medium' ? 'warning' : 'success') 
                                    }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $ticket->status === 'Open' ? 'primary' : 
                                        ($ticket->status === 'In Progress' ? 'info' : 
                                        ($ticket->status === 'Resolved' ? 'success' : 'secondary')) 
                                    }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y H:i') }}</td>
                              
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Report Generation Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Generate Report</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('testing.reports.generate') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="start_date" id="report_start_date">
                <input type="hidden" name="end_date" id="report_end_date">
                <input type="hidden" name="status" id="report_status">
                <input type="hidden" name="priority" id="report_priority">
                @if(session('role') === 'admin')
                    <input type="hidden" name="branch_id" id="report_branch_id">
                @endif
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="report_type">Report Type</label>
                            <select class="form-control" id="report_type" name="report_type" required>
                               
                                <option value="detailed">Detailed Report</option>
                                <option value="custom">Custom Report</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="format">Output Format</label>
                            <select class="form-control" id="format" name="format" required>
                                <option value="html">Web View (HTML)</option>
                                <option value="pdf">PDF Document</option>
                                <option value="csv">CSV Export</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-file-export mr-2"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize charts with initial data
    const initialStatusData = {
        Open: {{ $openTickets }},
        'In Progress': {{ $inProgressTickets }},
        Resolved: {{ $resolvedTickets }},
        Closed: {{ $closedTickets }}
    };

    const initialPriorityData = {
        @foreach($priorityStats as $priority)
            '{{ $priority->priority }}': {{ $priority->count }},
        @endforeach
    };

    let statusPieChart = initStatusPieChart(initialStatusData);
    let priorityPieChart = initPriorityPieChart(initialPriorityData);
    let monthlyTrendChart = initMonthlyTrendChart([]);

    // Load metrics and update charts
    function loadMetrics() {
        $.ajax({
            url: "{{ route('testing.reports.metrics') }}",
            type: "GET",
            data: $('#reportFiltersForm').serialize(),
            success: function(response) {
                // Update statistics cards
                $('#totalTickets').text(
                    (response.statusCounts.Open || 0) + 
                    (response.statusCounts['In Progress'] || 0) + 
                    (response.statusCounts.Resolved || 0) + 
                    (response.statusCounts.Closed || 0)
                );
                $('#openTickets').text(response.statusCounts.Open || 0);
                $('#inProgressTickets').text(response.statusCounts['In Progress'] || 0);
                $('#closedTickets').text(response.statusCounts.Closed || 0);

                // Update charts
                updateChartData(statusPieChart, Object.keys(response.statusCounts), Object.values(response.statusCounts));
                updateChartData(priorityPieChart, Object.keys(response.priorityCounts), Object.values(response.priorityCounts));
                updateMonthlyTrendChart(monthlyTrendChart, response.monthlyTrend);

                // Set hidden fields in report form
                $('#report_start_date').val($('#start_date').val());
                $('#report_end_date').val($('#end_date').val());
                $('#report_status').val($('#status').val());
                $('#report_priority').val($('#priority').val());
                @if(session('role') === 'admin')
                    $('#report_branch_id').val($('#branch_id').val());
                @endif
            }
        });
    }

    // Initialize Status Pie Chart
    function initStatusPieChart(data) {
        const ctx = document.getElementById('statusPieChart').getContext('2d');
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: getPieChartOptions()
        });
    }

    // Initialize Priority Pie Chart
    function initPriorityPieChart(data) {
        const ctx = document.getElementById('priorityPieChart').getContext('2d');
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#e74a3b', '#f6c23e', '#1cc88a'],
                    hoverBackgroundColor: ['#be2617', '#dda20a', '#17a673'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: getPieChartOptions()
        });
    }

    // Initialize Monthly Trend Chart
    function initMonthlyTrendChart(data) {
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: "Tickets",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: data.map(item => item.count),
                }],
            },
            options: getLineChartOptions()
        });
    }

    // Update chart data
    function updateChartData(chart, labels, data) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.update();
    }

    // Update monthly trend chart
    function updateMonthlyTrendChart(chart, data) {
        const labels = data.map(item => {
            const [year, month] = item.month.split('-');
            return new Date(year, month - 1).toLocaleString('default', { month: 'short' }) + ' ' + year;
        });
        const counts = data.map(item => item.count);

        chart.data.labels = labels;
        chart.data.datasets[0].data = counts;
        chart.update();
    }

    // Common pie chart options
    function getPieChartOptions() {
        return {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: true,
                    caretPadding: 10,
                },
            },
            cutout: '70%',
        };
    }

    // Common line chart options
    function getLineChartOptions() {
        return {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                },
                y: {
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            return 'Tickets: ' + context.parsed.y;
                        }
                    }
                }
            }
        };
    }

    // Apply filters button
    $('#applyFilters').click(function() {
        loadMetrics();
    });

    // Refresh tickets table
    $('#refreshTickets').click(function() {
        loadMetrics();
    });

    // Initial load
    loadMetrics();
});
</script>
@endsection