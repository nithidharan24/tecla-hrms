
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
    width:42px; height:42px;
    display:flex; justify-content:center; align-items:center;
    border-radius:8px; background:white;
    border:1px solid #ddd; cursor:pointer;
    font-size:18px;
}
.icon-btn.primary { background:#ff5a2c; color:white; border:none; }

/* Summary Cards */
.cards-row { display:flex; gap:18px; flex-wrap:wrap; margin-bottom:20px; }
.card {
    flex:1; min-width:160px;
    background:white; border-radius:10px;
    padding:15px 20px; border:1px solid #e8e8e8;
}
.card .label { color:#6b7280; font-size:13px; }
.card .value { margin-top:6px; font-weight:700; font-size:22px; }

/* TABLE */
.table-wrapper { display:block; }
.table { width:100%; border-collapse:collapse; background:white; border-radius:10px; }
.table th {
    background:#eef6ff; padding:12px;
    color:#ff5a2c; font-size:14px; font-weight:700;
}
.table td { padding:12px; border-bottom:1px solid #eee; }

/* BADGES */
.badge { padding:6px 14px; border-radius:999px; font-size:12px; font-weight:700; }
.badge-present { background:#d1fae5; color:#065f46; }
.badge-leave { background:#fee2e2; color:#b91c1c; }
.badge-weekend { background:#dbeafe; color:#1e3a8a; }
.badge-holiday { background:#fef3c7; color:#92400e; }
.badge-future { background:#f3f4f6; color:#6b7280; }
.badge-absent { background:#ffe4e6; color:#9f1239; }

/* CHART */
.chart-container {
    display:none; background:white;
    padding:20px; border-radius:10px; border:1px solid #e3e6ef;
    text-align:center;
}
</style>

<div class="page">

    <div class="header-bar"> Daily Leave Status</div>

    {{-- FILTERS --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

        <div style="display:flex; gap:12px; align-items:center;">

            {{-- DATE --}}
            <input type="date" value="{{ $date }}"
                onchange="applyFilter()"
                id="filterDate"
                style="padding:8px 12px; border:1px solid #ccc; border-radius:6px;">

            {{-- BRANCH --}}
            <select id="filterBranch" onchange="applyFilter()"
                style="padding:8px 12px; border:1px solid #ccc; border-radius:6px;">
                <option value="all">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $selectedBranch == $b->id ? 'selected':'' }}>
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>

            {{-- DEPARTMENT --}}
            <select id="filterDepartment" onchange="applyFilter()"
                style="padding:8px 12px; border:1px solid #ccc; border-radius:6px;">
                <option value="all">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ $selectedDepartment == $d->id ? 'selected':'' }}>
                        {{ $d->department }}
                    </option>
                @endforeach
            </select>

        </div>

        {{-- BUTTONS --}}
        <div style="display:flex; gap:12px;">
            <button class="icon-btn primary" id="btnTable" onclick="switchView('table')">
                <i class="fa-solid fa-table"></i>
            </button>

            <button class="icon-btn" id="btnChart" onclick="switchView('chart')">
                <i class="fa-solid fa-chart-pie"></i>
            </button>

            <a id="csvDownloadBtn"
               href="{{ route('Organization.dailyLeave.csv', ['date'=>$date, 'branch'=>$selectedBranch, 'department'=>$selectedDepartment]) }}"
               class="icon-btn">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <button onclick="window.print()" class="icon-btn">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>

    </div>

    {{-- SUMMARY --}}
    <div class="cards-row">
        <div class="card"><div class="label">Present</div><div class="value">{{ $summary['present'] }}</div></div>
        <div class="card"><div class="label">Leave</div><div class="value">{{ $summary['leave'] }}</div></div>
        <div class="card"><div class="label">Holiday</div><div class="value">{{ $summary['holiday'] }}</div></div>
        <div class="card"><div class="label">Weekend</div><div class="value">{{ $summary['weekend'] }}</div></div>
        <div class="card"><div class="label">Absent</div><div class="value">{{ $summary['absent'] }}</div></div>
        <div class="card"><div class="label">Future</div><div class="value">{{ $summary['future'] }}</div></div>
    </div>

    {{-- TABLE --}}
    <div id="tableView" class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Emp ID</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            @foreach($results as $row)
                @php
                    $badgeClass = [
                        "Present" => "badge-present",
                        "Leave" => "badge-leave",
                        "Holiday" => "badge-holiday",
                        "Weekend" => "badge-weekend",
                        "Future" => "badge-future",
                        "Absent" => "badge-absent",
                    ][$row['status']];
                @endphp

                <tr>
                    <td>{{ $row['employee']->firstname }} {{ $row['employee']->lastname }}</td>
                    <td>{{ $row['employee']->employeeid }}</td>
                    <td>{{ $row['branch'] ?? 'N/A' }}</td>
                    <td>{{ $row['department'] ?? 'N/A' }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $row['status'] }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- CHART --}}
    <div id="chartContainer" class="chart-container">
        <h4>Status Distribution</h4>
        <div style="width:260px; height:260px; margin:auto;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function applyFilter() {
    let d = document.getElementById("filterDate").value;
    let b = document.getElementById("filterBranch").value;
    let dep = document.getElementById("filterDepartment").value;

    window.location = `?date=${d}&branch=${b}&department=${dep}`;
}

function switchView(view) {
    const table = document.getElementById("tableView");
    const chart = document.getElementById("chartContainer");

    document.getElementById("btnTable").classList.remove("primary");
    document.getElementById("btnChart").classList.remove("primary");

    if (view === "table") {
        table.style.display = "block";
        chart.style.display = "none";
        document.getElementById("btnTable").classList.add("primary");
    } else {
        table.style.display = "none";
        chart.style.display = "block";
        document.getElementById("btnChart").classList.add("primary");
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
        summary.absent,
        summary.future
    ];

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(document.getElementById("statusChart"), {
        type: 'pie',
        data: {
            labels: ["Present","Leave","Holiday","Weekend","Absent","Future"],
            datasets: [{
                data,
                backgroundColor: ["#16a34a","#dc2626","#f59e0b","#2563eb","#a21caf","#6b7280"]
            }]
        },
        options: { maintainAspectRatio: false }
    });
}

switchView("table");
</script>

@endsection
