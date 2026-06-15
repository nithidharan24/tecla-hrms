@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Offer Letter');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Offer Letter Templates</h3>
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('offer-letter.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Template
                    </a>
                    @endif
                </div>
                <div class="card-body">
                   <div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Created At</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse($offerLetters as $letter)
            <tr>
                <td data-label="ID">{{ $letter->id }}</td>
                <td data-label="Title"><span class="od-chip-highlight">{{ $letter->title }}</span></td>
                <td data-label="Subject"><span class="high">{{ $letter->subject }}</span></td>
                <td data-label="Status">
                    @if($letter->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td data-label="Created At">{{ $letter->created_at->format('M d, Y') }}</td>
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        <!-- View PDF Button -->
                        <a href="{{ route('offer-letter.preview', $letter->id) }}" 
                           target="_blank" 
                           class="od-icon-btn primary" 
                           title="View PDF Template">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                        <!-- Edit Button -->
                        <a href="{{ route('offer-letter.edit', $letter->id) }}" 
                           class="od-icon-btn" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Activate/Deactivate Button -->
                        @if($letter->is_active)
                        <button onclick="toggleStatus({{ $letter->id }}, true)" 
                                class="od-icon-btn info" 
                                title="Deactivate">
                            <i class="fas fa-times"></i>
                        </button>
                        @else
                        <button onclick="toggleStatus({{ $letter->id }}, false)" 
                                class="od-icon-btn success" 
                                title="Activate">
                            <i class="fas fa-check"></i>
                        </button>
                        @endif

                        <!-- Delete Button -->
                        <button onclick="deleteTemplate({{ $letter->id }})" 
                                class="od-icon-btn danger" 
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No offer letter templates found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllLetters = document.getElementById('checkAllLetters');
const rowChecksLetter = document.querySelectorAll('.row-check-letter');

checkAllLetters?.addEventListener('change', function() {
    rowChecksLetter.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksLetter.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

                    {{ $offerLetters->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms for actions -->
@foreach($offerLetters as $letter)
    <form id="delete-form-{{ $letter->id }}" action="{{ route('offer-letter.destroy', $letter->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    {{-- Dynamic form for status toggle --}}
    <form id="status-form-{{ $letter->id }}" method="POST" style="display: none;">
        @csrf
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
            position: 'top-end'
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
            position: 'top-end'
        });
    @endif

    // Delete confirmation
    function deleteTemplate(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this template?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Toggle status confirmation
    function toggleStatus(id, isActive) {
        let title, text, confirmButtonText, confirmButtonColor, actionUrl;
        const form = document.getElementById('status-form-' + id);

        if (isActive) { // Currently active, so we want to deactivate
            title = 'Deactivate Template?';
            text = "This template will be set as inactive.";
            confirmButtonText = 'Yes, deactivate it!';
            confirmButtonColor = '#dc3545'; // Red for deactivate
            actionUrl = '{{ route('offer-letter.set-inactive', ':id') }}'.replace(':id', id);
        } else { // Currently inactive, so we want to activate
            title = 'Activate Template?';
            text = "This will deactivate all other templates and set this one as active.";
            confirmButtonText = 'Yes, activate it!';
            confirmButtonColor = '#28a745'; // Green for activate
            actionUrl = '{{ route('offer-letter.set-active', ':id') }}'.replace(':id', id);
        }

        Swal.fire({
            title: title,
            text: text,
            icon: isActive ? 'warning' : 'question', // Warning for deactivate, question for activate
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                form.action = actionUrl;
                form.submit();
            }
        });
    }

    // Function to view PDF (alternative method if needed)
    function viewPdf(id) {
        // Open in new tab
        window.open('{{ route('offer-letter.preview', ':id') }}'.replace(':id', id), '_blank');
    }
</script>

<style>
.od-inline-actions {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
    flex-wrap: nowrap;
}

.od-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.od-icon-btn.primary {
    background-color: #e3f2fd;
    color: #1976d2;
}

.od-icon-btn {
    background-color: #f5f5f5;
    color: #666;
}

.od-icon-btn.info {
    background-color: #e1f5fe;
    color: #0288d1;
}

.od-icon-btn.success {
    background-color: #e8f5e8;
    color: #388e3c;
}

.od-icon-btn.danger {
    background-color: #ffebee;
    color: #d32f2f;
}

.od-icon-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.od-icon-btn.primary:hover {
    background-color: #1976d2;
    color: white;
}

.od-icon-btn:hover {
    background-color: #e0e0e0;
    color: #333;
}

.od-icon-btn.info:hover {
    background-color: #0288d1;
    color: white;
}

.od-icon-btn.success:hover {
    background-color: #388e3c;
    color: white;
}

.od-icon-btn.danger:hover {
    background-color: #d32f2f;
    color: white;
}
</style>
@endsection