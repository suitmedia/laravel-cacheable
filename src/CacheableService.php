<?php

namespace Suitmedia\Cacheable;

use Closure;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\TaggableStore;
use Suitmedia\Cacheable\Contracts\CacheableRepository;

class CacheableService
{
    /**
     * Cache manager object.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Cacheable configurations.
     *
     * @var array
     */
    protected $configurations;

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
        $this->configurations = \Config::get('cacheable');
        $this->runtimeCache = $runtimeCache;
    }

    /**
     * Build CacheableDecorator based on the given class name
     * or repository object.
     *
     * @param mixed $repository
     *
     * @return \Suitmedia\Cacheable\CacheableDecorator
     */
    public function build($repository)
    {
        if (is_string($repository)) {
            $repository = \App::make($repository);
        }

        return $this->wrapWithDecorator($repository);
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
     * Get configuration value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfiguration($key)
    {
        return data_get($this->configurations, $key, null);
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
     */
    protected function setRuntimeCache($tags, $key, $value)
    {
        return $this->runtimeCache->tags($tags)->forever($key, $value);
    }

    /**
     * Get tagged cache object.
     *
     * @param TaggableStore $cache
     * @param mixed         $tags
     *
     * @return mixed
     */
    protected function taggedCache(TaggableStore $cache, $tags)
    {
        return ($tags) ? $cache->tags($tags) : $cache;
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
