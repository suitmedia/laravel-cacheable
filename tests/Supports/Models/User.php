<?php

namespace Suitmedia\Cacheable\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Traits\Model\CacheableTrait;

class User extends Model implements CacheableModel
{
    use CacheableTrait;
    use HasFactory;

    protected static $cacheTags = ['User', 'UserRoles'];

    protected $fillable = [
        'name',
        'email',
        'password'
    ];
}
