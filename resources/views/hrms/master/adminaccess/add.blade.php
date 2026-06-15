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

                    <h2 class="pageheader-title">Add Admin</h2>

                    <div class="page-breadcrumb">

                        <nav aria-label="breadcrumb">

                            <ol class="breadcrumb">

                                <li class="breadcrumb-item"><a href="{{ route('adminaccess.index') }}" class="breadcrumb-link">Admin List</a></li>

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



        <!-- Validation Errors -->

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

                    <h4 class="card-header">Add Admin</h4>

                    <div class="card-body">

                        <form id="adminForm" method="POST" action="{{ route('adminaccess.store') }}"  autocomplete="off" class="needs-validation" novalidate>

                            @csrf

                            <div class="row">

                                <!-- Name -->

                                <div class="col-md-6 mb-3">

                                    <label for="name">Name <span class="text-danger">*</span></label>

                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Full Name" value="{{ old('name') }}" required>

                                    <div class="invalid-feedback">Name is required and must be at least 3 characters long.</div>

                                    @error('name')

                                        <div class="text-danger">{{ $message }}</div>

                                    @enderror

                                </div>



                                <!-- Email -->

                                <div class="col-md-6 mb-3">

                                    <label for="email">Email <span class="text-danger">*</span></label>

                                    <input type="email"
                                    name="email"
                                    id="email"
                                    autocomplete="new-email"
                                    class="form-control">
                                    <div class="invalid-feedback" id="emailError">Valid email is required.</div>

                                    @error('email')

                                        <div class="text-danger">{{ $message }}</div>

                                    @enderror

                                </div>



                                <!-- Password -->

                                <div class="col-md-6 mb-3">

                                    <label for="password">Password <span class="text-danger">*</span></label>

                                    <input type="password"
                                    name="password"
                                    id="password"
                                    autocomplete="new-password"
                                    class="form-control">
                                    <div class="invalid-feedback">Password is required and must be at least 6 characters long.</div>

                                    @error('password')

                                        <div class="text-danger">{{ $message }}</div>

                                    @enderror

                                </div>



                                <!-- Branch Dropdown -->

                                <div class="col-md-6 mb-3">

                                    <label for="branch">Branch</label>

                                    <select id="branch" name="branch_id" class="form-control">

                                        <option value="">Select Branch</option>

                                        @foreach($branches as $branch)

                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>

                                                {{ $branch->name }} - {{ $branch->address }}

                                            </option>

                                        @endforeach

                                    </select>

                                    @error('branch_id')

                                        <div class="text-danger">{{ $message }}</div>

                                    @enderror

                                </div>





                                <!-- Module Access Permissions -->

<div class="col-md-12 mb-4">

    <div class="card">

        <div class="card-header bg-light d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0">Module Access Permissions</h5>

                <p class="text-muted mb-0">Modules will be automatically selected based on hierarchy level</p>

            </div>

            <div>

                <span class="badge bg-primary" id="hierarchyModuleCount">0 from hierarchy</span>

                <span class="badge bg-success" id="manualModuleCount">0 manual</span>

                <span class="badge bg-info" id="totalModuleCount">0 total</span>

            </div>

        </div>

        <div class="card-body">

            <div class="row">

                <!-- Search box -->

                <div class="col-md-12 mb-3">

                    <div class="input-group">

                        <span class="input-group-text"><i class="fas fa-search"></i></span>

                        <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">

                        <button class="btn btn-outline-secondary" type="button" id="selectAllBtn">Select All</button>

                        <button class="btn btn-outline-secondary" type="button" id="deselectAllBtn">Deselect All</button>

                        <button class="btn btn-outline-info" type="button" id="showHierarchyModulesBtn">Show Hierarchy Modules</button>

                    </div>

                </div>

                

                @if(in_array('Recruitment Management', $allowedModules))



                <!-- Recruitment -->

                <div class="col-lg-6 mb-3">

                    <div class="category-card h-100">

                        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">

                            <div class="d-flex align-items-center">

                                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                                    <i class="fas fa-user-plus"></i>

                                </div>

                                <div>

                                    <h6 class="fw-semibold mb-0">Recruitment</h6>

                                    <small class="text-muted">Hiring & onboarding processes</small>

                                </div>

                            </div>

                        </div>

                

                        <div class="category-body p-3">

                

                            <!-- Sub Module : Recruitment -->

                            <div class="module-item">

                                <div class="form-check">

                                    <input class="form-check-input module-checkbox" 

                                           type="checkbox" 

                                           id="module_recruitment" 

                                           name="modules[]" 

                                           value="Recruitment">

                                    <label class="form-check-label fw-semibold" for="module_recruitment">

                                        Recruitment

                                        <span class="hierarchy-indicator" style="display: none;">

                                            <i class="fas fa-sitemap text-primary" title="From Hierarchy"></i>

                                        </span>

                                    </label>

                                </div>

                            </div>

                

                        </div>

                    </div>

                </div>

                

                @endif

                @if(in_array('Leaves', $allowedModules))



