
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* --- minimal CSS adapted to your style --- */
.report-container { background:#f5f7fb; padding:22px; }
.header-bar { background: linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff; padding:14px 18px; border-radius:8px; font-size:18px; font-weight:600; margin-bottom:16px; }
.controls { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:12px; }
.controls select, .controls input { padding:8px 10px; border-radius:6px; border:1px solid #d6dbe9; background:#fff; font-size:14px; }
.icon-btn { width:42px; height:42px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:#fff; border:1px solid #d6dbe9; font-size:16px; cursor:pointer; }
.icon-btn.primary { background: linear-gradient(90deg,#ff6b3d,#ff8557); color:#fff; border:none; }
.summary-cards { display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
.summary-card { background:#fff; padding:12px 16px; border-radius:8px; border:1px solid #eef2f7; min-width:140px; text-align:center; }
.table-wrapper { overflow-x:auto; background:#fff; border-radius:10px; border:1px solid #e6e9f2; padding:10px; margin-top:12px; }
.status-table { width:100%; border-collapse:collapse; min-width:1100px; }
.status-table th { background:#eef6ff; color:#ff4800; padding:12px; font-weight:700; text-align:center; border-bottom:1px solid #e6eefc;}
.status-table td { padding:12px; border-bottom:1px solid #f3f4f8; text-align:center; font-weight:600; }
.status-table td:first-child { text-align:left; padding-left:16px; font-weight:700; }
.status-badge { padding:6px 12px; border-radius:18px; display:inline-block; font-weight:700; }
.present { background:#d4f8df; color:#0f5132; }
.absent { background:#f8d7da; color:#842029; }
.weekend { background:#e6f2ff; color:#1e3a8a; }
.holiday { background:#fff3cd; color:#8a6d3b; }
.future { background:#f1f1f1; color:#6b7280; }
.right-actions { margin-left:auto; display:flex; gap:8px; align-items:center; }
@media print { .controls, .icon-btn, .right-actions { display:none !important; } }
</style>

<div class="report-container">
    <div class="header-bar">Organization — Presence Hours (Grid)</div>

    <form method="get" class="controls" id="filterForm">
        <label>Period:
            <select name="filter" onchange="this.form.submit()">
                <option value="last7" {{ $filter=='last7'?'selected':'' }}>Last 7 Days</option>
                <option value="this_week" {{ $filter=='this_week'?'selected':'' }}>This Week</option>
                <option value="last_week" {{ $filter=='last_week'?'selected':'' }}>Last Week</option>
                <option value="this_month" {{ $filter=='this_month'?'selected':'' }}>This Month</option>
                <option value="custom" {{ $filter=='custom'?'selected':'' }}>Custom</option>
            </select>
        </label>

        <label>Branch:
            <select name="branch" onchange="this.form.submit()">
                <option value="all">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ (string)$selectedBranch === (string)$b->id ? 'selected':'' }}>
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>Department:
            <select name="department" onchange="this.form.submit()">
                <option value="all">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ (string)$selectedDepartment === (string)$d->id ? 'selected':'' }}>
                        {{ $d->department }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>Employee:
            <select name="employee" onchange="this.form.submit()">
                <option value="all" {{ $selectedEmployee === 'all' ? 'selected' : '' }}>All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ (string)$selectedEmployee === (string)$emp->id ? 'selected':'' }}>
                        {{ $emp->employeeid }} - {{ $emp->firstname }} {{ $emp->lastname }}
                    </option>
                @endforeach
            </select>
        </label>

        <div class="right-actions">
            {{-- CSV link uses action() helper to call controller method with current query --}}
            <a href="{{ action([App\Http\Controllers\OrganizationReportsController::class,'organizationPresenceHoursCSV'], request()->query()) }}" class="icon-btn" title="Download CSV">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <button type="button" class="icon-btn" onclick="window.print()" title="Print">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </form>

    {{-- Summary cards --}}
    <div class="summary-cards">
        <div class="summary-card"><div class="label">Total Hours</div><div class="value">{{ $summaryTotalsHH['total_hours'] }}</div></div>
        <div class="summary-card"><div class="label">Payable Hours</div><div class="value">{{ $summaryTotalsHH['payable_hours'] }}</div></div>
        <div class="summary-card"><div class="label">Present Hours</div><div class="value">{{ $summaryTotalsHH['present_hours'] }}</div></div>
        <div class="summary-card"><div class="label">Holiday Hours</div><div class="value">{{ $summaryTotalsHH['holiday_hours'] }}</div></div>
        <div class="summary-card"><div class="label">Weekend Hours</div><div class="value">{{ $summaryTotalsHH['weekend_hours'] }}</div></div>
    </div>

    {{-- Table view --}}
    <div class="table-wrapper">
        <table class="status-table">
            <thead>
                <tr>
                    <th style="min-width:240px; text-align:left;">Employee</th>
                    @foreach($dates as $d)
                        <th>
                            <div style="font-weight:700;">{{ $d['label'] }}</div>
                            <small style="color:#666;">{{ $d['day'] }}</small>
                        </th>
                    @endforeach
                    <th>Total</th>
                    <th>Payable</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $emp)
                    @php
                        $row = $matrix[$emp->id] ?? null;
                        $total = $row['row_total_minutes'] ?? 0;
                        $payable = $row['row_payable_minutes'] ?? 0;
                    @endphp

                    <tr>
                        <td style="text-align:left;">
                            <div style="font-weight:700;">{{ $emp->firstname }} {{ $emp->lastname }}</div>
                            <div style="font-size:12px; color:#666;">{{ $emp->employeeid }} — {{ $emp->department ?? '' }}</div>
                        </td>

                        @foreach($dates as $d)
                            @php
                                $dt = $d['full'];
                                $cell = $row['dates'][$dt] ?? null;
                                $status = $cell['status'] ?? '-';
                                $minutes = $cell['minutes'] ?? 0;
                                $payableMinutes = $cell['payable_minutes'] ?? 0;

                                if ($status === 'Present') {
                                    $badgeClass = 'present';
                                    $label = 'Present (' . \App\Http\Controllers\OrganizationReportsController::convertMinutes($minutes) . ')';
                                } elseif ($status === 'Holiday') {
                                    $badgeClass = 'holiday';
                                    $label = 'Holiday (08:00)';
                                } elseif ($status === 'Weekend') {
                                    $badgeClass = 'weekend';
                                    $label = 'Weekend (08:00)';
                                } elseif ($status === 'Future') {
                                    $badgeClass = 'future';
                                    $label = 'Future';
                                } else {
                                    $badgeClass = 'absent';
                                    $label = 'Absent (00:00)';
                                }
                            @endphp

                            <td>
                                <span class="status-badge {{ $badgeClass }}">{{ $label }}</span>
                            </td>
                        @endforeach

                        <td>{{ \App\Http\Controllers\OrganizationReportsController::convertMinutes($total) }}</td>
                        <td>{{ \App\Http\Controllers\OrganizationReportsController::convertMinutes($payable) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Chart.js (not required for CSV) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection
