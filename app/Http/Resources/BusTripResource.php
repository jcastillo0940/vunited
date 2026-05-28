<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusTripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'departure_location' => $this->departure_location,
            'departure_time'     => $this->departure_time?->toISOString(),
            'return_time'        => $this->return_time?->toISOString(),
            'price'              => $this->price,
            'currency'           => $this->currency,
            'capacity'           => $this->capacity,
            'available_seats'    => $this->available_seats,
            'is_available'       => $this->isAvailable(),
            'is_active'          => $this->is_active,
            'metadata'           => $this->metadata ?? [],
            'match'              => $this->whenLoaded('matchEvent', fn () => $this->matchEvent ? [
                'code'       => $this->matchEvent->code,
                'home_team'  => $this->matchEvent->home_team,
                'away_team'  => $this->matchEvent->away_team,
                'match_date' => $this->matchEvent->match_date?->toISOString(),
                'stadium'    => $this->matchEvent->stadium_name,
            ] : null),
        ];
    }
}
