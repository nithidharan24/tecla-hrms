@extends('super_admin.layouts.app')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Administrator</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Update Administrator Details</h5>
                </div>
                <div class="card-body">

                    {{-- SUCCESS MESSAGE --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-message">
                            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- VALIDATION ERRORS --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('superadmin.admins.update', $admin->id) }}" method="POST" id="editAdminForm">
                        @csrf

                        <div class="row">
                            {{-- NAME --}}
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ $admin->name }}" required>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- EMAIL (Read-only) --}}
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Email Address</label>
                                    <input type="email" class="form-control" value="{{ $admin->email }}" readonly>
                                    <small class="form-text text-muted">Email cannot be changed</small>
                                </div>
                            </div>

                            {{-- ROLE --}}
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select select @error('role') is-invalid @enderror" required>
                                        <option value="super_admin" {{ $admin->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="sub_admin" {{ $admin->role == 'sub_admin' ? 'selected' : '' }}>Sub Admin</option>
                                    </select>
                                    @error('role')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ $admin->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $admin->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- ADMIN INFO (Read-only) --}}
                            <div class="col-sm-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <strong>Created Date</strong>
                                                    <span>{{ $admin->created_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <strong>Last Updated</strong>
                                                    <span>{{ $admin->updated_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i> Update Administrator
                            </button>
                            <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
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

    .input-block {
        margin-bottom: 1.25rem;
    }

    .col-form-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.1);
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #f46a6a !important;
    }

    .form-control.is-invalid:focus, .form-select.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.1);
    }

    .text-danger {
        color: #f46a6a;
    }

    .card.bg-light {
        background: #f8f9fa !important;
        border: 1px solid #dee2e6;
    }

    .info-item {
        margin-bottom: 0.75rem;
    }

    .info-item strong {
        display: block;
        color: #495057;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-item span {
        color: #1a2332;
        font-weight: 500;
    }

    .submit-section {
        border-top: 1px solid #dee2e6;
        padding-top: 1.5rem;
        margin-top: 1rem;
        text-align: right;
    }

    .submit-section .btn {
        margin-left: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.3);
    }

    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .form-text.text-muted {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<script>
    setTimeout(function() {
        let alert = document.getElementById('alert-message');
        if (alert) {
            alert.remove();
        }
    }, 3000);
</script>
@endsection