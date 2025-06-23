<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'node_type' => 'sometimes|in:article,user_agreement,page',
            'visibility' => 'sometimes|in:public,private',
            'translations' => 'sometimes|array',
            'translations.*.article_translation_id' => 'sometimes|exists:article_translations,article_translation_id',
            'translations.*.language_code' => 'sometimes|string|size:2|exists:site_languages,language_code',
            'translations.*.title' => 'sometimes|string|max:70',
            'translations.*.path' => 'sometimes|string|max:70|unique:article_translations,path,' . ($this->translations[0]['article_translation_id'] ?? 'NULL') . ',article_translation_id',
            'translations.*.summary' => 'nullable|string|max:180',
            'translations.*.keywords' => 'nullable|string|max:255',
            'translations.*.content' => 'sometimes|string',
            'translations.*.status' => 'sometimes|in:draft,published,unpublished',
            'translations.*.unpublished_at' => 'nullable|date',
        ];
    }
}