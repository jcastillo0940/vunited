<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use Database\Factories\IssuedTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ticket_order_id',
    'ticket_order_item_id',
    'token',
    'qr_payload',
    'zone_name',
    'seat_label',
    'status',
    'issued_at',
    'used_at',
    'voided_at',
    'metadata',
])]
class IssuedTicket extends Model
{
    /** @use HasFactory<IssuedTicketFactory> */
    use HasFactory;

    protected static function newFactory(): IssuedTicketFactory
    {
        return IssuedTicketFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status'    => IssuedTicketStatus::class,
            'issued_at' => 'datetime',
            'used_at'   => 'datetime',
            'voided_at' => 'datetime',
            'metadata'  => 'array',
        ];
    }

    public function ticketOrder(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class);
    }

    public function ticketOrderItem(): BelongsTo
    {
        return $this->belongsTo(TicketOrderItem::class);
    }
}
