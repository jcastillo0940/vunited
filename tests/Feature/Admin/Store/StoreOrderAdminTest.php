<?php

namespace Tests\Feature\Admin\Store;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\StoreOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreOrderAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_store_orders(): void
    {
        $admin = $this->createAdminWithPermissions(['store_orders.view']);
        $order = StoreOrder::factory()->create([
            'order_number' => 'STORE-2026-0001',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/store-orders')
            ->assertOk()
            ->assertSee('Store Orders')
            ->assertSee($order->order_number);
    }

    public function test_admin_without_permission_cannot_view_store_orders(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/store-orders')
            ->assertForbidden();
    }

    public function test_admin_can_view_store_order_detail_with_items_and_payment(): void
    {
        $admin = $this->createAdminWithPermissions(['store_orders.view']);
        $product = Product::factory()->create([
            'name' => 'Balon Oficial',
        ]);
        $order = StoreOrder::factory()->create([
            'customer_name' => 'Diego Veraguas',
            'metadata' => [
                'card_number' => '4111111111111111',
                'client_secret' => 'secret-should-not-show',
            ],
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => '35.00',
            'quantity' => 2,
            'line_total' => '70.00',
            'metadata' => [
                'product_snapshot' => ['slug' => $product->slug],
            ],
        ]);

        Payment::factory()->create([
            'payable_type' => StoreOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-STORE-ADMIN',
            'status' => PaymentStatus::ProviderCreated,
            'amount' => 70.00,
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/store-orders/{$order->id}")
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Balon Oficial')
            ->assertSee('PAYID-STORE-ADMIN')
            ->assertDontSee('4111111111111111')
            ->assertDontSee('secret-should-not-show');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'store-order-role-' . fake()->unique()->slug(),
            'label' => 'Store Order Role',
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
