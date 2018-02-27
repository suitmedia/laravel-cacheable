<?php

namespace Suitmedia\Cacheable\Events;

use Illuminate\Queue\SerializesModels;
use Suitmedia\Cacheable\Contracts\CacheableModel;

abstract class CacheableEvent
{
    use SerializesModels;

    /**
     * Cacheable model object
     *
     * @var \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    protected $model;

    /**
     * Cache tags which are being invalidating
     *
     * @var mixed
     */
    protected $tags;

    /**
     * Event constructor
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     * @param mixed                                         $tags
     */
    public function __construct(CacheableModel $model, $tags)
    {
        $this->model = $model;
        $this->tags = $tags;
    }

    /**
     * Model accessor
     *
     * @return \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Tags accessor
     *
     * @return mixed
     */
    public function tags()
    {
        return $this->tags;
    }
}
