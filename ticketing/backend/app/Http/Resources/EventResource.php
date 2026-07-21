<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'code' => $this->code,
            'home_team' => $this->home_team,
            'away_team' => $this->away_team,
            'competition' => $this->competition,
            'round_label' => $this->round_label,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'venue_name' => $this->venue_name,
            'venue_location' => $this->venue_location,
            'status' => $this->status,
            'on_sale' => $this->isOnSale(),
            'purchase_limit_per_buyer' => $this->purchase_limit_per_buyer,
        ];
    }
}
