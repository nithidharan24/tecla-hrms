<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointment Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .highlight {
            background-color: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Appointment Letter</h1>
        <p>Your formal appointment confirmation is ready</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $candidate->first_name }} {{ $candidate->last_name }},</p>
        
        <div class="highlight">
            <strong>Welcome to our organization!</strong> Your background verification has been completed successfully, and we are pleased to send you your official appointment letter for the position of <strong>{{ $candidate->position_applied }}</strong>.
        </div>
        
        <p>This marks an important milestone in your journey with us as we move forward with your employment process.</p>
        
        @if($hasAppointmentLetter)
        <div class="highlight">
            <strong>📎 Appointment Letter Attached</strong><br>
            Please find your official appointment letter attached with this email. This document contains all the important details about your employment terms, joining date, compensation structure, and other relevant information.
        </div>
        @endif
        
        <h3>Next Steps:</h3>
        <ul>
            <li>Please carefully review the attached appointment letter</li>
            <li>Ensure all your personal and employment details are correct</li>
            <li>Print and sign both copies of the letter if required</li>
            <li>Our HR team will be in touch shortly with joining formalities</li>
            <li>Please keep the attachment safe for your records</li>
        </ul>
        
        <h3>Your Employment Details:</h3>
        <ul>
            <li><strong>Position:</strong> {{ $candidate->position_applied }}</li>
            <li><strong>Experience:</strong> {{ $candidate->experience_years }} Year(s)</li>
            <li><strong>Email:</strong> {{ $candidate->email }}</li>
            <li><strong>Phone:</strong> {{ $candidate->phone }}</li>
        </ul>
        
        <p>If you notice any discrepancies in the appointment letter or have any questions regarding the employment terms, please contact our HR department immediately.</p>
        
        <p>We are excited to have you on our team and look forward to a great working relationship!</p>
        
        <p>Best regards,<br>
        <strong>HR Department</strong><br>
        Your Company Name</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>
