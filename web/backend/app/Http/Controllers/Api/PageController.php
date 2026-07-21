<?php

namespace App\Http\Controllers\Api;

use App\Domain\Pages\Models\Page;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    public function __invoke(string $slug): PageResource
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where(function ($query): void {
                $query->where('status', 'published')
                    ->orWhere(function ($scheduledQuery): void {
                        $scheduledQuery->where('status', 'scheduled')
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());
                    });
            })
            ->with([
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->firstOrFail();

        return new PageResource($page);
    }
}
