@extends('layouts.index')

@section('title', 'Testing Tickets Summary Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-center">Testing Tickets Summary Report</h2>
            <p class="text-center text-muted">Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
            
            @if(request('start_date') || request('end_date'))
            <p class="text-center">
                <strong>Date Range:</strong> 
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M j, Y') : 'Start' }} 
                to 
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M j, Y') : 'End' }}
            </p>
            @endif
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Status Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = $data['totalTickets'];
                                @endphp
                                @foreach($data['statusData'] as $status)
                                <tr>
                                    <td>{{ $status->status }}</td>
                                    <td>{{ $status->count }}</td>
                                    <td>{{ $total > 0 ? number_format(($status->count / $total) * 100, 2) : 0 }}%</td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td><strong>{{ $total }}</strong></td>
                                    <td><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Priority Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
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
                                    <td>{{ $total > 0 ? number_format(($priority->count / $total) * 100, 2) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolution Time -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Resolution Time Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
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
            </div>
        </div>
    </div>

  
</div>
@endsection