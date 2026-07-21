<?php

namespace Database\Factories;

use App\Domain\Pages\Models\Page;
use App\Domain\Pages\Models\PageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageSection>
 */
class PageSectionFactory extends Factory
{
    protected $model = PageSection::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'section_key' => fake()->slug(),
            'type' => 'rich_text',
            'title' => fake()->sentence(2),
            'body' => fake()->paragraph(),
            'payload' => ['key' => 'value'],
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
            'image_path' => null,
        ];
    }
}
