@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('All Employees');
@endphp
@php
    $userRole = Session::get('role');
    $userId = Session::get('user_id');
    $adminId = Session::get('admin_id');
    $modules = [];
    
 
    if ($userRole === 'employee' && $userId) {
        $modules = DB::table('employee_module_access')
            ->where('employee_id', $userId)
            ->pluck('module_name')
            ->toArray();
       
    } elseif ($userRole === 'admin' && $adminId) {
        $modules = DB::table('admin_module_access')
            ->where('admin_id', $adminId)
            ->pluck('module_name')
            ->toArray();
       
    }
    

@endphp

@extends('layouts.index')
@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employee</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employee List</li>
                    </ul>
                </div>
            
                <div class="col-auto float-end ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkImportModal" style="border-radius: 50px;">
                        <i class="fas fa-upload me-1"></i> Bulk Upload
                    </button>
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('employee.create') }}" class="btn add-btn m-0">
                         Add Employee
                    </a>
                    @endif
                    {{-- <form action="{{ route('allemployees.truncate') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all employees? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Truncate All Employees
                        </button>
                    </form>  --}}
                    {{-- <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="sql_file">Upload SQL File:</label>
                        <input type="file" name="sql_file" accept=".sql" required>
                        <button type="submit">Import</button>
                    </form>    --}}
                    <div class="view-icons">
                        <a href="{{ route('employee.grid') }}" class="grid-view btn btn-link active"><i class="fa fa-th"></i></a>
                        <a href="{{ route('employee.index') }}" class="list-view btn btn-link"><i class="fa-solid fa-bars"></i></a>
                        <a href="{{ route('employee.trash') }}" class="btn btn-link" title="View Trash">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
<style>
/* Profile Image Styles */
.employee-profile-img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    transition: all 0.2s ease;
}

.profile-img-link:hover .employee-profile-img,
.profile-img-link:hover .employee-profile-initial {
    border-color: #3b82f6;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.employee-profile-initial {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #f6893bff 0%, #eb9925ff 100%);
    color: white;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

/* Single Line Filter Bar Styles matching the image */
.single-line-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
    padding: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    flex-wrap: wrap;
    margin-bottom: 24px;
}

.single-line-filter-bar::-webkit-scrollbar {
    height: 4px;
}

.single-line-filter-bar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.sl-filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Orange highlighted search input matching the image */
.sl-search-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #fb923c; /* Orange border */
    border-radius: 50px;
    padding: 6px 16px;
    background: #fffaf5; /* Very light orange background */
    min-width: 200px;
}

.sl-search-wrapper i {
    color: #94a3b8;
    margin-right: 8px;
    font-size: 14px;
}

.sl-search-input {
    border: none;
    background: transparent;
    outline: none;
    font-size: 13px;
    color: #334155;
    width: 100%;
}

.sl-search-input::placeholder {
    color: #94a3b8;
}

/* Vertical Divider */
.sl-divider {
    width: 1px;
    height: 24px;
    background-color: #e2e8f0;
    margin: 0 4px;
}

/* Normal pill dropdowns */
.sl-select-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
    padding: 6px 14px;
    background: #ffffff;
}

.sl-select-wrapper i.icon-left {
    color: #94a3b8;
    margin-right: 6px;
    font-size: 13px;
}

.sl-select-wrapper i.icon-right {
    color: #94a3b8;
    margin-left: 6px;
    font-size: 10px;
}

/* Custom Bootstrap Dropdown styling for Single Line Filter Bar */
.sl-dropdown-btn {
    display: flex;
    align-items: center;
    border: none;
    background: transparent;
    outline: none;
    font-size: 13px;
    color: #475569;
    padding: 0;
    margin-right: 4px;
}

.sl-dropdown-btn::after {
    display: none; /* Hide default bootstrap caret */
}

.sl-dropdown-menu {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    padding: 8px 0;
    min-width: 200px;
    margin-top: 8px !important;
    z-index: 1060;
}

