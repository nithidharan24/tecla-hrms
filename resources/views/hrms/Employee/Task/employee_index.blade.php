@extends('layouts.index')
@section('content')
<div class="container-fluid-custom">
    <div class="page-header-custom">
        <h2 class="page-title-custom">My Tasks</h2>
    </div>

    <div class="row-custom">
        <div class="col-md-12-custom">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">Task List</h5>
                </div>
                <div class="card-body-custom">
                    @if($tasks->isEmpty())
                        <p class="text-muted-custom">No tasks assigned to you yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Task</th>
                                        <th>Description</th>
                                        <th>Project</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $index => $task)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $task->task }}</td>
                                            <td>{{ $task->description }}</td>
                                            <td>{{ $task->projectname }}</td>
                                            <td>
                                                <span class="badge-custom priority-{{ $task->priority }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : 'No due date' }}
                                            </td>
                                            <td>
                                                <span class="badge-custom status-{{ str_replace('_', '-', $task->status) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->status == 'pending')
                                                    <form action="{{ route('tasks.start-work', $task->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            Start Work
                                                        </button>
                                                    </form>
                                                @elseif($task->status == 'in_progress')
                                                    <form action="{{ route('tasks.end-work', $task->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            End Work
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
