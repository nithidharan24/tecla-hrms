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
                <h3 class="page-title">Memo Templates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Memo Templates</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('memo.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Template
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table class="table custom-table datatable">
        <thead>
            <tr>
              
                <th>S.No</th>
                <th>Template Name</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse($memos as $index => $memo)
            <tr>
                <td data-label="S. No.">{{ $index + 1 }}</td>

<td data-label="Template Name">
    <span class="od-chip-highlight">{{ $memo->name }}</span>
</td>

@if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <td data-label="Actions" class="text-end">
        <div class="od-inline-actions">
            <a href="{{ route('memo.preview', $memo->id) }}" class="od-icon-btn" title="Preview" target="_blank">
                <i class="fa fa-eye"></i>
            </a>
            @if($permissions->can_edit)
            <a href="{{ route('memo.edit', $memo->id) }}" class="od-icon-btn" title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
            @endif
            @if($permissions->can_delete)
            <button class="od-icon-btn danger" title="Delete" onclick="deleteTemplate({{ $memo->id }}, '{{ addslashes($memo->name) }}')">
                <i class="fa fa-trash"></i>
            </button>
            @endif
        </div>
    </td>
@endif

            </tr>
            @empty
            <tr>
                <td colspan="{{ isset($permissions) && ($permissions->can_edit || $permissions->can_delete) ? 3 : 2 }}" class="text-center py-4">
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

        </div>
    </div>
</div>

<!-- Hidden forms for delete actions (SweetAlert will submit these) -->
@foreach($memos as $memo)
    <form id="delete-form-{{ $memo->id }}" action="{{ route('memo.destroy', $memo->id) }}" method="POST" style="display: none;">
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
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            background: '#fff',
            color: '#333',
            iconColor: '#28a745'
        });
    @endif

    // Show error message if exists
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            background: '#fff',
            color: '#333',
            iconColor: '#dc3545'
        });
    @endif

    // Delete confirmation function
    function deleteTemplate(id, templateName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete "${templateName}" template? This action cannot be undone!`,
            icon: 'warning',
            showConfirmButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete it!',
            showCancelButton: true, 
            allowOutsideClick: true, 
            allowEscapeKey: true,
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
    .swal2-toast {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 10px 15px;
    }
    .swal2-toast .swal2-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .swal2-toast .swal2-content {
        font-size: 0.9rem;
        margin-top: 0;
    }
    .swal2-timer-progress-bar {
        background: rgba(0, 0, 0, 0.2);
    }
</style>
@endsection
