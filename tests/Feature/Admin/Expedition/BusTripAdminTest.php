<?php

namespace Tests\Feature\Admin\Expedition;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Expedition\Models\BusTrip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BusTripAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/bus-trips')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['expeditions.view']);
        BusTrip::factory()->count(2)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/bus-trips')
            ->assertOk()
            ->assertSee('Expedición');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/bus-trips')
            ->assertForbidden();
    }

    public function test_admin_can_create_trip(): void
    {
        $admin = $this->adminWith(['expeditions.view', 'expeditions.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/bus-trips', [
                'title'              => 'Expedición Prueba',
                'departure_location' => 'Santiago, Terminal',
                'departure_time'     => '2026-08-10 14:00:00',
                'price'              => '12.00',
                'currency'           => 'USD',
                'capacity'           => 40,
                'available_seats'    => 40,
                'is_active'          => '1',
            ])
            ->assertRedirect('/admin/bus-trips');

        $this->assertDatabaseHas('bus_trips', [
            'title'              => 'Expedición Prueba',
            'departure_location' => 'Santiago, Terminal',
        ]);
    }

    public function test_admin_can_update_trip(): void
    {
        $admin = $this->adminWith(['expeditions.view', 'expeditions.manage']);
        $trip  = BusTrip::factory()->create(['available_seats' => 40]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/bus-trips/{$trip->id}", [
                'title'              => $trip->title,
                'departure_location' => $trip->departure_location,
                'departure_time'     => $trip->departure_time->format('Y-m-d H:i:s'),
                'price'              => $trip->price,
                'currency'           => 'USD',
                'capacity'           => $trip->capacity,
                'available_seats'    => 15,
            ])
            ->assertRedirect('/admin/bus-trips');

        $this->assertDatabaseHas('bus_trips', ['id' => $trip->id, 'available_seats' => 15]);
    }

    public function test_admin_can_delete_trip(): void
    {
        $admin = $this->adminWith(['expeditions.view', 'expeditions.manage']);
        $trip  = BusTrip::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/bus-trips/{$trip->id}")
            ->assertRedirect('/admin/bus-trips');

        $this->assertDatabaseMissing('bus_trips', ['id' => $trip->id]);
    }

    public function test_manage_permission_required_to_create(): void
    {
        $admin = $this->adminWith(['expeditions.view']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/bus-trips/create')
            ->assertForbidden();
    }

    public function test_create_validates_required_fields(): void
    {
        $admin = $this->adminWith(['expeditions.view', 'expeditions.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/bus-trips', [])
            ->assertSessionHasErrors(['title', 'departure_location', 'departure_time', 'price', 'capacity', 'available_seats']);
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'expedition-role-' . Str::random(6), 'label' => 'Expedition Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
