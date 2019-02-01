<?php

namespace Suitmedia\Cacheable\Traits\Repository;

trait CacheableTrait
{
    /**
     * Return the cache duration value
     * which would be used by the repository.
     *
     * @return int
     */
    public function cacheDuration()
    {
        if (property_exists($this, 'cacheDuration')) {
            return (int) static::$cacheDuration;
        }

        return (int) config('cacheable.duration');
    }

    /**
     * Return an array of method names which
     * you don't wish to be cached.
     *
     * @return array
     */
    public function cacheExcept()
    {
        $result = (array) config('cacheable.except');

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
        $class = class_basename(get_class($this));
        $args = sha1(serialize($args));

        if (ends_with($class, 'Repository')) {
            $class = substr($class, 0, -10);
        }

        return implode(':', [$class, $method, $args]);
    }

    /**
     * Return the cache tags which would
     * be used by the repository.
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
