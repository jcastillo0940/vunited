<?php

namespace Database\Factories;

use App\Domain\FanFest\Models\FanFestEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FanFestEvent>
 */
class FanFestEventFactory extends Factory
{
    protected $model = FanFestEvent::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;
        $title = 'FanFest ' . fake()->year();

        return [
            'title'           => $title,
            'slug'            => Str::slug($title) . '-' . self::$seq,
            'description'     => fake()->paragraph(2),
            'event_date'      => fake()->dateTimeBetween('now', '+6 months'),
            'location'        => 'Estadio Agustín Muquita Sánchez, Santiago de Veraguas',
            'hero_image_path' => null,
            'schedule'        => null,
            'is_active'       => false,
            'metadata'        => null,
        ];
    }

    public function active(): self
    {
        return $this->state(['is_active' => true]);
    }
}
