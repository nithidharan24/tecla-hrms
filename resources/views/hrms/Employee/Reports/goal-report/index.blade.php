
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
* { font-family: 'Inter', sans-serif; }
.report-container { background:#f4f6fb; padding:25px; }
.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff;
    padding:16px;
    border-radius:12px;
    font-size:20px;
    font-weight:700;
}

.controls {
    margin-top:20px;
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:20px;
    background:#fff;
    padding:18px;
    border-radius:12px;
    border:1px solid #e0e6f1;
}

.controls input, .controls select {
    padding:10px;
    border-radius:8px;
    border:1px solid #ccd3e1;
}

.table-wrapper {
    margin-top:20px;
    background:#fff;
    border-radius:12px;
    border:1px solid #e2e8f0;
    overflow-x:auto;
}

.time-table { width:100%; border-collapse:collapse; }

.time-table th {
    background:#f0f4ff;
    padding:12px;
    color:#ff6b3d;
    font-weight:700;
}

.time-table td {
    padding:12px;
    border-bottom:1px solid #f1f5f9;
    font-weight:600;
}

.status-badge {
    padding:4px 8px;
    border-radius:10px;
    font-size:12px;
    font-weight:700;
    color:#fff;
}

.status-Not_Started { background:#6c757d; }
.status-In_Progress { background:#0d6efd; }
.status-On_Hold { background:#ffc107; color:#000; }
.status-Completed { background:#28a745; }
.status-Cancelled { background:#dc3545; }

.btn-primary {
    padding:10px 20px;
    background:#ff6b3d;
    color:#fff;
    border-radius:8px;
    font-weight:600;
    border:none;
}

.btn-outline {
    padding:10px 20px;
    background:#fff;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}
</style>

<div class="report-container">

    <div class="header-bar">My Goal Report</div>

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
                        <option value="{{ $s }}" {{ $statusFilter == $s ? 'selected':'' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn-primary" onclick="applyFilter()">Filter</button>
        </div>
        @if(isset($permissions) && $permissions->can_download)
        <!-- EXPORT & PRINT -->
        <div style="display:flex; gap:10px;">
            <button class="btn-outline" onclick="downloadCSV()">Export CSV</button>
            <button class="btn-outline" onclick="printReport()">Print</button>
        </div>
        @endif
    </div>

    <div class="table-wrapper" id="goalTable">
        <table class="time-table">
            <thead>
                <tr>
                    <th>Goal Title</th>
                    <th>Department</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Target</th>
                    <th>Progress (%)</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($goals as $g)
                <tr>
                    <td>{{ $g->goal_title }}</td>
                    <td>{{ $g->department_name ?? '-' }}</td>
                    <td>{{ $g->start_date }}</td>
                    <td>{{ $g->end_date }}</td>
                    <td>{{ $g->target_value }} {{ $g->unit }}</td>
                    <td>{{ $g->progress_percentage }}%</td>
                    <td>
                        <span class="status-badge status-{{ str_replace(' ','_',$g->status) }}">
                            {{ $g->status }}
                        </span>
                    </td>
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
    window.location.search = params.toString();
}

function downloadCSV() {
    const table = document.querySelector(".time-table");
    let csv = "";

    for (let row of table.rows) {
        let cols = [...row.cells].map(td => `"${td.innerText}"`).join(",");
        csv += cols + "\n";
    }

    const a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([csv], { type: "text/csv" }));
    a.download = "my-goals-report.csv";
    a.click();
}

function printReport() {
    let printWin = window.open("", "", "width=900,height=700");
    printWin.document.write(`
        <html>
        <head>
            <title>My Goal Report</title>
            <style>
                table { width:100%; border-collapse:collapse; margin-top:20px; }
                th, td { padding:10px; border:1px solid #ccc; font-size:14px; }
                th { background:#f0f0f0; }
            </style>
        </head>
        <body>
            <h2>My Goal Report</h2>
            ${document.getElementById("goalTable").innerHTML}
        </body>
        </html>
    `);

    printWin.print();
    printWin.close();
}
</script>

@endsection
