<?php

namespace Suitmedia\Cacheable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as BaseTest;
use Suitmedia\Cacheable\Tests\Supports\Models\User;
use Suitmedia\Cacheable\Tests\Supports\Models\Video;

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
    protected function getEnvironmentSetUp($app): void
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
    protected function getPackageAliases()
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
     * @return void
     */
    protected function prepareDatabase(string $migrationPath)
    {
        $this->loadMigrationsFrom($migrationPath);
    }

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->prepareDatabase(
            realpath(__DIR__ . '/Supports/Migrations')
        );

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        Video::factory(20)->create();
    }
}
