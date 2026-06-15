@php
    $role = Session::get('role');
@endphp
@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('employee.show', $employee->id) }}#documents" class="btn btn-primary me-2">
                    <i class="fa-solid fa-folder-open"></i> View Documents
                </a>
                <a href="{{ route('employee.history', $employee->id) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-clock-rotate-left"></i> History
                </a>
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
                                <!-- Displaying default image, you can add dynamic images if stored -->
                                <a href="#"><img src="{{ asset($employee->profile_image) }}" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;"></a>
                            </div>
                        </div>
                        <div class="profile-basic">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="profile-info-left">
                                        <h3 class="user-name m-t-0 mb-0">{{ $employee->firstname }} {{ $employee->lastname }}</h3>
                                        <h6 class="text-muted">{{ $employee->department }}</h6>
                                        <small class="text-muted">{{ $employee->designation }}</small>
                                        <div class="staff-id">Employee ID: {{ $employee->employeeid }}</div>
                                        <div class="small doj text-muted">Date of Join: {{ \Carbon\Carbon::parse($employee->joiningdate)->format('jS M Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Phone:</div>
                                            <div class="text"><a href="tel:{{ $employee->employee_phone }}">{{ $employee->employee_phone }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Email:</div>
                                            <div class="text"><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Date of Join:</div>
                                            <div class="text">
                                                {{ \Carbon\Carbon::parse($employee->joining_date)->format('jS M Y') }}
                                            </div>
                                        </li>
                                        
                                        <li>
                                            <div class="title">Birthday:</div>
                                            <div class="text">
                                                {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('jS M Y') : '' }}
                                            </div>
                                        </li>
                                        
                                        
                                        <li>
                                            <div class="title">Address:</div>
                                            <div class="text">{{ $employee->address }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Gender:</div>
                                            <div class="text">{{ ucfirst($employee->gender) }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="pro-edit">
                            <a class="edit-icon" href="{{ route('employeeprofile.edit', $employee->id) }}">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#emp_profile" data-bs-toggle="tab" class="nav-link active">Profile</a></li>
                    <li class="nav-item"><a href="#emp_projects" data-bs-toggle="tab" class="nav-link">Projects</a></li>
                   
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
                            <h3 class="card-title">Personal Information
                                @if(
                                    (Session::get('role') === 'employee' && $employee->personal_info_edited == 0) ||
                                    (Session::get('role') === 'admin')
                                )
                                
                                <a href="{{ route('employee.personal_info.edit', $employee->id) }}" class="edit-icon">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                @endif
                            </h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Passport No.</div>
                                    <div class="text">{{ $employee->passport_no}}</div>
                                </li>
                                <li>
                                    <div class="title">Aadhaar Number</div>
                                    <div class="text">{{ $employee->passport_exp_date }}</div>
                                </li>
                                <li>
                                    <div class="title">Blood group</div>
                                    <div class="text"><a href="tel:{{ $employee->tel }}">{{ $employee->tel}}</a></div>
                                </li>
                                <li>
                                    <div class="title">Nationality</div>
                                    <div class="text">{{ $employee->nationality}}</div>
                                </li>
                                <li>
                                    <div class="title">Religion</div>
                                    <div class="text">{{ $employee->religion}}</div>
                                </li>
                                <li>
                                    <div class="title">Marital status</div>
                                    <div class="text">{{ $employee->marital_status}}</div>
                                </li>
                               
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Emergency Contact   </h3>
                            @if(
                                (Session::get('role') === 'employee' && $employee->employee_emergency_edited == 0) ||
                                (Session::get('role') === 'admin')
                            )
                                <a href="{{ route('employee.emergency_contact.edit', $employee->id ?? 0) }}" class="edit-icon">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            @endif
                            
                               
                           
                          
                            <h5 class="section-title">Primary</h5>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $employee->primary_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $employee->relationship }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone</div>
                                    <div class="text">{{ $employee->phone }}</div>
                                </li>
                            </ul>
                            <hr>
                            <h5 class="section-title">Secondary</h5>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $employee->secondary_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $employee->secondary_relationship }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone</div>
                                    <div class="text">{{ $employee->secondary_phone }}</div>
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
                            @if(
                                (Session::get('role') === 'employee' && $employee->employee_bank_edited == 0) ||
                                (Session::get('role') === 'admin')
                            )
                            <a href="{{ route('employee.bank_info.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a>
                            @endif
                            <h3 class="card-title">Bank Information</h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Bank Name</div>
                                    <div class="text">{{ $employee->bank_name }}</div>
                                </li>
                                <li>
                                    <div class="title">Bank Account No.</div>
                                    <div class="text">{{ $employee->bank_account_no }}</div>
                                </li>
                                <li>
                                    <div class="title">IFSC Code</div>
                                    <div class="text">{{ $employee->ifsc_code }}</div>
                                </li>
                                <li>
                                    <div class="title">PAN No</div>
                                    <div class="text">{{ $employee->pan_no }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title d-flex justify-content-between align-items-center">
                                Family Informations
                                @if((Session::get('role') === 'employee' && $family_info_edited == 0) || (Session::get('role') === 'admin'))
                                    <a href="{{ route('family.edit', $employee->id) }}" class="edit-icon">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                @endif
                            </h3>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Name</th>
                                            <th>Relationship</th>
                                            <th>Phone</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($familyMembers as $family)
                                        <tr>
                                            <td>{{ $family->name }}</td>
                                            <td class="text-center">{{ $family->relationship }}</td>
                                            <td class="text-center">{{ $family->phone }}</td>
                                            <td class="text-center">
                                                @if((Session::get('role') === 'employee' && $family_info_edited == 0) || Session::get('role') === 'admin')
                                                <div class="dropdown dropdown-action">
                                                    <a class="action-icon dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="material-icons">more_vert</i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteFamilyModal"
                                                           onclick="setDeleteFormAction('{{ route('family.delete', $family->id) }}')">
                                                            <i class="fa-regular fa-trash-can me-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No family members found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteFamilyModal" tabindex="-1" aria-labelledby="deleteFamilyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <!-- Centered modal -->
                    <div class="modal-content" style="border-radius: 15px;">
                        <div class="modal-header border-0">
                            <h5 class="modal-title w-100 text-center" id="deleteFamilyModalLabel" style="font-weight: bold;">Delete Family Member</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            Are you sure you want to delete this family member?
                        </div>
                        <div class="modal-footer d-flex justify-content-around border-0">
                            <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                            <form id="deleteFamilyForm" method="POST" action="" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Education Informations </h3>
                            
                            @if(
                                (Session::get('role') === 'employee' && ($education_info_edited ?? 0) == 0) ||
                                (Session::get('role') === 'admin')
                            )
                                <a href="{{ route('education.edit', $employee->id) }}" class="edit-icon">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                            @endif
                            
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
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Experience </h3>
                                @if(
    (Session::get('role') === 'employee' && $experience_info_edited == 0) ||
    (Session::get('role') === 'admin')
)<a href="{{ route('employee.experience.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a>
@endif
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
                                                
                                                <span class="time">
                                                    @if(!empty($experience->period_from) && !empty($experience->period_to))
                                                        {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($experience->period_to)->format('M Y') }}
                                                    @elseif(!empty($experience->period_from) && empty($experience->period_to))
                                                        {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }} - Present
                                                    @elseif(empty($experience->period_from) && !empty($experience->period_to))
                                                        {{ \Carbon\Carbon::parse($experience->period_to)->format('M Y') }}
                                                    @endif
                                                </span>
                                                
                                                
                                                
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Info Tab -->
        <!-- Projects Tab Content -->
        <div class="tab-pane fade" id="emp_projects">
            <div class="row">
                @foreach($projects as $project)
                <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown profile-action">
                                <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-bs-target="#edit_project" data-bs-toggle="modal" href="#" class="dropdown-item">
                                        <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                    </a>
                                    <a data-bs-target="#delete_project" data-bs-toggle="modal" href="#" class="dropdown-item">
                                        <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                    </a>
                                </div>
                            </div>
                            <h4 class="project-title">
                                <a href="project-view.html">{{ $project->projectname }}</a>
                            </h4>
                            <small class="block text-ellipsis m-b-15">
                                <span class="text-xs">Client: </span>
                                <span class="text-muted">{{ $project->client }}</span>
                            </small>
                            <p class="text-muted">{{ Str::limit(strip_tags($project->description), 100) }}</p>

                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">Start Date:</div>
                                <div class="text-muted">{{ $project->startdate }}</div>
                            </div>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">End Date:</div>
                                <div class="text-muted">{{ $project->enddate }}</div>
                            </div>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">Priority:</div>
                                <div class="text-muted">{{ $project->priority }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!-- Bank Statutory Tab Content -->
        <div class="tab-pane fade" id="bank_statutory">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Basic Salary Information</h3>
                    <form action="{{ route('bank_statutory.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Salary basis <span class="text-danger">*</span></label>
                                    <select name="salary_basis" class="form-control">
                                        <option value="Hourly" {{ ($bankStatutory->salary_basis ?? '') == 'Hourly' ? 'selected' : '' }}>Hourly</option>
                                        <option value="Daily" {{ ($bankStatutory->salary_basis ?? '') == 'Daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="Weekly" {{ ($bankStatutory->salary_basis ?? '') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="Monthly" {{ ($bankStatutory->salary_basis ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Salary amount <small class="text-muted">per month</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" name="salary_amount" class="form-control" placeholder="Type your salary amount" value="{{ $bankStatutory->salary_amount ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Payment type</label>
                                    <select name="payment_type" class="form-control">
                                        <option value="Bank transfer" {{ ($bankStatutory->payment_type ?? '') == 'Bank transfer' ? 'selected' : '' }}>Bank transfer</option>
                                        <option value="Check" {{ ($bankStatutory->payment_type ?? '') == 'Check' ? 'selected' : '' }}>Check</option>
                                        <option value="Cash" {{ ($bankStatutory->payment_type ?? '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h3 class="card-title">PF Information</h3>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">PF contribution</label>
                                    <select name="pf_contribution" class="form-control">
                                        <option value="Yes" {{ ($bankStatutory->pf_contribution ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ ($bankStatutory->pf_contribution ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">PF No. <span class="text-danger">*</span></label>
                                    <input type="text" name="pf_no" class="form-control" value="{{ $bankStatutory->pf_no ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Employee PF rate</label>
                                    <select name="employee_pf_rate" class="form-control" id="employee_pf_rate">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}%" {{ ($bankStatutory->employee_pf_rate ?? '') == $i . '%' ? 'selected' : '' }}>{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                                    <select name="additional_rate" class="form-control" id="additional_rate_pf">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}%" {{ ($bankStatutory->additional_rate ?? '') == $i . '%' ? 'selected' : '' }}>{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Total PF rate</label>
                                    <input type="text" name="total_rate_pf" class="form-control" id="total_rate_pf" readonly value="{{ $bankStatutory->total_rate ?? '0%' }}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h3 class="card-title">ESI Information</h3>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">ESI contribution</label>
                                    <select name="esi_contribution" class="form-control">
                                        <option value="Yes" {{ ($bankStatutory->esi_contribution ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ ($bankStatutory->esi_contribution ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">ESI No. <span class="text-danger">*</span></label>
                                    <input type="text" name="esi_no" class="form-control" value="{{ $bankStatutory->esi_no ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Employee ESI rate</label>
                                    <select name="employee_esi_rate" class="form-control" id="employee_esi_rate">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}%" {{ ($bankStatutory->employee_esi_rate ?? '') == $i . '%' ? 'selected' : '' }}>{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Additional ESI rate <span class="text-danger">*</span></label>
                                    <select name="esi_additional_rate" class="form-control" id="additional_rate_esi">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}%" {{ ($bankStatutory->esi_additional_rate ?? '') == $i . '%' ? 'selected' : '' }}>{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Total ESI rate</label>
                                    <input type="text" name="esi_total_rate" class="form-control" id="total_rate_esi" readonly value="{{ $bankStatutory->total_esi_rate ?? '0%' }}">
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Assets -->
        <div class="tab-pane fade" id="emp_assets">
            <div class="table-responsive table-newdatatable">
                <table class="table table-new custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Asset ID</th>
                            <th>Assigned Date</th>
                            <th>Assignee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companyAssets as $key => $asset)
                        <tr>
                            <td data-label="S.No">{{ $key + 1 }}</td>

<td data-label="Asset Name">
    <a href="{{ route('assets.details', $asset->id) }}" target="_blank" class="table-imgname">
        <span>{{ $asset->asset_name }}</span>
    </a>
</td>

<td data-label="Asset ID">{{ $asset->asset_id }}</td>

<td data-label="Purchase Date">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d M, Y h:i A') }}</td>

<td data-label="Assigned Employee" class="table-namesplit">
    <a href="javascript:void(0);" class="table-profileimage">
    </a>
    <a href="javascript:void(0);" class="table-name">
        <span>{{ $employee->firstname }} {{ $employee->lastname }}</span>
    </a>
</td>

<td data-label="Actions">
    <div class="table-actions d-flex">
        <a class="delete-table me-2" href="{{ route('assets.details', $asset->id) }}">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
<!-- Experience Modal -->
</div>
<!-- /Page Wrapper -->
<script>
    function setDeleteFormAction(actionUrl) {
        document.getElementById('deleteFamilyForm').action = actionUrl;
    }
    document.addEventListener('DOMContentLoaded', function() {
        // PF Rate Calculation
        let pfRate = document.getElementById('employee_pf_rate');
        let additionalPfRate = document.getElementById('additional_rate_pf');
        let totalPfRate = document.getElementById('total_rate_pf');

        function calculateTotalPfRate() {
            // Get the values of the selected options
            let pfValue = parseFloat(pfRate.value.replace('%', '').trim()) || 0;
            let additionalPfValue = parseFloat(additionalPfRate.value.replace('%', '').trim()) || 0;
            // Calculate and update the total PF rate
            totalPfRate.value = (pfValue + additionalPfValue) + '%';
        }
        // Add event listeners to both dropdowns for immediate calculation
        pfRate.addEventListener('input', calculateTotalPfRate);
        additionalPfRate.addEventListener('input', calculateTotalPfRate);
        // Trigger calculation on page load
        calculateTotalPfRate();

        // ESI Rate Calculation
        let esiRate = document.getElementById('employee_esi_rate');
        let additionalEsiRate = document.getElementById('additional_rate_esi');
        let totalEsiRate = document.getElementById('total_rate_esi');

        function calculateTotalEsiRate() {
            // Get the values of the selected options
            let esiValue = parseFloat(esiRate.value.replace('%', '').trim()) || 0;
            let additionalEsiValue = parseFloat(additionalEsiRate.value.replace('%', '').trim()) || 0;
            // Calculate and update the total ESI rate
            totalEsiRate.value = (esiValue + additionalEsiValue) + '%';
        }
        // Add event listeners to both dropdowns for immediate calculation
        esiRate.addEventListener('input', calculateTotalEsiRate);
        additionalEsiRate.addEventListener('input', calculateTotalEsiRate);
        // Trigger calculation on page load
        calculateTotalEsiRate();
    });
</script>
@endsection
