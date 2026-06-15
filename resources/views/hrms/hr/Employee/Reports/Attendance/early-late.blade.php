@extends('layouts.index')

@section('content')

<style>
/* Main Page */
.report-wrapper {
    background: #f4f6fb;
    padding: 25px;
}

/* Header card */
.report-header {
    background: #fff;
    padding: 18px 22px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Employee badge */
.emp-chip {
    background: #eef2ff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #3c4b9a;
}

/* Search box */
.date-filter input {
    padding: 6px 10px;
    border: 1px solid #dcdff0;
    border-radius: 6px;
    font-size: 13px;
}
.date-filter button {
    padding: 6px 14px;
    font-size: 13px;
}

/* Table */
.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead th {
    background: #eef2ff;
    padding: 14px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr:hover {
    background: #fafbff;
}

.table tbody td {
    padding: 14px;
    font-size: 13px;
    border-bottom: 1px solid #f0f0f0;
}

/* Colors for values */
.red { color: #e63946; font-weight: 600; }
.green { color: #2a9d8f; font-weight: 600; }
.gray { color: #777; }

/* Responsive */
@media(max-width: 768px){
    .report-header {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<div class="report-wrapper">

    {{-- ================= HEADER ================= --}}
    <div class="report-header">
        <div>
            <h4 class="m-0" style="font-weight:700;">Early / Late Check-In & Check-Out</h4>
            <small class="text-muted">Daily timing accuracy report</small>
        </div>

        <div class="emp-chip">
            EMP {{ $employee->employeeid }} — 
            {{ $employee->firstname }} {{ $employee->lastname }}
        </div>
    </div>

    {{-- ================= FILTER BAR ================= --}}
    <div class="report-header" style="margin-top: -10px;">
        <form method="get" class="date-filter d-flex gap-2 align-items-center">
            <input type="date" name="start_date" value="{{ $startDate }}">
            <input type="date" name="end_date" value="{{ $endDate }}">
            <button class="btn btn-primary btn-sm">Apply</button>
        </form>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="table-container mt-3">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>First In</th>
                    <th>Last Out</th>
                    <th>Total Hours</th>
                    <th>Entry Early</th>
                    <th>Entry Late</th>
                    <th>Exit Early</th>
                    <th>Exit Late</th>
                    <th>Net Hours</th>
                    <th>Shift</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($report as $r)
                <tr>
                    <td>{{ date('d-M-Y', strtotime($r['date'])) }}</td>
                    <td class="gray">{{ $r['first_in'] }}</td>
                    <td class="gray">{{ $r['last_out'] }}</td>

                    <td class="green">{{ $r['total_hours'] }}</td>

                    {{-- ENTRY EARLY --}}
                    <td class="{{ $r['entry_early'] != '-' ? 'green' : 'gray' }}">
                        {{ $r['entry_early'] }}
                    </td>

                    {{-- ENTRY LATE --}}
                    <td class="{{ $r['entry_late'] != '-' ? 'red' : 'gray' }}">
                        {{ $r['entry_late'] }}
                    </td>

                    {{-- EXIT EARLY --}}
                    <td class="{{ $r['exit_early'] != '-' ? 'red' : 'gray' }}">
                        {{ $r['exit_early'] }}
                    </td>

                    {{-- EXIT LATE --}}
                    <td class="{{ $r['exit_late'] != '-' ? 'green' : 'gray' }}">
                        {{ $r['exit_late'] }}
                    </td>

                    <td class="green">{{ $r['net_hours'] }}</td>
                    <td class="gray">{{ $r['shift'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
