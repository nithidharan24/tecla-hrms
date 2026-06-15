@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Team Reports');
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
}

.table-wrapper {
    background: white;
    border-radius: 10px;
    border: 1px solid #e4e7ec;
    overflow-x: auto;
}

.avail-table {
    width: max-content;
    border-collapse: collapse;
    min-width: 100%;
}

.avail-table thead th {
    padding: 10px;
    font-weight: 700;
    text-align: center;
    color: #ff6b00;
    background: #f5f7fb;
}

.avail-table tbody td {
    padding: 10px;
    text-align: center;
    font-weight: 600;
    border-bottom: 1px solid #f0f0f0;
}

/* BADGE COLORS */
.W { color: #d28a00; font-weight: 700; }  /* Weekend */
.H { color: #d89f00; font-weight: 700; }  /* Holiday */
.L { color: #c92a2a; font-weight: 700; }  /* Leave */
.dash { color: #888; }                    /* Future */
.empty { color:#999; }

/* Legend */
.legend-box {
    padding: 14px;
    background: white;
    border-radius: 10px;
    border: 1px solid #d7d7d7;
    margin-top: 20px;
    display: flex;
    gap: 25px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
}
.legend-color {
    width: 28px;
    height: 4px;
    border-radius: 20px;
}
</style>

<div class="header-bar">Resource Availability</div>

<form method="GET" class="month-box">
    <label>Month:</label>
    <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control" style="width:220px;">
    <button class="btn btn-primary">Apply</button>
    <button type="button" onclick="window.print()" class="btn btn-light">Print</button>
</form>

<div class="table-wrapper">
    <table class="avail-table">
        <thead>
            <tr>
                <th style="min-width:220px;text-align:left;padding-left:15px;">Employee</th>
                @foreach($dates as $d)
                    <th>
                        <div>{{ $d['day'] }}</div>
                        <small>{{ $d['dow'] }}</small>
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $emp)
                <tr>
                    <td style="text-align:left; padding-left:15px;">
                        <b>{{ $emp->firstname }} {{ $emp->lastname }}</b>
                        <div style="font-size:12px; color:#666;">{{ $emp->employeeid }}</div>
                    </td>

                    @foreach($dates as $d)
                        @php
                            $val = $availability[$emp->id][$d['full']] ?? '';
                        @endphp

                        <td class="{{ $val ?: 'empty' }}">
                            {{ $val === '' ? '' : $val }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="legend-box">
    <div class="legend-item"><div class="legend-color" style="background:#c92a2a;"></div> Leave</div>
    <div class="legend-item"><div class="legend-color" style="background:#d89f00;"></div> Holiday</div>
    <div class="legend-item"><div class="legend-color" style="background:#d28a00;"></div> Weekend</div>
    <div class="legend-item"><div class="legend-color" style="background:#888;"></div> Future</div>
    <div class="legend-item"><div class="legend-color" style="background:#000;"></div> Working Day</div>
</div>

@endsection