<!-- Leaves Management -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-success bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-calendar-alt"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Leaves Management</h6>

                    <small class="text-muted">Leave requests & approvals</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Team Leaves -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_team_leaves" 

                           name="modules[]" 

                           value="Team Leaves">

                    <label class="form-check-label fw-semibold" for="module_team_leaves">

                        Team Leaves

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Employee Leaves -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_employee_leaves" 

                           name="modules[]" 

                           value="Employee Leaves">

                    <label class="form-check-label fw-semibold" for="module_employee_leaves">

                        Employee Leaves

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Attendance', $allowedModules))



<!-- Attendance -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-clock"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Attendance</h6>

                    <small class="text-muted">Time tracking & punch management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Admin Attendance -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_admin_attendance" 

                           name="modules[]" 

                           value="Admin Attendance">

                    <label class="form-check-label fw-semibold" for="module_admin_attendance">

                        Admin Attendance

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Employee Attendance -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_employee_attendance" 

                           name="modules[]" 

                           value="Employee Attendance">

                    <label class="form-check-label fw-semibold" for="module_employee_attendance">

                        Employee Attendance

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Late Punch Approval -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_late_punch" 

                           name="modules[]" 

                           value="Late Punch Approval">

                    <label class="form-check-label fw-semibold" for="module_late_punch">

                        Late Punch Approval

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Time Tracker', $allowedModules))



<!-- Time Tracker -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-warning bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-stopwatch"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Time Tracker</h6>

                    <small class="text-muted">Project time & task management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Clients -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_clients"

                           name="modules[]"

                           value="Clients">

                    <label class="form-check-label fw-semibold" for="module_clients">

                        Clients

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Projects -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_projects"

                           name="modules[]"

                           value="Projects">

                    <label class="form-check-label fw-semibold" for="module_projects">

                        Projects

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Project Tasks -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_project_tasks"

                           name="modules[]"

                           value="Project Tasks">

                    <label class="form-check-label fw-semibold" for="module_project_tasks">

                        Project Tasks

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- My Tasks -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_my_tasks"

                           name="modules[]"

                           value="My Tasks">

                    <label class="form-check-label fw-semibold" for="module_my_tasks">

                        My Tasks

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Onboarding', $allowedModules))



<!-- Employee Management -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-users"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Employee Management</h6>

                    <small class="text-muted">Employee data & profiles</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Employee (Sub Module) -->

            <div class="module-item">

                <div class="form-check">

                    <input class="form-check-input module-checkbox" 

                           type="checkbox" 

                           id="module_employee" 

                           name="modules[]" 

                           value="Employee">

                    <label class="form-check-label fw-semibold" for="module_employee">

                        Employee

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Shifts', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-user-clock"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Shifts</h6>

                    <small class="text-muted">Shift management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_shifts"

                       name="modules[]"

                       value="Shifts">

                <label class="form-check-label fw-semibold" for="module_shifts">

                    Shifts

                    <span class="hierarchy-indicator" style="display: none;">

                        <i class="fas fa-sitemap text-primary"></i>

                    </span>

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Schedule', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-calendar-alt"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Schedule</h6>

                    <small class="text-muted">Work scheduling</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_schedule"

                       name="modules[]"

                       value="Schedule">

                <label class="form-check-label fw-semibold" for="module_schedule">

                    Schedule

                    <span class="hierarchy-indicator" style="display: none;">

                        <i class="fas fa-sitemap text-primary"></i>

                    </span>

                </label>

            </div>

        </div>

    </div>

</div>



@endif



@if(in_array('Payroll', $allowedModules))



<!-- Payroll -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-dark bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-money-bill"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Payroll</h6>

                    <small class="text-muted">Salary & compensation management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Payroll Items -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_payroll_items"

                           name="modules[]"

                           value="Payroll Items">

                    <label class="form-check-label fw-semibold" for="module_payroll_items">

                        Payroll Items

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Employee Salary -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_employee_salary"

                           name="modules[]"

                           value="Employee Salary">

                    <label class="form-check-label fw-semibold" for="module_employee_salary">

                        Employee Salary

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Automated Payslips -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_automated_payslips"

                           name="modules[]"

                           value="Automated Payslips">

                    <label class="form-check-label fw-semibold" for="module_automated_payslips">

                        Automated Payslips

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Activity Log -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_activity_log"

                           name="modules[]"

                           value="Activity Log">

                    <label class="form-check-label fw-semibold" for="module_activity_log">

                        Activity Log

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Tickets', $allowedModules))



