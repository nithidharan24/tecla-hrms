@extends('layouts.index')
@section('content')

<div class="container-fluid py-4">

    <!-- ============================= -->
    <!-- PAGE HEADER -->
    <!-- ============================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">{{ $project->projectname }} – Assign Tasks</h3>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <!-- ========================================== -->
            <!-- TASK FORM -->
            <!-- ========================================== -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-semibold">Create New Task</h5>
                </div>

                <div class="card-body">
                    <form id="taskForm" method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        <input type="hidden" name="projectid" value="{{ $project->id }}">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Task Name</label>
                                <input type="text" class="form-control" name="task" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assign To Employee</label>
                                <select class="form-select" name="assigned_to" required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->firstname }} {{ $emp->lastname }} ({{ $emp->employeeid }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="">Select priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>

                        </div>

                        <button id="submitBtn" class="btn btn-primary mt-3 px-4">
                            Submit Task
                        </button>
                    </form>
                </div>
            </div>

            <!-- =================================================== -->
            <!-- TABLE FILTERS -->
            <!-- =================================================== -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="fw-semibold">Filter by Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-semibold">Filter by Priority</label>
                            <select id="filterPriority" class="form-select">
                                <option value="">All</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-semibold">Filter by Employee</label>
                            <select id="filterEmp" class="form-select">
                                <option value="">All</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->firstname }} {{ $emp->lastname }}">
                                        {{ $emp->firstname }} {{ $emp->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- =================================================== -->
            <!-- TASK TABLE VIEW -->
            <!-- =================================================== -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-semibold">Tasks Overview</h5>
                </div>

                <div class="card-body table-responsive">

                    <table id="taskTable" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Task</th>
                                <th>Description</th>
                                <th>Employee</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td>{{ $task->task }}</td>
                                    <td>{{ $task->description }}</td>
                                    <td>{{ $task->employee_name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($task->priority) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($task->status) }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') }}</td>

                                    <td>
                                        <!-- Change Status -->
                                        <form method="POST"
                                              action="{{ route('tasks.updateStatus', $task->id) }}"
                                              class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-warning">Status</button>
                                        </form>

                                        <!-- Delete -->
                                        <button onclick="confirmDelete({{ $task->id }})"
                                                class="btn btn-sm btn-danger">
                                            Delete
                                        </button>

                                        <form id="delete-form-{{ $task->id }}"
                                              action="{{ route('tasks.destroy', $task->id) }}"
                                              method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- ============================ -->
<!-- JS SECTION -->
<!-- ============================ -->

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {

    let table = $('#taskTable').DataTable({
        pageLength: 10,
        ordering: true,
    });

    // Filter: Status
    $('#filterStatus').on('change', function () {
        table.column(4).search(this.value).draw();
    });

    // Filter: Priority
    $('#filterPriority').on('change', function () {
        table.column(3).search(this.value).draw();
    });

    // Filter: Employee
    $('#filterEmp').on('change', function () {
        table.column(2).search(this.value).draw();
    });

});
</script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This task will be deleted permanently!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("delete-form-" + id).submit();
        }
    });
}
</script>

@endsection
