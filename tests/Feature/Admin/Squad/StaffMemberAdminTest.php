<?php

namespace Tests\Feature\Admin\Squad;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Squad\Models\StaffMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StaffMemberAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/staff-members')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['staff.view']);
        StaffMember::factory()->count(2)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/staff-members')
            ->assertOk()
            ->assertSee('Cuerpo Técnico');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/staff-members')
            ->assertForbidden();
    }

    public function test_admin_can_create_staff_member(): void
    {
        $admin = $this->adminWith(['staff.view', 'staff.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/staff-members', [
                'name'       => 'Diego Herrera',
                'role'       => 'Preparador Físico',
                'category'   => 'first-team',
                'sort_order' => 3,
            ])
            ->assertRedirect('/admin/staff-members');

        $this->assertDatabaseHas('staff_members', [
            'name' => 'Diego Herrera',
            'slug' => 'diego-herrera',
            'role' => 'Preparador Físico',
        ]);
    }

    public function test_admin_can_update_staff_member(): void
    {
        $admin  = $this->adminWith(['staff.view', 'staff.manage']);
        $member = StaffMember::factory()->create(['role' => 'Asistente']);

        $this->actingAs($admin, 'admin')
            ->put("/admin/staff-members/{$member->id}", [
                'name'       => $member->name,
                'slug'       => $member->slug,
                'role'       => 'Director Técnico',
                'category'   => 'first-team',
                'sort_order' => 0,
            ])
            ->assertRedirect('/admin/staff-members');

        $this->assertDatabaseHas('staff_members', [
            'id'   => $member->id,
            'role' => 'Director Técnico',
        ]);
    }

    public function test_admin_can_delete_staff_member(): void
    {
        $admin  = $this->adminWith(['staff.view', 'staff.manage']);
        $member = StaffMember::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/staff-members/{$member->id}")
            ->assertRedirect('/admin/staff-members');

        $this->assertDatabaseMissing('staff_members', ['id' => $member->id]);
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'staff-role-' . Str::random(6), 'label' => 'Staff Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
