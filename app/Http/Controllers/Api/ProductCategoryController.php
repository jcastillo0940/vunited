<?php

namespace App\Http\Controllers\Api;

use App\Domain\Store\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductCategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProductCategoryResource::collection(
            ProductCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        );
    }
}
