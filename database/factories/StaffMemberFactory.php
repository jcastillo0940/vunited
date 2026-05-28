<?php

namespace Database\Factories;

use App\Domain\Squad\Models\StaffMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StaffMember>
 */
class StaffMemberFactory extends Factory
{
    protected $model = StaffMember::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;
        $name = fake()->name('male');

        return [
            'name'       => $name,
            'slug'       => Str::slug($name) . '-staff-' . self::$seq,
            'role'       => fake()->randomElement(['Director Técnico', 'Asistente Técnico', 'Preparador Físico', 'Analista de Rendimiento', 'Médico del Club']),
            'category'   => 'first-team',
            'photo_path' => null,
            'biography'  => fake()->paragraph(2),
            'is_active'  => true,
            'sort_order' => self::$seq,
        ];
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
