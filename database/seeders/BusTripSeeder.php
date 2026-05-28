<?php

namespace Database\Seeders;

use App\Domain\Expedition\Models\BusTrip;
use App\Domain\Ticketing\Models\MatchEvent;
use Illuminate\Database\Seeder;

class BusTripSeeder extends Seeder
{
    public function run(): void
    {
        $firstMatch = MatchEvent::query()->orderBy('match_date')->first();

        $trips = [
            [
                'title'              => 'Expedición India — Copa LPF Jornada 8',
                'match_event_id'     => $firstMatch?->id,
                'departure_location' => 'Santiago de Veraguas, Terminal de Buses David',
                'departure_time'     => now()->addDays(14)->setTime(14, 0),
                'return_time'        => now()->addDays(14)->setTime(23, 30),
                'price'              => '12.00',
                'currency'           => 'USD',
                'capacity'           => 45,
                'available_seats'    => 28,
                'is_active'          => true,
                'metadata'           => [
                    'includes'    => 'Transporte ida y vuelta en bus con A/C',
                    'contact'     => 'hola@veraguasunited.test',
                    'notes'       => 'Punto de encuentro: Terminal de Buses David, 30 min antes.',
                ],
            ],
            [
                'title'              => 'Expedición India — Partido de Copa',
                'match_event_id'     => null,
                'departure_location' => 'Santiago de Veraguas, Plaza 5 de Noviembre',
                'departure_time'     => now()->addDays(28)->setTime(12, 0),
                'return_time'        => now()->addDays(28)->setTime(22, 0),
                'price'              => '15.00',
                'currency'           => 'USD',
                'capacity'           => 40,
                'available_seats'    => 40,
                'is_active'          => true,
                'metadata'           => [
                    'includes' => 'Transporte ida y vuelta, hidratación incluida',
                    'contact'  => 'hola@veraguasunited.test',
                ],
            ],
        ];

        foreach ($trips as $data) {
            BusTrip::create($data);
        }
    }
}
