

@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
.header-bar {
    background: #ff6b00;
    color: #fff;
    padding: 16px 22px;
    border-radius: 8px;
    font-size: 20px;
    font-weight: 700;
}

.month-box {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0;
    flex-wrap: wrap;
    padding: 15px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e4e7ec;
}

.table-wrapper {
    background: white;
    border-radius: 10px;
    border: 1px solid #e4e7ec;
    overflow-x: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

/* Calendar/grid table */
.avail-table {
    width: max-content;
    border-collapse: collapse;
    min-width: 100%;
}

.avail-table thead th {
    padding: 12px 8px;
    font-weight: 700;
    text-align: center;
    color: #ff6b00;
    background: #f5f7fb;
    border-bottom: 2px solid #e4e7ec;
    position: sticky;
    top: 0;
    z-index: 10;
}

.avail-table tbody td {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    min-width: 64px;
    transition: background 0.2s;
}

.avail-table tbody tr:hover td {
    background: #f9fafb;
}

/* Employee info column */
.employee-col {
    min-width: 220px;
    max-width: 220px;
    position: sticky;
    left: 0;
    background: white;
    z-index: 5;
    box-shadow: 2px 0 5px rgba(0,0,0,0.05);
}

/* BADGE COLORS */
.W { 
    color: #d28a00; 
    font-weight: 700; 
    background: rgba(210,138,0,0.06); 
    border-radius: 6px; 
    padding: 6px 8px;
    display: inline-block;
    min-width: 32px;
}
.H { 
    color: #d89f00; 
    font-weight: 700; 
    background: rgba(216,159,0,0.06); 
    border-radius: 6px; 
    padding: 6px 8px;
    display: inline-block;
    min-width: 32px;
}
.L { 
    color: #c92a2a; 
    font-weight: 700; 
    background: rgba(201,42,42,0.06); 
    border-radius: 6px; 
    padding: 6px 8px;
    display: inline-block;
    min-width: 32px;
}
.dash { 
    color: #888; 
    font-weight: 600;
    padding: 6px 8px;
    display: inline-block;
    min-width: 32px;
}
.empty { 
    color: #999; 
    padding: 6px 8px;
    display: inline-block;
    min-width: 32px;
}

/* Legend */
.legend-box {
    padding: 14px 20px;
    background: white;
    border-radius: 10px;
    border: 1px solid #d7d7d7;
    margin-top: 20px;
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
}
.legend-color {
    width: 32px;
    height: 4px;
    border-radius: 20px;
}

/* Chart card */
.chart-card {
    background: white;
    border-radius: 10px;
    border: 1px solid #e4e7ec;
    padding: 20px;
    margin-top: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

/* Date header styling */
.date-header {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.date-day {
    font-size: 16px;
    font-weight: 700;
}
.date-dow {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    margin-top: 2px;
}

/* Icon buttons */
.icon-btn {
    width: 42px;
    height: 42px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e6edf7;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.2s ease;
    color: #666;
    text-decoration: none;
}

.icon-btn.primary {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color: #fff;
    border: none;
    box-shadow: 0 2px 4px rgba(255,107,61,0.2);
}

.icon-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    color: #ff6b00;
    border-color: #ff6b00;
}

.icon-btn.primary:hover {
    color: #fff;
    box-shadow: 0 4px 12px rgba(255,107,61,0.3);
}

/* Filter controls */
.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.filter-group label {
    font-weight: 500;
    color: #333;
    font-size: 14px;
    white-space: nowrap;
}
.form-control-sm {
    padding: 8px 12px;
    height: 42px;
    border-radius: 8px;
    border: 1px solid #d1d9e6;
    font-size: 14px;
}
.btn-primary-sm {
    background: linear-gradient(90deg,#ff6b3d,#ff8557);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    height: 42px;
    transition: all 0.2s;
}
.btn-primary-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255,107,61,0.2);
}

/* Action buttons container */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

/* Responsive */
@media (max-width: 1200px) {
    .avail-table thead th, 
    .avail-table tbody td { 
        padding: 10px 6px; 
        min-width: 56px; 
    }
    .employee-col {
        min-width: 200px;
    }
}

@media (max-width: 992px) {
    .month-box {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-group {
        width: 100%;
        justify-content: space-between;
    }
    .action-buttons {
        margin-left: 0;
        margin-top: 15px;
        justify-content: center;
        width: 100%;
    }
}

@media (max-width: 768px) {
    .avail-table thead th, 
    .avail-table tbody td { 
        padding: 8px 4px; 
        min-width: 48px; 
        font-size: 13px;
    }
    .date-day { font-size: 14px; }
    .date-dow { font-size: 10px; }
    .employee-col {
        min-width: 180px;
        font-size: 13px;
    }
    .legend-box {
        flex-direction: column;
        gap: 12px;
    }
}

/* Today highlight */
.today {
    background: rgba(255, 107, 0, 0.1) !important;
    position: relative;
}
.today::after {
    content: '';
    position: absolute;
    top: 2px;
    right: 2px;
    width: 6px;
    height: 6px;
    background: #ff6b00;
    border-radius: 50%;
}
.page-wrapper {
    left: 0;
    position: relative;
    transition: all 0.2s 
ease-in-out;
    margin: 0 0 0 230px;
    padding: 70px 20px 0;
}
</style>

<div class="header-bar">Resource Availability Report</div>

<form method="GET" class="month-box">
    <div class="filter-group">
        <label>Month:</label>
        <input type="month" 
               name="month" 
               value="{{ $selectedMonth }}" 
               class="form-control form-control-sm" 
               style="width:180px;">

        <label>Branch:</label>
        <select name="branch" class="form-control form-control-sm" style="width:180px;">
            <option value="all" {{ $selectedBranch == 'all' ? 'selected' : '' }}>All Branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $selectedBranch == $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                </option>
            @endforeach
        </select>

        <label>Employee:</label>
        <select name="employee" class="form-control form-control-sm" style="width:220px;">
            <option value="all" {{ $selectedEmployee == 'all' ? 'selected' : '' }}>All Employees</option>
            @foreach($employees as $empItem)
                <option value="{{ $empItem->id }}" {{ $selectedEmployee == $empItem->id ? 'selected' : '' }}>
                    {{ $empItem->employeeid }} — {{ $empItem->firstname }} {{ $empItem->lastname }}
                </option>
            @endforeach
        </select>

        <button class="btn-primary-sm">Apply Filters</button>
    </div>

    <div class="action-buttons">
        <!-- CALENDAR VIEW -->
        <button type="button" id="btnCalendar"
                class="icon-btn primary"
                onclick="switchView('calendar')"
                title="Calendar View">
            <i class="fa-solid fa-table-cells"></i>
        </button>

        <!-- CHART VIEW -->
        <button type="button" id="btnChart"
                class="icon-btn"
                onclick="switchView('chart')"
                title="Chart View">
            <i class="fa-solid fa-chart-column"></i>
        </button>
        @if(isset($permissions) && $permissions->can_download)
        <!-- CSV EXPORT -->
        <a href="javascript:void(0);" class="icon-btn" onclick="downloadCSV()" title="Download CSV">
            <i class="fa-solid fa-file-csv"></i>
        </a>

        <!-- PRINT -->
        <button type="button" class="icon-btn" onclick="window.print()" title="Print Report">
            <i class="fa-solid fa-print"></i>
        </button>
        @endif

        <!-- REFRESH -->
        <button type="submit" class="icon-btn" title="Refresh Data">
            <i class="fa-solid fa-rotate-right"></i>
        </button>
    </div>
</form>

{{-- CALENDAR GRID --}}
<div id="calendarView" class="table-wrapper">
    <table class="avail-table">
        <thead>
            <tr>
                <th class="employee-col" style="text-align:left;padding-left:20px;">Employee Details</th>
                @foreach($dates as $d)
                    @php
                        $isToday = $d['full'] == date('Y-m-d');
                    @endphp
                    <th class="{{ $isToday ? 'today' : '' }}">
                        <div class="date-header">
                            <span class="date-day">{{ $d['day'] }}</span>
                            <span class="date-dow">{{ $d['dow'] }}</span>
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $emp)
                <tr>
                    <td class="employee-col" style="padding:15px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                           
                            <div>
                                <b style="font-size:14px;">{{ $emp->firstname }} {{ $emp->lastname }}</b>
                                <div style="font-size:12px; color:#666;">ID: {{ $emp->employeeid }}</div>
                                <div style="font-size:11px; color:#888;">
                                    {{ $branches->firstWhere('id', $emp->branch_id)->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    @foreach($dates as $d)
                        @php
                            $val = $availability[$emp->id][$d['full']] ?? null;
                            $isToday = $d['full'] == date('Y-m-d');
                        @endphp

                        <td class="{{ $isToday ? 'today' : '' }}">
                            @if($val === 'H')
                                <div class="H" title="Holiday">H</div>
                            @elseif($val === 'W')
                                <div class="W" title="Weekend">W</div>
                            @elseif($val === 'L')
                                <div class="L" title="Leave">L</div>
                            @elseif($val === '-')
                                <div class="dash" title="Future Date">-</div>
                            @else
                                <div class="empty" title="Working Day">
                                    <i class="fa-solid fa-circle-check" style="color:#2a9d8f; font-size:12px;"></i>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- CHART CARD --}}
<div id="chartView" class="chart-card" style="display:none;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h5 style="margin:0; font-weight:600;">Resource Availability Summary</h5>
        @if(isset($permissions) && $permissions->can_download)
        <button class="btn-primary-sm" onclick="exportChart()">
            <i class="fa-solid fa-download"></i> Export Chart
        </button>
    </div>
    
    <div class="chart-card-inner">
        <canvas id="orgStackedChart" height="120"></canvas>
    </div>
    
    <div class="legend-box" style="margin-top:20px;">
        <div class="legend-item">
            <div class="legend-color" style="background:#c92a2a;"></div>
            <span>Leave ({{ array_sum($chartData['leave']) }})</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background:#d89f00;"></div>
            <span>Holiday ({{ array_sum($chartData['holiday']) }})</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background:#d28a00;"></div>
            <span>Weekend ({{ array_sum($chartData['weekend']) }})</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background:#2a9d8f;"></div>
            <span>Working Days ({{ array_sum($chartData['working']) }})</span>
        </div>
        @if(isset($chartData['future']))
        <div class="legend-item">
            <div class="legend-color" style="background:#888;"></div>
            <span>Future Dates</span>
        </div>
        @endif
    </div>
</div>

<div class="legend-box" style="margin-top:20px;">
    <div class="legend-item">
        <div class="H" style="min-width:40px; text-align:center;">H</div>
        <span>Holiday</span>
    </div>
    <div class="legend-item">
        <div class="W" style="min-width:40px; text-align:center;">W</div>
        <span>Weekend</span>
    </div>
    <div class="legend-item">
        <div class="L" style="min-width:40px; text-align:center;">L</div>
        <span>Leave</span>
    </div>
    <div class="legend-item">
        <div class="empty" style="min-width:40px; text-align:center;">
            <i class="fa-solid fa-circle-check" style="color:#2a9d8f;"></i>
        </div>
        <span>Working Day</span>
    </div>
    <div class="legend-item">
        <div class="dash" style="min-width:40px; text-align:center;">-</div>
        <span>Future Date</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
const chartData = @json($chartData);
let orgChart = null;

// Toggle views
function switchView(view) {
    document.getElementById('calendarView').style.display = (view === 'calendar') ? 'block' : 'none';
    document.getElementById('chartView').style.display = (view === 'chart') ? 'block' : 'none';

    document.getElementById('btnCalendar').classList.toggle('primary', view === 'calendar');
    document.getElementById('btnChart').classList.toggle('primary', view === 'chart');

    if (view === 'chart') {
        drawStackedChart();
    }
}

// Draw Chart
function drawStackedChart() {
    const ctx = document.getElementById('orgStackedChart').getContext('2d');

    if (orgChart) orgChart.destroy();

    orgChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Leave',
                    data: chartData.leave,
                    backgroundColor: '#c92a2a',
                    borderColor: '#a61e1e',
                    borderWidth: 1
                },
                {
                    label: 'Holiday',
                    data: chartData.holiday,
                    backgroundColor: '#d89f00',
                    borderColor: '#b88500',
                    borderWidth: 1
                },
                {
                    label: 'Weekend',
                    data: chartData.weekend,
                    backgroundColor: '#d28a00',
                    borderColor: '#b07100',
                    borderWidth: 1
                },
                {
                    label: 'Working',
                    data: chartData.working,
                    backgroundColor: '#2a9d8f',
                    borderColor: '#1f7a6e',
                    borderWidth: 1
                }
            ]
        },
        options: {
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                tooltip: { 
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' days';
                            return label;
                        }
                    }
                },
                legend: { 
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        maxRotation: 45,
                        minRotation: 30,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        callback: function(value) {
                            return value + ' days';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Number of Days'
                    }
                }
            }
        }
    });
}

