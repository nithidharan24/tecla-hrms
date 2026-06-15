<!DOCTYPE html>
<html>
<body>
    <p>Dear {{ $employee->firstname }},</p>
    <p>Please find attached your official memo titled <strong>{{ $memo->name }}</strong>.</p>
    <p>Regards,<br>HR Department</p>
</body>
</html>
