<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'kind' => $this->kind,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            // Nunca exponemos capacity_held (detalle interno de reservas en
            // curso); solo si hay cupo disponible y cuanto.
            'available' => (int) $this->capacity_available,
            'sold_out' => $this->capacity_available <= 0,
            'purchase_limit_per_buyer' => $this->purchase_limit_per_buyer,
        ];
    }
}
