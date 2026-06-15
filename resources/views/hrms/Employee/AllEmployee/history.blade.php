@extends('layouts.index')

@section('content')
<div class="container-fluid mt-4">
<style>
    /* Tecla HRMS Premium Modern Color Design System */
    :root {
        /* Brand Colors matching Sidebar & Header */
        --color-brand-orange: #ff7e40; /* Vibrant Tecla Orange */
        --color-brand-orange-hover: #e66b2f;
        --color-brand-orange-light: rgba(255, 126, 64, 0.08);
        --color-brand-dark: #0f172a; /* Slate 900 */
        --color-brand-slate: #1e293b; /* Slate 800 */
        
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); /* Sleek Slate Header */
        --accent-gradient: linear-gradient(135deg, #ff9f43 0%, #ff7e40 100%); /* Tecla Orange Gradient */
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --warning-gradient: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        --danger-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        --info-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        
        --color-primary: var(--color-brand-orange);
        --color-primary-light: var(--color-brand-orange-light);
        --color-secondary: var(--color-brand-slate);
        --color-success: #10b981;
        --color-success-light: rgba(16, 185, 129, 0.08);
        --color-warning: #f59e0b;
        --color-warning-light: rgba(245, 158, 11, 0.08);
        --color-danger: #ef4444;
        --color-danger-light: rgba(239, 68, 68, 0.08);
        --color-info: #0ea5e9;
        --color-info-light: rgba(14, 165, 233, 0.08);
        
        --bg-surface: #ffffff;
        --bg-body-gradient: radial-gradient(circle at 10% 20%, rgba(248, 250, 252, 0.5) 0%, rgba(255, 255, 255, 0.9) 90%);
        
        --text-main: #0f172a;
        --text-secondary: #475569;
        --text-light: #94a3b8;
        
        --border-subtle: rgba(15, 23, 42, 0.04);
        --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 16px -6px rgba(15, 23, 42, 0.02);
        --card-shadow-hover: 0 20px 35px -10px rgba(255, 126, 64, 0.1);
        --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        --radius-lg: 1rem;
        --radius-md: 0.75rem;
    }

    body {
        background: var(--bg-body-gradient);
    }

    /* Page Header */
    .page-title {
        font-weight: 800;
        letter-spacing: -0.025em;
        color: var(--text-main);
    }
    .breadcrumb-item a {
        color: var(--text-secondary);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition-smooth);
    }
    .breadcrumb-item a:hover {
        color: var(--color-brand-orange);
    }
    .btn-outline-secondary {
        border-color: #e2e8f0;
        color: var(--text-secondary);
        font-weight: 600;
        border-radius: var(--radius-md);
        padding: 0.5rem 1rem;
        transition: var(--transition-smooth);
        background: #ffffff;
    }
    .btn-outline-secondary:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: var(--text-main);
        transform: translateY(-1px);
    }

    /* Unified Container Cards */
    .history-page-header,
    .history-summary-card,
    .history-panel-card,
    .history-table-card {
        border: 1px solid var(--border-subtle) !important;
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        background: var(--bg-surface);
        transition: var(--transition-smooth);
    }

    /* Sophisticated, Clean Compact Header Card */
    .history-page-header {
        border-left: 5px solid var(--color-brand-orange) !important;
        border-top: 1px solid var(--border-subtle) !important;
        border-right: 1px solid var(--border-subtle) !important;
        border-bottom: 1px solid var(--border-subtle) !important;
        background: #ffffff !important;
        color: var(--text-main);
    }
    .history-page-header .card-body {
        padding: 1.5rem;
    }
    .history-page-header h4 {
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-main);
    }
    .history-page-header .text-muted-custom {
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Sleek Fallback Avatar */
    .avatar-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }

    /* Header Chips */
    .history-chip {
        display: inline-flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .history-chip span {
        background: #f8fafc;
        color: var(--text-secondary);
        padding: 0.4rem 0.95rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }
    .history-chip span.badge {
        background: var(--color-brand-orange-light) !important;
        color: var(--color-brand-orange) !important;
        border: 1px solid rgba(255, 126, 64, 0.15);
    }

    /* SaaS Dashboard Style Horizontal Top Summary Cards */
    .history-stat-card {
        min-height: 85px;
        position: relative;
        background: #ffffff;
        border: 1px solid var(--border-subtle) !important;
    }
    .history-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -5px rgba(255, 126, 64, 0.08) !important;
        border-color: rgba(255, 126, 64, 0.12) !important;
    }
    .history-stat-card .card-body {
        padding: 1.15rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .history-stat-card .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        transition: var(--transition-smooth);
    }
    .history-stat-card:hover .stat-icon {
        transform: scale(1.08);
    }

    /* Color variations for icons */
    .stat-tenure .stat-icon { background: rgba(255, 126, 64, 0.08); color: var(--color-brand-orange); }
    .stat-promotions .stat-icon { background: rgba(30, 41, 59, 0.06); color: var(--color-brand-slate); }
    .stat-salary .stat-icon { background: rgba(16, 185, 129, 0.08); color: var(--color-success); }
    .stat-leaves .stat-icon { background: rgba(255, 126, 64, 0.08); color: var(--color-brand-orange); }
    .stat-expenses .stat-icon { background: rgba(239, 68, 68, 0.08); color: var(--color-danger); }
    .stat-modules .stat-icon { background: rgba(30, 41, 59, 0.06); color: var(--color-brand-slate); }

    .history-stat-card .stat-info-block {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .history-stat-card .stat-label {
        color: var(--text-secondary);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 0.15rem;
    }
    .history-stat-card .stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    /* Premium Segmented Navigation Tabs */
    .nav-tabs {
        border-bottom: 0;
        background: rgba(241, 245, 249, 0.8);
        padding: 0.35rem;
        border-radius: 999px;
        display: inline-flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        max-width: 100%;
        border: 1px solid rgba(226, 232, 240, 0.8);
        margin-bottom: 1.75rem !important;
        scrollbar-width: none;
    }
    .nav-tabs::-webkit-scrollbar {
        display: none;
    }
    .nav-tabs .nav-link {
        border: none;
        border-radius: 999px;
        color: var(--text-secondary);
        background: transparent;
        padding: 0.6rem 1.3rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: var(--transition-smooth);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .nav-tabs .nav-link:hover {
        color: var(--color-brand-orange);
        background: rgba(255, 126, 64, 0.04);
    }
    .nav-tabs .nav-link.active {
        background: var(--accent-gradient);
        color: #fff !important;
        box-shadow: 0 6px 15px rgba(255, 126, 64, 0.22);
    }

    /* Distinct Dynamic Content Panels */
    .history-tab-pane {
        min-height: 350px;
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg) !important;
        padding: 2rem;
        box-shadow: var(--card-shadow);
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Visual Color-Coded Status Cards for Tab panes */
    .tab-status-card {
        border-radius: var(--radius-md);
        border: 1px solid rgba(226, 232, 240, 0.7);
        transition: var(--transition-smooth);
        min-height: 105px;
    }
    .tab-status-card .card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .tab-status-card .stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .tab-status-card .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tab-status-card .stat-icon {
        font-size: 1rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tab-status-card .stat-value {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1;
    }

    /* Color variations for tab status cards */
    .status-card-neutral {
        background: #f8fafc;
        border-left: 4px solid var(--text-secondary);
    }
    .status-card-neutral .stat-label { color: var(--text-secondary); }
    .status-card-neutral .stat-icon { background: rgba(100, 116, 139, 0.1); color: var(--text-secondary); }
    .status-card-neutral .stat-value { color: var(--text-main); }

    .status-card-success {
        background: rgba(16, 185, 129, 0.02);
        border-left: 4px solid var(--color-success);
        border-color: rgba(16, 185, 129, 0.1);
    }
    .status-card-success .stat-label { color: #047857; }
    .status-card-success .stat-icon { background: rgba(16, 185, 129, 0.1); color: var(--color-success); }
    .status-card-success .stat-value { color: #047857; }

    .status-card-warning {
        background: rgba(245, 158, 11, 0.02);
        border-left: 4px solid var(--color-warning);
        border-color: rgba(245, 158, 11, 0.1);
    }
    .status-card-warning .stat-label { color: #b45309; }
    .status-card-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: var(--color-warning); }
    .status-card-warning .stat-value { color: #b45309; }

    .status-card-danger {
        background: rgba(239, 68, 68, 0.02);
        border-left: 4px solid var(--color-danger);
        border-color: rgba(239, 68, 68, 0.1);
    }
    .status-card-danger .stat-label { color: #b91c1c; }
    .status-card-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: var(--color-danger); }
    .status-card-danger .stat-value { color: #b91c1c; }

    /* Timeline Panel Styling */
    .timeline {
        position: relative;
        padding-left: 2.2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.95rem;
        top: 8px;
        bottom: 8px;
        width: 3px;
        background: linear-gradient(180deg, var(--color-brand-orange) 0%, rgba(255, 126, 64, 0.25) 50%, rgba(241, 245, 249, 0) 100%);
        border-radius: 999px;
    }
    .timeline-event {
        position: relative;
        padding-left: 1.25rem;
    }
    .timeline-event::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 1.4rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px rgba(255, 126, 64, 0.18);
        background: var(--color-brand-orange);
        z-index: 2;
        transition: var(--transition-smooth);
    }
    .timeline-event:hover::before {
        background: var(--color-brand-dark);
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.12);
        transform: scale(1.1);
    }
    .timeline-event .card {
        border-radius: var(--radius-md) !important;
        border: 1px solid var(--border-subtle) !important;
        border-left: 4px solid var(--color-brand-orange) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
        transition: var(--transition-smooth);
    }
    .timeline-event:hover .card {
        border-left-color: var(--color-brand-dark) !important;
        transform: translateX(3px);
    }

    /* Table Enhancements */
    .table-responsive {
        border-radius: var(--radius-md);
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table th {
        background: #f8fafc !important;
        color: var(--text-secondary);
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.9rem 1.15rem !important;
        border-bottom: 2px solid #f1f5f9 !important;
    }
    .table td {
        padding: 0.9rem 1.15rem !important;
        color: var(--text-main);
        font-weight: 500;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .table tr:last-child td {
        border-bottom: 0 !important;
    }
    .table tbody tr {
        transition: var(--transition-smooth);
    }
    .table tbody tr:hover {
        background-color: rgba(255, 126, 64, 0.015);
    }

    /* Custom Section Titles */
    .history-section-title {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-secondary);
        border-left: 3px solid var(--color-brand-orange);
        padding-left: 0.6rem;
    }

    /* Leaves & Progress bar */
    .leave-progress-wrapper {
        background: #f8fafc;
        border-radius: var(--radius-md);
        padding: 1.15rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: var(--transition-smooth);
    }
    .leave-progress-wrapper:hover {
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .leave-bar-container {
        height: 8px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .leave-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--accent-gradient);
        transition: width 1s ease-in-out;
    }
    
    /* Module Grid cards */
    .module-card {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-subtle) !important;
        transition: var(--transition-smooth);
    }
    .module-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 126, 64, 0.15) !important;
        box-shadow: 0 8px 20px rgba(255, 126, 64, 0.05) !important;
    }

    /* Beautiful Soft Badges */
    .badge-soft-success {
        background-color: var(--color-success-light) !important;
        color: #047857 !important;
    }
    .badge-soft-warning {
        background-color: var(--color-warning-light) !important;
        color: #b45309 !important;
    }
    .badge-soft-danger {
        background-color: var(--color-danger-light) !important;
        color: #b91c1c !important;
    }
    .badge-soft-primary {
        background-color: var(--color-brand-orange-light) !important;
        color: #d85c1f !important;
    }
    .badge-soft-secondary {
        background-color: rgba(100, 116, 139, 0.08) !important;
        color: #475569 !important;
    }

    .salary-flex-row {
        transition: var(--transition-smooth);
    }
    .salary-flex-row:hover {
        background-color: #f8fafc;
        border-radius: 6px;
        padding-left: 4px;
        padding-right: 4px;
    }

    /* Custom empty state style */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: var(--radius-md);
        color: var(--text-secondary);
    }
    .empty-state i {
        font-size: 2.2rem;
        color: var(--text-light);
        margin-bottom: 0.75rem;
    }
</style>

<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Employee History</h3>
            <ul class="breadcrumb mb-0 mt-1">
                <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">Employees</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employee.show', $employee->id) }}">{{ $employee->firstname }} {{ $employee->lastname }}</a></li>
                <li class="breadcrumb-item active text-secondary">History</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
            </a>
        </div>
    </div>
</div>

{{-- Employee Header --}}
<div class="card history-page-header mb-4">
    <div class="card-body d-flex flex-column flex-md-row align-items-center gap-4">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 text-center text-sm-start w-100 w-md-auto">
            <div class="avatar-wrapper">
                @if($employee->profile_image)
                    <img src="{{ asset($employee->profile_image) }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="w-100 h-100 align-items-center justify-content-center fw-bold text-white bg-primary d-none" style="background:var(--accent-gradient) !important;">
                        {{ strtoupper(substr($employee->firstname, 0, 1)) }}
                    </div>
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center fw-bold text-white bg-primary" style="background:var(--accent-gradient) !important;">
                        {{ strtoupper(substr($employee->firstname, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h4 class="mb-1">{{ $employee->firstname }} {{ $employee->lastname }}</h4>
                <div class="text-muted-custom">
                    <i class="fa-solid fa-briefcase me-1 opacity-75"></i> {{ $employee->designation_name }} &nbsp;·&nbsp;
                    <i class="fa-solid fa-building me-1 opacity-75"></i> {{ $employee->department_name }} &nbsp;·&nbsp;
                    <i class="fa-solid fa-location-dot me-1 opacity-75"></i> {{ $employee->branch_name }}
                </div>
            </div>
        </div>
        <div class="ms-md-auto w-100 w-md-auto text-center text-md-end">
            <div class="history-chip">
                <span>ID: {{ $employee->employeeid }}</span>
                <span>Joined {{ \Carbon\Carbon::parse($employee->joiningdate)->format('d M Y') }}</span>
                <span class="badge">Active</span>
            </div>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-tenure h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Tenure</span>
                    <div class="stat-value">{{ \Carbon\Carbon::parse($employee->joiningdate)->diffInYears(now()) }}y {{ \Carbon\Carbon::parse($employee->joiningdate)->diffInMonths(now()) % 12 }}m</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-promotions h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Promotions</span>
                    <div class="stat-value">{{ $promotions->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-salary h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Net Salary</span>
                    <div class="stat-value">₹{{ $salary ? number_format($salary->net_salary) : '—' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-leaves h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-door-open"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Leaves</span>
                    <div class="stat-value">{{ $leaves->filter(fn($l) => \Carbon\Carbon::parse($l->from_date)->year == date('Y'))->sum('no_of_days') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-expenses h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Expenses</span>
                    <div class="stat-value">₹{{ number_format($expenseSummary['total']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card history-summary-card history-stat-card stat-modules h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
                <div class="stat-info-block">
                    <span class="stat-label">Modules</span>
                    <div class="stat-value">{{ $modules->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="d-flex justify-content-start overflow-hidden">
    <ul class="nav nav-tabs" id="historyTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#timeline">
                <i class="fa-solid fa-timeline"></i> Timeline
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#salary">
                <i class="fa-solid fa-money-bill-wave"></i> Salary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#leaves">
                <i class="fa-solid fa-umbrella-beach"></i> Leaves
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#expenses">
                <i class="fa-solid fa-receipt"></i> Expenses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#modules">
                <i class="fa-solid fa-bars-progress"></i> Modules
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#offboarding">
                <i class="fa-solid fa-door-closed"></i> Offboarding
            </a>
        </li>
    </ul>
</div>

<div class="tab-content">

{{-- TIMELINE --}}
<div class="tab-pane fade show active history-tab-pane" id="timeline">
    <div class="timeline">
        @forelse($timeline as $event)
        <div class="timeline-event mb-4">
            <div class="card history-panel-card">
                <div class="card-body bg-light-panel">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                        <div class="text-secondary small fw-600">
                            <i class="fa-regular fa-clock me-1"></i> {{ \Carbon\Carbon::parse($event['date'])->format('d M Y') }}
                        </div>
                        <span class="badge badge-soft-primary">{{ $event['type'] }}</span>
                    </div>
                    <h6 class="mb-1 text-main fw-700">{{ $event['title'] }}</h6>
                    <p class="text-secondary small mb-0">{{ $event['desc'] }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <h5>No Timeline Events</h5>
            <p class="mb-0 small text-light">This employee has no registered timeline activities.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- SALARY --}}
<div class="tab-pane fade history-tab-pane" id="salary">
    @if($salary)
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card history-panel-card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-4">
                        <i class="fa-solid fa-circle-plus text-success"></i> Earnings
                    </h6>
                    <div class="d-flex flex-column gap-1">
                        @foreach(['basic'=>'Basic','hra'=>'HRA','da'=>'DA','conveyance'=>'Conveyance','allowance'=>'Allowance','medical'=>'Medical'] as $key=>$label)
                        <div class="d-flex justify-content-between py-2 border-bottom small salary-flex-row">
                            <span class="text-secondary fw-500">{{ $label }}</span>
                            <span class="fw-700 text-main">₹{{ number_format($salary->$key ?? 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between pt-4 fw-800 text-main fs-6">
                        <span>Total Earnings</span>
                        <span class="text-primary">₹{{ number_format($salary->total_earnings ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card history-panel-card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-4">
                        <i class="fa-solid fa-circle-minus text-danger"></i> Deductions
                    </h6>
                    <div class="d-flex flex-column gap-1">
                        @foreach(['pf'=>'PF','esi'=>'ESI','tds'=>'TDS','tax'=>'Professional Tax','welfare'=>'Welfare'] as $key=>$label)
                        <div class="d-flex justify-content-between py-2 border-bottom small salary-flex-row">
                            <span class="text-secondary fw-500">{{ $label }}</span>
                            <span class="fw-700 text-danger opacity-75">₹{{ number_format($salary->$key ?? 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between pt-4 fw-800 text-success fs-5">
                        <span>Net Salary</span>
                        <span class="badge badge-soft-success fs-6 px-3 py-2 border-0">₹{{ number_format($salary->net_salary ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="empty-state mb-4">
            <i class="fa-solid fa-receipt"></i>
            <h5>No Salary Record Found</h5>
            <p class="mb-0 small text-light">No salary details exist for this employee.</p>
        </div>
    @endif

    @if($hikeHistory->count())
    <h6 class="mt-5 mb-3 history-section-title">Hike Letter History</h6>
    <div class="card history-table-card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>New CTC (Annual)</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($hikeHistory as $h)
                        @php $d = json_decode($h->new_salary_details, true) @endphp
                        <tr>
                            <td class="fw-600 text-secondary">{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y') }}</td>
                            <td class="fw-700 text-main">₹{{ $d['ctc_annual'] ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $h->status === 'sent' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($h->pdf_path && file_exists(storage_path('app/'.$h->pdf_path)))
                                <a href="{{ route('employee.hike-letter.download', $h->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-3">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                                </a>
                                @else
                                <span class="text-light small">Not Available</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- LEAVES --}}
<div class="tab-pane fade history-tab-pane" id="leaves">
    <div class="card history-panel-card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-4">
                <i class="fa-solid fa-chart-pie text-primary"></i> Leave Balance
            </h6>
            <div class="row g-3">
                @foreach(['Casual Leave'=>($leaveInfo->casual_leaves ?? 12),'Sick'=>($leaveInfo->sick_leaves ?? 6),'Hospitalisation'=>($leaveInfo->hospitalization_leaves ?? 5),'Maternity Leave'=>($leaveInfo->maternity_leaves ?? 90),'Paternity Leave'=>($leaveInfo->paternity_leaves ?? 5)] as $type=>$total)
                @php 
                    $used = $leaves->where('leave_type', $type)->sum('no_of_days'); 
                    $remaining = $leaveBalance[$type] ?? ($total - $used); 
                    $pct = $total > 0 ? min(100, round($used/$total*100)) : 0; 
                @endphp
                <div class="col-md-6">
                    <div class="leave-progress-wrapper h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-700 text-main fs-7">{{ $type }}</span>
                            <span class="badge badge-soft-secondary font-weight-700">{{ $remaining }} left</span>
                        </div>
                        <div class="leave-bar-container my-2">
                            <div class="leave-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 small text-secondary">
                            <span>Usage Percent: <strong class="text-main">{{ $pct }}%</strong></span>
                            <span>{{ $used }} / {{ $total }} used</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <h6 class="mt-4 mb-3 history-section-title">Leave History</h6>
    <div class="card history-table-card border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($leaves as $l)
                    <tr>
                        <td class="fw-700 text-main">{{ $l->leave_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($l->to_date)->format('d M Y') }}</td>
                        <td class="fw-700 text-primary">{{ $l->no_of_days }}</td>
                        <td class="text-secondary small">{{ Str::limit($l->leave_reason, 40) }}</td>
                        <td>
                            <span class="badge {{ $l->status==='approved'?'badge-soft-success':($l->status==='pending'?'badge-soft-warning':'badge-soft-danger') }}">
                                {{ ucfirst($l->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-secondary small">
                            <i class="fa-solid fa-umbrella-beach d-block fs-3 mb-2 opacity-50"></i> No leave records found.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($permissions->count())
    <h6 class="mt-4 mb-3 history-section-title">Permissions</h6>
    <div class="card history-table-card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $p)
                    <tr>
                        <td class="fw-600 text-secondary">{{ \Carbon\Carbon::parse($p->permission_date)->format('d M Y') }}</td>
                        <td class="text-main fw-500">{{ $p->start_time }} – {{ $p->end_time }}</td>
                        <td class="fw-700 text-primary">{{ $p->duration }} hrs</td>
                        <td class="text-secondary small">{{ Str::limit($p->permission_reason, 40) }}</td>
                        <td>
                            <span class="badge {{ $p->status==='approved'?'badge-soft-success':($p->status==='pending'?'badge-soft-warning':'badge-soft-danger') }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- EXPENSES --}}
<div class="tab-pane fade history-tab-pane" id="expenses">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card tab-status-card status-card-neutral">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Total Expenses</span>
                        <div class="stat-icon"><i class="fa-solid fa-calculator"></i></div>
                    </div>
                    <div class="stat-value">₹{{ number_format($expenseSummary['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card tab-status-card status-card-success">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Approved</span>
                        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    <div class="stat-value">{{ $expenseSummary['approved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card tab-status-card status-card-warning">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Pending</span>
                        <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    </div>
                    <div class="stat-value">{{ $expenseSummary['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card tab-status-card status-card-danger">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Rejected</span>
                        <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                    </div>
                    <div class="stat-value">{{ $expenseSummary['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="mt-4 mb-3 history-section-title">Expense History</h6>
    <div class="card history-table-card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td class="fw-600 text-secondary">{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
                        <td class="text-main fw-500">{{ Str::limit($e->expense_purpose, 50) }}</td>
                        <td class="fw-700 text-main">₹{{ number_format($e->expense_amount) }}</td>
                        <td>
                            <span class="badge {{ $e->expense_status==='approved'?'badge-soft-success':($e->expense_status==='pending'?'badge-soft-warning':'badge-soft-danger') }}">
                                {{ ucfirst($e->expense_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-secondary small">
                            <i class="fa-solid fa-receipt d-block fs-3 mb-2 opacity-50"></i> No expense records found.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODULES --}}
<div class="tab-pane fade history-tab-pane" id="modules">
    <div class="d-flex gap-3 mb-4 small text-secondary align-items-center justify-content-start bg-light py-2 px-3 rounded-pill d-inline-flex border">
        <span class="fw-600"><i class="fa-solid fa-circle-info text-primary me-1"></i> Module Source:</span>
        <span class="d-flex align-items-center gap-1">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;"></span> From Hierarchy
        </span>
        <span class="d-flex align-items-center gap-1">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ff7e40;"></span> Manual
        </span>
    </div>
    
    <div class="row g-3">
    @forelse($modules as $m)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card module-card h-100">
                <div class="card-body p-3 d-flex align-items-start gap-3">
                    <span style="width:10px;height:10px;border-radius:50%;margin-top:6px;flex-shrink:0;background:{{ $m->source==='hierarchy'?'#10b981':'#ff7e40' }}; box-shadow: 0 0 8px {{ $m->source==='hierarchy'?'#10b981':'#ff7e40' }};"></span>
                    <div>
                        <div class="fw-700 text-main small mb-1">{{ $m->module_name }}</div>
                        <div class="text-secondary d-flex flex-wrap gap-1 align-items-center" style="font-size:11px; font-weight: 600;">
                            @if($m->can_view) <span class="badge bg-light text-secondary border px-2 py-1">View</span> @endif
                            @if($m->can_create) <span class="badge bg-light text-secondary border px-2 py-1">Create</span> @endif
                            @if($m->can_edit) <span class="badge bg-light text-secondary border px-2 py-1">Edit</span> @endif
                            @if($m->can_delete) <span class="badge bg-light text-secondary border px-2 py-1">Delete</span> @endif
                            @if($m->can_approve) <span class="badge bg-light text-secondary border px-2 py-1">Approve</span> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col">
            <div class="empty-state">
                <i class="fa-solid fa-cubes"></i>
                <h5>No Modules Assigned</h5>
                <p class="mb-0 small text-light">This employee does not have any modules assigned to them.</p>
            </div>
        </div>
    @endforelse
    </div>
</div>

{{-- OFFBOARDING --}}
<div class="tab-pane fade history-tab-pane" id="offboarding">
    @if($offboarding)
    <div class="card history-panel-card border-0">
        <div class="card-body p-4">
            <h6 class="card-title mb-4 border-bottom pb-2">
                <i class="fa-solid fa-person-walking-arrow-right text-danger"></i> Exit Details
            </h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between border-bottom py-3 small salary-flex-row align-items-center">
                        <span class="text-secondary fw-600">Type</span>
                        <span class="fw-700 text-main text-capitalize">{{ $offboarding->offboarding_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-3 small salary-flex-row align-items-center">
                        <span class="text-secondary fw-600">Exit Type</span>
                        <span class="fw-700 text-main">{{ $offboarding->exit_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-3 small salary-flex-row align-items-center">
                        <span class="text-secondary fw-600">Reason</span>
                        <span class="fw-700 text-main" title="{{ $offboarding->reason }}">{{ Str::limit($offboarding->reason, 60) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-3 small salary-flex-row align-items-center">
                        <span class="text-secondary fw-600">Last Working Date</span>
                        <span class="fw-700 text-primary"><i class="fa-solid fa-calendar-day me-1"></i> {{ \Carbon\Carbon::parse($offboarding->last_working_date)->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-3 small salary-flex-row align-items-center">
                        <span class="text-secondary fw-600">Status</span>
                        <span class="badge {{ $offboarding->status==='inprogress'?'badge-soft-warning':'badge-soft-success' }} px-3 py-2 border-0 fw-700 text-uppercase">
                            {{ $offboarding->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-2">
                <a href="{{ route('offboarding.show', $offboarding->id) }}" class="btn btn-outline-secondary btn-sm py-2 px-3">
                    <i class="fa-solid fa-circle-info me-1"></i> View Full Offboarding Details
                </a>
            </div>
        </div>
    </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-user-check"></i>
            <h5>No Offboarding Request</h5>
            <p class="mb-0 small text-light">This employee has no active offboarding or resignation records.</p>
        </div>
    @endif
</div>

</div>
</div>
</div>
@endsection
