<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('pages.manage');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'excerpt' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'archived'])],
            'published_at' => ['nullable', 'date', 'required_if:status,scheduled'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'page_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_home' => ['sometimes', 'boolean'],
            'sections' => ['nullable', 'array'],
            'sections.*.section_key' => ['required_with:sections', 'string', 'max:255'],
            'sections.*.type' => ['nullable', 'string', 'max:255'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.body' => ['nullable', 'string'],
            'sections.*.payload' => ['nullable', 'array'],
            'sections.*.sort_order' => ['required_with:sections', 'integer'],
            'sections.*.is_active' => ['sometimes', 'boolean'],
            'sections.*.image_path' => ['nullable', 'string', 'max:255'],
            'sections.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
