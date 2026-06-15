@extends('layouts.index')

@section('content')
<div class="container">
    <h2>Asset Details: {{ $asset->asset_name }}</h2>
    <table class="table table-bordered">
        <tr>
            <th>Asset ID</th>
            <td>{{ $asset->asset_id }}</td>
        </tr>
        <tr>
            <th>Purchase Date</th>
            <td>{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <th>Purchase From</th>
            <td>{{ $asset->purchase_from }}</td>
        </tr>
        <tr>
            <th>Manufacturer</th>
            <td>{{ $asset->manufacturer }}</td>
        </tr>
        <tr>
            <th>Model</th>
            <td>{{ $asset->model }}</td>
        </tr>
        <tr>
            <th>Serial Number</th>
            <td>{{ $asset->serial_number }}</td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td>{{ $asset->supplier }}</td>
        </tr>
        <tr>
            <th>Condition</th>
            <td>{{ $asset->condition }}</td>
        </tr>
        <tr>
            <th>Warranty</th>
            <td>{{ $asset->warranty }}</td>
        </tr>
        <tr>
            <th>Value</th>
            <td>{{ $asset->value }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $asset->description }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $asset->status }}</td>
        </tr>
    </table>
</div>
@endsection
