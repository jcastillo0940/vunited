<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_dashboard_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_public_web_users_cannot_access_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/admin');

        $response->assertForbidden();
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertGuest('admin');
    }
}
