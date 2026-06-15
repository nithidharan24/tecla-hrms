@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Termination Letter Templates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Termination Letter Templates</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('termination-letter-templates.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Template
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    {{-- Removed the old Bootstrap alerts as SweetAlert will handle messages --}}

    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table class="table custom-table datatable">
        <thead>
            <tr>
              
                <th>S.No</th>
                <th>Template Name</th>
                <th>Subject</th>
                <th>Created At</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse($templates as $index => $template)
            <tr>
                <td data-label="S. No.">{{ $index + 1 }}</td>

<td data-label="Template Name">
    <span class="od-chip-highlight">{{ $template->name }}</span>
</td>

<td data-label="Subject">
    {{ $template->subject }}
</td>

<td data-label="Created At">
    {{ \Carbon\Carbon::parse($template->created_at)->format('d-m-Y H:i') }}
</td>

<td data-label="Actions" class="text-end">
    <div class="od-inline-actions">
        <a href="{{ route('termination-letter-templates.preview', $template->id) }}" class="od-icon-btn" title="Preview" target="_blank">
            <i class="fa fa-eye"></i>
        </a>
        @if(isset($permissions) && $permissions->can_edit)
        <a href="{{ route('termination-letter-templates.edit', $template->id) }}" class="od-icon-btn" title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        @endif
        @if(isset($permissions) && $permissions->can_delete)
        <button class="od-icon-btn danger" title="Delete" onclick="deleteTemplate({{ $template->id }}, '{{ $template->name }}')">
            <i class="fa fa-trash"></i>
        </button>
        @endif
    </div>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fa fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Templates Found</h5>
                        
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllTemplates = document.getElementById('checkAllTemplates');
const rowChecksTemplate = document.querySelectorAll('.row-check-template');

checkAllTemplates?.addEventListener('change', function() {
    rowChecksTemplate.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksTemplate.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

        </div>
    </div>
</div>

<!-- Hidden forms for delete actions (SweetAlert will submit these) -->
@foreach($templates as $template)
    <form id="delete-form-{{ $template->id }}" action="{{ route('termination-letter-templates.destroy', $template->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach




<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">



<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show success message if exists
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            showConfirmButton: false, // No confirm button
            timer: 3000, // Auto-close after 3 seconds
            timerProgressBar: true,
            toast: true, // Make it a toast
            position: 'top-end', // Position at top-end
            background: '#fff', // White background for toast
            color: '#333', // Dark text color
            iconColor: '#28a745' // Green icon color
        });
    @endif

    // Show error message if exists
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            showConfirmButton: false, // No confirm button
            timer: 3000, // Auto-close after 3 seconds
            timerProgressBar: true,
            toast: true, // Make it a toast
            position: 'top-end', // Position at top-end
            background: '#fff', // White background for toast
            color: '#333', // Dark text color
            iconColor: '#dc3545' // Red icon color
        });
    @endif

    // Delete confirmation function
    function deleteTemplate(id, templateName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete "${templateName}" template? This action cannot be undone!`,
            icon: 'warning',
            showConfirmButton: true,
            confirmButtonColor: '#dc3545', // Red confirm button
            confirmButtonText: 'Yes, delete it!',
            showCancelButton: true, 
            allowOutsideClick: true, 
            allowEscapeKey: true, // Prevent closing by pressing Escape
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                content: 'swal2-content-custom',
                confirmButton: 'swal2-confirm-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

<style>
    /* Custom SweetAlert2 styles for regular dialogs */
    .swal2-popup-custom {
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .swal2-title-custom {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }
    
    .swal2-content-custom {
        font-size: 1rem;
        color: #666;
    }
    
    .swal2-confirm-custom {
        border-radius: 5px;
        padding: 10px 20px;
        font-weight: 500;
    }
    
    /* SweetAlert2 Toast notification specific styles */
    .swal2-toast {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 10px 15px; /* Adjust padding for toasts */
    }
    
    .swal2-toast .swal2-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 5px; /* Space between title and text */
    }
    
    .swal2-toast .swal2-content {
        font-size: 0.9rem;
        margin-top: 0; /* Remove default margin */
    }
    
    /* Ensure progress bar is visible */
    .swal2-timer-progress-bar {
        background: rgba(0, 0, 0, 0.2); /* Darker for visibility on light background */
    }
</style>
@endsection