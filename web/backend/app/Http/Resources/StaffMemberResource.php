<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', $this->name, 2);

        return [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'name'       => $this->name,
            'first_name' => $nameParts[0] ?? $this->name,
            'last_name'  => $nameParts[1] ?? '',
            'role'       => $this->role,
            'category'   => $this->category,
            'photo_path' => $this->photo_path,
            'biography'  => $this->biography,
            'is_active'  => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
