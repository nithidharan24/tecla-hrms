@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-pQfmY...REPLACEWITHACTUALHASH..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
* { font-family: 'Inter', sans-serif; }
.report-container { background:#f4f6fb; padding:25px; }
.header-bar { background:linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff;
    padding:16px 22px; border-radius:12px; font-size:20px; font-weight:700; }

/* controls: left (filters) + right (icons) in single row */
.controls {
    margin-top:20px;
    display:flex;
    gap:18px;
    background:#fff;
    padding:14px 18px;
    border-radius:14px;
    border:1px solid #e0e6f1;
    align-items:center;
    flex-wrap:wrap;
}

/* left area stretches */
.controls .filters {
    display:flex;
    gap:12px;
    align-items:center;
    flex:1 1 auto; /* grow */
    min-width: 280px;
    flex-wrap:wrap;
}

/* each filter item */
.controls .filters > div {
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:120px;
}

/* right area stays compact */
.controls .actions {
    display:flex;
    gap:10px;
    align-items:center;
    justify-content:flex-end;
    flex:0 0 auto;
}

/* inputs/selects */
.controls input, .controls select {
    padding:9px 10px;
    border-radius:8px;
    border:1px solid #ccd3e1;
    min-width: 140px;
}

/* text label smaller */
.controls label { font-size:13px; color:#333; font-weight:600; }

/* icon button style */
.icon-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:42px;
    height:42px;
    border-radius:10px;
    border:1px solid transparent;
    background:#fff;
    cursor:pointer;
    box-shadow: 0 1px 0 rgba(0,0,0,0.02);
    font-size:16px;
}
.icon-btn:hover { transform:translateY(-1px); }

/* primary icon (table active) */
.icon-btn.primary {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color:#fff;
    border-color: transparent;
}

/* outline icon */
.icon-btn.outline {
    background:#fff;
    color:#333;
    border:1px solid #e6edf7;
}

/* active state for outline icons */
.icon-btn.active {
    border-color:#ff6b3d;
    box-shadow: 0 4px 12px rgba(255,107,61,0.12);
}

/* small labeled button fallback */
.btn-small { padding:8px 14px; border-radius:8px; font-size:14px; cursor:pointer; font-weight:600; }
.btn-primary { background:#ff6b3d; color:#fff; border:none; }
.btn-outline { background:#fff; border:1px solid #cbd5e1; color:#333; }

/* table + chart containers */
.table-wrapper {
    margin-top:20px;
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    overflow-x:auto;
    width:100%;
}
.time-table { width:100%; border-collapse:collapse; }
.time-table th { background:#f0f4ff; padding:12px; color:#ff6b3d; font-weight:700; border-bottom:1px solid #e0e7ff; }
.time-table td { padding:12px; border-bottom:1px solid #f1f5f9; font-weight:600; }
.status-badge { padding:4px 8px; border-radius:10px; color:#fff; font-size:12px; }
.status-completed { background:#28a745; }
.status-pending { background:#ffc107; color:#000; }
.status-in_progress { background:#17a2b8; }
.status-no_status { background:#6c757d; }

/* responsive tweaks */
@media (max-width:900px) {
    .controls .filters > div { min-width: 140px; }
    .controls input, .controls select { min-width: 120px; }
}
</style>

<div class="report-container">
    <div class="header-bar">Team Job Status Report</div>

    {{-- FILTER + ICON ACTIONS IN SAME ROW --}}
    <div class="controls">

        <div class="filters">
            <div>
                <label>Employee</label>
                <select id="employeeFilter">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected':'' }}>
                            {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>From</label>
                <input type="date" id="fromDate" value="{{ $from }}">
            </div>

            <div>
                <label>To</label>
                <input type="date" id="toDate" value="{{ $to }}">
            </div>

            <div>
                <label>Status</label>
                <select id="statusFilter">
                    <option value="all">All</option>
                    @foreach($allStatuses as $s)
                        <option value="{{ $s }}" {{ $statusFilter==$s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; align-items:flex-end; gap:8px;">
                <button class="btn-small btn-primary" onclick="applyFilter()">Filter</button>
            </div>
        </div>

        <div class="actions" role="toolbar" aria-label="Report actions">
            <!-- Table icon (default active) -->
            <button id="btnTable" class="icon-btn primary" title="Table view" aria-pressed="true" onclick="switchTo('table')">
                <i class="fa-solid fa-table-cells"></i>
            </button>

            <!-- Chart icon -->
            <button id="btnChart" class="icon-btn outline" title="Chart view" aria-pressed="false" onclick="switchTo('chart')">
                <i class="fa-solid fa-chart-column"></i>
            </button>
            @if(isset($permissions) && $permissions->can_download)
            <!-- Export CSV (icon) -->
            <button class="icon-btn outline" title="Export CSV" onclick="downloadCSV()">
                <i class="fa-solid fa-file-csv"></i>
            </button>

            <!-- Print (icon) -->
            <button class="icon-btn outline" title="Print" onclick="printReport()">
                <i class="fa-solid fa-print"></i>
            </button>
            @endif
        </div>
    </div>

    {{-- TABLE VIEW --}}
    <div class="table-wrapper" id="tableView">
        <table class="time-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Start Date & Time</th>
                    <th>End Date & Time</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($simpleReport as $row)
                <tr>
                    <td>{{ $row['project'] }}</td>
                    <td>{{ $row['task'] }}</td>

                    <td>
                        <span class="status-badge status-{{ str_replace(' ','_',$row['status']) }}">
                            {{ ucfirst($row['status']) }}
                        </span>
                    </td>

                    <td>{{ $row['start_full'] }}</td>
                    <td>{{ $row['end_full'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CHART VIEW --}}
    <div id="chartContainer" style="display:none; margin-top:20px; background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
        <canvas id="barChart" height="120"></canvas>
    </div>

</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* basic functions (filter/export/print) */
function applyFilter() {
    const params = new URLSearchParams();
    params.set("employee_id", document.getElementById("employeeFilter").value);
    params.set("from", document.getElementById("fromDate").value);
    params.set("to", document.getElementById("toDate").value);
    params.set("status", document.getElementById("statusFilter").value);
    params.set("project", "all");
    window.location.search = params.toString();
}

function downloadCSV() {
    const table = document.querySelector('#tableView table');
    if (!table) return;
    let csv = "";
    for (let row of table.rows) {
        let cols = [...row.cells].map(td => `"${td.innerText.replace(/\n/g,' ').trim()}"`).join(",");
        csv += cols + "\n";
    }
    let a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type: "text/csv"}));
    a.download = "team-job-status-report.csv";
    a.click();
}

function printReport() {
    const html = document.getElementById('tableView').innerHTML;
    let win = window.open("", "", "width=900,height=700");
    win.document.write('<html><head><title>Print</title></head><body>');
    win.document.write(html);
    win.document.write('</body></html>');
    win.print();
    win.close();
}

/* view switching + icon active state */
function switchTo(view) {
    const tableView = document.getElementById('tableView');
    const chartView = document.getElementById('chartContainer');

    const btnTable = document.getElementById('btnTable');
    const btnChart = document.getElementById('btnChart');

    if (view === 'table') {
        tableView.style.display = 'block';
        chartView.style.display = 'none';
        btnTable.classList.add('primary');
        btnTable.classList.remove('outline');
        btnTable.classList.remove('active');
        btnChart.classList.remove('primary');
        btnChart.classList.add('outline');
        btnChart.classList.remove('active');
        btnTable.setAttribute('aria-pressed','true');
        btnChart.setAttribute('aria-pressed','false');
    } else {
        tableView.style.display = 'none';
        chartView.style.display = 'block';
        btnChart.classList.add('primary');
        btnChart.classList.remove('outline');
        btnChart.classList.remove('active');
        btnTable.classList.remove('primary');
        btnTable.classList.add('outline');
        btnTable.classList.remove('active');
        btnChart.setAttribute('aria-pressed','true');
        btnTable.setAttribute('aria-pressed','false');
        renderBarChart();
    }
}

/* initial state */
document.addEventListener('DOMContentLoaded', function(){
    switchTo('table');
});

/* chart rendering */
let chart;
function renderBarChart() {
    if (chart) chart.destroy();

    const labels = [
        @foreach($simpleReport as $r)
            {!! json_encode($r['task']) !!},
        @endforeach
    ];

    const totals = [
        @foreach($simpleReport as $r)
            {{ intval(explode(':', $r['total'])[0]) }},
        @endforeach
    ];

    const ctx = document.getElementById('barChart').getContext('2d');

    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Working Hours",
                data: totals,
                backgroundColor: "#ff6b3d",
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}
</script>

@endsection
