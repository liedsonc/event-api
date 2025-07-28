<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'role',
        'can_view_tickets',
        'assigned_at',
    ];

    protected $casts = [
        'can_view_tickets' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAttendee(): bool
    {
        return $this->role === 'attendee';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function canViewTickets(): bool
    {
        return $this->can_view_tickets;
    }
} 