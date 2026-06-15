@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* (same styles as previous Zoho style) */
/* trimmed for brevity — you can reuse your existing styles above; important styles below */
/* Page Container */
.report-container {
    background: #f5f7fb;
    padding: 22px;
}

/* Header */
.header-bar {
    background: rgba(255, 55, 0, 0.8);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
}

/* Navigation */
.month-nav {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 18px 0;
    gap: 15px;
}

.month-nav button {
    background: #fff;
    border: 1px solid #d1d5e0;
    width: 38px;
    height: 38px;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
    color: #3b4a6b;
}

.month-title {
    font-size: 20px;
    font-weight: 600;
    color: rgba(255, 55, 0, 0.8);
}

/* Table Wrapper */
.table-wrapper {
    overflow-x: auto;
    background: #ffffff;
    border-radius: 10px;
    border: 1px solid #dde3ef;
    padding-bottom: 12px;
}

/* Table */
.status-table {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
}

.status-table thead th {
    background: #eef2fb;
    padding: 10px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(255, 55, 0, 0.8);
    text-align: center;
    border-bottom: 1px solid #dce2f2;
}

.status-table tbody td {
    padding: 11px;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    border-bottom: 1px solid #f1f2f6;
}

/* Status Colors */
.present     { color: #2a8f55 !important; }
.absent      { color: #e63946 !important; }
.weekend     { color: #bc6c25 !important; font-weight: 700; }
.holiday     { color: #d89f00 !important; font-weight: 700; }
.future      { color: #888 !important; }

/* Background Marks */
.bg-weekend {
    background: repeating-linear-gradient(
        -45deg,
        #f4f4f4,
        #f4f4f4 5px,
        #e7e7e7 5px,
        #e7e7e7 10px
    );
}
.bg-holiday {
    background: #fff8e0;
}
.bg-future {
    background: #fafafa;
}

/* Legend */
.legend-box {
    margin-top: 25px;
    padding: 12px;
    background: white;
    border-radius: 10px;
    border: 1px solid #d5ddee;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    margin-right: 20px;
    font-size: 14px;
}
.legend-symbol {
    width: 45px;
    height: 3px;
    margin-right: 6px;
    border-radius: 10px;
}
.controls {
    display:flex;
    gap:12px;
    align-items:center;
    margin:14px 0;
}
.controls select, .controls input {
    padding:8px 10px;
    border-radius:6px;
    border:1px solid #d6dbe9;
    background:#fff;
}
.controls .btn {
    padding:8px 12px;
    border-radius:6px;
    border: none;
    cursor:pointer;
}
.btn-primary { background:rgba(255, 55, 0, 0.8); color:#fff; }
.btn-outline { background:#fff; border:1px solid #cdd6ed; color:#333; }
.right-actions { margin-left:auto; display:flex; gap:8px; }
.small { font-size:13px; padding:6px 8px; }
</style>

<div class="report-container">
    <div class="header-bar">Present / Absent Status</div>

    {{-- FILTER CONTROLS --}}
    <form id="filterForm" method="get" class="controls">
        <label>
            View:
            <select name="filter" id="filterMode" onchange="onFilterChange()">
                <option value="monthly" {{ $filter === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="weekly"  {{ $filter === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="custom"  {{ $filter === 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
        </label>

        <div id="monthlyControls" style="display:{{ $filter === 'monthly' ? 'flex' : 'none' }}; gap:8px; align-items:center;">
            <select name="month">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ (int)date('m', strtotime($startDate)) === $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>
            <select name="year">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ (int)date('Y', strtotime($startDate)) === $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <div id="weeklyControls" style="display:{{ $filter === 'weekly' ? 'flex' : 'none' }}; gap:8px; align-items:center;">
            <label>Week start:
                <input type="date" name="week_start" value="{{ old('week_start', request('week_start', \Carbon\Carbon::parse($startDate)->format('Y-m-d'))) }}">
            </label>
        </div>

        <div id="customControls" style="display:{{ $filter === 'custom' ? 'flex' : 'none' }}; gap:8px; align-items:center;">
            <label>From: <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}"></label>
            <label>To:   <input type="date" name="end_date"   value="{{ request('end_date', $endDate) }}"></label>
        </div>

        <div class="right-actions">
            <button type="submit" class="btn btn-primary small">Apply</button>

            <a id="downloadLink" href="{{ route('myreports.presentAbsent.download', request()->all()) }}" class="btn btn-outline small">Download CSV</a>

            <button type="button" onclick="window.print()" class="btn btn-outline small">Print</button>
        </div>
    </form>

    {{-- Table (we reuse your existing table layout) --}}
    <div class="table-wrapper">
        <table class="status-table">
            <thead>
                <tr>
                    <th style="min-width:210px;text-align:left;padding-left:20px;">Employee</th>
                    @foreach ($dateLabels as $d)
                        <th>
                            <div>{{ $d['day'] }}</div>
                            <small>{{ $d['date'] }}</small>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="text-align:left; padding-left:20px;">
                        {{ $employee->employeeid }} {{ $employee->firstname }} {{ $employee->lastname }}
                    </td>

                    @foreach ($dateLabels as $d)
                        @php
                            $dt = $d['full'];
                            $s = $status[$dt] ?? '-';
                            $cellClass = $s === 'P' ? 'present' : ($s === 'A' ? 'absent' : ($s === 'W' ? 'weekend' : ($s === 'H' ? 'holiday' : 'future')));
                            $bgClass = ($s === 'W') ? 'bg-weekend' : (($s === 'H') ? 'bg-holiday' : (($s === '-') ? 'bg-future' : ''));
                        @endphp
                        <td class="{{ $cellClass }} {{ $bgClass }}">{{ $s }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Legend (same as before) --}}
    <div class="legend-box">
        <div class="legend-item"><span class="legend-symbol" style="background:#2a8f55;"></span> P - Present</div>
        <div class="legend-item"><span class="legend-symbol" style="background:#e63946;"></span> A - Absent</div>
        <div class="legend-item"><span class="legend-symbol" style="background:#d89f00;"></span> H - Holiday</div>
        <div class="legend-item"><span class="legend-symbol" style="background:#bc6c25;"></span> W - Weekend</div>
        <div class="legend-item"><span class="legend-symbol" style="background:#888;"></span> - Future Day</div>
    </div>

</div>

<script>
function onFilterChange() {
    const mode = document.getElementById('filterMode').value;
    document.getElementById('monthlyControls').style.display = (mode === 'monthly' ? 'flex' : 'none');
    document.getElementById('weeklyControls').style.display = (mode === 'weekly' ? 'flex' : 'none');
    document.getElementById('customControls').style.display = (mode === 'custom' ? 'flex' : 'none');

    // update download link querystring if needed (form submission will also handle)
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    document.getElementById('downloadLink').href = "{{ route('myreports.presentAbsent.download') }}?" + params.toString();
}

// update download href when page loads with current params
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    document.getElementById('downloadLink').href = "{{ route('myreports.presentAbsent.download') }}?" + params.toString();
});
</script>

@endsection
