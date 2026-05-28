<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_number'   => $this->order_number,
            'status'         => $this->status->value,
            'customer_name'  => $this->customer_name,
            'customer_email' => $this->customer_email,
            'total'          => $this->total,
            'currency'       => $this->currency,
            'paid_at'        => $this->paid_at?->toISOString(),
            'cancelled_at'   => $this->cancelled_at?->toISOString(),
            'payment_status' => $this->payment?->status?->value,
            'match'          => $this->whenLoaded('matchEvent', fn () => [
                'code'       => $this->matchEvent->code,
                'home_team'  => $this->matchEvent->home_team,
                'away_team'  => $this->matchEvent->away_team,
                'match_date' => $this->matchEvent->match_date?->toISOString(),
                'stadium'    => $this->matchEvent->stadium_name,
            ]),
            'items'          => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'zone_name'  => $item->zone_name,
                'unit_price' => $item->unit_price,
                'quantity'   => $item->quantity,
                'line_total' => $item->line_total,
            ])),
        ];
    }
}
