@php
    $planModules = DB::table('plan_modules')
        ->join('modules', 'plan_modules.module_id', '=', 'modules.id')
        ->pluck('modules.name')
        ->toArray();
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Employee</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('employee.index') }}" class="breadcrumb-link">Employee List</a></li>
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
                    <h4 class="card-header">Edit Employee - {{ $employee->firstname }} {{ $employee->lastname }}</h4>
                    <div class="card-body">
                        <form id="employeeEditForm" method="POST" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                              <!-- Employee ID Display -->
<div class="col-md-6 mb-3">
    <label class="form-label">Employee ID</label>
    <input type="text" class="form-control" name="employeeid" 
           value="{{ old('employeeid', $employee->employeeid) }}" required>
    
</div>


                                <!-- Current Profile Image Display -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Current Profile Image</label>
                                    <div class="current-image-container">
                                        @if($employee->profile_image)
                                            <img src="{{ asset($employee->profile_image) }}" alt="Current Profile" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        @else
                                            <div class="alert alert-info">No profile image uploaded</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- First Name and Last Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="firstname">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="firstname" id="firstname" 
                                           placeholder="Enter First Name" 
                                           value="{{ old('firstname', $employee->firstname) }}" required>
                                    <div class="invalid-feedback">First name is required and must be at least 3 characters long.</div>
                                    @error('firstname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="lastname" id="lastname" 
                                           placeholder="Enter Last Name" 
                                           value="{{ old('lastname', $employee->lastname) }}" required>
                                    <div class="invalid-feedback">Last name is required.</div>
                                    @error('lastname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                               

                                <div class="col-md-6 mb-3">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           placeholder="Enter Email" 
                                           value="{{ old('email', $employee->email) }}" required>
                                    <div class="invalid-feedback" id="emailError">Valid email is required.</div>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Joining Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="joining_date">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="joiningdate" id="joining_date" 
                                           value="{{ old('joiningdate', $employee->joiningdate) }}" required>
                                    <div class="invalid-feedback">Joining date is required.</div>
                                    @error('joiningdate')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone and Company -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" id="phone"
                                           placeholder="Enter Phone Number"
                                           pattern="[0-9]{10}"
                                           title="Please enter a 10-digit mobile number"
                                           value="{{ old('phone', $employee->phone) }}" required>
                                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                

                                <!-- Department Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="department">Department <span class="text-danger">*</span></label>
                                    <select id="department" name="department" class="form-control" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ old('department', $employee->department) == $department->id ? 'selected' : '' }}>
                                                {{ $department->department }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a department.</div>
                                    @error('department')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Designation Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="designation">Designation <span class="text-danger">*</span></label>
                                    <select id="designation" name="designation" class="form-control" required>
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" 
                                                {{ old('designation', $employee->designation) == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->designation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a designation.</div>
                                    @error('designation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Branch -->
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select id="branch_id" name="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" 
                                                {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a branch.</div>
                                    @error('branch_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Hierarchy Level Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="hierarchy">Hierarchy Level</label>
                                    <select id="hierarchy" name="hierarchy_id" class="form-control">
                                        <option value="">Select Hierarchy Level</option>
                                        @foreach($hierarchies as $hierarchy)
                                            <option value="{{ $hierarchy->id }}"
                                                    data-modules="{{ $hierarchy->modules }}"
                                                    {{ old('hierarchy_id', $employee->hierarchy_id) == $hierarchy->id ? 'selected' : '' }}>
                                                {{ $hierarchy->hierarchy_level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a hierarchy level.</div>
                                    @error('hierarchy_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                             <!-- <CHANGE> Updated Reporting Manager and added Team Lead fields for edit page -->
<!-- Reporting Manager -->
<div class="col-md-6 mb-3">
    <label for="manager_id">Reporting Manager</label>
    <select id="manager_id" name="manager_id" class="form-control" 
        data-current-manager="{{ $employee->manager_id ?? '' }}">
        <option value="">Select Reporting Manager (Optional)</option>
        @foreach($managers as $mgr)
            <option value="{{ $mgr->id }}" 
                {{ $employee->manager_id == $mgr->id ? 'selected' : '' }}>
                {{ $mgr->firstname }} {{ $mgr->lastname }} ({{ $mgr->designation_name }})
            </option>
        @endforeach
    </select>
    <small class="text-muted">Managers from the selected department will appear here (excluding self)</small>
    @error('manager_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Team Lead -->
<div class="col-md-6 mb-3">
    <label for="team_lead_id">Team Lead</label>
    <select name="team_lead_id" id="team_lead_id" class="form-control">
        <option value="">Select Team Lead (Optional)</option>
        @foreach($teamLeads as $tl)
            <option value="{{ $tl->id }}" {{ $employee->team_lead_id == $tl->id ? 'selected' : '' }}>
                {{ $tl->firstname }} {{ $tl->lastname }} ({{ $tl->employeeid }})
            </option>
        @endforeach
    </select>
    <small class="text-muted">Team Leads will appear here (excluding self)</small>
    @error('team_lead_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>


                                <!-- Training Needed -->
                                <div class="col-md-6 mb-3">
                                    <label for="training_needed">Training Needed <span class="text-danger">*</span></label>
                                    <select id="training_needed" name="training_needed" class="form-control" required>
                                        <option value="">Select Training Need</option>
                                        <option value="Yes" {{ old('training_needed', $employee->training_needed) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('training_needed', $employee->training_needed) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select if training is needed.</div>
                                    @error('training_needed')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Trainer Selection -->
                                <div class="col-md-6 mb-3" id="trainer_field" style="{{ old('training_needed', $employee->training_needed) == 'Yes' ? 'display: block;' : 'display: none;' }}">
                                    <label for="trainer_id">Select Trainer <span class="text-danger">*</span></label>
                                    <select id="trainer_id" name="trainer_id" class="form-control">
                                        <option value="">Select Trainer</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" 
                                                {{ old('trainer_id', $employee->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                                {{ $trainer->first_name }} {{ $trainer->last_name }} 
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a trainer when training is needed.</div>
                                    @error('trainer_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Employee Type <span class="text-danger">*</span></label>
                                    <select name="employee_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="Full Time" {{ (isset($employee) && $employee->employee_type == 'Full Time') ? 'selected' : '' }}>Full Time</option>
                                        <option value="Part Time" {{ (isset($employee) && $employee->employee_type == 'Part Time') ? 'selected' : '' }}>Part Time</option>
                                        <option value="Internship" {{ (isset($employee) && $employee->employee_type == 'Internship') ? 'selected' : '' }}>Internship</option>
                                    </select>
                                </div>
                                

                                <!-- Profile Image -->
                                <div class="col-md-6 mb-3">
                                    <label for="profile_image">Update Profile Image</label>
                                    <input type="file" class="form-control" name="profile_image" id="profile_image" accept=".jpg, .jpeg, .png">
                                    <small class="text-muted">Leave empty to keep current image. Allowed formats: .jpg, .jpeg, .png.</small>
                                    <div class="invalid-feedback">Valid image is required. Allowed formats: .jpg, .jpeg, .png.</div>
                                    <div class="mt-2">
                                        <img id="image_preview" src="#" alt="Preview" class="hidden img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                    @error('profile_image')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Document Files -->
                                <div class="col-md-6 mb-3">
                                    <label for="document_files">Add New Documents (Multiple Files)</label>
                                    <input type="file" class="form-control" name="document_files[]" id="document_files"
                                           multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="text-muted">
                                        Add new documents (will be added to existing archive). Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max 5MB per file)
                                    </small>
                                    @if($employee->document_path)
                                    <div class="mt-2">
                                        <a href="{{ route('employee.downloadDocuments', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download Current Documents
                                        </a>
                                    </div>
                                @endif
                                
                                    <div class="invalid-feedback">Please upload valid document files.</div>
                                    @error('document_files')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div id="file-preview" class="mt-2"></div>
                                </div>
                                <!-- Document Reminder (days) -->
<div class="col-md-6 mb-3">
    <label for="document_reminder_days">Document reminder (days)</label>
    <input type="number"
           min="0"
           name="document_reminder_days"
           id="document_reminder_days"
           class="form-control"
           value="{{ old('document_reminder_days', $documentReminder->reminder_days ?? '') }}">
    <small class="text-muted">
        Enter number of days after which HR should be reminded to verify uploaded documents.
        Leave blank or 0 to disable.
    </small>
    @error('document_reminder_days')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
  <div class="mb-3">
    <label class="form-label">Enable Module</label>
    <select id="moduleToggle" class="form-select">
        <!-- Pre-selects 'Yes' if the employee already has permission modules assigned -->
        <option value="no" {{ empty($employeeModules) ? 'selected' : '' }}>No</option>
        <option value="yes" {{ !empty($employeeModules) ? 'selected' : '' }}>Yes</option>
    </select>
</div>
             <!-- Tabs Navigation -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="employeeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab" aria-controls="salary" aria-selected="true">
                        <i class="fas fa-money-bill-wave me-2"></i>Salary Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="false">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab" aria-controls="emergency" aria-selected="false">
                        <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab" aria-controls="bank" aria-selected="false">
                        <i class="fas fa-university me-2"></i>Bank Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab" aria-controls="bank" aria-selected="false">
                        <i class="fas fa-university me-2"></i>Family Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab" aria-controls="education" aria-selected="false">
                        <i class="fas fa-graduation-cap me-2"></i>Education
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab" aria-controls="experience" aria-selected="false">
                        <i class="fas fa-briefcase me-2"></i>Experience
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="employeeTabsContent">
                
                <!-- Salary Details Tab (With Form) -->
                <div class="tab-pane fade show active" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary mb-0">Salary Details</h5>
                            <span class="badge bg-light text-dark" id="salarySummary">Not calculated</span>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Earnings Section -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Earnings</h6>
                            <!-- Gross Salary (auto-calculates Basic) -->
<div class="mb-3">
    <label class="form-label fw-bold">Gross Salary</label>
    <div class="input-group">
        <span class="input-group-text">₹</span>
        <input type="number" class="form-control" name="gross_salary" id="gross_salary"
               value="{{ old('gross_salary', $salary->gross_salary ?? '') }}"
               placeholder="Enter gross salary" step="0.01" min="0">
    </div>
    <small class="text-muted">Enter gross salary to auto-calculate basic (Gross × Gross-to-Basic %)</small>
</div>
                            
                            <!-- Basic Salary -->
                            <div class="mb-3">
                                <label class="form-label">Basic Salary *</label>
                             <!-- Basic Salary -->
<input type="number" class="form-control" name="basic" id="basic"
value="{{ old('basic', $salary->basic ?? '') }}" step="0.01" min="0">
                            </div>

                            <!-- DA -->
                            <div class="mb-3">
                                <label class="form-label">DA (%)</label>
                                <!-- DA -->
<input type="number" class="form-control" name="da" id="da"
value="{{ old('da', isset($salary) ? round(($salary->da / $salary->basic) * 100, 2) : '') }}"
placeholder="DA percentage" step="0.01" min="0" max="100">

                            </div>

                            <!-- HRA -->
                            <div class="mb-3">
                                <label class="form-label">HRA (%)</label>
                              <!-- HRA -->
<input type="number" class="form-control" name="hra" id="hra"
value="{{ old('hra', isset($salary) ? round(($salary->hra / $salary->basic) * 100, 2) : '') }}"
placeholder="HRA percentage" step="0.01" min="0" max="100">

                            </div>

                            <!-- Other Allowances -->
                            <div class="mb-3">
                                <label class="form-label">Conveyance Allowance</label>
                                <!-- Conveyance Allowance -->
<input type="number" class="form-control" name="conveyance" id="conveyance"
value="{{ old('conveyance', $salary->conveyance ?? 0) }}" step="0.01" min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Special Allowance</label>
                  <!-- Special Allowance -->
<input type="number" class="form-control" name="allowance" id="allowance"
value="{{ old('allowance', $salary->allowance ?? 0) }}" step="0.01" min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Medical Allowance</label>
                               <!-- Medical Allowance -->
<input type="number" class="form-control" name="medical" id="medical"
value="{{ old('medical', $salary->medical ?? 0) }}" step="0.01" min="0">
                            </div>
                        </div>

                        <!-- Deductions Section -->
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">Deductions</h6>
                            
                        <!-- PF -->
<!-- PF (EDITABLE) -->
<div class="mb-3">
    <label class="form-label">PF (%) <span class="badge bg-warning ms-2">Editable</span></label>
    <input type="number" 
           class="form-control" 
           name="pf" 
           id="pf" 
           value="{{ old('pf', (isset($salary) && $salary->basic > 0) ? round(($salary->pf / $salary->basic) * 100, 2) : '') }}" 
           placeholder="PF percentage" 
           step="0.01" min="0" max="100">
</div>

<!-- ESI (EDITABLE) -->
<div class="mb-3">
    <label class="form-label">ESI (%) <span class="badge bg-warning ms-2">Editable</span></label>
    <input type="number" 
           class="form-control" 
           name="esi" 
           id="esi" 
           value="{{ old('esi', (isset($salary) && $salary->basic > 0) ? round(($salary->esi / $salary->basic) * 100, 2) : '') }}" 
           placeholder="ESI percentage" 
           step="0.01" min="0" max="100">
</div>

                            <!-- TDS -->
                            <div class="mb-3">
                                <label class="form-label">TDS</label>
                              <!-- TDS -->
<input type="number" class="form-control" name="tds" id="tds"
value="{{ old('tds', $salary->tds ?? 0) }}" step="0.01" min="0">
                            </div>

                            <!-- Professional Tax -->
                            <div class="mb-3">
                                <label class="form-label">Professional Tax</label>
                               <!-- Professional Tax -->
<input type="number" class="form-control" name="tax" id="tax"
value="{{ old('tax', $salary->tax ?? 0) }}" step="0.01" min="0">
                            </div>

                            <!-- Welfare Fund -->
                            <div class="mb-3">
                                <label class="form-label">Welfare Fund</label>
                                <!-- Welfare Fund -->
<input type="number" class="form-control" name="welfare" id="welfare"
value="{{ old('welfare', $salary->welfare ?? 0) }}" step="0.01" min="0">
                            </div>

                            <!-- Net Salary (Readonly) -->
                            <div class="mb-3">
                                <label class="form-label">Net Salary</label>
                             <!-- Net Salary -->
<input type="number" class="form-control bg-light" name="net_salary" id="net_salary"
value="{{ old('net_salary', $salary->net_salary ?? 0) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Calculation Results -->
                    <div class="row mt-4" id="salaryCalculationResults" style="display: none;">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Salary Breakdown</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success">Earnings</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Basic Salary:</td>
                                                    <td class="text-end">₹<span id="display_basic">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>DA:</td>
                                                    <td class="text-end">₹<span id="display_da">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>HRA:</td>
                                                    <td class="text-end">₹<span id="display_hra">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Conveyance:</td>
                                                    <td class="text-end">₹<span id="display_conveyance">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Special Allowance:</td>
                                                    <td class="text-end">₹<span id="display_allowance">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Medical Allowance:</td>
                                                    <td class="text-end">₹<span id="display_medical">0.00</span></td>
                                                </tr>
                                                <tr class="table-success">
                                                    <td><strong>Total Earnings:</strong></td>
                                                    <td class="text-end"><strong>₹<span id="display_total_earnings">0.00</span></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-danger">Deductions</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>PF:</td>
                                                    <td class="text-end">₹<span id="display_pf">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>ESI:</td>
                                                    <td class="text-end">₹<span id="display_esi">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>TDS:</td>
                                                    <td class="text-end">₹<span id="display_tds">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Professional Tax:</td>
                                                    <td class="text-end">₹<span id="display_tax">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Welfare Fund:</td>
                                                    <td class="text-end">₹<span id="display_welfare">0.00</span></td>
                                                </tr>
                                                <tr class="table-danger">
                                                    <td><strong>Total Deductions:</strong></td>
                                                    <td class="text-end"><strong>₹<span id="display_total_deductions">0.00</span></strong></td>
                                                </tr>
                                                <tr class="table-primary">
                                                    <td><strong>Net Salary:</strong></td>
                                                    <td class="text-end"><strong>₹<span id="display_net_salary">0.00</span></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-outline-primary" id="calculateSalaryBtn">
                                <i class="fas fa-calculator me-2"></i>Calculate Salary
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="resetSalaryBtn">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
<!-- Personal Information Tab (Edit Form) -->
<div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
    <div class="card shadow-sm mt-4">
        <h4 class="card-header">Edit Personal Information</h4>
        <div class="card-body">
            <div class="row">
                <!-- Passport No -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Passport No</label>
                    <input type="text" name="passport_no" class="form-control" 
                        value="{{ old('passport_no', $personal->passport_no ?? '') }}"
                        placeholder="Enter Passport Number">
                </div>

                <!-- Aadhaar Number -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Aadhaar Number</label>
                    <input type="text" name="aadhaar_number" class="form-control" 
                        value="{{ old('aadhaar_number', $personal->aadhaar_number ?? '') }}"
                        placeholder="Enter Aadhaar Number">
                </div>

                <!-- Blood Group -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Blood Group</label>
                    <input type="text" name="blood_group" class="form-control" 
                        value="{{ old('blood_group', $personal->blood_group ?? '') }}"
                        placeholder="Enter Blood Group">
                </div>

                <!-- Nationality - remove required -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Nationality</label>  <!-- Removed * and required -->
                    <input type="text" name="nationality" class="form-control" 
                        value="{{ old('nationality', $personal->nationality ?? '') }}"
                        placeholder="Enter Nationality">
                </div>

                <!-- Religion -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Religion</label>
                    <input type="text" name="religion" class="form-control" 
                        value="{{ old('religion', $personal->religion ?? '') }}"
                        placeholder="Enter Religion">
                </div>

                <!-- Marital Status - remove required -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Marital Status</label>  <!-- Removed * and required -->
    <select class="form-control" name="marital_status">
        <option value="">Select</option>
        <option value="Single" {{ old('marital_status', $personal->marital_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
        <option value="Married" {{ old('marital_status', $personal->marital_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
    </select>
</div>

              <!-- Birthday -->
<!-- Birthday - remove required -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Birthday</label>  <!-- Removed * and required -->
    <input type="date" name="birthday" class="form-control"
        value="{{ old('birthday', isset($profile->birthday) ? date('Y-m-d', strtotime($profile->birthday)) : '') }}">
</div>

<!-- Gender - remove required -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Gender</label>  <!-- Removed * and required -->
    <select class="form-control" name="gender">
        <option value="">Select</option>
        <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
        <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
        <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
    </select>
</div>

<div class="col-md-12 mb-3">
    <label class="col-form-label">Address</label>  <!-- Removed * and required -->
    <textarea name="address" class="form-control" rows="3" placeholder="Enter Full Address">{{ old('address', $profile->address ?? '') }}</textarea>
</div>

            </div>
        </div>
    </div>
</div>

<!-- Emergency Contact Tab (Edit Form) -->
<div class="tab-pane fade" id="emergency" role="tabpanel" aria-labelledby="emergency-tab">
    <div class="card shadow-sm mt-4">
        <h4 class="card-header">Edit Emergency Contact</h4>
        <div class="card-body">
            <div class="row">
                <!-- Primary Contact Name -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Primary Contact Name</label>  <!-- Removed * and required -->
    <input type="text" name="primary_name" class="form-control"
        value="{{ old('primary_name', $emergency->primary_name ?? '') }}"
        placeholder="Enter Primary Contact Name">
</div>

                <!-- Relationship -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Relationship</label>  <!-- Removed * and required -->
    <input type="text" name="relationship" class="form-control"
        value="{{ old('relationship', $emergency->relationship ?? '') }}"
        placeholder="Enter Relationship">
</div>

                <!-- Phone -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Phone</label>  <!-- Removed * and required -->
    <input type="text" name="primary_phone" class="form-control"
        value="{{ old('phone', $emergency->phone ?? '') }}"
        placeholder="Enter Phone Number">
</div>

                <!-- Secondary Contact -->
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Secondary Contact Name</label>
                    <input type="text" name="secondary_name" class="form-control"
                        value="{{ old('secondary_name', $emergency->secondary_name ?? '') }}"
                        placeholder="Enter Secondary Contact Name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Secondary Relationship</label>
                    <input type="text" name="secondary_relationship" class="form-control"
                        value="{{ old('secondary_relationship', $emergency->secondary_relationship ?? '') }}"
                        placeholder="Enter Secondary Relationship">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Secondary Phone</label>
                    <input type="text" name="secondary_phone" class="form-control"
                        value="{{ old('secondary_phone', $emergency->secondary_phone ?? '') }}"
                        placeholder="Enter Secondary Phone Number">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bank Information Tab (Edit Form) -->
<div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
    <div class="card shadow-sm mt-4">
        <h4 class="card-header">Bank Information</h4>
        <div class="card-body">
            <div class="row">
                <!-- Bank Name -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Bank Name</label>  <!-- Removed * and required -->
    <input type="text" name="bank_name" id="bank_name" 
        class="form-control" 
        value="{{ $bankInfo->bank_name ?? '' }}" 
        placeholder="Enter Bank Name">
</div>
                <!-- Bank Account No -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Bank Account No.</label>  <!-- Removed * and required -->
    <input type="text" name="bank_account_no" id="bank_account_no" 
        class="form-control" 
        value="{{ $bankInfo->bank_account_no ?? '' }}" 
        placeholder="Enter Account Number">
</div>
            </div>

            <div class="row">
                <!-- IFSC Code -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">IFSC Code</label>  <!-- Removed * and required -->
    <input type="text" name="ifsc_code" id="ifsc_code" 
        class="form-control" 
        value="{{ $bankInfo->ifsc_code ?? '' }}" 
        placeholder="Enter IFSC Code">
</div>

                <!-- PAN No -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">PAN No</label>  <!-- Removed * and required -->
    <input type="text" name="pan_no" id="pan_no" 
        class="form-control" 
        value="{{ $bankInfo->pan_no ?? '' }}" 
        placeholder="Enter PAN Number">
</div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Branch Name</label>
                    <input type="text" name="branch_name" id="branch_name" 
                        class="form-control" 
                        value="{{ $bankInfo->branch_name ?? '' }}" 
                        placeholder="Enter Branch Name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="col-form-label">Account Type</label>
                    <select name="account_type" id="account_type" class="form-control">
                        <option value="">Select</option>
                        <option value="Savings" {{ ($bankInfo->account_type ?? '') == 'Savings' ? 'selected' : '' }}>Savings</option>
                        <option value="Current" {{ ($bankInfo->account_type ?? '') == 'Current' ? 'selected' : '' }}>Current</option>
                        <option value="Salary" {{ ($bankInfo->account_type ?? '') == 'Salary' ? 'selected' : '' }}>Salary</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="family" role="tabpanel" aria-labelledby="family-tab">
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Family Information</h5>
        </div>
        <div class="card-body">
            <div id="family_members_container">
                @if(isset($familyMembers) && count($familyMembers) > 0)
                    @foreach($familyMembers as $index => $member)
                        <div class="family-member mb-3">
                            <h5>Family Member 
                                <a href="javascript:void(0);" class="text-danger remove-family-member">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="family_members[{{ $index }}][name]" 
                                           class="form-control" 
                                           value="{{ $member->name }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                                    <input type="text" name="family_members[{{ $index }}][relationship]" 
                                           class="form-control" 
                                           value="{{ $member->relationship }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="col-form-label">Phone</label>
                                    <input type="text" name="family_members[{{ $index }}][phone]" 
                                           class="form-control" 
                                           value="{{ $member->phone }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Default empty form if no data -->
                    <div class="family-member mb-3">
                        <h5>Family Member 
                            <a href="javascript:void(0);" class="text-danger remove-family-member">
                                <i class="fa fa-trash"></i>
                            </a>
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="family_members[0][name]" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                                <input type="text" name="family_members[0][relationship]" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="col-form-label">Phone</label>
                                <input type="text" name="family_members[0][phone]" class="form-control">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add_family_member" class="btn btn-primary mt-2">Add More</button>
        </div>
    </div>
</div>

<!-- Education Tab -->
<div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Education Information</h5>
        </div>
        <div class="card-body">
            <div id="education_section_container">
                @if(isset($educationInfo) && count($educationInfo) > 0)
                    @foreach($educationInfo as $index => $edu)
                        <div class="card mb-3 education-card">
                            <div class="card-body">
                                <h5>Education 
                                    <a href="javascript:void(0);" class="text-danger remove-education">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Institution <span class="text-danger">*</span></label>
                                        <input type="text" name="education[{{ $index }}][institution]" 
                                               class="form-control" value="{{ $edu->institution }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <input type="text" name="education[{{ $index }}][subject]" 
                                               class="form-control" value="{{ $edu->subject }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Starting Date</label>
                                        <input type="date" name="education[{{ $index }}][start_date]" 
                                               class="form-control" value="{{ $edu->start_date }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Completion Date</label>
                                        <input type="date" name="education[{{ $index }}][end_date]" 
                                               class="form-control" value="{{ $edu->end_date }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Degree</label>
                                        <input type="text" name="education[{{ $index }}][degree]" 
                                               class="form-control" value="{{ $edu->degree }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Grade</label>
                                        <input type="text" name="education[{{ $index }}][grade]" 
                                               class="form-control" value="{{ $edu->grade }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty default form if no data exists -->
                    <div class="card mb-3 education-card">
                        <div class="card-body">
                            <h5>Education 
                                <a href="javascript:void(0);" class="text-danger remove-education">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Institution <span class="text-danger">*</span></label>
                                    <input type="text" name="education[0][institution]" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="education[0][subject]" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Starting Date</label>
                                    <input type="date" name="education[0][start_date]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Completion Date</label>
                                    <input type="date" name="education[0][end_date]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Degree</label>
                                    <input type="text" name="education[0][degree]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Grade</label>
                                    <input type="text" name="education[0][grade]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" id="add_education" class="btn btn-primary btn-lg">Add Another</button>
            </div>
        </div>
    </div>
</div>
<!-- Experience Tab -->
<div class="tab-pane fade" id="experience" role="tabpanel" aria-labelledby="experience-tab">
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Experience Information</h5>
        </div>

        <div class="card-body">
            <div id="experience_section_container">
                @if($experiences->isNotEmpty())
                    @foreach($experiences as $index => $exp)
                        <div class="card mb-3 experience-card">
                            <div class="card-body">
                                <h5>
                                    Experience Information
                                    <a href="javascript:void(0);" class="text-danger remove-experience">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Company Name </label>
                                        <input type="text" name="experience[{{ $index }}][company_name]" 
                                               class="form-control" 
                                               value="{{ $exp->company_name }}" >
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Location </label>
                                        <input type="text" name="experience[{{ $index }}][location]" 
                                               class="form-control" 
                                               value="{{ $exp->location }}" >
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Job Position</label>
                                        <input type="text" name="experience[{{ $index }}][job_position]" 
                                               class="form-control" 
                                               value="{{ $exp->position }}" >
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Period From </label>
                                        <input type="date" name="experience[{{ $index }}][period_from]" 
                                               class="form-control" 
                                               value="{{ $exp->period_from }}" >
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Period To </label>
                                        <input type="date" name="experience[{{ $index }}][period_to]" 
                                               class="form-control" 
                                               value="{{ $exp->period_to }}" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Default empty card if no experience found -->
                    <div class="card mb-3 experience-card">
                        <div class="card-body">
                            <h5>Experience Information
                                <a href="javascript:void(0);" class="text-danger remove-experience">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Company Name </label>
                                    <input type="text" name="experience[0][company_name]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Location </label>
                                    <input type="text" name="experience[0][location]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Job Position </label>
                                    <input type="text" name="experience[0][job_position]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Period From </label>
                                    <input type="date" name="experience[0][period_from]" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Period To </label>
                                    <input type="date" name="experience[0][period_to]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" id="add_experience" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Experience
                </button>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>
<div id="permission_modules_container" style="display: {{ !empty($employeeModules) ? 'block' : 'none' }};">
<!-- Updated Module Categories for Employee Edit Form -->
<div class="col-md-12 mb-4" id="modulePermissions" style="display: {{ count($employeeModules) > 0 ? 'block' : 'none' }};" >
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Module Access Permissions</h5>
                <p class="text-muted mb-0">Select modules and their permissions (Create, Edit, Delete, View)</p>
            </div>
            <div>
                <span class="badge bg-primary" id="hierarchyModuleCount">0 from hierarchy</span>
                <span class="badge bg-success" id="manualModuleCount">0 manual</span>
                <span class="badge bg-info" id="totalModuleCount">0 total</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Search and bulk actions -->
                <div class="col-md-12 mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">
                        <button class="btn btn-outline-secondary" type="button" id="selectAllModulesBtn">Select All Modules</button>
                        <button class="btn btn-outline-secondary" type="button" id="selectAllPermissionsBtn">Select All Permissions</button>
                                        <button class="btn btn-outline-info" type="button" id="showHierarchyModulesBtn">Show Hierarchy Modules</button>
                    </div>
                </div>

                @php
                    $allowedModules = $planModules ?? [];
                @endphp

                @if(in_array('Recruitment Management', $allowedModules))
                 <!-- Recruitment Section -->
                 <div class="col-md-6 mb-3">
                     <div class="category-card">
                         <h6 class="category-header bg-primary text-white p-2 rounded">
                             <i class="fas fa-user-plus me-2"></i>Recruitment
                         </h6>
                         <div class="category-modules">
                             @foreach([
                                 ['key' => 'Recruitment Overview', 'label' => 'Overview', 'perms' => ['view']],
                                 ['key' => 'Job Vacancy Requests', 'label' => 'Job Vacancy Requests', 'perms' => ['view','create','edit','delete','approve']],
                                 ['key' => 'Job Listings', 'label' => 'Job Listings', 'perms' => ['view','create','edit','delete']],
                                 ['key' => 'Candidates Management', 'label' => 'Candidates', 'perms' => ['view','create','edit','delete']],
                                 ['key' => 'Send Offer Letter', 'label' => 'Send Offer Letter', 'perms' => ['view','create','approve']],
                                 ['key' => 'Questions & Experience Management', 'label' => 'Questions & Experience Management', 'perms' => ['view','create','edit','delete']],
                                 ['key' => 'Recruitment Activity Log', 'label' => 'Activity Log', 'perms' => ['view']]
                             ] as $subModule)
                             <div class="module-item" data-module="{{ $subModule['key'] }}">
                                 <div class="module-header">
                                     <div class="form-check">
                                         <input class="form-check-input module-checkbox" type="checkbox" 
                                                id="module_{{ Str::slug($subModule['key'], '_') }}" 
                                                name="modules[{{ $subModule['key'] }}][enabled]" 
                                                value="1"
                                                {{ in_array($subModule['key'], $employeeModules) ? 'checked' : '' }}>
                                         <label class="form-check-label fw-bold" for="module_{{ Str::slug($subModule['key'], '_') }}">
                                             {{ $subModule['label'] }}
                                         </label>
                                     </div>
                                 </div>
                                 <div class="permissions-row" style="{{ in_array($subModule['key'], $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                     <div class="row">
                                         @if(in_array('view', $subModule['perms']))
                                         <div class="col-3">
                                             <div class="form-check form-check-inline">
                                                 <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="modules[{{ $subModule['key'] }}][can_view]" 
                                                        value="1" 
                                                        id="view_{{ Str::slug($subModule['key'], '_') }}"
                                                        {{ (isset($employeePermissions[$subModule['key']]) && $employeePermissions[$subModule['key']]->can_view) ? 'checked' : '' }}>
                                                 <label class="form-check-label text-success" for="view_{{ Str::slug($subModule['key'], '_') }}">
                                                     <i class="fas fa-eye"></i> View
                                                 </label>
                                             </div>
                                         </div>
                                         @endif
                                         @if(in_array('create', $subModule['perms']))
                                         <div class="col-3">
                                             <div class="form-check form-check-inline">
                                                 <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="modules[{{ $subModule['key'] }}][can_create]" 
                                                        value="1" 
                                                        id="create_{{ Str::slug($subModule['key'], '_') }}"
                                                        {{ (isset($employeePermissions[$subModule['key']]) && $employeePermissions[$subModule['key']]->can_create) ? 'checked' : '' }}>
                                                 <label class="form-check-label text-primary" for="create_{{ Str::slug($subModule['key'], '_') }}">
                                                     <i class="fas fa-plus"></i> Create
                                                 </label>
                                             </div>
                                         </div>
                                         @endif
                                         @if(in_array('edit', $subModule['perms']))
                                         <div class="col-3">
                                             <div class="form-check form-check-inline">
                                                 <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="modules[{{ $subModule['key'] }}][can_edit]" 
                                                        value="1" 
                                                        id="edit_{{ Str::slug($subModule['key'], '_') }}"
                                                        {{ (isset($employeePermissions[$subModule['key']]) && $employeePermissions[$subModule['key']]->can_edit) ? 'checked' : '' }}>
                                                 <label class="form-check-label text-warning" for="edit_{{ Str::slug($subModule['key'], '_') }}">
                                                     <i class="fas fa-edit"></i> Edit
                                                 </label>
                                             </div>
                                         </div>
                                         @endif
                                         @if(in_array('delete', $subModule['perms']))
                                         <div class="col-3">
                                             <div class="form-check form-check-inline">
                                                 <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="modules[{{ $subModule['key'] }}][can_delete]" 
                                                        value="1" 
                                                        id="delete_{{ Str::slug($subModule['key'], '_') }}"
                                                        {{ (isset($employeePermissions[$subModule['key']]) && $employeePermissions[$subModule['key']]->can_delete) ? 'checked' : '' }}>
                                                 <label class="form-check-label text-danger" for="delete_{{ Str::slug($subModule['key'], '_') }}">
                                                     <i class="fas fa-trash"></i> Delete
                                                 </label>
                                             </div>
                                         </div>
                                         @endif
                                         @if(in_array('approve', $subModule['perms']))
                                         <div class="col-3">
                                             <div class="form-check form-check-inline">
                                                 <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="modules[{{ $subModule['key'] }}][can_approve]" 
                                                        value="1" 
                                                        id="approve_{{ Str::slug($subModule['key'], '_') }}"
                                                        {{ (isset($employeePermissions[$subModule['key']]) && $employeePermissions[$subModule['key']]->can_approve) ? 'checked' : '' }}>
                                                 <label class="form-check-label text-info" for="approve_{{ Str::slug($subModule['key'], '_') }}">
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

                @if(in_array('Leaves', $allowedModules))
                <!-- Leaves Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-success text-white p-2 rounded">
                            <i class="fas fa-calendar-alt me-2"></i>Leaves Management
                        </h6>
                        <div class="category-modules">
                            @foreach(['Team Leaves', 'Employee Leaves'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_leaves_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_leaves_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_leaves_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_leaves_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_create]" 
                                                       value="1" 
                                                       id="create_leaves_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_leaves_{{ $loop->index }}">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_edit]" 
                                                       value="1" 
                                                       id="edit_leaves_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_leaves_{{ $loop->index }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_delete]" 
                                                       value="1" 
                                                       id="delete_leaves_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_delete) ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="delete_leaves_{{ $loop->index }}">
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

                @if(in_array('Attendance', $allowedModules))
                <!-- Attendance Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-info text-white p-2 rounded">
                            <i class="fas fa-clock me-2"></i>Attendance
                        </h6>
                        <div class="category-modules">
                            @foreach(['Admin Attendance', 'Employee Attendance', 'Late Punch Approval'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_attendance_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_attendance_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_attendance_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_attendance_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_create]" 
                                                       value="1" 
                                                       id="create_attendance_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_attendance_{{ $loop->index }}">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_edit]" 
                                                       value="1" 
                                                       id="edit_attendance_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_attendance_{{ $loop->index }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_delete]" 
                                                       value="1" 
                                                       id="delete_attendance_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_delete) ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="delete_attendance_{{ $loop->index }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </label>
                                            </div>
                                        </div>
                                        @if($module == 'Late Punch Approval')
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_approve]" 
                                                       value="1" 
                                                       id="approve_attendance_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_approve) ? 'checked' : '' }}>
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

                @if(in_array('Time Tracker', $allowedModules))
                <!-- Time Tracker Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-warning text-white p-2 rounded">
                            <i class="fas fa-stopwatch me-2"></i>Time Tracker
                        </h6>
                        <div class="category-modules">
                            @foreach(['Clients', 'Projects', 'Project Tasks', 'My Tasks'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_timetracker_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_timetracker_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        @if($module != 'My Tasks')
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_timetracker_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_timetracker_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_create]" 
                                                       value="1" 
                                                       id="create_timetracker_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_timetracker_{{ $loop->index }}">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_edit]" 
                                                       value="1" 
                                                       id="edit_timetracker_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_timetracker_{{ $loop->index }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_delete]" 
                                                       value="1" 
                                                       id="delete_timetracker_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_delete) ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="delete_timetracker_{{ $loop->index }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </label>
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_timetracker_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
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

                @if(in_array('Onboarding', $allowedModules))
                <!-- Employee Management Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-danger text-white p-2 rounded">
                            <i class="fas fa-users me-2"></i>Employee Management
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Employee">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_employee" 
                                               name="modules[Employee][enabled]" 
                                               value="1"
                                               {{ in_array('Employee', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_employee">
                                            Employee Management
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Employee', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[Employee][can_view]" 
                                                       value="1" 
                                                       id="view_employee"
                                                       {{ (isset($employeePermissions['Employee']) && $employeePermissions['Employee']->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_employee">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[Employee][can_create]" 
                                                       value="1" 
                                                       id="create_employee"
                                                       {{ (isset($employeePermissions['Employee']) && $employeePermissions['Employee']->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_employee">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[Employee][can_edit]" 
                                                       value="1" 
                                                       id="edit_employee"
                                                       {{ (isset($employeePermissions['Employee']) && $employeePermissions['Employee']->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_employee">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[Employee][can_delete]" 
                                                       value="1" 
                                                       id="delete_employee"
                                                       {{ (isset($employeePermissions['Employee']) && $employeePermissions['Employee']->can_delete) ? 'checked' : '' }}>
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

                @if(in_array('Shifts', $allowedModules))
                <!-- Shifts Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-secondary text-white p-2 rounded">
                            <i class="fas fa-user-clock me-2"></i>Shifts
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Manage Shifts">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" 
                                               type="checkbox" 
                                               id="module_manage_shifts" 
                                               name="modules[Manage Shifts][enabled]" 
                                               value="1"
                                               {{ in_array('Manage Shifts', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_manage_shifts">
                                            Manage Shifts
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Manage Shifts', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Manage Shifts][can_view]" value="1"
                                                       {{ (isset($employeePermissions['Manage Shifts']) && $employeePermissions['Manage Shifts']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Manage Shifts][can_create]" value="1"
                                                       {{ (isset($employeePermissions['Manage Shifts']) && $employeePermissions['Manage Shifts']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Manage Shifts][can_edit]" value="1"
                                                       {{ (isset($employeePermissions['Manage Shifts']) && $employeePermissions['Manage Shifts']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Manage Shifts][can_delete]" value="1"
                                                       {{ (isset($employeePermissions['Manage Shifts']) && $employeePermissions['Manage Shifts']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Schedule', $allowedModules))
                <!-- Schedule Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-info text-white p-2 rounded">
                            <i class="fas fa-calendar-alt me-2"></i>Schedule
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Schedule">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" 
                                               type="checkbox" 
                                               id="module_schedule" 
                                               name="modules[Schedule][enabled]" 
                                               value="1"
                                               {{ in_array('Schedule', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_schedule">
                                            Schedule
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Schedule', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Schedule][can_view]" value="1"
                                                       {{ (isset($employeePermissions['Schedule']) && $employeePermissions['Schedule']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Schedule][can_create]" value="1"
                                                       {{ (isset($employeePermissions['Schedule']) && $employeePermissions['Schedule']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Schedule][can_edit]" value="1"
                                                       {{ (isset($employeePermissions['Schedule']) && $employeePermissions['Schedule']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                       name="modules[Schedule][can_delete]" value="1"
                                                       {{ (isset($employeePermissions['Schedule']) && $employeePermissions['Schedule']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Payroll', $allowedModules))
                <!-- Payroll Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-dark text-white p-2 rounded">
                            <i class="fas fa-money-bill me-2"></i>Payroll
                        </h6>
                        <div class="category-modules">
                            @foreach(['Payroll Items', 'Employee Salary', 'Automated Payslips', 'Activity Log'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_payroll_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_payroll_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        @if($module != 'Activity Log')
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_payroll_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_payroll_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_create]" 
                                                       value="1" 
                                                       id="create_payroll_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_payroll_{{ $loop->index }}">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_edit]" 
                                                       value="1" 
                                                       id="edit_payroll_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_payroll_{{ $loop->index }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_delete]" 
                                                       value="1" 
                                                       id="delete_payroll_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_delete) ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="delete_payroll_{{ $loop->index }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </label>
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_payroll_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
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

                @if(in_array('Tickets', $allowedModules))
                <!-- Tickets Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-info text-white p-2 rounded">
                            <i class="fas fa-ticket-alt me-2"></i>Tickets
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Tickets">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_tickets"
                                               name="modules[Tickets][enabled]"
                                               value="1"
                                               {{ in_array('Tickets', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_tickets">
                                            Tickets
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Tickets', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Tickets][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Tickets']) && $employeePermissions['Tickets']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Tickets][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Tickets']) && $employeePermissions['Tickets']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Tickets][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Tickets']) && $employeePermissions['Tickets']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Tickets][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Tickets']) && $employeePermissions['Tickets']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Tickets][can_approve]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Tickets']) && $employeePermissions['Tickets']->can_approve) ? 'checked' : '' }}>
                                                <label class="text-info"><i class="fas fa-check"></i> Approve</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Testing', $allowedModules))
                <!-- Testing Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-secondary text-white p-2 rounded">
                            <i class="fas fa-vial me-2"></i>Testing
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Testing">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_testing"
                                               name="modules[Testing][enabled]"
                                               value="1"
                                               {{ in_array('Testing', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_testing">
                                            Testing
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Testing', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Testing][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Testing']) && $employeePermissions['Testing']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Testing][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Testing']) && $employeePermissions['Testing']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Testing][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Testing']) && $employeePermissions['Testing']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Testing][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Testing']) && $employeePermissions['Testing']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Testing][can_approve]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Testing']) && $employeePermissions['Testing']->can_approve) ? 'checked' : '' }}>
                                                <label class="text-info"><i class="fas fa-check"></i> Approve</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Accounts', $allowedModules))
                <!-- Accounts Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-primary text-white p-2 rounded">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Accounts
                        </h6>
                        <div class="category-modules">
                            @foreach(['Estimates', 'Invoices', 'Payments', 'Expenses', 'Taxes', 'Categories', 'Budgets', 'Budget Expenses', 'Budget Revenues'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_accounts_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_accounts_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_accounts_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_accounts_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_create]" 
                                                       value="1" 
                                                       id="create_accounts_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_create) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="create_accounts_{{ $loop->index }}">
                                                    <i class="fas fa-plus"></i> Create
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_edit]" 
                                                       value="1" 
                                                       id="edit_accounts_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_edit) ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="edit_accounts_{{ $loop->index }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_delete]" 
                                                       value="1" 
                                                       id="delete_accounts_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_delete) ? 'checked' : '' }}>
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

                <!-- Reports Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-secondary text-white p-2 rounded">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </h6>
                        <div class="category-modules">
                            @foreach(['My Reports', 'Team Reports', 'Organization Reports'] as $module)
                            <div class="module-item" data-module="{{ $module }}">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_reports_{{ $loop->index }}" 
                                               name="modules[{{ $module }}][enabled]" 
                                               value="1"
                                               {{ in_array($module, $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_reports_{{ $loop->index }}">
                                            {{ $module }}
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array($module, $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left: 20px; margin-top: 5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_view]" 
                                                       value="1" 
                                                       id="view_reports_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_view) ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="view_reports_{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i> View
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_download]" 
                                                       value="1" 
                                                       id="download_reports_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_download) ? 'checked' : '' }}>
                                                <label class="form-check-label text-primary" for="download_reports_{{ $loop->index }}">
                                                    <i class="fas fa-download"></i> Download
                                                </label>
                                            </div>
                                        </div>
                                        @if($module == 'Team Reports' || $module == 'Organization Reports')
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="modules[{{ $module }}][can_approve]" 
                                                       value="1" 
                                                       id="approve_reports_{{ $loop->index }}"
                                                       {{ (isset($employeePermissions[$module]) && $employeePermissions[$module]->can_approve) ? 'checked' : '' }}>
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

                @if(in_array('Policy', $allowedModules))
                <!-- Policies Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-success text-white p-2 rounded">
                            <i class="fas fa-file-alt me-2"></i>Policies
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Policy">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_policy"
                                               name="modules[Policy][enabled]"
                                               value="1"
                                               {{ in_array('Policy', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_policy">
                                            Policies
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Policy', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Policy][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Policy']) && $employeePermissions['Policy']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Policy][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Policy']) && $employeePermissions['Policy']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Policy][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Policy']) && $employeePermissions['Policy']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Policy][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Policy']) && $employeePermissions['Policy']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Goals', $allowedModules))
                <!-- Goals Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-info text-white p-2 rounded">
                            <i class="fas fa-bullseye me-2"></i>Goals
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Goals">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_goals"
                                               name="modules[Goals][enabled]"
                                               value="1"
                                               {{ in_array('Goals', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_goals">
                                            Goals
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Goals', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Goals][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Goals']) && $employeePermissions['Goals']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Goals][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Goals']) && $employeePermissions['Goals']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Goals][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Goals']) && $employeePermissions['Goals']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Goals][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Goals']) && $employeePermissions['Goals']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Training', $allowedModules))
                <!-- Training Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-primary text-white p-2 rounded">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Training
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Training">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_training"
                                               name="modules[Training][enabled]"
                                               value="1"
                                               {{ in_array('Training', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_training">
                                            Training
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Training', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Training][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Training']) && $employeePermissions['Training']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Training][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Training']) && $employeePermissions['Training']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Training][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Training']) && $employeePermissions['Training']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Training][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Training']) && $employeePermissions['Training']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Assets', $allowedModules))
                <!-- Assets Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-warning text-white p-2 rounded">
                            <i class="fas fa-box me-2"></i>Assets
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Assets">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_assets"
                                               name="modules[Assets][enabled]"
                                               value="1"
                                               {{ in_array('Assets', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_assets">
                                            Assets
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Assets', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Assets][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Assets']) && $employeePermissions['Assets']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Assets][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Assets']) && $employeePermissions['Assets']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Assets][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Assets']) && $employeePermissions['Assets']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Assets][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Assets']) && $employeePermissions['Assets']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Travel', $allowedModules))
                <!-- Travel Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-danger text-white p-2 rounded">
                            <i class="fas fa-plane me-2"></i>Travel
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Travel">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_travel"
                                               name="modules[Travel][enabled]"
                                               value="1"
                                               {{ in_array('Travel', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_travel">
                                            Travel
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Travel', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Travel][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Travel']) && $employeePermissions['Travel']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Travel][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Travel']) && $employeePermissions['Travel']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Travel][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Travel']) && $employeePermissions['Travel']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Travel][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Travel']) && $employeePermissions['Travel']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('Offboarding', $allowedModules))
                <!-- Offboarding Section -->
                <div class="col-md-6 mb-3">
                    <div class="category-card">
                        <h6 class="category-header bg-dark text-white p-2 rounded">
                            <i class="fas fa-user-minus me-2"></i>Offboarding
                        </h6>
                        <div class="category-modules">
                            <div class="module-item" data-module="Offboarding">
                                <div class="module-header">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox"
                                               type="checkbox"
                                               id="module_offboarding"
                                               name="modules[Offboarding][enabled]"
                                               value="1"
                                               {{ in_array('Offboarding', $employeeModules) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="module_offboarding">
                                            Offboarding
                                        </label>
                                    </div>
                                </div>
                                <div class="permissions-row" style="{{ in_array('Offboarding', $employeeModules) ? 'display: block;' : 'display: none;' }} margin-left:20px; margin-top:5px;">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Offboarding][can_view]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Offboarding']) && $employeePermissions['Offboarding']->can_view) ? 'checked' : '' }}>
                                                <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Offboarding][can_create]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Offboarding']) && $employeePermissions['Offboarding']->can_create) ? 'checked' : '' }}>
                                                <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Offboarding][can_edit]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Offboarding']) && $employeePermissions['Offboarding']->can_edit) ? 'checked' : '' }}>
                                                <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Offboarding][can_delete]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Offboarding']) && $employeePermissions['Offboarding']->can_delete) ? 'checked' : '' }}>
                                                <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-checkbox"
                                                       type="checkbox"
                                                       name="modules[Offboarding][can_approve]"
                                                       value="1"
                                                       {{ (isset($employeePermissions['Offboarding']) && $employeePermissions['Offboarding']->can_approve) ? 'checked' : '' }}>
                                                <label class="text-info"><i class="fas fa-check"></i> Approve</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button id="updateEmployeeBtn" class="btn btn-primary btn-lg" type="submit">
                                        <i class="fas fa-save me-2"></i>Update Employee
                                    </button>
                                    <a href="{{ route('employee.index') }}" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-info btn-lg ms-2">
                                        <i class="fas fa-eye me-2"></i>View Details
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
    
    .category-header {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .category-modules {
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .module-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .module-item:last-child {
        border-bottom: none;
    }
    
    .module-header {
        margin-bottom: 5px;
    }
    
    .permissions-row {
        background-color: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
        border-left: 3px solid #dee2e6;
    }
    
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .hierarchy-indicator {
        margin-left: 5px;
    }
    
    .module-item.hierarchy-module {
        background-color: #f8f9ff;
        border-left: 3px solid #0d6efd;
        padding-left: 10px;
    }
    
    .module-item.hierarchy-module .permissions-row {
        border-left-color: #0d6efd;
    }
    
    #moduleSearch {
        border-radius: 4px 0 0 4px;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .permission-checkbox {
        margin-right: 5px;
    }
    
    .form-check-label {
        font-size: 0.85rem;
    }
    
    .current-image-container {
        border: 2px dashed #dee2e6;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }
    
    /* Custom scrollbar */
    .category-modules::-webkit-scrollbar {
        width: 6px;
    }
    
    .category-modules::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .category-modules::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .category-modules::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
   
    
    // Department and Designation AJAX
    $('#department').on('change', function() {
        var departmentId = $(this).val();
        var currentDesignation = '{{ old("designation", $employee->designation) }}';
        
        $('#designation').html('<option value="">Loading...</option>');
        
        if (departmentId) {
            $.ajax({
                url: '{{ url("/get-designations") }}/' + departmentId,
                type: 'GET',
                success: function(res) {
                    $('#designation').empty().append('<option value="">Select Designation</option>');
                    $.each(res, function(key, designation) {
                        var selected = (designation.id == currentDesignation) ? 'selected' : '';
                        $('#designation').append('<option value="' + designation.id + '" ' + selected + '>' + designation.designation + '</option>');
                    });
                },
                error: function() {
                    $('#designation').html('<option value="">Error loading designations</option>');
                }
            });
        } else {
            $('#designation').html('<option value="">Select Designation</option>');
        }
    });

    // Trigger department change on page load to populate designations
    if ($('#department').val()) {
        $('#department').trigger('change');
    }

    // Training needed change handler
    $('#training_needed').on('change', function() {
        var trainingNeeded = $(this).val();
        var trainerField = $('#trainer_field');
        var trainerSelect = $('#trainer_id');
        
        if (trainingNeeded === 'Yes') {
            trainerField.show().addClass('show');
            trainerSelect.attr('required', true);
            showNotification('Please select a trainer for this employee', 'info');
        } else {
            trainerField.hide().removeClass('show');
            trainerSelect.attr('required', false);
            trainerSelect.val('');
        }
    });

    // Trigger training needed change on page load
    $('#training_needed').trigger('change');

    // Profile image preview
    $('#profile_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });

    // Module checkbox change handler - show/hide permissions
    $('.module-checkbox').on('change', function() {
        var permissionsRow = $(this).closest('.module-item').find('.permissions-row');
        if ($(this).is(':checked')) {
            permissionsRow.show();
            // Auto-check view permission when module is selected
            permissionsRow.find('input[name*="[can_view]"]').prop('checked', true);
        } else {
            permissionsRow.hide();
            // Uncheck all permissions when module is unchecked
            permissionsRow.find('.permission-checkbox').prop('checked', false);
        }
        updateModuleCounters();
    });

    let hierarchyModules = [];

$('#hierarchy').on('change', function () {

    const selectedOption = $(this).find('option:selected');
    let modules = selectedOption.data('modules');

    hierarchyModules = [];

    $('.module-item').removeClass('hierarchy-module');
    $('.hierarchy-indicator').hide();

    if (!modules) {
        updateModuleCounters();
        return;
    }

    try {
        const moduleObj = typeof modules === 'string'
            ? JSON.parse(modules)
            : modules;

        Object.entries(moduleObj).forEach(([moduleName, perms]) => {

            const moduleItem = $(`.module-item[data-module="${moduleName}"]`);
            if (!moduleItem.length) return;

            const checkbox = moduleItem.find('.module-checkbox');
            const permissionsRow = moduleItem.find('.permissions-row');

            hierarchyModules.push(moduleName);

            checkbox.prop('checked', true);
            moduleItem.addClass('hierarchy-module');
            moduleItem.find('.hierarchy-indicator').show();
            permissionsRow.show();

            permissionsRow.find('input[name*="[can_view]"]').prop('checked', !!perms.view);
            permissionsRow.find('input[name*="[can_create]"]').prop('checked', !!perms.create);
            permissionsRow.find('input[name*="[can_edit]"]').prop('checked', !!perms.edit);
            permissionsRow.find('input[name*="[can_delete]"]').prop('checked', !!perms.delete);
            permissionsRow.find('input[name*="[can_approve]"]').prop('checked', !!perms.approve);
            permissionsRow.find('input[name*="[can_download]"]').prop('checked', !!perms.download);
            permissionsRow.find('input[name*="[can_export]"]').prop('checked', !!perms.export);
        });

        updateModuleCounters();

    } catch (e) {
        console.error(e);
    }
});

    @if(old('hierarchy_id'))
        $('#hierarchy').trigger('change');
    @endif

    // Update module counters
    function updateModuleCounters() {
        var totalChecked = $('.module-checkbox:checked').length;
        var hierarchyCount = hierarchyModules.length;
        var manualCount = totalChecked - hierarchyCount;
        if (manualCount < 0) manualCount = 0;
        
        $('#hierarchyModuleCount').text(hierarchyCount + ' from hierarchy');
        $('#manualModuleCount').text(manualCount + ' manual');
        $('#totalModuleCount').text(totalChecked + ' total');
    }

    // Bulk actions
    $('#selectAllModulesBtn').on('click', function() {
        $('.module-checkbox').prop('checked', true).trigger('change');
        showNotification('All modules selected', 'info');
    });

    $('#selectAllPermissionsBtn').on('click', function() {
        $('.permission-checkbox').prop('checked', true);
        showNotification('All permissions selected for active modules', 'info');
    });

    // Search functionality
    $('#moduleSearch').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.module-item').each(function() {
            var moduleName = $(this).data('module').toLowerCase();
            if (moduleName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Show hierarchy modules
    $('#showHierarchyModulesBtn').on('click', function() {
        if (hierarchyModules.length > 0) {
            var hierarchyList = hierarchyModules.join(', ');
            showNotification('Hierarchy Modules: ' + hierarchyList, 'info');
        } else {
            showNotification('No hierarchy selected or no modules in selected hierarchy', 'warning');
        }
    });

    // Show notification function
    function showNotification(message, type) {
        $('.alert').not('.permanent').remove();
        
        var alertClass = 'alert-info';
        switch(type) {
            case 'success': alertClass = 'alert-success'; break;
            case 'error': alertClass = 'alert-danger'; break;
            case 'warning': alertClass = 'alert-warning'; break;
        }
        
        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');
        
        $('#employeeEditForm').prepend(notification);
        
        setTimeout(function() {
            notification.fadeOut();
        }, 5000);
    }

    // Form validation and submission
    // Form validation and submission - FIXED
const form = document.getElementById('employeeEditForm');
const updateBtn = document.getElementById('updateEmployeeBtn');

// Remove any existing submit handlers to prevent conflicts
const newForm = form.cloneNode(true);
form.parentNode.replaceChild(newForm, form);
const finalForm = document.getElementById('employeeEditForm');
const finalUpdateBtn = document.getElementById('updateEmployeeBtn');

finalForm.addEventListener('submit', function(event) {
    console.log('Form submit event triggered');
    
    if (!finalForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        finalForm.classList.add('was-validated');
        showNotification('Please fill all required fields correctly', 'error');
        return false;
    }

    const trainingNeeded = document.getElementById('training_needed').value;
    const trainerId = document.getElementById('trainer_id').value;
    
    if (trainingNeeded === 'Yes' && !trainerId) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('trainer_id').classList.add('is-invalid');
        showNotification('Please select a trainer when training is needed', 'error');
        return false;
    }

    if (finalUpdateBtn) {
        finalUpdateBtn.disabled = true;
        finalUpdateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
    }
    
    return true;
}, false);

    // Initialize
    updateModuleCounters();
    
    // Show permissions for already checked modules
    $('.module-checkbox:checked').each(function() {
        $(this).closest('.module-item').find('.permissions-row').show();
    });
});

let lastDepartmentId = document.getElementById('department').value;

document.getElementById('department').addEventListener('change', function () {
    let departmentId = this.value;
    if(departmentId == lastDepartmentId) return; // avoid reload if same department
    lastDepartmentId = departmentId;

    let branchId = document.getElementById('branch_id').value;
    let managerDropdown = document.getElementById('manager_id');
    let currentManagerId = managerDropdown.dataset.currentManager;

    if (!departmentId) {
        managerDropdown.innerHTML = '<option value="">Select Manager (Optional)</option>';
        return;
    }

    managerDropdown.innerHTML = '<option value="">Loading...</option>';

    fetch(`/get-employees-by-department/${departmentId}?branch_id=${branchId}`)
        .then(res => res.json())
        .then(data => {
            managerDropdown.innerHTML = '<option value="">Select Manager (Optional)</option>';

            let currentManagerExists = false;

            data.forEach(emp => {
                let selected = (emp.id == currentManagerId) ? 'selected' : '';
                if(emp.id == currentManagerId) currentManagerExists = true;

                managerDropdown.innerHTML += `
                    <option value="${emp.id}" data-employee-id="${emp.employeeid}" data-designation="${emp.designation}" ${selected}>
                        ${emp.firstname} ${emp.lastname}
                    </option>`;
            });

            // If current manager is not in this department, still show them at the top
            if(currentManagerId && !currentManagerExists) {
                fetch(`/get-employee/${currentManagerId}`) // create route to get single employee
                    .then(res => res.json())
                    .then(emp => {
                        managerDropdown.innerHTML = `
                            <option value="${emp.id}" selected>${emp.firstname} ${emp.lastname}</option>` 
                            + managerDropdown.innerHTML;
                    });
            }
        })
        .catch(err => {
            managerDropdown.innerHTML = '<option value="">Error loading employees</option>';
        });
});

</script>
<script>
const routes = {
        getManagersByDepartment: "{{ route('employee.getManagersByDepartment', ['departmentId' => ':departmentId']) }}",
        getTeamLeadsByDepartment: "{{ route('employee.getTeamLeadsByDepartment', ['departmentId' => ':departmentId']) }}"
    };
    document.getElementById('moduleToggle').addEventListener('change', function() {
    const permissionsDiv = document.getElementById('modulePermissions');
    if (this.value === 'yes') {
        permissionsDiv.style.display = 'block';
    } else {
        permissionsDiv.style.display = 'none';
        permissionsDiv.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
    }
});

// Load Team Leads on page load
document.addEventListener('DOMContentLoaded', function() {
    let departmentId = document.getElementById('department').value;
    let branchId = document.getElementById('branch_id').value;
    let teamLeadDropdown = document.getElementById('team_lead_id');
    let currentTeamLeadId = teamLeadDropdown.dataset.currentTeamLead;

    if (departmentId) {
        let url = routes.getTeamLeadsByDepartment.replace(':departmentId', departmentId) + `?branch_id=${branchId}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                teamLeadDropdown.innerHTML = '<option value="">Select Team Lead (Optional)</option>';
                data.forEach(emp => {
                    let selected = (emp.id == currentTeamLeadId) ? 'selected' : '';
                    teamLeadDropdown.innerHTML += `
                        <option value="${emp.id}" ${selected}>
                            ${emp.firstname} ${emp.lastname} (${emp.designation_name})
                        </option>`;
                });
            })
            .catch(err => {
                console.error('Error loading team leads:', err);
                teamLeadDropdown.innerHTML = '<option value="">Error loading team leads</option>';
            });
    }
});

// Department change listener
document.getElementById('department').addEventListener('change', function () {
    let departmentId = this.value;
    let branchId = document.getElementById('branch_id').value;
    let managerDropdown = document.getElementById('manager_id');
    let teamLeadDropdown = document.getElementById('team_lead_id');
    let currentManagerId = managerDropdown.dataset.currentManager;
    let currentTeamLeadId = teamLeadDropdown.dataset.currentTeamLead;

    if (!departmentId) {
        managerDropdown.innerHTML = '<option value="">Select Reporting Manager (Optional)</option>';
        teamLeadDropdown.innerHTML = '<option value="">Select Team Lead (Optional)</option>';
        return;
    }

    // Fetch Managers
    let managerUrl = routes.getManagersByDepartment.replace(':departmentId', departmentId) + `?branch_id=${branchId}`;
    fetch(managerUrl)
        .then(res => res.json())
        .then(data => {
            managerDropdown.innerHTML = '<option value="">Select Reporting Manager (Optional)</option>';
            data.forEach(emp => {
                let selected = (emp.id == currentManagerId) ? 'selected' : '';
                managerDropdown.innerHTML += `
                    <option value="${emp.id}" ${selected}>
                        ${emp.firstname} ${emp.lastname} (${emp.designation_name})
                    </option>`;
            });
        })
        .catch(err => {
            managerDropdown.innerHTML = '<option value="">Error loading managers</option>';
        });

    // Fetch Team Leads
    let teamLeadUrl = routes.getTeamLeadsByDepartment.replace(':departmentId', departmentId) + `?branch_id=${branchId}`;
    fetch(teamLeadUrl)
        .then(res => res.json())
        .then(data => {
            teamLeadDropdown.innerHTML = '<option value="">Select Team Lead (Optional)</option>';
            data.forEach(emp => {
                let selected = (emp.id == currentTeamLeadId) ? 'selected' : '';
                teamLeadDropdown.innerHTML += `
                    <option value="${emp.id}" ${selected}>
                        ${emp.firstname} ${emp.lastname} (${emp.designation_name})
                    </option>`;
            });
        })
        .catch(err => {
            teamLeadDropdown.innerHTML = '<option value="">Error loading team leads</option>';
        });
});


</script>
<script>
    // Salary Calculation Functions
    document.addEventListener('DOMContentLoaded', function() {
        const calculateBtn = document.getElementById('calculateSalaryBtn');
        const resetBtn = document.getElementById('resetSalaryBtn');
        const salaryInputs = ['basic', 'da', 'hra', 'conveyance', 'allowance', 'medical', 'pf', 'esi', 'tds', 'tax', 'welfare'];

        // 👇 ADD THIS — master-la irundhu PF/ESI fresh-a load pannu
        loadSalaryMasterConfig();
        
        // Calculate salary when button is clicked
        calculateBtn.addEventListener('click', calculateSalary);
        
        // Auto-calculate when basic salary changes significantly
        document.getElementById('basic').addEventListener('blur', function() {
            if (this.value > 0) {
                calculateSalary();
            }
        });
        
        // Reset salary form
        resetBtn.addEventListener('click', function() {
            salaryInputs.forEach(input => {
                if (input !== 'da' && input !== 'hra' && input !== 'pf' && input !== 'esi') {
                    document.getElementById(input).value = '';
                }
            });
            document.getElementById('net_salary').value = '';
            document.getElementById('salaryCalculationResults').style.display = 'none';
            document.getElementById('salarySummary').textContent = 'Not calculated';
            document.getElementById('salarySummary').className = 'badge bg-light text-dark';
        });
        
        // Auto-calculate TDS when basic salary is entered
        document.getElementById('basic').addEventListener('input', function() {
            const basicSalary = parseFloat(this.value) || 0;
            if (basicSalary > 0) {
                fetchTdsPercentage(basicSalary);
            }
        });
    });
    
    function calculateSalary() {
        const basicSalary = parseFloat(document.getElementById('basic').value) || 0;
        
        if (basicSalary <= 0) {
            showNotification('Please enter basic salary first', 'error');
            return;
        }
        
        // Show loading state
        const calculateBtn = document.getElementById('calculateSalaryBtn');
        const originalText = calculateBtn.innerHTML;
        calculateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calculating...';
        calculateBtn.disabled = true;
        
        // Collect form data
        const formData = new FormData();
        formData.append('basic', basicSalary);
        formData.append('da_percentage', document.getElementById('da').value || 0);
        formData.append('hra_percentage', document.getElementById('hra').value || 0);
        formData.append('pf_percentage', document.getElementById('pf').value || 0);
        formData.append('esi_percentage', document.getElementById('esi').value || 0);
        formData.append('conveyance', document.getElementById('conveyance').value || 0);
        formData.append('allowance', document.getElementById('allowance').value || 0);
        formData.append('medical', document.getElementById('medical').value || 0);
        formData.append('tds', document.getElementById('tds').value || 0);
        formData.append('tax', document.getElementById('tax').value || 0);
        formData.append('welfare', document.getElementById('welfare').value || 0);
        
        // AJAX request to calculate salary
        fetch('{{ route("employee.calculateSalary") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSalaryDisplay(data.data);
                showNotification('Salary calculated successfully!', 'success');
            } else {
                showNotification(data.message || 'Error calculating salary', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error calculating salary. Please try again.', 'error');
        })
        .finally(() => {
            calculateBtn.innerHTML = originalText;
            calculateBtn.disabled = false;
        });
    }
    
    function updateSalaryDisplay(salaryData) {
        // Update display values
        document.getElementById('display_basic').textContent = salaryData.basic || document.getElementById('basic').value || '0.00';
        document.getElementById('display_da').textContent = salaryData.da_amount.toFixed(2);
        document.getElementById('display_hra').textContent = salaryData.hra_amount.toFixed(2);
        document.getElementById('display_conveyance').textContent = (document.getElementById('conveyance').value || 0);
        document.getElementById('display_allowance').textContent = (document.getElementById('allowance').value || 0);
        document.getElementById('display_medical').textContent = (document.getElementById('medical').value || 0);
        document.getElementById('display_pf').textContent = salaryData.pf_amount.toFixed(2);
        document.getElementById('display_esi').textContent = salaryData.esi_amount.toFixed(2);
        document.getElementById('display_tds').textContent = (document.getElementById('tds').value || 0);
        document.getElementById('display_tax').textContent = (document.getElementById('tax').value || 0);
        document.getElementById('display_welfare').textContent = (document.getElementById('welfare').value || 0);
        
        document.getElementById('display_total_earnings').textContent = salaryData.total_earnings.toFixed(2);
        document.getElementById('display_total_deductions').textContent = salaryData.total_deductions.toFixed(2);
        document.getElementById('display_net_salary').textContent = salaryData.net_salary.toFixed(2);
        
        // Update net salary input
        document.getElementById('net_salary').value = salaryData.net_salary.toFixed(2);
        
        // Show results section
        document.getElementById('salaryCalculationResults').style.display = 'block';
        
        // Update summary badge
        const summaryBadge = document.getElementById('salarySummary');
        summaryBadge.textContent = '₹' + salaryData.net_salary.toFixed(2);
        summaryBadge.className = 'badge bg-success';
    }
    
    function fetchTdsPercentage(basicSalary) {
        fetch(`{{ route("employee.getTdsPercentage") }}?basic_salary=${basicSalary}`)
        .then(response => response.json())
        .then(data => {
            if (data.tds_percentage > 0) {
                // You can auto-fill TDS based on percentage if needed
                // const tdsAmount = (data.tds_percentage / 100) * basicSalary;
                // document.getElementById('tds').value = tdsAmount.toFixed(2);
                
                showNotification(`TDS applicable: ${data.tds_percentage}% for this salary range`, 'info');
            }
        })
        .catch(error => {
            console.error('Error fetching TDS percentage:', error);
        });
    }
   let grossToBasicPct = 50;   // global default

function loadSalaryMasterConfig() {
    fetch('{{ route("salary-master.get-config") }}')
        .then(response => response.json())
        .then(data => {
            grossToBasicPct = parseFloat(data.gross_to_basic_percentage) || 50;

            // 👇 NEW: gross empty-a iruntha, basic-la irundhu reverse calc panni fill pannu
            const grossEl = document.getElementById('gross_salary');
            const basicVal = parseFloat(document.getElementById('basic').value) || 0;
            if (grossEl && (!grossEl.value || parseFloat(grossEl.value) === 0) && basicVal > 0 && grossToBasicPct > 0) {
                grossEl.value = ((basicVal * 100) / grossToBasicPct).toFixed(2);
            }

            // PF/ESI default (empty-na mattum)
            const pfEl = document.getElementById('pf');
            const esiEl = document.getElementById('esi');
            if (!pfEl.value || parseFloat(pfEl.value) === 0) pfEl.value = data.pf_percentage;
            if (!esiEl.value || parseFloat(esiEl.value) === 0) esiEl.value = data.esi_percentage;

            const basic = parseFloat(document.getElementById('basic').value) || 0;
            if (basic > 0) {
                clearTimeout(window.salaryCalcTimeout);
                window.salaryCalcTimeout = setTimeout(calculateSalary, 300);
            }
        })
        .catch(error => console.error('Error loading salary master config:', error));
}

// Gross Salary → Basic auto-calc
const grossInput = document.getElementById('gross_salary');
if (grossInput) {
    grossInput.addEventListener('input', function () {
        const gross = parseFloat(this.value) || 0;
        if (gross > 0) {
            document.getElementById('basic').value = ((gross * grossToBasicPct) / 100).toFixed(2);
            clearTimeout(window.salaryCalcTimeout);
            window.salaryCalcTimeout = setTimeout(calculateSalary, 300);
        }
    });
}
    function showNotification(message, type) {
        // Remove existing notifications
        $('.alert').not('.permanent').remove();
        
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('#employeeForm').prepend(notification);
        
        setTimeout(() => {
            notification.fadeOut();
        }, 5000);
    }
    
    // Auto-calculate when percentage fields change
    ['da', 'hra', 'pf', 'esi'].forEach(field => {
        document.getElementById(field).addEventListener('input', function() {
            const basicSalary = parseFloat(document.getElementById('basic').value) || 0;
            if (basicSalary > 0) {
                // Debounce the calculation
                clearTimeout(window.salaryCalcTimeout);
                window.salaryCalcTimeout = setTimeout(calculateSalary, 1000);
            }
        });
    });
    </script>
    
<script>
document.getElementById('add_family_member').onclick = function() {
    const container = document.getElementById('family_members_container');
    const index = container.children.length;

    const newMember = `
        <div class="family-member mb-3">
            <h5>Family Member
                <a href="javascript:void(0);" class="text-danger remove-family-member"><i class="fa fa-trash"></i></a>
            </h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="family_members[${index}][name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Relationship <span class="text-danger">*</span></label>
                    <input type="text" name="family_members[${index}][relationship]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Phone</label>
                    <input type="text" name="family_members[${index}][phone]" class="form-control">
                </div>
                
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newMember);
    attachRemoveHandlers();
};

function attachRemoveHandlers() {
    document.querySelectorAll('.remove-family-member').forEach(button => {
        button.onclick = function() {
            this.closest('.family-member').remove();
        };
    });
}
attachRemoveHandlers();
</script>
<script>
document.getElementById('add_education').onclick = function() {
    const container = document.getElementById('education_section_container');
    const index = container.children.length;

    const newEducation = `
        <div class="card mb-3 education-card">
            <div class="card-body">
                <h5>New Education
                    <a href="javascript:void(0);" class="text-danger remove-education"><i class="fa fa-trash"></i></a>
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Institution <span class="text-danger">*</span></label>
                        <input type="text" name="education[${index}][institution]" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Subject <span class="text-danger">*</span></label>
                        <input type="text" name="education[${index}][subject]" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Starting Date</label>
                        <input type="date" name="education[${index}][start_date]" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Completion Date</label>
                        <input type="date" name="education[${index}][end_date]" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Degree</label>
                        <input type="text" name="education[${index}][degree]" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Grade</label>
                        <input type="text" name="education[${index}][grade]" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newEducation);
    attachRemoveHandlers();
};

function attachRemoveHandlers() {
    const deleteButtons = document.querySelectorAll('.remove-education');
    deleteButtons.forEach(button => {
        button.onclick = function() {
            this.closest('.education-card').remove();
        };
    });
}

attachRemoveHandlers();
</script>
<script>
document.getElementById('add_experience').onclick = function() {
const container = document.getElementById('experience_section_container');
const index = container.children.length;

const newExperience = `
    <div class="card mb-3 experience-card">
        <div class="card-body">
            <h5>New Experience
                <a href="javascript:void(0);" class="text-danger remove-experience"><i class="fa fa-trash"></i></a>
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="experience[${index}][company_name]" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Location <span class="text-danger">*</span></label>
                    <input type="text" name="experience[${index}][location]" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Job Position <span class="text-danger">*</span></label>
                    <input type="text" name="experience[${index}][job_position]" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Period From <span class="text-danger">*</span></label>
                    <input type="date" name="experience[${index}][period_from]" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Period To <span class="text-danger">*</span></label>
                    <input type="date" name="experience[${index}][period_to]" class="form-control" required>
                </div>
            </div>
        </div>
    </div>
`;
container.insertAdjacentHTML('beforeend', newExperience);
attachRemoveHandlers();
};

function attachRemoveHandlers() {
document.querySelectorAll('.remove-experience').forEach(btn => {
    btn.onclick = function() {
        this.closest('.experience-card').remove();
    };
});
}
attachRemoveHandlers();
// Module toggle for edit page
document.getElementById('moduleToggle').addEventListener('change', function() {
    const permissionsDiv = document.getElementById('modulePermissions');
    if (this.value === 'yes') {
        permissionsDiv.style.display = 'block';
    } else {
        permissionsDiv.style.display = 'none';
        permissionsDiv.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
    }
});

// Auto-show/load permissions on page load with hierarchy selection change handler
$(document).ready(function() {
    let hierarchyModules = [];

    function updateModuleCounters() {
        var totalChecked = $('.module-checkbox:checked').length;
        var hierarchyCount = hierarchyModules.length;
        var manualCount = totalChecked - hierarchyCount;
        if (manualCount < 0) manualCount = 0;
        
        $('#hierarchyModuleCount').text(hierarchyCount + ' from hierarchy');
        $('#manualModuleCount').text(manualCount + ' manual');
        $('#totalModuleCount').text(totalChecked + ' total');
    }

    // Expose updateCounters so other manual events can use it
    window.updateCounters = updateModuleCounters;

    $('#hierarchy').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var modules = selectedOption.data('modules');
        
        hierarchyModules = [];
        $('.module-item').removeClass('hierarchy-module');
        $('.module-checkbox').prop('checked', false);
        $('.permission-checkbox').prop('checked', false).closest('.permissions-row').hide();

        if (modules) {
            try {
                var moduleObj = typeof modules === 'string' ? JSON.parse(modules) : modules;
                if (typeof moduleObj === 'object') {
                    hierarchyModules = Object.keys(moduleObj);

                    Object.entries(moduleObj).forEach(([moduleName, perms]) => {
                        var moduleItem = $(`.module-item[data-module="${moduleName}"]`);
                        if (moduleItem.length) {
                            var checkbox = moduleItem.find('.module-checkbox');
                            checkbox.prop('checked', true);
                            moduleItem.addClass('hierarchy-module');
                            
                            var permissionsRow = moduleItem.find('.permissions-row');
                            permissionsRow.show();
                            
                            if (perms.can_view || perms.view) permissionsRow.find('input[name*="[can_view]"]').prop('checked', true);
                            if (perms.can_create || perms.create) permissionsRow.find('input[name*="[can_create]"]').prop('checked', true);
                            if (perms.can_edit || perms.edit) permissionsRow.find('input[name*="[can_edit]"]').prop('checked', true);
                            if (perms.can_delete || perms.delete) permissionsRow.find('input[name*="[can_delete]"]').prop('checked', true);
                            if (perms.can_approve || perms.approve) permissionsRow.find('input[name*="[can_approve]"]').prop('checked', true);
                            if (perms.can_download || perms.download) permissionsRow.find('input[name*="[can_download]"]').prop('checked', true);
                            if (perms.can_export || perms.export) permissionsRow.find('input[name*="[can_export]"]').prop('checked', true);
                        }
                    });
                }
            } catch (e) {
                console.error('Error parsing modules JSON:', e);
            }
        }
        updateModuleCounters();
    });

    // Handle manual module check changes
    $(document).on('change', '.module-checkbox', function() {
        var permissionsRow = $(this).closest('.module-item').find('.permissions-row');
        if ($(this).is(':checked')) {
            permissionsRow.show();
            permissionsRow.find('input[name*="[can_view]"]').prop('checked', true);
        } else {
            permissionsRow.hide();
            permissionsRow.find('.permission-checkbox').prop('checked', false);
        }
        updateModuleCounters();
    });

    const hasModules = {{ count($employeeModules) > 0 ? 'true' : 'false' }};
    const moduleToggle = document.getElementById('moduleToggle');
    const modulePermissions = document.getElementById('modulePermissions');
    
    if (hasModules && moduleToggle && modulePermissions) {
        moduleToggle.value = 'yes';
        modulePermissions.style.display = 'block';
    }

    // Load initial hierarchy indicator classes without clearing existing checks
    const initialHierarchy = $('#hierarchy').find('option:selected');
    const initialModules = initialHierarchy.data('modules');
    if (initialModules) {
        try {
            var moduleObj = typeof initialModules === 'string' ? JSON.parse(initialModules) : initialModules;
            if (typeof moduleObj === 'object') {
                hierarchyModules = Object.keys(moduleObj);
                hierarchyModules.forEach(moduleName => {
                    $(`.module-item[data-module="${moduleName}"]`).addClass('hierarchy-module');
                });
            }
        } catch (e) {}
    }

    // Load actual employee custom permissions on load
    const employeePermissions = @json($employeePermissions);
    Object.entries(employeePermissions).forEach(([moduleName, permissions]) => {
        const moduleItem = document.querySelector(`.module-item[data-module="${moduleName}"]`);
        if (!moduleItem) return;

        const moduleCheckbox = moduleItem.querySelector('.module-checkbox');
        if (moduleCheckbox) {
            moduleCheckbox.checked = true;
            const permissionsRow = moduleItem.querySelector('.permissions-row');
            if (permissionsRow) {
                permissionsRow.style.display = 'block';
                
                if (permissions.can_view) {
                    const cb = permissionsRow.querySelector('input[name*="[can_view]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_create) {
                    const cb = permissionsRow.querySelector('input[name*="[can_create]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_edit) {
                    const cb = permissionsRow.querySelector('input[name*="[can_edit]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_delete) {
                    const cb = permissionsRow.querySelector('input[name*="[can_delete]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_approve) {
                    const cb = permissionsRow.querySelector('input[name*="[can_approve]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_download) {
                    const cb = permissionsRow.querySelector('input[name*="[can_download]"]');
                    if (cb) cb.checked = true;
                }
                if (permissions.can_export) {
                    const cb = permissionsRow.querySelector('input[name*="[can_export]"]');
                    if (cb) cb.checked = true;
                }
            }
        }
    });

    updateModuleCounters();
});

</script>
<script>
// UPDATED VALIDATION - Only validates main employee fields
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFix);
    } else {
        initFix();
    }
    
    function initFix() {
        const form = document.getElementById('employeeEditForm');
        const btn = document.getElementById('updateEmployeeBtn');
        
        if (!form || !btn) return;
        
        // Replace button to remove conflicts
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        const finalBtn = document.getElementById('updateEmployeeBtn');
        
        finalBtn.onclick = function(e) {
            e.preventDefault();
            
            // ONLY validate these main fields (not tab fields)
            const mainFields = ['employeeid', 'firstname', 'lastname', 'email', 'joiningdate', 'phone', 'department', 'designation', 'branch_id', 'training_needed', 'employee_type'];
            let missingFields = [];
            
            mainFields.forEach(function(fieldName) {
                const field = form.querySelector('[name="' + fieldName + '"]');
                if (field && (!field.value || field.value.trim() === '')) {
                    field.style.borderColor = 'red';
                    field.style.backgroundColor = '#fff0f0';
                    missingFields.push(fieldName);
                } else if (field) {
                    field.style.borderColor = '';
                    field.style.backgroundColor = '';
                }
            });
            
            if (missingFields.length > 0) {
                alert('Please fill these required fields:\n- ' + missingFields.join('\n- '));
                return false;
            }
            
            // Check trainer requirement
            const trainingNeeded = document.getElementById('training_needed');
            const trainerId = document.getElementById('trainer_id');
            if (trainingNeeded && trainingNeeded.value === 'Yes' && (!trainerId || !trainerId.value)) {
                alert('Please select a trainer when training is needed');
                if (trainerId) trainerId.style.borderColor = 'red';
                return false;
            }
            
            // Submit form
            finalBtn.disabled = true;
            finalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            form.submit();
        };
    }
})();
</script>
<script>
$(document).ready(function() {

    // ==========================================================================
    // 1. AUTO-DISABLE TEAM LEAD DROPDOWN IF HIERARCHY IS "TEAM LEAD" (NEW)
    // ==========================================================================
    function checkHierarchy() {
        const selectedHierarchyText = $('#hierarchy option:selected').text().trim().toLowerCase();
        const teamLeadSelect = $('#team_lead_id');

        if (selectedHierarchyText === 'team lead') {
            teamLeadSelect.val(''); // Reset any selected Team Lead
            teamLeadSelect.prop('disabled', true); // Disable dropdown
            teamLeadSelect.css('background-color', '#e9ecef'); // Visual disabled indicator
        } else {
            teamLeadSelect.prop('disabled', false); // Re-enable dropdown
            teamLeadSelect.css('background-color', '#ffffff'); // Visual active indicator
        }
    }

    // Bind change listener to the Hierarchy dropdown
    $('#hierarchy').on('change', function() {
        checkHierarchy();
    });

    // Run once on page load (important for Edit Mode initial render)
    checkHierarchy();


    // ==========================================================================
    // 2. DYNAMIC PERMISSION MODULES SLIDE TOGGLE
    // ==========================================================================
    function checkModulePermissionToggle() {
        const toggleValue = $('#moduleToggle').val();
        const container = $('#permission_modules_container');

        if (toggleValue === 'yes') {
            container.slideDown(350); 
        } else {
            container.slideUp(350);   
        }
    }

    $('#moduleToggle').on('change', function() {
        checkModulePermissionToggle();
    });

    checkModulePermissionToggle();


    // ==========================================================================
    // 3. FIXED DYNAMIC CASCADING DESIGNATION DROPDOWN (AJAX PATH RESOLVED)
    // ==========================================================================
    $('#department').on('change', function() {
        const departmentId = $(this).val();
        const designationSelect = $('#designation');

        // Clear previous options
        designationSelect.empty();
        designationSelect.append('<option value="">Select Designation</option>');

        if (departmentId) {
            // Display loading placeholder
            designationSelect.append('<option value="" disabled>Loading designations...</option>');

            $.ajax({
                // Using absolute Blade url helper to guarantee 100% correct URL mapping
                url: "{{ url('employee/get-designations') }}/" + departmentId, 
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    designationSelect.find('option[disabled]').remove();

                    if (data && data.length > 0) {
                        data.forEach(function(designation) {
                            designationSelect.append(
                                `<option value="${designation.id}">${designation.designation}</option>`
                            );
                        });
                    } else {
                        // Fallback: If your route doesn't have the 'employee/' prefix, try the root level
                        tryGetRootDesignations(departmentId, designationSelect);
                    }
                },
                error: function(xhr) {
                    // Fallback search to root level get-designations
                    tryGetRootDesignations(departmentId, designationSelect);
                }
            });
        }
    });

    // Helper fallback method in case route maps to root level instead of prefixed level
    function tryGetRootDesignations(departmentId, designationSelect) {
        $.ajax({
            url: "{{ url('get-designations') }}/" + departmentId, 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                designationSelect.find('option[disabled]').remove();
                designationSelect.empty();
                designationSelect.append('<option value="">Select Designation</option>');

                if (data && data.length > 0) {
                    data.forEach(function(designation) {
                        designationSelect.append(
                            `<option value="${designation.id}">${designation.designation}</option>`
                        );
                    });
                } else {
                    designationSelect.append('<option value="" disabled>No designations found</option>');
                }
            },
            error: function(xhr) {
                designationSelect.find('option[disabled]').remove();
                console.error('AJAX Error loading designations: ', xhr.responseText);
            }
        });
    }

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('moduleToggle');
    const outer  = document.getElementById('permission_modules_container');
    const inner  = document.getElementById('modulePermissions');

    function syncModuleVisibility() {
        const show = toggle.value === 'yes';
        if (outer) outer.style.display = show ? 'block' : 'none';
        if (inner) inner.style.display = show ? 'block' : 'none';
        if (!show && inner) {
            inner.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
        }
    }

    if (toggle) {
        toggle.addEventListener('change', syncModuleVisibility);
        syncModuleVisibility();   // page load la initial state correct-a set pannum
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== SELECT ALL MODULES =====
    document.getElementById('selectAllModulesBtn')?.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelectorAll('.module-checkbox').forEach(function (cb) {
            cb.checked = true;
            const row = cb.closest('.module-item').querySelector('.permissions-row');
            if (row) {
                row.style.display = 'block';
                const view = row.querySelector('input[name*="[can_view]"]');
                if (view) view.checked = true;   // default view on
            }
        });
        updateCounters();
    });

    // ===== SELECT ALL PERMISSIONS =====
    document.getElementById('selectAllPermissionsBtn')?.addEventListener('click', function (e) {
        e.preventDefault();
        // only for modules that are enabled (checked)
        document.querySelectorAll('.module-checkbox:checked').forEach(function (cb) {
            const row = cb.closest('.module-item').querySelector('.permissions-row');
            if (row) {
                row.style.display = 'block';
                row.querySelectorAll('.permission-checkbox').forEach(p => p.checked = true);
            }
        });
        updateCounters();
    });

    // ===== SEARCH (re-bind, clone-safe) =====
    document.getElementById('moduleSearch')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.module-item').forEach(function (item) {
            const name = (item.getAttribute('data-module') || '').toLowerCase();
            item.style.display = name.includes(term) ? '' : 'none';
        });
    });

    // ===== module checkbox toggle (show/hide perms) =====
    document.querySelectorAll('.module-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const row = this.closest('.module-item').querySelector('.permissions-row');
            if (!row) return;
            if (this.checked) {
                row.style.display = 'block';
                const view = row.querySelector('input[name*="[can_view]"]');
                if (view) view.checked = true;
            } else {
                row.style.display = 'none';
                row.querySelectorAll('.permission-checkbox').forEach(p => p.checked = false);
            }
            updateCounters();
        });
    });

    function updateCounters() {
        const total = document.querySelectorAll('.module-checkbox:checked').length;
        const h = document.getElementById('hierarchyModuleCount');
        const m = document.getElementById('manualModuleCount');
        const t = document.getElementById('totalModuleCount');
        if (t) t.textContent = total + ' total';
        if (m) m.textContent = total + ' manual';
        if (h) h.textContent = '0 from hierarchy';
    }
});
</script>
@endsection
