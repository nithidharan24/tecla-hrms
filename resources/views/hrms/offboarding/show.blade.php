@extends('layouts.index')

@section('content')

@php
    use Carbon\Carbon;

    // defensive: if offboarding has no last_working_date
    $lastWorkingDate = $offboarding->last_working_date ? Carbon::parse($offboarding->last_working_date) : null;
    $today = Carbon::now();
    $daysRemaining = $lastWorkingDate ? $today->diffInDays($lastWorkingDate, false) : null;
@endphp

<div class="content container-fluid mt-3">
    <div class="card">
        <!-- Header Section -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Offboarding - {{ $offboarding->employeeid }} {{ $offboarding->firstname }} {{ $offboarding->lastname }}</h5>
                <small class="text-muted">Track Offboarding Process</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('offboarding.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('offboarding.edit', $offboarding->id) }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- MAIN TABS -->
            <ul class="nav leave-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details">
                        <i class="fa fa-info-circle"></i> Details
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#clearances">
                        <i class="fa fa-tasks"></i> Clearances ({{ $completedClearances }}/{{ $totalClearances }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                        <i class="fa fa-history"></i> Timeline
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#career-history">
                        <i class="fa fa-briefcase"></i> Career History
                    </a>
                </li>
            </ul>

            <div class="tabs-underline"></div>

            <!-- TAB CONTENT -->
            <div class="tab-content pt-3">

                <!-- DETAILS TAB -->
                <div class="tab-pane fade show active" id="details">
                    <div class="row">
                        <!-- Left Column - Employee Info -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Employee Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset($offboarding->profile_image ?? 'admin/assets/img/user.jpg') }}"
                                                 alt="Profile" class="rounded-circle" width="60" height="60">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $offboarding->firstname }} {{ $offboarding->lastname }}</h6>
                                            <small class="text-muted">{{ $offboarding->employeeid }}</small>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Designation</small>
                                        <strong>{{ $offboarding->designation_name ?? 'N/A' }}</strong>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Department</small>
                                        <strong>{{ $offboarding->department_name ?? 'N/A' }}</strong>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Current Experience</small>
                                        <strong>{{ $experience }}</strong>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Joining Date</small>
                                        <strong>{{ $offboarding->joiningdate ? Carbon::parse($offboarding->joiningdate)->format('d-M-Y') : '-' }}</strong>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Email</small>
                                        <strong>{{ $offboarding->email }}</strong>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Phone</small>
                                        <strong>{{ $offboarding->phone }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Reporting Manager -->
                            @if($reportingManager)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Reporting Manager</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                {{ substr($reportingManager->firstname ?? ' ', 0, 1) }}{{ substr($reportingManager->lastname ?? ' ', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $reportingManager->firstname }} {{ $reportingManager->lastname }}</h6>
                                            <small class="text-muted">{{ $reportingManager->employeeid }}</small>
                                            <div class="text-muted small">{{ $reportingManager->designation ?? 'Manager' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Progress Card -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Clearance Progress</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small">Completion</span>
                                            <span class="small">{{ $progressPercentage }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: {{ $progressPercentage }}%;"
                                                 aria-valuenow="{{ $progressPercentage }}"
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        @if($progressPercentage == 100)
                                            <span class="badge bg-success">
                                                <i class="fa fa-check-circle"></i> All clearances completed
                                            </span>
                                        @elseif($progressPercentage > 0)
                                            <span class="badge bg-info">
                                                <i class="fa fa-spinner"></i> In Progress
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fa fa-clock"></i> Not Started
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Offboarding Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ ucfirst($offboarding->offboarding_type) }} Details</h6>
                                    <span class="badge 
                                        @if($offboarding->status == 'inprogress') bg-info
                                        @elseif($offboarding->status == 'completed') bg-success
                                        @elseif($offboarding->status == 'rejected') bg-danger
                                        @elseif($offboarding->status == 'cancelled') bg-secondary
                                        @else bg-warning @endif">
                                        {{ ucfirst($offboarding->status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Employee ID</th>
                                                    <td>{{ $offboarding->employeeid }} {{ $offboarding->firstname }} {{ $offboarding->lastname }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Current Experience</th>
                                                    <td>{{ $experience }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Exit Type</th>
                                                    <td>
                                                        <span class="badge 
                                                            @if($offboarding->offboarding_type == 'resignation') bg-warning
                                                            @elseif($offboarding->offboarding_type == 'termination') bg-danger
                                                            @else bg-secondary @endif">
                                                            {{ ucfirst($offboarding->offboarding_type) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Request</th>
                                                    <td>{{ \Carbon\Carbon::parse($offboarding->created_at)->format('d-M-Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Last Working Date</th>
                                                    <td>
                                                        <strong>{{ $offboarding->last_working_date ? Carbon::parse($offboarding->last_working_date)->format('d-M-Y') : '-' }}</strong>
                                                        @if($lastWorkingDate !== null)
                                                            @if($daysRemaining > 0)
                                                                <span class="badge bg-info ms-2">{{ $daysRemaining }} day(s) remaining</span>
                                                            @elseif($daysRemaining == 0)
                                                                <span class="badge bg-warning ms-2">Today is last working day</span>
                                                            @else
                                                                <span class="badge bg-secondary ms-2">Completed {{ abs($daysRemaining) }} day(s) ago</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Login Disable Date</th>
                                                    <td>{{ $offboarding->login_disable_date ? Carbon::parse($offboarding->login_disable_date)->format('d-M-Y') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Reason</th>
                                                    <td>{{ $offboarding->reason ?? 'Not specified' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Explanation</th>
                                                    <td>{!! $offboarding->explanation ? nl2br(e($offboarding->explanation)) : '-' !!}</td>
                                                </tr>
                                                @if($offboarding->offboarding_type == 'deceased' && $offboarding->deceased_date)
                                                    <tr>
                                                        <th>Deceased Date</th>
                                                        <td>{{ Carbon::parse($offboarding->deceased_date)->format('d-M-Y') }}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>Replacement Required</th>
                                                    <td>
                                                        <span class="badge {{ $offboarding->replacement_required == 'Yes' ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $offboarding->replacement_required }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Employee Status</th>
                                                    <td>
                                                        <span class="badge 
                                                            @if($offboarding->employee_status == 'Active') bg-success
                                                            @elseif($offboarding->employee_status == 'Resigned') bg-warning
                                                            @elseif($offboarding->employee_status == 'Terminated') bg-danger
                                                            @elseif($offboarding->employee_status == 'Deceased') bg-secondary
                                                            @else bg-info @endif">
                                                            {{ $offboarding->employee_status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Attachment</th>
                                                    <td>
                                                        @if($offboarding->attachment_path)
                                                            <a href="{{ asset($offboarding->attachment_path) }}" 
                                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fa fa-download"></i> Download Attachment
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Created By</th>
                                                    <td>
                                                        @if($createdBy)
                                                            {{ $createdBy->firstname }} {{ $createdBy->lastname }} ({{ $createdBy->employeeid }})
                                                        @else
                                                            System
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- CLEARANCES TAB (FULL INTEGRATION) -->
                <div class="tab-pane fade" id="clearances">
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Clearance Processes</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClearanceModal">
                                <i class="fa fa-plus"></i> Add Task
                            </button>
                        </div>
                        <div class="card-body">

                            @if($clearances->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Task Name</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th>Assigned To</th>
                                                <th>Completed On</th>
                                                <th>Comments</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($clearances as $index => $clearance)
                                                @php
                                                    $assignee = $employees->firstWhere('id', $clearance->assigned_to);
                                                @endphp

                                                <tr id="clearance-row-{{ $clearance->id }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $clearance->task_name }}</td>
                                                    <td><span class="badge bg-light text-dark">{{ $clearance->department }}</span></td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($clearance->status == 'completed') bg-success
                                                            @elseif($clearance->status == 'pending') bg-warning
                                                            @else bg-danger
                                                            @endif">
                                                            {{ ucfirst($clearance->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $assignee ? $assignee->firstname.' '.$assignee->lastname : 'Not assigned' }}</td>
                                                    <td>{{ $clearance->completed_at ? Carbon::parse($clearance->completed_at)->format('d-M-Y h:i A') : '-' }}</td>
                                                    <td>{{ $clearance->comments ? Str::limit($clearance->comments, 40) : '-' }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary edit-clearance-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#updateClearanceModal"
                                                                    data-id="{{ $clearance->id }}"
                                                                    data-status="{{ $clearance->status }}"
                                                                    data-comments="{{ $clearance->comments }}">
                                                                <i class="fa fa-edit"></i>
                                                            </button>

                                                            <button type="button" 
                                                                    class="btn btn-outline-danger delete-clearance-btn"
                                                                    data-id="{{ $clearance->id }}">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Inline clearance details/edit forms (IT/HR/Admin) displayed below the table row --}}
                                                @if($clearance->clearance_type == 'it')
                                                    <tr class="clearance-form-row" id="it-form-{{ $clearance->id }}">
                                                        <td colspan="8">
                                                            <div class="card mb-0 border shadow-sm">
                                                                <div class="card-header bg-primary text-white">
                                                                    <div class="d-flex justify-content-between">
                                                                        <h6 class="mb-0">IT Clearance Details - {{ $clearance->task_name }}</h6>
                                                                        <div>
                                                                            <small class="text-white">{{ $clearance->department }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <form action="{{ route('offboarding.clearance.it', $clearance->id) }}" method="POST" class="card-body">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    @php $d = $clearance->details ?? (object)[]; @endphp

                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Laptop Returned *</label>
                                                                            <select name="laptop_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->laptop_returned) && $d->laptop_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Mobile Phone Returned *</label>
                                                                            <select name="mobile_phone_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->mobile_phone_returned) && $d->mobile_phone_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Keys Returned *</label>
                                                                            <select name="keys_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->keys_returned) && $d->keys_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Email Deactivated *</label>
                                                                            <select name="email_deactivated" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->email_deactivated) && $d->email_deactivated==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">System Access Revoked *</label>
                                                                            <select name="system_access_revoked" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->system_access_revoked) && $d->system_access_revoked==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">VPN Access Revoked *</label>
                                                                            <select name="vpn_access_revoked" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->vpn_access_revoked) && $d->vpn_access_revoked==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label class="form-label">Software Licenses Deactivated *</label>
                                                                            <select name="software_licenses_deactivated" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->software_licenses_deactivated) && $d->software_licenses_deactivated==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label class="form-label">Comments</label>
                                                                            <textarea name="comments" rows="3" class="form-control">{{ $d->comments ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-3 text-end">
                                                                        <button type="submit" class="btn btn-primary">Save IT Clearance</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if($clearance->clearance_type == 'hr')
                                                    <tr class="clearance-form-row" id="hr-form-{{ $clearance->id }}">
                                                        <td colspan="8">
                                                            <div class="card mb-0 border shadow-sm">
                                                                <div class="card-header bg-warning">
                                                                    <h6 class="mb-0">HR Clearance Details - {{ $clearance->task_name }}</h6>
                                                                </div>

                                                                <form action="{{ route('offboarding.clearance.hr', $clearance->id) }}" method="POST" class="card-body">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    @php $d = $clearance->details ?? (object)[]; @endphp

                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label>Exit Interview Completed *</label>
                                                                            <select name="exit_interview_completed" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->exit_interview_completed) && $d->exit_interview_completed==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Exit Interview Date</label>
                                                                            <input type="date" name="exit_interview_date" value="{{ $d->exit_interview_date ?? '' }}" class="form-control">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Exit Interview By</label>
                                                                            <input type="text" name="exit_interview_by" value="{{ $d->exit_interview_by ?? '' }}" class="form-control">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Final Paycheck Processed *</label>
                                                                            <select name="final_paycheck_processed" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->final_paycheck_processed) && $d->final_paycheck_processed==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Unused Vacation Compensated *</label>
                                                                            <select name="unused_vacation_compensated" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->unused_vacation_compensated) && $d->unused_vacation_compensated==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Benefits Terminated *</label>
                                                                            <select name="benefits_terminated" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->benefits_terminated) && $d->benefits_terminated==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Forms Signed *</label>
                                                                            <select name="forms_signed" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->forms_signed) && $d->forms_signed==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>NDA Signed *</label>
                                                                            <select name="nda_signed" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->nda_signed) && $d->nda_signed==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label>Comments</label>
                                                                            <textarea name="comments" rows="3" class="form-control">{{ $d->comments ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-3 text-end">
                                                                        <button type="submit" class="btn btn-warning">Save HR Clearance</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if($clearance->clearance_type == 'admin')
                                                    <tr class="clearance-form-row" id="admin-form-{{ $clearance->id }}">
                                                        <td colspan="8">
                                                            <div class="card mb-0 border shadow-sm">
                                                                <div class="card-header bg-secondary text-white">
                                                                    <h6 class="mb-0">Admin Clearance Details - {{ $clearance->task_name }}</h6>
                                                                </div>

                                                                <form action="{{ route('offboarding.clearance.admin', $clearance->id) }}" method="POST" class="card-body">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    @php $d = $clearance->details ?? (object)[]; @endphp

                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label>ID Badge Returned *</label>
                                                                            <select name="id_badge_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->id_badge_returned) && $d->id_badge_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Building Access Revoked *</label>
                                                                            <select name="building_access_revoked" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->building_access_revoked) && $d->building_access_revoked==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Access Cards Returned *</label>
                                                                            <select name="access_cards_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->access_cards_returned) && $d->access_cards_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label>Parking Permit Returned *</label>
                                                                            <select name="parking_permit_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->parking_permit_returned) && $d->parking_permit_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label>Other Property Returned *</label>
                                                                            <select name="other_property_returned" class="form-select" required>
                                                                                @foreach(['Yes','No','NA'] as $v)
                                                                                    <option value="{{ $v }}" {{ isset($d->other_property_returned) && $d->other_property_returned==$v ? 'selected' : '' }}>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label>Specify (if No)</label>
                                                                            <input type="text" name="other_property_specify" value="{{ $d->other_property_specify ?? '' }}" class="form-control">
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <label>Comments</label>
                                                                            <textarea name="comments" rows="3" class="form-control">{{ $d->comments ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-3 text-end">
                                                                        <button type="submit" class="btn btn-dark">Save Admin Clearance</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fa fa-tasks fa-3x text-muted mb-3"></i>
                                    <h5>No clearance tasks found</h5>
                                    <p class="text-muted">Add clearance tasks to track the offboarding process</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClearanceModal">
                                        <i class="fa fa-plus"></i> Add First Task
                                    </button>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <!-- TIMELINE TAB -->
                <div class="tab-pane fade" id="timeline">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Process Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1">Offboarding Request Created</h6>
                                            <small class="text-muted">{{ Carbon::parse($offboarding->created_at)->format('d M Y, h:i A') }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">Request initiated by 
                                            @if($createdBy)
                                                {{ $createdBy->firstname }} {{ $createdBy->lastname }}
                                            @else
                                                System
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @foreach($clearances->where('status', 'completed')->sortBy('completed_at') as $clearance)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">{{ $clearance->task_name }} Completed</h6>
                                                <small class="text-muted">{{ Carbon::parse($clearance->completed_at)->format('d M Y, h:i A') }}</small>
                                            </div>
                                            <p class="mb-0 text-muted">{{ $clearance->department }} Department</p>
                                            @if($clearance->comments)
                                                <small class="text-muted">Comments: {{ $clearance->comments }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if($offboarding->status == 'completed')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Offboarding Process Completed</h6>
                                                <small class="text-muted">{{ Carbon::parse($offboarding->updated_at)->format('d M Y, h:i A') }}</small>
                                            </div>
                                            <p class="mb-0 text-muted">All clearance processes completed successfully</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CAREER HISTORY TAB -->
                <div class="tab-pane fade" id="career-history">
                    <div class="career-page-container p-3">
                        <div class="career-wrapper d-flex gap-4">
                            <div class="career-profile-card card p-3" style="width:320px;">
                                <div class="text-center mb-3">
                                    <img src="{{ asset($employee->profile_image ?? 'admin/assets/img/user.jpg') }}" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:12px;">
                                </div>
                                <h3 class="text-center">{{ $employee->firstname }} {{ $employee->lastname }}</h3>
                                <p class="text-center text-muted">Employee ID: {{ $employee->employeeid }}</p>
                                <div class="career-profile-details p-3 bg-light rounded">
                                    <p><strong>Designation:</strong> {{ $employee->designation_name }}</p>
                                    <p><strong>Department:</strong> {{ $employee->department_name }}</p>
                                    <p><strong>Location:</strong> {{ $employee->branch_name }}</p>
                                    <p><strong>Joined On:</strong> {{ $employee->joiningdate ? date('d M Y', strtotime($employee->joiningdate)) : '-' }}</p>
                                </div>
                            </div>

                            <div class="career-timeline-container card p-3 flex-fill">
                                @php
                                    $grouped = collect($events)->groupBy(function($e){
                                        return date('Y', strtotime($e['date']));
                                    });
                                @endphp

                                @if(count($grouped))
                                    @foreach($grouped as $year => $yearEvents)
                                        <div class="mb-3 pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="badge bg-danger text-white" style="font-size:20px;padding:8px 18px;border-radius:10px;">{{ $year }}</div>
                                                <div><strong>{{ count($yearEvents) }} Event(s)</strong></div>
                                            </div>
                                        </div>

                                        <div class="career-timeline pb-3">
                                            @foreach($yearEvents as $ev)
                                                <div class="mb-4">
                                                    <div class="d-flex gap-3">
                                                        <div style="width:48px;height:48px;border-radius:50%;background:#ff7043;color:#fff;display:flex;align-items:center;justify-content:center;border:3px solid #fff;">
                                                            <i class="fa fa-briefcase"></i>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <div class="text-muted small"><i class="fa fa-calendar"></i> {{ date('d M Y', strtotime($ev['date'])) }}</div>
                                                            <h5 class="mb-1">{{ $ev['title'] }}</h5>
                                                            <div class="text-muted">{!! $ev['description'] !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="fa fa-history fa-3x mb-3"></i>
                                        <h4>No career events found</h4>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- tab-content -->

        </div> <!-- card-body -->
    </div> <!-- card -->
</div> <!-- container -->

<!-- ===================== MODALS ===================== -->

<!-- Add Clearance Modal (FIXED: includes clearance_type) -->
<div class="modal fade" id="addClearanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('offboarding.clearance.store', $offboarding->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Clearance Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Name *</label>
                        <input type="text" class="form-control" name="task_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Clearance Type *</label>
                        <select class="form-select" name="clearance_type" required>
                            <option value="">Select Type</option>
                            <option value="it">IT Clearance</option>
                            <option value="hr">HR Clearance</option>
                            <option value="admin">Admin Clearance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department *</label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <option value="IT">IT Department</option>
                            <option value="HR">HR Department</option>
                            <option value="Finance">Finance Department</option>
                            <option value="Admin">Admin Department</option>
                            <option value="Management">Management</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select class="form-select" name="assigned_to">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->employeeid }} - {{ $emp->firstname }} {{ $emp->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Clearance Modal (status + comments) -->
<div class="modal fade" id="updateClearanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateClearanceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Clearance Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" id="clearanceStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comments</label>
                        <textarea class="form-control" id="clearanceComments" name="comments" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     STYLES (small local styles)
============================================================= -->
<style>
.leave-tabs .nav-link { font-size: 15px; font-weight: 500; color: #333; padding: 10px 18px; border-bottom: 3px solid transparent; }
.leave-tabs .nav-link.active { color: rgb(249 115 22); border-bottom: 3px solid rgb(249 115 22); }
.tabs-underline { width: 100%; height: 2px; background: #e5eaf2; margin-top: -4px; margin-bottom: 12px; }
.avatar-placeholder { font-weight: bold; font-size: 14px; }
.timeline { position: relative; padding-left: 30px; }
.timeline-item { position: relative; padding-bottom: 30px; }
.timeline-marker { position: absolute; left: -30px; top: 0; width: 15px; height: 15px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px; }
.career-page-container { background: transparent; }
.career-profile-card { background: #fff; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,.04); }
.career-timeline-container { background: white; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,.04); }
.clearance-form-row td { background: #fbfbfb; }
</style>

<!-- =========================================================
     SCRIPTS
============================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Populate update modal when edit clicked
    const updateModal = document.getElementById('updateClearanceModal');
    const updateForm = document.getElementById('updateClearanceForm');

    document.querySelectorAll('.edit-clearance-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status') || 'pending';
            const comments = this.getAttribute('data-comments') || '';

            updateForm.action = `/offboarding/clearance/${id}`; // matches route: offboarding.clearance.update
            document.getElementById('clearanceStatus').value = status;
            document.getElementById('clearanceComments').value = comments;
        });
    });

    // Delete clearance with confirmation (AJAX)
    document.querySelectorAll('.delete-clearance-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const id = this.getAttribute('data-id');
            if (!confirm('Are you sure you want to delete this clearance task?')) return;

            fetch(`/offboarding/clearance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(res => res.json())
            .then(json => {
                if (json.success) {
                    // remove row(s)
                    const row = document.getElementById('clearance-row-' + id);
                    if (row) row.remove();
                    const formRowIt = document.getElementById('it-form-' + id);
                    if (formRowIt) formRowIt.remove();
                    const formRowHr = document.getElementById('hr-form-' + id);
                    if (formRowHr) formRowHr.remove();
                    const formRowAdmin = document.getElementById('admin-form-' + id);
                    if (formRowAdmin) formRowAdmin.remove();
                    alert('Task deleted.');
                } else {
                    alert('Failed to delete: ' + (json.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error deleting task.');
            });
        });
    });

    // Retain modal open on validation errors: Laravel will flash errors - we rely on server redirect back
    // If you want AJAX submission for add/update, tell me and I'll convert.

    // Initialize bootstrap tooltips (if any)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (t) { return new bootstrap.Tooltip(t) });
});
</script>

@endsection
