<!DOCTYPE html>
<html>
<head>
    <title>Invoice Notification</title>
</head>
<body>
    <h2>Invoice Notification</h2>
    
    <p>Dear {{ $clientName }},</p>
    
    <p>Please find attached your invoice #{{ $invoiceId }} dated {{ $invoiceDate }} for {{ config('app.currency') }}{{ number_format($amount, 2) }}.</p>
    
    <p>If you have any questions about this invoice, please don't hesitate to contact us.</p>
    
    <p>Thank you for your business!</p>
    
    <p>Best regards,<br>
    {{ config('app.name') }}</p>
</body>
</html>