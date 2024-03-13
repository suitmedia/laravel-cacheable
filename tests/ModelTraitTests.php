<?php

namespace Suitmedia\Cacheable\Tests;

use PHPUnit\Framework\Attributes\Test;
use Suitmedia\Cacheable\Tests\Supports\Models\User;
use Suitmedia\Cacheable\Tests\Supports\Models\Video;

class ModelTraitTests extends TestCase
{
    /**
     * User model object
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $userModel;

    /**
     * Video model object
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $videoModel;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userModel = new User;
        $this->videoModel = new Video;
    }

    #[Test]
    public function get_default_cache_tags_value()
    {
        $tags = $this->videoModel->cacheTags();

        $this->assertEquals('Video', $tags);
    }

    #[Test]
    public function get_overriden_cache_tags_value()
    {
        $tags = $this->userModel->cacheTags();

        $this->assertEquals(['User', 'UserRoles'], $tags);
    }
}
