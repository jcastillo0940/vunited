<?php

namespace App\Http\Controllers\Api;

use App\Domain\Store\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\V1\Store\ProductResource as VersionedProductResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('product_category_id')
                    ->orWhereHas('category', fn (Builder $category): Builder => $category->where('is_active', true));
            });

        if ($category = trim($request->string('category')->toString())) {
            $query->whereHas('category', fn (Builder $builder): Builder => $builder->where('slug', $category));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $resource = $request->routeIs('api.v1.store.*')
            ? VersionedProductResource::class
            : ProductResource::class;

        return $resource::collection(
            $query->orderBy('sort_order')->orderBy('name')->get(),
        );
    }

    public function featured(Request $request): JsonResponse
    {
        $product = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('product_category_id')
                    ->orWhereHas('category', fn (Builder $category): Builder => $category->where('is_active', true));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->first();

        if ($product === null) {
            return response()->json([
                'error' => 'No hay un producto destacado disponible.',
            ], 404);
        }

        $resource = $request->routeIs('api.v1.store.*')
            ? VersionedProductResource::class
            : ProductResource::class;

        return (new $resource($product))->response();
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('product_category_id')
                    ->orWhereHas('category', fn (Builder $category): Builder => $category->where('is_active', true));
            })
            ->first();

        if ($product === null) {
            return response()->json([
                'error' => 'Producto no encontrado.',
            ], 404);
        }

        $resource = $request->routeIs('api.v1.store.*')
            ? VersionedProductResource::class
            : ProductResource::class;

        return (new $resource($product))->response();
    }
}
