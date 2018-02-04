<?php

namespace Suitmedia\Cacheable\Tests;

use Suitmedia\Cacheable\Exceptions\MethodNotFoundException;

class MethodNotFoundExceptionTests extends TestCase
{
    /**
     * Method not found exception.
     *
     * @var \Suitmedia\Cacheable\Exceptions\MethodNotFoundException
     */
    protected $exception;

    /**
     * Setup test requirements.
     */
    public function setUp()
    {
        $this->exception = (new MethodNotFoundException())
            ->setRepositoryMethod('VideoRepository', 'getAllVideos');
    }

    /** @test */
    public function get_repository_class_name()
    {
        $className = $this->exception->getRepository();

        $this->assertEquals('VideoRepository', $className);
    }

    /** @test */
    public function get_repository_method_name()
    {
        $methodName = $this->exception->getMethod();

        $this->assertEquals('getAllVideos', $methodName);
    }
}
