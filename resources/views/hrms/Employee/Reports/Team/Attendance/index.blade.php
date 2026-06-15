@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* ================== PAGE WRAPPER ================== */
.report-wrapper {
    background:#f4f6fb;
    padding:25px;
}

/* ================== ORANGE TOP TITLE BAR ================== */
.top-title-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:18px 22px;
    border-radius:10px;
    font-size:18px;
    font-weight:700;
    margin-bottom:20px;
}

/* ================== FILTER CARD ================== */
.filter-card {
    background:#fff;
    padding:14px 18px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 1px 4px rgba(0,0,0,0.1);
    margin-bottom:15px;
}

.filter-card select,
.filter-card input {
    border:1px solid #dcdff0;
    border-radius:6px;
    padding:6px 10px;
}

/* ================== ACTION ICON BUTTONS ================== */
.action-buttons {
    display:flex;
    gap:10px;
}

.icon-btn {
    width:42px;
    height:42px;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#fff;
    border-radius:10px;
    border:1px solid #e6edf7;
    cursor:pointer;
    font-size:18px;
}

.icon-btn.primary {
    background:linear-gradient(90deg,#ff6b3d,#ff8557);
    color:#fff;
    border:none;
}

.icon-btn:hover {
    transform:translateY(-2px);
}

/* ================== EMPLOYEE BADGE ================== */
.emp-chip {
    background:#eef2ff;
    padding:7px 15px;
    border-radius:20px;
    color:#3949ab;
    font-weight:600;
    display:inline-block;
    margin-bottom:15px;
}

/* ================== TABLE ================== */
.table-container {
    background:#fff;
    border-radius:10px;
    overflow-x:auto;
    box-shadow:0 1px 4px rgba(0,0,0,0.1);
}

.table thead th {
    background:#eef2ff;
    padding:12px;
    font-size:13px;
    font-weight:700;
    white-space:nowrap;
}

.table tbody td {
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
    white-space:nowrap;
}

.red { color:#e63946; font-weight:600; }
.green { color:#2a9d8f; font-weight:600; }
.gray { color:#777; }

/* ================== CHART CONTAINER ================== */
.chart-container {
    display:none;
    background:#fff;
    padding:20px;
    border-radius:10px;
    margin-top:20px;
    box-shadow:0 1px 4px rgba(0,0,0,0.1);
}

/* ================== PRINT MODE ================== */
@media print {
    .top-title-bar,
    .filter-card,
    .action-buttons,
    select,
    input,
    button {
        display:none !important;
    }
}
</style>

<div class="report-wrapper">

    {{-- ================== TOP ORANGE TITLE ================== --}}
    <div class="top-title-bar">
        Team Early / Late Timing Report
    </div>

    {{-- ================== FILTER CARD ================== --}}
    <div class="filter-card">

        <form method="get" class="d-flex gap-2 align-items-center">

            <select name="employee_id" onchange="this.form.submit()">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                        {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="start_date" value="{{ $startDate }}">
            <input type="date" name="end_date" value="{{ $endDate }}">

            <button class="btn btn-primary btn-sm">Apply</button>
        </form>

        {{-- ================== RIGHT SIDE BUTTONS ================== --}}
        <div class="action-buttons">

            <!-- TABLE VIEW BTN -->
            <button id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View">
                <i class="fa-solid fa-table-cells"></i>
            </button>

            <!-- CHART VIEW BTN -->
            <button id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View">
                <i class="fa-solid fa-chart-column"></i>
            </button>
            @if(isset($permissions) && $permissions->can_download)
            <!-- CSV EXPORT -->
            <a href="{{ route('team.earlyLate.csv') }}?employee_id={{ $selectedEmployeeId }}" class="icon-btn" title="Download CSV">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <!-- PRINT -->
            <button onclick="window.print()" class="icon-btn" title="Print Report">
                <i class="fa-solid fa-print"></i>
            </button>
            @endif

        </div>
    </div>

    {{-- ================== EMPLOYEE NAME ================== --}}
    <div class="emp-chip">
        EMP {{ $employee->employeeid }} — {{ $employee->firstname }} {{ $employee->lastname }}
    </div>

    {{-- ================== TABLE VIEW ================== --}}
    <div id="tableView" class="table-container mt-3">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>First In</th>
                    <th>Last Out</th>
                    <th>Total</th>
                    <th>Entry Early</th>
                    <th>Entry Late</th>
                    <th>Exit Early</th>
                    <th>Exit Late</th>
                    <th>Net</th>
                    <th>Shift</th>
                </tr>
            </thead>

            <tbody>
                @foreach($report as $r)
                <tr>
                    <td>{{ date('d-M-Y', strtotime($r['date'])) }}</td>

                    <td class="gray">{{ $r['first_in'] }}</td>
                    <td class="gray">{{ $r['last_out'] }}</td>

                    <td class="green">{{ $r['total_hours'] }}</td>

                    <td class="{{ $r['entry_early']!='-'?'green':'gray' }}">{{ $r['entry_early'] }}</td>
                    <td class="{{ $r['entry_late']!='-'?'red':'gray' }}">{{ $r['entry_late'] }}</td>

                    <td class="{{ $r['exit_early']!='-'?'red':'gray' }}">{{ $r['exit_early'] }}</td>
                    <td class="{{ $r['exit_late']!='-'?'green':'gray' }}">{{ $r['exit_late'] }}</td>

                    <td class="green">{{ $r['net_hours'] }}</td>
                    <td class="gray">{{ $r['shift'] }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    {{-- ================== CHART VIEW ================== --}}
    <div id="chartContainer" class="chart-container">
        <h5 class="mb-3">Early / Late Overview</h5>

        <canvas id="earlyLateChart" height="120"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* SWITCH TABLE / CHART VIEW */
function switchView(view) {

    const tableView = document.getElementById("tableView");
    const chartView = document.getElementById("chartContainer");

    const btnTable = document.getElementById("btnTable");
    const btnChart = document.getElementById("btnChart");

    if (view === "table") {
        tableView.style.display = "block";
        chartView.style.display = "none";

        btnTable.classList.add("primary");
        btnChart.classList.remove("primary");
    }
    else {
        tableView.style.display = "none";
        chartView.style.display = "block";

        btnChart.classList.add("primary");
        btnTable.classList.remove("primary");

        drawChart();
    }
}

/* DRAW CHART */
let chartInstance = null;

function drawChart() {
    const report = @json($report);

    const entryLate = report.map(r => toMinutes(r.entry_late));
    const exitLate  = report.map(r => toMinutes(r.exit_late));
    const labels    = report.map(r => r.date);

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(document.getElementById('earlyLateChart'), {
        type:'bar',
        data:{
            labels,
            datasets:[
                {
                    label:"Entry Late (mins)",
                    backgroundColor:"#e63946",
                    data: entryLate
                },
                {
                    label:"Exit Late (mins)",
                    backgroundColor:"#2a9d8f",
                    data: exitLate
                }
            ]
        }
    });
}

function toMinutes(hhmm) {
    if (hhmm === "-" || !hhmm.includes(":")) return 0;
    let p = hhmm.split(":");
    return (parseInt(p[0]) * 60) + parseInt(p[1]);
}

/* DEFAULT VIEW = TABLE */
switchView("table");
</script>

@endsection
