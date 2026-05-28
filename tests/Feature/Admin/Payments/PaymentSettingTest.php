<?php

namespace Tests\Feature\Admin\Payments;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Audit\Models\AuditLog;
use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_payment_settings_view_can_view_page(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-settings')
            ->assertOk()
            ->assertSee('Payment Settings');
    }

    public function test_admin_without_payment_settings_view_cannot_view_page(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-settings')
            ->assertForbidden();
    }

    public function test_admin_with_payment_settings_update_can_update(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view', 'payment_settings.update']);

        $response = $this->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'       => 'sandbox',
                'client_id'  => 'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwznZyAshAmCpCnOTD_KUUkgwHb6wRLwzMoXR',
                'webhook_id' => 'WH-123456',
                'currency'   => 'USD',
                'is_enabled' => false,
            ]);

        $response->assertRedirect('/admin/payment-settings');

        $setting = PaymentSetting::query()->where('provider', 'paypal')->first();
        $this->assertNotNull($setting);
        $this->assertSame('sandbox', $setting->mode);
        $this->assertSame('AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwznZyAshAmCpCnOTD_KUUkgwHb6wRLwzMoXR', $setting->client_id);
        $this->assertSame('WH-123456', $setting->webhook_id);
        $this->assertSame('USD', $setting->currency);
        $this->assertFalse($setting->is_enabled);
    }

    public function test_admin_without_payment_settings_update_cannot_update(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'     => 'sandbox',
                'currency' => 'USD',
            ])
            ->assertForbidden();
    }

    public function test_client_secret_is_not_shown_in_view(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view']);

        PaymentSetting::query()->create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => null,
            'client_secret' => 'super-secret-value-do-not-expose',
            'webhook_id'    => null,
            'currency'      => 'USD',
            'is_enabled'    => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-settings')
            ->assertOk()
            ->assertDontSee('super-secret-value-do-not-expose');
    }

    public function test_client_secret_is_not_recorded_in_audit_log(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view', 'payment_settings.update']);

        PaymentSetting::query()->create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_secret' => 'old-secret-plain-text',
            'currency'      => 'USD',
            'is_enabled'    => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'          => 'sandbox',
                'client_secret' => 'new-secret-plain-text',
                'currency'      => 'USD',
                'is_enabled'    => false,
            ]);

        $audit = AuditLog::query()
            ->where('module', 'payment_settings')
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($audit);

        $oldJson = json_encode($audit->old_values);
        $newJson = json_encode($audit->new_values);

        $this->assertStringNotContainsString('old-secret-plain-text', $oldJson);
        $this->assertStringNotContainsString('new-secret-plain-text', $newJson);
        $this->assertSame('***', $audit->old_values['client_secret']);
        $this->assertSame('***', $audit->new_values['client_secret']);
    }

    public function test_setting_is_created_automatically_if_not_exists(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view']);

        $this->assertDatabaseCount('payment_settings', 0);

        $this->actingAs($admin, 'admin')
            ->get('/admin/payment-settings')
            ->assertOk();

        $this->assertDatabaseCount('payment_settings', 1);
    }

    public function test_invalid_mode_is_rejected(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view', 'payment_settings.update']);

        $this->from('/admin/payment-settings')
            ->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'     => 'invalid-mode',
                'currency' => 'USD',
            ])
            ->assertRedirect('/admin/payment-settings')
            ->assertSessionHasErrors('mode');
    }

    public function test_invalid_currency_is_rejected(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view', 'payment_settings.update']);

        $this->from('/admin/payment-settings')
            ->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'     => 'sandbox',
                'currency' => 'INVALID',
            ])
            ->assertRedirect('/admin/payment-settings')
            ->assertSessionHasErrors('currency');
    }

    public function test_client_secret_is_preserved_when_empty_string_submitted(): void
    {
        $admin = $this->createAdminWithPermissions(['payment_settings.view', 'payment_settings.update']);

        PaymentSetting::query()->create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_secret' => 'existing-secret',
            'currency'      => 'USD',
            'is_enabled'    => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->put('/admin/payment-settings', [
                'mode'          => 'live',
                'client_secret' => '',
                'currency'      => 'USD',
                'is_enabled'    => false,
            ]);

        $setting = PaymentSetting::query()->where('provider', 'paypal')->first();

        $this->assertSame('existing-secret', $setting->client_secret);
        $this->assertSame('live', $setting->mode);
    }

    public function test_no_public_payment_endpoints_exist(): void
    {
        $this->get('/payment/create-order')->assertStatus(404);
        $this->post('/payment/create-order')->assertStatus(404);
        $this->post('/payment/capture-order')->assertStatus(404);
        $this->post('/webhooks/paypal')->assertStatus(404);
        $this->get('/api/payment')->assertStatus(404);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create([
            'name'  => 'payments-role-' . fake()->unique()->slug(),
            'label' => 'Payments Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
