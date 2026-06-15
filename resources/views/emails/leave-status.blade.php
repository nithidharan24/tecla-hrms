<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
<div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px;">
    <h2 style="color:{{ $status === 'approved' ? '#28a745' : '#dc3545' }};">
        Leave Request {{ ucfirst($status) }}
    </h2>
    <p>Dear <strong>{{ $employeeName }}</strong>,</p>
    <p>Your leave request has been <strong>{{ strtoupper($status) }}</strong>.</p>
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
        <tr style="background:#f8f8f8;"><td style="padding:8px; border:1px solid #ddd;"><strong>Leave Type</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['leave_type'] }}</td></tr>
        <tr><td style="padding:8px; border:1px solid #ddd;"><strong>From</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['from_date'] }}</td></tr>
        <tr style="background:#f8f8f8;"><td style="padding:8px; border:1px solid #ddd;"><strong>To</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['to_date'] }}</td></tr>
        <tr><td style="padding:8px; border:1px solid #ddd;"><strong>Days</strong></td><td style="padding:8px; border:1px solid #ddd;">{{ $details['no_of_days'] }}</td></tr>
    </table>
    <p style="margin-top:20px; color:#888; font-size:12px;">This is an automated notification from the HRMS system.</p>
</div>
</body>
</html>