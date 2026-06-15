
@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

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

<style>
/* ============================================
   PAGE WRAPPER
============================================ */
.report-page {
    background: #f8f9fc;
    padding: 25px;
    min-height: 100vh;
}

/* ============================================
   TOP TABS 
============================================ */
.report-tabs {
    display: flex;
    gap: 35px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 25px;
}

.report-tabs a {
    font-size: 16px;
    font-weight: 600;
    color: #6b7280;
    padding: 10px 0;
    position: relative;
    text-decoration: none;
    transition: .3s ease;
    cursor: pointer;
}

.report-tabs a:hover {
    color: #fa8128;
}

.report-tabs a.active {
    color: #fa8128;
}

.report-tabs a.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    height: 3px;
    width: 100%;
    background: #fa8128;
    border-radius: 10px;
}

/* ============================================
   CARD WRAPPER (Unified Design)
============================================ */
.report-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 32px;
    margin-bottom: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    transition: .3s ease;
}

.report-card:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,0.09);
}

.report-header-box {
    background: #fff7ec;
    padding: 18px 22px;
    border-left: 5px solid #fa8128;
    border-radius: 10px;
    margin-bottom: 26px;
}

.report-header-box h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #fa8128;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ============================================
   UNIFIED GRID SYSTEM
============================================ */
.report-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 35px;
    margin-bottom: 30px;
}

@media(max-width: 1200px) {
    .report-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 768px) {
    .report-grid { grid-template-columns: 1fr; }
}

/* ============================================
   SECTION GROUP (Unified)
============================================ */
.report-section-group {
    margin-bottom: 40px;
    padding: 20px;
    border-radius: 12px;
    background: #fafafa;
    transition: all 0.3s ease;
}

.report-section-group:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
}

.report-section-group:last-child {
    margin-bottom: 0;
}

