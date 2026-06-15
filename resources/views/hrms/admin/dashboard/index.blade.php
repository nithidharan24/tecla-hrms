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
    /* Scope everything strictly inside .admin-dashboard to prevent global style pollution */
    
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

    .admin-dashboard * {
        box-sizing: border-box;
    }

    .admin-dashboard .dashboard-body {
        padding: 32px 40px;
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
    }

    .admin-dashboard h1, 
    .admin-dashboard h2, 
    .admin-dashboard h3, 
    .admin-dashboard h4, 
    .admin-dashboard h5, 
    .admin-dashboard h6, 
    .admin-dashboard p, 
    .admin-dashboard span, 
    .admin-dashboard td, 
    .admin-dashboard th {
        font-family: 'DM Sans', sans-serif !important;
    }

    /* -------------------------------------------------
       DASHBOARD HEADER & ACTION BAR
    ------------------------------------------------- */
    .admin-dashboard .dashboard-header {
        align-items: center;
        background: #FFFFFF !important;
        border-bottom: 1px solid #E5E7EB !important;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        min-height: 70px;
        padding: 16px 60px;
        position: sticky;
        top: 0;
        z-index: 20;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
    }

    .admin-dashboard .dashboard-header h1 {
        color: #0B1437 !important;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        letter-spacing: -0.5px;
    }

    .admin-dashboard .header-status {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #4B5563;
    }

    .admin-dashboard .status-time {
        font-weight: 500;
        color: #0B1437;
    }

    .admin-dashboard .status-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ECFDF5;
        color: #10B981;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }

    .admin-dashboard .live-dot {
        width: 6px;
        height: 6px;
        background-color: #10B981;
        border-radius: 50%;
        display: inline-block;
        position: relative;
    }
    .admin-dashboard .live-dot::after {
        content: '';
        position: absolute;
        top: -1px;
        left: -1px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        border: 1px solid #10B981;
        animation: pulse-live-dash 1.8s infinite;
    }
    @keyframes pulse-live-dash {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(2.8); opacity: 0; }
    }

    .admin-dashboard .header-actions {
        align-items: center;
        display: flex;
        gap: 12px;
    }

    .admin-dashboard .btn-dashboard-primary {
        background: #F97316 !important;
        color: #FFFFFF !important;
        border: none !important;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
    }
    .admin-dashboard .btn-dashboard-primary:hover {
        background: #EA580C !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
    }

    .admin-dashboard .btn-dashboard-secondary {
        background: #FFF7ED !important;
        color: #F97316 !important;
        border: 1px solid rgba(249, 115, 22, 0.2) !important;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .admin-dashboard .btn-dashboard-secondary:hover {
        background: #FFEFE6 !important;
        transform: translateY(-1px);
    }

    /* -------------------------------------------------
       GRID SYSTEM
    ------------------------------------------------- */
    .admin-dashboard .section-block {
        margin-top: 32px;
    }

    .admin-dashboard .section-head {
        align-items: flex-end;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .admin-dashboard .section-eyebrow {
        color: #8892B0;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .admin-dashboard .section-title {
        color: #0B1437;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .admin-dashboard .grid {
        display: grid;
        gap: 20px;
    }

    .admin-dashboard .grid-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .admin-dashboard .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .admin-dashboard .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .admin-dashboard .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    /* -------------------------------------------------
       STAT & PANEL CARDS
    ------------------------------------------------- */
    .admin-dashboard .stat-card,
    .admin-dashboard .panel,
    .admin-dashboard .mini-card {
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(11, 20, 55, 0.03), 0 2px 4px -1px rgba(11, 20, 55, 0.02) !important;
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        animation: fadeInUpDash 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .admin-dashboard .stat-card {
        min-height: 146px;
        overflow: hidden;
        padding: 22px 24px !important;
        border-bottom: 3px solid transparent !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .admin-dashboard .stat-card::before {
        display: none !important;
    }

    .admin-dashboard .stat-card:hover,
    .admin-dashboard .panel:hover,
    .admin-dashboard .mini-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 20px -8px rgba(11, 20, 55, 0.08), 0 4px 12px -2px rgba(11, 20, 55, 0.03) !important;
    }

    /* Staggered Delay for Cards */
    .admin-dashboard .stat-card:nth-child(1) { animation-delay: .05s; }
    .admin-dashboard .stat-card:nth-child(2) { animation-delay: .10s; }
    .admin-dashboard .stat-card:nth-child(3) { animation-delay: .15s; }
    .admin-dashboard .stat-card:nth-child(4) { animation-delay: .20s; }
    .admin-dashboard .stat-card:nth-child(5) { animation-delay: .25s; }
    .admin-dashboard .stat-card:nth-child(6) { animation-delay: .30s; }

    .admin-dashboard .stat-top {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .admin-dashboard .stat-icon {
        align-items: center;
        border-radius: 12px;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
        font-size: 18px;
    }

    /* Icon Tile Specific Colors mapped to indices */
    .admin-dashboard .stat-card:nth-child(1) { border-bottom-color: #6366F1 !important; }
    .admin-dashboard .stat-card:nth-child(1) .stat-icon { background: #EEF2FF !important; color: #6366F1 !important; }

    .admin-dashboard .stat-card:nth-child(2) { border-bottom-color: #10B981 !important; }
    .admin-dashboard .stat-card:nth-child(2) .stat-icon { background: #ECFDF5 !important; color: #10B981 !important; }

    .admin-dashboard .stat-card:nth-child(3) { border-bottom-color: #EF4444 !important; }
    .admin-dashboard .stat-card:nth-child(3) .stat-icon { background: #FEF2F2 !important; color: #EF4444 !important; }

    .admin-dashboard .stat-card:nth-child(4) { border-bottom-color: #F59E0B !important; }
    .admin-dashboard .stat-card:nth-child(4) .stat-icon { background: #FFFBEB !important; color: #F59E0B !important; }

    .admin-dashboard .stat-card:nth-child(5) { border-bottom-color: #F97316 !important; }
    .admin-dashboard .stat-card:nth-child(5) .stat-icon { background: #FFEFE6 !important; color: #F97316 !important; }

    .admin-dashboard .stat-card:nth-child(6) { border-bottom-color: #8B5CF6 !important; }
    .admin-dashboard .stat-card:nth-child(6) .stat-icon { background: #F5F3FF !important; color: #8B5CF6 !important; }

    .admin-dashboard .stat-value {
        color: #0B1437 !important;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        margin: 0 0 6px;
        letter-spacing: -0.5px;
    }

    .admin-dashboard .stat-label {
        color: #4B5563 !important;
        font-size: 13px;
        font-weight: 600;
        margin: 0;
    }

    .admin-dashboard .stat-sub,
    .admin-dashboard .mini-label {
        color: #8892B0 !important;
        font-size: 12px;
        margin: 0;
    }

    .admin-dashboard .panel {
        padding: 24px !important;
    }

    .admin-dashboard .panel-header {
        align-items: flex-start;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    /* -------------------------------------------------
       CHARTS & DONUT CENTER TEXT
    ------------------------------------------------- */
    .admin-dashboard .chart-box {
        height: 220px;
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .admin-dashboard .chart-box canvas {
        max-height: 220px;
    }

    .admin-dashboard .chart-box.short { height: 180px; }
    .admin-dashboard .chart-box.short canvas { max-height: 180px; }
    
    .admin-dashboard .chart-box.area { height: 200px; }
    .admin-dashboard .chart-box.area canvas { max-height: 200px; }

    .admin-dashboard .donut-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .admin-dashboard .donut-center-text {
        position: absolute;
        text-align: center;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .admin-dashboard .donut-number {
        font-size: 26px;
        font-weight: 700;
        color: #0B1437;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    
    .admin-dashboard .donut-label {
        font-size: 11px;
        color: #8892B0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        font-weight: 700;
    }

    .admin-dashboard .donut-wrap {
        align-items: center;
        display: grid;
        gap: 20px;
        grid-template-columns: minmax(150px, 1fr) minmax(150px, 180px);
    }

    .admin-dashboard .donut-wrap .chart-box {
        max-height: 250px;
    }

    .admin-dashboard .legend-list,
    .admin-dashboard .alert-list,
    .admin-dashboard .review-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .admin-dashboard .legend-item,
    .admin-dashboard .alert-item,
    .admin-dashboard .review-item {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    .admin-dashboard .legend-left,
    .admin-dashboard .alert-left,
    .admin-dashboard .review-person {
        align-items: center;
        display: flex;
        gap: 12px;
        min-width: 0;
    }

    .admin-dashboard .legend-dot,
    .admin-dashboard .alert-dot {
        border-radius: 50%;
        flex: 0 0 auto;
        height: 10px;
        width: 10px;
    }

    /* -------------------------------------------------
       RECRUITMENT PIPELINE LIST & PAYROLL ROWS
    ------------------------------------------------- */
    .admin-dashboard .pipeline-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 6px;
    }

    .admin-dashboard .pipeline-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .admin-dashboard .pipeline-label-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-dashboard .pipeline-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .admin-dashboard .pipeline-name {
        font-size: 13px;
        font-weight: 500;
        color: #4B5563;
    }

    .admin-dashboard .pipeline-bar-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .admin-dashboard .pipeline-bar-track {
        flex: 1;
        height: 6px;
        background: #E5E7EB;
        border-radius: 4px;
        overflow: hidden;
    }

    .admin-dashboard .pipeline-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.8s ease-out;
    }

    .admin-dashboard .pipeline-value {
        font-size: 13px;
        font-weight: 700;
        color: #0B1437;
        min-width: 24px;
        text-align: right;
    }

    /* Payroll rows */
    .admin-dashboard .payroll-rows {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
    }

    .admin-dashboard .payroll-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.04);
    }
    .admin-dashboard .payroll-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .admin-dashboard .payroll-label {
        font-size: 13px;
        color: #4B5563;
        font-weight: 500;
    }

    .admin-dashboard .payroll-val {
        font-size: 14px;
        font-weight: 600;
        color: #0B1437;
    }

    .admin-dashboard .payroll-val.val-green { color: #10B981; }
    .admin-dashboard .payroll-val.val-orange { color: #F97316; }
    .admin-dashboard .payroll-val.val-bold { font-weight: 700; }

    /* -------------------------------------------------
       TABLES STYLING
    ------------------------------------------------- */
    .admin-dashboard .table-scroll {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
    }

    .admin-dashboard .data-table {
        border-collapse: collapse;
        font-size: 13px;
        width: 100%;
        background: #FFFFFF;
    }

    .admin-dashboard .data-table th {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        color: #4B5563;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        padding: 12px 16px;
        text-align: left;
        text-transform: uppercase;
    }

    .admin-dashboard .data-table td {
        border-bottom: 1px solid #F3F4F6;
        color: #0B1437;
        padding: 14px 16px;
        vertical-align: middle;
    }

    .admin-dashboard .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .admin-dashboard .data-table tbody tr:hover td {
        background: #F5F6FA;
    }

    /* -------------------------------------------------
       BADGES & COMPONENT PILLS
    ------------------------------------------------- */
    .admin-dashboard .badge {
        border-radius: 20px;
        display: inline-flex;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        padding: 6px 12px;
        white-space: nowrap;
    }

    .admin-dashboard .badge-green { background: #ECFDF5; color: #047857; }
    .admin-dashboard .badge-amber { background: #FFFBEB; color: #B45309; }
    .admin-dashboard .badge-red { background: #FEF2F2; color: #B91C1C; }
    .admin-dashboard .badge-blue { background: #EFF6FF; color: #1D4ED8; }
    .admin-dashboard .badge-purple { background: #F5F3FF; color: #f6f0ff; }
    .admin-dashboard .badge-gray { background: #F3F4F6; color: #374151; }
    .admin-dashboard .badge-orange-light { background: #FFF7ED; color: #F97316; }

    .admin-dashboard .mini-card {
        padding: 18px !important;
    }

    .admin-dashboard .mini-value {
        color: #0B1437;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.1;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }

    .admin-dashboard .progress-track {
        background: #E5E7EB;
        border-radius: 4px;
        height: 6px;
        overflow: hidden;
        min-width: 96px;
    }

    .admin-dashboard .progress-fill {
        border-radius: 4px;
        height: 100%;
        transition: width .4s ease;
    }

    .admin-dashboard .empty-state {
        align-items: center;
        color: #8892B0;
        display: flex;
        justify-content: center;
        min-height: 96px;
        text-align: center;
        font-weight: 500;
    }

    .admin-dashboard .view-link {
        color: #F97316;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .admin-dashboard .view-link:hover {
        color: #EA580C;
        text-decoration: underline !important;
    }

    .admin-dashboard .review-avatar {
        align-items: center;
        background: #FFF7ED;
        border-radius: 50%;
        color: #F97316;
        display: inline-flex;
        flex: 0 0 auto;
        font-weight: 700;
        height: 38px;
        justify-content: center;
        width: 38px;
        border: 2px solid rgba(249, 115, 22, 0.1);
        font-size: 13px;
    }

    .admin-dashboard .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @keyframes fadeInUpDash {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1400px) {
        .admin-dashboard .grid-6 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .admin-dashboard .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 991px) {
        .admin-dashboard .grid-6,
        .admin-dashboard .grid-4,
        .admin-dashboard .grid-3,
        .admin-dashboard .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .admin-dashboard .donut-wrap {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 767px) {
        .admin-dashboard {
            display: block !important;
        }

        .admin-dashboard .dashboard-header {
            align-items: flex-start !important;
            flex-direction: column !important;
            gap: 10px !important;
            padding: 10px 12px !important;
            min-height: auto !important;
            width: 100% !important;
        }

        .admin-dashboard .dashboard-header > div:first-child {
            width: 100% !important;
            display: block !important;
        }

        .admin-dashboard .dashboard-header h1 {
            font-size: 15px !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
        }

        .admin-dashboard .header-status {
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
            font-size: 11px !important;
            display: flex !important;
        }

        .admin-dashboard .header-actions {
            width: 100% !important;
            display: flex !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
            margin-top: 8px !important;
        }

        .admin-dashboard .header-actions .btn-dashboard-primary,
        .admin-dashboard .header-actions .btn-dashboard-secondary {
            flex: 1 !important;
            min-width: 80px !important;
            text-align: center !important;
            padding: 8px 6px !important;
            font-size: 11px !important;
            border-radius: 6px !important;
            white-space: nowrap !important;
        }

        .admin-dashboard .dashboard-body {
            padding: 10px 8px !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .admin-dashboard .grid {
            gap: 12px !important;
        }

        .admin-dashboard .grid-6,
        .admin-dashboard .grid-4,
        .admin-dashboard .grid-3,
        .admin-dashboard .grid-2 {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        .admin-dashboard .section-title {
            font-size: 14px !important;
        }

        .admin-dashboard .section-eyebrow {
            font-size: 10px !important;
        }

        .admin-dashboard .section-head {
            flex-wrap: wrap !important;
            gap: 8px !important;
            align-items: flex-start !important;
        }

        .admin-dashboard .panel {
            padding: 12px !important;
            border-radius: 12px !important;
            width: 100% !important;
        }

        .admin-dashboard .panel-header {
            flex-wrap: wrap !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .admin-dashboard .stat-card {
            padding: 12px !important;
            min-height: 100px !important;
            width: 100% !important;
        }

        .admin-dashboard .stat-value {
            font-size: 18px !important;
        }

        .admin-dashboard .stat-label {
            font-size: 11px !important;
        }

        .admin-dashboard .stat-sub {
            font-size: 10px !important;
        }

        .admin-dashboard .stat-top {
            margin-bottom: 12px !important;
        }

        .admin-dashboard .stat-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 14px !important;
        }

        .admin-dashboard .chart-box {
            height: 140px !important;
            width: 100% !important;
        }

        .admin-dashboard .chart-box canvas {
            max-height: 140px !important;
            width: 100% !important;
        }

        .admin-dashboard .chart-box.short {
            height: 120px !important;
        }

        .admin-dashboard .chart-box.short canvas {
            max-height: 120px !important;
        }

        .admin-dashboard .chart-box.area {
            height: 130px !important;
        }

        .admin-dashboard .chart-box.area canvas {
            max-height: 130px !important;
        }

        .admin-dashboard .donut-wrap {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
            width: 100% !important;
        }

        .admin-dashboard .donut-container {
            min-height: 140px !important;
        }

        .admin-dashboard .donut-wrap .chart-box {
            height: 150px !important;
            max-height: 150px !important;
            width: 100% !important;
        }

        .admin-dashboard .donut-wrap .chart-box canvas {
            max-height: 150px !important;
            width: 100% !important;
        }

        .admin-dashboard .legend-list {
            gap: 8px !important;
            width: 100% !important;
        }

        .admin-dashboard .legend-item {
            padding: 6px 0 !important;
            font-size: 11px !important;
        }

        .admin-dashboard .mini-card {
            padding: 10px !important;
        }

        .admin-dashboard .mini-value {
            font-size: 16px !important;
        }

        .admin-dashboard .mini-label {
            font-size: 10px !important;
        }

        .admin-dashboard .table-scroll {
            font-size: 11px !important;
            overflow-x: auto !important;
        }

        .admin-dashboard .data-table th,
        .admin-dashboard .data-table td {
            padding: 8px 10px !important;
            font-size: 11px !important;
        }

        .admin-dashboard .section-block {
            margin-top: 16px !important;
        }

        .admin-dashboard .badge {
            font-size: 9px !important;
            padding: 3px 6px !important;
        }

        .admin-dashboard .pipeline-item {
            margin-bottom: 10px !important;
        }

        .admin-dashboard .review-avatar {
            width: 28px !important;
            height: 28px !important;
            font-size: 11px !important;
        }
    }

    @media (max-width: 480px) {
        .admin-dashboard {
            padding: 0 !important;
            background: #F5F6FA !important;
        }

        .admin-dashboard .dashboard-header {
            padding: 8px 10px !important;
            min-height: auto !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }

        .admin-dashboard .dashboard-header h1 {
            font-size: 13px !important;
            margin: 0 !important;
        }

        .admin-dashboard .header-status {
            font-size: 10px !important;
            gap: 4px !important;
        }

        .admin-dashboard .header-actions {
            width: 100% !important;
            gap: 4px !important;
            margin-top: 6px !important;
        }

        .admin-dashboard .header-actions .btn-dashboard-primary,
        .admin-dashboard .header-actions .btn-dashboard-secondary {
            padding: 6px 4px !important;
            font-size: 10px !important;
            flex: 1 !important;
        }

        .admin-dashboard .dashboard-body {
            padding: 8px 6px !important;
        }

        .admin-dashboard .grid,
        .admin-dashboard .grid-6,
        .admin-dashboard .grid-4,
        .admin-dashboard .grid-3,
        .admin-dashboard .grid-2 {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .admin-dashboard .section-title {
            font-size: 13px !important;
        }

        .admin-dashboard .section-eyebrow {
            font-size: 9px !important;
        }

        .admin-dashboard .panel {
            padding: 10px !important;
            border-radius: 10px !important;
        }

        .admin-dashboard .stat-card {
            padding: 10px !important;
            min-height: 90px !important;
        }

        .admin-dashboard .stat-value {
            font-size: 16px !important;
        }

        .admin-dashboard .stat-label {
            font-size: 10px !important;
        }

        .admin-dashboard .stat-sub {
            font-size: 9px !important;
        }

        .admin-dashboard .stat-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 12px !important;
        }

        .admin-dashboard .mini-value {
            font-size: 14px !important;
        }

        .admin-dashboard .mini-label {
            font-size: 9px !important;
        }

        .admin-dashboard .chart-box {
            height: 120px !important;
        }

        .admin-dashboard .chart-box canvas {
            max-height: 120px !important;
        }

        .admin-dashboard .chart-box.short {
            height: 100px !important;
        }

        .admin-dashboard .chart-box.short canvas {
            max-height: 100px !important;
        }

        .admin-dashboard .chart-box.area {
            height: 110px !important;
        }

        .admin-dashboard .chart-box.area canvas {
            max-height: 110px !important;
        }

        .admin-dashboard .donut-container {
            min-height: 120px !important;
        }

        .admin-dashboard .donut-number {
            font-size: 18px !important;
        }

        .admin-dashboard .donut-label {
            font-size: 9px !important;
        }

        .admin-dashboard .legend-dot {
            width: 6px !important;
            height: 6px !important;
        }

        .admin-dashboard .badge {
            font-size: 8px !important;
            padding: 2px 5px !important;
        }

        .admin-dashboard .table-scroll {
            font-size: 10px !important;
        }

        .admin-dashboard .data-table th,
        .admin-dashboard .data-table td {
            padding: 6px 8px !important;
        }

        .admin-dashboard .payroll-label {
            font-size: 11px !important;
        }

        .admin-dashboard .payroll-val {
            font-size: 12px !important;
        }

        .admin-dashboard .review-avatar {
            width: 24px !important;
            height: 24px !important;
            font-size: 10px !important;
        }

        .admin-dashboard .pipeline-name {
            font-size: 11px !important;
        }

        .admin-dashboard .pipeline-value {
            font-size: 11px !important;
        }

        .admin-dashboard .section-block {
            margin-top: 12px !important;
        }
    }
</style>

<div class="admin-dashboard">
    <!-- Action Bar / Header -->
    <div class="dashboard-header">
        <div>
            <h1>Welcome back, Admin 👋</h1>
            <div class="header-status">
                <span class="status-time" id="live-clock">11:13:51</span>
                <span class="status-live">
                    <span class="live-dot"></span> Live
                </span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.announcements.index') }}" class="btn-dashboard-secondary">📢 Internal Announcement</a>
            <button onclick="window.print()" class="btn-dashboard-secondary">Export</button>
        </div>
    </div>

    <div class="dashboard-body">
        @include('hrms.partials.dashboard-announcement-bar', [
            'announcementAccent' => '#F97316',
            'announcementAccentSoft' => 'rgba(249, 115, 22, 0.10)',
        ])

        {{-- ===== KPI SUMMARY ===== --}}
        <div class="grid grid-6">
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-users"></i></span><span class="badge badge-purple">Workforce</span></div>
                <p class="stat-value">{{ number_format($totalEmployees) }}</p>
                <p class="stat-label">Total Employees</p>
                <p class="stat-sub">+{{ number_format($newEmployees) }} new this week</p>
            </div>
            <div class="stat-card attendance-clickable" onclick="openAttendanceModal('present')" style="cursor:pointer;" title="Click to view present employees">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-check"></i></span><span class="badge badge-green">Today</span></div>
                <p class="stat-value">{{ number_format($presentToday) }}</p>
                <p class="stat-label">Present Today</p>
                <p class="stat-sub">{{ $punctualityRate }}% punctuality rate</p>
            </div>
            <div class="stat-card attendance-clickable" onclick="openAttendanceModal('absent')" style="cursor:pointer;" title="Click to view absent employees">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-user-times"></i></span><span class="badge badge-red">Today</span></div>
                <p class="stat-value">{{ number_format($absentToday) }}</p>
                <p class="stat-label">Absent Today</p>
                <p class="stat-sub">{{ number_format($todayLeave) }} on approved leave</p>
            </div>
            <div class="stat-card attendance-clickable" onclick="openAttendanceModal('late')" style="cursor:pointer;" title="Click to view late arrivals">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-clock"></i></span><span class="badge badge-amber">Exception</span></div>
                <p class="stat-value">{{ number_format($lateArrivalsToday) }}</p>
                <p class="stat-label">Late Arrivals</p>
                <p class="stat-sub">Early departures: {{ number_format($earlyDeparturesToday) }}</p>
            </div>
            <div class="stat-card">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-chart-bar"></i></span><span class="badge badge-orange-light">30 days</span></div>
                <p class="stat-value">{{ $attendanceRate }}%</p>
                <p class="stat-label">Attendance Rate</p>
                <p class="stat-sub">30-day rolling average</p>
            </div>
            <div class="stat-card attendance-clickable" onclick="openApprovalsModal()" style="cursor:pointer;" title="Click to manage pending approvals">
                <div class="stat-top"><span class="stat-icon"><i class="fa fa-list-check"></i></span><span class="badge badge-orange-light">Action</span></div>
                <p class="stat-value" id="dash-pending-approvals-count">{{ number_format($pendingSummary) }}</p>
                <p class="stat-label">Pending Approvals</p>
                <p class="stat-sub">Leaves, payroll, invoices, reviews</p>
            </div>
        </div>

        {{-- ===== WORKFORCE SUMMARY ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div>
                    <div class="section-eyebrow">Management Overview</div>
                    <h2 class="section-title">Workforce Summary</h2>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="grid grid-4">
                        <div class="mini-card"><p class="mini-value">{{ number_format($activeEmployees) }}</p><p class="mini-label">Active Employees</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($newJoinersThisMonth) }}</p><p class="mini-label">New Joiners This Month</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($employeesOnNoticePeriod) }}</p><p class="mini-label">Notice Period</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($employeesExitingThisMonth) }}</p><p class="mini-label">Exiting This Month</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <div class="section-eyebrow">Headcount</div>
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
                    <div class="section-eyebrow">Attendance Overview</div>
                    <h2 class="section-title">Today’s Movement</h2>
                </div>
                <span class="badge badge-blue">Attendance {{ $attendanceRate }}%</span>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Trend</div><h3 class="section-title">Weekly Attendance Trend</h3></div>
                    </div>
                    <div class="chart-box"><canvas id="weeklyAttendanceChart"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Breakdown</div><h3 class="section-title">Today’s Attendance Breakdown</h3></div>
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
                            @foreach([
                                ['Present', $presentToday, '#10B981'],
                                ['Absent', $absentToday, '#EF4444'],
                                ['Late', $lateArrivalsToday, '#F59E0B'],
                                ['On Leave', $todayLeave, '#3B82F6'],
                            ] as $item)
                                <div class="legend-item">
                                    <span class="legend-left"><span class="legend-dot" style="background: {{ $item[2] }}"></span>{{ $item[0] }}</span>
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
                            @php
                                $filterLabels = [
                                    'today' => 'Today’s',
                                    'yesterday' => 'Yesterday’s',
                                    'past7days' => 'Past 7 Days',
                                    'last1month' => 'Last 1 Month'
                                ];
                                $label = $filterLabels[$attendanceFilter] ?? 'Today’s';
                            @endphp
                            {{ $label }} Check-ins
                        </div>
                        <h3 class="section-title">Attendance Status</h3>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <form method="GET" action="{{ route('admin.dashboard') ?? url()->current() }}" id="attendanceFilterForm">
                            @foreach(request()->except('attendance_filter') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $k => $v)
                                        <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="attendance_filter" class="form-control" style="font-size: 13px; border-radius: 6px; border: 1px solid #E5E7EB; padding: 4px 8px; color: #4B5563; outline: none; cursor: pointer;" onchange="document.getElementById('attendanceFilterForm').submit()">
                                <option value="today" {{ $attendanceFilter === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ $attendanceFilter === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="past7days" {{ $attendanceFilter === 'past7days' ? 'selected' : '' }}>Past 7 Days</option>
                                <option value="last1month" {{ $attendanceFilter === 'last1month' ? 'selected' : '' }}>Last 1 Month</option>
                            </select>
                        </form>
                        <a class="view-link" href="{{ route('admin.attendance.daily') }}">View All &rarr;</a>
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
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span style="font-weight:600; color:#0B1437;">{{ $stat->total_punchins }} / {{ $stat->total_possible }}</span>
                                                <span class="badge badge-blue">{{ $stat->percentage }}%</span>
                                            </div>
                                            <div class="progress-track" style="margin-top: 6px; height: 4px;">
                                                <div class="progress-fill" style="width: {{ $stat->percentage }}%; background: #3B82F6;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($stat->early_bird_list->count() > 0)
                                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                                    @foreach($stat->early_bird_list->take(5) as $eb)
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <span style="font-size: 12px; color: #4B5563;">{{ $eb->firstname }} {{ $eb->lastname }}</span>
                                                            <span style="font-size: 11px; color: #8892B0;">
                                                                @if($daysDiff > 1)
                                                                    ({{ $eb->count }} times)
                                                                @else
                                                                    ({{ $eb->latest_time }})
                                                                @endif
                                                            </span>
                                                            <span class="badge badge-green" style="font-size: 9px; padding: 2px 6px;">Early Bird</span>
                                                        </div>
                                                    @endforeach
                                                    @if($stat->early_bird_list->count() > 5)
                                                        <div style="font-size: 11px; color: #8892B0; margin-top: 2px;">+ {{ $stat->early_bird_list->count() - 5 }} more</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span style="font-size: 12px; color: #8892B0;">No early birds</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No attendance records found for selected period.</div>
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
                <div><div class="section-eyebrow">Operations</div><h2 class="section-title">Leave, Recruitment & Payroll</h2></div>
            </div>
            <div class="grid grid-3">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Leave Management</div><h3 class="section-title">Requests & Trends</h3></div>
                    </div>
                    <div class="grid grid-3" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value" style="color:#F97316;">{{ number_format($pendingLeaveRequests) }}</p><p class="mini-label">Pending</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ number_format($approvedLeaves) }}</p><p class="mini-label">Approved</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ number_format($rejectedLeaves) }}</p><p class="mini-label">Rejected</p></div>
                    </div>
                    <div class="chart-box short"><canvas id="leaveTrendChart"></canvas></div>
                </div>
                
                {{-- RECRUITMENT PIPELINE LIST WITH PROGRESS BARS --}}
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Recruitment</div><h3 class="section-title">Hiring Pipeline</h3></div>
                    </div>
                    <div class="pipeline-list">
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap">
                                <span class="pipeline-dot" style="background: #F97316;"></span>
                                <span class="pipeline-name">Open Positions</span>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track">
                                    <div class="pipeline-bar-fill" style="width: 100%; background: #F97316;"></div>
                                </div>
                                <span class="pipeline-value">{{ number_format($openPositions) }}</span>
                            </div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap">
                                <span class="pipeline-dot" style="background: #3B82F6;"></span>
                                <span class="pipeline-name">Applied</span>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track">
                                    <div class="pipeline-bar-fill" style="width: {{ $candidatesApplied > 0 ? min(($candidatesApplied/($openPositions ?: 1))*100, 100) : 0 }}%; background: #3B82F6;"></div>
                                </div>
                                <span class="pipeline-value">{{ number_format($candidatesApplied) }}</span>
                            </div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap">
                                <span class="pipeline-dot" style="background: #F59E0B;"></span>
                                <span class="pipeline-name">Interviews</span>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track">
                                    <div class="pipeline-bar-fill" style="width: {{ $interviewsScheduled > 0 ? min(($interviewsScheduled/($openPositions ?: 1))*100, 100) : 0 }}%; background: #F59E0B;"></div>
                                </div>
                                <span class="pipeline-value">{{ number_format($interviewsScheduled) }}</span>
                            </div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap">
                                <span class="pipeline-dot" style="background: #10B981;"></span>
                                <span class="pipeline-name">Offers Released</span>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track">
                                    <div class="pipeline-bar-fill" style="width: {{ $offersReleased > 0 ? min(($offersReleased/($openPositions ?: 1))*100, 100) : 0 }}%; background: #10B981;"></div>
                                </div>
                                <span class="pipeline-value">{{ number_format($offersReleased) }}</span>
                            </div>
                        </div>
                        <div class="pipeline-item">
                            <div class="pipeline-label-wrap">
                                <span class="pipeline-dot" style="background: #EF4444;"></span>
                                <span class="pipeline-name">Acceptance Rate</span>
                            </div>
                            <div class="pipeline-bar-wrap">
                                <div class="pipeline-bar-track">
                                    <div class="pipeline-bar-fill" style="width: {{ $offerAcceptanceRate }}%; background: #EF4444;"></div>
                                </div>
                                <span class="pipeline-value">{{ $offerAcceptanceRate }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PAYROLL HEALTH KEY-VALUE LIST --}}
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Payroll</div><h3 class="section-title">Payroll Health</h3></div>
                        <span class="badge badge-orange-light">Pending Approval</span>
                    </div>
                    <div class="payroll-rows">
                        <div class="payroll-row">
                            <span class="payroll-label">Salary Processed</span>
                            <span class="payroll-val val-green">₹{{ number_format($totalSalaryProcessed) }}</span>
                        </div>
                        <div class="payroll-row">
                            <span class="payroll-label">Statutory Deductions</span>
                            <span class="payroll-val val-orange">₹{{ number_format($statutoryDeductions) }}</span>
                        </div>
                        <div class="payroll-row">
                            <span class="payroll-label">Salary Approvals</span>
                            <span class="payroll-val val-bold">{{ number_format($pendingSalaryApprovals) }} pending</span>
                        </div>
                        <div class="payroll-row">
                            <span class="payroll-label">Next Payroll</span>
                            <span class="payroll-val val-orange">{{ $upcomingPayrollDate }}</span>
                        </div>
                    </div>
                    <div class="chart-box short"><canvas id="payrollDepartmentChart"></canvas></div>
                </div>
            </div>

            <div class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <div><div class="section-eyebrow">Leave & Holiday Section</div><h3 class="section-title">Holiday Snapshot</h3></div>
                    <span class="badge badge-purple">{{ now()->format('F') }}</span>
                </div>
                <div class="grid grid-4">
                    <div class="mini-card"><p class="mini-value">{{ number_format($holidaysThisMonth) }}</p><p class="mini-label">Holidays This Month</p></div>
                    <div class="mini-card"><p class="mini-value">{{ $nextHoliday->holiday ?? 'None' }}</p><p class="mini-label">{{ $daysUntilNextHoliday !== null ? $daysUntilNextHoliday . ' days away' : 'No upcoming' }}</p></div>
                    <div class="mini-card"><p class="mini-value">{{ $nextHoliday && $nextHoliday->holidaydate ? Carbon::parse($nextHoliday->holidaydate)->format('d M Y') : 'N/A' }}</p><p class="mini-label">Next Holiday Date</p></div>
                    <div class="mini-card"><p class="mini-value">{{ number_format($todayLeave) }}</p><p class="mini-label">On Leave Today</p></div>
                </div>
                <div class="chart-box short" style="margin-top:20px;"><canvas id="holidayMonthlyChart"></canvas></div>
            </div>
        </section>

        {{-- ===== PERFORMANCE ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Performance Dashboard</div><h2 class="section-title">Goals, Reviews & Promotions</h2></div>
            </div>
            <div class="grid grid-2">
                <div class="panel">
                    <div class="panel-header">
                        <div><div class="section-eyebrow">Goal Tracks</div><h3 class="section-title">Goal Completion Overview</h3></div>
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
                                        @php
                                            $progressColor = $goal->progress >= 100 ? '#10B981' : ($goal->progress >= 50 ? '#3B82F6' : ($goal->progress > 0 ? '#F59E0B' : '#8892B0'));
                                        @endphp
                                        <tr>
                                            <td>{{ $goal->subject }}</td>
                                            <td>{{ $goal->goal_type ?? 'General' }}</td>
                                            <td><div class="progress-track"><div class="progress-fill" style="width: {{ $goal->progress }}%; background: {{ $progressColor }}"></div></div></td>
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
                        <div><div class="section-eyebrow">Reviews & Indicators</div><h3 class="section-title">Performance Reviews</h3></div>
                        <span class="badge badge-amber">{{ number_format($pendingPerformanceReviews) }} pending</span>
                    </div>
                    <div class="grid grid-4" style="margin-bottom:16px;">
                        <div class="mini-card"><p class="mini-value">{{ number_format($totalPerformanceReviews) }}</p><p class="mini-label">Total Reviews</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($performanceReviewsThisMonth) }}</p><p class="mini-label">This Month</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($performanceReviewsThisYear) }}</p><p class="mini-label">This Year</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#F97316;">{{ $kpiAchievement }}%</p><p class="mini-label">KPI Achievement</p></div>
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
                    <div><div class="section-eyebrow">Promotion Section</div><h3 class="section-title">Promotion Management</h3></div>
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

        {{-- ===== ASSETS, ANALYTICS, PROJECTS ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Enterprise Controls</div><h2 class="section-title">Assets, HR Analytics & Delivery</h2></div>
            </div>
            <div class="grid grid-3">
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Asset Management</div><h3 class="section-title">Asset Status</h3></div></div>
                    <div class="grid grid-3">
                        <div class="mini-card"><p class="mini-value" style="color:#F97316;">{{ number_format($assetsAssigned) }}</p><p class="mini-label">Assigned</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#F59E0B;">{{ number_format($assetsDueForReturn) }}</p><p class="mini-label">Due Return</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#EF4444;">{{ number_format($lostDamagedAssets) }}</p><p class="mini-label">Lost/Damaged</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">HR Analytics</div><h3 class="section-title">Risk & Growth</h3></div></div>
                    <div class="grid grid-2">
                        <div class="mini-card"><p class="mini-value">{{ $employeeTurnoverRate }}%</p><p class="mini-label">Turnover Rate</p></div>
                        <div class="mini-card"><p class="mini-value">{{ $attritionRate }}%</p><p class="mini-label">Attrition Rate</p></div>
                        <div class="mini-card"><p class="mini-value">{{ $absenteeismRate }}%</p><p class="mini-label">Absenteeism Rate</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#10B981;">{{ $headcountGrowth }}%</p><p class="mini-label">Headcount Growth</p></div>
                        <div class="mini-card" style="grid-column: span 2;"><p class="mini-value">₹{{ number_format($costPerHire, 2) }}</p><p class="mini-label">Cost Per Hire</p></div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Projects & Clients</div><h3 class="section-title">Delivery Snapshot</h3></div></div>
                    <div class="grid grid-3">
                        <div class="mini-card"><p class="mini-value">{{ number_format($totalProjects) }}</p><p class="mini-label">Projects</p></div>
                        <div class="mini-card"><p class="mini-value" style="color:#F97316;">{{ number_format($totalTasksCount) }}</p><p class="mini-label">{{ number_format($completedTasks) }} done / {{ number_format($pendingTasks) }} pending</p></div>
                        <div class="mini-card"><p class="mini-value">{{ number_format($totalClients) }}</p><p class="mini-label">Clients</p></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-2" style="margin-top:20px;">
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Latest Projects</div><h3 class="section-title">Project Progress</h3></div></div>
                    @if($latestProjects->count())
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead><tr><th>Project</th><th>Total</th><th>Completed</th><th>Pending</th><th>Progress</th><th>Created</th></tr></thead>
                                <tbody>
                                    @foreach($latestProjects as $project)
                                        @php
                                            $progress = $project->total_tasks > 0 ? round(($project->completed_tasks / $project->total_tasks) * 100) : 0;
                                            $progressColor = $progress > 75 ? '#10B981' : ($progress > 40 ? '#3B82F6' : '#F59E0B');
                                        @endphp
                                        <tr>
                                            <td>{{ $project->projectname }}</td>
                                            <td>{{ number_format($project->total_tasks) }}</td>
                                            <td>{{ number_format($project->completed_tasks) }}</td>
                                            <td>{{ number_format($project->pending_tasks) }}</td>
                                            <td><div class="progress-track"><div class="progress-fill" style="width: {{ $progress }}%; background: {{ $progressColor }}"></div></div></td>
                                            <td>{{ $project->created_at ? Carbon::parse($project->created_at)->format('d M Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">No project records found.</div>
                    @endif
                </div>
                <div class="panel">
                    <div class="panel-header"><div><div class="section-eyebrow">Latest Invoices</div><h3 class="section-title">Receivables</h3></div></div>
                    @if($latestInvoices->count())
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead><tr><th>Client</th><th>Due Date</th><th>Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach($latestInvoices as $invoice)
                                        @php
                                            $invoiceStatus = strtolower($invoice->status ?? 'pending');
                                            $statusClass = $invoiceStatus === 'paid' ? 'badge-green' : ($invoiceStatus === 'overdue' ? 'badge-red' : 'badge-amber');
                                        @endphp
                                        <tr>
                                            <td>{{ $invoice->client_name ?? 'N/A' }}</td>
                                            <td>{{ $invoice->due_date ? Carbon::parse($invoice->due_date)->format('d M Y') : 'N/A' }}</td>
                                            <td>₹{{ number_format($invoice->grant_amt, 2) }}</td>
                                            <td><span class="badge {{ $statusClass }}">{{ ucfirst($invoiceStatus) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">No invoice records found.</div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ===== NOTIFICATIONS ===== --}}
        <section class="section-block">
            <div class="section-head">
                <div><div class="section-eyebrow">Notifications</div><h2 class="section-title">Alerts & Approvals</h2></div>
            </div>
            <div class="panel">
                <div class="alert-list">
                    @foreach([
                        ['Pending Leave Requests', $pendingLeaveRequests . ' requests awaiting approval', $pendingLeaveRequests, '#F59E0B'],
                        ['Performance Reviews Pending', $pendingPerformanceReviews . ' reviews', $pendingPerformanceReviews, '#3B82F6'],
                        ['Overdue Goals', $overdueGoals . ' goals past deadline', $overdueGoals, '#EF4444'],
                        ['Next Holiday', ($nextHoliday->holiday ?? 'No upcoming') . ($daysUntilNextHoliday !== null ? ' - ' . $daysUntilNextHoliday . ' days' : ''), $holidaysThisMonth, '#10B981'],
                        ['New Employees', $newEmployees . ' joined in last 7 days', $newEmployees, '#10B981'],
                        ['Birthdays', $birthdaysThisMonth . ' birthdays this month', $birthdaysThisMonth, '#8B5CF6'],
                        ['Work Anniversaries', $workAnniversariesThisMonth . ' anniversaries this month', $workAnniversariesThisMonth, '#F97316'],
                        ['Policy Updates', $policyUpdates . ' policy records', $policyUpdates, '#4B5563'],
                    ] as $alert)
                        <div class="alert-item">
                            <div class="alert-left">
                                <span class="alert-dot" style="background: {{ $alert[3] }}"></span>
                                <div>
                                    <strong style="color: #0B1437;">{{ $alert[0] }}</strong>
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

{{-- ===== PENDING APPROVALS MODAL ===== --}}
<div id="approvalsModal" style="display:none; position:fixed; inset:0; background:rgba(11,20,55,0.55); z-index:10000; overflow-y:auto; padding:20px 12px;">
    <div style="background:#fff; border-radius:18px; max-width:940px; margin:32px auto; padding:28px 32px; position:relative; box-shadow:0 24px 64px rgba(11,20,55,0.18); font-family:'DM Sans',sans-serif;">

        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; gap:12px;">
            <div>
                <p style="font-size:11px; font-weight:700; color:#8892B0; text-transform:uppercase; letter-spacing:.1em; margin:0 0 5px;">Dashboard · Action Required</p>
                <h2 style="font-size:20px; font-weight:700; color:#0B1437; margin:0; letter-spacing:-0.3px;">Pending Approvals</h2>
            </div>
            <button onclick="closeApprovalsModal()" style="flex-shrink:0; background:#F3F4F6; border:none; cursor:pointer; color:#4B5563; font-size:20px; line-height:1; padding:6px 12px; border-radius:8px; font-weight:700;">&times;</button>
        </div>

        {{-- Tabs --}}
        <div class="apr-tabs" style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
            <button class="apr-tab apr-tab-active" onclick="switchAprTab('leaves',this)" id="tab-btn-leaves">
                <span>Leaves</span>
                <span class="apr-tab-count" id="tab-count-leaves" style="background:#F97316; color:#fff;">{{ $pendingLeavesList->count() }}</span>
            </button>
            <button class="apr-tab" onclick="switchAprTab('permissions',this)" id="tab-btn-permissions">
                <span>Permissions</span>
                <span class="apr-tab-count" id="tab-count-permissions" style="background:#3B82F6; color:#fff;">{{ $pendingPermissionsList->count() }}</span>
            </button>
            <button class="apr-tab" onclick="switchAprTab('payroll',this)" id="tab-btn-payroll">
                <span>Payroll</span>
                <span class="apr-tab-count" id="tab-count-payroll" style="background:#8B5CF6; color:#fff;">{{ $pendingSalariesList->count() }}</span>
            </button>
            <button class="apr-tab" onclick="switchAprTab('invoices',this)" id="tab-btn-invoices">
                <span>Invoices</span>
                <span class="apr-tab-count" id="tab-count-invoices" style="background:#EF4444; color:#fff;">{{ $pendingInvoicesList->count() }}</span>
            </button>
        </div>

        {{-- Tab Panels --}}
        <div id="apr-panel-leaves" class="apr-panel">
            @if($pendingLeavesList->count())
                <div style="overflow-x:auto; border-radius:12px; border:1px solid #E5E7EB;">
                    <table class="apr-table">
                        <thead><tr><th>#</th><th>Employee</th><th>Department</th><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th style="text-align:center;">Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingLeavesList as $i => $leave)
                            <tr data-approval-row="leave-{{ $leave->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $leave->firstname }} {{ $leave->lastname }}</strong></td>
                                <td>{{ $leave->department }}</td>
                                <td><span class="apr-pill" style="background:#FFF7ED; color:#F97316;">{{ $leave->leave_type }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</td>
                                <td style="text-align:center;">{{ $leave->no_of_days }}</td>
                                <td style="max-width:160px; color:#8892B0; font-size:12px;">{{ \Illuminate\Support\Str::limit($leave->leave_reason ?? '—', 40) }}</td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <button class="apr-btn-approve" onclick="handleDashApproval('leave',{{ $leave->id }},'approved')">✓ Approve</button>
                                    <button class="apr-btn-decline" onclick="handleDashApproval('leave',{{ $leave->id }},'declined')">✗ Decline</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="apr-empty">No pending leave requests.</div>
            @endif
        </div>

        <div id="apr-panel-permissions" class="apr-panel" style="display:none;">
            @if($pendingPermissionsList->count())
                <div style="overflow-x:auto; border-radius:12px; border:1px solid #E5E7EB;">
                    <table class="apr-table">
                        <thead><tr><th>#</th><th>Employee</th><th>Department</th><th>Date</th><th>From</th><th>To</th><th>Duration</th><th>Reason</th><th style="text-align:center;">Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingPermissionsList as $i => $perm)
                            <tr data-approval-row="permission-{{ $perm->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $perm->firstname }} {{ $perm->lastname }}</strong></td>
                                <td>{{ $perm->department }}</td>
                                <td>{{ \Carbon\Carbon::parse($perm->permission_date)->format('d M Y') }}</td>
                                <td>{{ $perm->start_time ? \Carbon\Carbon::parse($perm->start_time)->format('h:i A') : '—' }}</td>
                                <td>{{ $perm->end_time ? \Carbon\Carbon::parse($perm->end_time)->format('h:i A') : '—' }}</td>
                                <td style="text-align:center;"><span class="apr-pill" style="background:#EFF6FF; color:#1D4ED8;">{{ $perm->duration ?? 0 }} hr</span></td>
                                <td style="max-width:160px; color:#8892B0; font-size:12px;">{{ \Illuminate\Support\Str::limit($perm->permission_reason ?? '—', 40) }}</td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <button class="apr-btn-approve" onclick="handleDashApproval('permission',{{ $perm->id }},'approved')">✓ Approve</button>
                                    <button class="apr-btn-decline" onclick="handleDashApproval('permission',{{ $perm->id }},'declined')">✗ Decline</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="apr-empty">No pending permission requests.</div>
            @endif
        </div>

        <div id="apr-panel-payroll" class="apr-panel" style="display:none;">
            @if($pendingSalariesList->count())
                <div style="overflow-x:auto; border-radius:12px; border:1px solid #E5E7EB;">
                    <table class="apr-table">
                        <thead><tr><th>#</th><th>Employee</th><th>Department</th><th>Month</th><th>Net Salary</th><th style="text-align:center;">Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingSalariesList as $i => $sal)
                            <tr data-approval-row="salary-{{ $sal->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $sal->firstname }} {{ $sal->lastname }}</strong></td>
                                <td>{{ $sal->department }}</td>
                                <td>{{ $sal->created_at ? \Carbon\Carbon::parse($sal->created_at)->format('M Y') : '—' }}</td>
                                <td><strong style="color:#10B981;">₹{{ number_format($sal->net_salary, 2) }}</strong></td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <button class="apr-btn-approve" onclick="handleDashApproval('salary',{{ $sal->id }},'approved')">✓ Approve</button>
                                    <button class="apr-btn-decline" style="background:#FFFBEB; color:#B45309; border-color:#F59E0B;" onclick="handleDashApproval('salary',{{ $sal->id }},'hold')">⏸ Hold</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="apr-empty">No pending salary approvals.</div>
            @endif
        </div>

        <div id="apr-panel-invoices" class="apr-panel" style="display:none;">
            @if($pendingInvoicesList->count())
                <div style="overflow-x:auto; border-radius:12px; border:1px solid #E5E7EB;">
                    <table class="apr-table">
                        <thead><tr><th>#</th><th>Invoice ID</th><th>Client</th><th>Due Date</th><th>Amount</th><th style="text-align:center;">Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingInvoicesList as $i => $inv)
                            <tr data-approval-row="invoice-{{ $inv->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td><span class="apr-pill" style="background:#F5F3FF; color:#7C3AED;">{{ $inv->invoice_id }}</span></td>
                                <td><strong>{{ $inv->client_name ?? '—' }}</strong></td>
                                <td>{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : '—' }}</td>
                                <td><strong style="color:#0B1437;">₹{{ number_format($inv->grant_amt, 2) }}</strong></td>
                                <td style="text-align:center; white-space:nowrap;">
                                    <button class="apr-btn-approve" onclick="handleDashApproval('invoice',{{ $inv->id }},'approved','{{ $inv->invoice_id }}')">✓ Approve</button>
                                    <button class="apr-btn-decline" onclick="handleDashApproval('invoice',{{ $inv->id }},'rejected','{{ $inv->invoice_id }}')">✗ Reject</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="apr-empty">No pending invoices.</div>
            @endif
        </div>

    </div>
</div>

<style>
    .apr-tabs { border-bottom: 1px solid #E5E7EB; padding-bottom: 0; }
    .apr-tab {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px; border: 1px solid #E5E7EB; border-bottom: none;
        border-radius: 10px 10px 0 0; background: #F9FAFB; cursor: pointer;
        font-size: 13px; font-weight: 600; color: #4B5563;
        font-family: 'DM Sans', sans-serif; transition: all 0.15s ease;
        margin-bottom: -1px;
    }
    .apr-tab:hover { background: #F3F4F6; }
    .apr-tab-active { background: #fff !important; border-bottom-color: #fff !important; color: #0B1437 !important; }
    .apr-tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 20px; height: 20px; padding: 0 6px;
        border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .apr-panel { animation: fadeInUpDash 0.25s ease both; }
    .apr-table { width: 100%; border-collapse: collapse; font-size: 13px; background: #fff; }
    .apr-table th { background: #F9FAFB; border-bottom: 1px solid #E5E7EB; color: #4B5563; font-size: 11px; font-weight: 700; letter-spacing: .05em; padding: 11px 14px; text-align: left; text-transform: uppercase; white-space: nowrap; }
    .apr-table td { border-bottom: 1px solid #F3F4F6; color: #0B1437; padding: 12px 14px; vertical-align: middle; }
    .apr-table tbody tr:last-child td { border-bottom: none; }
    .apr-table tbody tr:hover td { background: #F5F6FA; }
    .apr-btn-approve {
        background: #ECFDF5; color: #047857; border: 1px solid #6EE7B7;
        padding: 5px 11px; border-radius: 7px; font-size: 12px; font-weight: 600;
        cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.15s;
        margin-right: 4px;
    }
    .apr-btn-approve:hover { background: #D1FAE5; }
    .apr-btn-decline {
        background: #FEF2F2; color: #B91C1C; border: 1px solid #FCA5A5;
        padding: 5px 11px; border-radius: 7px; font-size: 12px; font-weight: 600;
        cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.15s;
    }
    .apr-btn-decline:hover { background: #FEE2E2; }
    .apr-pill { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .apr-empty { text-align: center; padding: 48px 24px; color: #8892B0; font-size: 14px; font-weight: 500; }
</style>

{{-- ===== ATTENDANCE DETAIL MODAL ===== --}}
<div id="attendanceDetailModal" style="display:none; position:fixed; inset:0; background:rgba(11,20,55,0.55); z-index:10000; overflow-y:auto; padding:20px 12px;">
    <div style="background:#fff; border-radius:18px; max-width:780px; margin:40px auto; padding:32px; position:relative; box-shadow:0 24px 64px rgba(11,20,55,0.18); font-family:'DM Sans',sans-serif;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; gap:12px;">
            <div>
                <p id="adModalEyebrow" style="font-size:11px; font-weight:700; color:#8892B0; text-transform:uppercase; letter-spacing:.1em; margin:0 0 6px;"></p>
                <h2 id="adModalTitle" style="font-size:20px; font-weight:700; color:#0B1437; margin:0; letter-spacing:-0.3px;"></h2>
            </div>
            <button onclick="closeAttendanceModal()" style="flex-shrink:0; background:#F3F4F6; border:none; cursor:pointer; color:#4B5563; font-size:20px; line-height:1; padding:6px 12px; border-radius:8px; font-weight:700;" title="Close">&times;</button>
        </div>
        <div id="adModalContent"></div>
    </div>
</div>

<style>
    .attendance-clickable:hover { outline: 2px solid rgba(249,115,22,0.3); }
    #attendanceDetailModal table th { background:#F9FAFB; border-bottom:1px solid #E5E7EB; color:#4B5563; font-size:11px; font-weight:700; letter-spacing:.05em; padding:11px 14px; text-align:left; text-transform:uppercase; white-space:nowrap; }
    #attendanceDetailModal table td { border-bottom:1px solid #F3F4F6; color:#0B1437; padding:13px 14px; vertical-align:middle; font-size:13px; }
    #attendanceDetailModal table tbody tr:last-child td { border-bottom:none; }
    #attendanceDetailModal table tbody tr:hover td { background:#F5F6FA; }
    #attendanceDetailModal .ad-badge { border-radius:20px; display:inline-flex; font-size:11px; font-weight:700; padding:4px 10px; white-space:nowrap; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        // Live Clock logic
        function updateClock() {
            const now = new Date();
            const timeString = now.toTimeString().split(' ')[0];
            const clockEl = document.getElementById('live-clock');
            if (clockEl) clockEl.textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // -------------------------------------------------
        // Counter Animation
        // -------------------------------------------------
        document.querySelectorAll('.stat-value, .mini-value').forEach(el => {
            const text = el.textContent.trim();
            const match = text.match(/[\d,.]+/);
            if (!match) return;
            const originalNumStr = match[0];
            const target = parseFloat(originalNumStr.replace(/,/g, ''));
            if (isNaN(target)) return;
            
            const prefix = text.split(originalNumStr)[0] || '';
            const suffix = text.split(originalNumStr)[1] || '';
            
            const isDecimal = originalNumStr.includes('.');
            
            let start = 0;
            const duration = 1200;
            const startTime = performance.now();
            
            function run(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = progress * (2 - progress); // easeOutQuad
                
                const current = start + ease * (target - start);
                
                let formattedVal = '';
                if (isDecimal) {
                    formattedVal = current.toFixed(1);
                } else {
                    formattedVal = Math.floor(current).toLocaleString();
                }
                
                el.textContent = prefix + formattedVal + suffix;
                
                if (progress < 1) {
                    requestAnimationFrame(run);
                } else {
                    el.textContent = text;
                }
            }
            requestAnimationFrame(run);
        });

        // -------------------------------------------------
        // CHART.JS DEFAULT CONFIGURATIONS
        // -------------------------------------------------
        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#8892B0';
        Chart.defaults.plugins.legend.display = false;

        // Modern Navy Tooltips
        Chart.defaults.plugins.tooltip = {
            enabled: true,
            backgroundColor: '#0B1437',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            titleFont: { family: "'DM Sans', sans-serif", weight: 'bold', size: 12 },
            bodyFont: { family: "'DM Sans', sans-serif", size: 12 },
            padding: 10,
            cornerRadius: 8,
            displayColors: false
        };

        const gridColor = 'rgba(15, 23, 42, 0.04)';
        const tickColor = '#8892B0';
        const baseScales = {
            y: { beginAtZero: true, grid: { color: gridColor, lineWidth: 1 }, ticks: { color: tickColor, font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 11 } } }
        };

        // 1. Department Count Chart (Horizontal)
        const deptCanvas = document.getElementById('departmentCountChart');
        if (deptCanvas) {
            const ctx = deptCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 400, 0);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#FED7AA');
            new Chart(deptCanvas, {
                type: 'bar',
                data: {
                    labels: @json($deptLabels),
                    datasets: [{
                        data: @json($deptCounts),
                        backgroundColor: grad,
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#8892B0' } },
                        y: { grid: { color: gridColor }, ticks: { color: '#8892B0' } }
                    }
                }
            });
        }

        // 2. Holiday Monthly Chart (Vertical Bar)
        const holidayCanvas = document.getElementById('holidayMonthlyChart');
        if (holidayCanvas) {
            const ctx = holidayCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#FED7AA');
            new Chart(holidayCanvas, {
                type: 'bar',
                data: {
                    labels: @json($holidayMonthlyLabels),
                    datasets: [{
                        data: @json($holidayMonthlyData),
                        backgroundColor: grad,
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 3. Leave Trend Chart (Line Chart with Gradient Area)
        const leaveCanvas = document.getElementById('leaveTrendChart');
        if (leaveCanvas) {
            const ctx = leaveCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, 'rgba(249, 115, 22, 0.2)');
            grad.addColorStop(1, 'rgba(249, 115, 22, 0.0)');
            new Chart(leaveCanvas, {
                type: 'line',
                data: {
                    labels: @json($leaveTrendLabels),
                    datasets: [{
                        data: @json($leaveTrendData),
                        borderColor: '#F97316',
                        backgroundColor: grad,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 4. Payroll Department Chart
        const payrollCanvas = document.getElementById('payrollDepartmentChart');
        if (payrollCanvas) {
            const ctx = payrollCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#FED7AA');
            new Chart(payrollCanvas, {
                type: 'bar',
                data: {
                    labels: @json($payrollDeptLabels),
                    datasets: [{
                        data: @json($payrollDeptAmounts),
                        backgroundColor: grad,
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 5. Weekly Attendance Trend
        const weeklyCanvas = document.getElementById('weeklyAttendanceChart');
        if (weeklyCanvas) {
            const ctx = weeklyCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 200);
            grad.addColorStop(0, 'rgba(249, 115, 22, 0.2)');
            grad.addColorStop(1, 'rgba(249, 115, 22, 0.0)');
            new Chart(weeklyCanvas, {
                type: 'line',
                data: {
                    labels: @json($weeklyAttendanceLabels),
                    datasets: [{
                        data: @json($weeklyAttendanceData),
                        borderColor: '#F97316',
                        backgroundColor: grad,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 6. Monthly Attendance Chart
        const monthlyCanvas = document.getElementById('monthlyAttendanceChart');
        if (monthlyCanvas) {
            const ctx = monthlyCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 200);
            grad.addColorStop(0, 'rgba(249, 115, 22, 0.2)');
            grad.addColorStop(1, 'rgba(249, 115, 22, 0.0)');
            new Chart(monthlyCanvas, {
                type: 'line',
                data: {
                    labels: @json($monthlyAttendanceLabels),
                    datasets: [{
                        data: @json($monthlyAttendanceData),
                        borderColor: '#F97316',
                        backgroundColor: grad,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 11 } } },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { stepSize: 20, color: tickColor, font: { size: 11 }, callback: value => value + '%' },
                            grid: { color: gridColor, lineWidth: 1 }
                        }
                    }
                }
            });
        }

        // 7. Performance Review Trend Chart
        const reviewCanvas = document.getElementById('performanceReviewTrend');
        if (reviewCanvas) {
            const ctx = reviewCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 180);
            grad.addColorStop(0, 'rgba(249, 115, 22, 0.15)');
            grad.addColorStop(1, 'rgba(249, 115, 22, 0.0)');
            new Chart(reviewCanvas, {
                type: 'line',
                data: {
                    labels: @json($performanceReviewTrendLabels),
                    datasets: [{
                        data: @json($performanceReviewTrendData),
                        borderColor: '#F97316',
                        backgroundColor: grad,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 8. Promotion Trend Chart
        const promotionCanvas = document.getElementById('promotionTrendChart');
        if (promotionCanvas) {
            const ctx = promotionCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 200);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#FED7AA');
            new Chart(promotionCanvas, {
                type: 'bar',
                data: {
                    labels: @json($promotionTrendLabels),
                    datasets: [{
                        data: @json($promotionTrendData),
                        backgroundColor: grad,
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // 9. Today's Attendance Donut
        const donutCanvas = document.getElementById('todayAttendanceDonut');
        if (donutCanvas) {
            new Chart(donutCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late', 'On Leave'],
                    datasets: [{
                        data: [{{ $presentToday }}, {{ $absentToday }}, {{ $lateArrivalsToday }}, {{ $todayLeave }}],
                        backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#3B82F6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 10. Goal Progress Donut
        const goalCanvas = document.getElementById('goalProgressDonut');
        if (goalCanvas) {
            new Chart(goalCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Not Started', 'Overdue'],
                    datasets: [{
                        data: [{{ $completedGoals }}, {{ $inProgressGoals }}, {{ $notStartedGoals }}, {{ $overdueGoals }}],
                        backgroundColor: ['#10B981', '#3B82F6', '#8892B0', '#EF4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 11. Promotions By Department (Horizontal Bar)
        const promotionsByDept = document.getElementById('promotionsByDeptChart');
        if (promotionsByDept) {
            const ctx = promotionsByDept.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 400, 0);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#FED7AA');
            new Chart(promotionsByDept, {
                type: 'bar',
                data: {
                    labels: @json($promotionDeptLabels),
                    datasets: [{
                        data: @json($promotionDeptCounts),
                        backgroundColor: grad,
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 11 } } },
                        y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 11 } } }
                    }
                }
            });
        }
    });
</script>
<script>
// ===== APPROVALS MODAL =====
(function () {
    var _csrfToken = '{{ csrf_token() }}';
    var _activeAprTab = 'leaves';

    window.openApprovalsModal = function () {
        document.getElementById('approvalsModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    };

    window.closeApprovalsModal = function () {
        document.getElementById('approvalsModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    window.switchAprTab = function (tab, btn) {
        _activeAprTab = tab;
        document.querySelectorAll('.apr-panel').forEach(function (p) { p.style.display = 'none'; });
        document.querySelectorAll('.apr-tab').forEach(function (b) { b.classList.remove('apr-tab-active'); });
        document.getElementById('apr-panel-' + tab).style.display = 'block';
        btn.classList.add('apr-tab-active');
    };

    window.handleDashApproval = function (type, id, action, invoiceStrId) {
        var rowEl = document.querySelector('[data-approval-row="' + type + '-' + id + '"]');
        if (rowEl) { rowEl.style.opacity = '0.4'; rowEl.style.pointerEvents = 'none'; }

        fetch('{{ route("dashboard.process-approval") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': _csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type:           type,
                id:             id,
                action:         action,
                invoice_str_id: invoiceStrId || null
            })
        })
        .then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                if (rowEl) rowEl.remove();
                _updateAprTabCount(type);
                var labels = { approved: 'Approved', declined: 'Declined', rejected: 'Rejected', hold: 'Put on Hold' };
                var colors = { approved: '#10B981', declined: '#EF4444', rejected: '#EF4444', hold: '#F59E0B' };
                _showDashToast((labels[action] || 'Done') + ' successfully', colors[action] || '#10B981');
            } else {
                if (rowEl) { rowEl.style.opacity = '1'; rowEl.style.pointerEvents = ''; }
                _showDashToast(result.data.message || 'Action failed. Please try again.', '#EF4444');
            }
        })
        .catch(function () {
            if (rowEl) { rowEl.style.opacity = '1'; rowEl.style.pointerEvents = ''; }
            _showDashToast('Network error. Please try again.', '#EF4444');
        });
    };

    function _updateAprTabCount(type) {
        var tabMap = { leave: 'leaves', permission: 'permissions', salary: 'payroll', invoice: 'invoices' };
        var panel = document.getElementById('apr-panel-' + tabMap[type]);
        var countEl = document.getElementById('tab-count-' + tabMap[type]);
        if (!panel || !countEl) return;
        var remaining = panel.querySelectorAll('tbody tr').length;
        countEl.textContent = remaining;
        if (remaining === 0) {
            panel.innerHTML = '<div class="apr-empty">No pending ' + tabMap[type] + '.</div>';
        }

        // Decrement the dashboard stat card total
        var cardEl = document.getElementById('dash-pending-approvals-count');
        if (cardEl) {
            var current = parseInt(cardEl.textContent.replace(/,/g, ''), 10) || 0;
            var next = Math.max(0, current - 1);
            cardEl.textContent = next.toLocaleString();
        }
    }

    function _showDashToast(msg, color) {
        var toast = document.getElementById('_dashToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = '_dashToast';
            toast.style.cssText = 'position:fixed;bottom:28px;right:28px;padding:13px 20px;border-radius:10px;color:#fff;font-weight:600;font-size:13px;z-index:11000;font-family:"DM Sans",sans-serif;box-shadow:0 8px 24px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.background = color || '#10B981';
        toast.style.opacity = '1';
        toast.style.display = 'block';
        clearTimeout(toast._timer);
        toast._timer = setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { toast.style.display = 'none'; }, 320);
        }, 2800);
    }

    // Close on backdrop click
    document.getElementById('approvalsModal').addEventListener('click', function (e) {
        if (e.target === this) window.closeApprovalsModal();
    });

    // Close on Escape (shared with attendance modal)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            window.closeApprovalsModal();
            window.closeAttendanceModal && window.closeAttendanceModal();
        }
    });
})();
</script>

<script>
(function () {
    var _adData = {
        present: @json($presentEmployees),
        absent:  @json($absentEmployees),
        late:    @json($lateEmployees)
    };

    function fmtTime(dt) {
        if (!dt) return '<span style="color:#8892B0;">—</span>';
        var d = new Date(dt.replace(' ', 'T'));
        if (isNaN(d)) return dt;
        return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    function fmtName(emp) {
        return (emp.firstname || '') + ' ' + (emp.lastname || '');
    }

    function badge(text, bg, color) {
        return '<span class="ad-badge" style="background:' + bg + ';color:' + color + ';">' + text + '</span>';
    }

    function buildTable(cols, rows) {
        var th = cols.map(function(c){ return '<th>' + c + '</th>'; }).join('');
        var tbody = rows.map(function(r, i){
            var tds = r.map(function(cell){ return '<td>' + cell + '</td>'; }).join('');
            return '<tr>' + tds + '</tr>';
        }).join('');
        return '<div style="overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB;">'
            + '<table style="width:100%;border-collapse:collapse;">'
            + '<thead><tr>' + th + '</tr></thead>'
            + '<tbody>' + tbody + '</tbody>'
            + '</table></div>';
    }

    var configs = {
        present: { eyebrow: 'Attendance · Today', title: 'Present Employees', accentColor: '#10B981' },
        absent:  { eyebrow: 'Attendance · Today', title: 'Absent Employees',  accentColor: '#EF4444' },
        late:    { eyebrow: 'Attendance · Today', title: 'Late Arrivals',      accentColor: '#F59E0B' }
    };

    window.openAttendanceModal = function(type) {
        var modal   = document.getElementById('attendanceDetailModal');
        var cfg     = configs[type];
        var data    = _adData[type] || [];

        document.getElementById('adModalEyebrow').textContent = cfg.eyebrow;
        document.getElementById('adModalTitle').textContent   = cfg.title + ' (' + data.length + ')';

        var content = '';
        if (data.length === 0) {
            content = '<div style="text-align:center;padding:48px 24px;color:#8892B0;font-size:14px;font-weight:500;">No records found for today.</div>';
        } else if (type === 'present') {
            var rows = data.map(function(emp, i) {
                var statusBadge = emp.is_late
                    ? badge('Late', '#FFFBEB', '#B45309')
                    : badge('On Time', '#ECFDF5', '#047857');
                return [
                    i + 1,
                    '<strong>' + fmtName(emp) + '</strong><br><small style="color:#8892B0;">' + (emp.designation || '') + '</small>',
                    emp.department || '—',
                    fmtTime(emp.punch_in),
                    fmtTime(emp.punch_out),
                    statusBadge
                ];
            });
            content = buildTable(['#', 'Employee', 'Department', 'Check In', 'Check Out', 'Status'], rows);
        } else if (type === 'absent') {
            var rows = data.map(function(emp, i) {
                return [
                    i + 1,
                    '<strong>' + fmtName(emp) + '</strong><br><small style="color:#8892B0;">' + (emp.designation || '') + '</small>',
                    emp.department || '—',
                    badge('Absent', '#FEF2F2', '#B91C1C')
                ];
            });
            content = buildTable(['#', 'Employee', 'Department', 'Status'], rows);
        } else if (type === 'late') {
            var rows = data.map(function(emp, i) {
                var minsLate = emp.late_minutes || 0;
                var lateLabel = minsLate > 0 ? minsLate + ' min late' : 'Late';
                return [
                    i + 1,
                    '<strong>' + fmtName(emp) + '</strong><br><small style="color:#8892B0;">' + (emp.designation || '') + '</small>',
                    emp.department || '—',
                    fmtTime(emp.punch_in),
                    fmtTime(emp.punch_out),
                    badge(lateLabel, '#FFFBEB', '#B45309')
                ];
            });
            content = buildTable(['#', 'Employee', 'Department', 'Check In', 'Check Out', 'Late By'], rows);
        }

        document.getElementById('adModalContent').innerHTML = content;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    };

    window.closeAttendanceModal = function() {
        document.getElementById('attendanceDetailModal').style.display = 'none';
        document.body.style.overflow = '';
    };

    // Close on backdrop click
    document.getElementById('attendanceDetailModal').addEventListener('click', function(e) {
        if (e.target === this) window.closeAttendanceModal();
    });

})();
</script>

@endsection
