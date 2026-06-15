@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
* { font-family:'Inter', sans-serif; }

/* Container */
.report-container { background:#f5f7fb; padding:22px; }

/* Header */
.header-bar {
    background: linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:14px 18px;
    border-radius:10px;
    font-size:18px;
    font-weight:700;
    margin-bottom:16px;
}

/* Controls */
.controls {
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
    margin-bottom:16px;
}
.controls select, .controls input {
    padding:8px 12px;
    border-radius:6px;
    border:1px solid #d6dbe9;
}

/* Icon Buttons */
.icon-btn {
    width:40px; height:40px;
    display:flex; align-items:center; justify-content:center;
    border-radius:8px; cursor:pointer;
    background:#fff; border:1px solid #d6dbe9;
}
.icon-btn.primary {
    background:linear-gradient(90deg,#ff6b3d,#ff8557);
    border:none; color:white;
}

/* TABLE */
.table-wrapper {
    overflow-x:auto;
    background:#fff;
    border-radius:10px;
    border:1px solid #e6e9f2;
    padding-bottom:12px;
}
.status-table {
    width:100%;
    min-width:900px;
    border-collapse:collapse;
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
    border-bottom:1px solid #f3f4f8;
    font-size:14px;
    text-align:center;
}
.status-table tbody td:first-child {
    text-align:left;
    padding-left:18px;
    font-weight:700;
}

/* Status Badges */
.badge-status {
    padding:6px 12px;
    border-radius:18px;
    font-weight:700;
    color:white;
}
.status-late { background:#d9534f; }
.status-on_time { background:#28a745; }
.status-pending { background:#ffc107; color:black; }
.status-no_schedule { background:#6c757d; }

/* CHART AREA */
.chart-container {
    display:none;
    background:#fff;
    padding:20px;
    border-radius:10px;
    border:1px solid #e6e9f2;
    margin-top:20px;
}

/* Legend */
.legend-box {
    display:flex;
    gap:14px;
    margin-top:12px;
    flex-wrap:wrap;
}
.legend-item { display:flex; gap:8px; align-items:center; font-size:14px; }
.legend-dot { width:16px; height:12px; border-radius:3px; display:inline-block; }

/* PRINT */
@media print {
    .controls, .icon-btn { display:none !important; }
}
</style>

<div class="report-container">

    <div class="header-bar">Team Scheduled vs Worked Report</div>

    <!-- FILTER ROW -->
    <form method="get" class="controls">

        <!-- TEAM DROPDOWN -->
        <select name="employee_id" onchange="this.form.submit()">
            @foreach($team as $emp)
                <option value="{{ $emp->id }}"
                    {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                    {{ $emp->employeeid }} — {{ $emp->firstname }} {{ $emp->lastname }}
                </option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ $from }}">
        <input type="date" name="to" value="{{ $to }}">

        <!-- STATUS FILTER -->
        <select name="status">
            <option value="all">All Status</option>
            @foreach($allProjectStatuses as $s)
                <option value="{{ $s }}" {{ $statusFilter == $s ? 'selected':'' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>

        <!-- PROJECT FILTER -->
        <select name="project">
            <option value="all">All Projects</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ $projectFilter == $p->id ? 'selected':'' }}>
                    {{ $p->projectname }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="icon-btn primary">
            <i class="fa-solid fa-filter"></i>
        </button>

        <!-- VIEW SWITCH -->
        <button type="button" id="btnTable" class="icon-btn primary" onclick="switchView('table')">
            <i class="fa-solid fa-table-cells"></i>
        </button>
        <button type="button" id="btnChart" class="icon-btn" onclick="switchView('chart')">
            <i class="fa-solid fa-chart-column"></i>
        </button>
        @if(isset($permissions) && $permissions->can_download)
        <!-- EXPORT + PRINT -->
        <button type="button" class="icon-btn" onclick="downloadCSV()">
            <i class="fa-solid fa-file-csv"></i>
        </button>
        <button type="button" class="icon-btn" onclick="window.print()">
            <i class="fa-solid fa-print"></i>
        </button>
        @endif

    </form>

    <!-- HEADER INFO -->
    <p style="margin:6px 0 12px; font-weight:700;">
        {{ $team->where('id',$selectedEmployeeId)->first()->employeeid ?? '' }}
        —
        {{ $team->where('id',$selectedEmployeeId)->first()->firstname ?? '' }}
        {{ $team->where('id',$selectedEmployeeId)->first()->lastname ?? '' }}
        <span style="color:#666; margin-left:10px;">
            ({{ \Carbon\Carbon::parse($from)->format('d M Y') }} —
             {{ \Carbon\Carbon::parse($to)->format('d M Y') }})
        </span>
    </p>

    <!-- TABLE VIEW -->
    <div id="tableView" class="table-wrapper">
        <table class="status-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Scheduled Hours</th>
                    <th>Worked Hours</th>
                    <th>Due Date</th>
                    <th>Completed</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($report as $r)
                <tr>
                    <td>{{ $r['employee'] }}</td>
                    <td>{{ $r['project'] }}</td>
                    <td>{{ $r['task'] }}</td>
                    <td>{{ $r['scheduled'] }}</td>
                    <td>{{ $r['worked'] }}</td>
                    <td>{{ $r['due_date'] ?? '-' }}</td>
                    <td>{{ $r['completed_date'] ?? '-' }}</td>
                    <td>
                        @php $cls = strtolower(str_replace(' ','_',$r['status'])); @endphp
                        <span class="badge-status status-{{ $cls }}">
                            {{ $r['status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="8">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- CHART VIEW -->
    <div id="chartContainer" class="chart-container">

        <h4 style="margin-bottom:12px;">Charts</h4>

        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div style="flex:1 1 360px;">
                <h5>Status-wise Hours</h5>
                <canvas id="statusChart"></canvas>
            </div>

            <div style="flex:1 1 360px;">
                <h5>Project-wise Hours</h5>
                <canvas id="projectChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const projectTotals = @json($projectTotals);
const statusTotals  = @json($statusTotals);
const simpleRows    = @json($simpleReport);

// Convert minutes → hours
const toHours = mins => +(mins/60).toFixed(2);

// ------------------ STATUS CHART ------------------
const statusLabels = Object.keys(statusTotals);
const statusData   = statusLabels.map(k => toHours(statusTotals[k]));
const statusColors = ['#d9534f','#28a745','#ffc107','#6c757d'];

// ------------------ PROJECT CHART ------------------
const proj = Object.entries(projectTotals).sort((a,b)=>b[1]-a[1]).slice(0,12);
const projectLabels = proj.map(p => p[0]);
const projectData   = proj.map(p => toHours(p[1]));

const colors = ['#ff6b3d','#ffb84d','#7c4a03','#2a8f55','#2a9d8f','#6b46c1','#1e40af','#ef476f','#06d6a0','#118ab2','#f72585','#7209b7'];

// Render Charts
function renderCharts(){
    new Chart(document.getElementById('statusChart'), {
        type:'doughnut',
        data:{ labels:statusLabels, datasets:[{ data:statusData, backgroundColor:statusColors }] },
        options:{ responsive:true, plugins:{legend:{position:'bottom'}} }
    });

    new Chart(document.getElementById('projectChart'), {
        type:'bar',
        data:{ labels:projectLabels, datasets:[{ data:projectData, backgroundColor:colors }] },
        options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true}} }
    });
}

// ------------------ VIEW SWITCH ------------------
function switchView(v){
    if (v === 'table'){
        tableView.style.display='block';
        chartContainer.style.display='none';
        btnTable.classList.add('primary');
        btnChart.classList.remove('primary');
    } else {
        tableView.style.display='none';
        chartContainer.style.display='block';
        btnChart.classList.add('primary');
        btnTable.classList.remove('primary');
        renderCharts();
    }
}

// ------------------ CSV EXPORT ------------------
function downloadCSV(){
    let rows = [["Employee","Project","Task","Project Status","Start","End","Total"]];
    simpleRows.forEach(r => rows.push([
        r.employee, r.project, r.task, r.project_status, r.start, r.end, r.total
    ]));

    let csv = rows.map(r => r.join(",")).join("\n");

    let a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([csv], {type:"text/csv"}));
    a.download = "team_scheduled_vs_worked.csv";
    a.click();
}

switchView('table');
</script>

@endsection
