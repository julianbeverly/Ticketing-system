<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketReportExport implements FromCollection, WithHeadings
{
    // store filter values
    protected $status;
    protected $startDate;
    protected $endDate;

    public function __construct($status = null, $startDate = null, $endDate = null)
    {
        // store filters inside object
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    //    It talks to the Model to get the data
    public function collection()
    {
        $query = Ticket::with(['category', 'type', 'technician']);

        if ($this->status) {
            if (str_starts_with($this->status, 'tech_')) {
                $query->where('technician_id', str_replace('tech_', '', $this->status));
            } else {
                $query->where('status', $this->status);
            }
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->get()->map(function ($ticket) {

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
    //    It defines the top row of your Excel file
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
