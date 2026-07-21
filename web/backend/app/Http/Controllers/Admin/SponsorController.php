<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Sponsors\Models\Sponsor;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sponsor::query();

        if ($tier = $request->string('tier')->toString()) {
            $query->where('tier', $tier);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.sponsors.index', [
            'sponsors' => $query->orderByRaw("CASE tier WHEN 'main_partner' THEN 1 WHEN 'official_sponsor' THEN 2 WHEN 'strategic_ally' THEN 3 ELSE 4 END")
                                ->orderBy('sort_order')->orderBy('name')->get(),
            'filters' => [
                'tier'   => $request->string('tier')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.sponsors.create', ['sponsor' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        Sponsor::create($data);

        return redirect()->route('admin.sponsors.index');
    }

    public function edit(Sponsor $sponsor): View
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $data = $this->validated($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $sponsor->update($data);

        return redirect()->route('admin.sponsors.index');
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        $sponsor->delete();

        return redirect()->route('admin.sponsors.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:150'],
            'tier'        => ['required', 'string', 'in:main_partner,official_sponsor,strategic_ally'],
            'logo_path'   => ['nullable', 'string', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:300'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]) + ['is_active' => false, 'sort_order' => 0];
    }
}
