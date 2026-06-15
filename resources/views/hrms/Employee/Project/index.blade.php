@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Projects');
@endphp

<!-- ============================= -->
<!-- TOP BAR -->
<div class="d-flex justify-content-between align-items-center mb-3" style="margin-top:-10px;">

    <h4 class="fw-bold mb-0">Projects</h4>

    <div class="d-flex align-items-center gap-2">

        @if(isset($permissions) && $permissions->can_create)
        <a href="{{ route('projects.create') }}" class="btn btn-primary px-3">
            <i class="fa fa-plus me-1"></i> Create Project
        </a>
        @endif

        <button id="openProjectFilterBtn" class="filter-square-btn">
            <i class="fa-solid fa-filter"></i>
        </button>

    </div>
</div>


<!-- ============================= -->
<!-- FILTER PANEL (right slide) -->
<!-- ============================= -->



<div id="projectFilterPanel" class="filter-slide-panel">
    <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="mb-0">Filter Projects</h5>
        <button id="closeProjectFilterBtn" class="btn btn-sm btn-light">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <form class="p-3" method="GET">

        <div class="mb-3">
            <label class="form-label fw-bold">Project Name</label>
            <input type="text" name="projectname" value="{{ request('projectname') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach($statuses as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Priority</label>
            <select name="priority" class="form-select">
                <option value="">All</option>
                <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Leader</label>
            <select name="leader" class="form-select">
                <option value="">All</option>
                @foreach($leaders as $l)
                <option value="{{ $l->id }}" {{ request('leader')==$l->id?'selected':'' }}>
                    {{ $l->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Start Date</label>
            <input type="date" name="startdate" value="{{ request('startdate') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">End Date</label>
            <input type="date" name="enddate" value="{{ request('enddate') }}" class="form-control">
        </div>

        <div class="d-grid gap-2 mt-3">
            <button class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('projects.index') }}" class="btn btn-light border">Reset</a>
        </div>

    </form>
</div>

<!-- ============================= -->
<!-- PROJECTS TABLE -->
<!-- ============================= -->
<!-- ============================= -->
@if(isset($permissions) && $permissions->can_delete)
<button type="button" class="btn btn-danger mb-2" id="deleteSelectedBtn">
    <i class="fa-solid fa-trash me-1"></i> Delete Selected
</button>
@endif

<div class="table-responsive">
    <table class="table custom-table datatable dataTable no-footer" id="DataTables_Table_0">

        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllProjects"></th>
                <th>S.I.</th>
                <th>Project</th>
                <th>Project ID</th>
                <th>Leader</th>
                <th>Team</th>
                <th>Deadline</th>
                <th>Priority</th>
                <th>Status</th>
                <th>File</th>
                     <th class="text-end">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($projects as $project)
            <tr>
                <td>
                    <input type="checkbox" class="row-check" name="ids[]" value="{{ $project->id }}">
                </td>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $project->projectname }}</strong></td>
                <td>{{ $project->projectid }}</td>
                <td>{{ $project->leaderName }}</td>
                <td>{!! $project->teamNames !!}</td>
                <td>{{ \Carbon\Carbon::parse($project->enddate)->format('d-m-Y') }}</td>

                <td>
                    <span class="badge 
                        {{ $project->priority=='high' ? 'bg-danger' : ($project->priority=='medium'?'bg-warning':'bg-success') }}">
                        {{ ucfirst($project->priority) }}
                    </span>
                </td>

              
                <td data-label="Status">
                    <div class="od-status-toggle">
                        <div class="dropdown">
                            @php
                                $statusClass = match($project->status) {
                                    'Initiated' => 'btn-secondary',
                                    'Planned' => 'btn-info',
                                    'Active' => 'btn-success',
                                    'On Hold' => 'btn-warning',
                                    'Pending' => 'btn-primary',
                                    'Review' => 'btn-dark',
                                    'Completed' => 'btn-success',
                                    'Closed' => 'btn-secondary',
                                    'Cancelled' => 'btn-danger',
                                    default => 'btn-secondary',
                                };
                            @endphp
                            <button class="btn {{ $statusClass }} dropdown-toggle"
                                    type="button"
                                    id="statusDropdown{{ $project->id }}"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    style="width: 120px;">
                                {{ $project->status }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $project->id }}">
                                @foreach($statuses as $key => $status)
                                    @if($status != $project->status)
                                        <li>
                                            <a class="dropdown-item"
                                               href="#"
                                               onclick="statusChange('{{ $project->id }}', '{{ $status }}')">
                                                {{ $status }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </td>

                <td>
                    @if($project->projectfile)
                    <a href="{{ route('projects.download', $project->id) }}" class="od-icon-btn">
                        <i class="fa fa-download"></i>
                    </a>
                    @else
                    <span class="text-muted">No file</span>
                    @endif
                </td>

                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        <a href="{{ route('projects.show', $project->id) }}"
                           class="od-icon-btn"
                           title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        @if(isset($permissions) && $permissions->can_edit)
                        <a href="{{ route('projects.edit', $project->id) }}"
                           class="od-icon-btn"
                           title="Edit">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        @endif

                        @if(isset($permissions) && $permissions->can_delete)
                        <button type="button"
                                class="od-icon-btn danger delete-btn"
                                onclick="confirmDelete({{ $project->id }});"
                                title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <form id="delete-form-{{ $project->id }}"
                              action="{{ route('projects.destroy', $project->id) }}"
                              method="POST"
                              style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">No Projects Found</td>
            </tr>
            @endforelse
        </tbody>

    </table>
</div>

<style>
    /* Right slide filter */
    .filter-slide-panel {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: #fff;
        z-index: 9999;
        transition: all 0.3s ease;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1);
    }

    .filter-slide-panel.active {
        right: 0;
    }
    
    /* Square Filter Icon (Zoho Style) */
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
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        cursor: pointer;
    }

    .filter-square-btn:hover {
        background: #f5f5f5;
        border-color: #c9c9c9;
    }

    .filter-square-btn i {
        font-size: 16px;
    }

    /* Selected row style */
    .od-selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    @media (max-width: 576px) {
        .filter-slide-panel {
            width: 100%;
        }

        .btn {
            font-size: 14px;
        }

        table {
            font-size: 12px;
        }
    }
