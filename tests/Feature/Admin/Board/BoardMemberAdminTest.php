<?php

namespace Tests\Feature\Admin\Board;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Board\Models\BoardMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BoardMemberAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/board-members')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['board_members.view']);
        BoardMember::factory()->count(3)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/board-members')
            ->assertOk()
            ->assertSee('Directiva');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/board-members')
            ->assertForbidden();
    }

    public function test_admin_can_create_board_member(): void
    {
        $admin = $this->adminWith(['board_members.view', 'board_members.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/board-members', [
                'name'       => 'Ana González',
                'role'       => 'Secretaria General',
                'group'      => 'board',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/board-members');

        $this->assertDatabaseHas('board_members', [
            'name'  => 'Ana González',
            'slug'  => 'ana-gonzalez',
            'group' => 'board',
        ]);
    }

    public function test_slug_is_auto_generated(): void
    {
        $admin = $this->adminWith(['board_members.view', 'board_members.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/board-members', [
                'name'       => 'José Martínez López',
                'role'       => 'Tesorero',
                'group'      => 'board',
                'sort_order' => 0,
            ]);

        $member = BoardMember::query()->where('name', 'José Martínez López')->first();
        $this->assertNotNull($member);
        $this->assertSame('jose-martinez-lopez', $member->slug);
    }

    public function test_admin_can_update_board_member(): void
    {
        $admin  = $this->adminWith(['board_members.view', 'board_members.manage']);
        $member = BoardMember::factory()->board()->create(['role' => 'Vocal']);

        $this->actingAs($admin, 'admin')
            ->put("/admin/board-members/{$member->id}", [
                'name'       => $member->name,
                'slug'       => $member->slug,
                'role'       => 'Vocal Principal',
                'group'      => 'board',
                'sort_order' => $member->sort_order,
            ])
            ->assertRedirect('/admin/board-members');

        $this->assertDatabaseHas('board_members', [
            'id'   => $member->id,
            'role' => 'Vocal Principal',
        ]);
    }

    public function test_admin_can_delete_board_member(): void
    {
        $admin  = $this->adminWith(['board_members.view', 'board_members.manage']);
        $member = BoardMember::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/board-members/{$member->id}")
            ->assertRedirect('/admin/board-members');

        $this->assertDatabaseMissing('board_members', ['id' => $member->id]);
    }

    public function test_create_rejects_invalid_group(): void
    {
        $admin = $this->adminWith(['board_members.view', 'board_members.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/board-members', [
                'name'       => 'Test',
                'role'       => 'Test Role',
                'group'      => 'invalid_group',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('group');
    }

    public function test_index_filters_by_group(): void
    {
        $admin = $this->adminWith(['board_members.view']);
        BoardMember::factory()->presidency()->create(['name' => 'El Presidente']);
        BoardMember::factory()->board()->create(['name' => 'Vocal Uno']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/board-members?group=presidency')
            ->assertOk()
            ->assertSee('El Presidente')
            ->assertDontSee('Vocal Uno');
    }

    public function test_manage_permission_required_to_create(): void
    {
        $admin = $this->adminWith(['board_members.view']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/board-members/create')
            ->assertForbidden();
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'board-role-' . Str::random(6), 'label' => 'Board Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
