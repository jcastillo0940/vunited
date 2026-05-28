<?php

namespace Tests\Feature\Admin\Ticketing;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketZoneAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_ticket_zones(): void
    {
        $admin = $this->createAdminWithPermissions(['ticket_zones.view']);
        $matchEvent = MatchEvent::factory()->create();
        $zone = TicketZone::factory()->create([
            'match_event_id' => $matchEvent->id,
            'name' => 'VIP Indio',
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/match-events/{$matchEvent->id}/ticket-zones")
            ->assertOk()
            ->assertSee('Ticket Zones')
            ->assertSee($zone->name);
    }

    public function test_admin_without_permission_cannot_view_ticket_zones(): void
    {
        $admin = AdminUser::factory()->create();
        $matchEvent = MatchEvent::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get("/admin/match-events/{$matchEvent->id}/ticket-zones")
            ->assertForbidden();
    }

    public function test_admin_can_create_and_edit_ticket_zone(): void
    {
        $admin = $this->createAdminWithPermissions(['ticket_zones.view', 'ticket_zones.manage']);
        $matchEvent = MatchEvent::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post("/admin/match-events/{$matchEvent->id}/ticket-zones", [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Acceso general',
                'price' => '5.00',
                'currency' => 'USD',
                'capacity' => 1000,
                'available_quantity' => 750,
                'sort_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect("/admin/match-events/{$matchEvent->id}/ticket-zones");

        $zone = TicketZone::query()->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->put("/admin/match-events/{$matchEvent->id}/ticket-zones/{$zone->id}", [
                'name' => 'General Norte',
                'slug' => 'general-norte',
                'description' => 'Acceso general norte',
                'price' => '6.00',
                'currency' => 'USD',
                'capacity' => 1000,
                'available_quantity' => 700,
                'sort_order' => 2,
                'is_active' => '1',
            ])
            ->assertRedirect("/admin/match-events/{$matchEvent->id}/ticket-zones");

        $this->assertDatabaseHas('ticket_zones', [
            'id' => $zone->id,
            'name' => 'General Norte',
            'price' => '6.00',
        ]);
    }

    public function test_invalid_price_is_rejected(): void
    {
        $admin = $this->createAdminWithPermissions(['ticket_zones.view', 'ticket_zones.manage']);
        $matchEvent = MatchEvent::factory()->create();

        $this->from("/admin/match-events/{$matchEvent->id}/ticket-zones/create")
            ->actingAs($admin, 'admin')
            ->post("/admin/match-events/{$matchEvent->id}/ticket-zones", [
                'name' => 'Zona invalida',
                'price' => '0',
                'currency' => 'USD',
                'sort_order' => 0,
            ])
            ->assertRedirect("/admin/match-events/{$matchEvent->id}/ticket-zones/create")
            ->assertSessionHasErrors('price');
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'ticketing-zone-role-' . fake()->unique()->slug(),
            'label' => 'Ticketing Zone Role',
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
