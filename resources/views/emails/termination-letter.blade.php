<!DOCTYPE html>
<html>
<head>
    <title>{{ $emailSubject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .content { padding: 20px 0; }
        .footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; font-size: 0.9em; color: #777; }
        p { margin-bottom: 10px; }
        strong { color: #000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Termination of Employment</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>,</p>
            <p>This letter is to formally inform you of the termination of your employment, effective <strong>{{ \Carbon\Carbon::parse($termination->termination_date)->format('F d, Y') }}</strong>.</p>
            <p>Your last day of employment will be <strong>{{ \Carbon\Carbon::parse($termination->termination_date)->format('F d, Y') }}</strong>. Your notice period commenced on <strong>{{ \Carbon\Carbon::parse($termination->notice_date)->format('F d, Y') }}</strong>.</p>
            <p>The reason for this termination is: <strong>{{ $termination->reason }}</strong> (Type: {{ $termination->termination_type }}).</p>
            <p>We understand that this news may be difficult, and we want to assure you that this decision was made after careful consideration.</p>
            <p>During your employment, you served as a <strong>{{ $employee->designation_name }}</strong> in the <strong>{{ $employee->department_name }}</strong> department, joining us on <strong>{{ \Carbon\Carbon::parse($employee->joiningdate)->format('F d, Y') }}</strong>.</p>
            <p>Further details regarding your final pay, benefits, and any company property return will be communicated to you separately by the HR department. Please refer to the attached PDF for a formal copy of this termination letter.</p>
            <p>We wish you the best in your future endeavors.</p>
            <p>Sincerely,</p>
            <p><strong>HR Department</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>