<!-- Tickets -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-ticket-alt"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Tickets</h6>

                    <small class="text-muted">Support tickets management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Tickets Submodule -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_tickets"

                           name="modules[]"

                           value="Tickets">

                    <label class="form-check-label fw-semibold" for="module_tickets">

                        Tickets

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Testing', $allowedModules))



<!-- Testing -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-vial"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Testing</h6>

                    <small class="text-muted">Quality testing module</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Testing Submodule -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_testing"

                           name="modules[]"

                           value="Testing">

                    <label class="form-check-label fw-semibold" for="module_testing">

                        Testing

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Accounts', $allowedModules))



<!-- Accounts -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-file-invoice-dollar"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Accounts</h6>

                    <small class="text-muted">Financial management & accounting</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- Estimates -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_estimates"

                           name="modules[]"

                           value="Estimates">

                    <label class="form-check-label fw-semibold" for="module_estimates">

                        Estimates

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Invoices -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_invoices"

                           name="modules[]"

                           value="Invoices">

                    <label class="form-check-label fw-semibold" for="module_invoices">

                        Invoices

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Payments -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_payments"

                           name="modules[]"

                           value="Payments">

                    <label class="form-check-label fw-semibold" for="module_payments">

                        Payments

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Expenses -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_expenses"

                           name="modules[]"

                           value="Expenses">

                    <label class="form-check-label fw-semibold" for="module_expenses">

                        Expenses

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Taxes -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_taxes"

                           name="modules[]"

                           value="Taxes">

                    <label class="form-check-label fw-semibold" for="module_taxes">

                        Taxes

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Categories -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_categories"

                           name="modules[]"

                           value="Categories">

                    <label class="form-check-label fw-semibold" for="module_categories">

                        Categories

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Budgets -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_budgets"

                           name="modules[]"

                           value="Budgets">

                    <label class="form-check-label fw-semibold" for="module_budgets">

                        Budgets

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Budget Expenses -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_budget_expenses"

                           name="modules[]"

                           value="Budget Expenses">

                    <label class="form-check-label fw-semibold" for="module_budget_expenses">

                        Budget Expenses

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Budget Revenues -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_budget_revenues"

                           name="modules[]"

                           value="Budget Revenues">

                    <label class="form-check-label fw-semibold" for="module_budget_revenues">

                        Budget Revenues

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Reports', $allowedModules))



<!-- Reports -->

<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-secondary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">

                    <i class="fas fa-chart-bar"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Reports</h6>

                    <small class="text-muted">Analytics & performance reports</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">



            <!-- My Reports -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_my_reports"

                           name="modules[]"

                           value="My Reports">

                    <label class="form-check-label fw-semibold" for="module_my_reports">

                        My Reports

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Team Reports -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_team_reports"

                           name="modules[]"

                           value="Team Reports">

                    <label class="form-check-label fw-semibold" for="module_team_reports">

                        Team Reports

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



            <!-- Organization Reports -->

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_org_reports"

                           name="modules[]"

                           value="Organization Reports">

                    <label class="form-check-label fw-semibold" for="module_org_reports">

                        Organization Reports

                        <span class="hierarchy-indicator" style="display: none;">

                            <i class="fas fa-sitemap text-primary"></i>

                        </span>

                    </label>

                </div>

            </div>



        </div>

    </div>

</div>



@endif

