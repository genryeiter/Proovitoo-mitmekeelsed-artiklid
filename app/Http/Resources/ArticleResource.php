<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->article_id,
            'node_type' => $this->node_type,
            'visibility' => $this->visibility,
            'translations' => ArticleTranslationResource::collection($this->translations),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}