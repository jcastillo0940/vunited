<?php

namespace Tests\Feature\Api\Store;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionedStoreApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_versioned_catalog_routes_expose_the_existing_contract(): void
    {
        $category = ProductCategory::factory()->active()->create([
            'slug' => 'camisetas',
        ]);

        Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Camiseta oficial',
            'slug' => 'camiseta-oficial',
            'is_active' => true,
            'is_featured' => true,
            'price' => '19.95',
            'compare_at_price' => '25.00',
            'currency' => 'usd',
        ]);

        $this->getJson('/api/v1/store/products')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'camiseta-oficial')
            ->assertJsonPath('data.0.price_minor', 1995)
            ->assertJsonPath('data.0.compare_at_price_minor', 2500)
            ->assertJsonPath('data.0.currency', 'USD')
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.price');

        $this->getJson('/api/v1/store/products/camiseta-oficial')
            ->assertOk()
            ->assertJsonPath('data.slug', 'camiseta-oficial');

        $this->getJson('/api/v1/store/categories')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'camisetas');

        $this->getJson('/api/v1/store/featured-product')
            ->assertOk()
            ->assertJsonPath('data.slug', 'camiseta-oficial');
    }

    public function test_versioned_store_does_not_expose_transactional_routes_yet(): void
    {
        $this->postJson('/api/v1/store/orders', [])->assertNotFound();
        $this->getJson('/api/v1/store/orders/VU-123')->assertNotFound();
    }

    public function test_api_preserves_a_valid_correlation_id(): void
    {
        $correlationId = 'store-checkout-12345678';

        $this->withHeader('X-Correlation-ID', $correlationId)
            ->getJson('/api/v1/store/products')
            ->assertOk()
            ->assertHeader('X-Correlation-ID', $correlationId);
    }

    public function test_api_replaces_an_invalid_correlation_id(): void
    {
        $response = $this->withHeader('X-Correlation-ID', 'bad value')
            ->getJson('/api/v1/store/products')
            ->assertOk();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            (string) $response->headers->get('X-Correlation-ID'),
        );
    }
}
