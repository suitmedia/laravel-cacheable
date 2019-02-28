<?php

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
    | Define the default cache duration in seconds.
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
