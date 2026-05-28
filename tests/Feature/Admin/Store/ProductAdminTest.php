<?php

namespace Tests\Feature\Admin\Store;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_products(): void
    {
        $admin = $this->createAdminWithPermissions(['products.view']);
        $product = Product::factory()->create([
            'name' => 'Camiseta Local 2024',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Products')
            ->assertSee($product->name);
    }

    public function test_admin_without_permission_cannot_view_products(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_admin_can_create_and_edit_product(): void
    {
        $admin = $this->createAdminWithPermissions(['products.view', 'products.manage', 'product_categories.view']);
        $category = ProductCategory::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/products', [
                'product_category_id' => $category->id,
                'sku' => 'VU-LOCAL-2024',
                'name' => 'Camiseta Local 2024',
                'slug' => 'camiseta-local-2024',
                'description' => 'Version oficial de temporada.',
                'short_description' => 'Blanco de la provincia',
                'price' => '65.00',
                'compare_at_price' => '75.00',
                'currency' => 'USD',
                'stock_quantity' => 12,
                'track_stock' => '1',
                'is_featured' => '0',
                'is_active' => '1',
                'badge' => 'LOCAL',
                'image_path' => 'products/local-2024.jpg',
                'gallery' => json_encode(['products/local-2024.jpg']),
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/products');

        $product = Product::query()->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->put("/admin/products/{$product->id}", [
                'product_category_id' => $category->id,
                'sku' => 'VU-LOCAL-2024',
                'name' => 'Camiseta Local Oficial 2024',
                'slug' => 'camiseta-local-2024',
                'description' => 'Version oficial actualizada.',
                'short_description' => 'Orgullo blanco',
                'price' => '69.00',
                'compare_at_price' => '79.00',
                'currency' => 'USD',
                'stock_quantity' => 10,
                'track_stock' => '1',
                'is_featured' => '1',
                'is_active' => '1',
                'badge' => 'NUEVO',
                'image_path' => 'products/local-2024-new.jpg',
                'gallery' => json_encode(['products/local-2024-new.jpg']),
                'sort_order' => 2,
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Camiseta Local Oficial 2024',
            'price' => '69.00',
            'is_featured' => 1,
            'badge' => 'NUEVO',
        ]);
    }

    public function test_admin_can_mark_product_as_featured(): void
    {
        $admin = $this->createAdminWithPermissions(['products.view', 'products.manage']);
        $product = Product::factory()->create([
            'is_featured' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/products/{$product->id}", [
                'product_category_id' => $product->product_category_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'currency' => $product->currency,
                'stock_quantity' => $product->stock_quantity,
                'track_stock' => $product->track_stock ? '1' : '0',
                'is_featured' => '1',
                'is_active' => $product->is_active ? '1' : '0',
                'badge' => $product->badge,
                'image_path' => $product->image_path,
                'gallery' => json_encode($product->gallery),
                'sort_order' => $product->sort_order,
            ])
            ->assertRedirect('/admin/products');

        $this->assertTrue($product->fresh()->is_featured);
    }

    public function test_invalid_price_is_rejected(): void
    {
        $admin = $this->createAdminWithPermissions(['products.view', 'products.manage']);

        $this->from('/admin/products/create')
            ->actingAs($admin, 'admin')
            ->post('/admin/products', [
                'name' => 'Producto invalido',
                'slug' => 'producto-invalido',
                'price' => '0',
                'currency' => 'USD',
                'sort_order' => 0,
            ])
            ->assertRedirect('/admin/products/create')
            ->assertSessionHasErrors('price');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'product-role-' . fake()->unique()->slug(),
            'label' => 'Product Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
