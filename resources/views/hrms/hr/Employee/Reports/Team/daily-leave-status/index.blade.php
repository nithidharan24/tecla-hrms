@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
.page { background:#f8f9fc; padding:22px; }

.header-bar {
    background: #ff5a2c;
    padding: 16px 20px;
    color:white;
    border-radius:10px;
    font-size:18px;
    font-weight:700;
    margin-bottom:20px;
}

/* ICON BUTTONS */
.icon-btn {
    width:42px;
    height:42px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:8px;
    background:white;
    border:1px solid #ddd;
    cursor:pointer;
    font-size:18px;
}
.icon-btn.primary {
    background:#ff5a2c;
    color:white;
    border:none;
}

/* Summary Cards */
.cards-row { display:flex; gap:18px; flex-wrap:wrap; margin-bottom:20px; }
.card {
    flex:1;
    min-width:160px;
    background:white;
    border-radius:10px;
    padding:15px 20px;
    border:1px solid #e8e8e8;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}
.card .label { color:#6b7280; font-size:13px; }
.card .value { margin-top:6px; font-weight:700; font-size:22px; }

/* TABLE */
.table-wrapper { display:block; }
.table {
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
}
.table th {
    background:#eef6ff;
    padding:12px;
    color:#ff5a2c;
    font-size:14px;
    font-weight:700;
}
.table td {
    padding:12px;
    border-bottom:1px solid #eee;
}

/* BADGES */
.badge {
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    display:inline-block;
}
.badge-present { background:#d1fae5; color:#065f46; }
.badge-leave { background:#fee2e2; color:#b91c1c; }
.badge-weekend { background:#dbeafe; color:#1e3a8a; }
.badge-holiday { background:#fef3c7; color:#92400e; }
.badge-future { background:#f3f4f6; color:#6b7280; }

/* CHART */
.chart-container {
    display:none;
    background:white;
    padding:20px;
    border-radius:10px;
    border:1px solid #e3e6ef;
    text-align:center;
}

/* PRINT */
@media print {
    .icon-btn, input, button { display:none !important; }
}
</style>

<div class="page">

    <div class="header-bar">Team – Daily Leave Status</div>

    {{-- TOP ROW: LEFT DATE + RIGHT BUTTONS --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

        <!-- LEFT: DATE INPUT -->
        <div>
            <input type="date" name="date" value="{{ $date }}"
                onchange="window.location='?date='+this.value"
                style="padding:8px 12px; border:1px solid #ccc; border-radius:6px; min-width:160px;">
        </div>

        <!-- RIGHT: ACTION BUTTONS -->
        <div style="display:flex; gap:12px;">

            <!-- Table View -->
            <button class="icon-btn primary" id="btnTable" onclick="switchView('table')" title="Table View">
                <i class="fa-solid fa-table"></i>
            </button>

            <!-- Chart View -->
            <button class="icon-btn" id="btnChart" onclick="switchView('chart')" title="Chart View">
                <i class="fa-solid fa-chart-pie"></i>
            </button>

            <!-- CSV -->
            <a href="{{ route('team.dailyLeave.csv', ['date' => $date]) }}" class="icon-btn" title="Download CSV">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <!-- PRINT -->
            <button onclick="window.print()" class="icon-btn" title="Print">
                <i class="fa-solid fa-print"></i>
            </button>

        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="cards-row">
        <div class="card"><div class="label">Present</div> <div class="value">{{ $summary['present'] }}</div></div>
        <div class="card"><div class="label">Leave</div> <div class="value">{{ $summary['leave'] }}</div></div>
        <div class="card"><div class="label">Holiday</div> <div class="value">{{ $summary['holiday'] }}</div></div>
        <div class="card"><div class="label">Weekend</div> <div class="value">{{ $summary['weekend'] }}</div></div>
        <div class="card"><div class="label">Future</div> <div class="value">{{ $summary['future'] }}</div></div>
    </div>

    {{-- TABLE VIEW --}}
    <div id="tableView" class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Emp ID</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            @foreach($results as $row)
                @php
                    $badge = [
                        "Present" => "badge-present",
                        "Leave" => "badge-leave",
                        "Holiday" => "badge-holiday",
                        "Weekend" => "badge-weekend",
                        "Future" => "badge-future"
                    ][$row['status']];
                @endphp

                <tr>
                    <td>{{ $row['employee']->firstname }} {{ $row['employee']->lastname }}</td>
                    <td>{{ $row['employee']->employeeid }}</td>
                    <td><span class="badge {{ $badge }}">{{ $row['status'] }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- CHART VIEW --}}
    <div id="chartContainer" class="chart-container">
        <h4>Status Distribution</h4>

        <!-- Reduced Pie Chart Size -->
        <div style="width:260px; height:260px; margin:auto;">
            <canvas id="statusChart"></canvas>
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
        renderChart();
    }
}

let chartInstance = null;

function renderChart() {
    const summary = @json($summary);

    const data = [
        summary.present,
        summary.leave,
        summary.holiday,
        summary.weekend,
        summary.future
    ];

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(document.getElementById("statusChart"), {
        type: 'pie',
        data: {
            labels: ["Present", "Leave", "Holiday", "Weekend", "Future"],
            datasets: [{
                data,
                backgroundColor: ["#16a34a", "#dc2626", "#f59e0b", "#2563eb", "#6b7280"]
            }]
        },
        options: {
            maintainAspectRatio: false,
        }
    });
}

switchView("table");
</script>

@endsection
