<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ArticleService
{
    public function getArticles(bool $isPrivate, array $filters = [], int $perPage = 10): array
    {
        $query = ArticleTranslation::with('article', 'language')
            ->when(!$isPrivate, function ($query) {
                $query->where('status', 'published')
                    ->whereHas('article', function ($q) {
                        $q->where('visibility', 'public');
                    });
            })
            ->latest('article_translations.updated_at');

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage);
        
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ];
    }

    public function getArticleByPath(string $path, bool $isPrivate): ?ArticleTranslation
    {
        return ArticleTranslation::where('path', $path)
            ->when(!$isPrivate, function ($query) {
                $query->where('status', 'published')
                    ->whereHas('article', function ($q) {
                        $q->where('visibility', 'public');
                    });
            })
            ->with('article', 'language')
            ->first();
    }

    public function createArticle(array $data): Article
    {
        $article = Article::create([
            'node_type' => $data['node_type'],
            'visibility' => $data['visibility'],
        ]);

        foreach ($data['translations'] as $translation) {
            $article->translations()->create($translation);
        }

        return $article->load('translations');
    }

    public function updateArticle(Article $article, array $data): Article
    {
        $article->update([
            'node_type' => $data['node_type'] ?? $article->node_type,
            'visibility' => $data['visibility'] ?? $article->visibility,
        ]);

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $translation) {
                $article->translations()->updateOrCreate(
                    ['article_translation_id' => $translation['article_translation_id'] ?? null],
                    $translation
                );
            }
        }

        return $article->load('translations');
    }

    protected function applyFilters($query, array $filters): void
    {
        $query->when(Arr::get($filters, 'title'), function ($q, $title) {
            $q->where('title', 'like', "%$title%");
        })
        ->when(Arr::get($filters, 'node_type'), function ($q, $type) {
            $q->whereHas('article', function ($q) use ($type) {
                $q->where('node_type', $type);
            });
        })
        ->when(Arr::get($filters, 'status'), function ($q, $status) {
            $q->where('status', $status);
        })
        ->when(Arr::get($filters, 'language_code'), function ($q, $code) {
            $q->where('language_code', $code);
        })
        ->when(Arr::get($filters, 'from'), function ($q, $date) {
            $q->whereDate('article_translations.updated_at', '>=', $date);
        })
        ->when(Arr::get($filters, 'to'), function ($q, $date) {
            $q->whereDate('article_translations.updated_at', '<=', $date);
        });
    }
}