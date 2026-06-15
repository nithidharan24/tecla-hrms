@extends('layouts.index')

@section('content')

<!-- Page Content -->
<div class="content container-fluid">
				
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Profile</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
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
                            <!-- Displaying default image, you can add dynamic images if stored -->
                            <a href="#"><img src="{{ asset($employee->profile_image) }}" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;"></a>
                        </div>
                    </div>
                    <div class="profile-basic">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="profile-info-left">
                                    <h3 class="user-name m-t-0 mb-0">{{ $emp loyee->firstname }} {{ $employee->lastname }}</h3>
                                    <h6 class="text-muted">{{ $employee->department }}</h6>
                                    <small class="text-muted">{{ $employee->designation }}</small>
                                    <div class="staff-id">Employee ID: {{ $employee->employeeid }}</div>
                                    <div class="small doj text-muted">Date of Join: {{ \Carbon\Carbon::parse($employee->joiningdate)->format('jS M Y') }}</div>
                                    <div class="staff-msg"><a class="btn btn-custom" href="chat.html">Send Message</a></div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <ul class="personal-info">
                                    <li>
                                        <div class="title">Phone:</div>
                                        <div class="text"><a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a></div>
                                    </li>
                                    <li>
                                        <div class="title">Email:</div>
                                        <div class="text"><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></div>
                                    </li>
                                    <li>
                                        <div class="title">Birthday:</div>
                                        <div class="text">{{ \Carbon\Carbon::parse($employee->birthday)->format('jS M Y') }}</div>
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
                                <li class="nav-item"><a href="#bank_statutory" data-bs-toggle="tab" class="nav-link">Bank & Statutory <small class="text-danger">(Admin Only)</small></a></li>
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
        <a href="{{ route('employee.personal_info.edit', $employee->id) }}" class="edit-icon">
            <i class="fa-solid fa-pencil"></i>
        </a>
    </h3>
    <ul class="personal-info">
        <li>
            <div class="title">Passport No.</div>
            <div class="text">{{ $employee->passport_no}}</div>
        </li>
        <li>
            <div class="title">Passport Exp Date</div>
            <div class="text">{{ $employee->passport_exp_date }}</div>
        </li>
        <li>
            <div class="title">Tel</div>
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
        <li>
            <div class="title">Employment of spouse</div>
            <div class="text">{{ $employee->employment_of_spouse}}</div>
        </li>
        <li>
            <div class="title">No. of children</div>
            <div class="text">{{ $employee->no_of_children }}</div>
        </li>
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
        <a href="{{ route('employee.personal_info.edit', $employee->id) }}" class="edit-icon">
            <i class="fa-solid fa-pencil"></i>
        </a>
    </h3>
    <ul class="personal-info">
        <li>
            <div class="title">Passport No.</div>
            <div class="text">{{ $employee->passport_no}}</div>
        </li>
        <li>
            <div class="title">Passport Exp Date</div>
            <div class="text">{{ $employee->passport_exp_date }}</div>
        </li>
        <li>
            <div class="title">Tel</div>
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
        <li>
            <div class="title">Employment of spouse</div>
            <div class="text">{{ $employee->employment_of_spouse}}</div>
        </li>
        <li>
            <div class="title">No. of children</div>
            <div class="text">{{ $employee->no_of_children }}</div>
        </li>
    </ul>
