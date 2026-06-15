
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Minimal styles - kept similar to your current reports */
.report-container { background:#f5f7fb; padding:22px; }
.header-bar { background: linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff; padding:14px 18px; border-radius:8px; font-size:18px; font-weight:600; margin-bottom:16px; }
.controls { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:14px; }
.controls select, .controls input { padding:8px 12px; border-radius:6px; border:1px solid #d6dbe9; background:#fff; font-size:14px; }
.icon-btn { width:42px; height:42px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:#fff; border:1px solid #d6dbe9; font-size:16px; cursor:pointer; }
.icon-btn.primary { background: linear-gradient(90deg,#ff6b3d,#ff8557); color:#fff; border:none; }
.table-wrapper { overflow-x:auto; background:#fff; border-radius:10px; border:1px solid #e6e9f2; padding-bottom:12px; }
.status-table { width:100%; border-collapse:collapse; min-width:900px; }
.status-table thead th { background:#eef6ff; color:#ff4800; padding:12px; font-weight:700; border-bottom:1px solid #e6eefc; text-align:center; }
.status-table tbody td { padding:12px; text-align:center; border-bottom:1px solid #f3f4f8; font-size:14px; }
.status-table tbody td:first-child { text-align:left; padding-left:18px; font-weight:700; }
.present { color:#0f5132; background:#d4f8df; padding:6px 12px; border-radius:18px; font-weight:700; }
.absent { color:#842029; background:#f8d7da; padding:6px 12px; border-radius:18px; font-weight:700; }
.holiday { color:#8a6d3b; background:#fff3cd; padding:6px 12px; border-radius:18px; font-weight:700; }
.weekend { color:#7c4a03; background:#ffe5cc; padding:6px 12px; border-radius:18px; font-weight:700; }
.future { color:#6c757d; background:#f1f1f1; padding:6px 12px; border-radius:18px; font-weight:600; }
.chart-container { display:none; background:#fff; padding:18px; border-radius:10px; border:1px solid #e6e9f2; margin-top:18px; }
.legend-box { margin-top:12px; display:flex; gap:14px; flex-wrap:wrap; }
.legend-item { display:flex; gap:8px; align-items:center; font-size:14px; color:#444; }
.legend-dot { width:16px; height:12px; border-radius:3px; display:inline-block; }
@media print { .controls, .icon-btn, .chart-container { display:none !important; } }
</style>

<div class="report-container">

    <div class="header-bar">Organization — Present / Absent (Single Employee)</div>

    <form method="get" class="controls">
        <select name="branch" onchange="this.form.submit()">
            <option value="all">All Branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $selectedBranch == $b->id ? 'selected':'' }}>{{ $b->name }}</option>
            @endforeach
        </select>

        <select name="department" onchange="this.form.submit()">
            <option value="all">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ $selectedDepartment == $d->id ? 'selected':'' }}>{{ $d->department }}</option>
            @endforeach
        </select>

        <select name="employee" onchange="this.form.submit()">
            <option value="all">Select Employee</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                    {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                </option>
            @endforeach
        </select>

        <select name="filter" onchange="this.form.submit()">
            <option value="monthly" {{ $filter==='monthly'?'selected':'' }}>Monthly</option>
            <option value="weekly" {{ $filter==='weekly'?'selected':'' }}>Weekly</option>
            <option value="custom" {{ $filter==='custom'?'selected':'' }}>Custom</option>
        </select>

        @if($filter === 'monthly')
            <select name="month" onchange="this.form.submit()">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ \Carbon\Carbon::parse($startDate)->format('m') == $m ? 'selected':'' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" onchange="this.form.submit()">
                @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ \Carbon\Carbon::parse($startDate)->format('Y') == $y ? 'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
        @elseif($filter === 'weekly')
            <input type="date" name="week_start" value="{{ request('week_start', $startDate) }}" onchange="this.form.submit()">
        @else
            <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()">
            <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()">
        @endif

        <!-- Table / Chart toggle + CSV + Print -->
        <div style="margin-left:auto; display:flex; gap:10px;">
            <button type="button" id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View">
                <i class="fa-solid fa-table-cells"></i>
            </button>

            <button type="button" id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View">
                <i class="fa-solid fa-chart-column"></i>
            </button>

            <a href="{{ route('Organization.presentAbsent.csv', request()->all()) }}" class="icon-btn" title="Download CSV">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <button type="button" class="icon-btn" onclick="window.print()" title="Print">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </form>

    <p style="margin:8px 0 6px; font-weight:700;">
        @if($employee)
            {{ $employee->employeeid }} — {{ $employee->firstname }} {{ $employee->lastname }}
            <span style="color:#666; font-weight:500; margin-left:10px;">
                ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
            </span>
        @else
            Select an employee to show the calendar
        @endif
    </p>

    <div id="tableView" class="table-wrapper">
        <table class="status-table">
            <thead>
                <tr>
                    <th style="min-width:200px; text-align:left; padding-left:18px;">Employee</th>
                    @foreach($dateLabels as $d)
                        <th>
                            <div style="font-weight:700;">{{ $d['day'] }}</div>
                            <div style="font-size:12px;color:#666;">{{ $d['date'] }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="text-align:left; padding-left:18px; font-weight:700;">
                        {{ $employee->firstname ?? '' }} {{ $employee->lastname ?? '' }}
                    </td>

                    @foreach($dateLabels as $d)
                        @php
                            $dt = $d['full'];
                            $s = $status[$dt] ?? '-';
                            $class = $s === 'P' ? 'present' : ($s === 'A' ? 'absent' : ($s === 'H' ? 'holiday' : ($s === 'W' ? 'weekend' : 'future')));
                        @endphp

                        <td>
                            <span class="{{ $class }}">{{ $s }}</span>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div id="chartContainer" class="chart-container">
        <h4 style="margin-bottom:12px;">Presence Summary</h4>
        <canvas id="statusChart" height="120"></canvas>

        <div class="legend-box">
            <div class="legend-item"><span class="legend-dot" style="background:#a6eec4"></span>Present</div>
            <div class="legend-item"><span class="legend-dot" style="background:#f1b0b7"></span>Absent</div>
            <div class="legend-item"><span class="legend-dot" style="background:#ffe8a1"></span>Holiday</div>
            <div class="legend-item"><span class="legend-dot" style="background:#ffcf99"></span>Weekend</div>
            <div class="legend-item"><span class="legend-dot" style="background:#e4e4e4"></span>Future</div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const dateLabelsArr = @json(array_map(fn($d)=>$d['date'], $dateLabels));
const dateFull = @json(array_map(fn($d)=>$d['full'], $dateLabels));
const statusMap = @json($status);
const counts = @json($counts);

function switchView(view) {
    const table = document.getElementById('tableView');
    const chart = document.getElementById('chartContainer');
    const btnTable = document.getElementById('btnTable');
    const btnChart = document.getElementById('btnChart');

    if (view === 'table') {
        table.style.display = 'block';
        chart.style.display = 'none';
        btnTable.classList.add('primary');
        btnChart.classList.remove('primary');
    } else {
        table.style.display = 'none';
        chart.style.display = 'block';
        btnChart.classList.add('primary');
        btnTable.classList.remove('primary');
        renderChart();
    }
}

let chartInstance = null;
function renderChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Present','Absent','Holiday','Weekend','Future'],
            datasets: [{
                label: 'Days',
                data: [counts.P, counts.A, counts.H, counts.W, counts.F],
                backgroundColor: ['#a6eec4','#f1b0b7','#ffe8a1','#ffcf99','#e4e4e4'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display:false } },
            scales: { y: { beginAtZero:true, ticks:{ precision:0 } } }
        }
    });
}

// default
switchView('table');
</script>

@endsection
