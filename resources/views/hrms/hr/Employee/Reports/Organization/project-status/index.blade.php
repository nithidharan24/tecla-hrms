
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { font-family: 'Inter', sans-serif; }

.report-container { background:#f5f7fb; padding:22px; }
.header-bar { background: linear-gradient(90deg,#ff4800,#ff6b3d); color:#fff; padding:14px 18px; border-radius:10px; font-size:18px; font-weight:700; }

/* Filters / Controls */
.controls {
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
    margin:16px 0;
    background:#fff;
    padding:12px;
    border-radius:10px;
    border:1px solid #e6edf7;
}
.controls .group { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.controls label { font-size:13px; color:#333; font-weight:600; display:block; }
.controls select, .controls input { padding:8px 10px; border-radius:8px; border:1px solid #d6dbe9; min-width:160px; }

/* icon buttons */
.icon-btn { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; background:#fff; border:1px solid #d6dbe9; cursor:pointer; font-size:16px; }
.icon-btn.primary { background:linear-gradient(90deg,#ff6b3d,#ff8557); color:#fff; border:none; }
.icon-btn:hover { transform:translateY(-2px); }

/* table */
.table-wrapper { overflow-x:auto; background:#fff; border-radius:10px; border:1px solid #e6eefc; padding:12px; }
.table-report { width:100%; border-collapse:collapse; min-width:1000px; }
.table-report thead th { background:#f0f6ff; color:#ff4800; padding:12px; font-weight:700; border-bottom:1px solid #e9f0ff; text-align:center; }
.table-report tbody td { padding:12px; border-bottom:1px dashed #eef3fb; font-weight:600; text-align:center; }
.table-report tbody td.project { text-align:left; font-weight:700; padding-left:16px; }

/* status badge */
.status-badge { padding:6px 10px; border-radius:16px; font-weight:700; color:#fff; display:inline-block; text-transform:capitalize; }
.status-completed { background:#22c55e; }
.status-in_progress { background:#0ea5e9; }
.status-pending { background:#f59e0b; color:#000; }
.status-on_hold { background:#6b7280; }

/* chart */
.chart-container { display:none; background:#fff; padding:18px; border-radius:10px; border:1px solid #e6eefc; margin-top:16px; }

/* small responsive */
@media (max-width:900px) {
    .controls select, .controls input { min-width:120px; }
}
</style>

<div class="report-container">
    <div class="header-bar">Organization — Project Status Report</div>

    {{-- FILTER + ACTIONS --}}
    <div class="controls" role="region" aria-label="Filters and actions">

        <div class="group" role="group" aria-label="Filters">
            <div>
                <label for="branchFilter">Branch</label>
                <select id="branchFilter">
                    <option value="all">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (string)($branch ?? 'all') === (string)$b->id ? 'selected':'' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="deptFilter">Department</label>
                <select id="deptFilter">
                    <option value="all">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ (string)($department ?? 'all') === (string)$d->id ? 'selected':'' }}>
                            {{ $d->department }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="employeeFilter">Employee</label>
                <select id="employeeFilter">
                    <option value="all">All Employees</option>
                    @foreach($allEmployees as $e)
                        <option value="{{ $e->id }}" {{ (string)($employee ?? 'all') === (string)$e->id ? 'selected':'' }}>
                            {{ $e->employeeid }} — {{ $e->firstname }} {{ $e->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fromDate">From</label>
                <input type="date" id="fromDate" value="{{ $from ?? \Carbon\Carbon::now()->startOfWeek()->toDateString() }}">
            </div>

            <div>
                <label for="toDate">To</label>
                <input type="date" id="toDate" value="{{ $to ?? \Carbon\Carbon::now()->endOfWeek()->toDateString() }}">
            </div>

            <div>
                <label for="projectFilter">Project</label>
                <select id="projectFilter">
                    <option value="all">All Projects</option>
                    @foreach($allProjects as $p)
                        <option value="{{ $p->id }}" {{ (string)($project ?? 'all') === (string)$p->id ? 'selected':'' }}>
                            {{ $p->projectname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statusFilter">Status</label>
                <select id="statusFilter">
                    <option value="all">All</option>
                    @foreach($allStatuses as $s)
                        <option value="{{ $s }}" {{ ($status ?? 'all') === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="align-self:flex-end;">
                <button class="icon-btn primary" onclick="applyFilter()" title="Apply filters">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </div>
        @if(isset($permissions) && $permissions->can_download)
        <div class="group" style="margin-left:auto; align-items:center;">
            <button id="btnTable" class="icon-btn primary" onclick="switchView('table')" title="Table view"><i class="fa-solid fa-table-cells"></i></button>
            <button id="btnChart" class="icon-btn" onclick="switchView('chart')" title="Chart view"><i class="fa-solid fa-chart-column"></i></button>

            <a class="icon-btn" href="{{ route('organization.projectstatus.csv', request()->all()) }}" title="Export CSV">
                <i class="fa-solid fa-file-csv"></i>
            </a>

            <button class="icon-btn" onclick="window.print()" title="Print"><i class="fa-solid fa-print"></i></button>
        </div>
        @endif
    </div>

    {{-- TABLE VIEW --}}
    <div id="tableView" class="table-wrapper" role="table" aria-label="Project status table">
        <table class="table-report" aria-describedby="project-status">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Branch</th>
                    <th>Employee</th>
                    <th>Hours Worked</th>
                    <th>Project Total</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // If empty, show friendly row
                    $hasRows = !empty($projectEmployeeHours) && count($projectEmployeeHours) > 0;
                @endphp

                @if(!$hasRows)
                    <tr><td colspan="7" style="text-align:center; padding:20px;">No data found for selected filters.</td></tr>
                @else
                    @foreach($projectEmployeeHours as $projectName => $employees)
                        @php
                            $projTotalMin = $projectTotals[$projectName]['total_minutes'] ?? 0;
                            $projTotalFmt = sprintf('%02d:%02d', floor($projTotalMin/60), $projTotalMin % 60);
                            $projBranch = $projectTotals[$projectName]['branch'] ?? '-';
                            $projDept   = $projectTotals[$projectName]['department'] ?? '-';
                            $projStatus = $projectTotals[$projectName]['status'] ?? '-';
                        @endphp

                        @foreach($employees as $empName => $mins)
                            <tr>
                                <td class="project">{{ $projectName }}</td>
                                <td>{{ $projBranch }}</td>
                                <td>{{ $empName }}</td>
                                <td>{{ sprintf('%02d:%02d', floor($mins/60), $mins % 60) }}</td>
                                <td>{{ $projTotalFmt }}</td>
                                <td>
                                  <span class="status-badge status-{{ str_replace(' ', '_', strtolower($projStatus)) }}" style="color:#ff6b3d;">
    {{ ucfirst($projStatus) }}
</span>
<!-- Orange dolor text would go here if needed -->
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{-- CHART VIEW --}}
    <div id="chartContainer" class="chart-container" role="region" aria-label="Project working hours chart">
        <h4 style="margin:0 0 12px;">Project — Total Working Hours (hours)</h4>
        <canvas id="projectBarChart" height="110"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function applyFilter() {
    const params = new URLSearchParams();

    params.set('branch', document.getElementById('branchFilter').value);
    params.set('department', document.getElementById('deptFilter').value);
    params.set('employee', document.getElementById('employeeFilter').value);
    params.set('from', document.getElementById('fromDate').value);
    params.set('to', document.getElementById('toDate').value);
    params.set('project', document.getElementById('projectFilter').value);
    params.set('status', document.getElementById('statusFilter').value);

    // submit by changing location so server re-renders with filters
    window.location.search = params.toString();
}

function switchView(view) {
    const table = document.getElementById('tableView');
    const chart = document.getElementById('chartContainer');
    const btnTable = document.getElementById('btnTable');
    const btnChart = document.getElementById('btnChart');

    if (view === 'table') {
        table.style.display = 'block';
        chart.style.display = 'none';
        btnTable.classList.add('primary');
        btnChart.classList.remove('primary');
    } else {
        table.style.display = 'none';
        chart.style.display = 'block';
        btnChart.classList.add('primary');
        btnTable.classList.remove('primary');
        renderChart();
    }
}

/* Chart: projects -> total minutes (converted to hours for display) */
let projChart = null;
function renderChart() {
    const ctx = document.getElementById('projectBarChart').getContext('2d');

    const labels = {!! json_encode(array_keys($chartProjects ?? [])) !!};
    // convert minutes to hours with 2 decimals
    const dataVals = {!! json_encode(array_values($chartProjects ?? [])) !!}.map(m => Math.round((m/60) * 100)/100);

    if (projChart) projChart.destroy();

    projChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hours',
                data: dataVals,
                backgroundColor: '#ff6b3d',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display:true, text:'Hours' }
                }
            }
        }
    });
}

// default view = table
switchView('table');
</script>

@endsection
