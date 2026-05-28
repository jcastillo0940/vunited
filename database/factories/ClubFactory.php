<?php

namespace Database\Factories;

use App\Domain\Sports\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Club> */
class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Tauro FC', 'Plaza Amador', 'CAI Panama', 'Alianza FC',
            'UMECIT FC', 'Herrera FC', 'Chepo FC', 'Atletico Chiriqui',
            'Sporting SM', 'Universitario FC', 'Chiriqui United', 'Bocas FC',
        ]);

        return [
            'name'            => $name,
            'short_name'      => strtoupper(Str::limit(Str::slug($name, ''), 3, '')),
            'slug'            => Str::slug($name),
            'logo_path'       => null,
            'city'            => fake()->city(),
            'primary_color'   => fake()->hexColor(),
            'secondary_color' => fake()->hexColor(),
            'is_active'       => true,
            'sort_order'      => fake()->numberBetween(1, 20),
            'metadata'        => null,
        ];
    }
}
