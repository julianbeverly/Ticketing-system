<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 20px; }
        .ticket-info { background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .footer { font-size: 0.9rem; color: #666; border-top: 1px solid #eee; padding-top: 15px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #059669; margin: 0;">Ticket Resolved</h2>
        </div>
        
        <p>Dear {{ $ticket->user->name }},</p>
        
        <p>Your IT ticket has been resolved by the technician.</p>
        
        <div class="ticket-info">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</li>
                <li><strong>Subject:</strong> {{ $ticket->subject }}</li>
                <li><strong>Resolution Type:</strong> {{ $ticket->resolution_type }}</li>
                <li><strong>Resolved At:</strong> {{ $ticket->resolved_at->format('M d, Y h:i A') }}</li>
            </ul>
        </div>
        
        <p>Please log in to your dashboard to review the solution either by:</p>
        <ul>
            <li><strong>Accepting the solution</strong>: Ticket will be Closed</li>
            <li><strong>Rejecting the solution</strong>: Ticket will be Reopened for further action</li>
        </ul>
        
        <div class="footer">
            <p>Thank you,<br>
            <strong>Resolve</strong></p>
        </div>
    </div>
</body>
</html>
