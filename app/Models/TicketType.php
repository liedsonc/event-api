<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'max_persons_per_ticket',
        'available_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_ticket_types')
            ->withPivot(['event_price', 'quantity_available', 'is_active'])
            ->withTimestamps();
    }

    public function isGroupTicket(): bool
    {
        return $this->max_persons_per_ticket > 1;
    }

    public function isUnlimited(): bool
    {
        return $this->available_quantity === null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedGroupSizeAttribute(): string
    {
        if ($this->max_persons_per_ticket === 1) {
            return 'Individual';
        }
        return $this->max_persons_per_ticket . ' persons';
    }
} 