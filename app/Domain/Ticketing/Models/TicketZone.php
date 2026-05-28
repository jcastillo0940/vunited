<?php

namespace App\Domain\Ticketing\Models;

use Database\Factories\TicketZoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'match_event_id',
    'name',
    'slug',
    'description',
    'price',
    'currency',
    'capacity',
    'available_quantity',
    'sort_order',
    'is_active',
    'metadata',
])]
class TicketZone extends Model
{
    /** @use HasFactory<TicketZoneFactory> */
    use HasFactory;

    protected static function newFactory(): TicketZoneFactory
    {
        return TicketZoneFactory::new();
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'capacity' => 'integer',
            'available_quantity' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function matchEvent(): BelongsTo
    {
        return $this->belongsTo(MatchEvent::class);
    }

    public function isOutOfStock(): bool
    {
        return $this->available_quantity !== null && (int) $this->available_quantity <= 0;
    }
}
