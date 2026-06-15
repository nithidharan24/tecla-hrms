@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <!-- Page Header -->
     <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('adminaccess.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add New Admin
                </a>
                @endif
            </div>
    <!-- Success/Error Messages -->
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- =====================================================
         ZOHO STYLE TABS
    ====================================================== -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#summary-tab">
                <i class="fa fa-chart-bar me-1"></i> Admin-Access Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $admins->count() }}</span>
            </a>
        </li>
    </ul>

    <!-- =====================================================
         TAB CONTENT AREA
    ====================================================== -->
    <div class="tab-content pt-4">

        <!-- =====================================================
             TAB 1 : SUMMARY
        ====================================================== -->
        <div class="tab-pane fade show active" id="summary-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-group m-b-30">
                        <!-- Total Admins Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-users text-primary"></i> Total Admins
                                    </span>
                                    <span class="text-success">+{{ $growthPercentage ?? '0' }}%</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $totalAdmins ?? count($admins) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: {{ min(($growthPercentage ?? 0), 100) }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All system administrators</small>
                            </div>
                        </div>

                        <!-- Active Admins Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-check-circle text-success"></i> Active Admins
                                    </span>
                                    <span class="text-success">+{{ $activeGrowthPercentage ?? '0' }}%</span>
                                </div>
                                <h3 class="mb-3 text-success">{{ $activeAdmins ?? $admins->where('status', 1)->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: {{ $activePercentage ?? '0' }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Currently active administrators</small>
                            </div>
                        </div>

                        <!-- Inactive Admins Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-times-circle text-danger"></i> Inactive Admins
                                    </span>
                                    <span class="text-danger">-{{ $inactiveGrowthPercentage ?? '0' }}%</span>
                                </div>
                                <h3 class="mb-3 text-danger">{{ $inactiveAdmins ?? $admins->where('status', 0)->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-danger" style="width: {{ $inactivePercentage ?? '0' }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Currently inactive administrators</small>
                            </div>
                        </div>

                        <!-- Modules Access Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-shield-alt text-info"></i> Total Modules
                                    </span>
                                    <span class="text-success">+{{ $modulesGrowthPercentage ?? '0' }}%</span>
                                </div>
                                <h3 class="mb-3 text-info">{{ $totalModules ?? '0' }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Modules with access permissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- END TAB 1 -->

        <!-- =====================================================
             TAB 2 : LIST WITH FILTER
        ====================================================== -->
        <div class="tab-pane fade" id="list-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Top Bar with Filter Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top:-10px;">
                                <h4 class="fw-bold mb-0">All Admins</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openAdminFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="adminFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Admins</h5>
                                    <button id="closeAdminFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('adminaccess.index') }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Admin Name</label>
                                        <input type="text" name="admin_name" value="{{ request('admin_name') }}" 
                                               class="form-control" placeholder="Search by name">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <input type="email" name="email" value="{{ request('email') }}" 
                                               class="form-control" placeholder="Search by email">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Branch</label>
                                        <select name="branch_id" class="form-select">
                                            <option value="">All Branches</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hierarchy Level</label>
                                        <select name="hierarchy_id" class="form-select">
                                            <option value="">All Hierarchies</option>
                                            @foreach($hierarchies as $hierarchy)
                                                <option value="{{ $hierarchy->id }}" {{ request('hierarchy_id') == $hierarchy->id ? 'selected' : '' }}>
                                                    {{ $hierarchy->hierarchy_level }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2 mt-3">
                                        <button class="btn btn-primary">Apply Filters</button>
                                        <a href="{{ route('adminaccess.index') }}" class="btn btn-light border">Reset</a>
                                    </div>
                                </form>
                            </div>

                            <!-- Admins Table -->
                            <div class="table-responsive">
                                <table class="table custom-table datatable mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Branch</th>
                                            <th>Hierarchy Level</th>
                                            <th>Module Count</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                <th class="text-end">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($admins as $admin)
                                        <tr>
                                            <td data-label="S.No">{{ $loop->iteration }}</td>

                                            <td data-label="Name">
                                                <span class="od-chip-highlight">{{ $admin->name }}</span>
                                            </td>

                                            <td data-label="Email">
                                                <span class="high">{{ $admin->email }}</span>
                                            </td>

                                            <td data-label="Branch">
                                                @if($admin->branch_name)
                                                    <span class="badge bg-info">{{ $admin->branch_name }}</span>
                                                    <br><small class="text-muted">{{ Str::limit($admin->branch_address, 30) }}</small>
                                                @else
                                                    <span class="badge bg-secondary">No Branch</span>
                                                @endif
                                            </td>

                                            <td data-label="Hierarchy">
                                                @if($admin->hierarchy_level)
                                                    <span class="badge bg-info">{{ $admin->hierarchy_level }}</span>
                                                @else
                                                    <span class="badge bg-secondary">No Hierarchy</span>
                                                @endif
                                            </td>

                                            <td data-label="Modules">
                                                <span class="badge bg-success">{{ $admin->module_count }} modules</span>
                                            </td>

                                            <td data-label="Status">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox"
                                                           data-admin-id="{{ $admin->id }}"
                                                           data-admin-name="{{ $admin->name }}"
                                                           {{ $admin->status == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label status-label-{{ $admin->id }}">
                                                        {{ $admin->status == 1 ? 'Active' : 'Inactive' }}
                                                    </label>
                                                </div>
                                            </td>

                                            <td data-label="Created At">{{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y') }}</td>

                                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                            <td data-label="Actions" class="text-end">
                                                <div class="od-inline-actions">
                                                    <a href="{{ route('adminaccess.show', $admin->id) }}" class="od-icon-btn" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                    <a href="{{ route('adminaccess.edit', $admin->id) }}" class="od-icon-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endif
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                  <form action="{{ route('adminaccess.destroy', $admin->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')

    <button type="submit" class="od-icon-btn danger"
        onclick="return confirm('Are you sure you want to delete this admin?');"
        title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</form>
                                                    @endif
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ isset($permissions) && ($permissions->can_edit || $permissions->can_delete) ? '9' : '8' }}" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fa fa-users fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No Admins Found</h5>
                                                    <p class="text-muted">No administrators found matching your criteria.</p>
                                                    <a href="{{ route('adminaccess.create') }}" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Create New Admin
                                                    </a>
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
        </div> <!-- END TAB 2 -->

    </div> <!-- END tab-content -->
</div>

<!-- Include jQuery and SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Zoho Style Tabs */
    .leave-tabs .nav-link {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        margin-top: -25px;
        border-bottom: 3px solid transparent;
        padding: 10px 18px;
    }

    .leave-tabs .nav-link.active {
        color: #f97316;
        border-bottom: 3px solid #f97316;
    }

    .tabs-underline {
        width: 100%;
        height: 2px;
        background: #e5eaf2;
        margin-top: -4px;
        margin-bottom: 12px;
    }

    /* Filter Slide Panel Styles */
    .filter-slide-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 380px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .filter-slide-panel.active {
        right: 0;
    }

    .filter-square-btn {
        width: 42px;
        height: 42px;
        background: #fff;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #595959;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .filter-square-btn:hover {
        background: #f5f5f5;
    }

    /* Overlay when filter is open */
    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1049;
        display: none;
    }

    .filter-overlay.active {
        display: block;
    }

    /* Tab Content Area */
    .tab-content {
        margin-top: 20px;
    }

    /* Card Group Styling */
    .card-group.m-b-30 .card {
        border: 1px solid #e5eaf2;
        border-radius: 8px;
        margin-right: 15px;
    }

    .card-group.m-b-30 .card:last-child {
        margin-right: 0;
    }

    /* Form Switch Styling */
    .form-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
        cursor: pointer;
    }

    .form-switch .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .form-switch .form-check-input:not(:checked) {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .form-check-label {
        margin-left: 10px;
        cursor: pointer;
    }

    /* Alert Styling */
    .alert {
        position: relative;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1.25rem 1rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 0;
    }

    .empty-state i {
        opacity: 0.5;
    }

    .empty-state h5 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            });
        }, 5000);

        // Filter Panel Functionality
        const openFilterBtn = document.getElementById('openAdminFilterBtn');
        const closeFilterBtn = document.getElementById('closeAdminFilterBtn');
        const filterPanel = document.getElementById('adminFilterPanel');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
            };
        }

        if (closeFilterBtn) {
            closeFilterBtn.onclick = () => {
                filterPanel.classList.remove('active');
            };
        }

        // Close filter when clicking outside
        document.addEventListener('click', (e) => {
            if (filterPanel.classList.contains('active') && 
                !filterPanel.contains(e.target) && 
                e.target !== openFilterBtn) {
                filterPanel.classList.remove('active');
            }
        });

        // Status Toggle Handler
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const adminId = this.getAttribute('data-admin-id');
                const adminName = this.getAttribute('data-admin-name');
                const isChecked = this.checked;
                const newStatus = isChecked ? 'Active' : 'Inactive';
                const toggleElement = this;
                
                Swal.fire({
                    title: 'Change Status?',
                    text: `Are you sure you want to change ${adminName}'s status to ${newStatus}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create form data
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('status', isChecked ? 1 : 0);

                        fetch("{{ route('adminaccess.changeStatus', ':id') }}".replace(':id', adminId), {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(response => {
                            Swal.close();
                            if (response.success) {
                                // Update the label
                                const label = document.querySelector(`.status-label-${adminId}`);
                                if (label) {
                                    label.textContent = response.status_text;
                                }
                                
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            } else {
                                // Revert toggle state
                                toggleElement.checked = !isChecked;
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            // Revert toggle state
                            toggleElement.checked = !isChecked;
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to change status. Please try again.',
                                icon: 'error'
                            });
                        });
                    } else {
                        // Revert toggle state if cancelled
                        this.checked = !isChecked;
                    }
                });
            });
        });

        // Delete Admin Handler
        document.querySelectorAll('.delete-admin').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.getAttribute('data-admin-id');
                const adminName = this.getAttribute('data-admin-name');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will permanently delete ${adminName} and all related data. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete permanently!',
                    cancelButtonText: 'Cancel',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading indicator
                        Swal.fire({
                            title: 'Processing',
                            html: 'Please wait while we delete the admin...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Create and submit form for deletion
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/adminaccess/${adminId}`;
                        
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = '{{ csrf_token() }}';
                        form.appendChild(tokenInput);
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection