<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticketing\StoreMatchEventRequest;
use App\Http\Requests\Admin\Ticketing\UpdateMatchEventRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchEventController extends Controller
{
    public function index(): View
    {
        return view('admin.match-events.index', [
            'matchEvents' => MatchEvent::query()
                ->withCount('ticketZones')
                ->orderByDesc('match_date')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.match-events.create', [
            'matchEvent' => new MatchEvent([
                'status' => 'scheduled',
                'is_featured' => false,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(StoreMatchEventRequest $request): RedirectResponse
    {
        $matchEvent = MatchEvent::query()->create($request->validated());
        RecordsAdminAudit::created('match_events', $matchEvent, $request);

        return redirect()->route('admin.match-events.index');
    }

    public function edit(MatchEvent $matchEvent): View
    {
        return view('admin.match-events.edit', [
            'matchEvent' => $matchEvent,
        ]);
    }

    public function update(UpdateMatchEventRequest $request, MatchEvent $matchEvent): RedirectResponse
    {
        $before = $matchEvent->attributesToArray();
        $matchEvent->update($request->validated());
        $matchEvent->refresh();

        RecordsAdminAudit::updated('match_events', $matchEvent, $request, $before);

        return redirect()->route('admin.match-events.index');
    }

    public function destroy(Request $request, MatchEvent $matchEvent): RedirectResponse
    {
        if ($matchEvent->ticketZones()->exists()) {
            return redirect()
                ->route('admin.match-events.index')
                ->with('error', 'No puedes eliminar un partido con zonas asociadas.');
        }

        $before = $matchEvent->attributesToArray();
        $matchEvent->delete();

        RecordsAdminAudit::deleted('match_events', $matchEvent, $request, $before);

        return redirect()->route('admin.match-events.index');
    }
}
