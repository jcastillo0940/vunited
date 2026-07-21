<?php

namespace App\Domain\FanFest\Models;

use Database\Factories\FanFestEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'slug',
    'description',
    'event_date',
    'location',
    'hero_image_path',
    'schedule',
    'is_active',
    'metadata',
])]
class FanFestEvent extends Model
{
    /** @use HasFactory<FanFestEventFactory> */
    use HasFactory;

    protected static function newFactory(): FanFestEventFactory
    {
        return FanFestEventFactory::new();
    }

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'schedule'   => 'array',
            'is_active'  => 'boolean',
            'metadata'   => 'array',
        ];
    }

    public function zones(): HasMany
    {
        return $this->hasMany(FanFestZone::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allZones(): HasMany
    {
        return $this->hasMany(FanFestZone::class)->orderBy('sort_order');
    }
}
