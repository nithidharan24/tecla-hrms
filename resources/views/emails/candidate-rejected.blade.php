<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
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
            background-color: #6c757d;
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
            background-color: #f8d7da;
            padding: 15px;
            border-left: 4px solid #dc3545;
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
        .encouragement {
            background-color: #d1ecf1;
            padding: 15px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Application Status Update</h1>
        <p>{{ $candidate->position_applied }}</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $candidate->first_name }} {{ $candidate->last_name }},</p>
        
        <p>Thank you for your interest in the <strong>{{ $candidate->position_applied }}</strong> position and for taking the time to apply with us.</p>
        
        <div class="highlight">
            After careful consideration, we have decided to move forward with other candidates whose qualifications more closely match our current requirements.
        </div>
        
        <div class="encouragement">
            <strong>Please don't be discouraged!</strong> Your qualifications and experience are valuable, and we encourage you to apply for future opportunities that may be a better fit.
        </div>
        
        <h3>Your Application Details:</h3>
        <ul>
            <li><strong>Position Applied:</strong> {{ $candidate->position_applied }}</li>
            <li><strong>Experience:</strong> {{ $candidate->experience_years }}</li>
            <li><strong>Application Date:</strong> {{ $candidate->created_at->format('M d, Y') }}</li>
        </ul>
        
        <h3>What's Next?</h3>
        <ul>
            <li>We will keep your resume on file for future opportunities</li>
            <li>You will be notified if a suitable position becomes available</li>
            <li>Feel free to apply for other positions that match your skills</li>
            <li>Continue to check our careers page for new openings</li>
        </ul>
        
        <p>We appreciate the time and effort you put into your application and wish you the best of luck in your job search.</p>
        
        <p>Thank you again for your interest in our company.</p>
        
        <p>Best regards,<br>
        <strong>HR Team</strong><br>
        Your Company Name</p>
    </div>
     b 
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>