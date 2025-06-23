<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string',
            'node_type' => 'sometimes|in:article,user_agreement,page',
            'status' => 'sometimes|in:draft,published,unpublished',
            'language_code' => 'sometimes|string|size:2',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date|after_or_equal:from',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }
}