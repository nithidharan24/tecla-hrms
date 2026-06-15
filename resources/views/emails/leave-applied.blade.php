<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
<div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px;">
    <h2 style="color:#ff5500;">Leave Request Notification</h2>
    <p>Dear <strong>{{ $recipientName }}</strong>,</p>
    <p><strong>{{ $employeeName }}</strong> (ID: {{ $employeeId }}) has submitted a leave request.</p>
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
        <tr style="background:#f8f8f8;"><td style="padding:8px; border:1px solid #ddd;"><strong>Leave Type</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['leave_type'] }}</td></tr>
        <tr><td style="padding:8px; border:1px solid #ddd;"><strong>From</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['from_date'] }}</td></tr>
        <tr style="background:#f8f8f8;"><td style="padding:8px; border:1px solid #ddd;"><strong>To</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['to_date'] }}</td></tr>
        <tr><td style="padding:8px; border:1px solid #ddd;"><strong>Days</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['num_days'] }}</td></tr>
        <tr style="background:#f8f8f8;"><td style="padding:8px; border:1px solid #ddd;"><strong>Reason</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['leave_reason'] }}</td></tr>
    </table>
    <p style="margin-top:20px; color:#888; font-size:12px;">This is an automated notification from the HRMS system.</p>
</div>
</body>
</html>