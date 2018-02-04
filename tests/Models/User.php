<?php

namespace Suitmedia\Cacheable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Traits\Model\CacheableTrait;

class User extends Model implements CacheableModel
{
    use CacheableTrait;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function cacheTags()
    {
        return ['User', 'UserRoles'];
    }
}
