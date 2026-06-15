@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
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

/* Controls */
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

.controls .btn {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary { 
    background: rgba(255, 55, 0, 0.8); 
    color: #fff;
}
.btn-primary:hover {
    background: rgba(220, 45, 0, 0.9);
}

.btn-outline { 
    background: #fff; 
    border: 1px solid #cdd6ed; 
    color: #333;
}
.btn-outline:hover {
    background: #f5f7fb;
}

.right-actions { 
    margin-left: auto; 
    display: flex; 
    gap: 8px;
}

/* Table Wrapper */
.table-wrapper {
    overflow-x: auto;
    background: #ffffff;
    border-radius: 10px;
    border: 1px solid #dde3ef;
    padding-bottom: 12px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Table */
.presence-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.presence-table thead th {
    background: #eef2fb;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(255, 55, 0, 0.8);
    text-align: left;
    border-bottom: 2px solid #dce2f2;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.presence-table tbody td {
    padding: 14px 16px;
    font-size: 14px;
    border-bottom: 1px solid #f1f2f6;
    color: #333;
}

.presence-table tbody tr:hover {
    background-color: #f9fafc;
}

/* Status Badges */
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    min-width: 70px;
    text-align: center;
}

.badge-present { 
    background: #d1fae5; 
    color: #065f46;
}
.badge-absent { 
    background: #fee2e2; 
    color: #991b1b;
}
.badge-holiday { 
    background: #fef3c7; 
    color: #92400e;
}
.badge-weekend { 
    background: #dbeafe; 
    color: #1e40af;
}
.badge-future { 
    background: #f3f4f6; 
    color: #6b7280;
}

/* Employee Info Card */
.employee-card {
    background: white;
    border-radius: 8px;
    padding: 16px 20px;
    border-left: 4px solid rgba(255, 55, 0, 0.8);
    margin: 20px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.employee-info h4 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.employee-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.employee-period {
    text-align: right;
}

.employee-period .period {
    font-size: 15px;
    font-weight: 600;
    color: rgba(255, 55, 0, 0.8);
}

.employee-period .days {
    font-size: 13px;
    color: #666;
}

/* Summary Cards */
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
    border-top: 3px solid rgba(255, 55, 0, 0.8);
}

.summary-card .value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 8px 0;
}

