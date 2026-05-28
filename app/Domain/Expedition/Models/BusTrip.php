<?php

namespace App\Domain\Expedition\Models;

use App\Domain\Ticketing\Models\MatchEvent;
use Database\Factories\BusTripFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'match_event_id',
    'departure_location',
    'departure_time',
    'return_time',
    'price',
    'currency',
    'capacity',
    'available_seats',
    'is_active',
    'metadata',
])]
class BusTrip extends Model
{
    /** @use HasFactory<BusTripFactory> */
    use HasFactory;

    protected static function newFactory(): BusTripFactory
    {
        return BusTripFactory::new();
    }

    protected function casts(): array
    {
        return [
            'departure_time'  => 'datetime',
            'return_time'     => 'datetime',
            'price'           => 'decimal:2',
            'capacity'        => 'integer',
            'available_seats' => 'integer',
            'is_active'       => 'boolean',
            'metadata'        => 'array',
        ];
    }

    public function matchEvent(): BelongsTo
    {
        return $this->belongsTo(MatchEvent::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->available_seats > 0;
    }
}
