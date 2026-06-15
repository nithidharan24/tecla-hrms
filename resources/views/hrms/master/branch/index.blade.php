@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')
@section('content')
    <div class="content container-fluid">
       <div class="col-auto float-end ms-auto">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('branches.create') }}" class="btn add-btn">
                        <i class="fa fa-plus"></i> Add Branch
                    </a>
                    @endif
                </div>

        <!-- =====================================================
             ZOHO STYLE TABS
        ====================================================== -->
        <ul class="nav leave-tabs mb-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#summary-tab">
                    <i class="fa fa-chart-bar me-1"></i>Branch Summary
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                    <i class="fa fa-list me-1"></i> List
                    <span class="badge bg-primary ms-1">{{ $branches->count() }}</span>
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
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="d-block">
                                            <i class="fa fa-building text-primary"></i> Total Branches
                                        </span>
                                        <span class="text-success">+{{ $growthPercentage }}%</span>
                                    </div>
                                    <h3 class="mb-3 text-primary">{{ $totalBranches }}</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-primary" style="width: {{ min($growthPercentage, 100) }}%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="d-block">
                                            <i class="fa fa-check-circle text-success"></i> Active Branches
                                        </span>
                                        <span class="text-success">+{{ $activeGrowthPercentage }}%</span>
                                    </div>
                                    <h3 class="mb-3 text-success">{{ $activeBranches }}</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-success" style="width: {{ $activePercentage }}%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="d-block">
                                            <i class="fa fa-times-circle text-danger"></i> Inactive Branches
                                        </span>
                                        <span class="text-danger">-{{ $inactiveGrowthPercentage }}%</span>
                                    </div>
                                    <h3 class="mb-3 text-danger">{{ $inactiveBranches }}</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-danger" style="width: {{ $inactivePercentage }}%" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="d-block">
                                            <i class="fa fa-map-marker text-info"></i> Locations
                                        </span>
                                        <span class="text-success">+{{ $locationsGrowthPercentage }}%</span>
                                    </div>
                                    <h3 class="mb-3 text-info">{{ $uniqueLocations }}</h3>
                                    <div class="progress height-five mb-2">
                                        <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                    </div>
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
                                    <h4 class="fw-bold mb-0">All Branches</h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <button id="openBranchFilterBtn" class="filter-square-btn">
                                            <i class="fa-solid fa-filter"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Panel (right slide) -->
                                <div id="branchFilterPanel" class="filter-slide-panel">
                                    <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                        <h5 class="mb-0">Filter Branches</h5>
                                        <button id="closeBranchFilterBtn" class="btn btn-sm btn-light">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>

                                    <form class="p-3" method="GET" action="{{ route('branches.index') }}">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Branch Name</label>
                                            <input type="text" name="name" value="{{ request('name') }}" 
                                                   class="form-control" placeholder="Search by name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Location</label>
                                            <input type="text" name="address" value="{{ request('address') }}" 
                                                   class="form-control" placeholder="Search by location">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="">All Status</option>
                                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-primary">Apply Filters</button>
                                            <a href="{{ route('branches.index') }}" class="btn btn-light border">Reset</a>
                                        </div>
                                    </form>
                                </div>

                                <!-- Branches Table -->
                                <div class="table-responsive">
                                    <table class="table custom-table datatable mb-0">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Branch Name</th>
                                                <th>Location</th>
                                                <th>Contact</th>
                                                <th>Opening Hours</th>
                                                <th>Status</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($branches as $branch)
                                                <tr>
                                                    <td data-label="S.No">{{ $loop->iteration }}</td>

                                                    <td data-label="Branch Name">
                                                        <span class="od-chip-highlight">{{ $branch->name }}</span>
                                                    </td>
                                                    
                                                    <td data-label="Address">{{ Str::limit($branch->address, 30) }}</td>
                                                    
                                                    <td data-label="Contact">
                                                        <div>{{ $branch->phone }}</div>
                                                        @if(isset($branch->email))
                                                            <small class="text-muted">{{ $branch->email }}</small>
                                                        @endif
                                                    </td>
                                                    
                                                    <td data-label="Timings">
                                                        {{ \Carbon\Carbon::parse($branch->opening_time)->format('h:i A') }} -
                                                        {{ \Carbon\Carbon::parse($branch->closing_time)->format('h:i A') }}
                                                    </td>
                                                    
                                                    <td data-label="Status">
                                                        <div class="dropdown action-label">
                                                            <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" type="button"
                                                                id="statusDropdown{{ $branch->id }}" data-bs-toggle="dropdown">
                                                                <i class="fa fa-circle text-{{ $branch->status ? 'success' : 'danger' }}"></i>
                                                                {{ $branch->status ? 'Active' : 'Inactive' }}
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <button class="dropdown-item" onclick="updateStatus({{ $branch->id }}, 1)">
                                                                    <i class="fa fa-circle text-success"></i> Active
                                                                </button>
                                                                <button class="dropdown-item" onclick="updateStatus({{ $branch->id }}, 0)">
                                                                    <i class="fa fa-circle text-danger"></i> Inactive
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    <td data-label="Actions" class="text-end">
                                                        <div class="od-inline-actions">
                                                            <a href="{{ route('branches.show', $branch->id) }}" class="od-icon-btn" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @if(isset($permissions) && $permissions->can_edit)
                                                            <a href="{{ route('branches.edit', $branch->id) }}" class="od-icon-btn" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            @endif
                                                            @if(isset($permissions) && $permissions->can_delete)
                                                            <button class="od-icon-btn danger" title="Delete"
                                                                data-branch-id="{{ $branch->id }}"
                                                                data-branch-name="{{ $branch->name }}"
                                                                onclick="setDeleteBranch(this)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="empty-state">
                                                            <i class="fa fa-building fa-3x text-muted mb-3"></i>
                                                            <h5 class="text-muted">No Branches Found</h5>
                                                            <p class="text-muted">No branches found matching your criteria.</p>
                                                            <a href="{{ route('branches.create') }}" class="btn btn-primary">
                                                                <i class="fa fa-plus"></i> Create New Branch
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if($branches->hasPages())
                                <div class="mt-3">
                                    {{ $branches->appends(request()->query())->links() }}
                                </div>
                                @endif
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success/error messages
            @if(session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Filter Panel Functionality
            const openFilterBtn = document.getElementById('openBranchFilterBtn');
            const closeFilterBtn = document.getElementById('closeBranchFilterBtn');
            const filterPanel = document.getElementById('branchFilterPanel');

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

            // Auto-close alerts
            setTimeout(function() {
                $(".alert-dismissible").fadeTo(500, 0).slideUp(500, function(){ 
                    $(this).remove(); 
                });
            }, 3000);
        });

        function updateStatus(branchId, status) {
            const statusText = status ? 'Active' : 'Inactive';
            
            Swal.fire({
                title: 'Update Status',
                text: `Are you sure you want to change the status to "${statusText}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait while we update the status.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const url = "{{ route('branches.updateStatus', ['branch' => 'BRANCH_ID']) }}".replace('BRANCH_ID', branchId);
                    
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                const statusButton = $(`#statusDropdown${branchId}`);
                                const statusColor = response.status ? 'success' : 'danger';
                                statusButton.html(`<i class="fa fa-circle text-${statusColor}"></i> ${response.status_text}`);
                                
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
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr);
                            let errorMessage = 'Failed to update status';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 404) {
                                errorMessage = 'Branch not found';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Server error occurred';
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        function setDeleteBranch(element) {
            const branchId = element.getAttribute('data-branch-id');
            const branchName = element.getAttribute('data-branch-name');
            
            Swal.fire({
                title: 'Delete Branch',
                html: `Are you sure you want to delete the branch:<br><strong>${branchName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the branch.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('branches.destroy', ':id') }}".replace(':id', branchId);
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection