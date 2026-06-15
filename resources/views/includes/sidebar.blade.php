@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

$modules = [];

if (in_array($userRole, ['employee', 'hr', 'manager']) && $userId) {
    // Primary: load from employee_module_access
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();

    // Fallback: if no individual modules assigned, load from hierarchy JSON
    if (empty($modules)) {
        $hierarchyId = Session::get('hierarchy_id');
        if ($hierarchyId) {
            $hierarchy = DB::table('hierarchies')->where('id', $hierarchyId)->first();
            if ($hierarchy && $hierarchy->modules) {
                $decoded = json_decode($hierarchy->modules, true);
                if (is_array($decoded)) {
                    // Support both flat array ['Module A','Module B']
                    // and object format {"Module A":{"view":true,...}}
                    foreach ($decoded as $key => $value) {
                        if (is_string($value)) {
                            // flat array: value is the module name
                            $modules[] = $value;
                        } elseif (is_string($key)) {
                            // object format: key is the module name
                            $modules[] = $key;
                        }
                    }
                }
            }
        }
    }

} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>

                <!-- DASHBOARD -->
                <li>
                    <a href="{{ $userRole == 'admin' ? route('admin.dashboard') : ($userRole == 'hr' ? route('hr.dashboard') : ($userRole == 'manager' ? route('manager.dashboard') : route('eemployee.dashboard'))) }}"
                        class="{{ request()->routeIs('admin.dashboard','hr.dashboard','manager.dashboard','eemployee.dashboard') ? 'active' : '' }}">
                        <i class="la la-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(in_array('Recruitment', $modules) || in_array('Recruitment Management', $modules))
                <!-- Onboarding -->
                <li>
                    <a href="{{ route('recruitment.index') }}">
                        <i class="la la-user-plus"></i>
                        <span>Onboarding</span>
                    </a>
                </li>
                @endif

                @if(in_array('Employee Leaves', $modules) || in_array('Team Leaves', $modules))
                @php
                $leaveRoute = session('role') === 'admin' 
                    ? route('team-leaves.index') 
                    : route('employee-leaves.index');
            @endphp
            
            <li>
                <a href="{{ $leaveRoute }}"
                   class="{{ request()->routeIs('employee-leaves.*') || request()->routeIs('team-leaves.*') ? 'active' : '' }}">
                    <i class="la la-plane"></i>
                    <span>Leaves</span>
                </a>
            </li>
            
@endif


@if(
    in_array('Admin Attendance', $modules) ||
    in_array('Employee Attendance', $modules) ||
    in_array('Late Punch Approval', $modules)
)
<li>
    <a href="{{ in_array('Admin Attendance', $modules) 
        ? route('admin.attendance.index') 
        : route('attendance') }}"
       class="{{ request()->routeIs('admin.attendance.*','attendance') ? 'active' : '' }}">
        <i class="la la-calendar-check"></i>
        <span>Attendance</span>
    </a>
</li>
@endif


               
@if(
    in_array('My Tasks', $modules) ||
    in_array('Project Tasks', $modules) ||
    in_array('Time Tracker', $modules)
)
<li>
    <a href="{{ route('time-tracker.index') }}"
       class="{{ request()->routeIs('time-tracker.*') ? 'active' : '' }}">
        <i class="la la-clock-o"></i>
        <span>Time Tracker</span>
    </a>
</li>
@endif

{{-- ✅ GOALS (below Time Tracker) --}}
@if(
    in_array('My Reports', $modules) ||
    in_array('Team Reports', $modules) ||
    in_array('Organization Reports', $modules)
)
<li>
    <a href="{{ route('goal.index') }}"
       class="{{ request()->routeIs('goal.*') ? 'active' : '' }}">
        <i class="la la-line-chart"></i>
        <span>Goals</span>
    </a>
</li>
@endif


{{-- ✅ TESTING (below Goals) --}}
@if(in_array('Testing', $modules))
<li>
    <a href="{{ route('testing.index') }}"
       class="{{ request()->routeIs('testing.*') ? 'active' : '' }}">
        <i class="la la-flask"></i>
        <span>Testing</span>
    </a>
</li>
@endif



                <!-- MORE BUTTON (DYNAMIC) -->
                <li>
                    <a href="javascript:void(0)" id="moreBtn">
                        <i class="la la-ellipsis-h"></i>
                        <span id="moreBtnText">More</span>
                    </a>
                </li>

                <!-- Operations -->
                <li>
                    <a href="{{ route('operations.index') }}">
                        <i class="la la-cogs"></i>
                        <span>Operations</span>
                    </a>
                </li>
                @if(
                    in_array('My Reports', $modules) ||
                    in_array('Team Reports', $modules) ||
                    in_array('Organization Reports', $modules)
                )
                <li>
                    <a href="{{ route('employeeReports') }}"
                       class="{{ request()->routeIs('employeeReports*') ? 'active' : '' }}">
                        <i class="la la-chart-area"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @endif
                
            </ul>
        </div>
    </div>
</div>

<!-- Right Mini Drawer (More Menu) -->
<div id="moreDrawer" class="more-drawer">
    <div class="drawer-header">
        <h4>More Services</h4>
        <span id="closeDrawer" class="drawer-close">&times;</span>
    </div>

    <div class="drawer-content">
        <div class="drawer-search">
            <input type="text" id="drawerSearchInput" placeholder="Search services...">
        </div>
        
        <ul class="drawer-menu">
             <!-- Employee -->
             @if(in_array('Employee', $modules))
             <li>
                 <a href="{{ route('employee.index') }}">
                     <i class="la la-users"></i>
                     <span>Employee</span>
                 </a>
             </li>
             @endif
             

             @if(in_array('Manage Shifts', $modules) || in_array('Schedule', $modules))
             <li>
                 <a href="{{ route('scheduling.index') }}">
                     <i class="la la-random"></i>
                     <span>Shift & Schedule</span>
                 </a>
             </li>
             @endif
             

             @if(
                in_array('Payroll Items', $modules) ||
                in_array('Salary', $modules) ||
                in_array('Employee Salary', $modules) ||
                in_array('Automated Payslips', $modules)
            )
            <li id="dynamicModuleSlot">
                <a href="{{ route('payroll.combined') }}">
                    <i class="la la-wallet"></i>
                    <span>Payroll</span>
                </a>
            </li>
            @endif
            
            <!-- Tickets -->
            @if(in_array('Tickets', $modules))
            <li>
                <a href="{{ route('tickets.index') }}">
                    <i class="la la-life-ring"></i> Tickets
                </a>
            </li>
            @endif
           
            
            @if(
                in_array('Estimates', $modules) ||
                in_array('Invoices', $modules) ||
                in_array('Payments', $modules) ||
                in_array('Expenses', $modules) ||
                in_array('Taxes', $modules) ||
                in_array('Budgets', $modules) ||
                in_array('Budget Expenses', $modules) ||
                in_array('Budget Revenues', $modules)
            )
            <li>
                <a href="{{ route('accounts.services') }}">
                    <i class="la la-file-invoice-dollar"></i> Accounts
                </a>
            </li>
            @endif
            

            @if(in_array('Travel', $modules))
<li>
    <a href="{{ route('expense.index') }}">
        <i class="la la-plane"></i> Travel
    </a>
</li>
@endif


@if(in_array('Policy', $modules))
<li>
    <a href="{{ route('policies.index') }}">
        <i class="la la-folder-open"></i> Policies & Files
    </a>
</li>
@endif





            <!-- Training -->
            <li>
                <a href="{{ route('trainings.index') }}" 
                   class="drawer-module" 
                   id="drawer-training-btn"
                   data-name="Training" 
                   data-icon="la la-graduation-cap">
                    <i class="la la-graduation-cap"></i> Training
                </a>
            </li>

            <!-- Promotion -->
            <li>
                <a href="{{ route('promotion.index') }}" 
                   class="drawer-module" 
                   id="drawer-promotion-btn"
                   data-name="Promotion" 
                   data-icon="la la-arrow-up">
                    <i class="la la-arrow-up"></i> Promotion
                </a>
            </li>

            

            <!-- Termination -->
            <li>
                <a href="{{ route('terminations.index') }}" 
                   class="drawer-module" 
                   id="drawer-termination-btn"
                   data-name="Termination" 
                   data-icon="la la-user-times">
                    <i class="la la-user-times"></i> Termination
                </a>
            </li>

            @if(in_array('Offboarding Approval', $modules))
            <li>
                <a href="{{ route('offboarding.index') }}">
                    <i class="la la-arrow-circle-left"></i> Offboarding
                </a>
            </li>
            @endif
            

            {!! $slot ?? '' !!} <!-- your extra modules -->

        </ul>
    </div>
</div>
<!-- Dark overlay -->
<div id="drawerOverlay" class="drawer-overlay"></div>

<style>
/* ----------------------------------------
   FIXED ZOHO STYLE + SMALLER SIZE
---------------------------------------- */

.sidebar {
    width: 78px !important;              /* REDUCED WIDTH */
    background: #131c2e !important;
    margin-left: 0 !important;
    border-right: 1px solid #00000020;
}

.page-wrapper,
.page-content,
.page-container {
    margin-left: 78px !important;        /* MATCH NEW WIDTH */
    width: calc(100% - 78px) !important;
}

.sidebar-inner {
    padding-top: 15px;                   /* Smaller spacing */
}

/* ----------------------------------------
   ICON + TEXT ALIGN (COMPACT)
---------------------------------------- */
.sidebar-menu ul {
    padding: 0;
    margin: 0;
}

.sidebar-menu ul li {
    list-style: none;
    text-align: center;
    margin-bottom: 5px;                 /* Reduced spacing */
}

.sidebar-menu ul li a {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    padding: 8px 5px;                    /* Smaller padding */
    text-decoration: none;
    border-radius: 10px;                 /* Slightly smaller radius */
    transition: 0.2s all;
}

/* ICON */
.sidebar-menu a i {
    font-size: 18px !important;          /* Smaller icon */
    margin-bottom: 3px;
    color: #f97316  !important;
}

/* LABEL */
.sidebar-menu a span {
    font-size: 11px;                     /* Slightly smaller text */
    color: #e1e7f5;
    margin-top: 2px;
    letter-spacing: 0.15px;
}

/* ACTIVE + HOVER */
.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #1f2b4d !important;
    transition: 0.15s;
}

/* "More" hidden section */
#more-modules-section {
    display: none;
    margin-top: 18px;
    border-top: 1px solid #2a3754;
    padding-top: 8px;
}

/* Hide all section titles */
.menu-title {
    display: none !important;
}

/* Submenu inner UL fix */
.submenu ul {
    padding-left: 10px !important;
}
/* -----------------------------------------
   LEFT MINI DRAWER – MATCH SIDEBAR THEME
-----------------------------------------*/

.more-drawer {
    position: fixed;
    top: 0;
    left: -320px;
    width: 280px;
    height: 100%;
    background: #131c2e !important;          /* SAME AS MAIN SIDEBAR */
    box-shadow: 3px 0 15px rgba(0,0,0,0.25);
    z-index: 20000;
    transition: left 0.35s ease;
    border-radius: 0 10px 10px 0;
    overflow-y: auto;
}

.more-drawer.open {
    left: 0 !important;
}

/* Drawer Header */
.drawer-header {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background: #131c2e !important;          /* match drawer */
}

.drawer-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #ffffff !important;
}

.drawer-close {
    font-size: 26px;
    cursor: pointer;
    color: #ffffffa8;
    transition: 0.2s;
}
.drawer-close:hover {
    color: #f97316 !important;              /* orange hover */
}

/* Drawer Content */
.drawer-content {
    padding: 15px;
}

.drawer-menu li {
    margin-bottom: 12px;
}

