<div class="content container-fluid" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; max-width: 800px; margin: 0 auto;">
    <div class="payslip-container" style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);">
        <!-- Header Section -->
        <div class="payslip-header" style="background-color: #2c3e50; padding: 15px 20px; color: white;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ public_path('admin/assets/img/logo.png') }}" alt="Company Logo" style="height: 40px; margin-bottom: 10px;">
                    <h2 class="company-name" style="font-weight: 600; margin: 5px 0; font-size: 18px;">Dreamguy's Technologies</h2>
                    <p style="margin: 0; font-size: 11px; opacity: 0.8;">3864 Quiet Valley Lane, Sherman Oaks, CA, 91403</p>
                </div>
                <div class="col-md-6 text-end">
                    <h1 class="payslip-title" style="font-weight: 700; margin: 0; font-size: 24px; color: white;">PAYSLIP</h1>
                    <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.9;">Salary Month: {{ $salaryMonth }}</p>
                </div>
            </div>
        </div>

        <!-- Employee Details Section -->
        <div class="employee-details" style="padding: 20px; background-color: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
            <h3 style="font-weight: 600; margin-bottom: 15px; font-size: 14px; color: #2c3e50; text-transform: uppercase;">Employee Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item" style="margin-bottom: 8px;">
                        <span style="font-weight: 600; display: inline-block; width: 120px;">Employee Name:</span>
                        <span>{{ $employee->firstname }} {{ $employee->lastname }}</span>
                    </div>
                    <div class="detail-item" style="margin-bottom: 8px;">
                        <span style="font-weight: 600; display: inline-block; width: 120px;">Employee ID:</span>
                        <span>{{ $employee->employeeid }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item" style="margin-bottom: 8px;">
                        <span style="font-weight: 600; display: inline-block; width: 120px;">Designation:</span>
                        <span>{{ $employee->designation_name }}</span>
                    </div>
                    <div class="detail-item" style="margin-bottom: 8px;">
                        <span style="font-weight: 600; display: inline-block; width: 120px;">Joining Date:</span>
                        <span>{{ $employee->joiningdate }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions Section -->
        <div class="salary-details" style="padding: 20px;">
            <div class="row">
                <!-- Earnings Column -->
                <div class="col-md-6" style="padding-right: 15px;">
                    <div class="earnings-box" style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
                        <div class="section-header" style="background-color: #18a689; padding: 8px 15px; color: white;">
                            <h4 style="font-weight: 600; margin: 0; font-size: 14px;">EARNINGS</h4>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <tbody>
                                @foreach ($earnings as $earning)
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td style="padding: 10px 15px;">{{ $earning['label'] }}</td>
                                    <td style="padding: 10px 15px; text-align: right; font-weight: 500;">${{ number_format((float) $earning['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 600;">Total Earnings</td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 600;">${{ number_format($totalEarnings, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="col-md-6" style="padding-left: 15px;">
                    <div class="deductions-box" style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
                        <div class="section-header" style="background-color: #e74c3c; padding: 8px 15px; color: white;">
                            <h4 style="font-weight: 600; margin: 0; font-size: 14px;">DEDUCTIONS</h4>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <tbody>
                                @foreach ($deductions as $deduction)
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td style="padding: 10px 15px;">{{ $deduction['label'] }}</td>
                                    <td style="padding: 10px 15px; text-align: right; font-weight: 500;">${{ number_format((float) $deduction['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 600;">Total Deductions</td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: 600;">${{ number_format($totalDeductions, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Salary Section -->
        <div class="net-salary" style="padding: 20px; background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
            <div class="row">
                <div class="col-md-8">
                    <p style="margin: 0; font-size: 12px; color: #555;">{{ $netSalaryInWords }} only.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="net-amount" style="background-color: #2c3e50; color: white; padding: 10px 15px; border-radius: 4px; display: inline-block;">
                        <span style="font-weight: 500; margin-right: 10px;">NET SALARY</span>
                        <span style="font-weight: 700; font-size: 16px;">${{ number_format($netSalary, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="payslip-footer" style="padding: 15px 20px; background-color: #2c3e50; color: white; text-align: center;">
            <p style="margin: 0; font-size: 11px; opacity: 0.8;">
                This is computer generated payslip and does not require signature.
                <br>For any discrepancies, please contact HR department within 7 days.
            </p>
        </div>
    </div>
</div>