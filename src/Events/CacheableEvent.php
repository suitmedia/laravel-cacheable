<?php

namespace Suitmedia\Cacheable\Events;

use Illuminate\Queue\SerializesModels;
use Suitmedia\Cacheable\Contracts\CacheableModel;

abstract class CacheableEvent
{
    use SerializesModels;

    /**
     * The affected fields in the current cacheable event.
     *
     * @var array
     */
    protected $affectedFields;

    /**
     * Cacheable model object.
     *
     * @var \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    protected $model;

    /**
     * Cache tags which are being invalidating.
     *
     * @var mixed
     */
    protected $tags;

    /**
     * Event constructor.
     *
     * @param \Suitmedia\Cacheable\Contracts\CacheableModel $model
     * @param mixed                                         $tags
     * @param array                                         $affectedFields
     */
    public function __construct(CacheableModel $model, $tags, $affectedFields = [])
    {
        $this->affectedFields = $affectedFields;
        $this->model = $model;
        $this->tags = $tags;
    }

    /**
     * Affected fields accessor.
     *
     * @return array
     */
    public function affectedFields()
    {
        return $this->affectedFields;
    }

    /**
     * Model accessor.
     *
     * @return \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Tags accessor.
     *
     * @return mixed
     */
    public function tags()
    {
        return $this->tags;
    }
}
