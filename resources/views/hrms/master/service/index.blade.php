
@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp

@extends('layouts.index')


@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Service Management</h4>
                </div>
                <div class="card-body">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Add Service Form -->
                    <form method="POST" action="{{ route('services.store') }}" id="serviceForm">
                        @csrf
                        <input type="hidden" id="service_id" name="service_id">
                        <input type="hidden" id="method_field" name="_method" value="POST">
                        
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="remark" class="form-label">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" rows="1"></textarea>
                                @error('remark')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end m-3">
                                @if(isset($permissions) && $permissions->can_create)
                                <button type="submit" class="btn btn-primary " id="submitBtn">
                                    <span id="submitText">Add Service</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </form>

                  <!-- Services List - Odoo Style -->
<div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead>
            <tr>
               
                <th>ID</th>
                <th>Service Name</th>
                <th>Remark</th>
                <th>Created At</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td data-label="S. No.">{{ $loop->iteration }}</td>

                <td data-label="Service Name">
                    <span class="high">{{ $service->name }}</span>
                </td>
                
                <td data-label="Remark">
                    <span class="od-chip-highlight">{{ $service->remark ?? '-' }}</span>
                </td>
                
                <td data-label="Created At">
                    {{ $service->created_at ? date('d-m-Y', strtotime($service->created_at)) : '-' }}
                </td>
                
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if(isset($permissions) && $permissions->can_edit)
                        <button class="od-icon-btn edit-btn" title="Edit" data-id="{{ $service->id }}">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        @endif
                        @if(isset($permissions) && $permissions->can_delete)
                        <button class="od-icon-btn danger delete-btn" title="Delete" data-id="{{ $service->id }}">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                        @endif
                    </div>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllServices = document.getElementById('checkAllServices');
const rowChecksService = document.querySelectorAll('.row-check-service');

checkAllServices?.addEventListener('change', function() {
    rowChecksService.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksService.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this service?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let isEditing = false;

        // Form submission handling
        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            const submitText = $('#submitText');
            const submitSpinner = $('#submitSpinner');
            
            submitBtn.prop('disabled', true);
            submitText.text('Processing...');
            submitSpinner.removeClass('d-none');
            
            const formData = $(this).serialize();
            const serviceId = $('#service_id').val();
            let url, method;
            
            if (serviceId) {
                url = "{{ route('services.update', ':id') }}".replace(':id', serviceId);
                method = 'PUT';
                $('#method_field').val('PUT');
            } else {
                url = "{{ route('services.store') }}";
                method = 'POST';
                $('#method_field').val('POST');
            }
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Reset form and reload page after short delay
                        resetForm();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false);
                    submitText.text(serviceId ? 'Update' : 'Add Service');
                    submitSpinner.addClass('d-none');
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        
                        for (const field in errors) {
                            errorMessages += errors[field][0] + '\n';
                        }
                        
                        alert('Validation Errors:\n' + errorMessages);
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Edit button click handler
        $('.edit-btn').click(function() {
            const serviceId = $(this).data('id');
            const editUrl = "{{ route('services.edit', ':id') }}".replace(':id', serviceId);
            
            $.ajax({
                url: editUrl,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    isEditing = true;
                    $('#service_id').val(data.id);
                    $('#name').val(data.name);
                    $('#remark').val(data.remark || '');
                    $('#submitText').text('Update');
                    $('#cancelBtn').removeClass('d-none');
                    $('#method_field').val('PUT');
                    
                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $('#serviceForm').offset().top - 100
                    }, 500);
                },
                error: function(xhr) {
                    alert('Error loading service data. Please try again.');
                }
            });
        });

        // Cancel button click handler
        $('#cancelBtn').click(function() {
            resetForm();
        });

        // Delete button click handler
        let deleteId;
        $('.delete-btn').click(function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        // Confirm delete button click handler
        $('#confirmDelete').click(function() {
            const deleteUrl = "{{ route('services.destroy', ':id') }}".replace(':id', deleteId);
            
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').prepend(alertHtml);
                        
                        // Reload page after short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    alert('An error occurred while deleting. Please try again.');
                }
            });
        });

        // Function to reset form
        function resetForm() {
            isEditing = false;
            $('#serviceForm')[0].reset();
            $('#service_id').val('');
            $('#method_field').val('POST');
            $('#submitText').text('Add Service');
            $('#cancelBtn').addClass('d-none');
            $('#submitBtn').prop('disabled', false);
            $('#submitSpinner').addClass('d-none');
        }
    });
</script>
@endsection