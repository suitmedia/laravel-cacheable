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
    public function cacheDuration(): int;

    /**
     * Return an array of method names which
     * you don't wish to be cached.
     *
     * @return array
     */
    public function cacheExcept(): array;

    /**
     * Generate cache key.
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return string
     */
    public function cacheKey($method, $args): string;

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
     * @return \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    public function model(): CacheableModel;
}
