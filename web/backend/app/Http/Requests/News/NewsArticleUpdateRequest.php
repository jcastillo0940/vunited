<?php

namespace App\Http\Requests\Admin\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsArticleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null
            && $this->user('admin')->hasPermission('news.manage');
    }

    public function rules(): array
    {
        $newsArticleId = $this->route('newsArticle')?->id;

        return [
            'news_category_id' => ['nullable', 'integer', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('news_articles', 'slug')->ignore($newsArticleId)],
            'summary' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'archived'])],
            'published_at' => ['nullable', 'date', 'required_if:status,scheduled'],
            'is_featured' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ];
    }
}
