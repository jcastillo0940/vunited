<?php

namespace App\Http\Resources;

use App\Domain\Pages\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'published_at' => $this->published_at?->toISOString(),
            'sections' => collect($this->sections)->map(function (PageSection $section): array {
                return [
                    'section_key' => $section->section_key,
                    'type' => $section->type,
                    'title' => $section->title,
                    'body' => $section->body,
                    'payload' => $section->payload ?? [],
                    'sort_order' => $section->sort_order,
                    'image_url' => $this->publicPath($section->image_path),
                ];
            })->values()->all(),
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
