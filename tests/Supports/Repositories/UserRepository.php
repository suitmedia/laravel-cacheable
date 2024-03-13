<?php

namespace Suitmedia\Cacheable\Tests\Supports\Repositories;

use Suitmedia\Cacheable\Tests\Supports\Models\User;

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
        return $this->model()->get();
    }

    public function getUser($UserId)
    {
        return $this->model()->find($UserId);
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