.sl-dropdown-menu .dropdown-item {
    font-size: 13px;
    padding: 8px 16px;
    color: #334155;
    transition: all 0.2s ease;
}

.sl-dropdown-menu .dropdown-item:hover,
.sl-dropdown-menu .dropdown-item:focus {
    background-color: #f1f5f9;
    color: #0f172a;
}

.sl-dropdown-menu .dropdown-item.active {
    background-color: #e0f2fe;
    color: #0284c7;
    font-weight: 500;
}

/* Action button matching the right-side button in image */
.sl-action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    border: none;
    border-radius: 50px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: background 0.2s;
    margin-left: auto;
}

.sl-action-btn:hover {
    background: #e2e8f0;
}

.sl-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #fee2e2;
    color: #ef4444;
    text-decoration: none;
    margin-right: 4px;
}

.sl-clear-btn:hover {
    background: #fecaca;
    color: #dc2626;
}

.filter-results-count i {
    font-size: 12px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .single-line-filter-bar {
        border-radius: 12px;
        padding: 12px;
    }
    
    .single-line-filter-bar form {
        flex-direction: column;
        align-items: stretch !important;
        gap: 12px;
    }

    .sl-search-wrapper, .sl-select-wrapper {
        width: 100%;
        min-width: unset;
        margin-left: 0 !important;
    }
    
    .sl-divider {
        display: none;
    }
    
    .sl-action-btn {
        width: 100%;
        justify-content: center;
        margin-top: 8px;
    }
}
</style>
<!-- Single Line Filter Bar -->
<div class="single-line-filter-bar">
    <form action="{{ route('employee.index') }}" method="GET" id="employeeFilterForm" class="d-flex align-items-center w-100 m-0 flex-wrap" style="gap: 8px;">
        
        <!-- Primary Search (Orange Pill) -->
        <div class="sl-search-wrapper">
            <i class="fa-solid fa-search"></i>
            <input type="text" name="employee_name" placeholder="Search name..." value="{{ request('employee_name') }}" class="sl-search-input">
        </div>

        <div class="sl-divider"></div>

        <div class="sl-select-wrapper">
            <i class="fa-solid fa-id-badge icon-left"></i>
            <input type="text" name="employee_id" placeholder="ID..." value="{{ request('employee_id') }}" class="sl-search-input" style="width: 80px;">
        </div>

        <!-- Dropdowns (Custom Bootstrap) -->
        <div class="sl-select-wrapper ms-2 dropdown">
            <i class="fa-solid fa-briefcase icon-left"></i>
            <input type="hidden" name="designation" id="filter_designation" value="{{ request('designation') }}">
            <button class="sl-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ request('designation') ? ($designations->firstWhere('id', request('designation'))->designation ?? 'Designation') : 'Designation' }}
            </button>
            <i class="fa-solid fa-chevron-down icon-right dropdown-toggle-icon" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
            
            <ul class="dropdown-menu sl-dropdown-menu">
                <li><a class="dropdown-item {{ !request('designation') ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_designation').value=''; document.getElementById('employeeFilterForm').submit();">All Designations</a></li>
                @foreach($designations as $designation)
                    <li><a class="dropdown-item {{ request('designation') == $designation->id ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_designation').value='{{ $designation->id }}'; document.getElementById('employeeFilterForm').submit();">{{ $designation->designation }}</a></li>
                @endforeach
            </ul>
        </div>
        
        <div class="sl-select-wrapper ms-2 dropdown">
            <i class="fa-solid fa-building icon-left"></i>
            <input type="hidden" name="department" id="filter_department" value="{{ request('department') }}">
            <button class="sl-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ request('department') ? ($departments->firstWhere('id', request('department'))->department ?? 'Department') : 'Department' }}
            </button>
            <i class="fa-solid fa-chevron-down icon-right dropdown-toggle-icon" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
            
            <ul class="dropdown-menu sl-dropdown-menu">
                <li><a class="dropdown-item {{ !request('department') ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_department').value=''; document.getElementById('employeeFilterForm').submit();">All Departments</a></li>
                @foreach($departments as $department)
                    <li><a class="dropdown-item {{ request('department') == $department->id ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_department').value='{{ $department->id }}'; document.getElementById('employeeFilterForm').submit();">{{ $department->department }}</a></li>
                @endforeach
            </ul>
        </div>
        
        <div class="sl-select-wrapper ms-2 dropdown">
            <i class="fa-solid fa-clock icon-left"></i>
            <input type="hidden" name="notice_period" id="filter_notice_period" value="{{ request('notice_period') }}">
            <button class="sl-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ request('notice_period') == '1' ? 'In Notice Period' : 'Notice Period' }}
            </button>
            <i class="fa-solid fa-chevron-down icon-right dropdown-toggle-icon" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
            
            <ul class="dropdown-menu sl-dropdown-menu">
                <li><a class="dropdown-item {{ !request('notice_period') ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_notice_period').value=''; document.getElementById('employeeFilterForm').submit();">All Notice Periods</a></li>
                <li><a class="dropdown-item {{ request('notice_period') == '1' ? 'active' : '' }}" href="#" onclick="event.preventDefault(); document.getElementById('filter_notice_period').value='1'; document.getElementById('employeeFilterForm').submit();">In Notice Period</a></li>
            </ul>
        </div>

        <div class="sl-divider ms-auto"></div>

        <!-- Action Area -->
        @php
            $appliedFilters = 0;
            if(request('employee_id')) $appliedFilters++;
            if(request('employee_name')) $appliedFilters++;
            if(request('designation')) $appliedFilters++;
            if(request('department')) $appliedFilters++;
            if(request('notice_period')) $appliedFilters++;
        @endphp

        @if($appliedFilters > 0)
        <a href="{{ route('employee.index') }}" class="sl-clear-btn" title="Clear filters">
            <i class="fa-solid fa-xmark"></i>
        </a>
        @endif

        <button type="submit" class="sl-action-btn">
            <i class="fa-solid fa-sliders"></i> Filters
        </button>
    </form>
