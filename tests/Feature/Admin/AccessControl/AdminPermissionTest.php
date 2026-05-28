<?php

namespace Tests\Feature\Admin\AccessControl;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_permission_cannot_access_admin_users_index(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')
            ->get('/admin/admin-users');

        $response->assertForbidden();
    }

    public function test_admin_with_admin_users_view_permission_can_access_admin_users_index(): void
    {
        $adminUser = AdminUser::factory()->create();
        $permission = Permission::create([
            'name' => 'admin_users.view',
            'label' => 'View admin users',
        ]);
        $role = Role::create([
            'name' => 'admin-operator',
            'label' => 'Admin Operator',
        ]);

        $role->permissions()->attach($permission);
        $adminUser->roles()->attach($role);

        $response = $this->actingAs($adminUser, 'admin')
            ->get('/admin/admin-users');

        $response
            ->assertOk()
            ->assertSee('Admin Users');
    }

    public function test_admin_with_roles_view_permission_can_access_roles_index(): void
    {
        $adminUser = AdminUser::factory()->create();
        $permission = Permission::create([
            'name' => 'roles.view',
            'label' => 'View roles',
        ]);
        $role = Role::create([
            'name' => 'rbac-manager',
            'label' => 'RBAC Manager',
        ]);

        $role->permissions()->attach($permission);
        $adminUser->roles()->attach($role);

        $response = $this->actingAs($adminUser, 'admin')
            ->get('/admin/roles');

        $response
            ->assertOk()
            ->assertSee('Roles');
    }

    public function test_admin_user_has_permission_resolves_through_roles(): void
    {
        $adminUser = AdminUser::factory()->create();
        $permission = Permission::create([
            'name' => 'settings.view',
            'label' => 'View settings',
        ]);
        $role = Role::create([
            'name' => 'content-admin',
            'label' => 'Content Admin',
        ]);

        $role->permissions()->attach($permission);
        $adminUser->roles()->attach($role);

        $this->assertTrue($adminUser->hasPermission('settings.view'));
        $this->assertFalse($adminUser->hasPermission('news.manage'));
    }
}
