<?php

namespace Database\Seeders;

use App\Domain\Sports\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = [
            ['name' => 'Veraguas United FC', 'short_name' => 'VUA', 'city' => 'Santiago', 'primary_color' => '#1D428A', 'secondary_color' => '#5BC2E7', 'sort_order' => 1],
            ['name' => 'Tauro FC',            'short_name' => 'TAU', 'city' => 'Panama',   'primary_color' => '#EF4444', 'secondary_color' => '#FFFFFF', 'sort_order' => 2],
            ['name' => 'CAI Panama',          'short_name' => 'CAI', 'city' => 'Panama',   'primary_color' => '#1C1C1C', 'secondary_color' => '#F59E0B', 'sort_order' => 3],
            ['name' => 'Plaza Amador',        'short_name' => 'PLA', 'city' => 'Panama',   'primary_color' => '#16A34A', 'secondary_color' => '#FFFFFF', 'sort_order' => 4],
            ['name' => 'Alianza FC',          'short_name' => 'ALI', 'city' => 'Panama',   'primary_color' => '#7C3AED', 'secondary_color' => '#FFFFFF', 'sort_order' => 5],
            ['name' => 'UMECIT FC',           'short_name' => 'UME', 'city' => 'Panama',   'primary_color' => '#0369A1', 'secondary_color' => '#FFFFFF', 'sort_order' => 6],
            ['name' => 'Herrera FC',          'short_name' => 'HFC', 'city' => 'Chitre',   'primary_color' => '#B45309', 'secondary_color' => '#FFFFFF', 'sort_order' => 7],
            ['name' => 'Atletico Chiriqui',   'short_name' => 'CHI', 'city' => 'David',    'primary_color' => '#166534', 'secondary_color' => '#FFFFFF', 'sort_order' => 8],
        ];

        foreach ($clubs as $data) {
            Club::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, ['slug' => Str::slug($data['name']), 'is_active' => true]),
            );
        }
    }
}
