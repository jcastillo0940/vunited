<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Squad\Models\StaffMember;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffMemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = StaffMember::query();

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.staff-members.index', [
            'staff'   => $query->orderBy('category')->orderBy('sort_order')->orderBy('name')->get(),
            'filters' => [
                'category' => $request->string('category')->toString(),
                'search'   => $request->string('search')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.staff-members.create', ['member' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        StaffMember::create($data);

        return redirect()->route('admin.staff-members.index');
    }

    public function edit(StaffMember $staffMember): View
    {
        return view('admin.staff-members.edit', ['member' => $staffMember]);
    }

    public function update(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $data = $this->validated($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $staffMember->update($data);

        return redirect()->route('admin.staff-members.index');
    }

    public function destroy(StaffMember $staffMember): RedirectResponse
    {
        $staffMember->delete();

        return redirect()->route('admin.staff-members.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'slug'       => ['nullable', 'string', 'max:150'],
            'role'       => ['required', 'string', 'max:100'],
            'category'   => ['required', 'string', 'max:30'],
            'photo_path' => ['nullable', 'string', 'max:500'],
            'biography'  => ['nullable', 'string'],
            'is_active'  => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]) + ['is_active' => false, 'sort_order' => 0];
    }
}
