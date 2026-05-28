<?php

namespace Database\Seeders;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Domain\FanFest\Models\FanFestZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FanFestSeeder extends Seeder
{
    public function run(): void
    {
        $event = FanFestEvent::query()->updateOrCreate(
            ['slug' => 'fanfest-veraguas-2026'],
            [
                'title'           => 'FanFest Veraguas United 2026',
                'slug'            => 'fanfest-veraguas-2026',
                'description'     => 'La gran fiesta del fútbol veragüense. Una jornada llena de música, gastronomía, deporte y la identidad de los Indios en su máxima expresión. Únete a la tribu y vive el FanFest.',
                'event_date'      => now()->addMonths(2)->setTime(16, 0),
                'location'        => 'Estadio Agustín Muquita Sánchez, Santiago de Veraguas',
                'hero_image_path' => null,
                'schedule'        => [
                    ['time' => '15:00', 'activity' => 'Apertura de puertas'],
                    ['time' => '16:00', 'activity' => 'Shows artísticos y activaciones de marca'],
                    ['time' => '17:30', 'activity' => 'Competencias y torneos relámpago'],
                    ['time' => '18:30', 'activity' => 'Presentación del plantel'],
                    ['time' => '19:00', 'activity' => 'Partido oficial — Veraguas United'],
                    ['time' => '21:00', 'activity' => 'Cierre FanFest y fuegos artificiales'],
                ],
                'is_active' => true,
                'metadata'  => ['edition' => '2026', 'expected_attendance' => '5000'],
            ],
        );

        $zones = [
            ['name' => 'Zona Familiar',      'description' => 'Área recreativa con juegos y actividades para toda la familia.',        'icon' => 'family_restroom', 'sort_order' => 1],
            ['name' => 'Zona Gastronómica',  'description' => 'Lo mejor de la gastronomía veragüense. Comida típica y bebidas.',       'icon' => 'restaurant',      'sort_order' => 2],
            ['name' => 'Zona Deportiva',     'description' => 'Minifútbol, torneos de habilidad y retos deportivos en vivo.',          'icon' => 'sports_soccer',   'sort_order' => 3],
            ['name' => 'Zona Cultural',      'description' => 'Expresiones artísticas, música en vivo y folclore de la región.',       'icon' => 'music_note',      'sort_order' => 4],
            ['name' => 'Zona Patrocinadores','description' => 'Activaciones de nuestros aliados y patrocinadores del club.',           'icon' => 'handshake',       'sort_order' => 5],
            ['name' => 'Zona VIP',           'description' => 'Área exclusiva para socios La Tribu y patrocinadores principales.',     'icon' => 'star',            'sort_order' => 6],
        ];

        foreach ($zones as $zone) {
            FanFestZone::query()->updateOrCreate(
                ['fan_fest_event_id' => $event->id, 'name' => $zone['name']],
                array_merge($zone, ['fan_fest_event_id' => $event->id, 'is_active' => true]),
            );
        }
    }
}
