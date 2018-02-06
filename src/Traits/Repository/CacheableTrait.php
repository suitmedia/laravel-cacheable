<?php

namespace Suitmedia\Cacheable\Traits\Repository;

trait CacheableTrait
{
    /**
     * Get the cache duration value from
     * the model object.
     *
     * @return int
     */
    public function cacheDuration()
    {
        return $this->model()->cacheDuration();
    }

    /**
     * Return an array of method names which
     * you don't wish to be cached.
     *
     * @return array
     */
    public function cacheExcept()
    {
        return \Cacheable::getConfiguration('except');
    }

    /**
     * Generate cache key.
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return string
     */
    public function cacheKey($method, $args)
    {
        $class = last(explode('\\', get_class($this)));
        $args = sha1(serialize($args));

        if (ends_with($class, 'Repository')) {
            $class = substr($class, 0, strlen($class) - 10);
        }

        return implode(':', [$class, $method, $args]);
    }

    /**
     * Get the cache tags from the model object.
     *
     * @return mixed
     */
    public function cacheTags()
    {
        return $this->model()->cacheTags();
    }

    /**
     * Return the primary model object which would
     * be used by the repository.
     *
     * @return \Suitmedia\Contracts\Cacheable\CacheableModel
     */
    abstract public function model();
}
