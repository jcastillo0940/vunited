<?php

namespace Database\Seeders;

use App\Domain\Sponsors\Models\Sponsor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            // Main partners
            ['name' => 'Banco Provincial',   'tier' => 'main_partner',    'description' => 'Aliado principal de la temporada 2024.',                           'sort_order' => 1],
            ['name' => 'Canal 7 Deportes',   'tier' => 'main_partner',    'description' => 'Transmisión oficial y experiencias en estadio.',                    'sort_order' => 2],
            // Official sponsors
            ['name' => 'Cemento Veraguas',   'tier' => 'official_sponsor','description' => null,                                                               'sort_order' => 1],
            ['name' => 'Rapi Envíos',        'tier' => 'official_sponsor','description' => null,                                                               'sort_order' => 2],
            ['name' => 'Panamá Segura',      'tier' => 'official_sponsor','description' => null,                                                               'sort_order' => 3],
            ['name' => 'Hotel Atalaya',      'tier' => 'official_sponsor','description' => null,                                                               'sort_order' => 4],
            // Strategic allies
            ['name' => 'Agro Centro',        'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 1],
            ['name' => 'Digital 360',        'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 2],
            ['name' => 'Fundación Veraguas', 'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 3],
            ['name' => 'Café del Istmo',     'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 4],
            ['name' => 'Rutas del Indio',    'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 5],
            ['name' => 'Energía Central',    'tier' => 'strategic_ally',  'description' => null,                                                               'sort_order' => 6],
        ];

        foreach ($sponsors as $data) {
            Sponsor::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'      => Str::slug($data['name']),
                    'is_active' => true,
                ]),
            );
        }
    }
}
