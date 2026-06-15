@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Permission</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Permission List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Permission</h4>
                    <div class="card-body">
                        <form id="permissionForm" method="POST" action="{{ route('employee-permissions.update', $permission->id) }}" class="needs-validation" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Employee ID Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="employee_id" id="employee_id" value="{{ old('employee_id', $employeeid) }}" readonly required>
                                    <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
                                </div>

                                <!-- Permission Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="permission_date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="permission_date" id="permission_date" value="{{ old('permission_date', $permission->permission_date) }}" required>
                                    <div class="error-message text-danger" style="display: none;">Permission date is required.</div>
                                </div>

                                <!-- Start Time -->
                                <div class="col-md-6 mb-3">
                                    <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time', $permission->start_time) }}" required>
                                    <div class="error-message text-danger" style="display: none;">Start time is required.</div>
                                </div>

                                <!-- End Time -->
                                <div class="col-md-6 mb-3">
                                    <label for="end_time">End Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="end_time" id="end_time" value="{{ old('end_time', $permission->end_time) }}" required>
                                    <div class="error-message text-danger" style="display: none;">End time is required.</div>
                                </div>

                                <!-- Duration (Auto-calculated) -->
                                <div class="col-md-6 mb-3">
                                    <label for="duration">Duration (Hours) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="duration" id="duration" value="{{ old('duration', $permission->duration) }}" step="0.5" readonly>
                                    <div class="error-message text-danger" style="display: none;">Duration is required.</div>
                                </div>

                                <!-- Permission Reason -->
                                <div class="col-md-12 mb-3">
                                    <label for="permission_reason">Permission Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="permission_reason" id="permission_reason" rows="3" placeholder="Enter Permission Reason" maxlength="255" required>{{ old('permission_reason', $permission->permission_reason) }}</textarea>
                                    <div class="error-message text-danger" style="display: none;">Permission reason is required and must be less than 255 characters.</div>
                                </div>

                                <!-- Supporting Document Upload (Optional) -->
                                <div class="col-md-12 mb-3">
                                    <label for="supporting_document">Supporting Document (Optional)</label>
                                    
                                    <!-- Current Supporting Document Display -->
                                    @if($permission->supporting_document)
                                    <div class="mb-3">
                                        <div class="alert alert-success p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file-medical me-2"></i>
                                                    <!-- <strong>Current Supporting Document:</strong>  -->
                                                    <!-- <a href="{{ Storage::url($permission->supporting_document) }}" target="_blank" class="ms-2">
                                                        View Current Document
                                                    </a> -->
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSupportingDocument()">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="current_supporting_document" value="{{ $permission->supporting_document }}">
                                    </div>
                                    @endif

                                    <!-- New Supporting Document Upload -->
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control" 
                                               name="supporting_document" 
                                               id="supporting_document"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearSupportingDocument()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Allowed file types: PDF, JPG, JPEG, PNG, DOC, DOCX. Max size: 5MB
                                        @if($permission->supporting_document)
                                        <br><span class="text-warning">Uploading a new file will replace the current document.</span>
                                        @endif
                                    </small>
                                    <div class="error-message text-danger" style="display: none;" id="supporting_document_error">
                                        Invalid file type or size exceeded.
                                    </div>
                                    <div id="supportingDocumentPreview" class="mt-2" style="display: none;">
                                        <div class="alert alert-info p-2">
                                            <i class="fas fa-file"></i> 
                                            <span id="supportingDocumentName"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearSupportingDocument()">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Remove Document Checkbox -->
                                    @if($permission->supporting_document)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_supporting_document" id="remove_supporting_document" value="1">
                                        <label class="form-check-label text-danger" for="remove_supporting_document">
                                            Remove current supporting document
                                        </label>
                                    </div>
                                    @endif
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitPermissionBtn">Update Permission</button>
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
    const permissionForm = document.getElementById('permissionForm');
    const submitPermissionBtn = document.getElementById('submitPermissionBtn');
    const permissionDateInput = document.getElementById('permission_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const durationInput = document.getElementById('duration');
    const supportingDocumentInput = document.getElementById('supporting_document');
    const supportingDocumentPreview = document.getElementById('supportingDocumentPreview');
    const supportingDocumentNameSpan = document.getElementById('supportingDocumentName');
    const supportingDocumentError = document.getElementById('supporting_document_error');
    const removeSupportingDocumentCheckbox = document.getElementById('remove_supporting_document');

    // Set min date to today
    const today = new Date().toISOString().split('T')[0];
    permissionDateInput.min = today;

    // Supporting document file validation
    if (supportingDocumentInput) {
        supportingDocumentInput.addEventListener('change', function(e) {
            validateSupportingDocument();
        });
    }

    // Remove supporting document checkbox handler
    if (removeSupportingDocumentCheckbox) {
        removeSupportingDocumentCheckbox.addEventListener('change', function() {
            if (this.checked) {
                supportingDocumentInput.disabled = true;
                clearSupportingDocument();
            } else {
                supportingDocumentInput.disabled = false;
            }
        });
    }

    function validateSupportingDocument() {
        const file = supportingDocumentInput.files[0];
        supportingDocumentError.style.display = 'none';
        
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
                supportingDocumentError.innerText = 'Invalid file type. Please upload PDF, JPG, JPEG, PNG, DOC, or DOCX files.';
                supportingDocumentError.style.display = 'block';
                supportingDocumentInput.value = '';
                hideSupportingDocumentPreview();
                return false;
            }
            
            if (file.size > maxSize) {
                supportingDocumentError.innerText = 'File size exceeds 5MB limit.';
                supportingDocumentError.style.display = 'block';
                supportingDocumentInput.value = '';
                hideSupportingDocumentPreview();
                return false;
            }
            
            // Show file preview
            showSupportingDocumentPreview(file.name);
            return true;
        } else {
            hideSupportingDocumentPreview();
            return true;
        }
    }

    function showSupportingDocumentPreview(name) {
        supportingDocumentNameSpan.textContent = name;
        supportingDocumentPreview.style.display = 'block';
    }

    function hideSupportingDocumentPreview() {
        supportingDocumentPreview.style.display = 'none';
        supportingDocumentNameSpan.textContent = '';
    }

    function clearSupportingDocument() {
        if (supportingDocumentInput) {
            supportingDocumentInput.value = '';
        }
        hideSupportingDocumentPreview();
        supportingDocumentError.style.display = 'none';
    }

    function validatePermissionField(field) {
        const errorMessage = field.nextElementSibling;
        const trimmedValue = field.value.trim();

        if (!trimmedValue) {
            errorMessage.style.display = 'block';
        } else if (field.name === 'permission_reason') {
            if (trimmedValue.length > 255) {
                errorMessage.innerText = `Permission reason exceeds the maximum limit of 255 characters. (${trimmedValue.length} characters used)`;
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else {
            errorMessage.style.display = 'none';
        }
    }

    function calculateDuration() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime) {
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            
            if (end < start) {
                // If end time is before start time, assume it's the next day
                end.setDate(end.getDate() + 1);
            }
            
            const durationMs = end - start;
            const durationHours = (durationMs / (1000 * 60 * 60)).toFixed(1);
            
            durationInput.value = durationHours;
        } else {
            durationInput.value = '';
        }
    }

    // Event listeners
    if (permissionForm) {
        startTimeInput.addEventListener('change', calculateDuration);
        endTimeInput.addEventListener('change', calculateDuration);

        permissionForm.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name !== 'supporting_document' && field.type !== 'checkbox') {
                field.addEventListener('input', () => validatePermissionField(field));
            }
        });

        // Initial duration calculation
        calculateDuration();

        permissionForm.addEventListener('submit', function(event) {
            let allValid = true;
            
            // Validate supporting document
            if (!validateSupportingDocument()) {
                allValid = false;
            }
            
            // Validate other fields
            permissionForm.querySelectorAll('input, select, textarea').forEach(field => {
                if (field.name !== 'supporting_document' && field.type !== 'checkbox' && field.name !== 'remove_supporting_document') {
                    const trimmedValue = field.value.trim();
                    if (!trimmedValue) {
                        allValid = false;
                        validatePermissionField(field);
                    } else if (field.name === 'permission_reason' && trimmedValue.length > 255) {
                        allValid = false;
                        validatePermissionField(field);
                    }
                }
            });

            if (allValid) {
                submitPermissionBtn.disabled = true;
                submitPermissionBtn.innerText = 'Updating...';
                submitPermissionBtn.classList.add('btn-processing');
            } else {
                event.preventDefault();
            }
        });
    }
});

// Global functions
function clearSupportingDocument() {
    const input = document.getElementById('supporting_document');
    const checkbox = document.getElementById('remove_supporting_document');
    
    if (input) {
        input.value = '';
    }
    if (checkbox) {
        checkbox.checked = false;
        input.disabled = false;
    }
    
    document.getElementById('supportingDocumentPreview').style.display = 'none';
    document.getElementById('supporting_document_error').style.display = 'none';
}

function removeSupportingDocument() {
    if (confirm('Are you sure you want to remove the current supporting document?')) {
        const checkbox = document.getElementById('remove_supporting_document');
        const input = document.getElementById('supporting_document');
        
        if (checkbox) {
            checkbox.checked = true;
            if (input) {
                input.disabled = true;
                input.value = '';
            }
        }
        alert('Supporting document will be removed upon saving.');
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

.input-group button {
    border: 1px solid #ced4da;
    border-left: none;
}

.alert-success a {
    color: #0f5132;
    font-weight: bold;
}
.alert-success a:hover {
    text-decoration: underline;
}
</style>
@endsection