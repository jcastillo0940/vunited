<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Squad\Models\Player;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Player::query();

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return view('admin.players.index', [
            'players' => $query->orderBy('category')->orderBy('sort_order')->orderBy('name')->get(),
            'filters' => [
                'category' => $request->string('category')->toString(),
                'search'   => $request->string('search')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.players.create', ['player' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        Player::create($data);

        return redirect()->route('admin.players.index');
    }

    public function edit(Player $player): View
    {
        return view('admin.players.edit', compact('player'));
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $data = $this->validated($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $player->update($data);

        return redirect()->route('admin.players.index');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();

        return redirect()->route('admin.players.index');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'slug'          => ['nullable', 'string', 'max:150'],
            'number'        => ['nullable', 'string', 'max:10'],
            'position'      => ['nullable', 'string', 'max:80'],
            'position_key'  => ['nullable', 'string', 'max:30'],
            'category'      => ['required', 'string', 'max:30'],
            'birth_date'    => ['nullable', 'date'],
            'nationality'   => ['nullable', 'string', 'max:100'],
            'height'        => ['nullable', 'string', 'max:20'],
            'weight'        => ['nullable', 'string', 'max:20'],
            'dominant_foot' => ['nullable', 'string', 'max:20'],
            'photo_path'    => ['nullable', 'string', 'max:500'],
            'biography'     => ['nullable', 'string'],
            'stats'         => ['nullable', 'string'],
            'attributes'    => ['nullable', 'string'],
            'gallery'       => ['nullable', 'string'],
            'is_active'     => ['boolean'],
            'sort_order'    => ['integer', 'min:0'],
        ]) + ['is_active' => false, 'sort_order' => 0];

        foreach (['stats', 'attributes', 'gallery'] as $field) {
            if (! empty($data[$field])) {
                $decoded = json_decode($data[$field], true);
                $data[$field] = is_array($decoded) ? $decoded : null;
            } else {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
