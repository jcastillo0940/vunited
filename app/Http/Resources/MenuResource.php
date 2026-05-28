<?php

namespace App\Http\Resources;

use App\Domain\Menus\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'location' => $this->location,
            'items' => $this->menuItems($this->items ?? collect()),
        ];
    }

    private function menuItems(iterable $items): array
    {
        return collect($items)->map(function (MenuItem $item): array {
            return [
                'label' => $item->label,
                'url' => $item->url,
                'target' => $item->target,
                'sort_order' => $item->sort_order,
                'children' => $this->menuItems($item->children ?? []),
            ];
        })->values()->all();
    }
}
