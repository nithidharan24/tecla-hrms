<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payslip->firstname }} {{ $payslip->lastname }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        
        .payslip-container {
            border: 2px solid #000;
            padding: 0;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #000;
            position: relative;
        }
        
        .company-logo {
            position: absolute;
            left: 15px;
            top: 15px;
            max-width: 80px;
            max-height: 60px;
            border: 1px solid #ddd;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .company-address {
            font-size: 10px;
            margin-bottom: 15px;
        }
        
        .payslip-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .employee-details {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        
        .employee-left, .employee-right {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        
        .employee-right {
            border-left: 1px solid #000;
        }
        
        .detail-row {
            margin-bottom: 3px;
            font-size: 9px;
        }
        
        .detail-label {
            display: inline-block;
            width: 120px;
            font-weight: normal;
        }
        
        .detail-value {
            font-weight: bold;
        }
        
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        .salary-table th {
            background-color: white;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }
        
        .salary-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background-color:white;
            font-weight: bold;
        }
        
        .net-pay {
            padding: 8px;
            border-top: 1px solid #000;
            font-weight: bold;
            font-size: 11px;
        }
        
        .footer-note {
            text-align: center;
            padding: 10px;
            font-size: 8px;
            font-style: italic;
            border-top: 1px solid #000;
        }
        
        .empty-cell {
            background-color: white;
        }
    </style>
</head>
<body>
    @php
        // Calculate LOP deduction for PDF
        $employeeDbId = $payslip->employee_id ?? $payslip->id ?? null;
        $payrollMonthForLop = date('Y-m', strtotime($payslip->payroll_month ?? now()));
        
        $lopRecord = DB::table('employee_lop_records')
            ->where('employee_id', $employeeDbId)
            ->where('month', $payrollMonthForLop)
            ->first();

        $lopDays = $lopRecord ? $lopRecord->lop_days : 0;
        $perDaySalary = $payslip->total_working_days > 0 ? ($payslip->basic_salary / $payslip->total_working_days) : 0;
        $lopDeductionAmount = $lopDays * $perDaySalary;
    @endphp

    <div class="payslip-container">
        
        <div class="header">
            @if($companySettings['company_logo'] && file_exists(public_path($companySettings['company_logo'])))
                <div class="logo">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($companySettings['company_logo']))) }}" alt="Company Logo" class="company-logo">
                </div>
            @endif
            
            <div class="company-name">{{ $companySettings['company_name'] }}</div>
            <div class="company-address">
                {{ $companySettings['company_address'] }}<br>
                @if($companySettings['company_phone'] !== 'N/A')
                    Phone: {{ $companySettings['company_phone'] }} | 
                @endif
                Email: {{ $companySettings['company_email'] }}
            </div>
            <div class="payslip-title">Pay slip for the Month {{ $payslip->payroll_month_formatted }}</div>
        </div>

        <div class="employee-details">
            <div class="employee-left">
                <div class="detail-row">
                    <span class="detail-label">Employee Code</span>
                    <span class="detail-value">:{{ $payslip->employeeid }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department</span>
                    <span class="detail-value">:{{ $payslip->designation_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date of Joining</span>
                    <span class="detail-value">:{{ $payslip->joiningdate ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Actual Working Days</span>
                    <span class="detail-value">:{{ $payslip->actual_working_days }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pan Card Number</span>
                    <span class="detail-value">:{{ $bankInfo['pan_no'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bank Name</span>
                    <span class="detail-value">:{{ $bankInfo['bank_name'] }}</span>
                </div>
                
            </div>
            
            <div class="employee-right">
                <div class="detail-row">
                    <span class="detail-label">Employee Name</span>
                    <span class="detail-value">:{{ $payslip->firstname }} {{ $payslip->lastname }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Designation</span>
                    <span class="detail-value">:{{ $payslip->designation_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">LOP days</span>
                    <span class="detail-value">:{{ $lopDays }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Working Days</span>
                    <span class="detail-value">:{{ $payslip->total_working_days }}</span>
                </div>
              
                <div class="detail-row">
                    <span class="detail-label">Account No</span>
                    <span class="detail-value">:{{ $bankInfo['bank_account_no'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">IFSC Code</span>
                    <span class="detail-value">:{{ $bankInfo['ifsc_code'] }}</span>
                </div>
            </div>
        </div>

        
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Components</th>
                    <th style="width: 15%;">Fixed Value (Rs.)</th>
                    <th style="width: 15%;">Earned Value (Rs.)</th>
                    <th style="width: 25%;">Deductions</th>
                    <th style="width: 15%;">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Earned Basic Pay</td>
                    <td class="amount-cell">{{ number_format($payslip->basic_salary, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->basic_salary, 0) }}</td>
                    <td>Employee PF</td>
                    <td class="amount-cell">{{ number_format($payslip->pf, 0) }}</td>
                </tr>
                <tr>
                    <td>Earned HRA</td>
                    <td class="amount-cell">{{ number_format($payslip->hra, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->hra, 0) }}</td>
                    <td>Employee ESIC</td>
                    <td class="amount-cell">{{ number_format($payslip->esi, 0) }}</td>
                </tr>
                <tr>
                    <td>Earned Conveyance</td>
                    <td class="amount-cell">{{ number_format($payslip->conveyance, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->conveyance, 0) }}</td>
                    <td>Professional Tax</td>
                    <td class="amount-cell">{{ number_format($payslip->tax, 0) }}</td>
                </tr>
                <tr>
                    <td>Earned DA</td>
                    <td class="amount-cell">{{ number_format($payslip->da, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->da, 0) }}</td>
                    <td>Welfare Fund</td>
                    <td class="amount-cell">{{ number_format($payslip->welfare, 0) }}</td>
                </tr>
                <tr>
                    <td>Earned Medical Allowance</td>
                    <td class="amount-cell">{{ number_format($payslip->medical, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->medical, 0) }}</td>
                    <td>LOP Deduction</td>
                    <td class="amount-cell">{{ number_format($lopDeductionAmount, 0) }}</td>
                </tr>
                <tr>
                    <td>Earned Special Allowance</td>
                    <td class="amount-cell">{{ number_format($payslip->allowance, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->allowance, 0) }}</td>
                    <td>TDS</td>
                    <td class="amount-cell">{{ number_format($payslip->tds, 0) }}</td>
                </tr>
                @if($payslip->overtime_amount > 0)
                <tr>
                    <td>Overtime Amount</td>
                    <td class="amount-cell">{{ number_format($payslip->overtime_amount, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->overtime_amount, 0) }}</td>
                    <td class="empty-cell"></td>
                    <td class="empty-cell"></td>
                </tr>
                @endif
                @if($payslip->dynamic_additions > 0)
                <tr>
                    <td>Other Additions</td>
                    <td class="amount-cell">{{ number_format($payslip->dynamic_additions, 0) }}</td>
                    <td class="amount-cell">{{ number_format($payslip->dynamic_additions, 0) }}</td>
                    <td class="empty-cell"></td>
                    <td class="empty-cell"></td>
                </tr>
                @endif
                @if($payslip->dynamic_deductions > 0)
                <tr>
                    <td class="empty-cell"></td>
                    <td class="empty-cell"></td>
                    <td class="empty-cell"></td>
                    <td>Other Deductions</td>
                    <td class="amount-cell">{{ number_format($payslip->dynamic_deductions, 0) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Arrears Amount</td>
                    <td class="amount-cell">0</td>
                    <td class="amount-cell">0</td>
                    <td class="empty-cell"></td>
                    <td class="empty-cell"></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Gross Total</strong></td>
                    <td class="amount-cell"><strong>{{ number_format($payslip->total_earnings, 0) }}</strong></td>
                    <td class="amount-cell"><strong>{{ number_format($payslip->total_earnings, 0) }}</strong></td>
                    <td><strong>Total Deductions</strong></td>
                    <td class="amount-cell"><strong>{{ number_format($payslip->total_deductions, 0) }}</strong></td>
                </tr>
            </tbody>
        </table>

      
        <div class="net-pay">
            Net Pay Rs.{{ number_format($payslip->net_salary, 0) }}/- ({{ ucwords((new \App\Http\Controllers\Backend\Hr\AutomatedPayslipController())->convertNumberToWords($payslip->net_salary)) }} Rupees Only)
        </div>

        
        <div class="footer-note">
            "This is a computer generated Payslip. Hence, Signature is not required."
        </div>
    </div>
</body>
</html>