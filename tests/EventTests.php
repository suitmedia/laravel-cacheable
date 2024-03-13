<?php

namespace Suitmedia\Cacheable\Tests;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Suitmedia\Cacheable\Events\CacheableInvalidated;
use Suitmedia\Cacheable\Events\CacheableInvalidating;
use Suitmedia\Cacheable\Tests\Supports\Models\Video;

class EventTests extends TestCase
{
    #[Test]
    public function fire_cacheable_events_as_expected()
    {
        Event::fake([
            CacheableInvalidating::class,
            CacheableInvalidated::class,
        ]);

        Video::create([
            'title' => 'Ed Sheeran - Perfect',
            'url' => 'https://www.youtube.com/watch?v=2Vv-BfVoq4g'
        ]);

        Event::assertDispatched(CacheableInvalidating::class);
        Event::assertDispatched(CacheableInvalidated::class);
    }
}
