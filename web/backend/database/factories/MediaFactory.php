<?php

namespace Database\Factories;

use App\Domain\Media\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'disk' => 'public',
            'path' => 'media/'.fake()->uuid().'.jpg',
            'original_name' => 'sample.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1200,
            'alt_text' => fake()->sentence(3),
            'mediable_type' => null,
            'mediable_id' => null,
            'collection' => 'default',
            'is_public' => true,
        ];
    }
}
