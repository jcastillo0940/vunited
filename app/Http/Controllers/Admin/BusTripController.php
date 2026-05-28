<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Expedition\Models\BusTrip;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BusTripController extends Controller
{
    public function index(): View
    {
        return view('admin.bus-trips.index', [
            'trips' => BusTrip::query()->with('matchEvent')->orderBy('departure_time')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.bus-trips.create', [
            'trip'   => null,
            'matches' => MatchEvent::query()->orderByDesc('match_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BusTrip::create($this->validated($request));

        return redirect()->route('admin.bus-trips.index');
    }

    public function edit(BusTrip $busTrip): View
    {
        return view('admin.bus-trips.edit', [
            'trip'    => $busTrip,
            'matches' => MatchEvent::query()->orderByDesc('match_date')->get(),
        ]);
    }

    public function update(Request $request, BusTrip $busTrip): RedirectResponse
    {
        $busTrip->update($this->validated($request));

        return redirect()->route('admin.bus-trips.index');
    }

    public function destroy(BusTrip $busTrip): RedirectResponse
    {
        $busTrip->delete();

        return redirect()->route('admin.bus-trips.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'              => ['required', 'string', 'max:200'],
            'match_event_id'     => ['nullable', 'exists:match_events,id'],
            'departure_location' => ['required', 'string', 'max:300'],
            'departure_time'     => ['required', 'date'],
            'return_time'        => ['nullable', 'date', 'after:departure_time'],
            'price'              => ['required', 'numeric', 'min:0'],
            'currency'           => ['required', 'string', 'size:3'],
            'capacity'           => ['required', 'integer', 'min:1'],
            'available_seats'    => ['required', 'integer', 'min:0'],
            'is_active'          => ['boolean'],
        ]) + ['is_active' => false];
    }
}
