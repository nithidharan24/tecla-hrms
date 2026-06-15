{{-- resources/views/emails/permission-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Permission Request Notification</title>
</head>
<body>
    <h2>Permission Request Notification</h2>
    
    <p>Dear {{ $managerName ?? 'Manager' }},</p>
    
    <p>{{ $employeeName }} (Employee ID: {{ $employeeId }}) has submitted a permission request with the following details:</p>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Permission Date</th>
            <td>{{ $details['permission_date'] }}</td>
        </tr>
        <tr>
            <th>Start Time</th>
            <td>{{ $details['start_time'] }}</td>
        </tr>
        <tr>
            <th>End Time</th>
            <td>{{ $details['end_time'] }}</td>
        </tr>
        <tr>
            <th>Duration</th>
            <td>{{ $details['duration'] }} hours</td>
        </tr>
        <tr>
            <th>Reason</th>
            <td>{{ $details['permission_reason'] }}</td>
        </tr>
    </table>
    
    <p>Please review this request in the system.</p>
    
    <br>
    <p>Regards,<br>HR System</p>
</body>
</html>