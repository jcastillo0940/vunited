<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'primary_logo_url' => $this->publicPath($this->primary_logo_path),
            'secondary_logo_url' => $this->publicPath($this->secondary_logo_path),
            'primary_color' => $this->primary_color,
            'accent_color' => $this->accent_color,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'social_links' => $this->social_links ?? [],
            'global_seo_title' => $this->global_seo_title,
            'global_seo_description' => $this->global_seo_description,
            'hero_video_url' => $this->hero_video_url,
            'fanfest_hero_video_url' => $this->fanfest_hero_video_url,
            'expedition_hero_video_url' => $this->expedition_hero_video_url,
            'sponsors_hero_video_url' => $this->sponsors_hero_video_url,
            'stadium_hero_video_url' => $this->stadium_hero_video_url,
            'academy_hero_video_url' => $this->academy_hero_video_url,
            'squad_hero_video_url' => $this->squad_hero_video_url,
            'news_hero_video_url' => $this->news_hero_video_url,
            'maintenance_mode' => $this->maintenance_mode,
        ];
    }

    private function publicPath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        return parse_url(Storage::disk('public')->url($path), PHP_URL_PATH) ?: null;
    }
}
