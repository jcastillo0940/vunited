<?php

namespace Tests\Feature\Admin\Sports;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Sports\Models\Club;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClubAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_clubs(): void
    {
        $admin = $this->createAdminWithPermissions(['clubs.view']);
        $club  = Club::factory()->create(['name' => 'Tauro FC', 'slug' => 'tauro-fc']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/clubs')
            ->assertOk()
            ->assertSee('Tauro FC');
    }

    public function test_admin_without_permission_cannot_view_clubs(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/clubs')
            ->assertForbidden();
    }

    public function test_admin_can_create_club(): void
    {
        $admin = $this->createAdminWithPermissions(['clubs.view', 'clubs.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/clubs', [
                'name'          => 'Alianza FC',
                'short_name'    => 'ALI',
                'city'          => 'Panama',
                'primary_color' => '#7C3AED',
                'is_active'     => '1',
                'sort_order'    => '5',
            ])
            ->assertRedirect('/admin/clubs');

        $this->assertDatabaseHas('clubs', ['name' => 'Alianza FC', 'slug' => 'alianza-fc']);
    }

    public function test_admin_can_update_club(): void
    {
        $admin = $this->createAdminWithPermissions(['clubs.view', 'clubs.manage']);
        $club  = Club::factory()->create();

        $this->actingAs($admin, 'admin')
            ->put("/admin/clubs/{$club->id}", [
                'name'       => 'Updated Club',
                'is_active'  => '1',
                'sort_order' => '0',
            ])
            ->assertRedirect('/admin/clubs');

        $this->assertDatabaseHas('clubs', ['id' => $club->id, 'name' => 'Updated Club']);
    }

    public function test_admin_can_delete_club(): void
    {
        $admin = $this->createAdminWithPermissions(['clubs.view', 'clubs.manage']);
        $club  = Club::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/clubs/{$club->id}")
            ->assertRedirect('/admin/clubs');

        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);
    }

    public function test_slug_is_auto_generated_from_name(): void
    {
        $admin = $this->createAdminWithPermissions(['clubs.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/clubs', [
                'name'       => 'Plaza Amador',
                'is_active'  => '1',
                'sort_order' => '0',
            ]);

        $this->assertDatabaseHas('clubs', ['slug' => 'plaza-amador']);
    }

    private function createAdminWithPermissions(array $names): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'club-role-' . fake()->unique()->slug(), 'label' => 'Club Role']);

        foreach ($names as $name) {
            $perm = Permission::firstOrCreate(
                ['name' => $name],
                ['label' => str($name)->replace('.', ' ')->title()->toString()],
            );
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
