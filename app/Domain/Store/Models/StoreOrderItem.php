<?php

namespace App\Domain\Store\Models;

use Database\Factories\StoreOrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'store_order_id',
    'product_id',
    'product_name',
    'product_sku',
    'unit_price',
    'quantity',
    'line_total',
    'metadata',
])]
class StoreOrderItem extends Model
{
    /** @use HasFactory<StoreOrderItemFactory> */
    use HasFactory;

    protected static function newFactory(): StoreOrderItemFactory
    {
        return StoreOrderItemFactory::new();
    }

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(StoreOrder::class, 'store_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
