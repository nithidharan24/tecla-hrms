@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* ----------------------
   Container + Header
   ---------------------- */
.report-container { background:#f5f7fb; padding:22px; }
.header-bar {
    background: linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:14px 18px;
    border-radius:8px;
    font-size:18px;
    font-weight:600;
    margin-bottom:16px;
}

/* ----------------------
   Controls Row
   ---------------------- */
.controls {
    display:flex;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:14px;
}
.controls select, .controls input {
    padding:8px 12px;
    border-radius:6px;
    border:1px solid #d6dbe9;
    background:#fff;
    font-size:14px;
}

/* Icon buttons to match other reports */
.icon-btn {
    width:42px;
    height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    background:#fff;
    border:1px solid #d6dbe9;
    font-size:16px;
    cursor:pointer;
}
.icon-btn.primary {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color:#fff;
    border:none;
}
.icon-btn:hover { transform:translateY(-2px); }

/* ----------------------
   Table container
   ---------------------- */
.table-wrapper {
    overflow-x:auto;
    background:#fff;
    border-radius:10px;
    border:1px solid #e6e9f2;
    padding-bottom:12px;
}
.status-table {
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}
.status-table thead th {
    background:#eef6ff;
    color:#ff4800;
    padding:12px;
    font-weight:700;
    border-bottom:1px solid #e6eefc;
    text-align:center;
}
.status-table tbody td {
    padding:12px;
    text-align:center;
    border-bottom:1px solid #f3f4f8;
    font-size:14px;
}
.status-table tbody td:first-child {
    text-align:left;
    padding-left:18px;
    font-weight:700;
}

/* ----------------------
   Beautiful Status Badges
   (from your final design)
   ---------------------- */
.present {
    color: #0f5132;
    background: linear-gradient(135deg,#d4f8df,#a6eec4);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,150,80,0.12);
}

.absent {
    color: #842029;
    background: linear-gradient(135deg,#f8d7da,#f1b0b7);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(150,0,0,0.12);
}

.holiday {
    color: #8a6d3b;
    background: linear-gradient(135deg,#fff3cd,#ffe8a1);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(200,150,0,0.12);
}

.weekend {
    color: #7c4a03;
    background: linear-gradient(135deg,#ffe5cc,#ffcf99);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(180,100,0,0.12);
}

.future {
    color: #6c757d;
    background: linear-gradient(135deg,#f1f1f1,#e4e4e4);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(120,120,120,0.08);
}

.status-table td span:hover { transform: scale(1.06); transition: 0.18s ease-in-out; }

/* ----------------------
   Chart container
   ---------------------- */
.chart-container {
    display:none;
    background:#fff;
    padding:18px;
    border-radius:10px;
    border:1px solid #e6e9f2;
    margin-top:18px;
}

/* Legend */
.legend-box {
    margin-top:12px;
    display:flex;
    gap:14px;
    flex-wrap:wrap;
}
.legend-item { display:flex; gap:8px; align-items:center; font-size:14px; color:#444; }
.legend-dot { width:16px; height:12px; border-radius:3px; display:inline-block; }

/* Print */
@media print {
    .controls, .icon-btn, .chart-container { display:none !important; }
}
</style>

<div class="report-container">

    <div class="header-bar">Team Present / Absent — Calendar View</div>

    {{-- FILTER + ACTIONS --}}
    <form method="get" class="controls" id="filterForm">
        <select name="employee_id" onchange="this.form.submit()">
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                    {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                </option>
            @endforeach
        </select>

        <select name="filter" id="filterMode" onchange="onFilterChange()">
            <option value="monthly" {{ $filter==='monthly'?'selected':'' }}>Monthly</option>
            <option value="weekly"  {{ $filter==='weekly'?'selected':'' }}>Weekly</option>
            <option value="custom"  {{ $filter==='custom'?'selected':'' }}>Custom</option>
        </select>

        <div id="monthlyControls" style="display:{{ $filter==='monthly'?'flex':'none' }};gap:8px;">
            <select name="month">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ (int)date('m',strtotime($startDate))==$m?'selected':'' }}>
                        {{ DateTime::createFromFormat('!m',$m)->format('F') }}
                    </option>
                @endfor
            </select>

            <select name="year">
                @for($y=date('Y')-2;$y<=date('Y')+1;$y++)
                    <option value="{{ $y }}" {{ (int)date('Y',strtotime($startDate))==$y?'selected':'' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <div id="weeklyControls" style="display:{{ $filter==='weekly'?'flex':'none' }};gap:8px;">
            <input type="date" name="week_start" value="{{ request('week_start',$startDate) }}">
        </div>

        <div id="customControls" style="display:{{ $filter==='custom'?'flex':'none' }};gap:8px;">
            <input type="date" name="start_date" value="{{ $startDate }}">
            <input type="date" name="end_date" value="{{ $endDate }}">
        </div>

     <button type="submit" class="btn-primary" style="padding:8px 14px; border-radius:8px; font-weight:600;">
    Apply
</button>

        <!-- Table / Chart Toggle -->
        <button type="button" id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View">
            <i class="fa-solid fa-table-cells"></i>
        </button>

        <button type="button" id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View">
            <i class="fa-solid fa-chart-column"></i>
        </button>

        <!-- CSV (client-side) -->
        <button type="button" class="icon-btn" onclick="downloadCSV()" title="Download CSV">
            <i class="fa-solid fa-file-csv"></i>
        </button>

        <!-- PRINT -->
        <button type="button" class="icon-btn" onclick="window.print()" title="Print">
            <i class="fa-solid fa-print"></i>
        </button>
    </form>

    {{-- Employee header --}}
    <p style="margin:8px 0 6px; font-weight:700;">
        {{ $employee->employeeid ?? '' }} — {{ $employee->firstname ?? '' }} {{ $employee->lastname ?? '' }}
        <span style="color:#666; font-weight:500; margin-left:10px;">({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</span>
    </p>

    {{-- TABLE VIEW --}}
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
                        {{ $employee->firstname }} {{ $employee->lastname }}
                    </td>

                    @foreach($dateLabels as $d)
                        @php
                            $dt = $d['full'];
                            $s = $status[$dt] ?? '-';
                            $class = match($s) {
                                'P' => 'present',
                                'A' => 'absent',
                                'W' => 'weekend',
                                'H' => 'holiday',
                                default => 'future'
                            };
                        @endphp

                        <td>
                            <span class="{{ $class }}">{{ $s }}</span>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    {{-- CHART VIEW --}}
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ----- Helpers / Data from server ----- */
const dateLabels = @json(array_map(fn($d) => $d['date'], $dateLabels));
const dateFull = @json(array_map(fn($d) => $d['full'], $dateLabels));
const statusMap = @json($status); // { "2025-12-01":"P", ... }

/* ----- View toggle ----- */
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

/* ----- CSV download (client-side) ----- */
function downloadCSV() {
    // Build CSV content
    const rows = [];
    // Header rows
    rows.push(['Team Present / Absent Report']);
    rows.push(['Employee', '{{ $employee->firstname }} {{ $employee->lastname }}']);
    rows.push(['Employee ID', '{{ $employee->employeeid ?? '' }}']);
    rows.push(['Period', '{{ $startDate }} to {{ $endDate }}']);
    rows.push([]);
    // Table header
    const header = ['Date','Day','Status'];
    rows.push(header);

    // Data rows
    for (let i = 0; i < dateFull.length; i++) {
        const dFull = dateFull[i];
        const dLabel = dateLabels[i];
        const s = statusMap[dFull] ?? '-';
        rows.push([dFull, dLabel, s]);
    }

    // Convert to CSV string
    const csv = rows.map(r => r.map(c => `"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');

    // Download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const fname = 'present_absent_{{ $employee->employeeid ?? 'report' }}_{{ $startDate }}_to_{{ $endDate }}.csv';
    a.download = fname;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

/* ----- Chart: aggregated counts ----- */
let chartInstance = null;
function renderChart() {
    // Count statuses
    const counts = { P:0, A:0, W:0, H:0, F:0 };
    for (const d of dateFull) {
        const s = statusMap[d] ?? '-';
        if (s === 'P') counts.P++;
        else if (s === 'A') counts.A++;
        else if (s === 'W') counts.W++;
        else if (s === 'H') counts.H++;
        else counts.F++;
    }

    const labels = ['Present','Absent','Holiday','Weekend','Future'];
    const data = [counts.P, counts.A, counts.H, counts.W, counts.F];
    const bg = ['#a6eec4','#f1b0b7','#ffe8a1','#ffcf99','#e4e4e4'];

    const ctx = document.getElementById('statusChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Days',
                data,
                backgroundColor: bg,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display:false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } }
            }
        }
    });
}

/* Initialize default view */
switchView('table');

/* Filter controls toggle (same as your earlier code) */
function onFilterChange() {
    const f = document.getElementById('filterMode').value;
    document.getElementById('monthlyControls').style.display = (f==='monthly') ? 'flex' : 'none';
    document.getElementById('weeklyControls').style.display  = (f==='weekly') ? 'flex' : 'none';
    document.getElementById('customControls').style.display  = (f==='custom') ? 'flex' : 'none';
}
</script>

@endsection
