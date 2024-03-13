<?php

namespace Suitmedia\Cacheable\Tests;

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Suitmedia\Cacheable\Tests\Supports\Repositories\UserRepository;
use Suitmedia\Cacheable\Tests\Supports\Repositories\VideoRepository;

class RepositoryTraitTests extends TestCase
{
    /**
     * User Repository Object
     *
     * @var \Suitmedia\Cacheable\Tests\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Video Repository Object
     *
     * @var \Suitmedia\Cacheable\Tests\Repositories\VideoRepository
     */
    protected $videoRepository;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = App::make(UserRepository::class);
        $this->videoRepository = App::make(VideoRepository::class);
    }

    #[Test]
    public function get_default_cache_duration_value_from_configuration()
    {
        $duration = (int) $this->videoRepository->cacheDuration();

        $this->assertEquals(0, $duration);
    }

    #[Test]
    public function get_overriden_cache_duration_value()
    {
        $duration = (int) $this->userRepository->cacheDuration();

        $this->assertEquals(3600, $duration);
    }

    #[Test]
    public function get_default_cache_except_value()
    {
        $actual = $this->videoRepository->cacheExcept();

        $expected = [
            'cacheDuration',
            'cacheExcept',
            'cacheKey',
            'cacheTags',
            'create',
            'delete',
            'restore',
            'update',
            'updateOrCreate',
        ];

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function get_overriden_cache_except_value()
    {
        $actual = $this->userRepository->cacheExcept();

        $expected = [
            'cacheDuration', 'cacheExcept', 'cacheKey',
            'cacheTags', 'create', 'delete', 'restore',
            'update', 'updateOrCreate', 'add', 'edit',
        ];

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function get_default_cache_key_value()
    {
        $args = [
            'asd' => 'qwe',
            'qwe' => 'rty'
        ];
        $actual = $this->videoRepository->cacheKey('getMyVideo', $args);
        $expected = 'Video:getMyVideo:6bc761f7eee0bc5fae1f5758f8e9f9dac3a94c7e';

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function get_overriden_cache_key_value()
    {
        $actual = $this->userRepository->cacheKey('getMyVideo', null);
        $expected = 'test-override-cache-key-method';

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function get_default_cache_tags_value_from_model_object()
    {
        $actual = $this->userRepository->cacheTags();
        $expected = ['User', 'UserRoles'];

        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function get_overriden_cache_tags_value()
    {
        $actual = $this->videoRepository->cacheTags();
        $expected = ['Video', 'VideoAlbum'];

        $this->assertEquals($expected, $actual);
    }
}