/* ============================================
   SECTION TITLE (Unified)
============================================ */
.report-section-title {
    font-size: 17px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.report-section-title i {
    background: #f3f4f6;
    padding: 10px;
    border-radius: 10px;
    color: #fa8128;
    font-size: 16px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ============================================
   REPORT ITEM (Unified)
============================================ */
.report-item {
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #444;
    font-size: 14px;
    border-radius: 8px;
    margin-bottom: 6px;
    transition: all 0.2s ease;
    background: white;
    border: 1px solid #f0f0f0;
}

.report-item:last-child {
    margin-bottom: 0;
}

.report-item i {
    color: #fa8128;
    font-size: 14px;
    width: 20px;
    text-align: center;
}

.report-item .item-icon {
    background: #fff5ed;
    padding: 8px;
    border-radius: 8px;
    color: #fa8128;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.report-item a {
    color: #444;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    transition: all 0.2s;
}

.report-item:hover {
    background: #fff5ed;
    border-color: #fa8128;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(250, 129, 40, 0.1);
}

.report-item:hover a {
    color: #fa8128;
}

.report-item:hover .item-icon {
    background: #fa8128;
    color: white;
}

/* ============================================
   SPECIAL ICONS FOR CATEGORIES
============================================ */
.financial-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.project-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.attendance-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.hr-icon {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.support-icon {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

/* ============================================
   INFO BADGES
============================================ */
.report-count {
    background: #fa8128;
    color: white;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: auto;
    font-weight: 600;
}

/* ============================================
   TAB CONTENT SYSTEM
============================================ */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ============================================
   EMPTY STATE
============================================ */
.report-empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #6b7280;
}

.report-empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 15px;
}

.report-empty-state h5 {
    font-size: 18px;
    color: #374151;
    margin-bottom: 10px;
}
</style>

<div class="content container-fluid report-page">

    <!-- ======================================
         TOP TABS 
    ======================================= -->
    <div class="report-tabs">
        @if( in_array('My Reports', $modules))
        <a href="#" class="tab-link active" data-tab="my-reports">My Reports</a>
        @endif
        @if( in_array('Team Reports', $modules))
        <a href="#" class="tab-link" data-tab="team-reports">Team Reports</a>
        @endif
        @if( in_array('Organization Reports', $modules))
        <a href="#" class="tab-link" data-tab="org-reports">Organization Reports</a>
        @endif
    </div>

    <!-- ======================================
         MY REPORTS TAB
    ======================================= -->
    @if( in_array('My Reports', $modules))
    <div id="my-reports" class="tab-content active">
        <div class="report-card">
            <div class="report-header-box">
                <h4><i class="fas fa-user-circle"></i> My Report Categories</h4>
            </div>

            <!-- UNIFIED GRID LAYOUT -->
            <div class="report-grid">
                <!-- Employee Information -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="far fa-id-card"></i> Employee Information
                        <span class="report-count">1</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <a href="{{ route('reports.careerhistory') }}">Career History</a>
                    </div>
                </div>

                <!-- Leave Tracker -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="far fa-calendar-alt"></i> Leave Tracker
                        <span class="report-count">1</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        <a href="{{ route('reports.leavebalance') }}">Leave Balance</a>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="far fa-clock"></i> Attendance
                        <span class="report-count">3</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-running"></i>
                        </div>
                        <a href="{{ route('myreports.attendance.earlylate') }}">Early / Late Check-in</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="{{ route('myreports.presentAbsent') }}">Present / Absent Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <a href="{{ route('myreports.presenceHours') }}">Presence Hours Breakup</a>
                    </div>
                </div>

                <!-- Time Tracker -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-stopwatch"></i> Time Tracker
                        <span class="report-count">4</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <a href="{{ url('/job-status-report') }}">Time Logs</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="{{ url('/job-status-report') }}">Job Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <a href="{{ route('project.status.report') }}">Projects Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="{{ route('scheduled.worked.report') }}">Scheduled vs Worked Hours</a>
                    </div>
                </div>

                <!-- Performance -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-trophy"></i> Performance
                        <span class="report-count">2</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <a href="{{ route('my.goals.report') }}">Goals</a>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- ======================================
         TEAM REPORTS TAB
    ======================================= -->
    @if( in_array('Team Reports', $modules))
    <div id="team-reports" class="tab-content">
        <div class="report-card">
            <div class="report-header-box">
                <h4><i class="fas fa-users"></i> Team Report Categories</h4>
            </div>

            <!-- UNIFIED GRID LAYOUT -->
            <div class="report-grid">
                <!-- Leave Tracker -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="far fa-calendar-alt attendance-icon"></i> Leave Tracker
                        <span class="report-count">3</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="{{ route('team.daily.leave.status') }}">Daily Leave Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <a href="{{ route('resource.availability') }}">Resource Availability</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <a href="{{ route('team.leave.balance') }}">Employee Leave Balance</a>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="far fa-clock attendance-icon"></i> Attendance
                        <span class="report-count">5</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="{{ route('team.presentabsent') }}">Daily Attendance Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="{{ route('team.earlyLate') }}">Early / Late Check-In & Check-Out</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="{{ route('team.presentabsent') }}">Present / Absent Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <a href="{{ route('team.presence.hours') }}">Presence Hours Breakup</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="{{ route('team.presentabsent') }}">Attendance Data for Payroll</a>
                    </div>
                </div>

                <!-- Time Tracker -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-stopwatch project-icon"></i> Time Tracker
                        <span class="report-count">4</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="{{ route('team.weekly.report') }}">Time Logs</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="{{ route('team.job.status.report') }}">Job Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <a href="{{ route('team.project.status') }}">Projects Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon">
                            <i class="fas fa-balance-scale-right"></i>
                        </div>
                        <a href="{{ route('team.scheduled.worked') }}">Scheduled vs Worked Hours</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ======================================
         ORGANIZATION REPORTS TAB
    ======================================= -->
    @if( in_array('Organization Reports', $modules))
    <div id="org-reports" class="tab-content">
        <div class="report-card">
            <div class="report-header-box">
                <h4><i class="fas fa-building"></i> Organization Report Categories</h4>
            </div>

            <!-- UNIFIED GRID LAYOUT -->
            <div class="report-grid">
                <!-- 1. FINANCIAL REPORTS -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-chart-line financial-icon" style="background: transparent; color: #667eea;"></i> Financial Reports
                        <span class="report-count">4</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #667eea20; color: #667eea;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <a href="{{ route('expenses-reports.index') }}">Expense Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #764ba220; color: #764ba2;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <a href="{{ route('invoice-reports.index') }}">Invoice Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #4c51bf20; color: #4c51bf;">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <a href="{{ route('payments-report.index') }}">Payments Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #4299e120; color: #4299e1;">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <a href="{{ route('accounts-reports.index') }}">Account Reports</a>
                    </div>
                </div>

                <!-- 2. PROJECT & TASK REPORTS -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-diagram-project project-icon" style="background: transparent; color: #f093fb;"></i> Project & Task Reports
                        <span class="report-count">5</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #f093fb20; color: #f093fb;">
                            <i class="fa-solid fa-diagram-project"></i>
                        </div>
                        <a href="{{ route('project-reports.index') }}">Project Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #f5576c20; color: #f5576c;">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <a href="{{ route('newproject.reports.index') }}">New Project Reports</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #ff6b6b20; color: #ff6b6b;">
                            <i class="fa-solid fa-tasks"></i>
                        </div>
                        <a href="{{ route('task-reports.index') }}">Task Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #ff9a3c20; color: #ff9a3c;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <a href="{{ route('timesheet-reports.index') }}">Timesheet Reports</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #feca5720; color: #feca57;">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <a href="{{ route('organization.projectstatus') }}">Project Status Report</a>
                    </div>
                </div>

                <!-- 3. EMPLOYEE ATTENDANCE & LEAVE REPORTS -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-user-clock attendance-icon" style="background: transparent; color: #4facfe;"></i> Employee Attendance & Leave Reports
                        <span class="report-count">7</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #4facfe20; color: #4facfe;">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <a href="{{ route('OrganizationReports.ResourceAvailability') }}">Resource Availability</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #00f2fe20; color: #00f2fe;">
                            <i class="fa-solid fa-calendar-alt"></i>
                        </div>
                        <a href="{{ route('Organization.LeaveBalance') }}">Leave Balance</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #1e90ff20; color: #1e90ff;">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <a href="{{ route('Organization.dailyLeaveStatus') }}">Daily Leave Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #4169e120; color: #4169e1;">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <a href="{{ route('Organization.presentAbsent') }}">Present/Absent Status</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #6495ed20; color: #6495ed;">
                            <i class="fa-solid fa-business-time"></i>
                        </div>
                        <a href="{{ route('organization.presence') }}">Presence Hours</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #00bfff20; color: #00bfff;">
                            <i class="fa-solid fa-running"></i>
                        </div>
                        <a href="{{ route('organization.earlylate') }}">Early/Late Check-in</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #87ceeb20; color: #87ceeb;">
                            <i class="fa-solid fa-calendar-week"></i>
                        </div>
                        <a href="{{ route('Organization.weekly') }}">Weekly Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #4682b420; color: #4682b4;">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <a href="{{ route('organization.jobstatus') }}">Job Status Report</a>
                    </div>
                </div>

                <!-- 4. HR & ADMINISTRATIVE REPORTS -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-users hr-icon" style="background: transparent; color: #43e97b;"></i> HR & Administrative Reports
                        <span class="report-count">5</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #43e97b20; color: #43e97b;">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <a href="{{ route('policies-report.index') }}">Policy Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #38f9d720; color: #38f9d7;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <a href="{{ route('interview-process.index') }}">Interview Process Reports</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #00d2ff20; color: #00d2ff;">
                            <i class="fa-solid fa-laptop"></i>
                        </div>
                        <a href="{{ route('asset-reports.index') }}">Asset Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #32cd3220; color: #32cd32;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <a href="{{ route('client-reports.index') }}">Client Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #98fb9820; color: #98fb98;">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <a href="{{ route('daily-report.index') }}">Daily Report</a>
                    </div>
                </div>

                <!-- 5. SUPPORT & TICKET REPORTS -->
                <div class="report-section-group">
                    <div class="report-section-title">
                        <i class="fas fa-headset support-icon" style="background: transparent; color: #fa709a;"></i> Support & Ticket Reports
                        <span class="report-count">2</span>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #fa709a20; color: #fa709a;">
                            <i class="fa-solid fa-ticket-alt"></i>
                        </div>
                        <a href="{{ route('ticket.reports.index') }}">Ticket Report</a>
                    </div>
                    
                    <div class="report-item">
                        <div class="item-icon" style="background: #fee14020; color: #fee140;">
                            <i class="fa-solid fa-vial"></i>
                        </div>
                        <a href="{{ route('testing.reports.index') }}">Test Ticket Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all tab links and tab contents
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Function to switch tabs
    function switchTab(tabId) {
        // Remove active class from all tabs and contents
        tabLinks.forEach(link => link.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        const activeTabLink = document.querySelector(`[data-tab="${tabId}"]`);
        const activeTabContent = document.getElementById(tabId);
        
        if (activeTabLink) activeTabLink.classList.add('active');
        if (activeTabContent) activeTabContent.classList.add('active');
        
        // Update URL hash without scrolling
        history.replaceState(null, null, `#${tabId}`);
        
        // Save preference to localStorage
        localStorage.setItem('activeReportTab', tabId);
    }
    
    // Add click event to all tab links
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
    
    // Check for saved preference or URL hash on page load
    const savedTab = localStorage.getItem('activeReportTab');
    const hash = window.location.hash.substring(1);
    
    if (hash && ['my-reports', 'team-reports', 'org-reports'].includes(hash)) {
        switchTab(hash);
    } else if (savedTab && ['my-reports', 'team-reports', 'org-reports'].includes(savedTab)) {
        switchTab(savedTab);
    } else {
        // Default to first tab
        switchTab('my-reports');
    }
    
    // Add keyboard shortcuts for tab navigation
    document.addEventListener('keydown', function(e) {
        if (e.altKey) {
            if (e.key === '1') {
                e.preventDefault();
                switchTab('my-reports');
            } else if (e.key === '2') {
                e.preventDefault();
                switchTab('team-reports');
            } else if (e.key === '3') {
                e.preventDefault();
                switchTab('org-reports');
            }
        }
    });
});
</script>

@endsection