<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;zzzz
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

        /* Watermark as same logo */
        .watermark-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.10;
            z-index: 20;
            pointer-events: none;
        }
        .watermark-image img {
            max-width: 400px;
            height: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .logo img {
            width: 150px;
            height: auto;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
        }
        .invoice-details p {
            color: #666;
            font-size: 13px;
        }
        .company-details, .client-details {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            max-width: 300px;
            position: relative;
            z-index: 1;
        }
        .company-details p, .client-details p {
            margin-bottom: 5px;
        }
        .client-details strong {
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
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
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
        .total-amount {
            color: #e57300;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #999;
        }
        .text-regular {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Watermark using the same logo -->
        <div class="watermark-image">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Watermark Logo">
        </div>

        <!-- Header section with logo and invoice details -->
        <div class="header">
            <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Company Logo">
            </div>
            <div class="invoice-details">
                <h1>INVOICE #{{ substr($invoice->invoice_id, strlen('INV-')) }}</h1>
                <p>Create Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
                <p>Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Company and Client Information -->
        <div class="company-details">
            <p><strong>{{ $generalSettings->site_name }}</strong></p>
            <p>{{ $generalSettings->contact_address }}</p>
            <p>GST No: {{ $generalSettings->gst_number ?? '' }}</p>
        </div>

        <div class="client-details">
            <p><strong>INVOICE TO:</strong></p>
            <p><strong>{{ $invoice->client_name }}</strong></p>
            <p>
                @php
                    $billingChunks = str_split($invoice->billing_address, 60);
                    echo implode('<br>', $billingChunks);
                @endphp
            </p>
            <p><a href="tel:{{ $invoice->phone }}">{{ $invoice->phone }}</a></p>
            <p><a href="mailto:{{ $invoice->email }}">{{ $invoice->email }}</a></p>
        </div>

        <!-- Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Unit Cost</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if (count($invoice_items) > 0)
                    @foreach ($invoice_items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($item->item) }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->unit_cost, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="amount">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">No Items</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5">Subtotal:</td>
                    <td class="amount">{{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5">Tax:
                        @if (count($taxDetails) > 0)
                            @foreach ($taxDetails as $tx)
                                <span class="text-regular">{{ $tx->name }} ({{ $tx->percentage }}%)</span>
                                @if (!$loop->last), @endif
                            @endforeach
                        @else
                            <span class="text-regular">(0%)</span>
                        @endif
                    </td>
                    <td class="amount">{{ number_format($invoice->tax_amt, 2) }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr class="total-row">
                    <td colspan="5">Discount ({{ $invoice->discount }}%):</td>
                    <td class="amount">- {{ number_format(($invoice->total * $invoice->discount) / 100, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="5"><strong>Grand Total:</strong></td>
                    <td class="amount total-amount">{{ number_format($invoice->grant_amt, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if(!empty($invoice->other_information))
        <div class="notes" style="margin-top: 20px;">
            <h4>Notes:</h4>
            <p>{{ $invoice->other_information }}</p>
        </div>
        @endif

        <div style="padding: 20px 0; text-align: center; color: #999;">
            <p class="text-muted">...This is a Computer Generated Statement...</p>
        </div>
    </div>
</body>
</html>