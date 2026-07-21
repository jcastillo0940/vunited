<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Menus\Models\Menu;
use App\Domain\Menus\Models\MenuItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\MenuItemStoreRequest;
use App\Http\Requests\Admin\Menu\MenuItemUpdateRequest;
use App\Http\Requests\Admin\Menu\MenuStoreRequest;
use App\Http\Requests\Admin\Menu\MenuUpdateRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.index', [
            'menus' => Menu::query()->withCount('items')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.menus.create');
    }

    public function store(MenuStoreRequest $request): RedirectResponse
    {
        $menu = Menu::query()->create($request->validated());

        RecordsAdminAudit::created('menus', $menu, $request);

        return redirect()->route('admin.menus.index');
    }

    public function edit(Menu $menu): View
    {
        return view('admin.menus.edit', [
            'menu' => $menu->load(['items' => fn ($query) => $query->orderBy('sort_order')]),
        ]);
    }

    public function update(MenuUpdateRequest $request, Menu $menu): RedirectResponse
    {
        $before = $menu->attributesToArray();
        $menu->update($request->validated());
        $menu->refresh();

        RecordsAdminAudit::updated('menus', $menu, $request, $before);

        return redirect()->route('admin.menus.index');
    }

    public function storeItem(MenuItemStoreRequest $request, Menu $menu): RedirectResponse
    {
        $menuItem = $menu->items()->create($request->validated());

        RecordsAdminAudit::created('menus', $menuItem, $request);

        return redirect()->route('admin.menus.edit', $menu);
    }

    public function updateItem(MenuItemUpdateRequest $request, Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($menuItem->menu_id === $menu->id, 404);

        $before = $menuItem->attributesToArray();
        $menuItem->update($request->validated());
        $menuItem->refresh();

        RecordsAdminAudit::updated('menus', $menuItem, $request, $before);

        return redirect()->route('admin.menus.edit', $menu);
    }

    public function destroyItem(Menu $menu, MenuItem $menuItem, \Illuminate\Http\Request $request): RedirectResponse
    {
        abort_unless($menuItem->menu_id === $menu->id, 404);

        $before = $menuItem->attributesToArray();
        $menuItem->delete();

        RecordsAdminAudit::deleted('menus', $menuItem, $request, $before);

        return redirect()->route('admin.menus.edit', $menu);
    }
}
