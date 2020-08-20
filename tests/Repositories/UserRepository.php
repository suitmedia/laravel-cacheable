<?php

namespace Suitmedia\Cacheable\Tests\Repositories;

use Suitmedia\Cacheable\Tests\Models\User;
use Suitmedia\Cacheable\Traits\Repository\CacheableTrait;

class UserRepository extends EloquentRepository
{
    protected static $cacheDuration = 3600;
    protected static $cacheExcept = ['add', 'edit', 'create'];

    public function __construct(User $model)
    {
        parent::__construct($model);    
    }

    public function getAllUsers()
    {
        return $this->model->get();
    }

    public function getUser($UserId)
    {
        return $this->model->find($UserId);
    }

    public function update($params)
    {
        //
    }

    public function cacheKey($method, $args): string
    {
        return 'test-override-cache-key-method';
    }
}
