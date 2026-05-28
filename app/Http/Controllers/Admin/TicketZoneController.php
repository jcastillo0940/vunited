<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticketing\StoreTicketZoneRequest;
use App\Http\Requests\Admin\Ticketing\UpdateTicketZoneRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketZoneController extends Controller
{
    public function index(MatchEvent $matchEvent): View
    {
        return view('admin.ticket-zones.index', [
            'matchEvent' => $matchEvent,
            'ticketZones' => $matchEvent->ticketZones()->get(),
        ]);
    }

    public function create(MatchEvent $matchEvent): View
    {
        return view('admin.ticket-zones.create', [
            'matchEvent' => $matchEvent,
            'ticketZone' => new TicketZone([
                'currency' => 'USD',
                'sort_order' => 0,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(StoreTicketZoneRequest $request, MatchEvent $matchEvent): RedirectResponse
    {
        $ticketZone = $matchEvent->ticketZones()->create($request->validated());
        RecordsAdminAudit::created('ticket_zones', $ticketZone, $request);

        return redirect()->route('admin.ticket-zones.index', $matchEvent);
    }

    public function edit(MatchEvent $matchEvent, TicketZone $ticketZone): View
    {
        abort_unless($ticketZone->match_event_id === $matchEvent->id, 404);

        return view('admin.ticket-zones.edit', [
            'matchEvent' => $matchEvent,
            'ticketZone' => $ticketZone,
        ]);
    }

    public function update(UpdateTicketZoneRequest $request, MatchEvent $matchEvent, TicketZone $ticketZone): RedirectResponse
    {
        abort_unless($ticketZone->match_event_id === $matchEvent->id, 404);

        $before = $ticketZone->attributesToArray();
        $ticketZone->update($request->validated());
        $ticketZone->refresh();

        RecordsAdminAudit::updated('ticket_zones', $ticketZone, $request, $before);

        return redirect()->route('admin.ticket-zones.index', $matchEvent);
    }

    public function destroy(Request $request, MatchEvent $matchEvent, TicketZone $ticketZone): RedirectResponse
    {
        abort_unless($ticketZone->match_event_id === $matchEvent->id, 404);

        $before = $ticketZone->attributesToArray();
        $ticketZone->delete();

        RecordsAdminAudit::deleted('ticket_zones', $ticketZone, $request, $before);

        return redirect()->route('admin.ticket-zones.index', $matchEvent);
    }
}
