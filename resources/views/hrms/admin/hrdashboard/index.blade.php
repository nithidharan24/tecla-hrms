@extends('layouts.index')

@section('content')
@php
    use Carbon\Carbon;

    $deptLabels = $departmentEmployeeCounts->pluck('department')->toArray();
    $deptCounts = $departmentEmployeeCounts->pluck('count')->toArray();
    $payrollDeptLabels = $payrollCostByDepartment->pluck('department')->toArray();
    $payrollDeptAmounts = $payrollCostByDepartment->pluck('amount')->map(fn ($amount) => round((float) $amount, 2))->toArray();
    $promotionDeptLabels = $promotionsByDepartment->pluck('department')->toArray();
    $promotionDeptCounts = $promotionsByDepartment->pluck('count')->toArray();
    $pendingSummary = $pendingApprovals ?? (($pendingLeaveRequests ?? 0) + ($pendingSalaryApprovals ?? 0) + ($pendingInvoices ?? 0) + ($pendingPerformanceReviews ?? 0));
@endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .admin-dashboard {
        background: #F5F6FA !important;
        color: #0B1437 !important;
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        padding: 0 0 40px;
        width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    .admin-dashboard * { box-sizing: border-box; }
    .admin-dashboard .dashboard-body { padding: 32px 40px; max-width: 1600px; margin: 0 auto; width: 100%; }
    .admin-dashboard h1,.admin-dashboard h2,.admin-dashboard h3,.admin-dashboard h4,.admin-dashboard h5,.admin-dashboard h6,.admin-dashboard p,.admin-dashboard span,.admin-dashboard td,.admin-dashboard th { font-family: 'DM Sans', sans-serif !important; }
    .admin-dashboard .dashboard-header { align-items: center; background: #FFFFFF !important; border-bottom: 1px solid #E5E7EB !important; display: flex; justify-content: space-between; flex-wrap: wrap; min-height: 70px; padding: 16px 60px; position: sticky; top: 0; z-index: 20; box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important; }
    .admin-dashboard .dashboard-header h1 { color: #0B1437 !important; font-size: 22px; font-weight: 700; margin: 0 0 4px; letter-spacing: -0.5px; }
    .admin-dashboard .header-status { display: flex; align-items: center; gap: 12px; font-size: 13px; color: #4B5563; }
    .admin-dashboard .status-time { font-weight: 500; color: #0B1437; }
    .admin-dashboard .status-live { display: inline-flex; align-items: center; gap: 6px; background: #ECFDF5; color: #10B981; padding: 2px 8px; border-radius: 20px; font-weight: 600; font-size: 11px; }
    .admin-dashboard .live-dot { width: 6px; height: 6px; background-color: #10B981; border-radius: 50%; display: inline-block; position: relative; }
    .admin-dashboard .live-dot::after { content: ''; position: absolute; top: -1px; left: -1px; width: 6px; height: 6px; border-radius: 50%; border: 1px solid #10B981; animation: pulse-live-dash 1.8s infinite; }
    @keyframes pulse-live-dash { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(2.8); opacity: 0; } }
    .admin-dashboard .header-actions { align-items: center; display: flex; gap: 12px; }
    .admin-dashboard .btn-dashboard-primary { background: #6366F1 !important; color: #FFFFFF !important; border: none !important; border-radius: 10px; padding: 10px 18px; font-size: 14px; font-weight: 600; text-decoration: none !important; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(99,102,241,0.15); }
    .admin-dashboard .btn-dashboard-primary:hover { background: #4F46E5 !important; transform: translateY(-1px); }
    .admin-dashboard .btn-dashboard-secondary { background: #EEF2FF !important; color: #6366F1 !important; border: 1px solid rgba(99,102,241,0.2) !important; border-radius: 10px; padding: 10px 18px; font-size: 14px; font-weight: 600; text-decoration: none !important; transition: all 0.2s ease; }
    .admin-dashboard .btn-dashboard-secondary:hover { background: #E0E7FF !important; transform: translateY(-1px); }
    .admin-dashboard .section-block { margin-top: 32px; }
    .admin-dashboard .section-head { align-items: flex-end; display: flex; justify-content: space-between; margin-bottom: 18px; }
    .admin-dashboard .section-eyebrow { color: #8892B0; font-size: 11px; font-weight: 700; letter-spacing: .1em; margin-bottom: 6px; text-transform: uppercase; }
    .admin-dashboard .section-title { color: #0B1437; font-size: 20px; font-weight: 700; margin: 0; letter-spacing: -0.3px; }
    .admin-dashboard .grid { display: grid; gap: 20px; }
    .admin-dashboard .grid-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .admin-dashboard .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .admin-dashboard .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .admin-dashboard .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-dashboard .stat-card,.admin-dashboard .panel,.admin-dashboard .mini-card { background: #FFFFFF !important; border: 1px solid #E5E7EB !important; border-radius: 16px !important; box-shadow: 0 4px 6px -1px rgba(11,20,55,.03),0 2px 4px -1px rgba(11,20,55,.02) !important; position: relative; transition: all 0.25s cubic-bezier(0.4,0,0.2,1) !important; animation: fadeInUpDash 0.5s cubic-bezier(0.16,1,0.3,1) both; }
    .admin-dashboard .stat-card { min-height: 146px; overflow: hidden; padding: 22px 24px !important; border-bottom: 3px solid transparent !important; display: flex; flex-direction: column; justify-content: space-between; }
    .admin-dashboard .stat-card::before { display: none !important; }
    .admin-dashboard .stat-card:hover,.admin-dashboard .panel:hover,.admin-dashboard .mini-card:hover { transform: translateY(-4px) !important; box-shadow: 0 12px 20px -8px rgba(11,20,55,.08),0 4px 12px -2px rgba(11,20,55,.03) !important; }
    .admin-dashboard .stat-card:nth-child(1) { animation-delay:.05s; border-bottom-color:#6366F1 !important; }
    .admin-dashboard .stat-card:nth-child(1) .stat-icon { background:#EEF2FF !important; color:#6366F1 !important; }
    .admin-dashboard .stat-card:nth-child(2) { animation-delay:.10s; border-bottom-color:#10B981 !important; }
    .admin-dashboard .stat-card:nth-child(2) .stat-icon { background:#ECFDF5 !important; color:#10B981 !important; }
    .admin-dashboard .stat-card:nth-child(3) { animation-delay:.15s; border-bottom-color:#EF4444 !important; }
    .admin-dashboard .stat-card:nth-child(3) .stat-icon { background:#FEF2F2 !important; color:#EF4444 !important; }
    .admin-dashboard .stat-card:nth-child(4) { animation-delay:.20s; border-bottom-color:#F59E0B !important; }
    .admin-dashboard .stat-card:nth-child(4) .stat-icon { background:#FFFBEB !important; color:#F59E0B !important; }
    .admin-dashboard .stat-card:nth-child(5) { animation-delay:.25s; border-bottom-color:#3B82F6 !important; }
    .admin-dashboard .stat-card:nth-child(5) .stat-icon { background:#EFF6FF !important; color:#3B82F6 !important; }
    .admin-dashboard .stat-card:nth-child(6) { animation-delay:.30s; border-bottom-color:#8B5CF6 !important; }
    .admin-dashboard .stat-card:nth-child(6) .stat-icon { background:#F5F3FF !important; color:#8B5CF6 !important; }
    .admin-dashboard .stat-top { align-items: center; display: flex; justify-content: space-between; margin-bottom: 20px; }
    .admin-dashboard .stat-icon { align-items: center; border-radius: 12px; display: inline-flex; height: 42px; justify-content: center; width: 42px; font-size: 18px; }
    .admin-dashboard .stat-value { color: #0B1437 !important; font-size: 28px; font-weight: 700; line-height: 1.1; margin: 0 0 6px; letter-spacing: -0.5px; }
    .admin-dashboard .stat-label { color: #4B5563 !important; font-size: 13px; font-weight: 600; margin: 0; }
    .admin-dashboard .stat-sub,.admin-dashboard .mini-label { color: #8892B0 !important; font-size: 12px; margin: 0; }
    .admin-dashboard .panel { padding: 24px !important; }
    .admin-dashboard .panel-header { align-items: flex-start; display: flex; gap: 12px; justify-content: space-between; margin-bottom: 20px; }
    .admin-dashboard .chart-box { height: 220px; position: relative; width: 100%; overflow: hidden; }
    .admin-dashboard .chart-box canvas { max-height: 220px; }
    .admin-dashboard .chart-box.short { height: 180px; }
    .admin-dashboard .chart-box.short canvas { max-height: 180px; }
    .admin-dashboard .chart-box.area { height: 200px; }
    .admin-dashboard .chart-box.area canvas { max-height: 200px; }
    .admin-dashboard .donut-container { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .admin-dashboard .donut-center-text { position: absolute; text-align: center; pointer-events: none; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .admin-dashboard .donut-number { font-size: 26px; font-weight: 700; color: #0B1437; line-height: 1; letter-spacing: -0.5px; }
    .admin-dashboard .donut-label { font-size: 11px; color: #8892B0; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; font-weight: 700; }
    .admin-dashboard .donut-wrap { align-items: center; display: grid; gap: 20px; grid-template-columns: minmax(150px,1fr) minmax(150px,180px); }
    .admin-dashboard .donut-wrap .chart-box { max-height: 250px; }
    .admin-dashboard .legend-list,.admin-dashboard .alert-list,.admin-dashboard .review-list { display: flex; flex-direction: column; gap: 12px; }
    .admin-dashboard .legend-item,.admin-dashboard .alert-item,.admin-dashboard .review-item { align-items: center; display: flex; justify-content: space-between; gap: 12px; }
    .admin-dashboard .legend-left,.admin-dashboard .alert-left,.admin-dashboard .review-person { align-items: center; display: flex; gap: 12px; min-width: 0; }
    .admin-dashboard .legend-dot,.admin-dashboard .alert-dot { border-radius: 50%; flex: 0 0 auto; height: 10px; width: 10px; }
    .admin-dashboard .pipeline-list { display: flex; flex-direction: column; gap: 14px; margin-top: 6px; }
    .admin-dashboard .pipeline-item { display: flex; flex-direction: column; gap: 6px; }
    .admin-dashboard .pipeline-label-wrap { display: flex; align-items: center; gap: 10px; }
    .admin-dashboard .pipeline-dot { width: 8px; height: 8px; border-radius: 50%; }
    .admin-dashboard .pipeline-name { font-size: 13px; font-weight: 500; color: #4B5563; }
    .admin-dashboard .pipeline-bar-wrap { display: flex; align-items: center; gap: 12px; }
    .admin-dashboard .pipeline-bar-track { flex: 1; height: 6px; background: #E5E7EB; border-radius: 4px; overflow: hidden; }
    .admin-dashboard .pipeline-bar-fill { height: 100%; border-radius: 4px; transition: width 0.8s ease-out; }
    .admin-dashboard .pipeline-value { font-size: 13px; font-weight: 700; color: #0B1437; min-width: 24px; text-align: right; }
    .admin-dashboard .payroll-rows { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
    .admin-dashboard .payroll-row { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid rgba(15,23,42,.04); }
    .admin-dashboard .payroll-row:last-child { border-bottom: none; padding-bottom: 0; }
    .admin-dashboard .payroll-label { font-size: 13px; color: #4B5563; font-weight: 500; }
    .admin-dashboard .payroll-val { font-size: 14px; font-weight: 600; color: #0B1437; }
    .admin-dashboard .payroll-val.val-green { color: #10B981; }
    .admin-dashboard .payroll-val.val-orange { color: #6366F1; }
    .admin-dashboard .payroll-val.val-bold { font-weight: 700; }
    .admin-dashboard .table-scroll { overflow-x: auto; border-radius: 12px; border: 1px solid #E5E7EB; }
    .admin-dashboard .data-table { border-collapse: collapse; font-size: 13px; width: 100%; background: #FFFFFF; }
    .admin-dashboard .data-table th { background: #F9FAFB; border-bottom: 1px solid #E5E7EB; color: #4B5563; font-size: 11px; font-weight: 700; letter-spacing: .05em; padding: 12px 16px; text-align: left; text-transform: uppercase; }
    .admin-dashboard .data-table td { border-bottom: 1px solid #F3F4F6; color: #0B1437; padding: 14px 16px; vertical-align: middle; }
    .admin-dashboard .data-table tbody tr:last-child td { border-bottom: none; }
    .admin-dashboard .data-table tbody tr:hover td { background: #F5F6FA; }
    .admin-dashboard .badge { border-radius: 20px; display: inline-flex; font-size: 11px; font-weight: 700; line-height: 1; padding: 6px 12px; white-space: nowrap; }
    .admin-dashboard .badge-green { background: #ECFDF5; color: #047857; }
    .admin-dashboard .badge-amber { background: #FFFBEB; color: #B45309; }
    .admin-dashboard .badge-red { background: #FEF2F2; color: #B91C1C; }
    .admin-dashboard .badge-blue { background: #EFF6FF; color: #1D4ED8; }
    .admin-dashboard .badge-purple { background: #EEF2FF; color: #4338CA; }
    .admin-dashboard .badge-gray { background: #F3F4F6; color: #374151; }
    .admin-dashboard .badge-orange-light { background: #EEF2FF; color: #6366F1; }
    .admin-dashboard .mini-card { padding: 18px !important; }
    .admin-dashboard .mini-value { color: #0B1437; font-size: 22px; font-weight: 700; line-height: 1.1; margin: 0 0 6px; letter-spacing: -0.3px; }
    .admin-dashboard .progress-track { background: #E5E7EB; border-radius: 4px; height: 6px; overflow: hidden; min-width: 96px; }
    .admin-dashboard .progress-fill { border-radius: 4px; height: 100%; transition: width .4s ease; }
    .admin-dashboard .empty-state { align-items: center; color: #8892B0; display: flex; justify-content: center; min-height: 96px; text-align: center; font-weight: 500; }
    .admin-dashboard .view-link { color: #6366F1; font-size: 13px; font-weight: 700; text-decoration: none; transition: color 0.2s ease; }
    .admin-dashboard .view-link:hover { color: #4F46E5; text-decoration: underline !important; }
    .admin-dashboard .review-avatar { align-items: center; background: #EEF2FF; border-radius: 50%; color: #6366F1; display: inline-flex; flex: 0 0 auto; font-weight: 700; height: 38px; justify-content: center; width: 38px; border: 2px solid rgba(99,102,241,.1); font-size: 13px; }
    .admin-dashboard .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    @keyframes fadeInUpDash { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @media(max-width:1400px){.admin-dashboard .grid-6{grid-template-columns:repeat(3,minmax(0,1fr))}.admin-dashboard .grid-4{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:991px){.admin-dashboard .grid-6,.admin-dashboard .grid-4,.admin-dashboard .grid-3,.admin-dashboard .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.admin-dashboard .donut-wrap{grid-template-columns:1fr !important}}
    @media(max-width:767px){.admin-dashboard .dashboard-header{flex-direction:column !important;gap:10px !important;padding:10px 12px !important;align-items:flex-start !important}.admin-dashboard .dashboard-body{padding:10px 8px !important}.admin-dashboard .grid-6,.admin-dashboard .grid-4,.admin-dashboard .grid-3,.admin-dashboard .grid-2{grid-template-columns:1fr !important}}
</style>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <div>
            <h1>HR Dashboard 👥</h1>
            <div class="header-status">
                <span class="status-time" id="live-clock">--:--:--</span>
                <span class="status-live"><span class="live-dot"></span> Live</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('chat.hr.index') }}" class="btn-dashboard-secondary" id="hr-chat-btn" style="position:relative;display:inline-flex;align-items:center;gap:8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Chat with Employees
                <span id="hr-chat-unread" style="display:none;position:absolute;top:-8px;right:-8px;background:#EF4444;color:#fff;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:999px;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;">0</span>
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="btn-dashboard-secondary">📢 Internal Announcement</a>
            <button onclick="window.print()" class="btn-dashboard-secondary">Export Report</button>
            <a href="{{ route('employee.create') }}" class="btn-dashboard-primary">Add Employee</a>
        </div>
    </div>

    <div class="dashboard-body">
        @include('hrms.partials.dashboard-announcement-bar', [
            'announcementAccent' => '#6366F1',
            'announcementAccentSoft' => 'rgba(99, 102, 241, 0.10)',
        ])

        {{-- ===== HR KPI SUMMARY ===== --}}
        <div class="grid grid-6">
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-users"></i></span><span class="badge badge-purple">Workforce</span></div>
                <p class="stat-value">{{ number_format($totalEmployees) }}</p>
                <p class="stat-label">Total Employees</p>
                <p class="stat-sub">+{{ number_format($newEmployees) }} joined this week</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-check"></i></span><span class="badge badge-green">Today</span></div>
                <p class="stat-value">{{ number_format($presentToday) }}</p>
                <p class="stat-label">Present Today</p>
                <p class="stat-sub">{{ $punctualityRate }}% on-time rate</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-times"></i></span><span class="badge badge-red">Today</span></div>
                <p class="stat-value">{{ number_format($absentToday) }}</p>
                <p class="stat-label">Absent Today</p>
                <p class="stat-sub">{{ number_format($todayLeave) }} on approved leave</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-calendar-check"></i></span><span class="badge badge-amber">Leaves</span></div>
                <p class="stat-value">{{ number_format($pendingLeaveRequests) }}</p>
                <p class="stat-label">Pending Leave Requests</p>
                <p class="stat-sub">{{ number_format($approvedLeaves) }} approved this period</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-chart-bar"></i></span><span class="badge badge-orange-light">30 days</span></div>
                <p class="stat-value">{{ $attendanceRate }}%</p>
                <p class="stat-label">Attendance Rate</p>
                <p class="stat-sub">30-day rolling average</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-list-check"></i></span><span class="badge badge-orange-light">Action</span></div>
                <p class="stat-value">{{ number_format($pendingSummary) }}</p>
                <p class="stat-label">Pending HR Actions</p>
                <p class="stat-sub">Leaves, payroll, reviews</p>
            </div>
        </div>

        {{-- ===== WORKFORCE SUMMARY ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div>
                    <div class="section-eyebrow">HR Overview</div>
                    <h2 class="section-title">Workforce Summary</h2>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="grid grid-4">
                        <div class="mini-card"><p class="mini-value">{{ number_format($activeEmployees) }}</p><p class="mini-label">Active Employees</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($newJoinersThisMonth) }}</p><p class="mini-label">New Joiners This Month</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($employeesOnNoticePeriod) }}</p><p class="mini-label">On Notice Period</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($employeesExitingThisMonth) }}</p><p class="mini-label">Exiting This Month</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <div class="section-eyebrow">Headcount by Department</div>
                            <h3 class="section-title">Department-wise Employee Count</h3>
                        </div>
                    </div>
                    <div class="chart-box short"><canvas id="departmentCountChart"></canvas></div>
                </div>
            </div>
        </section>

        {{-- ===== ATTENDANCE ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div>
                    <div class="section-eyebrow">Attendance Tracking</div>
                    <h2 class="section-title">Today's Attendance Overview</h2>
                </div>
                <span class="badge badge-blue">Attendance {{ $attendanceRate }}%</span>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Weekly Trend</div><h3 class="section-title">7-Day Attendance Trend</h3></div>
                    </div>
                    <div class="chart-box"><canvas id="weeklyAttendanceChart"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Today's Breakdown</div><h3 class="section-title">Attendance Status Breakdown</h3></div>
                    </div>
                    <div class="donut-wrap">
                        <div class="chart-box">
                            <div class="donut-container">
                                <canvas id="todayAttendanceDonut"></canvas>
                                <div class="donut-center-text">
                                    <div class="donut-number">{{ $presentToday + $absentToday }}</div>
                                    <div class="donut-label">total</div>
                                </div>
                            </div>
                        </div>
                        <div class="legend-list">
                            @foreach([['Present', $presentToday, '#10B981'],['Absent', $absentToday, '#EF4444'],['Late', $lateArrivalsToday, '#F59E0B'],['On Leave', $todayLeave, '#3B82F6']] as $item)
                                <div class="legend-item">
                                    <span class="legend-left"><span class="legend-dot" style="background:{{ $item[2] }}"></span>{{ $item[0] }}</span>
                                    <strong>{{ number_format($item[1]) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div>
                        <div class="section-eyebrow">
                            @php $filterLabels = ['today' => "Today's",'yesterday' => "Yesterday's",'past7days' => 'Past 7 Days','last1month' => 'Last 1 Month']; $label = $filterLabels[$attendanceFilter] ?? "Today's"; @endphp
                            {{ $label }} Check-ins
                        </div>
                        <h3 class="section-title">Department Attendance Status</h3>
                    </div>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <form method="GET" action="{{ route('hr.dashboard') }}" id="attendanceFilterForm">
                            @foreach(request()->except('attendance_filter') as $key => $value)
                                @if(is_array($value)) @foreach($value as $k => $v)<input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">@endforeach
                                @else <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endif
                            @endforeach
                            <select name="attendance_filter" class="form-control" style="font-size:13px;border-radius:6px;border:1px solid #E5E7EB;padding:4px 8px;color:#4B5563;" onchange="document.getElementById('attendanceFilterForm').submit()">
                                <option value="today" {{ $attendanceFilter==='today'?'selected':'' }}>Today</option>
                                <option value="yesterday" {{ $attendanceFilter==='yesterday'?'selected':'' }}>Yesterday</option>
                                <option value="past7days" {{ $attendanceFilter==='past7days'?'selected':'' }}>Past 7 Days</option>
                                <option value="last1month" {{ $attendanceFilter==='last1month'?'selected':'' }}>Last 1 Month</option>
                            </select>
                        </form>
                    </div>
                </div>
                @if($deptAttendanceStats->count())
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead><tr><th>Department</th><th>Punch-ins & Percent</th><th>Early Birds (< 9:30 AM)</th></tr></thead>
                            <tbody>
                                @foreach($deptAttendanceStats as $stat)
                                    <tr>
                                        <td><strong>{{ $stat->dept_name }}</strong></td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <span style="font-weight:600;color:#0B1437;">{{ $stat->total_punchins }} / {{ $stat->total_possible }}</span>
                                                <span class="badge badge-blue">{{ $stat->percentage }}%</span>
                                            </div>
                                            <div class="progress-track" style="margin-top:6px;height:4px;">
                                                <div class="progress-fill" style="width:{{ $stat->percentage }}%;background:#3B82F6;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($stat->early_bird_list->count() > 0)
                                                <div style="display:flex;flex-direction:column;gap:4px;">
                                                    @foreach($stat->early_bird_list->take(5) as $eb)
                                                        <div style="display:flex;align-items:center;gap:6px;">
                                                            <span style="font-size:12px;color:#4B5563;">{{ $eb->firstname }} {{ $eb->lastname }}</span>
                                                            <span style="font-size:11px;color:#8892B0;">@if($daysDiff > 1)({{ $eb->count }} times)@else({{ $eb->latest_time }})@endif</span>
                                                            <span class="badge badge-green" style="font-size:9px;padding:2px 6px;">Early Bird</span>
                                                        </div>
                                                    @endforeach
                                                    @if($stat->early_bird_list->count() > 5)<div style="font-size:11px;color:#8892B0;margin-top:2px;">+ {{ $stat->early_bird_list->count() - 5 }} more</div>@endif
                                                </div>
                                            @else
                                                <span style="font-size:12px;color:#8892B0;">No early birds</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No attendance records found for the selected period.</div>
                @endif
            </div>

            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div><div class="section-eyebrow">Monthly</div><h3 class="section-title">Monthly Attendance Rate Trend</h3></div>
                </div>
                <div class="chart-box area"><canvas id="monthlyAttendanceChart"></canvas></div>
            </div>
        </section>

        {{-- ===== LEAVE, RECRUITMENT, PAYROLL ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">HR Operations</div><h2 class="section-title">Leave Management, Recruitment & Payroll</h2></div>
            </div>
            <div class="grid grid-3">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Leave Management</div><h3 class="section-title">Leave Requests & Trends</h3></div>
                    </div>
                    <div class="grid grid-3" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value" style="color:#F59E0B;">{{ number_format($pendingLeaveRequests) }}</p><p class="mini-label">Pending</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ number_format($approvedLeaves) }}</p><p class="mini-label">Approved</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ number_format($rejectedLeaves) }}</p><p class="mini-label">Rejected</p></div>
                    </div>
                    <div class="chart-box short"><canvas id="leaveTrendChart"></canvas></div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Recruitment</div><h3 class="section-title">Hiring Pipeline</h3></div>
                    </div>
                    <div class="pipeline-list">
                        @foreach([
                            ['Open Positions', $openPositions, '#6366F1', 100],
                            ['Candidates Applied', $candidatesApplied, '#3B82F6', min(($candidatesApplied/($openPositions ?: 1))*100, 100)],
                            ['Interviews Scheduled', $interviewsScheduled, '#F59E0B', min(($interviewsScheduled/($openPositions ?: 1))*100, 100)],
                            ['Offers Released', $offersReleased, '#10B981', min(($offersReleased/($openPositions ?: 1))*100, 100)],
                        ] as $row)
                            <div class="pipeline-item">
                                <div class="pipeline-label-wrap"><span class="pipeline-dot" style="background:{{ $row[2] }}"></span><span class="pipeline-name">{{ $row[0] }}</span></div>
                                <div class="pipeline-bar-wrap"><div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:{{ $row[3] }}%;background:{{ $row[2] }}"></div></div><span class="pipeline-value">{{ number_format($row[1]) }}</span></div>
                            </div>
                        @endforeach
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap"><span class="pipeline-dot" style="background:#EF4444"></span><span class="pipeline-name">Offer Acceptance Rate</span></div>
                            <div class="pipeline-bar-wrap"><div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:{{ $offerAcceptanceRate }}%;background:#EF4444"></div></div><span class="pipeline-value">{{ $offerAcceptanceRate }}%</span></div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Payroll</div><h3 class="section-title">Payroll Summary</h3></div>
                        <span class="badge badge-amber">{{ $pendingSalaryApprovals }} Pending</span>
                    </div>
                    <div class="payroll-rows">
                        <div class="payroll-row"><span class="payroll-label">Total Salary Processed</span><span class="payroll-val val-green">₹{{ number_format($totalSalaryProcessed) }}</span></div>
                        <div class="payroll-row"><span class="payroll-label">Statutory Deductions</span><span class="payroll-val val-orange">₹{{ number_format($statutoryDeductions) }}</span></div>
                        <div class="payroll-row"><span class="payroll-label">Pending Approvals</span><span class="payroll-val val-bold">{{ number_format($pendingSalaryApprovals) }} pending</span></div>
                        <div class="payroll-row"><span class="payroll-label">Next Payroll Date</span><span class="payroll-val val-orange">{{ $upcomingPayrollDate }}</span></div>
                    </div>
                    <div class="chart-box short"><canvas id="payrollDepartmentChart"></canvas></div>
                </div>
            </div>

            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div><div class="section-eyebrow">Leave & Holiday</div><h3 class="section-title">Holiday Calendar Snapshot</h3></div>
                    <span class="badge badge-purple">{{ now()->format('F') }}</span>
                </div>
                <div class="grid grid-4">
                    <div class="mini-card"><p class="mini-value">{{ number_format($holidaysThisMonth) }}</p><p class="mini-label">Holidays This Month</p></div>
                    <div class="mini-card"><p class="mini-value">{{ $nextHoliday->holiday ?? 'None' }}</p><p class="mini-label">{{ $daysUntilNextHoliday !== null ? $daysUntilNextHoliday . ' days away' : 'No upcoming' }}</p></div>
                    <div class="mini-card"><p class="mini-value">{{ $nextHoliday && $nextHoliday->holidaydate ? Carbon::parse($nextHoliday->holidaydate)->format('d M Y') : 'N/A' }}</p><p class="mini-label">Next Holiday Date</p></div>
                    <div class="mini-card"><p class="mini-value">{{ number_format($todayLeave) }}</p><p class="mini-label">Employees on Leave Today</p></div>
                </div>
                <div class="chart-box short" style="margin-top:20px;"><canvas id="holidayMonthlyChart"></canvas></div>
            </div>
        </section>

        {{-- ===== PERFORMANCE ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Performance Management</div><h2 class="section-title">Goals, Reviews & Promotions</h2></div>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Goal Tracking</div><h3 class="section-title">Employee Goal Completion</h3></div>
                        <span class="badge badge-green">{{ $goalCompletionRate }}% complete</span>
                    </div>
                    <div class="grid grid-4" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ number_format($completedGoals) }}</p><p class="mini-label">Completed</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#3B82F6;">{{ number_format($inProgressGoals) }}</p><p class="mini-label">In Progress</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($notStartedGoals) }}</p><p class="mini-label">Not Started</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ number_format($overdueGoals) }}</p><p class="mini-label">Overdue</p></div>
                    </div>
                    <div class="chart-box short" style="margin-bottom:20px;">
                        <div class="donut-container">
                            <canvas id="goalProgressDonut"></canvas>
                            <div class="donut-center-text">
                                <div class="donut-number">{{ $completedGoals + $inProgressGoals + $notStartedGoals + $overdueGoals }}</div>
                                <div class="donut-label">goals</div>
                            </div>
                        </div>
                    </div>
                    @if($recentGoalTracks->count())
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead><tr><th>Subject</th><th>Type</th><th>Progress</th><th>End Date</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach($recentGoalTracks as $goal)
                                        @php $progressColor = $goal->progress >= 100 ? '#10B981' : ($goal->progress >= 50 ? '#3B82F6' : ($goal->progress > 0 ? '#F59E0B' : '#8892B0')); @endphp
                                        <tr>
                                            <td>{{ $goal->subject }}</td>
                                            <td>{{ $goal->goal_type ?? 'General' }}</td>
                                            <td><div class="progress-track"><div class="progress-fill" style="width:{{ $goal->progress }}%;background:{{ $progressColor }}"></div></div></td>
                                            <td>{{ $goal->end_date ? Carbon::parse($goal->end_date)->format('d M Y') : 'N/A' }}</td>
                                            <td><span class="badge badge-gray">{{ ucfirst($goal->status ?? 'N/A') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Performance Reviews</div><h3 class="section-title">Employee Performance Reviews</h3></div>
                        <span class="badge badge-amber">{{ number_format($pendingPerformanceReviews) }} pending</span>
                    </div>
                    <div class="grid grid-4" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value">{{ number_format($totalPerformanceReviews) }}</p><p class="mini-label">Total Reviews</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($performanceReviewsThisMonth) }}</p><p class="mini-label">This Month</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($performanceReviewsThisYear) }}</p><p class="mini-label">This Year</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#6366F1;">{{ $kpiAchievement }}%</p><p class="mini-label">Avg KPI Score</p></div>
                    </div>
                    <div class="chart-box short" style="margin-bottom:20px;"><canvas id="performanceReviewTrend"></canvas></div>
                    <div class="review-list">
                        @forelse($recentPerformanceReviews as $review)
                            @php $initials = strtoupper(substr($review->firstname ?? 'E', 0, 1) . substr($review->lastname ?? '', 0, 1)); @endphp
                            <div class="review-item">
                                <div class="review-person">
                                    <span class="review-avatar">{{ $initials }}</span>
                                    <div class="text-truncate">
                                        <strong>{{ $review->firstname }} {{ $review->lastname }}</strong>
                                        <p class="mini-label">{{ $review->designation ?? 'N/A' }} · {{ $review->department ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <span class="badge {{ $review->hrd_signature ?? null ? 'badge-green' : 'badge-amber' }}">{{ $review->hrd_signature ?? null ? 'Completed' : 'Pending' }}</span>
                            </div>
                        @empty
                            <div class="empty-state">No recent performance reviews found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div><div class="section-eyebrow">Promotion Records</div><h3 class="section-title">Employee Promotions</h3></div>
                    <span class="badge badge-purple">{{ $promotionRate }}% of workforce</span>
                </div>
                <div class="grid grid-4">
                    <div class="mini-card"><p class="mini-value">{{ number_format($totalPromotions) }}</p><p class="mini-label">Total Promotions</p></div>
                    <div class="mini-card"><p class="mini-value">{{ number_format($promotionsThisMonth) }}</p><p class="mini-label">This Month {{ $promotionsPercentageChange > 0 ? '+' : '' }}{{ round($promotionsPercentageChange, 1) }}%</p></div>
                    <div class="mini-card"><p class="mini-value">{{ number_format($promotionsThisYear) }}</p><p class="mini-label">This Year</p></div>
                    <div class="mini-card"><p class="mini-value">{{ $promotionRate }}%</p><p class="mini-label">Promotion Rate</p></div>
                </div>
                <div class="grid grid-2" style="margin-top:20px;">
                    <div class="chart-box area"><canvas id="promotionTrendChart"></canvas></div>
                    <div class="chart-box area"><canvas id="promotionsByDeptChart"></canvas></div>
                </div>
                @if($recentPromotions->count())
                    <div class="table-scroll" style="margin-top:20px;">
                        <table class="data-table">
                            <thead><tr><th>Employee</th><th>Department</th><th>From → To</th><th>Date</th></tr></thead>
                            <tbody>
                                @foreach($recentPromotions as $promo)
                                    <tr>
                                        <td>{{ $promo->firstname }} {{ $promo->lastname }} ({{ $promo->employeeid }})</td>
                                        <td>{{ $promo->department }}</td>
                                        <td>{{ $promo->from_designation }} &rarr; {{ $promo->to_designation }}</td>
                                        <td>{{ $promo->created_at ? Carbon::parse($promo->created_at)->format('d M Y') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        {{-- ===== HR ANALYTICS ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">HR Analytics</div><h2 class="section-title">Workforce Insights & Asset Overview</h2></div>
            </div>
            <div class="grid grid-3">
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Asset Management</div><h3 class="section-title">Employee Asset Status</h3></div></div>
                    <div class="grid grid-3">
                        <div class="mini-card"><p class="mini-value" style="color:#6366F1;">{{ number_format($assetsAssigned) }}</p><p class="mini-label">Assigned</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#F59E0B;">{{ number_format($assetsDueForReturn) }}</p><p class="mini-label">Due Return</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ number_format($lostDamagedAssets) }}</p><p class="mini-label">Lost/Damaged</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Workforce Analytics</div><h3 class="section-title">Attrition & Growth</h3></div></div>
                    <div class="grid grid-2">
                        <div class="mini-card"><p class="mini-value">{{ $employeeTurnoverRate }}%</p><p class="mini-label">Turnover Rate</p></div>
                        <div class="mini-card"><p class="mini-value">{{ $attritionRate }}%</p><p class="mini-label">Attrition Rate</p></div>
                        <div class="mini-card"><p class="mini-value">{{ $absenteeismRate }}%</p><p class="mini-label">Absenteeism Rate</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ $headcountGrowth }}%</p><p class="mini-label">Headcount Growth</p></div>
                        <div class="mini-card" style="grid-column:span 2;"><p class="mini-value">₹{{ number_format($costPerHire, 2) }}</p><p class="mini-label">Cost Per Hire</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Employee Milestones</div><h3 class="section-title">Celebrations & Updates</h3></div></div>
                    <div class="grid grid-2">
                        <div class="mini-card"><p class="mini-value" style="color:#8B5CF6;">{{ number_format($birthdaysThisMonth) }}</p><p class="mini-label">Birthdays This Month</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#6366F1;">{{ number_format($workAnniversariesThisMonth) }}</p><p class="mini-label">Work Anniversaries</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($policyUpdates) }}</p><p class="mini-label">Policy Records</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ number_format($newJoinersThisMonth) }}</p><p class="mini-label">New Joiners</p></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== HR ALERTS ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">HR Notifications</div><h2 class="section-title">Alerts & Action Items</h2></div>
            </div>
            <div class="panel">
                <div class="alert-list">
                    @foreach([
                        ['Pending Leave Requests', $pendingLeaveRequests . ' requests awaiting HR approval', $pendingLeaveRequests, '#F59E0B'],
                        ['Performance Reviews Pending', $pendingPerformanceReviews . ' reviews awaiting HR sign-off', $pendingPerformanceReviews, '#3B82F6'],
                        ['Overdue Employee Goals', $overdueGoals . ' goals past deadline', $overdueGoals, '#EF4444'],
                        ['Next Holiday', ($nextHoliday->holiday ?? 'No upcoming') . ($daysUntilNextHoliday !== null ? ' — ' . $daysUntilNextHoliday . ' days away' : ''), $holidaysThisMonth, '#10B981'],
                        ['New Employees (Last 7 Days)', $newEmployees . ' new joiners onboarded', $newEmployees, '#10B981'],
                        ['Employee Birthdays', $birthdaysThisMonth . ' birthdays this month', $birthdaysThisMonth, '#8B5CF6'],
                        ['Work Anniversaries', $workAnniversariesThisMonth . ' anniversaries this month', $workAnniversariesThisMonth, '#6366F1'],
                        ['Policy Records', $policyUpdates . ' policy documents on record', $policyUpdates, '#4B5563'],
                    ] as $alert)
                        <div class="alert-item">
                            <div class="alert-left">
                                <span class="alert-dot" style="background:{{ $alert[3] }}"></span>
                                <div>
                                    <strong style="color:#0B1437;">{{ $alert[0] }}</strong>
                                    <p class="mini-label" style="margin-top:2px;">{{ $alert[1] }}</p>
                                </div>
                            </div>
                            <span class="badge badge-gray">{{ number_format($alert[2]) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    function updateClock() {
        const clockEl = document.getElementById('live-clock');
        if (clockEl) clockEl.textContent = new Date().toTimeString().split(' ')[0];
    }
    setInterval(updateClock, 1000);
    updateClock();

    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#8892B0';
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.plugins.tooltip = { enabled: true, backgroundColor: '#0B1437', titleColor: '#FFFFFF', bodyColor: '#FFFFFF', padding: 10, cornerRadius: 8, displayColors: false };

    const gridColor = 'rgba(15,23,42,0.04)';
    const tickColor = '#8892B0';
    const baseScales = {
        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 11 } } },
        x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 11 } } }
    };

    function makeGrad(canvas, c1, c2, horiz) {
        const ctx = canvas.getContext('2d');
        const g = horiz ? ctx.createLinearGradient(0,0,400,0) : ctx.createLinearGradient(0,0,0,200);
        g.addColorStop(0, c1); g.addColorStop(1, c2); return g;
    }

    const charts = [
        ['departmentCountChart', 'bar', @json($deptLabels), @json($deptCounts), true, '#6366F1', '#C7D2FE'],
        ['holidayMonthlyChart', 'bar', @json($holidayMonthlyLabels), @json($holidayMonthlyData), false, '#6366F1', '#C7D2FE'],
        ['promotionTrendChart', 'bar', @json($promotionTrendLabels), @json($promotionTrendData), false, '#6366F1', '#C7D2FE'],
        ['payrollDepartmentChart', 'bar', @json($payrollDeptLabels), @json($payrollDeptAmounts), false, '#6366F1', '#C7D2FE'],
    ];

    charts.forEach(([id, type, labels, data, horiz, c1, c2]) => {
        const el = document.getElementById(id);
        if (!el) return;
        const grad = makeGrad(el, c1, c2, horiz);
        new Chart(el, {
            type,
            data: { labels, datasets: [{ data, backgroundColor: grad, borderRadius: 8, borderWidth: 0 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                ...(horiz ? { indexAxis: 'y' } : {}),
                plugins: { legend: { display: false } },
                scales: horiz
                    ? { x: { grid: { display: false }, ticks: { color: tickColor } }, y: { grid: { color: gridColor }, ticks: { color: tickColor } } }
                    : baseScales
            }
        });
    });

    function makeLineChart(id, labels, data, color) {
        const el = document.getElementById(id);
        if (!el) return;
        const ctx = el.getContext('2d');
        const grad = ctx.createLinearGradient(0,0,0,200);
        grad.addColorStop(0, color.replace(')', ',0.2)').replace('rgb', 'rgba'));
        grad.addColorStop(1, color.replace(')', ',0.0)').replace('rgb', 'rgba'));
        new Chart(el, {
            type: 'line',
            data: { labels, datasets: [{ data, borderColor: color, backgroundColor: grad, borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: color, pointBorderColor: '#FFFFFF', pointBorderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: baseScales }
        });
    }

    makeLineChart('leaveTrendChart', @json($leaveTrendLabels), @json($leaveTrendData), '#6366F1');
    makeLineChart('weeklyAttendanceChart', @json($weeklyAttendanceLabels), @json($weeklyAttendanceData), '#6366F1');
    makeLineChart('performanceReviewTrend', @json($performanceReviewTrendLabels), @json($performanceReviewTrendData), '#6366F1');

    const monthlyEl = document.getElementById('monthlyAttendanceChart');
    if (monthlyEl) {
        const ctx = monthlyEl.getContext('2d');
        const grad = ctx.createLinearGradient(0,0,0,200);
        grad.addColorStop(0,'rgba(99,102,241,0.2)'); grad.addColorStop(1,'rgba(99,102,241,0.0)');
        new Chart(monthlyEl, {
            type: 'line',
            data: { labels: @json($monthlyAttendanceLabels), datasets: [{ data: @json($monthlyAttendanceData), borderColor: '#6366F1', backgroundColor: grad, borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#6366F1', pointBorderColor: '#FFFFFF', pointBorderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 11 } } }, y: { beginAtZero: true, max: 100, ticks: { stepSize: 20, color: tickColor, callback: v => v + '%' }, grid: { color: gridColor } } } }
        });
    }

    const donutEl = document.getElementById('todayAttendanceDonut');
    if (donutEl) {
        new Chart(donutEl, {
            type: 'doughnut',
            data: { labels: ['Present','Absent','Late','On Leave'], datasets: [{ data: [{{ $presentToday }},{{ $absentToday }},{{ $lateArrivalsToday }},{{ $todayLeave }}], backgroundColor: ['#10B981','#EF4444','#F59E0B','#3B82F6'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
    }

    const goalDonut = document.getElementById('goalProgressDonut');
    if (goalDonut) {
        new Chart(goalDonut, {
            type: 'doughnut',
            data: { labels: ['Completed','In Progress','Not Started','Overdue'], datasets: [{ data: [{{ $completedGoals }},{{ $inProgressGoals }},{{ $notStartedGoals }},{{ $overdueGoals }}], backgroundColor: ['#10B981','#3B82F6','#8892B0','#EF4444'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
    }

    const deptPromoEl = document.getElementById('promotionsByDeptChart');
    if (deptPromoEl) {
        const grad = makeGrad(deptPromoEl, '#6366F1', '#C7D2FE', true);
        new Chart(deptPromoEl, {
            type: 'bar',
            data: { labels: @json($promotionDeptLabels), datasets: [{ data: @json($promotionDeptCounts), backgroundColor: grad, borderRadius: 6, borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: tickColor } }, y: { grid: { color: gridColor }, ticks: { color: tickColor } } } }
        });
    }
});
</script>
<script>
(function pollChatUnread() {
    function refresh() {
        fetch('{{ route('chat.hr.conversations') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(function(convs) {
            const total = convs.reduce((s, c) => s + (c.unread_count || 0), 0);
            const badge = document.getElementById('hr-chat-unread');
            if (!badge) return;
            if (total > 0) {
                badge.textContent = total > 99 ? '99+' : total;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(function(){});
    }
    refresh();
    setInterval(refresh, 5000);
})();
</script>
@endsection
