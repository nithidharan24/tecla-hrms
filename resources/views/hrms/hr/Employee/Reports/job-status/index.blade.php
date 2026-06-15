@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* same styling from your timelog report */
* { font-family: 'Inter', sans-serif; }
.report-container { background:#f4f6fb; padding:25px; }
.header-bar { background:linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff;
    padding:16px 22px; border-radius:12px; font-size:20px; font-weight:700; }
.controls { margin-top:20px; display:flex; justify-content:space-between; flex-wrap:wrap;
    gap:20px; background:#fff; padding:18px; border-radius:14px;
    border:1px solid #e0e6f1; }
.controls input, .controls select { padding:10px; border-radius:8px;
    border:1px solid #ccd3e1; }
.table-wrapper { margin-top:20px; background:#fff; border-radius:12px;
    border:1px solid #e2e8f0; overflow-x:auto; }
.time-table th { background:#f0f4ff; padding:12px; color:#ff6b3d;
    font-weight:700; border-bottom:1px solid #e0e7ff; }
.time-table td { padding:12px; border-bottom:1px solid #f1f5f9; font-weight:600; }
.status-badge { padding:4px 8px; border-radius:10px; color:#fff; font-size:12px; }
.status-completed { background:#28a745; }
.status-pending { background:#ffc107; color:#000; }
.status-in_progress { background:#17a2b8; }
.status-no_status { background:#6c757d; }
.btn-small { padding:9px 18px; border-radius:8px; font-size:14px; cursor:pointer; transition:.25s; font-weight:600; }
.btn-primary { background:#ff6b3d; color:#fff; border:none; box-shadow:0 3px 10px rgba(79,70,229,0.35); }
.btn-outline { background:#fff; border:1px solid #cbd5e1; color:#333; }
</style>
<style>
.table-wrapper {
    margin-top:20px;
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    width:100% !important;
    overflow-x:auto;
}

.time-table {
    width:100% !important;
    min-width:100% !important;
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
</style>


<div class="report-container">
    <div class="header-bar">Job Status Report</div>

    <div class="controls">
        <div style="display:flex; gap:15px; flex-wrap:wrap;">
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
                        <option value="{{ $s }}" {{ $statusFilter==$s ? 'selected':'' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Project</label>
                <select id="projectFilter">
                    <option value="all">All Projects</option>
                    @foreach($allProjects as $p)
                        <option value="{{ $p->id }}" {{ $projectFilter==$p->id ? 'selected':'' }}>
                            {{ $p->projectname }}
                        </option>
                    @endforeach
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

    <!-- TABLE VIEW ONLY -->
    <div class="table-wrapper">
        <table class="time-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Total Hours</th>
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
                        <td>{{ $row['start_time'] }}</td>
                        <td>{{ $row['end_time'] }}</td>
                        <td>{{ $row['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function applyFilter() {
    const params = new URLSearchParams();
    params.set("from", document.getElementById("fromDate").value);
    params.set("to", document.getElementById("toDate").value);
    params.set("status", document.getElementById("statusFilter").value);
    params.set("project", document.getElementById("projectFilter").value);
    window.location.search = params.toString();
}

function downloadCSV() {
    const table = document.querySelector('table');
    let csv = "";
    for (let row of table.rows) {
        let cols = [...row.cells].map(td => `"${td.innerText}"`).join(",");
        csv += cols + "\n";
    }
    let a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type: "text/csv"}));
    a.download = "job-status-report.csv";
    a.click();
}

function printReport() {
    let win = window.open("", "", "width=900,height=700");
    win.document.write(document.querySelector('.table-wrapper').innerHTML);
    win.print();
    win.close();
}
</script>

@endsection
