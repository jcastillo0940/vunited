<?php

namespace Tests\Feature\Admin\Dashboard;

use App\Domain\AdminUsers\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_can_access_the_admin_dashboard(): void
    {
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }
}
