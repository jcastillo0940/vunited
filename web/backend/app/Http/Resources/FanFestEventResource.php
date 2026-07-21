<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FanFestEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'slug'            => $this->slug,
            'title'           => $this->title,
            'description'     => $this->description,
            'event_date'      => $this->event_date?->toISOString(),
            'location'        => $this->location,
            'hero_image_path' => $this->hero_image_path,
            'schedule'        => $this->schedule ?? [],
            'is_active'       => $this->is_active,
            'metadata'        => $this->metadata ?? [],
            'zones'           => $this->whenLoaded('zones', fn () =>
                $this->zones->map(fn ($z) => [
                    'id'          => $z->id,
                    'name'        => $z->name,
                    'description' => $z->description,
                    'icon'        => $z->icon,
                    'sort_order'  => $z->sort_order,
                ])
            ),
        ];
    }
}
