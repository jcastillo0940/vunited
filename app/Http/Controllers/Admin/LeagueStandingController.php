<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\LeagueStanding;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sports\StoreLeagueStandingRequest;
use App\Http\Requests\Admin\Sports\UpdateLeagueStandingRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeagueStandingController extends Controller
{
    public function index(Request $request): View
    {
        $season      = $request->string('season', date('Y'))->toString();
        $competition = $request->string('competition', 'LPF')->toString();

        return view('admin.standings.index', [
            'standings'   => LeagueStanding::query()
                ->with('club')
                ->where('season', $season)
                ->where('competition', $competition)
                ->orderBy('position')
                ->get(),
            'season'      => $season,
            'competition' => $competition,
        ]);
    }

    public function create(): View
    {
        return view('admin.standings.create', [
            'standing' => new LeagueStanding(['competition' => 'LPF', 'season' => date('Y'), 'is_active' => true]),
            'clubs'    => Club::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreLeagueStandingRequest $request): RedirectResponse
    {
        $standing = LeagueStanding::query()->create($request->validated());
        RecordsAdminAudit::created('standings', $standing, $request);

        return redirect()->route('admin.standings.index');
    }

    public function edit(LeagueStanding $standing): View
    {
        return view('admin.standings.edit', [
            'standing' => $standing,
            'clubs'    => Club::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateLeagueStandingRequest $request, LeagueStanding $standing): RedirectResponse
    {
        $before = $standing->attributesToArray();
        $standing->update($request->validated());
        RecordsAdminAudit::updated('standings', $standing, $request, $before);

        return redirect()->route('admin.standings.index');
    }

    public function destroy(Request $request, LeagueStanding $standing): RedirectResponse
    {
        $before = $standing->attributesToArray();
        $standing->delete();
        RecordsAdminAudit::deleted('standings', $standing, $request, $before);

        return redirect()->route('admin.standings.index');
    }
}
