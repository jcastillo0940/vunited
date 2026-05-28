<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'Veraguas United FC',
            'site_tagline' => 'Orgullo de Veraguas',
            'primary_logo_path' => null,
            'secondary_logo_path' => null,
            'primary_color' => '#0F172A',
            'accent_color' => '#10B981',
            'contact_email' => 'hola@veraguasunited.test',
            'contact_phone' => '+507 6000-0000',
            'social_links' => [],
            'global_seo_title' => 'Veraguas United FC',
            'global_seo_description' => 'Sitio oficial del club.',
            'maintenance_mode' => false,
        ]);
    }
}
