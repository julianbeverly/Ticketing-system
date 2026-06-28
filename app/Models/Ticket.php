<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Allow mass-assignment for these fields when creating/updating tickets
    protected $fillable = [
        'ticket_id',
        'user_id',
        'technician_id',
        'category_id',
        'type_id',
        'custom_type',
        'subject',
        'description',
        'priority',
        'status',
        'due_at',
        'reminder_interval',
        'last_reminder_at',
        'resolution_note',
        'resolution_type',
        'resolved_at',
        'rejection_note',
        'rejected_at',
        'sla_breached',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'resolved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sla_breached' => 'boolean',
    ];

    /**
     * Boot method to auto-generate the display ticket_id on creation.
     * Format: TC-{id + 1000} (e.g. TC-1001, TC-1002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($ticket) {
            // Only set ticket_id if it hasn't been manually set
            if (empty($ticket->ticket_id) || $ticket->ticket_id === 'TEMP') {
                $ticket->ticket_id = 'TC-' . ($ticket->id + 1000);
                $ticket->saveQuietly(); // Save without triggering events again
            }
        });
    }

    /**
     * Get the employee (user) who created this ticket.
     * Each ticket belongs to exactly one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the technician assigned to this ticket.
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get the incident category associated with this ticket.
     * A ticket optionally belongs to one category.
     */
    public function category()
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }

    /**
     * Get the incident type associated with this ticket.
     * A ticket optionally belongs to one type.
     */
    public function type()
    {
        return $this->belongsTo(IncidentType::class, 'type_id');
    }

    /**
     * Get all file attachments for this ticket.
     * A ticket can have multiple images and videos attached.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Get all activity log entries for this ticket.
     */
    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }
}
