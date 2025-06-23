<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleTranslationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'article_translation_id' => $this->article_translation_id,
            'language_code' => $this->language_code,
            'title' => $this->title,
            'path' => $this->path,
            'summary' => $this->summary,
            'keywords' => $this->keywords,
            'content' => $this->content,
            'status' => $this->status,
            'unpublished_at' => $this->unpublished_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}