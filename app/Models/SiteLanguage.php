<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteLanguage extends Model
{
    protected $primaryKey = 'language_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'language_code',
        'language_name'
    ];
}