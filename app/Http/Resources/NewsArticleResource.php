<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'body' => $this->body,
            'published_at' => $this->published_at?->toISOString(),
            'is_featured' => $this->is_featured,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'featured_image_url' => $this->publicPath($this->featured_image_path),
            'category' => $this->category ? [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
        ];
    }

    private function publicPath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        return parse_url(Storage::disk('public')->url($path), PHP_URL_PATH) ?: null;
    }
}
