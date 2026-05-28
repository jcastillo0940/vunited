<?php

namespace Database\Factories;

use App\Domain\Squad\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;
        $name = fake()->name('male');

        return [
            'name'          => $name,
            'slug'          => Str::slug($name) . '-' . self::$seq,
            'number'        => str_pad((string) fake()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT),
            'position'      => fake()->randomElement(['Portero', 'Defensa', 'Volante', 'Delantero']),
            'position_key'  => fake()->randomElement(['goalkeeper', 'defender', 'midfielder', 'forward']),
            'category'      => fake()->randomElement(['first-team', 'women-team', 'academy']),
            'birth_date'    => fake()->dateTimeBetween('-35 years', '-18 years')->format('Y-m-d'),
            'nationality'   => 'Panameño',
            'height'        => fake()->randomElement(['1.70 m', '1.75 m', '1.80 m', '1.85 m', '1.90 m']),
            'weight'        => fake()->randomElement(['68 kg', '72 kg', '75 kg', '80 kg', '84 kg']),
            'dominant_foot' => fake()->randomElement(['Derecho', 'Zurdo']),
            'photo_path'    => null,
            'gallery'       => null,
            'stats'         => null,
            'attributes'    => null,
            'biography'     => fake()->paragraph(3),
            'is_active'     => true,
            'sort_order'    => self::$seq,
        ];
    }

    public function firstTeam(): self
    {
        return $this->state(['category' => 'first-team']);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
