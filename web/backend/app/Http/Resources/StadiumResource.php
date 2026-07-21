<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Stadium\Models\Stadium */
class StadiumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $meta = $this->metadata ?? [];

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'subtitle'        => $this->subtitle,
            'location'        => $this->location,
            'address'         => $this->address,
            'capacity'        => $this->capacity,
            'venue_type'      => $this->venue_type,
            'hero_image_path' => $this->hero_image_path,
            'map_embed_url'   => $this->map_embed_url,
            'zones'           => $this->zones ?? [],
            'matchday'        => $this->matchday ?? [],
            'rules'           => $this->rules ?? [],
            'metadata'        => $meta,
        ];
    }
}
