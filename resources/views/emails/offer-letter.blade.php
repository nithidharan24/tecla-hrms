<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter - {{ $candidate->position_applied }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .highlight-box {
            background-color: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .highlight-box strong {
            color: #667eea;
        }
        .details-section {
            margin: 30px 0;
        }
        .details-section h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            width: 40%;
        }
        .detail-value {
            color: #333;
            width: 60%;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        .salary-table th {
            background-color: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .salary-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .salary-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .salary-table .total-row {
            background-color: #f0f7ff;
            font-weight: 600;
            color: #667eea;
        }
        .next-steps {
            background-color: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #667eea;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 13px;
            color: #666;
        }
        .footer strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .pdf-notice {
            background-color: #ffeaa7;
            border-left: 4px solid #fdcb6e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .pdf-notice strong {
            color: #d63031;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
            <p>You Have Been Selected</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <p>Dear <strong>{{ $candidate->first_name }} {{ $candidate->last_name }},</strong></p>
            </div>
            
            <div class="highlight-box">
                <strong>🎯 Great News!</strong> We are pleased to inform you that you have been selected for the position of <strong>{{ $candidate->position_applied }}</strong> at <strong>{{ $companyName }}</strong>. Congratulations!
            </div>
            
            <p>After careful consideration of your application, qualifications, and performance in our selection process, we are confident that you would be an excellent addition to our team.</p>
            
            @if($hasOfferLetter)
            <div class="pdf-notice">
                <strong>📎 Official Offer Letter Attached</strong><br>
                Please find your official offer letter PDF attached to this email. It contains all important details about your employment terms, compensation, and joining procedures.
            </div>
            @endif
            
            <div class="details-section">
                <h3>📋 Position Details</h3>
                <div class="detail-row">
                    <div class="detail-label">Position:</div>
                    <div class="detail-value"><strong>{{ $offerData->designation }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Department:</div>
                    <div class="detail-value">{{ $offerData->department }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Employment Type:</div>
                    <div class="detail-value">{{ $offerData->employment_type }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Joining Date:</div>
                    <div class="detail-value"><strong>{{ $offerData->joining_date }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Reporting To:</div>
                    <div class="detail-value">{{ $offerData->reporting_to }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Work Location:</div>
                    <div class="detail-value">{{ $offerData->work_location }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Probation Period:</div>
                    <div class="detail-value">{{ $offerData->probation_period }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Notice Period:</div>
                    <div class="detail-value">{{ $offerData->notice_period }}</div>
                </div>
            </div>
            
            <div class="details-section">
                <h3>💰 Compensation Package</h3>
                <p><strong>Annual CTC:</strong> ₹{{ $offerData->ctc_annual }} ({{ $offerData->ctc_words }})</p>
                
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Component</th>
                            <th style="text-align: right;">Monthly</th>
                            <th style="text-align: right;">Annual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td style="text-align: right;">₹{{ $offerData->basic_monthly }}</td>
                            <td style="text-align: right;">₹{{ $offerData->basic_annual }}</td>
                        </tr>
                        <tr>
                            <td>HRA (House Rent Allowance)</td>
                            <td style="text-align: right;">₹{{ $offerData->hra_monthly }}</td>
                            <td style="text-align: right;">₹{{ $offerData->hra_annual }}</td>
                        </tr>
                        <tr>
                            <td>City Compensation Allowance</td>
                            <td style="text-align: right;">₹{{ $offerData->cca_monthly }}</td>
                            <td style="text-align: right;">₹{{ $offerData->cca_annual }}</td>
                        </tr>
                        <tr>
                            <td>Special Allowance</td>
                            <td style="text-align: right;">₹{{ $offerData->special_allowance_monthly }}</td>
                            <td style="text-align: right;">₹{{ $offerData->special_allowance_annual }}</td>
                        </tr>
                        <tr>
                            <td>Statutory Bonus</td>
                            <td style="text-align: right;">₹{{ $offerData->statutory_bonus_monthly }}</td>
                            <td style="text-align: right;">₹{{ $offerData->statutory_bonus_annual }}</td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>Gross Salary</strong></td>
                            <td style="text-align: right;"><strong>₹{{ $offerData->gross_monthly }}</strong></td>
                            <td style="text-align: right;"><strong>₹{{ $offerData->gross_annual }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="next-steps">
                <h3>📞 Next Steps</h3>
                <ul>
                    <li>Please carefully review the attached offer letter and salary structure</li>
                    <li>Our HR team will contact you within the next <strong>2-3 business days</strong></li>
                    <li>We will discuss the offer details and answer any questions you may have</li>
                    <li>Please confirm your availability for the proposed joining date</li>
                    <li>Keep your phone available for our HR team's call</li>
                </ul>
            </div>
            
            <p>We are excited to have you join our team and look forward to working with you!</p>
            
            <p>If you have any immediate questions or concerns, please feel free to contact our HR department:</p>
            <p>
                <strong>HR Department</strong><br>
                Email: {{ $companyEmail }}<br>
                Phone: {{ $companyPhone }}
            </p>
            
            <p>Best regards,<br>
            <strong>Human Resources Team</strong><br>
            {{ $companyName }}</p>
        </div>
        
        <div class="footer">
            <strong>{{ $companyName }}</strong>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
