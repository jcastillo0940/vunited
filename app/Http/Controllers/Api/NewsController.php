<?php

namespace App\Http\Controllers\Api;

use App\Domain\News\Models\NewsArticle;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsArticleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $articles = NewsArticle::query()
            ->with('category')
            ->where(function ($query): void {
                $query->where('status', 'published')
                    ->orWhere(function ($scheduledQuery): void {
                        $scheduledQuery->where('status', 'scheduled')
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());
                    });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return NewsArticleResource::collection($articles);
    }

    public function show(string $slug): NewsArticleResource
    {
        $article = NewsArticle::query()
            ->with('category')
            ->where('slug', $slug)
            ->where(function ($query): void {
                $query->where('status', 'published')
                    ->orWhere(function ($scheduledQuery): void {
                        $scheduledQuery->where('status', 'scheduled')
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());
                    });
            })
            ->firstOrFail();

        return new NewsArticleResource($article);
    }
}
