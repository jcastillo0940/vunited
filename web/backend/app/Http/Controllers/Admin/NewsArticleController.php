<?php

namespace App\Http\Controllers\Admin;

use App\Domain\News\Models\NewsArticle;
use App\Domain\News\Models\NewsCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\News\NewsArticleStoreRequest;
use App\Http\Requests\Admin\News\NewsArticleUpdateRequest;
use App\Support\Audit\RecordsAdminAudit;
use App\Support\Media\StoresUploadedMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class NewsArticleController extends Controller
{
    public function index(): View
    {
        return view('admin.news.index', [
            'articles' => NewsArticle::query()->with('category')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.create', [
            'categories' => NewsCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(NewsArticleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newsArticle = NewsArticle::query()->create(Arr::except($validated, 'featured_image'));

        if ($request->hasFile('featured_image')) {
            $media = StoresUploadedMedia::store($newsArticle, $request->file('featured_image'), 'featured_image');
            $newsArticle->update(['featured_image_path' => $media->path]);
        }

        $newsArticle->refresh();
        RecordsAdminAudit::created('news', $newsArticle, $request);

        return redirect()->route('admin.news.index');
    }

    public function edit(NewsArticle $newsArticle): View
    {
        return view('admin.news.edit', [
            'newsArticle' => $newsArticle,
            'categories' => NewsCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(NewsArticleUpdateRequest $request, NewsArticle $newsArticle): RedirectResponse
    {
        $validated = $request->validated();
        $before = $newsArticle->attributesToArray();
        $newsArticle->update(Arr::except($validated, 'featured_image'));

        if ($request->hasFile('featured_image')) {
            $media = StoresUploadedMedia::store($newsArticle, $request->file('featured_image'), 'featured_image');
            $newsArticle->update(['featured_image_path' => $media->path]);
        }

        $newsArticle->refresh();
        RecordsAdminAudit::updated('news', $newsArticle, $request, $before);

        return redirect()->route('admin.news.index');
    }

    public function destroy(\Illuminate\Http\Request $request, NewsArticle $newsArticle): RedirectResponse
    {
        $before = $newsArticle->attributesToArray();
        $newsArticle->delete();

        RecordsAdminAudit::deleted('news', $newsArticle, $request, $before);

        return redirect()->route('admin.news.index');
    }
}
