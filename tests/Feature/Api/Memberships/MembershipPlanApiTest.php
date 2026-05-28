<?php

namespace Tests\Feature\Api\Memberships;

use App\Domain\Memberships\Models\MembershipPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPlanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_active_membership_plan(): void
    {
        MembershipPlan::factory()->create([
            'code' => 'tribu',
            'name' => 'Socio Indio',
            'headline' => 'Plan oficial',
            'description' => 'Beneficios del club.',
            'price' => '120.00',
            'currency' => 'USD',
            'duration_months' => 12,
            'benefits' => ['Preventa', 'Descuento tienda'],
            'kit_items' => ['Carnet digital'],
            'partner_discounts' => ['Cafe Atalaya'],
            'is_active' => true,
        ]);

        $this->getJson('/api/membership-plans/active')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'code' => 'tribu',
                    'name' => 'Socio Indio',
                    'price' => '120.00',
                    'currency' => 'USD',
                    'duration_months' => 12,
                    'benefits' => ['Preventa', 'Descuento tienda'],
                    'kit_items' => ['Carnet digital'],
                    'partner_discounts' => ['Cafe Atalaya'],
                ],
            ]);
    }

    public function test_api_returns_controlled_error_when_no_active_plan_exists(): void
    {
        MembershipPlan::factory()->create([
            'code' => 'tribu',
            'is_active' => false,
        ]);

        $this->getJson('/api/membership-plans/active')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'No hay un plan de membresia activo disponible.',
            ]);
    }
}
