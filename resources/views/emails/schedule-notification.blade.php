<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule Notification</title>
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
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .schedule-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .interchange-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #007bff;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }
        .status-approved {
            background-color: #28a745;
        }
        .status-rejected {
            background-color: #dc3545;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if(str_starts_with($data['type'], 'interchange_'))
                @if($data['type'] === 'interchange_request')
                    🔄 Shift Interchange Request
                @elseif($data['type'] === 'interchange_approved')
                    ✅ Shift Interchange Approved
                @elseif($data['type'] === 'interchange_rejected')
                    ❌ Shift Interchange Rejected
                @endif
            @else
                @if($data['type'] === 'created')
                    📅 New Schedule Assignment
                @elseif($data['type'] === 'updated')
                    ✏️ Schedule Updated
                @elseif($data['type'] === 'new_version')
                    🔄 New Upcoming Schedule
                @elseif($data['type'] === 'cancelled')
                    ❌ Schedule Cancelled
                @endif
            @endif
        </h1>
    </div>

    <div class="content">
        @if(str_starts_with($data['type'], 'interchange_'))
            {{-- Interchange Request Notifications --}}
            <p>Dear {{ $data['is_requester'] ? $data['requester_name'] : $data['target_name'] }},</p>
            
            @if($data['type'] === 'interchange_request')
                <div class="alert alert-info">
                    <strong>Action Required:</strong> You have received a shift interchange request.
                </div>
                <p>{{ $data['requester_name'] }} has requested to interchange shifts with you on {{ $data['interchange_date'] }}.</p>
            @else
                <p>
                    Your shift interchange request for <strong>{{ $data['interchange_date'] }}</strong> has been 
                    <span class="status-badge status-{{ $data['status'] }}">{{ $data['status'] }}</span>.
                </p>
            @endif
            
            <div class="interchange-details">
                <h3>Interchange Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $data['interchange_date'] }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">{{ $data['is_requester'] ? 'Your Original Shift' : 'Their Original Shift' }}:</span>
                    <span class="detail-value">
                        {{ $data['is_requester'] ? $data['requester_original_shift']['name'] : $data['target_original_shift']['name'] }}
                        ({{ $data['is_requester'] ? $data['requester_original_shift']['start'] : $data['target_original_shift']['start'] }} - 
                        {{ $data['is_requester'] ? $data['requester_original_shift']['end'] : $data['target_original_shift']['end'] }})
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">{{ $data['is_requester'] ? 'Their Original Shift' : 'Your Original Shift' }}:</span>
                    <span class="detail-value">
                        {{ $data['is_requester'] ? $data['target_original_shift']['name'] : $data['requester_original_shift']['name'] }}
                        ({{ $data['is_requester'] ? $data['target_original_shift']['start'] : $data['requester_original_shift']['start'] }} - 
                        {{ $data['is_requester'] ? $data['target_original_shift']['end'] : $data['requester_original_shift']['end'] }})
                    </span>
                </div>
                
                @if($data['status'] === 'approved')
                    <div class="detail-row">
                        <span class="detail-label">{{ $data['is_requester'] ? 'Your New Shift' : 'Their New Shift' }}:</span>
                        <span class="detail-value">
                            {{ $data['is_requester'] ? $data['target_original_shift']['name'] : $data['requester_original_shift']['name'] }}
                            ({{ $data['is_requester'] ? $data['target_original_shift']['start'] : $data['requester_original_shift']['start'] }} - 
                            {{ $data['is_requester'] ? $data['target_original_shift']['end'] : $data['requester_original_shift']['end'] }})
                        </span>
                    </div>
                @endif
                
                @if(!empty($data['reason']))
                    <div class="detail-row">
                        <span class="detail-label">Reason:</span>
                        <span class="detail-value">{{ $data['reason'] }}</span>
                    </div>
                @endif
                
                @if(!empty($data['admin_notes']))
                    <div class="detail-row">
                        <span class="detail-label">Admin Notes:</span>
                        <span class="detail-value">{{ $data['admin_notes'] }}</span>
                    </div>
                @endif
            </div>
            
            @if($data['type'] === 'interchange_request')
                <div class="alert alert-warning">
                    <strong>Note:</strong> Please respond to this request through the scheduling system.
                </div>
            @else
                <p>If you have any questions about this decision, please contact your supervisor.</p>
            @endif
            
        @else
            {{-- Regular Schedule Notifications --}}
            <p>Dear {{ $data['employee_name'] }},</p>

            @if($data['type'] === 'created')
                <p>A new schedule has been assigned to you. Please review the details below:</p>
            @elseif($data['type'] === 'updated')
                <p>Your current schedule has been updated. Please review the changes below:</p>
            @elseif($data['type'] === 'new_version')
                <div class="alert alert-info">
                    <strong>Important:</strong> This is your upcoming schedule that will take effect after your current schedule ends.
                </div>
                <p>A new schedule has been created for you. Please review the details below:</p>
            @elseif($data['type'] === 'cancelled')
                <div class="alert alert-warning">
                    <strong>Notice:</strong> Your schedule has been cancelled.
                </div>
                <p>Your schedule from {{ $data['schedule_start'] }} to {{ $data['schedule_end'] }} has been cancelled.</p>
            @endif

            @if($data['type'] !== 'cancelled')
                <div class="schedule-details">
                    <h3>Schedule Details</h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Employee:</span>
                        <span class="detail-value">{{ $data['employee_name'] }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Department:</span>
                        <span class="detail-value">{{ $data['department'] }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Shift:</span>
                        <span class="detail-value">{{ $data['shift_name'] }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Working Hours:</span>
                        <span class="detail-value">{{ $data['start_time'] }} - {{ $data['end_time'] }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Schedule Period:</span>
                        <span class="detail-value">{{ $data['schedule_start'] }} to {{ $data['schedule_end'] }}</span>
                    </div>
                </div>
            @endif

            @if($data['type'] === 'created')
                <div class="alert alert-success">
                    <strong>Action Required:</strong> Please confirm receipt of this schedule and contact HR if you have any questions.
                </div>
                <p>This schedule was created by {{ $data['created_by'] }} on {{ $data['created_date'] }}.</p>
            @elseif($data['type'] === 'updated')
                <div class="alert alert-info">
                    <strong>Changes Made:</strong> Your schedule has been modified. Please review the updated details above.
                </div>
                <p>This schedule was updated by {{ $data['updated_by'] }} on {{ $data['updated_date'] }}.</p>
            @elseif($data['type'] === 'new_version')
                <div class="alert alert-info">
                    <strong>Timeline:</strong> This new schedule will automatically take effect after your current schedule ends.
                </div>
                <p>This schedule was created by {{ $data['updated_by'] }} on {{ $data['updated_date'] }}.</p>
            @elseif($data['type'] === 'cancelled')
                <p>This schedule was cancelled by {{ $data['cancelled_by'] }} on {{ $data['cancelled_date'] }}.</p>
                <p>Please contact HR for more information or to discuss alternative arrangements.</p>
            @endif
        @endif

        <p>If you have any questions or concerns, please contact the HR department immediately.</p>
        
        <p>Best regards,<br>
        HR Department</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'Company Name') }}. All rights reserved.</p>
    </div>
</body>
</html>