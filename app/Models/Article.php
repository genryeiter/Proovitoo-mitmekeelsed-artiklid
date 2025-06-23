<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'article_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'node_type',
        'visibility'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id');
    }
}