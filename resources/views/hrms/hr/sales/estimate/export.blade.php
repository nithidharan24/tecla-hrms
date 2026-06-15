<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .watermark-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.10;
            z-index: 10;
            pointer-events: none;
        }
        .watermark-image img {
            max-width: 500px;
            height: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1;
            position: relative;
        }
        .logo img {
            width: 150px;
        }
        .estimate-details {
            text-align: right;
        }
        .estimate-details h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
        }
        .estimate-details p {
            color: #666;
            font-size: 13px;
        }
        .company-details, .client-details {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
            max-width: 300px;
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }
        .company-details p, .client-details p {
            margin-bottom: 5px;
        }
        .client-details p strong {
            font-size: 14px;
            color: #000;
        }
        .client-details a {
            color: #007bff;
            text-decoration: none;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .table th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        .table td.amount {
            text-align: right;
        }
        .table .total-row {
            font-weight: 600;
            background-color: #f9f9f9;
        }
        .table .total-row .amount {
            font-size: 16px;
        }
        .total-amount {
            text-align: right;
            margin-top: 10px;
            font-size: 18px;
            color: #e57300;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Watermark background image -->
        <div class="watermark-image">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Watermark Logo">
        </div>

        <!-- Header section with logo and estimate details -->
        <div class="header">
            <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Company Logo">
            </div>
            <div class="estimate-details">
                <h1>ESTIMATE{{ substr($estimates->estimate_id, strlen('EST-')) }}</h1>
                <p>Create Date: {{ \Carbon\Carbon::parse($estimates->estimate_date)->format('d M Y') }}</p>
                <p>Expiry Date: {{ \Carbon\Carbon::parse($estimates->expiry_date)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Company and Client Information -->
        <div class="company-details">
            <p><strong>{{ $generalSettings->site_name }}</strong></p>
            <p>{{ $generalSettings->contact_address }}</p>
            <p>GST No:</p>
        </div>

        <div class="client-details">
            <p><strong>Estimate to:</strong></p>
            <p><strong>{{ $estimates->client_name }}</strong></p>
            <ul style="list-style-type: none">
                <li>
                    @php
                        $billingChunks = str_split($estimates->billing_address, 60);
                    @endphp
                </li>
                @foreach ($billingChunks as $chunk)
                    <li>{{ $chunk }}</li>
                @endforeach
            </ul>
            <p><a href="tel:{{ $estimates->phone }}"><strong style="color: #000;">{{ $estimates->phone }}</strong></a></p>
            <p><a href="mailto:{{ $estimates->email }}"><strong style="color: #000;">{{ $estimates->email }}</strong></a></p>
        </div>

        <!-- Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Unit Cost</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if (count($estimate_items) > 0)
                    @foreach ($estimate_items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($item->item) }}</td>
                            <td class="d-none d-sm-table-cell">{{ $item->description }}</td>
                            <td>{{ number_format($item->unit_cost, 0) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end" style="text-align: start;">{{ number_format($item->amount, 0) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">No Items</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" style="text-align: left;">Subtotal:</td>
                    <td class="amount" style="text-align: left;">{{ number_format($estimates->total, 0) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align: left;">Tax:
                        @if (count($taxDetails) > 0)
                            @foreach ($taxDetails as $tx)
                                <span class="text-regular">{{ $tx->name }} ({{ $tx->percentage }}%)</span>
                            @endforeach
                        @else
                            <span class="text-regular">(0%)</span>
                        @endif
                    </td>
                    <td class="amount" style="text-align: left;">{{ number_format($estimates->tax_amt, 0) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align: left;">Discount:</td>
                    <td class="amount total-amount" style="text-align: left;">- {{ number_format(($estimates->total * $estimates->discount) / 100, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align: left;">Grand Total:</td>
                    <td class="amount total-amount" style="text-align: left;">{{ number_format($estimates->grant_amt, 0) }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="padding: 5px; text-align: center; color: #ccc;">
            <p class="text-muted text-center">...This is a Computer Generated Statement...</p>
        </div>
    </div>
</body>
</html>