</style>

<script>
    // Open/Close filter panel
    document.getElementById("openProjectFilterBtn").onclick = () => {
        document.getElementById("projectFilterPanel").classList.add("active");
    };
    document.getElementById("closeProjectFilterBtn").onclick = () => {
        document.getElementById("projectFilterPanel").classList.remove("active");
    };

    // Hide success/error messages after 2 seconds
    setTimeout(() => {
        const successMsg = document.getElementById('success-message');
        const errorMsg = document.getElementById('error-message');
        if (successMsg) successMsg.remove();
        if (errorMsg) errorMsg.remove();
    }, 2000);

    // Status change with SweetAlert
    function statusChange(id, newStatus) {
        Swal.fire({
            title: `Change status to ${newStatus}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "PUT",
                    url: "{{ route('projects.updateStatus') }}",
                    data: {
                        id: id,
                        newstatus: newStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {
                        Swal.fire("Success", "Status updated successfully!", "success").then(() => location.reload());
                    },
                    error: function () {
                        Swal.fire("Failed", "Failed to update status", "error");
                    }
                });
            }
        });
    }

    // Delete confirmation for single project
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete the project!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Initialize checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAllProjects');
        const rowCheckboxes = document.querySelectorAll('.row-check');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

        // Function to update delete button visibility
        function updateDeleteButtonVisibility() {
            if (!deleteSelectedBtn) return;
            
            const selectedCount = document.querySelectorAll('.row-check:checked').length;
            // Always show the button (you can change this if you want it hidden)
            // deleteSelectedBtn.style.display = selectedCount > 0 ? 'inline-block' : 'none';
        }

        // "Check All" functionality
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    const row = checkbox.closest('tr');
                    if (row) {
                        row.classList.toggle('od-selected', this.checked);
                    }
                });
                updateDeleteButtonVisibility();
            });
        }

        // Individual row checkboxes
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                if (row) {
                    row.classList.toggle('od-selected', this.checked);
                }
                
                // Update "Check All" checkbox state
                if (checkAll) {
                    const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                    checkAll.checked = allChecked;
                    checkAll.indeterminate = someChecked && !allChecked;
                }
                
                updateDeleteButtonVisibility();
            });
        });

        // Multi-delete functionality
        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const selected = Array.from(document.querySelectorAll('.row-check:checked'))
                    .map(cb => cb.value)
                    .filter(value => value); // Filter out any null/undefined values

                console.log('Selected IDs:', selected); // Debug log

                if (selected.length === 0) {
                    Swal.fire({
                        title: "No selection",
                        text: "Please select at least one project.",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                Swal.fire({
                    title: "Are you sure?",
                    html: `You are about to delete <strong>${selected.length}</strong> selected project(s).<br>This action cannot be undone!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete them!",
                    cancelButtonText: "Cancel",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the selected projects.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        $.ajax({
                            url: "{{ route('projects.multiDelete') }}",
                            type: "POST",
                            data: {
                                ids: selected,
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message || "Selected projects have been deleted successfully.",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                let errorMsg = "An error occurred while deleting the projects.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.status === 500) {
                                    errorMsg = "Server error. Please try again later.";
                                } else if (xhr.status === 404) {
                                    errorMsg = "The requested resource was not found.";
                                }
                                
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMsg,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });
        }

        // Initial button visibility
        updateDeleteButtonVisibility();
    });
</script>