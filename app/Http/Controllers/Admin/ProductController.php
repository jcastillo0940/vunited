<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Store\StoreProductRequest;
use App\Http\Requests\Admin\Store\UpdateProductRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('category')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'currency' => 'USD',
                'sort_order' => 0,
                'track_stock' => false,
                'is_featured' => false,
                'is_active' => true,
            ]),
            'categories' => ProductCategory::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::query()->create($request->validated());
        RecordsAdminAudit::created('products', $product, $request);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => ProductCategory::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $before = $product->attributesToArray();
        $product->update($request->validated());
        $product->refresh();

        RecordsAdminAudit::updated('products', $product, $request, $before);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $before = $product->attributesToArray();
        $product->delete();

        RecordsAdminAudit::deleted('products', $product, $request, $before);

        return redirect()->route('admin.products.index');
    }
}
