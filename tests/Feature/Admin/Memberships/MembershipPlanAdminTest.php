<?php

namespace Tests\Feature\Admin\Memberships;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Memberships\Models\MembershipPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPlanAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_membership_plans(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_plans.view']);
        $plan = MembershipPlan::factory()->create([
            'code' => 'tribu',
            'name' => 'Socio Indio',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/membership-plans')
            ->assertOk()
            ->assertSee('Membership Plans')
            ->assertSee($plan->name)
            ->assertSee($plan->code);
    }

    public function test_admin_without_permission_cannot_view_membership_plans(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/membership-plans')
            ->assertForbidden();
    }

    public function test_admin_with_manage_permission_can_create_membership_plan(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_plans.manage', 'membership_plans.view']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/membership-plans', [
                'code' => 'tribu',
                'name' => 'Socio Indio',
                'headline' => 'Plan anual oficial',
                'description' => 'Acceso y beneficios de temporada.',
                'price' => '120.00',
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['Preventa exclusiva', 'Descuento en tienda'],
                'kit_items' => ['Carnet digital', 'Bufanda'],
                'partner_discounts' => ['Cafe Atalaya 10%'],
                'is_active' => '1',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/membership-plans');

        $this->assertDatabaseHas('membership_plans', [
            'code' => 'tribu',
            'name' => 'Socio Indio',
            'price' => '120.00',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_membership_plan_price(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_plans.manage', 'membership_plans.view']);
        $plan = MembershipPlan::factory()->create([
            'code' => 'tribu',
            'price' => '120.00',
        ]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/membership-plans/{$plan->id}", [
                'code' => $plan->code,
                'name' => $plan->name,
                'headline' => $plan->headline,
                'description' => $plan->description,
                'price' => '135.00',
                'currency' => $plan->currency,
                'duration_months' => $plan->duration_months,
                'benefits' => $plan->benefits,
                'kit_items' => $plan->kit_items,
                'partner_discounts' => $plan->partner_discounts,
                'is_active' => $plan->is_active ? '1' : '0',
                'sort_order' => $plan->sort_order,
            ])
            ->assertRedirect('/admin/membership-plans');

        $this->assertDatabaseHas('membership_plans', [
            'id' => $plan->id,
            'price' => '135.00',
        ]);
    }

    public function test_admin_can_activate_plan(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_plans.manage', 'membership_plans.view']);
        $plan = MembershipPlan::factory()->create([
            'code' => 'tribu',
            'is_active' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/membership-plans/{$plan->id}", [
                'code' => $plan->code,
                'name' => $plan->name,
                'headline' => $plan->headline,
                'description' => $plan->description,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'duration_months' => $plan->duration_months,
                'benefits' => $plan->benefits,
                'kit_items' => $plan->kit_items,
                'partner_discounts' => $plan->partner_discounts,
                'is_active' => '1',
                'sort_order' => $plan->sort_order,
            ])
            ->assertRedirect('/admin/membership-plans');

        $this->assertTrue($plan->fresh()->is_active);
    }

    public function test_plan_with_orders_cannot_be_deleted(): void
    {
        $admin = $this->createAdminWithPermissions(['membership_plans.manage', 'membership_plans.view']);
        $plan = MembershipPlan::factory()->create(['code' => 'tribu']);
        MembershipOrder::factory()->create([
            'membership_plan' => 'tribu',
        ]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/membership-plans/{$plan->id}")
            ->assertRedirect('/admin/membership-plans');

        $this->assertDatabaseHas('membership_plans', [
            'id' => $plan->id,
        ]);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'membership-plans-role-' . fake()->unique()->slug(),
            'label' => 'Membership Plans Role',
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
