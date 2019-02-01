<?php

namespace Suitmedia\Cacheable\Tests;

use Illuminate\Cache\ArrayStore;
use Suitmedia\Cacheable\Tests\Models\User;
use Suitmedia\Cacheable\Tests\Models\Video;
use Suitmedia\Cacheable\Tests\Repositories\VideoRepository;
use Suitmedia\Cacheable\CacheableDecorator;
use Suitmedia\Cacheable\CacheableService;

class ServiceTests extends TestCase
{
    /**
     * Runtime cache object
     *
     * @var \Illuminate\Cache\ArrayStore
     */
    protected $runtimeCache;

    /**
     * Service object
     * @var \Suitmedia\Cacheable\CacheableService
     */
    protected $service;

    /**
     * Setup test requirements
     */
    public function setUp()
    {
        parent::setUp();

        $this->runtimeCache = new ArrayStore;
        $this->service = new CacheableService(app('cache'), $this->runtimeCache);
    }

    /** @test */
    public function build_decorator_object_by_class_name()
    {
        $decorator = $this->service->build(VideoRepository::class);

        $this->assertInstanceOf(CacheableDecorator::class, $decorator);
    }

    /** @test */
    public function build_decorator_object_by_repository_object()
    {
        $repository = new VideoRepository(new Video);
        $decorator = $this->service->build($repository);

        $this->assertInstanceOf(CacheableDecorator::class, $decorator);
    }

    /** @test */
    public function flush_cache()
    {
        // Set runtime cache
        $this->invokeMethod(
            $this->service,
            'setRuntimeCache',
            ['tag', 'key', 'value']
        );

        $this->service->flush();

        $data = $this->invokeMethod(
            $this->service,
            'getRuntimeCache',
            ['tag', 'key']
        );

        $this->assertEquals(null, $data);
    }

    /** @test */
    public function runtime_cache_getter_and_setter()
    {
        // Set runtime cache
        $this->invokeMethod(
            $this->service,
            'setRuntimeCache',
            ['tag', 'key', 'value']
        );

        $data = $this->invokeMethod(
            $this->service,
            'getRuntimeCache',
            ['tag', 'key']
        );

        $this->assertEquals('value', $data);
    }

    /** @test */
    public function retrieve_data_from_runtime_cache()
    {
        $counter = 0;
        $closure = function () use (&$counter) {
            return 'Data ' . ++$counter;
        };

        $this->service->retrieve('Video', 'first', 120, $closure);
        $data = $this->service->retrieve('Video', 'first', 120, $closure);

        $this->assertEquals('Data 1', $data);
    }

    /** @test */
    public function retrieve_data_from_cache_with_duration()
    {
        $counter = 0;
        $closure = function () use (&$counter) {
            return 'Data ' . ++$counter;
        };

        $this->service->retrieve('Video', 'first', 120, $closure);

        $this->runtimeCache->flush();

        $data = $this->service->retrieve('Video', 'first', 120, $closure);

        $this->assertEquals('Data 1', $data);
    }

    /** @test */
    public function retrieve_data_from_cache_without_duration()
    {
        $counter = 0;
        $closure = function () use (&$counter) {
            return 'Data ' . ++$counter;
        };

        $this->service->retrieve('Video', 'first', 0, $closure);

        $this->runtimeCache->flush();

        $data = $this->service->retrieve('Video', 'first', 0, $closure);

        $this->assertEquals('Data 1', $data);
    }

    /** @test */
    public function wrap_repository_with_decorator_object()
    {
        $repository = new VideoRepository(new Video);
        $decorator = $this->invokeMethod(
            $this->service,
            'wrapWithDecorator',
            [$repository]
        );

        $this->assertInstanceOf(CacheableDecorator::class, $decorator);
    }

    /** @test */
    public function execute_closure_when_the_tagged_cache_are_flushed()
    {
        $counter = 0;
        $closure = function () use (&$counter) {
            return 'Data ' . ++$counter;
        };

        $this->service->retrieve('Video', 'first', 120, $closure);

        $this->service->flush('Video');

        $data = $this->service->retrieve('Video', 'first', 120, $closure);

        $this->assertEquals('Data 2', $data);
    }

    /** @test */
    public function execute_closure_when_all_cache_are_flushed()
    {
        $counter = 0;
        $closure = function () use (&$counter) {
            return 'Data ' . ++$counter;
        };

        $this->service->retrieve('Video', 'first', 120, $closure);

        $this->service->flush();

        $data = $this->service->retrieve('Video', 'first', 120, $closure);

        $this->assertEquals('Data 2', $data);
    }
}
