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
        if (property_exists($this, 'cacheDuration')) {
            return (int) static::$cacheDuration;
        }

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
        $result = \Cacheable::getConfiguration('except');

        if (property_exists($this, 'cacheExcept')) {
            $result = array_unique(array_merge($result, (array) static::$cacheExcept));
        }

        return $result;
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
        if (property_exists($this, 'cacheTags')) {
            return (array) static::$cacheTags;
        }

        return $this->model()->cacheTags();
    }

    /**
     * Return the primary model object which would
     * be used by the repository.
     *
     * @return \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    abstract public function model();
}
