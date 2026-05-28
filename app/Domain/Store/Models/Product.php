<?php

namespace App\Domain\Store\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_category_id',
    'sku',
    'name',
    'slug',
    'description',
    'short_description',
    'price',
    'compare_at_price',
    'currency',
    'stock_quantity',
    'track_stock',
    'is_featured',
    'is_active',
    'badge',
    'image_path',
    'gallery',
    'metadata',
    'sort_order',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'track_stock' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'gallery' => 'array',
            'metadata' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function isOutOfStock(): bool
    {
        return $this->track_stock && (int) ($this->stock_quantity ?? 0) <= 0;
    }
}
