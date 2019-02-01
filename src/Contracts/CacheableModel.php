<?php

namespace Suitmedia\Cacheable\Contracts;

interface CacheableModel
{
    /**
     * Return the cache tags which would be used
     * by the model and model observer.
     *
     * @return mixed
     */
    public function cacheTags();

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty();
}
