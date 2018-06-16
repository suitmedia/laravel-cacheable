<?php

namespace Suitmedia\Cacheable\Tests;

use Orchestra\Testbench\TestCase as BaseTest;
use Suitmedia\Cacheable\Tests\Models\User;
use Suitmedia\Cacheable\Tests\Models\Video;

abstract class TestCase extends BaseTest
{
    /**
     * Base user
     *
     * @var \Suitmedia\Cacheable\Tests\Models\User
     */
    protected $user;

    /**
     * Another user
     *
     * @var \Suitmedia\Cacheable\Tests\Models\User
     */
    protected $otherUser;

    /**
     * Define environment setup
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('cacheable.customTags', User::class);
    }

    /**
     * Define package aliases
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Cache' => \Illuminate\Support\Facades\Cache::class,
            'Cacheable' => \Suitmedia\Cacheable\Facade::class,
        ];
    }

    /**
     * Define package service provider
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Illuminate\Cache\CacheServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Suitmedia\Cacheable\ServiceProvider::class,
        ];
    }

    /**
     * Invoke protected / private method of the given object
     *
     * @param  Object      $object
     * @param  String      $methodName
     * @param  Array|array $parameters
     * @return mixed
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Prepare database requirements
     * to perform any tests.
     *
     * @param  string $migrationPath
     * @param  string $factoryPath
     * @return void
     */
    protected function prepareDatabase($migrationPath, $factoryPath = null)
    {
        $this->loadMigrationsFrom($migrationPath);

        if (!$factoryPath) {
            return;
        }

        if (method_exists($this, 'withFactories')) {
            $this->withFactories($factoryPath);
        } else {
            $this->app->make(ModelFactory::class)->load($factoryPath);
        }
    }

    /**
     * Prepare to get an exception in a test
     *
     * @param  mixed $exception
     * @return void
     */
    protected function prepareException($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
        } else {
            $this->setExpectedException($exception);
        }
    }

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase(
            realpath(__DIR__ . '/database/migrations'),
            realpath(__DIR__ . '/database/factories')
        );

        $this->user = factory(User::class)->create();
        $this->otherUser = factory(User::class)->create();

        factory(Video::class, 20)->create();
    }
}