// CSV Export Function
function downloadCSV() {
    // Show loading
    const btn = event.target.closest('.icon-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    
    // Prepare CSV content
    let csv = 'Resource Availability Report\n';
    csv += 'Month: {{ $selectedMonth }}\n';
    csv += 'Generated: ' + new Date().toLocaleString() + '\n\n';
    
    // Headers
    csv += 'Employee ID,Employee Name,Branch,';
    @foreach($dates as $d)
        csv += '{{ $d["day"] }} {{ $d["dow"] }},';
    @endforeach
    csv += 'Total Leaves,Total Holidays,Total Weekends,Total Working Days\n';
    
    // Data rows
    @foreach($employees as $emp)
        @php
            $empId = $emp->id;
            $totalL = $totalH = $totalW = $totalWork = 0;
        @endphp
        
        csv += '"{{ $emp->employeeid }}","{{ $emp->firstname }} {{ $emp->lastname }}","{{ $branches->firstWhere('id', $emp->branch_id)->name ?? 'N/A' }}",';
        
        @foreach($dates as $d)
            @php
                $val = $availability[$empId][$d['full']] ?? null;
                $displayVal = '';
                if ($val === 'H') {
                    $displayVal = 'Holiday';
                    $totalH++;
                } elseif ($val === 'W') {
                    $displayVal = 'Weekend';
                    $totalW++;
                } elseif ($val === 'L') {
                    $displayVal = 'Leave';
                    $totalL++;
                } elseif ($val === '-') {
                    $displayVal = 'Future';
                } else {
                    $displayVal = 'Working';
                    $totalWork++;
                }
            @endphp
            csv += '"{{ $displayVal }}",';
        @endforeach
        
        csv += '{{ $totalL }},{{ $totalH }},{{ $totalW }},{{ $totalWork }}\n';
    @endforeach
    
    // Summary row
    csv += '\n\nSummary\n';
    csv += 'Total Employees,{{ count($employees) }}\n';
    csv += 'Total Leave Days,' + {{ array_sum($chartData['leave']) }} + '\n';
    csv += 'Total Holiday Days,' + {{ array_sum($chartData['holiday']) }} + '\n';
    csv += 'Total Weekend Days,' + {{ array_sum($chartData['weekend']) }} + '\n';
    csv += 'Total Working Days,' + {{ array_sum($chartData['working']) }} + '\n';
    
    // Create and download CSV
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'Resource_Availability_{{ $selectedMonth }}_{{ date('Y-m-d') }}.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Restore button
    setTimeout(() => {
        btn.innerHTML = originalHTML;
    }, 500);
}

// Export Chart as Image
function exportChart() {
    if (orgChart) {
        const link = document.createElement('a');
        link.download = 'Resource_Availability_Chart_{{ $selectedMonth }}.png';
        link.href = orgChart.toBase64Image();
        link.click();
    }
}

// Print optimization
function beforePrint() {
    document.querySelectorAll('.icon-btn').forEach(btn => btn.style.display = 'none');
}

function afterPrint() {
    document.querySelectorAll('.icon-btn').forEach(btn => btn.style.display = 'flex');
}

// Add print event listeners
if (window.matchMedia) {
    const mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(mql => {
        if (mql.matches) {
            beforePrint();
        } else {
            afterPrint();
        }
    });
}

window.onafterprint = afterPrint;

// Initialize calendar view by default
switchView('calendar');

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + 1 for calendar view
    if (e.ctrlKey && e.key === '1') {
        e.preventDefault();
        switchView('calendar');
    }
    // Ctrl + 2 for chart view
    else if (e.ctrlKey && e.key === '2') {
        e.preventDefault();
        switchView('chart');
    }
    // Ctrl + E for export
    else if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        downloadCSV();
    }
});

// Add tooltips
document.querySelectorAll('[title]').forEach(el => {
    new bootstrap.Tooltip(el);
});
</script>

@endsection