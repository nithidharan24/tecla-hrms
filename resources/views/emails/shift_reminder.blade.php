<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⏰ Shift Starting Soon!</h2>
        </div>
        <div class="content">
            <p>Hi {{ $name }},</p>
            <p>This is a friendly reminder that your shift <strong>{{ $shift_name }}</strong> starts at <strong>{{ $start_time }}</strong>.</p>
            <p>Please punch in within the next 5 minutes to mark your attendance.</p>
            <p>Thank you!</p>
        </div>
    </div>
</body>
</html>
