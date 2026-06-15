@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* Page Container */
.report-container {
    background: #f5f7fb;
    padding: 22px;
}

/* Header */
.header-bar {
    background: rgba(255, 55, 0, 0.8);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Controls Row */
.controls {
    display: flex;
    gap: 12px;
    align-items: center;
    margin: 18px 0;
    flex-wrap: wrap;
}

.controls select, 
.controls input {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #d6dbe9;
    background: #fff;
    font-size: 14px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.btn-primary { background: rgba(255, 55, 0, 0.8); color: #fff; }
.btn-outline { background: white; border: 1px solid #cdd6ed; color:#333; }

.right-actions { margin-left: auto; display: flex; gap: 8px; }

/* ICON BTNS (Table / Chart) */
.icon-btn {
    width: 42px;
    height: 42px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #d8dcec;
    cursor: pointer;
    font-size: 16px;
}

.icon-btn.primary {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color: white;
    border: none;
}

.icon-btn:hover { transform: translateY(-2px); }

/* Employee Card */
.employee-card {
    background: #fff;
    border-left: 4px solid rgba(255,55,0,0.8);
    padding: 16px 20px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    margin-bottom: 22px;
}

/* Summary Boxes */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin: 25px 0;
}
.summary-card {
    background: white;
    padding: 18px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.summary-card .value { font-size: 24px; font-weight: 700; }
.summary-card .label { font-size: 13px; color: #666; }

/* TABLE */
.table-wrapper {
    overflow-x: auto;
    background: white;
    border-radius: 10px;
    border: 1px solid #dde3ef;
    padding-bottom: 12px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.presence-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.presence-table thead th {
    background: #eef2fb;
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(255, 55, 0, 0.8);
    border-bottom: 2px solid #dce2f2;
}

.presence-table tbody td {
    padding: 12px 14px;
    font-size: 14px;
    border-bottom: 1px solid #f1f2f6;
}

/* Status Badge */
.status-badge {
    padding: 4px 12px;
    border-radius: 18px;
    font-size: 12px;
    font-weight: 600;
}

.badge-present { background: #d1fae5; color: #065f46; }
.badge-absent { background: #fee2e2; color: #991b1b; }
.badge-holiday { background: #fef3c7; color: #92400e; }
.badge-weekend { background: #dbeafe; color: #1e40af; }
.badge-future { background: #e5e7eb; color:#6b7280; }

/* Chart Container */
.chart-container {
    display: none;
    background: white;
    margin-top: 20px;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #dde3ef;
}

/* Print */
@media print {
    .controls, .header-bar, .icon-btn, a, select, button { display:none !important; }
}
</style>

<div class="report-container">

    <div class="header-bar">Team Presence Hours Report</div>

    {{-- ========== FILTERS + TABLE/CHART BUTTONS ========== --}}
    <form method="get" class="controls">

        <label>
            Employee:
            <select name="employee_id" onchange="this.form.submit()">
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected' : '' }}>
                        {{ $emp->employeeid }} - {{ $emp->firstname }} {{ $emp->lastname }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Period:
            <select name="filter" onchange="this.form.submit()">
                <option value="last7"      {{ $filter=='last7'?'selected':'' }}>Last 7 Days</option>
                <option value="this_week"  {{ $filter=='this_week'?'selected':'' }}>This Week</option>
                <option value="last_week"  {{ $filter=='last_week'?'selected':'' }}>Last Week</option>
                <option value="this_month" {{ $filter=='this_month'?'selected':'' }}>This Month</option>
            </select>
        </label>

        <div class="right-actions">

            <!-- TABLE VIEW -->
            <button type="button" id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table View">
                <i class="fa-solid fa-table-cells"></i>
            </button>

            <!-- CHART VIEW -->
            <button type="button" id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart View">
                <i class="fa-solid fa-chart-column"></i>
            </button>

<a href="{{ route('team.weekly.csv') }}" class="icon-btn" title="Download CSV">
    <i class="fa-solid fa-file-csv"></i>
</a>


<!-- PRINT -->
<button type="button" onclick="window.print()" class="icon-btn" title="Print Report">
    <i class="fa-solid fa-print"></i>
</button>

        </div>
    </form>

    {{-- EMPLOYEE CARD --}}
    <div class="employee-card">
        <div>
            <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong><br>
            <small>ID: {{ $employee->employeeid }}</small>
        </div>
        <div>
            <strong>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</strong><br>
            <small>{{ $filter }}</small>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-grid">
        <div class="summary-card"><div class="label">Total Hours</div><div class="value">{{ $totalHours }}</div></div>
        <div class="summary-card"><div class="label">Payable Hours</div><div class="value">{{ $totalPayable }}</div></div>
        <div class="summary-card"><div class="label">Present Hours</div><div class="value">{{ $presentHours }}</div></div>
        <div class="summary-card"><div class="label">Holiday Hours</div><div class="value">{{ $holidayHours }}</div></div>
        <div class="summary-card"><div class="label">Weekend Hours</div><div class="value">{{ $weekendHours }}</div></div>
    </div>

    {{-- ========== TABLE VIEW ========== --}}
    <div id="tableView" class="table-wrapper">
        <table class="presence-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>First In</th>
                    <th>Last Out</th>
                    <th>Total Hours</th>
                    <th>Payable Hours</th>
                    <th>Status</th>
                    <th>Shift</th>
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $r)
                    @php
                        $d = \Carbon\Carbon::parse($r['date_raw']);
                        $badge = match($r['status']) {
                            'Present' => 'badge-present',
                            'Absent' => 'badge-absent',
                            'Holiday' => 'badge-holiday',
                            'Weekend' => 'badge-weekend',
                            default => 'badge-future'
                        };
                    @endphp

                    <tr>
                        <td>{{ $d->format('d M Y') }}</td>
                        <td>{{ $d->format('D') }}</td>
                        <td>{{ $r['first_in'] }}</td>
                        <td>{{ $r['last_out'] }}</td>
                        <td>{{ $r['total_hours'] }}</td>
                        <td>{{ $r['payable_hours'] }}</td>
                        <td><span class="status-badge {{ $badge }}">{{ $r['status'] }}</span></td>
                        <td>{{ $r['shift'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ========== CHART VIEW ========== --}}
    <div id="chartContainer" class="chart-container">
        <h4>Presence Trend Chart</h4>
        <canvas id="presenceChart" height="120"></canvas>
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
    const rows = @json($rows);

    const labels = rows.map(r => r.date_raw);
    const totals = rows.map(r => parseFloat(r.total_hours.replace(':','.')));

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(document.getElementById("presenceChart"), {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label: "Total Hours",
                data: totals,
                backgroundColor: "#ff6b3d",
            }]
        }
    });
}

switchView("table");
</script>

@endsection
