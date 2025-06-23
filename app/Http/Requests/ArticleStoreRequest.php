<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'node_type' => 'required|in:article,user_agreement,page',
            'visibility' => 'required|in:public,private',
            'translations' => 'required|array|min:1',
            'translations.*.language_code' => 'required|string|size:2|exists:site_languages,language_code',
            'translations.*.title' => 'required|string|max:70',
            'translations.*.path' => 'required|string|max:70|unique:article_translations,path',
            'translations.*.summary' => 'nullable|string|max:180',
            'translations.*.keywords' => 'nullable|string|max:255',
            'translations.*.content' => 'required|string',
            'translations.*.status' => 'required|in:draft,published,unpublished',
            'translations.*.unpublished_at' => 'nullable|date',
        ];
    }
}