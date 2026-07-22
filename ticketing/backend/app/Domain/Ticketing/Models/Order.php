<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'event_id', 'customer_id', 'status', 'customer_email', 'customer_name', 'customer_phone',
    'currency', 'subtotal', 'total', 'idempotency_key', 'hold_expires_at',
    'paid_at', 'cancelled_at', 'refunded_at', 'payment_intent_id', 'payment_method',
    'cash_confirmed_by', 'cash_confirmed_at', 'metadata',
])]
class Order extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'hold_expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
            'cash_confirmed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignOrderNumber(): void
    {
        if ($this->order_number) {
            return;
        }
        // Basado en el id autoincremental (ya unico y garantizado por la
        // base), nunca en un COUNT() que puede colisionar bajo concurrencia.
        $this->order_number = sprintf('TCK-%d-%06d', $this->created_at->year, $this->id);
        $this->saveQuietly();
    }
}
