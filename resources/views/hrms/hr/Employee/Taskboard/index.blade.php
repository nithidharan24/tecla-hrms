@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Task Board');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
<div class="container-fluid py-4">
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <h3 class="page-title mb-0 fw-semibold text-dark">
            Task Board
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-muted hover-text-primary">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-muted hover-text-primary">Task Board</a></li>
              
            </ul>
        </h3>
        
    </div>

    <div class="row g-4">
        @forelse($projects as $project)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card border-0 shadow-sm h-100 project-card">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-primary mb-2 text-truncate">
                                <i class="fa-solid fa-diagram-project me-2 text-secondary"></i> 
                                {{ $project->projectname }}
                            </h5>

                            {{-- Optional: Add description or stats here --}}
                            {{-- <p class="text-muted small mb-3">{{ Str::limit($project->description, 60) }}</p> --}}
                        </div>

                        <div class="mt-3 text-end">
                            <a href="{{ route('taskboard.create', ['projectid' => $project->id]) }}" 
                               class="btn btn-outline-primary btn-sm px-3">
                                <i class="fa-solid fa-eye me-1"></i> View Board
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="90" alt="No Projects">
                <h5 class="mt-3 text-muted">No projects found</h5>
                <p class="text-secondary small">Create a project to start managing tasks</p>
            </div>
        @endforelse
    </div>
</div>
</div>

{{-- Custom Styles --}}
@push('styles')
<style>
    .project-card {
        border-radius: 1rem;
        transition: all 0.3s ease-in-out;
        background-color: #fff;
        border: 1px solid #eaeaea;
    }

    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #007bff20;
    }

    .project-card .card-body {
        background: linear-gradient(180deg, rgba(0,123,255,0.04), rgba(255,255,255,1));
        border-radius: 1rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .btn-outline-primary {
        border-radius: 50px;
    }
</style>
@endpush
@endsection
