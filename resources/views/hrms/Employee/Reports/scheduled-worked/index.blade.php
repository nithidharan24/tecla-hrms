@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
* { font-family:'Inter', sans-serif; }

/* Container */
.report-container {
    background:#f4f6fb;
    padding:25px;
}

/* Header */
.header-bar {
    background:linear-gradient(90deg,#ff4800,#ff6b3d);
    color:#fff; padding:16px 22px;
    border-radius:12px; font-size:20px; font-weight:700;
}

/* Filters Section */
.controls {
    margin-top:20px; display:flex; justify-content:space-between;
    flex-wrap:wrap; gap:20px; background:#fff; padding:18px;
    border-radius:14px; border:1px solid #e0e6f1;
}
.controls input, .controls select {
    padding:10px; border-radius:8px; border:1px solid #ccd3e1;
}

/* Buttons */
.btn-small {
    padding:9px 18px; border-radius:8px;
    font-size:14px; cursor:pointer; transition:.25s; font-weight:600;
}
.btn-primary { background:#ff6b3d; color:#fff; border:none; }
.btn-outline { background:#fff; border:1px solid #cbd5e1; color:#333; }

/* Table */
.table-wrapper {
    margin-top:20px; background:#fff;
    border-radius:12px; border:1px solid #e2e8f0;
    width:100% !important; overflow-x:auto;
}
.time-table { width:100%; min-width:100%; border-collapse:collapse; }
.time-table th {
    background:#f0f4ff; padding:12px;
    color:#ff6b3d; font-weight:700; border-bottom:1px solid #e0e7ff;
}
.time-table td {
    padding:12px; border-bottom:1px solid #f1f5f9; font-weight:600;
}

/* Status badges */
.status-badge { padding:4px 8px; border-radius:10px; color:#fff; font-size:12px; }
.status-late { background:#d9534f; }
.status-on_time { background:#28a745; }
.status-pending { background:#ffc107; color:#000; }
.status-no_schedule { background:#6c757d; }

</style>

<div class="report-container">

    <div class="header-bar">Scheduled vs Worked Hours Report</div>

    <!-- FILTERS -->
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
                @foreach($report as $row)
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
                @endforeach
            </tbody>

        </table>
    </div>

</div>

<script>
// Apply filters
function applyFilter() {
    const params = new URLSearchParams();
    params.set("from", document.getElementById("fromDate").value);
    params.set("to", document.getElementById("toDate").value);
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
    a.download = "scheduled-vs-worked-report.csv";
    a.click();
}

// Print Report
function printReport() {
    let win = window.open("", "", "width=900,height=700");
    win.document.write(document.querySelector('.table-wrapper').outerHTML);
    win.print();
    win.close();
}
</script>

@endsection
