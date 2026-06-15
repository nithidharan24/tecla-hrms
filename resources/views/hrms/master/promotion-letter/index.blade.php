@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Promotion Letter Templates</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Promotion Letters</li>
            </ul>
        </div>
        <div class="col-auto float-right ml-auto">
            @if(isset($permissions) && $permissions->can_create)
            <a href="{{ route('promotion-letter.create') }}" class="btn add-btn"><i class="fa fa-plus"></i> Add Template</a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
       <div class="table-responsive">
    <table class="table custom-table datatable">
        <thead>
            <tr>
                
                <th>S.No</th>
                <th>Template Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse($templates as $index => $template)
            <tr>
                <td data-label="S.No">{{ $index + 1 }}</td>
<td data-label="Template Name"><span class="od-chip-highlight">{{ $template->name }}</span></td>
<td data-label="Created At">{{ $template->created_at->format('d M Y') }}</td>
<td data-label="Updated At">{{ $template->updated_at->format('d M Y') }}</td>
<td data-label="Actions" class="text-end">
    <div class="od-inline-actions">
        @if(isset($permissions) && $permissions->can_edit)
        <a href="{{ route('promotion-letter.edit', $template->id) }}" class="od-icon-btn" title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        @endif

        @if(isset($permissions) && $permissions->can_delete)
        <form action="{{ route('promotion-letter.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="od-icon-btn danger" title="Delete">
                <i class="fa fa-trash"></i>
            </button>
        </form>
        @endif

        <a href="{{ route('promotion-letter.preview', $template->id) }}" target="_blank" class="od-icon-btn" title="Preview">
            <i class="fa fa-eye"></i>
        </a>
    </div>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fa fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Templates Found</h5>
                        <p class="text-muted">Start by creating your first promotion letter template.</p>
                        @if(isset($permissions) && $permissions->can_create)
                        <a href="{{ route('promotion-letter.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Template
                        </a>
                        @endif
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
@endsection