.drawer-menu a {
    display: block;
    padding: 12px 14px;
    border-radius: 10px;
    background: #1b263b;                     /* dark tile */
    border: 1px solid rgba(255,255,255,0.08);
    color: #e1e7f5 !important;               /* light text */
    font-size: 14px;
    font-weight: 500;
    transition: 0.25s;
}

.drawer-menu a:hover {
    background: #1f2b4d !important;          /* same hover as sidebar */
    border-color: rgba(255,255,255,0.18);
    color: #ffffff !important;
}
.drawer-menu a i {
    font-size: 16px;
    margin-right: 10px;
    color: #f97316 !important;   /* same orange icons */
}
/* Search bar inside drawer */
.drawer-search {
    padding: 12px 15px;
    position: sticky;
    top: 0;
    background: #131c2e;
    z-index: 10;
}

.drawer-search input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.2);
    background: #1b263b;
    color: #e1e7f5;
    font-size: 14px;
    outline: none;
    transition: 0.25s;
}

.drawer-search input::placeholder {
    color: #94a3b8;
}

.drawer-search input:focus {
    border-color: #f97316;
}



/* -------------------------------------------------
   MOBILE SIDEBAR MODE (screen < 768px)
------------------------------------------------- */

@media (max-width: 768px) {

/* Sidebar hidden by default */
.sidebar {
    position: fixed;
    left: -260px;                   /* fully hidden */
    width: 260px !important;        /* full mobile menu width */
    height: 100%;
    z-index: 9999;
    transition: left 0.3s ease;
}

/* When opened */
.mobile-open .sidebar {
    left: 0 !important;
}

/* Content should not shift */
.page-wrapper,
.page-content,
.page-container {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Overlay behind the menu */
#mobile_overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 9998;
    display: none;
}

.mobile-open #mobile_overlay {
    display: block;
}
}


</style>


<script>
    const mobileBtn = document.getElementById("mobile_btn");
    const mobileOverlay = document.getElementById("mobile_overlay");

    mobileBtn.addEventListener("click", function() {
        document.body.classList.toggle("mobile-open");
    });

    mobileOverlay.addEventListener("click", function() {
        document.body.classList.remove("mobile-open");
    });
</script>
<script>
    const moreBtn = document.getElementById("moreBtn");
const drawer = document.getElementById("moreDrawer");
const drawerOverlay = document.getElementById("drawerOverlay");
const closeDrawer = document.getElementById("closeDrawer");

moreBtn.addEventListener("click", function () {
    drawer.classList.add("open");
    drawerOverlay.classList.add("show");
});

closeDrawer.addEventListener("click", function () {
    drawer.classList.remove("open");
    drawerOverlay.classList.remove("show");
});

drawerOverlay.addEventListener("click", function () {
    drawer.classList.remove("open");
    drawerOverlay.classList.remove("show");
});

// LIVE search inside drawer
const drawerSearchInput = document.getElementById("drawerSearchInput");
const drawerMenuItems = document.querySelectorAll(".drawer-menu li");

drawerSearchInput.addEventListener("keyup", function () {
    let filter = drawerSearchInput.value.toLowerCase();

    drawerMenuItems.forEach(li => {
        let text = li.innerText.toLowerCase();

        if (text.includes(filter)) {
            li.style.display = "block";
        } else {
            li.style.display = "none";
        }
    });
});
// CLICK ON A MODULE INSIDE DRAWER → REPLACE PAYROLL ONLY
document.querySelectorAll(".drawer-module").forEach(item => {
    item.addEventListener("click", function () {

        let moduleName = this.dataset.name;
        let moduleIcon = this.dataset.icon;

        // Update payroll slot (ONLY payroll, not More)
        document.getElementById("dynamicModuleText").innerText = moduleName;
        document.getElementById("dynamicModuleIcon").className = moduleIcon;

        // Close drawer after selection
        drawer.classList.remove("open");
        drawerOverlay.classList.remove("show");
    });
});


    </script>

