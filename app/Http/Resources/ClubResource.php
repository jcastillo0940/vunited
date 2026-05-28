<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Sports\Models\Club */
class ClubResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'short_name'      => $this->short_name,
            'slug'            => $this->slug,
            'logo_path'       => $this->logo_path,
            'city'            => $this->city,
            'primary_color'   => $this->primary_color,
            'secondary_color' => $this->secondary_color,
        ];
    }
}
