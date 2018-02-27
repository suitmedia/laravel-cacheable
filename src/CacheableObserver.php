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
     * @param  \Cacheable\Events\CacheableEvent $event
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
     * @param \Illuminate\Database\Eloquent\Model $model
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
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function saved(Model $model)
    {
        $this->flushCache($model);
    }

    /**
     * Deleted event handler.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->flushCache($model);
    }

    /**
     * Restored event handler.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function restored(Model $model)
    {
        $this->flushCache($model);
    }
}
