<?php

namespace Suitmedia\Cacheable;

use Closure;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\TaggableStore;
use Illuminate\Cache\TaggedCache;
use Suitmedia\Cacheable\Contracts\CacheableRepository;

class CacheableService
{
    /**
     * Cache manager object.
     *
     * @var \Illuminate\Cache\TaggableStore
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
    public function build($repository)
    {
        return $this->wrap($repository);
    }

    /**
     * Flush cache.
     *
     * @param mixed $tags
     *
     * @return void
     */
    public function flush($tags = null)
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
    protected function setRuntimeCache($tags, $key, $value)
    {
        $this->runtimeCache->tags($tags)->forever($key, $value);
    }

    /**
     * Get tagged cache object.
     *
     * @param TaggableStore $cache
     * @param mixed         $tags
     *
     * @return \Illuminate\Cache\TaggedCache|TaggableStore
     */
    protected function taggedCache(TaggableStore $cache, $tags)
    {
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
    public function wrap($repository)
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
    protected function wrapWithDecorator(CacheableRepository $repository)
    {
        return new CacheableDecorator($this, $repository);
    }
}
