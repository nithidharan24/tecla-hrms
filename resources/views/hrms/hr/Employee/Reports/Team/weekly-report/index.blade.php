@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { font-family: 'Inter', sans-serif; }

/* PAGE WRAPPER */
.report-container { background:#f4f6fb; padding:25px; }

/* HEADER BAR */
.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:16px 22px;
    border-radius:12px;
    font-size:20px;
    font-weight:700;
}

/* FILTER + BUTTON BAR */
.controls {
    margin-top:20px;
    display:flex;
    gap:18px;
    flex-wrap:wrap;
    background:#fff;
    padding:14px 18px;
    border-radius:14px;
    border:1px solid #e0e6f1;
    align-items:center;
}

.controls .filters {
    display:flex;
    gap:12px;
    flex:1;
    flex-wrap:wrap;
}

.controls label {
    font-size:13px;
    color:#333;
    font-weight:600;
    display:block;
}

.controls select,
.controls input {
    padding:9px 10px;
    border-radius:8px;
    border:1px solid #ccd3e1;
    min-width:150px;
}

/* BUTTON ICONS */
.actions {
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
    font-size:16px;
}

.icon-btn.primary {
    background:linear-gradient(90deg,#ff6b3d,#ff8557);
    color:#fff;
    border:none;
}

.icon-btn:hover { transform:translateY(-2px); }

/* TABLE WRAPPER */
.table-wrapper {
    margin-top:20px;
    background:#fff;
    padding:16px;
    border-radius:12px;
    border:1px solid #e2e8f0;
    overflow-x:auto;
}

.table-report {
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

.table-report thead th {
    padding:14px;
    text-align:center;
    background:#f9f9ff;
    color:#ff6b3d;
    font-weight:700;
    border-bottom:1px solid #eaefff;
}

.table-report tbody td {
    padding:14px;
    text-align:center;
    border-bottom:1px dashed #eee;
    font-weight:600;
}

.project-title { text-align:left; font-weight:700; padding-left:12px; }

/* CHART VIEW BOX */
.chart-container {
    display:none;
    background:#fff;
    margin-top:20px;
    padding:20px;
    border-radius:12px;
    border:1px solid #e2e8f0;
}

.chart-row {
    display:flex;
    justify-content:center;
    gap:40px;
    flex-wrap:wrap;
}
</style>

<div class="report-container">

    <div class="header-bar">Weekly Time Log Report</div>

    <!-- FILTER + ACTION BUTTON BAR -->
    <div class="controls">

        <div class="filters">
            <div>
                <label>Employee</label>
                <select id="employeeFilter">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                            {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>From</label>
                <input type="date" id="weekStart" value="{{ $startDate }}">
            </div>

            <div>
                <label>To</label>
                <input type="date" id="weekEnd" value="{{ $endDate }}">
            </div>

            <button onclick="applyWeeklyFilter()"
                style="background:#ff6b3d;color:#fff;border:none;padding:10px 16px;border-radius:8px;font-weight:600;">
                Apply
            </button>
        </div>

       <div class="actions">

    <!-- TABLE VIEW -->
    <button id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View">
        <i class="fa-solid fa-table-cells"></i>
    </button>

    <!-- CHART VIEW -->
    <button id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View">
        <i class="fa-solid fa-chart-column"></i>
    </button>
    @if(isset($permissions) && $permissions->can_download)
    <!-- CSV EXPORT -->
    <button class="icon-btn" onclick="downloadCSV()" title="Export CSV">
        <i class="fa-solid fa-file-csv"></i>
    </button>

    <!-- PRINT -->
    <button class="icon-btn" onclick="window.print()" title="Print Report">
        <i class="fa-solid fa-print"></i>
    </button>
    @endif

</div>

    </div>

    <!-- ====================== TABLE VIEW ====================== -->
    <div id="tableView" class="table-wrapper">
        <table class="table-report">
            <thead>
                <tr>
                    <th>Project</th>
                    @foreach($dates as $d)
                        <th>
                            {{ \Carbon\Carbon::parse($d)->format('D') }} <br>
                            <span style="font-size:12px;color:#777;">
                                {{ \Carbon\Carbon::parse($d)->format('d-M') }}
                            </span>
                        </th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($report as $project => $p)
                    <tr>
                        <td class="project-title">{{ $project }}</td>

                        @foreach($dates as $d)
                            <td>{{ $p['daily'][$d] ?? '00:00' }}</td>
                        @endforeach

                        <td style="font-weight:700;">{{ $p['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ====================== CHART VIEW ====================== -->
    <div id="chartContainer" class="chart-container">

        <div class="chart-row">
            <div style="width:650px;height:300px;">
                <canvas id="barChart"></canvas>
            </div>

            <div style="width:300px;height:300px;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* APPLY FILTER */
function applyWeeklyFilter() {
    const params = new URLSearchParams();
    params.set("employee_id", document.getElementById("employeeFilter").value);
    params.set("week_start", document.getElementById("weekStart").value);
    params.set("week_end", document.getElementById("weekEnd").value);

    window.location.search = params.toString();
}

/* SWITCH TABLE / CHART VIEW */
function switchView(view) {

    const table = document.getElementById("tableView");
    const charts = document.getElementById("chartContainer");

    const btnTable = document.getElementById("btnTable");
    const btnChart = document.getElementById("btnChart");

    if (view === "table") {
        table.style.display = "block";
        charts.style.display = "none";

        btnTable.classList.add("primary");
        btnChart.classList.remove("primary");
    }
    else {
        table.style.display = "none";
        charts.style.display = "block";

        btnChart.classList.add("primary");
        btnTable.classList.remove("primary");

        renderCharts();
    }
}

/* CHART RENDERING */
let barC, pieC;

function renderCharts() {

    const labels = @json($dates);
    const hours = @json($dailyHours);

    if (barC) barC.destroy();
    if (pieC) pieC.destroy();

    /* BAR CHART */
    barC = new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels,
            datasets: [{
                data: hours,
                backgroundColor:"#ff6b3d",
                borderRadius:6
            }]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true}}
        }
    });

    /* PIE CHART */
    pieC = new Chart(document.getElementById("pieChart"), {
        type:"pie",
        data:{
            labels,
            datasets:[{
                data: hours,
                backgroundColor:[
                    "#ff6b3d","#ff8c66","#ffa07d",
                    "#ffb199","#ffd0c2","#ff9970",
                    "#ff7f56"
                ]
            }]
        }
    });
}

/* DEFAULT VIEW = TABLE */
switchView("table");
</script>

@endsection
