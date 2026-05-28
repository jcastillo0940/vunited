<?php

namespace Database\Seeders;

use App\Domain\Memberships\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        MembershipPlan::query()->updateOrCreate(
            ['code' => 'tribu'],
            [
                'name' => 'Socio Indio',
                'headline' => 'El orgullo de Veraguas se vive desde adentro.',
                'description' => 'Hazte miembro y disfruta de beneficios exclusivos durante toda la temporada.',
                'price' => '120.00',
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => [
                    'Tienda oficial (20% off)',
                    'Comercios aliados',
                    'Entrenamientos exclusivos',
                    'Preventa exclusiva',
                ],
                'kit_items' => [
                    'Camiseta oficial',
                    'Termo de acero',
                    'Bandana edicion especial',
                ],
                'partner_discounts' => [
                    'Cafe Atalaya',
                    'Rapi Envios',
                    'Hotel Santiago',
                    'Gimnasio Titan',
                    'Clinica SportsMed',
                    'Mercado Veraguas',
                ],
                'is_active' => true,
                'sort_order' => 1,
                'metadata' => [
                    'badge' => '2 JUEGOS GRATIS',
                    'access' => 'Preferencial',
                    'billing_note' => 'PAGO UNICO ANUAL',
                    'season_label' => 'PASE ANUAL',
                    'sales_title' => 'MAS QUE UN FAN,',
                    'sales_highlight' => 'ERES PARTE DEL CLUB.',
                ],
            ],
        );
    }
}
