<?php

namespace Suitmedia\Cacheable;

use Illuminate\Database\Eloquent\Model;
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
     * @param array $tags
     * @param Model $object
     *
     * @return array<string,boolean>
     */
    private function generateCustomTags($tags, Model $object)
    {
        $class = class_basename(get_class($object));
        $customTags = [$class.':'.$object->getKey() => true];

        foreach ($tags as $tag) {
            $key = $tag.':'.$class.':'.$object->getKey();
            $customTags[$key] = true;
        }

        return $customTags;
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
        $args = (array) $args;
        $tags = (array) $this->repository->cacheTags();
        $customTagInstances = (array) $this->service->getConfiguration('customTags');
        $customTags = [];

        foreach ($args as $arg) {
            if (is_object($arg) && in_array(get_class($arg), $customTagInstances, true)) {
                $customTags = array_merge($customTags, $this->generateCustomTags($tags, $arg));
            }
        }

        return array_merge($tags, array_keys($customTags));
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
