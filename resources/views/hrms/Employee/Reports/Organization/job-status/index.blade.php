
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { font-family: 'Inter', sans-serif; }

.report-container { background:#f4f6fb; padding:25px; }

.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff; padding:16px 22px;
    border-radius:12px; font-size:20px; font-weight:700;
}

/* CONTROLS */
.controls {
    margin-top:20px; display:flex; gap:18px; flex-wrap:wrap;
    background:#fff; padding:14px 18px; border-radius:14px;
    border:1px solid #e0e6f1; align-items:center;
}
.controls .filters { display:flex; gap:12px; flex-wrap:wrap; flex:1; }
.controls label { font-size:13px; font-weight:600; color:#333; }
.controls select, .controls input {
    padding:9px 10px; border-radius:8px; border:1px solid #ccd3e1;
    min-width:150px;
}

/* ACTION BUTTONS */
.actions { display:flex; gap:10px; }
.icon-btn {
    width:42px; height:42px; display:flex; align-items:center;
    justify-content:center; background:#fff; border-radius:10px;
    border:1px solid #e6edf7; cursor:pointer; font-size:16px;
}
.icon-btn.primary { background:linear-gradient(90deg,#ff6b3d,#ff8557); color:#fff; border:none; }
.icon-btn:hover { transform:translateY(-2px); }

/* TABLE */
.table-wrapper {
    margin-top:20px; background:#fff; padding:16px;
    border-radius:12px; border:1px solid #e2e8f0; overflow-x:auto;
}
.table-report { width:100%; border-collapse:collapse; }
.table-report thead th {
    background:#f9f9ff; color:#ff6b3d; padding:14px;
    font-weight:700; text-align:center; border-bottom:1px solid #eaefff;
}
.table-report tbody td {
    padding:14px; border-bottom:1px dashed #eee;
    font-weight:600; text-align:center;
}

/* STATUS BADGES */
.status-badge {
    padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600;
    text-transform:capitalize;
}
.status-completed { background:#22c55e; color:#fff; }
.status-pending { background:#facc15; color:#000; }
.status-in_progress { background:#0ea5e9; color:#fff; }
.status-no_status { background:#9ca3af; color:#fff; }

/* CHART AREA */
.chart-container {
    display:none; background:#fff; padding:20px;
    border-radius:12px; margin-top:20px; border:1px solid #e2e8f0;
}
.chart-row {
    display:flex; flex-wrap:wrap; gap:40px;
    justify-content:center; margin-bottom:40px;
}
.chart-box { width:300px; text-align:center; }
.chart-box h6 { font-weight:700; margin-bottom:10px; }

.bar-box { width:900px; margin:auto; text-align:center; }
</style>


<div class="report-container">

    <div class="header-bar">Organization — Job Status Report</div>

   <!-- CONTROLS -->
<div class="controls">
    <div style="display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; flex: 1;">
        <div style="display: flex; flex-direction: column;">
            <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 4px;">Branch</label>
            <select id="branchFilter" style="width: 160px;">
                <option value="all">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branch == $b->id ? 'selected':'' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; flex-direction: column;">
            <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 4px;">Department</label>
            <select id="deptFilter" style="width: 170px;">
                <option value="all">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ $department == $d->id ? 'selected':'' }}>{{ $d->department }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 12px;">
            <div style="display: flex; flex-direction: column;">
                <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 4px;">From</label>
                <input type="date" id="fromDate" value="{{ $from }}" style="width: 150px;">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 4px;">To</label>
                <input type="date" id="toDate" value="{{ $to }}" style="width: 150px;">
            </div>
        </div>

        <div style="display: flex; flex-direction: column;">
            <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 4px;">Status</label>
            <select id="statusFilter" style="width: 150px;">
                <option value="all">All</option>
                @foreach($allStatuses as $s)
                    <option value="{{ $s }}" {{ $statusFilter==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        <button onclick="applyFilter()" style="height: 42px; padding: 0 24px; background: #ff6b3d; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
            Apply Filter
        </button>
    </div>
    
    @if(isset($permissions) && $permissions->can_download)
    <div class="actions">
        <button id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View"><i class="fa-solid fa-table-cells"></i></button>
        <button id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View"><i class="fa-solid fa-chart-column"></i></button>

        <a class="icon-btn" href="{{ route('organization.jobstatus.csv', request()->all()) }}" title="CSV Export">
            <i class="fa-solid fa-file-csv"></i>
        </a>

        <button class="icon-btn" onclick="window.print()" title="Print"><i class="fa-solid fa-print"></i></button>
    </div>
    @endif
</div>


    <!-- TABLE VIEW -->
    <div id="tableView" class="table-wrapper">
        <table class="table-report">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Branch</th>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($simpleReport as $r)
                <tr>
                    <td>{{ $r['employee'] }}</td>
                    <td>{{ $r['branch'] }}</td>
                    <td>{{ $r['project'] }}</td>
                    <td>{{ $r['task'] }}</td>

                    <td>
                        <span class="status-badge status-{{ str_replace(' ','_',$r['status']) }}">
                            {{ ucfirst($r['status']) }}
                        </span>
                    </td>

                    <td>{{ $r['start_full'] }}</td>
                    <td>{{ $r['end_full'] }}</td>
                    <td>{{ $r['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- CHART VIEW -->
    <div id="chartContainer" class="chart-container">

        <!-- PIE CHARTS -->
        <div class="chart-row">

            <div class="chart-box">
                <h6>Tasks Distribution</h6>
                <canvas id="taskPie"></canvas>
            </div>

            <div class="chart-box">
                <h6>Project Working Hours</h6>
                <canvas id="projectPie"></canvas>
            </div>

           

        </div>

        <!-- BAR CHART -->
        <div class="bar-box">
            <h6>Employee Working Hours (Bar Chart)</h6>
            <canvas id="employeeBar"></canvas>
        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

function applyFilter() {
    const p = new URLSearchParams();
    p.set("branch", document.getElementById("branchFilter").value);
    p.set("department", document.getElementById("deptFilter").value);
    p.set("from", document.getElementById("fromDate").value);
    p.set("to", document.getElementById("toDate").value);
    p.set("status", document.getElementById("statusFilter").value);
    window.location.search = p.toString();
}

function switchView(v) {
    const table = document.getElementById("tableView");
    const chart = document.getElementById("chartContainer");
    const btnT = document.getElementById("btnTable");
    const btnC = document.getElementById("btnChart");

    if (v == "table") {
        table.style.display = "block";
        chart.style.display = "none";
        btnT.classList.add("primary"); btnC.classList.remove("primary");
    } else {
        table.style.display = "none";
        chart.style.display = "block";
        btnC.classList.add("primary"); btnT.classList.remove("primary");
        renderCharts();
    }
}

let pie1, pie2, pie3, bar1;
function renderCharts() {

    const report = @json($simpleReport);

    /* -------- PIE 1: TASK COUNT -------- */
    const taskCount = {};
    report.forEach(r => taskCount[r.task] = (taskCount[r.task] || 0) + 1);

    /* -------- PIE 2: PROJECT HOURS -------- */
    const projectHours = {};
    report.forEach(r => {
        const h = parseInt(r.total.split(":")[0]);
        projectHours[r.project] = (projectHours[r.project] || 0) + h;
    });

    /* -------- PIE 3: EMPLOYEE HOURS -------- */
    const empHours = {};
    report.forEach(r => {
        const h = parseInt(r.total.split(":")[0]);
        empHours[r.employee] = (empHours[r.employee] || 0) + h;
    });

    /* Destroy old charts */
    if (pie1) pie1.destroy();
    if (pie2) pie2.destroy();
    if (pie3) pie3.destroy();
    if (bar1) bar1.destroy();

    /* PIE CHART 1 */
    pie1 = new Chart(document.getElementById("taskPie"), {
        type: "pie",
        data: {
            labels: Object.keys(taskCount),
            datasets: [{
                data: Object.values(taskCount),
                backgroundColor:["#ff6b3d","#ff8c66","#ffa07d","#ffd0c2","#ffad80","#ff7f56"]
            }]
        }
    });

    /* PIE CHART 2 */
    pie2 = new Chart(document.getElementById("projectPie"), {
        type: "pie",
        data: {
            labels: Object.keys(projectHours),
            datasets: [{
                data: Object.values(projectHours),
                backgroundColor:["#2563eb","#3b82f6","#60a5fa","#93c5fd","#bfdbfe","#e0f2fe"]
            }]
        }
    });

    /* PIE CHART 3 */
    pie3 = new Chart(document.getElementById("employeePie"), {
        type: "pie",
        data: {
            labels: Object.keys(empHours),
            datasets: [{
                data: Object.values(empHours),
                backgroundColor:["#16a34a","#22c55e","#4ade80","#86efac","#bbf7d0","#dcfce7"]
            }]
        }
    });

    /* ------- BAR CHART (EMPLOYEE HOURS) ------- */
    bar1 = new Chart(document.getElementById("employeeBar"), {
        type: "bar",
        data: {
            labels: Object.keys(empHours),
            datasets: [{
                label:"Total Working Hours",
                data: Object.values(empHours),
                backgroundColor:"#ff6b3d",
                borderRadius: 6
            }]
        },
        options:{
            responsive:true,
            plugins:{ legend:{ display:false }},
            scales:{ y:{ beginAtZero:true }}
        }
    });
}

/* DEFAULT VIEW */
switchView("table");
</script>
<style>
/* Add to your existing styles */

.report-container { background:#f4f6fb; padding:25px; }

.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff; padding:16px 22px;
    border-radius:12px; font-size:20px; font-weight:700;
}

/* CONTROLS */
.controls {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    background: #fff;
    padding: 18px 22px;
    border-radius: 14px;
    border: 1px solid #e0e6f1;
}

.controls select, 
.controls input {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccd3e1;
    font-size: 14px;
    background-color: #fff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.controls select:focus,
.controls input:focus {
    outline: none;
    border-color: #ff6b3d;
    box-shadow: 0 0 0 3px rgba(255, 107, 61, 0.1);
}

.controls label {
    font-size: 13px;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 4px;
    display: block;
}

/* ACTION BUTTONS */
.actions { 
    display: flex; 
    gap: 10px; 
    align-items: center;
}

.icon-btn {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e6edf7;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s ease;
    text-decoration: none;
    color: #333;
}

.icon-btn.primary {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color: #fff;
    border: none;
}

.icon-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* TABLE */
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
}

.table-report thead th {
    background:#f9f9ff;
    color:#ff6b3d;
    padding:14px;
    font-weight:700;
    text-align:center;
    border-bottom:1px solid #eaefff;
}

.table-report tbody td {
    padding:14px;
    border-bottom:1px dashed #eee;
    font-weight:600;
    text-align:center;
}

/* STATUS BADGES */
.status-badge {
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    text-transform:capitalize;
    display: inline-block;
}
.status-completed { background:#22c55e; color:#fff; }
.status-pending { background:#facc15; color:#000; }
.status-in_progress { background:#0ea5e9; color:#fff; }
.status-no_status { background:#9ca3af; color:#fff; }

/* CHART AREA */
.chart-container {
    display:none;
    background:#fff;
    padding:20px;
    border-radius:12px;
    margin-top:20px;
    border:1px solid #e2e8f0;
}
.chart-row {
    display:flex;
    flex-wrap:wrap;
    gap:40px;
    justify-content:center;
    margin-bottom:40px;
}
.chart-box {
    width:300px;
    text-align:center;
}
.chart-box h6 {
    font-weight:700;
    margin-bottom:10px;
}
.bar-box {
    width:900px;
    margin:auto;
    text-align:center;
}
</style>
@endsection
