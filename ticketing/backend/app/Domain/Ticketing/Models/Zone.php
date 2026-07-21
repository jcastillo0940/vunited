<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'event_id', 'name', 'slug', 'description', 'kind', 'price', 'currency',
    'capacity_total', 'capacity_available', 'capacity_held',
    'purchase_limit_per_buyer', 'sort_order', 'is_active',
])]
class Zone extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function isGeneralAdmission(): bool
    {
        return $this->kind === 'general';
    }
}