</div>






<!-- Bulk Import Modal -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkImportModalLabel">Bulk Import Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('employee.importEmployees') }}" method="POST" enctype="multipart/form-data" id="excelImportForm">
                    @csrf
                    
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show w-100 mb-2" role="alert" id="importErrorAlert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Import Failed:</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show w-100 mb-2" role="alert" id="importSuccessAlert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="excelFileInput" class="form-label">Upload File</label>
                        <input type="file" name="file" id="excelFileInput" 
                               class="form-control" 
                               accept=".xlsx,.xls,.csv" 
                               required>
                        <div class="form-text text-danger small mt-1">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Only .xlsx, .xls, .csv files allowed
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('employee.downloadTemplate') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="importSubmitBtn">
                            <i class="fas fa-upload me-1"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('.modern-file-input');
    const fileName = document.querySelector('.file-name');
    const uploadArea = document.querySelector('.file-upload-area');
    
    fileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            fileName.textContent = `Selected file: ${this.files[0].name}`;
        } else {
            fileName.textContent = '';
        }
    });
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileName.textContent = `Selected file: ${e.dataTransfer.files[0].name}`;
        }
    });
});
</script>

<div class="employee-table-container">
    <button id="bulkDeleteBtn" class="btn btn-danger btn-sm ms-3 d-none" disabled>
        <i class="fa-solid fa-trash"></i> Delete Selected
    </button>

    <div class="">
        <table id="employee-table" class="table custom-table datatable">
            <thead>
                <tr>
                    <!-- ✅ Checkbox for bulk delete -->
                    <th style="width: 40px;">
                        <input type="checkbox" id="checkAll" class="form-check-input" title="Select all employees">
                    </th>
                    <th style="width: 80px;">Profile</th>
                    <th>Name</th>
                    <th>Employee ID</th>
                    <th>Contact Info</th>
                    <th class="text-nowrap">Join Date</th>
                    <th>Department & Role</th>
                    <th class="text-end no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <!-- ✅ Individual checkbox -->
                        <td>
                            <input type="checkbox" class="employee-checkbox form-check-input" value="{{ $employee->id }}">
                        </td>

                        <!-- Profile Image Column -->
                        <td class="text-center">
                            <a href="{{ route('employee.show', $employee->id) }}" class="profile-img-link">
                                @if(!empty($employee->profile_image) && file_exists(public_path($employee->profile_image)))
                                    <img src="{{ asset($employee->profile_image) }}" alt="Profile Image" class="employee-profile-img">
                                @else
                                    <div class="employee-profile-initial">
                                        {{ strtoupper(substr($employee->firstname, 0, 1)) }}
                                    </div>
                                @endif
                            </a>
                        </td>

                        <td data-label="Employee">
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong>{{ ucfirst(strtolower($employee->firstname)) }} {{ $employee->lastname }}</strong>
                                    <div class="text-muted" id="designation-profile-{{ $employee->id }}">{{ $employee->designation_name }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td>{{ $employee->employeeid }}</td>
                        <td>
                            <div><i class="fa fa-envelope text-muted me-1"></i> <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></div>
                            <div><i class="fa fa-phone text-muted me-1"></i> {{ $employee->phone }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($employee->joiningdate)->format('d-m-y') }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1 align-items-start">
                                <span class="badge bg-light text-secondary border border-secondary">{{ $employee->department_name }}</span>
                                <span class="od-chip-highlight mt-1">{{ $employee->designation_name }}</span>
                            </div>
                        </td>
                        
                        <td class="text-end">
                            <div class="od-inline-actions">
                                <!-- View/Show Button -->
                                <a href="{{ route('employee.show', $employee->id) }}" class="od-icon-btn" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if($permissions->can_edit)
                                <a href="{{ route('employee.edit', $employee->id) }}" class="od-icon-btn" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                @endif

                                <a href="{{ route('employee.sendMemo', $employee->id) }}" class="od-icon-btn" title="Send Memo">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>


                               @if(($userRole === 'admin' || $userRole === 'employee') && ($userRole === 'admin' || in_array('Promotion', $modules)))
    <a href="{{ route('promotion.create', ['employee_id' => $employee->employeeid]) }}" class="od-icon-btn" title="Promote">
        <i class="fa-solid fa-arrow-up"></i>
    </a>
@endif

@if(($userRole === 'admin' || $userRole === 'employee') && ($userRole === 'admin' || in_array('Termination', $modules)))
    <a href="{{ route('terminations.create', ['employee_id' => $employee->employeeid]) }}" class="od-icon-btn" title="Terminate">
        <i class="fa-solid fa-user-slash"></i>
    </a>
@endif

                                @if($permissions->can_delete)
                                <button type="button" class="od-icon-btn danger" onclick="confirmDelete({{ $employee->id }});" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $employee->id }}" action="{{ route('employee.destroy', $employee->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No employees found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>



    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="confirmationModalLabel" style="font-weight: bold;">Move to Trash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to move this employee to trash?
            </div>
            <div class="modal-footer d-flex justify-content-around border-0">
                <button type="button" class="btn btn-outline-warning btn-lg" id="cancelDeleteBtn" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteBtn" style="border-radius: 50px; width: 150px;">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal (if you have one) -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="notificationMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableContainer = document.querySelector(".table-responsive");
        let isDragging = false;
        let startX;
        let scrollLeft;

        // Mouse down (only when Ctrl is pressed)
        tableContainer.addEventListener("mousedown", (e) => {
            if (!e.ctrlKey) return; // Only activate when Ctrl is pressed
            isDragging = true;
            tableContainer.classList.add("active");
            startX = e.pageX - tableContainer.offsetLeft;
            scrollLeft = tableContainer.scrollLeft;
            tableContainer.style.cursor = "grabbing";
        });

        // Mouse leave
        tableContainer.addEventListener("mouseleave", () => {
            isDragging = false;
            tableContainer.style.cursor = "grab";
        });

        // Mouse up
        tableContainer.addEventListener("mouseup", () => {
            isDragging = false;
            tableContainer.style.cursor = "grab";
        });

        // Mouse move (drag)
        tableContainer.addEventListener("mousemove", (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - tableContainer.offsetLeft;
            const walk = (x - startX) * 1.5; // Scroll speed multiplier
            tableContainer.scrollLeft = scrollLeft - walk;
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentEmployeeId = null;
    const confirmationModalEl = document.getElementById('confirmationModal');
    const confirmationModal = new bootstrap.Modal(confirmationModalEl);

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    // Function to show delete confirmation modal
    window.confirmDelete = function(employeeId) {
        currentEmployeeId = employeeId;
        confirmationModal.show();
    };

    // Confirm delete button click
    confirmBtn.addEventListener('click', function() {
        if (!currentEmployeeId) return;

        const deleteForm = document.getElementById('delete-form-' + currentEmployeeId);
        if (deleteForm) {
            // Submit the form
            deleteForm.submit();
        }
    });

    // Cancel button click
    cancelBtn.addEventListener('click', function() {
        currentEmployeeId = null;
        confirmationModal.hide();
    });

    // Optional: Reset currentEmployeeId when modal closes
    confirmationModalEl.addEventListener('hidden.bs.modal', function () {
        currentEmployeeId = null;
    });

    // Checkbox row selection (for your table selection)
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

    // Function to show notifications
    window.showNotification = function(title, message) {
        const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
        document.getElementById('notificationModalLabel').textContent = title;
        document.getElementById('notificationMessage').textContent = message;
        notificationModal.show();
    };

    // Example: changeDesignation function (if needed)
    window.changeDesignation = function(employeeId, designationId) {
        $.ajax({
            url: '{{ route("employee.updateDesignation") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: employeeId,
                designation_id: designationId
            },
            success: function(response) {
                if(response.success) {
                    $('#designation-display-' + employeeId).text(response.new_designation);
                    $('#designation-profile-' + employeeId).text(response.new_designation);
                    showNotification('Updated!', 'Designation updated successfully!');
                } else {
                    showNotification('Error!', 'Error updating designation!');
                }
            },
            error: function() {
                showNotification('Error!', 'Something went wrong!');
            }
        });
    };
});
</script>


<!-- JS for checkbox row selection -->
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

document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // Select All Checkbox Logic
    checkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkDeleteButton();
    });

    // Enable/Disable Delete Button
    checkboxes.forEach(cb => cb.addEventListener('change', toggleBulkDeleteButton));

    function toggleBulkDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteBtn.disabled = !anyChecked;
    }

    // Bulk Delete Logic
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.length} employees.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('employee.bulkDelete') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ employee_ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Deleted!", data.message, "success").then(() => location.reload());
                    } else {
                        Swal.fire("Error", data.message || "Something went wrong", "error");
                    }
                })
                .catch(() => Swal.fire("Error", "Server error occurred", "error"));
            }
        });
    });
});


</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('excelImportForm');
    const fileInput = document.getElementById('excelFileInput');
    const submitBtn = document.getElementById('importSubmitBtn');
    
    if (importForm && fileInput) {
        // Real-time validation when file is selected
        fileInput.addEventListener('change', function() {
            if (this.files.length === 0) return;
            
            const file = this.files[0];
            const fileName = file.name;
            const fileExtension = fileName.split('.').pop().toLowerCase();
            
            // Clear any previous alerts
            const existingAlert = document.getElementById('importErrorAlert');
            if (existingAlert) existingAlert.remove();
            
            // Check for disallowed files
            const disallowedExtensions = [
                'pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
                'zip', 'rar', 'doc', 'docx', 'txt', 'ppt', 'pptx'
            ];
            
            if (disallowedExtensions.includes(fileExtension)) {
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show w-100 mb-2';
                errorDiv.id = 'clientErrorAlert';
                errorDiv.innerHTML = `
                    <i class="fas fa-ban me-2"></i>
                    <strong>File Rejected:</strong> .${fileExtension} files are not allowed!<br>
                    <small>Please upload only Excel files (.xlsx, .xls, .csv)</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert before the form
                importForm.insertBefore(errorDiv, importForm.firstChild);
                
                // Clear the file input
                this.value = '';
                
                // Focus on the file input
                this.focus();
                
                return false;
            }
            
            // Check for allowed extensions
            const allowedExtensions = ['xlsx', 'xls', 'csv'];
            if (!allowedExtensions.includes(fileExtension)) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-warning alert-dismissible fade show w-100 mb-2';
                errorDiv.id = 'clientErrorAlert';
                errorDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Unknown File Type:</strong> .${fileExtension}<br>
                    <small>Please use only .xlsx, .xls, or .csv files</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                importForm.insertBefore(errorDiv, importForm.firstChild);
                this.value = '';
                return false;
            }
            
            // Check file size (50MB limit)
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            if (file.size > maxSize) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show w-100 mb-2';
                errorDiv.id = 'clientErrorAlert';
                errorDiv.innerHTML = `
                    <i class="fas fa-weight-hanging me-2"></i>
                    <strong>File Too Large:</strong> ${(file.size / (1024*1024)).toFixed(2)} MB<br>
                    <small>Maximum file size is 50MB</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                importForm.insertBefore(errorDiv, importForm.firstChild);
                this.value = '';
                return false;
            }
        });
        
        // Form submission validation
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (fileInput.files.length === 0) {
                alert('Please select an Excel file to import.');
                fileInput.focus();
                return false;
            }
            
            const file = fileInput.files[0];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const allowedExtensions = ['xlsx', 'xls', 'csv'];
            
            // Final check before submission
            if (!allowedExtensions.includes(fileExtension)) {
                alert(`Cannot upload .${fileExtension} files.\n\nPlease select an Excel file (.xlsx, .xls, .csv).`);
                fileInput.value = '';
                fileInput.focus();
                return false;
            }
            
            submitAjax(false);
        });

        function submitAjax(forceImport) {
            const formData = new FormData(importForm);
            if (forceImport) {
                formData.append('force_import', '1');
            }

            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ' + (forceImport ? 'Importing...' : 'Validating...');

            $.ajax({
                url: importForm.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status === 'confirm') {
                        // Show SweetAlert confirmation
                        let htmlContent = '<div style="max-height: 200px; overflow-y: auto; text-align: left; background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24; margin-bottom: 10px;">';
                        htmlContent += '<ul style="margin:0; padding-left: 20px;">';
                        response.errors.forEach(err => {
                            htmlContent += `<li>${err}</li>`;
                        });
                        htmlContent += '</ul></div>';
                        htmlContent += `<p><strong>${response.valid_count}</strong> rows are valid and ready to be imported.</p>`;

                        Swal.fire({
                            title: "Validation Errors Found",
                            html: htmlContent,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Proceed with Valid Rows",
                            cancelButtonText: "Cancel & Re-upload"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                submitAjax(true);
                            } else {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                        });
                    } else if (response.status === 'success') {
                        Swal.fire({
                            title: "Import Successful!",
                            text: response.message,
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    let msg = "An error occurred during import.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire("Import Failed", msg, "error");
                }
            });
        }
    }
    
    // Auto-focus on file input if there's an error
    @if(session('error'))
    setTimeout(function() {
        const fileInput = document.getElementById('excelFileInput');
        if (fileInput) {
            fileInput.focus();
        }
    }, 300);
    @endif
    // Move Bulk Delete Button to DataTables length menu
    function appendBulkDeleteBtn() {
        const lengthContainer = $('.dataTables_length');
        if (lengthContainer.length > 0) {
            lengthContainer.addClass('d-flex align-items-center');
            $('#bulkDeleteBtn').removeClass('d-none').appendTo(lengthContainer);
            return true;
        }
        return false;
    }

    if (!appendBulkDeleteBtn()) {
        $(document).on('init.dt', function() {
            appendBulkDeleteBtn();
        });
        setTimeout(appendBulkDeleteBtn, 500);
    }
});
</script>
@endsection