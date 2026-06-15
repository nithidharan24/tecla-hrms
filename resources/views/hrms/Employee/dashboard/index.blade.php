@extends('layouts.index')

@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
    $pendingItems = (int) ($leaveStats['pending'] ?? 0) + (int) ($ticketStats['open'] ?? 0);
    $employeeName = trim(($employee->firstname ?? 'Employee') . ' ' . ($employee->lastname ?? ''));
    $employeeInitials = strtoupper(Str::substr($employee->firstname ?? 'E', 0, 1) . Str::substr($employee->lastname ?? '', 0, 1));
    $leaveDonutData = [
        (int) ($leaveStats['approved'] ?? 0),
        (int) ($leaveStats['pending'] ?? 0),
        (int) ($leaveStats['rejected'] ?? 0),
        (int) ($leaveStats['remaining'] ?? 0),
    ];
    $ticketDonutData = [
        (int) ($ticketStats['open'] ?? 0),
        (int) ($ticketStats['closed'] ?? 0),
    ];
    $taskDonutData = [
        (int) ($taskStats['completed'] ?? 0),
        (int) ($taskStats['in_progress'] ?? 0),
        (int) ($taskStats['pending'] ?? 0),
    ];
@endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .employee-dashboard {
        --brand: #E8612C;
        --green: #1D9E75;
        --red: #E24B4A;
        --amber: #EF9F27;
        --blue: #378ADD;
        --purple: #7F77DD;
        --text-primary: #0F172A;
        --text-secondary: #64748B;
        --text-muted: #94A3B8;
        --border: #E2E8F0;
        --card-bg: #FFFFFF;
        --page-bg: #F5F6FA;
        background: var(--page-bg);
        color: #0B1437;
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        padding-bottom: 40px;
    }

    .employee-dashboard * { box-sizing: border-box; }
    .employee-dashboard h1,
    .employee-dashboard h2,
    .employee-dashboard h3,
    .employee-dashboard h4,
    .employee-dashboard p { margin-top: 0; }

    .employee-dashboard .dashboard-header {
        align-items: center;
        background: #FFFFFF !important;
        border-bottom: 1px solid #E5E7EB !important;
        display: flex;
        justify-content: space-between;
        min-height: 70px;
        padding: 16px 60px;
        position: sticky;
        top: 0;
        z-index: 20;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
    }

    .employee-dashboard .welcome-title {
        color: #0B1437 !important;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.5px;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .employee-dashboard .welcome-meta,
    .employee-dashboard .muted {
        color: var(--text-secondary);
        font-size: 13px;
    }
    

    .employee-dashboard .header-actions {
        align-items: center;
        display: flex;
        gap: 12px;
    }

    .employee-dashboard .notif-btn {
        align-items: center;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-primary);
        display: inline-flex;
        height: 42px;
        justify-content: center;
        position: relative;
        width: 42px;
    }

    .employee-dashboard .notif-count {
        align-items: center;
        background: var(--red);
        border: 2px solid #fff;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 10px;
        font-weight: 700;
        height: 20px;
        justify-content: center;
        min-width: 20px;
        padding: 0 5px;
        position: absolute;
        right: -7px;
        top: -7px;
    }

    .employee-dashboard .dashboard-body {
        margin: 0 auto;
        max-width: 1600px;
        padding: 32px 40px;
    }

    .employee-dashboard .grid { display: grid; gap: 20px; }
    .employee-dashboard .grid-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    .employee-dashboard .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .employee-dashboard .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .employee-dashboard .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    .employee-dashboard .section-block { margin-top: 32px; }
    .employee-dashboard .section-head {
        align-items: flex-end;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .employee-dashboard .section-eyebrow {
        color: #8892B0;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .employee-dashboard .section-title {
        color: #0B1437;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.3px;
        margin: 0;
    }

    .employee-dashboard .panel,
    .employee-dashboard .stat-card,
    .employee-dashboard .mini-card,
    .employee-dashboard .project-card,
    .employee-dashboard .achievement-card {
        animation: fadeInUp .45s ease both;
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(11, 20, 55, 0.03), 0 2px 4px -1px rgba(11, 20, 55, 0.02) !important;
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .employee-dashboard .panel { padding: 24px !important; }
    .employee-dashboard .stat-card {
        border-bottom: 3px solid transparent !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 146px;
        overflow: hidden;
        padding: 22px 24px !important;
    }

    .employee-dashboard .stat-card:hover,
    .employee-dashboard .panel:hover,
    .employee-dashboard .mini-card:hover,
    .employee-dashboard .project-card:hover,
    .employee-dashboard .achievement-card:hover {
        box-shadow: 0 12px 20px -8px rgba(11, 20, 55, 0.08), 0 4px 12px -2px rgba(11, 20, 55, 0.03) !important;
        transform: translateY(-4px) !important;
    }

    .employee-dashboard .stat-card::before,
    .employee-dashboard .mini-card::before {
        bottom: 0;
        content: '';
        left: 0;
        position: absolute;
        top: 0;
        width: 4px;
    }

    .employee-dashboard .stat-card::before { display: none !important; }
    .employee-dashboard .stat-card.accent-green { border-bottom-color: var(--green) !important; }
    .employee-dashboard .stat-card.accent-red { border-bottom-color: var(--red) !important; }
    .employee-dashboard .stat-card.accent-amber { border-bottom-color: var(--amber) !important; }
    .employee-dashboard .stat-card.accent-blue { border-bottom-color: var(--blue) !important; }
    .employee-dashboard .stat-card.accent-purple { border-bottom-color: var(--purple) !important; }

    .employee-dashboard .accent-green::before { background: var(--green); }
    .employee-dashboard .accent-red::before { background: var(--red); }
    .employee-dashboard .accent-amber::before { background: var(--amber); }
    .employee-dashboard .accent-blue::before { background: var(--blue); }
    .employee-dashboard .accent-purple::before { background: var(--purple); }

    .employee-dashboard .stat-card:nth-child(1) { animation-delay: .05s; }
    .employee-dashboard .stat-card:nth-child(2) { animation-delay: .10s; }
    .employee-dashboard .stat-card:nth-child(3) { animation-delay: .15s; }
    .employee-dashboard .stat-card:nth-child(4) { animation-delay: .20s; }
    .employee-dashboard .stat-card:nth-child(5) { animation-delay: .25s; }

    .employee-dashboard .stat-top {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .employee-dashboard .stat-icon {
        align-items: center;
        border-radius: 12px;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .employee-dashboard .icon-green { background: #ECFDF5; color: var(--green); }
    .employee-dashboard .icon-red { background: #FEF2F2; color: var(--red); }
    .employee-dashboard .icon-amber { background: #FFFBEB; color: var(--amber); }
    .employee-dashboard .icon-blue { background: #EFF6FF; color: var(--blue); }
    .employee-dashboard .icon-purple { background: #F5F3FF; color: var(--purple); }
    .employee-dashboard .icon-brand { background: #FDF0EA; color: var(--brand); }

    .employee-dashboard .stat-value {
        color: #0B1437 !important;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.05;
        margin-bottom: 6px;
    }

    .employee-dashboard .stat-label {
        color: #4B5563 !important;
        font-size: 13px;
        font-weight: 600;
        margin: 0;
    }

    .employee-dashboard .stat-sub,
    .employee-dashboard .small-text {
        color: #8892B0 !important;
        font-size: 12px;
        margin: 0;
    }

    .employee-dashboard .shift-card {
        background: linear-gradient(135deg, #FDF0EA 0%, #FFF7F5 100%);
        border: 1px solid rgba(232, 97, 44, .2);
        border-radius: 14px;
        padding: 20px 24px;
    }

    .employee-dashboard .shift-main {
        align-items: center;
        display: grid;
        gap: 18px;
        grid-template-columns: auto 1fr auto;
    }

    .employee-dashboard .shift-icon {
        align-items: center;
        background: #fff;
        border-radius: 14px;
        color: var(--brand);
        display: inline-flex;
        height: 58px;
        justify-content: center;
        width: 58px;
    }

    .employee-dashboard .shift-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
    .employee-dashboard .shift-time { color: var(--text-secondary); font-size: 14px; font-weight: 600; }

    .employee-dashboard .progress-track {
        background: #E2E8F0;
        border-radius: 999px;
        height: 7px;
        overflow: hidden;
        width: 100%;
    }

    .employee-dashboard .progress-fill {
        border-radius: inherit;
        height: 100%;
        transition: width .4s ease;
    }

    .employee-dashboard .shift-progress { margin-top: 18px; }
    .employee-dashboard .progress-caption { color: var(--text-secondary); font-size: 12px; margin-top: 8px; }

    .employee-dashboard .badge {
        align-items: center;
        border-radius: 999px;
        display: inline-flex;
        font-size: 11px;
        font-weight: 700;
        gap: 6px;
        line-height: 1;
        padding: 7px 11px;
        white-space: nowrap;
    }

    .employee-dashboard .badge-green { background: #ECFDF5; color: #047857; }
    .employee-dashboard .badge-amber { background: #FFFBEB; color: #B45309; }
    .employee-dashboard .badge-red { background: #FEF2F2; color: #B91C1C; }
    .employee-dashboard .badge-blue { background: #EFF6FF; color: #1D4ED8; }
    .employee-dashboard .badge-purple { background: #F5F3FF; color: #5B54B8; }
    .employee-dashboard .badge-gray { background: #F1F5F9; color: #475569; }
    .employee-dashboard .badge-brand { background: var(--brand); color: #fff; }
    .employee-dashboard .badge-celebrate { animation: pulseSoft 1.8s ease-in-out infinite; color: #fff; }
    .employee-dashboard .badge-birthday { background: var(--brand); }
    .employee-dashboard .badge-anniversary { background: var(--purple); }

    .employee-dashboard .pulse-dot {
        animation: pulse 1.5s infinite;
        background: var(--green);
        border-radius: 50%;
        display: inline-block;
        height: 8px;
        width: 8px;
    }

    .employee-dashboard .week-strip {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(7, 1fr);
    }

    .employee-dashboard .day-col {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        min-height: 126px;
        padding: 10px 8px;
        text-align: center;
    }

    .employee-dashboard .day-col.today {
        border-color: var(--brand);
        box-shadow: 0 0 0 2px rgba(232, 97, 44, .15);
    }

    .employee-dashboard .day-col.today .day-label { color: var(--brand); font-weight: 700; }
    .employee-dashboard .day-col.no-shift { background: #F8FAFC; opacity: .75; }
    .employee-dashboard .day-col.past { opacity: .55; }
    .employee-dashboard .day-label { color: var(--text-primary); font-size: 13px; font-weight: 700; }
    .employee-dashboard .day-date { color: var(--text-muted); font-size: 11px; margin-top: 2px; }

    .employee-dashboard .shift-block {
        background: #EAF3DE;
        border-radius: 6px;
        color: #27500A;
        font-size: 11px;
        margin-top: 8px;
        padding: 7px 4px;
    }

    .employee-dashboard .off-block {
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        margin-top: 28px;
    }

    .employee-dashboard .info-list {
        display: grid;
        gap: 10px;
    }

    .employee-dashboard .info-row {
        align-items: center;
        border-bottom: 1px solid #F1F5F9;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding-bottom: 10px;
    }

    .employee-dashboard .info-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .employee-dashboard .info-label { color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .employee-dashboard .info-value { color: var(--text-primary); font-size: 13px; font-weight: 700; text-align: right; }
    .employee-dashboard .notice-strip {
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        margin-top: 14px;
        padding: 11px 12px;
    }
    .employee-dashboard .notice-amber { background: #FFFBEB; color: #92400E; }
    .employee-dashboard .notice-green { background: #ECFDF5; color: #047857; }

    .employee-dashboard .timeline-strip {
        align-items: center;
        background: #fff;
        border: 1px dashed var(--border);
        border-radius: 12px;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-top: 16px;
        padding: 14px 16px;
    }

    .employee-dashboard .chart-wrap {
        height: 200px;
        position: relative;
        width: 100%;
    }

    .employee-dashboard .chart-wrap.short { height: 150px; }
    .employee-dashboard .legend-list { display: grid; gap: 10px; margin-top: 16px; }
    .employee-dashboard .legend-item {
        align-items: center;
        display: flex;
        justify-content: space-between;
    }
    .employee-dashboard .legend-left { align-items: center; display: flex; gap: 9px; }
    .employee-dashboard .legend-dot { border-radius: 50%; height: 10px; width: 10px; }

    .employee-dashboard .table-scroll { overflow-x: auto; }
    .employee-dashboard .data-table {
        border-collapse: collapse;
        font-size: 13px;
        width: 100%;
    }
    .employee-dashboard .data-table th {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        padding: 11px 12px;
        text-align: left;
        text-transform: uppercase;
    }
    .employee-dashboard .data-table td {
        border-top: 1px solid #F1F5F9;
        color: var(--text-primary);
        padding: 12px;
        vertical-align: middle;
    }
    .employee-dashboard .data-table tbody tr:nth-child(even) td { background: #F8FAFC; }
    .employee-dashboard .data-table tbody tr:hover td { background: #FFF7F5; }

    .employee-dashboard .cta-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }
    .employee-dashboard .view-link {
        color: var(--brand);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }
    .employee-dashboard .view-link:hover { text-decoration: underline; }

    .employee-dashboard .mini-card {
        min-height: 92px;
        padding: 16px 16px 16px 20px;
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        border-left: 4px solid transparent;
        box-shadow: 0 4px 6px -1px rgba(11,20,55,0.03) !important;
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1) !important;
    }
    .employee-dashboard .mini-card.accent-green { border-left-color: var(--green) !important; }
    .employee-dashboard .mini-card.accent-red { border-left-color: var(--red) !important; }
    .employee-dashboard .mini-card.accent-amber { border-left-color: var(--amber) !important; }
    .employee-dashboard .mini-card.accent-blue { border-left-color: var(--blue) !important; }
    .employee-dashboard .mini-card.accent-purple { border-left-color: var(--purple) !important; }
    .employee-dashboard .mini-value { font-size: 24px; font-weight: 700; line-height: 1; margin-bottom: 6px; }
    .employee-dashboard .mini-label { color: var(--text-secondary); font-size: 12px; font-weight: 700; }

    .employee-dashboard .project-card { padding: 18px; }
    .employee-dashboard .project-title { font-size: 15px; font-weight: 700; margin-bottom: 10px; }
    .employee-dashboard .avatar-row { display: flex; margin: 12px 0; }
    .employee-dashboard .avatar-circle {
        align-items: center;
        background: #EFF6FF;
        border: 2px solid #fff;
        border-radius: 50%;
        color: #1D4ED8;
        display: flex;
        flex-shrink: 0;
        font-size: 11px;
        font-weight: 700;
        height: 34px;
        justify-content: center;
        margin-left: -8px;
        width: 34px;
    }
    .employee-dashboard .avatar-circle:first-child { margin-left: 0; }
    .employee-dashboard .avatar-more { background: #F1F5F9; color: var(--text-secondary); }

    .employee-dashboard .holiday-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
        scroll-snap-type: x mandatory;
    }
    .employee-dashboard .holiday-scroll::-webkit-scrollbar { height: 4px; }
    .employee-dashboard .holiday-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
    .employee-dashboard .holiday-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        flex: 0 0 180px;
        padding: 16px;
        scroll-snap-align: start;
    }
    .employee-dashboard .holiday-card.next { border-color: var(--brand); box-shadow: 0 0 0 2px rgba(232, 97, 44, .08); }
    .employee-dashboard .holiday-title { font-size: 14px; font-weight: 700; margin: 10px 0 6px; }

    .employee-dashboard .celebration-banner {
        align-items: center;
        animation: fadeInUp .4s ease both;
        border-radius: 14px;
        color: #fff;
        display: flex;
        gap: 16px;
        padding: 20px 28px;
    }
    .employee-dashboard .celebration-banner.birthday { background: linear-gradient(135deg, #E8612C, #F59E0B); }
    .employee-dashboard .celebration-banner.anniversary { background: linear-gradient(135deg, #7F77DD, #A78BFA); }
    .employee-dashboard .celebration-icon { font-size: 34px; line-height: 1; }
    .employee-dashboard .celebration-title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }

    .employee-dashboard .feed-list { display: grid; gap: 12px; max-height: 280px; overflow-y: auto; padding-right: 4px; }
    .employee-dashboard .feed-item {
        align-items: flex-start;
        background: #F8FAFC;
        border-radius: 10px;
        display: flex;
        gap: 12px;
        padding: 12px;
    }
    .employee-dashboard .feed-avatar {
        align-items: center;
        background: #FFF7ED;
        border-radius: 50%;
        color: var(--brand);
        display: flex;
        flex: 0 0 36px;
        font-size: 12px;
        font-weight: 700;
        height: 36px;
        justify-content: center;
        width: 36px;
    }
    .employee-dashboard .feed-name { font-size: 13px; font-weight: 700; }
    .employee-dashboard .feed-message { color: var(--text-secondary); font-size: 13px; margin: 5px 0; }

    .employee-dashboard .achievement-card {
        padding: 20px 16px;
        text-align: center;
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(11,20,55,0.03) !important;
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .employee-dashboard .achievement-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,.08) !important; transform: translateY(-3px) !important; }
    .employee-dashboard .achievement-icon { font-size: 2.2rem; margin-bottom: 10px; display: block; }

    .employee-dashboard .empty-state {
        align-items: center;
        color: var(--text-muted);
        display: flex;
        justify-content: center;
        min-height: 104px;
        text-align: center;
    }

    .employee-dashboard .stacked-bar {
        border-radius: 999px;
        display: flex;
        height: 8px;
        margin-top: 16px;
        overflow: hidden;
        width: 100%;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .6; transform: scale(1.3); }
    }

    @keyframes pulseSoft {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(232,97,44,.35); }
        50% { transform: scale(1.02); box-shadow: 0 0 0 8px rgba(232,97,44,0); }
    }

    @media (max-width: 1300px) {
        .employee-dashboard .grid-5 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .employee-dashboard .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 991px) {
        .employee-dashboard .dashboard-header { align-items: flex-start; flex-direction: column; padding: 16px 22px; }
        .employee-dashboard .dashboard-body { padding: 22px; }
        .employee-dashboard .grid-5,
        .employee-dashboard .grid-3,
        .employee-dashboard .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .employee-dashboard .week-strip { grid-template-columns: repeat(4, minmax(120px, 1fr)); overflow-x: auto; }
    }

    @media (max-width: 640px) {
        .employee-dashboard .grid-5,
        .employee-dashboard .grid-4,
        .employee-dashboard .grid-3,
        .employee-dashboard .grid-2 { grid-template-columns: 1fr; }
        .employee-dashboard .shift-main { grid-template-columns: 1fr; }
        .employee-dashboard .week-strip { grid-template-columns: 1fr; }
        .employee-dashboard .timeline-strip { align-items: flex-start; flex-direction: column; }
        .employee-dashboard .section-head { align-items: flex-start; flex-direction: column; gap: 8px; }
    }
</style>

<div class="employee-dashboard">
    {{-- ===== WELCOME HEADER ===== --}}
    <header class="dashboard-header">
        <div>
            <div class="welcome-title">{{ $greeting }}, {{ $employee->firstname ?? 'Employee' }}! <span aria-hidden="true">&#128075;</span></div>
            <div class="welcome-meta">{{ Carbon::today()->format('l, d M Y') }}</div>
            <div class="welcome-meta">
                Employee ID: {{ $employee->employeeid ?? 'N/A' }} &middot;
                Department: {{ $employee->department_name ?? $employee->department ?? 'N/A' }} &middot;
                Designation: {{ $employee->designation_name ?? $employee->designation ?? ($employee->position ?? 'N/A') }}
            </div>
        </div>
        <div class="header-actions">
            @if($isBirthdayToday)
                <span class="badge badge-celebrate badge-birthday">&#127874; Happy Birthday!</span>
            @elseif($isAnniversaryToday)
                <span class="badge badge-celebrate badge-anniversary">&#127881; Work Anniversary!</span>
            @endif
            <a href="{{ route('chat.employee') }}" id="emp-chat-btn" style="position:relative;display:inline-flex;align-items:center;gap:8px;padding:0 16px;height:42px;font-size:13px;font-weight:700;color:#E8612C;text-decoration:none;background:#fff;border:1px solid #E2E8F0;border-radius:10px;" title="Chat with HR">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Chat to HR
                <span id="emp-chat-unread" style="display:none;position:absolute;top:-8px;right:-8px;background:#E24B4A;color:#fff;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:999px;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;">0</span>
            </a>
        </div>
    </header>

    <main class="dashboard-body">
        @include('hrms.partials.dashboard-announcement-bar', [
            'announcementAccent' => '#E8612C',
            'announcementAccentSoft' => 'rgba(232, 97, 44, 0.10)',
        ])

        {{-- ===== TODAY'S SHIFT ===== --}}
        <section class="section-block" style="margin-top:0;">
            @if($todaysShift)
                @php
                    $shiftStart = Carbon::today()->setTimeFromTimeString($todaysShift->start_time);
                    $shiftEnd = Carbon::today()->setTimeFromTimeString($todaysShift->end_time);
                    if ($shiftEnd->lt($shiftStart)) { $shiftEnd->addDay(); }
                    $now = Carbon::now();
                    $totalMins = max(0, $shiftStart->diffInMinutes($shiftEnd));
                    $elapsed = $todaysShift->status === 'completed' ? $totalMins : max(0, $shiftStart->diffInMinutes($now, false));
                    $pct = $totalMins > 0 ? min(100, round(($elapsed / $totalMins) * 100)) : 0;
                    $shiftBadge = $todaysShift->status === 'in_progress' ? 'badge-green' : ($todaysShift->status === 'upcoming' ? 'badge-blue' : 'badge-gray');
                @endphp
                <div class="shift-card">
                    <div class="shift-main">
                        <div class="shift-icon"><svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                        <div>
                            <div class="shift-title">{{ $todaysShift->shift_name ?? 'Today Shift' }}</div>
                            <div class="shift-time">{{ $todaysShift->start_time_formatted ?? 'N/A' }} &rarr; {{ $todaysShift->end_time_formatted ?? 'N/A' }}</div>
                            <p class="stat-sub" style="margin-top:6px;">{{ $todaysShift->shift_duration ?? 'N/A' }} &middot; Break: {{ $todaysShift->break_duration ?? 'N/A' }}</p>
                            @if(!empty($todaysShift->punched_in_at))
                                <p class="stat-sub" style="margin-top:6px;color:#1D9E75;font-weight:700;">Punched in at {{ $todaysShift->punched_in_at }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="badge {{ $shiftBadge }}">
                                @if($todaysShift->status === 'in_progress') <span class="pulse-dot"></span> Active &middot; {{ $todaysShift->time_until ?? '' }}
                                @elseif($todaysShift->status === 'completed') Completed
                                @else {{ $todaysShift->time_until ?? 'Upcoming' }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="shift-progress">
                        <div class="progress-track"><div class="progress-fill" style="width:{{ $pct }}%;background:var(--brand);"></div></div>
                        <div class="progress-caption">{{ $pct }}% of shift completed</div>
                    </div>
                </div>
            @else
                <div class="shift-card" style="background:#fff;">
                    <div class="shift-main">
                        <div class="shift-icon"><svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 2v4M16 2v4M3 10h18"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg></div>
                        <div>
                            <div class="shift-title">No shift scheduled today</div>
                            <p class="stat-sub">Your schedule does not include a shift for {{ Carbon::today()->format('l, d M Y') }}.</p>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        {{-- ===== KPI SUMMARY ===== --}}
        <section class="section-block">
            <div class="grid grid-5">
                <div class="stat-card accent-green"><div class="stat-top"><div><div class="stat-label">Leave Balance</div><div class="stat-value">{{ number_format($leaveStats['remaining'] ?? 0) }}</div></div><span class="stat-icon icon-green"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 2v4M16 2v4M3 10h18"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg></span></div><p class="stat-sub">of {{ number_format($leaveStats['totalAnnual'] ?? 0) }} annual days remaining</p></div>
                <div class="stat-card accent-amber"><div class="stat-top"><div><div class="stat-label">Pending Leaves</div><div class="stat-value">{{ number_format($leaveStats['pending'] ?? 0) }}</div></div><span class="stat-icon icon-amber"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div><p class="stat-sub">{{ number_format($leaveStats['approved'] ?? 0) }} approved this year</p></div>
                <div class="stat-card accent-red"><div class="stat-top"><div><div class="stat-label">Open Tickets</div><div class="stat-value">{{ number_format($ticketStats['open'] ?? 0) }}</div></div><span class="stat-icon icon-red"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a3 3 0 0 0 0 6v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a3 3 0 0 0 0-6V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/><path d="M13 5v14"/></svg></span></div><p class="stat-sub">{{ number_format($ticketStats['closed'] ?? 0) }} closed &middot; {{ number_format($ticketStats['total'] ?? 0) }} total</p></div>
                <div class="stat-card accent-blue"><div class="stat-top"><div><div class="stat-label">Active Projects</div><div class="stat-value">{{ number_format($projectStats['active'] ?? 0) }}</div></div><span class="stat-icon icon-blue"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h7l2 2h9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></span></div><p class="stat-sub">{{ number_format($projectStats['completed'] ?? 0) }} completed &middot; {{ number_format($projectStats['total'] ?? 0) }} total</p></div>
                <div class="stat-card accent-purple"><div class="stat-top"><div><div class="stat-label">Total Tasks</div><div class="stat-value">{{ number_format($taskStats['total'] ?? 0) }}</div></div><span class="stat-icon icon-purple"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span></div><p class="stat-sub">{{ number_format($taskStats['completed'] ?? 0) }} completed &middot; {{ number_format($taskStats['in_progress'] ?? 0) }} in progress</p></div>
            </div>
        </section>

        {{-- ===== WEEKLY SCHEDULE ===== --}}
        <section class="section-block">
            <div class="section-head"><div><div class="section-eyebrow">This Week's Schedule</div><h2 class="section-title">Shift Plan</h2></div></div>
            <div class="panel">
                <div class="week-strip">
                    @foreach($weeklySchedule as $day)
                        @php $dayClass = ($day['is_today'] ?? false) ? 'today' : ''; $dayClass .= !($day['has_shift'] ?? false) ? ' no-shift' : ''; $dayClass .= ($day['is_past'] ?? false) ? ' past' : ''; @endphp
                        <div class="day-col {{ $dayClass }}">
                            <div class="day-label">{{ $day['day_name'] ?? 'Day' }}</div>
                            <div class="day-date">{{ $day['date'] ?? '' }}</div>
                            @if($day['has_shift'] ?? false)
                                <div class="shift-block">
                                    <strong>{{ $day['start_time'] ?? 'N/A' }} - {{ $day['end_time'] ?? 'N/A' }}</strong><br>
                                    {{ $day['shift_name'] ?? 'Shift' }}<br>
                                    {{ $day['duration'] ?? '' }}
                                </div>
                            @else
                                <div class="off-block">Off</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== SCHEDULE INFO ===== --}}
        <section class="section-block">
            <div class="grid grid-2">
                <div class="panel">
                    <div class="section-eyebrow">Current Schedule Details</div>
                    @if($employeeSchedule)
                        <div class="info-list" style="margin-top:16px;">
                            <div class="info-row"><span class="info-label">Shift</span><span class="info-value">{{ $employeeSchedule->shift_name ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Hours</span><span class="info-value">{{ $employeeSchedule->start_time_formatted ?? 'N/A' }} &rarr; {{ $employeeSchedule->end_time_formatted ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Duration</span><span class="info-value">{{ $employeeSchedule->duration ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Working days</span><span class="info-value">{{ $employeeSchedule->days_formatted ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Valid until</span><span class="info-value">@if(!empty($employeeSchedule->schedule_end_date)) {{ Carbon::parse($employeeSchedule->schedule_end_date)->format('d M Y') }} @else N/A @endif</span></div>
                        </div>
                        @if(!empty($employeeSchedule->is_ending_soon))
                            <div class="notice-strip notice-amber">Schedule ends in {{ $employeeSchedule->days_remaining ?? 0 }} days</div>
                        @endif
                    @else
                        <div class="empty-state">No active schedule assigned.</div>
                    @endif
                </div>

                <div class="panel">
                    <div class="section-eyebrow">Upcoming Schedule / Transition</div>
                    @if($nextSchedule)
                        <div style="margin:14px 0;"><span class="badge {{ ($nextSchedule->status ?? '') === 'upcoming' ? 'badge-blue' : 'badge-amber' }}">{{ ($nextSchedule->status ?? '') === 'upcoming' ? 'Upcoming' : 'Ending Soon' }}</span></div>
                        <div class="info-list">
                            <div class="info-row"><span class="info-label">Status</span><span class="info-value">{{ $nextSchedule->status_text ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Shift</span><span class="info-value">{{ $nextSchedule->shift_name ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Time</span><span class="info-value">{{ $nextSchedule->start_time_formatted ?? 'N/A' }} &rarr; {{ $nextSchedule->end_time_formatted ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Duration</span><span class="info-value">{{ $nextSchedule->duration ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Days</span><span class="info-value">{{ $nextSchedule->days_formatted ?? 'N/A' }}</span></div>
                            <div class="info-row"><span class="info-label">Period</span><span class="info-value">{{ $nextSchedule->formatted_start_date ?? 'N/A' }} &rarr; {{ $nextSchedule->formatted_end_date ?? 'N/A' }}</span></div>
                        </div>
                        @if(!empty($nextSchedule->is_schedule_change))
                            <div class="notice-strip notice-green">This is a schedule change</div>
                        @endif
                    @else
                        <div class="empty-state">No upcoming schedule changes.</div>
                    @endif
                </div>
            </div>
            @if($scheduleTransition)
                <div class="timeline-strip">
                    <strong>{{ $scheduleTransition['current_shift'] ?? 'Current shift' }} &rarr; {{ $scheduleTransition['next_shift'] ?? 'Next shift' }}</strong>
                    <span class="muted">Transitions on {{ $scheduleTransition['current_ends'] ?? 'N/A' }}</span>
                    @if($scheduleTransition['has_gap'] ?? false)
                        <span class="badge badge-amber">{{ $scheduleTransition['gap_days'] ?? 0 }}-day gap between schedules</span>
                    @endif
                </div>
            @endif
        </section>

        {{-- ===== LEAVE MANAGEMENT ===== --}}
        <section class="section-block">
            <div class="grid grid-2">
                <div class="panel">
                    <div class="section-eyebrow">Leave Summary</div>
                    <div class="chart-wrap"><canvas id="leaveDonut"></canvas></div>
                    <div class="legend-list">
                        @foreach([['Approved', $leaveStats['approved'] ?? 0, '#1D9E75'], ['Pending', $leaveStats['pending'] ?? 0, '#EF9F27'], ['Rejected', $leaveStats['rejected'] ?? 0, '#E24B4A'], ['Remaining', $leaveStats['remaining'] ?? 0, '#E2E8F0']] as $legend)
                            <div class="legend-item"><span class="legend-left"><span class="legend-dot" style="background:{{ $legend[2] }}"></span>{{ $legend[0] }}</span><strong>{{ number_format($legend[1]) }}</strong></div>
                        @endforeach
                    </div>
                </div>
                <div class="panel">
                    <div class="section-eyebrow">Recent Leave Requests</div>
                    @if($recentLeaves->count() > 0)
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead><tr><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr></thead>
                                <tbody>
                                @foreach($recentLeaves as $leave)
                                    @php $leaveStatus = strtolower($leave->status ?? 'pending'); $leaveBadge = $leaveStatus === 'approved' ? 'badge-green' : ($leaveStatus === 'rejected' ? 'badge-red' : 'badge-amber'); @endphp
                                    <tr><td>{{ $leave->leave_type ?? 'N/A' }}</td><td>{{ $leave->formatted_from ?? 'N/A' }}</td><td>{{ $leave->formatted_to ?? 'N/A' }}</td><td>{{ $leave->no_of_days ?? 0 }}</td><td><span class="badge {{ $leaveBadge }}">{{ ucfirst($leaveStatus) }}</span></td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">No leave requests found.</div>
                    @endif
                    <div class="cta-row"><a class="view-link" href="{{ route('employee-leaves.create') }}">Apply Leave &rarr;</a></div>
                </div>
            </div>
        </section>

        {{-- ===== PROJECTS & TASKS ===== --}}
        <section class="section-block">
            <div class="section-head"><div><div class="section-eyebrow">Projects & Tasks</div><h2 class="section-title">Current Work</h2></div><a class="view-link" href="{{ route('tasks.my') }}">View My Tasks &rarr;</a></div>
            <div class="grid grid-2">
                <!-- Task Stats -->
                <div class="panel">
                    <div class="section-eyebrow">My Task Statistics</div>
                    <div class="grid grid-2" style="margin-top:14px; gap: 10px;">
                        <div class="mini-card accent-blue" style="min-height: 80px; padding: 12px;"><div class="mini-value" style="font-size: 20px;">{{ number_format($taskStats['total'] ?? 0) }}</div><div class="mini-label">Total Tasks</div></div>
                        <div class="mini-card accent-green" style="min-height: 80px; padding: 12px;"><div class="mini-value" style="font-size: 20px;">{{ number_format($taskStats['completed'] ?? 0) }}</div><div class="mini-label">Completed</div></div>
                        <div class="mini-card accent-amber" style="min-height: 80px; padding: 12px;"><div class="mini-value" style="font-size: 20px;">{{ number_format($taskStats['in_progress'] ?? 0) }}</div><div class="mini-label">In Progress</div></div>
                        <div class="mini-card accent-red" style="min-height: 80px; padding: 12px;"><div class="mini-value" style="font-size: 20px;">{{ number_format($taskStats['pending'] ?? 0) }}</div><div class="mini-label">Pending</div></div>
                    </div>
                    <div class="chart-wrap short" style="margin-top: 20px;"><canvas id="taskDonut"></canvas></div>
                    <div class="legend-list">
                        @foreach([['Completed', $taskStats['completed'] ?? 0, '#1D9E75'], ['In Progress', $taskStats['in_progress'] ?? 0, '#EF9F27'], ['Pending', $taskStats['pending'] ?? 0, '#E24B4A']] as $legend)
                            <div class="legend-item"><span class="legend-left"><span class="legend-dot" style="background:{{ $legend[2] }}"></span>{{ $legend[0] }}</span><strong>{{ number_format($legend[1]) }}</strong></div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="panel" style="background: transparent; border: none; box-shadow: none; padding: 0 !important;">
                    <div class="grid grid-1" style="gap: 15px;">
                    @forelse($recentProjects as $project)
                        <div class="project-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div class="project-title">{{ $project->projectname ?? 'Untitled Project' }}</div>
                                    <span class="badge {{ !empty($project->isLeader) ? 'badge-brand' : 'badge-blue' }}">{{ !empty($project->isLeader) ? 'Project Leader' : 'Team Member' }}</span>
                                </div>
                                <div style="text-align: right;">
                                    <span class="stat-value" style="font-size: 18px; color: var(--brand) !important;">{{ $project->progressPercentage ?? 0 }}%</span>
                                </div>
                            </div>
                            
                            <div class="shift-progress" style="margin-top: 12px; margin-bottom: 12px;">
                                <div class="progress-track"><div class="progress-fill" style="width:{{ $project->progressPercentage ?? 0 }}%;background:var(--brand);"></div></div>
                                <div class="progress-caption" style="display: flex; justify-content: space-between;">
                                    <span>{{ $project->completedTasks ?? 0 }} / {{ $project->totalTasks ?? 0 }} Tasks Done</span>
                                    <span>{{ $project->inProgressTasks ?? 0 }} In Progress</span>
                                </div>
                            </div>

                            <div class="avatar-row" style="margin-top: 10px;">
                                @foreach(array_slice($project->teamMembers ?? [], 0, 4) as $member)
                                    @php $parts = preg_split('/\s+/', trim($member)); $initials = strtoupper(Str::substr($parts[0] ?? 'T', 0, 1) . Str::substr($parts[1] ?? '', 0, 1)); @endphp
                                    <span class="avatar-circle" title="{{ $member }}">{{ $initials }}</span>
                                @endforeach
                                @if(count($project->teamMembers ?? []) > 4)
                                    <span class="avatar-circle avatar-more">+{{ count($project->teamMembers) - 4 }}</span>
                                @endif
                            </div>
                            <div class="cta-row" style="justify-content:flex-start; margin-top: 8px;"><a class="view-link" href="{{ route('taskboard.index') }}">View Taskboard &rarr;</a></div>
                        </div>
                    @empty
                        <div class="panel" style="grid-column:1/-1;"><div class="empty-state">No active projects assigned.</div></div>
                    @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== TICKETS ===== --}}
        <section class="section-block">
            <div class="grid grid-2">
                <div class="panel">
                    <div class="section-eyebrow">Ticket Stats</div>
                    <div class="grid grid-3" style="margin:14px 0;">
                        <div class="mini-card accent-blue"><div class="mini-value">{{ number_format($ticketStats['total'] ?? 0) }}</div><div class="mini-label">Total</div></div>
                        <div class="mini-card accent-red"><div class="mini-value">{{ number_format($ticketStats['open'] ?? 0) }}</div><div class="mini-label">Open</div></div>
                        <div class="mini-card accent-green"><div class="mini-value">{{ number_format($ticketStats['closed'] ?? 0) }}</div><div class="mini-label">Closed</div></div>
                    </div>
                    <div class="chart-wrap short"><canvas id="ticketDonut"></canvas></div>
                </div>
                <div class="panel">
                    <div class="section-eyebrow">Recent Tickets</div>
                    @if($recentTickets->count() > 0)
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead><tr><th>Ticket #</th><th>Subject</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
                                <tbody>
                                @foreach($recentTickets as $ticket)
                                    @php
                                        $priority = ucfirst(strtolower($ticket->priority ?? $ticket->priorities ?? 'Low'));
                                        $priorityBadge = $priority === 'High' ? 'badge-red' : ($priority === 'Medium' ? 'badge-amber' : 'badge-blue');
                                        $ticketStatus = strtolower($ticket->states ?? $ticket->status ?? 'open');
                                        $statusBadge = in_array($ticketStatus, ['new','open','reopened']) ? 'badge-red' : ($ticketStatus === 'in progress' ? 'badge-blue' : ($ticketStatus === 'on hold' ? 'badge-amber' : 'badge-gray'));
                                    @endphp
                                    <tr>
                                        <td>#{{ $ticket->ticket_id ?? $ticket->id }}</td>
                                        <td>{{ Str::limit($ticket->subject ?? 'No subject', 30) }}</td>
                                        <td><span class="badge {{ $priorityBadge }}">{{ $priority }}</span></td>
                                        <td><span class="badge {{ $statusBadge }}">{{ ucfirst($ticketStatus) }}</span></td>
                                        <td>@if(!empty($ticket->created_at)) {{ Carbon::parse($ticket->created_at)->format('d M Y') }} @else N/A @endif</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">No tickets found.</div>
                    @endif
                    <div class="cta-row"><a class="view-link" href="{{ route('tickets.create') }}">Raise New Ticket &rarr;</a></div>
                </div>
            </div>
        </section>

        {{-- ===== TODAY'S ATTENDANCE ===== --}}
        <section class="section-block">
            <div class="panel" style="overflow: hidden; position: relative;">
                <div class="section-eyebrow">Today's Attendance Status</div>
                <div class="grid grid-3" style="margin-top:14px;">
                    <!-- Punch In Card - Now using CSS classes for animation -->
                    <div class="stat-card attendance-stat-card punch-in">
                        <div class="stat-top">
                            <div>
                                <div class="stat-label">Punched In</div>
                                <div class="stat-value" style="color: #1D9E75 !important; font-size: 24px;">
                                    {{ !empty($todaysShift->punched_in_at) ? $todaysShift->punched_in_at : (!empty($todayAttendance->punch_in) ? \Carbon\Carbon::parse($todayAttendance->punch_in)->format('h:i A') : '--:--') }}
                                </div>
                            </div>
                            <span class="stat-icon icon-green"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg></span>
                        </div>
                        <p class="stat-sub">{{ (!empty($todaysShift->punched_in_at) || !empty($todayAttendance->punch_in)) ? 'You have started your shift' : 'Not punched in yet' }}</p>
                    </div>
                    
                    <!-- Punch Out Card -->
                    <div class="stat-card attendance-stat-card punch-out" style="border-bottom-color: #E24B4A;">
                        <div class="stat-top">
                            <div>
                                <div class="stat-label">Punched Out</div>
                                <div class="stat-value" style="color: #E24B4A !important; font-size: 24px;">
                                    {{ !empty($todaysShift->punched_out_at) ? $todaysShift->punched_out_at : (!empty($todayAttendance->punch_out) ? \Carbon\Carbon::parse($todayAttendance->punch_out)->format('h:i A') : '--:--') }}
                                </div>
                            </div>
                            <span class="stat-icon icon-red"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                        </div>
                        <p class="stat-sub">{{ (!empty($todaysShift->punched_out_at) || !empty($todayAttendance->punch_out)) ? 'You have ended your shift' : 'Currently working' }}</p>
                    </div>

                    <!-- Break Time Card -->
                    <div class="stat-card" style="background: linear-gradient(135deg, #FFFBEB 0%, #ffffff 100%); border-color: #EF9F27; transform-origin: center; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 10px 25px rgba(239,159,39,0.2)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px -1px rgba(11,20,55,0.03)';">
                        <div class="stat-top">
                            <div>
                                <div class="stat-label">Break Time</div>
                                <div class="stat-value" style="color: #EF9F27 !important; font-size: 24px;">
                                    @php
                                        $breakTimeDisplay = '--';
                                        if (isset($todayAttendance) && $todayAttendance) {
                                            $sessions = json_decode($todayAttendance->break_sessions ?? '[]', true);
                                            if (is_array($sessions) && count($sessions) > 0) {
                                                $totalBreakMins = 0;
                                                foreach ($sessions as $session) {
                                                    if (isset($session['start']) && isset($session['end'])) {
                                                        $start = \Carbon\Carbon::parse($session['start']);
                                                        $end = \Carbon\Carbon::parse($session['end']);
                                                        $totalBreakMins += $start->diffInMinutes($end);
                                                    } elseif (isset($session['start']) && !isset($session['end'])) {
                                                        // currently on break
                                                        $start = \Carbon\Carbon::parse($session['start']);
                                                        $totalBreakMins += $start->diffInMinutes(\Carbon\Carbon::now());
                                                    }
                                                }
                                                if ($totalBreakMins > 0) {
                                                    $breakTimeDisplay = $totalBreakMins . ' mins';
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $breakTimeDisplay }}
                                </div>
                            </div>
                            <span class="stat-icon icon-amber"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        </div>
                        <p class="stat-sub">Allocated: {{ $todaysShift->break_duration ?? '-- mins' }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== HOLIDAYS ===== --}}
        <section class="section-block">
            <div class="section-head"><div><div class="section-eyebrow">Upcoming Holidays</div><h2 class="section-title">Calendar</h2></div></div>
            <div class="holiday-scroll">
                @forelse($upcomingHolidays as $holiday)
                    @php $daysAway = !empty($holiday->holidaydate) ? Carbon::parse($holiday->holidaydate)->diffInDays(now()) : null; @endphp
                    <div class="holiday-card {{ !empty($holiday->is_next) ? 'next' : '' }}">
                        @if(!empty($holiday->is_next)) <span class="badge badge-brand">Next Holiday</span> @endif
                        <div class="holiday-title">{{ $holiday->holiday ?? 'Holiday' }}</div>
                        <div class="muted">{{ $holiday->formatted_date ?? 'N/A' }}</div>
                        <div class="muted">{{ $holiday->day ?? '' }}</div>
                        @if($daysAway !== null)
                            <div style="margin-top:10px;"><span class="badge {{ $daysAway === 0 ? 'badge-green' : ($daysAway <= 7 ? 'badge-amber' : 'badge-gray') }}">{{ $daysAway === 0 ? 'Today!' : 'In ' . $daysAway . ' days' }}</span></div>
                        @endif
                    </div>
                @empty
                    <div class="panel" style="width:100%;"><div class="empty-state">No upcoming holidays found.</div></div>
                @endforelse
            </div>
        </section>

        {{-- ===== COMMUNITY ===== --}}
        @if($isBirthdayToday || $isAnniversaryToday || ($communityStats['wishes_received_today'] ?? 0) > 0)
            <section class="section-block">
                @if($isBirthdayToday)
                    <div class="celebration-banner birthday"><div class="celebration-icon">&#127874;</div><div><div class="celebration-title">Happy Birthday, {{ $employee->firstname ?? 'Employee' }}!</div><div>{{ number_format($communityStats['wishes_received_today'] ?? 0) }} colleagues have wished you today</div></div></div>
                @elseif($isAnniversaryToday)
                    <div class="celebration-banner anniversary"><div class="celebration-icon">&#127881;</div><div><div class="celebration-title">Happy Work Anniversary, {{ $employee->firstname ?? 'Employee' }}!</div><div>{{ number_format($communityStats['wishes_received_today'] ?? 0) }} colleagues have wished you today</div></div></div>
                @endif
                <div class="grid grid-2" style="margin-top:18px;">
                    @if($myBirthdayWishes->count() > 0)
                        <div class="panel"><div class="section-eyebrow">Birthday Wishes ({{ $myBirthdayWishes->count() }})</div><div class="feed-list" style="margin-top:14px;">@foreach($myBirthdayWishes as $wish) @php $wishInitials = strtoupper(Str::substr($wish->sender_firstname ?? 'C', 0, 1) . Str::substr($wish->sender_lastname ?? '', 0, 1)); @endphp <div class="feed-item"><div class="feed-avatar">{{ $wishInitials }}</div><div><div class="feed-name">{{ $wish->sender_name ?? 'Colleague' }}</div><div class="small-text">{{ $wish->sender_department ?? 'N/A' }}</div><div class="feed-message">{{ Str::limit($wish->message ?? '', 120) }}</div><div class="small-text">{{ $wish->time_ago ?? '' }}</div></div></div> @endforeach</div></div>
                    @endif
                    @if($myAnniversaryWishes->count() > 0)
                        <div class="panel"><div class="section-eyebrow">Anniversary Wishes ({{ $myAnniversaryWishes->count() }})</div><div class="feed-list" style="margin-top:14px;">@foreach($myAnniversaryWishes as $wish) @php $wishInitials = strtoupper(Str::substr($wish->sender_firstname ?? 'C', 0, 1) . Str::substr($wish->sender_lastname ?? '', 0, 1)); @endphp <div class="feed-item"><div class="feed-avatar" style="background:#F5F3FF;color:#5B54B8;">{{ $wishInitials }}</div><div><div class="feed-name">{{ $wish->sender_name ?? 'Colleague' }}</div><div class="small-text">{{ $wish->sender_department ?? 'N/A' }}</div><div class="feed-message">{{ Str::limit($wish->message ?? '', 120) }}</div><div class="small-text">{{ $wish->time_ago ?? '' }}</div></div></div> @endforeach</div></div>
                    @endif
                </div>
            </section>
        @endif

        {{-- ===== ACHIEVEMENTS ===== --}}
        <section class="section-block">
            <div class="section-head"><div><div class="section-eyebrow">Achievements</div><h2 class="section-title">Recognition</h2></div><a class="view-link" href="{{ route('community.index') }}">View All &rarr;</a></div>
            <div class="grid grid-4">
                <div class="mini-card accent-amber"><div class="mini-value">{{ number_format($communityStats['total_achievements'] ?? 0) }}</div><div class="mini-label">Total Achievements</div></div>
                <div class="mini-card accent-red"><div class="mini-value">{{ number_format($communityStats['wishes_received_today'] ?? 0) }}</div><div class="mini-label">Wishes Received Today</div></div>
                <div class="mini-card accent-green"><div class="mini-value">{{ number_format($communityStats['congratulations_received'] ?? 0) }}</div><div class="mini-label">Congrats Received</div></div>
                <div class="mini-card accent-blue"><div class="mini-value">{{ number_format($communityStats['congratulations_sent'] ?? 0) }}</div><div class="mini-label">Congrats Sent</div></div>
            </div>
            <div class="grid grid-5" style="margin-top:18px;">
                @forelse($myPersonalAchievements as $achievement)
                    <div class="achievement-card">
                        <div class="achievement-icon">{{ $achievement->icon ?? '&#127942;' }}</div>
                        <div class="project-title">{{ $achievement->title ?? 'Achievement' }}</div>
                        <p class="stat-sub">{{ Str::limit($achievement->description ?? '', 80) }}</p>
                        <p class="stat-sub" style="margin-top:10px;">Congratulations: {{ number_format($achievement->congratulations_count ?? 0) }}</p>
                        <p class="stat-sub">{{ $achievement->time_ago ?? '' }}</p>
                        <div style="margin-top:10px;"><span class="badge badge-green">Approved</span></div>
                    </div>
                @empty
                    <div class="panel" style="grid-column:1/-1;"><div class="empty-state">No achievements yet - keep up the great work!</div></div>
                @endforelse
            </div>
            @if($achievementCongratulations->count() > 0)
                <div class="panel" style="margin-top:18px;">
                    <div class="section-eyebrow">Recent Congratulations</div>
                    <div class="feed-list" style="margin-top:14px;max-height:none;">
                        @foreach($achievementCongratulations->take(5) as $congrats)
                            @php $congratsInitials = strtoupper(Str::substr($congrats->sender_firstname ?? 'C', 0, 1) . Str::substr($congrats->sender_lastname ?? '', 0, 1)); @endphp
                            <div class="feed-item"><div class="feed-avatar">{{ $congratsInitials }}</div><div><div class="feed-name">{{ $congrats->sender_name ?? 'Colleague' }} congratulated you on "{{ $congrats->achievement_title ?? 'your achievement' }}"</div><div class="small-text">Dept: {{ $congrats->sender_department ?? 'N/A' }} &middot; {{ $congrats->time_ago ?? '' }}</div></div></div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#888780';
        Chart.defaults.plugins.legend.display = false;

        const makeDonut = (id, labels, data, colors, cutout) => {
            const canvas = document.getElementById(id);
            if (!canvas) return;
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout,
                    plugins: { legend: { display: false } }
                }
            });
        };

        makeDonut(
            'leaveDonut',
            ['Approved', 'Pending', 'Rejected', 'Remaining'],
            @json($leaveDonutData),
            ['#1D9E75', '#EF9F27', '#E24B4A', '#E2E8F0'],
            '68%'
        );

        makeDonut(
            'ticketDonut',
            ['Open', 'Closed'],
            @json($ticketDonutData),
            ['#E24B4A', '#1D9E75'],
            '65%'
        );

        makeDonut(
            'taskDonut',
            ['Completed', 'In Progress', 'Pending'],
            @json($taskDonutData),
            ['#1D9E75', '#EF9F27', '#E24B4A'],
            '65%'
        );
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function refreshChatBadge() {
        fetch('{{ route("chat.employee.unread") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var badge = document.getElementById('emp-chat-unread');
            if (!badge) return;
            var count = data.unread || 0;
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(function() {});
    }
    refreshChatBadge();
    setInterval(refreshChatBadge, 5000);
});
</script>
@endsection
