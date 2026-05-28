<?php

namespace Database\Factories;

use App\Domain\News\Models\NewsArticle;
use App\Domain\News\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsArticle>
 */
class NewsArticleFactory extends Factory
{
    protected $model = NewsArticle::class;

    public function definition(): array
    {
        return [
            'news_category_id' => null,
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'summary' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
            'featured_image_path' => null,
            'status' => 'draft',
            'published_at' => null,
            'is_featured' => false,
            'seo_title' => fake()->sentence(3),
            'seo_description' => fake()->sentence(),
        ];
    }

    public function withCategory(): self
    {
        return $this->state(fn () => [
            'news_category_id' => NewsCategory::factory(),
        ]);
    }
}
