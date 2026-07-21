<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_number'     => $this->order_number,
            'status'           => $this->status->value,
            'full_name'        => $this->full_name,
            'email'            => $this->email,
            'membership_plan'  => $this->membership_plan,
            'membership_price' => $this->membership_price,
            'currency'         => $this->currency,
            'paid_at'          => $this->paid_at?->toISOString(),
            'starts_at'        => $this->starts_at?->toISOString(),
            'expires_at'       => $this->expires_at?->toISOString(),
            'payment_status'   => $this->payment?->status?->value,
        ];
    }
}
