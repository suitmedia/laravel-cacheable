<?php

namespace Suitmedia\Cacheable\Tests\Repositories;

use Suitmedia\Cacheable\Tests\Models\Video;
use Suitmedia\Cacheable\Traits\Repository\CacheableTrait;

class VideoRepository extends EloquentRepository
{
    protected static $cacheTags = ['Video', 'VideoAlbum'];

    public function __construct(Video $model)
    {
        parent::__construct($model);    
    }

    public function getAllVideos()
    {
        return $this->model->get();
    }

    public function getVideo($videoId)
    {
        return $this->model->find($videoId);
    }

    public function update($params)
    {
        //
    }
}
