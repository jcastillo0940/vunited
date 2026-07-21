<?php

namespace Database\Factories;

use App\Domain\Board\Models\BoardMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BoardMember>
 */
class BoardMemberFactory extends Factory
{
    protected $model = BoardMember::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;
        $name = fake()->name();

        return [
            'name'       => $name,
            'slug'       => Str::slug($name) . '-bm-' . self::$seq,
            'role'       => fake()->jobTitle(),
            'group'      => fake()->randomElement(['presidency', 'executive_staff', 'board', 'transparency']),
            'photo_path' => null,
            'biography'  => fake()->paragraph(2),
            'email'      => null,
            'sort_order' => self::$seq,
            'is_active'  => true,
            'metadata'   => null,
        ];
    }

    public function presidency(): self
    {
        return $this->state(['group' => 'presidency']);
    }

    public function executiveStaff(): self
    {
        return $this->state(['group' => 'executive_staff']);
    }

    public function board(): self
    {
        return $this->state(['group' => 'board']);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
