<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'status' => $this->status,
            'zone_name' => $this->zone?->name,
            'seat_label' => $this->seat?->label,
            'qr_token' => $this->qr_token,
            'issued_at' => $this->issued_at?->toIso8601String(),
        ];
    }
}
