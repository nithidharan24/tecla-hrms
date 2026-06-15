@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
* { font-family:'Inter', sans-serif; }

.report-container { background:#f4f6fb; padding:25px; }

.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:16px 22px;
    border-radius:12px;
    font-size:20px;
    font-weight:700;
}

/* Filters */
.controls {
    margin-top:20px;
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:20px;
    background:#fff;
    padding:18px;
    border-radius:14px;
    border:1px solid #e0e6f1;
}
.controls input, .controls select {
    padding:10px;
    border-radius:8px;
    border:1px solid #ccd3e1;
}

/* Buttons */
.btn-small {
    padding:9px 18px;
    border-radius:8px;
    font-size:14px;
    cursor:pointer;
    transition:.25s;
    font-weight:600;
}
.btn-primary { background:#ff6b3d; color:#fff; border:none; }
.btn-outline { background:#fff; border:1px solid #cbd5e1; color:#333; }

/* Table */
.table-wrapper {
    margin-top:20px;
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    width:100% !important;
    overflow-x:auto;
}
.time-table {
    width:100%;
    border-collapse:collapse;
}
.time-table th {
    background:#f0f4ff;
    padding:12px;
    color:#ff6b3d;
    font-weight:700;
    border-bottom:1px solid #e0e7ff;
}
.time-table td {
    padding:12px;
    border-bottom:1px solid #f1f5f9;
    font-weight:600;
}

/* Status badges */
.status-badge {
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    color:#fff;
    font-weight:700;
}
.status-late { background:#d9534f; }
.status-on_time { background:#28a745; }
.status-pending { background:#ffc107; color:#000; }
.status-no_schedule { background:#6c757d; }

</style>

<div class="report-container">

    <div class="header-bar">Team Scheduled vs Worked Report</div>

    <!-- FILTERS -->
    <div class="controls">
        <div style="display:flex; gap:15px; flex-wrap:wrap;">

            <div>
                <label>Employee</label>
                <select id="employeeFilter">
                    <option value="all">All Employees</option>
                    @foreach($team as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id?'selected':'' }}>
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
                <label>Project</label>
                <select id="projectFilter">
                    <option value="all">All</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $projectFilter==$p->id?'selected':'' }}>
                            {{ $p->projectname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Status</label>
                <select id="statusFilter">
                    <option value="all">All</option>
                    <option value="On Time" {{ $statusFilter=="On Time"?'selected':'' }}>On Time</option>
                    <option value="Late" {{ $statusFilter=="Late"?'selected':'' }}>Late</option>
                    <option value="Pending" {{ $statusFilter=="Pending"?'selected':'' }}>Pending</option>
                    <option value="No Schedule" {{ $statusFilter=="No Schedule"?'selected':'' }}>No Schedule</option>
                </select>
            </div>

            <button class="btn-small btn-primary" onclick="applyFilter()">Filter</button>
        </div>
        @if(isset($permissions) && $permissions->can_download)
        <div style="display:flex; align-items:center; gap:10px;">
            <button class="btn-small btn-outline" onclick="downloadCSV()">Export CSV</button>
            <button class="btn-small btn-outline" onclick="printReport()">Print</button>
        </div>
        @endif
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="time-table">
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
                @forelse($report as $row)
                <tr>
                    <td>{{ $row['employee'] }}</td>
                    <td>{{ $row['project'] }}</td>
                    <td>{{ $row['task'] }}</td>
                    <td>{{ $row['scheduled'] }}</td>
                    <td>{{ $row['worked'] }}</td>
                    <td>{{ $row['due_date'] ?? '-' }}</td>
                    <td>{{ $row['completed_date'] ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower(str_replace(' ','_',$row['status'])) }}">
                            {{ $row['status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px;">No data found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
function applyFilter() {
    const params = new URLSearchParams();

    params.set("employee_id", document.getElementById("employeeFilter").value);
    params.set("from", document.getElementById("fromDate").value);
    params.set("to", document.getElementById("toDate").value);
    params.set("project", document.getElementById("projectFilter").value);
    params.set("status", document.getElementById("statusFilter").value);

    window.location.search = params.toString();
}

// CSV Export
function downloadCSV() {
    const table = document.querySelector('table');
    let csv = "";

    for (let row of table.rows) {
        let cols = [...row.cells].map(td => `"${td.innerText}"`).join(",");
        csv += cols + "\n";
    }

    let a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type:"text/csv"}));
    a.download = "team-scheduled-vs-worked.csv";
    a.click();
}

// Print
function printReport() {
    let win = window.open("", "", "width=900,height=700");
    win.document.write(document.querySelector('.table-wrapper').outerHTML);
    win.print();
    win.close();
}
</script>

@endsection
