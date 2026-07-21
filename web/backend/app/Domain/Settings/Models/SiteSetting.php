<?php

namespace App\Domain\Settings\Models;

use App\Domain\Media\Models\Media;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'site_name',
    'site_tagline',
    'primary_logo_path',
    'secondary_logo_path',
    'primary_color',
    'accent_color',
    'contact_email',
    'contact_phone',
    'social_links',
    'global_seo_title',
    'global_seo_description',
    'hero_video_url',
    'fanfest_hero_video_url',
    'expedition_hero_video_url',
    'sponsors_hero_video_url',
    'stadium_hero_video_url',
    'academy_hero_video_url',
    'squad_hero_video_url',
    'news_hero_video_url',
    'maintenance_mode',
])]
class SiteSetting extends Model
{
    use HasFactory;

    protected static function newFactory(): SiteSettingFactory
    {
        return SiteSettingFactory::new();
    }

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'maintenance_mode' => 'boolean',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
