<?php

namespace Tests\Feature\Admin\Settings;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_settings_view_permission_can_view_settings_page(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view']);

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/settings');

        $response
            ->assertOk()
            ->assertSee('Site Settings');
    }

    public function test_admin_without_settings_view_permission_cannot_view_settings_page(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get('/admin/settings');

        $response->assertForbidden();
    }

    public function test_admin_with_settings_update_permission_can_update_settings(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);

        $response = $this->actingAs($adminUser, 'admin')->put('/admin/settings', [
            'site_name' => 'Veraguas United FC',
            'site_tagline' => 'Orgullo de Veraguas',
            'primary_logo_path' => 'logos/primary.png',
            'secondary_logo_path' => 'logos/secondary.png',
            'primary_color' => '#0F172A',
            'accent_color' => '#10B981',
            'contact_email' => 'hola@veraguasunited.test',
            'contact_phone' => '+507 6000-0000',
            'social_links' => [
                'facebook' => 'https://facebook.com/veraguasunited',
                'instagram' => 'https://instagram.com/veraguasunited',
            ],
            'global_seo_title' => 'Veraguas United FC',
            'global_seo_description' => 'Sitio oficial del club.',
            'maintenance_mode' => true,
        ]);

        $response->assertRedirect('/admin/settings');

        $setting = SiteSetting::query()->first();

        $this->assertNotNull($setting);
        $this->assertSame('Veraguas United FC', $setting->site_name);
        $this->assertSame('#0F172A', $setting->primary_color);
        $this->assertTrue($setting->maintenance_mode);
        $this->assertSame([
            'facebook' => 'https://facebook.com/veraguasunited',
            'instagram' => 'https://instagram.com/veraguasunited',
        ], $setting->social_links);
    }

    public function test_settings_record_is_created_automatically_if_it_does_not_exist(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view']);

        $this->assertDatabaseCount('site_settings', 0);

        $this->actingAs($adminUser, 'admin')
            ->get('/admin/settings')
            ->assertOk();

        $this->assertDatabaseCount('site_settings', 1);
    }

    public function test_settings_update_rejects_invalid_email(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);

        $response = $this->from('/admin/settings')
            ->actingAs($adminUser, 'admin')
            ->put('/admin/settings', [
                'site_name' => 'Veraguas United FC',
                'contact_email' => 'correo-invalido',
            ]);

        $response
            ->assertRedirect('/admin/settings')
            ->assertSessionHasErrors('contact_email');
    }

    public function test_settings_update_accepts_valid_hex_colors(): void
    {
        $adminUser = $this->createAdminWithPermissions(['settings.view', 'settings.update']);

        $response = $this->actingAs($adminUser, 'admin')->put('/admin/settings', [
            'site_name' => 'Veraguas United FC',
            'primary_color' => '#123ABC',
            'accent_color' => '#abcdef',
        ]);

        $response->assertRedirect('/admin/settings');

        $setting = SiteSetting::query()->first();

        $this->assertSame('#123ABC', $setting->primary_color);
        $this->assertSame('#abcdef', $setting->accent_color);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $adminUser = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'settings-role-'.count($permissionNames).'-'.fake()->unique()->slug(),
            'label' => 'Settings Role',
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
