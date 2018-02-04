<?php

namespace Suitmedia\Cacheable;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/cacheable.php') => config_path('cacheable.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/cacheable.php'), 'cacheable');

        $this->app->singleton(CacheableService::class, function () {
            return new CacheableService(cache(), new ArrayStore);
        });
    }
}
