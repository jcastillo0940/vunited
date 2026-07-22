<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'currency' => $this->currency,
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
            'hold_expires_at' => $this->hold_expires_at?->toIso8601String(),
            'payment_method' => $this->payment_method,
            'payment_redirect_url' => $this->metadata['payment_redirect_url'] ?? null,
            'created_at' => $this->created_at?->toIso8601String(),
            'event' => $this->whenLoaded('event', fn () => $this->event ? [
                'id' => $this->event->public_id,
                'label' => trim($this->event->home_team.' vs '.$this->event->away_team),
                'starts_at' => $this->event->starts_at?->toIso8601String(),
            ] : null),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'zone_id' => $item->zone?->public_id,
                'zone_name' => $item->zone?->name,
                'seat_id' => $item->seat?->public_id,
                'seat_label' => $item->seat?->label,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])),
        ];
    }
}
