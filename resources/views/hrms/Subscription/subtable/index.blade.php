@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Subscribed Users</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Subscribed Users</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('subscribetable.create') }}" class="btn add-btn">
                     Add Subscription
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="subscription-table" class="table custom-table datatable mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px;">
                                <input type="checkbox" class="od-check" id="checkAll" aria-label="Select all">
                            </th>
                            <th>#</th>
                            <th>Client</th>
                            <th>Plan</th>
                            <th>Plan Duration</th>
                            <th>Users</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                           @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscribedCompanies as $index => $company)
                        <tr>
                            <td>
                                <input type="checkbox" class="od-check row-check" aria-label="Select row">
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="high">{{ $company->client }}</span></td>
                            <td><span class="digh">{{ $company->plan }}</span></td>
                            <td>{{ $company->plan_duration }}</td>
                            <td>{{ $company->users }}</td>
                            <td>{{ \Carbon\Carbon::parse($company->start_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($company->end_date)->format('d-m-Y') }}</td>
                            <td>
                                <span class="badge {{ $company->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="od-inline-actions">
                                
                                    <button type="button" class="od-icon-btn danger delete-btn" 
                                            data-id="{{ $company->id }}" 
                                            data-name="{{ $company->client }}"
                                            title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
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

<!-- Delete Form (Hidden) -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Checkbox functionality
const checkAll = document.getElementById('checkAll');
const rowChecks = document.querySelectorAll('.row-check');

checkAll?.addEventListener('change', function() {
    rowChecks.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecks.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

// SweetAlert Delete Confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const companyId = this.getAttribute('data-id');
            const companyName = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete subscription for "${companyName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set up the form action and submit
                    const form = document.getElementById('delete-form');
                    form.action = `{{ url('subscribetable') }}/${companyId}`;
                    form.submit();
                }
            });
        });
    });
});

// Success message handling
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif
</script>

@endsection