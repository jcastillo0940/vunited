<?php

namespace Database\Factories;

use App\Domain\Memberships\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    protected $model = MembershipPlan::class;

    public function definition(): array
    {
        return [
            'code' => 'tribu',
            'name' => 'Socio Indio',
            'headline' => 'Plan anual oficial',
            'description' => 'Acceso y beneficios exclusivos de la temporada.',
            'price' => '120.00',
            'currency' => 'USD',
            'duration_months' => 12,
            'benefits' => ['Preventa exclusiva', 'Descuentos oficiales'],
            'kit_items' => ['Carnet digital', 'Bufanda oficial'],
            'partner_discounts' => ['Cafe Atalaya 10%'],
            'is_active' => false,
            'sort_order' => 0,
            'metadata' => null,
        ];
    }
}
