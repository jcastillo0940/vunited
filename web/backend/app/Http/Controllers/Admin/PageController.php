<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Pages\Models\Page;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\PageStoreRequest;
use App\Http\Requests\Admin\Page\PageUpdateRequest;
use App\Support\Audit\RecordsAdminAudit;
use App\Support\Media\StoresUploadedMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->withCount('sections')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $page = Page::query()->create(Arr::except($validated, ['sections', 'page_image']));

        if ($request->hasFile('page_image')) {
            StoresUploadedMedia::store($page, $request->file('page_image'), 'page_image');
        }

        foreach ($validated['sections'] ?? [] as $index => $section) {
            $pageSection = $page->sections()->create(Arr::except($section, 'image'));

            if ($request->hasFile("sections.$index.image")) {
                $media = StoresUploadedMedia::store($pageSection, $request->file("sections.$index.image"), 'section_image');
                $pageSection->update(['image_path' => $media->path]);
            }

            $pageSection->refresh();
            RecordsAdminAudit::created('pages', $pageSection, $request);
        }

        $page->refresh();
        RecordsAdminAudit::created('pages', $page, $request);

        return redirect()->route('admin.pages.index');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page->load('sections'),
        ]);
    }

    public function update(PageUpdateRequest $request, Page $page): RedirectResponse
    {
        $validated = $request->validated();
        $before = $page->attributesToArray();

        $page->update(Arr::except($validated, ['sections', 'page_image']));

        if ($request->hasFile('page_image')) {
            StoresUploadedMedia::store($page, $request->file('page_image'), 'page_image');
        }

        if (array_key_exists('sections', $validated)) {
            $existingSections = $page->sections()->get();

            foreach ($existingSections as $existingSection) {
                $existingSectionBefore = $existingSection->attributesToArray();
                $existingSection->delete();
                RecordsAdminAudit::deleted('pages', $existingSection, $request, $existingSectionBefore);
            }

            foreach ($validated['sections'] as $index => $section) {
                $pageSection = $page->sections()->create(Arr::except($section, 'image'));

                if ($request->hasFile("sections.$index.image")) {
                    $media = StoresUploadedMedia::store($pageSection, $request->file("sections.$index.image"), 'section_image');
                    $pageSection->update(['image_path' => $media->path]);
                }

                $pageSection->refresh();
                RecordsAdminAudit::created('pages', $pageSection, $request);
            }
        }

        $page->refresh();
        RecordsAdminAudit::updated('pages', $page, $request, $before);

        return redirect()->route('admin.pages.index');
    }

    public function destroy(\Illuminate\Http\Request $request, Page $page): RedirectResponse
    {
        $pageBefore = $page->attributesToArray();
        $pageSections = $page->sections()->get();

        foreach ($pageSections as $pageSection) {
            $sectionBefore = $pageSection->attributesToArray();
            $pageSection->delete();
            RecordsAdminAudit::deleted('pages', $pageSection, $request, $sectionBefore);
        }

        $page->delete();

        RecordsAdminAudit::deleted('pages', $page, $request, $pageBefore);

        return redirect()->route('admin.pages.index');
    }
}
