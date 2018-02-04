<?php

namespace Suitmedia\Cacheable;

use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;

class CacheableObserver
{
    /**
     * Tell the cacheable service to flush all cache
     * that related to the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function flushCache(Model $model)
    {
        \Cacheable::flush($model->cacheTags());
    }

    /**
     * Saved event handler
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function saved(Model $model)
    {
        $this->flushCache($model);
    }

    /**
     * Deleted event handler
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->flushCache($model);
    }

    /**
     * Restored event handler
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function restored(Model $model)
    {
        $this->flushCache($model);
    }
}
