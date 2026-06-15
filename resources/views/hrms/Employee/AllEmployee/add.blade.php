@php
    // Since only one company exists, just fetch first row
    $company = DB::table('subcompany')->first();

    $planId = $company->plan_id ?? null;

    $allowedModules = [];

    if ($planId) {
        $allowedModules = DB::table('plan_modules')
            ->join('modules', 'plan_modules.module_id', '=', 'modules.id')
            ->where('plan_modules.plan_id', $planId)
            ->pluck('modules.name')
            ->toArray();
    }
@endphp

@extends('layouts.index')
@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Employee</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('employee.index') }}" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add</li>
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
                    <h4 class="card-header">Add Employee</h4>
                    <div class="card-body">
                        <form id="employeeForm" method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" class="needs-validation">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="employeeid">Employee ID <span class="text-muted"></span></label>
                                    <input type="text" class="form-control" name="employeeid" id="employeeid" placeholder="Enter Employee ID (or leave blank for auto)" value="{{ old('employeeid') }}">
                                    <small class="text-muted">Leave blank to auto-generate Employee ID.</small>
                                    @error('employeeid')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- First Name and Last Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="firstname">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control validate-required" name="firstname" id="firstname" placeholder="Enter First Name" value="{{ old('firstname') }}" required>
                                    <div class="invalid-feedback">First name is required and must be at least 3 characters long.</div>
                                    @error('firstname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control validate-required" name="lastname" id="lastname" placeholder="Enter Last Name" value="{{ old('lastname') }}" required>
                                    <div class="invalid-feedback">Last name is required.</div>
                                    @error('lastname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                              
                                <div class="col-md-6 mb-3">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control validate-required" name="email" id="email" placeholder="Enter Email" value="{{ old('email') }}" required>
                                    <div class="invalid-feedback" id="emailError">Valid email is required.</div>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Joining Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="joining_date">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control validate-required" name="joiningdate" id="joining_date" value="{{ old('joiningdate') }}" required>
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
                                           value="{{ old('phone') }}"
                                           required>
                                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                               

                                <!-- Department Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="department">Department <span class="text-danger">*</span></label>
                                    <select id="department" name="department" class="form-control validate-required" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department') == $department->id ? 'selected' : '' }}>
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
                                    <select id="designation" name="designation" class="form-control validate-required" required>
                                        <option value="">Select Designation</option>
                                        @if(old('department'))
                                            <!-- This will be populated via AJAX -->
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Please select a designation.</div>
                                    @error('designation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Branch -->
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select id="branch_id" name="branch_id" class="form-control validate-required" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a branch.</div>
                                    @error('branch_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Reporting Manager -->
                                <div class="col-md-6 mb-3">
                                    <label for="manager_id">Reporting Manager</label>
                                    <select id="manager_id" name="manager_id" class="form-control">
                                        <option value="">Select Reporting Manager (Optional)</option>
                                    </select>
                                    <small class="text-muted">Managers from the selected department will appear here</small>
                                    @error('manager_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Team Lead -->
                                <div class="col-md-6 mb-3">
                                    <label for="team_lead_id">Team Lead</label>
                                    <select id="team_lead_id" name="team_lead_id" class="form-control">
                                        <option value="">Select Team Lead (Optional)</option>
                                    </select>
                                    <small class="text-muted">Team Leads from the selected department will appear here</small>
                                    @error('team_lead_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Training Needed -->
                                <div class="col-md-6 mb-3">
                                    <label for="training_needed">Training Needed <span class="text-danger">*</span></label>
                                    <select id="training_needed" name="training_needed" class="form-control validate-required" required>
                                        <option value="">Select Training Need</option>
                                        <option value="Yes" {{ old('training_needed') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('training_needed') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select if training is needed.</div>
                                    @error('training_needed')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Trainer Selection (Initially Hidden) -->
                                <div class="col-md-6 mb-3" id="trainer_field" style="display: none;">
                                    <label for="trainer_id">Select Trainer <span class="text-danger">*</span></label>
                                    <select id="trainer_id" name="trainer_id" class="form-control">
                                        <option value="">Select Trainer</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                                {{ $trainer->first_name }} {{ $trainer->last_name }} - {{ $trainer->role }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a trainer when training is needed.</div>
                                    @error('trainer_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Document Files -->
                                <div class="col-md-6 mb-3">
                                    <label for="document_files">Employee Documents (Multiple Files)</label>
                                    <input type="file" class="form-control" name="document_files[]" id="document_files"
                                            multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="text-muted">
                                        Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max 5MB per file)
                                    </small>
                                    <div class="invalid-feedback">Please upload valid document files.</div>
                                    @error('document_files')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div id="file-preview" class="mt-2"></div>
                                </div>

                                <!-- Document Reminder (days) -->
                                <div class="col-md-6 mb-3">
                                    <label for="document_reminder_days">Document reminder (days)</label>
                                    <input type="number" min="0" name="document_reminder_days" id="document_reminder_days" class="form-control" value="{{ old('document_reminder_days') }}">
                                    <small class="text-muted">Enter number of days after which HR should be reminded to verify uploaded documents. Leave blank or 0 to disable.</small>
                                    @error('document_reminder_days')
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
                                                    {{ old('hierarchy_id') == $hierarchy->id ? 'selected' : '' }}>
                                                {{ $hierarchy->hierarchy_level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a hierarchy level.</div>
                                    @error('hierarchy_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>Employee Type</label>
                                    <select name="employee_type" class="form-control validate-required" required>
                                        <option value="">Select Type</option>
                                        <option value="Full Time" {{ (isset($employee) && $employee->employee_type == 'Full Time') ? 'selected' : '' }}>Full Time</option>
                                        <option value="Part Time" {{ (isset($employee) && $employee->employee_type == 'Part Time') ? 'selected' : '' }}>Part Time</option>
                                        <option value="Internship" {{ (isset($employee) && $employee->employee_type == 'Internship') ? 'selected' : '' }}>Internship</option>
                                    </select>
                                </div>
                                
                                <!-- Profile Image -->
                                <div class="col-md-6 mb-3">
                                    <label for="profile_image">Profile Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control validate-required" name="profile_image" id="profile_image" accept=".jpg, .jpeg, .png" required>
                                    <div class="invalid-feedback">Valid image is required. Allowed formats: .jpg, .jpeg, .png.</div>
                                    <div class="mt-2">
                                        <img id="image_preview" src="#" alt="Preview" class="hidden img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                    @error('profile_image')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Toggle Yes/No -->
                                <div class="mb-3">
                                    <label class="form-label">Enable Module</label>
                                    <select id="moduleToggle" class="form-select">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
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
                                                    <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab" aria-controls="family" aria-selected="false">
                                                        <i class="fas fa-users me-2"></i>Family Information
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
                                                
                                                <!-- Salary Details Tab -->
                                                <!-- UPDATED SALARY DETAILS TAB FOR ADD EMPLOYEE FORM -->
<div class="tab-pane fade show active" id="salary" role="tabpanel" aria-labelledby="salary-tab">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-primary mb-0">Salary Details</h5>
            <span class="badge bg-light text-dark" id="salarySummary">Not calculated</span>
        </div>
    </div>

    <!-- INFO ALERT -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> Below values are loaded from Salary Master Configuration. 
        <a href="{{ route('salary-master.index') }}" target="_blank" class="alert-link">Edit Salary Master</a> to change default values.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: EARNINGS -->
        <div class="col-md-6">
            <h6 class="text-primary mb-3">Earnings</h6>

            <!-- Gross Salary (NEW FIELD) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Gross Salary <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           name="gross_salary" 
                           id="gross_salary" 
                           placeholder="Enter gross salary" 
                           value="{{ old('gross_salary', '') }}"
                           step="0.01" 
                           min="0">
                </div>
                <small class="text-muted">Enter total gross salary (will auto-calculate basic salary)</small>
            </div>

            <!-- Basic Salary (AUTO-CALCULATED) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Basic Salary <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           name="basic" 
                           id="basic"
                           placeholder="Auto-calculated from gross salary" 
                           value="{{ old('basic', '') }}"
                           step="0.01" 
                           min="0">
                </div>
                <small class="text-muted">Auto-calculated as Gross × Gross-to-Basic percentage</small>
            </div>

            <!-- DA (READ-ONLY FROM MASTER) -->
            <div class="mb-3">
                <label class="form-label fw-bold">DA (%) <span class="badge bg-secondary ms-2">From Master</span></label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           id="da" 
                           name="da"
                           placeholder="DA percentage" 
                           step="0.01" 
                           min="0" 
                           max="100"
                           >
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <!-- HRA (READ-ONLY FROM MASTER) -->
            <div class="mb-3">
                <label class="form-label fw-bold">HRA (%) <span class="badge bg-secondary ms-2">From Master</span></label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           id="hra" 
                           name="hra"
                           placeholder="HRA percentage" 
                           step="0.01" 
                           min="0" 
                           max="100"
                           >
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <!-- Conveyance (READ-ONLY FROM MASTER) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Conveyance <span class="badge bg-secondary ms-2">From Master</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           id="conveyance" 
                           name="conveyance"
                           placeholder="Conveyance amount" 
                           value="{{ old('conveyance', 0) }}"
                           step="0.01" 
                           min="0"
                           >
                </div>
            </div>

            <!-- Special Allowance (READ-ONLY FROM MASTER) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Special Allowance <span class="badge bg-secondary ms-2">From Master</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           id="allowance" 
                           name="allowance"
                           placeholder="Special allowance" 
                           value="{{ old('allowance', 0) }}"
                           step="0.01" 
                           min="0"
                           >
                </div>
            </div>

            <!-- Medical Allowance (READ-ONLY FROM MASTER) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Medical Allowance <span class="badge bg-secondary ms-2">From Master</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           id="medical" 
                           name="medical"
                           placeholder="Medical allowance" 
                           value="{{ old('medical', 0) }}"
                           step="0.01" 
                           min="0"
                           >
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: DEDUCTIONS -->
        <div class="col-md-6">
            <h6 class="text-danger mb-3">Deductions</h6>

            <!-- PF (EDITABLE, master default) -->
<div class="mb-3">
    <label class="form-label fw-bold">PF (%) <span class="badge bg-warning ms-2">Editable</span></label>
    <div class="input-group">
        <input type="number" class="form-control" name="pf" id="pf"
               value="{{ old('pf', 0) }}" placeholder="PF percentage"
               step="0.01" min="0" max="100">
        <span class="input-group-text">%</span>
    </div>
    <small class="text-muted">Default from salary master — edit if needed for this employee</small>
</div>

            <!-- ESI (EDITABLE, master default) -->
<div class="mb-3">
    <label class="form-label fw-bold">ESI (%) <span class="badge bg-warning ms-2">Editable</span></label>
    <div class="input-group">
        <input type="number" class="form-control" name="esi" id="esi"
               value="{{ old('esi', 0) }}" placeholder="ESI percentage"
               step="0.01" min="0" max="100">
        <span class="input-group-text">%</span>
    </div>
    <small class="text-muted">Default from salary master — edit if needed for this employee</small>
</div>

            <!-- TDS (EDITABLE) -->
            <div class="mb-3">
                <label class="form-label fw-bold">TDS <span class="badge bg-warning ms-2">Editable</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           name="tds" 
                           id="tds" 
                           value="{{ old('tds', 0) }}"
                           placeholder="TDS amount" 
                           step="0.01" 
                           min="0">
                </div>
                <small class="text-muted">Tax Deducted at Source (can be edited per employee)</small>
            </div>

            <!-- Professional Tax (EDITABLE) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Professional Tax <span class="badge bg-warning ms-2">Editable</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           name="tax" 
                           id="tax" 
                           value="{{ old('tax', 0) }}"
                           placeholder="Professional tax" 
                           step="0.01" 
                           min="0">
                </div>
                <small class="text-muted">Professional/Income tax (can be edited per employee)</small>
            </div>

            <!-- Welfare Fund (EDITABLE) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Welfare Fund <span class="badge bg-warning ms-2">Editable</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control" 
                           name="welfare" 
                           id="welfare" 
                           value="{{ old('welfare', 0) }}"
                           placeholder="Welfare fund" 
                           step="0.01" 
                           min="0">
                </div>
                <small class="text-muted">Employee welfare/other deductions (can be edited per employee)</small>
            </div>

            
                                

            <!-- Net Salary (AUTO-CALCULATED) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Net Salary</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" 
                           class="form-control bg-light" 
                           name="net_salary" 
                           id="net_salary" 
                           placeholder="Calculated net salary"
                           readonly>
                </div>
                <small class="text-muted">Auto-calculated: Total Earnings - Total Deductions</small>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load salary master configuration on page load
    loadSalaryMasterConfig();

    // Gross Salary → Basic Salary conversion
    document.getElementById('gross_salary').addEventListener('change', function() {
        const grossSalary = parseFloat(this.value) || 0;
        const grossToBasicPercentage = parseFloat(document.getElementById('da').dataset.grossToBasicPercentage) || 50;
        
        const basicSalary = (grossSalary * grossToBasicPercentage) / 100;
        document.getElementById('basic').value = basicSalary.toFixed(2);
        
        // Auto-calculate salary on gross entry
        if (grossSalary > 0) {
            calculateSalary();
        }
    });

    // Auto-calculate when salary fields change
    ['basic', 'tds', 'tax', 'welfare'].forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', function() {
            clearTimeout(window.salaryCalcTimeout);
            window.salaryCalcTimeout = setTimeout(() => {
                if (parseFloat(document.getElementById('basic').value) > 0) {
                    calculateSalary();
                }
            }, 800);
        });
    });

    // Calculate button
    document.getElementById('calculateSalaryBtn').addEventListener('click', calculateSalary);

    // Reset button
    document.getElementById('resetSalaryBtn').addEventListener('click', function() {
        document.getElementById('gross_salary').value = '';
        document.getElementById('basic').value = '';
        document.getElementById('tds').value = '0';
        document.getElementById('tax').value = '0';
        document.getElementById('welfare').value = '0';
        document.getElementById('net_salary').value = '';
        document.getElementById('salaryCalculationResults').style.display = 'none';
        document.getElementById('salarySummary').textContent = 'Not calculated';
        document.getElementById('salarySummary').className = 'badge bg-light text-dark';
    });
});

function loadSalaryMasterConfig() {
    fetch('{{ route("salary-master.get-config") }}')
        .then(response => response.json())
        .then(data => {
            // Earnings
            document.getElementById('da').dataset.grossToBasicPercentage = data.gross_to_basic_percentage;
            document.getElementById('da').value = data.da_percentage;
            document.getElementById('hra').value = data.hra_percentage;
            document.getElementById('conveyance').value = data.conveyance;
            document.getElementById('allowance').value = data.special_allowance;
            document.getElementById('medical').value = data.medical_allowance;

            // Deductions (all from master)
            document.getElementById('pf').value = data.pf_percentage;
            document.getElementById('esi').value = data.esi_percentage;
            document.getElementById('tax').value = data.professional_tax;
            document.getElementById('welfare').value = data.welfare_fund;
            document.getElementById('tds').value = data.tds;   // 👈 new
        })
        .catch(error => {
            console.error('Error loading salary master config:', error);
        });
}

function calculateSalary() {
    const basicSalary = parseFloat(document.getElementById('basic').value) || 0;
    
    if (basicSalary <= 0) {
        alert('Please enter basic salary first');
        return;
    }
    
    const daPercentage = parseFloat(document.getElementById('da').value) || 0;
    const hraPercentage = parseFloat(document.getElementById('hra').value) || 0;
    const pfPercentage = parseFloat(document.getElementById('pf').value) || 0;
    const esiPercentage = parseFloat(document.getElementById('esi').value) || 0;
    const conveyance = parseFloat(document.getElementById('conveyance').value) || 0;
    const allowance = parseFloat(document.getElementById('allowance').value) || 0;
    const medical = parseFloat(document.getElementById('medical').value) || 0;
    const tds = parseFloat(document.getElementById('tds').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const welfare = parseFloat(document.getElementById('welfare').value) || 0;
    
    const daAmount = (daPercentage / 100) * basicSalary;
    const hraAmount = (hraPercentage / 100) * basicSalary;
    const pfAmount = (pfPercentage / 100) * basicSalary;
    const esiAmount = (esiPercentage / 100) * basicSalary;
    
    const totalEarnings = basicSalary + daAmount + hraAmount + conveyance + allowance + medical;
    const totalDeductions = pfAmount + esiAmount + tds + tax + welfare;
    const netSalary = totalEarnings - totalDeductions;
    
    updateSalaryDisplay({
        basic: basicSalary,
        da_amount: daAmount,
        hra_amount: hraAmount,
        pf_amount: pfAmount,
        esi_amount: esiAmount,
        conveyance: conveyance,
        allowance: allowance,
        medical: medical,
        tds: tds,
        tax: tax,
        welfare: welfare,
        total_earnings: totalEarnings,
        total_deductions: totalDeductions,
        net_salary: netSalary
    });
}

function updateSalaryDisplay(salaryData) {
    document.getElementById('display_basic').textContent = salaryData.basic.toFixed(2);
    document.getElementById('display_da').textContent = salaryData.da_amount.toFixed(2);
    document.getElementById('display_hra').textContent = salaryData.hra_amount.toFixed(2);
    document.getElementById('display_conveyance').textContent = salaryData.conveyance.toFixed(2);
    document.getElementById('display_allowance').textContent = salaryData.allowance.toFixed(2);
    document.getElementById('display_medical').textContent = salaryData.medical.toFixed(2);
    document.getElementById('display_pf').textContent = salaryData.pf_amount.toFixed(2);
    document.getElementById('display_esi').textContent = salaryData.esi_amount.toFixed(2);
    document.getElementById('display_tds').textContent = salaryData.tds.toFixed(2);
    document.getElementById('display_tax').textContent = salaryData.tax.toFixed(2);
    document.getElementById('display_welfare').textContent = salaryData.welfare.toFixed(2);
    document.getElementById('display_total_earnings').textContent = salaryData.total_earnings.toFixed(2);
    document.getElementById('display_total_deductions').textContent = salaryData.total_deductions.toFixed(2);
    document.getElementById('display_net_salary').textContent = salaryData.net_salary.toFixed(2);
    
    document.getElementById('net_salary').value = salaryData.net_salary.toFixed(2);
    document.getElementById('salaryCalculationResults').style.display = 'block';
    
    const summaryBadge = document.getElementById('salarySummary');
    summaryBadge.textContent = '₹' + salaryData.net_salary.toFixed(2);
    summaryBadge.className = 'badge bg-success';
}
</script>

                                                <!-- Personal Information Tab -->
                                                <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                                    <div class="card shadow-sm mt-4">
                                                        <h4 class="card-header">Add Personal Information</h4>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Passport No</label>
                                                                    <input type="text" name="passport_no" class="form-control" placeholder="Enter Passport Number">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Aadhaar Number</label>
                                                                    <input type="text" name="aadhaar_number" class="form-control" placeholder="Enter Aadhaar Number">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Blood Group</label>
                                                                    <input type="text" name="blood_group" class="form-control" placeholder="Enter Blood Group">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Nationality</label>
                                                                    <input type="text" name="nationality" class="form-control" placeholder="Enter Nationality">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Religion</label>
                                                                    <input type="text" name="religion" class="form-control" placeholder="Enter Religion">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Marital Status</label>
                                                                    <select class="form-control" name="marital_status">
                                                                        <option value="">Select</option>
                                                                        <option value="Single">Single</option>
                                                                        <option value="Married">Married</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Birthday</label>
                                                                    <input type="date" name="birthday" class="form-control">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Gender</label>
                                                                    <select class="form-control" name="gender">
                                                                        <option value="">Select</option>
                                                                        <option value="Male">Male</option>
                                                                        <option value="Female">Female</option>
                                                                        <option value="Other">Other</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-12 mb-3">
                                                                    <label class="col-form-label">Address</label>
                                                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter Full Address"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Emergency Contact Tab -->
                                                <div class="tab-pane fade" id="emergency" role="tabpanel" aria-labelledby="emergency-tab">
                                                    <div class="card shadow-sm mt-4">
                                                        <h4 class="card-header">Add Emergency Contact</h4>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Primary Contact Name</label>
                                                                    <input type="text" name="primary_name" class="form-control" placeholder="Enter Primary Contact Name">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Relationship</label>
                                                                    <input type="text" name="relationship" class="form-control" placeholder="Enter Relationship">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Phone</label>
                                                                    <input type="text" name="primary_phone" class="form-control" placeholder="Enter Phone Number">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Secondary Contact Name</label>
                                                                    <input type="text" name="secondary_name" class="form-control" placeholder="Enter Secondary Contact Name">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Secondary Relationship</label>
                                                                    <input type="text" name="secondary_relationship" class="form-control" placeholder="Enter Secondary Relationship">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Secondary Phone</label>
                                                                    <input type="text" name="secondary_phone" class="form-control" placeholder="Enter Secondary Phone Number">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bank Information Tab -->
                                                <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                                                    <div class="card shadow-sm mt-4">
                                                        <h4 class="card-header">Add Bank Information</h4>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Bank Name</label>
                                                                    <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter Bank Name">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Bank Account No.</label>
                                                                    <input type="text" name="bank_account_no" id="bank_account_no" class="form-control" placeholder="Enter Account Number">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">IFSC Code</label>
                                                                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" placeholder="Enter IFSC Code">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">PAN No</label>
                                                                    <input type="text" name="pan_no" id="pan_no" class="form-control" placeholder="Enter PAN Number">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Branch Name</label>
                                                                    <input type="text" name="branch_name" id="branch_name" class="form-control" placeholder="Enter Branch Name">
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="col-form-label">Account Type</label>
                                                                    <select name="account_type" id="account_type" class="form-control">
                                                                        <option value="">Select</option>
                                                                        <option value="Savings">Savings</option>
                                                                        <option value="Current">Current</option>
                                                                        <option value="Salary">Salary</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Family Information Tab -->
                                                <div class="tab-pane fade" id="family" role="tabpanel" aria-labelledby="family-tab">
                                                    <div class="card shadow-sm mt-4">
                                                        <div class="card-header bg-primary text-white">
                                                            <h5 class="mb-0">Add Family Information</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="family_members_container">
                                                                <div class="family-member mb-3">
                                                                    <h5>Family Member 
                                                                        <a href="javascript:void(0);" class="text-danger remove-family-member"><i class="fa fa-trash"></i></a>
                                                                    </h5>
                                                                    <div class="row">
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="col-form-label">Name</label>
                                                                            <input type="text" name="family_members[0][name]" class="form-control">
                                                                        </div>
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="col-form-label">Relationship</label>
                                                                            <input type="text" name="family_members[0][relationship]" class="form-control">
                                                                        </div>
                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="col-form-label">Phone</label>
                                                                            <input type="text" name="family_members[0][phone]" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" id="add_family_member" class="btn btn-primary mt-2">Add More</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Education Tab -->
                                                <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                                                    <div class="card shadow-sm mt-4">
                                                        <div class="card-header bg-primary text-white">
                                                            <h5 class="mb-0">Add Education Information</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="education_section_container">
                                                                <div class="card mb-3 education-card">
                                                                    <div class="card-body">
                                                                        <h5>Education Information
                                                                            <a href="javascript:void(0);" class="text-danger remove-education"><i class="fa fa-trash"></i></a>
                                                                        </h5>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Institution</label>
                                                                                <input type="text" name="education[0][institution]" class="form-control">
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Subject</label>
                                                                                <input type="text" name="education[0][subject]" class="form-control">
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
                                                            <h5 class="mb-0">Add Experience Information</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="experience_section_container">
                                                                <div class="card mb-3 experience-card">
                                                                    <div class="card-body">
                                                                        <h5>Experience Information
                                                                            <a href="javascript:void(0);" class="text-danger remove-experience">
                                                                                <i class="fa fa-trash"></i>
                                                                            </a>
                                                                        </h5>
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Company Name</label>
                                                                                <input type="text" name="experience[0][company_name]" class="form-control">
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Location</label>
                                                                                <input type="text" name="experience[0][location]" class="form-control">
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Job Position</label>
                                                                                <input type="text" name="experience[0][job_position]" class="form-control">
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Period From</label>
                                                                                <input type="date" name="experience[0][period_from]" class="form-control">
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Period To</label>
                                                                                <input type="date" name="experience[0][period_to]" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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

                                <!-- Module Permissions Section -->
                                <div class="col-md-12 mb-4" id="modulePermissions" style="display: none;">
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
                                                <div class="col-md-12 mb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                        <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">
                                                        <button class="btn btn-outline-secondary" type="button" id="selectAllModulesBtn">Select All Modules</button>
                                                        <button class="btn btn-outline-secondary" type="button" id="selectAllPermissionsBtn">Select All Permissions</button>
                                                        <button class="btn btn-outline-info" type="button" id="showHierarchyModulesBtn">Show Hierarchy Modules</button>
                                                    </div>
                                                </div>

                                                @if(in_array('Recruitment Management', $allowedModules))
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_{{ Str::slug($subModule['key'], '_') }}">
                                                                            {{ $subModule['label'] }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        @if(in_array('view', $subModule['perms']))
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $subModule['key'] }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_{{ Str::slug($subModule['key'], '_') }}">
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
                                                                                       id="create_{{ Str::slug($subModule['key'], '_') }}">
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
                                                                                       id="edit_{{ Str::slug($subModule['key'], '_') }}">
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
                                                                                       id="delete_{{ Str::slug($subModule['key'], '_') }}">
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
                                                                                       id="approve_{{ Str::slug($subModule['key'], '_') }}">
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_leaves_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_leaves_{{ $loop->index }}">
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
                                                                                       id="create_leaves_{{ $loop->index }}">
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
                                                                                       id="edit_leaves_{{ $loop->index }}">
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
                                                                                       id="delete_leaves_{{ $loop->index }}">
                                                                                <label class="form-check-label text-danger" for="delete_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_leaves_{{ $loop->index }}">
                                                                                <label class="form-check-label text-info" for="approve_leaves_{{ $loop->index }}">
                                                                                    <i class="fas fa-check"></i> Approve
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_attendance_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_attendance_{{ $loop->index }}">
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
                                                                                       id="create_attendance_{{ $loop->index }}">
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
                                                                                       id="edit_attendance_{{ $loop->index }}">
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
                                                                                       id="delete_attendance_{{ $loop->index }}">
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
                                                                                       id="approve_attendance_{{ $loop->index }}">
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_timetracker_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        @if($module != 'My Tasks')
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_timetracker_{{ $loop->index }}">
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
                                                                                       id="create_timetracker_{{ $loop->index }}">
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
                                                                                       id="edit_timetracker_{{ $loop->index }}">
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
                                                                                       id="delete_timetracker_{{ $loop->index }}">
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
                                                                                       id="view_timetracker_{{ $loop->index }}">
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_employee">
                                                                            Employee Management
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Employee][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_employee">
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
                                                                                       id="create_employee">
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
                                                                                       id="edit_employee">
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
                                                                                       id="delete_employee">
                                                                                <label class="form-check-label text-danger" for="delete_employee">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Employee][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_employee">
                                                                                <label class="form-check-label text-info" for="approve_employee">
                                                                                    <i class="fas fa-check"></i> Approve
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_manage_shifts">
                                                                            Manage Shifts
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Manage Shifts][can_view]" value="1">
                                                                            <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Manage Shifts][can_create]" value="1">
                                                                            <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Manage Shifts][can_edit]" value="1">
                                                                            <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Manage Shifts][can_delete]" value="1">
                                                                            <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(in_array('Schedule', $allowedModules))
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_schedule">
                                                                            Schedule
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Schedule][can_view]" value="1">
                                                                            <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Schedule][can_create]" value="1">
                                                                            <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Schedule][can_edit]" value="1">
                                                                            <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                                   name="modules[Schedule][can_delete]" value="1">
                                                                            <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(in_array('Payroll', $allowedModules))
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_payroll_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        @if($module != 'Activity Log')
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_payroll_{{ $loop->index }}">
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
                                                                                       id="create_payroll_{{ $loop->index }}">
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
                                                                                       id="edit_payroll_{{ $loop->index }}">
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
                                                                                       id="delete_payroll_{{ $loop->index }}">
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
                                                                                       id="view_payroll_{{ $loop->index }}">
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_tickets">
                                                                            Tickets
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Tickets][can_view]"
                                                                                   value="1">
                                                                            <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Tickets][can_create]"
                                                                                   value="1">
                                                                            <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Tickets][can_edit]"
                                                                                   value="1">
                                                                            <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Tickets][can_delete]"
                                                                                   value="1">
                                                                            <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                                                        </div>
                                                                        <div class="col-3 mt-2">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Tickets][can_approve]"
                                                                                   value="1">
                                                                            <label class="text-info"><i class="fas fa-check"></i> Approve</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(in_array('Testing', $allowedModules))
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_testing">
                                                                            Testing
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Testing][can_view]"
                                                                                   value="1">
                                                                            <label class="text-success"><i class="fas fa-eye"></i> View</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Testing][can_create]"
                                                                                   value="1">
                                                                            <label class="text-primary"><i class="fas fa-plus"></i> Create</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Testing][can_edit]"
                                                                                   value="1">
                                                                            <label class="text-warning"><i class="fas fa-edit"></i> Edit</label>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Testing][can_delete]"
                                                                                   value="1">
                                                                            <label class="text-danger"><i class="fas fa-trash"></i> Delete</label>
                                                                        </div>
                                                                        <div class="col-3 mt-2">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Testing][can_approve]"
                                                                                   value="1">
                                                                            <label class="text-info"><i class="fas fa-check"></i> Approve</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(in_array('Accounts', $allowedModules))
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_accounts_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_accounts_{{ $loop->index }}">
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
                                                                                       id="create_accounts_{{ $loop->index }}">
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
                                                                                       id="edit_accounts_{{ $loop->index }}">
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
                                                                                       id="delete_accounts_{{ $loop->index }}">
                                                                                <label class="form-check-label text-danger" for="delete_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_accounts_{{ $loop->index }}">
                                                                                <label class="form-check-label text-info" for="approve_accounts_{{ $loop->index }}">
                                                                                    <i class="fas fa-check"></i> Approve
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
                                                                               value="1">
                                                                        <label class="form-check-label fw-bold" for="module_reports_{{ $loop->index }}">
                                                                            {{ $module }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="permissions-row" style="display: none; margin-left: 20px; margin-top: 5px;">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[{{ $module }}][can_view]" 
                                                                                       value="1" 
                                                                                       id="view_reports_{{ $loop->index }}">
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
                                                                                       id="download_reports_{{ $loop->index }}">
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
                                                                                       id="approve_reports_{{ $loop->index }}">
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
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-success text-white p-2 rounded">
                                                            <i class="fas fa-file-alt me-2"></i>Policies
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Policy">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_policy"
                                                                           name="modules[Policy][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_policy">
                                                                        Policies
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Policy][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Policy][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_policy">
                                                                                <label class="form-check-label text-info" for="approve_policy">
                                                                                    <i class="fas fa-check"></i> Approve
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

                                                @if(in_array('Goals', $allowedModules))
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-info text-white p-2 rounded">
                                                            <i class="fas fa-bullseye me-2"></i>Goals
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Goals">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_goals"
                                                                           name="modules[Goals][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_goals">
                                                                        Goals
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Goals][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Goals][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_goals">
                                                                                <label class="form-check-label text-info" for="approve_goals">
                                                                                    <i class="fas fa-check"></i> Approve
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

                                                @if(in_array('Training', $allowedModules))
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-primary text-white p-2 rounded">
                                                            <i class="fas fa-chalkboard-teacher me-2"></i>Training
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Training">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_training"
                                                                           name="modules[Training][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_training">
                                                                        Training
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Training][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Training][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_training">
                                                                                <label class="form-check-label text-info" for="approve_training">
                                                                                    <i class="fas fa-check"></i> Approve
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

                                                @if(in_array('Assets', $allowedModules))
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-warning text-white p-2 rounded">
                                                            <i class="fas fa-box me-2"></i>Assets
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Assets">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_assets"
                                                                           name="modules[Assets][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_assets">
                                                                        Assets
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Assets][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Assets][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_assets">
                                                                                <label class="form-check-label text-info" for="approve_assets">
                                                                                    <i class="fas fa-check"></i> Approve
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

                                                @if(in_array('Travel', $allowedModules))
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-danger text-white p-2 rounded">
                                                            <i class="fas fa-plane me-2"></i>Travel
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Travel">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_travel"
                                                                           name="modules[Travel][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_travel">
                                                                        Travel
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Travel][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                                       name="modules[Travel][can_approve]" 
                                                                                       value="1" 
                                                                                       id="approve_travel">
                                                                                <label class="form-check-label text-info" for="approve_travel">
                                                                                    <i class="fas fa-check"></i> Approve
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

                                                @if(in_array('Offboarding', $allowedModules))
                                                <div class="col-md-6 mb-3">
                                                    <div class="category-card">
                                                        <h6 class="category-header bg-dark text-white p-2 rounded">
                                                            <i class="fas fa-user-minus me-2"></i>Offboarding
                                                        </h6>
                                                        <div class="category-modules">
                                                            <div class="module-item" data-module="Offboarding">
                                                                <div class="form-check">
                                                                    <input class="form-check-input module-checkbox"
                                                                           type="checkbox"
                                                                           id="module_offboarding"
                                                                           name="modules[Offboarding][enabled]"
                                                                           value="1">
                                                                    <label class="form-check-label fw-bold" for="module_offboarding">
                                                                        Offboarding
                                                                    </label>
                                                                </div>
                                                                <div class="permissions-row" style="display:none; margin-left:20px; margin-top:5px;">
                                                                    <div class="row">
                                                                        @foreach(['can_view','can_create','can_edit','can_delete'] as $perm)
                                                                        <div class="col-3">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Offboarding][{{ $perm }}]"
                                                                                   value="1">
                                                                            <label>{{ ucfirst(str_replace('can_','',$perm)) }}</label>
                                                                        </div>
                                                                        @endforeach
                                                                        <div class="col-3 mt-2">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="modules[Offboarding][can_approve]"
                                                                                   value="1">
                                                                            <label class="text-info"><i class="fas fa-check"></i> Approve</label>
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

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button id="saveEmployeeBtn" class="btn btn-primary btn-lg" type="submit">
                                        <i class="fas fa-save me-2"></i>Save Employee
                                    </button>
                                    <a href="{{ route('employee.index') }}" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i>Cancel
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    
    .file-preview-item {
        margin: 5px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #f9f9f9;
    }
