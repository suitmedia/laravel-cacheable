<?php

namespace Suitmedia\Cacheable;

use Closure;
use ErrorException;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Store;
use Suitmedia\Cacheable\Contracts\CacheableRepository;

class CacheableService
{
    /**
     * Cache manager object.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    protected $cache;

    /**
     * Runtime cache.
     *
     * @var \Illuminate\Cache\ArrayStore
     */
    protected $runtimeCache;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Cache\CacheManager $cache
     * @param \Illuminate\Cache\ArrayStore   $runtimeCache
     */
    public function __construct(CacheManager $cache, ArrayStore $runtimeCache)
    {
        $this->cache = $cache->store()->getStore();
        $this->runtimeCache = $runtimeCache;
    }

    /**
     * Just an alias of wrap() method.
     *
     * @param mixed $repository
     *
     * @return \Suitmedia\Cacheable\CacheableDecorator
     */
    public function build($repository): CacheableDecorator
    {
        return $this->wrap($repository);
    }

    /**
     * Flush cache.
     *
     * @param mixed $tags
     *
     * @throws ErrorException
     *
     * @return void
     */
    public function flush($tags = null): void
    {
        $this->taggedCache($this->cache, $tags)->flush();
        $this->taggedCache($this->runtimeCache, $tags)->flush();
    }

    /**
     * Get runtime cached object.
     *
     * @param mixed  $tags
     * @param string $key
     *
     * @return mixed
     */
    protected function getRuntimeCache($tags, $key)
    {
        return $this->runtimeCache->tags($tags)->get($key);
    }

    /**
     * Retrieve cached items.
     *
     * @param mixed   $tags
     * @param string  $key
     * @param int     $duration
     * @param Closure $callable
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function retrieve($tags, $key, $duration, Closure $callable)
    {
        if ($data = $this->getRuntimeCache($tags, $key)) {
            return $data;
        }

        $cache = $this->taggedCache($this->cache, $tags);

        $data = ($duration > 0) ?
            $cache->remember($key, $duration, $callable) :
            $cache->rememberForever($key, $callable);

        $this->setRuntimeCache($tags, $key, $data);

        return $data;
    }

    /**
     * Set runtime cache object.
     *
     * @param mixed  $tags
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function setRuntimeCache($tags, $key, $value): void
    {
        $this->runtimeCache->tags($tags)->forever($key, $value);
    }

    /**
     * Get tagged cache object.
     *
     * @param Store $cache
     * @param mixed $tags
     *
     * @throws ErrorException
     *
     * @return \Illuminate\Cache\TaggedCache|TaggableStore
     */
    protected function taggedCache(Store $cache, $tags)
    {
        if (!($cache instanceof TaggableStore)) {
            throw new ErrorException('Laravel Cacheable requires taggable cache store.');
        }

        return !empty($tags) ? $cache->tags($tags) : $cache;
    }

    /**
     * Build CacheableDecorator and wrap the given class name
     * or repository object.
     *
     * @param mixed $repository
     *
     * @return \Suitmedia\Cacheable\CacheableDecorator
     */
    public function wrap($repository): CacheableDecorator
    {
        if (is_string($repository)) {
            $repository = \App::make($repository);
        }

        return $this->wrapWithDecorator($repository);
    }

    /**
     * Wrap the given CacheableRepository with a new CacheableDecorator.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableRepository $repository
     *
     * @return \Suitmedia\Cacheable\CacheableDecorator
     */
    protected function wrapWithDecorator(CacheableRepository $repository): CacheableDecorator
    {
        return new CacheableDecorator($this, $repository);
    }
}
