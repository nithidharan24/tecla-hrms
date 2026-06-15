@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
.page {
    background:#f8f9fc;
    padding:20px;
}

.card {
    background:#fff;
    padding:25px;
    border-radius:12px;
    border:1px solid #e6e6e6;
    margin-bottom:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
}

/* Table Styling */
.table {
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.table thead th {
    background:#eef2ff;
    padding:14px;
    font-weight:600;
    color:#333;
    border-bottom:1px solid #ddd;
}

.table tbody tr:hover {
    background:#f9fafc;
}

.table tbody td {
    padding:14px;
    border-bottom:1px solid #efefef;
    font-size:15px;
}

/* Color Indicators (Zoho Style) */
.color-dot {
    height:12px;
    width:12px;
    border-radius:50%;
    display:inline-block;
    margin-right:8px;
}

.dot-blue { background:#3b82f6; }
.dot-green { background:#22c55e; }
.dot-orange { background:#f97316; }
.dot-yellow { background:#eab308; }
.dot-purple { background:#a855f7; }
.dot-red { background:#ef4444; }

/* History Button */
.history-btn {
    padding:6px 12px;
    border-radius:6px;
    background:#f3f4f6;
    border:1px solid #d1d5db;
    font-size:14px;
    transition:0.2s;
}
.history-btn:hover {
    background:#e5e7eb;
}
</style>


<div class="page">

    <!-- Header -->
    <div class="card">
        <h4 style="font-weight:700;">Leave Balance</h4>
        <p class="text-muted" style="margin:0;">
            <strong>EMP {{ $employee->employeeid }}</strong> – 
            {{ $employee->firstname }} {{ $employee->lastname }}
        </p>
    </div>

    <!-- Leave Summary Table -->
    <div class="card">

        <table class="table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Total Allocated</th>
                    <th>Used</th>
                    <th>Remaining</th>
                    <th style="width:120px;">History</th>
                </tr>
            </thead>

          <tbody>
    @foreach($summary as $leave)
        <tr>
            <td>
                @if($leave['type'] === 'Casual Leave')
                    <span class="color-dot dot-blue"></span>
                @elseif($leave['type'] === 'Sick')
                    <span class="color-dot dot-purple"></span>
                @elseif($leave['type'] === 'Hospitalisation')
                    <span class="color-dot dot-orange"></span>
                @elseif($leave['type'] === 'Maternity Leave')
                    <span class="color-dot dot-yellow"></span>
                @elseif($leave['type'] === 'Paternity Leave')
                    <span class="color-dot dot-green"></span>
                @elseif($leave['type'] === 'LOP')
                    <span class="color-dot dot-red"></span>
                @endif

                {{ $leave['type'] }}
            </td>

            <td>{{ $leave['total'] }}</td>
            <td>{{ $leave['used'] }}</td>
            <td>{{ $leave['remaining'] }}</td>

            <td>
                <a href="{{ route('myreports.leave.history.type', $leave['type']) }}" 
                   class="history-btn">
                   View
                </a>
            </td>
        </tr>
    @endforeach
</tbody>

        </table>

    </div>

</div>

@endsection
