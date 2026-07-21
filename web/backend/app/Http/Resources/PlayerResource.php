<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', $this->name, 2);

        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'name'          => $this->name,
            'first_name'    => $nameParts[0] ?? $this->name,
            'last_name'     => $nameParts[1] ?? '',
            'number'        => $this->number,
            'position'      => $this->position,
            'position_key'  => $this->position_key,
            'category'      => $this->category,
            'birth_date'    => $this->birth_date?->toDateString(),
            'nationality'   => $this->nationality,
            'height'        => $this->height,
            'weight'        => $this->weight,
            'dominant_foot' => $this->dominant_foot,
            'photo_path'    => $this->photo_path,
            'gallery'       => $this->gallery ?? [],
            'stats'         => $this->stats ?? [],
            'attributes'    => $this->attributes ?? [],
            'biography'          => $this->biography,
            'is_active'          => $this->is_active,
            'is_exported'        => $this->is_exported,
            'foreign_club'       => $this->foreign_club,
            'foreign_league'     => $this->foreign_league,
            'foreign_country'    => $this->foreign_country,
            'foreign_club_logo'  => $this->foreign_club_logo,
            'achievements'       => $this->achievements ?? [],
            'sort_order'         => $this->sort_order,
        ];
    }
}
