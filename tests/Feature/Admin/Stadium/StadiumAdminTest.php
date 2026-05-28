<?php

namespace Tests\Feature\Admin\Stadium;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Stadium\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StadiumAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_stadium_list(): void
    {
        $admin   = $this->createAdminWithPermissions(['stadium.view']);
        $stadium = Stadium::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/stadium')
            ->assertOk()
            ->assertSee($stadium->name);
    }

    public function test_admin_without_permission_cannot_view_stadium(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/stadium')
            ->assertForbidden();
    }

    public function test_admin_can_create_stadium(): void
    {
        $admin = $this->createAdminWithPermissions(['stadium.view', 'stadium.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/stadium', [
                'name'       => 'Estadio Test',
                'subtitle'   => 'Casa del club',
                'location'   => 'Veraguas',
                'capacity'   => '5,000',
                'venue_type' => 'Principal',
                'is_active'  => '1',
            ])
            ->assertRedirect('/admin/stadium');

        $this->assertDatabaseHas('stadiums', ['name' => 'Estadio Test']);
    }

    public function test_admin_can_update_stadium(): void
    {
        $admin   = $this->createAdminWithPermissions(['stadium.view', 'stadium.manage']);
        $stadium = Stadium::factory()->create();

        $this->actingAs($admin, 'admin')
            ->put("/admin/stadium/{$stadium->id}", [
                'name'       => 'Estadio Atalaya Actualizado',
                'capacity'   => '10,000',
                'is_active'  => '1',
            ])
            ->assertRedirect('/admin/stadium');

        $this->assertDatabaseHas('stadiums', [
            'id'       => $stadium->id,
            'name'     => 'Estadio Atalaya Actualizado',
            'capacity' => '10,000',
        ]);
    }

    public function test_admin_can_delete_stadium(): void
    {
        $admin   = $this->createAdminWithPermissions(['stadium.view', 'stadium.manage']);
        $stadium = Stadium::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/stadium/{$stadium->id}")
            ->assertRedirect('/admin/stadium');

        $this->assertDatabaseMissing('stadiums', ['id' => $stadium->id]);
    }

    public function test_stadium_name_is_required(): void
    {
        $admin = $this->createAdminWithPermissions(['stadium.manage']);

        $this->from('/admin/stadium/create')
            ->actingAs($admin, 'admin')
            ->post('/admin/stadium', ['name' => ''])
            ->assertRedirect('/admin/stadium/create')
            ->assertSessionHasErrors('name');
    }

    private function createAdminWithPermissions(array $names): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'stadium-role-' . fake()->unique()->slug(), 'label' => 'Stadium Role']);

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
