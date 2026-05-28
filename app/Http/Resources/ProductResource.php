<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'currency' => $this->currency,
            'stock_quantity' => $this->stock_quantity,
            'track_stock' => $this->track_stock,
            'is_featured' => $this->is_featured,
            'badge' => $this->badge,
            'image_url' => $this->resolvePath($this->image_path),
            'gallery' => collect($this->gallery ?? [])->map(fn ($item) => $this->resolvePath($item))->values()->all(),
            'metadata' => $this->metadata ?? [],
            'sort_order' => $this->sort_order,
            'out_of_stock' => $this->resource->isOutOfStock(),
            'category' => $this->category ? [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
        ];
    }

    private function resolvePath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return '/storage/' . ltrim($path, '/');
    }
}
