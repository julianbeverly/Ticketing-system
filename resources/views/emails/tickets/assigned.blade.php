<x-mail::message>
# Ticket Assigned

Hello {{ $ticket->technician->name }},

You have been assigned to a support ticket.

**Ticket ID:** {{ $ticket->ticket_id }}  
**Subject:** {{ $ticket->subject }}  
**Employee:** {{ $ticket->user->name }}  
**Priority:** {{ strtoupper($ticket->priority) }}  
@if($ticket->reminder_interval)
**Reminder Interval:** {{ $ticket->reminder_interval }}
@endif



<!-- *CC: Admin, Employee* -->

Thank you,<br>
Resolve
</x-mail::message>