.summary-card .label {
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Time cells */
.time-cell {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #333;
}

.hours-cell {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: rgba(255, 55, 0, 0.8);
}

/* Print Styles */
@media print {
    .controls,
    .employee-card,
    .summary-grid,
    .header-bar {
        display: none !important;
    }
    
    .table-wrapper {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        overflow: visible !important;
    }
    
    .presence-table {
        min-width: auto !important;
    }
    
    body {
        background: white !important;
        font-size: 12px !important;
    }
    
    .presence-table th {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .report-container {
        padding: 12px;
    }
    
    .controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .right-actions {
        margin-left: 0;
        justify-content: flex-start;
        margin-top: 10px;
    }
    
    .employee-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .employee-period {
        text-align: left;
    }
    
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="report-container">
    <div class="header-bar">Presence Hours Report - Last 7 Days</div>
    
    {{-- Employee Info Card --}}
    @if(isset($employee))
    <div class="employee-card">
        <div class="employee-info">
            <h4>{{ $employee->firstname }} {{ $employee->lastname }}</h4>
            <p>Employee ID: {{ $employee->employeeid }} | Department: {{ $employee->department ?? 'N/A' }}</p>
        </div>
        <div class="employee-period">
            <div class="period">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</div>
            <div class="days">(Last 7 Days)</div>
        </div>
    </div>
    @endif
    @if(isset($permissions) && $permissions->can_download)
    {{-- Export Controls --}}
    <form id="filterForm" method="get" class="controls">
        <div style="display: flex; gap: 12px; align-items: center;">
            <label>View Period:</label>
            <select name="filter" onchange="this.form.submit()">
                <option value="last7" selected>Last 7 Days</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
            </select>
        </div>
        
        <div class="right-actions">
            <a href="{{ route('presence.hours.csv') }}" class="btn btn-outline">
                <i class="fas fa-file-csv"></i> Download CSV
            </a>
            <a href="{{ route('presence.hours.pdf') }}" class="btn btn-outline">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </form>
    @endif
    
    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Hours</div>
            <div class="value">{{ $totalHours }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Payable Hours</div>
            <div class="value">{{ $totalPayable }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Present Hours</div>
            <div class="value">{{ $presentHours }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Holiday Hours</div>
            <div class="value">{{ $holidayHours }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Weekend Hours</div>
            <div class="value">{{ $weekendHours }}</div>
        </div>
    </div>
    
    {{-- Main Table --}}
    <div class="table-wrapper">
        <table class="presence-table">
            <thead>
                <tr>
                    <th width="15%">Date</th>
                    <th width="10%">Day</th>
                    <th width="12%">First In</th>
                    <th width="12%">Last Out</th>
                    <th width="12%">Total Hours</th>
                    <th width="12%">Payable Hours</th>
                    <th width="12%">Status</th>
                    <th width="15%">Shift</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                @php
                    $date = \Carbon\Carbon::parse($r['date_raw']);
                    $badgeClass = 'badge-future';
                    if($r['status'] == 'Present') $badgeClass = 'badge-present';
                    elseif($r['status'] == 'Absent') $badgeClass = 'badge-absent';
                    elseif($r['status'] == 'Holiday') $badgeClass = 'badge-holiday';
                    elseif($r['status'] == 'Weekend') $badgeClass = 'badge-weekend';
                @endphp
                <tr>
                    <td><strong>{{ $date->format('d M Y') }}</strong></td>
                    <td>{{ $date->format('D') }}</td>
                    <td class="time-cell">{{ $r['first_in'] }}</td>
                    <td class="time-cell">{{ $r['last_out'] }}</td>
                    <td class="hours-cell">{{ $r['total_hours'] }}</td>
                    <td class="hours-cell">{{ $r['payable_hours'] }}</td>
                    <td>
                        <span class="status-badge {{ $badgeClass }}">
                            {{ $r['status'] }}
                        </span>
                    </td>
                    <td>{{ $r['shift'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- Legend --}}
    <div style="margin-top: 25px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #d5ddee;">
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <div style="display: inline-flex; align-items: center; gap: 8px;">
                <span class="status-badge badge-present" style="width: auto; min-width: 70px;">Present</span>
                <span style="font-size: 13px; color: #666;">- Working day with attendance</span>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 8px;">
                <span class="status-badge badge-absent" style="width: auto; min-width: 70px;">Absent</span>
                <span style="font-size: 13px; color: #666;">- Working day without attendance</span>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 8px;">
                <span class="status-badge badge-holiday" style="width: auto; min-width: 70px;">Holiday</span>
                <span style="font-size: 13px; color: #666;">- Company holiday</span>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 8px;">
                <span class="status-badge badge-weekend" style="width: auto; min-width: 70px;">Weekend</span>
                <span style="font-size: 13px; color: #666;">- Non-working day</span>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #888;">
            <i class="fas fa-info-circle"></i> Report shows last 7 days attendance data. Hours are calculated based on punch-in/out times.
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
// Update period selection
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.querySelector('select[name="filter"]');
    
    if(periodSelect) {
        periodSelect.addEventListener('change', function() {
            const period = this.value;
            let startDate, endDate;
            
            switch(period) {
                case 'this_week':
                    startDate = new Date();
                    startDate.setDate(startDate.getDate() - startDate.getDay() + 1); // Monday
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // Sunday
                    break;
                case 'last_week':
                    startDate = new Date();
                    startDate.setDate(startDate.getDate() - startDate.getDay() - 6); // Previous Monday
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // Sunday
                    break;
                case 'this_month':
                    startDate = new Date();
                    startDate.setDate(1); // First day of month
                    endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0); // Last day of month
                    break;
                default: // last7
                    endDate = new Date();
                    startDate = new Date();
                    startDate.setDate(endDate.getDate() - 6);
            }
            
            // Format dates for display
            const formatDate = (date) => {
                return date.toISOString().split('T')[0];
            };
            
            // You could update the UI here or just let the form submit
            document.querySelector('form').submit();
        });
    }
});
</script>

@endsection