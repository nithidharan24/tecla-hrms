@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Pending Worklog Approvals</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Project</th>
                                    <th>Task</th>
                                    <th>Hours</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($worklogs as $worklog)
                                <tr>
                                    <td>{{ $worklog->date->format('Y-m-d') }}</td>
                                    <td>{{ $worklog->user->name }}</td>
                                    <td>{{ $worklog->project }}</td>
                                    <td>{{ $worklog->task }}</td>
                                    <td>{{ floor($worklog->calculateTotalHours() / 60) }}h {{ $worklog->calculateTotalHours() % 60 }}m</td>
                                    <td>{{ Str::limit($worklog->description, 50) }}</td>
                                    <td>
                                        <form action="{{ route('worklogs.approve', $worklog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $worklog->id }}">
                                            Reject
                                        </button>
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $worklog->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Worklog</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('worklogs.reject', $worklog->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="comment">Reason for rejection</label>
                                                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $worklogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection