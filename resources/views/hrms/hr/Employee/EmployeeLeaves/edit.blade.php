@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Leave</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('employee-leaves.index') }}" class="breadcrumb-link">Leave List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Leave</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Leave</h4>
                    <div class="card-body">
                        <form id="leaveForm" method="POST" action="{{ route('employee-leaves.update', $leave->id) }}" class="needs-validation" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Employee ID Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="employee_id" id="employee_id" value="{{ old('employee_id', $employeeid) }}" placeholder="Enter Employee ID" required>
                                    <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
                                </div>

                                <!-- Leave Type (Dropdown) -->
                                <div class="col-md-6 mb-3">
                                    <label for="leave_type">Leave Type <span class="text-danger">*</span></label>
                                    <select name="leave_type" id="leave_type" class="form-control" required>
                                        <option value="">Select Leave Type</option>
                                        <option value="Medical Leave" {{ $leave->leave_type == 'Medical Leave' ? 'selected' : '' }}>Medical Leave</option>
                                        <option value="Hospitalisation" {{ $leave->leave_type == 'Hospitalisation' ? 'selected' : '' }}>Hospitalisation</option>
                                        <option value="Maternity Leave" {{ $leave->leave_type == 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                                        <option value="Casual Leave" {{ $leave->leave_type == 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                                        <option value="LOP" {{ $leave->leave_type == 'LOP' ? 'selected' : '' }}>LOP</option>
                                        <option value="Paternity Leave" {{ $leave->leave_type == 'Paternity Leave' ? 'selected' : '' }}>Paternity Leave</option>
                                        <option value="Sick" {{ $leave->leave_type == 'Sick' ? 'selected' : '' }}>Sick</option>
                                    </select>
                                    <div class="error-message text-danger" style="display: none;">Leave type is required.</div>
                                </div>

                                <!-- From Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="from_date">From <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date', $leave->from_date) }}" required>
                                    <div class="error-message text-danger" style="display: none;">Start date is required.</div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="to_date">To <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date', $leave->to_date) }}" required>
                                    <div class="error-message text-danger" style="display: none;">End date is required.</div>
                                </div>

                                <!-- Number of Days -->
                                <div class="col-md-6 mb-3">
                                    <label for="num_days">Number of Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="num_days" id="num_days" value="{{ old('num_days', $leave->no_of_days) }}" readonly>
                                    <div class="error-message text-danger" style="display: none;">Number of days is required.</div>
                                </div>

                                <!-- Remaining Leaves -->
                                <div class="col-md-6 mb-3">
                                    <label for="remaining_leaves">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="remaining_leaves" id="remaining_leaves" value="{{ old('remaining_leaves', $remainingLeaves) }}"
                                     readonly>
                                    <div class="error-message text-danger" style="display: none;">Remaining leaves are required.</div>
                                </div>

                                <!-- Leave Reason -->
                                <div class="col-md-12 mb-3">
                                    <label for="leave_reason">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="leave_reason" id="leave_reason" rows="3" placeholder="Enter Leave Reason" maxlength="255" required>{{ old('leave_reason', $leave->leave_reason) }}</textarea>
                                    <div class="error-message text-danger" style="display: none;">Leave reason is required and must be less than 255 characters.</div>
                                </div>

                                <!-- Medical Certificate Upload (Optional) -->
                                <div class="col-md-12 mb-3">
                                    <label for="medical_certificate">Medical Certificate (Optional)</label>
                                    
                                    <!-- Current Medical Certificate Display -->
                                    @if($leave->medical_certificate)
                                    <div class="mb-3">
                                        <div class="alert alert-success p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file-medical me-2"></i>
                                                    <strong>Current Medical Certificate:</strong> 
                                                    
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMedicalCertificate()">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="current_medical_certificate" value="{{ $leave->medical_certificate }}">
                                    </div>
                                    @endif

                                    <!-- New Medical Certificate Upload -->
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control" 
                                               name="medical_certificate" 
                                               id="medical_certificate"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearMedicalCertificate()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Allowed file types: PDF, JPG, JPEG, PNG, DOC, DOCX. Max size: 5MB
                                        @if($leave->medical_certificate)
                                        <br><span class="text-warning">Uploading a new file will replace the current certificate.</span>
                                        @endif
                                    </small>
                                    <div class="error-message text-danger" style="display: none;" id="medical_certificate_error">
                                        Invalid file type or size exceeded.
                                    </div>
                                    <div id="filePreview" class="mt-2" style="display: none;">
                                        <div class="alert alert-info p-2">
                                            <i class="fas fa-file"></i> 
                                            <span id="fileName"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearMedicalCertificate()">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Remove Certificate Checkbox -->
                                    @if($leave->medical_certificate)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_medical_certificate" id="remove_medical_certificate" value="1">
                                        <label class="form-check-label text-danger" for="remove_medical_certificate">
                                            Remove current medical certificate
                                        </label>
                                    </div>
                                    @endif
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitLeaveBtn">Update Leave</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation and Calculation Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('leaveForm');
    const submitBtn = document.getElementById('submitLeaveBtn');
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    const numDaysInput = document.getElementById('num_days');
    const remainingLeavesInput = document.getElementById('remaining_leaves');
    const medicalCertificateInput = document.getElementById('medical_certificate');
    const filePreview = document.getElementById('filePreview');
    const fileNameSpan = document.getElementById('fileName');
    const medicalCertificateError = document.getElementById('medical_certificate_error');
    const removeCertificateCheckbox = document.getElementById('remove_medical_certificate');

    const originalRemainingLeaves = parseInt(remainingLeavesInput.value, 10);

    // Medical certificate file validation
    medicalCertificateInput.addEventListener('change', function(e) {
        validateMedicalCertificate();
    });

    // Remove certificate checkbox handler
    if (removeCertificateCheckbox) {
        removeCertificateCheckbox.addEventListener('change', function() {
            if (this.checked) {
                medicalCertificateInput.disabled = true;
                clearMedicalCertificate();
            } else {
                medicalCertificateInput.disabled = false;
            }
        });
    }

    function validateMedicalCertificate() {
        const file = medicalCertificateInput.files[0];
        medicalCertificateError.style.display = 'none';
        
        if (file) {
            // Check file type
            const allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            // Check file size (5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            
            if (!allowedTypes.includes(file.type)) {
                medicalCertificateError.innerText = 'Invalid file type. Please upload PDF, JPG, JPEG, PNG, DOC, or DOCX files.';
                medicalCertificateError.style.display = 'block';
                medicalCertificateInput.value = '';
                hideFilePreview();
                return false;
            }
            
            if (file.size > maxSize) {
                medicalCertificateError.innerText = 'File size exceeds 5MB limit.';
                medicalCertificateError.style.display = 'block';
                medicalCertificateInput.value = '';
                hideFilePreview();
                return false;
            }
            
            // Show file preview
            showFilePreview(file.name);
            return true;
        } else {
            hideFilePreview();
            return true;
        }
    }

    function showFilePreview(name) {
        fileNameSpan.textContent = name;
        filePreview.style.display = 'block';
    }

    function hideFilePreview() {
        filePreview.style.display = 'none';
        fileNameSpan.textContent = '';
    }

    function clearMedicalCertificate() {
        medicalCertificateInput.value = '';
        hideFilePreview();
        medicalCertificateError.style.display = 'none';
    }

    function removeMedicalCertificate() {
        if (confirm('Are you sure you want to remove the current medical certificate?')) {
            if (removeCertificateCheckbox) {
                removeCertificateCheckbox.checked = true;
                medicalCertificateInput.disabled = true;
            }
            // You might want to add some visual feedback here
        }
    }

    function validateField(field) {
        const errorMessage = field.nextElementSibling;
        const trimmedValue = field.value.trim();

        if (!trimmedValue) {
            errorMessage.style.display = 'block';
        } else if (field.name === 'employee_name') {
            if (trimmedValue.length < 3) {
                errorMessage.innerText = 'Employee name must be at least 3 characters.';
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else if (field.name === 'leave_reason') {
            if (trimmedValue.length > 255) {
                errorMessage.innerText = `Leave reason exceeds the maximum limit of 255 characters. (${trimmedValue.length} characters used)`;
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else {
            errorMessage.style.display = 'none';
        }
    }

    function calculateDays() {
        const fromDate = new Date(fromDateInput.value);
        const toDate = new Date(toDateInput.value);

        if (fromDate && toDate && fromDate <= toDate) {
            // Calculate difference in days (inclusive of both dates)
            const timeDiff = toDate.getTime() - fromDate.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;
            numDaysInput.value = daysDiff;
            updateRemainingLeaves(daysDiff);
        } else {
            numDaysInput.value = '';
            updateRemainingLeaves(0);
        }
    }

    function updateRemainingLeaves(takenDays) {
        const newRemainingLeaves = originalRemainingLeaves - takenDays;
        remainingLeavesInput.value = newRemainingLeaves < 0 ? 0 : newRemainingLeaves;
    }

    // Event listeners
    form.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.name !== 'medical_certificate' && field.type !== 'checkbox') {
            field.addEventListener('input', () => validateField(field));
        }
    });

    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
        calculateDays();
    });

    toDateInput.addEventListener('change', calculateDays);

    // Initial calculation
    calculateDays();

    form.addEventListener('submit', function(event) {
        let allValid = true;
        
        // Validate medical certificate
        if (!validateMedicalCertificate()) {
            allValid = false;
        }
        
        // Validate other fields
        form.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name !== 'medical_certificate' && field.type !== 'checkbox' && field.name !== 'remove_medical_certificate') {
                const trimmedValue = field.value.trim();
                if (!trimmedValue) {
                    allValid = false;
                    validateField(field);
                } else if (field.name === 'leave_reason' && trimmedValue.length > 255) {
                    allValid = false;
                    validateField(field);
                }
            }
        });

        if (allValid) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Updating...';
            submitBtn.classList.add('btn-processing');
        } else {
            event.preventDefault();
        }
    });
});

// Global functions
function clearMedicalCertificate() {
    const input = document.getElementById('medical_certificate');
    const checkbox = document.getElementById('remove_medical_certificate');
    
    if (input) {
        input.value = '';
    }
    if (checkbox) {
        checkbox.checked = false;
        input.disabled = false;
    }
    
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('medical_certificate_error').style.display = 'none';
}

function removeMedicalCertificate() {
    if (confirm('Are you sure you want to remove the current medical certificate?')) {
        const checkbox = document.getElementById('remove_medical_certificate');
        const input = document.getElementById('medical_certificate');
        
        if (checkbox) {
            checkbox.checked = true;
            if (input) {
                input.disabled = true;
                input.value = '';
            }
        }
        
        // Show success message
        alert('Medical certificate will be removed upon saving.');
    }
}
</script>

<style>
.btn-processing {
    position: relative;
    padding-left: 40px;
}
.btn-processing:after {
    content: "";
    position: absolute;
    left: 12px;
    top: 50%;
    width: 20px;
    height: 20px;
    margin-top: -10px;
    border: 2px solid #fff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* File input styling */
.input-group button {
    border: 1px solid #ced4da;
    border-left: none;
}

/* Current certificate styling */
.alert-success a {
    color: #0f5132;
    font-weight: bold;
}
.alert-success a:hover {
    text-decoration: underline;
}
</style>
@endsection