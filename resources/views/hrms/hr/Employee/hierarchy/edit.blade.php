@php
    // Since only one company exists, just fetch first row
    $company = DB::table('subcompany')->first();

    $planId = $company->plan_id ?? null;

    $allowedModules = [];

    if ($planId) {
        $allowedModules = DB::table('plan_modules')
            ->join('modules', 'plan_modules.module_id', '=', 'modules.id')
            ->where('plan_modules.plan_id', $planId)
            ->pluck('modules.name')
            ->toArray();
    }

    // Decode the hierarchy modules
    $decodedModules = is_array($hierarchy->modules) ? $hierarchy->modules : json_decode($hierarchy->modules ?? '{}', true);
    if (!is_array($decodedModules)) {
        $decodedModules = [];
    }
@endphp

@extends('layouts.index')
@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Hierarchy Level</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('hierarchy.index') }}" class="breadcrumb-link">Hierarchy Levels</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show permanent" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show permanent" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show permanent" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Hierarchy Level</h4>
                    <div class="card-body">
                        <form id="hierarchyForm" method="POST" action="{{ route('hierarchy.update', $hierarchy->id) }}" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Hierarchy Level Name -->
                                <div class="col-md-12 mb-3">
                                    <label for="hierarchy_level">Hierarchy Level Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="hierarchy_level" id="hierarchy_level" placeholder="e.g., Manager, Team Lead, Supervisor, Executive" value="{{ old('hierarchy_level', $hierarchy->hierarchy_level) }}" required>
                                    <div class="invalid-feedback">Hierarchy level name is required.</div>
                                    @error('hierarchy_level')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Module Permissions Section -->
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">Module Permissions</h5>
                                                <p class="text-muted mb-0">Configure module access and permissions for this hierarchy level</p>
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Changes will affect all employees assigned to this hierarchy
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary" id="enabledModuleCount">0 enabled</span>
                                                <span class="badge bg-info" id="totalPermissionsCount">0 permissions</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Search and Action Buttons -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                        <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">
                                                        <button class="btn btn-outline-primary" type="button" id="selectAllModulesBtn">Select All Modules</button>
                                                        <button class="btn btn-outline-primary" type="button" id="selectAllPermissionsBtn">Select All Permissions</button>
                                                        <button class="btn btn-outline-secondary" type="button" id="deselectAllBtn">Clear All</button>
                                                    </div>
                                                </div>

                                                <!-- Permissions Legend -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="border rounded p-2 bg-light">
                                                        <div class="row text-center">
                                                            <div class="col"><span class="badge bg-success"><i class="fas fa-eye"></i> View</span></div>
                                                            <div class="col"><span class="badge bg-primary"><i class="fas fa-plus"></i> Create</span></div>
                                                            <div class="col"><span class="badge bg-warning text-dark"><i class="fas fa-edit"></i> Edit</span></div>
                                                            <div class="col"><span class="badge bg-danger"><i class="fas fa-trash"></i> Delete</span></div>
                                                            <div class="col"><span class="badge bg-info"><i class="fas fa-check"></i> Approve</span></div>
                                                            <div class="col"><span class="badge bg-secondary"><i class="fas fa-download"></i> Download</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Recruitment -->
                                                @if(in_array('Recruitment Management', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-user-plus"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Recruitment</h6>
                                                                    <small class="text-muted">Hiring & onboarding processes</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Recruitment';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_recruitment" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_recruitment">
                                                                        Recruitment Management
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_recruitment"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_recruitment">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_recruitment"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_recruitment">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_recruitment"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_recruitment">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_recruitment"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_recruitment">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Leaves Management -->
                                                @if(in_array('Leaves', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-success bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Leaves Management</h6>
                                                                    <small class="text-muted">Leave requests & approvals</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['Team Leaves', 'Employee Leaves'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_leaves_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_leaves_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_leaves_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_leaves_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_leaves_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_leaves_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_leaves_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_approve'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-info" for="approve_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Attendance -->
                                                @if(in_array('Attendance', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-clock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Attendance</h6>
                                                                    <small class="text-muted">Time tracking & punch management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['Admin Attendance', 'Employee Attendance', 'Late Punch Approval'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_attendance_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_attendance_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_attendance_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_attendance_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_attendance_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_attendance_{{ $loop->index }}">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_attendance_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_attendance_{{ $loop->index }}">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_attendance_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_attendance_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @if($module == 'Late Punch Approval')
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_attendance_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_approve'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-info" for="approve_attendance_{{ $loop->index }}">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Time Tracker -->
                                                @if(in_array('Time Tracker', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-warning bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-stopwatch"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Time Tracker</h6>
                                                                    <small class="text-muted">Project time & task management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['Clients', 'Projects', 'Project Tasks', 'My Tasks'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_timetracker_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_timetracker_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        @if($module != 'My Tasks')
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_timetracker_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_timetracker_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_timetracker_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_timetracker_{{ $loop->index }}">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_timetracker_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_timetracker_{{ $loop->index }}">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_timetracker_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_timetracker_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @else
                                                                        <div class="col-12">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_timetracker_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_timetracker_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View Only
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Employee Management -->
                                                @if(in_array('Onboarding', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-users"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Employee Management</h6>
                                                                    <small class="text-muted">Employee data & profiles</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Employee';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_employee" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_employee">
                                                                        Employee Management
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_employee"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_employee">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_employee"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_employee">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_employee"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_employee">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_employee"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_employee">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Shifts -->
                                                @if(in_array('Shifts', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-user-clock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Shifts</h6>
                                                                    <small class="text-muted">Shift management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Manage Shifts';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_shifts"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_shifts">
                                                                        Manage Shifts
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_shifts"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_shifts">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_shifts"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_shifts">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_shifts"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_shifts">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_shifts"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_shifts">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Schedule -->
                                                @if(in_array('Schedule', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Schedule</h6>
                                                                    <small class="text-muted">Work scheduling</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Schedule';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_schedule"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_schedule">
                                                                        Schedule
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_schedule"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_schedule">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_schedule"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_schedule">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_schedule"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_schedule">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_schedule"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_schedule">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Payroll -->
                                                @if(in_array('Payroll', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-dark bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-money-bill"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Payroll</h6>
                                                                    <small class="text-muted">Salary & compensation management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['Payroll Items', 'Employee Salary', 'Automated Payslips', 'Activity Log'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_payroll_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_payroll_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        @if($module != 'Activity Log')
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_payroll_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_payroll_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_payroll_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_payroll_{{ $loop->index }}">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_payroll_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_payroll_{{ $loop->index }}">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_payroll_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_payroll_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @else
                                                                        <div class="col-12">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_payroll_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_payroll_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View Only
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Tickets -->
                                                @if(in_array('Tickets', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-ticket-alt"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Tickets</h6>
                                                                    <small class="text-muted">Support tickets management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Tickets';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_tickets"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_tickets">
                                                                        Tickets
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_tickets"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_tickets">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_tickets"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_tickets">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_tickets"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_tickets">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_tickets"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_tickets">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_approve]"
                                                                                       value="1"
                                                                                       id="approve_tickets"
                                                                                       {{ ($permissions['can_approve'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-info" for="approve_tickets">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Testing -->
                                                @if(in_array('Testing', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-vial"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Testing</h6>
                                                                    <small class="text-muted">Quality testing module</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Testing';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_testing"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_testing">
                                                                        Testing
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_testing"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_testing">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_testing"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_testing">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_testing"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_testing">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_testing"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_testing">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_approve]"
                                                                                       value="1"
                                                                                       id="approve_testing"
                                                                                       {{ ($permissions['can_approve'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-info" for="approve_testing">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Accounts -->
                                                @if(in_array('Accounts', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Accounts</h6>
                                                                    <small class="text-muted">Financial management & accounting</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['Estimates', 'Invoices', 'Payments', 'Expenses', 'Taxes', 'Categories', 'Budgets', 'Budget Expenses', 'Budget Revenues'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_accounts_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_accounts_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_accounts_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_create]" 
                                                                                       value="1" 
                                                                                       id="create_accounts_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_edit]" 
                                                                                       value="1" 
                                                                                       id="edit_accounts_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_delete]" 
                                                                                       value="1" 
                                                                                       id="delete_accounts_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Reports -->
                                                @if(in_array('Reports', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                                    <i class="fas fa-chart-bar"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Reports</h6>
                                                                    <small class="text-muted">Analytics & performance reports</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @foreach(['My Reports', 'Team Reports', 'Organization Reports'] as $module)
                                                            @php
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item mb-3">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox" 
                                                                           type="checkbox" 
                                                                           id="module_reports_{{ $loop->index }}" 
                                                                           name="modules[{{ $module }}][enabled]" 
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_reports_{{ $loop->index }}">
                                                                        {{ $module }}
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_reports_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_reports_{{ $loop->index }}">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_download]" 
                                                                                       value="1" 
                                                                                       id="download_reports_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_download'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="download_reports_{{ $loop->index }}">
                                                                                    <i class="fas fa-download"></i> Download
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @if($module == 'Team Reports' || $module == 'Organization Reports')
                                                                        <div class="col-4">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox" 
                                                                                       type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_reports_{{ $loop->index }}"
                                                                                       {{ ($permissions['can_approve'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-info" for="approve_reports_{{ $loop->index }}">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Policy -->
                                                @if(in_array('Policy', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-success bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Policies & Files</h6>
                                                                    <small class="text-muted">Company policies and documents</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Policy';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_policy"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_policy">
                                                                        Policies & Files
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_policy"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_policy">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_policy"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_policy">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_policy"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_policy">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_policy"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_policy">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Goals -->
                                                @if(in_array('Goals', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-bullseye"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Goals</h6>
                                                                    <small class="text-muted">Goal management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Goals';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_goals"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_goals">
                                                                        Goals
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_goals"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_goals">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_goals"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_goals">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_goals"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_goals">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_goals"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_goals">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Assets -->
                                                @if(in_array('Assets', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-warning bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-box"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Assets</h6>
                                                                    <small class="text-muted">Asset management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Assets';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_assets"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_assets">
                                                                        Assets
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_assets"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_assets">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_assets"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_assets">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_assets"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_assets">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_assets"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_assets">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Training -->
                                                @if(in_array('Training', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Training</h6>
                                                                    <small class="text-muted">Employee training</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Training';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_training"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_training">
                                                                        Training
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_training"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_training">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_training"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_training">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_training"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_training">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_training"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_training">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Travel -->
                                                @if(in_array('Travel', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-plane"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Travel</h6>
                                                                    <small class="text-muted">Travel requests & approvals</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Travel';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_travel"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_travel">
                                                                        Travel
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_travel"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_travel">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_travel"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_travel">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_travel"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_travel">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_travel"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_travel">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Offboarding -->
                                                @if(in_array('Offboarding', $allowedModules))
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-user-minus"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">Offboarding</h6>
                                                                    <small class="text-muted">Employee exit management</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Offboarding';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_offboarding"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_offboarding">
                                                                        Offboarding
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_offboarding"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_offboarding">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_create]"
                                                                                       value="1"
                                                                                       id="create_offboarding"
                                                                                       {{ ($permissions['can_create'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-primary" for="create_offboarding">
                                                                                    <i class="fas fa-plus"></i> Create
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_offboarding"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_offboarding">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_delete]"
                                                                                       value="1"
                                                                                       id="delete_offboarding"
                                                                                       {{ ($permissions['can_delete'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-danger" for="delete_offboarding">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Settings (always available) -->
                                                <div class="col-lg-6 mb-3">
                                                    <div class="category-card h-100">
                                                        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">
                                                                    <i class="fas fa-cog"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fw-semibold mb-0">System Settings</h6>
                                                                    <small class="text-muted">System configuration</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="category-body p-3">
                                                            @php
                                                            $module = 'Settings';
                                                            $isEnabled = isset($decodedModules[$module]);
                                                            $permissions = $isEnabled ? $decodedModules[$module] : [];
                                                            @endphp
                                                            <div class="module-item">
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_settings"
                                                                           name="modules[{{ $module }}][enabled]"
                                                                           value="1"
                                                                           {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold" for="module_settings">
                                                                        Settings
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-grid" style="display: {{ $isEnabled ? 'block' : 'none' }};">
                                                                    <div class="row g-2">
                                                                        <div class="col-6">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_view]"
                                                                                       value="1"
                                                                                       id="view_settings"
                                                                                       {{ ($permissions['can_view'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-success" for="view_settings">
                                                                                    <i class="fas fa-eye"></i> View
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="permission-check">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                       type="checkbox"
                                                                                       name="modules[{{ $module }}][can_edit]"
                                                                                       value="1"
                                                                                       id="edit_settings"
                                                                                       {{ ($permissions['can_edit'] ?? false) ? 'checked' : '' }}>
                                                                                <label class="form-check-label text-warning" for="edit_settings">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button id="updateHierarchyBtn" class="btn btn-primary btn-lg" type="submit">
                                        <i class="fas fa-save me-2"></i>Update Hierarchy Level
                                    </button>
                                    <a href="{{ route('hierarchy.index') }}" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .category-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
    }
    
    .text-muted {
        color: white !important;
    }

    .category-header {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .category-body {
        padding: 15px;
        max-height: 300px;
        overflow-y: auto;
    }

    .module-item {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .module-item:last-child {
        border-bottom: none;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .permissions-grid {
        background: #f8f9ff;
        border-left: 3px solid #0d6efd;
        padding: 10px;
        margin-top: 10px;
        border-radius: 4px;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .permission-check {
        padding: 5px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .permission-check:hover {
        background: rgba(0, 123, 255, 0.1);
    }

    .permission-check .form-check-input {
        margin-right: 5px;
        cursor: pointer;
    }

    .permission-check .form-check-label {
        cursor: pointer;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    #moduleSearch {
        border-radius: 4px 0 0 4px;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    /* Custom scrollbar */
    .category-body::-webkit-scrollbar {
        width: 6px;
    }

    .category-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .category-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .category-body::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Toggle permissions grid when module is enabled/disabled
    document.querySelectorAll('.module-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permissionsGrid = this.closest('.module-item').querySelector('.permissions-grid');
            if (this.checked) {
                permissionsGrid.style.display = 'block';
                // Auto-check view permission when module is enabled (if not already checked)
                const viewCheckbox = permissionsGrid.querySelector('input[name*="[can_view]"]');
                if (viewCheckbox && !viewCheckbox.checked) {
                    viewCheckbox.checked = true;
                }
            } else {
                permissionsGrid.style.display = 'none';
                // Uncheck all permission checkboxes
                permissionsGrid.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = false;
                });
            }
            updateModuleCount();
        });
    });

    // Module search functionality
    const moduleSearch = document.getElementById('moduleSearch');
    
    moduleSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        document.querySelectorAll('.module-item').forEach(item => {
            const moduleName = item.querySelector('.form-check-label').textContent.toLowerCase();
            const categoryCard = item.closest('.category-card');
            
            if (moduleName.includes(searchTerm) || searchTerm === '') {
                item.style.display = 'block';
                categoryCard.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Hide categories with no visible modules
        document.querySelectorAll('.category-card').forEach(card => {
            const visibleModules = card.querySelectorAll('.module-item[style*="block"]');
            if (visibleModules.length === 0 && searchTerm !== '') {
                card.style.display = 'none';
            }
        });
    });

    // Clear search
    document.getElementById('clearSearch').addEventListener('click', function() {
        moduleSearch.value = '';
        moduleSearch.dispatchEvent(new Event('input'));
        moduleSearch.focus();
    });

    // Select All Modules
    document.getElementById('selectAllModulesBtn').addEventListener('click', function() {
        document.querySelectorAll('.module-checkbox').forEach(checkbox => {
            if (!checkbox.checked && checkbox.closest('.category-card').style.display !== 'none') {
                checkbox.checked = true;
                // Trigger change event to show permissions
                const event = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(event);
            }
        });
    });

    // Select All Permissions
    document.getElementById('selectAllPermissionsBtn').addEventListener('click', function() {
        document.querySelectorAll('.module-checkbox:checked').forEach(moduleCheckbox => {
            const permissionsGrid = moduleCheckbox.closest('.module-item').querySelector('.permissions-grid');
            if (permissionsGrid.style.display !== 'none') {
                permissionsGrid.querySelectorAll('.permission-checkbox').forEach(permCheckbox => {
                    permCheckbox.checked = true;
                });
            }
        });
    });

    // Deselect All
    document.getElementById('deselectAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all selections?')) {
            document.querySelectorAll('.module-checkbox, .permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.permissions-grid').forEach(grid => {
                grid.style.display = 'none';
            });
            updateModuleCount();
        }
    });

    // Update module count
    function updateModuleCount() {
        const enabledCount = document.querySelectorAll('.module-checkbox:checked').length;
        let totalPermissions = 0;
        document.querySelectorAll('.permission-checkbox:checked').forEach(() => totalPermissions++);
        
        document.getElementById('enabledModuleCount').textContent = enabledCount + ' enabled';
        document.getElementById('totalPermissionsCount').textContent = totalPermissions + ' permissions';
    }

    // Form validation
    const form = document.getElementById('hierarchyForm');
    form.addEventListener('submit', function(event) {
        const hierarchyLevel = document.getElementById('hierarchy_level');
        
        if (!hierarchyLevel.value.trim()) {
            event.preventDefault();
            hierarchyLevel.classList.add('is-invalid');
            showNotification('Hierarchy level name is required', 'error');
            return;
        }
        
        const enabledModules = document.querySelectorAll('.module-checkbox:checked');
        if (enabledModules.length === 0) {
            event.preventDefault();
            showNotification('Please select at least one module', 'error');
            return;
        }
        
        // Show confirmation dialog
        if (!confirm('Updating this hierarchy will affect all employees assigned to it. Are you sure you want to continue?')) {
            event.preventDefault();
            return;
        }
        
        // Disable submit button
        const submitBtn = document.getElementById('updateHierarchyBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
    }, false);

    // Real-time validation
    document.getElementById('hierarchy_level').addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
        }
    });

    // Show notification function
    function showNotification(message, type) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
        const notification = $(`<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`);
        
        $('#hierarchyForm').prepend(notification);
        
        setTimeout(function() {
            notification.fadeOut();
        }, 5000);
    }

    // Initialize module count
    updateModuleCount();
});
</script>
@endsection