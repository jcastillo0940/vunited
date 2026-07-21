<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Board\Models\BoardMember;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BoardMemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = BoardMember::query();

        if ($group = $request->string('group')->toString()) {
            $query->where('group', $group);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.board-members.index', [
            'members' => $query->orderByRaw("CASE `group` WHEN 'presidency' THEN 1 WHEN 'executive_staff' THEN 2 WHEN 'board' THEN 3 WHEN 'transparency' THEN 4 ELSE 5 END")
                               ->orderBy('sort_order')->orderBy('name')->get(),
            'filters' => [
                'group'  => $request->string('group')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.board-members.create', ['member' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        BoardMember::create($data);

        return redirect()->route('admin.board-members.index');
    }

    public function edit(BoardMember $boardMember): View
    {
        return view('admin.board-members.edit', ['member' => $boardMember]);
    }

    public function update(Request $request, BoardMember $boardMember): RedirectResponse
    {
        $data = $this->validated($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $boardMember->update($data);

        return redirect()->route('admin.board-members.index');
    }

    public function destroy(BoardMember $boardMember): RedirectResponse
    {
        $boardMember->delete();

        return redirect()->route('admin.board-members.index');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'slug'       => ['nullable', 'string', 'max:150'],
            'role'       => ['required', 'string', 'max:100'],
            'group'      => ['required', 'string', 'in:presidency,executive_staff,board,transparency'],
            'photo_path' => ['nullable', 'string', 'max:500'],
            'biography'  => ['nullable', 'string'],
            'email'      => ['nullable', 'email', 'max:200'],
            'sort_order' => ['integer', 'min:0'],
            'is_active'  => ['boolean'],
            'metadata'   => ['nullable', 'string'],
        ]) + ['is_active' => false, 'sort_order' => 0];

        if (! empty($data['metadata'])) {
            $decoded = json_decode($data['metadata'], true);
            $data['metadata'] = is_array($decoded) ? $decoded : null;
        } else {
            $data['metadata'] = null;
        }

        return $data;
    }
}
