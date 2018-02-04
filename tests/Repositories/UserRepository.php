<?php

namespace Suitmedia\Cacheable\Tests\Repositories;

use Suitmedia\Cacheable\Tests\Models\User;
use Suitmedia\Cacheable\Traits\Repository\CacheableTrait;

class UserRepository extends EloquentRepository
{
    use CacheableTrait;

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

    public function cacheDuration()
    {
        return 3600;
    }

    public function cacheExcept()
    {
        return ['add', 'edit'];
    }

    public function cacheKey($method, $args)
    {
        return 'test-override-cache-key-method';
    }
}
