@extends('layouts.index')
@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Bulk Employee Import</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">Employee List</a></li>
                        <li class="breadcrumb-item active">Bulk Import</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Import Instructions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Import Instructions</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fa fa-info-circle"></i> How to Import Employees:</h6>
                            <ol>
                                <li><strong>Download Template:</strong> Click the "Download Template" button to get the Excel template with sample data.</li>
                                <li><strong>Fill Data:</strong> Open the template and fill in employee information in the "Employee_Template" sheet.</li>
                                <li><strong>Reference Data:</strong> Use the reference sheets (Departments, Designations, etc.) to find correct IDs.</li>
                                <li><strong>Upload File:</strong> Save your file and upload it using the form below.</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fa fa-exclamation-triangle"></i> Important Notes:</h6>
                            <ul class="mb-0">
                                <li>All fields marked with * are required</li>
                                <li>Email and Username must be unique</li>
                                <li>Date format must be YYYY-MM-DD (e.g., 2024-01-15)</li>
                                <li>Master data (Departments, Designations, etc.) cannot be created via import</li>
                                <li>Profile images and documents will need to be uploaded individually</li>
                                <li>Random passwords will be generated and emailed to employees</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Template Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Step 1: Download Template</h5>
                    </div>
                    <div class="card-body text-center">
                        <p>Download the Excel template with sample data and reference sheets.</p>
                        <a href="{{ route('downloadTemplate') }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-download"></i> Download Excel Template
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Step 2: Upload Filled Template</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="excel_file">Select Excel File <span class="text-danger">*</span></label>
                                        <input type="file" 
                                               class="form-control @error('excel_file') is-invalid @enderror" 
                                               id="excel_file" 
                                               name="excel_file" 
                                               accept=".xlsx,.xls"
                                               required>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Supported formats: .xlsx, .xls (Max size: 10MB)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success btn-lg w-100" id="importBtn">
                                        <i class="fa fa-upload"></i> Import Employees
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-secondary">
                                        <h6><i class="fa fa-shield-alt"></i> What happens during import:</h6>
                                        <ul class="mb-0">
                                            <li>System validates all data before importing</li>
                                            <li>Duplicate emails/usernames will be rejected</li>
                                            <li>Employee IDs will be auto-generated if not provided</li>
                                            <li>Random passwords will be created and emailed</li>
                                            <li>Default profile images will be assigned</li>
                                            <li>Hierarchy-based module permissions will be applied</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Importing Employees...</h5>
                <p class="mb-0">Please wait while we process your file. This may take a few minutes.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('excel_file');
    const importBtn = document.getElementById('importBtn');
    
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('Please select an Excel file to import.');
        return;
    }
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Disable the import button
    importBtn.disabled = true;
    importBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Importing...';
});

// File input validation
document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        
        if (fileSize > 10) {
            alert('File size must be less than 10MB');
            e.target.value = '';
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid Excel file (.xlsx or .xls)');
            e.target.value = '';
            return;
        }
    }
});
</script>
@endsection
