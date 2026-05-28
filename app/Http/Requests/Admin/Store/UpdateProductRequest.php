<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('products.manage');
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'compare_at_price' => ['nullable', 'numeric', 'gte:price'],
            'currency' => ['required', 'string', 'size:3'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'badge' => ['nullable', 'string', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:1000'],
            'gallery' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'sort_order' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = (string) $this->input('name');
        $slug = (string) $this->input('slug');

        $this->merge([
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'currency' => strtoupper((string) $this->input('currency')),
            'track_stock' => $this->boolean('track_stock'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
            'gallery' => $this->normalizeListOrJson($this->input('gallery')),
            'metadata' => $this->normalizeJson($this->input('metadata')),
        ]);
    }

    private function normalizeListOrJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return array_values($decoded);
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value) ?: [])));
    }

    private function normalizeJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }
}
