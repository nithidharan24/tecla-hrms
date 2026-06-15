<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-header { margin-bottom: 20px; }
        .invoice-details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .totals-table { width: 50%; margin-left: auto; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>INVOICE #{{ $invoice_id }}</h1>
        <p>Date: {{ $invoice_date }}</p>
        <p>Due Date: {{ $due_date }}</p>
    </div>

    <div class="invoice-details">
        <div style="float: left; width: 50%;">
            <h3>Bill To:</h3>
            <p>{{ $client_name }}</p>
            <p>{!! nl2br(e($billing_address)) !!}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Description</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ number_format($item['unitCost'], 2) }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td class="text-right">{{ number_format($total, 2) }}</td>
        </tr>
        @if(!empty($taxDetails))
            @foreach($taxDetails as $tax)
            <tr>
                <td><strong>{{ $tax->name }} ({{ $tax->percentage }}%):</strong></td>
                <td class="text-right">
                    {{ number_format($total * ($tax->percentage / 100), 2) }}
                </td>
            </tr>
            @endforeach
        @endif
        @if($discount > 0)
        <tr>
            <td><strong>Discount ({{ $discount }}%):</strong></td>
            <td class="text-right">
                -{{ number_format($total * ($discount / 100), 2) }}
            </td>
        </tr>
        @endif
        <tr>
            <td><strong>Total:</strong></td>
            <td class="text-right">{{ number_format($grant_amt, 2) }}</td>
        </tr>
    </table>

    @if(!empty($other_information))
    <div class="additional-info">
        <h3>Additional Information:</h3>
        <p>{!! nl2br(e($other_information)) !!}</p>
    </div>
    @endif

    <div class="footer" style="margin-top: 50px;">
        <p>Thank you for your business!</p>
    </div>
</body>
</html>