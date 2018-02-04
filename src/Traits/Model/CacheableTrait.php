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
     * @return integer
     */
    public function cacheDuration()
    {
        return \Cacheable::getConfiguration('duration');
    }

    /**
     * Generate cache tags automatically
     * based on the model class name.
     *
     * @return string
     */
    public function cacheTags()
    {
        $className = get_class($this);
        return last(explode('\\', $className));
    }
}
