<?php

namespace Tests\Feature\Api\Store;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_only_active_products(): void
    {
        $category = ProductCategory::factory()->active()->create(['slug' => 'camisetas']);
        Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Activo',
            'slug' => 'activo',
            'is_active' => true,
        ]);
        Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Inactivo',
            'slug' => 'inactivo',
            'is_active' => false,
        ]);

        $this->getJson('/api/store/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'activo');
    }

    public function test_api_returns_only_active_categories(): void
    {
        ProductCategory::factory()->create([
            'name' => 'Activa',
            'slug' => 'activa',
            'is_active' => true,
        ]);
        ProductCategory::factory()->create([
            'name' => 'Inactiva',
            'slug' => 'inactiva',
            'is_active' => false,
        ]);

        $this->getJson('/api/store/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'activa');
    }

    public function test_api_returns_featured_product(): void
    {
        Product::factory()->create([
            'name' => 'Destacado',
            'slug' => 'destacado',
            'is_featured' => true,
            'is_active' => true,
        ]);

        $this->getJson('/api/store/featured-product')
            ->assertOk()
            ->assertJsonPath('data.slug', 'destacado');
    }

    public function test_api_filters_products_by_category(): void
    {
        $shirts = ProductCategory::factory()->active()->create(['slug' => 'camisetas']);
        $accessories = ProductCategory::factory()->active()->create(['slug' => 'accesorios']);

        Product::factory()->create([
            'product_category_id' => $shirts->id,
            'slug' => 'camiseta-local',
            'is_active' => true,
        ]);
        Product::factory()->create([
            'product_category_id' => $accessories->id,
            'slug' => 'gorra-oficial',
            'is_active' => true,
        ]);

        $this->getJson('/api/store/products?category=camisetas')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'camiseta-local');
    }

    public function test_api_product_detail_by_slug_works(): void
    {
        $product = Product::factory()->create([
            'slug' => 'camiseta-local-2024',
            'is_active' => true,
        ]);

        $this->getJson('/api/store/products/camiseta-local-2024')
            ->assertOk()
            ->assertJsonPath('data.slug', 'camiseta-local-2024')
            ->assertJsonPath('data.name', $product->name);
    }

    public function test_api_returns_controlled_404_if_slug_not_found(): void
    {
        $this->getJson('/api/store/products/no-existe')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Producto no encontrado.',
            ]);
    }
}
