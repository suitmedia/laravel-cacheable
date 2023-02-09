<?php

namespace Suitmedia\Cacheable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Suitmedia\Cacheable\Contracts\CacheableRepository;
use Suitmedia\Cacheable\Exceptions\MethodNotFoundException;

class CacheableDecorator
{
    const NULL_VALUE = 'NULL_VALUE';

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
    private function generateCustomTags(Collection $tags, Model $object): void
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
    private function generateTags($args): array
    {
        $args = new Collection((array) $args);
        $tags = new Collection($this->repository->cacheTags());

        $args->each(function ($object) use ($tags) {
            if ($this->isCustomTagInstance($object)) {
                $this->generateCustomTags($tags, $object);
            }
        });

        return $tags->sort()->unique()->all();
    }

    /**
     * Get the correct return value if the repository returns itself.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function getReturnValue($value)
    {
        if ($value === $this->repository) {
            return $this;
        }

        return ($value === CacheableDecorator::NULL_VALUE) ? null : $value;
    }

    /**
     * Determine if the given object is a custom tag instance.
     *
     * @param mixed $object
     *
     * @return bool
     */
    private function isCustomTagInstance($object): bool
    {
        $customTagInstances = (array) config('cacheable.customTags');

        return is_object($object) && ($object instanceof Model) && in_array(get_class($object), $customTagInstances, true);
    }

    /**
     * Finds whether the method is cacheable.
     *
     * @param string $method
     *
     * @return bool
     */
    private function methodIsCacheable($method): bool
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
            /* @phpstan-ignore-next-line */
            return $this->getReturnValue(call_user_func_array([$repository, $method], $args));
        }

        return $this->getReturnValue($this->service->retrieve(
            $this->generateTags($args),
            $repository->cacheKey($method, $args),
            $repository->cacheDuration(),
            static function () use ($repository, $method, $args) {
                /* @phpstan-ignore-next-line */
                $result = call_user_func_array([$repository, $method], $args);

                return $result ?? CacheableDecorator::NULL_VALUE;
            }
        ));
    }
}
