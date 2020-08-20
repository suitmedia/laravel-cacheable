<?php

namespace Suitmedia\Cacheable\Tests\Repositories;

use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Contracts\CacheableRepository;
use Suitmedia\Cacheable\Traits\Repository\CacheableTrait;

abstract class EloquentRepository implements CacheableRepository
{
    use CacheableTrait;

    /**
     * Cacheable model object
     *
     * @var \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    private $model;

    /**
     * EloquentRepository class constructor
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     */
    public function __construct(CacheableModel $model)
    {
        $this->model = $model;
    }

    /**
     * Return the model object which would
     * be used by the repository
     *
     * @return \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    public function model(): CacheableModel
    {
        return $this->model;
    }
}
