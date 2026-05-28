<?php

namespace Tests\Feature\Admin\Sponsors;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Sponsors\Models\Sponsor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SponsorAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/sponsors')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['sponsors.view']);
        Sponsor::factory()->count(3)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/sponsors')
            ->assertOk()
            ->assertSee('Patrocinadores');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/sponsors')
            ->assertForbidden();
    }

    public function test_admin_can_create_sponsor(): void
    {
        $admin = $this->adminWith(['sponsors.view', 'sponsors.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/sponsors', [
                'name'       => 'Banco Regional',
                'tier'       => 'main_partner',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/sponsors');

        $this->assertDatabaseHas('sponsors', [
            'name' => 'Banco Regional',
            'slug' => 'banco-regional',
            'tier' => 'main_partner',
        ]);
    }

    public function test_slug_is_auto_generated(): void
    {
        $admin = $this->adminWith(['sponsors.view', 'sponsors.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/sponsors', [
                'name'       => 'Café del Istmo',
                'tier'       => 'strategic_ally',
                'sort_order' => 0,
            ]);

        $sponsor = Sponsor::query()->where('name', 'Café del Istmo')->first();
        $this->assertNotNull($sponsor);
        $this->assertSame('cafe-del-istmo', $sponsor->slug);
    }

    public function test_admin_can_update_sponsor(): void
    {
        $admin   = $this->adminWith(['sponsors.view', 'sponsors.manage']);
        $sponsor = Sponsor::factory()->create(['tier' => 'official_sponsor']);

        $this->actingAs($admin, 'admin')
            ->put("/admin/sponsors/{$sponsor->id}", [
                'name'        => $sponsor->name,
                'slug'        => $sponsor->slug,
                'tier'        => 'main_partner',
                'sort_order'  => 1,
            ])
            ->assertRedirect('/admin/sponsors');

        $this->assertDatabaseHas('sponsors', [
            'id'   => $sponsor->id,
            'tier' => 'main_partner',
        ]);
    }

    public function test_admin_can_delete_sponsor(): void
    {
        $admin   = $this->adminWith(['sponsors.view', 'sponsors.manage']);
        $sponsor = Sponsor::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/sponsors/{$sponsor->id}")
            ->assertRedirect('/admin/sponsors');

        $this->assertDatabaseMissing('sponsors', ['id' => $sponsor->id]);
    }

    public function test_create_rejects_invalid_tier(): void
    {
        $admin = $this->adminWith(['sponsors.view', 'sponsors.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/sponsors', [
                'name'       => 'Test',
                'tier'       => 'invalid_tier',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('tier');
    }

    public function test_index_filters_by_tier(): void
    {
        $admin = $this->adminWith(['sponsors.view']);
        Sponsor::factory()->mainPartner()->create(['name' => 'Main Corp']);
        Sponsor::factory()->strategicAlly()->create(['name' => 'Ally Corp']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/sponsors?tier=main_partner')
            ->assertOk()
            ->assertSee('Main Corp')
            ->assertDontSee('Ally Corp');
    }

    public function test_manage_permission_required_to_create(): void
    {
        $admin = $this->adminWith(['sponsors.view']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/sponsors/create')
            ->assertForbidden();
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'sponsors-role-' . Str::random(6), 'label' => 'Sponsors Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
