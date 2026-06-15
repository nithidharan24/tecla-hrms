<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f44336; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #f44336; color: white; text-decoration: none; border-radius: 4px; margin-top: 15px; }
        .urgent { color: #f44336; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🚨 Missed Punch In - Action Required</h2>
        </div>
        <div class="content">
            <p>Hi {{ $name }},</p>
            <p class="urgent">You have missed punching in for your shift!</p>
            <p>Your shift <strong>{{ $shift_name }}</strong> started at <strong>{{ $start_time }}</strong>, but we haven't recorded your punch-in yet.</p>
            <p><strong>Please punch in immediately</strong> or contact your supervisor if there's an issue.</p>
            <p>Late punch-ins may affect your attendance record.</p>
            <p>Thank you for your immediate attention to this matter.</p>
        </div>
    </div>
</body>
</html>