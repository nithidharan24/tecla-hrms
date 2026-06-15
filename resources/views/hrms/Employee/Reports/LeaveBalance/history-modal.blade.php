@php
$permissions = App\Helpers\PermissionHelper::getPermissions('My Reports');
@endphp
@extends('layouts.index')

@section('content')

<style>
.history-modal-box {
    background: #fff;
    padding: 25px;
    width: 90%;
    max-width: 900px;
    margin: 30px auto;
    border-radius: 12px;
    box-shadow: 0px 5px 25px rgba(0,0,0,0.15);
}

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.history-header h3 {
    font-size: 20px;
    font-weight: 700;
}

.close-btn {
    background: #f2f2f2;
    border-radius: 50%;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 18px;
}

.date-box {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.date-box input {
    padding: 8px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead th {
    background: #eef2ff;
    padding: 12px;
    font-size: 14px;
    font-weight: 600;
}

.table tbody td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.summary-box {
    margin-top: 25px;
    padding: 15px;
    background: #f8f9ff;
    border-radius: 8px;
}

.summary-box p {
    margin: 0;
    padding: 5px 0;
}
</style>

<div class="history-modal-box">

    {{-- HEADER --}}
    <div class="history-header">
        <h3>{{ $leaveType }} – History</h3>

       
    </div>

    {{-- DATE RANGE --}}
    <div class="date-box">
        <input type="date" value="{{ date('Y-01-01') }}">
        <span style="padding-top:8px;">–</span>
        <input type="date" value="{{ date('Y-12-31') }}">
    </div>

    {{-- TABLE --}}
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Added</th>
                <th>Booked</th>
                <th>Balance</th>
            </tr>
        </thead>

        <tbody>

            {{-- Opening Balance --}}
            <tr>
                <td>{{ date('01-Jan-Y') }}</td>
                <td>Opening Balance</td>
                <td>{{ $allocated }}</td>
                <td>-</td>
                <td>{{ $remaining }}</td>
            </tr>

            {{-- Leave History Rows --}}
            @foreach ($history as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->from_date)->format('d-M-Y') }}</td>
                    <td>Leave Applied</td>
                    <td>-</td>
                    <td>{{ $item->no_of_days }}</td>
                    <td>{{ $remaining + $item->no_of_days }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary-box">
        <p><strong>Total Allocated:</strong> {{ $allocated }}</p>
        <p><strong>Total Used:</strong> {{ $booked }}</p>
        <p><strong>Remaining Balance:</strong> {{ $remaining }}</p>
    </div>

</div>

@endsection
