<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promotion Announcement</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            color: #333;
            margin: 0;
            padding: 40px 0;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            padding: 30px 40px;
        }
        h2 {
            color: #1a202c;
            font-size: 22px;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            font-size: 15px;
            margin: 10px 0;
        }
        strong {
            color: #111827;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Congratulations, {{ $employee->firstname }} {{ $employee->lastname }}!</h2>

        <p>We are thrilled to inform you of your promotion!</p>

        <p>
            Effective <strong>{{ \Carbon\Carbon::parse($promotion->promotion_date)->format('F jS, Y') }}</strong>, 
            you have been promoted from <strong>{{ $oldDesignationName }}</strong> 
            to <strong>{{ $newDesignationName }}</strong>.
        </p>

        <p>
            This promotion is a testament to your hard work, dedication, and valuable contributions to our team. 
            We truly appreciate your commitment and look forward to your continued success in your new role.
        </p>

        <p>
            If you have any questions, please do not hesitate to reach out to the HR department.
        </p>

        <p>Thanks,<br><strong>Team Tecla</strong></p>

        <div class="footer">
            © {{ date('Y') }} Tecla. All rights reserved.
        </div>
    </div>
</body>
</html>
