<?php

namespace Tests\Feature\Admin\Ticketing;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketOrderAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_ticket_orders_list(): void
    {
        $admin = $this->createAdminWithPermissions(['ticket_orders.view']);
        $matchEvent = MatchEvent::factory()->create();
        $order = TicketOrder::factory()->create([
            'match_event_id' => $matchEvent->id,
            'order_number' => 'TICKET-2026-0001',
            'customer_name' => 'Luis Tribu',
            'customer_email' => 'luis@example.com',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/ticket-orders')
            ->assertOk()
            ->assertSee('Ticket Orders')
            ->assertSee('TICKET-2026-0001')
            ->assertSee('Luis Tribu');
    }

    public function test_admin_without_permission_cannot_view_ticket_orders_list(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/ticket-orders')
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_view_ticket_order_details(): void
    {
        $admin = $this->createAdminWithPermissions(['ticket_orders.view']);
        $matchEvent = MatchEvent::factory()->create();
        $order = TicketOrder::factory()->create([
            'match_event_id' => $matchEvent->id,
            'order_number' => 'TICKET-2026-0002',
            'customer_name' => 'Juan Tribu',
            'customer_email' => 'juan@example.com',
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/ticket-orders/{$order->id}")
            ->assertOk()
            ->assertSee('Juan Tribu')
            ->assertSee('TICKET-2026-0002');
    }

    public function test_admin_without_permission_cannot_view_ticket_order_details(): void
    {
        $admin = AdminUser::factory()->create();
        $matchEvent = MatchEvent::factory()->create();
        $order = TicketOrder::factory()->create([
            'match_event_id' => $matchEvent->id,
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/ticket-orders/{$order->id}")
            ->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'ticketing-orders-role-' . fake()->unique()->slug(),
            'label' => 'Ticketing Orders Role',
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
