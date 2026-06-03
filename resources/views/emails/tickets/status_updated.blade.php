<x-mail::message>
# Ticket Status Updated

Hello {{ $ticket->user->name }},

The status of your support ticket has been updated by the technician.

**Ticket ID:** {{ $ticket->ticket_id }}  
**Subject:** {{ $ticket->subject }}  
**New Status:** {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}



Thank you,<br>
Resolve
</x-mail::message>
