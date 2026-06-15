@php
    $role = Session::get('role');
@endphp
@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Terminated Employee Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('terminations.index') }}">Terminations</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-view">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#"><img src="{{ asset($termination->profile_image) }}" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;"></a>
                            </div>
                            <!-- Display terminated badge -->
                            <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px;">Terminated</span>
                        </div>
                        <div class="profile-basic">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="profile-info-left">
                                        <h3 class="user-name m-t-0 mb-0">{{ $termination->firstname }} {{ $termination->lastname }}</h3>
                                        <h6 class="text-muted">{{ $termination->department_name }}</h6>
                                        <small class="text-muted">{{ $termination->designation_name }}</small>
                                        <div class="staff-id">Employee ID: {{ $termination->employeeid }}</div>
                                        <div class="small doj text-muted">Date of Join: {{ \Carbon\Carbon::parse($termination->joiningdate)->format('jS M Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Phone:</div>
                                            <div class="text"><a href="tel:{{ $termination->phone }}">{{ $termination->phone }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Email:</div>
                                            <div class="text"><a href="mailto:{{ $termination->email }}">{{ $termination->email }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Termination Date:</div>
                                            <div class="text">
                                                <strong>{{ \Carbon\Carbon::parse($termination->termination_date)->format('jS M Y') }}</strong>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">Termination Type:</div>
                                            <div class="text">
                                                <span class="badge bg-info">{{ $termination->termination_type }}</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">Notice Date:</div>
                                            <div class="text">{{ \Carbon\Carbon::parse($termination->notice_date)->format('jS M Y') }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Termination Reason:</div>
                                            <div class="text">{{ $termination->reason }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#emp_profile" data-bs-toggle="tab" class="nav-link active">Profile</a></li>
                    <li class="nav-item"><a href="#past_salary" data-bs-toggle="tab" class="nav-link">Past Salary Records</a></li>
                    <li class="nav-item"><a href="#emp_assets" data-bs-toggle="tab" class="nav-link">Assets</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Personal Information</h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Passport No.</div>
                                    <div class="text">{{ $personalInfo->passport_no ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Nationality</div>
                                    <div class="text">{{ $personalInfo->nationality ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Religion</div>
                                    <div class="text">{{ $personalInfo->religion ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Marital Status</div>
                                    <div class="text">{{ $personalInfo->marital_status ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Gender</div>
                                    <div class="text">{{ ucfirst($employeeProfile->gender ?? 'N/A') }}</div>
                                </li>
                                <li>
                                    <div class="title">Date of Birth</div>
                                    <div class="text">
                                        {{ $employeeProfile->birthday ? \Carbon\Carbon::parse($employeeProfile->birthday)->format('jS M Y') : 'N/A' }}
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Emergency Contact</h3>
                            @if($emergencyContact)
                            <h5 class="section-title">Primary</h5>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $emergencyContact->primary_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $emergencyContact->relationship }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone</div>
                                    <div class="text">{{ $emergencyContact->phone }}</div>
                                </li>
                            </ul>
                            @if($emergencyContact->secondary_name)
                            <hr>
                            <h5 class="section-title">Secondary</h5>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $emergencyContact->secondary_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $emergencyContact->secondary_relationship }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone</div>
                                    <div class="text">{{ $emergencyContact->secondary_phone }}</div>
                                </li>
                            </ul>
                            @endif
                            @else
                            <p class="text-muted">No emergency contact on file.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Bank Information</h3>
                            @if($bankInfo)
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Bank Name</div>
                                    <div class="text">{{ $bankInfo->bank_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Bank Account No.</div>
                                    <div class="text">{{ $bankInfo->bank_account_no }}</div>
                                </li>
                                <li>
                                    <div class="title">IFSC Code</div>
                                    <div class="text">{{ $bankInfo->ifsc_code }}</div>
                                </li>
                                <li>
                                    <div class="title">PAN No</div>
                                    <div class="text">{{ $bankInfo->pan_no }}</div>
                                </li>
                            </ul>
                            @else
                            <p class="text-muted">No bank information on file.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Address Information</h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Address</div>
                                    <div class="text">{{ $employeeProfile->address ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">State</div>
                                    <div class="text">{{ $employeeProfile->state ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Country</div>
                                    <div class="text">{{ $employeeProfile->country ?? 'N/A' }}</div>
                                </li>
                                <li>
                                    <div class="title">Pin Code</div>
                                    <div class="text">{{ $employeeProfile->pin_code ?? 'N/A' }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Family Information</h3>
                            @if($familyMembers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Name</th>
                                            <th>Relationship</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($familyMembers as $family)
                                        <tr>
                                            <td>{{ $family->name }}</td>
                                            <td class="text-center">{{ $family->relationship }}</td>
                                            <td class="text-center">{{ $family->phone }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No family members on record.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Education Information</h3>
                            @if($educationInfos->count() > 0)
                            <div class="experience-box">
                                <ul class="experience-list">
                                    @foreach($educationInfos as $education)
                                    <li>
                                        <div class="experience-user">
                                            <div class="before-circle"></div>
                                        </div>
                                        <div class="experience-content">
                                            <div class="timeline-content">
                                                <a href="#/" class="name">{{ $education->institution }}</a>
                                                <div>{{ $education->degree }}</div>
                                                <span class="time">{{ $education->start_date }} - {{ $education->end_date }}</span>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @else
                            <p class="text-muted">No education records.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Experience Information</h3>
                            @if($experienceInfos->count() > 0)
                            <div class="experience-box">
                                <ul class="experience-list">
                                    @foreach($experienceInfos as $experience)
                                    <li>
                                        <div class="experience-user">
                                            <div class="before-circle"></div>
                                        </div>
                                        <div class="experience-content">
                                            <div class="timeline-content">
                                                <a href="#/" class="name">
                                                    @if(!empty($experience->position) && !empty($experience->company_name))
                                                        {{ $experience->position }} at {{ $experience->company_name }}
                                                    @elseif(!empty($experience->position))
                                                        {{ $experience->position }}
                                                    @elseif(!empty($experience->company_name))
                                                        {{ $experience->company_name }}
                                                    @endif
                                                </a>
                                                <div>{{ $experience->location ?? '' }}</div>
                                                <span class="time">
                                                    @if(!empty($experience->period_from) && !empty($experience->period_to))
                                                        {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }} - {{ \Carbon\Carbon::parse($experience->period_to)->format('M Y') }}
                                                    @elseif(!empty($experience->period_from))
                                                        {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }} - Present
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @else
                            <p class="text-muted">No experience records.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Info Tab -->

        <!-- Past Salary Records Tab -->
        <div id="past_salary" class="pro-overview tab-pane fade">
            <div class="row">
                <div class="col-md-12 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Past Salary Records & Statutory Information</h3>
                            
                            @if($pastSalaryRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table custom-table table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Salary Basis</th>
                                            <th>Salary Amount</th>
                                            <th>Payment Type</th>
                                            <th>PF Contribution</th>
                                            <th>PF No.</th>
                                            <th>PF Rate</th>
                                            <th>ESI Contribution</th>
                                            <th>ESI No.</th>
                                            <th>ESI Rate</th>
                                            <th>Last Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pastSalaryRecords as $record)
                                        <tr>
                                            <td data-label="Salary Basis">{{ $record->salary_basis ?? 'N/A' }}</td>
                                            <td data-label="Salary Amount">
                                                <strong>₹ {{ number_format($record->salary_amount, 2) ?? 'N/A' }}</strong>
                                            </td>
                                            <td data-label="Payment Type">{{ $record->payment_type ?? 'N/A' }}</td>
                                            <td data-label="PF Contribution">
                                                <span class="badge {{ $record->pf_contribution === 'Yes' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $record->pf_contribution ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td data-label="PF No.">{{ $record->pf_no ?? '-' }}</td>
                                            <td data-label="PF Rate">
                                                <span class="badge bg-info">{{ $record->employee_pf_rate ?? '0%' }} + {{ $record->additional_rate ?? '0%' }} = {{ $record->total_rate ?? '0%' }}</span>
                                            </td>
                                            <td data-label="ESI Contribution">
                                                <span class="badge {{ $record->esi_contribution === 'Yes' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $record->esi_contribution ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td data-label="ESI No.">{{ $record->esi_no ?? '-' }}</td>
                                            <td data-label="ESI Rate">
                                                <span class="badge bg-warning">{{ $record->employee_esi_rate ?? '0%' }} + {{ $record->esi_additional_rate ?? '0%' }} = {{ $record->total_esi_rate ?? '0%' }}</span>
                                            </td>
                                            <td data-label="Last Updated">
                                                {{ $record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('d M Y H:i') : 'N/A' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <strong>No salary records found</strong> for this terminated employee.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Past Salary Records Tab -->

        <!-- Assets Tab -->
        <div id="emp_assets" class="pro-overview tab-pane fade">
            <div class="table-responsive table-newdatatable">
                <table class="table table-new custom-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Asset Name</th>
                            <th>Asset ID</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Asset information is archived separately. Please refer to Asset Management module for complete details.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-md-12">
            <a href="{{ route('terminations.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Terminations
            </a>
        </div>
    </div>
</div>
<!-- /Page Content -->

@endsection
