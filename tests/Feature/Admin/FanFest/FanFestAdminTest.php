<?php

namespace Tests\Feature\Admin\FanFest;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\FanFest\Models\FanFestEvent;
use App\Domain\FanFest\Models\FanFestZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FanFestAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/fanfest-events')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['fanfest.view']);
        FanFestEvent::factory()->count(2)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/fanfest-events')
            ->assertOk()
            ->assertSee('FanFest');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/fanfest-events')
            ->assertForbidden();
    }

    public function test_admin_can_create_event(): void
    {
        $admin = $this->adminWith(['fanfest.view', 'fanfest.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/fanfest-events', [
                'title'      => 'FanFest Test 2026',
                'event_date' => '2026-07-15 18:00:00',
                'location'   => 'Estadio Prueba',
            ])
            ->assertRedirect('/admin/fanfest-events');

        $this->assertDatabaseHas('fan_fest_events', [
            'title' => 'FanFest Test 2026',
            'slug'  => 'fanfest-test-2026',
        ]);
    }

    public function test_admin_can_update_event(): void
    {
        $admin = $this->adminWith(['fanfest.view', 'fanfest.manage']);
        $event = FanFestEvent::factory()->create(['title' => 'Viejo Título']);

        $this->actingAs($admin, 'admin')
            ->put("/admin/fanfest-events/{$event->id}", [
                'title'    => 'Nuevo Título',
                'slug'     => $event->slug,
                'is_active'=> '1',
            ])
            ->assertRedirect('/admin/fanfest-events');

        $this->assertDatabaseHas('fan_fest_events', ['id' => $event->id, 'title' => 'Nuevo Título', 'is_active' => true]);
    }

    public function test_admin_can_delete_event(): void
    {
        $admin = $this->adminWith(['fanfest.view', 'fanfest.manage']);
        $event = FanFestEvent::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/fanfest-events/{$event->id}")
            ->assertRedirect('/admin/fanfest-events');

        $this->assertDatabaseMissing('fan_fest_events', ['id' => $event->id]);
    }

    public function test_admin_can_view_zones(): void
    {
        $admin = $this->adminWith(['fanfest.view']);
        $event = FanFestEvent::factory()->active()->create();
        FanFestZone::factory()->count(3)->create(['fan_fest_event_id' => $event->id]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/fanfest-events/{$event->id}/zones")
            ->assertOk()
            ->assertSee('Zonas');
    }

    public function test_admin_can_create_zone(): void
    {
        $admin = $this->adminWith(['fanfest.view', 'fanfest.manage']);
        $event = FanFestEvent::factory()->active()->create();

        $this->actingAs($admin, 'admin')
            ->post("/admin/fanfest-events/{$event->id}/zones", [
                'name'        => 'Zona Prueba',
                'description' => 'Descripción de prueba.',
                'icon'        => 'sports_soccer',
                'sort_order'  => 1,
                'is_active'   => '1',
            ])
            ->assertRedirect("/admin/fanfest-events/{$event->id}/zones");

        $this->assertDatabaseHas('fan_fest_zones', [
            'fan_fest_event_id' => $event->id,
            'name'              => 'Zona Prueba',
        ]);
    }

    public function test_deleting_event_cascades_to_zones(): void
    {
        $admin = $this->adminWith(['fanfest.view', 'fanfest.manage']);
        $event = FanFestEvent::factory()->active()->create();
        FanFestZone::factory()->count(3)->create(['fan_fest_event_id' => $event->id]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/fanfest-events/{$event->id}")
            ->assertRedirect('/admin/fanfest-events');

        $this->assertDatabaseMissing('fan_fest_events', ['id' => $event->id]);
        $this->assertDatabaseCount('fan_fest_zones', 0);
    }

    public function test_manage_permission_required_to_create(): void
    {
        $admin = $this->adminWith(['fanfest.view']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/fanfest-events/create')
            ->assertForbidden();
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'fanfest-role-' . Str::random(6), 'label' => 'FanFest Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
