<?php

namespace App\Http\Requests;

use App\Enums\ArticleCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', Rule::enum(ArticleCategory::class)],
            'excerpt' => ['nullable', 'string', 'max:400'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'category' => 'catégorie',
            'excerpt' => 'chapô',
            'content' => 'contenu',
            'image' => 'image',
            'published_at' => 'date de publication',
            'meta_title' => 'titre SEO',
            'meta_description' => 'description SEO',
        ];
    }
}
