<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Ticketing\Models\TicketZone */
class TicketZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'capacity' => $this->capacity,
            'available_quantity' => $this->available_quantity,
            'out_of_stock' => $this->isOutOfStock(),
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata,
        ];
    }
}
