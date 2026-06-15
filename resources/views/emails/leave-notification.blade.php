{{-- resources/views/emails/leave-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Notification</title>
</head>
<body>
    <h2>Leave Request Notification</h2>
    
    <p>Dear {{ $managerName ?? 'Manager' }},</p>
    
    <p>{{ $employeeName }} (Employee ID: {{ $employeeId }}) has submitted a leave request with the following details:</p>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Leave Type</th>
            <td>{{ $details['leave_type'] }}</td>
        </tr>
        <tr>
            <th>From Date</th>
            <td>{{ $details['from_date'] }}</td>
        </tr>
        <tr>
            <th>To Date</th>
            <td>{{ $details['to_date'] }}</td>
        </tr>
        <tr>
            <th>Number of Days</th>
            <td>{{ $details['num_days'] }}</td>
        </tr>
        <tr>
            <th>Reason</th>
            <td>{{ $details['leave_reason'] }}</td>
        </tr>
    </table>
    
    <p>Please review this request in the system.</p>
    
    <br>
    <p>Regards,<br>HR System</p>
</body>
</html>