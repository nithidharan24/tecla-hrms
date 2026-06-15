<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Payslip - {{ $month }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
        }
        .company-logo {
            max-width: 100px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .company-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .payslip-title {
            font-size: 20px;
            color: #495057;
            margin: 0;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #495057;
        }
        .payslip-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
        }
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .summary-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .summary-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        .net-salary {
            grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .net-salary .summary-label {
            font-size: 14px;
        }
        .net-salary .summary-value {
            font-size: 24px;
        }
        .details-section {
            margin: 25px 0;
        }
        .details-title {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .detail-label {
            font-weight: 500;
            color: #6c757d;
        }
        .detail-value {
            font-weight: bold;
            color: #495057;
        }
        .important-notes {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .important-notes h4 {
            color: #856404;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .important-notes ul {
            margin: 0;
            padding-left: 20px;
        }
        .important-notes li {
            color: #856404;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }
        .footer-company {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .footer-disclaimer {
            font-size: 12px;
            color: #6c757d;
            font-style: italic;
        }
        @media (max-width: 600px) {
            .summary-grid, .details-grid {
                grid-template-columns: 1fr;
            }
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            @if($companySettings['company_logo'] && file_exists(public_path($companySettings['company_logo'])))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($companySettings['company_logo']))) }}" alt="Company Logo" class="company-logo">
            @endif
            <div class="company-name">{{ $companySettings['company_name'] }}</div>
            <div class="company-address">{{ $companySettings['company_address'] }}</div>
            <p class="payslip-title">Monthly Payslip</p>
        </div>
        
        <div class="greeting">
            Dear {{ $employeeName }},
        </div>
        
        <p>We hope this email finds you well. Please find attached your detailed payslip for <strong>{{ $month }}</strong>.</p>
        
        <div class="payslip-summary">
            <div class="summary-title">Payslip Summary - {{ $month }}</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Employee ID</div>
                    <div class="summary-value">{{ $employeeId }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Designation</div>
                    <div class="summary-value">{{ $designationName }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Working Days</div>
                    <div class="summary-value">{{ $workingDays }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Hours Worked</div>
                    <div class="summary-value">{{ $totalHoursWorked }}h</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Earnings</div>
                    <div class="summary-value">₹{{ $totalEarnings }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Deductions</div>
                    <div class="summary-value">₹{{ $totalDeductions }}</div>
                </div>
                <div class="net-salary">
                    <div class="summary-label">Net Salary</div>
                    <div class="summary-value">₹{{ $netSalary }}</div>
                </div>
            </div>
        </div>
        
        <div class="details-section">
            <div class="details-title">📊 Earnings Breakdown</div>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Basic Salary:</span>
                    <span class="detail-value">₹{{ $basicSalary }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">HRA:</span>
                    <span class="detail-value">₹{{ $hra }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">DA:</span>
                    <span class="detail-value">₹{{ $da }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Conveyance:</span>
                    <span class="detail-value">₹{{ $conveyance }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Allowance:</span>
                    <span class="detail-value">₹{{ $allowance }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Medical:</span>
                    <span class="detail-value">₹{{ $medical }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Welfare:</span>
                    <span class="detail-value">₹{{ $welfare }}</span>
                </div>
                @if($overtimeHours > 0)
                <div class="detail-item">
                    <span class="detail-label">Overtime ({{ $overtimeHours }}h):</span>
                    <span class="detail-value">₹{{ $overtimeAmount }}</span>
                </div>
                @endif
                @if($dynamicAdditions > 0)
                <div class="detail-item">
                    <span class="detail-label">Additional Earnings:</span>
                    <span class="detail-value">₹{{ $dynamicAdditions }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="details-section">
            <div class="details-title">📉 Deductions Breakdown</div>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">PF Contribution:</span>
                    <span class="detail-value">₹{{ $pf }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">ESI:</span>
                    <span class="detail-value">₹{{ $esi }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">TDS:</span>
                    <span class="detail-value">₹{{ $tds }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tax:</span>
                    <span class="detail-value">₹{{ $tax }}</span>
                </div>
                @if($lopDeductionAmount > 0)
                <div class="detail-item">
                    <span class="detail-label">LOP Deduction ({{ $lopDays }} days):</span>
                    <span class="detail-value">₹{{ $lopDeductionAmount }}</span>
                </div>
                @endif
                @if($dynamicDeductions > 0)
                <div class="detail-item">
                    <span class="detail-label">Other Deductions:</span>
                    <span class="detail-value">₹{{ $dynamicDeductions }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="details-section">
            <div class="details-title">🏦 Bank Information</div>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Bank Name:</span>
                    <span class="detail-value">{{ $bankInfo['bank_name'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Account Number:</span>
                    <span class="detail-value">{{ $bankInfo['bank_account_no'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">IFSC Code:</span>
                    <span class="detail-value">{{ $bankInfo['ifsc_code'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PAN Number:</span>
                    <span class="detail-value">{{ $bankInfo['pan_no'] }}</span>
                </div>
            </div>
        </div>
        
        @if($leaveDays > 0 || $overtimeHours > 0 || $lateArrivals > 0)
        <div class="details-section">
            <div class="details-title">📅 Attendance Summary</div>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Total Working Days:</span>
                    <span class="detail-value">{{ $totalWorkingDays }} days</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Days Worked:</span>
                    <span class="detail-value">{{ $actualWorkingDays }} days</span>
                </div>
                @if($leaveDays > 0)
                <div class="detail-item">
                    <span class="detail-label">Leave Days:</span>
                    <span class="detail-value">{{ $leaveDays }} days</span>
                </div>
                @endif
                @if($lopDays > 0)
                <div class="detail-item">
                    <span class="detail-label">LOP Days:</span>
                    <span class="detail-value">{{ $lopDays }} days</span>
                </div>
                @endif
                @if($overtimeHours > 0)
                <div class="detail-item">
                    <span class="detail-label">Overtime Hours:</span>
                    <span class="detail-value">{{ $overtimeHours }} hours</span>
                </div>
                @endif
                @if($lateArrivals > 0)
                <div class="detail-item">
                    <span class="detail-label">Late Arrivals:</span>
                    <span class="detail-value">{{ $lateArrivals }} times</span>
                </div>
                @endif
                @if($earlyDepartures > 0)
                <div class="detail-item">
                    <span class="detail-label">Early Departures:</span>
                    <span class="detail-value">{{ $earlyDepartures }} times</span>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <div class="important-notes">
            <h4>📋 Important Notes</h4>
            <ul>
                <li>This payslip is generated automatically by our HRMS system</li>
                <li>Please keep this document for your tax and financial records</li>
                <li>For any discrepancies or queries, contact HR within 7 working days</li>
                <li>The attached PDF contains detailed breakdown of all components</li>
                <li>This email and attachment are confidential and intended only for the recipient</li>
                @if($overtimeHours > 0)
                <li>Overtime payment is calculated as per company policy</li>
                @endif
                @if($lopDays > 0)
                <li>LOP deduction has been applied for {{ $lopDays }} day(s)</li>
                @endif
                <li>Salary will be credited to your bank account: {{ $bankInfo['bank_account_no'] }}</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <p>Need help? Contact our HR team at <strong>{{ $companySettings['company_email'] }}</strong></p>
        </div>
        
        <div class="footer">
            <div class="footer-company">
                {{ $companySettings['company_name'] }}<br>
                Human Resources Department
            </div>
            <div class="footer-disclaimer">
                This is an automated email. Please do not reply to this message.<br>
                Generated on {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>