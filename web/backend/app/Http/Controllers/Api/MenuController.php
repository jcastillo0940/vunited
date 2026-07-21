<?php

namespace App\Http\Controllers\Api;

use App\Domain\Menus\Models\Menu;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;

class MenuController extends Controller
{
    public function __invoke(string $location): MenuResource
    {
        $menu = Menu::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->with([
                'items' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'children' => fn ($childQuery) => $childQuery
                            ->where('is_active', true)
                            ->orderBy('sort_order'),
                    ]),
            ])
            ->first();

        return new MenuResource($menu ?? new Menu([
            'name' => ucfirst($location),
            'location' => $location,
            'is_active' => true,
        ]));
    }
}
