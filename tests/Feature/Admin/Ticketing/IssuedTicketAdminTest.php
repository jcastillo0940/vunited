<?php

namespace Tests\Feature\Admin\Ticketing;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssuedTicketAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/issued-tickets')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->createAdminWithPermissions(['issued_tickets.view']);
        IssuedTicket::factory()->count(2)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/issued-tickets')
            ->assertOk()
            ->assertSee('Issued Tickets');
    }

    public function test_admin_without_permission_is_forbidden_on_index(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/issued-tickets')
            ->assertForbidden();
    }

    public function test_index_filters_by_status(): void
    {
        $admin  = $this->createAdminWithPermissions(['issued_tickets.view']);
        $issued = IssuedTicket::factory()->create(['status' => IssuedTicketStatus::Issued, 'seat_label' => 'VIP #1', 'zone_name' => 'VIP']);
        $used   = IssuedTicket::factory()->used()->create(['seat_label' => 'General #1', 'zone_name' => 'General']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/issued-tickets?status=used')
            ->assertOk()
            ->assertSee('General #1')
            ->assertDontSee('VIP #1');
    }

    public function test_admin_with_permission_can_view_show(): void
    {
        $admin  = $this->createAdminWithPermissions(['issued_tickets.view']);
        $ticket = IssuedTicket::factory()->create(['zone_name' => 'Preferencia Sur']);

        $this->actingAs($admin, 'admin')
            ->get("/admin/issued-tickets/{$ticket->id}")
            ->assertOk()
            ->assertSee('Preferencia Sur')
            ->assertSee(substr($ticket->token, 0, 10));
    }

    public function test_admin_without_permission_is_forbidden_on_show(): void
    {
        $admin  = AdminUser::factory()->create();
        $ticket = IssuedTicket::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get("/admin/issued-tickets/{$ticket->id}")
            ->assertForbidden();
    }

    public function test_ticket_order_show_displays_issued_tickets_section(): void
    {
        $admin  = $this->createAdminWithPermissions(['ticket_orders.view']);
        $order  = TicketOrder::factory()->paid()->create();
        IssuedTicket::factory()->count(2)->create(['ticket_order_id' => $order->id]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/ticket-orders/{$order->id}")
            ->assertOk()
            ->assertSee('Issued Tickets');
    }

    public function test_validate_endpoint_requires_admin_auth(): void
    {
        $this->postJson('/admin/tickets/validate', ['token' => str_repeat('a', 40)])
            ->assertUnauthorized();
    }

    public function test_admin_with_permission_can_validate_ticket(): void
    {
        $admin  = $this->createAdminWithPermissions(['issued_tickets.validate']);
        $ticket = IssuedTicket::factory()->create();

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/tickets/validate', ['token' => $ticket->token])
            ->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('ticket.status', 'used');
    }

    public function test_admin_without_permission_is_forbidden_on_validate(): void
    {
        $admin  = AdminUser::factory()->create();
        $ticket = IssuedTicket::factory()->create();

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/tickets/validate', ['token' => $ticket->token])
            ->assertForbidden();
    }

    public function test_validate_returns_error_for_unknown_token(): void
    {
        $admin = $this->createAdminWithPermissions(['issued_tickets.validate']);

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/tickets/validate', ['token' => str_repeat('0', 40)])
            ->assertStatus(422)
            ->assertJsonPath('valid', false)
            ->assertJsonPath('reason', 'not_found');
    }

    public function test_validate_rejects_already_used_ticket(): void
    {
        $admin  = $this->createAdminWithPermissions(['issued_tickets.validate']);
        $ticket = IssuedTicket::factory()->used()->create();

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/tickets/validate', ['token' => $ticket->token])
            ->assertStatus(422)
            ->assertJsonPath('valid', false)
            ->assertJsonPath('reason', 'already_used');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create([
            'name'  => 'issued-tickets-role-' . fake()->unique()->slug(),
            'label' => 'Issued Tickets Role',
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
