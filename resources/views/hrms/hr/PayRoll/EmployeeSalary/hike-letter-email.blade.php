<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hike Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #fff;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }
        .highlight {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        button {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Hike Letter</h2>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>,</p>
            
            <p>We are pleased to inform you about your salary revision effective from <strong>{{ $hike->effective_date }}</strong>.</p>
            
            <div class="highlight">
                <h3>Revised Salary Details:</h3>
                <p><strong>New CTC:</strong> <span class="amount">₹{{ $hike->new_ctc }}</span></p>
                <p><strong>New CTC (in words):</strong> {{ ucfirst($hike->new_ctc_words) }}</p>
                <p><strong>Designation:</strong> {{ $hike->designation }}</p>
            </div>
            
            <p><strong>Monthly Breakdown:</strong></p>
            <ul>
                <li>Basic Salary: ₹{{ $hike->basic_monthly }}</li>
                <li>HRA: ₹{{ $hike->hra_monthly }}</li>
                <li>Gross Monthly: ₹{{ $hike->gross_monthly }}</li>
                <li>Net Monthly: ₹{{ $hike->net_income_monthly }}</li>
            </ul>
            
            <p>Please find attached the detailed hike letter for your reference.</p>
            
            <p>We appreciate your continued contribution to the organization.</p>
            
            <p>Best Regards,<br>
            <strong>HR Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is a system-generated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>