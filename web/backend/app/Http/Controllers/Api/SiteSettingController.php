<?php

namespace App\Http\Controllers\Api;

use App\Domain\Settings\Models\SiteSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;

class SiteSettingController extends Controller
{
    public function __invoke(): SiteSettingResource
    {
        $settings = SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'Veraguas United FC',
            'site_tagline' => null,
            'primary_logo_path' => null,
            'secondary_logo_path' => null,
            'primary_color' => null,
            'accent_color' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'social_links' => [],
            'global_seo_title' => null,
            'global_seo_description' => null,
            'maintenance_mode' => false,
        ]);

        return new SiteSettingResource($settings);
    }
}
