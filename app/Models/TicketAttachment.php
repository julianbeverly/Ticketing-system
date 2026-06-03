<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    // Allow mass-assignment for these fields when storing attachments
    protected $fillable = [
        'ticket_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    /**
     * Get the parent ticket this attachment belongs to.
     * Each attachment is linked to exactly one ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
