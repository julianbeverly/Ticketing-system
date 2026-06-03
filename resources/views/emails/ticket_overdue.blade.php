<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { border-bottom: 2px solid #dc2626; padding-bottom: 10px; margin-bottom: 20px; }
        .ticket-info { background: #fef2f2; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fee2e2; }
        .footer { font-size: 0.9rem; color: #666; border-top: 1px solid #eee; padding-top: 15px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #dc2626; margin: 0;">Overdue Ticket Notification</h2>
        </div>
        
        <p>Dear {{ $ticket->user->name }},</p>
        
        <p>This is an automated notification to inform you that the following ticket has exceeded its resolution deadline and is now marked as <strong>OVERDUE</strong>.</p>
        
        <div class="ticket-info">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</li>
                <li><strong>Subject:</strong> {{ $ticket->subject }}</li>
                <li><strong>Priority:</strong> {{ strtoupper($ticket->priority) }}</li>
                <li><strong>Deadline:</strong> {{ $ticket->due_at->format('M d, Y h:i A') }}</li>
                <li><strong>Technician:</strong> {{ $ticket->technician ? $ticket->technician->name : 'Unassigned' }}</li>
            </ul>
        </div>
        
        <p>The IT administration and the assigned technician have been notified. We are working to prioritize this issue immediately.</p>
        
        <div class="footer">
            <p>Thank you,<br>
            <strong>Resolve</strong></p>
        </div>
    </div>
</body>
</html>
