<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\MatchGoal;
use App\Domain\Squad\Models\Player;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sports\StoreMatchGoalRequest;
use App\Http\Requests\Admin\Sports\UpdateMatchGoalRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchGoalController extends Controller
{
    public function index(MatchEvent $matchEvent): View
    {
        return view('admin.match-goals.index', [
            'matchEvent' => $matchEvent,
            'goals'      => $matchEvent->goals()->with(['club', 'player'])->get(),
        ]);
    }

    public function create(MatchEvent $matchEvent): View
    {
        return view('admin.match-goals.create', [
            'matchEvent' => $matchEvent,
            'clubs'      => Club::query()->where('is_active', true)->orderBy('name')->get(),
            'players'    => Player::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreMatchGoalRequest $request, MatchEvent $matchEvent): RedirectResponse
    {
        $data = array_merge($request->validated(), ['match_event_id' => $matchEvent->id]);
        $goal = MatchGoal::query()->create($data);
        RecordsAdminAudit::created('match_goals', $goal, $request);

        return redirect()->route('admin.match-events.goals.index', $matchEvent);
    }

    public function edit(MatchEvent $matchEvent, MatchGoal $goal): View
    {
        return view('admin.match-goals.edit', [
            'matchEvent' => $matchEvent,
            'goal'       => $goal,
            'clubs'      => Club::query()->where('is_active', true)->orderBy('name')->get(),
            'players'    => Player::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMatchGoalRequest $request, MatchEvent $matchEvent, MatchGoal $goal): RedirectResponse
    {
        $before = $goal->attributesToArray();
        $goal->update($request->validated());
        RecordsAdminAudit::updated('match_goals', $goal, $request, $before);

        return redirect()->route('admin.match-events.goals.index', $matchEvent);
    }

    public function destroy(Request $request, MatchEvent $matchEvent, MatchGoal $goal): RedirectResponse
    {
        $before = $goal->attributesToArray();
        $goal->delete();
        RecordsAdminAudit::deleted('match_goals', $goal, $request, $before);

        return redirect()->route('admin.match-events.goals.index', $matchEvent);
    }
}
