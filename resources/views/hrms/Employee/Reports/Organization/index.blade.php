@extends('layouts.index')

@section('content')

<style>
/* PAGE AREA */
.org-page {
    background: #f8f9fc;
    padding: 22px;
}

/* TOP TABS */
.report-tabs {
    display: flex;
    gap: 35px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 25px;
}
.report-tabs a {
    font-size: 16px;
    font-weight: 600;
    color: #555;
    text-decoration: none;
    padding-bottom: 10px;
    position: relative;
}
.report-tabs a.active {
    color: #fa8128;
}
.report-tabs a.active::after {
    content: "";
    position: absolute;
    height: 3px;
    width: 100%;
    background: #fa8128;
    bottom: -3px;
    left: 0;
    border-radius: 6px;
}

/* MAIN CARD */
.org-card {
    background: #fff;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    border: 1px solid #efefef;
}

/* ORANGE LABEL HEADER */
.org-header {
    background: #fff5eb;
    border-left: 5px solid #fa8128;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 17px;
    font-weight: 700;
    color: #fa8128;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}

/* SECTION GROUPS */
.org-section-group {
    margin-bottom: 40px;
}
.org-section-group:last-child {
    margin-bottom: 0;
}

/* SECTION TITLE */
.org-section-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #444;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}
.org-section-title i {
    background: #f3f4f6;
    padding: 10px;
    border-radius: 8px;
    color: #555;
    font-size: 14px;
}

/* GRID COLUMNS */
.org-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

@media(max-width: 1200px) {
    .org-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 768px) {
    .org-grid { grid-template-columns: 1fr; }
}

/* LIST ITEMS */
.org-item {
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #444;
    font-size: 14px;
}
.org-item i {
    color: #fa8128;
    font-size: 14px;
    width: 20px;
    text-align: center;
}
.org-item a {
    color: #444;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s;
}
.org-item:hover a {
    color: #fa8128;
    transform: translateX(3px);
}
.org-item-img {
    width: 20px;
    height: 20px;
    object-fit: contain;
}
</style>


<div class="org-page">

    <!-- TOP TABS -->
    <div class="report-tabs">
        <a href="{{ route('employeeReports') }}">My Reports</a>
        <a href="{{ route('TeamReports') }}">Team Reports</a>
        <a href="{{ route('OrganizationReports') }}" class="active">Organization Reports</a>
    </div>

    <!-- MAIN CARD -->
    <div class="org-card">

        <!-- HEADER LABEL -->
        <div class="org-header">
            <i class="fas fa-building"></i>
            Organization Report Categories
        </div>

        <!-- 1. FINANCIAL REPORTS SECTION -->
        <div class="org-section-group">
            <div class="org-section-title">
                <i class="fas fa-chart-line"></i>
                Financial Reports
            </div>
            
            <div class="org-grid">
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-receipt"></i>
                        <a href="{{ route('expenses-reports.index') }}">Expense Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <a href="{{ route('invoice-reports.index') }}">Invoice Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-credit-card"></i>
                        <a href="{{ route('payments-report.index') }}">Payments Report</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-chart-line"></i>
                        <a href="{{ route('accounts-reports.index') }}">Account Reports</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PROJECT & TASK REPORTS SECTION -->
        <div class="org-section-group">
            <div class="org-section-title">
                <i class="fas fa-diagram-project"></i>
                Project & Task Reports
            </div>
            
            <div class="org-grid">
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-diagram-project"></i>
                        <a href="{{ route('project-reports.index') }}">Project Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-rocket"></i>
                        <a href="{{ route('newproject.reports.index') }}">New Project Reports</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-tasks"></i>
                        <a href="{{ route('task-reports.index') }}">Task Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-clock"></i>
                        <a href="{{ route('timesheet-reports.index') }}">Timesheet Reports</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-chart-pie"></i>
                        <a href="{{ route('organization.projectstatus') }}">Project Status Report</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. EMPLOYEE ATTENDANCE & LEAVE REPORTS SECTION -->
        <div class="org-section-group">
            <div class="org-section-title">
                <i class="fas fa-user-clock"></i>
                Employee Attendance & Leave Reports
            </div>
            
            <div class="org-grid">
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-user-check"></i>
                        <a href="{{ route('OrganizationReports.ResourceAvailability') }}">Resource Availability</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-calendar-alt"></i>
                        <a href="{{ route('Organization.LeaveBalance') }}">Leave Balance</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-calendar-check"></i>
                        <a href="{{ route('Organization.dailyLeaveStatus') }}">Daily Leave Status</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-user-clock"></i>
                        <a href="{{ route('Organization.presentAbsent') }}">Present/Absent Status</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-business-time"></i>
                        <a href="{{ route('organization.presence') }}">Presence Hours</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-running"></i>
                        <a href="{{ route('organization.earlylate') }}">Early/Late Check-in</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-calendar-week"></i>
                        <a href="{{ route('Organization.weekly') }}">Weekly Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-briefcase"></i>
                        <a href="{{ route('organization.jobstatus') }}">Job Status Report</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. HR & ADMINISTRATIVE REPORTS SECTION -->
        <div class="org-section-group">
            <div class="org-section-title">
                <i class="fas fa-users"></i>
                HR & Administrative Reports
            </div>
            
            <div class="org-grid">
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-file-contract"></i>
                        <a href="{{ route('policies-report.index') }}">Policy Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-user-tie"></i>
                        <a href="{{ route('interview-process.index') }}">Interview Process Reports</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-laptop"></i>
                        <a href="{{ route('asset-reports.index') }}">Asset Report</a>
                    </div>
                    <div class="org-item">
                        <i class="fa-solid fa-users"></i>
                        <a href="{{ route('client-reports.index') }}">Client Report</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-calendar-day"></i>
                        <a href="{{ route('daily-report.index') }}">Daily Report</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. SUPPORT & TICKET REPORTS SECTION -->
        <div class="org-section-group">
            <div class="org-section-title">
                <i class="fas fa-headset"></i>
                Support & Ticket Reports
            </div>
            
            <div class="org-grid">
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-ticket-alt"></i>
                        <a href="{{ route('ticket.reports.index') }}">Ticket Report</a>
                    </div>
                </div>
                
                <div>
                    <div class="org-item">
                        <i class="fa-solid fa-vial"></i>
                        <a href="{{ route('testing.reports.index') }}">Test Ticket Report</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection