<?php

namespace Tests\Feature\Admin\Auth;

use App\Domain\AdminUsers\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered_by_guests(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_admin_users_can_authenticate_using_the_admin_login_screen(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $adminUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($adminUser, 'admin');
        $this->assertGuest('web');
        $response->assertRedirect('/admin');
    }

    public function test_admin_users_can_logout(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->post('/admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect('/admin/login');
    }
}
