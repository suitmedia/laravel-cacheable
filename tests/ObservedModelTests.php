<?php

namespace Suitmedia\Cacheable\Tests;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Suitmedia\Cacheable\Tests\Supports\Models\Video;

class ObservedModelTests extends TestCase
{
    /**
     * Model object
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->model = new Video;

        Cache::tags('Video')->put('foo', 'bar', 120);
    }

    #[Test]
    public function flush_cache_on_saving()
    {
        $video = Video::find(1);
        $video->title = 'Lorem Ipsum';
        $video->save();

        $data = Cache::tags('Video')->get('foo');

        $this->assertEquals(null, $data);
    }

    #[Test]
    public function flush_cache_on_deleting()
    {
        Video::find(2)->delete();

        $data = Cache::tags('Video')->get('foo');

        $this->assertEquals(null, $data);
    }

    #[Test]
    public function flush_cache_on_restoring_deleted_record()
    {
        Video::find(3)->delete();
        Cache::tags('Video')->put('foo', 'barbar', 120);

        $data = Cache::tags('Video')->get('foo');
        $this->assertEquals('barbar', $data);

        Video::withTrashed()->find(3)->restore();

        $data = Cache::tags('Video')->get('foo');
        $this->assertEquals(null, $data);
    }
}
