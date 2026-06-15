@extends('layouts.index')

@section('title', 'Testing Tickets Detailed Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-center">Testing Tickets Detailed Report</h2>
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
                            <td>{{ $ticket->assigned_name }}</td>
                            <td>{{ $ticket->creator_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('M j, Y h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($ticket->updated_at)->format('M j, Y h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <p class="text-muted">Total Tickets: {{ $data->count() }}</p>
        </div>
    </div>
</div>
@endsection