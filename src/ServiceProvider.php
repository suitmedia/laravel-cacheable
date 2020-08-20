<?php

namespace Suitmedia\Cacheable;

use Illuminate\Cache\ArrayStore;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            realpath(dirname(__DIR__).'/config/cacheable.php') => config_path('cacheable.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $configFile = realpath(dirname(__DIR__).'/config/cacheable.php');
        if ($configFile !== false) {
            $this->mergeConfigFrom($configFile, 'cacheable');
        }

        $this->registerSingletons();
    }

    /**
     * Register the package's singleton objects.
     *
     * @return void
     */
    public function registerSingletons(): void
    {
        $this->app->singleton(CacheableService::class, function () {
            return new CacheableService(app('cache'), new ArrayStore());
        });

        $this->app->singleton(CacheableObserver::class, function () {
            return new CacheableObserver(collect());
        });
    }
}
