@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Termination');     
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Termination List</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Terminations</li>
                </ul>
            </div>
          
        </div>
    </div>
    <!-- /Page Header -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
         <div class="table-responsive">
    <table class="table custom-table datatable" id="termination-table">
        <thead>
            <tr>
              
                <th>Employee</th>
                <th>Department</th>
                <th>Termination Type</th>
                <th>Termination Date</th>
                <th>Notice Date</th>
                <th>Reason</th>
                <th class="text-end no-sort">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($terminations as $termination)
                <tr>
                    
                    <td data-label="Employee">
                        <a class="high" href="#">
                            {{ $termination->firstname }} {{ $termination->lastname }} 
                            <span>{{ $termination->employeeid }}</span>
                        </a>
                    </td>
                    
                    <td data-label="Department"><span class="od-chip-highlight">{{ $termination->department_name }}</span></td>
                    
                    <td data-label="Termination Type"><span>{{ $termination->termination_type }}</span></td>
                    
                    <td data-label="Termination Date">{{ \Carbon\Carbon::parse($termination->termination_date)->format('d M Y') }}</td>
                    
                    <td data-label="Notice Date">{{ \Carbon\Carbon::parse($termination->notice_date)->format('d M Y') }}</td>
                    
                    <td data-label="Reason">{{ $termination->reason }}</td>
                    
                    <td data-label="Actions" class="text-end">
                        <div class="od-inline-actions">
                            {{-- Added View button to show terminated employee details --}}
                            <a href="{{ route('terminations.show', $termination->id) }}" class="od-icon-btn" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            @if(isset($permissions) && $permissions->can_edit)
                            <a href="{{ route('terminations.edit', $termination->id) }}" class="od-icon-btn" title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            @endif
                    
                            @if(isset($permissions) && $permissions->can_delete)
                            <button type="button" class="od-icon-btn danger" title="Delete" data-bs-toggle="modal" data-bs-target="#delete_termination_{{ $termination->id }}">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            @endif
                    
                            <button type="button" class="od-icon-btn" title="Send Email" data-id="{{ $termination->id }}" onclick="sendTerminationEmail(this)">
                                <i class="fa-regular fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                    
                </tr>

                <!-- Delete Modal -->
                <div class="modal custom-modal fade" id="delete_termination_{{ $termination->id }}" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="form-header">
                                    <h3>Delete Termination</h3>
                                    <p>Are you sure you want to delete this termination record?</p>
                                </div>
                                <div class="modal-btn delete-action">
                                    <div class="row">
                                        <div class="col-6">
                                            <form action="{{ route('terminations.destroy', $termination->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-primary continue-btn w-100">Delete</button>
                                            </form>
                                        </div>
                                        <div class="col-6">
                                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Delete Modal -->
            @empty
                <tr>
                    <td colspan="8" class="text-center">No terminations found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function sendTerminationEmail(button) {
    const terminationId = button.dataset.id;
    if(confirm('Are you sure you want to send the termination email?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("terminations") }}/' + terminationId + '/send-email';
        form.style.display = 'none';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}

// Checkbox select all functionality
document.getElementById('checkAll')?.addEventListener('change', function(){
    document.querySelectorAll('#termination-table .row-check').forEach(cb => cb.checked = this.checked);
});
</script>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Auto-hide success/error messages after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.classList.remove('show');
            successMessage.classList.add('fade');
        }
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.classList.remove('show');
            errorMessage.classList.add('fade');
        }
    }, 5000);
});
</script>
<script>
const checkAll = document.getElementById('checkAll');
const rows = document.querySelectorAll('.row-check');

checkAll?.addEventListener('change', function() {
    rows.forEach(r => {
        r.checked = this.checked;
        r.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rows.forEach(r => {
    r.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

@endsection
