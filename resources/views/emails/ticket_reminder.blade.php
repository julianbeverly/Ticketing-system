<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 3px solid #004db5; border-radius: 10px 10px 0 0; }
        .header h1 { color: #004db5; margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .ticket-info { background-color: #f0f7ff; border-left: 4px solid #004db5; padding: 15px; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #004db5; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .urgent { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ticket Deadline Reminder</h1>
    </div>
    <div class="content">
        <p>Hello <strong>{{ $ticket->technician->name }}</strong>,</p>
        <p>This is a reminder that the following support ticket is approaching its resolution deadline.</p>
        
        <div class="ticket-info">
            <p><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</p>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p><strong>Priority:</strong> <span class="urgent">{{ strtoupper($ticket->priority) }}</span></p>
            <p><strong>Deadline:</strong> {{ $ticket->due_at->format('M d, Y h:i A') }} ({{ $ticket->due_at->diffForHumans() }})</p>
        </div>

        <p>Please ensure that the ticket is addressed and resolved within the SLA timeframe to maintain high service standards.</p>

        <p>Thank you,<br>Resolve</p>
    </div>
    <div class="footer">
        <p>This is an automated notification from the <strong>Resolve Ticketing System</strong>.</p>
        <p>&copy; {{ date('Y') }} Resolve. All rights reserved.</p>
    </div>
</body>
</html>
