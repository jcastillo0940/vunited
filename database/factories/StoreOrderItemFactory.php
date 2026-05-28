<?php

namespace Database\Factories;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\StoreOrder;
use App\Domain\Store\Models\StoreOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreOrderItem>
 */
class StoreOrderItemFactory extends Factory
{
    protected $model = StoreOrderItem::class;

    public function definition(): array
    {
        return [
            'store_order_id' => StoreOrder::factory(),
            'product_id' => Product::factory(),
            'product_name' => 'Camiseta Local Oficial',
            'product_sku' => 'SKU-1001',
            'unit_price' => '65.00',
            'quantity' => 1,
            'line_total' => '65.00',
            'metadata' => [
                'product_snapshot' => [
                    'slug' => 'camiseta-local-oficial',
                    'badge' => 'LOCAL',
                ],
            ],
        ];
    }
}
