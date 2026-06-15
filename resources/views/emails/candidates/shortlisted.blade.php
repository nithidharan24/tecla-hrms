<!DOCTYPE html>
<html>
<head>
    <title>Application Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .header { background-color: #007bff; color: white; padding: 10px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Application Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $candidateName }},</p>
            <p>We are pleased to inform you that your application for the <strong>{{ $position }}</strong> position has been shortlisted!</p>
            <p>Our team was very impressed with your qualifications and experience.</p>
            <p>We will be in touch shortly with the next steps in the hiring process. Please keep an eye on your inbox for further updates.</p>
            <p>Thank you for your patience and interest in our company.</p>
            <p>Sincerely,</p>
            <p>The HR Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