</style>

<script>
    // DECLARE ALL GLOBAL VARIABLES
    let hierarchyModules = [];
    
    // ========== HELPER FUNCTIONS ==========
    function formatCurrency(amount) {
        return parseFloat(amount || 0).toFixed(2);
    }
    
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
        
        $('#employeeForm').prepend(notification);
        
        setTimeout(function() {
            notification.fadeOut();
        }, 5000);
    }
    
    function updateModuleCounters() {
        var totalChecked = $('.module-checkbox:checked').length;
        var hierarchyCount = hierarchyModules.length;
        var manualCount = totalChecked - hierarchyCount;
        if (manualCount < 0) manualCount = 0;
        
        $('#hierarchyModuleCount').text(hierarchyCount + ' from hierarchy');
        $('#manualModuleCount').text(manualCount + ' manual');
        $('#totalModuleCount').text(totalChecked + ' total');
    }
    
    // ========== DOCUMENT READY ==========
    $(document).ready(function() {
        // ========== DEPARTMENT & DESIGNATION ==========
        $('#department').on('change', function() {
            var departmentId = $(this).val();
            $('#designation').html('<option value="">Loading...</option>');
            if (departmentId) {
                $.ajax({
                    url: '{{ url("/get-designations") }}/' + departmentId,
                    type: 'GET',
                    success: function(res) {
                        $('#designation').empty().append('<option value="">Select Designation</option>');
                        $.each(res, function(key, designation) {
                            $('#designation').append('<option value="' + designation.id + '">' + designation.designation + '</option>');
                        });
                        @if(old('designation'))
                            $('#designation').val('{{ old('designation') }}');
                        @endif
                    },
                    error: function() {
                        $('#designation').html('<option value="">Error loading designations</option>');
                    }
                });
                
                // Load managers and team leads for this department
                loadManagersAndTeamLeads(departmentId);
            } else {
                $('#designation').html('<option value="">Select Designation</option>');
                $('#manager_id').html('<option value="">Select Reporting Manager (Optional)</option>');
                $('#team_lead_id').html('<option value="">Select Team Lead (Optional)</option>');
            }
        });
    
        @if(old('department'))
            $('#department').trigger('change');
        @endif
    
        // ========== BRANCH CHANGE ==========
        $('#branch_id').on('change', function() {
            const departmentId = $('#department').val();
            if (departmentId) {
                loadManagersAndTeamLeads(departmentId);
            }
        });
    
        // ========== TRAINING NEEDED TOGGLE ==========
        $('#training_needed').on('change', function() {
            if ($(this).val() === 'Yes') {
                $('#trainer_field').show();
                $('#trainer_id').prop('required', true);
            } else {
                $('#trainer_field').hide();
                $('#trainer_id').prop('required', false);
                $('#trainer_id').val('');
            }
        });
    
        // Trigger on page load if training is "Yes"
        if ($('#training_needed').val() === 'Yes') {
            $('#trainer_field').show();
            $('#trainer_id').prop('required', true);
        }
    
        // ========== MODULE TOGGLE ==========
        $('#moduleToggle').on('change', function() {
            const permissionsDiv = $('#modulePermissions');
            if (this.value === 'yes') {
                permissionsDiv.show();
            } else {
                permissionsDiv.hide();
                permissionsDiv.find('input[type=checkbox]').prop('checked', false);
                updateModuleCounters();
            }
        });
    
        // ========== HIERARCHY CHANGE ==========
        $('#hierarchy').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var modules = selectedOption.data('modules');
            
            // Clear existing hierarchy modules
            hierarchyModules = [];
            $('.hierarchy-indicator').hide();
            $('.module-item').removeClass('hierarchy-module');
            $('.module-checkbox').prop('checked', false);
            $('.permission-checkbox').prop('checked', false).closest('.permissions-row').hide();
    
            if (modules) {
                try {
                    var moduleObj = typeof modules === 'string' ? JSON.parse(modules) : modules;
                    if (typeof moduleObj === 'object') {
                        hierarchyModules = Object.keys(moduleObj);
    
                        Object.entries(moduleObj).forEach(([moduleName, perms]) => {
                            var moduleItem = $('.module-item[data-module="' + moduleName + '"]');
                            var checkbox = moduleItem.find('.module-checkbox');
    
                            // Enable module
                            checkbox.prop('checked', true);
                            moduleItem.addClass('hierarchy-module');
    
                            // Show permission row
                            var permissionsRow = moduleItem.find('.permissions-row');
                            permissionsRow.show();
    
                            // Check each permission based on JSON
                            if (perms.view) permissionsRow.find('input[name*="[can_view]"]').prop('checked', true);
                            if (perms.create) permissionsRow.find('input[name*="[can_create]"]').prop('checked', true);
                            if (perms.edit) permissionsRow.find('input[name*="[can_edit]"]').prop('checked', true);
                            if (perms.delete) permissionsRow.find('input[name*="[can_delete]"]').prop('checked', true);
                            if (perms.approve) permissionsRow.find('input[name*="[can_approve]"]').prop('checked', true);
                            if (perms.download) permissionsRow.find('input[name*="[can_download]"]').prop('checked', true);
                            if (perms.export) permissionsRow.find('input[name*="[can_export]"]').prop('checked', true);
                        });
    
                        updateModuleCounters();
                        showNotification(
                            'Modules with permissions auto-loaded from hierarchy (' + hierarchyModules.length + ' modules)',
                            'success'
                        );
                    }
                } catch (e) {
                    console.error('Error parsing modules JSON:', e);
                    showNotification('Error loading hierarchy modules', 'error');
                }
            } else {
                updateModuleCounters();
            }
        });
    
        // ========== MODULE CHECKBOX HANDLERS ==========
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
    
        // ========== MODULE SEARCH FUNCTIONALITY ==========
        $('#moduleSearch').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            $('.module-item').each(function() {
                var moduleName = $(this).data('module').toLowerCase();
                if (moduleName.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    
        // ========== BULK ACTIONS FOR MODULES ==========
        $('#selectAllModulesBtn').on('click', function() {
            $('.module-checkbox').prop('checked', true).trigger('change');
        });
    
        $('#selectAllPermissionsBtn').on('click', function() {
            $('.permission-checkbox').prop('checked', true);
        });
    
        $('#showHierarchyModulesBtn').on('click', function() {
            $('.module-item').show();
            $('.module-item.hierarchy-module').css('background-color', '#f8f9ff');
            $('.module-item:not(.hierarchy-module)').css('background-color', 'transparent');
        });
    
        // ========== FILE PREVIEWS ==========
        $('#document_files').on('change', function(e) {
            const preview = $('#file-preview');
            preview.empty();
            
            Array.from(e.target.files).forEach(file => {
                const fileElement = $('<div class="file-preview-item"></div>');
                fileElement.text(file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)');
                preview.append(fileElement);
            });
        });
    
        $('#profile_image').on('change', function(e) {
            const reader = new FileReader();
            const preview = $('#image_preview');
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
                preview.show();
            }
            
            if (this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });
    
        // ========== FORM VALIDATION ==========
        const form = document.getElementById('employeeForm');
        const saveBtn = document.getElementById('saveEmployeeBtn');
    
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });

        $('#employeeForm').on('click', 'button', function(event) {
            if (this.type !== 'submit') {
                event.preventDefault();
            }
        });
    
        // ========== DYNAMIC FORM SECTIONS ==========
        $('#add_family_member').on('click', function() {
            const container = $('#family_members_container');
            const index = container.children().length;
    
            const newMember = `
                <div class="family-member mb-3">
                    <h5>Family Member
                        <a href="javascript:void(0);" class="text-danger remove-family-member"><i class="fa fa-trash"></i></a>
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Name</label>
                            <input type="text" name="family_members[${index}][name]" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Relationship</label>
                            <input type="text" name="family_members[${index}][relationship]" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Phone</label>
                            <input type="text" name="family_members[${index}][phone]" class="form-control">
                        </div>
                    </div>
                </div>
            `;
            container.append(newMember);
        });
    
        $('#add_education').on('click', function() {
            const container = $('#education_section_container');
            const index = container.children().length;
    
            const newEducation = `
                <div class="card mb-3 education-card">
                    <div class="card-body">
                        <h5>New Education
                            <a href="javascript:void(0);" class="text-danger remove-education"><i class="fa fa-trash"></i></a>
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Institution</label>
                                <input type="text" name="education[${index}][institution]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Subject</label>
                                <input type="text" name="education[${index}][subject]" class="form-control">
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
            container.append(newEducation);
        });
    
        $('#add_experience').on('click', function() {
            const container = $('#experience_section_container');
            const index = container.children().length;
    
            const newExperience = `
                <div class="card mb-3 experience-card">
                    <div class="card-body">
                        <h5>New Experience
                            <a href="javascript:void(0);" class="text-danger remove-experience"><i class="fa fa-trash"></i></a>
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Company Name</label>
                                <input type="text" name="experience[${index}][company_name]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Location</label>
                                <input type="text" name="experience[${index}][location]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Job Position</label>
                                <input type="text" name="experience[${index}][job_position]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Period From</label>
                                <input type="date" name="experience[${index}][period_from]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Period To</label>
                                <input type="date" name="experience[${index}][period_to]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(newExperience);
        });
    
        $(document).on('click', '.remove-family-member', function() {
            $(this).closest('.family-member').remove();
        });
    
        $(document).on('click', '.remove-education', function() {
            $(this).closest('.education-card').remove();
        });
    
        $(document).on('click', '.remove-experience', function() {
            $(this).closest('.experience-card').remove();
        });
    
        updateModuleCounters();
        $('.module-checkbox:checked').each(function() {
            $(this).closest('.module-item').find('.permissions-row').show();
        });
    });
    
    function loadManagersAndTeamLeads(departmentId) {
        const branchId = $('#branch_id').val();
        
        $('#manager_id').html('<option value="">Loading...</option>');
        $('#team_lead_id').html('<option value="">Loading...</option>');
    
        if (departmentId) {
            const managerUrl = "{{ route('employee.getManagersByDepartment', ':id') }}".replace(':id', departmentId) + `?branch_id=${branchId}`;
            const teamLeadUrl = "{{ route('employee.getTeamLeadsByDepartment', ':id') }}".replace(':id', departmentId) + `?branch_id=${branchId}`;
    
            fetch(managerUrl)
                .then(response => response.json())
                .then(data => {
                    $('#manager_id').empty().append('<option value="">Select Reporting Manager (Optional)</option>');
                    data.forEach(emp => {
                        $('#manager_id').append(`
                            <option value="${emp.id}" data-employee-id="${emp.employeeid}">
                                ${emp.firstname} ${emp.lastname} (${emp.designation_name})
                            </option>`);
                    });
                })
                .catch(err => {
                    $('#manager_id').html('<option value="">Error loading managers</option>');
                    console.error('Error:', err);
                });
    
            fetch(teamLeadUrl)
                .then(response => response.json())
                .then(data => {
                    $('#team_lead_id').empty().append('<option value="">Select Team Lead (Optional)</option>');
                    data.forEach(emp => {
                        $('#team_lead_id').append(`
                            <option value="${emp.id}" data-employee-id="${emp.employeeid}">
                                ${emp.firstname} ${emp.lastname} (${emp.designation_name})
                            </option>`);
                    });
                })
                .catch(err => {
                    $('#team_lead_id').html('<option value="">Error loading team leads</option>');
                    console.error('Error:', err);
                });
        } else {
            $('#manager_id').html('<option value="">Select Reporting Manager (Optional)</option>');
            $('#team_lead_id').html('<option value="">Select Team Lead (Optional)</option>');
        }
    }
    
    function initializeSalaryCalculations() {
        $('#basic').on('blur', function() {
            const basicSalary = parseFloat(this.value) || 0;
            if (basicSalary > 0) {
                calculateSalary();
            }
        });
        
        const salaryFields = ['basic', 'da', 'hra', 'conveyance', 'allowance', 'medical', 'tds', 'tax', 'welfare'];
        
        salaryFields.forEach(field => {
            const fieldElement = document.getElementById(field);
            if (fieldElement) {
                fieldElement.addEventListener('input', function() {
                    const basicSalary = parseFloat(document.getElementById('basic').value) || 0;
                    if (basicSalary > 0) {
                        clearTimeout(window.salaryCalcTimeout);
                        window.salaryCalcTimeout = setTimeout(calculateSalary, 500);
                    }
                });
            }
        });
    }
    
    function calculateSalary() {
        const basicSalary = parseFloat($('#basic').val()) || 0;
        
        if (basicSalary <= 0) {
            $('#salaryCalculationResults').hide();
            $('#salarySummary').text('Enter basic salary').removeClass().addClass('badge bg-warning');
            return;
        }
        
        const calculateBtn = $('#calculateSalaryBtn');
        const originalText = calculateBtn.html();
        calculateBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Calculating...').prop('disabled', true);
        
        try {
            const daPercentage = parseFloat($('#da').val()) || 0;
            const hraPercentage = parseFloat($('#hra').val()) || 0;
            const pfPercentage = parseFloat($('#pf').val()) || 0;
            const esiPercentage = parseFloat($('#esi').val()) || 0;
            const conveyance = parseFloat($('#conveyance').val()) || 0;
            const allowance = parseFloat($('#allowance').val()) || 0;
            const medical = parseFloat($('#medical').val()) || 0;
            const tds = parseFloat($('#tds').val()) || 0;
            const tax = parseFloat($('#tax').val()) || 0;
            const welfare = parseFloat($('#welfare').val()) || 0;
            
            const daAmount = (daPercentage / 100) * basicSalary;
            const hraAmount = (hraPercentage / 100) * basicSalary;
            const pfAmount = (pfPercentage / 100) * basicSalary;
            const esiAmount = (esiPercentage / 100) * basicSalary;
            
            const totalEarnings = basicSalary + daAmount + hraAmount + conveyance + allowance + medical;
            const totalDeductions = pfAmount + esiAmount + tds + tax + welfare;
            const netSalary = totalEarnings - totalDeductions;
            
            updateSalaryDisplay({
                basic: basicSalary,
                da_amount: daAmount,
                hra_amount: hraAmount,
                pf_amount: pfAmount,
                esi_amount: esiAmount,
                conveyance: conveyance,
                allowance: allowance,
                medical: medical,
                tds: tds,
                tax: tax,
                welfare: welfare,
                total_earnings: totalEarnings,
                total_deductions: totalDeductions,
                net_salary: netSalary
            });
            
        } catch (error) {
            console.error('Calculation error:', error);
            showNotification('Error calculating salary. Please check your inputs.', 'error');
        } finally {
            calculateBtn.html(originalText).prop('disabled', false);
        }
    }
    
    function updateSalaryDisplay(salaryData) {
        $('#display_basic').text(formatCurrency(salaryData.basic));
        $('#display_da').text(formatCurrency(salaryData.da_amount));
        $('#display_hra').text(formatCurrency(salaryData.hra_amount));
        $('#display_conveyance').text(formatCurrency(salaryData.conveyance));
        $('#display_allowance').text(formatCurrency(salaryData.allowance));
        $('#display_medical').text(formatCurrency(salaryData.medical));
        $('#display_pf').text(formatCurrency(salaryData.pf_amount));
        $('#display_esi').text(formatCurrency(salaryData.esi_amount));
        $('#display_tds').text(formatCurrency(salaryData.tds));
        $('#display_tax').text(formatCurrency(salaryData.tax));
        $('#display_welfare').text(formatCurrency(salaryData.welfare));
        $('#display_total_earnings').text(formatCurrency(salaryData.total_earnings));
        $('#display_total_deductions').text(formatCurrency(salaryData.total_deductions));
        $('#display_net_salary').text(formatCurrency(salaryData.net_salary));
        
        $('#net_salary').val(salaryData.net_salary.toFixed(2));
        $('#salaryCalculationResults').show();
        $('#salarySummary').text('₹' + formatCurrency(salaryData.net_salary)).removeClass().addClass('badge bg-success');
    }
    
    $(document).ready(function() {
        initializeSalaryCalculations();
        const resetBtn = $('#resetSalaryBtn');
        
        resetBtn.on('click', function() {
            $('#basic').val('');
            $('#da').val('');
            $('#hra').val('');
            $('#conveyance').val('0');
            $('#allowance').val('0');
            $('#medical').val('0');
            $('#tds').val('0');
            $('#tax').val('0');
            $('#welfare').val('0');
            $('#net_salary').val('');
            $('#salaryCalculationResults').hide();
            $('#salarySummary').text('Not calculated').removeClass().addClass('badge bg-light text-dark');
        });
    
        const basicSalary = parseFloat($('#basic').val()) || 0;
        if (basicSalary > 0) {
            setTimeout(calculateSalary, 1000);
        }
    });
</script>
@endsection