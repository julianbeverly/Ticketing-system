<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechOwnReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $tech = Auth::user();
// Count ALL tickets assigned to this technician
        $assignedCount   = Ticket::where('technician_id', $tech->id)->where('status', 'assigned')->count();
        $inprogressCount = Ticket::where('technician_id', $tech->id)->where('status', 'in_progress')->count();
        $closedCount     = Ticket::where('technician_id', $tech->id)->where('status', 'closed')->count();

        return collect([[
            'Name' => $tech->name,
            'Assigned' => $assignedCount,
            'In Progress' => $inprogressCount,
            'Closed' => $closedCount,
        ]]);
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
