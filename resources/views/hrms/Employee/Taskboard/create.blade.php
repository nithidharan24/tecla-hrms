@extends('layouts.index')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark">{{ $project->projectname }}</h1>
                <div class="d-flex align-items-center text-muted">
                    <span class="me-3">
                        <i class="fas fa-user me-1"></i> 
                        Lead: {{ $project->leader_name }}
                    </span>
                    
                    <span class="badge bg-{{ $project->status == 'active' ? 'primary' : ($project->status == 'completed' ? 'success' : 'secondary') }} rounded-pill">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('tasks.create', ['projectid' => $project->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add New Task
                </a>
            </div>
        </div>
    </div>

    <!-- Task Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Total Tasks</h6>
                            <h3 class="fw-bold text-dark">{{ $totalTasks }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-tasks text-primary fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $totalTasks > 0 ? 100 : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Pending Tasks</h6>
                            <h3 class="fw-bold text-dark">{{ $pendingTasks }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalTasks > 0 ? ($pendingTasks / $totalTasks) * 100 : 0 }}%;"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            Due: {{ \Carbon\Carbon::parse($project->enddate)->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">In Progress</h6>
                            <h3 class="fw-bold text-dark">{{ $inProgressTasks }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-spinner text-info fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $totalTasks > 0 ? ($inProgressTasks / $totalTasks) * 100 : 0 }}%;"></div>
                        </div>
                        <small class="text-muted mt-1">Tasks currently being worked on</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Completed Tasks</h6>
                            <h3 class="fw-bold text-dark">{{ $completedTasks }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%;"></div>
                        </div>
                        <small class="text-muted mt-1">Tasks completed successfully</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Details Section -->
    <div class="row">
        <!-- Pending Tasks -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Pending Tasks</h5>
                        <span class="badge bg-warning text-dark">{{ $pendingTasksList->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($pendingTasksList->isEmpty())
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-tasks fa-2x text-muted"></i>
                            </div>
                            <p class="text-muted">No pending tasks</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pendingTasksList as $task)
                                <div class="list-group-item border-0 p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="fw-bold mb-1">{{ $task->task }}</h6>
                                            <p class="small text-muted mb-2">{{ Str::limit($task->description, 60) }}</p>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-3"><i class="fas fa-user me-1"></i> {{ $task->employee_name ?? 'Unassigned' }}</span>
                                                <span><i class="fas fa-calendar me-1"></i> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M') : 'No due date' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }} mb-2">
                                                {{ ucfirst($task->priority ?? 'medium') }}
                                            </span>
                                            <div>
                                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Start Task">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-0">
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">In Progress</h5>
                        <span class="badge bg-info">{{ $inProgressTasksList->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($inProgressTasksList->isEmpty())
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-spinner fa-2x text-muted"></i>
                            </div>
                            <p class="text-muted">No tasks in progress</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($inProgressTasksList as $task)
                                <div class="list-group-item border-0 p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="fw-bold mb-1">{{ $task->task }}</h6>
                                            <p class="small text-muted mb-2">{{ Str::limit($task->description, 60) }}</p>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-3"><i class="fas fa-user me-1"></i> {{ $task->employee_name ?? 'Unassigned' }}</span>
                                                <span><i class="fas fa-calendar me-1"></i> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M') : 'No due date' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }} mb-2">
                                                {{ ucfirst($task->priority ?? 'medium') }}
                                            </span>
                                            <div>
                                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Complete Task">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-0">
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Completed</h5>
                        <span class="badge bg-success">{{ $completedTasksList->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($completedTasksList->isEmpty())
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-check-circle fa-2x text-muted"></i>
                            </div>
                            <p class="text-muted">No completed tasks</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($completedTasksList as $task)
                                <div class="list-group-item border-0 p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="fw-bold mb-1 text-decoration-line-through">{{ $task->task }}</h6>
                                            <p class="small text-muted mb-2">{{ Str::limit($task->description, 60) }}</p>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-3"><i class="fas fa-user me-1"></i> {{ $task->employee_name ?? 'Unassigned' }}</span>
                                                <span><i class="fas fa-calendar-check me-1"></i> {{ $task->updated_at ? \Carbon\Carbon::parse($task->updated_at)->format('d M') : 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }} mb-2">
                                                {{ ucfirst($task->priority ?? 'medium') }}
                                            </span>
                                            <div>
                                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Reopen Task">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-0">
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Project Progress Overview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Project Progress Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3">Overall Progress</h6>
                            <div class="progress mb-4" style="height: 20px; border-radius: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0 }}%;">
                                    <span class="fw-bold">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0 }}% Complete</span>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="fw-bold text-warning fs-4">{{ $pendingTasks }}</div>
                                    <small class="text-muted">Pending</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-info fs-4">{{ $inProgressTasks }}</div>
                                    <small class="text-muted">In Progress</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-success fs-4">{{ $completedTasks }}</div>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-primary fs-4">{{ $totalTasks }}</div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-3">Project Details</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Start Date:</span>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($project->startdate)->format('d M Y') }}</span>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">End Date:</span>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($project->enddate)->format('d M Y') }}</span>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Priority:</span>
                                        <span class="badge bg-{{ $project->priority == 'high' ? 'danger' : ($project->priority == 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($project->priority) }}
                                        </span>
                                    </div>
                                </li>
                                <li class="mb-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Status:</span>
                                        <span class="badge bg-{{ $project->status == 'active' ? 'primary' : ($project->status == 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Assign Tester Section -->
<div class="row mt-5">
    <div class="col-lg-6 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Assign Tester</h5>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                @if(isset($assignedTester))
                    <div class="alert alert-info">
                        <strong>Tester already assigned:</strong> {{ $assignedTester->tester_name }}
                    </div>
                @else
                    <form action="{{ route('taskboard.assignTester') }}" method="POST">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        
                        <div class="mb-3">
                            <label for="tester_id" class="form-label">Select Tester (Testing Department)</label>
                            <select name="tester_id" id="tester_id" class="form-select" required>
                                <option value="">-- Choose Tester --</option>
                                @foreach($testers as $tester)
                                    <option value="{{ $tester->id }}">
                                        {{ $tester->firstname }} {{ $tester->lastname }} ({{ $tester->department_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-check me-2"></i> Assign Tester
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>



<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.btn {
    border-radius: 6px;
}

.page-header h1 {
    font-weight: 600;
}

.text-decoration-line-through {
    opacity: 0.7;
}
</style>

@endsection