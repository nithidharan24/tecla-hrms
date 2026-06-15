@extends('layouts.index')

@section('content')
@php use Carbon\Carbon; @endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .mgr-dashboard {
        background: #F5F6FA !important;
        color: #0B1437 !important;
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        padding: 0 0 40px;
        width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    .mgr-dashboard * { box-sizing: border-box; }

    .mgr-dashboard .dashboard-body {
        padding: 32px 40px;
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }

    .mgr-dashboard h1,.mgr-dashboard h2,.mgr-dashboard h3,
    .mgr-dashboard h4,.mgr-dashboard h5,.mgr-dashboard h6,
    .mgr-dashboard p,.mgr-dashboard span,.mgr-dashboard td,.mgr-dashboard th {
        font-family: 'DM Sans', sans-serif !important;
    }

    /* Header */
    .mgr-dashboard .dashboard-header {
        align-items: center; background: #FFFFFF !important;
        border-bottom: 1px solid #E5E7EB !important;
        display: flex; justify-content: space-between; flex-wrap: wrap;
        min-height: 70px; padding: 16px 60px;
        position: sticky; top: 0; z-index: 20;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
    }
    .mgr-dashboard .dashboard-header h1 { color: #0B1437 !important; font-size: 22px; font-weight: 700; margin: 0 0 4px; letter-spacing: -0.5px; }
    .mgr-dashboard .header-status { display: flex; align-items: center; gap: 12px; font-size: 13px; color: #4B5563; }
    .mgr-dashboard .status-time { font-weight: 500; color: #0B1437; }
    .mgr-dashboard .status-live { display: inline-flex; align-items: center; gap: 6px; background: #ECFDF5; color: #10B981; padding: 2px 8px; border-radius: 20px; font-weight: 600; font-size: 11px; }
    .mgr-dashboard .live-dot { width: 6px; height: 6px; background-color: #10B981; border-radius: 50%; display: inline-block; position: relative; }
    .mgr-dashboard .live-dot::after { content: ''; position: absolute; top: -1px; left: -1px; width: 6px; height: 6px; border-radius: 50%; border: 1px solid #10B981; animation: mgr-pulse 1.8s infinite; }
    @keyframes mgr-pulse { 0%{transform:scale(1);opacity:1} 100%{transform:scale(2.8);opacity:0} }

    /* Grid */
    .mgr-dashboard .section-block { margin-top: 32px; }
    .mgr-dashboard .section-head { align-items: flex-end; display: flex; justify-content: space-between; margin-bottom: 18px; }
    .mgr-dashboard .section-eyebrow { color: #8892B0; font-size: 11px; font-weight: 700; letter-spacing: .1em; margin-bottom: 6px; text-transform: uppercase; }
    .mgr-dashboard .section-title { color: #0B1437; font-size: 20px; font-weight: 700; margin: 0; letter-spacing: -0.3px; }
    .mgr-dashboard .grid { display: grid; gap: 20px; }
    .mgr-dashboard .grid-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .mgr-dashboard .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .mgr-dashboard .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .mgr-dashboard .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    /* Cards */
    .mgr-dashboard .stat-card,
    .mgr-dashboard .panel,
    .mgr-dashboard .mini-card {
        background: #FFFFFF !important; border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(11,20,55,.03), 0 2px 4px -1px rgba(11,20,55,.02) !important;
        position: relative; transition: all 0.25s cubic-bezier(0.4,0,0.2,1) !important;
        animation: mgrFadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
    }
    .mgr-dashboard .stat-card { min-height: 146px; overflow: hidden; padding: 22px 24px !important; border-bottom: 3px solid transparent !important; display: flex; flex-direction: column; justify-content: space-between; }
    .mgr-dashboard .stat-card:hover,.mgr-dashboard .panel:hover,.mgr-dashboard .mini-card:hover { transform: translateY(-4px) !important; box-shadow: 0 12px 20px -8px rgba(11,20,55,.08),0 4px 12px -2px rgba(11,20,55,.03) !important; }

    .mgr-dashboard .stat-card:nth-child(1) { border-bottom-color: #6366F1 !important; animation-delay:.05s; }
    .mgr-dashboard .stat-card:nth-child(1) .stat-icon { background:#EEF2FF!important; color:#6366F1!important; }
    .mgr-dashboard .stat-card:nth-child(2) { border-bottom-color: #10B981 !important; animation-delay:.10s; }
    .mgr-dashboard .stat-card:nth-child(2) .stat-icon { background:#ECFDF5!important; color:#10B981!important; }
    .mgr-dashboard .stat-card:nth-child(3) { border-bottom-color: #EF4444 !important; animation-delay:.15s; }
    .mgr-dashboard .stat-card:nth-child(3) .stat-icon { background:#FEF2F2!important; color:#EF4444!important; }
    .mgr-dashboard .stat-card:nth-child(4) { border-bottom-color: #F59E0B !important; animation-delay:.20s; }
    .mgr-dashboard .stat-card:nth-child(4) .stat-icon { background:#FFFBEB!important; color:#F59E0B!important; }
    .mgr-dashboard .stat-card:nth-child(5) { border-bottom-color: #F97316 !important; animation-delay:.25s; }
    .mgr-dashboard .stat-card:nth-child(5) .stat-icon { background:#FFEFE6!important; color:#F97316!important; }
    .mgr-dashboard .stat-card:nth-child(6) { border-bottom-color: #8B5CF6 !important; animation-delay:.30s; }
    .mgr-dashboard .stat-card:nth-child(6) .stat-icon { background:#F5F3FF!important; color:#8B5CF6!important; }

    .mgr-dashboard .stat-top { align-items: center; display: flex; justify-content: space-between; margin-bottom: 20px; }
    .mgr-dashboard .stat-icon { align-items: center; border-radius: 12px; display: inline-flex; height: 42px; justify-content: center; width: 42px; font-size: 18px; }
    .mgr-dashboard .stat-value { color: #0B1437 !important; font-size: 28px; font-weight: 700; line-height: 1.1; margin: 0 0 6px; letter-spacing: -0.5px; }
    .mgr-dashboard .stat-label { color: #4B5563 !important; font-size: 13px; font-weight: 600; margin: 0; }
    .mgr-dashboard .stat-sub, .mgr-dashboard .mini-label { color: #8892B0 !important; font-size: 12px; margin: 0; }
    .mgr-dashboard .panel { padding: 24px !important; }
    .mgr-dashboard .panel-header { align-items: flex-start; display: flex; gap: 12px; justify-content: space-between; margin-bottom: 20px; }
    .mgr-dashboard .mini-card { padding: 18px !important; }
    .mgr-dashboard .mini-value { color: #0B1437; font-size: 22px; font-weight: 700; line-height: 1.1; margin: 0 0 6px; letter-spacing: -0.3px; }

    /* Chart */
    .mgr-dashboard .chart-box { height: 220px; position: relative; width: 100%; overflow: hidden; }
    .mgr-dashboard .chart-box canvas { max-height: 220px; }
    .mgr-dashboard .chart-box.short { height: 180px; }
    .mgr-dashboard .chart-box.short canvas { max-height: 180px; }
    .mgr-dashboard .donut-container { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .mgr-dashboard .donut-center-text { position: absolute; text-align: center; pointer-events: none; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .mgr-dashboard .donut-number { font-size: 26px; font-weight: 700; color: #0B1437; line-height: 1; letter-spacing: -0.5px; }
    .mgr-dashboard .donut-label { font-size: 11px; color: #8892B0; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; font-weight: 700; }
    .mgr-dashboard .donut-wrap { align-items: center; display: grid; gap: 20px; grid-template-columns: minmax(150px,1fr) minmax(150px,180px); }
    .mgr-dashboard .legend-list { display: flex; flex-direction: column; gap: 12px; }
    .mgr-dashboard .legend-item { align-items: center; display: flex; justify-content: space-between; gap: 12px; }
    .mgr-dashboard .legend-left { align-items: center; display: flex; gap: 12px; }
    .mgr-dashboard .legend-dot { border-radius: 50%; flex: 0 0 auto; height: 10px; width: 10px; }

    /* Table */
    .mgr-dashboard .table-scroll { overflow-x: auto; border-radius: 12px; border: 1px solid #E5E7EB; }
    .mgr-dashboard .data-table { border-collapse: collapse; font-size: 13px; width: 100%; background: #FFFFFF; }
    .mgr-dashboard .data-table th { background: #F9FAFB; border-bottom: 1px solid #E5E7EB; color: #4B5563; font-size: 11px; font-weight: 700; letter-spacing: .05em; padding: 12px 16px; text-align: left; text-transform: uppercase; }
    .mgr-dashboard .data-table td { border-bottom: 1px solid #F3F4F6; color: #0B1437; padding: 14px 16px; vertical-align: middle; }
    .mgr-dashboard .data-table tbody tr:last-child td { border-bottom: none; }
    .mgr-dashboard .data-table tbody tr:hover td { background: #F5F6FA; }

    /* Badges */
    .mgr-dashboard .badge { border-radius: 20px; display: inline-flex; font-size: 11px; font-weight: 700; line-height: 1; padding: 6px 12px; white-space: nowrap; }
    .mgr-dashboard .badge-green { background: #ECFDF5; color: #047857; }
    .mgr-dashboard .badge-amber { background: #FFFBEB; color: #B45309; }
    .mgr-dashboard .badge-red { background: #FEF2F2; color: #B91C1C; }
    .mgr-dashboard .badge-blue { background: #EFF6FF; color: #1D4ED8; }
    .mgr-dashboard .badge-gray { background: #F3F4F6; color: #374151; }
    .mgr-dashboard .badge-orange { background: #FFF7ED; color: #F97316; }

    /* Progress */
    .mgr-dashboard .progress-track { background: #E5E7EB; border-radius: 4px; height: 6px; overflow: hidden; }
    .mgr-dashboard .progress-fill { border-radius: 4px; height: 100%; transition: width .4s ease; }

    /* Pipeline */
    .mgr-dashboard .pipeline-list { display: flex; flex-direction: column; gap: 14px; margin-top: 6px; }
    .mgr-dashboard .pipeline-item { display: flex; flex-direction: column; gap: 6px; }
    .mgr-dashboard .pipeline-label-wrap { display: flex; align-items: center; gap: 10px; }
    .mgr-dashboard .pipeline-dot { width: 8px; height: 8px; border-radius: 50%; }
    .mgr-dashboard .pipeline-name { font-size: 13px; font-weight: 500; color: #4B5563; }
    .mgr-dashboard .pipeline-bar-wrap { display: flex; align-items: center; gap: 12px; }
    .mgr-dashboard .pipeline-bar-track { flex: 1; height: 6px; background: #E5E7EB; border-radius: 4px; overflow: hidden; }
    .mgr-dashboard .pipeline-bar-fill { height: 100%; border-radius: 4px; transition: width 0.8s ease-out; }
    .mgr-dashboard .pipeline-value { font-size: 13px; font-weight: 700; color: #0B1437; min-width: 24px; text-align: right; }

    .mgr-dashboard .empty-state { align-items: center; color: #8892B0; display: flex; justify-content: center; min-height: 96px; text-align: center; font-weight: 500; }
    .mgr-dashboard .view-link { color: #F97316; font-size: 13px; font-weight: 700; text-decoration: none; }
    .mgr-dashboard .view-link:hover { color: #EA580C; text-decoration: underline !important; }
    .mgr-dashboard .review-avatar { align-items: center; background: #FFF7ED; border-radius: 50%; color: #F97316; display: inline-flex; flex: 0 0 auto; font-weight: 700; height: 38px; justify-content: center; width: 38px; border: 2px solid rgba(249,115,22,0.1); font-size: 13px; }

    .mgr-dashboard .card-clickable { cursor: pointer; }
    .mgr-dashboard .card-clickable:hover { outline: 2px solid rgba(249,115,22,0.3); }

    @keyframes mgrFadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }

    @media(max-width:1400px) { .mgr-dashboard .grid-6{grid-template-columns:repeat(3,minmax(0,1fr))} .mgr-dashboard .grid-4{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media(max-width:991px) { .mgr-dashboard .grid-6,.mgr-dashboard .grid-4,.mgr-dashboard .grid-3,.mgr-dashboard .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))} .mgr-dashboard .donut-wrap{grid-template-columns:1fr!important} }
    @media(max-width:767px) { .mgr-dashboard .dashboard-body{padding:10px 8px!important} .mgr-dashboard .grid-6,.mgr-dashboard .grid-4,.mgr-dashboard .grid-3,.mgr-dashboard .grid-2{grid-template-columns:1fr!important} .mgr-dashboard .dashboard-header{padding:12px 16px!important;flex-direction:column;gap:8px} }
</style>

<div class="mgr-dashboard">

    {{-- ── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="dashboard-header">
        <div>
            <h1>Welcome back, {{ session('first_name') }} 👋</h1>
            <div class="header-status">
                <span class="status-time" id="mgr-live-clock"></span>
                <span class="status-live"><span class="live-dot"></span> Live</span>
                <span style="color:#8892B0">Dept: <strong style="color:#0B1437">{{ $departmentName }}</strong></span>
            </div>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <button onclick="window.print()" style="background:#FFF7ED;color:#F97316;border:1px solid rgba(249,115,22,.2);border-radius:10px;padding:10px 18px;font-size:14px;font-weight:600;cursor:pointer;">Export</button>
        </div>
    </div>

    <div class="dashboard-body">

        {{-- ── KPI CARDS ──────────────────────────────────────────────── --}}
        <div class="grid grid-6">
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-users"></i></span><span class="badge badge-blue">Team</span></div>
                <p class="stat-value">{{ $teamStrength }}</p>
                <p class="stat-label">Team Strength</p>
                <p class="stat-sub">{{ $activeTeamCount }} active members</p>
            </div>
            <div class="stat-card card-clickable" onclick="openMgrModal('present')" title="View present members">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-check"></i></span><span class="badge badge-green">Today</span></div>
                <p class="stat-value">{{ $presentToday }}</p>
                <p class="stat-label">Present Today</p>
                <p class="stat-sub">Click to view details</p>
            </div>
            <div class="stat-card card-clickable" onclick="openMgrModal('absent')" title="View absent members">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-times"></i></span><span class="badge badge-red">Today</span></div>
                <p class="stat-value">{{ $absentToday }}</p>
                <p class="stat-label">Absent Today</p>
                <p class="stat-sub">Click to view details</p>
            </div>
            <div class="stat-card card-clickable" onclick="openMgrModal('late')" title="View late arrivals">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-clock"></i></span><span class="badge badge-amber">Exception</span></div>
                <p class="stat-value">{{ $lateArrivals }}</p>
                <p class="stat-label">Late Arrivals</p>
                <p class="stat-sub">Click to view details</p>
            </div>
            <div class="stat-card card-clickable" onclick="openMgrApprovals('leaves')" title="Approve / Decline leave requests">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-umbrella-beach"></i></span><span class="badge badge-amber">Action</span></div>
                <p class="stat-value" id="mgr-leave-count">{{ $pendingLeavesCount }}</p>
                <p class="stat-label">Pending Leaves</p>
                <p class="stat-sub">Click to approve / decline</p>
            </div>
            <div class="stat-card card-clickable" onclick="openMgrApprovals('permissions')" title="Approve / Decline permission requests">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-door-open"></i></span><span class="badge badge-orange">Action</span></div>
                <p class="stat-value" id="mgr-perm-count">{{ $pendingPermissionsCount }}</p>
                <p class="stat-label">Pending Permissions</p>
                <p class="stat-sub">Click to approve / decline</p>
            </div>
        </div>

        {{-- ── ATTENDANCE ─────────────────────────────────────────────── --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Attendance Overview</div><h2 class="section-title">Today's Movement — {{ $departmentName }}</h2></div>
                <span class="badge badge-blue">{{ $teamStrength }} total</span>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Trend</div><h3 class="section-title">7-Day Attendance Trend</h3></div></div>
                    <div class="chart-box"><canvas id="mgrAttendanceChart"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Breakdown</div><h3 class="section-title">Today's Breakdown</h3></div></div>
                    <div class="donut-wrap">
                        <div class="chart-box">
                            <div class="donut-container">
                                <canvas id="mgrAttendanceDonut"></canvas>
                                <div class="donut-center-text">
                                    <div class="donut-number">{{ $teamStrength }}</div>
                                    <div class="donut-label">total</div>
                                </div>
                            </div>
                        </div>
                        <div class="legend-list">
                            @foreach([['Present',$presentToday,'#10B981'],['Absent',$absentToday,'#EF4444'],['Late',$lateArrivals,'#F59E0B']] as $item)
                            <div class="legend-item">
                                <span class="legend-left"><span class="legend-dot" style="background:{{ $item[2] }}"></span>{{ $item[0] }}</span>
                                <strong>{{ $item[1] }}</strong>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Attendance Table --}}
            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div><div class="section-eyebrow">Recent</div><h3 class="section-title">Recent Attendance Records</h3></div>
                </div>
                @if($recentAttendance->count())
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>Employee</th><th>Designation</th><th>Check In</th><th>Check Out</th><th>Date</th></tr></thead>
                        <tbody>
                            @foreach($recentAttendance as $a)
                            <tr>
                                <td><strong>{{ $a->firstname }} {{ $a->lastname }}</strong></td>
                                <td>{{ $a->designation ?? '—' }}</td>
                                <td>{{ $a->punch_in ? Carbon::parse($a->punch_in)->format('h:i A') : '—' }}</td>
                                <td>{{ $a->punch_out ? Carbon::parse($a->punch_out)->format('h:i A') : '—' }}</td>
                                <td>{{ Carbon::parse($a->date)->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">No recent attendance records.</div>
                @endif
            </div>
        </section>

        {{-- ── LEAVE, INTERVIEW, EMPLOYEE STATUS ─────────────────────── --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Department Operations</div><h2 class="section-title">Leave, Recruitment & Team Status</h2></div>
            </div>
            <div class="grid grid-3">

                {{-- Leave Summary --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Leave Management</div><h3 class="section-title">Leave Summary</h3></div></div>
                    <div class="grid grid-3" style="margin-bottom:16px;">
                        <div class="mini-card card-clickable" onclick="openMgrApprovals('leaves')"><p class="mini-value" style="color:#F97316;">{{ $pendingLeavesCount }}</p><p class="mini-label">Pending</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ $approvedLeaves }}</p><p class="mini-label">Approved</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ $rejectedLeaves }}</p><p class="mini-label">Rejected</p></div>
                    </div>
                    @php
                        $total = $pendingLeavesCount + $approvedLeaves + $rejectedLeaves;
                        $appPct = $total > 0 ? round($approvedLeaves/$total*100) : 0;
                        $rejPct = $total > 0 ? round($rejectedLeaves/$total*100) : 0;
                        $penPct = $total > 0 ? round($pendingLeavesCount/$total*100) : 0;
                    @endphp
                    <div style="margin-top:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;"><span style="color:#10B981">Approved</span><span>{{ $appPct }}%</span></div>
                        <div class="progress-track" style="margin-bottom:10px;"><div class="progress-fill" style="width:{{ $appPct }}%;background:#10B981"></div></div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;"><span style="color:#F59E0B">Pending</span><span>{{ $penPct }}%</span></div>
                        <div class="progress-track" style="margin-bottom:10px;"><div class="progress-fill" style="width:{{ $penPct }}%;background:#F59E0B"></div></div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;"><span style="color:#EF4444">Rejected</span><span>{{ $rejPct }}%</span></div>
                        <div class="progress-track"><div class="progress-fill" style="width:{{ $rejPct }}%;background:#EF4444"></div></div>
                    </div>
                </div>

                {{-- Interview Pipeline --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Recruitment</div><h3 class="section-title">Interview Pipeline</h3></div></div>
                    <div class="pipeline-list">
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap"><span class="pipeline-dot" style="background:#F97316"></span><span class="pipeline-name">Open Positions</span></div>
                            <div class="pipeline-bar-wrap"><div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:100%;background:#F97316"></div></div><span class="pipeline-value">{{ $openPositions }}</span></div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap"><span class="pipeline-dot" style="background:#F59E0B"></span><span class="pipeline-name">Scheduled Interviews</span></div>
                            <div class="pipeline-bar-wrap"><div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:{{ $openPositions > 0 ? min($interviewsScheduled/$openPositions*100,100) : 0 }}%;background:#F59E0B"></div></div><span class="pipeline-value">{{ $interviewsScheduled }}</span></div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap"><span class="pipeline-dot" style="background:#10B981"></span><span class="pipeline-name">Interviews Today</span></div>
                            <div class="pipeline-bar-wrap"><div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:{{ $interviewsScheduled > 0 ? min($interviewsToday/$interviewsScheduled*100,100) : 0 }}%;background:#10B981"></div></div><span class="pipeline-value">{{ $interviewsToday }}</span></div>
                        </div>
                    </div>
                    @if($recentInterviews->count())
                    <div class="table-scroll" style="margin-top:16px;">
                        <table class="data-table">
                            <thead><tr><th>Candidate</th><th>Position</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($recentInterviews as $iv)
                                <tr>
                                    <td>{{ $iv->candidate_name }}</td>
                                    <td>{{ $iv->job_title }}</td>
                                    <td>{{ Carbon::parse($iv->interview_datetime)->format('d M h:i A') }}</td>
                                    <td><span class="badge {{ $iv->status === 'completed' ? 'badge-green' : ($iv->status === 'scheduled' ? 'badge-blue' : 'badge-gray') }}">{{ ucfirst($iv->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Employee Status --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Team Status</div><h3 class="section-title">Employee Overview</h3></div></div>
                    <div class="grid grid-3" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ $activeTeamCount }}</p><p class="mini-label">Active</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#3B82F6;">{{ $newJoinersThisMonth }}</p><p class="mini-label">New Joiners</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ $onNoticePeriod }}</p><p class="mini-label">Notice Period</p></div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;max-height:200px;overflow-y:auto;">
                        @foreach($recentEmployees as $emp)
                        <div style="display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid #F3F4F6;">
                            @if($emp->profile_image)
                                <img src="{{ asset($emp->profile_image) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            @else
                                <div class="review-avatar" style="width:32px;height:32px;font-size:11px;flex-shrink:0;">{{ strtoupper(substr($emp->firstname,0,1).substr($emp->lastname,0,1)) }}</div>
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $emp->firstname }} {{ $emp->lastname }}</div>
                                <div class="mini-label">{{ $emp->designation ?? 'N/A' }}</div>
                            </div>
                            <span class="badge {{ $emp->status === 'active' ? 'badge-green' : (in_array($emp->status,['notice','on_notice','resigned']) ? 'badge-amber' : 'badge-gray') }}" style="font-size:10px;">{{ ucfirst($emp->status ?? 'N/A') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ── TASK OVERVIEW ───────────────────────────────────────────── --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Task Management</div><h2 class="section-title">Task Overview</h2></div>
            </div>
            <div class="grid grid-4">
                <div class="stat-card">
                    <div class="stat-top"><span class="stat-icon"><i class="fa fa-tasks"></i></span><span class="badge badge-blue">All</span></div>
                    <p class="stat-value">{{ $assignedTasks }}</p>
                    <p class="stat-label">Assigned Tasks</p>
                    <p class="stat-sub">Total assigned to team</p>
                </div>
                <div class="stat-card">
                    <div class="stat-top"><span class="stat-icon"><i class="fa fa-check-circle"></i></span><span class="badge badge-green">Done</span></div>
                    <p class="stat-value">{{ $completedTasks }}</p>
                    <p class="stat-label">Completed Tasks</p>
                    <p class="stat-sub">{{ $assignedTasks > 0 ? round($completedTasks/$assignedTasks*100) : 0 }}% completion rate</p>
                </div>
                <div class="stat-card">
                    <div class="stat-top"><span class="stat-icon"><i class="fa fa-hourglass-half"></i></span><span class="badge badge-amber">WIP</span></div>
                    <p class="stat-value">{{ $pendingTasks }}</p>
                    <p class="stat-label">Pending Tasks</p>
                    <p class="stat-sub">In progress / not started</p>
                </div>
                <div class="stat-card">
                    <div class="stat-top"><span class="stat-icon"><i class="fa fa-exclamation-triangle"></i></span><span class="badge badge-red">Overdue</span></div>
                    <p class="stat-value">{{ $overdueTasks }}</p>
                    <p class="stat-label">Overdue Tasks</p>
                    <p class="stat-sub">Past due date</p>
                </div>
            </div>

            <div class="grid grid-2" style="margin-top:20px;">
                {{-- Team Productivity --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">This Month</div><h3 class="section-title">Team Productivity Graph</h3></div></div>
                    @if($teamProductivity->count())
                    <div class="chart-box short"><canvas id="mgrProductivityChart"></canvas></div>
                    @else
                    <div class="empty-state">No completed tasks this month.</div>
                    @endif
                </div>

                {{-- Project-wise Task Status --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Projects</div><h3 class="section-title">Project-wise Task Status</h3></div></div>
                    @if($projectTaskStatus->count())
                    <div class="pipeline-list">
                        @foreach($projectTaskStatus as $proj)
                        @php $pct = $proj->total > 0 ? round($proj->completed/$proj->total*100) : 0; @endphp
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap" style="justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span class="pipeline-dot" style="background:#6366F1"></span>
                                    <span class="pipeline-name">{{ $proj->projectname }}</span>
                                </div>
                                <div style="display:flex;gap:8px;">
                                    <span style="font-size:11px;background:#ECFDF5;color:#047857;border-radius:20px;padding:2px 8px;font-weight:700;">{{ $proj->completed }} done</span>
                                    @if($proj->overdue > 0)<span style="font-size:11px;background:#FEF2F2;color:#B91C1C;border-radius:20px;padding:2px 8px;font-weight:700;">{{ $proj->overdue }} overdue</span>@endif
                                </div>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track"><div class="pipeline-bar-fill" style="width:{{ $pct }}%;background:#6366F1"></div></div>
                                <span class="pipeline-value">{{ $pct }}%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">No project tasks found.</div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ── ATTENDANCE EXCEPTIONS ───────────────────────────────────── --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Attendance Exceptions</div><h2 class="section-title">Planned Leaves, Missing Check-ins & Early Check-outs</h2></div>
            </div>
            <div class="grid grid-3">
                {{-- Upcoming Planned Leaves --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Upcoming</div><h3 class="section-title">Planned Leaves</h3></div></div>
                    @if($upcomingPlannedLeaves->count())
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead><tr><th>Employee</th><th>Type</th><th>From</th><th>Days</th></tr></thead>
                            <tbody>
                                @foreach($upcomingPlannedLeaves as $ul)
                                <tr>
                                    <td><strong>{{ $ul->firstname }} {{ $ul->lastname }}</strong><br><span class="mini-label">{{ $ul->designation ?? '' }}</span></td>
                                    <td><span class="badge badge-blue">{{ $ul->leave_type }}</span></td>
                                    <td>{{ Carbon::parse($ul->from_date)->format('d M') }}</td>
                                    <td>{{ $ul->no_of_days }}d</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">No upcoming planned leaves.</div>
                    @endif
                </div>

                {{-- Missing Check-ins --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Today</div><h3 class="section-title">Missing Check-ins</h3></div></div>
                    @if($missingCheckIns->count())
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead><tr><th>Employee</th><th>Designation</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($missingCheckIns as $mc)
                                <tr>
                                    <td><strong>{{ $mc->firstname }} {{ $mc->lastname }}</strong></td>
                                    <td>{{ $mc->designation ?? '—' }}</td>
                                    <td><span class="badge badge-red">No Check-in</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">All team members checked in today.</div>
                    @endif
                </div>

                {{-- Early Check-outs --}}
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Today</div><h3 class="section-title">Early Check-outs</h3></div></div>
                    @if($earlyCheckOuts->count())
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead><tr><th>Employee</th><th>Check Out</th><th>Shift End</th></tr></thead>
                            <tbody>
                                @foreach($earlyCheckOuts as $ec)
                                <tr>
                                    <td><strong>{{ $ec->firstname }} {{ $ec->lastname }}</strong><br><span class="mini-label">{{ $ec->designation ?? '' }}</span></td>
                                    <td>{{ Carbon::parse($ec->punch_out)->format('h:i A') }}</td>
                                    <td><span class="badge badge-amber">{{ Carbon::parse($ec->end_time)->format('h:i A') }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">No early check-outs today.</div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ── UPCOMING HOLIDAYS ─────────────────────────────────────── --}}
        @if($upcomingHolidays->count())
        <section class="section-block">
            <div class="section-head"><div><div class="section-eyebrow">Calendar</div><h2 class="section-title">Upcoming Holidays</h2></div></div>
            <div class="panel">
                <div class="grid grid-4">
                    @foreach($upcomingHolidays->take(8) as $h)
                    <div class="mini-card" style="text-align:center;">
                        <p class="mini-value" style="font-size:16px;">{{ Carbon::parse($h->holidaydate)->format('d M') }}</p>
                        <p class="mini-label">{{ $h->holidayname }}</p>
                        <p class="mini-label" style="color:#3B82F6;">{{ Carbon::parse($h->holidaydate)->diffInDays(now()) }} days away</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

    </div>
</div>

{{-- ══ ATTENDANCE DETAIL MODAL ════════════════════════════════════════════ --}}
<div id="mgrAttModal" style="display:none;position:fixed;inset:0;background:rgba(11,20,55,.55);z-index:10000;overflow-y:auto;padding:20px 12px;">
    <div style="background:#fff;border-radius:18px;max-width:760px;margin:40px auto;padding:32px;position:relative;box-shadow:0 24px 64px rgba(11,20,55,.18);font-family:'DM Sans',sans-serif;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;gap:12px;">
            <div>
                <p id="mgrAttEyebrow" style="font-size:11px;font-weight:700;color:#8892B0;text-transform:uppercase;letter-spacing:.1em;margin:0 0 6px;"></p>
                <h2 id="mgrAttTitle" style="font-size:20px;font-weight:700;color:#0B1437;margin:0;"></h2>
            </div>
            <button onclick="closeMgrModal()" style="flex-shrink:0;background:#F3F4F6;border:none;cursor:pointer;color:#4B5563;font-size:20px;padding:6px 12px;border-radius:8px;font-weight:700;">&times;</button>
        </div>
        <div id="mgrAttContent"></div>
    </div>
</div>

{{-- ══ APPROVALS MODAL ════════════════════════════════════════════════════ --}}
<div id="mgrAprModal" style="display:none;position:fixed;inset:0;background:rgba(11,20,55,.55);z-index:10000;overflow-y:auto;padding:20px 12px;">
    <div style="background:#fff;border-radius:18px;max-width:920px;margin:32px auto;padding:28px 32px;position:relative;box-shadow:0 24px 64px rgba(11,20,55,.18);font-family:'DM Sans',sans-serif;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;gap:12px;">
            <div>
                <p style="font-size:11px;font-weight:700;color:#8892B0;text-transform:uppercase;letter-spacing:.1em;margin:0 0 5px;">Manager · Action Required</p>
                <h2 style="font-size:20px;font-weight:700;color:#0B1437;margin:0;">Pending Approvals</h2>
            </div>
            <button onclick="closeMgrApprovals()" style="flex-shrink:0;background:#F3F4F6;border:none;cursor:pointer;color:#4B5563;font-size:20px;padding:6px 12px;border-radius:8px;font-weight:700;">&times;</button>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;gap:8px;margin-bottom:20px;border-bottom:1px solid #E5E7EB;flex-wrap:wrap;">
            <button class="mgr-apr-tab mgr-apr-tab-active" onclick="switchMgrAprTab('leaves',this)" id="mgrtab-leaves">
                Leaves <span id="mgrtabcount-leaves" style="background:#F97316;color:#fff;display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border-radius:20px;font-size:11px;font-weight:700;margin-left:6px;">{{ $pendingLeavesCount }}</span>
            </button>
            <button class="mgr-apr-tab" onclick="switchMgrAprTab('permissions',this)" id="mgrtab-permissions">
                Permissions <span id="mgrtabcount-permissions" style="background:#3B82F6;color:#fff;display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border-radius:20px;font-size:11px;font-weight:700;margin-left:6px;">{{ $pendingPermissionsCount }}</span>
            </button>
        </div>

        {{-- Leaves Panel --}}
        <div id="mgr-panel-leaves">
            @if($pendingLeavesList->count())
            <div style="overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;background:#fff;">
                    <thead><tr>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">#</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Employee</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Leave Type</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">From</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">To</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:center;">Days</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Reason</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:center;">Action</th>
                    </tr></thead>
                    <tbody>
                        @foreach($pendingLeavesList as $i => $leave)
                        <tr data-mgr-row="leave-{{ $leave->id }}">
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;color:#0B1437;">{{ $i+1 }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;"><strong>{{ $leave->firstname }} {{ $leave->lastname }}</strong></td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;"><span style="background:#FFF7ED;color:#F97316;border-radius:20px;font-size:11px;font-weight:700;padding:3px 9px;">{{ $leave->leave_type }}</span></td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ Carbon::parse($leave->to_date)->format('d M Y') }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;text-align:center;">{{ $leave->no_of_days }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;color:#8892B0;font-size:12px;max-width:160px;">{{ \Illuminate\Support\Str::limit($leave->leave_reason ?? '—', 40) }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;text-align:center;white-space:nowrap;">
                                <button onclick="mgrHandleApproval('leave',{{ $leave->id }},'approved')" style="background:#ECFDF5;color:#047857;border:1px solid #6EE7B7;padding:5px 11px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;margin-right:4px;font-family:'DM Sans',sans-serif;">✓ Approve</button>
                                <button onclick="mgrHandleApproval('leave',{{ $leave->id }},'declined')" style="background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5;padding:5px 11px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">✗ Decline</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:48px 24px;color:#8892B0;font-size:14px;font-weight:500;">No pending leave requests.</div>
            @endif
        </div>

        {{-- Permissions Panel --}}
        <div id="mgr-panel-permissions" style="display:none;">
            @if($pendingPermissionsList->count())
            <div style="overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;background:#fff;">
                    <thead><tr>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">#</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Employee</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Date</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">From</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">To</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:left;">Reason</th>
                        <th style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;padding:11px 14px;text-transform:uppercase;text-align:center;">Action</th>
                    </tr></thead>
                    <tbody>
                        @foreach($pendingPermissionsList as $i => $perm)
                        <tr data-mgr-row="permission-{{ $perm->id }}">
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ $i+1 }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;"><strong>{{ $perm->firstname }} {{ $perm->lastname }}</strong></td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ Carbon::parse($perm->permission_date)->format('d M Y') }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ $perm->start_time ? Carbon::parse($perm->start_time)->format('h:i A') : '—' }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;">{{ $perm->end_time ? Carbon::parse($perm->end_time)->format('h:i A') : '—' }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;color:#8892B0;font-size:12px;max-width:160px;">{{ \Illuminate\Support\Str::limit($perm->permission_reason ?? '—', 40) }}</td>
                            <td style="border-bottom:1px solid #F3F4F6;padding:12px 14px;text-align:center;white-space:nowrap;">
                                <button onclick="mgrHandleApproval('permission',{{ $perm->id }},'approved')" style="background:#ECFDF5;color:#047857;border:1px solid #6EE7B7;padding:5px 11px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;margin-right:4px;font-family:'DM Sans',sans-serif;">✓ Approve</button>
                                <button onclick="mgrHandleApproval('permission',{{ $perm->id }},'declined')" style="background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5;padding:5px 11px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">✗ Decline</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:48px 24px;color:#8892B0;font-size:14px;font-weight:500;">No pending permission requests.</div>
            @endif
        </div>
    </div>
</div>

<style>
    .mgr-apr-tab { display:inline-flex;align-items:center;padding:9px 16px;border:1px solid #E5E7EB;border-bottom:none;border-radius:10px 10px 0 0;background:#F9FAFB;cursor:pointer;font-size:13px;font-weight:600;color:#4B5563;font-family:'DM Sans',sans-serif;transition:all .15s;margin-bottom:-1px; }
    .mgr-apr-tab:hover { background:#F3F4F6; }
    .mgr-apr-tab-active { background:#fff!important;border-bottom-color:#fff!important;color:#0B1437!important; }
    #mgrAttModal table th,.mgr-att-th { background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#4B5563;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap; }
    #mgrAttModal table td { border-bottom:1px solid #F3F4F6;color:#0B1437;padding:13px 14px;vertical-align:middle;font-size:13px; }
    #mgrAttModal table tbody tr:last-child td { border-bottom:none; }
    #mgrAttModal table tbody tr:hover td { background:#F5F6FA; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    // Live clock
    function updateClock(){
        const el=document.getElementById('mgr-live-clock');
        if(el) el.textContent=new Date().toTimeString().split(' ')[0];
    }
    setInterval(updateClock,1000); updateClock();

    // Counter animation
    document.querySelectorAll('.mgr-dashboard .stat-value, .mgr-dashboard .mini-value').forEach(el=>{
        const text=el.textContent.trim();
        const m=text.match(/[\d,.]+/);
        if(!m) return;
        const target=parseFloat(m[0].replace(/,/g,''));
        if(isNaN(target)) return;
        const prefix=text.split(m[0])[0]||'';
        const suffix=text.split(m[0])[1]||'';
        const isDec=m[0].includes('.');
        const start=performance.now();
        const dur=1200;
        (function run(now){
            const p=Math.min((now-start)/dur,1);
            const e=p*(2-p);
            el.textContent=prefix+(isDec?(target*e).toFixed(1):Math.floor(target*e).toLocaleString())+suffix;
            if(p<1) requestAnimationFrame(run); else el.textContent=text;
        })(start);
    });

    Chart.defaults.font.family="'DM Sans',sans-serif";
    Chart.defaults.font.size=12;
    Chart.defaults.color='#8892B0';
    Chart.defaults.plugins.legend.display=false;
    Chart.defaults.plugins.tooltip={enabled:true,backgroundColor:'#0B1437',titleColor:'#fff',bodyColor:'#fff',padding:10,cornerRadius:8,displayColors:false};
    const gridColor='rgba(15,23,42,.04)';
    const baseScales={y:{beginAtZero:true,grid:{color:gridColor},ticks:{color:'#8892B0',font:{size:11}}},x:{grid:{display:false},ticks:{color:'#8892B0',font:{size:11}}}};

    // Attendance trend
    const attCtx=document.getElementById('mgrAttendanceChart');
    if(attCtx){
        const ctx=attCtx.getContext('2d');
        const g=ctx.createLinearGradient(0,0,0,200);
        g.addColorStop(0,'rgba(249,115,22,.2)'); g.addColorStop(1,'rgba(249,115,22,0)');
        new Chart(attCtx,{type:'line',data:{labels:{!! json_encode($attendanceTrendLabels) !!},datasets:[{data:{!! json_encode($attendanceTrendData) !!},borderColor:'#F97316',backgroundColor:g,borderWidth:3,fill:true,tension:0.4,pointRadius:5,pointBackgroundColor:'#F97316',pointBorderColor:'#fff',pointBorderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:baseScales}});
    }

    // Attendance donut
    const donutCtx=document.getElementById('mgrAttendanceDonut');
    if(donutCtx){
        new Chart(donutCtx,{type:'doughnut',data:{labels:['Present','Absent','Late'],datasets:[{data:[{{ $presentToday }},{{ $absentToday }},{{ $lateArrivals }}],backgroundColor:['#10B981','#EF4444','#F59E0B'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'75%',plugins:{legend:{display:false}}}});
    }

    // Team Productivity chart
    const prodCtx=document.getElementById('mgrProductivityChart');
    if(prodCtx){
        const prodLabels={!! json_encode($teamProductivity->map(fn($e)=>$e->firstname.' '.substr($e->lastname,0,1).'.')) !!};
        const prodData={!! json_encode($teamProductivity->pluck('completed_count')) !!};
        new Chart(prodCtx,{type:'bar',data:{labels:prodLabels,datasets:[{data:prodData,backgroundColor:'rgba(99,102,241,0.8)',borderRadius:8,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>ctx.raw+' tasks completed'}}},scales:{y:{beginAtZero:true,grid:{color:gridColor},ticks:{color:'#8892B0',font:{size:11},stepSize:1}},x:{grid:{display:false},ticks:{color:'#8892B0',font:{size:11}}}}}});
    }
})();
</script>

<script>
(function(){
    var _csrf='{{ csrf_token() }}';

    // ── Attendance detail modal data ─────────────────────────────────────
    var _adData={
        present: @json($presentList),
        absent:  @json($absentList),
        late:    @json($lateList)
    };

    function fmtTime(dt){
        if(!dt) return '<span style="color:#8892B0;">—</span>';
        var d=new Date(dt.replace(' ','T'));
        return isNaN(d)?dt:d.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',hour12:true});
    }
    function badge(t,bg,c){return '<span style="background:'+bg+';color:'+c+';border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;">'+t+'</span>';}
    function buildTable(cols,rows){
        return '<div style="overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB;"><table style="width:100%;border-collapse:collapse;"><thead><tr>'
            +cols.map(c=>'<th class="mgr-att-th">'+c+'</th>').join('')+'</tr></thead><tbody>'
            +rows.map(r=>'<tr>'+r.map(cell=>'<td style="border-bottom:1px solid #F3F4F6;padding:13px 14px;font-size:13px;">'+cell+'</td>').join('')+'</tr>').join('')
            +'</tbody></table></div>';
    }

    var cfg={
        present:{eyebrow:'Attendance · Today',title:'Present Employees'},
        absent: {eyebrow:'Attendance · Today',title:'Absent Employees'},
        late:   {eyebrow:'Attendance · Today',title:'Late Arrivals'}
    };

    window.openMgrModal=function(type){
        var data=_adData[type]||[];
        document.getElementById('mgrAttEyebrow').textContent=cfg[type].eyebrow;
        document.getElementById('mgrAttTitle').textContent=cfg[type].title+' ('+data.length+')';
        var content='';
        if(!data.length){content='<div style="text-align:center;padding:48px;color:#8892B0;font-weight:500;">No records for today.</div>';}
        else if(type==='present'){
            content=buildTable(['#','Employee','Designation','Check In','Check Out','Status'],data.map(function(e,i){
                return[i+1,'<strong>'+(e.firstname||'')+' '+(e.lastname||'')+'</strong>',e.designation||'—',fmtTime(e.punch_in),fmtTime(e.punch_out),e.is_late?badge('Late','#FFFBEB','#B45309'):badge('On Time','#ECFDF5','#047857')];
            }));
        } else if(type==='absent'){
            content=buildTable(['#','Employee','Designation','Status'],data.map(function(e,i){
                return[i+1,'<strong>'+(e.firstname||'')+' '+(e.lastname||'')+'</strong>',e.designation||'—',badge('Absent','#FEF2F2','#B91C1C')];
            }));
        } else {
            content=buildTable(['#','Employee','Designation','Check In','Late By'],data.map(function(e,i){
                return[i+1,'<strong>'+(e.firstname||'')+' '+(e.lastname||'')+'</strong>',e.designation||'—',fmtTime(e.punch_in),badge((e.late_minutes||0)+' min late','#FFFBEB','#B45309')];
            }));
        }
        document.getElementById('mgrAttContent').innerHTML=content;
        document.getElementById('mgrAttModal').style.display='block';
        document.body.style.overflow='hidden';
    };
    window.closeMgrModal=function(){
        document.getElementById('mgrAttModal').style.display='none';
        document.body.style.overflow='';
    };
    document.getElementById('mgrAttModal').addEventListener('click',function(e){if(e.target===this)window.closeMgrModal();});

    // ── Approvals modal ──────────────────────────────────────────────────
    window.openMgrApprovals=function(tab){
        document.getElementById('mgrAprModal').style.display='block';
        document.body.style.overflow='hidden';
        switchMgrAprTab(tab,document.getElementById('mgrtab-'+tab));
    };
    window.closeMgrApprovals=function(){
        document.getElementById('mgrAprModal').style.display='none';
        document.body.style.overflow='';
    };
    window.switchMgrAprTab=function(tab,btn){
        ['leaves','permissions'].forEach(function(t){
            var p=document.getElementById('mgr-panel-'+t);
            var b=document.getElementById('mgrtab-'+t);
            if(p) p.style.display=t===tab?'block':'none';
            if(b){ b.classList.remove('mgr-apr-tab-active'); if(t===tab) b.classList.add('mgr-apr-tab-active'); }
        });
    };
    document.getElementById('mgrAprModal').addEventListener('click',function(e){if(e.target===this)window.closeMgrApprovals();});

    window.mgrHandleApproval=function(type,id,action){
        var rowEl=document.querySelector('[data-mgr-row="'+type+'-'+id+'"]');
        if(rowEl){rowEl.style.opacity='0.4';rowEl.style.pointerEvents='none';}
        fetch('{{ route("manager.process-approval") }}',{
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':_csrf,'X-Requested-With':'XMLHttpRequest'},
            body:JSON.stringify({type:type,id:id,action:action})
        })
        .then(function(r){return r.json().then(function(d){return{ok:r.ok,d:d};});})
        .then(function(res){
            if(res.ok&&res.d.success){
                if(rowEl) rowEl.remove();
                _updateMgrCount(type);
                _showMgrToast(action==='approved'?'Approved successfully':'Declined successfully',action==='approved'?'#10B981':'#EF4444');
            } else {
                if(rowEl){rowEl.style.opacity='1';rowEl.style.pointerEvents='';}
                _showMgrToast(res.d.message||'Action failed','#EF4444');
            }
        })
        .catch(function(){
            if(rowEl){rowEl.style.opacity='1';rowEl.style.pointerEvents='';}
            _showMgrToast('Network error','#EF4444');
        });
    };

    function _updateMgrCount(type){
        var tabKey=type==='leave'?'leaves':'permissions';
        var panel=document.getElementById('mgr-panel-'+tabKey);
        var countEl=document.getElementById('mgrtabcount-'+tabKey);
        var statEl=document.getElementById('mgr-'+(type==='leave'?'leave':'perm')+'-count');
        if(panel&&countEl){
            var rem=panel.querySelectorAll('tbody tr').length;
            countEl.textContent=rem;
            if(statEl) statEl.textContent=rem;
            if(rem===0) panel.innerHTML='<div style="text-align:center;padding:48px;color:#8892B0;font-weight:500;">No pending '+tabKey+'.</div>';
        }
    }

    function _showMgrToast(msg,color){
        var t=document.getElementById('_mgrToast');
        if(!t){t=document.createElement('div');t.id='_mgrToast';t.style.cssText='position:fixed;bottom:28px;right:28px;padding:13px 20px;border-radius:10px;color:#fff;font-weight:600;font-size:13px;z-index:11000;font-family:"DM Sans",sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.15);transition:opacity .3s;';document.body.appendChild(t);}
        t.textContent=msg;t.style.background=color||'#10B981';t.style.opacity='1';t.style.display='block';
        clearTimeout(t._t);t._t=setTimeout(function(){t.style.opacity='0';setTimeout(function(){t.style.display='none';},320);},2800);
    }

    document.addEventListener('keydown',function(e){if(e.key==='Escape'){window.closeMgrModal();window.closeMgrApprovals();}});
})();
</script>

@endsection
