<?php

namespace Database\Seeders;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Database\Seeder;

class TicketZoneSeeder extends Seeder
{
    public function run(): void
    {
        $match = MatchEvent::query()->where('code', 'LPF-J12-VU-CAI-20261024')->first();

        if ($match === null) {
            return;
        }

        $zones = [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Acceso a las graderias generales laterales.',
                'price' => '5.00',
                'currency' => 'USD',
                'capacity' => 1200,
                'available_quantity' => 850,
                'sort_order' => 1,
                'is_active' => true,
                'metadata' => [
                    'display_name' => 'GENERAL',
                    'area' => 'SUR / NORTE',
                    'tone' => 'neutral',
                ],
            ],
            [
                'name' => 'Preferencial',
                'slug' => 'preferencial',
                'description' => 'Asientos numerados con mejor visibilidad.',
                'price' => '12.00',
                'currency' => 'USD',
                'capacity' => 450,
                'available_quantity' => 180,
                'sort_order' => 2,
                'is_active' => true,
                'metadata' => [
                    'display_name' => 'PREFERENCIAL',
                    'area' => 'ESTE / OESTE',
                    'tone' => 'neutral',
                ],
            ],
            [
                'name' => 'VIP Indio',
                'slug' => 'vip-indio',
                'description' => 'Zona exclusiva con servicio de catering.',
                'price' => '25.00',
                'currency' => 'USD',
                'capacity' => 120,
                'available_quantity' => 42,
                'sort_order' => 3,
                'is_active' => true,
                'metadata' => [
                    'display_name' => 'VIP INDIO',
                    'area' => 'PREMIUM',
                    'tone' => 'featured',
                ],
            ],
        ];

        foreach ($zones as $zone) {
            TicketZone::query()->updateOrCreate(
                [
                    'match_event_id' => $match->id,
                    'slug' => $zone['slug'],
                ],
                $zone,
            );
        }
    }
}
