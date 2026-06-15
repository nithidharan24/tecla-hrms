@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Hike Letter Templates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hike Letters</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('hike-letter.create') }}" class="btn add-btn"><i class="fa fa-plus"></i> Add Template</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
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
                        <a href="{{ route('hike-letter.show', $template->id) }}" class="od-icon-btn" title="View">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route('hike-letter.edit', $template->id) }}" class="od-icon-btn" title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="{{ route('hike-letter.preview', $template->id) }}" class="od-icon-btn" title="Preview" target="_blank">
                            <i class="fa fa-file"></i>
                        </a>
                        <button type="button" class="od-icon-btn danger" title="Delete"
                            onclick="deleteHikeTemplate({{ $template->id }}, '{{ $template->name }}')">
                            <i class="fa fa-trash"></i>
                        </button>
                
                        {{-- Hidden delete form --}}
                        <form id="delete-form-{{ $template->id }}" action="{{ route('hike-letter.destroy', $template->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
                
                
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fa fa-file-text fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Hike Letter Templates Found</h5>
                        <p class="text-muted">Start by creating your first template.</p>
                        <a href="{{ route('hike-letter.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Template
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const checkAllHike = document.getElementById('checkAllHikeTemplates');
const rowChecksHike = document.querySelectorAll('.row-check-hike');

checkAllHike?.addEventListener('change', function() {
    rowChecksHike.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksHike.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});


function deleteHikeTemplate(id, name) {
    Swal.fire({
        title: 'Delete Template?',
        text: `Are you sure you want to delete "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}


</script>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection