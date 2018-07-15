<?php

namespace Suitmedia\Cacheable;

use Illuminate\Database\Eloquent\Model;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Events\CacheableInvalidated;
use Suitmedia\Cacheable\Events\CacheableInvalidating;

class CacheableObserver
{
    /**
     * Tell the cacheable service to flush all cache
     * that related to the given model.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    protected function flushCache(CacheableModel $model)
    {
        $tags = $model->cacheTags();

        event(new CacheableInvalidating($model, $tags));

        \Cacheable::flush($tags);

        event(new CacheableInvalidated($model, $tags));
    }

    /**
     * Saved event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function saved(CacheableModel $model)
    {
        $this->flushCache($model);
    }

    /**
     * Deleted event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function deleted(CacheableModel $model)
    {
        $this->flushCache($model);
    }

    /**
     * Restored event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function restored(CacheableModel $model)
    {
        $this->flushCache($model);
    }
}
