<?php

namespace Suitmedia\Cacheable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Events\CacheableInvalidated;
use Suitmedia\Cacheable\Events\CacheableInvalidating;

class CacheableObserver
{
    /**
     * The dirty fields of all observed models.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $dirtyFields;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $dirtyFields
     */
    public function __construct(Collection $dirtyFields)
    {
        $this->dirtyFields = $dirtyFields;
    }

    /**
     * Tell the cacheable service to flush all cache
     * that related to the given model.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    protected function flushCache(CacheableModel $model): void
    {
        $affectedFields = $this->dirtyFields->get(get_class($model));
        $tags = $model->cacheTags();

        event(new CacheableInvalidating($model, $tags, $affectedFields));

        app(CacheableService::class)->flush($tags);

        event(new CacheableInvalidated($model, $tags, $affectedFields));
    }

    /**
     * Saved event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function saved(CacheableModel $model): void
    {
        $this->flushCache($model);
    }

    /**
     * saving event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function saving(CacheableModel $model): void
    {
        $this->dirtyFields->put(get_class($model), $model->getDirty());
    }

    /**
     * Deleted event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function deleted(CacheableModel $model): void
    {
        $this->flushCache($model);
    }

    /**
     * deleting event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function deleting(CacheableModel $model): void
    {
        $this->dirtyFields->put(get_class($model), ['deleted_at' => Carbon::now()]);
    }

    /**
     * Restored event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function restored(CacheableModel $model): void
    {
        $this->flushCache($model);
    }

    /**
     * restoring event handler.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     *
     * @return void
     */
    public function restoring(CacheableModel $model): void
    {
        $this->dirtyFields->put(get_class($model), ['deleted_at' => null]);
    }
}
