<?php

namespace Suitmedia\Cacheable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Traits\Model\CacheableTrait;

class Video extends Model implements CacheableModel
{
    use CacheableTrait;
    use SoftDeletes;

    protected static $cacheDuration = 120;

    protected $fillable = [
        'title',
        'url'
    ];
}
