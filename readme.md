[![Build Status](https://travis-ci.org/suitmedia/laravel-cacheable.svg?branch=master)](https://travis-ci.org/suitmedia/laravel-cacheable) 
[![codecov](https://codecov.io/gh/suitmedia/laravel-cacheable/branch/master/graph/badge.svg)](https://codecov.io/gh/suitmedia/laravel-cacheable) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/suitmedia/laravel-cacheable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/suitmedia/laravel-cacheable/?branch=master) 
[![Total Downloads](https://poser.pugx.org/suitmedia/laravel-cacheable/d/total.svg)](https://packagist.org/packages/suitmedia/laravel-cacheable) 
[![Latest Stable Version](https://poser.pugx.org/suitmedia/laravel-cacheable/v/stable.svg)](https://packagist.org/packages/suitmedia/laravel-cacheable) 
[![License: MIT](https://poser.pugx.org/laravel/framework/license.svg)](https://opensource.org/licenses/MIT) 

# Laravel Cacheable

> Decorate your repositories and make them cacheable

## Synopsis

This package will help you to make your repositories cacheable without worrying about how to manage the cache, and when you should flush the cache. Laravel Cacheable package uses a decorator pattern to wrap your existing repository and add the auto-caching feature into it.

## Table of contents

* [Compatibility](#compatibility)
* [Requirements](#requirements)
* [Setup](#setup)
* [Configuration](#configuration)
* [License](#license)

## Compatibility

This package supports Laravel versions from ``5.1.35`` to ``5.6.*``

## Requirements

This package require you to use taggable cache storage like memcached or redis. You will get errors if you use this package while using any cache storage which doesn't support tags.

## Setup

Install the package via Composer :
```sh
$ composer require suitmedia/laravel-cacheable
```

> If you are using Laravel version 5.5+ then you can skip registering the service provider and package alias in your application.

### Register Service Provider

Add the package service provider in your ``config/app.php``

```php
'providers' => [
    // ...
    \Suitmedia\Cacheable\ServiceProvider::class,
];
```

### Register Package Alias

Add the package alias in your ``config/app.php``

```php
'aliases' => [
    // ...
    'Cacheable' => \Suitmedia\Cacheable\Facade::class,
];
```

## Configuration

Publish configuration file using ``php artisan`` command

```sh
$ php artisan vendor:publish --provider="Suitmedia\Cacheable\ServiceProvider"
```

The command above would copy a new configuration file to ``/config/cacheable.php``

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Custom Tags
    |--------------------------------------------------------------------------
    |
    | Define all of Eloquent Models which should add custom cache tags
    | automatically to the cached objects.
    |
    */

    'customTags' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Default Cache Duration
    |--------------------------------------------------------------------------
    |
    | Define the default cache duration here.
    | Setting the cache duration to '0' will make the cache lasts forever.
    |
    */

    'duration' => 0,

    /*
    |--------------------------------------------------------------------------
    | Methods which shouldn't be cached
    |--------------------------------------------------------------------------
    |
    | Define a collection of method names which you don't wish
    | to be cached.
    |
    */

    'except' => [
        'cacheDuration',
        'cacheExcept',
        'cacheKey',
        'cacheTags',
        'create',
        'delete',
        'restore',
        'update',
        'updateOrCreate',
    ],
];
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.