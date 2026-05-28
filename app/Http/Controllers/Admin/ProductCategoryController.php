<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Store\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Store\StoreProductCategoryRequest;
use App\Http\Requests\Admin\Store\UpdateProductCategoryRequest;
use App\Support\Audit\RecordsAdminAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.product-categories.index', [
            'categories' => ProductCategory::query()->withCount('products')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.product-categories.create', [
            'category' => new ProductCategory([
                'sort_order' => 0,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $category = ProductCategory::query()->create($request->validated());
        RecordsAdminAudit::created('product_categories', $category, $request);

        return redirect()->route('admin.product-categories.index');
    }

    public function edit(ProductCategory $productCategory): View
    {
        return view('admin.product-categories.edit', [
            'category' => $productCategory,
        ]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $before = $productCategory->attributesToArray();
        $productCategory->update($request->validated());
        $productCategory->refresh();

        RecordsAdminAudit::updated('product_categories', $productCategory, $request, $before);

        return redirect()->route('admin.product-categories.index');
    }

    public function destroy(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        if ($productCategory->products()->exists()) {
            return redirect()
                ->route('admin.product-categories.index')
                ->with('error', 'No puedes eliminar una categoria con productos asociados.');
        }

        $before = $productCategory->attributesToArray();
        $productCategory->delete();

        RecordsAdminAudit::deleted('product_categories', $productCategory, $request, $before);

        return redirect()->route('admin.product-categories.index');
    }
}
