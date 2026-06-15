<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Company - Login Credentials</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }
        
        .email-wrapper {
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #e2e8f0;
        }
        
        .header {
            background-color: #ffffff;
            border-bottom: 3px solid #ea580c;
            padding: 40px 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        
        .header p {
            margin: 8px 0 0 0;
            font-size: 15px;
            color: #64748b;
        }
        
        .content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #0f172a;
        }
        
        .welcome-message {
            margin-bottom: 32px;
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
        }
        
        .credentials-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 32px;
            margin: 32px 0;
        }
        
        .credentials-box h3 {
            color: #0f172a;
            margin-bottom: 24px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .credential-item {
            margin-bottom: 20px;
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .credential-label {
            font-weight: 600;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .credential-value {
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace;
            font-size: 15px;
            color: #0f172a;
            font-weight: 500;
            word-break: break-all;
        }
        
        .password-notice {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 20px;
            margin: 32px 0;
            border-radius: 8px;
            color: #9a3412;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .password-notice strong {
            color: #9a3412;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }
        
        .next-steps {
            margin: 32px 0;
        }
        
        .next-steps h3 {
            color: #0f172a;
            margin-bottom: 16px;
            font-size: 16px;
            font-weight: 600;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        
        .next-steps li {
            padding: 12px 0;
            padding-left: 32px;
            position: relative;
            color: #475569;
            font-size: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .next-steps li:last-child {
            border-bottom: none;
        }
        
        .next-steps li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #ea580c;
            font-weight: bold;
        }
        
        .action-area {
            text-align: center;
            margin: 40px 0;
        }

        .login-button {
            display: inline-block;
            background-color: #ea580c;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.2s;
        }
        
        .login-button:hover {
            background-color: #c2410c;
        }
        
        .support-info {
            border-top: 1px solid #e2e8f0;
            padding-top: 32px;
            margin-top: 32px;
        }
        
        .support-info h4 {
            color: #0f172a;
            margin-bottom: 12px;
            font-size: 15px;
            font-weight: 600;
        }
        
        .support-info p {
            color: #64748b;
            font-size: 14px;
            margin: 4px 0;
        }
        
        .footer {
            padding: 32px 20px;
            text-align: center;
        }
        
        .footer p {
            color: #94a3b8;
            font-size: 13px;
            margin: 4px 0;
        }
        
        .footer .company-name {
            font-weight: 600;
            color: #64748b;
            margin-top: 12px;
        }
        
        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            .content {
                padding: 30px 20px;
            }
            .header {
                padding: 30px 20px 20px;
            }
            .credentials-box {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="header">
                <h1>Welcome to the Team</h1>
                <p>Your employee account has been provisioned</p>
            </div>
            
            <!-- Main Content -->
            <div class="content">
                <div class="greeting">
                    Hello {{ $employeeData['firstName'] }},
                </div>
                
                <div class="welcome-message">
                    <p>We're thrilled to have you onboard. Your employee account for the HRMS portal has been successfully created. Please use the credentials provided below to access your account.</p>
                </div>
                
                <!-- Credentials Box -->
                <div class="credentials-box">
                    <h3>Authentication Details</h3>
                    
                    <div class="credential-item">
                        <div class="credential-label">Login Email Address</div>
                        <div class="credential-value">{{ $employeeData['email'] }}</div>
                    </div>
                    
                    <div class="credential-item">
                        <div class="credential-label">Temporary Password</div>
                        <div class="credential-value">{{ $employeeData['password'] }}</div>
                    </div>
                    
                    <div class="credential-item">
                        <div class="credential-label">System Access URL</div>
                        <div class="credential-value">{{ url('/') }}</div>
                    </div>
                </div>
                
                <!-- Important Security Notice -->
                <div class="password-notice">
                    <strong>Important Security Notice</strong>
                    For your security, you are required to change your temporary password immediately upon your first login. Please select a strong, unique password.
                </div>
                
                <!-- Login Button -->
                <div class="action-area">
                    <a href="{{ url('/') }}" class="login-button">
                        Sign In to HRMS
                    </a>
                </div>
                
                <!-- Next Steps -->
                <div class="next-steps">
                    <h3>Recommended Next Steps</h3>
                    <ul>
                        <li>Sign in to your account using the button above</li>
                        <li>Update your temporary password</li>
                        <li>Complete your personal profile information</li>
                        <li>Review our company policies and guidelines</li>
                    </ul>
                </div>
                
                <!-- Support Information -->
                <div class="support-info">
                    <h4>Technical Support</h4>
                    <p>If you encounter any difficulties accessing your account, please reach out to our IT support desk:</p>
                    <p>Email: support@company.com &nbsp;&nbsp;|&nbsp;&nbsp; Phone: +1 (555) 123-4567</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>This is an automated system notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Our Company. All rights reserved.</p>
            <p class="company-name">Powered by Tecla HRMS</p>
        </div>
    </div>
</body>
</html>