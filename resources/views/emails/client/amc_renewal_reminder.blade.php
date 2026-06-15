<!DOCTYPE html>
<html>
<head>
    <title>Hosting AMC Renewal Reminder</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 15px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .urgent { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Hosting AMC Renewal Reminder</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $client->first_name }} {{ $client->last_name }},</p>
            
            <p>This is a friendly reminder that your Hosting AMC (Annual Maintenance Contract) is due for renewal in <span class="urgent">{{ $daysLeft }} days</span>.</p>
            
            <p><strong>Renewal Date:</strong> {{ \Carbon\Carbon::parse($renewalDate)->format('F d, Y') }}</p>
            
            <p><strong>Client Details:</strong></p>
            <ul>
                <li><strong>Company:</strong> {{ $client->company_name }}</li>
                <li><strong>Client ID:</strong> {{ $client->client_id }}</li>
                <li><strong>Email:</strong> {{ $client->email }}</li>
                <li><strong>Phone:</strong> {{ $client->phone }}</li>
            </ul>
            
            <p>To ensure uninterrupted service, please complete the renewal process before the due date.</p>
            
            <p>If you have any questions or need assistance with the renewal process, please don't hesitate to contact us.</p>
            
            <p>Best regards,<br>
            Your Service Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>