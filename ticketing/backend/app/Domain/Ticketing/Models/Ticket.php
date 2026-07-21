<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_id', 'order_item_id', 'event_id', 'zone_id', 'seat_id', 'status',
    'qr_token', 'issued_at', 'used_at', 'revoked_at', 'revoked_reason', 'reissue_of_ticket_id',
])]
class Ticket extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function validationEvents(): HasMany
    {
        return $this->hasMany(ValidationEvent::class);
    }

    public function reissuedFrom(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'reissue_of_ticket_id');
    }
}