@if(in_array('Policy', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-success bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-file-alt"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Policies & Files</h6>

                    <small class="text-muted">Company policies and documents</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="module-item mb-3">

                <div class="form-check">

                    <input class="form-check-input module-checkbox"

                           type="checkbox"

                           id="module_policy"

                           name="modules[]"

                           value="Policy">

                    <label class="form-check-label fw-semibold" for="module_policy">

                        Policies & Files

                    </label>

                </div>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Goals', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-info bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-bullseye"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Goals</h6>

                    <small class="text-muted">Goal management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_goals"

                       name="modules[]"

                       value="Goals">

                <label class="form-check-label fw-semibold" for="module_goals">

                    Goals

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Assets', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-warning bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-box"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Assets</h6>

                    <small class="text-muted">Asset management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_assets"

                       name="modules[]"

                       value="Assets">

                <label class="form-check-label fw-semibold" for="module_assets">

                    Assets

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Training', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-chalkboard-teacher"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Training</h6>

                    <small class="text-muted">Employee training</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_training"

                       name="modules[]"

                       value="Training">

                <label class="form-check-label fw-semibold" for="module_training">

                    Training

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Travel', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-plane"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Travel</h6>

                    <small class="text-muted">Travel requests & approvals</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_travel"

                       name="modules[]"

                       value="Travel">

                <label class="form-check-label fw-semibold" for="module_travel">

                    Travel

                    <span class="hierarchy-indicator" style="display: none;">

                        <i class="fas fa-sitemap text-primary"></i>

                    </span>

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Training', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-primary bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-chalkboard-teacher"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Training</h6>

                    <small class="text-muted">Employee training</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_training"

                       name="modules[]"

                       value="Training">

                <label class="form-check-label fw-semibold" for="module_training">

                    Training

                </label>

            </div>

        </div>

    </div>

</div>



@endif

@if(in_array('Offboarding', $allowedModules))



<div class="col-lg-6 mb-3">

    <div class="category-card h-100">

        <div class="category-header bg-danger bg-opacity-10 border-bottom p-3">

            <div class="d-flex align-items-center">

                <div class="category-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;">

                    <i class="fas fa-user-minus"></i>

                </div>

                <div>

                    <h6 class="fw-semibold mb-0">Offboarding</h6>

                    <small class="text-muted">Employee exit management</small>

                </div>

            </div>

        </div>



        <div class="category-body p-3">

            <div class="form-check">

                <input class="form-check-input module-checkbox"

                       type="checkbox"

                       id="module_offboarding"

                       name="modules[]"

                       value="Offboarding">

                <label class="form-check-label fw-semibold" for="module_offboarding">

                    Offboarding

                    <span class="hierarchy-indicator" style="display: none;">

                        <i class="fas fa-sitemap text-primary"></i>

                    </span>

                </label>

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

                                    <button id="saveAdminBtn" class="btn btn-primary btn-lg" type="submit">

                                        <i class="fas fa-save me-2"></i>Save Admin

                                    </button>

                                    <a href="{{ route('adminaccess.index') }}" class="btn btn-secondary btn-lg ms-2">

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



<style>

    .category-card {

        border: 1px solid #e0e0e0;

        border-radius: 8px;

        overflow: hidden;

        height: 100%;

    }

    .text-muted {

      color:white !important;

    }



    .category-header {

        font-size: 0.9rem;

        font-weight: 600;

    }



    .category-modules {

        padding: 25px;

        max-height: 250px;

        overflow-y: auto;

    }



    .module-item {

        padding: 8px 0;

        border-bottom: 1px solid #f0f0f0;

    }



    .module-item:last-child {

        border-bottom: none;

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



    #moduleSearch {

        border-radius: 4px 0 0 4px;

    }



    .bg-purple {

        background-color: #6f42c1 !important;

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

        background: #fffefe;

        border-radius: 10px;

    }



    .category-modules::-webkit-scrollbar-thumb:hover {

        background: #a1a1a1;

    }

</style>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function() {

    let hierarchyModules = [];



    // Hierarchy change handler - Auto select modules

    $('#hierarchy').on('change', function() {

        var selectedOption = $(this).find('option:selected');

        var modules = selectedOption.data('modules');



        // Reset hierarchy modules array

        hierarchyModules = [];



        // Remove hierarchy indicators from all modules

        $('.hierarchy-indicator').hide();

        $('.module-item').removeClass('hierarchy-module');



        if (modules) {

            try {

                // Parse the JSON modules

                var moduleArray = typeof modules === 'string' ? JSON.parse(modules) : modules;

                if (Array.isArray(moduleArray)) {

                    hierarchyModules = moduleArray;



                    // Check the modules that are in the hierarchy

                    moduleArray.forEach(function(moduleName) {

                        var checkbox = $('.module-checkbox[value="' + moduleName + '"]');

                        checkbox.prop('checked', true);



                        // Add hierarchy indicator

                        checkbox.closest('.module-item').addClass('hierarchy-module');

                        checkbox.siblings('label').find('.hierarchy-indicator').show();

                    });



                    // Update counters

                    updateModuleCounters();



                    // Show success message

                    showNotification('Modules automatically selected based on hierarchy level! (' + moduleArray.length + ' modules)', 'success');

                }

            } catch (e) {

                console.error('Error parsing modules JSON:', e);

                showNotification('Error loading hierarchy modules', 'error');

            }

        } else {

            updateModuleCounters();

        }

    });



    // Trigger hierarchy change if there's a selected hierarchy (from old input)

    @if(old('hierarchy_id'))

        $('#hierarchy').trigger('change');

    @endif



    // Update module counters

    function updateModuleCounters() {

        var totalChecked = $('.module-checkbox:checked').length;

        var hierarchyCount = hierarchyModules.length;

        var manualCount = totalChecked - hierarchyCount;



        if (manualCount < 0) manualCount = 0; // Ensure we don't show negative numbers



        $('#hierarchyModuleCount').text(hierarchyCount + ' from hierarchy');

        $('#manualModuleCount').text(manualCount + ' manual');

        $('#totalModuleCount').text(totalChecked + ' total');

    }



    // Module checkbox change handler

    $('.module-checkbox').on('change', function() {

        updateModuleCounters();

    });



    // Show hierarchy modules button

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

        // Remove existing notifications that are not permanent

        $('.alert').not('.permanent').remove();



        // Create notification element

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



        // Add to top of form

        $('#adminForm').prepend(notification);



        // Auto remove after 5 seconds

        setTimeout(function() {

            notification.fadeOut();

        }, 5000);

    }



    // Search functionality

    $('#moduleSearch').on('input', function() {

        var searchTerm = $(this).val().toLowerCase();

        $('.module-item').each(function() {

            var moduleName = $(this).find('label').text().toLowerCase();

            if (moduleName.includes(searchTerm)) {

                $(this).show();

            } else {

                $(this).hide();

            }

        });

    });



    // Select all functionality

    $('#selectAllBtn').on('click', function() {

        $('.module-checkbox').prop('checked', true);

        updateModuleCounters();

        showNotification('All modules selected', 'info');

    });



    // Deselect all functionality

    $('#deselectAllBtn').on('click', function() {

        $('.module-checkbox').prop('checked', false);

        updateModuleCounters();

        showNotification('All modules deselected', 'info');

    });



    const form = document.getElementById('adminForm');

    const emailField = document.getElementById('email');

    const checkEmailUrl = "{{ route('adminaccess.checkEmail') }}";

    const saveBtn = document.getElementById('saveAdminBtn');



    // Form submission handler

    form.addEventListener('submit', function(event) {

        // First validate all fields

        validateAllFields();



        if (!form.checkValidity()) {

            event.preventDefault();

            event.stopPropagation();

            form.classList.add('was-validated');

            showNotification('Please fill all required fields correctly', 'error');

            return;

        }



        // If form is valid, change button state

        saveBtn.disabled = true;

        saveBtn.innerHTML = `

            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

            Processing...

        `;



        // Form is valid, allow submission

    }, false);



    // Validate all fields

    function validateAllFields() {

        const fields = form.querySelectorAll('input, select');

        let isValid = true;



        fields.forEach(field => {

            if (!validateField(field)) {

                isValid = false;

            }

        });



        return isValid;

    }



    // Validate individual field

    function validateField(field) {

        const errorMessage = field.nextElementSibling;



        if (field.required && !field.value.trim()) {

            field.classList.add('is-invalid');

            return false;

        }



        // Specific validations

        if (field.id === 'name' && field.value.trim().length < 3) {

            field.classList.add('is-invalid');

            if (errorMessage) errorMessage.textContent = 'Name must be at least 3 characters long.';

            return false;

        }



        if (field.id === 'email' && !validateEmail(field.value.trim())) {

            field.classList.add('is-invalid');

            if (errorMessage) errorMessage.textContent = 'Valid email is required.';

            return false;

        }



        if (field.id === 'password' && field.value.trim().length < 6) {

            field.classList.add('is-invalid');

            if (errorMessage) errorMessage.textContent = 'Password must be at least 6 characters long.';

            return false;

        }



        field.classList.remove('is-invalid');

        return true;

    }



    // Email validation

    function validateEmail(email) {

        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        return regex.test(email);

    }



    // Check if email exists

    function checkEmailExists(email) {

        if (!email) return;



        fetch(`${checkEmailUrl}?email=${encodeURIComponent(email)}`)

            .then(response => response.json())

            .then(data => {

                if (data.exists) {

                    emailField.classList.add('is-invalid');

                    document.getElementById('emailError').textContent = 'Email already exists!';

                }

            })

            .catch(error => console.error('Error:', error));

    }



    // Real-time validation

    form.querySelectorAll('input, select').forEach(field => {

        field.addEventListener('input', () => validateField(field));

        field.addEventListener('change', () => validateField(field));

    });



    // Email uniqueness check

    emailField.addEventListener('blur', function() {

        if (validateEmail(this.value.trim())) {

            checkEmailExists(this.value.trim());

        }

    });



    // Initialize module counters

    updateModuleCounters();

});

</script>

@endsection