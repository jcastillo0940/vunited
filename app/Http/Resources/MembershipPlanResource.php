<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'headline' => $this->headline,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'duration_months' => $this->duration_months,
            'benefits' => $this->benefits ?? [],
            'kit_items' => $this->kit_items ?? [],
            'partner_discounts' => $this->partner_discounts ?? [],
            'metadata' => $this->metadata ?? [],
        ];
    }
}
