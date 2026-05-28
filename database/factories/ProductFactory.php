<?php

namespace Database\Factories;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'product_category_id' => ProductCategory::factory(),
            'sku' => 'SKU-' . fake()->unique()->numerify('####'),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'price' => '65.00',
            'compare_at_price' => null,
            'currency' => 'USD',
            'stock_quantity' => 10,
            'track_stock' => false,
            'is_featured' => false,
            'is_active' => true,
            'badge' => 'NUEVO',
            'image_path' => 'products/example.jpg',
            'gallery' => ['products/example.jpg'],
            'metadata' => null,
            'sort_order' => 0,
        ];
    }
}
