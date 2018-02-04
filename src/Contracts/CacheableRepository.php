<?php

namespace Suitmedia\Cacheable\Contracts;

interface CacheableRepository
{
    /**
     * Return the cache duration value
     * which would be used by the repository.
     *
     * @return int
     */
    public function cacheDuration();

    /**
     * Return an array of method names which
     * you don't wish to be cached.
     *
     * @return array
     */
    public function cacheExcept();

    /**
     * Generate cache key.
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return string
     */
    public function cacheKey($method, $args);

    /**
     * Return the cache tags which would
     * be used by the repository.
     *
     * @return mixed
     */
    public function cacheTags();

    /**
     * Return the primary model object which would
     * be used by the repository.
     *
     * @return \Suitmedia\Contracts\Cacheable\CacheableModel
     */
    public function model();
}
