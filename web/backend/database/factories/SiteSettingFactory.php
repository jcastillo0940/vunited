<?php

namespace Database\Factories;

use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    protected $model = SiteSetting::class;

    public function definition(): array
    {
        return [
            'site_name' => 'Veraguas United FC',
            'site_tagline' => fake()->sentence(3),
            'primary_logo_path' => null,
            'secondary_logo_path' => null,
            'primary_color' => '#0F172A',
            'accent_color' => '#10B981',
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => '+507 6000-0000',
            'social_links' => [
                'facebook' => 'https://facebook.com/veraguasunited',
            ],
            'global_seo_title' => 'Veraguas United FC',
            'global_seo_description' => fake()->sentence(),
            'maintenance_mode' => false,
        ];
    }
}
