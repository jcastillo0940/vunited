<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Sports\Models\Club;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sports\StoreClubRequest;
use App\Http\Requests\Admin\Sports\UpdateClubRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(Request $request): View
    {
        $query = Club::query();

        if ($search = trim($request->string('search')->toString())) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.clubs.index', [
            'clubs'  => $query->orderBy('sort_order')->orderBy('name')->get(),
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.clubs.create', ['club' => new Club(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(StoreClubRequest $request): RedirectResponse
    {
        $club = Club::query()->create($request->validated());
        RecordsAdminAudit::created('clubs', $club, $request);

        return redirect()->route('admin.clubs.index');
    }

    public function edit(Club $club): View
    {
        return view('admin.clubs.edit', compact('club'));
    }

    public function update(UpdateClubRequest $request, Club $club): RedirectResponse
    {
        $before = $club->attributesToArray();
        $club->update($request->validated());
        $club->refresh();

        RecordsAdminAudit::updated('clubs', $club, $request, $before);

        return redirect()->route('admin.clubs.index');
    }

    public function destroy(Request $request, Club $club): RedirectResponse
    {
        $before = $club->attributesToArray();
        $club->delete();

        RecordsAdminAudit::deleted('clubs', $club, $request, $before);

        return redirect()->route('admin.clubs.index');
    }
}
