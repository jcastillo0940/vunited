<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'zone_id', 'seat_id', 'quantity', 'status', 'expires_at', 'released_at'])]
class Hold extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function isExpired(): bool
    {
        return $this->status === 'active' && $this->expires_at->isPast();
    }
}
