
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

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

/* FILTER + ACTION BUTTONS */
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

.filters {
    display:flex;
    flex-wrap:wrap;
    gap:15px;
}

.controls label {
    font-size:13px;
    font-weight:600;
    color:#333;
    display:block;
}

.controls select,
.controls input {
    padding:9px 10px;
    border-radius:8px;
    border:1px solid #ccd3e1;
    min-width:150px;
}

.actions {
    display:flex;
    gap:10px;
    margin-left:auto;
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

/* EMPLOYEE BLOCK */
.emp-block {
    background:#fff;
    padding:16px;
    border-radius:12px;
    border:1px solid #e4e9f3;
    margin-top:20px;
}

.emp-title {
    font-weight:800;
    font-size:15px;
    margin-bottom:10px;
}

/* TABLE WRAPPER */
.table-wrapper {
    overflow-x:auto;
}

.project-table {
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

.project-table thead th {
    padding:14px;
    background:#f9f9ff;
    color:#ff6b3d;
    font-weight:700;
    border-bottom:1px solid #eaefff;
    text-align:center;
}

.project-table tbody td {
    padding:12px;
    text-align:center;
    border-bottom:1px dashed #eee;
    font-weight:600;
}

.project-name {
    text-align:left;
    padding-left:14px;
    font-weight:700;
    color:#222;
}

/* CHART CONTAINER */
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

.summary {
    font-weight:700;
    margin-top:12px;
    font-size:15px;
}
</style>


<div class="report-container">

    <div class="header-bar">Organization — Weekly Time Log (Employee → Project → Days)</div>

    <form method="get" class="controls">

        <div class="filters">

            <div>
                <label>Branch</label>
                <select name="branch" onchange="this.form.submit()">
                    <option value="all">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branch==$b->id?'selected':'' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Department</label>
                <select name="department" onchange="this.form.submit()">
                    <option value="all">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $department==$d->id?'selected':'' }}>
                            {{ $d->department }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>From</label>
                <input type="date" name="start_date" value="{{ $start }}">
            </div>

            <div>
                <label>To</label>
                <input type="date" name="end_date" value="{{ $end }}">
            </div>

            <button class="btn btn-warning" style="font-weight:600;border-radius:8px;">Apply</button>

        </div>

        <div class="actions">

            <button id="btnTable" class="icon-btn primary" onclick="switchView('table')" type="button">
                <i class="fa-solid fa-table-cells"></i>
            </button>

            <button id="btnChart" class="icon-btn" onclick="switchView('chart')" type="button">
                <i class="fa-solid fa-chart-column"></i>
            </button>

            <a href="{{ route('Organization.weekly.csv') }}" class="icon-btn">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <button type="button" class="icon-btn" onclick="window.print()">
                <i class="fa-solid fa-print"></i>
            </button>

        </div>

    </form>

    <!-- ===================== TABLE VIEW ===================== -->
    <div id="tableView">

        @foreach($matrix as $empId => $empRow)

            @php $emp = $empRow['employee']; @endphp

            <div class="emp-block">

                <div class="emp-title">
                    {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                </div>

                <div class="table-wrapper">
                    <table class="project-table">

                        <thead>
                            <tr>
                                <th>Project</th>
                                @foreach($dates as $d)
                                    <th>
                                        {{ \Carbon\Carbon::parse($d)->format('D') }} <br>
                                        <small>{{ \Carbon\Carbon::parse($d)->format('d-M') }}</small>
                                    </th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($empRow['projects'] as $projLabel => $proj)
                                <tr>
                                    <td class="project-name">{{ $projLabel }}</td>

                                    @foreach($dates as $d)
                                        <td>{{ \App\Http\Controllers\OrganizationReportsController::minutesToHHMM($proj['daily'][$d]) }}</td>
                                    @endforeach

                                    <td style="font-weight:700;">
                                        {{ \App\Http\Controllers\OrganizationReportsController::minutesToHHMM($proj['total']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="summary">
                        Employee Total:
                        {{ \App\Http\Controllers\OrganizationReportsController::minutesToHHMM($empRow['row_total']) }}
                    </div>

                </div>

            </div>

        @endforeach

        <div class="summary" style="margin-top:18px;">
            Organization Total:
            {{ \App\Http\Controllers\OrganizationReportsController::minutesToHHMM($summary['total_minutes']) }}
        </div>

    </div>


    <!-- ===================== CHART VIEW ===================== -->
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

function switchView(view) {

    const table = document.getElementById("tableView");
    const chart = document.getElementById("chartContainer");

    const btnTable = document.getElementById("btnTable");
    const btnChart = document.getElementById("btnChart");

    if (view === "table") {
        table.style.display = "block";
        chart.style.display = "none";

        btnTable.classList.add("primary");
        btnChart.classList.remove("primary");
    } else {
        table.style.display = "none";
        chart.style.display = "block";

        btnChart.classList.add("primary");
        btnTable.classList.remove("primary");

        renderCharts();
    }
}

let barC, pieC;

function renderCharts() {

    const labels = @json($dates);
    const hours = Object.values(@json($summary['by_date_minutes'] ?? [])).map(v => v/60);

    if (barC) barC.destroy();
    if (pieC) pieC.destroy();

    barC = new Chart(document.getElementById("barChart"), {
        type:"bar",
        data:{
            labels: labels,
            datasets:[{
                data: hours,
                backgroundColor:"#ff6b3d",
                borderRadius:6
            }]
        },
        options:{
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true}}
        }
    });

    pieC = new Chart(document.getElementById("pieChart"), {
        type:"pie",
        data:{
            labels: labels,
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

/* Default view: Table */
switchView("table");

</script>

@endsection
