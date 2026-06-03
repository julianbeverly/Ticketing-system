<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', 'technician')->get()->map(function ($tech) {
            $assignedCount   = Ticket::where('technician_id', $tech->id)->where('status', 'assigned')->count();
            $inprogressCount = Ticket::where('technician_id', $tech->id)->where('status', 'in_progress')->count();
            $closedCount     = Ticket::where('technician_id', $tech->id)->where('status', 'closed')->count();

            return [
                'Name' => $tech->name,
                'Assigned' => $assignedCount,
                'In Progress' => $inprogressCount,
                'Closed' => $closedCount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Assigned',
            'In Progress',
            'Closed'
        ];
    }
}
