<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class EmployeeReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Ticket::with(['category', 'type', 'technician'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {

            $isOverdue = $ticket->due_at &&
                now()->greaterThan($ticket->due_at) &&
                !in_array($ticket->status, ['resolved', 'closed']);

            return [
                'Ticket ID' => $ticket->ticket_id,
                'Subject' => $ticket->subject,
                'Category' => optional($ticket->category)->name,
                'Type' => optional($ticket->type)->name ?? $ticket->custom_type,
                'Assignee' => optional($ticket->technician)->name ?? 'Unassigned',
                'SLA Status' => $isOverdue ? 'Overdue' : 'Within SLA',
                'Status' => ucfirst($ticket->status),
                'Created At' => $ticket->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Subject',
            'Category',
            'Type',
            'Assignee',
            'SLA Status',
            'Status',
            'Created At'
        ];
    }
}
