<?php

namespace App\Http\Controllers\Admin;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FanFestEventController extends Controller
{
    public function index(): View
    {
        return view('admin.fanfest-events.index', [
            'events' => FanFestEvent::query()->withCount('allZones')->orderByDesc('event_date')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.fanfest-events.create', ['event' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['title']);

        FanFestEvent::create($data);

        return redirect()->route('admin.fanfest-events.index');
    }

    public function edit(FanFestEvent $fanFestEvent): View
    {
        return view('admin.fanfest-events.edit', ['event' => $fanFestEvent]);
    }

    public function update(Request $request, FanFestEvent $fanFestEvent): RedirectResponse
    {
        $data = $this->validated($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $fanFestEvent->update($data);

        return redirect()->route('admin.fanfest-events.index');
    }

    public function destroy(FanFestEvent $fanFestEvent): RedirectResponse
    {
        $fanFestEvent->delete();

        return redirect()->route('admin.fanfest-events.index');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:200'],
            'slug'            => ['nullable', 'string', 'max:200'],
            'description'     => ['nullable', 'string'],
            'event_date'      => ['nullable', 'date'],
            'location'        => ['nullable', 'string', 'max:300'],
            'hero_image_path' => ['nullable', 'string', 'max:500'],
            'schedule'        => ['nullable', 'string'],
            'is_active'       => ['boolean'],
        ]) + ['is_active' => false];

        if (! empty($data['schedule'])) {
            $decoded = json_decode($data['schedule'], true);
            $data['schedule'] = is_array($decoded) ? $decoded : null;
        } else {
            $data['schedule'] = null;
        }

        return $data;
    }
}
