<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Payments\Models\Payment;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\IssuedTicket;
use Database\Factories\TicketOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable([
    'order_number',
    'match_event_id',
    'status',
    'customer_name',
    'customer_email',
    'customer_phone',
    'subtotal',
    'discount_total',
    'tax_total',
    'total',
    'currency',
    'paid_at',
    'cancelled_at',
    'metadata',
])]
class TicketOrder extends Model
{
    /** @use HasFactory<TicketOrderFactory> */
    use HasFactory;

    protected static function newFactory(): TicketOrderFactory
    {
        return TicketOrderFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status'         => TicketOrderStatus::class,
            'subtotal'       => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total'      => 'decimal:2',
            'total'          => 'decimal:2',
            'paid_at'        => 'datetime',
            'cancelled_at'   => 'datetime',
            'metadata'       => 'array',
        ];
    }

    public function matchEvent(): BelongsTo
    {
        return $this->belongsTo(MatchEvent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TicketOrderItem::class);
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }
}
