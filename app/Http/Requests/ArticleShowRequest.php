<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Handle private access via API key
        if ($this->is('private/*')) {
            return $this->header('X-API-KEY') === config('app.api_key');
        }
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => 'required|string|max:70'
        ];
    }

    // Add path to validation data
    public function validationData()
    {
        return array_merge($this->all(), [
            'path' => $this->route('path')
        ]);
    }
}