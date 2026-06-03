<x-mail::message>
# New Support Ticket Created

A new support ticket has been submitted by an employee.

**Ticket ID:** {{ $ticket->ticket_id }}  
**Subject:** {{ $ticket->subject }}  
**Employee:** {{ $ticket->user->name }}  
**Priority:** {{ strtoupper($ticket->priority) }}



Thank you,<br>
Resolve
</x-mail::message>
