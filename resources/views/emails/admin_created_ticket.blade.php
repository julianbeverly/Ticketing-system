<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { background: #0b57d0; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .ticket-info { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { font-size: 0.8rem; color: #777; text-align: center; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #0b57d0; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Ticket Created For You</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new support ticket has been created on your behalf by <strong>{{ $adminName }}</strong>.</p>
            
            <div class="ticket-info">
                <p><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</p>
                <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
                <p><strong>Priority:</strong> {{ strtoupper($ticket->priority) }}</p>
                <p><strong>Status:</strong> OPEN</p>
            </div>



            <p>If you have any immediate questions, please feel free to reply to this message.</p>
            <p>Thank you,<br>Resolve</p>
        </div>
        <div class="footer">
            <p>This is an automated notification from the Resolve Ticketing System.</p>
        </div>
    </div>
</body>
</html>
