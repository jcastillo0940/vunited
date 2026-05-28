<?php

namespace App\Http\Resources;

use App\Domain\Board\Models\BoardMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'name'        => $this->name,
            'role'        => $this->role,
            'group'       => $this->group,
            'group_label' => BoardMember::groupLabel($this->group),
            'photo_path'  => $this->photo_path,
            'biography'   => $this->biography,
            'email'       => $this->email,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'metadata'    => $this->metadata ?? [],
        ];
    }
}
