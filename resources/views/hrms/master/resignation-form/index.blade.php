@extends('layouts.index')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@if (session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" id="error-message">
        {{ session('error') }}
    </div>
@endif

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Resignation Records</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Resignation Records</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('resignation.create') }}" class="btn add-btn">
                    <i class="fa-solid fa-plus"></i> Add Resignation
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
   
   
    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead>
            <tr>
               
                <th>Employee</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Resignation Date</th>
                <th>Last Working Day</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($resignationRecords as $resignation)
                <tr>
                  

                    <td data-label="Employee Name"><span class="od-chip-highlight">{{ $resignation->employee_name }}</span></td>
                    <td data-label="Employee ID"><span class="high">{{ $resignation->employee_id }}</span></td>
                    <td data-label="Department">{{ $resignation->department ?? 'N/A' }}</td>
                    <td data-label="Date of Resignation">{{ $resignation->date_of_resignation ? $resignation->date_of_resignation->format('d M Y') : 'N/A' }}</td>
                    <td data-label="Last Working Day">{{ $resignation->last_working_day ? $resignation->last_working_day->format('d M Y') : 'N/A' }}</td>
                    <td data-label="Approval Status">
                        <span class="od-chip od-chip-{{ 
                            $resignation->approval_status == 'approved' ? 'success' : 
                            ($resignation->approval_status == 'rejected' ? 'danger' : 'warning') 
                        }}">
                            {{ ucfirst($resignation->approval_status ?? 'pending') }}
                        </span>
                    </td>
                    <td data-label="Actions" class="text-center">
                        <div class="od-inline-actions">
                            <a href="{{ route('resignation.show', $resignation->id) }}" class="od-icon-btn" title="View">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                    
                            @if(isset($permissions) && $permissions->can_edit)
                            <a href="{{ route('resignation.edit', $resignation->id) }}" class="od-icon-btn" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>
                            @endif
                    
                            <a href="{{ route('resignation.downloadPdf', $resignation->id) }}" class="od-icon-btn" title="Download PDF">
                                <i class="fa-regular fa-file-pdf"></i>
                            </a>
                    
                            @if(isset($permissions) && $permissions->can_delete)
                            <button class="od-icon-btn danger" title="Delete" data-bs-toggle="modal" data-bs-target="#delete_resignation_{{ $resignation->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    
                        <!-- Delete Modal (unchanged) -->
                        <div class="modal fade" id="delete_resignation_{{ $resignation->id }}" tabindex="-1" role="dialog" aria-labelledby="delete_resignation_label_{{ $resignation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="delete_resignation_label_{{ $resignation->id }}">
                                            <i class="fas fa-exclamation-triangle"></i> Delete Resignation Record
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center py-5">
                                        <p class="fs-5 text-muted">
                                            Are you sure you want to delete this resignation record? This action cannot be undone.
                                        </p>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <i class="fas fa-trash-alt fa-3x text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                            <i class="fas fa-times-circle"></i> Cancel
                                        </button>
                                        <form action="{{ route('resignation.destroy', $resignation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-4">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">No resignation records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllResignations = document.getElementById('checkAllResignations');
const rowChecksResignation = document.querySelectorAll('.row-check-resignation');

checkAllResignations?.addEventListener('change', function() {
    rowChecksResignation.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksResignation.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2
    $('.select').select2();

    // Filter button click handler
    $('#filter_btn').click(function() {
        let employeeId = $('#employee_filter').val();
        let departmentId = $('#department_filter').val();
        let status = $('#status_filter').val();

        let queryParams = [];
        
        if (employeeId) queryParams.push(`employee_id=${employeeId}`);
        if (departmentId) queryParams.push(`department=${departmentId}`);
        if (status) queryParams.push(`status=${status}`);

        let queryString = queryParams.join('&');
        window.location.href = "{{ route('resignation.index') }}" + (queryString ? '?' + queryString : '');
    });

    // Reset filter button
    $('#reset_filter').click(function() {
        window.location.href = "{{ route('resignation.index') }}";
    });

    // Hide success/error messages after 5 seconds
    setTimeout(function() {
        $('#success-message, #error-message').fadeOut('slow');
    }, 5000);
});
</script>

@endsection