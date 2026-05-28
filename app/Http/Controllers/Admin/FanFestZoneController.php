<?php

namespace App\Http\Controllers\Admin;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Domain\FanFest\Models\FanFestZone;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FanFestZoneController extends Controller
{
    public function index(FanFestEvent $fanFestEvent): View
    {
        return view('admin.fanfest-zones.index', [
            'event' => $fanFestEvent,
            'zones' => $fanFestEvent->allZones()->get(),
        ]);
    }

    public function create(FanFestEvent $fanFestEvent): View
    {
        return view('admin.fanfest-zones.create', [
            'event' => $fanFestEvent,
            'zone'  => null,
        ]);
    }

    public function store(Request $request, FanFestEvent $fanFestEvent): RedirectResponse
    {
        $data = $this->validated($request);
        $data['fan_fest_event_id'] = $fanFestEvent->id;

        FanFestZone::create($data);

        return redirect()->route('admin.fanfest-events.zones.index', $fanFestEvent);
    }

    public function edit(FanFestEvent $fanFestEvent, FanFestZone $fanFestZone): View
    {
        return view('admin.fanfest-zones.edit', [
            'event' => $fanFestEvent,
            'zone'  => $fanFestZone,
        ]);
    }

    public function update(Request $request, FanFestEvent $fanFestEvent, FanFestZone $fanFestZone): RedirectResponse
    {
        $fanFestZone->update($this->validated($request));

        return redirect()->route('admin.fanfest-events.zones.index', $fanFestEvent);
    }

    public function destroy(FanFestEvent $fanFestEvent, FanFestZone $fanFestZone): RedirectResponse
    {
        $fanFestZone->delete();

        return redirect()->route('admin.fanfest-events.zones.index', $fanFestEvent);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:60'],
            'sort_order'  => ['integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]) + ['is_active' => false, 'sort_order' => 0];
    }
}
