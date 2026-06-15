@php
$userRole = Session::get('role');
$userId   = Session::get('user_id');
$adminId  = Session::get('admin_id');

$modules = [];

if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();
} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp

@extends('layouts.index')

@section('content')

<div class="container-fluid py-4">

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold service-title">Services</h4>
            <div class="service-search-wrapper">
                <i class="la la-search search-icon"></i>
                <input type="text" id="serviceSearch" class="service-search" placeholder="Search Operations">
            </div>
        </div>
    </div>

    <!-- Plan Tabs -->
    <div class="row mb-4">
        <div class="col-md-12">
            <ul class="nav nav-pills plan-tabs" id="planTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="essential-tab" data-bs-toggle="pill" data-bs-target="#essential" type="button" role="tab">Essential</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pro-tab" data-bs-toggle="pill" data-bs-target="#pro" type="button" role="tab">Pro</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="enterprise-tab" data-bs-toggle="pill" data-bs-target="#enterprise" type="button" role="tab">Enterprise</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Plan Content -->
    <div class="tab-content" id="planTabContent">
        
        <!-- ESSENTIAL PLAN -->
        <div class="tab-pane fade show active" id="essential" role="tabpanel">
            <div class="row g-4 justify-content-start" id="essentialGrid">
                @php
                $essentialServices = [];
                
                /* 1. Recruitment */
                if (in_array('Recruitment', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Recruitment',
                        'icon' => 'la la-briefcase',
                        'route' => route('recruitment.index')
                    ];
                }
                
                /* 2. Onboarding (Employees) */
                if (in_array('Employee', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Onboarding',
                        'icon' => 'la la-user-plus',
                        'route' => route('employee.index')
                    ];
                }
                
                /* 3. Attendance */
                if (
                    in_array('Admin Attendance', $modules) ||
                    in_array('Employee Attendance', $modules) ||
                    in_array('Late Punch Approval', $modules)
                ) {
                    $essentialServices[] = [
                        'name' => 'Attendance',
                        'icon' => 'la la-calendar-check',
                        'route' => in_array('Admin Attendance', $modules)
                            ? route('admin.attendance.index')
                            : route('attendance')
                    ];
                }
                
                /* 4. Shift & Schedule */
                if (in_array('Manage Shifts', $modules) || in_array('Schedule', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Shift & Schedule',
                        'icon' => 'la la-random',
                        'route' => route('scheduling.index')
                    ];
                }
                
                /* 5. Leaves */
                if (in_array('Employee Leaves', $modules) || in_array('Team Leaves', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Leaves',
                        'icon' => 'la la-umbrella',
                        'route' => route('employee-leaves.index')
                    ];
                }
                
                /* 6. Payroll */
                if (in_array('Employee Salary', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Payroll',
                        'icon' => 'la la-money',
                        'route' => route('payroll.index')
                    ];
                }
                
                /* 7. Policies & Files */
                if (in_array('Policy', $modules) || in_array('Files', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Policies & Files',
                        'icon' => 'la la-file-text',
                        'route' => route('policies.index')
                    ];
                }
                
                /* 8. Offboarding */
                if (in_array('Offboarding Approval', $modules)) {
                    $essentialServices[] = [
                        'name' => 'Offboarding',
                        'icon' => 'la la-user-times',
                        'route' => route('offboarding.index')
                    ];
                }
                 /* 12. Reports */
                 /* 12. Reports */
if (
    in_array('My Reports', $modules) ||
    in_array('Team Reports', $modules) ||
    in_array('Organization Reports', $modules)
) {
    $essentialServices[] = [
        'name' => 'Reports',
        'icon' => 'la la-chart-bar',
        'route' => route('employeeReports')
    ];
}
                @endphp
                
                
                
                @foreach($essentialServices as $service)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-8 service-item" data-name="{{ strtolower($service['name']) }}">
                    <a href="{{ $service['route'] }}" class="service-card shadow-sm">
                        <div class="service-icon">
                            <i class="{{ $service['icon'] }}"></i>
                        </div>
                        <div class="service-name">{{ $service['name'] }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- PRO PLAN -->
        <div class="tab-pane fade" id="pro" role="tabpanel">
            <div class="row g-4 justify-content-start" id="proGrid">
                @php
                $proServices = [];
                
                /* 1. Recruitment */
                if (in_array('Recruitment', $modules)) {
                    $proServices[] = [
                        'name' => 'Recruitment',
                        'icon' => 'la la-briefcase',
                        'route' => route('recruitment.index')
                    ];
                }
                
                /* 2. Onboarding */
                if (in_array('Employee', $modules)) {
                    $proServices[] = [
                        'name' => 'Onboarding',
                        'icon' => 'la la-user-plus',
                        'route' => route('employee.index')
                    ];
                }
                
                /* 3. Attendance */
                if (
                    in_array('Admin Attendance', $modules) ||
                    in_array('Employee Attendance', $modules) ||
                    in_array('Late Punch Approval', $modules)
                ) {
                    $proServices[] = [
                        'name' => 'Attendance',
                        'icon' => 'la la-calendar-check',
                        'route' => in_array('Admin Attendance', $modules)
                            ? route('admin.attendance.index')
                            : route('attendance')
                    ];
                }
                
                /* 4. Shift & Schedule */
                if (in_array('Manage Shifts', $modules) || in_array('Schedule', $modules)) {
                    $proServices[] = [
                        'name' => 'Shift & Schedule',
                        'icon' => 'la la-random',
                        'route' => route('scheduling.index')
                    ];
                }
                
                /* 5. Leaves */
                if (in_array('Employee Leaves', $modules) || in_array('Team Leaves', $modules)) {
                    $proServices[] = [
                        'name' => 'Leaves',
                        'icon' => 'la la-umbrella',
                        'route' => route('employee-leaves.index')
                    ];
                }
                
                /* 6. Payroll */
                if (in_array('Employee Salary', $modules)) {
                    $proServices[] = [
                        'name' => 'Payroll',
                        'icon' => 'la la-money',
                        'route' => route('payroll.index')
                    ];
                }
                      /* 10. Time Tracker (Pro Feature) */
                      if (
                    in_array('My Tasks', $modules) ||
                    in_array('Project Tasks', $modules) ||
                    in_array('Time Tracker', $modules)
                ) {
                    $proServices[] = [
                        'name' => 'Time Tracker',
                        'icon' => 'la la-clock',
                        'route' => route('time-tracker.index')
                    ];
                }
                
                /* 11. Testing Account (Pro Feature) */
                if (in_array('Testing', $modules)) {
                    $proServices[] = [
                        'name' => 'Testing',
                        'icon' => 'la la-flask',
                        'route' => route('testing.index')
                    ];
                }
                if (
                    in_array('Estimates', $modules) ||
                    in_array('Invoices', $modules) ||
                    in_array('Expenses', $modules) ||
                    in_array('Taxes', $modules) ||
                    in_array('Categories', $modules) ||
                    in_array('Budgets', $modules) ||
                    in_array('Budget Expenses', $modules) ||
                    in_array('Budget Revenues', $modules) ||
                    in_array('Payments', $modules)
                ) {
                    $proServices[] = [
                        'name' => 'Accounts',
                        'icon' => 'la la-flask',
                        'route' => route('accounts.services')
                    ];
                }
                
                /* 7. Policies & Files */
                if (in_array('Policy', $modules) || in_array('Files', $modules)) {
                    $proServices[] = [
                        'name' => 'Policies & Files',
                        'icon' => 'la la-file-text',
                        'route' => route('policies.index')
                    ];
                }
                
                /* 8. Offboarding */
                if (in_array('Offboarding Approval', $modules)) {
                    $proServices[] = [
                        'name' => 'Offboarding',
                        'icon' => 'la la-user-times',
                        'route' => route('offboarding.index')
                    ];
                }
                
                /* 9. Reports (Pro Feature) */
                if (
                    in_array('My Reports', $modules) ||
                    in_array('Team Reports', $modules) ||
                    in_array('Organization Reports', $modules)
                ) {
                    $proServices[] = [
                        'name' => 'Reports',
                        'icon' => 'la la-chart-bar',
                        'route' => route('employeeReports')
                    ];
                }
                
          
                @endphp
                
                @foreach($proServices as $service)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-8 service-item" data-name="{{ strtolower($service['name']) }}">
                    <a href="{{ $service['route'] }}" class="service-card shadow-sm">
                        <div class="service-icon">
                            <i class="{{ $service['icon'] }}"></i>
                        </div>
                        <div class="service-name">{{ $service['name'] }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ENTERPRISE PLAN -->
        <div class="tab-pane fade" id="enterprise" role="tabpanel">
            <div class="row g-4 justify-content-start" id="enterpriseGrid">
                @php
                $enterpriseServices = [];
                
                /* 1. Recruitment */
                if (in_array('Recruitment', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Recruitment',
                        'icon' => 'la la-briefcase',
                        'route' => route('recruitment.index')
                    ];
                }
                
                /* 2. Onboarding */
                if (in_array('Employee', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Onboarding',
                        'icon' => 'la la-user-plus',
                        'route' => route('employee.index')
                    ];
                }
                
                /* 3. Attendance */
                if (
                    in_array('Admin Attendance', $modules) ||
                    in_array('Employee Attendance', $modules) ||
                    in_array('Late Punch Approval', $modules)
                ) {
                    $enterpriseServices[] = [
                        'name' => 'Attendance',
                        'icon' => 'la la-calendar-check',
                        'route' => in_array('Admin Attendance', $modules)
                            ? route('admin.attendance.index')
                            : route('attendance')
                    ];
                }
                
                /* 4. Shift & Schedule */
                if (in_array('Manage Shifts', $modules) || in_array('Schedule', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Shift & Schedule',
                        'icon' => 'la la-random',
                        'route' => route('scheduling.index')
                    ];
                }
                
                /* 5. Leaves */
                if (in_array('Employee Leaves', $modules) || in_array('Team Leaves', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Leaves',
                        'icon' => 'la la-umbrella',
                        'route' => route('employee-leaves.index')
                    ];
                }
                
                /* 6. Payroll */
                if (in_array('Employee Salary', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Payroll',
                        'icon' => 'la la-money',
                        'route' => route('payroll.index')
                    ];
                }
                
                /* 7. Time Tracker */
                if (
                    in_array('My Tasks', $modules) ||
                    in_array('Project Tasks', $modules) ||
                    in_array('Time Tracker', $modules)
                ) {
                    $enterpriseServices[] = [
                        'name' => 'Time Tracker',
                        'icon' => 'la la-clock',
                        'route' => route('time-tracker.index')
                    ];
                }
                
                /* 8. Testing */
                if (in_array('Testing', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Testing',
                        'icon' => 'la la-flask',
                        'route' => route('testing.index')
                    ];
                }
                
                /* 9. Accounts */
                if (
                    in_array('Estimates', $modules) ||
                    in_array('Invoices', $modules) ||
                    in_array('Expenses', $modules) ||
                    in_array('Taxes', $modules) ||
                    in_array('Categories', $modules) ||
                    in_array('Budgets', $modules) ||
                    in_array('Budget Expenses', $modules) ||
                    in_array('Budget Revenues', $modules) ||
                    in_array('Payments', $modules)
                ) {
                    $enterpriseServices[] = [
                        'name' => 'Accounts',
                        'icon' => 'la la-calculator',
                        'route' => route('accounts.services')
                    ];
                }
                
                /* 10. Policies & Files */
                if (in_array('Policy', $modules) || in_array('Files', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Policies & Files',
                        'icon' => 'la la-file-text',
                        'route' => route('policies.index')
                    ];
                }
                
                /* 11. Offboarding */
                if (in_array('Offboarding Approval', $modules)) {
                    $enterpriseServices[] = [
                        'name' => 'Offboarding',
                        'icon' => 'la la-user-times',
                        'route' => route('offboarding.index')
                    ];
                }
                
               
                
             
                    $enterpriseServices[] = [
                        'name' => 'Goals',
                        'icon' => 'la la-bullseye',
                        'route' => route('goal.index')
                    ];
               
                
             
                    $enterpriseServices[] = [
                        'name' => 'Tickets',
                        'icon' => 'la la-life-ring',
                        'route' => route('tickets.index')
                    ];
             
                    $enterpriseServices[] = [
                        'name' => 'Assets',
                        'icon' => 'la la-graduation-cap',
                        'route' => route('assets.index')
                    ];
            
             
                    $enterpriseServices[] = [
                        'name' => 'Training',
                        'icon' => 'la la-graduation-cap',
                        'route' => route('trainings.index')
                    ];
                     /* 12. Reports */
                if (
                    in_array('My Reports', $modules) ||
                    in_array('Team Reports', $modules) ||
                    in_array('Organization Reports', $modules)
                ) {
                    $enterpriseServices[] = [
                        'name' => 'Reports',
                        'icon' => 'la la-chart-bar',
                        'route' => route('employeeReports')
                    ];
                }
            
                @endphp
                
                
                @foreach($enterpriseServices as $service)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-8 service-item" data-name="{{ strtolower($service['name']) }}">
                    <a href="{{ $service['route'] }}" class="service-card shadow-sm">
                        <div class="service-icon">
                            <i class="{{ $service['icon'] }}"></i>
                        </div>
                        <div class="service-name">{{ $service['name'] }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* -----------------------------------------
   OVERALL CLEAN LOOK
----------------------------------------- */

.container-fluid {
    padding-left: 40px !important;
    padding-right: 40px !important;
}

.service-title {
    font-size: 22px;
    font-weight: 700;
    color: #222;
}

/* -----------------------------------------
   PLAN TABS
----------------------------------------- */
.plan-tabs {
    border-bottom: 2px solid #e5e5e5;
    padding-bottom: 10px;
}

.plan-tabs .nav-link {
    color: #666;
    font-weight: 600;
    font-size: 16px;
    padding: 10px 25px;
    margin-right: 10px;
    border-radius: 8px;
    background: transparent;
    border: 1px solid transparent;
}

.plan-tabs .nav-link:hover {
    border-color: #ff8800;
    color: #ff8800;
}

.plan-tabs .nav-link.active {
    background: #ff8800;
    color: white;
    border-color: #ff8800;
}

/* -----------------------------------------
   SEARCH BAR
----------------------------------------- */

.service-search-wrapper {
    position: relative;
    width: 350px;
}

.service-search {
    width: 100%;
    padding: 11px 42px;
    border-radius: 12px;
    border: 1.5px solid #d9dfe8;
    background: #fff;
    font-size: 14px;
    transition: 0.25s;
}

.service-search:focus {
    border-color: #ff8800;
    box-shadow: 0 0 6px rgba(255,136,0,0.25);
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 11px;
    font-size: 18px;
    color: #666;
}

/* -----------------------------------------
   SERVICE CARDS
----------------------------------------- */

.service-card {
    background: #ffffff;
    border-radius: 15px;
    height: 135px;
    padding-top: 18px;
    padding-bottom: 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: 1px solid #e5e5e5;
    transition: 0.25s;
    text-decoration: none;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.10);
    border-color: #ff8800;
}

.service-icon i {
    font-size: 30px;
    margin-bottom: 10px;
    color: #ff8800;
}

.service-name {
    font-size: 13px;
    color: #222;
    font-weight: 600;
    text-align: center;
}

/* -----------------------------------------
   GRID CONFIG — 8 CARDS PER ROW
----------------------------------------- */

.col-xl-1-8 {
    flex: 0 0 12.5%;
    max-width: 12.5%;
    padding-left: 12px;
    padding-right: 12px;
}

/* Laptop */
@media (max-width: 1400px) {
    .col-xl-1-8 {
        flex: 0 0 14.28%;
        max-width: 14.28%;
    }
}

/* Tablet */
@media (max-width: 992px) {
    .col-xl-1-8, .col-lg-2 {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Mobile */
@media (max-width: 576px) {
    .col-xl-1-8, .col-lg-2 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Tab Content Padding */
.tab-content {
    padding-top: 20px;
}
</style>

<script>
// LIVE SEARCH - Works across all tabs
document.getElementById("serviceSearch").addEventListener("keyup", function () {
    let search = this.value.toLowerCase().trim();
    
    // Get currently active tab
    let activeTab = document.querySelector('.tab-pane.active');
    if (activeTab) {
        let items = activeTab.querySelectorAll(".service-item");
        items.forEach(item => {
            item.style.display = item.dataset.name.includes(search) ? "block" : "none";
        });
    }
});

// Reset search when switching tabs
document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function () {
        let searchInput = document.getElementById("serviceSearch");
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('keyup'));
    });
});
</script>

@endsection