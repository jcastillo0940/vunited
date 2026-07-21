<?php

namespace App\Http\Resources\V1\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price_minor' => $this->toMinorUnits($this->price),
            'compare_at_price_minor' => $this->compare_at_price === null
                ? null
                : $this->toMinorUnits($this->compare_at_price),
            'currency' => strtoupper((string) $this->currency),
            'stock_quantity' => $this->stock_quantity,
            'track_stock' => $this->track_stock,
            'is_featured' => $this->is_featured,
            'badge' => $this->badge,
            'image_url' => $this->resolvePath($this->image_path),
            'gallery' => collect($this->gallery ?? [])
                ->map(fn ($item) => $this->resolvePath($item))
                ->values()
                ->all(),
            'metadata' => $this->metadata ?? [],
            'sort_order' => $this->sort_order,
            'out_of_stock' => $this->resource->isOutOfStock(),
            'category' => $this->category ? [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
        ];
    }

    private function toMinorUnits(mixed $amount): int
    {
        $normalized = (string) $amount;

        if (! preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $normalized, $matches)) {
            throw new InvalidArgumentException('El monto no tiene un formato monetario válido.');
        }

        $fraction = str_pad($matches[2] ?? '', 2, '0');

        return ((int) $matches[1] * 100) + (int) $fraction;
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
