<?php

namespace Tests\Feature\Admin\Memberships;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipOrderAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_membership_orders(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_orders.view']);
        $order = MembershipOrder::factory()->create([
            'order_number' => 'TRIBU-2026-0001',
            'full_name' => 'Juan Perez',
            'email' => 'juan@example.com',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/membership-orders')
            ->assertOk()
            ->assertSee('Membership Orders')
            ->assertSee($order->order_number)
            ->assertSee('Juan Perez');
    }

    public function test_admin_without_permission_cannot_view_membership_orders(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/membership-orders')
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_view_membership_order_detail(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_orders.view']);
        $order = MembershipOrder::factory()->paid()->create([
            'order_number' => 'TRIBU-2026-0042',
            'full_name' => 'Maria Castillo',
            'email' => 'maria@example.com',
        ]);
        $payment = Payment::factory()->captured()->create([
            'payable_type' => MembershipOrder::class,
            'payable_id' => $order->id,
            'status' => PaymentStatus::Captured,
            'amount' => '120.00',
            'currency' => 'USD',
            'provider_order_id' => 'PAYID-MEMBERSHIP-42',
            'provider_capture_id' => 'CAP-MEMBERSHIP-42',
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/membership-orders/{$order->id}")
            ->assertOk()
            ->assertSee('TRIBU-2026-0042')
            ->assertSee('Maria Castillo')
            ->assertSee('PAYID-MEMBERSHIP-42')
            ->assertSee('CAP-MEMBERSHIP-42')
            ->assertSee((string) $payment->amount);
    }

    public function test_membership_orders_filter_by_status_and_search(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_orders.view']);

        MembershipOrder::factory()->create([
            'order_number' => 'TRIBU-2026-0001',
            'status' => MembershipOrderStatus::PendingPayment,
            'full_name' => 'Pendiente Uno',
            'email' => 'pendiente@example.com',
        ]);

        MembershipOrder::factory()->paid()->create([
            'order_number' => 'TRIBU-2026-0002',
            'full_name' => 'Pagado Visible',
            'email' => 'pagado@example.com',
        ]);

        MembershipOrder::factory()->failed()->create([
            'order_number' => 'TRIBU-2026-0003',
            'full_name' => 'Fallido Oculto',
            'email' => 'fallido@example.com',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/membership-orders?status=paid&search=pagado@example.com')
            ->assertOk()
            ->assertSee('TRIBU-2026-0002')
            ->assertDontSee('TRIBU-2026-0001')
            ->assertDontSee('TRIBU-2026-0003');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'membership-orders-role-' . fake()->unique()->slug(),
            'label' => 'Membership Orders Role',
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
