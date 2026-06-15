
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* -------------------------------------------------
   Page layout and header
-------------------------------------------------*/
.report-page { background:#f8f9fc; padding:22px; }
.header-bar {
  background: linear-gradient(90deg,#ff6b2c,#ff4a1a);
  color:#fff; padding:18px 20px; border-radius:10px; font-weight:700; font-size:20px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  margin-bottom:18px;
}

/* Employee info strip */
.employee-strip {
  background: #fff; border-radius:10px; padding:14px 18px; display:flex; gap:18px; align-items:center;
  border:1px solid #e7e9ef; margin-bottom:16px;
}
.employee-strip .info { flex:1; }
.employee-strip h3 { margin:0; font-size:18px; color:#1f2937; font-weight:700; }
.employee-strip p { margin:4px 0 0; color:#6b7280; font-size:14px; }

/* Controls row */
.controls { display:flex; gap:12px; align-items:center; margin-bottom:18px; flex-wrap:wrap; }
.select-box { padding:10px 12px; border-radius:8px; border:1px solid #d6dbe9; background:#fff; font-size:14px; }
.btn { padding:10px 14px; border-radius:8px; border: none; cursor:pointer; font-weight:600; }
.btn-outline { background:#fff; border:1px solid #d6dbe9; color:#374151; }
.btn-primary { background:#ff6b2c; color:#fff; }

/* Cards row */
.cards-row { display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.card {
  background:#fff; padding:18px 20px; border-radius:12px; min-width:160px; flex:1;
  border:1px solid #eef2f7; box-shadow:0 6px 18px rgba(0,0,0,0.03);
}
.card .label { color:#6b7280; font-size:12px; text-transform:uppercase; letter-spacing:0.6px; }
.card .value { margin-top:8px; font-size:22px; font-weight:800; color:#111827; }

/* Main content layout */
.content-grid { display:flex; gap:18px; align-items:flex-start; flex-wrap:wrap; }
.left-panel { flex: 0 1 420px; min-width:300px; }
.right-panel { flex: 1 1 680px; min-width:420px; }

/* Chart container */
.chart-box { background:#fff; padding:16px; border-radius:12px; border:1px solid #eef2f7; }

/* Table styles */
.table-card { background:#fff; border-radius:12px; padding:10px; border:1px solid #eef2f7; overflow:auto; }
.table { width:100%; border-collapse:collapse; min-width:720px; }
.table thead th { background:#f1f6ff; padding:12px 14px; font-size:13px; color:#ff6b2c; text-align:left; font-weight:700; border-bottom:1px solid #e8eef8; }
.table tbody td { padding:12px 14px; border-bottom:1px solid #f3f5f8; color:#374151; font-weight:600; }

/* Legend */
.legend { background:#fff; border-radius:10px; padding:12px; margin-top:18px; border:1px solid #eef2f7; display:flex; gap:16px; flex-wrap:wrap; align-items:center; }

/* print */
@media print {
  .controls, .btn, .select-box, .csv-btn { display:none !important; }
}

/* Responsive */
@media (max-width:900px) {
  .content-grid { flex-direction:column; }
  .cards-row { flex-direction:column; }
}
</style>


<div class="report-page">

    <div class="header-bar"> Leave Balance</div>

    {{-- Employee / Branch Strip --}}
    <div class="employee-strip">
        <div class="info">
            @if(isset($employee))
                <h3>{{ $employee->firstname }} {{ $employee->lastname }}</h3>
                <p><strong>EMP {{ $employee->employeeid }}</strong> |
                Department: {{ $employee->department ?? 'N/A' }}</p>
            @else
                <h3>Select Employee</h3>
                <p>Please choose an employee to view leave summary.</p>
            @endif
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
            <a href="{{ route('Organization.LeaveBalance.csv', ['employee_id' => $selectedEmployeeId]) }}"
               class="btn btn-outline csv-btn">
                <i class="fa fa-file-csv"></i> CSV
            </a>

            <button class="btn btn-primary" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>


    {{-- Filters --}}
    <form method="get" class="controls">
        
        {{-- Branch Filter --}}
        <div>
            <label style="font-weight:700; color:#374151;">Branch</label>
            <select name="branch" class="select-box" onchange="this.form.submit()">
                <option value="all" {{ $selectedBranch == 'all' ? 'selected':'' }}>All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $b->id == $selectedBranch ? 'selected':'' }}>
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Employee Dropdown (Filtered by branch) --}}
        <div>
            <label style="font-weight:700; color:#374151;">Employee</label>
            <select name="employee_id" class="select-box" onchange="this.form.submit()">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected':'' }}>
                        {{ $emp->employeeid }} - {{ $emp->firstname }} {{ $emp->lastname }}
                    </option>
                @endforeach
            </select>
        </div>

    </form>


    {{-- Summary Cards --}}
    @php
        $sumTotal = array_sum(array_column($summary,'total'));
        $sumUsed  = array_sum(array_column($summary,'used'));
    @endphp

    <div class="cards-row">
        <div class="card"><div class="label">Total</div><div class="value">{{ $sumTotal }}</div></div>
        <div class="card"><div class="label">Used</div><div class="value">{{ $sumUsed }}</div></div>
        <div class="card"><div class="label">Remaining</div><div class="value">{{ $sumTotal - $sumUsed }}</div></div>
    </div>


    {{-- GRID: Chart + Table --}}
    <div class="content-grid">

        {{-- LEFT: CHART --}}
        <div class="left-panel">
            <div class="chart-box">
                <h5 style="margin-bottom:10px; font-weight:700;">Leave Usage</h5>
                <canvas id="leaveUsageChart" height="230"></canvas>
            </div>

            <div class="legend">
                <div><span style="display:inline-block;width:36px;height:6px;background:#ff6b2c;border-radius:6px;margin-right:8px;"></span> Used</div>
                <div><span style="display:inline-block;width:36px;height:6px;background:#36a2eb;border-radius:6px;margin-right:8px;"></span> Remaining</div>
            </div>
        </div>


        {{-- RIGHT: TABLE --}}
        <div class="right-panel">
            <div class="table-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Total</th>
                            <th>Used</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($summary as $row)
                        <tr>
                            <td>{{ $row['type'] }}</td>
                            <td>{{ $row['total'] }}</td>
                            <td>{{ $row['used'] }}</td>
                            <td>{{ $row['remaining'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>


{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function () {
    const labels = {!! json_encode(array_column($summary, 'type')) !!};
    const used = {!! json_encode(array_column($summary, 'used')) !!};
    const remaining = {!! json_encode(array_column($summary, 'remaining')) !!};

    new Chart(document.getElementById('leaveUsageChart'), {
        type: "bar",
        data: {
            labels,
            datasets: [
                { label: "Used", data: used, backgroundColor: "#ff6b2c", borderRadius: 8 },
                { label: "Remaining", data: remaining, backgroundColor: "#36a2eb", borderRadius: 8 },
            ]
        },
        options: {
            plugins: { legend: { position: "bottom" } },
            responsive: true,
        }
    });
})();
</script>

@endsection
