@extends('super_admin.layouts.app')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Manage Administrators</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admins</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto d-flex gap-2">
                <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i> Create Admin
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-message">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
            <i class="fa fa-exclamation-circle"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <script>
        setTimeout(function() {
            let alert = document.getElementById('alert-message');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    </script>

    <!-- Admins Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Administrators List</h5>
                    <span class="badge bg-primary">{{ $admins->count() }} Total</span>
                </div>
                <div class="card-body table-responsive">
                    @if($admins->count() > 0)
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $admin->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initials">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                                            </div>
                                            <span>{{ $admin->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                                    </td>
                                    <td>
                                        @if($admin->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('superadmin.admins.edit', $admin->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="setDeleteAdmin(this)" data-id="{{ $admin->id }}" data-name="{{ $admin->name }}" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-state-icon mb-3">
                                <i class="fa fa-users" style="font-size: 48px; color: #d1d5db;"></i>
                            </div>
                            <div class="empty-state-title mb-2">No Administrators Found</div>
                            <div class="empty-state-text mb-4">There are no administrators in the system yet.</div>
                            <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-2"></i> Create First Admin
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1a2332;
        margin: 0;
    }

    .breadcrumb {
        margin: 0.5rem 0 0 0;
        padding: 0;
        background: none;
    }

    .breadcrumb-item {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .breadcrumb-item a {
        color: #ff6b35;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #f7931e;
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1.5rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1a2332;
    }

    .table-responsive {
        margin: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .badge {
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .btn-group .btn {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
    }

    .empty-state {
        padding: 3rem 1rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d1d5db;
    }

    .empty-state-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
    }

    .empty-state-text {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .gap-2 {
        gap: 0.5rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ($.fn.dataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable({
                "order": [[0, 'desc']]
            });
        } else {
            $('.datatable').DataTable({
                "order": [[0, 'desc']]
            });
        }
    });

    function setDeleteAdmin(element) {
        const adminId = element.getAttribute('data-id');
        const adminName = element.getAttribute('data-name');

        Swal.fire({
            title: 'Delete Administrator',
            html: `Are you sure you want to delete <strong>${adminName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteAdmin(adminId);
            }
        });
    }

    function deleteAdmin(adminId) {
        window.location.href = "{{ url('superadmin/admins/delete') }}/" + adminId;
    }
</script>
@endsection