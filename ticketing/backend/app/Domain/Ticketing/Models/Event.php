<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code', 'home_team', 'away_team', 'competition', 'round_label', 'starts_at',
    'venue_name', 'venue_location', 'status', 'sales_open_at', 'sales_close_at',
    'is_visible', 'purchase_limit_per_buyer', 'metadata',
])]
class Event extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'sales_open_at' => 'datetime',
            'sales_close_at' => 'datetime',
            'is_visible' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function doors(): HasMany
    {
        return $this->hasMany(Door::class);
    }

    public function isOnSale(): bool
    {
        if ($this->status !== 'on_sale') {
            return false;
        }
        $now = now();
        if ($this->sales_open_at && $now->lt($this->sales_open_at)) {
            return false;
        }
        if ($this->sales_close_at && $now->gt($this->sales_close_at)) {
            return false;
        }

        return true;
    }
}
