<?php

namespace Tests\Feature\Admin\Store;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_product_categories(): void
    {
        $admin = $this->createAdminWithPermissions(['product_categories.view']);
        $category = ProductCategory::factory()->create([
            'name' => 'Camisetas',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/product-categories')
            ->assertOk()
            ->assertSee('Product Categories')
            ->assertSee($category->name);
    }

    public function test_admin_without_permission_cannot_view_product_categories(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/product-categories')
            ->assertForbidden();
    }

    public function test_admin_can_create_and_edit_product_category(): void
    {
        $admin = $this->createAdminWithPermissions(['product_categories.view', 'product_categories.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/product-categories', [
                'name' => 'Camisetas',
                'slug' => 'camisetas',
                'description' => 'Uniformes oficiales.',
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/product-categories');

        $category = ProductCategory::query()->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->put("/admin/product-categories/{$category->id}", [
                'name' => 'Camisetas Oficiales',
                'slug' => 'camisetas',
                'description' => 'Uniformes y jerseys.',
                'sort_order' => 2,
                'is_active' => '0',
            ])
            ->assertRedirect('/admin/product-categories');

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Camisetas Oficiales',
            'sort_order' => 2,
            'is_active' => 0,
        ]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $admin = $this->createAdminWithPermissions(['product_categories.view', 'product_categories.manage']);
        $category = ProductCategory::factory()->create();
        Product::factory()->create([
            'product_category_id' => $category->id,
        ]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/product-categories/{$category->id}")
            ->assertRedirect('/admin/product-categories');

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
        ]);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'product-category-role-' . fake()->unique()->slug(),
            'label' => 'Product Category Role',
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
