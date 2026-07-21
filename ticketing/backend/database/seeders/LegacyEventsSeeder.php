<?php

namespace Database\Seeders;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * Migra los eventos y zonas semilla de `weveraguas.match_events` /
 * `weveraguas.ticket_zones` (monolito legado). No hay ordenes ni tickets
 * emitidos que migrar - ver docs/architecture/ticketing-legacy-findings.md.
 * Mapeo legado -> nuevo:
 *   match_events.code        -> events.code
 *   match_events.home_team   -> events.home_team
 *   ticket_zones.capacity    -> zones.capacity_total (= capacity_available inicial)
 *   ticket_zones.available_quantity se descarta: no era confiable (ver
 *   hallazgo de sobreventa), se recalcula capacity_available = capacity_total.
 */
class LegacyEventsSeeder extends Seeder
{
    public function run(): void
    {
        $event1 = Event::updateOrCreate(
            ['code' => 'vua-vs-cai-2026-10-24'],
            [
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'CAI PANAMA',
                'competition' => 'JORNADA 12 - LPF',
                'starts_at' => '2026-10-24 20:00:00',
                'venue_name' => 'Estadio Atalaya',
                'venue_location' => 'Veraguas',
                'status' => 'on_sale',
                'sales_open_at' => now()->subDay(),
                'sales_close_at' => '2026-10-24 18:00:00',
                'is_visible' => true,
                'purchase_limit_per_buyer' => 6,
                'metadata' => ['legacy_match_event_id' => 1],
            ],
        );

        $event2 = Event::updateOrCreate(
            ['code' => 'vua-vs-tauro-2026-10-17'],
            [
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'TAURO FC',
                'competition' => 'JORNADA 11 - LPF',
                'starts_at' => '2026-10-17 18:00:00',
                'venue_name' => 'Estadio Atalaya',
                'venue_location' => 'Veraguas',
                'status' => 'finished',
                'is_visible' => false,
                'purchase_limit_per_buyer' => 6,
                'metadata' => ['legacy_match_event_id' => 2],
            ],
        );

        $zones = [
            ['event' => $event1, 'name' => 'General', 'slug' => 'general', 'price' => 5.00, 'capacity' => 1200],
            ['event' => $event1, 'name' => 'Preferencial', 'slug' => 'preferencial', 'price' => 12.00, 'capacity' => 450],
            ['event' => $event1, 'name' => 'VIP Indio', 'slug' => 'vip-indio', 'price' => 25.00, 'capacity' => 120],
        ];

        foreach ($zones as $z) {
            Zone::updateOrCreate(
                ['event_id' => $z['event']->id, 'slug' => $z['slug']],
                [
                    'name' => $z['name'],
                    'kind' => 'general',
                    'price' => $z['price'],
                    'currency' => 'USD',
                    'capacity_total' => $z['capacity'],
                    'capacity_available' => $z['capacity'],
                    'capacity_held' => 0,
                    'is_active' => true,
                ],
            );
        }
    }
}
