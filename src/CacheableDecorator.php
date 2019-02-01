<?php

namespace Suitmedia\Cacheable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Suitmedia\Cacheable\Contracts\CacheableRepository;
use Suitmedia\Cacheable\Exceptions\MethodNotFoundException;

class CacheableDecorator
{
    /**
     * Cacheable Repository.
     *
     * @var \Suitmedia\Cacheable\Contracts\CacheableRepository
     */
    private $repository;

    /**
     * Cacheable service object.
     *
     * @var \Suitmedia\Cacheable\CacheableService
     */
    private $service;

    /**
     * Class constructor.
     *
     * @param \Suitmedia\Cacheable\CacheableService              $service
     * @param \Suitmedia\Cacheable\Contracts\CacheableRepository $repository
     */
    public function __construct(CacheableService $service, CacheableRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Generate custom cache tags.
     *
     * @param \Illuminate\Support\Collection      $tags
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return void
     */
    private function generateCustomTags(Collection $tags, Model $object)
    {
        $class = class_basename(get_class($object));
        $baseTags = (array) $this->repository->cacheTags();

        $tags->push($class.':'.$object->getKey());

        foreach ($baseTags as $tag) {
            $tags->push($tag.':'.$class.':'.$object->getKey());
        }
    }

    /**
     * Generate cache tags.
     *
     * @param mixed $args
     *
     * @return array
     */
    private function generateTags($args)
    {
        $args = collect($args);
        $tags = collect($this->repository->cacheTags());

        $args->each(function ($object) use ($tags) {
            if ($this->isCustomTagInstance($object)) {
                $this->generateCustomTags($tags, $object);
            }
        });

        return $tags->sort()->unique()->all();
    }

    /**
     * Determine if the given object is a custom tag instance.
     *
     * @param mixed $object
     *
     * @return bool
     */
    private function isCustomTagInstance($object)
    {
        $customTagInstances = (array) $this->service->getConfiguration('customTags');

        return is_object($object) && ($object instanceof Model) && in_array(get_class($object), $customTagInstances, true);
    }

    /**
     * Finds whether the method is cacheable.
     *
     * @param string $method
     *
     * @return bool
     */
    private function methodIsCacheable($method)
    {
        return !in_array($method, $this->repository->cacheExcept(), true);
    }

    /**
     * Dynamically call methods from repository.
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $repository = $this->repository;

        if (!method_exists($repository, $method)) {
            throw (new MethodNotFoundException())->setRepositoryMethod(
                get_class($repository),
                $method
            );
        }

        if (!$this->methodIsCacheable($method)) {
            return call_user_func_array([$repository, $method], $args);
        }

        return $this->service->retrieve(
            $this->generateTags($args),
            $repository->cacheKey($method, $args),
            $repository->cacheDuration(),
            function () use ($repository, $method, $args) {
                $result = call_user_func_array([$repository, $method], $args);

                return ($result !== null) ? $result : false;
            }
        );
    }
}
