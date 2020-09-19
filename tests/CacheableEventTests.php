<?php

namespace Suitmedia\Cacheable\Tests;

use Suitmedia\Cacheable\Events\CacheableInvalidating;
use Suitmedia\Cacheable\Tests\Supports\Models\Video;

class CacheableEventTests extends TestCase
{
    /**
     * Cacheable Event Object
     *
     * @var \Suitmedia\Cacheable\Events\CacheableEvent
     */
    protected $event;

    /**
     * Cacheable model object
     *
     * @var \Suitmedia\Cacheable\Contracts\CacheableModel
     */
    protected $model;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();
        
        $this->model = new Video;
        $this->event = new CacheableInvalidating(
            $this->model,
            $this->model->cacheTags(),
            ['title' => 'new title']
        );
    }

    /** @test */
    public function return_affected_fields_correctly()
    {
        $actual = $this->event->affectedFields();
        $expected = ['title' => 'new title'];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function return_affected_model_correctly()
    {
        $actual = $this->event->model();

        $this->assertEquals($this->model, $actual);
    }

    /** @test */
    public function return_invalidate_tags_correctly()
    {
        $actual = $this->event->tags();
        $expected = $this->model->cacheTags();

        $this->assertEquals($expected, $actual);
    }
}
