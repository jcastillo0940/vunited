<?php

namespace Tests\Feature\Admin\Menus;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Menus\Models\Menu;
use App\Domain\Menus\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_menus_view_permission_can_view_menus_index(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view']);

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/menus');

        $response
            ->assertOk()
            ->assertSee('Menus');
    }

    public function test_admin_without_menus_view_permission_cannot_view_menus_index(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/menus');

        $response->assertForbidden();
    }

    public function test_admin_with_menus_manage_permission_can_create_a_menu(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view', 'menus.manage']);

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/menus', [
            'name' => 'Main Header',
            'location' => 'header',
            'is_active' => true,
        ]);

        $response->assertRedirect('/admin/menus');

        $this->assertDatabaseHas('menus', [
            'name' => 'Main Header',
            'location' => 'header',
            'is_active' => 1,
        ]);
    }

    public function test_admin_with_menus_manage_permission_can_update_a_menu(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view', 'menus.manage']);
        $menu = Menu::factory()->create([
            'name' => 'Footer Legal',
            'location' => 'footer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($adminUser, 'admin')->put("/admin/menus/{$menu->id}", [
            'name' => 'Footer Legal Links',
            'location' => 'footer',
            'is_active' => false,
        ]);

        $response->assertRedirect('/admin/menus');

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Footer Legal Links',
            'is_active' => 0,
        ]);
    }

    public function test_admin_with_menus_manage_permission_can_create_a_menu_item(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view', 'menus.manage']);
        $menu = Menu::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->post("/admin/menus/{$menu->id}/items", [
            'parent_id' => null,
            'label' => 'Noticias',
            'url' => '/noticias',
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $response->assertRedirect("/admin/menus/{$menu->id}/edit");

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'label' => 'Noticias',
            'sort_order' => 3,
            'parent_id' => null,
        ]);
    }

    public function test_menu_items_support_nullable_parent_id_and_persist_sort_order(): void
    {
        $menu = Menu::factory()->create();
        $parentItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sort_order' => 1,
        ]);

        $childItem = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parentItem->id,
            'sort_order' => 2,
        ]);

        $this->assertNull(MenuItem::query()->find($parentItem->id)?->parent_id);
        $this->assertSame(2, MenuItem::query()->find($childItem->id)?->sort_order);
        $this->assertSame($parentItem->id, MenuItem::query()->find($childItem->id)?->parent_id);
    }

    public function test_admin_without_menus_manage_permission_cannot_create_or_update_menus(): void
    {
        $adminUser = $this->createAdminWithPermissions(['menus.view']);
        $menu = Menu::factory()->create();

        $createResponse = $this->actingAs($adminUser, 'admin')->post('/admin/menus', [
            'name' => 'Blocked Header',
            'location' => 'header',
            'is_active' => true,
        ]);

        $createResponse->assertForbidden();

        $updateResponse = $this->actingAs($adminUser, 'admin')->put("/admin/menus/{$menu->id}", [
            'name' => 'Still Blocked',
            'location' => 'header',
            'is_active' => false,
        ]);

        $updateResponse->assertForbidden();
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'menu-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'Menu Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $adminUser->roles()->syncWithoutDetaching([$role->id]);

        return $adminUser;
    }
}
