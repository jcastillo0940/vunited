<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Stadium\Models\Stadium;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Stadium\StoreStadiumRequest;
use App\Http\Requests\Admin\Stadium\UpdateStadiumRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StadiumController extends Controller
{
    public function index(): View
    {
        return view('admin.stadium.index', [
            'stadiums' => Stadium::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stadium.create', [
            'stadium' => new Stadium(['is_active' => true]),
        ]);
    }

    public function store(StoreStadiumRequest $request): RedirectResponse
    {
        $stadium = Stadium::query()->create($request->validated());
        RecordsAdminAudit::created('stadium', $stadium, $request);

        return redirect()->route('admin.stadium.index');
    }

    public function edit(Stadium $stadium): View
    {
        return view('admin.stadium.edit', compact('stadium'));
    }

    public function update(UpdateStadiumRequest $request, Stadium $stadium): RedirectResponse
    {
        $before = $stadium->attributesToArray();
        $stadium->update($request->validated());
        $stadium->refresh();

        RecordsAdminAudit::updated('stadium', $stadium, $request, $before);

        return redirect()->route('admin.stadium.index');
    }

    public function destroy(Request $request, Stadium $stadium): RedirectResponse
    {
        $before = $stadium->attributesToArray();
        $stadium->delete();

        RecordsAdminAudit::deleted('stadium', $stadium, $request, $before);

        return redirect()->route('admin.stadium.index');
    }
}
