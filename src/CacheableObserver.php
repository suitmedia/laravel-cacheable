<?php

namespace Suitmedia\Cacheable;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Events\CacheableEvent;
use Suitmedia\Cacheable\Events\CacheableInvalidated;
use Suitmedia\Cacheable\Events\CacheableInvalidating;

class CacheableObserver
{
    /**
     * Event dispatcher object.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Cacheable observer constructor.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Fire cacheable events.
     *
     * @param \Suitmedia\Cacheable\Events\CacheableEvent $event
     *
     * @return mixed
     */
    public function fireEvent(CacheableEvent $event)
    {
        if (method_exists($this->events, 'fire')) {
            return $this->events->fire($event);
        }

        return $this->events->dispatch($event);
    }

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

        $this->fireEvent(new CacheableInvalidating($model, $tags));

        \Cacheable::flush($tags);

        $this->fireEvent(new CacheableInvalidated($model, $tags));
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
