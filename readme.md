[![Build](https://github.com/suitmedia/laravel-cacheable/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/suitmedia/laravel-cacheable/actions/workflows/main.yml) 
[![codecov](https://codecov.io/gh/suitmedia/laravel-cacheable/branch/master/graph/badge.svg)](https://codecov.io/gh/suitmedia/laravel-cacheable) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/suitmedia/laravel-cacheable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/suitmedia/laravel-cacheable/?branch=master) 
[![Total Downloads](https://poser.pugx.org/suitmedia/laravel-cacheable/d/total.svg)](https://packagist.org/packages/suitmedia/laravel-cacheable) 
[![Latest Stable Version](https://poser.pugx.org/suitmedia/laravel-cacheable/v/stable.svg)](https://packagist.org/packages/suitmedia/laravel-cacheable) 
[![License: MIT](https://poser.pugx.org/laravel/framework/license.svg)](https://opensource.org/licenses/MIT) 

# Laravel Cacheable

> Decorate your repositories and make them cacheable

## Synopsis

This package will help you to make your repositories cacheable without worrying about how to manage the cache, and provide an easy way to invalidate the cache. Laravel Cacheable package uses a dynamic decorator class to wrap your existing repository and add the auto-caching feature into it.

## Table of contents

* [Compatibility](#compatibility)
* [Requirements](#requirements)
* [Setup](#setup)
* [Configuration](#configuration)
* [Usage](#usage)
* [License](#license)

## Compatibility

 Laravel version   | Cacheable version
:------------------|:-----------------
 5.1.x - 5.4.x     | 1.0.x - 1.3.x
 5.5.x - 5.8.x     | 1.4.x
 6.x               | 1.5.x
 7.x               | 1.6.x
 8.x               | 1.7.x
 9.x               | 1.9.x - 1.10.x
 10.x              | 1.11.x

## Requirements

This package require you to use cache storage which supports tags like memcached or redis. You will get errors if you use this package while using any cache storage which does not support tags.

## Setup

Install the package via Composer :
```sh
$ composer require suitmedia/laravel-cacheable
```

> If you are using Laravel version 5.5+ then you can skip registering the service provider and package alias in your application.

### Register The Service Provider

Add the package service provider in your ``config/app.php``

```php
'providers' => [
    // ...
    \Suitmedia\Cacheable\ServiceProvider::class,
];
```

### Register The Package Alias

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

    'customTags' => \App\User::class,

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

## Usage

#### Prepare Your Model

Every Model that you wish to be cached should implement the `CacheableModel` contract and use the `CacheableTrait`. The trait will add extra features to your model and observe your model for any future changes. This way, the package will notice whenever the observed model get updated and then it will flush the cache related to the affected model immediately.

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Suitmedia\Cacheable\Contracts\CacheableModel;
use Suitmedia\Cacheable\Traits\Model\CacheableTrait;

class Article extends Model implements CacheableModel
{
    use CacheableTrait;
}
```

#### Prepare Your Repository

Every Repository that you need to be cached also have to implements the `CacheableRepository` contract. You can implements the contract simply by using the `CacheableTrait` like this :

```php
namespace App\Repositories;

use App\Article;
use Suitmedia\Cacheable\Traits\Repository\CacheableTrait;
use Suitmedia\Cacheable\Contracts\CacheableRepository;

class ArticleRepository implements CacheableRepository
{
    use CacheableTrait;

    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function model()
    {
        return $this->article;
    }

    /*
    |--------------------------------------------------------------------------
    | Repository's method definition starts from here
    |--------------------------------------------------------------------------
    */
    
    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($videoId);
    }
}
```

#### Retrieve Data From Repository And Cache The Result

With this package, you won't need to create new classes to decorate each of your repositories. You can just decorate them using the `Cacheable` facade, and all results of the repository's methods will be cached automatically.
 
```php
// Lets decorate the repository first
$repo = \Cacheable::wrap(ArticleRepository::class);

// The result of these codes will be cached automatically
$repo->all();
$repo->find(1);
```

#### Cache Invalidation

This package will help you to invalidate the cache automatically whenever the `CacheableModel` is updated. It will invalidate the cache based on the cache tags which have been defined in your model.

But, you can also invalidate the cache manually using the `Cacheable` facade.

```php
// Flush everything
\Cacheable::flush();

// Flush the cache using a specific tag
\Cacheable::flush('Article');

// Flush the cache using a specific tag,
// and only for the cache which belongs to a specific user
\Cacheable::flush('LikedArticle:User:7');
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
