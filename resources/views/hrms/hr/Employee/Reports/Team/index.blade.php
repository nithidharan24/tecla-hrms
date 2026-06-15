@extends('layouts.index')

@section('content')

<style>
/* ============================================
   PAGE WRAPPER
============================================ */
.report-page {
    background: #f4f6fa;
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
    padding-bottom: 10px;
    position: relative;
    text-decoration: none;
    transition: .3s ease;
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
   CARD WRAPPER (Improved)
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

/* GRID ENHANCED */
.report-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 35px;
}

@media(max-width: 1200px) {
    .report-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 768px) {
    .report-grid { grid-template-columns: 1fr; }
}

/* Section Title */
.report-section-title {
    font-size: 16.5px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 14px;
    display: flex;
    gap: 10px;
    align-items: center;
}

.report-section-title i {
    background: #f3f4f6;
    padding: 8px;
    border-radius: 8px;
    font-size: 15px;
    color: #6b7280;
}

/* Divider line */
.report-divider {
    border-bottom: 1px dashed #d1d5db;
    margin-bottom: 14px;
}

/* Report Item */
.report-item {
    padding: 9px 0;
    display: flex;
    gap: 10px;
    align-items: center;
    font-size: 14px;
    color: #4b5563;
    cursor: pointer;
    transition: .3s ease;
    border-radius: 6px;
}

.report-item i {
    font-size: 12px;
    color: #a1a1aa;
}

.report-item a {
    text-decoration: none;
    color: inherit;
    width: 100%;
}

/* Smooth Hover Animation */
.report-item:hover {
    background: #fff5ed;
    padding-left: 14px;
    color: #fa8128;
    transform: translateX(5px);
}

.report-item:hover i {
    color: #fa8128;
}
</style>


<div class="content container-fluid report-page">

    <!-- ======================================
         TOP TABS 
    ======================================= -->
    <div class="report-tabs">
        <a href="{{ route('employeeReports') }}">My Reports</a>
        <a href="#" class="active">Team Reports</a>
        <a href="{{ route('OrganizationReports')}}">Organization Reports</a>
    </div>

    <!-- ======================================
         CARD 
    ======================================= -->
    <div class="report-card">

        <div class="report-header-box">
            <h4><i class="fas fa-folder-open"></i> Team Report Categories</h4>
        </div>

        <!-- MAIN GRID -->
        <div class="report-grid">

            <!-- ============================
                 LEAVE TRACKER
            ============================== -->
            <div>
                <div class="report-section-title">
                    <i class="far fa-calendar-alt"></i> Leave Tracker
                </div>
                <div class="report-divider"></div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.daily.leave.status') }}">Daily Leave Status</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('resource.availability') }}">Resource Availability</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.leave.balance') }}">Employee Leave Balance</a>
                </div>
            </div>

            <!-- ============================
                 ATTENDANCE
            ============================== -->
            <div>
                <div class="report-section-title">
                    <i class="far fa-clock"></i> Attendance
                </div>
                <div class="report-divider"></div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.presentabsent') }}">Daily Attendance Status</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.earlyLate') }}">Early / Late Check-In & Check-Out</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.presentabsent') }}">Present / Absent Status</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.presence.hours') }}">Presence Hours Breakup</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.presentabsent') }}">Attendance Data for Payroll</a>
                </div>
            </div>

            <!-- ============================
                 TIME TRACKER
            ============================== -->
            <div>
                <div class="report-section-title">
                    <i class="fas fa-stopwatch"></i> Time Tracker
                </div>
                <div class="report-divider"></div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.weekly.report') }}">Time Logs</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.job.status.report') }}">Job Status</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.project.status') }}">Projects Status</a>
                </div>

                <div class="report-item"><i class="far fa-star"></i>
                    <a href="{{ route('team.scheduled.worked') }}">Scheduled vs Worked Hours</a>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
