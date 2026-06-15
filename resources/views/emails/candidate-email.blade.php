<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'HRMS') }} - HR Communication</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #553c9a 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .message {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            white-space: pre-wrap;
            border-left: 4px solid #1a365d;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'HRMS') }}</h1>
            <p>Human Resources Department</p>
        </div>
        
        <div class="content">
            <div style="font-size: 18px; margin-bottom: 20px; color: #1a365d;">
                Hello {{ $candidateName }},
            </div>
            
            <div class="message">
                {{ $emailMessage }}
            </div>
            
            <div style="background: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px 0; color: #1a365d;">Application Details</h3>
                <strong>Position:</strong> {{ $jobTitle }}<br>
                <strong>Department:</strong> {{ $department }}<br>
                <strong>Email Type:</strong> {{ ucfirst($emailType) }}
            </div>
            
            <div class="footer">
                <p><strong>This email was sent from {{ config('app.name', 'HRMS') }} HR System</strong></p>
                <p>Please do not reply to this email. For any queries, contact our HR department.</p>
            </div>
        </div>
    </div>
</body>
</html>