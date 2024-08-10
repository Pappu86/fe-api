<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for MeiliSearch Search Engine.
    |
    | To learn more: https://docs.meilisearch.com
    |
    */

    'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),

    'key' => env('MEILISEARCH_KEY')
];