</div>

                                </div>
                            </div>
                            <div class="col-md-6 d-flex">
                                <div class="card profile-box flex-fill">
                                <div class="card-body">
    <h3 class="card-title">Emergency Contact <a href="{{ route('employee.emergency_contact.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a></h3>
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
    <a href="{{ route('employee.bank_info.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a>
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
                                        <h3 class="card-title">Family Informations <a href="{{ route('family.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a></h3>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Relationship</th>
                                                        <th>Date of Birth</th>
                                                        <th>Phone</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
    @foreach($familyMembers as $family)
    <tr>
        <td>{{ $family->name }}</td>
        <td>{{ $family->relationship }}</td>
        <td>{{ \Carbon\Carbon::parse($family->date_of_birth)->format('F jS, Y') }}</td>
        <td>{{ $family->phone }}</td>
        <td class="text-end">
            <div class="dropdown dropdown-action">
                <a aria-expanded="false" data-bs-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="material-icons">more_vert</i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                   <!-- Trigger Delete Modal -->
            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteFamilyModal" 
               onclick="setDeleteFormAction('{{ route('family.delete', $family->id) }}')">
                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
            </a>
                </div>
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
                        </div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFamilyModal" tabindex="-1" aria-labelledby="deleteFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
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
                                    <h3 class="card-title">Education Informations <a href="{{ route('education.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a></h3>

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
                                    <h3 class="card-title">Education Informations <a href="{{ route('education.edit', $employee->id) }}" class="edit-icon"><i class="fa-solid fa-pencil"></i></a></h3>

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
                        </div>
                    </div>
                    <!-- /Profile Info Tab -->
                    
                    <!-- Projects Tab -->
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
                <p class="text-muted">{{ Str::limit($project->description, 100) }}</p>
                <div class="pro-deadline m-b-15">
                    <div class="sub-title">Start Date:</div>
                    <div class="text-muted">{{ $project->startdate }}</div>
                </div>
                <div class="pro-deadline m-b-15">
                    <div class="sub-title">End Date:</div>
                    <div class="text-muted">{{ $project->enddate }}</div>
                </div>
                <!-- Add any other project information here -->
                <div class="pro-deadline m-b-15">
                    <div class="sub-title">Priority:</div>
                    <div class="text-muted">{{ $project->priority }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

                    <!-- /Projects Tab -->
                    
                    <!-- Bank Statutory Tab -->
                                        
                    <!-- Bank Statutory Tab Content -->
            <div  class="pro-overview tab-pane fade show active" id="bank_statutory">
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
                                        <select name="salary_basis" class="select form-control">
                                            <option value="Hourly">Hourly</option>
                                            <option value="Daily">Daily</option>
                                            <option value="Weekly">Weekly</option>
                                            <option value="Monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Salary amount <small class="text-muted">per month</small></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" name="salary_amount" class="form-control" placeholder="Type your salary amount" value="{{ $bankStatutory->salary_amount }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Payment type</label>
                                        <select name="payment_type" class="select form-control">
                                            <option value="Bank transfer" {{ $bankStatutory->payment_type == 'Bank transfer' ? 'selected' : '' }}>Bank transfer</option>
                                            <option value="Check" {{ $bankStatutory->payment_type == 'Check' ? 'selected' : '' }}>Check</option>
                                            <option value="Cash" {{ $bankStatutory->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
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
                                        <select name="pf_contribution" class="select form-control">
                                            <option value="Yes" {{ $bankStatutory->pf_contribution == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $bankStatutory->pf_contribution == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">PF No. <span class="text-danger">*</span></label>
                                        <input type="text" name="pf_no" class="form-control" value="{{ $bankStatutory->pf_no }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Employee PF rate</label>
                                        <input type="text" name="employee_pf_rate" class="form-control" value="{{ $bankStatutory->employee_pf_rate }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Additional rate <span class="text-danger">*</span></label>
                                        <input type="text" name="additional_rate" class="form-control" value="{{ $bankStatutory->additional_rate }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Total rate</label>
                                        <input type="text" name="total_rate" class="form-control" value="{{ $bankStatutory->total_rate }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h3 class="card-title">ESI Information</h3>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">ESI contribution</label>
                                        <select name="esi_contribution" class="select form-control">
                                            <option value="Yes" {{ $bankStatutory->esi_contribution == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $bankStatutory->esi_contribution == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">ESI No. <span class="text-danger">*</span></label>
                                        <input type="text" name="esi_no" class="form-control" value="{{ $bankStatutory->esi_no }}">
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
                    <!-- /Bank Statutory Tab -->
                    
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
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <a href="assets-details.html" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 001</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage">
                                                <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>John Paul Raj</span>
                                                <p><span class="__cf_email__" data-cfemail="8ae0e5e2e4caeef8efebe7edfff3f9feefe9e2a4e9e5e7">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <a href="assets-details.html" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 002</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Vinod Selvaraj</span>
                                                <p><span class="__cf_email__" data-cfemail="d5a3bcbbbab1fba695b1a7b0b4b8b2a0aca6a1b0b6bdfbb6bab8">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            <a href="assets-details.html" class="table-imgname">
                                                <img src="assets/img/keyboard.png" class="me-2" alt="Keyboard Image">
                                                <span>Dell Keyboard</span>
                                            </a>
                                        </td>
                                        <td>AST - 003</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage" data-bs-toggle="modal" data-bs-target="#edit-asset">
                                                <img src="assets/img/profiles/avatar-03.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Harika </span>
                                                <p><span class="__cf_email__" data-cfemail="88e0e9fae1e3e9a6fec8ecfaede9e5effdf1fbfcedebe0a6ebe7e5">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/mouse.png" class="me-2" alt="Mouse Image">
                                                <span>Logitech Mouse</span>
                                            </a>
                                        </td>
                                        <td>AST - 0024</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="assets-details.html" class="table-profileimage">
                                                <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="assets-details.html" class="table-name">
                                                <span>Mythili</span>
                                                <p><span class="__cf_email__" data-cfemail="4e23373a262722270e2a3c2b2f23293b373d3a2b2d26602d2123">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 005</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="assets-details.html" class="table-profileimage">
                                                <img src="assets/img/profiles/avatar-02.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="assets-details.html" class="table-name">
                                                <span>John Paul Raj</span>
                                                <p><span class="__cf_email__" data-cfemail="a5cfcacdcbe5c1d7c0c4c8c2d0dcd6d1c0c6cd8bc6cac8">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
                                            <a href="#" class="table-imgname">
                                                <img src="assets/img/laptop.png" class="me-2" alt="Laptop Image">
                                                <span>Laptop</span>
                                            </a>
                                        </td>
                                        <td>AST - 006</td>
                                        <td>22 Nov, 2022    10:32AM</td>
                                        <td class="table-namesplit">
                                            <a href="javascript:void(0);" class="table-profileimage">
                                                <img src="assets/img/profiles/avatar-05.jpg" class="me-2" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="table-name">
                                                <span>Vinod Selvaraj</span>
                                                <p><span class="__cf_email__" data-cfemail="8ff9e6e1e0eba1fccfebfdeaeee2e8faf6fcfbeaece7a1ece0e2">[email&#160;protected]</span></p>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex">
                                                <a class="delete-table me-2" href="user-asset-details.html">
                                                   <img src="assets/img/icons/eye.svg" alt="Eye Icon">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /Assets -->
                    
                </div>
            </div>
            <!-- /Page Content -->
            
           
        </div>
        <!-- /Page Wrapper -->
