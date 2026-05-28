<?php

namespace App\Domain\Store\Models;

use App\Domain\Store\Enums\StoreOrderStatus;
use Database\Factories\StoreOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable([
    'order_number',
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
class StoreOrder extends Model
{
    /** @use HasFactory<StoreOrderFactory> */
    use HasFactory;

    protected static function newFactory(): StoreOrderFactory
    {
        return StoreOrderFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => StoreOrderStatus::class,
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
            'metadata' => 'array',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreOrderItem::class);
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(\App\Domain\Payments\Models\Payment::class, 'payable');
    }
}
