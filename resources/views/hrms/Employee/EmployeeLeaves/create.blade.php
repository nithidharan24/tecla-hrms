@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Leave / Permission</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('employee-leaves.index') }}" class="breadcrumb-link">Leave List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Leave/Permission</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Leave / Permission</h4>
                    <div class="card-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button" role="tab" aria-controls="leave" aria-selected="true">
                                    Apply Leave
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="permission-tab" data-bs-toggle="tab" data-bs-target="#permission" type="button" role="tab" aria-controls="permission" aria-selected="false">
                                    Apply Permission
                                </button>
                            </li>
                        </ul>

                        <!-- Shift Information Alert -->
                        @if(isset($employeeShift) && $employeeShift)
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Current Shift:</strong> {{ $employeeShift->shift_name }}
                            @if($employeeShift->start_time && $employeeShift->end_time)
                                | <strong>Timing:</strong> {{ date('g:i A', strtotime($employeeShift->start_time)) }} - {{ date('g:i A', strtotime($employeeShift->end_time)) }}
                            @endif
                            @if($employeeShift->repeat_every_week == 1)
                                | <strong>Duration:</strong> Ongoing
                            @elseif($employeeShift->schedule_end_date)
                                | <strong>End Date:</strong> {{ date('d M Y', strtotime($employeeShift->schedule_end_date)) }}
                            @endif
                        </div>
                        @endif

                        <!-- JS config passed via data attributes -->
                        <div id="jsConfig"
                             data-week-off-days="{{ implode(',', $weekOffDays) }}"
                             data-max-allowed="{{ intval($maxAllowed) }}"
                             style="display:none;"></div>
                        <div class="tab-content" id="myTabContent">
                            <!-- Leave Tab -->
                            <div class="tab-pane fade show active" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                                <form id="leaveForm" method="POST" action="{{ route('employee-leaves.store') }}" class="needs-validation mt-3" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <!-- Employee ID Input -->
                                        <div class="col-md-6 mb-3">
                                            <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="employee_id" 
                                                id="employee_id" 
                                                value="{{ $employee->employeeid ?? '' }}" 
                                                readonly
                                                required
                                            >
                                            <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
                                        </div>

                                        <!-- Leave Type (Dropdown) -->
                                        <div class="col-md-6 mb-3">
                                            <label for="leave_type">Leave Type <span class="text-danger">*</span></label>
                                            <select name="leave_type" id="leave_type" class="form-control" required>
                                                <option value="">Select Leave Type</option>
                                                @foreach($leaveBalances as $type => $balance)
                                                    <option 
                                                        value="{{ $type }}"
                                                        data-balance="{{ $balance }}"
                                                        @if($type == 'Casual Leave') selected @endif
                                                    >
                                                        {{ $type }} 
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="error-message text-danger" style="display: none;">Leave type is required.</div>
                                        </div>

                                        <!-- From Date -->
                                        <div class="col-md-6 mb-3">
                                            <label for="from_date">From <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="from_date" id="from_date" required>
                                            <div class="error-message text-danger" style="display: none;">Start date is required.</div>
                                        </div>

                                        <!-- To Date -->
                                        <div class="col-md-6 mb-3">
                                            <label for="to_date">To <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="to_date" id="to_date" required>
                                            <div class="error-message text-danger" style="display: none;">End date is required.</div>
                                        </div>

                                        <!-- Number of Days -->
                                        <div class="col-md-6 mb-3">
                                            <label for="num_days">Number of Days <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="num_days" id="num_days" placeholder="Enter Number of Days" readonly>
                                            <div class="error-message text-danger" style="display: none;">Number of days is required.</div>
                                        </div>

                                    <!-- Remaining Leaves -->
                                    <div class="col-md-6 mb-3">
                                        <label for="remaining_leaves">Remaining Leaves <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="remaining_leaves" id="remaining_leaves" value="{{ $remainingLeaves }}" readonly>
                                        <div class="error-message text-danger" style="display: none;">Remaining leaves are required.</div>
                                    </div>

                                    <!-- Leave Summary -->
                                    <div class="col-md-12 mb-3" id="leaveSummaryContainer" style="display: none;">
                                        <div class="alert alert-info p-2 mb-0">
                                            <strong>Leave Summary:</strong>
                                            Total working days: <span id="summaryTotal">0</span> &nbsp;|&nbsp;
                                            Paid: <span id="summaryPaid" class="text-success fw-bold">0</span> &nbsp;|&nbsp;
                                            LOP: <span id="summaryLop" class="text-danger fw-bold">0</span>
                                        </div>
                                    </div>

                                    <!-- LOP Days Calculation -->
                                    <div class="col-md-6 mb-3" id="lopDaysContainer" style="display: none;">
                                        <label for="lop_days_display">LOP Days (Loss of Pay)</label>
                                        <input type="number" class="form-control bg-warning" name="lop_days_display" id="lop_days_display" value="0" readonly>
                                        <small class="text-warning">Days exceeding max_allowed ({{ $maxAllowed ?? 0 }}) will be marked as LOP.</small>
                                    </div>

                                        <!-- Leave Reason -->
                                        <div class="col-md-12 mb-3">
                                            <label for="leave_reason">Leave Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="leave_reason" id="leave_reason" rows="3" placeholder="Enter Leave Reason" maxlength="255" required></textarea>
                                            <div class="error-message text-danger" style="display: none;">Leave reason is required and must be less than 255 characters.</div>
                                        </div>

                                        <!-- Medical Certificate Upload (Optional) -->
                                        <div class="col-md-12 mb-3">
                                            <label for="medical_certificate">Medical Certificate (Optional)</label>
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
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-12 mt-4">
                                            <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitLeaveBtn">Save Leave</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Permission Tab -->
                            <div class="tab-pane fade" id="permission" role="tabpanel" aria-labelledby="permission-tab">
                                <form id="permissionForm" method="POST" action="{{ route('employee-permissions.store') }}" class="needs-validation mt-3" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <!-- Employee ID Input -->
                                        <div class="col-md-6 mb-3">
                                            <label for="permission_employee_id">Employee ID <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="employee_id" 
                                                id="permission_employee_id" 
                                                value="{{ $employee->employeeid ?? '' }}" 
                                                readonly
                                                required
                                            >
                                            <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
                                        </div>

                                        <!-- Permission Date -->
                                        <div class="col-md-6 mb-3">
                                            <label for="permission_date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="permission_date" id="permission_date" required>
                                            <div class="error-message text-danger" style="display: none;">Permission date is required.</div>
                                        </div>

                                        <!-- Start Time -->
                                        <div class="col-md-6 mb-3">
                                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="start_time" id="start_time" required>
                                            <div class="error-message text-danger" style="display: none;">Start time is required.</div>
                                        </div>

                                        <!-- End Time -->
                                        <div class="col-md-6 mb-3">
                                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="end_time" id="end_time" required>
                                            <div class="error-message text-danger" style="display: none;">End time is required.</div>
                                        </div>

                                        <!-- Duration (Auto-calculated) -->
                                        <div class="col-md-6 mb-3">
                                            <label for="duration">Duration (Hours) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="duration" id="duration" step="0.5" readonly>
                                            <div class="error-message text-danger" style="display: none;">Duration is required.</div>
                                        </div>

                                        <!-- Permission Reason -->
                                        <div class="col-md-12 mb-3">
                                            <label for="permission_reason">Permission Reason <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="permission_reason" id="permission_reason" rows="3" placeholder="Enter Permission Reason" maxlength="255" required></textarea>
                                            <div class="error-message text-danger" style="display: none;">Permission reason is required and must be less than 255 characters.</div>
                                        </div>

                                        <!-- Supporting Document Upload (Optional) -->
                                        <div class="col-md-12 mb-3">
                                            <label for="supporting_document">Supporting Document (Optional)</label>
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
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-12 mt-4">
                                            <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitPermissionBtn">Save Permission</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation and Calculation Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Leave Form Elements
    const leaveForm = document.getElementById('leaveForm');
    const submitLeaveBtn = document.getElementById('submitLeaveBtn');
    const leaveTypeSelect = document.getElementById('leave_type');
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    const numDaysInput = document.getElementById('num_days');
    const remainingLeavesInput = document.getElementById('remaining_leaves');
    const lopDaysContainer = document.getElementById('lopDaysContainer');
    const lopDaysDisplay = document.getElementById('lop_days_display');
    const leaveSummaryContainer = document.getElementById('leaveSummaryContainer');
    const summaryTotal = document.getElementById('summaryTotal');
    const summaryPaid  = document.getElementById('summaryPaid');
    const summaryLop   = document.getElementById('summaryLop');
    const medicalCertificateInput = document.getElementById('medical_certificate');
    const filePreview = document.getElementById('filePreview');
    const fileNameSpan = document.getElementById('fileName');
    const medicalCertificateError = document.getElementById('medical_certificate_error');

    // Permission Form Elements
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

    // Store initial leave balances
    const leaveBalances = {};
    document.querySelectorAll('#leave_type option').forEach(option => {
        if (option.value) {
            const bal = parseInt(option.dataset.balance);
            leaveBalances[option.value] = isNaN(bal) ? 999 : bal;
        }
    });

    // Read config from data attributes (avoids PHP inside JS parse issues)
    const jsConfig    = document.getElementById('jsConfig');
    const weekOffRaw  = jsConfig.dataset.weekOffDays;
    const weekOffDays = weekOffRaw ? weekOffRaw.split(',') : ['Sat','Sun'];
    const maxAllowed  = parseInt(jsConfig.dataset.maxAllowed) || 0;

    // Set min date to today for both forms
    const today = new Date().toISOString().split('T')[0];
    fromDateInput.min = today;
    toDateInput.min = today;
    permissionDateInput.min = today;

    // LEAVE FORM VALIDATION AND CALCULATIONS
    // Medical certificate file validation
    medicalCertificateInput.addEventListener('change', function(e) {
        validateMedicalCertificate();
    });

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

    function validateLeaveField(field) {
        const errorMessage = field.nextElementSibling;
        const trimmedValue = field.value.trim();

        if (!trimmedValue) {
            errorMessage.style.display = 'block';
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

    // Day mapping for Date.getDay()
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    /**
     * Calculate only working days (excluding employee's week-offs)
     * between from_date and to_date
     */
    // Parse YYYY-MM-DD as LOCAL date (avoids UTC midnight timezone off-by-one)
    function parseLocalDate(str) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function calculateWorkingDays(fromStr, toStr) {
        let count = 0;
        const end = parseLocalDate(toStr);
        const cur = parseLocalDate(fromStr);
        while (cur <= end) {
            const dayName = dayNames[cur.getDay()];
            if (!weekOffDays.includes(dayName)) count++;
            cur.setDate(cur.getDate() + 1);
        }
        return count;
    }

    function calculateDays() {
        const fromVal = fromDateInput.value;
        const toVal   = toDateInput.value;
        if (!fromVal || !toVal) return;

        const fromDate = parseLocalDate(fromVal);
        const toDate   = parseLocalDate(toVal);

        if (fromDate <= toDate) {
            const workingDays = calculateWorkingDays(fromVal, toVal);
            numDaysInput.value = workingDays;
            updateRemainingLeaves(workingDays);
        } else {
            numDaysInput.value = '';
            remainingLeavesInput.value = leaveBalances[leaveTypeSelect.value] || 0;
            leaveSummaryContainer.style.display = 'none';
            lopDaysContainer.style.display = 'none';
            lopDaysDisplay.value = 0;
        }
    }

    function updateRemainingLeaves(requestedDays) {
        const selectedLeaveType = leaveTypeSelect.value;
        const currentBalance = leaveBalances[selectedLeaveType] || 0;

        let paidDays = requestedDays;
        let lopDays  = 0;

        if (maxAllowed > 0 && requestedDays > maxAllowed) {
            paidDays = maxAllowed;
            lopDays  = requestedDays - maxAllowed;
        }

        remainingLeavesInput.value = currentBalance - paidDays;

        // Summary
        leaveSummaryContainer.style.display = requestedDays > 0 ? 'block' : 'none';
        summaryTotal.textContent = requestedDays;
        summaryPaid.textContent  = paidDays;
        summaryLop.textContent   = lopDays;

        if (lopDays > 0) {
            lopDaysContainer.style.display = 'block';
            lopDaysDisplay.value = lopDays;
        } else {
            lopDaysContainer.style.display = 'none';
            lopDaysDisplay.value = 0;
        }

        submitLeaveBtn.disabled = false;
    }

    // PERMISSION FORM VALIDATION AND CALCULATIONS
    // Supporting document file validation
    supportingDocumentInput.addEventListener('change', function(e) {
        validateSupportingDocument();
    });

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
        supportingDocumentInput.value = '';
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

    // EVENT LISTENERS FOR LEAVE FORM
    leaveTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        remainingLeavesInput.value = leaveBalances[selectedType] || 0;
        leaveSummaryContainer.style.display = 'none';
        lopDaysContainer.style.display = 'none';

        if (fromDateInput.value && toDateInput.value) {
            calculateDays();
        }
    });

    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
        calculateDays();
    });

    toDateInput.addEventListener('change', calculateDays);

    leaveForm.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.name !== 'medical_certificate') {
            field.addEventListener('input', () => validateLeaveField(field));
        }
    });

    leaveForm.addEventListener('submit', function(event) {
        let allValid = true;
        
        // Prevent double submission
        if (submitLeaveBtn.disabled) { event.preventDefault(); return; }

        // Validate medical certificate
        if (!validateMedicalCertificate()) {
            allValid = false;
        }
        
        // Validate other fields
        leaveForm.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name !== 'medical_certificate') {
                const trimmedValue = field.value.trim();
                if (!trimmedValue) {
                    allValid = false;
                    validateLeaveField(field);
                } else if (field.name === 'leave_reason' && trimmedValue.length > 255) {
                    allValid = false;
                    validateLeaveField(field);
                }
            }
        });

        // Check balance
        if (parseFloat(remainingLeavesInput.value) < 0) {
            allValid = false;
        }

        if (allValid) {
            submitLeaveBtn.disabled = true;
            submitLeaveBtn.innerText = 'Saving...';
            submitLeaveBtn.classList.add('btn-processing');
        } else {
            event.preventDefault();
        }
    });

    // EVENT LISTENERS FOR PERMISSION FORM
    startTimeInput.addEventListener('change', calculateDuration);
    endTimeInput.addEventListener('change', calculateDuration);

    permissionForm.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.name !== 'supporting_document') {
            field.addEventListener('input', () => validatePermissionField(field));
        }
    });

    permissionForm.addEventListener('submit', function(event) {
        let allValid = true;
        
        // Validate supporting document
        if (!validateSupportingDocument()) {
            allValid = false;
        }
        
        // Validate other fields
        permissionForm.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name !== 'supporting_document') {
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
            submitPermissionBtn.innerText = 'Saving...';
            submitPermissionBtn.classList.add('btn-processing');
        } else {
            event.preventDefault();
        }
    });
});

// Global functions for clear buttons
function clearMedicalCertificate() {
    document.getElementById('medical_certificate').value = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('medical_certificate_error').style.display = 'none';
}

function clearSupportingDocument() {
    document.getElementById('supporting_document').value = '';
    document.getElementById('supportingDocumentPreview').style.display = 'none';
    document.getElementById('supporting_document_error').style.display = 'none';
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

/* Tab styling */
.nav-tabs .nav-link.active {
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 600;
}
</style>
@endsection