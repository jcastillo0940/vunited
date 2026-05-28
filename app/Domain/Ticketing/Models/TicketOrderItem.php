<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Models\IssuedTicket;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_order_id',
    'ticket_zone_id',
    'zone_name',
    'unit_price',
    'quantity',
    'line_total',
    'metadata',
])]
class TicketOrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'quantity'   => 'integer',
            'metadata'   => 'array',
        ];
    }

    public function ticketOrder(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class);
    }

    public function ticketZone(): BelongsTo
    {
        return $this->belongsTo(TicketZone::class);
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }
}
