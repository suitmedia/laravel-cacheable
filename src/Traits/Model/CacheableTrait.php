<?php

namespace Suitmedia\Cacheable\Traits\Model;

use Suitmedia\Cacheable\CacheableObserver;

trait CacheableTrait
{
    /**
     * Boot the Cacheable trait by attaching
     * a new observer to the current model.
     *
     * @return void
     */
    public static function bootCacheableTrait()
    {
        static::observe(app(CacheableObserver::class));
    }

    /**
     * Return the cache duration value
     * for this model.
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
     * Generate cache tags automatically
     * based on the model class name.
     *
     * @return string|array
     */
    public function cacheTags()
    {
        if (property_exists($this, 'cacheTags')) {
            return (array) static::$cacheTags;
        }

        return class_basename(get_class($this));
    }
}
