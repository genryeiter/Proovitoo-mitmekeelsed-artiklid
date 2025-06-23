<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ArticleTranslation extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'article_translation_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'article_id',
        'language_code',
        'title',
        'path',
        'summary',
        'keywords',
        'content',
        'status',
        'unpublished_at',
        'created_by',
        'modified_by'
    ];

    protected $casts = [
        'unpublished_at' => 'datetime'
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(SiteLanguage::class, 'language_code', 'language_code');
    }

    // Scopes for filtering
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['title'] ?? null, fn($q, $title) => $q->where('title', 'like', "%$title%"))
            ->when($filters['node_type'] ?? null, fn($q, $type) => $q->whereHas('article', fn($q) => $q->where('node_type', $type)))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['language_code'] ?? null, fn($q, $code) => $q->where('language_code', $code))
            ->when($filters['from'] ?? null, fn($q, $date) => $q->whereDate('updated_at', '>=', $date))
            ->when($filters['to'] ?? null, fn($q, $date) => $q->whereDate('updated_at', '<=', $date));
    }
}
