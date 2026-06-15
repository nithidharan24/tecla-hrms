@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leave Type</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Leave Type</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_leavetype"> Add Leave Type</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Leave Type</th>
                            <th>Leave Days</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveTypes as $leaveType)
                        <tr>
                            <td>{{ $loop->iteration }}</td> 
                            <td>{{ $leaveType->leave_type }}</td>
                            <td>{{ $leaveType->leave_days }} Days</td>
                            <!-- Status Dropdown -->
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle update-status" href="#" data-id="{{ $leaveType->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-regular fa-circle-dot {{ $leaveType->status == 'Active' ? 'text-success' : 'text-danger' }}"></i> 
                                        <span class="status-text">{{ $leaveType->status }}</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item status-option" href="#" data-status="active"> <!-- Changed to lowercase -->
    <i class="fa-regular fa-circle-dot text-success"></i> Active
</a>
<a class="dropdown-item status-option" href="#" data-status="inactive"> <!-- Changed to lowercase -->
    <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
</a>

                                    </div>
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <!-- Edit Action -->
                                        <a class="dropdown-item edit-leavetype" href="#" data-bs-toggle="modal" data-bs-target="#edit_leavetype"
                                           data-id="{{ $leaveType->id }}"
                                           data-leave_type="{{ $leaveType->leave_type }}"
                                           data-leave_days="{{ $leaveType->leave_days }}"
                                           data-status="{{ $leaveType->status }}">
                                           <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                        </a>
                                        <!-- Delete Action -->
                                        <form action="{{ route('leave-types.destroy', $leaveType->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                     <!-- Delete Action -->
<a href="#" class="dropdown-item delete-leavetype" data-id="{{ $leaveType->id }}" data-bs-toggle="modal" data-bs-target="#deleteLeaveTypeModal">
    <i class="fa-regular fa-trash-can m-r-5"></i> Delete
</a>

                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Leave Type Modal -->
<div id="add_leavetype" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('leave-types.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="leave_type" required>
                    </div>
                    <div class="mb-3">
                        <label>Number of Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="leave_days" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Leave Type Modal -->

<!-- Edit Leave Type Modal -->
<div id="edit_leavetype" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="edit-leavetype-form">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="leave_type" id="edit-leave-type" required>
                    </div>
                    <div class="mb-3">
                        <label>Number of Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="leave_days" id="edit-leave-days" required>
                    </div>
                   
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Leave Type Modal -->
<!-- Delete Leave Type Confirmation Modal -->
<div class="modal fade" id="deleteLeaveTypeModal" tabindex="-1" aria-labelledby="deleteLeaveTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="deleteLeaveTypeModalLabel" style="font-weight: bold;">Delete Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to delete this leave type?
            </div>
            <div class="modal-footer d-flex justify-content-around border-0">
                <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>

                <form id="deleteLeaveTypeForm" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   document.querySelectorAll('.status-option').forEach(option => {
    option.addEventListener('click', function (e) {
        e.preventDefault();

        const leaveTypeId = this.closest('.dropdown').querySelector('.update-status').getAttribute('data-id');
        const newStatus = this.getAttribute('data-status').toLowerCase(); // Convert to lowercase

        fetch(`/leave-types/update-status/${leaveTypeId}/${newStatus}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log(response); // Log the entire response
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const statusText = this.closest('.dropdown').querySelector('.status-text');
                statusText.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1); // Update displayed status with proper casing
                const statusIcon = this.closest('.dropdown').querySelector('i');
                statusIcon.classList.toggle('text-success', newStatus === 'active');
                statusIcon.classList.toggle('text-danger', newStatus === 'inactive');
            } 
        })
        .catch(error => console.error('Error:', error)); // Catch any errors
    });
});

 

    document.querySelectorAll('.edit-leavetype').forEach(item => {
        item.addEventListener('click', function() {
            const form = document.getElementById('edit-leavetype-form');
            const id = this.getAttribute('data-id');
            form.action = `/leave-types/${id}`; // Set form action
            document.getElementById('edit-leave-type').value = this.getAttribute('data-leave_type');
            document.getElementById('edit-leave-days').value = this.getAttribute('data-leave_days');
            document.getElementById('edit-status').value = this.getAttribute('data-status');
        });
    });
    document.querySelectorAll('.delete-leavetype').forEach(item => {
    item.addEventListener('click', function () {
        const leaveTypeId = this.getAttribute('data-id');
        const deleteForm = document.getElementById('deleteLeaveTypeForm');
        deleteForm.action = `/leave-types/${leaveTypeId}`; // Set the action dynamically
    });
});

</script>

@endsection
