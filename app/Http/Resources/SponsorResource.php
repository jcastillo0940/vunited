<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'name'        => $this->name,
            'tier'        => $this->tier,
            'tier_label'  => self::tierLabel($this->tier),
            'logo_path'   => $this->logo_path,
            'website_url' => $this->website_url,
            'description' => $this->description,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'metadata'    => $this->metadata ?? [],
        ];
    }

    public static function tierLabel(string $tier): string
    {
        return match ($tier) {
            'main_partner'    => 'Main Partner',
            'official_sponsor' => 'Official Sponsor',
            'strategic_ally'  => 'Alianza Estratégica',
            default           => $tier,
        };
    }
}
