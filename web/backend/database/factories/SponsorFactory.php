<?php

namespace Database\Factories;

use App\Domain\Sponsors\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    protected $model = Sponsor::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;
        $name = fake()->company();

        return [
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . self::$seq,
            'tier'        => fake()->randomElement(['main_partner', 'official_sponsor', 'strategic_ally']),
            'logo_path'   => null,
            'website_url' => fake()->url(),
            'description' => fake()->sentence(),
            'sort_order'  => self::$seq,
            'is_active'   => true,
            'metadata'    => null,
        ];
    }

    public function mainPartner(): self
    {
        return $this->state(['tier' => 'main_partner']);
    }

    public function officialSponsor(): self
    {
        return $this->state(['tier' => 'official_sponsor']);
    }

    public function strategicAlly(): self
    {
        return $this->state(['tier' => 'strategic_ally']);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
