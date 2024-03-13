<?php

namespace Suitmedia\Cacheable\Tests;

use PHPUnit\Framework\Attributes\Test;
use Suitmedia\Cacheable\Exceptions\MethodNotFoundException;

class MethodNotFoundExceptionTests extends TestCase
{
    /**
     * Method not found exception
     *
     * @var \Suitmedia\Cacheable\Exceptions\MethodNotFoundException
     */
    protected $exception;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->exception = (new MethodNotFoundException())
            ->setRepositoryMethod('VideoRepository', 'getAllVideos');
    }

    #[Test]
    public function get_repository_class_name()
    {
        $className = $this->exception->getRepository();

        $this->assertEquals('VideoRepository', $className);
    }

    #[Test]
    public function get_repository_method_name()
    {
        $methodName = $this->exception->getMethod();

        $this->assertEquals('getAllVideos', $methodName);
    }
}
