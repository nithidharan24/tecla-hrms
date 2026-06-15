<!DOCTYPE html>
<html>
<head>
    <title>AMC Renewal Reminder</title>
</head>
<body>
    <h2>AMC Renewal Reminder</h2>
    
    <p>Dear {{ $client->first_name }},</p>
    
    <p>This is a reminder that your Annual Maintenance Contract (AMC) will expire in <strong>{{ $daysLeft }} days</strong>.</p>
    
    <p><strong>Renewal Date:</strong> {{ \Carbon\Carbon::parse($renewalDate)->format('F d, Y') }}</p>
    
    <p>Please contact us to renew your AMC and avoid any service interruptions.</p>
    
    <p>Best regards,<br>Your Company Name</p>
</body>
</html>