@extends('layouts.index')

@section('title', 'Testing Tickets Custom Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-center">Testing Tickets Custom Report</h2>
            <p class="text-center text-muted">Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
            
            @if(request('start_date') || request('end_date'))
            <p class="text-center">
                <strong>Date Range:</strong> 
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M j, Y') : 'Start' }} 
                to 
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M j, Y') : 'End' }}
            </p>
            @endif
            
            @if(request('status'))
            <p class="text-center">
                <strong>Status:</strong> {{ request('status') }}
            </p>
            @endif
            
            @if(request('priority'))
            <p class="text-center">
                <strong>Priority:</strong> {{ request('priority') }}
            </p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                            <td>{{ Str::limit($ticket->description, 50) }}</td>
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
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Statistics</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Tickets
                            <span class="badge badge-primary badge-pill">{{ $data->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            High Priority
                            <span class="badge badge-danger badge-pill">{{ $data->where('priority', 'High')->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Open Tickets
                            <span class="badge badge-primary badge-pill">{{ $data->where('status', 'Open')->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection