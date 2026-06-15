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
            
                <div class="col-auto float-end ms-auto">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('employee.create') }}" class="btn add-btn">
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
<!-- Employee Filters -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('employee.index') }}" method="GET" class="row g-3 align-items-end">
            
            <!-- Employee ID -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-semibold text-muted small mb-1">Employee ID</label>
                <input type="text" name="employee_id" class="form-control form-control-sm"
                    placeholder="Enter Employee ID" value="{{ request('employee_id') }}">
            </div>

            <!-- Employee Name -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-semibold text-muted small mb-1">Employee Name</label>
                <input type="text" name="employee_name" class="form-control form-control-sm"
                    placeholder="Enter Employee Name" value="{{ request('employee_name') }}">
            </div>

            <!-- Designation -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-semibold text-muted small mb-1">Designation</label>
                <select name="designation" class="form-select form-select-sm">
                    <option value="">Select Designation</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ request('designation') == $designation->id ? 'selected' : '' }}>
                            {{ $designation->designation }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-semibold text-muted small mb-1">Department</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->department }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Notice Period -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-semibold text-muted small mb-1">Notice Period</label>
                <select name="notice_period" class="form-select form-select-sm">
                    <option value="">Select Option</option>
                    <option value="1" {{ request('notice_period') == '1' ? 'selected' : '' }}>In Notice Period</option>
                </select>
            </div>

            <!-- Search & Reset Buttons -->
            <div class="col-12 col-sm-6 col-md-3 text-end mt-2">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}
.card-body {
    padding: 1.25rem 1.5rem;
    background-color: #fafbfc;
}
.form-label {
    font-size: 0.85rem;
    color: #555;
}
.form-control-sm, .form-select-sm {
    font-size: 0.9rem;
    border-radius: 6px;
}
.btn-sm {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
}
</style>


<!-- Bulk Import Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
    <form action="{{ route('employee.importEmployees') }}" method="POST" enctype="multipart/form-data" id="excelImportForm" class="d-flex align-items-center gap-2 flex-wrap">
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
        
        <!-- File input with strict validation -->
        <div class="form-group mb-0">
            <input type="file" name="file" id="excelFileInput" 
                   class="form-control form-control-sm" 
                   accept=".xlsx,.xls,.csv" 
                   required
                   style="width: 220px;">
            <div class="form-text text-danger small mt-1">
                <i class="fas fa-exclamation-circle me-1"></i>
                Only .xlsx, .xls, .csv files allowed
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-sm" id="importSubmitBtn">
            <i class="fas fa-upload me-1"></i> Import
        </button>
        
        <a href="{{ route('employee.downloadTemplate') }}" class="btn btn-success btn-sm">
            <i class="fas fa-download me-1"></i> Template
        </a>
    </form>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Employee List</h4>
        <button id="bulkDeleteBtn" class="btn btn-danger btn-sm" disabled>
            <i class="fa-solid fa-trash"></i> Delete Selected
        </button>
    </div>

    <div class="table-responsive">
        <table id="employee-table" class="table custom-table datatable">
            <thead>
                <tr>
                    <!-- ✅ Checkbox for bulk delete -->
                    <th style="width: 40px;">
                        <input type="checkbox" id="checkAll" class="form-check-input" title="Select all employees">
                    </th>
                    <th>Name</th>
                    <th>Employee ID</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th class="text-nowrap">Join Date</th>
                    <th>Role</th>
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

                        <td data-label="Employee">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('employee.show', $employee->id) }}">
                                    <img src="{{ asset($employee->profile_image) }}" alt="Profile Image" style="width: 50px; height: 50px; object-fit: cover; border-radius:50%">
                                </a>
                                <div class="ms-2">
                                    <strong>{{ ucfirst(strtolower($employee->firstname)) }} {{ $employee->lastname }}</strong>
                                    <div class="text-muted" id="designation-profile-{{ $employee->id }}">{{ $employee->designation_name }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td>{{ $employee->employeeid }}</td>
                        <td><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ \Carbon\Carbon::parse($employee->joiningdate)->format('d-m-y') }}</td>
                        <td><span class="od-chip-highlight">{{ $employee->designation_name }}</span></td>
                        
                        <td class="text-end">
                            <div class="od-inline-actions">
                                @if($permissions->can_edit)
                                <a href="{{ route('employee.edit', $employee->id) }}" class="od-icon-btn" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                @endif

                                <a href="{{ route('employee.sendMemo', $employee->id) }}" class="od-icon-btn" title="Send Memo">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>

                                <a href="{{ route('employee.downloadDocuments', $employee->id) }}" class="od-icon-btn" title="Documents">
                                    <i class="fa-solid fa-file-zipper"></i>
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
                        <td colspan="8" class="text-center">No employees found</td>
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
<style>
    /* Make table container scrollable */
    .table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        cursor: grab;
    }

    .table-responsive:active {
        cursor: grabbing;
    }
</style>

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
            
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importing...';
            
            // Submit the form
            setTimeout(() => {
                this.submit();
            }, 100);
        });
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
});
</script>
@endsection












