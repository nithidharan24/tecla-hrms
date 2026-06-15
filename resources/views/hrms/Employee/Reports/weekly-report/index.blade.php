@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
/* (The CSS is the same improved block you showed earlier) */
* { font-family: 'Inter', sans-serif; }
.report-container { background: #f4f6fb; padding: 25px; }
.header-bar { background: linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff; padding:16px 22px; border-radius:12px; font-size:20px; font-weight:700; box-shadow:0 4px 15px rgba(255,80,0,0.25); }
.controls { margin-top:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px; padding:18px; background:#fff; border-radius:14px; border:1px solid #e0e6f1; box-shadow:0 4px 12px rgba(0,0,0,0.05); }
.controls input, .controls select { padding:10px 12px; border:1px solid #ccd3e1; font-size:14px; border-radius:8px; background:#fafbff; transition:.25s; }
.btn-small { padding:9px 18px; border-radius:8px; font-size:14px; cursor:pointer; transition:.25s; font-weight:600; }
.btn-primary { background:#ff6b3d; color:#fff; border:none; box-shadow:0 3px 10px rgba(79,70,229,0.35); }
.btn-outline { background:#fff; border:1px solid #cbd5e1; color:#333; }
.view-tabs { display:flex; gap:12px; }
.view-tabs button { border:2px solid #d1d5db; background:#fff; padding:4px 14px; border-radius:10px; font-size:20px; cursor:pointer; color:#6b7280; transition:all .25s; }
.view-tabs .active { background:#ff6b3d; border-color:#ff6b3d; color:#fff; transform:translateY(-2px); box-shadow:0 4px 12px rgba(79,70,229,0.4); }
.table-wrapper { margin-top:20px; background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow-x:auto; box-shadow:0 4px 15px rgba(0,0,0,0.06); }
.time-table { width:100%; min-width:800px; border-collapse:collapse; }
.time-table th { background:#f0f4ff; padding:12px; font-size:13px; font-weight:700; color:#ff6b3d; border-bottom:1px solid #e0e7ff; text-align:left; }
.time-table td { padding:12px; border-bottom:1px solid #f1f5f9; font-size:14px; font-weight:600; color:#333; vertical-align:middle; }
.time-table tbody tr:hover { background:#fafaff; }
.chart-container { background:#fff; padding:25px; margin-top:22px; border-radius:14px; border:1px solid #e5e7eb; box-shadow:0 4px 14px rgba(0,0,0,0.06); }
.chart-title { font-size:18px; font-weight:700; margin-bottom:12px; color:#333; }
</style>

<div class="report-container">
    <div class="header-bar">Time Log Report</div>

    <div class="controls">
        <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
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
        <div style="display:flex; align-items:center; gap:15px;">
            <div class="view-tabs">
                <button id="tableBtn" class="active" title="Table View"><i class="fa-solid fa-table"></i></button>
                <button id="chartBtn" title="Chart View"><i class="fa-solid fa-chart-pie"></i></button>
            </div>
            <button class="btn-small btn-outline" onclick="printReport()">Print</button>
            <button class="btn-small btn-outline" onclick="downloadCSV()">Export CSV</button>
        </div>
        @endif
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView">
        <div class="table-wrapper">
            <table class="time-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Project</th>
                        <th>Task</th>
                        @foreach ($dates as $d)
                            <th>{{ \Carbon\Carbon::parse($d)->format('M d') }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $prevEmployee = null;
                @endphp

                @foreach ($report as $empId => $empData)
                    @php
                        // track if employee name printed for first task row
                        $employeeShown = false;
                        // compute employee overall minutes
                        $employeeTotalMins = 0;
                        // precompute totals (in minutes) across projects to show as summary row after employee tasks
                        foreach ($empData['projects'] as $proj) {
                            foreach ($proj['tasks'] as $task) {
                                list($th,$tm) = explode(':', $task['total']);
                                $employeeTotalMins += ($th*60) + $tm;
                            }
                        }
                        $employeeOverall = sprintf('%02d:%02d', floor($employeeTotalMins/60), $employeeTotalMins%60);
                    @endphp

                    @foreach ($empData['projects'] as $proj)
                        @foreach ($proj['tasks'] as $task)
                            <tr>
                                <td style="width:220px;">
                                    @if (!$employeeShown)
                                        {{ $empData['employee'] }}
                                        @php $employeeShown = true; @endphp
                                    @endif
                                </td>
                                <td style="width:220px;">{{ $proj['project_name'] ?? 'Unnamed Project' }}</td>
                                <td style="width:260px;">{{ $task['task_name'] ?? 'Unnamed Task' }}</td>

                                @foreach ($dates as $date)
                                    <td>{{ $task['daily'][$date] ?? '00:00' }}</td>
                                @endforeach

                                <td>{{ $task['total'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                    <!-- Employee overall total row -->
                    <tr style="background:#ffe9e2;">
                        <td colspan="{{ 3 + count($dates) }}" style="text-align:right; font-weight:900; color:#d63b1a;">
                            Overall Total ({{ $empData['employee'] }}):
                        </td>
                        <td style="font-weight:900; color:#000;">{{ $employeeOverall }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- CHART VIEW -->
    <div id="chartView" style="display:none;">
        <div class="chart-container">
            <div class="chart-title">Daily Hours – Bar Chart</div>
            <canvas id="barChart"></canvas>
        </div>

        <div class="chart-row" style="display:flex; gap:22px;">
            <div class="chart-container chart-half" style="width:50%;">
                <div class="chart-title">Daily Hours – Pie Chart</div>
                <canvas id="pieChart"></canvas>
            </div>

            <div class="chart-container chart-half" style="width:50%;">
                <div class="chart-title">Project-wise Hours – Pie Chart</div>
                <canvas id="projectPieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // view toggles
    const tableBtn = document.getElementById('tableBtn');
    const chartBtn = document.getElementById('chartBtn');
    const tableView = document.getElementById('tableView');
    const chartView = document.getElementById('chartView');

    tableBtn.onclick = () => {
        tableView.style.display = 'block';
        chartView.style.display = 'none';
        tableBtn.classList.add('active');
        chartBtn.classList.remove('active');
    };
    chartBtn.onclick = () => {
        tableView.style.display = 'none';
        chartView.style.display = 'block';
        chartBtn.classList.add('active');
        tableBtn.classList.remove('active');
    };

    function applyFilter() {
        const from = document.getElementById('fromDate').value;
        const to = document.getElementById('toDate').value;
        const params = new URLSearchParams(window.location.search);
        if (from) params.set('from', from);
        if (to) params.set('to', to);
        window.location.search = params.toString();
    }

    function downloadCSV() {
        // build CSV from table headers + body
        const table = document.querySelector('.time-table');
        const rows = Array.from(table.querySelectorAll('tr'));
        const csv = rows.map(r => {
            const cols = Array.from(r.querySelectorAll('th,td'));
            return cols.map(c => `"${c.innerText.replace(/"/g, '""')}"`).join(',');
        }).join('\n');

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'time-log-report.csv';
        a.click();
        URL.revokeObjectURL(url);
    }

    function printReport() {
        const printContent = document.querySelector('#tableView').innerHTML;
        const original = document.body.innerHTML;
        document.body.innerHTML = `<html><head><title>Print</title><style>table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:8px;}th{background:#eef2fb;}</style></head><body>${printContent}</body></html>`;
        window.print();
        document.body.innerHTML = original;
        window.location.reload(); // reload to re-bind scripts (simple approach)
    }

    // Chart data populated from server variables
    const labels = @json(array_map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'), $dates));
    const values = @json($dailyHours);
    const projectLabels = @json($projectLabels);
    const projectValues = @json($projectHours);

    // Bar chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hours',
                data: values,
                borderRadius: 8
            }]
        },
        options: { responsive: true }
    });

    // Pie chart (daily)
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values
            }]
        },
        options: { responsive: true }
    });

    // Project pie
    new Chart(document.getElementById('projectPieChart'), {
        type: 'pie',
        data: {
            labels: projectLabels,
            datasets: [{
                data: projectValues
            }]
        },
        options: { responsive: true }
    });
</script>

@endsection
