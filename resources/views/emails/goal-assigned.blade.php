<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Goal Assigned</title>
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
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fc;
            padding: 20px;
            border: 1px solid #e3e6f0;
            border-radius: 0 0 5px 5px;
        }
        .goal-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #4e73df;
        }
        .info-row {
            margin-bottom: 10px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #4e73df;
            display: inline-block;
            width: 120px;
        }
        .value {
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #858796;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .priority-high {
            color: #e74a3b;
            font-weight: bold;
        }
        .priority-critical {
            color: #e74a3b;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-medium {
            color: #f6c23e;
            font-weight: bold;
        }
        .priority-low {
            color: #1cc88a;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Goal Assigned</h2>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $employeeName }}</strong>,</p>
        
        <p>A new goal has been assigned to you by <strong>{{ $assignedByName }}</strong>. Please review the details below:</p>
        
        <div class="goal-details">
            <h3 style="color: #4e73df; margin-top: 0;">{{ $goal->goal_title }}</h3>
            
            <div class="info-row">
                <span class="label">Description:</span>
                <span class="value">{{ $goal->goal_description }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Goal Type:</span>
                <span class="value">{{ $goal->goal_type }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Category:</span>
                <span class="value">{{ $goal->category }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Priority:</span>
                <span class="value priority-{{ strtolower($goal->priority) }}">{{ $goal->priority }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Start Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($goal->start_date)->format('d M Y') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">End Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($goal->end_date)->format('d M Y') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Target:</span>
                <span class="value">{{ $goal->target_value }} {{ $goal->unit }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Weightage:</span>
                <span class="value">{{ $goal->weightage }}%</span>
            </div>
            
            <div class="info-row">
                <span class="label">Review Cycle:</span>
                <span class="value">{{ $goal->review_cycle }}</span>
            </div>
            
            @if($goal->remarks)
            <div class="info-row">
                <span class="label">Remarks:</span>
                <span class="value">{{ $goal->remarks }}</span>
            </div>
            @endif
        </div>
        
        <p>Please log in to the HRMS system to track your progress and update your achievements regularly.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('goals.index') }}" class="button">View My Goals</a>
        </div>
        
        <p>If you have any questions or need clarification, please reach out to your manager or the HR department.</p>
        
        <p>Best regards,<br>
        HRMS Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>