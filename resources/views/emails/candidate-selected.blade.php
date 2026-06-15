<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations - You've Been Selected!</title>
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
            background-color: #28a745;
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
            background-color: #d4edda;
            padding: 15px;
            border-left: 4px solid #28a745;
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
        <h1>🎉 Congratulations!</h1>
        <p>You've Been Selected</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $candidate->first_name }} {{ $candidate->last_name }},</p>
        
        <div class="highlight">
            <strong>Great news!</strong> We are pleased to inform you that you have been selected for the position of <strong>{{ $candidate->position_applied }}</strong>.
        </div>
        
        <p>After careful consideration of your application and qualifications, we believe you would be an excellent addition to our team.</p>
        
        @if($hasOfferLetter)
        <div class="highlight">
            <strong>📎 Offer Letter Attached</strong><br>
            Please find your official offer letter attached with this email. This document contains all the important details about your employment terms, compensation, and joining formalities.
        </div>
        @endif
        
        <h3>Next Steps:</h3>
        <ul>
            <li>Please review the attached offer letter carefully</li>
            <li>Our HR team will contact you within the next 2-3 business days</li>
            <li>We will discuss the offer details and answer any questions you may have</li>
            <li>Please keep your phone available for our call</li>
        </ul>
        
        <h3>Your Application Details:</h3>
        <ul>
            <li><strong>Position:</strong> {{ $candidate->position_applied }}</li>
            <li><strong>Experience:</strong> {{ $candidate->experience_years }}</li>
            <li><strong>Contact Email:</strong> {{ $candidate->email }}</li>
            <li><strong>Contact Phone:</strong> {{ $candidate->phone }}</li>
        </ul>
        
        <p>We are excited to have you join our team and look forward to working with you!</p>
        
        <p>If you have any questions in the meantime, please don't hesitate to contact our HR department.</p>
        
        <p>Best regards,<br>
        <strong>HR Team</strong><br>
        Your Company Name</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>
