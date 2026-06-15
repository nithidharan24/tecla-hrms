<!DOCTYPE html>
<html>
<head>
    <title>Testing Ticket Assigned</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .ticket-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .ticket-details table {
            width: 100%;
        }
        .ticket-details td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .ticket-details td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }
        .priority-medium {
            color: #ffc107;
            font-weight: bold;
        }
        .priority-low {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Testing Ticket Assigned</h2>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $assignedEmployee->firstname }} {{ $assignedEmployee->lastname }}</strong>,</p>
            
            <p>A new testing ticket has been assigned to you by <strong>{{ $assignedBy }}</strong>.</p>
            
            <div class="ticket-details">
                <h3>Ticket Details:</h3>
                <table>
                    <tr>
                        <td>Ticket ID:</td>
                        <td><strong>{{ $ticket->testing_ticket_id }}</strong></td>
                    </tr>
                    <tr>
                        <td>Project:</td>
                        <td>{{ $ticket->projectname ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Module:</td>
                        <td>{{ $ticket->module_name }}</td>
                    </tr>
                    <tr>
                        <td>Priority:</td>
                        <td class="priority-{{ strtolower($ticket->priority) }}">{{ $ticket->priority }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>{{ $ticket->status }}</td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td>{{ Str::limit($ticket->description, 200) }}</td>
                    </tr>
                    @if($ticket->steps_to_reproduce)
                    <tr>
                        <td>Steps to Reproduce:</td>
                        <td>{{ Str::limit($ticket->steps_to_reproduce, 200) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Created Date:</td>
                        <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Please log in to the system to view and work on this ticket.</p>
            
            <a href="{{ url('/testing/' . $ticket->id) }}" class="button">View Ticket Details</a>
            
            <p style="margin-top: 20px;">Thank you for your attention to this matter.</p>
            
            <p>Best Regards,<br>
            {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>