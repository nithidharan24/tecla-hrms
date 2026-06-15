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
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1a365d;
        }
        .message {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            white-space: pre-wrap;
            border-left: 4px solid #1a365d;
        }
        .job-details {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .job-details h3 {
            margin: 0 0 10px 0;
            color: #1a365d;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .badge-interview { background: #17a2b8; color: white; }
        .badge-offer { background: #28a745; color: white; }
        .badge-rejection { background: #dc3545; color: white; }
        .badge-general { background: #6c757d; color: white; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
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
            <div class="greeting">
                Hello {{ $candidateName }},
            </div>
            
            <div class="message">
                {{ $emailMessage }}
            </div>
            
            <div class="job-details">
                <h3>Application Details</h3>
                <strong>Position:</strong> {{ $jobTitle }}<br>
                <strong>Department:</strong> {{ $department }}<br>
                <span class="badge badge-{{ $emailType }}">{{ ucfirst($emailType) }}</span>
            </div>
            
            <div class="footer">
                <p><strong>This email was sent from {{ config('app.name', 'HRMS') }} HR System</strong></p>
                <p>Please do not reply to this email. For any queries, contact our HR department.</p>
                <p style="margin-top: 15px; font-size: 12px; color: #999;">
                    © {{ date('Y') }} {{ config('app.name', 'HRMS') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
