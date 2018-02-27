<?php

namespace Suitmedia\Cacheable\Tests;

use Suitmedia\Cacheable\Events\CacheableInvalidated;
use Suitmedia\Cacheable\Events\CacheableInvalidating;
use Suitmedia\Cacheable\Tests\Models\Video;

class EventTests extends TestCase
{
    /** @test */
    public function fire_cacheable_events_as_expected()
    {
        $this->expectsEvents([
            CacheableInvalidating::class,
            CacheableInvalidated::class,
        ]);

        Video::create([
            'title' => 'Ed Sheeran - Perfect',
            'url' => 'https://www.youtube.com/watch?v=2Vv-BfVoq4g'
        ]);
    }
}
