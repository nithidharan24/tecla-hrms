@extends('layouts.index')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Site Customizations</h3>
                    <div>
                        <a href="{{ route('customize-site.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Customization
                        </a>
                        <a href="{{ route('customize-site.logo.management') }}" class="btn btn-success">
                            <i class="fas fa-image"></i> Logo Management
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="selectAll()">
                                    Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                                    Deselect All
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="bulkToggleStatus(1)">
                                    Activate Selected
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="bulkToggleStatus(0)">
                                    Deactivate Selected
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search customizations...">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchCustomizations()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Customizations Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="customizationsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Key</th>
                                    <th>Type</th>
                                    <th>Value/Preview</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customizations as $customization)
                                <tr data-id="{{ $customization->id }}">
                                    <td>
                                        <input type="checkbox" class="row-checkbox" value="{{ $customization->id }}">
                                    </td>
                                    <td>
                                        <strong>{{ $customization->key }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $customization->type === 'image' ? 'info' : ($customization->type === 'json' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($customization->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($customization->type === 'image' && $customization->value)
                                            <img src="{{ asset('storage/' . $customization->value) }}" 
                                                 alt="Preview" 
                                                 style="max-width: 100px; max-height: 50px; object-fit: contain;">
                                        @elseif($customization->type === 'json')
                                            <code>{{ Str::limit($customization->value, 50) }}</code>
                                        @else
                                            {{ Str::limit($customization->value, 50) }}
                                        @endif
                                    </td>
                                    <td>{{ $customization->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $customization->is_active ? 'success' : 'danger' }}">
                                            {{ $customization->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($customization->created_at)->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customize-site.show', $customization->id) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customize-site.edit', $customization->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="deleteCustomization({{ $customization->id }})"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No customizations found. <a href="{{ route('customize-site.create') }}">Create your first customization</a></p>
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
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this customization? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteCustomization(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/customize-site/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function selectAll() {
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllCheckbox');
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function getSelectedIds() {
    const selected = [];
    document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
        selected.push(checkbox.value);
    });
    return selected;
}

function bulkToggleStatus(status) {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one item.');
        return;
    }

    fetch('{{ route("customize-site.bulk.toggle-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status');
        }
    });
}

function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one item.');
        return;
    }

    if (confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
        fetch('{{ route("customize-site.bulk.delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting items');
            }
        });
    }
}

function searchCustomizations() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#customizationsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Real-time search
document.getElementById('searchInput').addEventListener('input', searchCustomizations);
</script>



@endsection