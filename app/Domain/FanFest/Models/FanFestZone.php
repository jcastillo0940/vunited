<?php

namespace App\Domain\FanFest\Models;

use Database\Factories\FanFestZoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'fan_fest_event_id',
    'name',
    'description',
    'icon',
    'sort_order',
    'is_active',
    'metadata',
])]
class FanFestZone extends Model
{
    /** @use HasFactory<FanFestZoneFactory> */
    use HasFactory;

    protected static function newFactory(): FanFestZoneFactory
    {
        return FanFestZoneFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'metadata'   => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(FanFestEvent::class, 'fan_fest_event_id');
    }
}
