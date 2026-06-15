
@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Organization Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>

/* ===================== WRAPPER ===================== */
.org-earlylate-wrapper {
    padding: 24px;
    background: #f0f2f8;
}

/* ===================== HEADER ===================== */
.org-header-box {
    background: linear-gradient(90deg, #fe5201, #ff7b4d);
    color: #fff;
    padding: 20px 24px;
    border-radius: 14px;
    font-size: 22px;
    font-weight: 700;
    box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    margin-bottom: 24px;
}

/* ===================== FILTER BAR ===================== */
.org-filter-panel {
    background: #fff;
    padding: 14px 20px;
    border-radius: 14px;
    display: flex;
    gap: 14px;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.org-filter-panel select {
    border: 1px solid #cfd6e4;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    background: #f9fbff;
}

/* ===================== TABLE WRAPPER ===================== */
.org-table-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow-x: auto;
}

/* ===================== TABLE ===================== */
.org-table {
    width: 100%;
    border-collapse: collapse;
}

.org-table thead th {
    background: #f1f4ff;
    color: #ff5722;
    font-weight: 700;
    padding: 12px;
    white-space: nowrap;
    border-bottom: 2px solid #e6e8f1;
    font-size: 14px;
}

.org-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #eef0f6;
    font-size: 14px;
    white-space: nowrap;
}

/* ===================== STATUS COLORS ===================== */
.txt-red { color: #e63946; font-weight: 700; }
.txt-green { color: #2a9d8f; font-weight: 700; }
.txt-gray { color: #6c7280; }

/* ===================== BUTTON ===================== */
.org-btn-download {
    background: #22c55e;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
}

.org-btn-download:hover {
    background: #16a34a;
}
</style>



<div class="org-earlylate-wrapper">

    {{-- =============== TOP TITLE =============== --}}
    <div class="org-header-box">
        Organization — Early / Late Timing Report
    </div>


    {{-- =============== FILTER BAR =============== --}}
    <form method="get" class="org-filter-panel">

        {{-- Branch --}}
        <select name="branch" onchange="this.form.submit()">
            <option value="all">All Branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $selectedBranch==$b->id?'selected':'' }}>
                    {{ $b->name }}
                </option>
            @endforeach
        </select>

        {{-- Department --}}
        <select name="department" onchange="this.form.submit()">
            <option value="all">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ $selectedDepartment==$d->id?'selected':'' }}>
                    {{ $d->department }}
                </option>
            @endforeach
        </select>

        {{-- Date Filter --}}
        <select name="filter" onchange="this.form.submit()">
            <option value="last7" {{ $filter=='last7'?'selected':'' }}>Last 7 Days</option>
            <option value="this_week" {{ $filter=='this_week'?'selected':'' }}>This Week</option>
            <option value="last_week" {{ $filter=='last_week'?'selected':'' }}>Last Week</option>
            <option value="this_month" {{ $filter=='this_month'?'selected':'' }}>This Month</option>
        </select>

        <a href="" class="org-btn-download">
            Download CSV
        </a>

    </form>


    {{-- =============== TABLE =============== --}}
    <div class="org-table-card">
        <table class="org-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>EmpID</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>IN</th>
                    <th>OUT</th>
                    <th>Entry Early</th>
                    <th>Entry Late</th>
                    <th>Exit Early</th>
                    <th>Exit Late</th>
                    <th>Net</th>
                    <th>Shift</th>
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $r)
                    <tr>
                        <td>{{ $r['emp']->firstname }} {{ $r['emp']->lastname }}</td>
                        <td>{{ $r['emp']->employeeid }}</td>
                        <td>{{ $r['branch'] }}</td>
                        <td>{{ $r['department'] }}</td>
                        <td>{{ date('d-M-Y', strtotime($r['date'])) }}</td>

                        <td class="txt-gray">{{ $r['first_in'] }}</td>
                        <td class="txt-gray">{{ $r['last_out'] }}</td>

                        <td class="txt-green">{{ $r['entry_early'] }}</td>
                        <td class="txt-red">{{ $r['entry_late'] }}</td>

                        <td class="txt-red">{{ $r['exit_early'] }}</td>
                        <td class="txt-green">{{ $r['exit_late'] }}</td>

                        <td><strong>{{ $r['net_hours'] }}</strong></td>
                        <td class="txt-gray">{{ $r['shift'] }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>

@endsection
