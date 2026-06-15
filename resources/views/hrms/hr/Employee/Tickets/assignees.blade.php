@extends('layouts.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-users-cog"></i> Ticket Assignees Management
                    </h3>
                    <a href="{{ route('ticket-assignees.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Assign New Employee
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success')) 
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                    @endif
                    
                    @if(session('error')) 
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                    @endif
                    
                    @if($assignableEmployees->count())
                    <div class="table-responsive">
                        <table class="table table-striped custom-table datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Branch</th>
                                    <th>Assignee Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignableEmployees as $index => $emp)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $emp->firstname }} {{ $emp->lastname }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary p-2">
                                            <i class="fas fa-building"></i> 
                                            {{ $emp->department_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span >
                                            
                                            {{ $emp->designation_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $emp->branch_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $emp->assignee_role ?? 'General Assignee' }}</td>
                                    
                                    <td class="text-center">
                                        @if($emp->is_active)
                                            <span class="badge badge-success p-2">
                                                <i class="fas fa-check-circle"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger p-2">
                                                <i class="fas fa-times-circle"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('ticket-assignees.edit', $emp->id) }}" 
                                               class="btn btn-primary" 
                                               title="Edit Assignee"
                                               data-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('ticket-assignees.destroy', $emp->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger" 
                                                        onclick="return confirm('Are you sure you want to remove this assignee? This action cannot be undone.');"
                                                        title="Remove Assignee"
                                                        data-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else 
                        <div class="text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h4>No Assignable Employees Found</h4>
                                <p class="mb-0">There are currently no employees assigned to handle tickets.</p>
                                <a href="{{ route('ticket-assignees.create') }}" class="btn btn-info mt-3">
                                    <i class="fas fa-user-plus"></i> Assign Your First Employee
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                @if($assignableEmployees->count())
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Showing {{ $assignableEmployees->count() }} assignee(s) sorted by priority
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});
</script>

<style>
.badge {
    font-size: 0.85em;
    font-weight: 500;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
.btn-group .btn {
    margin: 0 2px;
    border-radius: 4px;
}
.card-title {
    margin-bottom: 0;
}
</style>
@endsection