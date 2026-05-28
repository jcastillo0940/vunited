<?php

namespace Database\Factories;

use App\Domain\Pages\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->sentence(),
            'status' => 'draft',
            'published_at' => null,
            'seo_title' => fake()->sentence(3),
            'seo_description' => fake()->sentence(),
            'is_home' => false,
        ];
    }
}
